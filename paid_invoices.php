<?php
/**
 * Paid Invoices Report - Sorted by Date Paid
 * With done/unchecked tracking
 */

$db_host = 'localhost';
$db_username = 'apfkgyek_whmc291';
$db_password = 't0pp65)SX!';
$db_name = 'apfkgyek_whmc291';

$handled_file = __DIR__ . '/handled_invoices.json';

// Load handled invoices from JSON file
$handled = [];
if (file_exists($handled_file)) {
    $handled = json_decode(file_get_contents($handled_file), true) ?: [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_invoice'])) {
    $invoice_id = $_POST['invoice_id'];
    if (isset($handled[$invoice_id])) {
        unset($handled[$invoice_id]); // Uncheck
    } else {
        $handled[$invoice_id] = date('Y-m-d H:i:s'); // Check and store timestamp
    }
    file_put_contents($handled_file, json_encode($handled, JSON_PRETTY_PRINT));
    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get last 30 paid invoices
$sql = "SELECT i.`id`, i.`invoicenum`, i.`duedate`, i.`datepaid`, i.`total`, i.`status`, i.`userid`, i.`paymentmethod`, 
               u.`firstname`, u.`lastname`, u.`companyname`
        FROM `tblinvoices` i
        LEFT JOIN `tblclients` u ON i.`userid` = u.`id`
        WHERE i.`status` = 'Paid' 
        ORDER BY i.`datepaid` DESC
        LIMIT 30";

$result = $conn->query($sql);

if (!$result) {
    echo "Query Error: " . $conn->error;
    exit;
}

// Count unchecked
$total = $result->num_rows;
$checked_count = 0;
$unchecked_count = 0;

// Reset result pointer
$result->data_seek(0);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Paid Invoices - Mark as Done</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        h1 { color: #333; }
        .summary { 
            background: #e7f3e7; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
            font-size: 1.1em;
        }
        .summary.unchecked { 
            background: #fff3e7; 
            border-left-color: #ff9800;
        }
        .count { font-size: 1.3em; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; background: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr.checked { 
            background-color: #e8e8e8 !important; 
            color: #888;
        }
        tr.checked td { 
            text-decoration: line-through; 
        }
        a { text-decoration: none; color: #4CAF50; }
        a:hover { text-decoration: underline; }
        .checkbox-form { display: inline; margin: 0; padding: 0; }
        .checkbox-btn {
            background: none;
            border: 2px solid #4CAF50;
            color: #4CAF50;
            padding: 5px 12px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
        }
        .checkbox-btn.checked {
            background: #4CAF50;
            color: white;
        }
        .checkbox-btn:hover {
            background: #45a049;
            color: white;
        }
        .empty { color: #888; font-style: italic; }
        .client-link { color: #4CAF50; font-weight: 500; }
        .client-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Paid Invoices - Mark as Done</h1>
    
    <?php
    // Calculate counts
    $result->data_seek(0);
    while($row = $result->fetch_assoc()) {
        if (isset($handled[$row['id']])) {
            $checked_count++;
        } else {
            $unchecked_count++;
        }
    ?>
    <?php } ?>
    
    <div class="summary <?php echo $unchecked_count > 0 ? 'unchecked' : ''; ?>">
        📋 <strong><?php echo $unchecked_count; ?></strong> unchecked / <strong><?php echo $checked_count; ?></strong> done / <strong><?php echo $total; ?></strong> total
        &nbsp;&nbsp;
        <a href="?refresh=1" style="font-size: 0.9em;">🔄 Refresh list</a>
    </div>
    
    <table>
        <tr>
            <th style="width: 60px;">Done</th>
            <th>Invoice #</th>
            <th>Client Name</th>
            <th>Date Paid</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Checked</th>
        </tr>
        
        <?php
        $result->data_seek(0);
        while($row = $result->fetch_assoc()) {
            $invoice_id = $row['id'];
            $is_checked = isset($handled[$invoice_id]);
            $checked_date = $is_checked ? $handled[$invoice_id] : '';
            
            // Build client name from firstname + lastname, fallback to companyname, then user ID
            $clientName = '';
            if (!empty($row['firstname']) || !empty($row['lastname'])) {
                $clientName = trim($row['firstname'] . ' ' . $row['lastname']);
            }
            if (empty($clientName) && !empty($row['companyname'])) {
                $clientName = $row['companyname'];
            }
            if (empty($clientName)) {
                $clientName = 'User #' . $row['userid'];
            }
            
            // Client summary link
            $clientLink = 'https://veilhosts.shop/plans/Zxn8u4jnn/clientssummary.php?userid=' . $row['userid'];
            
            echo "<tr class=\"" . ($is_checked ? 'checked' : '') . "\">";
            echo "<td>";
            echo "<form method=\"POST\" class=\"checkbox-form\">";
            echo "<input type=\"hidden\" name=\"invoice_id\" value=\"" . $invoice_id . "\">";
            echo "<button type=\"submit\" name=\"toggle_invoice\" class=\"checkbox-btn " . ($is_checked ? 'checked' : '') . "\">";
            echo $is_checked ? '✓' : '○';
            echo "</button>";
            echo "</form>";
            echo "</td>";
            echo "<td>" . htmlspecialchars($row['invoicenum'] ?: $row['id']) . "</td>";
            echo "<td><a href=\"" . $clientLink . "\" target=\"_blank\" class=\"client-link\">" . htmlspecialchars($clientName) . "</a></td>";
            echo "<td>" . htmlspecialchars($row['datepaid']) . "</td>";
            echo "<td>$" . number_format($row['total'], 2) . "</td>";
            echo "<td>" . htmlspecialchars($row['paymentmethod']) . "</td>";
            echo "<td>" . htmlspecialchars($checked_date) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>