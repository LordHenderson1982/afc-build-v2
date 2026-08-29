<?php
/**
 * IPTV Support Helper Bot
 * 
 * Lives in your Telegram group, answers common questions
 * Uses button menus for easy navigation
 * 
 * Setup:
 * 1. Create bot via @BotFather
 * 2. Add bot to your group
 * 3. Set webhook: https://api.telegram.org/bot<TOKEN>/setWebhook?url=<YOUR_URL>
 * 4. Edit faq.php to add your questions/answers
 */

$botToken = '8948021349:AAH61h2ZzEVwCF90d6wpBj-slHXRc2e7jPc'; // Replace with your bot token
$adminId = '572118258'; // Your Telegram ID for admin alerts

// Load FAQ data
$faq = include 'faq.php';

// Get update from Telegram
$update = json_decode(file_get_contents('php://input'), true);

if (!$update) {
    exit('OK');
}

// Handle callback queries (button presses)
if (isset($update['callback_query'])) {
    handleCallback($update['callback_query'], $botToken, $faq);
    exit;
}

// Handle regular messages
if (isset($update['message'])) {
    $msg = $update['message'];
    $chatId = $msg['chat']['id'];
    $text = $msg['text'] ?? '';
    
    // Ignore bot commands
    if (strpos($text, '/') === 0) {
        exit;
    }
    
    // Respond in both groups and DMs
    // Check for keyword matches
    $answer = findBestAnswer($text, $faq);
    if ($answer) {
        sendMessage($chatId, $answer, $botToken);
    }
    
    // Check for /menu command or 'help' keyword anywhere
    if ($text === '/menu' || $text === 'help' || $text === 'hey' || $text === 'hi') {
        showMainMenu($chatId, $botToken);
    }
}

/**
 * Show main menu with categories
 */
function showMainMenu($chatId, $botToken) {
    // Text menu (simpler, more reliable)
    $text = "📚 *How can I help?*\n\n" .
        "💳 *How to Pay* - say 'how to pay'\n" .
        "💰 *Add Funds* - say 'add funds'\n" .
        "📱 *Firestick* - say 'firestick'\n" .
        "📱 *Android* - say 'android'\n" .
        "🔮 *Portal* - say 'portal'\n" .
        "🔗 *Link Telegram* - say 'link telegram'\n" .
        "🛑 *Buffering* - say 'buffering'\n" .
        "📡 *Channels* - say 'channels down'\n" .
        "🔄 *Renew* - say 'renew'\n" .
        "🔐 *Login* - say 'login'\n" .
        "📞 *Support* - say 'support'\n\n" .
        "Just ask me about any of these!";
    
    sendMessage($chatId, $text, $botToken);
}

/**
 * Show FAQ article
 */
function showArticle($chatId, $articleKey, $botToken, $faq) {
    if (!isset($faq[$articleKey])) {
        sendMessage($chatId, "Article not found.", $botToken);
        return;
    }
    
    $article = $faq[$articleKey];
    
    $text = $article['answer'];
    
    $keyboard = array(
        array(array('text' => '🔙 Back to Menu', 'callback_data' => 'menu'))
    );
    
    sendKeyboard($chatId, $text, $keyboard, $botToken, true);
}

/**
 * Find best matching answer based on keywords
 */
function findBestAnswer($text, $faq) {
    $text = strtolower($text);
    $bestMatch = null;
    $bestScore = 0;
    
    foreach ($faq as $key => $item) {
        $score = 0;
        foreach ($item['keywords'] as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                $score++;
            }
        }
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestMatch = $item['answer'];
        }
    }
    
    // Only return if we have a decent match
    return ($bestScore > 0) ? $bestMatch : null;
}

/**
 * Handle callback queries (button presses)
 */
function handleCallback($callback, $botToken, $faq) {
    $callbackId = $callback['id'];
    $chatId = $callback['message']['chat']['id'];
    $data = $callback['data'] ?? '';
    
    // Answer immediately to stop loading
    $url = "https://api.telegram.org/bot{$botToken}/answerCallbackQuery";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('callback_query_id' => $callbackId));
    curl_exec($ch);
    curl_close($ch);
    
    // Main menu
    if ($data === 'menu' || $data === 'main_menu') {
        showMainMenu($chatId, $botToken);
        return;
    }
    
    // Category/article buttons
    $articleMap = array(
        'cat_payment' => 'how_to_pay',
        'cat_addfunds' => 'add_funds',
        'cat_firestick' => 'firestick',
        'cat_android' => 'android',
        'cat_portal' => 'portal',
        'cat_link_telegram' => 'link_telegram',
        'cat_buffering' => 'buffering',
        'cat_channels' => 'channels_not_working',
        'cat_no_streams' => 'no_streams',
        'cat_renew' => 'renew',
        'cat_login' => 'login_issue',
        'cat_password' => 'reset_password',
        'cat_details' => 'login_details',
        'cat_support' => 'contact_support',
    );
    
    if (isset($articleMap[$data])) {
        showArticle($chatId, $articleMap[$data], $botToken, $faq);
    }
}

/**
 * Send message
 */
function sendMessage($chatId, $text, $botToken) {
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
    curl_exec($ch);
    curl_close($ch);
}

/**
 * Send keyboard message
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
    curl_exec($ch);
    curl_close($ch);
}
