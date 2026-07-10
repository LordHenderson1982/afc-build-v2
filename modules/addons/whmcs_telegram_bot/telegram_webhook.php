<?php
/**
 * WHMCS Telegram Bot - Lightweight Webhook Handler
 * Connects directly to DB without full WHMCS bootstrap
 */

// Database configuration - UPDATE THESE to match your WHMCS
$db_config = array(
    'host' => 'localhost',
    'username' => 'apfkgyek_whmc291',
    'password' => 't0pp65)SX!',
    'database' => 'apfkgyek_whmc291'
);

// Connect to database
$conn = new mysqli($db_config['host'], $db_config['username'], $db_config['password'], $db_config['database']);
if ($conn->connect_error) {
    http_response_code(500);
    exit('Database connection failed');
}

// Get bot token from addon module config
$botToken = getModuleConfig('whmcs_telegram_bot', 'bot_token');

if (empty($botToken)) {
    http_response_code(403);
    exit('Bot not configured');
}

// Read the webhook update
$update = json_decode(file_get_contents('php://input'), true);

if (!$update) {
    http_response_code(200);
    exit('OK');
}

// Handle callback queries (button presses)
if (isset($update['callback_query'])) {
    handleCallbackQuery($update['callback_query'], $botToken, $conn);
    exit;
}

// Handle regular messages
if (isset($update['message'])) {
    handleMessage($update['message'], $botToken, $conn);
    exit;
}

// Handle inline queries
if (isset($update['inline_query'])) {
    handleInlineQuery($update['inline_query'], $botToken);
    exit;
}

/**
 * Get module configuration from database
 */
function getModuleConfig($module, $setting) {
    global $conn;
    $stmt = $conn->prepare("SELECT value FROM tbladdonmodules WHERE module = ? AND setting = ?");
    $stmt->bind_param("ss", $module, $setting);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['value'];
    }
    return null;
}

/**
 * Handle callback queries (inline button presses)
 */
function handleCallbackQuery($callback, $botToken, $conn) {
    $callbackId = $callback['id'];
    $userId = $callback['from']['id'];
    $chatId = $callback['message']['chat']['id'];
    $data = $callback['data'] ?? '';
    
    $parts = explode(':', $data);
    $action = $parts[0] ?? '';
    $param = $parts[1] ?? '';
    
    // Answer callback immediately
    answerCallback($callbackId, '', $botToken);
    
    // Check if user is linked
    $clientId = getLinkedClient($userId, $conn);
    
    if (!$clientId && $action !== 'link') {
        sendMessage($chatId, "Please link your account first. Visit the linking page in your WHMCS client area.", $botToken);
        return;
    }
    
    switch ($action) {
        case 'link':
            handleLinkRequest($chatId, $userId, $callback['from'], $botToken);
            break;
        case 'balance':
            showBalance($chatId, $clientId, $botToken, $conn);
            break;
        case 'invoices':
            showInvoices($chatId, $clientId, $botToken, $conn);
            break;
        case 'services':
            showServices($chatId, $clientId, $botToken, $conn);
            break;
        case 'domains':
            showDomains($chatId, $clientId, $botToken, $conn);
            break;
        case 'support':
            showSupportMenu($chatId, $clientId, $botToken);
            break;
        case 'invoice':
            showInvoiceDetail($chatId, $clientId, $param, $botToken, $conn);
            break;
        case 'service':
            showServiceDetail($chatId, $clientId, $param, $botToken, $conn);
            break;
        case 'domain':
            showDomainDetail($chatId, $clientId, $param, $botToken, $conn);
            break;
        case 'back':
            showMainMenu($chatId, $clientId, $botToken);
            break;
        case 'unlink':
            unlinkAccount($chatId, $userId, $botToken, $conn);
            break;
    }
}

/**
 * Handle incoming messages
 */
