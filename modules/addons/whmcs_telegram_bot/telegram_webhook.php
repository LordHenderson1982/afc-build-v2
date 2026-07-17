<?php
/**
 * WHMCS Telegram Bot - Webhook Handler
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
    
    // Check for /link command with token
    if (strpos($text, '/link ') === 0) {
        $token = trim(str_replace('/link ', '', $text));
        handleLinkToken($chatId, $userId, $token, $botToken, $conn, $firstName, $lastName, $username);
        exit;
    }
    
    // Check user is linked
    $clientId = getLinkedClient($userId, $conn);
    
    if (!$clientId) {
        sendMessage($chatId, "Welcome! 👋\n\nTo use this bot, please link your WHMCS account.\n\nVisit your client area and click 'Connect Telegram'.", $botToken);
        exit;
    }
    
    $text = strtolower(trim($text));
    
    switch ($text) {
        case '/start':
        case '/menu':
            showMainMenu($chatId, $clientId, $botToken, $conn);
            break;
        case '/balance':
            showBalance($chatId, $clientId, $botToken, $conn);
            break;
        case '/invoices':
        case '/invoice':
            showInvoices($chatId, $clientId, $botToken, $conn);
            break;
        case '/services':
        case '/service':
            showServices($chatId, $clientId, $botToken, $conn);
            break;
        case '/knowledgebase':
        case '/kb':
            showKnowledgebase($chatId, $clientId, $botToken, $conn);
            break;
        case '/help':
            showHelp($chatId, $botToken);
            break;
        case '/unlink':
            unlinkAccount($chatId, $userId, $botToken, $conn);
            break;
        default:
            sendMessage($chatId, "Use /menu for options", $botToken);
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
    
    // Create link
    $stmt = $conn->prepare("
        INSERT INTO `mod_whmcs_telegram_links` (user_id, client_id, chat_id, username, first_name, last_name) 
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE client_id = VALUES(client_id), chat_id = VALUES(chat_id), username = VALUES(username), first_name = VALUES(first_name), last_name = VALUES(last_name)
    ");
    $stmt->bind_param("iiisss", $userId, $clientId, $chatId, $username, $firstName, $lastName);
    $stmt->execute();
    
    sendMessage($chatId, "✅ Account linked successfully!\n\nUse /menu to see options.", $botToken);
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
 * Show main menu
 */
