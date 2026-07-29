<?php
/**
 * Telegram Bridge Webhook - English Only Version
 * Simplified: no language switching, English only
 */

// Bootstrap WHMCS
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

use WHMCS\Database\Capsule;

// Load module functions
require_once __DIR__ . '/telegrambridge.php';
require_once __DIR__ . '/helpers.php';

// Read raw POST body
$input = file_get_contents('php://input');
$update = json_decode($input, true);

if (!$update) {
    http_response_code(400);
    die('Invalid JSON');
}

logModuleCall('telegrambridge', 'webhook', $update, 'Received update');

try {
    $message = null;
    $chatId = null;
    
    if (isset($update['message'])) {
        $message = $update['message'];
        $chatId = $message['chat']['id'];
    } elseif (isset($update['callback_query'])) {
        $callbackQuery = $update['callback_query'];
        $message = $callbackQuery['message'];
        $chatId = $message['chat']['id'];
    }
    
    if (!$message || !$chatId) {
        http_response_code(200);
        die('OK');
    }
    
    $rawText = isset($message['text']) ? trim($message['text']) : '';
    $callbackData = isset($update['callback_query']['data']) ? $update['callback_query']['data'] : '';
    
    $text = $rawText;
    $isButtonPress = false;
    if (!empty($rawText) && strpos($rawText, '/') !== 0) {
        $mappedCommand = telegrambridge_mapButtonToCommand($rawText);
        if ($mappedCommand !== false) {
            $text = $mappedCommand;
            $isButtonPress = true;
        }
    }
    
    $supportState = !empty($text) ? getSupportState($chatId) : null;
    
    // Handle callback queries
    if (!empty($callbackData)) {
        if (isset($update['callback_query'])) {
            answerCallbackQuery($update['callback_query']['id']);
        }
        clearSupportState($chatId);
        
        if (strpos($callbackData, 'renew:') === 0) {
            handleServiceRenewalCallback($chatId, $callbackData);
        } elseif (strpos($callbackData, 'lang:') === 0) {
            // Language buttons - just answer, no-op in English-only version
        } elseif (strpos($callbackData, 'settings:') === 0) {
            handleSettingsCallback($chatId, $callbackData);
        } elseif (strpos($callbackData, 'pay_card:') === 0) {
            handlePayCardCallback($chatId, $callbackData);
        } elseif (strpos($callbackData, 'pay_usdt:') === 0) {
            handlePayUsdtCallback($chatId, $callbackData);
        } elseif (in_array($callbackData, ['invoices', 'balance', 'services', 'lastinvoice', 'kb'])) {
            if ($callbackData === 'invoices') handleInvoicesCommand($chatId);
            elseif ($callbackData === 'balance') handleBalanceCommand($chatId);
            elseif ($callbackData === 'services') handleServicesCommand($chatId);
            elseif ($callbackData === 'lastinvoice') handleLastInvoiceCommand($chatId);
            elseif ($callbackData === 'kb') handleKBCommand($chatId);
        }
    }
    // Handle text commands
    elseif (!empty($text)) {
        if (strpos($text, '/start') === 0) {
            handleStartCommand($chatId, $text);
        } elseif ($text === '/help') {
            handleHelpCommand($chatId);
        } elseif ($text === '/invoices') {
            clearSupportState($chatId);
            handleInvoicesCommand($chatId);
        } elseif ($text === '/balance') {
            clearSupportState($chatId);
            handleBalanceCommand($chatId);
        } elseif ($text === '/services') {
            clearSupportState($chatId);
            handleServicesCommand($chatId);
        } elseif ($text === '/lastinvoice') {
            clearSupportState($chatId);
            handleLastInvoiceCommand($chatId);
        } elseif ($text === '/kb') {
            clearSupportState($chatId);
            handleKBCommand($chatId);
        } elseif ($text === '/support') {
            clearSupportState($chatId);
            handleSupportCommand($chatId);
        } elseif ($text === '/done') {
            handleSupportDoneCommand($chatId);
        } elseif ($text === '/unlink') {
            clearSupportState($chatId);
            handleUnlinkCommand($chatId);
        } elseif ($text === '/settings') {
            clearSupportState($chatId);
            handleSettingsCommand($chatId);
        } elseif ($text === '/menu') {
            clearSupportState($chatId);
            handleMenuCommand($chatId);
        } elseif ($supportState && $supportState->state === 'awaiting_message' && strpos($text, '/') !== 0 && !$isButtonPress) {
            handleSupportMessage($chatId, $rawText, $supportState);
        } elseif (!empty($rawText) && strpos($text, '/') !== 0 && !$isButtonPress && !$supportState) {
            $clientId = telegrambridge_getClientIdByChatId($chatId);
            if ($clientId) {
                $replyMarkup = telegrambridge_getMainMenuKeyboard();
                telegrambridge_sendMessageToChat($chatId, telegrambridge_t('menu_show'), $replyMarkup);
            }
        } elseif (strpos($text, '/') === 0) {
            clearSupportState($chatId);
            handleUnknownCommand($chatId);
        }
    }
    
} catch (Exception $e) {
    logModuleCall('telegrambridge', 'webhook_error', $update, "Error: {$e->getMessage()}");
    if (isset($chatId) && $chatId) {
        try {
            telegrambridge_sendMessageToChat($chatId, telegrambridge_t('error_generic'));
        } catch (Exception $e2) {}
    }
}