function handleMessage($message, $botToken, $conn) {
    $chatId = $message['chat']['id'];
    $userId = $message['from']['id'];
    $text = $message['text'] ?? '';
    $firstName = $message['from']['first_name'] ?? '';
    $lastName = $message['from']['last_name'] ?? '';
    $username = $message['from']['username'] ?? '';
    
    // Check if this is a /start with a link token
    if (strpos($text, '/start link_') === 0) {
        $token = str_replace('/start link_', '', $text);
        handleLinkToken($chatId, $userId, $token, $botToken, $conn, $firstName, $lastName, $username);
        return;
    }
    
    // Check if user is linked
    $clientId = getLinkedClient($userId, $conn);
    
    if (!$clientId) {
        sendMessage($chatId, "Welcome! 👋\n\nTo use this bot, please link your WHMCS account.\n\nVisit your client area and look for the 'Connect Telegram' button, or contact support for help.", $botToken);
        return;
    }
    
    // Handle commands
    $text = strtolower(trim($text));
    
    switch ($text) {
        case '/start':
        case '/menu':
            showMainMenu($chatId, $clientId, $botToken);
            break;
        case '/balance':
            showBalance($chatId, $clientId, $botToken, $conn);
            break;
        case '/invoices':
        case '/invoice':
            showInvoices($chatId, $clientId, $botToken, $conn);
            break;
        case '/lastinvoice':
            showLastInvoice($chatId, $clientId, $botToken, $conn);
            break;
        case '/services':
        case '/service':
            showServices($chatId, $clientId, $botToken, $conn);
            break;
        case '/domains':
        case '/domain':
            showDomains($chatId, $clientId, $botToken, $conn);
            break;
        case '/support':
        case '/ticket':
            showSupportMenu($chatId, $clientId, $botToken);
            break;
        case '/unlink':
            unlinkAccount($chatId, $userId, $botToken, $conn);
            break;
        case '/help':
            showHelp($chatId, $botToken);
            break;
        default:
            sendMessage($chatId, "Sorry, I didn't understand that. Use /menu to see available options.", $botToken);
    }
}

/**
 * Show main menu with inline buttons
 */
function showMainMenu($chatId, $clientId, $botToken) {
    $client = getClientDetails($clientId, $conn ?? null);
    $name = $client['firstname'] ?? 'Client';
    
    $keyboard = array(
        array(
            array('text' => '💰 Balance', 'callback_data' => 'balance'),
            array('text' => '📄 Invoices', 'callback_data' => 'invoices')
        ),
        array(
            array('text' => '🖥️ Services', 'callback_data' => 'services'),
            array('text' => '🌐 Domains', 'callback_data' => 'domains')
        ),
        array(
            array('text' => '🎫 Support', 'callback_data' => 'support')
        ),
        array(
            array('text' => '❌ Unlink Account', 'callback_data' => 'unlink')
        )
    );
    
    $text = "Welcome, {$name}! 👋\n\nWhat would you like to do?";
    sendKeyboard($chatId, $text, $keyboard, $botToken);
}

/**
 * Show account balance
 */
