<?php

return [
    // ===== WHATSAPP DEVICE =====
    'whatsapp' => [
        // Section Titles
        'device_info'                   => 'Device Information',
        'ai_config'                     => 'AI Agent Configuration',
        'schedule_limits'               => 'Schedule & Limits',
        
        // Device Information
        'device_name'                   => 'Device Name',
        'device_name_placeholder'       => 'Example: Admin Device',
        'device_name_hint'              => 'Name to identify this WhatsApp device',
        'wa_number'                     => 'WhatsApp Number',
        'wa_number_placeholder'         => '+62 xxx xxx xxx',
        'wa_number_hint'                => 'WhatsApp number that will be connected to the system (format: +62)',
        'team_member'                   => 'Team Member',
        'team_member_hint'              => 'Select team member who will manage this device',
        'team_member_placeholder'       => 'Select team member...',
        
        // Device Settings
        'device_notification'           => 'Device Notification',
        'device_notification_hint'      => 'Disable if you don\'t want to receive notifications on your device',
        'save_chat_history'             => 'Save Chat History',
        'save_chat_history_hint'        => 'History will be saved automatically without daily reset',
        'auto_read_before_reply'        => 'Read chat first before ChatBot replies',
        'auto_read_before_reply_hint'   => 'Enable if you want to read chat first before AI replies automatically. Mobile phone notifications will be disabled.',
        'webhook_url'                   => 'WebHook URL (Optional)',
        'webhook_url_placeholder'       => 'https://example.com/webhook',
        'webhook_url_hint'              => 'Webhook URL to receive incoming message notifications (optional)',
        
        // AI Configuration
        'chatbot_method'                => 'Chatbot Method',
        'chatbot_method_hint'           => 'Select auto-reply method to be used',
        'method_all'                    => 'All (Manual + AI)',
        'method_chatbot'                => 'Chatbot (Manual)',
        'method_ai'                     => 'AI (Fine Tunnel)',
        'ai_training'                   => 'AI Training',
        'ai_training_hint'              => 'Select AI training data for this device',
        'ai_training_full_hint'         => 'Select AI training dataset to improve answer accuracy',
        'ai_training_placeholder'       => 'Select AI Training...',
        'select_ai_training'            => 'Select AI Training',
        'choose_ai_training'            => 'Choose AI Training',
        'auto_reply_option'             => 'Chatbot Active in',
        'auto_reply_option_hint'        => 'Determine where chatbot will actively reply messages',
        'reply_all'                     => 'All (Personal & Group)',
        'reply_personal'                => 'Personal',
        'reply_group'                   => 'Group',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'Chatbot Inactive on Certain Days',
        'inactive_certain_day_hint'     => 'Enable if you want chatbot inactive on certain days',
        'select_days'                   => 'Select Days',
        'select_days_placeholder'       => 'Select days...',
        'day_monday'                    => 'Monday',
        'day_tuesday'                   => 'Tuesday',
        'day_wednesday'                 => 'Wednesday',
        'day_thursday'                  => 'Thursday',
        'day_friday'                    => 'Friday',
        'day_saturday'                  => 'Saturday',
        'day_sunday'                    => 'Sunday',
        
        'inactive_certain_time'         => 'Chatbot Inactive at Certain Hours',
        'inactive_certain_time_hint'    => 'Enable if you want chatbot inactive at certain hours',
        'start_time'                    => 'Inactive Start Time',
        'start_time_hint'               => 'Time when chatbot starts being inactive',
        'end_time'                      => 'Inactive End Time',
        'end_time_hint'                 => 'Time when chatbot stops being inactive',
        
        'daily_broadcast_limit'         => 'Daily Broadcast Limit',
        'daily_broadcast_limit_hint'    => 'Enable if chatbot has daily message sending limit',
        'enter_daily_limit'             => 'Enter Daily Limit',
        'daily_limit_placeholder'       => 'Example: 100',
        'daily_limit_suffix'            => 'messages/day',
        'daily_limit_hint'              => 'Maximum number of messages that can be sent per day',
        
        // Actions
        'save_device'                   => 'Save Device',
        'update_device'                 => 'Update Device',
        'cancel'                        => 'Cancel',
        'required_fields'               => 'Fields with * are required',
        
        // Messages
        'device_created'                => 'WhatsApp device successfully added',
        'device_updated'                => 'WhatsApp device successfully updated',
        'device_deleted'                => 'WhatsApp device successfully deleted',
        
        // List/Index Page
        'add_connection'                => 'Add WhatsApp Connection',
        'total_device'                  => 'Total Device',
        'not_connected'                 => 'Not Connected',
        'device_connected'              => 'Device Connected',
        'connection_list'               => 'WhatsApp Connection List',
        'broadcast_sent_today'          => 'Broadcast Sent Today',
        'daily_broadcast_limit_label'   => 'Daily Broadcast Limit',
        'device_name_label'             => 'Device Name',
        'phone_number'                  => 'Phone Number',
        
        // Actions
        'scan_qr'                       => 'Scan QR',
        'copy_id'                       => 'Copy ID',
        'settings'                      => 'Settings',
        'edit_device'                   => 'Edit Device',
        'delete_device'                 => 'Delete Device',
        'copied_device_id'              => 'Device ID successfully copied',
        'search_device'                 => 'Search device...',
        
        // Status
        'status_active'                 => 'Active',
        'status_inactive'               => 'Inactive',
    ],
    // ===== TELEGRAM =====
    'telegram' => [
        // Section Titles
        'device_info'                   => 'Telegram Device Information',
        'ai_config'                     => 'AI Agent Configuration',
        'schedule_limits'               => 'Schedule & Limits',
        'integrated_telegram_list'      => 'Integrated Telegram List',
        'add_telegram'                  => 'Add Telegram',
        'edit_telegram'                 => 'Edit Telegram',
        
        // Device Information
        'device_name'                   => 'Device Name',
        'device_name_placeholder'       => 'Example: Customer Service Bot',
        'device_name_hint'              => 'Name to identify this Telegram bot',
        'bot_token'                     => 'Bot Token',
        'bot_token_placeholder'         => 'Enter bot token from @BotFather',
        'bot_token_hint'                => 'Bot token obtained from @BotFather on Telegram',
        'team_member'                   => 'Team Member',
        'team_member_hint'              => 'Select team member who will manage this bot',
        'team_member_placeholder'       => 'Select team member...',
        
        // AI Configuration
        'auto_reply_method'             => 'AutoReply Method',
        'auto_reply_method_hint'        => 'Select auto-reply method to be used',
        'method_all'                    => 'All (Manual + AI)',
        'method_chatbot'                => 'ChatBot (Manual)',
        'method_ai'                     => 'AI (Fine Tunnel)',
        'ai_training'                   => 'AI Training',
        'ai_training_hint'              => 'Select AI training data for this bot',
        'ai_training_placeholder'       => 'Select AI Training...',
        'choose_ai_training'            => 'Choose AI Training',
        
        // Status & Options
        'status'                        => 'Status',
        'status_hint'                   => 'Active/inactive status of Telegram bot',
        'status_active'                 => 'Active',
        'status_inactive'               => 'Inactive',
        'auto_reply_option'             => 'Bot Active in',
        'auto_reply_option_hint'        => 'Determine where bot will actively reply messages',
        'reply_all'                     => 'All (Personal & Group)',
        'reply_personal'                => 'Personal',
        'reply_group'                   => 'Group',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'Bot Inactive on Certain Days',
        'inactive_certain_day_hint'     => 'Enable if you want bot inactive on certain days',
        'inactive_certain_day_no'       => 'No, Active Every Day',
        'inactive_certain_day_yes'      => 'Yes, Inactive on Certain Days',
        'select_days'                   => 'Select Days',
        'select_days_placeholder'       => 'Select days...',
        'day_monday'                    => 'Monday',
        'day_tuesday'                   => 'Tuesday',
        'day_wednesday'                 => 'Wednesday',
        'day_thursday'                  => 'Thursday',
        'day_friday'                    => 'Friday',
        'day_saturday'                  => 'Saturday',
        'day_sunday'                    => 'Sunday',
        
        'inactive_certain_time'         => 'Bot Inactive at Certain Hours',
        'inactive_certain_time_hint'    => 'Enable if you want bot inactive at certain hours',
        'inactive_certain_time_no'      => 'No, Active 24 Hours',
        'inactive_certain_time_yes'     => 'Yes, Inactive at Certain Hours',
        'start_time'                    => 'Inactive Start Time',
        'start_time_hint'               => 'Time when bot starts being inactive',
        'end_time'                      => 'Inactive End Time',
        'end_time_hint'                 => 'Time when bot stops being inactive',
        
        'daily_limit'                   => 'Daily Limit',
        'daily_limit_hint'              => 'Enable if bot has daily message sending limit',
        'daily_limit_no'                => 'No Limit',
        'daily_limit_yes'               => 'Has Daily Limit',
        'enter_daily_limit'             => 'Enter Daily Limit',
        'daily_limit_placeholder'       => 'Example: 1000',
        'daily_limit_suffix'            => 'messages/day',
        'daily_limit_hint_input'        => 'Maximum number of messages that can be sent per day',
        
        // Actions
        'back_to_list'                  => 'Back to Telegram Page',
        'add_device'                    => 'Add Device',
        'save_device'                   => 'Save Device',
        'update_device'                 => 'Update Device',
        'cancel'                        => 'Cancel',
        'required_fields'               => 'Fields with * are required',
        
        // Messages
        'device_created'                => 'Telegram bot successfully added',
        'device_updated'                => 'Telegram bot successfully updated',
        'device_deleted'                => 'Telegram bot successfully deleted',
        
        // List/Index Page
        'add_connection'                => 'Add Telegram Connection',
        'total_bot'                     => 'Total Telegram',
        'not_connected'                 => 'Not Connected',
        'bot_connected'                 => 'Telegram Connected',
        'connection_list'               => 'Telegram Connection List',
        'bot_name'                      => 'Bot Name',
        'broadcast_sent_today'          => 'Broadcast Sent Today',
        'daily_broadcast_limit_label'   => 'Daily Broadcast Limit',
        
        // Actions List
        'copy_id'                       => 'Copy ID',
        'edit_bot'                      => 'Edit Bot',
        'delete_bot'                    => 'Delete Bot',
        'copied_bot_id'                 => 'Bot ID successfully copied',
        'search_bot'                    => 'Search telegram bot...',
    ],
    'facebook' => [
        'add_account'                   => 'Add Account',
        'account_list'                  => 'Facebook Account List',
        'account_connected'             => 'Facebook account successfully connected.',
        'login_failed'                  => 'Facebook login failed: ',
    ],
    'instagram' => [
        'add_account'                   => 'Add Account',
        'account_list'                  => 'Instagram Account List',
        'account_connected'             => 'Instagram account successfully connected.',
        'login_failed'                  => 'Instagram login failed: ',
    ],
];