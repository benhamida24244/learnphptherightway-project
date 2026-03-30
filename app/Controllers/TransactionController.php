<?php

namespace App\Controllers;

use App\Models\Transactions;
use App\Service\UploadDirectary;
use App\View;

class TransactionController {
    public function show(): View
    {
        return $this->renderTransactions();
    }

    public function add()
    {
        if (
            isset($_POST['Date'], $_POST['Description'], $_POST['Amount'])
            && trim((string) $_POST['Date']) !== ''
            && trim((string) $_POST['Description']) !== ''
            && trim((string) $_POST['Amount']) !== ''
        )
            {
                $Transaction = new Transactions();
                $result =$Transaction->add();
                    return json_encode($result);
            } 
            return json_encode([
                'success' => false,
                'message' => 'All Data required'
             ]);
    }
    public function uploadFile()
    {
        $uploadedFiles = $this->normalizeUploadedFiles($_FILES['csv-files'] ?? null);

        if ($uploadedFiles === []) {
            return $this->renderTransactions(null, ['Please select at least one CSV file.']);
        }

        $importedFilesCount = 0;
        $uploadErrors = [];

        foreach ($uploadedFiles as $file) {
            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                $uploadErrors[] = sprintf('Failed to upload "%s".', $file['name'] ?? 'Unknown file');
                continue;
            }

            $uploadDirectory = new UploadDirectary(__DIR__ . '/../../public/storage/', $file);
            $uploadResult = $uploadDirectory->upload();

            if (! $uploadResult['success']) {
                $uploadErrors[] = sprintf('%s: %s', $file['name'], $uploadResult['message']);
                continue;
            }

            $handle = fopen($uploadDirectory->filePath, 'r');

            if ($handle === false) {
                $uploadErrors[] = sprintf('Unable to read "%s".', $file['name']);
                continue;
            }

            fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                $transaction = new Transactions();
                $transaction->addCombine($this->extractTransactionsFromFile($row));
            }

            fclose($handle);
            $importedFilesCount++;
        }

        $message = null;

        if ($importedFilesCount > 0) {
            $message = $importedFilesCount === 1
                ? '1 CSV file uploaded and imported successfully.'
                : sprintf('%d CSV files uploaded and imported successfully.', $importedFilesCount);
        }

        return $this->renderTransactions($message, $uploadErrors);
    }
    public function extractTransactionsFromFile($row)
    {
        return [
            'Date' => $row[0],
            'Check' => $row[1],
            'Description' => $row[2],
            'Amount' => $row[3] // Pass the raw string; parsing will be handled in the model
        ];
    }

    private function renderTransactions(?string $uploadMessage = null, array $uploadErrors = []): View
    {
        $transactions = (new Transactions())->all();
        $analytics = $this->buildAnalytics($transactions);

        return View::make('transactions', [
            'transactions' => $transactions,
            'analytics' => $analytics,
            'uploadMessage' => $uploadMessage,
            'uploadErrors' => $uploadErrors,
        ]);
    }

    private function buildAnalytics(array $transactions): array
    {
        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($transactions as $transaction) {
            if ($transaction['Amount'] > 0) {
                $totalIncome += (float) $transaction['Amount'];
                continue;
            }

            $totalExpense += (float) $transaction['Amount'];
        }

        return [
            'TotalIncome' => $totalIncome,
            'TotalExpense' => $totalExpense,
            'TotalNet' => $totalIncome + $totalExpense
        ];
    }

    private function normalizeUploadedFiles(?array $files): array
    {
        if ($files === null || ! isset($files['name'])) {
            return [];
        }

        if (! is_array($files['name'])) {
            return [$files];
        }

        $normalizedFiles = [];

        foreach ($files['name'] as $index => $name) {
            $normalizedFiles[] = [
                'name' => $name,
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$index] ?? 0,
            ];
        }

        return array_filter(
            $normalizedFiles,
            fn (array $file): bool => ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
        );
    }
    }
