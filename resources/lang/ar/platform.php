<?php

return [
    // ===== WHATSAPP DEVICE =====
    'whatsapp' => [
        // Section Titles
        'device_info'                   => 'معلومات الجهاز',
        'ai_config'                     => 'إعدادات وكيل الذكاء الاصطناعي',
        'schedule_limits'               => 'الجدولة والحدود',
        
        // Device Information
        'device_name'                   => 'اسم الجهاز',
        'device_name_placeholder'       => 'مثال: جهاز المدير',
        'device_name_hint'              => 'اسم لتحديد جهاز الواتساب هذا',
        'wa_number'                     => 'رقم الواتساب',
        'wa_number_placeholder'         => '+966 xxx xxx xxx',
        'wa_number_hint'                => 'رقم الواتساب الذي سيتم ربطه بالنظام (التنسيق: +966)',
        'team_member'                   => 'عضو الفريق',
        'team_member_hint'              => 'اختر عضو الفريق الذي سيدير هذا الجهاز',
        'team_member_placeholder'       => 'اختر عضو الفريق...',
        
        // Device Settings
        'device_notification'           => 'إشعارات الجهاز',
        'device_notification_hint'      => 'قم بإلغاء التفعيل إذا كنت لا تريد تلقي إشعارات على جهازك',
        'save_chat_history'             => 'حفظ سجل الرسائل',
        'save_chat_history_hint'        => 'سيتم حفظ السجل تلقائياً بدون إعادة تعيين يومية',
        'auto_read_before_reply'        => 'قراءة المحادثة قبل رد الشات بوت',
        'auto_read_before_reply_hint'   => 'فعل هذا إذا كنت تريد قراءة المحادثة أولاً قبل الرد التلقائي للذكاء الاصطناعي. سيتم إلغاء إشعارات الهاتف.',
        'webhook_url'                   => 'رابط الـ WebHook (اختياري)',
        'webhook_url_placeholder'       => 'https://example.com/webhook',
        'webhook_url_hint'              => 'رابط الـ webhook لتلقي إشعارات الرسائل الواردة (اختياري)',
        
        // AI Configuration
        'chatbot_method'                => 'طريقة الشات بوت',
        'chatbot_method_hint'           => 'اختر طريقة الرد التلقائي المستخدمة',
        'method_all'                    => 'الكل (يدوي + ذكاء اصطناعي)',
        'method_chatbot'                => 'شات بوت (يدوي)',
        'method_ai'                     => 'ذكاء اصطناعي (نفق دقيق)',
        'ai_training'                   => 'تدريب الذكاء الاصطناعي',
        'ai_training_hint'              => 'اختر بيانات تدريب الذكاء الاصطناعي لهذا الجهاز',
        'ai_training_full_hint'         => 'اختر مجموعة بيانات تدريب الذكاء الاصطناعي لتحسين دقة الإجابات',
        'ai_training_placeholder'       => 'اختر تدريب الذكاء الاصطناعي...',
        'select_ai_training'            => 'اختر تدريب الذكاء الاصطناعي',
        'choose_ai_training'            => 'اختر تدريب الذكاء الاصطناعي',
        'auto_reply_option'             => 'الشات بوت نشط في',
        'auto_reply_option_hint'        => 'حدد أين سيكون الشات بوت نشطاً في الرد على الرسائل',
        'reply_all'                     => 'الكل (شخصي ومجموعة)',
        'reply_personal'                => 'شخصي',
        'reply_group'                   => 'مجموعة',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'الشات بوت غير نشط في أيام معينة',
        'inactive_certain_day_hint'     => 'فعل هذا إذا كنت تريد الشات بوت غير نشط في أيام معينة',
        'select_days'                   => 'اختر الأيام',
        'select_days_placeholder'       => 'اختر الأيام...',
        'day_monday'                    => 'الإثنين',
        'day_tuesday'                   => 'الثلاثاء',
        'day_wednesday'                 => 'الأربعاء',
        'day_thursday'                  => 'الخميس',
        'day_friday'                    => 'الجمعة',
        'day_saturday'                  => 'السبت',
        'day_sunday'                    => 'الأحد',
        
        'inactive_certain_time'         => 'الشات بوت غير نشط في أوقات معينة',
        'inactive_certain_time_hint'    => 'فعل هذا إذا كنت تريد الشات بوت غير نشط في أوقات معينة',
        'start_time'                    => 'وقت بداية عدم النشاط',
        'start_time_hint'               => 'وقت بداية عدم نشاط الشات بوت',
        'end_time'                      => 'وقت انتهاء عدم النشاط',
        'end_time_hint'                 => 'وقت انتهاء عدم نشاط الشات بوت',
        
        'daily_broadcast_limit'         => 'حد البث اليومي',
        'daily_broadcast_limit_hint'    => 'فعل هذا إذا كان للشات بوت حد في إرسال الرسائل يومياً',
        'enter_daily_limit'             => 'أدخل الحد اليومي',
        'daily_limit_placeholder'       => 'مثال: 100',
        'daily_limit_suffix'            => 'رسالة/يوم',
        'daily_limit_hint'              => 'العدد الأقصى للرسائل التي يمكن إرسالها يومياً',
        
        // Actions
        'save_device'                   => 'حفظ الجهاز',
        'update_device'                 => 'تحديث الجهاز',
        'cancel'                        => 'إلغاء',
        'required_fields'               => 'الحقول التي تحمل علامة * مطلوبة',
        
        // Messages
        'device_created'                => 'تم إضافة جهاز الواتساب بنجاح',
        'device_updated'                => 'تم تحديث جهاز الواتساب بنجاح',
        'device_deleted'                => 'تم حذف جهاز الواتساب بنجاح',
        
        // List/Index Page
        'add_connection'                => 'إضافة اتصال واتساب',
        'total_device'                  => 'إجمالي الأجهزة',
        'not_connected'                 => 'غير متصل',
        'device_connected'              => 'الجهاز متصل',
        'connection_list'               => 'قائمة اتصالات الواتساب',
        'broadcast_sent_today'          => 'إرسال البث اليوم',
        'daily_broadcast_limit_label'   => 'حد البث اليومي',
        'device_name_label'             => 'اسم الجهاز',
        'phone_number'                  => 'رقم الهاتف',
        
        // Actions
        'scan_qr'                       => 'مسح QR',
        'copy_id'                       => 'نسخ المعرف',
        'settings'                      => 'الإعدادات',
        'edit_device'                   => 'تعديل الجهاز',
        'delete_device'                 => 'حذف الجهاز',
        'copied_device_id'              => 'تم نسخ معرف الجهاز بنجاح',
        'search_device'                 => 'البحث عن جهاز...',
        
        // Status
        'status_active'                 => 'نشط',
        'status_inactive'               => 'غير نشط',
    ],
    // ===== TELEGRAM =====
    'telegram' => [
        // Section Titles
        'device_info'                   => 'معلومات جهاز التليجرام',
        'ai_config'                     => 'إعدادات وكيل الذكاء الاصطناعي',
        'schedule_limits'               => 'الجدولة والحدود',
        'integrated_telegram_list'      => 'قائمة التليجرام المتكاملة',
        'add_telegram'                  => 'إضافة تليجرام',
        'edit_telegram'                 => 'تعديل تليجرام',
        
        // Device Information
        'device_name'                   => 'اسم الجهاز',
        'device_name_placeholder'       => 'مثال: بوت خدمة العملاء',
        'device_name_hint'              => 'اسم لتحديد بوت التليجرام هذا',
        'bot_token'                     => 'رمز البوت',
        'bot_token_placeholder'         => 'أدخل رمز البوت من @BotFather',
        'bot_token_hint'                => 'رمز البوت المستلم من @BotFather في التليجرام',
        'team_member'                   => 'عضو الفريق',
        'team_member_hint'              => 'اختر عضو الفريق الذي سيدير هذا البوت',
        'team_member_placeholder'       => 'اختر عضو الفريق...',
        
        // AI Configuration
        'auto_reply_method'             => 'طريقة الرد التلقائي',
        'auto_reply_method_hint'        => 'اختر طريقة الرد التلقائي المستخدمة',
        'method_all'                    => 'الكل (يدوي + ذكاء اصطناعي)',
        'method_chatbot'                => 'شات بوت (يدوي)',
        'method_ai'                     => 'ذكاء اصطناعي (نفق دقيق)',
        'ai_training'                   => 'تدريب الذكاء الاصطناعي',
        'ai_training_hint'              => 'اختر بيانات تدريب الذكاء الاصطناعي لهذا البوت',
        'ai_training_placeholder'       => 'اختر تدريب الذكاء الاصطناعي...',
        'choose_ai_training'            => 'اختر تدريب الذكاء الاصطناعي',
        
        // Status & Options
        'status'                        => 'الحالة',
        'status_hint'                   => 'حالة نشط/غير نشط لبوت التليجرام',
        'status_active'                 => 'نشط',
        'status_inactive'               => 'غير نشط',
        'auto_reply_option'             => 'البوت نشط في',
        'auto_reply_option_hint'        => 'حدد أين سيكون البوت نشطاً في الرد على الرسائل',
        'reply_all'                     => 'الكل (شخصي ومجموعة)',
        'reply_personal'                => 'شخصي',
        'reply_group'                   => 'مجموعة',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'البوت غير نشط في أيام معينة',
        'inactive_certain_day_hint'     => 'فعل هذا إذا كنت تريد البوت غير نشط في أيام معينة',
        'inactive_certain_day_no'       => 'لا، نشط كل يوم',
        'inactive_certain_day_yes'      => 'نعم، غير نشط في أيام معينة',
        'select_days'                   => 'اختر الأيام',
        'select_days_placeholder'       => 'اختر الأيام...',
        'day_monday'                    => 'الإثنين',
        'day_tuesday'                   => 'الثلاثاء',
        'day_wednesday'                 => 'الأربعاء',
        'day_thursday'                  => 'الخميس',
        'day_friday'                    => 'الجمعة',
        'day_saturday'                  => 'السبت',
        'day_sunday'                    => 'الأحد',
        
        'inactive_certain_time'         => 'البوت غير نشط في أوقات معينة',
        'inactive_certain_time_hint'    => 'فعل هذا إذا كنت تريد البوت غير نشط في أوقات معينة',
        'inactive_certain_time_no'      => 'لا، نشط 24 ساعة',
        'inactive_certain_time_yes'     => 'نعم، غير نشط في أوقات معينة',
        'start_time'                    => 'وقت بداية عدم النشاط',
        'start_time_hint'               => 'وقت بداية عدم نشاط البوت',
        'end_time'                      => 'وقت انتهاء عدم النشاط',
        'end_time_hint'                 => 'وقت انتهاء عدم نشاط البوت',
        
        'daily_limit'                   => 'الحد اليومي',
        'daily_limit_hint'              => 'فعل هذا إذا كان للبوت حد في إرسال الرسائل يومياً',
        'daily_limit_no'                => 'لا يوجد حد',
        'daily_limit_yes'               => 'يوجد حد يومي',
        'enter_daily_limit'             => 'أدخل الحد اليومي',
        'daily_limit_placeholder'       => 'مثال: 1000',
        'daily_limit_suffix'            => 'رسالة/يوم',
        'daily_limit_hint_input'        => 'العدد الأقصى للرسائل التي يمكن إرسالها يومياً',
        
        // Actions
        'back_to_list'                  => 'العودة لصفحة التليجرام',
        'add_device'                    => 'إضافة جهاز',
        'save_device'                   => 'حفظ الجهاز',
        'update_device'                 => 'تحديث الجهاز',
        'cancel'                        => 'إلغاء',
        'required_fields'               => 'الحقول التي تحمل علامة * مطلوبة',
        
        // Messages
        'device_created'                => 'تم إضافة بوت التليجرام بنجاح',
        'device_updated'                => 'تم تحديث بوت التليجرام بنجاح',
        'device_deleted'                => 'تم حذف بوت التليجرام بنجاح',
        
        // List/Index Page
        'add_connection'                => 'إضافة اتصال تليجرام',
        'total_bot'                     => 'إجمالي التليجرام',
        'not_connected'                 => 'غير متصل',
        'bot_connected'                 => 'التليجرام متصل',
        'connection_list'               => 'قائمة اتصالات التليجرام',
        'bot_name'                      => 'اسم البوت',
        'broadcast_sent_today'          => 'إرسال البث اليوم',
        'daily_broadcast_limit_label'   => 'حد البث اليومي',
        
        // Actions List
        'copy_id'                       => 'نسخ المعرف',
        'edit_bot'                      => 'تعديل البوت',
        'delete_bot'                    => 'حذف البوت',
        'copied_bot_id'                 => 'تم نسخ معرف البوت بنجاح',
        'search_bot'                    => 'البحث عن بوت تليجرام...',
    ],
    'facebook' => [
        'add_account'                   => 'إضافة حساب',
        'account_list'                  => 'قائمة حسابات فيسبوك',
        'account_connected'             => 'تم ربط حساب فيسبوك بنجاح.',
        'login_failed'                  => 'فشل تسجيل الدخول إلى فيسبوك: ',
    ],
    'instagram' => [
        'add_account'                   => 'إضافة حساب',
        'account_list'                  => 'قائمة حسابات إنستغرام',
        'account_connected'             => 'تم ربط حساب إنستغرام بنجاح.',
        'login_failed'                  => 'فشل تسجيل الدخول إلى إنستغرام: ',
    ],
];