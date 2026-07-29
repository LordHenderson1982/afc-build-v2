<?php
/**
 * Combined Report: Paid Invoices & Pending Orders
 * Protected directory recommended
 */

$db_host = 'localhost';
$db_username = 'apfkgyek_whmc291';
$db_password = 't0pp65)SX!';
$db_name = 'apfkgyek_whmc291';

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get active tab from query string
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'invoices';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports - Invoices & Orders</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        h1 { color: #333; }
        .tabs { margin-bottom: 20px; }
        .tabs a { 
            display: inline-block; 
            padding: 10px 20px; 
            margin-right: 5px; 
            background: #ddd; 
            color: #333; 
            text-decoration: none; 
            border-radius: 5px 5px 0 0;
        }
        .tabs a.active { background: #4CAF50; color: white; }
        .tabs a:hover { background: #ccc; }
        .tabs a.active:hover { background: #45a049; }
        table { border-collapse: collapse; width: 100%; background: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        a { text-decoration: none; color: #4CAF50; }
        a:hover { text-decoration: underline; }
        .summary { 
            background: #e7f3e7; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }
        .count { font-size: 1.2em; font-weight: bold; }
        .empty { color: #888; font-style: italic; }
        .back-link { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="back-link">
        <a href="?">&larr; Refresh</a>
    </div>
    
    <h1>WHMCS Reports</h1>
    
    <div class="tabs">
        <a href="?tab=invoices" class="<?php echo $active_tab == 'invoices' ? 'active' : ''; ?>">Paid Invoices</a>
        <a href="?tab=orders" class="<?php echo $active_tab == 'orders' ? 'active' : ''; ?>">Pending Orders</a>
    </div>
    
    <?php if ($active_tab == 'invoices'): ?>
    <div class="summary">
        <span class="count"><?php
            $count_sql = "SELECT COUNT(*) as cnt FROM tblinvoices WHERE status = 'Paid'";
            $count_result = $conn->query($count_sql);
            $count_row = $count_result->fetch_assoc();
            echo $count_row['cnt'];
        ?></span> paid invoices total
    </div>
    
    <h2>Last 30 Paid Invoices (Newest First)</h2>
    <table>
        <tr>
            <th>Invoice #</th>
            <th>Client Name</th>
            <th>Date Paid</th>
            <th>Total</th>
            <th>Payment</th>
        </tr>
        
        <?php
        $sql = "SELECT i.`id`, i.`invoicenum`, i.`duedate`, i.`datepaid`, i.`total`, i.`status`, i.`userid`, i.`paymentmethod`, 
                       u.`firstname`, u.`lastname`, u.`companyname`
                FROM `tblinvoices` i
                LEFT JOIN `tblclients` u ON i.`userid` = u.`id`
                WHERE i.`status` = 'Paid' 
                ORDER BY i.`datepaid` DESC
                LIMIT 30";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
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
                
                $clientLink = '/plans/Zxn8u4jnn/clientssummary.php?userid=' . $row['userid'];
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['invoicenum'] ?: $row['id']) . "</td>";
                echo "<td><a href=\"" . $clientLink . "\" target=\"_blank\">" . htmlspecialchars($clientName) . "</a></td>";
                echo "<td>" . htmlspecialchars($row['datepaid']) . "</td>";
                echo "<td>$" . number_format($row['total'], 2) . "</td>";
                echo "<td>" . htmlspecialchars($row['paymentmethod']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan=\"5\" class=\"empty\">No paid invoices found</td></tr>";
        }
        ?>
    </table>
    
    <?php elseif ($active_tab == 'orders'): ?>
    <div class="summary">
        <span class="count"><?php
            $count_sql = "SELECT COUNT(*) as cnt FROM tblorders WHERE status = 'Pending'";
            $count_result = $conn->query($count_sql);
            $count_row = $count_result->fetch_assoc();
            echo $count_row['cnt'];
        ?></span> pending orders awaiting fulfillment
    </div>
    
    <h2>Pending Orders</h2>
    <table>
        <tr>
            <th>Order #</th>
            <th>Client Name</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Payment</th>
            <th>Action</th>
        </tr>
        
        <?php
        $sql = "SELECT o.`id`, o.`ordernum`, o.`date`, o.`amount`, o.`status`, o.`userid`, o.`paymentmethod`,
                       u.`firstname`, u.`lastname`, u.`companyname`
                FROM `tblorders` o
                LEFT JOIN `tblclients` u ON o.`userid` = u.`id`
                WHERE o.`status` = 'Pending'
                ORDER BY o.`date` DESC";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
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
                
                $clientLink = '/plans/Zxn8u4jnn/clientssummary.php?userid=' . $row['userid'];
                $orderLink = '/plans/Zxn8u4jnn_orders/status.php?orderid=' . $row['id'];
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['ordernum'] ?: $row['id']) . "</td>";
                echo "<td><a href=\"" . $clientLink . "\" target=\"_blank\">" . htmlspecialchars($clientName) . "</a></td>";
                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                echo "<td>$" . number_format($row['amount'], 2) . "</td>";
                echo "<td>" . htmlspecialchars($row['paymentmethod']) . "</td>";
                echo "<td><a href=\"" . $orderLink . "\" target=\"_blank\">View Order</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan=\"6\" class=\"empty\">No pending orders</td></tr>";
        }
        ?>
    </table>
    <?php endif; ?>
    
</body>
</html>

<?php
$conn->close();
?>