<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStage;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CrmConversionReport extends Component
{
    use WithPagination;

    public $date_from;

    public $date_to;

    public $selected_branch = '';

    public $selected_source = '';

    public $selected_stage = '';

    public $branches;

    public $sources;

    public $stages;

    protected $queryString = ['date_from', 'date_to', 'selected_branch', 'selected_source', 'selected_stage'];

    public function mount()
    {
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->endOfDay()->format('Y-m-d');
        $this->branches = Branch::where('tenant_id', auth()->user()->tenant_id)->get();
        $this->sources = LeadSource::where('tenant_id', auth()->user()->tenant_id)->get();
        $this->stages = LeadStage::where('tenant_id', auth()->user()->tenant_id)->get();
    }

    public function getSourceConversionProperty()
    {
        $leads = Lead::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->date_from, fn ($q) => $q->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('created_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_source, fn ($q) => $q->where('lead_source_id', $this->selected_source))
            ->when($this->selected_stage, fn ($q) => $q->where('lead_stage_id', $this->selected_stage))
            ->select('lead_source_id', DB::raw('COUNT(*) as total_leads'), DB::raw("SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted_count"), DB::raw("SUM(CASE WHEN status = 'lost' THEN 1 ELSE 0 END) as lost_count"))
            ->with('source')
            ->groupBy('lead_source_id')
            ->get()
            ->map(function ($item) {
                $item->conversion_rate = $item->total_leads > 0 ? round(($item->converted_count / $item->total_leads) * 100, 1) : 0;

                return $item;
            })
            ->sortByDesc('total_leads');

        return $leads;
    }

    public function getStageDistributionProperty()
    {
        return Lead::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->date_from, fn ($q) => $q->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('created_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_source, fn ($q) => $q->where('lead_source_id', $this->selected_source))
            ->when($this->selected_stage, fn ($q) => $q->where('lead_stage_id', $this->selected_stage))
            ->select('lead_stage_id', 'status', DB::raw('COUNT(*) as count'))
            ->with('stage')
            ->groupBy('lead_stage_id', 'status')
            ->get();
    }

    public function getGrandTotalsProperty()
    {
        $stats = Lead::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->date_from, fn ($q) => $q->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('created_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_source, fn ($q) => $q->where('lead_source_id', $this->selected_source))
            ->when($this->selected_stage, fn ($q) => $q->where('lead_stage_id', $this->selected_stage))
            ->select(DB::raw('COUNT(*) as total_leads'), DB::raw("SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count"), DB::raw("SUM(CASE WHEN status = 'contacted' THEN 1 ELSE 0 END) as contacted_count"), DB::raw("SUM(CASE WHEN status = 'qualified' THEN 1 ELSE 0 END) as qualified_count"), DB::raw("SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted_count"), DB::raw("SUM(CASE WHEN status = 'lost' THEN 1 ELSE 0 END) as lost_count"))
            ->first();

        $stats->conversion_rate = $stats->total_leads > 0 ? round(($stats->converted_count / $stats->total_leads) * 100, 1) : 0;
        $stats->loss_rate = $stats->total_leads > 0 ? round(($stats->lost_count / $stats->total_leads) * 100, 1) : 0;

        return $stats;
    }

    public function getBranchPerformanceProperty()
    {
        return Lead::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($this->date_from, fn ($q) => $q->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('created_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_source, fn ($q) => $q->where('lead_source_id', $this->selected_source))
            ->when($this->selected_stage, fn ($q) => $q->where('lead_stage_id', $this->selected_stage))
            ->select('branch_id', DB::raw('COUNT(*) as total_leads'), DB::raw("SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted_count"))
            ->with('branch')
            ->groupBy('branch_id')
            ->get()
            ->map(function ($item) {
                $item->conversion_rate = $item->total_leads > 0 ? round(($item->converted_count / $item->total_leads) * 100, 1) : 0;

                return $item;
            })
            ->sortByDesc('total_leads');
    }

    public function getRecentConversionsProperty()
    {
        return Lead::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'converted')
            ->with(['source', 'stage', 'branch', 'convertedCustomer'])
            ->when($this->date_from, fn ($q) => $q->whereDate('converted_at', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('converted_at', '<=', $this->date_to))
            ->when($this->selected_branch, fn ($q) => $q->where('branch_id', $this->selected_branch))
            ->when($this->selected_source, fn ($q) => $q->where('lead_source_id', $this->selected_source))
            ->when($this->selected_stage, fn ($q) => $q->where('lead_stage_id', $this->selected_stage))
            ->orderBy('converted_at', 'desc')
            ->paginate(15);
    }

    public function exportCsv()
    {
        $filename = 'crm_conversion_'.$this->date_from.'_to_'.$this->date_to.'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Source', 'Total Leads', 'Converted', 'Lost', 'Conversion Rate']);

            foreach ($this->sourceConversion as $item) {
                fputcsv($file, [
                    $item->source->name ?? '-',
                    $item->total_leads,
                    $item->converted_count,
                    $item->lost_count,
                    $item->conversion_rate.'%',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function resetFilters()
    {
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->endOfDay()->format('Y-m-d');
        $this->selected_branch = '';
        $this->selected_source = '';
        $this->selected_stage = '';
    }

    public function getStatusLabel($status)
    {
        return match ($status) {
            'new' => 'New',
            'contacted' => 'Contacted',
            'qualified' => 'Qualified',
            'converted' => 'Converted',
            'lost' => 'Lost',
            default => ucfirst($status),
        };
    }

    public function getStatusColor($status)
    {
        return match ($status) {
            'new' => 'bg-blue-100 text-blue-700',
            'contacted' => 'bg-amber-100 text-amber-700',
            'qualified' => 'bg-cyan-100 text-cyan-700',
            'converted' => 'bg-emerald-100 text-emerald-700',
            'lost' => 'bg-rose-100 text-rose-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function render()
    {
        return view('livewire.reports.crm-conversion-report', [
            'grandTotals' => $this->grandTotals,
            'sourceConversion' => $this->sourceConversion,
            'branchPerformance' => $this->branchPerformance,
            'recentConversions' => $this->recentConversions,
        ]);
    }
}
