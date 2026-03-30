<?php

namespace App\Models;


use App\Model;
use App\Service\Converter;
use Dotenv\Parser\Value;
use PDOException;

class Transactions extends Model
{
    public function add()
    {
        try {
            $this->db->prepare('INSERT INTO transactions (Date, `Check`, Description, Amount) VALUES (?, ?, ?, ?)')
                ->execute([
                    (new \DateTime($_POST['Date']))->format('Y-m-d'),
                    $_POST['Check'],
                    $_POST['Description'],
                    Converter::convertAmountToInt($_POST['Amount']) // Already using Converter
                ]);
            return
                [
                    'success' => true,
                    'message' => 'Data added successfully !'
                ];
        } catch (PDOException $th) {
            return
                [
                    'success' => false,
                    'message' => $th->getMessage()
                ];
        }
    }
    public function addCombine($Transactions)
    {
        try {
            $this->db->prepare('INSERT INTO transactions (Date, `Check`, Description, Amount) VALUES (?, ?, ?, ?)')
                ->execute([
                    (new \DateTime($Transactions['Date']))->format('Y-m-d'),
                    $Transactions['Check'],
                    $Transactions['Description'],
                    Converter::convertAmountToInt($Transactions['Amount']) // Use Converter for CSV imported amounts
                ]);
            return
                [
                    'success' => true,
                    'message' => 'Data added successfully !'
                ];
        } catch (PDOException $th) {
            return
                [
                    'success' => false,
                    'message' => $th->getMessage()
                ];
        }
    }
    public function all()
    {
        $stmt = $this->db->prepare('SELECT * FROM transactions');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