function showMainMenu($chatId, $clientId, $botToken, $conn) {
    $client = getClientDetails($clientId, $conn);
    $name = $client['firstname'] ?? 'Client';
    
    $keyboard = array(
        array(
            array('text' => '💰 Balance', 'callback_data' => 'balance'),
            array('text' => '📄 Invoices', 'callback_data' => 'invoices')
        ),
        array(
            array('text' => '🖥️ Services', 'callback_data' => 'services'),
            array('text' => '📚 Knowledgebase', 'callback_data' => 'knowledgebase')
        ),
        array(
            array('text' => '❌ Unlink', 'callback_data' => 'unlink')
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
        array(array('text' => '🔙 Back', 'callback_data' => 'back'))
    );
    
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Show invoices
 */
function showInvoices($chatId, $clientId, $botToken, $conn) {
    $stmt = $conn->prepare("SELECT id, date, total, status, currency FROM tblinvoices WHERE userid = ? ORDER BY date DESC LIMIT 5");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $text = "📄 *Your Invoices*\n\n";
    $keyboard = array();
    
    while ($row = $result->fetch_assoc()) {
        $status = $row['status'] === 'Paid' ? '✅' : '⏳';
        $text .= "{$status} #{$row['id']} - " . formatCurrency($row['total'], $row['currency'], $conn) . "\n";
        $text .= "   {$row['status']} | {$row['date']}\n\n";
    }
    
    if ($result->num_rows === 0) {
        $text .= "No invoices found.";
    }
    
    $keyboard[] = array(array('text' => '🔙 Back', 'callback_data' => 'back'));
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Show services
 */
function showServices($chatId, $clientId, $botToken, $conn) {
    $stmt = $conn->prepare("
        SELECT t1.id, t1.domain, t1.domainstatus, t1.nextduedate, t1.username, t1.password, t2.name 
        FROM tblhosting t1 
        JOIN tblproducts t2 ON t1.packageid = t2.id 
        WHERE t1.userid = ? 
        ORDER BY t1.id DESC LIMIT 5
    ");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $text = "🖥️ *Your Services*\n\n";
    $keyboard = array();
    
    while ($row = $result->fetch_assoc()) {
        $status = $row['domainstatus'] === 'Active' ? '✅' : '⚠️';
        $text .= "{$status} {$row['name']}\n";
        $text .= "   🌐 {$row['domain']}\n";
        if (!empty($row['username'])) {
            $text .= "   👤 Username: `{$row['username']}`\n";
        }
        $text .= "   📅 Due: {$row['nextduedate']}\n\n";
    }
    
    if ($result->num_rows > 0) {
        $text .= "_💡 Passwords: View in Client Area → Services_\n";
    }
    
    if ($result->num_rows === 0) {
        $text .= "No services found.";
    }
    
    $keyboard[] = array(array('text' => '🔙 Back', 'callback_data' => 'back'));
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Show knowledgebase articles
 */
function showKnowledgebase($chatId, $clientId, $botToken, $conn) {
    // Get all knowledgebase categories and articles
    $stmt = $conn->prepare("
        SELECT kba.id, kba.title, kba.article, kbc.name as category 
        FROM tblkbarticles kba
        JOIN tblkbcategories kbc ON kba.category = kbc.id
        WHERE kbc.hidden = 0
        ORDER BY kbc.displayorder, kba.displayorder
        LIMIT 10
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $text = "📚 *Knowledgebase*\n\n";
    $keyboard = array();
    
    if ($result->num_rows === 0) {
        $text .= "No articles available.";
    } else {
        $text .= "Available articles:\n\n";
        while ($row = $result->fetch_assoc()) {
            $title = html_entity_decode($row['title'], ENT_QUOTES, 'UTF-8');
            $text .= "📄 *{$title}*\n";
            $text .= "   Category: {$row['category']}\n\n";
            $keyboard[] = array(array('text' => '📖 ' . substr($title, 0, 30), 'callback_data' => 'kb_' . $row['id']));
        }
    }
    
    $keyboard[] = array(array('text' => '🔙 Back', 'callback_data' => 'back'));
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Show a specific knowledgebase article
 */
function showKnowledgebaseArticle($chatId, $articleId, $botToken, $conn) {
    $stmt = $conn->prepare("SELECT id, title, article FROM tblkbarticles WHERE id = ?");
    $stmt->bind_param("i", $articleId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $title = html_entity_decode($row['title'], ENT_QUOTES, 'UTF-8');
        $article = strip_tags(html_entity_decode($row['article'], ENT_QUOTES, 'UTF-8'));
        // Truncate if too long for Telegram
        if (strlen($article) > 1000) {
            $article = substr($article, 0, 997) . '...';
        }
        
        $text = "📖 *{$title}*\n\n" . $article;
    } else {
        $text = "Article not found.";
    }
    
    $keyboard = array(
        array(array('text' => '🔙 Back to KB', 'callback_data' => 'knowledgebase'))
    );
    
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Handle callback queries
 */
function handleCallbackQuery($callback, $botToken, $conn) {
    $callbackId = $callback['id'];
    $userId = $callback['from']['id'];
    $chatId = $callback['message']['chat']['id'];
    $data = $callback['data'] ?? '';
    
    answerCallback($callbackId, '', $botToken);
    
    // Debug: log what we received
    sendMessage($chatId, "DEBUG: Got callback data: '" . $data . "'", $botToken);
    
    $clientId = getLinkedClient($userId, $conn);
    if (!$clientId) {
        sendMessage($chatId, "Please link your account first.", $botToken);
        return;
    }
    
    // Handle callback actions explicitly
    if ($data === 'balance') {
        showBalance($chatId, $clientId, $botToken, $conn);
    } elseif ($data === 'invoices') {
        showInvoices($chatId, $clientId, $botToken, $conn);
    } elseif ($data === 'services') {
        showServices($chatId, $clientId, $botToken, $conn);
    } elseif ($data === 'knowledgebase') {
        showKnowledgebase($chatId, $clientId, $botToken, $conn);
    } elseif ($data === 'back') {
        showMainMenu($chatId, $clientId, $botToken, $conn);
    } elseif ($data === 'unlink') {
        unlinkAccount($chatId, $userId, $botToken, $conn);
    } elseif (strpos($data, 'kb_') === 0) {
        // Handle KB article clicks (kb_123)
        $articleId = (int)str_replace('kb_', '', $data);
        showKnowledgebaseArticle($chatId, $articleId, $botToken, $conn);
    } else {
        // Debug - show what was received
        error_log("Unknown callback data: " . $data);
        sendMessage($chatId, "Debug: Received unknown action: " . $data, $botToken);
    }
}

/**
 * Unlink account
 */
function unlinkAccount($chatId, $userId, $botToken, $conn) {
    $stmt = $conn->prepare("DELETE FROM `mod_whmcs_telegram_links` WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    sendMessage($chatId, "Your account has been unlinked. To use the bot again, link from your client area.", $botToken);
}

/**
 * Show help
 */
function showHelp($chatId, $botToken) {
    $text = "📖 *Help*\n\n";
    $text .= "/menu - Main menu\n";
    $text .= "/balance - Check balance\n";
    $text .= "/invoices - View invoices\n";
    $text .= "/services - View services\n";
    $text .= "/knowledgebase - Knowledgebase articles\n";
    $text .= "/unlink - Unlink account\n";
    $text .= "/help - Show this help";
    
    sendMessage($chatId, $text, $botToken, true);
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
 * Send message
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
 * Send keyboard
 */
function sendKeyboard($chatId, $text, $keyboard, $botToken, $parseMarkdown = false) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    
    $inlineKeyboard = array();
    foreach ($keyboard as $row) {
        $inlineRow = array();
        foreach ($row as $button) {
            $inlineRow[] = array('text' => $button['text'], 'callback_data' => $button['callback_data']);
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
 * Answer callback
 */
function answerCallback($callbackId, $text, $botToken) {
    $url = "https://api.telegram.org/bot{$botToken}/answerCallbackQuery";
    $data = array('callback_query_id' => $callbackId, 'text' => $text);
    
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
