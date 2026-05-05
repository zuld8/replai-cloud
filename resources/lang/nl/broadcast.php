<?php

return [
    'wa' => [
        // Index/List Page
        'create_schedule_button' => 'Maak Broadcast Schema',
        'wa_group' => 'Wa Groep',
        'page_title' => 'WhatsApp Broadcast Lijst',
        
        // Card Headers
        'broadcast_info' => 'Broadcast Informatie',
        'target_template' => 'Doel & Sjabloon',
        
        // Form Labels
        'name' => 'Broadcast Titel',
        'schedule' => 'Verzend Schema',
        'template' => 'Bericht Sjabloon',
        'devices' => 'Apparaten',
        'device_option' => 'Apparaat Gebruik Optie',
        'delay' => 'Vertraging Tussen Verzendingen',
        'stop_sending' => 'Stop Verzenden Na',
        'rest_sending' => 'Rust Pauze',
        'category' => 'Bedrijfs Categorie',
        'whatsapp_group' => 'WhatsApp Groep',
        'location_target' => 'Locatie Doel',
        
        // Placeholders
        'name_placeholder' => 'Voorbeeld: Eindejaars Promotie 2024',
        'choose_group' => 'Kies WhatsApp groep',
        'choose_device' => 'Kies WhatsApp apparaat',
        
        // Device Options
        'device_sequence' => 'Enkel Apparaat (Opeenvolgend)',
        'device_spin' => 'AI Keuze (Spin)',
        'device_random' => 'Willekeurig',
        
        // Units
        'seconds' => 'seconden',
        'numbers' => 'nummers',
        
        // Helper Texts
        'name_help' => 'Broadcast campagne naam voor identificatie',
        'schedule_help' => 'Broadcast bericht verzend schema',
        'template_help' => 'Bericht sjabloon dat verzonden wordt',
        'devices_help' => 'Selecteer WhatsApp apparaat voor broadcast verzending',
        'device_option_help' => 'Apparaat selectie methode voor verzending',
        'delay_help' => 'Aanbeveling: 30-300 seconden. Hoe kleiner, hoe hoger het risico op blokkering',
        'stop_sending_help' => 'Stop verzenden na hoeveel nummers',
        'rest_sending_help' => 'Pauze voordat berichten weer verzonden worden',
        'category_help' => 'Filter doelen gebaseerd op bedrijfs categorie',
        'whatsapp_group_help' => 'Richt contacten van specifieke WhatsApp groepen',
        'province_help' => 'Filter doelen gebaseerd op provincie',
        'city_help' => 'Filter doelen gebaseerd op stad/gemeente',
        'district_help' => 'Filter doelen gebaseerd op district',
        
        // Badges & Labels
        'optional' => 'Optioneel',
        'required_field' => 'Verplicht veld',
        
        // Alert & Tips
        'safe_sending_tips' => 'Veilig Verzenden Tips:',
        'tip_delay' => 'Gebruik minimaal 30 seconden vertraging om blokkering te voorkomen',
        'tip_batch' => 'Beperk verzending tot maximaal 50-100 berichten per batch',
        'tip_rest' => 'Geef voldoende rustpauze tussen batches',
        'tip_multiple' => 'Gebruik meerdere apparaten voor load balancing',
    ],
    
    'email' => [
        // Form Fields
        'category' => 'Bedrijfs Categorie',
        'template' => 'Email Sjabloon',
        'name' => 'Broadcast Titel',
        'schedule' => 'Verzend Schema',
        
        // Placeholders
        'name_placeholder' => 'Voorbeeld: Maandelijkse Nieuwsbrief Oktober 2024',
        
        // Helper Texts
        'category_help' => 'Filter doel ontvangers gebaseerd op bedrijfs categorie',
        'template_help' => 'Email sjabloon dat naar ontvangers verzonden wordt',
        'name_help' => 'Broadcast campagne naam voor identificatie',
        'schedule_help' => 'Email broadcast verzend schema',
        
        // Badges & Labels
        'optional' => 'Optioneel',
        'required_field' => 'Verplicht veld',
        
        // Alert & Tips
        'email_sending_tips' => 'Email Verzenden Tips:',
        'tip_test_template' => 'Zorg ervoor dat het email sjabloon getest is voor broadcast',
        'tip_optimal_time' => 'Kies optimale verzendtijd voor maximale betrokkenheid',
        'tip_use_category' => 'Gebruik categorieën voor meer specifieke targeting',
        'tip_check_spam' => 'Controleer inhoud nogmaals om spam folder te vermijden',
    ],
    
    'upselling' => [
        // List/Index Page
        'page_title' => 'Upselling Campagne Lijst',
        'create_campaign_button' => 'Maak Nieuwe Campagne',
        'refresh_button' => 'Vernieuwen',
        
        // Table Headers
        'campaign_info' => 'Campagne Info',
        'schedule_frequency' => 'Schema & Frequentie',
        'target_category' => 'Doel & Categorie',
        'method_template' => 'Methode & Sjabloon',
        'status' => 'Status',
        'action' => 'Actie',
        
        // Filter Options
        'all_status' => 'Alle Statussen',
        'status_active' => 'Actief',
        'status_inactive' => 'Inactief',
        'status_scheduled' => 'Gepland',
        'status_completed' => 'Voltooid',
        'all_frequency' => 'Alle Frequenties',
        'frequency_once' => 'Eenmaal',
        'frequency_daily' => 'Dagelijks',
        'frequency_monthly' => 'Maandelijks',
        'frequency_yearly' => 'Jaarlijks',
        
        // DataTable Language
        'search_placeholder' => 'Zoek campagne...',
        'length_menu' => 'Toon _MENU_ campagnes per pagina',
        'info' => 'Toont _START_ tot _END_ van _TOTAL_ campagnes',
        'info_empty' => 'Geen campagnes',
        'info_filtered' => '(gefilterd van _MAX_ totale campagnes)',
        'paginate_first' => 'Eerste',
        'paginate_last' => 'Laatste',
        'paginate_next' => 'Volgende',
        'paginate_previous' => 'Vorige',
        
        // DataTable Content
        'devices_count' => 'apparaten',
        'delay_label' => 'Vertraging',
        'once_send' => 'Eenmalige verzending',
        'date_prefix' => 'Datum',
        'every_month' => 'elke maand',
        'every_year' => 'elk jaar',
        'ongoing' => 'Doorlopend',
        'all_categories' => 'Alle categorieën',
        'labels_count' => 'labels',
        'template_label' => 'Sjabloon',
        'ai_generated' => 'AI Gegenereerd',
        
        // Day Short Labels
        'day_monday_short' => 'Ma',
        'day_tuesday_short' => 'Di',
        'day_wednesday_short' => 'Wo',
        'day_thursday_short' => 'Do',
        'day_friday_short' => 'Vr',
        'day_saturday_short' => 'Za',
        'day_sunday_short' => 'Zo',
        
        // Month Short Labels
        'month_jan' => 'Jan',
        'month_feb' => 'Feb',
        'month_mar' => 'Mrt',
        'month_apr' => 'Apr',
        'month_may' => 'Mei',
        'month_jun' => 'Jun',
        'month_jul' => 'Jul',
        'month_aug' => 'Aug',
        'month_sep' => 'Sep',
        'month_oct' => 'Okt',
        'month_nov' => 'Nov',
        'month_dec' => 'Dec',
        
        // Action Button Tooltips
        'btn_view_detail' => 'Bekijk Details',
        'btn_edit_campaign' => 'Bewerk Campagne',
        'btn_duplicate_campaign' => 'Dupliceer Campagne',
        'btn_delete_campaign' => 'Verwijder Campagne',
        
        // Confirmation Messages
        'confirm_activate' => 'Weet u zeker dat u deze campagne wilt activeren?',
        'confirm_deactivate' => 'Weet u zeker dat u deze campagne wilt deactiveren?',
        'confirm_delete' => 'Weet u zeker dat u deze campagne wilt verwijderen? Deze actie kan niet ongedaan gemaakt worden.',
        
        // Success Messages
        'success_title' => 'Gelukt',
        'success_delete' => 'Campagne succesvol verwijderd',
        
        // Error Messages
        'error_title' => 'Fout',
        'error_status_change' => 'Er is een fout opgetreden bij het wijzigen van de campagne status',
        'error_delete' => 'Er is een fout opgetreden bij het verwijderen van de campagne',
        
        // Back Button
        'back_to_campaign' => 'Terug Naar Upselling Campagne',
        
        // Card Headers (Form)
        'basic_info' => 'Basis Campagne Informatie',
        'schedule_config' => 'Schema Opties & Bericht Configuratie',
        
        // Form Fields
        'campaign_title' => 'Upselling Campagne Titel',
        'delay' => 'Vertraging Tussen Verzendingen',
        'devices' => 'Apparaten',
        'device_option' => 'Apparaat Gebruik Optie',
        'contact_category' => 'Contact Categorie',
        'contact_labels' => 'Contact Labels',
        'schedule_frequency' => 'Verzend Frequentie',
        'select_days' => 'Selecteer Dagen',
        'date_in_month' => 'Datum in Maand',
        'specific_date' => 'Specifieke Datum',
        'sending_time' => 'Verzendtijd',
        'start_date' => 'Startdatum',
        'end_date' => 'Einddatum',
        'broadcast_method' => 'Gebruikte Methode',
        'ai_prompt' => 'AI Prompt',
        'template_message' => 'Bericht Sjabloon',
        
        // Placeholders
        'name_placeholder' => 'Voorbeeld: Eindejaars Promotie Campagne',
        'ai_prompt_placeholder' => 'Voorbeeld: Maak een upselling bericht voor nieuw product met vriendelijke en aantrekkelijke toon...',
        'select_category' => 'Selecteer categorie...',
        'select_template' => 'Selecteer sjabloon...',
        'select_device' => 'Selecteer apparaat...',
        'select_day' => 'Selecteer dag...',
        
        // Helper Texts
        'name_help' => 'Upselling campagne naam voor interne identificatie',
        'delay_help' => 'Tijd vertraging tussen bericht verzendingen',
        'devices_help' => 'Selecteer een of meer apparaten voor bericht verzending',
        'device_option_help' => 'Apparaat selectie methode voor bericht verzending',
        'category_help' => 'Filter contacten gebaseerd op specifieke categorie',
        'labels_help' => 'Selecteer contact labels die berichten zullen ontvangen (meerdere mogelijk)',
        'frequency_help' => 'Bepaal hoe vaak berichten verzonden worden',
        'days_help' => 'Selecteer dagen voor verzending',
        'date_help' => 'Selecteer datum in maand voor verzending',
        'yearly_help' => 'Selecteer maand en datum voor jaarlijkse verzending',
        'time_help' => 'Bericht verzendtijd',
        'start_date_help' => 'Campagne startdatum',
        'end_date_help' => 'Laat leeg als er geen tijdslimiet is',
        'method_help' => 'Selecteer campagne bericht aanmaakmethode',
        'ai_prompt_help' => 'AI zal deze prompt gebruiken om berichten te maken die aangepast zijn aan elk contact',
        'template_help' => 'Selecteer eerder gemaakte bericht sjabloon',
        
        // Frequency Options
        'freq_once' => 'Eenmaal Verzenden',
        'freq_daily' => 'Dagelijks',
        'freq_monthly' => 'Maandelijks',
        'freq_yearly' => 'Jaarlijks',
        
        // Days
        'monday' => 'Maandag',
        'tuesday' => 'Dinsdag',
        'wednesday' => 'Woensdag',
        'thursday' => 'Donderdag',
        'friday' => 'Vrijdag',
        'saturday' => 'Zaterdag',
        'sunday' => 'Zondag',
        'last_day' => 'Laatste Dag',
        
        // Months
        'january' => 'Januari',
        'february' => 'Februari',
        'march' => 'Maart',
        'april' => 'April',
        'may' => 'Mei',
        'june' => 'Juni',
        'july' => 'Juli',
        'august' => 'Augustus',
        'september' => 'September',
        'october' => 'Oktober',
        'november' => 'November',
        'december' => 'December',
        
        // Broadcast Methods
        'method_template_option' => 'Gebruik Sjabloon',
        'method_ai_option' => 'Gebruik AI Prompt',
        
        // Device Options
        'device_sequence' => 'Enkel Apparaat (Opeenvolgend)',
        'device_spin' => 'AI Keuze (Automatisch Selecteren)',
        'device_random' => 'Willekeurig',
        
        // Units
        'seconds' => 'seconden',
        
        // Badges & Labels
        'optional' => 'Optioneel',
        'required_field' => 'Verplicht veld',
        
        // Buttons
        'create_campaign' => 'Maak Campagne',
        'update_campaign' => 'Update Campagne',
        
        // Alert & Tips
        'campaign_tips' => 'Upselling Campagne Tips:',
        'tip_ai_prompt' => 'Gebruik AI prompt voor meer persoonlijke en dynamische berichten',
        'tip_optimal_time' => 'Kies juiste verzendtijd voor maximale betrokkenheid',
        'tip_use_labels' => 'Gebruik labels voor meer specifieke targeting',
        'tip_frequency' => 'Gebruik dagelijkse/maandelijkse frequentie voor automatische follow-ups',
    ]
];