function showBalance($chatId, $clientId, $botToken, $conn) {
    $client = getClientDetails($clientId, $conn);
    
    $balance = formatCurrency($client['balance'] ?? 0, $client['currency'] ?? 1, $conn);
    $credit = formatCurrency($client['credit'] ?? 0, $client['currency'] ?? 1, $conn);
    
    $text = "💰 *Account Balance*\n\n";
    $text .= "Current Balance: *{$balance}*\n";
    $text .= "Available Credit: *{$credit}*";
    
    $keyboard = array(
        array(array('text' => '🔙 Back to Menu', 'callback_data' => 'back'))
    );
    
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Show invoices
 */
function showInvoices($chatId, $clientId, $botToken, $conn) {
    $invoices = getInvoices($clientId, $conn);
    
    if (empty($invoices)) {
        sendMessage($chatId, "No invoices found.", $botToken);
        return;
    }
    
    $text = "📄 *Your Invoices*\n\n";
    
    $keyboard = array();
    
    foreach (array_slice($invoices, 0, 5) as $inv) {
        $status = $inv['status'] === 'Paid' ? '✅' : '⏳';
        $text .= "{$status} #{$inv['id']} - " . formatCurrency($inv['total'], $inv['currency'], $conn) . "\n";
        $text .= "   " . $inv['status'] . " | " . $inv['date'] . "\n\n";
        $keyboard[] = array(array('text' => "View #{$inv['id']}", 'callback_data' => 'invoice:' . $inv['id']));
    }
    
    $keyboard[] = array(array('text' => '🔙 Back to Menu', 'callback_data' => 'back'));
    
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Show last invoice
 */
function showLastInvoice($chatId, $clientId, $botToken, $conn) {
    $invoices = getInvoices($clientId, $conn);
    
    if (empty($invoices)) {
        sendMessage($chatId, "No invoices found.", $botToken);
        return;
    }
    
    $inv = $invoices[0];
    showInvoiceDetail($chatId, $clientId, $inv['id'], $botToken, $conn);
}

/**
 * Show invoice detail
 */
function showInvoiceDetail($chatId, $clientId, $invoiceId, $botToken, $conn) {
    $invoice = getInvoiceDetail($clientId, $invoiceId, $conn);
    
    if (!$invoice) {
        sendMessage($chatId, "Invoice not found.", $botToken);
        return;
    }
    
    $text = "📄 *Invoice #{$invoice['id']}*\n\n";
    $text .= "Date: {$invoice['date']}\n";
    $text .= "Due Date: {$invoice['duedate']}\n";
    $text .= "Status: {$invoice['status']}\n";
    $text .= "Amount: " . formatCurrency($invoice['total'], $invoice['currency'], $conn) . "\n\n";
    
    if ($invoice['status'] !== 'Paid') {
        $text .= "Payment Method: {$invoice['paymentmethod']}";
    }
    
    $keyboard = array(
        array(array('text' => '🔙 Back to Invoices', 'callback_data' => 'invoices'))
    );
    
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Show services
 */
function showServices($chatId, $clientId, $botToken, $conn) {
    $services = getServices($clientId, $conn);
    
    if (empty($services)) {
        sendMessage($chatId, "No services found.", $botToken);
        return;
    }
    
    $text = "🖥️ *Your Services*\n\n";
    
    $keyboard = array();
    
    foreach (array_slice($services, 0, 5) as $srv) {
        $status = $srv['domainstatus'] === 'Active' ? '✅' : '⚠️';
        $text .= "{$status} {$srv['name']}\n";
        $text .= "   {$srv['domain']}\n";
        $text .= "   Next Due: {$srv['nextduedate']}\n\n";
        $keyboard[] = array(array('text' => "View {$srv['name']}", 'callback_data' => 'service:' . $srv['id']));
    }
    
    $keyboard[] = array(array('text' => '🔙 Back to Menu', 'callback_data' => 'back'));
    
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Show service detail
 */
function showServiceDetail($chatId, $clientId, $serviceId, $botToken, $conn) {
    $service = getServiceDetail($clientId, $serviceId, $conn);
    
    if (!$service) {
        sendMessage($chatId, "Service not found.", $botToken);
        return;
    }
    
    $text = "🖥️ *Service Details*\n\n";
    $text .= "Product: {$service['name']}\n";
    $text .= "Domain: {$service['domain']}\n";
    $text .= "Status: {$service['domainstatus']}\n";
    $text .= "Billing Cycle: {$service['billingcycle']}\n";
    $text .= "Next Due: {$service['nextduedate']}\n";
    $text .= "Amount: " . formatCurrency($service['amount'], $service['currency'], $conn) . "\n";
    
    $keyboard = array(
        array(array('text' => '🔙 Back to Services', 'callback_data' => 'services'))
    );
    
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Show domains
 */
function showDomains($chatId, $clientId, $botToken, $conn) {
    $domains = getDomains($clientId, $conn);
    
    if (empty($domains)) {
        sendMessage($chatId, "No domains found.", $botToken);
        return;
    }
    
    $text = "🌐 *Your Domains*\n\n";
    
    $keyboard = array();
    
    foreach (array_slice($domains, 0, 5) as $dom) {
        $status = $dom['status'] === 'Active' ? '✅' : '⚠️';
        $text .= "{$status} {$dom['domain']}\n";
        $text .= "   Expires: {$dom['expirydate']}\n\n";
        $keyboard[] = array(array('text' => "View {$dom['domain']}", 'callback_data' => 'domain:' . $dom['id']));
    }
    
    $keyboard[] = array(array('text' => '🔙 Back to Menu', 'callback_data' => 'back'));
    
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Show domain detail
 */
function showDomainDetail($chatId, $clientId, $domainId, $botToken, $conn) {
    $domain = getDomainDetail($clientId, $domainId, $conn);
    
    if (!$domain) {
        sendMessage($chatId, "Domain not found.", $botToken);
        return;
    }
    
    $text = "🌐 *Domain Details*\n\n";
    $text .= "Domain: {$domain['domain']}\n";
    $text .= "Status: {$domain['status']}\n";
    $text .= "Registration Date: {$domain['registrationdate']}\n";
    $text .= "Next Due: {$domain['nextduedate']}\n";
    $text .= "Expiry Date: {$domain['expirydate']}\n";
    
    $keyboard = array(
        array(array('text' => '🔙 Back to Domains', 'callback_data' => 'domains'))
    );
    
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Show support menu
 */
function showSupportMenu($chatId, $clientId, $botToken) {
    $text = "🎫 *Support*\n\n";
    $text .= "To create a support ticket, please visit your client area or email support.\n\n";
    $text .= "I can help you view your existing tickets.";
    
    $keyboard = array(
        array(array('text' => '🔙 Back to Menu', 'callback_data' => 'back'))
    );
    
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Handle account linking via token
 */
function handleLinkToken($chatId, $userId, $token, $botToken, $conn, $firstName = '', $lastName = '', $username = '') {
    // Find pending link request
    $stmt = $conn->prepare("SELECT * FROM `mod_whmcs_telegram_pending` WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result || !($pending = $result->fetch_assoc())) {
        sendMessage($chatId, "Invalid or expired link token. Please request a new link from your client area.", $botToken);
        return;
    }
    
    $clientId = $pending['client_id'];
    
    // Delete pending request
    $stmt = $conn->prepare("DELETE FROM `mod_whmcs_telegram_pending` WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    
    // Create link (upsert)
    $stmt = $conn->prepare("
        INSERT INTO `mod_whmcs_telegram_links` (user_id, client_id, chat_id, username, first_name, last_name) 
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE client_id = VALUES(client_id), chat_id = VALUES(chat_id), 
        username = VALUES(username), first_name = VALUES(first_name), last_name = VALUES(last_name)
    ");
    $stmt->bind_param("iiisss", $userId, $clientId, $chatId, $username, $firstName, $lastName);
    $stmt->execute();
    
    sendMessage($chatId, "✅ Account linked successfully!\n\nYou can now check your balance, invoices, services, and more.\n\nUse /menu to get started.", $botToken);
}

/**
 * Handle link request from callback
 */
function handleLinkRequest($chatId, $userId, $user, $botToken) {
    sendMessage($chatId, "To link your account, please visit the linking page in your WHMCS client area. Contact support if you need help.", $botToken);
}

/**
 * Unlink account
 */
function unlinkAccount($chatId, $userId, $botToken, $conn) {
    $stmt = $conn->prepare("DELETE FROM `mod_whmcs_telegram_links` WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    sendMessage($chatId, "Your account has been unlinked. To use the bot again, link your account from the client area.", $botToken);
}

/**
 * Show help
 */
function showHelp($chatId, $botToken) {
    $text = "📖 *Help*\n\n";
    $text .= "Available commands:\n";
    $text .= "/start - Show main menu\n";
    $text .= "/menu - Show main menu\n";
    $text .= "/balance - Check account balance\n";
    $text .= "/invoices - View invoices\n";
    $text .= "/lastinvoice - View last invoice\n";
    $text .= "/services - View hosting services\n";
    $text .= "/domains - View domains\n";
    $text .= "/support - Get support help\n";
    $text .= "/unlink - Unlink your account\n";
    $text .= "/help - Show this help";
    
    sendMessage($chatId, $text, $botToken, true);
}

/**
 * Get linked client ID for a Telegram user
 */
function getLinkedClient($userId, $conn) {
    $stmt = $conn->prepare("SELECT client_id FROM `mod_whmcs_telegram_links` WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['client_id'];
    }
    
    return null;
}

/**
 * Get client details
 */
function getClientDetails($clientId, $conn) {
    $stmt = $conn->prepare("SELECT * FROM tblclients WHERE id = ?");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    
    return array();
}

/**
 * Get client invoices
 */
function getInvoices($clientId, $conn) {
    $invoices = array();
    $stmt = $conn->prepare("SELECT id, date, duedate, total, status, paymentmethod, currency FROM tblinvoices WHERE userid = ? ORDER BY date DESC LIMIT 10");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $invoices[] = $row;
    }
    
    return $invoices;
}

/**
 * Get invoice detail
 */
function getInvoiceDetail($clientId, $invoiceId, $conn) {
    $stmt = $conn->prepare("SELECT * FROM tblinvoices WHERE id = ? AND userid = ?");
    $stmt->bind_param("ii", $invoiceId, $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    
    return null;
}

/**
 * Get client services
 */
function getServices($clientId, $conn) {
    $services = array();
    $stmt = $conn->prepare("
        SELECT t1.id, t1.domain, t1.domainstatus, t1.nextduedate, t1.billingcycle, t1.amount, t1.currency, t2.name 
        FROM tblhosting t1 
        JOIN tblproducts t2 ON t1.packageid = t2.id 
        WHERE t1.userid = ? 
        ORDER BY t1.id DESC LIMIT 10
    ");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    
    return $services;
}

/**
 * Get service detail
 */
function getServiceDetail($clientId, $serviceId, $conn) {
    $stmt = $conn->prepare("SELECT t1.*, t2.name FROM tblhosting t1 JOIN tblproducts t2 ON t1.packageid = t2.id WHERE t1.id = ? AND t1.userid = ?");
    $stmt->bind_param("ii", $serviceId, $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    
    return null;
}

/**
 * Get client domains
 */
function getDomains($clientId, $conn) {
    $domains = array();
    $stmt = $conn->prepare("SELECT * FROM tbldomains WHERE userid = ? ORDER BY id DESC LIMIT 10");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $domains[] = $row;
    }
    
    return $domains;
}

/**
 * Get domain detail
 */
function getDomainDetail($clientId, $domainId, $conn) {
    $stmt = $conn->prepare("SELECT * FROM tbldomains WHERE id = ? AND userid = ?");
    $stmt->bind_param("ii", $domainId, $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    
    return null;
}

/**
 * Format currency
 */
function formatCurrency($amount, $currencyId, $conn) {
    $stmt = $conn->prepare("SELECT prefix, suffix FROM tblcurrencies WHERE id = ?");
    $stmt->bind_param("i", $currencyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['prefix'] . number_format($amount, 2) . $row['suffix'];
    }
    
    return '$' . number_format($amount, 2);
}

/**
 * Send message via Telegram API
 */
function sendMessage($chatId, $text, $botToken, $parseMarkdown = false) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    
    $data = array(
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => $parseMarkdown ? 'Markdown' : ''
    );
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
}

/**
 * Send keyboard via Telegram API
 */
function sendKeyboard($chatId, $text, $keyboard, $botToken, $parseMarkdown = false) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    
    $inlineKeyboard = array();
    foreach ($keyboard as $row) {
        $inlineRow = array();
        foreach ($row as $button) {
            $inlineRow[] = array(
                'text' => $button['text'],
                'callback_data' => $button['callback_data']
            );
        }
        $inlineKeyboard[] = $inlineRow;
    }
    
    $data = array(
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => $parseMarkdown ? 'Markdown' : '',
        'reply_markup' => json_encode(array('inline_keyboard' => $inlineKeyboard))
    );
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
}

/**
 * Answer callback query
 */
function answerCallback($callbackId, $text, $botToken) {
    $url = "https://api.telegram.org/bot{$botToken}/answerCallbackQuery";
    
    $data = array(
        'callback_query_id' => $callbackId,
        'text' => $text
    );
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
}

/**
 * Handle inline queries (for future use)
 */
function handleInlineQuery($inlineQuery, $botToken) {
    // Not implemented yet
}