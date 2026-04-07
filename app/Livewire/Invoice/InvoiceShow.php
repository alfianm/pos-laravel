<?php
declare(strict_types=1);
namespace App\Livewire\Invoice;

use App\Constants\InvoiceStatus;
use App\Constants\PaymentMethod;
use App\Constants\PaymentStatus;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InvoiceShow extends Component
{
    public Invoice $invoice;

    public bool $showPaymentModal = false;
    public array $paymentData = [
        'amount' => '',
        'method' => '',
        'reference_number' => '',
        'payment_date' => '',
        'notes' => '',
    ];

    public bool $showCancelModal = false;
    public string $cancelReason = '';

    protected $listeners = ['refreshInvoice' => '$refresh'];

    protected function rules(): array
    {
        return [
            'paymentData.amount' => 'required|numeric|min:0.01|max:' . $this->invoice->remaining_amount,
            'paymentData.method' => 'required|string|in:' . implode(',', array_column(PaymentMethod::cases(), 'value')),
            'paymentData.reference_number' => 'nullable|string|max:100',
            'paymentData.payment_date' => 'required|date',
            'paymentData.notes' => 'nullable|string|max:500',
        ];
    }

    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice;
        $this->paymentData['payment_date'] = now()->format('Y-m-d');
        $this->paymentData['amount'] = $invoice->remaining_amount;
    }

    public function openPaymentModal(): void
    {
        if ($this->invoice->status === InvoiceStatus::PAID) {
            $this->dispatch('notify', message: 'Invoice sudah lunas', type: 'error');
            return;
        }

        $this->showPaymentModal = true;
        $this->paymentData['amount'] = $this->invoice->remaining_amount;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->reset('paymentData');
        $this->paymentData['payment_date'] = now()->format('Y-m-d');
    }

    public function recordPayment(InvoiceService $service): void
    {
        $this->validate();

        try {
            $service->recordPayment($this->invoice, [
                'amount' => (float) $this->paymentData['amount'],
                'method' => $this->paymentData['method'],
                'reference_number' => $this->paymentData['reference_number'] ?: null,
                'payment_date' => $this->paymentData['payment_date'],
                'notes' => $this->paymentData['notes'] ?: null,
            ]);

            $this->invoice->refresh();
            $this->closePaymentModal();
            $this->dispatch('notify', message: 'Pembayaran berhasil dicatat', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal mencatat pembayaran: ' . $e->getMessage(), type: 'error');
        }
    }

    public function openCancelModal(): void
    {
        if ($this->invoice->status === InvoiceStatus::PAID) {
            $this->dispatch('notify', message: 'Invoice yang sudah lunas tidak dapat dibatalkan', type: 'error');
            return;
        }

        $this->showCancelModal = true;
    }

    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->cancelReason = '';
    }

    public function cancelInvoice(InvoiceService $service): void
    {
        $this->validate([
            'cancelReason' => 'nullable|string|max:500',
        ]);

        try {
            $result = $service->cancelInvoice($this->invoice, $this->cancelReason);

            if ($result) {
                $this->invoice->refresh();
                $this->closeCancelModal();
                $this->dispatch('notify', message: 'Invoice berhasil dibatalkan', type: 'success');
            } else {
                $this->dispatch('notify', message: 'Gagal membatalkan invoice', type: 'error');
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal membatalkan invoice: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.invoice.invoice-show', [
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }
}
