<?php
/**
 * WHMCS Telegram Bot - Hooks
 * Send notifications to Telegram users
 */

use WHMCS\Database\Capsule;

/**
 * Hook: AfterInvoiceCreation
 */
function whmcs_telegram_after_invoice_created($vars) {
    $invoiceId = $vars['invoiceid'];
    $userId = $vars['userid'];
    
    sendTelegramNotification($userId, 'invoice_created', $invoiceId);
}

/**
 * Hook: AfterPaymentReceived
 */
function whmcs_telegram_after_payment_received($vars) {
    $invoiceId = $vars['invoiceid'];
    $userId = $vars['userid'];
    $amount = $vars['amount'];
    
    sendTelegramNotification($userId, 'payment_received', $invoiceId, $amount);
}

/**
 * Hook: AfterServiceCreate
 */
function whmcs_telegram_after_service_create($vars) {
    $serviceId = $vars['serviceid'];
    $userId = $vars['userid'];
    
    sendTelegramNotification($userId, 'service_created', $serviceId);
}

/**
 * Hook: AfterServiceSuspend
 */
function whmcs_telegram_after_service_suspend($vars) {
    $serviceId = $vars['serviceid'];
    $userId = $vars['userid'];
    
    sendTelegramNotification($userId, 'service_suspended', $serviceId);
}

/**
 * Hook: AfterServiceUnsuspend
 */
function whmcs_telegram_after_service_unsuspend($vars) {
    $serviceId = $vars['serviceid'];
    $userId = $vars['userid'];
    
    sendTelegramNotification($userId, 'service_unsuspended', $serviceId);
}

/**
 * Hook: AfterServiceTerminate
 */
function whmcs_telegram_after_service_terminate($vars) {
    $serviceId = $vars['serviceid'];
    $userId = $vars['userid'];
    
    sendTelegramNotification($userId, 'service_terminated', $serviceId);
}

/**
 * Hook: AfterDomainRegistration
 */
function whmcs_telegram_after_domain_register($vars) {
    $domainId = $vars['domainid'];
    $userId = $vars['userid'];
    
    sendTelegramNotification($userId, 'domain_registered', $domainId);
}

/**
 * Hook: AfterDomainTransfer
 */
function whmcs_telegram_after_domain_transfer($vars) {
    $domainId = $vars['domainid'];
    $userId = $vars['userid'];
    
    sendTelegramNotification($userId, 'domain_transferred', $domainId);
}

/**
 * Hook: AfterDomainRenewal
 */
function whmcs_telegram_after_domain_renewal($vars) {
    $domainId = $vars['domainid'];
    $userId = $vars['userid'];
    
    sendTelegramNotification($userId, 'domain_renewed', $domainId);
}

/**
 * Hook: AfterSupportTicketOpen
 */
function whmcs_telegram_after_ticket_open($vars) {
    $ticketId = $vars['ticketid'];
    $userId = $vars['userid'];
    
    sendTelegramNotification($userId, 'ticket_created', $ticketId);
}

/**
 * Hook: AfterSupportTicketReply
 */
function whmcs_telegram_after_ticket_reply($vars) {
    $ticketId = $vars['ticketid'];
    $userId = $vars['userid'];
    
    sendTelegramNotification($userId, 'ticket_reply', $ticketId);
}

/**
 * Send notification to linked Telegram user
 */
function sendTelegramNotification($clientId, $type, $entityId, $extra = null) {
    // Get bot token
    $botToken = getTelegramBotToken();
    if (empty($botToken)) {
        return;
    }
    
    // Get user's Telegram chat ID
    $chatId = getTelegramChatId($clientId);
    if (empty($chatId)) {
        return;
    }
    
    // Get client details
    $client = getClientDetails($clientId);
    if (empty($client)) {
        return;
    }
    
    // Build message based on type
    $message = buildNotificationMessage($type, $entityId, $client, $extra);
    
    if ($message) {
        sendTelegramMessage($chatId, $message, $botToken);
    }
}

/**
 * Get bot token from config
 */
