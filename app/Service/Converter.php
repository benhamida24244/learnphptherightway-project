<?php

namespace App\Service;

class Converter
{
    public static function convertAmountToInt(string $amountString): int
    {
        // Remove currency symbols ($) and thousands separators (,)
        $cleanedAmount = str_replace(['$', ','], '', $amountString);

        // Determine if the amount is negative
        $isNegative = false;
        if (str_starts_with($cleanedAmount, '-')) {
            $isNegative = true;
            $cleanedAmount = substr($cleanedAmount, 1); // Remove the leading minus sign
        } elseif (str_starts_with($cleanedAmount, '(') && str_ends_with($cleanedAmount, ')')) {
            // Handle amounts in parentheses, e.g., "(100.00)"
            $isNegative = true;
            $cleanedAmount = trim($cleanedAmount, '()');
        }

        // Convert to float to handle decimal values
        $numericAmount = (float) $cleanedAmount;

        // Apply the negative sign if it was detected
        if ($isNegative) {
            $numericAmount = -$numericAmount;
        }

        // Round to the nearest integer as the database column 'Amount' is INT
        return (int) round($numericAmount);
    }
}
