<?php
/**
 * Telegram Bridge Helper Functions
 * 
 * Shared helper functions used by webhook and hooks
 * 
 * @package    WHMCS
 * @author     Your Name
 * @copyright  Copyright (c) 2024
 * @license    Proprietary
 * @version    1.0
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/**
 * Get client ID by Telegram chat ID
 * 
 * @param int $chatId Telegram chat ID
 * @return int|false Client ID or false if not found
 */
function telegrambridge_getClientIdByChatId($chatId) {
    $link = Capsule::table('tg_client_links')
        ->where('chat_id', $chatId)
        ->where('is_active', 1)
        ->first();
    
    if (!$link) {
        return false;
    }
    
    // Update last_seen_at
    Capsule::table('tg_client_links')
        ->where('id', $link->id)
        ->update(['last_seen_at' => date('Y-m-d H:i:s')]);
    
    return $link->client_id;
}

/**
 * Check if user is linked
 * 
 * @param int $chatId Telegram chat ID
 * @return bool
 */
function telegrambridge_isLinked($chatId) {
    return telegrambridge_getClientIdByChatId($chatId) !== false;
}

/**
 * Get welcome message with all commands (English only - translation function handles others)
 * 
 * @param bool $isLinked Whether user is linked
 * @return string
 */
function telegrambridge_getWelcomeMessage($isLinked = true) {
    $message = "";
    
    if ($isLinked) {
        $message .= "✅ Your Telegram has been linked to your account.\n";
        $message .= "You can now receive invoices, payment notifications, view your balance and manage services here.\n\n";
    } else {
        $message .= "👋 Welcome!\n\n";
        $message .= "To link Telegram to your account, you need to get a link from support.\n\n";
    }
    
    $message .= "Available commands:\n";
    $message .= "/invoices – Your unpaid invoices\n";
    $message .= "/lastinvoice – Last invoice\n";
    $message .= "/balance – Balance and credit\n";
    $message .= "/services – Active services (paid until)\n";
    $message .= "/support – Create support ticket\n";
    $message .= "/unlink – Unlink Telegram\n";
    $message .= "/help – Help and command list";
    
    return $message;
}

/**
 * Get help message (English only - translation function handles others)
 * 
 * @param bool $isLinked Whether user is linked
 * @return string
 */
function telegrambridge_getHelpMessage($isLinked = true) {
    $message = "📋 Available commands:\n\n";
    $message .= "/invoices – Your unpaid invoices\n";
    $message .= "/lastinvoice – Last invoice\n";
    $message .= "/balance – Balance and credit\n";
    $message .= "/services – Active services (paid until) + renewal button\n";
    $message .= "/support – Create support ticket\n";
    $message .= "/unlink – Unlink Telegram\n";
    $message .= "/lang – Change language\n";
    $message .= "/settings – Settings\n";
    $message .= "/help – Help and command list\n\n";
    
    if (!$isLinked) {
        $message .= "⚠️ To use these commands, you must first link Telegram to your account.\n";
        $message .= "Ask support for a link to connect.";
    }
    
    return $message;
}

/**
 * Get client language preference
 * 
 * @param int $clientId Client ID
 * @return string Language code (ua, ru, en)
 */
function telegrambridge_getClientLanguage($clientId) {
    $prefs = Capsule::table('tg_client_prefs')
        ->where('client_id', $clientId)
        ->first();
    
    if ($prefs && !empty($prefs->language)) {
        return $prefs->language;
    }
    
    // Get default from config
    $config = telegrambridge_getConfig();
    $defaultLang = !empty($config['defaultLanguage']) ? $config['defaultLanguage'] : 'en';
    
    // Validate language code
    if (!in_array($defaultLang, ['ua', 'ru', 'en'])) {
        $defaultLang = 'en';
    }
    
    return $defaultLang;
}

/**
 * Get or create client preferences
 * 
 * @param int $clientId Client ID
 * @return object Preferences object
 */
