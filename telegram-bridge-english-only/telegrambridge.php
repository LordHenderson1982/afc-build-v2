<?php
/**
 * Telegram Bridge - English Only Config
 * Simplified telegrambridge.php - English only, no language switching
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/**
 * Module configuration
 */
function telegrambridge_config() {
    return [
        'name' => 'Telegram Bridge',
        'description' => 'Integrate WHMCS with Telegram Bot API for invoice notifications and client commands',
        'version' => '1.1',
        'author' => 'Your Name',
        'language' => 'english',
        'fields' => [
            'botToken' => [
                'FriendlyName' => 'Telegram Bot Token',
                'Type' => 'text',
                'Size' => '64',
                'Description' => 'Your Telegram bot token from @BotFather',
                'Required' => true,
            ],
            'botName' => [
                'FriendlyName' => 'Bot Username',
                'Type' => 'text',
                'Size' => '32',
                'Description' => 'Your bot username (without @)',
                'Required' => false,
            ],
            'systemUrl' => [
                'FriendlyName' => 'System URL (Optional)',
                'Type' => 'text',
                'Size' => '128',
                'Description' => 'Override SystemURL if different from WHMCS setting',
                'Required' => false,
            ],
            'supportDeptId' => [
                'FriendlyName' => 'Default Support Department ID',
                'Type' => 'text',
                'Size' => '10',
                'Description' => 'Default department ID for tickets created via /support command',
                'Required' => false,
            ],
        ],
    ];
}

/**
 * Module activation
 */
function telegrambridge_activate() {
    try {
        if (!Capsule::schema()->hasTable('tg_link_tokens')) {
            Capsule::schema()->create('tg_link_tokens', function ($table) {
                $table->increments('id');
                $table->integer('client_id')->unsigned();
                $table->string('token', 64)->unique();
                $table->dateTime('created_at');
                $table->dateTime('used_at')->nullable();
                $table->dateTime('expires_at')->nullable();
                $table->index('client_id');
                $table->index('token');
            });
        }
        
        if (!Capsule::schema()->hasTable('tg_client_links')) {
            Capsule::schema()->create('tg_client_links', function ($table) {
                $table->increments('id');
                $table->integer('client_id')->unsigned();
                $table->bigInteger('chat_id');
                $table->boolean('is_active')->default(true);
                $table->dateTime('created_at');
                $table->dateTime('last_seen_at')->nullable();
                $table->index('client_id');
                $table->index('chat_id');
                $table->unique(['client_id', 'chat_id']);
            });
        }
        
        if (!Capsule::schema()->hasTable('tg_support_state')) {
            Capsule::schema()->create('tg_support_state', function ($table) {
                $table->increments('id');
                $table->bigInteger('chat_id');
                $table->integer('client_id')->unsigned();
                $table->string('state', 32);
                $table->text('message_buffer')->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index('chat_id');
                $table->index('client_id');
                $table->index('state');
            });
        }
        
        if (!Capsule::schema()->hasTable('tg_client_prefs')) {
            Capsule::schema()->create('tg_client_prefs', function ($table) {
                $table->increments('id');
                $table->integer('client_id')->unsigned()->unique();
                $table->string('language', 5)->default('en');
                $table->boolean('notify_new_invoice')->default(true);
                $table->boolean('notify_invoice_paid')->default(true);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index('client_id');
            });
        }
        
        return ['status' => 'success', 'description' => 'Telegram Bridge module activated successfully.'];
    } catch (Exception $e) {
        return ['status' => 'error', 'description' => 'Failed to activate module: ' . $e->getMessage()];
    }
}

/**
 * Module deactivation
 */
function telegrambridge_deactivate() {
    return ['status' => 'success', 'description' => 'Telegram Bridge module deactivated. Data preserved.'];
}

/**
 * Get module configuration
 */
function telegrambridge_getConfig() {
    $config = [];
    $result = Capsule::table('tbladdonmodules')
        ->where('module', 'telegrambridge')
        ->get();
    
    foreach ($result as $row) {
        $config[$row->setting] = $row->value;
    }
    
    return $config;
}

/**
 * Get system URL
 */
function telegrambridge_getSystemUrl() {
    $config = telegrambridge_getConfig();
    if (!empty($config['systemUrl'])) {
        return rtrim($config['systemUrl'], '/');
    }
    return rtrim(\WHMCS\Config\Setting::getValue('SystemURL'), '/');
}

