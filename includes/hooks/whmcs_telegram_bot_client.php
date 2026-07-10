<?php
/**
 * WHMCS Telegram Bot - Client Area Hook
 * Automatically adds Telegram link to client area
 */

use WHMCS\Database\Capsule;

add_hook('ClientAreaFooterOutput', 1, function($vars) {
    // Check if user is logged in
    if (!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
        return '';
    }
    
    $clientId = (int)$_SESSION['uid'];
    
    // Check if already linked
    try {
        $link = Capsule::table('mod_whmcs_telegram_links')
            ->where('client_id', $clientId)
            ->first();
        
        $isLinked = !empty($link);
    } catch (Exception $e) {
        $isLinked = false;
    }
    
    // Get bot info
    $botUsername = '';
    try {
        $config = Capsule::table('tbladdonmodules')
            ->where('module', 'whmcs_telegram_bot')
            ->where('setting', 'bot_token')
            ->first();
        
        if ($config && !empty($config->value)) {
            $botToken = $config->value;
            $botInfo = @json_decode(@file_get_contents("https://api.telegram.org/bot{$botToken}/getMe"), true);
            if ($botInfo && isset($botInfo['result']['username'])) {
                $botUsername = $botInfo['result']['username'];
            }
        }
    } catch (Exception $e) {}
    
    if (empty($botUsername)) {
        return '';
    }
    
    // Build the link
    $linkUrl = '/modules/addons/whmcs_telegram_bot/link.php';
    
    if ($isLinked) {
        return <<<HTML
<style>
.telegram-link-btn {
    display: inline-block;
    padding: 8px 16px;
    background: #0088cc;
    color: white !important;
    border-radius: 4px;
    text-decoration: none;
    font-size: 13px;
    margin: 10px 0;
}
.telegram-link-btn:hover {
    background: #0077b3;
    color: white;
}
</style>
<span class="telegram-link-btn">✅ Telegram Connected</span>
HTML;
    }
    
    return <<<HTML
<style>
.telegram-link-btn {
    display: inline-block;
    padding: 8px 16px;
    background: #0088cc;
    color: white !important;
    border-radius: 4px;
    text-decoration: none;
    font-size: 13px;
    margin: 10px 0;
}
.telegram-link-btn:hover {
    background: #0077b3;
    color: white;
}
</style>
<a href="{$linkUrl}?action=link" class="telegram-link-btn">📱 Connect Telegram</a>
HTML;
});

/**
 * Add to sidebar as a widget
 */
add_hook('ClientAreaSidebarNav', 1, function($vars) {
    if (!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
        return '';
    }
    
    $clientId = (int)$_SESSION['uid'];
    
    try {
        $link = Capsule::table('mod_whmcs_telegram_links')
            ->where('client_id', $clientId)
            ->first();
        $isLinked = !empty($link);
    } catch (Exception $e) {
        $isLinked = false;
    }
    
    $botUsername = '';
    try {
        $config = Capsule::table('tbladdonmodules')
            ->where('module', 'whmcs_telegram_bot')
            ->where('setting', 'bot_token')
            ->first();
        
        if ($config && !empty($config->value)) {
            $botToken = $config->value;
            $botInfo = @json_decode(@file_get_contents("https://api.telegram.org/bot{$botToken}/getMe"), true);
            if ($botInfo && isset($botInfo['result']['username'])) {
                $botUsername = $botInfo['result']['username'];
            }
        }
    } catch (Exception $e) {}
    
    if (empty($botUsername)) {
        return '';
    }
    
    $linkUrl = '/modules/addons/whmcs_telegram_bot/link.php';
    
    if ($isLinked) {
        return '<div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">📱 Telegram</h3>
            </div>
            <div class="panel-body text-center">
                <span class="label label-success">✅ Connected</span>
            </div>
        </div>';
    }
    
    return '<div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">📱 Telegram</h3>
        </div>
        <div class="panel-body text-center">
            <p>Connect Telegram to manage your account via bot</p>
            <a href="' . $linkUrl . '?action=link" class="btn btn-primary btn-sm btn-block">Connect Telegram</a>
        </div>
    </div>';
});
