<?php

return [
    // ===== WHATSAPP DEVICE =====
    'whatsapp' => [
        // Section Titles
        'device_info'                   => 'Apparaat Informatie',
        'ai_config'                     => 'AI Agent Configuratie',
        'schedule_limits'               => 'Planning & Beperkingen',
        
        // Device Information
        'device_name'                   => 'Apparaat Naam',
        'device_name_placeholder'       => 'Voorbeeld: Admin Apparaat',
        'device_name_hint'              => 'Naam om dit WhatsApp apparaat te identificeren',
        'wa_number'                     => 'WhatsApp Nr.',
        'wa_number_placeholder'         => '+31 xxx xxx xxx',
        'wa_number_hint'                => 'WhatsApp nummer dat verbonden wordt met het systeem (formaat: +31)',
        'team_member'                   => 'Teamlid',
        'team_member_hint'              => 'Kies een teamlid dat dit apparaat zal beheren',
        'team_member_placeholder'       => 'Kies teamlid...',
        
        // Device Settings
        'device_notification'           => 'Apparaat Notificaties',
        'device_notification_hint'      => 'Uitschakelen als u geen notificaties op uw apparaat wilt ontvangen',
        'save_chat_history'             => 'Chat Geschiedenis Opslaan',
        'save_chat_history_hint'        => 'Geschiedenis wordt automatisch opgeslagen zonder dagelijkse reset',
        'auto_read_before_reply'        => 'Chat eerst lezen voordat ChatBot antwoordt',
        'auto_read_before_reply_hint'   => 'Activeer als u de chat eerst wilt lezen voordat AI automatisch antwoordt. Telefoon notificaties worden uitgeschakeld.',
        'webhook_url'                   => 'WebHook URL (Optioneel)',
        'webhook_url_placeholder'       => 'https://voorbeeld.com/webhook',
        'webhook_url_hint'              => 'Webhook URL om meldingen van inkomende berichten te ontvangen (optioneel)',
        
        // AI Configuration
        'chatbot_method'                => 'Chatbot Methode',
        'chatbot_method_hint'           => 'Kies de automatische antwoord methode die gebruikt wordt',
        'method_all'                    => 'Alle (Handmatig + AI)',
        'method_chatbot'                => 'Chatbot (Handmatig)',
        'method_ai'                     => 'AI (Fine Tunnel)',
        'ai_training'                   => 'AI Training',
        'ai_training_hint'              => 'Kies AI training data voor dit apparaat',
        'ai_training_full_hint'         => 'Kies AI training dataset om de nauwkeurigheid van antwoorden te verbeteren',
        'ai_training_placeholder'       => 'Kies AI Training...',
        'select_ai_training'            => 'Selecteer AI Training',
        'choose_ai_training'            => 'Kies AI Training',
        'auto_reply_option'             => 'Chatbot Actief in',
        'auto_reply_option_hint'        => 'Bepaal waar de chatbot actief berichten zal beantwoorden',
        'reply_all'                     => 'Alle (Persoonlijk & Groep)',
        'reply_personal'                => 'Persoonlijk',
        'reply_group'                   => 'Groep',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'Chatbot Inactief op Bepaalde Dagen',
        'inactive_certain_day_hint'     => 'Activeer als u wilt dat chatbot inactief is op bepaalde dagen',
        'select_days'                   => 'Selecteer Dagen',
        'select_days_placeholder'       => 'Kies dagen...',
        'day_monday'                    => 'Maandag',
        'day_tuesday'                   => 'Dinsdag',
        'day_wednesday'                 => 'Woensdag',
        'day_thursday'                  => 'Donderdag',
        'day_friday'                    => 'Vrijdag',
        'day_saturday'                  => 'Zaterdag',
        'day_sunday'                    => 'Zondag',
        
        'inactive_certain_time'         => 'Chatbot Inactief op Bepaalde Tijden',
        'inactive_certain_time_hint'    => 'Activeer als u wilt dat chatbot inactief is op bepaalde tijden',
        'start_time'                    => 'Start Tijd Inactief',
        'start_time_hint'               => 'Tijd wanneer chatbot inactief wordt',
        'end_time'                      => 'Eind Tijd Inactief',
        'end_time_hint'                 => 'Tijd wanneer chatbot weer actief wordt',
        
        'daily_broadcast_limit'         => 'Dagelijkse Broadcast Limiet',
        'daily_broadcast_limit_hint'    => 'Activeer als chatbot een dagelijkse limiet voor verzonden berichten heeft',
        'enter_daily_limit'             => 'Voer Dagelijkse Limiet In',
        'daily_limit_placeholder'       => 'Voorbeeld: 100',
        'daily_limit_suffix'            => 'berichten/dag',
        'daily_limit_hint'              => 'Maximum aantal berichten dat per dag verzonden kan worden',
        
        // Actions
        'save_device'                   => 'Apparaat Opslaan',
        'update_device'                 => 'Apparaat Bijwerken',
        'cancel'                        => 'Annuleren',
        'required_fields'               => 'Velden met * zijn verplicht',
        
        // Messages
        'device_created'                => 'WhatsApp apparaat succesvol toegevoegd',
        'device_updated'                => 'WhatsApp apparaat succesvol bijgewerkt',
        'device_deleted'                => 'WhatsApp apparaat succesvol verwijderd',
        
        // List/Index Page
        'add_connection'                => 'WhatsApp Verbinding Toevoegen',
        'total_device'                  => 'Totaal Apparaten',
        'not_connected'                 => 'Niet Verbonden',
        'device_connected'              => 'Apparaat Verbonden',
        'connection_list'               => 'WhatsApp Verbindingen Lijst',
        'broadcast_sent_today'          => 'Broadcast Verzonden Vandaag',
        'daily_broadcast_limit_label'   => 'Dagelijkse Broadcast Limiet',
        'device_name_label'             => 'Apparaat Naam',
        'phone_number'                  => 'Telefoonnummer',
        
        // Actions
        'scan_qr'                       => 'QR Scannen',
        'copy_id'                       => 'ID Kopiëren',
        'settings'                      => 'Instellingen',
        'edit_device'                   => 'Apparaat Bewerken',
        'delete_device'                 => 'Apparaat Verwijderen',
        'copied_device_id'              => 'Apparaat ID succesvol gekopieerd',
        'search_device'                 => 'Zoek apparaat...',
        
        // Status
        'status_active'                 => 'Actief',
        'status_inactive'               => 'Inactief',
    ],
    // ===== TELEGRAM =====
    'telegram' => [
        // Section Titles
        'device_info'                   => 'Telegram Apparaat Informatie',
        'ai_config'                     => 'AI Agent Configuratie',
        'schedule_limits'               => 'Planning & Beperkingen',
        'integrated_telegram_list'      => 'Geïntegreerde Telegram Lijst',
        'add_telegram'                  => 'Telegram Toevoegen',
        'edit_telegram'                 => 'Telegram Bewerken',
        
        // Device Information
        'device_name'                   => 'Apparaat Naam',
        'device_name_placeholder'       => 'Voorbeeld: Klantenservice Bot',
        'device_name_hint'              => 'Naam om deze Telegram bot te identificeren',
        'bot_token'                     => 'Bot Token',
        'bot_token_placeholder'         => 'Voer bot token in van @BotFather',
        'bot_token_hint'                => 'Bot token verkregen van @BotFather in Telegram',
        'team_member'                   => 'Teamlid',
        'team_member_hint'              => 'Kies een teamlid dat deze bot zal beheren',
        'team_member_placeholder'       => 'Kies teamlid...',
        
        // AI Configuration
        'auto_reply_method'             => 'AutoReply Methode',
        'auto_reply_method_hint'        => 'Kies de automatische antwoord methode die gebruikt wordt',
        'method_all'                    => 'Alle (Handmatig + AI)',
        'method_chatbot'                => 'ChatBot (Handmatig)',
        'method_ai'                     => 'AI (Fine Tunnel)',
        'ai_training'                   => 'AI Training',
        'ai_training_hint'              => 'Kies AI training data voor deze bot',
        'ai_training_placeholder'       => 'Kies AI Training...',
        'choose_ai_training'            => 'Kies AI Training',
        
        // Status & Options
        'status'                        => 'Status',
        'status_hint'                   => 'Actief/inactief status van Telegram bot',
        'status_active'                 => 'Actief',
        'status_inactive'               => 'Inactief',
        'auto_reply_option'             => 'Bot Actief in',
        'auto_reply_option_hint'        => 'Bepaal waar de bot actief berichten zal beantwoorden',
        'reply_all'                     => 'Alle (Persoonlijk & Groep)',
        'reply_personal'                => 'Persoonlijk',
        'reply_group'                   => 'Groep',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'Bot Inactief op Bepaalde Dagen',
        'inactive_certain_day_hint'     => 'Activeer als u wilt dat bot inactief is op bepaalde dagen',
        'inactive_certain_day_no'       => 'Nee, Actief Elke Dag',
        'inactive_certain_day_yes'      => 'Ja, Inactief op Bepaalde Dagen',
        'select_days'                   => 'Selecteer Dagen',
        'select_days_placeholder'       => 'Kies dagen...',
        'day_monday'                    => 'Maandag',
        'day_tuesday'                   => 'Dinsdag',
        'day_wednesday'                 => 'Woensdag',
        'day_thursday'                  => 'Donderdag',
        'day_friday'                    => 'Vrijdag',
        'day_saturday'                  => 'Zaterdag',
        'day_sunday'                    => 'Zondag',
        
        'inactive_certain_time'         => 'Bot Inactief op Bepaalde Tijden',
        'inactive_certain_time_hint'    => 'Activeer als u wilt dat bot inactief is op bepaalde tijden',
        'inactive_certain_time_no'      => 'Nee, Actief 24 Uur',
        'inactive_certain_time_yes'     => 'Ja, Inactief op Bepaalde Tijden',
        'start_time'                    => 'Start Tijd Inactief',
        'start_time_hint'               => 'Tijd wanneer bot inactief wordt',
        'end_time'                      => 'Eind Tijd Inactief',
        'end_time_hint'                 => 'Tijd wanneer bot weer actief wordt',
        
        'daily_limit'                   => 'Dagelijkse Limiet',
        'daily_limit_hint'              => 'Activeer als bot een dagelijkse limiet voor verzonden berichten heeft',
        'daily_limit_no'                => 'Geen Limiet',
        'daily_limit_yes'               => 'Dagelijkse Limiet Aanwezig',
        'enter_daily_limit'             => 'Voer Dagelijkse Limiet In',
        'daily_limit_placeholder'       => 'Voorbeeld: 1000',
        'daily_limit_suffix'            => 'berichten/dag',
        'daily_limit_hint_input'        => 'Maximum aantal berichten dat per dag verzonden kan worden',
        
        // Actions
        'back_to_list'                  => 'Terug naar Telegram Pagina',
        'add_device'                    => 'Apparaat Toevoegen',
        'save_device'                   => 'Apparaat Opslaan',
        'update_device'                 => 'Apparaat Bijwerken',
        'cancel'                        => 'Annuleren',
        'required_fields'               => 'Velden met * zijn verplicht',
        
        // Messages
        'device_created'                => 'Telegram bot succesvol toegevoegd',
        'device_updated'                => 'Telegram bot succesvol bijgewerkt',
        'device_deleted'                => 'Telegram bot succesvol verwijderd',
        
        // List/Index Page
        'add_connection'                => 'Telegram Verbinding Toevoegen',
        'total_bot'                     => 'Totaal Telegram',
        'not_connected'                 => 'Niet Verbonden',
        'bot_connected'                 => 'Telegram Verbonden',
        'connection_list'               => 'Telegram Verbindingen Lijst',
        'bot_name'                      => 'Bot Naam',
        'broadcast_sent_today'          => 'Broadcast Verzonden Vandaag',
        'daily_broadcast_limit_label'   => 'Dagelijkse Broadcast Limiet',
        
        // Actions List
        'copy_id'                       => 'ID Kopiëren',
        'edit_bot'                      => 'Bot Bewerken',
        'delete_bot'                    => 'Bot Verwijderen',
        'copied_bot_id'                 => 'Bot ID succesvol gekopieerd',
        'search_bot'                    => 'Zoek telegram bot...',
    ],
    // ===== FACEBOOK =====
    'facebook' => [
        'add_account'                   => 'Facebook-account toevoegen',
        'account_list'                  => 'Facebook-accountlijst',
        'account_connected'             => 'Facebook-account succesvol verbonden.',
        'login_failed'                  => 'Facebook-login mislukt: ',
    ],
    // ===== INSTAGRAM =====
    'instagram' => [
        'add_account'                   => 'Account toevoegen',
        'account_list'                  => 'Instagram-accountlijst',
        'account_connected'             => 'Instagram-account succesvol verbonden.',
        'login_failed'                  => 'Instagram-login mislukt: ',
    ],
];