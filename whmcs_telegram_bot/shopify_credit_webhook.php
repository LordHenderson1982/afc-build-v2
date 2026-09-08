<?php
/**
 * Shopify to WHMCS Credit Automation - DEBUG VERSION
 * 
 * This version adds extensive logging to diagnose the issue
 */

$logFile = '/home/apfkgyeksbf/public_html/plans/shopify_credit.log';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Debug: log the raw input
$debugLog = "\n=== " . date('Y-m-d H:i:s') . " ===\n";
$debugLog .= "RAW: " . substr($json, 0, 1000) . "\n";
$debugLog .= "Parsed JSON: " . json_encode($data) . "\n";
file_put_contents($logFile, $debugLog, FILE_APPEND);

// If no data, return early
if (empty($data)) {
    echo "No data received";
    exit;
}

// Config - WHMCS API
$whmcs_api = array(
    'url' => 'https://veilhosts.shop/includes/api.php',
    'identifier' => 'WhxUDRFGPYKX8OibgI0gJwo7XAnUdJfZ',
    'secret' => 'GhnpSHejTbuAbmsNIQ0M38yJ3tHrzPIg'
);

// Only process paid orders - Shopify sends 'paid'
$financialStatus = $data['financial_status'] ?? '';
$debugLog = "financial_status = '$financialStatus'\n";
file_put_contents($logFile, $debugLog, FILE_APPEND);

if ($financialStatus !== 'paid') {
    echo "Not a paid order (status: $financialStatus)";
    exit;
}

// Get email - try multiple possible fields
$email = $data['email'] ?? $data['customer']['email'] ?? $data['contact_email'] ?? '';
$debugLog = "email = '$email'\n";
file_put_contents($logFile, $debugLog, FILE_APPEND);

if (empty($email)) {
    echo "No email found";
    exit;
}

// Get total amount
$totalPrice = $data['total_price'] ?? $data['total_price_set']['shop_money']['amount'] ?? 0;
$amount = (float)$totalPrice;

$debugLog = "amount = $amount\n";
file_put_contents($logFile, $debugLog, FILE_APPEND);

if ($amount <= 0) {
    echo "No amount";
    exit;
}

// Find WHMCS client by email
$client = whmcs_api_call('GetClients', array('search' => $email));

$debugLog = "GetClients response: " . json_encode($client) . "\n";
file_put_contents($logFile, $debugLog, FILE_APPEND);

if (empty($client['clients']['client'])) {
    echo "Client not found: $email";
    file_put_contents($logFile, date('Y-m-d H:i:s') . " | Client not found: $email\n", FILE_APPEND);
    exit;
}

// Get client ID
$clients = $client['clients']['client'];
if (isset($clients[0])) {
    $clientId = $clients[0]['id'];
} else {
    $clientId = $clients['id'];
}

$debugLog = "clientId = $clientId\n";
file_put_contents($logFile, $debugLog, FILE_APPEND);

// Add credit to client
$result = whmcs_api_call('AddCredit', array(
    'clientid' => $clientId,
    'amount' => $amount,
    'description' => 'Shopify order credit'
));

$debugLog = "AddCredit response: " . json_encode($result) . "\n";
file_put_contents($logFile, $debugLog, FILE_APPEND);

$logMsg = date('Y-m-d H:i:s') . " | Added $amount credit to client $clientId ($email)\n";
file_put_contents($logFile, $logMsg, FILE_APPEND);

echo "OK - Added $amount credit to $email";

/**
 * Make WHMCS API call
 */
function whmcs_api_call($action, $postData = array()) {
    global $whmcs_api;
    
    $postData = array_merge($postData, array(
        'action' => $action,
        'identifier' => $whmcs_api['identifier'],
        'secret' => $whmcs_api['secret'],
        'responsetype' => 'json'
    ));
    
    $ch = curl_init($whmcs_api['url']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
