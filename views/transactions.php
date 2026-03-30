<!DOCTYPE html>
<html>
    <head>
        <title>Transactions</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 30px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                text-align: center;
                margin-bottom: 30px;
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

            .upload-section {
                max-width: 500px;
            }

            .status-message {
                padding: 10px;
                margin-bottom: 15px;
                background: #ebf9f1;
                border: 1px solid #b7ebc6;
                color: #1f7a3d;
            }

            .error-list {
                margin: 0 0 15px;
                padding: 10px 15px;
                background: #fff1f0;
                border: 1px solid #ffccc7;
                color: #a8071a;
            }
        </style>
    </head>
    <body>
<?php
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
$uploadAction = ($basePath === '' ? '' : $basePath) . '/upload';
?>
<?php if (! empty($uploadMessage)): ?>
        <div class="status-message"><?= htmlspecialchars($uploadMessage) ?></div>
<?php endif; ?>

<?php if (! empty($uploadErrors)): ?>
        <ul class="error-list">
<?php foreach ($uploadErrors as $uploadError): ?>
            <li><?= htmlspecialchars($uploadError) ?></li>
<?php endforeach; ?>
        </ul>
<?php endif; ?>

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
<?php foreach ($transactions as $transaction): ?>
        <tr>
            <td><?= htmlspecialchars($transaction['Date']) ?></td>
            <td><?= htmlspecialchars((string) ($transaction['Check'] ?? '')) ?></td>
            <td><?= htmlspecialchars($transaction['Description']) ?></td>
            
            <td style="color: <?= $transaction['Amount'] >= 0 ? 'green' : 'red' ?>">
                <?= ($transaction['Amount'] < 0 ? '-' : '') . '$' . number_format(abs((float) $transaction['Amount']), 2) ?>
            </td>
        </tr>
    <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total Income:</th>
                    <td>$<?= number_format((float) $analytics['TotalIncome'], 2) ?></td>
                </tr>
                <tr>
                    <th colspan="3">Total Expense:</th>
                    <td>-$<?= number_format(abs((float) $analytics['TotalExpense']), 2) ?></td>
                </tr>
                <tr>
                    <th colspan="3">Net Total:</th>
                    <td>$<?= number_format((float) $analytics['TotalNet'], 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="upload-section">
            <h2>Upload Transactions CSV</h2>
            <form id="upload-form" method="POST" enctype="multipart/form-data" action="<?= htmlspecialchars($uploadAction) ?>">
                <input type="file" name="csv-files[]" accept=".csv" multiple required>
                <button type="submit">Upload</button>
            </form>
        </div>
    </body>
</html>
