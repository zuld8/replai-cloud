<?php

return [
    // ===== WHATSAPP DEVICE =====
    'whatsapp' => [
        // Section Titles
        'device_info'                   => 'डिवाइस जानकारी',
        'ai_config'                     => 'एआई एजेंट कॉन्फ़िगरेशन',
        'schedule_limits'               => 'समय सारणी और सीमाएं',
        
        // Device Information
        'device_name'                   => 'डिवाइस नाम',
        'device_name_placeholder'       => 'उदाहरण: एडमिन डिवाइस',
        'device_name_hint'              => 'इस WhatsApp डिवाइस को पहचानने के लिए नाम',
        'wa_number'                     => 'WhatsApp नंबर',
        'wa_number_placeholder'         => '+91 xxx xxx xxx',
        'wa_number_hint'                => 'WhatsApp नंबर जो सिस्टम से जुड़ेगा (फॉर्मेट: +91)',
        'team_member'                   => 'टीम सदस्य',
        'team_member_hint'              => 'उस टीम सदस्य का चयन करें जो इस डिवाइस का प्रबंधन करेगा',
        'team_member_placeholder'       => 'टीम सदस्य चुनें...',
        
        // Device Settings
        'device_notification'           => 'डिवाइस नोटिफिकेशन',
        'device_notification_hint'      => 'यदि आप अपने डिवाइस पर नोटिफिकेशन नहीं चाहते तो इसे निष्क्रिय करें',
        'save_chat_history'             => 'चैट इतिहास सहेजें',
        'save_chat_history_hint'        => 'इतिहास दैनिक रीसेट के बिना स्वचालित रूप से सहेजा जाएगा',
        'auto_read_before_reply'        => 'चैटबॉट जवाब देने से पहले चैट पढ़ें',
        'auto_read_before_reply_hint'   => 'यदि AI के स्वचालित उत्तर से पहले चैट पढ़ना चाहते हैं तो सक्रिय करें। फोन को नोटिफिकेशन निष्क्रिय हो जाएगी।',
        'webhook_url'                   => 'WebHook URL (वैकल्पिक)',
        'webhook_url_placeholder'       => 'https://example.com/webhook',
        'webhook_url_hint'              => 'आने वाले संदेशों की नोटिफिकेशन के लिए webhook URL (वैकल्पिक)',
        
        // AI Configuration
        'chatbot_method'                => 'चैटबॉट विधि',
        'chatbot_method_hint'           => 'उपयोग की जाने वाली स्वचालित उत्तर विधि चुनें',
        'method_all'                    => 'सभी (मैन्युअल + AI)',
        'method_chatbot'                => 'चैटबॉट (मैन्युअल)',
        'method_ai'                     => 'AI (फाइन ट्यूनल)',
        'ai_training'                   => 'AI प्रशिक्षण',
        'ai_training_hint'              => 'इस डिवाइस के लिए AI प्रशिक्षण डेटा चुनें',
        'ai_training_full_hint'         => 'उत्तर की सटीकता बढ़ाने के लिए AI प्रशिक्षण डेटासेट चुनें',
        'ai_training_placeholder'       => 'AI प्रशिक्षण चुनें...',
        'select_ai_training'            => 'AI प्रशिक्षण चुनें',
        'choose_ai_training'            => 'AI प्रशिक्षण चुनें',
        'auto_reply_option'             => 'चैटबॉट सक्रिय है',
        'auto_reply_option_hint'        => 'निर्धारित करें कि चैटबॉट कहाँ संदेशों का उत्तर देगा',
        'reply_all'                     => 'सभी (व्यक्तिगत और ग्रुप)',
        'reply_personal'                => 'व्यक्तिगत',
        'reply_group'                   => 'ग्रुप',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'निश्चित दिनों में चैटबॉट निष्क्रिय',
        'inactive_certain_day_hint'     => 'यदि निश्चित दिनों में चैटबॉट निष्क्रिय करना चाहते हैं तो सक्रिय करें',
        'select_days'                   => 'दिन चुनें',
        'select_days_placeholder'       => 'दिन चुनें...',
        'day_monday'                    => 'सोमवार',
        'day_tuesday'                   => 'मंगलवार',
        'day_wednesday'                 => 'बुधवार',
        'day_thursday'                  => 'गुरुवार',
        'day_friday'                    => 'शुक्रवार',
        'day_saturday'                  => 'शनिवार',
        'day_sunday'                    => 'रविवार',
        
        'inactive_certain_time'         => 'निश्चित समय में चैटबॉट निष्क्रिय',
        'inactive_certain_time_hint'    => 'यदि निश्चित समय में चैटबॉट निष्क्रिय करना चाहते हैं तो सक्रिय करें',
        'start_time'                    => 'निष्क्रिय शुरुआत का समय',
        'start_time_hint'               => 'चैटबॉट निष्क्रिय होने का शुरुआती समय',
        'end_time'                      => 'निष्क्रिय समाप्ति का समय',
        'end_time_hint'                 => 'चैटबॉट निष्क्रिय समाप्ति का समय',
        
        'daily_broadcast_limit'         => 'दैनिक प्रसारण सीमा',
        'daily_broadcast_limit_hint'    => 'यदि चैटबॉट की प्रति दिन संदेश भेजने की सीमा है तो सक्रिय करें',
        'enter_daily_limit'             => 'दैनिक सीमा दर्ज करें',
        'daily_limit_placeholder'       => 'उदाहरण: 100',
        'daily_limit_suffix'            => 'संदेश/दिन',
        'daily_limit_hint'              => 'प्रति दिन भेजे जा सकने वाले अधिकतम संदेशों की संख्या',
        
        // Actions
        'save_device'                   => 'डिवाइस सहेजें',
        'update_device'                 => 'डिवाइस अपडेट करें',
        'cancel'                        => 'रद्द करें',
        'required_fields'               => '* चिह्न वाले फील्ड भरना अनिवार्य है',
        
        // Messages
        'device_created'                => 'WhatsApp डिवाइस सफलतापूर्वक जोड़ा गया',
        'device_updated'                => 'WhatsApp डिवाइस सफलतापूर्वक अपडेट किया गया',
        'device_deleted'                => 'WhatsApp डिवाइस सफलतापूर्वक हटाया गया',
        
        // List/Index Page
        'add_connection'                => 'WhatsApp कनेक्शन जोड़ें',
        'total_device'                  => 'कुल डिवाइस',
        'not_connected'                 => 'कनेक्ट नहीं है',
        'device_connected'              => 'डिवाइस कनेक्ट है',
        'connection_list'               => 'WhatsApp कनेक्शन सूची',
        'broadcast_sent_today'          => 'आज का प्रसारण भेजा गया',
        'daily_broadcast_limit_label'   => 'दैनिक प्रसारण सीमा',
        'device_name_label'             => 'डिवाइस नाम',
        'phone_number'                  => 'फोन नंबर',
        
        // Actions
        'scan_qr'                       => 'QR स्कैन करें',
        'copy_id'                       => 'ID कॉपी करें',
        'settings'                      => 'सेटिंग्स',
        'edit_device'                   => 'डिवाइस संपादित करें',
        'delete_device'                 => 'डिवाइस हटाएं',
        'copied_device_id'              => 'डिवाइस ID सफलतापूर्वक कॉपी किया गया',
        'search_device'                 => 'डिवाइस खोजें...',
        
        // Status
        'status_active'                 => 'सक्रिय',
        'status_inactive'               => 'निष्क्रिय',
    ],
    // ===== TELEGRAM =====
    'telegram' => [
        // Section Titles
        'device_info'                   => 'टेलीग्राम डिवाइस जानकारी',
        'ai_config'                     => 'एआई एजेंट कॉन्फ़िगरेशन',
        'schedule_limits'               => 'समय सारणी और सीमाएं',
        'integrated_telegram_list'      => 'एकीकृत टेलीग्राम सूची',
        'add_telegram'                  => 'टेलीग्राम जोड़ें',
        'edit_telegram'                 => 'टेलीग्राम संपादित करें',
        
        // Device Information
        'device_name'                   => 'डिवाइस नाम',
        'device_name_placeholder'       => 'उदाहरण: ग्राहक सेवा बॉट',
        'device_name_hint'              => 'इस टेलीग्राम बॉट को पहचानने के लिए नाम',
        'bot_token'                     => 'बॉट टोकन',
        'bot_token_placeholder'         => '@BotFather से बॉट टोकन दर्ज करें',
        'bot_token_hint'                => 'टेलीग्राम में @BotFather से प्राप्त बॉट टोकन',
        'team_member'                   => 'टीम सदस्य',
        'team_member_hint'              => 'उस टीम सदस्य का चयन करें जो इस बॉट का प्रबंधन करेगा',
        'team_member_placeholder'       => 'टीम सदस्य चुनें...',
        
        // AI Configuration
        'auto_reply_method'             => 'स्वचालित उत्तर विधि',
        'auto_reply_method_hint'        => 'उपयोग की जाने वाली स्वचालित उत्तर विधि चुनें',
        'method_all'                    => 'सभी (मैन्युअल + AI)',
        'method_chatbot'                => 'चैटबॉट (मैन्युअल)',
        'method_ai'                     => 'AI (फाइन ट्यूनल)',
        'ai_training'                   => 'AI प्रशिक्षण',
        'ai_training_hint'              => 'इस बॉट के लिए AI प्रशिक्षण डेटा चुनें',
        'ai_training_placeholder'       => 'AI प्रशिक्षण चुनें...',
        'choose_ai_training'            => 'AI प्रशिक्षण चुनें',
        
        // Status & Options
        'status'                        => 'स्थिति',
        'status_hint'                   => 'टेलीग्राम बॉट की सक्रिय/निष्क्रिय स्थिति',
        'status_active'                 => 'सक्रिय',
        'status_inactive'               => 'निष्क्रिय',
        'auto_reply_option'             => 'बॉट सक्रिय है',
        'auto_reply_option_hint'        => 'निर्धारित करें कि बॉट कहाँ संदेशों का उत्तर देगा',
        'reply_all'                     => 'सभी (व्यक्तिगत और ग्रुप)',
        'reply_personal'                => 'व्यक्तिगत',
        'reply_group'                   => 'ग्रुप',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'निश्चित दिनों में बॉट निष्क्रिय',
        'inactive_certain_day_hint'     => 'यदि निश्चित दिनों में बॉट निष्क्रिय करना चाहते हैं तो सक्रिय करें',
        'inactive_certain_day_no'       => 'नहीं, हर दिन सक्रिय',
        'inactive_certain_day_yes'      => 'हाँ, निश्चित दिनों में निष्क्रिय',
        'select_days'                   => 'दिन चुनें',
        'select_days_placeholder'       => 'दिन चुनें...',
        'day_monday'                    => 'सोमवार',
        'day_tuesday'                   => 'मंगलवार',
        'day_wednesday'                 => 'बुधवार',
        'day_thursday'                  => 'गुरुवार',
        'day_friday'                    => 'शुक्रवार',
        'day_saturday'                  => 'शनिवार',
        'day_sunday'                    => 'रविवार',
        
        'inactive_certain_time'         => 'निश्चित समय में बॉट निष्क्रिय',
        'inactive_certain_time_hint'    => 'यदि निश्चित समय में बॉट निष्क्रिय करना चाहते हैं तो सक्रिय करें',
        'inactive_certain_time_no'      => 'नहीं, 24 घंटे सक्रिय',
        'inactive_certain_time_yes'     => 'हाँ, निश्चित समय में निष्क्रिय',
        'start_time'                    => 'निष्क्रिय शुरुआत का समय',
        'start_time_hint'               => 'बॉट निष्क्रिय होने का शुरुआती समय',
        'end_time'                      => 'निष्क्रिय समाप्ति का समय',
        'end_time_hint'                 => 'बॉट निष्क्रिय समाप्ति का समय',
        
        'daily_limit'                   => 'दैनिक सीमा',
        'daily_limit_hint'              => 'यदि बॉट की प्रति दिन संदेश भेजने की सीमा है तो सक्रिय करें',
        'daily_limit_no'                => 'कोई सीमा नहीं',
        'daily_limit_yes'               => 'दैनिक सीमा है',
        'enter_daily_limit'             => 'दैनिक सीमा दर्ज करें',
        'daily_limit_placeholder'       => 'उदाहरण: 1000',
        'daily_limit_suffix'            => 'संदेश/दिन',
        'daily_limit_hint_input'        => 'प्रति दिन भेजे जा सकने वाले अधिकतम संदेशों की संख्या',
        
        // Actions
        'back_to_list'                  => 'टेलीग्राम पेज पर वापस जाएं',
        'add_device'                    => 'डिवाइस जोड़ें',
        'save_device'                   => 'डिवाइस सहेजें',
        'update_device'                 => 'डिवाइस अपडेट करें',
        'cancel'                        => 'रद्द करें',
        'required_fields'               => '* चिह्न वाले फील्ड भरना अनिवार्य है',
        
        // Messages
        'device_created'                => 'टेलीग्राम बॉट सफलतापूर्वक जोड़ा गया',
        'device_updated'                => 'टेलीग्राम बॉट सफलतापूर्वक अपडेट किया गया',
        'device_deleted'                => 'टेलीग्राम बॉट सफलतापूर्वक हटाया गया',
        
        // List/Index Page
        'add_connection'                => 'टेलीग्राम कनेक्शन जोड़ें',
        'total_bot'                     => 'कुल टेलीग्राम',
        'not_connected'                 => 'कनेक्ट नहीं है',
        'bot_connected'                 => 'टेलीग्राम कनेक्ट है',
        'connection_list'               => 'टेलीग्राम कनेक्शन सूची',
        'bot_name'                      => 'बॉट नाम',
        'broadcast_sent_today'          => 'आज का प्रसारण भेजा गया',
        'daily_broadcast_limit_label'   => 'दैनिक प्रसारण सीमा',
        
        // Actions List
        'copy_id'                       => 'ID कॉपी करें',
        'edit_bot'                      => 'बॉट संपादित करें',
        'delete_bot'                    => 'बॉट हटाएं',
        'copied_bot_id'                 => 'बॉट ID सफलतापूर्वक कॉपी किया गया',
        'search_bot'                    => 'टेलीग्राम बॉट खोजें...',
    ],
    'facebook' => [
        'add_account'                   => 'अकाउंट जोड़ें',
        'account_list'                  => 'फेसबुक अकाउंट सूची',
        'account_connected'             => 'फेसबुक अकाउंट सफलतापूर्वक जुड़ा गया।',
        'login_failed'                  => 'फेसबुक लॉगिन विफल: ',
    ],
    'instagram' => [
        'add_account'                   => 'अकाउंट जोड़ें',
        'account_list'                  => 'इंस्टाग्राम अकाउंट सूची',
        'account_connected'             => 'इंस्टाग्राम अकाउंट सफलतापूर्वक जुड़ा गया।',
        'login_failed'                  => 'इंस्टाग्राम लॉगिन विफल: ',
    ],
];