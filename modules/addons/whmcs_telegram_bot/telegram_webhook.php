<?php
/**
 * WHMCS Telegram Bot - Lightweight Webhook Handler
 * Connects directly to DB without full WHMCS bootstrap
 */

// Database configuration
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
    $msg = $update['message'];
    $chatId = $msg['chat']['id'];
    $userId = $msg['from']['id'];
    $text = $msg['text'] ?? '';
    $firstName = $msg['from']['first_name'] ?? '';
    $lastName = $msg['from']['last_name'] ?? '';
    $username = $msg['from']['username'] ?? '';
    
    // DEBUG: Show what we received
$fullUpdate = json_encode($update);
sendMessage($chatId, "DEBUG update: " . substr($fullUpdate, 0, 500), $botToken);
    
    // Check for deep link start parameter
    if (!empty($update['start_parameter']) && strpos($update['start_parameter'], 'link_') === 0) {
        $token = str_replace('link_', '', $update['start_parameter']);
        handleLinkToken($chatId, $userId, $token, $botToken, $conn, $firstName, $lastName, $username);
        exit;
    }
    
    // Also check text format /start link_xxx
    if (strpos($text, '/start link_') === 0) {
        $token = str_replace('/start link_', '', $text);
        handleLinkToken($chatId, $userId, $token, $botToken, $conn, $firstName, $lastName, $username);
        exit;
    }
    
    // Check for /link command with token
    if (strpos($text, '/link ') === 0) {
        $token = trim(str_replace('/link ', '', $text));
        handleLinkToken($chatId, $userId, $token, $botToken, $conn, $firstName, $lastName, $username);
        exit;
    }
    
    // Check user is linked
    $clientId = getLinkedClient($userId, $conn);
    
    if (!$clientId) {
        sendMessage($chatId, "Welcome! Please link your account.", $botToken);
        exit;
    }
    
    $text = strtolower(trim($text));
    
    switch ($text) {
        case '/start':
        case '/menu':
            showMainMenu($chatId, $clientId, $botToken);
            break;
        default:
            sendMessage($chatId, "Use /menu", $botToken);
    }
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
 * Handle account linking via token
 */
function handleLinkToken($chatId, $userId, $token, $botToken, $conn, $firstName = '', $lastName = '', $username = '') {
    sendMessage($chatId, "DEBUG: handleLinkToken called with token: " . substr($token, 0, 20) . "...", $botToken);
    
    $stmt = $conn->prepare("SELECT * FROM `mod_whmcs_telegram_pending` WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    sendMessage($chatId, "DEBUG: DB rows: " . $result->num_rows, $botToken);
    
    if (!$result || !($pending = $result->fetch_assoc())) {
        sendMessage($chatId, "Invalid or expired token.", $botToken);
        return;
    }
    
    $clientId = $pending['client_id'];
    
    $stmt = $conn->prepare("DELETE FROM `mod_whmcs_telegram_pending` WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    
    $stmt = $conn->prepare("
        INSERT INTO `mod_whmcs_telegram_links` (user_id, client_id, chat_id, username, first_name, last_name) 
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE client_id = VALUES(client_id), chat_id = VALUES(chat_id)
    ");
    $stmt->bind_param("iiisss", $userId, $clientId, $chatId, $username, $firstName, $lastName);
    $stmt->execute();
    
    sendMessage($chatId, "✅ Account linked! Use /menu", $botToken);
}

/**
 * Get linked client ID
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
 * Show main menu
 */
function showMainMenu($chatId, $clientId, $botToken) {
    sendMessage($chatId, "Menu coming soon - account is linked!", $botToken);
}

/**
 * Send message via Telegram API
 */
function sendMessage($chatId, $text, $botToken) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = array('chat_id' => $chatId, 'text' => $text);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
}

/**
 * Handle inline queries
 */
function handleInlineQuery($inlineQuery, $botToken) {
    // Not implemented
}

/**
 * Register bot commands (run once)
 */
function registerCommands($botToken) {
    $commands = json_encode([
        ['command' => 'start', 'description' => 'Show main menu'],
        ['command' => 'link', 'description' => 'Link your WHMCS account'],
        ['command' => 'balance', 'description' => 'Check account balance'],
        ['command' => 'invoices', 'description' => 'View invoices'],
        ['command' => 'services', 'description' => 'View hosting services'],
        ['command' => 'domains', 'description' => 'View domains'],
        ['command' => 'help', 'description' => 'Show help']
    ]);
    
    $url = "https://api.telegram.org/bot{$botToken}/setMyCommands";
    $data = ['commands' => $commands];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// Register commands on first run (can be called manually)
if (isset($_GET['register_commands'])) {
    registerCommands($botToken);
    exit('Commands registered');
}
