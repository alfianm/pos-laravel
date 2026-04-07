<?php

namespace App\Livewire\MasterData;

use App\Models\ImportBatch;
use App\Jobs\ProcessBulkImportJob;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class BulkImport extends Component
{
    use WithFileUploads;

    public $importType = 'products';
    public $file;
    public $batches = [];

    protected $rules = [
        'importType' => 'required|in:products,customers,suppliers',
        'file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
    ];

    public function mount()
    {
        $this->loadBatches();
    }

    public function loadBatches()
    {
        $this->batches = ImportBatch::where('tenant_id', auth()->user()->tenant_id)
            ->latest()
            ->limit(10)
            ->get();
    }

    public function startImport()
    {
        $this->validate();

        $user = auth()->user();
        $tenantId = $user->tenant_id;

        // Store the file
        $fileName = 'import_' . Str::random(10) . '.' . $this->file->getClientOriginalExtension();
        $path = $this->file->storeAs('imports', $fileName);

        // Count rows for progress (rough estimate)
        $fileHandle = fopen(Storage::path($path), 'r');
        $rowCount = 0;
        while (fgetcsv($fileHandle)) {
            $rowCount++;
        }
        fclose($fileHandle);

        // 1. Create Batch Header
        $batch = ImportBatch::create([
            'tenant_id' => $tenantId,
            'user_id' => $user->id,
            'import_type' => $this->importType,
            'file_path' => $path,
            'original_filename' => $this->file->getClientOriginalName(),
            'status' => 'pending',
            'total_rows' => $rowCount - 1, // Subtract header row
            'processed_rows' => 0,
            'success_count' => 0,
            'error_count' => 0,
        ]);

        // 2. Dispatch Job
        ProcessBulkImportJob::dispatch($batch);

        $this->reset(['file']);
        $this->loadBatches();
        
        session()->flash('message', 'Import ditambahkan ke antrian. Silakan refresh halaman untuk melihat status.');
    }

    public function render()
    {
        return view('livewire.master-data.bulk-import');
    }
}