function getTelegramBotToken() {
    try {
        $config = Capsule::table('tbladdonmodules')
            ->where('module', 'whmcs_telegram_bot')
            ->where('setting', 'bot_token')
            ->first();
        
        return $config ? $config->value : '';
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Get Telegram chat ID for a client
 */
function getTelegramChatId($clientId) {
    try {
        $link = Capsule::table('mod_whmcs_telegram_links')
            ->where('client_id', $clientId)
            ->first();
        
        return $link ? $link->chat_id : null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Get client details
 */
function getClientDetails($clientId) {
    try {
        $client = Capsule::table('tblclients')
            ->where('id', $clientId)
            ->first();
        
        return $client ? (array)$client : null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Get invoice details
 */
function getInvoiceDetails($invoiceId) {
    try {
        $invoice = Capsule::table('tblinvoices')
            ->where('id', $invoiceId)
            ->first();
        
        return $invoice ? (array)$invoice : null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Get service details
 */
function getServiceDetails($serviceId) {
    try {
        $service = Capsule::table('tblhosting')
            ->where('id', $serviceId)
            ->first();
        
        if ($service) {
            $product = Capsule::table('tblproducts')
                ->where('id', $service->packageid)
                ->first();
            
            $result = (array)$service;
            $result['product_name'] = $product ? $product->name : 'Service';
            return $result;
        }
        return null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Get domain details
 */
function getDomainDetails($domainId) {
    try {
        $domain = Capsule::table('tbldomains')
            ->where('id', $domainId)
            ->first();
        
        return $domain ? (array)$domain : null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Get ticket details
 */
function getTicketDetails($ticketId) {
    try {
        $ticket = Capsule::table('tbltickets')
            ->where('id', $ticketId)
            ->first();
        
        return $ticket ? (array)$ticket : null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Format currency
 */
function formatCurrencyAmount($amount, $currencyId) {
    try {
        $currency = Capsule::table('tblcurrencies')
            ->where('id', $currencyId)
            ->first();
        
        if ($currency) {
            return $currency->prefix . number_format($amount, 2) . $currency->suffix;
        }
    } catch (Exception $e) {
        // Ignore
    }
    
    return '$' . number_format($amount, 2);
}

/**
 * Build notification message
 */
function buildNotificationMessage($type, $entityId, $client, $extra = null) {
    $name = $client['firstname'] ?? 'Valued Client';
    
    switch ($type) {
        case 'invoice_created':
            $invoice = getInvoiceDetails($entityId);
            if ($invoice) {
                $amount = formatCurrencyAmount($invoice['total'], $invoice['currency']);
                return "📄 *New Invoice Created*\n\nHi {$name}!\n\nA new invoice has been created for you.\n\nInvoice #{$invoice['id']}\nAmount: {$amount}\nDue: {$invoice['duedate']}\n\nPlease log in to pay.";
            }
            break;
            
        case 'payment_received':
            $invoice = getInvoiceDetails($entityId);
            if ($invoice) {
                $amount = formatCurrencyAmount($extra ?: $invoice['total'], $invoice['currency']);
                return "✅ *Payment Received*\n\nHi {$name}!\n\nThank you! We've received your payment of {$amount}.\n\nInvoice #{$invoice['id']} is now paid.";
            }
            break;
            
        case 'service_created':
            $service = getServiceDetails($entityId);
            if ($service) {
                return "🖥️ *New Service Activated*\n\nHi {$name}!\n\nYour new service is now active!\n\n{$service['product_name']}\nDomain: {$service['domain']}\n\nThank you for your order!";
            }
            break;
            
        case 'service_suspended':
            $service = getServiceDetails($entityId);
            if ($service) {
                return "⚠️ *Service Suspended*\n\nHi {$name}!\n\nYour service has been suspended due to non-payment.\n\n{$service['product_name']}\nDomain: {$service['domain']}\n\nPlease pay your outstanding invoice to restore service.";
            }
            break;
            
        case 'service_unsuspended':
            $service = getServiceDetails($entityId);
            if ($service) {
                return "✅ *Service Resumed*\n\nHi {$name}!\n\nGood news! Your service has been reactivated.\n\n{$service['product_name']}\nDomain: {$service['domain']}\n\nThank you!";
            }
            break;
            
        case 'service_terminated':
            $service = getServiceDetails($entityId);
            if ($service) {
                return "❌ *Service Terminated*\n\nHi {$name}!\n\nYour service has been terminated.\n\n{$service['product_name']}\nDomain: {$service['domain']}\n\nContact support if you have questions.";
            }
            break;
            
        case 'domain_registered':
            $domain = getDomainDetails($entityId);
            if ($domain) {
                return "🌐 *Domain Registered*\n\nHi {$name}!\n\nYour domain has been registered!\n\n{$domain['domain']}\nExpires: {$domain['expirydate']}";
            }
            break;
            
        case 'domain_transferred':
            $domain = getDomainDetails($entityId);
            if ($domain) {
                return "🌐 *Domain Transferred*\n\nHi {$name}!\n\nYour domain transfer is complete!\n\n{$domain['domain']}\nExpires: {$domain['expirydate']}";
            }
            break;
            
        case 'domain_renewed':
            $domain = getDomainDetails($entityId);
            if ($domain) {
                return "🌐 *Domain Renewed*\n\nHi {$name}!\n\nYour domain has been renewed!\n\n{$domain['domain']}\nNew Expiry: {$domain['expirydate']}";
            }
            break;
            
        case 'ticket_created':
            $ticket = getTicketDetails($entityId);
            if ($ticket) {
                return "🎫 *Support Ticket Created*\n\nHi {$name}!\n\nYour support ticket has been created.\n\nSubject: {$ticket['title']}\nTicket ID: #{$ticket['id']}\n\nWe will respond as soon as possible.";
            }
            break;
            
        case 'ticket_reply':
            $ticket = getTicketDetails($entityId);
            if ($ticket) {
                return "💬 *New Ticket Reply*\n\nHi {$name}!\n\nThere's a new reply to your support ticket.\n\nSubject: {$ticket['title']}\nTicket ID: #{$ticket['id']}\n\nLog in to view the response.";
            }
            break;
    }
    
    return null;
}

/**
 * Send message via Telegram API
 */
function sendTelegramMessage($chatId, $text, $botToken) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    
    $data = array(
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'Markdown'
    );
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
}