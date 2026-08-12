<?php
/**
 * Telegram Bridge - English Only Version
 * Simplified helpers.php - no language switching
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/**
 * Get client ID by Telegram chat ID
 */
function telegrambridge_getClientIdByChatId($chatId) {
    $link = Capsule::table('tg_client_links')
        ->where('chat_id', $chatId)
        ->where('is_active', 1)
        ->first();
    
    if (!$link) {
        return false;
    }
    
    Capsule::table('tg_client_links')
        ->where('id', $link->id)
        ->update(['last_seen_at' => date('Y-m-d H:i:s')]);
    
    return $link->client_id;
}

/**
 * Check if user is linked
 */
function telegrambridge_isLinked($chatId) {
    return telegrambridge_getClientIdByChatId($chatId) !== false;
}

/**
 * Get welcome message - English only
 */
function telegrambridge_getWelcomeMessage($isLinked = true) {
    if ($isLinked) {
        return "✅ Your Telegram has been linked to your account.\n" .
               "You can now receive invoices, payment notifications, view your balance and manage services here.\n\n" .
               "Available commands:\n" .
               "/invoices – Your unpaid invoices\n" .
               "/lastinvoice – Last invoice\n" .
               "/balance – Balance and credit\n" .
               "/services – Active services\n" .
               "/support – Create support ticket\n" .
               "/unlink – Unlink Telegram\n" .
               "/help – Help and command list";
    } else {
        return "👋 Welcome!\n\n" .
               "To link Telegram to your account, you need to get a link from support.\n\n" .
               "Available commands:\n" .
               "/help – See all commands";
    }
}

/**
 * Get help message - English only
 */
function telegrambridge_getHelpMessage($isLinked = true) {
    $msg = "📋 Available commands:\n\n" .
           "/invoices – Your unpaid invoices\n" .
           "/lastinvoice – Last invoice\n" .
           "/balance – Balance and credit\n" .
           "/services – Active services (paid until)\n" .
           "/support – Create support ticket\n" .
           "/unlink – Unlink Telegram\n" .
           "/settings – Settings\n" .
           "/help – Help and command list\n\n";
    
    if (!$isLinked) {
        $msg .= "⚠️ To use these commands, you must first link Telegram to your account.\n" .
                "Ask support for a link to connect.";
    }
    
    return $msg;
}

/**
 * Translation function - English only
 */
