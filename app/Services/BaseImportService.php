<?php

namespace App\Services;

use App\Models\ImportBatch;
use Illuminate\Support\Facades\Storage;

abstract class BaseImportService
{
    protected $batch;

    /**
     * Start the import process.
     */
    public function import(ImportBatch $batch)
    {
        $this->batch = $batch;
        $this->batch->update(['status' => 'processing']);

        try {
            $filePath = Storage::path($batch->file_path);
            if (!file_exists($filePath)) {
                throw new \Exception("File tidak ditemukan: " . $batch->file_path);
            }

            $handle = fopen($filePath, 'r');
            $header = fgetcsv($handle); // Assuming first row is header

            $rowCount = 0;
            while (($row = fgetcsv($handle)) !== false) {
                $rowCount++;
                $data = array_combine($header, $row);
                
                try {
                    $this->processRow($data, $rowCount);
                    $this->batch->incrementSuccess();
                } catch (\Exception $e) {
                    $this->batch->incrementError();
                    $this->batch->addError($rowCount, $e->getMessage());
                }

                $this->batch->incrementProcessed();
            }

            fclose($handle);
            $this->batch->update(['status' => 'completed']);
        } catch (\Exception $e) {
            $this->batch->update(['status' => 'failed', 'meta' => ['error' => $e->getMessage()]]);
        }
    }

    /**
     * Process a single row of data.
     */
    abstract protected function processRow(array $data, int $rowNumber);
}
