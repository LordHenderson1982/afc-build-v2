<?php
/**
 * WHMCS Telegram Bot - Lightweight Linking Page
 * Allows clients to link their WHMCS account to Telegram
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
    die("Database connection failed: " . $conn->error);
}

// Get bot token from config
$stmt = $conn->prepare("SELECT value FROM tbladdonmodules WHERE module = 'whmcs_telegram_bot' AND setting = 'bot_token'");
$stmt->execute();
$result = $stmt->get_result();
$botToken = '';
if ($row = $result->fetch_assoc()) {
    $botToken = $row['value'];
}

// Get bot info if token exists
$botUsername = '';
if (!empty($botToken)) {
    $ch = curl_init("https://api.telegram.org/bot{$botToken}/getMe");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    $botInfo = json_decode($response, true);
    if ($botInfo['ok']) {
        $botUsername = $botInfo['result']['username'];
    }
}

// Get session client ID (from WHMCS session or parameter)
$clientId = 0;

// Try to get from WHMCS session
// WHMCS uses 'uid' or 'client-login' session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Try different session variables WHMCS might use
if (isset($_SESSION['uid']) && !empty($_SESSION['uid'])) {
    $clientId = (int)$_SESSION['uid'];
} elseif (isset($_SESSION['client']['id']) && !empty($_SESSION['client']['id'])) {
    $clientId = (int)$_SESSION['client']['id'];
} elseif (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $clientId = (int)$_SESSION['user_id'];
} elseif (isset($_GET['client_id'])) {
    // Accept client_id from URL (passed by hook)
    $clientId = (int)$_GET['client_id'];
}

$action = $_GET['action'] ?? '';
$message = '';
$isLinked = false;

// Generate a unique token for linking
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Check if client is logged in
if ($clientId === 0) {
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Telegram Account</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f5f5; padding: 40px; text-align: center; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2296f3; margin-bottom: 20px; }
        .error { color: #f44336; padding: 15px; background: #ffebee; border-radius: 8px; margin: 20px 0; }
        .login-btn { display: inline-block; padding: 12px 24px; background: #2296f3; color: white; text-decoration: none; border-radius: 6px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Link Telegram</h1>
        <div class="error">Please log in to your client area first, then visit this page.</div>
        <a href="/clientarea.php" class="login-btn">Go to Client Login</a>
    </div>
</body>
</html>';
    exit;
}

// Check if already linked
$stmt = $conn->prepare("SELECT id FROM mod_whmcs_telegram_links WHERE client_id = ?");
$stmt->bind_param("i", $clientId);
$stmt->execute();
$result = $stmt->get_result();
$isLinked = ($result->num_rows > 0);

// Handle link request
if ($action === 'link' && !$isLinked) {
    // Generate new token
    $token = generateToken();
    $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    // Delete old pending tokens for this client
    $stmt = $conn->prepare("DELETE FROM mod_whmcs_telegram_pending WHERE client_id = ?");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    
    // Insert new pending token
    $stmt = $conn->prepare("INSERT INTO mod_whmcs_telegram_pending (token, client_id, created_at, expires_at) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("sis", $token, $clientId, $expiresAt);
    $stmt->execute();
    
    // Get base URL from request
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'veilhosts.shop';
    $baseUrl = $scheme . '://' . $host;
    $webhookUrl = $baseUrl . '/modules/addons/whmcs_telegram_bot/telegram_webhook.php';
    
    // Build Telegram link - show instructions to send command
    $botPart = !empty($botUsername) ? $botUsername : 'YourBot';
    // Old deep link method - doesn't always work
    // $linkUrl = "https://t.me/{$botPart}?start=link_{$token}";
    
    // New method - show the user a command to send
    $linkCommand = "/link {$token}";
    
    $message = 'link_ready';
}

// Handle unlink request
if ($action === 'unlink') {
    $stmt = $conn->prepare("DELETE FROM mod_whmcs_telegram_links WHERE client_id = ?");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $isLinked = false;
    $message = 'unlinked';
}

// Get client details for display
$stmt = $conn->prepare("SELECT firstname, lastname, email FROM tblclients WHERE id = ?");
$stmt->bind_param("i", $clientId);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();

$clientName = ($client['firstname'] ?? '') . ' ' . ($client['lastname'] ?? '');
$clientName = trim($clientName) ?: 'Client';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Telegram Account</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f5f5; padding: 40px; text-align: center; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2296f3; margin-bottom: 20px; }
        .success { color: #4caf50; padding: 15px; background: #e8f5e9; border-radius: 8px; margin: 20px 0; }
        .info { color: #333; padding: 15px; background: #e3f2fd; border-radius: 8px; margin: 20px 0; text-align: left; }
        .error { color: #f44336; padding: 15px; background: #ffebee; border-radius: 8px; margin: 20px 0; }
        .telegram-btn { display: inline-block; padding: 12px 24px; background: #2296f3; color: white; text-decoration: none; border-radius: 6px; margin: 10px 5px; font-size: 16px; }
        .telegram-btn:hover { background: #1976d2; }
        .secondary-btn { background: #6c757d; }
        .secondary-btn:hover { background: #5a6268; }
        .link-box { background: #fff3e0; border: 1px solid #ffb74d; padding: 15px; border-radius: 8px; margin: 15px 0; word-break: break-all; font-family: monospace; font-size: 14px; }
        .steps { text-align: left; margin: 15px 0; }
        .steps li { margin: 8px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Link Telegram</h1>
        
        <?php if ($message === 'link_ready'): ?>
            <div class="success">✅ Linking token generated!</div>
            <div class="info">
                <strong>Logged in as:</strong> <?= htmlspecialchars($clientName) ?><br>
                <strong>Email:</strong> <?= htmlspecialchars($client['email'] ?? 'N/A') ?>
            </div>
            <p>Click the button below to open Telegram, then send this command:</p>
            <div class="link-box"><?= htmlspecialchars($linkCommand) ?></div>
            <a href="https://t.me/<?= htmlspecialchars($botUsername) ?>" class="telegram-btn" target="_blank">📱 Open Telegram Bot</a>
            
            <div class="info" style="margin-top: 20px;">
                <strong>Instructions:</strong>
                <ol class="steps">
                    <li>Click "Open Telegram Bot" above</li>
                    <li>Paste this command in the chat: <code><?= htmlspecialchars($linkCommand) ?></code></li>
                    <li>Send it to the bot</li>
                    <li>The token expires in 15 minutes</li>
                </ol>
            </div>
        
        <?php elseif ($message === 'unlinked'): ?>
            <div class="success">✅ Your account has been unlinked from Telegram.</div>
            <a href="?action=link" class="telegram-btn">Link Telegram Account</a>
        
        <?php elseif ($isLinked): ?>
            <div class="success">✅ Your Telegram account is already linked!</div>
            <div class="info">
                <strong>Logged in as:</strong> <?= htmlspecialchars($clientName) ?>
            </div>
            <p>You can now use the Telegram bot to check your invoices, services, knowledgebase, and more.</p>
            <?php if (!empty($botUsername)): ?>
            <a href="https://t.me/<?= htmlspecialchars($botUsername) ?>" class="telegram-btn">Open Telegram Bot</a>
            <?php endif; ?>
            <a href="?action=unlink" class="telegram-btn secondary-btn">Unlink Account</a>
        
        <?php else: ?>
            <div class="info">
                <strong>Logged in as:</strong> <?= htmlspecialchars($clientName) ?><br>
                <strong>Email:</strong> <?= htmlspecialchars($client['email'] ?? 'N/A') ?>
            </div>
            <p>Link your WHMCS account to Telegram for instant access to:</p>
            <ul style="text-align: left; margin: 20px 0;">
                <li>💰 Account balance</li>
                <li>📄 Invoices</li>
                <li>🖥️ Hosting services & login details</li>
                <li>📚 Knowledgebase articles</li>
                <li>🎫 Support tickets</li>
            </ul>
            <a href="?action=link" class="telegram-btn">🔗 Link My Account</a>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>