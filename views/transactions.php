<?php


use App\Controllers\TransactionController;

$data = json_decode(TransactionController::show(), true);
$analytics = TransactionController::analytics();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Transactions</title>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                text-align: center;
            }

            table tr th, table tr td {
                padding: 5px;
                border: 1px #eee solid;
            }

            tfoot tr th, tfoot tr td {
                font-size: 20px;
            }

            tfoot tr th {
                text-align: right;
            }
        </style>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Check #</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- TODO -->
<?php foreach ($data as $transaction): ?>
        <tr>
            <td><?= htmlspecialchars($transaction['Date']) ?></td>
            <td><?= htmlspecialchars($transaction['Check']) ?></td>
            <td><?= htmlspecialchars($transaction['Description']) ?></td>
            
            <td style="color: <?= $transaction['Amount'] >= 0 ? 'green' : 'red' ?>">
                <?= ($transaction['Amount'] < 0 ? '-' : '') . '$' . abs($transaction['Amount']) ?>
            </td>
        </tr>
    <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total Income:</th>
                    <td><?= $analytics['TotalIncome'] ?></td>
                </tr>
                <tr>
                    <th colspan="3">Total Expense:</th>
                    <td><?= $analytics['TotalExpense'] ?></td>
                </tr>
                <tr>
                    <th colspan="3">Net Total:</th>
                    <td><?= $analytics['TotalNet'] ?></td>
                </tr>
            </tfoot>
        </table>
    </body>
</html>
