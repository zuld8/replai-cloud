<?php

return [
    // ===== WHATSAPP DEVICE =====
    'whatsapp' => [
        // Section Titles
        'device_info'                   => 'Geräteinformationen',
        'ai_config'                     => 'KI-Agent Konfiguration',
        'schedule_limits'               => 'Zeitplan & Begrenzungen',
        
        // Device Information
        'device_name'                   => 'Gerätename',
        'device_name_placeholder'       => 'Beispiel: Admin-Gerät',
        'device_name_hint'              => 'Name zur Identifizierung dieses WhatsApp-Geräts',
        'wa_number'                     => 'WhatsApp-Nr.',
        'wa_number_placeholder'         => '+49 xxx xxx xxx',
        'wa_number_hint'                => 'WhatsApp-Nummer, die mit dem System verbunden wird (Format: +49)',
        'team_member'                   => 'Teammitglied',
        'team_member_hint'              => 'Wählen Sie das Teammitglied aus, das dieses Gerät verwalten wird',
        'team_member_placeholder'       => 'Teammitglied auswählen...',
        
        // Device Settings
        'device_notification'           => 'Gerätebenachrichtigung',
        'device_notification_hint'      => 'Deaktivieren, wenn Sie keine Benachrichtigungen auf Ihrem Gerät erhalten möchten',
        'save_chat_history'             => 'Chat-Verlauf speichern',
        'save_chat_history_hint'        => 'Verlauf wird automatisch ohne tägliches Zurücksetzen gespeichert',
        'auto_read_before_reply'        => 'Chat lesen bevor ChatBot antwortet',
        'auto_read_before_reply_hint'   => 'Aktivieren, wenn Sie den Chat zuerst lesen möchten, bevor die KI automatisch antwortet. Benachrichtigungen an das Handy werden deaktiviert.',
        'webhook_url'                   => 'WebHook URL (Optional)',
        'webhook_url_placeholder'       => 'https://example.com/webhook',
        'webhook_url_hint'              => 'Webhook-URL zum Empfang von eingehenden Nachrichten-Benachrichtigungen (optional)',
        
        // AI Configuration
        'chatbot_method'                => 'Chatbot-Methode',
        'chatbot_method_hint'           => 'Wählen Sie die verwendete automatische Antwortmethode',
        'method_all'                    => 'Alle (Manuell + KI)',
        'method_chatbot'                => 'Chatbot (Manuell)',
        'method_ai'                     => 'KI (Fine Tunnel)',
        'ai_training'                   => 'KI-Training',
        'ai_training_hint'              => 'Wählen Sie KI-Trainingsdaten für dieses Gerät',
        'ai_training_full_hint'         => 'Wählen Sie KI-Training-Dataset zur Verbesserung der Antwortgenauigkeit',
        'ai_training_placeholder'       => 'KI-Training auswählen...',
        'select_ai_training'            => 'KI-Training auswählen',
        'choose_ai_training'            => 'KI-Training auswählen',
        'auto_reply_option'             => 'Chatbot aktiv in',
        'auto_reply_option_hint'        => 'Bestimmen Sie, wo der Chatbot aktiv auf Nachrichten antworten wird',
        'reply_all'                     => 'Alle (Persönlich & Gruppe)',
        'reply_personal'                => 'Persönlich',
        'reply_group'                   => 'Gruppe',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'Chatbot inaktiv an bestimmten Tagen',
        'inactive_certain_day_hint'     => 'Aktivieren, wenn der Chatbot an bestimmten Tagen inaktiv sein soll',
        'select_days'                   => 'Tage auswählen',
        'select_days_placeholder'       => 'Tage auswählen...',
        'day_monday'                    => 'Montag',
        'day_tuesday'                   => 'Dienstag',
        'day_wednesday'                 => 'Mittwoch',
        'day_thursday'                  => 'Donnerstag',
        'day_friday'                    => 'Freitag',
        'day_saturday'                  => 'Samstag',
        'day_sunday'                    => 'Sonntag',
        
        'inactive_certain_time'         => 'Chatbot inaktiv zu bestimmten Zeiten',
        'inactive_certain_time_hint'    => 'Aktivieren, wenn der Chatbot zu bestimmten Zeiten inaktiv sein soll',
        'start_time'                    => 'Startzeit Inaktivität',
        'start_time_hint'               => 'Zeitpunkt, ab dem der Chatbot inaktiv ist',
        'end_time'                      => 'Endzeit Inaktivität',
        'end_time_hint'                 => 'Zeitpunkt, bis zu dem der Chatbot inaktiv ist',
        
        'daily_broadcast_limit'         => 'Tägliche Broadcast-Begrenzung',
        'daily_broadcast_limit_hint'    => 'Aktivieren, wenn der Chatbot eine tägliche Nachrichtenversendung-Begrenzung hat',
        'enter_daily_limit'             => 'Tägliches Limit eingeben',
        'daily_limit_placeholder'       => 'Beispiel: 100',
        'daily_limit_suffix'            => 'Nachrichten/Tag',
        'daily_limit_hint'              => 'Maximale Anzahl von Nachrichten, die pro Tag gesendet werden können',
        
        // Actions
        'save_device'                   => 'Gerät speichern',
        'update_device'                 => 'Gerät aktualisieren',
        'cancel'                        => 'Abbrechen',
        'required_fields'               => 'Felder mit * sind Pflichtfelder',
        
        // Messages
        'device_created'                => 'WhatsApp-Gerät erfolgreich hinzugefügt',
        'device_updated'                => 'WhatsApp-Gerät erfolgreich aktualisiert',
        'device_deleted'                => 'WhatsApp-Gerät erfolgreich gelöscht',
        
        // List/Index Page
        'add_connection'                => 'WhatsApp-Verbindung hinzufügen',
        'total_device'                  => 'Geräte gesamt',
        'not_connected'                 => 'Nicht verbunden',
        'device_connected'              => 'Gerät verbunden',
        'connection_list'               => 'WhatsApp-Verbindungsliste',
        'broadcast_sent_today'          => 'Heute gesendete Broadcasts',
        'daily_broadcast_limit_label'   => 'Tägliches Broadcast-Limit',
        'device_name_label'             => 'Gerätename',
        'phone_number'                  => 'Telefonnummer',
        
        // Actions
        'scan_qr'                       => 'QR scannen',
        'copy_id'                       => 'ID kopieren',
        'settings'                      => 'Einstellungen',
        'edit_device'                   => 'Gerät bearbeiten',
        'delete_device'                 => 'Gerät löschen',
        'copied_device_id'              => 'Geräte-ID erfolgreich kopiert',
        'search_device'                 => 'Gerät suchen...',
        
        // Status
        'status_active'                 => 'Aktiv',
        'status_inactive'               => 'Inaktiv',
    ],
    // ===== TELEGRAM =====
    'telegram' => [
        // Section Titles
        'device_info'                   => 'Telegram-Geräteinformationen',
        'ai_config'                     => 'KI-Agent Konfiguration',
        'schedule_limits'               => 'Zeitplan & Begrenzungen',
        'integrated_telegram_list'      => 'Integrierte Telegram-Liste',
        'add_telegram'                  => 'Telegram hinzufügen',
        'edit_telegram'                 => 'Telegram bearbeiten',
        
        // Device Information
        'device_name'                   => 'Gerätename',
        'device_name_placeholder'       => 'Beispiel: Kundenservice-Bot',
        'device_name_hint'              => 'Name zur Identifizierung dieses Telegram-Bots',
        'bot_token'                     => 'Bot-Token',
        'bot_token_placeholder'         => 'Bot-Token von @BotFather eingeben',
        'bot_token_hint'                => 'Bot-Token erhalten von @BotFather in Telegram',
        'team_member'                   => 'Teammitglied',
        'team_member_hint'              => 'Wählen Sie das Teammitglied aus, das diesen Bot verwalten wird',
        'team_member_placeholder'       => 'Teammitglied auswählen...',
        
        // AI Configuration
        'auto_reply_method'             => 'AutoReply-Methode',
        'auto_reply_method_hint'        => 'Wählen Sie die verwendete automatische Antwortmethode',
        'method_all'                    => 'Alle (Manuell + KI)',
        'method_chatbot'                => 'ChatBot (Manuell)',
        'method_ai'                     => 'KI (Fine Tunnel)',
        'ai_training'                   => 'KI-Training',
        'ai_training_hint'              => 'Wählen Sie KI-Trainingsdaten für diesen Bot',
        'ai_training_placeholder'       => 'KI-Training auswählen...',
        'choose_ai_training'            => 'KI-Training auswählen',
        
        // Status & Options
        'status'                        => 'Status',
        'status_hint'                   => 'Aktiv/Inaktiv-Status des Telegram-Bots',
        'status_active'                 => 'Aktiv',
        'status_inactive'               => 'Inaktiv',
        'auto_reply_option'             => 'Bot aktiv in',
        'auto_reply_option_hint'        => 'Bestimmen Sie, wo der Bot aktiv auf Nachrichten antworten wird',
        'reply_all'                     => 'Alle (Persönlich & Gruppe)',
        'reply_personal'                => 'Persönlich',
        'reply_group'                   => 'Gruppe',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'Bot inaktiv an bestimmten Tagen',
        'inactive_certain_day_hint'     => 'Aktivieren, wenn der Bot an bestimmten Tagen inaktiv sein soll',
        'inactive_certain_day_no'       => 'Nein, jeden Tag aktiv',
        'inactive_certain_day_yes'      => 'Ja, inaktiv an bestimmten Tagen',
        'select_days'                   => 'Tage auswählen',
        'select_days_placeholder'       => 'Tage auswählen...',
        'day_monday'                    => 'Montag',
        'day_tuesday'                   => 'Dienstag',
        'day_wednesday'                 => 'Mittwoch',
        'day_thursday'                  => 'Donnerstag',
        'day_friday'                    => 'Freitag',
        'day_saturday'                  => 'Samstag',
        'day_sunday'                    => 'Sonntag',
        
        'inactive_certain_time'         => 'Bot inaktiv zu bestimmten Zeiten',
        'inactive_certain_time_hint'    => 'Aktivieren, wenn der Bot zu bestimmten Zeiten inaktiv sein soll',
        'inactive_certain_time_no'      => 'Nein, 24 Stunden aktiv',
        'inactive_certain_time_yes'     => 'Ja, inaktiv zu bestimmten Zeiten',
        'start_time'                    => 'Startzeit Inaktivität',
        'start_time_hint'               => 'Zeitpunkt, ab dem der Bot inaktiv ist',
        'end_time'                      => 'Endzeit Inaktivität',
        'end_time_hint'                 => 'Zeitpunkt, bis zu dem der Bot inaktiv ist',
        
        'daily_limit'                   => 'Tägliche Begrenzung',
        'daily_limit_hint'              => 'Aktivieren, wenn der Bot eine tägliche Nachrichtenversendung-Begrenzung hat',
        'daily_limit_no'                => 'Keine Begrenzung',
        'daily_limit_yes'               => 'Tägliche Begrenzung vorhanden',
        'enter_daily_limit'             => 'Tägliches Limit eingeben',
        'daily_limit_placeholder'       => 'Beispiel: 1000',
        'daily_limit_suffix'            => 'Nachrichten/Tag',
        'daily_limit_hint_input'        => 'Maximale Anzahl von Nachrichten, die pro Tag gesendet werden können',
        
        // Actions
        'back_to_list'                  => 'Zurück zur Telegram-Seite',
        'add_device'                    => 'Gerät hinzufügen',
        'save_device'                   => 'Gerät speichern',
        'update_device'                 => 'Gerät aktualisieren',
        'cancel'                        => 'Abbrechen',
        'required_fields'               => 'Felder mit * sind Pflichtfelder',
        
        // Messages
        'device_created'                => 'Telegram-Bot erfolgreich hinzugefügt',
        'device_updated'                => 'Telegram-Bot erfolgreich aktualisiert',
        'device_deleted'                => 'Telegram-Bot erfolgreich gelöscht',
        
        // List/Index Page
        'add_connection'                => 'Telegram-Verbindung hinzufügen',
        'total_bot'                     => 'Telegram gesamt',
        'not_connected'                 => 'Nicht verbunden',
        'bot_connected'                 => 'Telegram verbunden',
        'connection_list'               => 'Telegram-Verbindungsliste',
        'bot_name'                      => 'Bot-Name',
        'broadcast_sent_today'          => 'Heute gesendete Broadcasts',
        'daily_broadcast_limit_label'   => 'Tägliches Broadcast-Limit',
        
        // Actions List
        'copy_id'                       => 'ID kopieren',
        'edit_bot'                      => 'Bot bearbeiten',
        'delete_bot'                    => 'Bot löschen',
        'copied_bot_id'                 => 'Bot-ID erfolgreich kopiert',
        'search_bot'                    => 'Telegram-Bot suchen...',
    ],
    'facebook' => [
        'add_account'                   => 'Facebook-Konto hinzufügen',
        'account_list'                  => 'Facebook-Kontoliste',
        'account_connected'             => 'Facebook-Konto erfolgreich verbunden.',
        'login_failed'                  => 'Facebook-Anmeldung fehlgeschlagen: ',
    ],
    'instagram' => [
        'add_account'                   => 'Instagram-Konto hinzufügen',
        'account_list'                  => 'Instagram-Kontoliste',
        'account_connected'             => 'Instagram-Konto erfolgreich verbunden.',
        'login_failed'                  => 'Instagram-Anmeldung fehlgeschlagen: ',
    ],
];