/**
 * Send message to Telegram chat
 */
function telegrambridge_sendMessageToChat($chatId, $text, $replyMarkup = null) {
    $config = telegrambridge_getConfig();
    if (empty($config['botToken'])) {
        return false;
    }
    
    $botToken = $config['botToken'];
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    
    $data = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML',
    ];
    
    if ($replyMarkup !== null) {
        $data['reply_markup'] = json_encode($replyMarkup);
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    return ($httpCode === 200 && isset($result['ok']) && $result['ok']);
}

/**
 * Send photo to Telegram chat
 */
function telegrambridge_sendPhotoToChat($chatId, $photoUrl, $caption = null) {
    $config = telegrambridge_getConfig();
    if (empty($config['botToken'])) {
        return false;
    }
    
    $botToken = $config['botToken'];
    $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";
    
    $data = [
        'chat_id' => $chatId,
        'photo' => $photoUrl,
    ];
    
    if ($caption !== null) {
        $data['caption'] = $caption;
        $data['parse_mode'] = 'HTML';
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    return ($httpCode === 200 && isset($result['ok']) && $result['ok']);
}

/**
 * Send message to WHMCS client via Telegram
 */
function telegrambridge_sendMessageToClient($clientId, $text, $replyMarkup = null) {
    $links = Capsule::table('tg_client_links')
        ->where('client_id', $clientId)
        ->where('is_active', 1)
        ->get();
    
    if ($links->isEmpty()) {
        return false;
    }
    
    $success = false;
    foreach ($links as $link) {
        if (telegrambridge_sendMessageToChat($link->chat_id, $text, $replyMarkup)) {
            Capsule::table('tg_client_links')
                ->where('id', $link->id)
                ->update(['last_seen_at' => date('Y-m-d H:i:s')]);
            $success = true;
        }
    }
    
    return $success;
}

/**
 * Generate link token for client
 */
function telegrambridge_generateLinkToken($clientId, $expiresInHours = 24) {
    try {
        $token = bin2hex(random_bytes(16));
        $expiresAt = date('Y-m-d H:i:s', time() + ($expiresInHours * 3600));
        
        Capsule::table('tg_link_tokens')->insert([
            'client_id' => $clientId,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => $expiresAt,
        ]);
        
        return $token;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get deep link URL for client
 */
function telegrambridge_getDeepLink($clientId) {
    $config = telegrambridge_getConfig();
    $botName = !empty($config['botName']) ? $config['botName'] : 'YourBot';
    
    $token = telegrambridge_generateLinkToken($clientId);
    if (!$token) {
        return false;
    }
    
    return "https://t.me/{$botName}?start=link_{$token}";
}

/**
 * Get main menu keyboard
 */
function telegrambridge_getMainMenuKeyboard() {
    return [
        'inline_keyboard' => [
            [['text' => '🧾 Invoices', 'callback_data' => 'invoices']],
            [['text' => '💰 Balance', 'callback_data' => 'balance']],
            [['text' => '🔧 Services', 'callback_data' => 'services']],
            [['text' => '📋 Last Invoice', 'callback_data' => 'lastinvoice']],
            [['text' => '📚 Knowledge Base', 'callback_data' => 'kb']],
        ]
    ];
}

/**
 * Map button text to command
 */
function telegrambridge_mapButtonToCommand($text) {
    $map = [
        '🧾 Invoices' => '/invoices',
        '💰 Balance' => '/balance',
        '🔧 Services' => '/services',
        '📋 Last Invoice' => '/lastinvoice',
        '📚 Knowledge Base' => '/kb',
    ];
    return isset($map[$text]) ? $map[$text] : false;
}

/**
 * Answer callback query
 */
function answerCallbackQuery($callbackQueryId) {
    $config = telegrambridge_getConfig();
    if (empty($config['botToken'])) return;
    
    $botToken = $config['botToken'];
    $url = "https://api.telegram.org/bot{$botToken}/answerCallbackQuery";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['callback_query_id' => $callbackQueryId]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

/**
 * Get or create client preferences
 */
function telegrambridge_getOrCreateClientPrefs($clientId) {
    $prefs = Capsule::table('tg_client_prefs')
        ->where('client_id', $clientId)
        ->first();
    
    if ($prefs) {
        return $prefs;
    }
    
    $now = date('Y-m-d H:i:s');
    Capsule::table('tg_client_prefs')->insert([
        'client_id' => $clientId,
        'language' => 'en',
        'notify_new_invoice' => 1,
        'notify_invoice_paid' => 1,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    
    return Capsule::table('tg_client_prefs')
        ->where('client_id', $clientId)
        ->first();
}

/**
 * Get support state
 */
function getSupportState($chatId) {
    return Capsule::table('tg_support_state')
        ->where('chat_id', $chatId)
        ->orderBy('id', 'desc')
        ->first();
}

/**
 * Clear support state
 */
function clearSupportState($chatId) {
    Capsule::table('tg_support_state')
        ->where('chat_id', $chatId)
        ->delete();
}

/**
 * Handle support command
 */
function handleSupportCommand($chatId) {
    $clientId = telegrambridge_getClientIdByChatId($chatId);
    if (!$clientId) {
        telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_not_linked_generic'));
        return;
    }
    
    $now = date('Y-m-d H:i:s');
    Capsule::table('tg_support_state')->insert([
        'chat_id' => $chatId,
        'client_id' => $clientId,
        'state' => 'awaiting_message',
        'message_buffer' => '',
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    
    telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_support_prompt'));
}

/**
 * Handle support message
 */
function handleSupportMessage($chatId, $text, $supportState) {
    $clientId = $supportState->client_id;
    $buffer = $supportState->message_buffer . $text . "\n";
    
    Capsule::table('tg_support_state')
        ->where('chat_id', $chatId)
        ->update([
            'message_buffer' => $buffer,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    
    telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_support_message_added'));
}

/**
 * Handle support done command
 */
function handleSupportDoneCommand($chatId) {
    $supportState = getSupportState($chatId);
    
    if (!$supportState) {
        telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_support_not_started'));
        return;
    }
    
    $message = trim($supportState->message_buffer);
    
    if (empty($message)) {
        telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_support_empty'));
        return;
    }
    
    $clientId = $supportState->client_id;
    clearSupportState($chatId);
    
    // Create ticket
    $config = telegrambridge_getConfig();
    $deptId = !empty($config['supportDeptId']) ? $config['supportDeptId'] : 1;
    
    $client = Capsule::table('tblclients')->where('id', $clientId)->first();
    
    try {
        $ticketId = Capsule::table('tbltickets')->insertGetId([
            'tid' => strtoupper(substr(md5(time()), 0, 7)),
            'userid' => $clientId,
            'contactid' => 0,
            'name' => $client->firstname . ' ' . $client->lastname,
            'email' => $client->email,
            'department' => $deptId,
            'subject' => 'Telegram Support Request',
            'message' => $message,
            'status' => 'Open',
            'priority' => 'Medium',
            'created' => date('Y-m-d H:i:s'),
            'lastreply' => '0000-00-00 00:00:00',
            'adminunread' => '1',
            'clientunread' => '1',
        ]);
        
        telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_support_created', [':ticketid' => $ticketId]));
    } catch (Exception $e) {
        telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_support_error'));
    }
}

/**
 * Handle service renewal callback
 */
function handleServiceRenewalCallback($chatId, $callbackData) {
    // Simplified - just acknowledge
}

/**
 * Handle settings callback
 */
function handleSettingsCallback($chatId, $callbackData) {
    handleSettingsCommand($chatId);
}

/**
 * Handle pay card callback
 */
function handlePayCardCallback($chatId, $callbackData) {
    // Simplified payment
}

/**
 * Handle pay USDT callback
 */
function handlePayUsdtCallback($chatId, $callbackData) {
    // Simplified payment
}

/**
 * Admin area output
 */
function telegrambridge_output($vars) {
    $action = isset($_GET['action']) ? $_GET['action'] : (isset($vars['action']) ? $vars['action'] : 'index');
    $clientId = isset($_GET['client_id']) ? intval($_GET['client_id']) : (isset($vars['client_id']) ? intval($vars['client_id']) : 0);
    
    $output = '<div class="telegrambridge-admin" style="padding: 20px;">';
    $output .= '<h2>Telegram Bridge</h2>';
    
    $config = telegrambridge_getConfig();
    if (empty($config['botToken'])) {
        $output .= '<div class="alert alert-warning">';
        $output .= '<strong>⚠️ Module not configured!</strong><br>';
        $output .= 'Please configure the module: <a href="configaddonmods.php?module=telegrambridge">Settings</a>';
        $output .= '</div>';
    } else {
        $output .= '<div class="alert alert-success">';
        $output .= '<strong>✅ Module configured</strong><br>';
        $output .= 'Bot: @' . htmlspecialchars($config['botName'] ?? 'N/A');
        $output .= '</div>';
    }
    
    if ($action === 'generate_link' && $clientId > 0) {
        $deepLink = telegrambridge_getDeepLink($clientId);
        if ($deepLink) {
            $output .= '<div class="alert alert-success">';
            $output .= '<h3>Deep Link Generated</h3>';
            $output .= '<p><strong>Client ID:</strong> ' . htmlspecialchars($clientId) . '</p>';
            $output .= '<p><strong>Deep Link:</strong></p>';
            $output .= '<textarea class="form-control" rows="3" readonly>' . htmlspecialchars($deepLink) . '</textarea>';
            $output .= '<p class="mt-2">Send this link to the client to link their Telegram account.</p>';
            $output .= '</div>';
        } else {
            $output .= '<div class="alert alert-danger">Failed to generate link token.</div>';
        }
    }
    
    $output .= '<div class="panel panel-default" style="margin-top: 20px;">';
    $output .= '<div class="panel-heading"><h3 class="panel-title">Generate Deep Link for Client</h3></div>';
    $output .= '<div class="panel-body">';
    $output .= '<form method="get" action="">';
    if (isset($_GET['module'])) {
        $output .= '<input type="hidden" name="module" value="' . htmlspecialchars($_GET['module']) . '">';
    }
    $output .= '<input type="hidden" name="action" value="generate_link">';
    $output .= '<div class="form-group">';
    $output .= '<label for="client_id">Client ID:</label>';
    $output .= '<input type="number" id="client_id" name="client_id" class="form-control" value="' . htmlspecialchars($clientId) . '" required style="max-width: 300px;">';
    $output .= '<small class="help-block">Enter client ID from WHMCS</small>';
    $output .= '</div>';
    $output .= '<button type="submit" class="btn btn-primary">Generate Link</button>';
    $output .= '</form>';
    $output .= '</div></div>';
    
    // Show linked clients
    $output .= '<div class="panel panel-default" style="margin-top: 20px;">';
    $output .= '<div class="panel-heading"><h3 class="panel-title">Linked Clients</h3></div>';
    $output .= '<div class="panel-body">';
    
    try {
        $links = Capsule::table('tg_client_links')
            ->join('tblclients', 'tg_client_links.client_id', '=', 'tblclients.id')
            ->select('tg_client_links.*', 'tblclients.firstname', 'tblclients.lastname', 'tblclients.email')
            ->where('tg_client_links.is_active', 1)
            ->orderBy('tg_client_links.last_seen_at', 'desc')
            ->limit(50)
            ->get();
        
        if ($links->count() > 0) {
            $output .= '<div class="table-responsive">';
            $output .= '<table class="table table-striped table-hover">';
            $output .= '<thead><tr><th>Client</th><th>Email</th><th>Chat ID</th><th>Last Activity</th><th>Created</th></tr></thead>';
            $output .= '<tbody>';
            foreach ($links as $link) {
                $output .= '<tr>';
                $output .= '<td>' . htmlspecialchars($link->firstname . ' ' . $link->lastname) . '</td>';
                $output .= '<td>' . htmlspecialchars($link->email) . '</td>';
                $output .= '<td><code>' . htmlspecialchars($link->chat_id) . '</code></td>';
                $output .= '<td>' . ($link->last_seen_at ? htmlspecialchars($link->last_seen_at) : 'Never') . '</td>';
                $output .= '<td>' . htmlspecialchars($link->created_at) . '</td>';
                $output .= '</tr>';
            }
            $output .= '</tbody></table></div>';
        } else {
            $output .= '<p>No linked clients yet.</p>';
        }
    } catch (Exception $e) {
        $output .= '<div class="alert alert-danger">Error loading data: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    
    $output .= '</div></div></div>';
    
    return $output;
}
