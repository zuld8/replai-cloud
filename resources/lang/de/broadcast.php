<?php

return [
    'wa' => [
        // Index/List Page
        'create_schedule_button' => 'Broadcast-Zeitplan erstellen',
        'wa_group' => 'WhatsApp-Gruppe',
        'page_title' => 'WhatsApp-Broadcast-Liste',
        
        // Card Headers
        'broadcast_info' => 'Broadcast-Informationen',
        'target_template' => 'Ziel & Vorlage',
        
        // Form Labels
        'name' => 'Broadcast-Titel',
        'schedule' => 'Sendezeitplan',
        'template' => 'Nachrichtenvorlage',
        'devices' => 'Geräte',
        'device_option' => 'Gerätenutzungsoption',
        'delay' => 'Verzögerung zwischen Sendungen',
        'stop_sending' => 'Senden stoppen nach',
        'rest_sending' => 'Ruhepause',
        'category' => 'Geschäftskategorie',
        'whatsapp_group' => 'WhatsApp-Gruppe',
        'location_target' => 'Zielort',
        
        // Placeholders
        'name_placeholder' => 'Beispiel: Jahresend-Promotion 2024',
        'choose_group' => 'WhatsApp-Gruppe auswählen',
        'choose_device' => 'WhatsApp-Gerät auswählen',
        
        // Device Options
        'device_sequence' => 'Einzelgerät (Sequenziell)',
        'device_spin' => 'KI-Auswahl (Spin)',
        'device_random' => 'Zufällig',
        
        // Units
        'seconds' => 'Sekunden',
        'numbers' => 'Nummern',
        
        // Helper Texts
        'name_help' => 'Name der Broadcast-Kampagne zur Identifikation',
        'schedule_help' => 'Zeitplan für die Broadcast-Nachricht',
        'template_help' => 'Vorlage der zu sendenden Nachricht',
        'devices_help' => 'WhatsApp-Gerät für Broadcast auswählen',
        'device_option_help' => 'Geräteauswahlmethode für Versendung',
        'delay_help' => 'Empfehlung: 30-300 Sekunden. Je kleiner, desto höher das Blockierungsrisiko',
        'stop_sending_help' => 'Senden stoppen nach wieviel Nummern',
        'rest_sending_help' => 'Pause vor erneutem Nachrichtenversand',
        'category_help' => 'Ziel nach Geschäftskategorie filtern',
        'whatsapp_group_help' => 'Zielkontakte aus bestimmter WhatsApp-Gruppe',
        'province_help' => 'Ziel nach Bundesland filtern',
        'city_help' => 'Ziel nach Stadt/Landkreis filtern',
        'district_help' => 'Ziel nach Bezirk filtern',
        
        // Badges & Labels
        'optional' => 'Optional',
        'required_field' => 'Pflichtfeld',
        
        // Alert & Tips
        'safe_sending_tips' => 'Tipps für sicheres Senden:',
        'tip_delay' => 'Mindestens 30 Sekunden Verzögerung verwenden, um Blockierung zu vermeiden',
        'tip_batch' => 'Versendung auf maximal 50-100 Nachrichten pro Stapel begrenzen',
        'tip_rest' => 'Ausreichende Ruhepausen zwischen Stapeln einhalten',
        'tip_multiple' => 'Mehrere Geräte für Lastverteilung verwenden',
    ],
    
    'email' => [
        // Form Fields
        'category' => 'Geschäftskategorie',
        'template' => 'E-Mail-Vorlage',
        'name' => 'Broadcast-Titel',
        'schedule' => 'Sendezeitplan',
        
        // Placeholders
        'name_placeholder' => 'Beispiel: Monatlicher Newsletter Oktober 2024',
        
        // Helper Texts
        'category_help' => 'Zielempfänger nach Geschäftskategorie filtern',
        'template_help' => 'E-Mail-Vorlage, die an Empfänger gesendet wird',
        'name_help' => 'Name der Broadcast-Kampagne zur Identifikation',
        'schedule_help' => 'Zeitplan für E-Mail-Broadcast',
        
        // Badges & Labels
        'optional' => 'Optional',
        'required_field' => 'Pflichtfeld',
        
        // Alert & Tips
        'email_sending_tips' => 'E-Mail-Versand-Tipps:',
        'tip_test_template' => 'E-Mail-Vorlage vor Broadcast testen',
        'tip_optimal_time' => 'Optimale Sendezeit für maximales Engagement wählen',
        'tip_use_category' => 'Kategorien für spezifischeres Targeting verwenden',
        'tip_check_spam' => 'Inhalt prüfen, um Spam-Ordner zu vermeiden',
    ],
    
    'upselling' => [
        // List/Index Page
        'page_title' => 'Upselling-Kampagnen-Liste',
        'create_campaign_button' => 'Neue Kampagne erstellen',
        'refresh_button' => 'Aktualisieren',
        
        // Table Headers
        'campaign_info' => 'Kampagnen-Info',
        'schedule_frequency' => 'Zeitplan & Häufigkeit',
        'target_category' => 'Ziel & Kategorie',
        'method_template' => 'Methode & Vorlage',
        'status' => 'Status',
        'action' => 'Aktion',
        
        // Filter Options
        'all_status' => 'Alle Status',
        'status_active' => 'Aktiv',
        'status_inactive' => 'Inaktiv',
        'status_scheduled' => 'Geplant',
        'status_completed' => 'Abgeschlossen',
        'all_frequency' => 'Alle Häufigkeiten',
        'frequency_once' => 'Einmalig',
        'frequency_daily' => 'Täglich',
        'frequency_monthly' => 'Monatlich',
        'frequency_yearly' => 'Jährlich',
        
        // DataTable Language
        'search_placeholder' => 'Kampagne suchen...',
        'length_menu' => '_MENU_ Kampagnen pro Seite anzeigen',
        'info' => 'Zeige _START_ bis _END_ von _TOTAL_ Kampagnen',
        'info_empty' => 'Keine Kampagnen',
        'info_filtered' => '(gefiltert von _MAX_ Kampagnen insgesamt)',
        'paginate_first' => 'Erste',
        'paginate_last' => 'Letzte',
        'paginate_next' => 'Weiter',
        'paginate_previous' => 'Zurück',
        
        // DataTable Content
        'devices_count' => 'Geräte',
        'delay_label' => 'Verzögerung',
        'once_send' => 'Einmalige Sendung',
        'date_prefix' => 'Datum',
        'every_month' => 'jeden Monat',
        'every_year' => 'jedes Jahr',
        'ongoing' => 'Laufend',
        'all_categories' => 'Alle Kategorien',
        'labels_count' => 'Labels',
        'template_label' => 'Vorlage',
        'ai_generated' => 'KI-generiert',
        
        // Day Short Labels
        'day_monday_short' => 'Mo',
        'day_tuesday_short' => 'Di',
        'day_wednesday_short' => 'Mi',
        'day_thursday_short' => 'Do',
        'day_friday_short' => 'Fr',
        'day_saturday_short' => 'Sa',
        'day_sunday_short' => 'So',
        
        // Month Short Labels
        'month_jan' => 'Jan',
        'month_feb' => 'Feb',
        'month_mar' => 'Mär',
        'month_apr' => 'Apr',
        'month_may' => 'Mai',
        'month_jun' => 'Jun',
        'month_jul' => 'Jul',
        'month_aug' => 'Aug',
        'month_sep' => 'Sep',
        'month_oct' => 'Okt',
        'month_nov' => 'Nov',
        'month_dec' => 'Dez',
        
        // Action Button Tooltips
        'btn_view_detail' => 'Details anzeigen',
        'btn_edit_campaign' => 'Kampagne bearbeiten',
        'btn_duplicate_campaign' => 'Kampagne duplizieren',
        'btn_delete_campaign' => 'Kampagne löschen',
        
        // Confirmation Messages
        'confirm_activate' => 'Sind Sie sicher, dass Sie diese Kampagne aktivieren möchten?',
        'confirm_deactivate' => 'Sind Sie sicher, dass Sie diese Kampagne deaktivieren möchten?',
        'confirm_delete' => 'Sind Sie sicher, dass Sie diese Kampagne löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden.',
        
        // Success Messages
        'success_title' => 'Erfolgreich',
        'success_delete' => 'Kampagne erfolgreich gelöscht',
        
        // Error Messages
        'error_title' => 'Fehler',
        'error_status_change' => 'Fehler beim Ändern des Kampagnenstatus',
        'error_delete' => 'Fehler beim Löschen der Kampagne',
        
        // Back Button
        'back_to_campaign' => 'Zurück zu Upselling-Kampagnen',
        
        // Card Headers (Form)
        'basic_info' => 'Grundlegende Kampagnen-Informationen',
        'schedule_config' => 'Versandplanung & Nachrichtenkonfiguration',
        
        // Form Fields
        'campaign_title' => 'Upselling-Kampagnen-Titel',
        'delay' => 'Verzögerung zwischen Sendungen',
        'devices' => 'Geräte',
        'device_option' => 'Gerätenutzungsoption',
        'contact_category' => 'Kontaktkategorie',
        'contact_labels' => 'Kontakt-Labels',
        'schedule_frequency' => 'Versandfrequenz',
        'select_days' => 'Tage auswählen',
        'date_in_month' => 'Datum im Monat',
        'specific_date' => 'Spezifisches Datum',
        'sending_time' => 'Sendezeit',
        'start_date' => 'Startdatum',
        'end_date' => 'Enddatum',
        'broadcast_method' => 'Verwendete Methode',
        'ai_prompt' => 'KI-Prompt',
        'template_message' => 'Nachrichtenvorlage',
        
        // Placeholders
        'name_placeholder' => 'Beispiel: Jahresend-Promotion-Kampagne',
        'ai_prompt_placeholder' => 'Beispiel: Erstelle eine Upselling-Nachricht für ein neues Produkt mit freundlichem und ansprechendem Ton...',
        'select_category' => 'Kategorie auswählen...',
        'select_template' => 'Vorlage auswählen...',
        'select_device' => 'Gerät auswählen...',
        'select_day' => 'Tag auswählen...',
        
        // Helper Texts
        'name_help' => 'Name der Upselling-Kampagne für interne Identifikation',
        'delay_help' => 'Zeitverzögerung zwischen Nachrichtensendungen',
        'devices_help' => 'Ein oder mehrere Geräte zum Senden von Nachrichten auswählen',
        'device_option_help' => 'Geräteauswahlmethode für Nachrichtenversand',
        'category_help' => 'Kontakte nach bestimmter Kategorie filtern',
        'labels_help' => 'Kontakt-Labels auswählen, die Nachrichten erhalten (mehrere möglich)',
        'frequency_help' => 'Bestimmen, wie oft Nachrichten gesendet werden',
        'days_help' => 'Tage für Versendung auswählen',
        'date_help' => 'Datum im Monat für Versendung auswählen',
        'yearly_help' => 'Monat und Datum für jährliche Versendung auswählen',
        'time_help' => 'Uhrzeit für Nachrichtenversand',
        'start_date_help' => 'Kampagnen-Startdatum',
        'end_date_help' => 'Leer lassen, wenn kein Zeitlimit',
        'method_help' => 'Methode zur Kampagnen-Nachrichtenerstellung auswählen',
        'ai_prompt_help' => 'KI verwendet diesen Prompt, um personalisierte Nachrichten für jeden Kontakt zu erstellen',
        'template_help' => 'Zuvor erstellte Nachrichtenvorlage auswählen',
        
        // Frequency Options
        'freq_once' => 'Einmal senden',
        'freq_daily' => 'Täglich',
        'freq_monthly' => 'Monatlich',
        'freq_yearly' => 'Jährlich',
        
        // Days
        'monday' => 'Montag',
        'tuesday' => 'Dienstag',
        'wednesday' => 'Mittwoch',
        'thursday' => 'Donnerstag',
        'friday' => 'Freitag',
        'saturday' => 'Samstag',
        'sunday' => 'Sonntag',
        'last_day' => 'Letzter Tag',
        
        // Months
        'january' => 'Januar',
        'february' => 'Februar',
        'march' => 'März',
        'april' => 'April',
        'may' => 'Mai',
        'june' => 'Juni',
        'july' => 'Juli',
        'august' => 'August',
        'september' => 'September',
        'october' => 'Oktober',
        'november' => 'November',
        'december' => 'Dezember',
        
        // Broadcast Methods
        'method_template_option' => 'Vorlage verwenden',
        'method_ai_option' => 'KI-Prompt verwenden',
        
        // Device Options
        'device_sequence' => 'Einzelgerät (Sequenziell)',
        'device_spin' => 'KI-Auswahl (Automatisch wählen)',
        'device_random' => 'Zufällig',
        
        // Units
        'seconds' => 'Sekunden',
        
        // Badges & Labels
        'optional' => 'Optional',
        'required_field' => 'Pflichtfeld',
        
        // Buttons
        'create_campaign' => 'Kampagne erstellen',
        'update_campaign' => 'Kampagne aktualisieren',
        
        // Alert & Tips
        'campaign_tips' => 'Upselling-Kampagnen-Tipps:',
        'tip_ai_prompt' => 'KI-Prompt für personalisiertere und dynamischere Nachrichten verwenden',
        'tip_optimal_time' => 'Richtige Sendezeit für maximales Engagement wählen',
        'tip_use_labels' => 'Labels für spezifischeres Targeting nutzen',
        'tip_frequency' => 'Tägliche/monatliche Frequenz für automatisches Follow-up verwenden',
    ]
];