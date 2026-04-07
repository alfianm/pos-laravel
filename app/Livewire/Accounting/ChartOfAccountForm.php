<?php

namespace App\Livewire\Accounting;

use App\Constants\NormalBalance;
use App\Models\AccountCategory;
use App\Models\ChartOfAccount;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ChartOfAccountForm extends Component
{
    use AuthorizesRequests;

    public ?ChartOfAccount $account = null;

    // Form fields
    public string $account_code = '';
    public string $account_name = '';
    public ?string $account_category_id = null;
    public ?string $parent_id = null;
    public string $normal_balance = 'debit';
    public string $description = '';
    public bool $is_active = true;

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->account = ChartOfAccount::findOrFail($id);
            $this->authorize('view', $this->account);

            $this->account_code = $this->account->account_code;
            $this->account_name = $this->account->account_name;
            $this->account_category_id = $this->account->account_category_id;
            $this->parent_id = $this->account->parent_id;
            $this->normal_balance = $this->account->normal_balance;
            $this->description = $this->account->description ?? '';
            $this->is_active = $this->account->is_active;
        } else {
            $this->authorize('create', ChartOfAccount::class);
        }
    }

    protected function rules(): array
    {
        $tenantId = auth()->user()->tenant_id;
        $accountId = $this->account?->id;

        return [
            'account_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('chart_of_accounts', 'account_code')
                    ->where('tenant_id', $tenantId)
                    ->ignore($accountId),
            ],
            'account_name' => 'required|string|max:100',
            'account_category_id' => 'required|exists:account_categories,id',
            'parent_id' => [
                'nullable',
                'exists:chart_of_accounts,id',
                function ($attribute, $value, $fail) use ($accountId) {
                    if ($value && $value == $accountId) {
                        $fail('An account cannot be its own parent.');
                    }
                },
            ],
            'normal_balance' => ['required', Rule::in(['debit', 'credit'])],
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'account_code.unique' => 'This account code is already in use.',
            'account_category_id.required' => 'Please select an account category.',
            'parent_id.exists' => 'The selected parent account does not exist.',
        ];
    }

    public function save(): void
    {
        if ($this->account) {
            $this->authorize('update', $this->account);
        } else {
            $this->authorize('create', ChartOfAccount::class);
        }

        $validated = $this->validate();

        $data = [
            'account_code' => $validated['account_code'],
            'account_name' => $validated['account_name'],
            'account_category_id' => $validated['account_category_id'],
            'parent_id' => $validated['parent_id'] ?: null,
            'normal_balance' => $validated['normal_balance'],
            'description' => $validated['description'] ?: null,
            'is_active' => $validated['is_active'],
            'tenant_id' => auth()->user()->tenant_id,
        ];

        if ($this->account) {
            $this->account->update($data);
            $message = 'Chart of account updated successfully.';
        } else {
            ChartOfAccount::create($data);
            $message = 'Chart of account created successfully.';
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message,
        ]);

        $this->redirect(route('accounting.chart-of-accounts.index'), navigate: true);
    }

    public function render()
    {
        $categories = AccountCategory::orderBy('code')
            ->get()
            ->groupBy(fn($cat) => $cat->type->label());

        // Get potential parent accounts (exclude self and descendants)
        $potentialParents = ChartOfAccount::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->account, function ($query) {
                $query->where('id', '!=', $this->account->id)
                    ->whereNotIn('id', $this->getDescendantIds($this->account->id));
            })
            ->orderBy('account_code')
            ->get();

        return view('livewire.accounting.chart-of-account-form', [
            'categories' => $categories,
            'potentialParents' => $potentialParents,
        ]);
    }

    private function getDescendantIds(string $parentId): array
    {
        $descendants = [];
        $children = ChartOfAccount::where('parent_id', $parentId)->pluck('id')->toArray();

        foreach ($children as $childId) {
            $descendants[] = $childId;
            $descendants = array_merge($descendants, $this->getDescendantIds($childId));
        }

        return $descendants;
    }
}
