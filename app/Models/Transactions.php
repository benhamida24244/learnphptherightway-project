<?php

namespace App\Models;

use App\Model;
use Dotenv\Parser\Value;
use PDOException;

class Transactions extends Model
{
    /**
     * Cleans and parses an amount string into an integer.
     * Handles various formats by extracting the numeric part and rounding.
     *
     * @param string $amountString The raw amount string (e.g., "$1,234.56", "Expense 50.25", "100.00").
     * @return int The parsed amount as an integer.
     */
    private function parseAmount(string $amountString): int
    {
        // Remove thousands separators (commas)
        $cleanedAmount = str_replace(',', '', $amountString);

        // Extract the numeric part using regex: optional minus, digits, optional decimal with digits.
        preg_match('/-?\d+(\.\d+)?/', $cleanedAmount, $matches);

        $numericAmount = 0.0;
        if (!empty($matches[0])) {
            $numericAmount = (float) $matches[0];
        }
        // Round to the nearest integer, as the database column 'Amount' is INT.
        return (int) round($numericAmount);
    }

    public function add()
    {
        try {
            $this->db->prepare('INSERT INTO transactions (Date, `Check`, Description, Amount) VALUES (?, ?, ?, ?)')
                ->execute([
                    (new \DateTime($_POST['Date']))->format('Y-m-d'),
                    $_POST['Check'],
                    $_POST['Description'],
                    $this->parseAmount($_POST['Amount'])
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
                    $this->parseAmount($Transactions['Amount'])
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