function telegrambridge_getOrCreateClientPrefs($clientId) {
    $prefs = Capsule::table('tg_client_prefs')
        ->where('client_id', $clientId)
        ->first();
    
    if ($prefs) {
        return $prefs;
    }
    
    // Get default language from config
    $config = telegrambridge_getConfig();
    $defaultLang = !empty($config['defaultLanguage']) ? $config['defaultLanguage'] : 'en';
    
    // Validate language code
    if (!in_array($defaultLang, ['ua', 'ru', 'en'])) {
        $defaultLang = 'en';
    }
    
    // Try to get language from WHMCS client record
    $client = Capsule::table('tblclients')
        ->where('id', $clientId)
        ->first();
    
    if ($client && !empty($client->language)) {
        // Map WHMCS language codes to our codes
        $whmcsLang = strtolower($client->language);
        if (strpos($whmcsLang, 'ukrainian') !== false || strpos($whmcsLang, 'uk') !== false) {
            $defaultLang = 'ua';
        } elseif (strpos($whmcsLang, 'russian') !== false || strpos($whmcsLang, 'ru') !== false) {
            $defaultLang = 'ru';
        } elseif (strpos($whmcsLang, 'english') !== false || strpos($whmcsLang, 'en') !== false) {
            $defaultLang = 'en';
        }
    }
    
    // Create new preferences record
    $now = date('Y-m-d H:i:s');
    Capsule::table('tg_client_prefs')->insert([
        'client_id' => $clientId,
        'language' => $defaultLang,
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
 * Translation function
 * 
 * @param string $lang Language code (ua, ru, en)
 * @param string $key Translation key
 * @param array $params Parameters for placeholder replacement (e.g. [':invoiceid' => 123])
 * @return string Translated text
 */
function telegrambridge_t($lang, $key, array $params = []) {
    // Translation dictionary
    $messages = [
        'ua' => [
            'welcome_linked' => "👋 Вітаю! Я ваш BillingBot.\n\n✅ Ваш Telegram успішно прив'язано до особистого кабінету.\nТепер ви зможете отримувати рахунки, сповіщення про оплату, переглядати баланс та керувати послугами тут.\n\nОберіть дію нижче або скористайтеся командами:\n/invoices – Ваші неоплачені рахунки\n/lastinvoice – Останній рахунок\n/balance – Баланс та кредит\n/services – Активні послуги\n/support – Створити звернення до підтримки\n/lang – Вибір мови\n/settings – Налаштування повідомлень\n/help – Допомога та список команд",
            'welcome_already_linked' => "👋 Вітаю! Ви вже прив'язані до особистого кабінету.\n\nОберіть дію нижче або скористайтеся командами.",
            'need_link' => "Щоб користуватися ботом, спочатку прив'яжіть Telegram до вашого акаунту. Запитайте посилання у підтримки.",
            'help' => "📋 Доступні команди:\n\n/invoices – Ваші неоплачені рахунки\n/lastinvoice – Останній рахунок\n/balance – Баланс та кредит\n/services – Активні послуги (до якої дати оплачені) + кнопка продовження\n/support – Створити звернення до підтримки\n/unlink – Відв'язати Telegram\n/lang – Змінити мову\n/settings – Налаштування\n/help – Допомога та список команд",
            'cmd_invoices_title' => "🧾 Ваші неоплачені рахунки:",
            'cmd_invoices_empty' => "✅ У вас зараз немає неоплачених рахунків.",
            'cmd_invoices_item' => "• №:invoiceid — :amount :currency, до :duedate",
            'cmd_lastinvoice_title' => "🧾 Останній рахунок №:invoiceid",
            'cmd_lastinvoice_empty' => "У вас ще немає рахунків.",
            'cmd_lastinvoice_status' => "Статус: :status",
            'cmd_lastinvoice_amount' => "Сума: :amount :currency",
            'cmd_lastinvoice_duedate' => "Термін оплати: :duedate",
            'cmd_balance' => "💰 Баланс:\n\nКредит на акаунті: :credit :currency\nСума неоплачених рахунків: :unpaid :currency",
            'cmd_services_title' => "🔧 Ваші активні послуги:",
            'cmd_services_empty' => "У вас немає активних послуг.",
            'cmd_services_item' => "• :productname (:domain) — оплачено до :duedate",
            'cmd_support_prompt' => "✉️ Напишіть, будь ласка, суть вашого запиту одним або кількома повідомленнями.\nКоли закінчите – надішліть `/done`.",
            'cmd_support_message_added' => "✅ Повідомлення додано. Надішліть `/done` коли закінчите, або продовжуйте писати.",
            'cmd_support_empty' => "❌ Повідомлення порожнє. Будь ласка, надішліть текст запиту перед `/done`.",
            'cmd_support_not_started' => "❌ Ви не розпочали створення звернення. Використайте `/support` для початку.",
            'cmd_support_created' => "✅ Ваше звернення №:ticketid створено.\nНаша підтримка відповість вам найближчим часом.",
            'cmd_support_error' => "❌ Помилка при створенні звернення. Будь ласка, спробуйте пізніше або зверніться до підтримки іншим способом.",
            'cmd_unlinked' => "🔓 Цей Telegram успішно відв'язано від вашого акаунту.\nЩоб знову прив'язати – запросіть нове посилання у підтримки.",
            'cmd_not_linked' => "❌ Цей Telegram не прив'язаний до жодного акаунту.",
            'cmd_not_linked_generic' => "❌ Щоб використовувати цю команду, спочатку прив'яжіть Telegram до вашого акаунту.\n\nЗапитайте у підтримки посилання для прив'язки.",
            'error_generic' => "❌ Виникла помилка при обробці запиту. Будь ласка, спробуйте пізніше.",
            'error_invalid_data' => "❌ Помилка: некоректні дані.",
            'error_service_not_found' => "❌ Послуга не знайдена або не належить вам.",
            'error_client_not_found' => "❌ Помилка: клієнт не знайдений.",
            'error_invoice_exists' => "ℹ️ Для цієї послуги вже є неоплачений рахунок №:invoiceid.\nСума: :amount :currency",
            'error_invoice_creation' => "❌ Помилка при створенні рахунка. Будь ласка, спробуйте пізніше або зверніться до підтримки.",
            'invoice_created_notify' => "🧾 Створено новий рахунок №:invoiceid\nСума: :amount :currency\nТермін оплати: :duedate\n\nПереглянути рахунок: :link",
            'invoice_paid_notify' => "✅ Оплату за рахунком №:invoiceid отримано.\nДякуємо!\n\nПереглянути рахунок: :link",
            'invoice_renewal_created' => "🧾 Створено рахунок №:invoiceid для продовження послуги :productname (:domain).\n\nСума: :amount :currency\n\nПереглянути рахунок: :link",
            'lang_current' => "Поточна мова: :lang",
            'lang_choose' => "🌐 Оберіть мову / Choose language:",
            'lang_changed' => "✅ Мову змінено на :lang",
            'lang_ua' => "Українська",
            'lang_ru' => "Русский",
            'lang_en' => "English",
            'settings_header' => "⚙️ Налаштування:",
            'settings_language' => "Мова: :lang",
            'settings_notify_new_invoice_on' => "🧾 Нові рахунки: УВІМКНЕНО",
            'settings_notify_new_invoice_off' => "🧾 Нові рахунки: ВИМКНЕНО",
            'settings_notify_invoice_paid_on' => "✅ Оплата рахунку: УВІМКНЕНО",
            'settings_notify_invoice_paid_off' => "✅ Оплата рахунку: ВИМКНЕНО",
            'link_invalid' => "❌ Посилання недійсне або застаріле.\n\nЗапитайте нове посилання у підтримки.",
            'link_expired' => "❌ Посилання застаріле.\n\nЗапитайте нове посилання у підтримки.",
            'unknown_command' => "❓ Невідома команда.",
            'status_paid' => "Оплачено",
            'status_unpaid' => "Неоплачено",
            'pay_card_button' => "💳 Оплата на картку",
            'pay_usdt_button' => "💰 Оплата USDT TRC20",
            'pay_card_instructions' => "💳 Оплата на картку (Україна)\n\nСума: :amount :currency\nНомер картки: :card_number\nIBAN: :iban\nОтримувач: :recipient_name\n\nПризначення платежу:\n:payment_reference\n\nСкопіюйте суму та призначення в додатку банку. Не змінюйте текст призначення.",
            'pay_usdt_instructions' => "💰 Оплата USDT TRC20\n\nВідправте рівно: :amount USDT (TRC20)\nНа адресу: :tron_address\n\nУвага:\n• Не округляйте суму\n• Мережа: TRON (TRC20)\n\n📱 Відскануйте QR-код вище для швидкої оплати",
            'pay_usdt_qr_caption' => "💰 QR-код для оплати\n\nСума: :amount USDT\nАдреса: :tron_address\n\nВідскануйте QR-код у вашому криптогаманці",
            'error_payment_data' => "❌ Помилка при отриманні даних для оплати. Будь ласка, спробуйте пізніше.",
            'menu_show' => "Ось головне меню. Оберіть дію нижче.",
        ],
        'ru' => [
            'welcome_linked' => "👋 Привет! Я ваш BillingBot.\n\n✅ Ваш Telegram успешно привязан к личному кабинету.\nТеперь вы сможете получать счета, уведомления об оплате, смотреть баланс и управлять услугами здесь.\n\nВыберите действие ниже или используйте команды:\n/invoices – Ваши неоплаченные счета\n/lastinvoice – Последний счет\n/balance – Баланс и кредит\n/services – Активные услуги\n/support – Создать обращение в поддержку\n/lang – Выбор языка\n/settings – Настройки уведомлений\n/help – Помощь и список команд",
            'welcome_already_linked' => "👋 Привет! Вы уже привязаны к личному кабинету.\n\nВыберите действие ниже или используйте команды.",
            'need_link' => "Чтобы пользоваться ботом, сначала привяжите Telegram к вашему аккаунту. Попросите ссылку у поддержки.",
            'help' => "📋 Доступные команды:\n\n/invoices – Ваши неоплаченные счета\n/lastinvoice – Последний счет\n/balance – Баланс и кредит\n/services – Активные услуги (до какой даты оплачены) + кнопка продления\n/support – Создать обращение в поддержку\n/unlink – Отвязать Telegram\n/lang – Изменить язык\n/settings – Настройки\n/help – Помощь и список команд",
            'cmd_invoices_title' => "🧾 Ваши неоплаченные счета:",
            'cmd_invoices_empty' => "✅ У вас сейчас нет неоплаченных счетов.",
            'cmd_invoices_item' => "• №:invoiceid — :amount :currency, до :duedate",
            'cmd_lastinvoice_title' => "🧾 Последний счет №:invoiceid",
            'cmd_lastinvoice_empty' => "У вас еще нет счетов.",
            'cmd_lastinvoice_status' => "Статус: :status",
            'cmd_lastinvoice_amount' => "Сумма: :amount :currency",
            'cmd_lastinvoice_duedate' => "Срок оплаты: :duedate",
            'cmd_balance' => "💰 Баланс:\n\nКредит на аккаунте: :credit :currency\nСумма неоплаченных счетов: :unpaid :currency",
            'cmd_services_title' => "🔧 Ваши активные услуги:",
            'cmd_services_empty' => "У вас нет активных услуг.",
            'cmd_services_item' => "• :productname (:domain) — оплачено до :duedate",
            'cmd_support_prompt' => "✉️ Напишите, пожалуйста, суть вашего запроса одним или несколькими сообщениями.\nКогда закончите – отправьте `/done`.",
            'cmd_support_message_added' => "✅ Сообщение добавлено. Отправьте `/done` когда закончите, или продолжайте писать.",
            'cmd_support_empty' => "❌ Сообщение пустое. Пожалуйста, отправьте текст запроса перед `/done`.",
            'cmd_support_not_started' => "❌ Вы не начали создание обращения. Используйте `/support` для начала.",
            'cmd_support_created' => "✅ Ваше обращение №:ticketid создано.\nНаша поддержка ответит вам в ближайшее время.",
            'cmd_support_error' => "❌ Ошибка при создании обращения. Пожалуйста, попробуйте позже или обратитесь в поддержку другим способом.",
            'cmd_unlinked' => "🔓 Этот Telegram успешно отвязан от вашего аккаунта.\nЧтобы снова привязать – попросите новую ссылку у поддержки.",
            'cmd_not_linked' => "❌ Этот Telegram не привязан ни к одному аккаунту.",
            'cmd_not_linked_generic' => "❌ Чтобы использовать эту команду, сначала привяжите Telegram к вашему аккаунту.\n\nПопросите у поддержки ссылку для привязки.",
            'error_generic' => "❌ Произошла ошибка при обработке запроса. Пожалуйста, попробуйте позже.",
            'error_invalid_data' => "❌ Ошибка: некорректные данные.",
            'error_service_not_found' => "❌ Услуга не найдена или не принадлежит вам.",
            'error_client_not_found' => "❌ Ошибка: клиент не найден.",
            'error_invoice_exists' => "ℹ️ Для этой услуги уже есть неоплаченный счет №:invoiceid.\nСумма: :amount :currency",
            'error_invoice_creation' => "❌ Ошибка при создании счета. Пожалуйста, попробуйте позже или обратитесь в поддержку.",
            'invoice_created_notify' => "🧾 Создан новый счет №:invoiceid\nСумма: :amount :currency\nСрок оплаты: :duedate\n\nПосмотреть счет: :link",
            'invoice_paid_notify' => "✅ Оплата по счету №:invoiceid получена.\nСпасибо!\n\nПосмотреть счет: :link",
            'invoice_renewal_created' => "🧾 Создан счет №:invoiceid для продления услуги :productname (:domain).\n\nСумма: :amount :currency\n\nПосмотреть счет: :link",
            'lang_current' => "Текущий язык: :lang",
            'lang_choose' => "🌐 Выберите язык / Choose language:",
            'lang_changed' => "✅ Язык изменен на :lang",
            'lang_ua' => "Українська",
            'lang_ru' => "Русский",
            'lang_en' => "English",
            'settings_header' => "⚙️ Настройки:",
            'settings_language' => "Язык: :lang",
            'settings_notify_new_invoice_on' => "🧾 Новые счета: ВКЛЮЧЕНО",
            'settings_notify_new_invoice_off' => "🧾 Новые счета: ВЫКЛЮЧЕНО",
            'settings_notify_invoice_paid_on' => "✅ Оплата счета: ВКЛЮЧЕНО",
            'settings_notify_invoice_paid_off' => "✅ Оплата счета: ВЫКЛЮЧЕНО",
            'link_invalid' => "❌ Ссылка недействительна или устарела.\n\nПопросите новую ссылку у поддержки.",
            'link_expired' => "❌ Ссылка устарела.\n\nПопросите новую ссылку у поддержки.",
            'unknown_command' => "❓ Неизвестная команда.",
            'status_paid' => "Оплачено",
            'status_unpaid' => "Неоплачено",
            'pay_card_button' => "💳 Оплата на карту",
            'pay_usdt_button' => "💰 Оплата USDT TRC20",
            'pay_card_instructions' => "💳 Оплата на карту (Украина)\n\nСумма: :amount :currency\nНомер карты: :card_number\nIBAN: :iban\nПолучатель: :recipient_name\n\nНазначение платежа:\n:payment_reference\n\nСкопируйте сумму и назначение в приложении банка. Не изменяйте текст назначения.",
            'pay_usdt_instructions' => "💰 Оплата USDT TRC20\n\nОтправьте ровно: :amount USDT (TRC20)\nНа адрес: :tron_address\n\nВнимание:\n• Не округляйте сумму\n• Сеть: TRON (TRC20)\n\n📱 Отсканируйте QR-код выше для быстрой оплаты",
            'pay_usdt_qr_caption' => "💰 QR-код для оплаты\n\nСумма: :amount USDT\nАдрес: :tron_address\n\nОтсканируйте QR-код в вашем криптокошельке",
            'error_payment_data' => "❌ Ошибка при получении данных для оплаты. Пожалуйста, попробуйте позже.",
            'menu_show' => "Вот главное меню. Выберите действие ниже.",
        ],
        'en' => [
            'welcome_linked' => "👋 Hello! I'm your BillingBot.\n\n✅ Your Telegram has been linked to your account.\nYou can now receive invoices, payment notifications, view your balance and manage services here.\n\n\nChoose an action below or use commands:\n/invoices – Your unpaid invoices\n/lastinvoice – Last invoice\n/balance – Balance and credit\n/services – Active services\n/support – Create support ticket\n/lang – Language selection\n/settings – Notification settings\n/help – Help and command list",
            'welcome_already_linked' => "👋 Hello! You're already linked to your account.\n\nChoose an action below or use commands.",
            'need_link' => "To use the bot, first link Telegram to your account. Ask support for a link.",
            'help' => "📋 Available commands:\n\n/invoices – Your unpaid invoices\n/lastinvoice – Last invoice\n/balance – Balance and credit\n/services – Active services (paid until) + renewal button\n/support – Create support ticket\n/unlink – Unlink Telegram\n/lang – Change language\n/settings – Settings\n/help – Help and command list",
            'cmd_invoices_title' => "🧾 Your unpaid invoices:",
            'cmd_invoices_empty' => "✅ You have no unpaid invoices right now.",
            'cmd_invoices_item' => "• #:invoiceid — :amount :currency, due :duedate",
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
            'lang_current' => "Current language: :lang",
            'lang_choose' => "🌐 Choose language:",
            'lang_changed' => "✅ Language changed to :lang",
            'lang_ua' => "Ukrainian",
            'lang_ru' => "Russian",
            'lang_en' => "English",
            'settings_header' => "⚙️ Settings:",
            'settings_language' => "Language: :lang",
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
            'pay_card_instructions' => "💳 Pay by Card (Ukraine)\n\nAmount: :amount :currency\nCard number: :card_number\nIBAN: :iban\nRecipient: :recipient_name\n\nPayment reference:\n:payment_reference\n\nCopy the amount and reference in your banking app. Do not change the reference text.",
            'pay_usdt_instructions' => "💰 Pay with USDT TRC20\n\nSend exactly: :amount USDT (TRC20)\nTo address: :tron_address\n\nImportant:\n• Do not round the amount\n• Network: TRON (TRC20)\n\n\n📱 Scan the QR code above for quick payment",
            'pay_usdt_qr_caption' => "💰 QR code for payment\n\nAmount: :amount USDT\nAddress: :tron_address\n\nScan the QR code in your crypto wallet",
            'error_payment_data' => "❌ Error getting payment data. Please try again later.",
            'menu_show' => "Here's the main menu. Choose an action below.",
        ],
    ];
    
    // Fallback to English if language not found
    if (!isset($messages[$lang])) {
        $lang = 'en';
    }
    
    // Get the message
    $message = isset($messages[$lang][$key]) ? $messages[$lang][$key] : (isset($messages['en'][$key]) ? $messages['en'][$key] : $key);
    
    // Replace parameters
    foreach ($params as $key => $value) {
        $message = str_replace($key, $value, $message);
    }
    
    return $message;
}
