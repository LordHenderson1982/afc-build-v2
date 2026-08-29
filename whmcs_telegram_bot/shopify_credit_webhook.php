<?php
/**
 * Shopify to WHMCS Credit Automation
 * 
 * Set up in Shopify:
 * 1. Settings → Notifications → Webhooks
 * 2. Create webhook: Order created + Paid
 * 3. URL: https://veilhosts.shop/plans/shopify_credit_webhook.php
 * 
 * This script receives the webhook, finds the WHMCS client by email, and adds credit
 */

// Config - WHMCS API (from your existing setup)
$whmcs_api = array(
    'url' => 'https://veilhosts.shop/includes/api.php',
    'identifier' => 'WhxUDRFGPYKX8OibgI0gJwo7XAnUdJfZ',
    'secret' => 'GhnpSHejTbuAbmsNIQ0M38yJ3tHrzPIg'
);

// Log file for debugging
$logFile = '/home/apfkgyeksbf/public_html/plans/shopify_credit.log';

// Get the JSON from Shopify
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Log incoming webhook
$logMsg = date('Y-m-d H:i:s') . " | Received: " . substr($json, 0, 500) . "\n";
file_put_contents($logFile, $logMsg, FILE_APPEND);

// Only process paid orders
$financialStatus = $data['financial_status'] ?? '';
if ($financialStatus !== 'paid' && $financial_status !== 'paid') {
    echo "Not a paid order, ignoring";
    exit;
}

// Get email from Shopify order
$email = $data['email'] ?? '';
if (empty($email)) {
    echo "No email found";
    file_put_contents($logFile, date('Y-m-d H:i:s') . " | No email in order\n", FILE_APPEND);
    exit;
}

// Get total amount (in cents, convert to dollars)
$totalPrice = $data['total_price'] ?? 0;
$amount = (float)$totalPrice;

if ($amount <= 0) {
    echo "No amount";
    exit;
}

// Find WHMCS client by email
$client = whmcs_api_call('GetClients', array('search' => $email));

if (empty($client['clients']['client'])) {
    $logMsg = date('Y-m-d H:i:s') . " | Client not found: $email\n";
    file_put_contents($logFile, $logMsg, FILE_APPEND);
    echo "Client not found";
    exit;
}

// Get client ID
$clients = $client['clients']['client'];
if (isset($clients[0])) {
    $clientId = $clients[0]['id'];
} else {
    $clientId = $clients['id'];
}

// Add credit to client
$result = whmcs_api_call('AddCredit', array(
    'clientid' => $clientId,
    'amount' => $amount,
    'description' => 'Shopify order credit'
));

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
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