function telegrambridge_t($key, $params = []) {
    $messages = [
        'welcome_linked' => "👋 Hello! I'm your BillingBot.\n\n✅ Your Telegram has been linked to your account.\nYou can now receive invoices, payment notifications, view your balance and manage services here.\n\nChoose an action below or use commands:\n/invoices – Your unpaid invoices\n/lastinvoice – Last invoice\n/balance – Balance and credit\n/services – Active services\n/support – Create support ticket\n/settings – Notification settings\n/help – Help and command list",
        'welcome_already_linked' => "👋 Hello! You're already linked to your account.\n\nChoose an action below or use commands.",
        'need_link' => "To use the bot, first link Telegram to your account. Ask support for a link.",
        'help' => "📋 Available commands:\n\n/invoices – Your unpaid invoices\n/lastinvoice – Last invoice\n/balance – Balance and credit\n/services – Active services (paid until)\n/support – Create support ticket\n/unlink – Unlink Telegram\n/settings – Settings\n/help – Help and command list",
        'cmd_invoices_title' => "🧾 Your unpaid invoices:",
        'cmd_invoices_empty' => "✅ You have no unpaid invoices right now.",
        'cmd_invoices_item' => "• ##:invoiceid — :amount :currency, due :duedate",
        'cmd_lastinvoice_title' => "🧾 Last invoice ##:invoiceid",
        'cmd_lastinvoice_empty' => "You don't have any invoices yet.",
        'cmd_lastinvoice_status' => "Status: :status",
        'cmd_lastinvoice_amount' => "Amount: :amount :currency",
        'cmd_lastinvoice_duedate' => "Due date: :duedate",
        'cmd_balance' => "💰 Balance:\n\nCredit on account: :credit :currency\nUnpaid invoices total: :unpaid :currency",
        'cmd_services_title' => "🔧 Your active services:",
        'cmd_services_empty' => "You have no active services.",
        'cmd_services_item' => "• :productname (:domain) — paid until :duedate",
        'cmd_support_prompt' => "✉️ Please write the details of your request in one or more messages.\nWhen finished, send `/done`.",
        'cmd_support_message_added' => "✅ Message added. Send `/done` when finished, or continue writing.",
        'cmd_support_empty' => "❌ Message is empty. Please send your request text before `/done`.",
        'cmd_support_not_started' => "❌ You haven't started a support ticket. Use `/support` to begin.",
        'cmd_support_created' => "✅ Your ticket ##:ticketid has been created.\nOur support will respond as soon as possible.",
        'cmd_support_error' => "❌ Error creating ticket. Please try again later or contact support another way.",
        'cmd_unlinked' => "🔓 This Telegram has been successfully unlinked from your account.\nTo link again – ask support for a new link.",
        'cmd_not_linked' => "❌ This Telegram is not linked to any account.",
        'cmd_not_linked_generic' => "❌ To use this command, first link Telegram to your account.\n\nAsk support for a link to connect.",
        'error_generic' => "❌ An error occurred processing your request. Please try again later.",
        'error_invalid_data' => "❌ Error: invalid data.",
        'error_service_not_found' => "❌ Service not found or does not belong to you.",
        'error_client_not_found' => "❌ Error: client not found.",
        'error_invoice_exists' => "ℹ️ There's already an unpaid invoice for this service ##:invoiceid.\nAmount: :amount :currency",
        'error_invoice_creation' => "❌ Error creating invoice. Please try again later or contact support.",
        'invoice_created_notify' => "🧾 New invoice ##:invoiceid created\nAmount: :amount :currency\nDue date: :duedate\n\nView invoice: :link",
        'invoice_paid_notify' => "✅ Payment received for invoice ##:invoiceid.\nThank you!\n\nView invoice: :link",
        'invoice_renewal_created' => "🧾 Invoice ##:invoiceid created for renewing :productname (:domain).\n\nAmount: :amount :currency\n\nView invoice: :link",
        'settings_header' => "⚙️ Settings:",
        'settings_notify_new_invoice_on' => "🧾 New invoices: ENABLED",
        'settings_notify_new_invoice_off' => "🧾 New invoices: DISABLED",
        'settings_notify_invoice_paid_on' => "✅ Invoice paid: ENABLED",
        'settings_notify_invoice_paid_off' => "✅ Invoice paid: DISABLED",
        'link_invalid' => "❌ Link invalid or expired.\n\nAsk support for a new link.",
        'link_expired' => "❌ Link expired.\n\nAsk support for a new link.",
        'unknown_command' => "❓ Unknown command.",
        'status_paid' => "Paid",
        'status_unpaid' => "Unpaid",
        'pay_card_button' => "💳 Pay by Card",
        'pay_usdt_button' => "💰 Pay with USDT TRC20",
        'pay_card_instructions' => "💳 Pay by Card\n\nAmount: :amount :currency\nCard number: :card_number\nIBAN: :iban\nRecipient: :recipient_name\n\nPayment reference:\n:payment_reference\n\nCopy the amount and reference in your banking app.",
        'pay_usdt_instructions' => "💰 Pay with USDT TRC20\n\nSend exactly: :amount USDT (TRC20)\nTo address: :tron_address\n\nImportant:\n• Do not round the amount\n• Network: TRON (TRC20)",
        'pay_usdt_qr_caption' => "💰 QR code for payment\n\nAmount: :amount USDT\nAddress: :tron_address",
        'error_payment_data' => "❌ Error getting payment data. Please try again later.",
        'menu_show' => "Here's the main menu. Choose an action below.",
    ];
    
    $message = isset($messages[$key]) ? $messages[$key] : $key;
    
    foreach ($params as $k => $v) {
        $message = str_replace($k, $v, $message);
    }
    
    return $message;
}