http_response_code(200);
die('OK');

/**
 * Handle /start command
 */
function handleStartCommand($chatId, $text) {
    if (preg_match('/\/start\s+link_([a-f0-9]{32})/', $text, $matches)) {
        $token = $matches[1];
        
        $tokenRecord = Capsule::table('tg_link_tokens')
            ->where('token', $token)
            ->whereNull('used_at')
            ->first();
        
        if (!$tokenRecord) {
            telegrambridge_sendMessageToChat($chatId, telegrambridge_t('link_invalid'));
            return;
        }
        
        if ($tokenRecord->expires_at && strtotime($tokenRecord->expires_at) < time()) {
            telegrambridge_sendMessageToChat($chatId, telegrambridge_t('link_expired'));
            return;
        }
        
        $clientId = $tokenRecord->client_id;
        
        $existingLink = Capsule::table('tg_client_links')
            ->where('client_id', $clientId)
            ->where('chat_id', $chatId)
            ->first();
        
        if ($existingLink) {
            Capsule::table('tg_client_links')
                ->where('id', $existingLink->id)
                ->update([
                    'is_active' => 1,
                    'last_seen_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            Capsule::table('tg_client_links')->insert([
                'client_id' => $clientId,
                'chat_id' => $chatId,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'last_seen_at' => date('Y-m-d H:i:s'),
            ]);
        }
        
        Capsule::table('tg_link_tokens')
            ->where('id', $tokenRecord->id)
            ->update(['used_at' => date('Y-m-d H:i:s')]);
        
        $message = telegrambridge_t('welcome_linked');
        $replyMarkup = telegrambridge_getMainMenuKeyboard();
        telegrambridge_sendMessageToChat($chatId, $message, $replyMarkup);
    } else {
        $isLinked = telegrambridge_isLinked($chatId);
        
        if ($isLinked) {
            $message = telegrambridge_t('welcome_already_linked');
            $replyMarkup = telegrambridge_getMainMenuKeyboard();
        } else {
            $message = telegrambridge_t('need_link');
            $replyMarkup = null;
        }
        
        telegrambridge_sendMessageToChat($chatId, $message, $replyMarkup);
    }
}

/**
 * Handle /help command
 */
function handleHelpCommand($chatId) {
    $isLinked = telegrambridge_isLinked($chatId);
    $message = telegrambridge_t('help');
    if (!$isLinked) {
        $message .= "\n\n" . telegrambridge_t('need_link');
    }
    $replyMarkup = telegrambridge_getMainMenuKeyboard();
    telegrambridge_sendMessageToChat($chatId, $message, $replyMarkup);
}

/**
 * Handle /menu command
 */
function handleMenuCommand($chatId) {
    $isLinked = telegrambridge_isLinked($chatId);
    if ($isLinked) {
        $message = telegrambridge_t('menu_show');
        $replyMarkup = telegrambridge_getMainMenuKeyboard();
    } else {
        $message = telegrambridge_t('need_link');
        $replyMarkup = null;
    }
    telegrambridge_sendMessageToChat($chatId, $message, $replyMarkup);
}

