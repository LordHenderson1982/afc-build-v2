<?php
/**
 * WHMCS Telegram Bot - Database Setup
 * Run this file directly to create required tables
 * Access: https://veilhosts.shop/modules/addons/whmcs_telegram_bot/install.php
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
    die("Connection failed: " . $conn->connect_error);
}

echo "<h1>WHMCS Telegram Bot - Database Setup</h1>";

// Create mod_whmcs_telegram_links table
$sql1 = "CREATE TABLE IF NOT EXISTS `mod_whmcs_telegram_links` (
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

if ($conn->query($sql1)) {
    echo "<p>✅ Table 'mod_whmcs_telegram_links' created successfully</p>";
} else {
    echo "<p>❌ Error creating mod_whmcs_telegram_links: " . $conn->error . "</p>";
}

// Create mod_whmcs_telegram_pending table
$sql2 = "CREATE TABLE IF NOT EXISTS `mod_whmcs_telegram_pending` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `token` VARCHAR(64) NOT NULL,
    `client_id` INT(10) UNSIGNED NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME NOT NULL,
    UNIQUE KEY `token` (`token`),
    KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql2)) {
    echo "<p>✅ Table 'mod_whmcs_telegram_pending' created successfully</p>";
} else {
    echo "<p>❌ Error creating mod_whmcs_telegram_pending: " . $conn->error . "</p>";
}

// Check if addon module config exists, create if not
$checkConfig = $conn->query("SELECT COUNT(*) as cnt FROM tbladdonmodules WHERE module = 'whmcs_telegram_bot'");

if ($checkConfig && $checkConfig->fetch_assoc()['cnt'] == 0) {
    echo "<p>⚠️ Note: Module not registered in WHMCS. Please activate the addon in WHMCS Admin first.</p>";
} else {
    echo "<p>✅ Module configuration found in WHMCS</p>";
    
    // Show current config
    echo "<h2>Current Configuration</h2>";
    $config = $conn->query("SELECT setting, value FROM tbladdonmodules WHERE module = 'whmcs_telegram_bot'");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Setting</th><th>Value</th></tr>";
    while ($row = $config->fetch_assoc()) {
        $displayValue = $row['setting'] === 'bot_token' && !empty($row['value']) ? '(configured)' : $row['value'];
        echo "<tr><td>" . htmlspecialchars($row['setting']) . "</td><td>" . htmlspecialchars($displayValue) . "</td></tr>";
    }
    echo "</table>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Go to WHMCS Admin → Addon Modules →WHMCS Telegram Bot</li>";
echo "<li>Configure your Telegram Bot Token</li>";
echo "<li>Click 'Set Webhook' to register the webhook with Telegram</li>";
echo "</ol>";

$conn->close();
echo "<p><em>Setup complete!</em></p>";