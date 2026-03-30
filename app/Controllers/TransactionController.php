<?php

namespace App\Controllers;

use App\Models\Transactions;
use App\Service\UploadDirectary;

class TransactionController {
    public function upload() {
        $file = $_FILES['transaction_file'];
        $filePath = __DIR__ . '/../../';
    }


    public function add()
    {
        if(isset($_POST['Date']) && isset($_POST['Check']) && isset($_POST['Amount']))
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
        $uploadDirectory = new UploadDirectary(__DIR__ . '/../../public/storage/', $_FILES['csv-file']);
        $uploadDirectory->upload();
        $handle = fopen($uploadDirectory->filePath , 'r');
        fgetcsv($handle);
        while(($row = fgetcsv($handle)) !== false)
            {

                $transaction = new Transactions();
                $transaction->addCombine($this->extractTransactionsFromFile($row));
            }
        fclose($handle);
        return json_encode([
            'success' => true,
            'message' => 'File uploaded and transactions added successfully !'
        ]);

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
    public static function show()
    {
        $Transactions = new Transactions();
        $data = $Transactions->all();
        return json_encode($data);
    }
    public static function analytics()
    {
            $totalIncome = 0;
            $totalExpense = 0;
            $Transactions = new Transactions();
            $data = $Transactions->all();
            foreach($data as $transaction)
                {
                    if($transaction['Amount'] > 0)
                        {
                           $totalIncome += $transaction['Amount']; 
                        }
                    else
                        {
                           $totalExpense += $transaction['Amount']; 
                        }
                }
            return [
                'TotalIncome' => $totalIncome,
                'TotalExpense' => $totalExpense,
                'TotalNet' => $totalIncome + $totalExpense
            ];
    }
    }