/**
 * Handle /invoices command
 */
function handleInvoicesCommand($chatId) {
    $clientId = telegrambridge_getClientIdByChatId($chatId);
    
    if (!$clientId) {
        telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_not_linked_generic'));
        return;
    }
    
    $invoices = Capsule::table('tblinvoices')
        ->where('userid', $clientId)
        ->whereIn('status', ['Unpaid', 'Payment Pending'])
        ->orderBy('duedate', 'asc')
        ->get();
    
    if ($invoices->isEmpty()) {
        $message = telegrambridge_t('cmd_invoices_empty');
        $replyMarkup = telegrambridge_getMainMenuKeyboard();
        telegrambridge_sendMessageToChat($chatId, $message, $replyMarkup);
        return;
    }
    
    $systemUrl = telegrambridge_getSystemUrl();
    $message = telegrambridge_t('cmd_invoices_title') . "\n\n";
    
    $buttons = [];
    foreach ($invoices as $invoice) {
        $dueDate = date('Y-m-d', strtotime($invoice->duedate));
        $amount = number_format($invoice->total, 2, '.', ' ');
        $currency = $invoice->currencycode;
        
        $message .= telegrambridge_t('cmd_invoices_item', [
            ':invoiceid' => $invoice->id,
            ':amount' => $amount,
            ':currency' => $currency,
            ':duedate' => $dueDate,
        ]) . "\n";
    }
    
    $replyMarkup = telegrambridge_getMainMenuKeyboard();
    telegrambridge_sendMessageToChat($chatId, $message, $replyMarkup);
}

/**
 * Handle /balance command
 */
function handleBalanceCommand($chatId) {
    $clientId = telegrambridge_getClientIdByChatId($chatId);
    
    if (!$clientId) {
        telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_not_linked_generic'));
        return;
    }
    
    $client = Capsule::table('tblclients')->where('id', $clientId)->first();
    $credit = number_format($client->credit, 2, '.', ' ');
    
    $unpaid = Capsule::table('tblinvoices')
        ->where('userid', $clientId)
        ->whereIn('status', ['Unpaid', 'Payment Pending'])
        ->sum('total');
    
    $unpaidFormatted = number_format($unpaid, 2, '.', ' ');
    $currency = $client->currencycode;
    
    $message = telegrambridge_t('cmd_balance', [
        ':credit' => $credit,
        ':unpaid' => $unpaidFormatted,
        ':currency' => $currency,
    ]);
    
    $replyMarkup = telegrambridge_getMainMenuKeyboard();
    telegrambridge_sendMessageToChat($chatId, $message, $replyMarkup);
}

/**
 * Handle /services command
 */
function handleServicesCommand($chatId) {
    $clientId = telegrambridge_getClientIdByChatId($chatId);
    
    if (!$clientId) {
        telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_not_linked_generic'));
        return;
    }
    
    $services = Capsule::table('tblhosting')
        ->where('userid', $clientId)
        ->where('domainstatus', 'Active')
        ->orderBy('nextduedate', 'asc')
        ->get();
    
    if ($services->isEmpty()) {
        $message = telegrambridge_t('cmd_services_empty');
        $replyMarkup = telegrambridge_getMainMenuKeyboard();
        telegrambridge_sendMessageToChat($chatId, $message, $replyMarkup);
        return;
    }
    
    $message = telegrambridge_t('cmd_services_title') . "\n\n";
    
    foreach ($services as $service) {
        $dueDate = date('Y-m-d', strtotime($service->nextduedate));
        $message .= telegrambridge_t('cmd_services_item', [
            ':productname' => $service->productname,
            ':domain' => $service->domain,
            ':duedate' => $dueDate,
        ]) . "\n";
    }
    
    $replyMarkup = telegrambridge_getMainMenuKeyboard();
    telegrambridge_sendMessageToChat($chatId, $message, $replyMarkup);
}

/**
 * Handle /lastinvoice command
 */
