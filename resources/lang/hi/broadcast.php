<?php

return [
    'wa' => [
        // Index/List Page
        'create_schedule_button' => 'ब्रॉडकास्ट शेड्यूल बनाएं',
        'wa_group' => 'व्हाट्सएप ग्रुप',
        'page_title' => 'व्हाट्सएप ब्रॉडकास्ट सूची',
        
        // Card Headers
        'broadcast_info' => 'ब्रॉडकास्ट जानकारी',
        'target_template' => 'लक्ष्य और टेम्प्लेट',
        
        // Form Labels
        'name' => 'ब्रॉडकास्ट शीर्षक',
        'schedule' => 'भेजने का समय',
        'template' => 'संदेश टेम्प्लेट',
        'devices' => 'डिवाइस',
        'device_option' => 'डिवाइस उपयोग विकल्प',
        'delay' => 'भेजने के बीच देरी',
        'stop_sending' => 'इसके बाद भेजना बंद करें',
        'rest_sending' => 'आराम की देरी',
        'category' => 'व्यावसायिक श्रेणी',
        'whatsapp_group' => 'व्हाट्सएप ग्रुप',
        'location_target' => 'लक्ष्य स्थान',
        
        // Placeholders
        'name_placeholder' => 'उदाहरण: साल अंत ऑफर 2024',
        'choose_group' => 'व्हाट्सएप ग्रुप चुनें',
        'choose_device' => 'व्हाट्सएप डिवाइस चुनें',
        
        // Device Options
        'device_sequence' => 'एकल डिवाइस (क्रमानुसार)',
        'device_spin' => 'AI चुनें (स्पिन)',
        'device_random' => 'यादृच्छिक (रैंडम)',
        
        // Units
        'seconds' => 'सेकंड',
        'numbers' => 'नंबर',
        
        // Helper Texts
        'name_help' => 'पहचान के लिए ब्रॉडकास्ट अभियान का नाम',
        'schedule_help' => 'ब्रॉडकास्ट संदेश भेजने का समय',
        'template_help' => 'भेजा जाने वाला संदेश टेम्प्लेट',
        'devices_help' => 'ब्रॉडकास्ट भेजने के लिए व्हाट्सएप डिवाइस चुनें',
        'device_option_help' => 'भेजने के लिए डिवाइस चुनने की विधि',
        'delay_help' => 'सुझाव: 30-300 सेकंड। कम समय से ब्लॉक होने का खतरा बढ़ता है',
        'stop_sending_help' => 'कितने नंबर के बाद भेजना बंद करना है',
        'rest_sending_help' => 'दोबारा संदेश भेजने से पहले आराम',
        'category_help' => 'व्यावसायिक श्रेणी के आधार पर लक्ष्य फ़िल्टर करें',
        'whatsapp_group_help' => 'विशिष्ट व्हाट्सएप ग्रुप से संपर्क',
        'province_help' => 'राज्य के आधार पर लक्ष्य फ़िल्टर करें',
        'city_help' => 'शहर/जिले के आधार पर लक्ष्य फ़िल्टर करें',
        'district_help' => 'तहसील के आधार पर लक्ष्य फ़िल्टर करें',
        
        // Badges & Labels
        'optional' => 'वैकल्पिक',
        'required_field' => 'आवश्यक फ़ील्ड',
        
        // Alert & Tips
        'safe_sending_tips' => 'सुरक्षित भेजने की युक्तियां:',
        'tip_delay' => 'ब्लॉक से बचने के लिए न्यूनतम 30 सेकंड की देरी का उपयोग करें',
        'tip_batch' => 'प्रति बैच अधिकतम 50-100 संदेशों की सीमा रखें',
        'tip_rest' => 'बैच के बीच पर्याप्त आराम दें',
        'tip_multiple' => 'लोड बैलेंसिंग के लिए कई डिवाइस का उपयोग करें',
    ],
    
    'email' => [
        // Form Fields
        'category' => 'व्यावसायिक श्रेणी',
        'template' => 'ईमेल टेम्प्लेट',
        'name' => 'ब्रॉडकास्ट शीर्षक',
        'schedule' => 'भेजने का समय',
        
        // Placeholders
        'name_placeholder' => 'उदाहरण: मासिक न्यूज़लेटर अक्टूबर 2024',
        
        // Helper Texts
        'category_help' => 'व्यावसायिक श्रेणी के आधार पर लक्ष्य प्राप्तकर्ता फ़िल्टर करें',
        'template_help' => 'प्राप्तकर्ताओं को भेजा जाने वाला ईमेल टेम्प्लेट',
        'name_help' => 'पहचान के लिए ब्रॉडकास्ट अभियान का नाम',
        'schedule_help' => 'ईमेल ब्रॉडकास्ट भेजने का समय',
        
        // Badges & Labels
        'optional' => 'वैकल्पिक',
        'required_field' => 'आवश्यक फ़ील्ड',
        
        // Alert & Tips
        'email_sending_tips' => 'ईमेल भेजने की युक्तियां:',
        'tip_test_template' => 'ब्रॉडकास्ट से पहले ईमेल टेम्प्लेट का परीक्षण सुनिश्चित करें',
        'tip_optimal_time' => 'अधिकतम एंगेजमेंट के लिए सर्वोत्तम समय चुनें',
        'tip_use_category' => 'अधिक विशिष्ट टार्गेटिंग के लिए श्रेणी का उपयोग करें',
        'tip_check_spam' => 'स्पैम से बचने के लिए सामग्री की दोबारा जांच करें',
    ],
    
    'upselling' => [
        // List/Index Page
        'page_title' => 'अपसेलिंग कैंपेन सूची',
        'create_campaign_button' => 'नया कैंपेन बनाएं',
        'refresh_button' => 'रीफ्रेश',
        
        // Table Headers
        'campaign_info' => 'कैंपेन जानकारी',
        'schedule_frequency' => 'समय और आवृत्ति',
        'target_category' => 'लक्ष्य और श्रेणी',
        'method_template' => 'विधि और टेम्प्लेट',
        'status' => 'स्थिति',
        'action' => 'कार्य',
        
        // Filter Options
        'all_status' => 'सभी स्थितियां',
        'status_active' => 'सक्रिय',
        'status_inactive' => 'निष्क्रिय',
        'status_scheduled' => 'निर्धारित',
        'status_completed' => 'पूर्ण',
        'all_frequency' => 'सभी आवृत्तियां',
        'frequency_once' => 'एक बार',
        'frequency_daily' => 'दैनिक',
        'frequency_monthly' => 'मासिक',
        'frequency_yearly' => 'वार्षिक',
        
        // DataTable Language
        'search_placeholder' => 'कैंपेन खोजें...',
        'length_menu' => 'प्रति पृष्ठ _MENU_ कैंपेन दिखाएं',
        'info' => '_TOTAL_ कैंपेन में से _START_ से _END_ तक दिखाया जा रहा है',
        'info_empty' => 'कोई कैंपेन नहीं',
        'info_filtered' => '(कुल _MAX_ कैंपेन से फ़िल्टर किया गया)',
        'paginate_first' => 'पहला',
        'paginate_last' => 'अंतिम',
        'paginate_next' => 'अगला',
        'paginate_previous' => 'पिछला',
        
        // DataTable Content
        'devices_count' => 'डिवाइस',
        'delay_label' => 'देरी',
        'once_send' => 'एक बार भेजना',
        'date_prefix' => 'तारीख',
        'every_month' => 'हर महीने',
        'every_year' => 'हर साल',
        'ongoing' => 'जारी',
        'all_categories' => 'सभी श्रेणियां',
        'labels_count' => 'लेबल',
        'template_label' => 'टेम्प्लेट',
        'ai_generated' => 'AI जेनेरेटेड',
        
        // Day Short Labels
        'day_monday_short' => 'सोम',
        'day_tuesday_short' => 'मंग',
        'day_wednesday_short' => 'बुध',
        'day_thursday_short' => 'गुरु',
        'day_friday_short' => 'शुक्र',
        'day_saturday_short' => 'शनि',
        'day_sunday_short' => 'रवि',
        
        // Month Short Labels
        'month_jan' => 'जन',
        'month_feb' => 'फर',
        'month_mar' => 'मार',
        'month_apr' => 'अप्र',
        'month_may' => 'मई',
        'month_jun' => 'जून',
        'month_jul' => 'जुल',
        'month_aug' => 'अग',
        'month_sep' => 'सित',
        'month_oct' => 'अक्ट',
        'month_nov' => 'नव',
        'month_dec' => 'दिस',
        
        // Action Button Tooltips
        'btn_view_detail' => 'विवरण देखें',
        'btn_edit_campaign' => 'कैंपेन संपादित करें',
        'btn_duplicate_campaign' => 'कैंपेन डुप्लिकेट करें',
        'btn_delete_campaign' => 'कैंपेन हटाएं',
        
        // Confirmation Messages
        'confirm_activate' => 'क्या आप इस कैंपेन को सक्रिय करना चाहते हैं?',
        'confirm_deactivate' => 'क्या आप इस कैंपेन को निष्क्रिय करना चाहते हैं?',
        'confirm_delete' => 'क्या आप इस कैंपेन को हटाना चाहते हैं? यह कार्य रद्द नहीं किया जा सकता।',
        
        // Success Messages
        'success_title' => 'सफल',
        'success_delete' => 'कैंपेन सफलतापूर्वक हटाया गया',
        
        // Error Messages
        'error_title' => 'त्रुटि',
        'error_status_change' => 'कैंपेन की स्थिति बदलते समय त्रुटि हुई',
        'error_delete' => 'कैंपेन हटाते समय त्रुटि हुई',
        
        // Back Button
        'back_to_campaign' => 'अपसेलिंग कैंपेन पर वापस जाएं',
        
        // Card Headers (Form)
        'basic_info' => 'कैंपेन की मूल जानकारी',
        'schedule_config' => 'भेजने का समय निर्धारण और संदेश कॉन्फ़िगरेशन',
        
        // Form Fields
        'campaign_title' => 'अपसेलिंग कैंपेन शीर्षक',
        'delay' => 'भेजने के बीच देरी',
        'devices' => 'डिवाइस',
        'device_option' => 'डिवाइस उपयोग विकल्प',
        'contact_category' => 'संपर्क श्रेणी',
        'contact_labels' => 'संपर्क लेबल',
        'schedule_frequency' => 'भेजने की आवृत्ति',
        'select_days' => 'दिन चुनें',
        'date_in_month' => 'महीने में तारीख',
        'specific_date' => 'विशिष्ट तारीख',
        'sending_time' => 'भेजने का समय',
        'start_date' => 'शुरुआती तारीख',
        'end_date' => 'समाप्ति तारीख',
        'broadcast_method' => 'उपयोग की जाने वाली विधि',
        'ai_prompt' => 'AI प्रॉम्प्ट',
        'template_message' => 'टेम्प्लेट संदेश',
        
        // Placeholders
        'name_placeholder' => 'उदाहरण: साल अंत प्रमो कैंपेन',
        'ai_prompt_placeholder' => 'उदाहरण: नए उत्पाद के लिए मित्रवत और आकर्षक टोन में अपसेलिंग संदेश बनाएं...',
        'select_category' => 'श्रेणी चुनें...',
        'select_template' => 'टेम्प्लेट चुनें...',
        'select_device' => 'डिवाइस चुनें...',
        'select_day' => 'दिन चुनें...',
        
        // Helper Texts
        'name_help' => 'आंतरिक पहचान के लिए अपसेलिंग कैंपेन का नाम',
        'delay_help' => 'संदेश भेजने के बीच का समय अंतराल',
        'devices_help' => 'संदेश भेजने के लिए एक या अधिक डिवाइस चुनें',
        'device_option_help' => 'संदेश भेजने के लिए डिवाइस चुनने की विधि',
        'category_help' => 'विशिष्ट श्रेणी के आधार पर संपर्क फ़िल्टर करें',
        'labels_help' => 'संदेश पाने वाले संपर्क लेबल चुनें (एकाधिक हो सकते हैं)',
        'frequency_help' => 'तय करें कि संदेश कितनी बार भेजा जाएगा',
        'days_help' => 'भेजने के लिए दिन चुनें',
        'date_help' => 'भेजने के लिए महीने में तारीख चुनें',
        'yearly_help' => 'वार्षिक भेजने के लिए महीना और तारीख चुनें',
        'time_help' => 'संदेश भेजने का समय',
        'start_date_help' => 'कैंपेन शुरू होने की तारीख',
        'end_date_help' => 'कोई समय सीमा न हो तो खाली छोड़ें',
        'method_help' => 'कैंपेन संदेश बनाने की विधि चुनें',
        'ai_prompt_help' => 'AI इस प्रॉम्प्ट का उपयोग करके हर संपर्क के अनुकूल संदेश बनाएगा',
        'template_help' => 'पहले से बना संदेश टेम्प्लेट चुनें',
        
        // Frequency Options
        'freq_once' => 'एक बार भेजें',
        'freq_daily' => 'दैनिक',
        'freq_monthly' => 'मासिक',
        'freq_yearly' => 'वार्षिक',
        
        // Days
        'monday' => 'सोमवार',
        'tuesday' => 'मंगलवार',
        'wednesday' => 'बुधवार',
        'thursday' => 'गुरुवार',
        'friday' => 'शुक्रवार',
        'saturday' => 'शनिवार',
        'sunday' => 'रविवार',
        'last_day' => 'अंतिम दिन',
        
        // Months
        'january' => 'जनवरी',
        'february' => 'फरवरी',
        'march' => 'मार्च',
        'april' => 'अप्रैल',
        'may' => 'मई',
        'june' => 'जून',
        'july' => 'जुलाई',
        'august' => 'अगस्त',
        'september' => 'सितंबर',
        'october' => 'अक्टूबर',
        'november' => 'नवंबर',
        'december' => 'दिसंबर',
        
        // Broadcast Methods
        'method_template_option' => 'टेम्प्लेट का उपयोग करें',
        'method_ai_option' => 'AI प्रॉम्प्ट का उपयोग करें',
        
        // Device Options
        'device_sequence' => 'एकल डिवाइस (क्रमानुसार)',
        'device_spin' => 'AI चुनें (स्वचालित चयन)',
        'device_random' => 'यादृच्छिक (रैंडम)',
        
        // Units
        'seconds' => 'सेकंड',
        
        // Badges & Labels
        'optional' => 'वैकल्पिक',
        'required_field' => 'आवश्यक फ़ील्ड',
        
        // Buttons
        'create_campaign' => 'कैंपेन बनाएं',
        'update_campaign' => 'कैंपेन अपडेट करें',
        
        // Alert & Tips
        'campaign_tips' => 'अपसेलिंग कैंपेन की युक्तियां:',
        'tip_ai_prompt' => 'अधिक व्यक्तिगत और गतिशील संदेशों के लिए AI प्रॉम्प्ट का उपयोग करें',
        'tip_optimal_time' => 'अधिकतम एंगेजमेंट के लिए सही समय चुनें',
        'tip_use_labels' => 'अधिक विशिष्ट टार्गेटिंग के लिए लेबल का उपयोग करें',
        'tip_frequency' => 'स्वचालित फॉलो-अप के लिए दैनिक/मासिक आवृत्ति का उपयोग करें',
    ]
];