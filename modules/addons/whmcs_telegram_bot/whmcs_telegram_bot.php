<?php
/**
 * WHMCS Telegram Bot - Main Module File
 * Connect your WHMCS to Telegram for client self-service
 */

if (!defined("WHMCS")) die("This file cannot be accessed directly");

// Load client area hooks
require_once __DIR__ . "/../../includes/hooks/whmcs_telegram_bot_client.php";

/**
 * Module Configuration
 */
function whmcs_telegram_bot_config() {
    return array(
        "name"            => "WHMCS Telegram Bot",
        "description"     => "Connect Telegram with your WHMCS for account linking, services, domains, invoices, and support.",
        "author"          => "Custom Build",
        "language"        => "english",
        "version"         => "1.0.1",
        "fields"          => array(
            "bot_token" => array(
                "Type"        => "text",
                "Size"        => "50",
                "Default"     => "",
                "Description" => "Telegram Bot Token (get from @BotFather)"
            ),
            "admin_ids" => array(
                "Type"        => "text",
                "Size"        => "50",
                "Default"     => "",
                "Description" => "Admin Telegram User IDs (comma separated, for notifications)"
            ),
            "default_department" => array(
                "Type"        => "dropdown",
                "Options"     => "1,2,3,4,5,6,7,8,9,10",
                "Default"     => "1",
                "Description" => "Default support department for tickets"
            )
        )
    );
}

/**
 * Activate Module
 */
function whmcs_telegram_bot_activate() {
    // Create database table for Telegram user links
    $query = "CREATE TABLE IF NOT EXISTS `mod_whmcs_telegram_links` (
        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `user_id` BIGINT(20) UNSIGNED NOT NULL,
        `client_id` INT(10) UNSIGNED NOT NULL,
        `chat_id` BIGINT(20) NOT NULL,
        `username` VARCHAR(255) DEFAULT NULL,
        `first_name` VARCHAR(255) DEFAULT NULL,
        `last_name` VARCHAR(255) DEFAULT NULL,
        `language_code` VARCHAR(10) DEFAULT 'en',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_user_id` (`user_id`),
        KEY `client_id` (`client_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    full_query($query);
    
    // Create table for pending linking requests
    $query2 = "CREATE TABLE IF NOT EXISTS `mod_whmcs_telegram_pending` (
        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `token` VARCHAR(64) NOT NULL,
        `client_id` INT(10) UNSIGNED NOT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `expires_at` DATETIME NOT NULL,
        UNIQUE KEY `token` (`token`),
        KEY `client_id` (`client_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    full_query($query2);
    
    return array(
        "status"      => "success",
        "description" => "WHMCS Telegram Bot activated successfully!"
    );
}

/**
 * Deactivate Module
 */
function whmcs_telegram_bot_deactivate() {
    return array(
        "status"      => "success",
        "description" => "WHMCS Telegram Bot deactivated."
    );
}

/**
 * Output Admin Area
 */
function whmcs_telegram_bot_output($vars) {
    $modulelink = $vars['modulelink'];
    $config = $vars['config'];
    
    // Get current settings
    $botToken = isset($config['bot_token']) ? $config['bot_token'] : '';
    $adminIds = isset($config['admin_ids']) ? $config['admin_ids'] : '';
    $defaultDept = isset($config['default_department']) ? $config['default_department'] : '1';
    
    // Generate webhook URL
    $baseUrl = rtrim(\App::getSystemUrl(), '/');
    $webhookUrl = $baseUrl . '/modules/addons/whmcs_telegram_bot/telegram_webhook.php';
    
    // Get link stats
    $stats = array('total' => 0);
    $result = full_query("SELECT COUNT(*) as total FROM `mod_whmcs_telegram_links`");
    if ($result && $data = mysql_fetch_assoc($result)) {
        $stats['total'] = $data['total'];
    }
    
    // Check webhook status
    $webhookStatus = '';
    if (!empty($botToken)) {
        $ch = curl_init("https://api.telegram.org/bot{$botToken}/getWebhookInfo");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);
        $webhookInfo = json_decode($response, true);
        
        if ($webhookInfo['ok'] && !empty($webhookInfo['result']['url'])) {
            $webhookStatus = '<div class="alert alert-success">✅ Webhook is active: ' . htmlspecialchars($webhookInfo['result']['url']) . '</div>';
        } else {
            $webhookStatus = '<div class="alert alert-warning">⚠️ Webhook not set. Click the button below to set it.</div>';
        }
    }
    
    echo <<<HTML
<style>
.telegram-admin-panel {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}
.telegram-admin-panel h2 {
    margin-top: 0;
    color: #2296f3;
}
.telegram-admin-panel .stats-box {
    display: flex;
    gap: 20px;
    margin: 20px 0;
}
.telegram-admin-panel .stat {
    background: #f5f5f5;
    padding: 15px 25px;
    border-radius: 8px;
    text-align: center;
}
.telegram-admin-panel .stat-number {
    font-size: 24px;
    font-weight: bold;
    color: #2296f3;
}
.telegram-admin-panel .stat-label {
    font-size: 12px;
    color: #666;
}
.telegram-admin-panel .help-box {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
    padding: 15px;
    margin: 20px 0;
}
.telegram-admin-panel .webhook-url {
    background: #fff3e0;
    border: 1px solid #ffb74d;
    padding: 10px;
    border-radius: 4px;
    font-family: monospace;
    word-break: break-all;
    margin: 10px 0;
}
.telegram-admin-panel .section {
    margin: 20px 0;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 5px;
}
.alert {
    padding: 10px 15px;
    border-radius: 4px;
    margin: 10px 0;
}
.alert-success {
    background: #d4edda;
    border-left: 4px solid #28a745;
    color: #155724;
}
.alert-warning {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    color: #856404;
}
.alert-error {
    background: #f8d7da;
    border-left: 4px solid #dc3545;
    color: #721c24;
}
</style>

<div class="telegram-admin-panel">
    <h2>🤖 WHMCS Telegram Bot</h2>
    
    {$webhookStatus}
    
    <div class="stats-box">
        <div class="stat">
            <div class="stat-number">{$stats['total']}</div>
            <div class="stat-label">Linked Users</div>
        </div>
    </div>
    
    <div class="section">
        <h3>Webhook URL</h3>
        <div class="webhook-url">{$webhookUrl}</div>
        
        <form method="post" action="https://api.telegram.org/bot{$botToken}/setWebhook" target="_blank">
            <input type="hidden" name="url" value="{$webhookUrl}">
            <button type="submit" class="btn btn-primary">Set Webhook with Telegram</button>
        </form>
    </div>
    
    <div class="help-box">
        <strong>Setup Instructions:</strong><br>
        1. Create a bot at @BotFather on Telegram<br>
        2. Copy the bot token and paste in the configuration above<br>
        3. Set commands at @BotFather: /start, /balance, /invoices, /services, /domains, /support<br>
        4. Click "Set Webhook with Telegram" above<br>
        5. Share the linking page with clients (see below)
    </div>
    
    <div class="section">
        <h3>Client Linking Page</h3>
        <p>Share this URL with clients to link their Telegram account:</p>
        <div class="webhook-url">{$webhookUrl}?action=link</div>
        
        <p><strong>Template Code:</strong></p>
        <div class="webhook-url">&lt;a href="{$webhookUrl}?action=link" class="btn btn-primary"&gt;Connect Telegram&lt;/a&gt;</div>
    </div>
</div>
HTML;
}