function handleLastInvoiceCommand($chatId) {
    $clientId = telegrambridge_getClientIdByChatId($chatId);
    
    if (!$clientId) {
        telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_not_linked_generic'));
        return;
    }
    
    $invoice = Capsule::table('tblinvoices')
        ->where('userid', $clientId)
        ->orderBy('id', 'desc')
        ->first();
    
    if (!$invoice) {
        $message = telegrambridge_t('cmd_lastinvoice_empty');
        telegrambridge_sendMessageToChat($chatId, $message);
        return;
    }
    
    $systemUrl = telegrambridge_getSystemUrl();
    $statusText = ($invoice->status === 'Paid') ? telegrambridge_t('status_paid') : telegrambridge_t('status_unpaid');
    $dueDate = date('Y-m-d', strtotime($invoice->duedate));
    $amount = number_format($invoice->total, 2, '.', ' ');
    $currency = $invoice->currencycode;
    
    $message = telegrambridge_t('cmd_lastinvoice_title', [':invoiceid' => $invoice->id]) . "\n";
    $message .= telegrambridge_t('cmd_lastinvoice_status', [':status' => $statusText]) . "\n";
    $message .= telegrambridge_t('cmd_lastinvoice_amount', [':amount' => $amount, ':currency' => $currency]) . "\n";
    $message .= telegrambridge_t('cmd_lastinvoice_duedate', [':duedate' => $dueDate]);
    
    $replyMarkup = telegrambridge_getMainMenuKeyboard();
    telegrambridge_sendMessageToChat($chatId, $message, $replyMarkup);
}

/**
 * Handle /unlink command
 */
function handleUnlinkCommand($chatId) {
    $clientId = telegrambridge_getClientIdByChatId($chatId);
    
    if (!$clientId) {
        telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_not_linked'));
        return;
    }
    
    Capsule::table('tg_client_links')
        ->where('chat_id', $chatId)
        ->update(['is_active' => 0]);
    
    telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_unlinked'));
}

/**
 * Handle /settings command
 */
function handleSettingsCommand($chatId) {
    $clientId = telegrambridge_getClientIdByChatId($chatId);
    
    if (!$clientId) {
        telegrambridge_sendMessageToChat($chatId, telegrambridge_t('cmd_not_linked_generic'));
        return;
    }
    
    $prefs = Capsule::table('tg_client_prefs')
        ->where('client_id', $clientId)
        ->first();
    
    $newInvoice = $prefs && $prefs->notify_new_invoice ? 'ON' : 'OFF';
    $paidInvoice = $prefs && $prefs->notify_invoice_paid ? 'ON' : 'OFF';
    
    $message = telegrambridge_t('settings_header') . "\n\n";
    $message .= "🧾 New invoices: " . $newInvoice . "\n";
    $message .= "✅ Invoice paid: " . $paidInvoice;
    
    $replyMarkup = telegrambridge_getMainMenuKeyboard();
    telegrambridge_sendMessageToChat($chatId, $message, $replyMarkup);
}

/**
 * Handle unknown command
 */
function handleUnknownCommand($chatId) {
    telegrambridge_sendMessageToChat($chatId, telegrambridge_t('unknown_command'));
}

/**
 * Handle /kb command - Knowledge Base
 */
function handleKBCommand($chatId) {
    $kbFile = __DIR__ . '/../Machine/ticket-knowledge-base.md';
    
    if (!file_exists($kbFile)) {
        telegrambridge_sendMessageToChat($chatId, "📚 Knowledge Base is currently unavailable.");
        return;
    }
    
    $content = file_get_contents($kbFile);
    
    // Parse the markdown to extract Q&A sections
    $message = "📚 *Knowledge Base*\n\n";
    
    // Extract payment questions section
    if (strpos($content, '## Payment Questions') !== false) {
        $message .= "*Payment Methods:*\n\n";
        $message .= "We accept Credit/Debit, PayPal, Venmo & Google Pay via Shopify!\n";
        $message .= "\n*How to pay:*\n";
        $message .= "1. Visit: https://veilcreds.myshopify.com/password\n";
        $message .= "2. Password: `savage`\n";
        $message .= "3. Select product matching your credit amount\n";
        $message .= "4. Complete checkout & send screenshot\n";
        $message .= "5. Provide your veilhosts.shop account email\n";
    }
    
    $message .= "\n\n_For more help, type /support_";
    
    $replyMarkup = telegrambridge_getMainMenuKeyboard();
    telegrambridge_sendMessageToChat($chatId, $message, $replyMarkup);
}
