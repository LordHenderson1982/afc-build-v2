<?php
/**
 * Unchecked invoice count API - uses WHMCS API, no DB credentials
 */

$handled_file = __DIR__ . '/handled_invoices.json';

// Load handled invoices
$handled = [];
if (file_exists($handled_file)) {
    $handled = json_decode(file_get_contents($handled_file), true) ?: [];
}

// Use WHMCS API
$api_url = 'https://veilhosts.shop/includes/api.php';
$identifier = 'WhxUDRFGPYKX8OibgI0gJwo7XAnUdJfZ';
$secret = 'GhnpSHejTbuAbmsNIQ0M38yJ3tHrzPIg';

$post_data = [
    'action' => 'GetInvoices',
    'status' => 'Paid',
    'limit' => 30,
    'identifier' => $identifier,
    'secret' => $secret,
    'responsetype' => 'xml'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    echo "0";
    exit;
}

// Parse XML response
$xml = simplexml_load_string($response);
if (!$xml || !isset($xml->invoice)) {
    echo "0";
    exit;
}

$unchecked = 0;
foreach ($xml->invoice as $invoice) {
    $invoice_id = (string)$invoice['id'];
    if (!isset($handled[$invoice_id])) {
        $unchecked++;
    }
}

echo $unchecked;
?>