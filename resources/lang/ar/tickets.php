<?php

return [
    // Ticket Management
    'ticket_management' => 'إدارة التذاكر',
    'contact_list' => 'قائمة التذاكر',
    'total' => 'المجموع',
    'contact' => 'جهة الاتصال',
    'search_contact' => 'بحث عن جهة اتصال',
    
    // Filters
    'start_date' => 'تاريخ البدء',
    'end_date' => 'تاريخ الانتهاء',
    'level' => 'المستوى',
    'agent' => 'الوكيل',
    'tickets' => 'تذاكر',
    'all_categories' => 'جميع الفئات',
    'all_levels' => 'جميع المستويات',
    'all_agents' => 'جميع الوكلاء',
    'all_status' => 'جميع الحالات',
    'no_ticket_in_column' => 'لا توجد تذاكر في هذا العمود',
    
    // Ticket Levels
    'level_low' => 'منخفض',
    'level_medium' => 'متوسط',
    'level_high' => 'عالي',
    'level_urgent' => 'عاجل',
    
    // Ticket Information
    'ticket_id' => 'معرف التذكرة',
    'ticket_level' => 'مستوى التذكرة',
    'ticket_name' => 'اسم التذكرة',
    'ticket_name_placeholder' => 'أدخل اسم التذكرة',
    'ticket_detail' => 'تفاصيل التذكرة',
    
    // Status
    'status' => 'الحالة',
    'status_open' => 'مفتوح',
    'status_resolved' => 'تم الحل',
    'status_pending' => 'قيد الانتظار',
    'status_block' => 'محظور',
    'status_in_progress' => 'قيد التنفيذ',
    'status_closed' => 'مغلق',
    
    // Basic Info
    'basic_info' => 'المعلومات الأساسية',
    'name' => 'الاسم',
    'email' => 'البريد الإلكتروني',
    'phone' => 'الهاتف',
    'title' => 'العنوان',
    'title_placeholder' => 'أدخل عنوان التذكرة',
    'priority' => 'الأولوية',
    
    // Category
    'category' => 'الفئة',
    'select_category' => 'اختر الفئة',
    'category_name' => 'اسم الفئة',
    'category_name_placeholder' => 'أدخل اسم الفئة',
    'category_slug' => 'رابط الفئة',
    'category_slug_placeholder' => 'رابط-تلقائي',
    'category_slug_hint' => 'اتركه فارغاً للإنشاء التلقائي من الاسم',
    'category_description' => 'الوصف',
    'category_description_placeholder' => 'أدخل وصف الفئة',
    'category_active' => 'نشط',
    'category_management' => 'إدارة الفئات',
    'manage_categories' => 'إدارة الفئات',
    'add_category' => 'إضافة فئة',
    'edit_category' => 'تعديل الفئة',
    'create_category' => 'إنشاء فئة',
    'update_category' => 'تحديث الفئة',
    'search_category' => 'بحث عن الفئات...',
    'no_categories' => 'لم يتم العثور على فئات',
    'category_deleted' => 'تم حذف الفئة بنجاح',
    'categories_deleted' => 'تم حذف الفئات بنجاح',
    'category_updated' => 'تم تحديث الفئة بنجاح',
    'failed_delete_category' => 'فشل حذف الفئة',
    'failed_update_category' => 'فشل تحديث الفئة',
    'confirm_delete_category_title' => 'حذف الفئة؟',
    'confirm_delete_category_text' => 'هل أنت متأكد من حذف هذه الفئة؟',
    'confirm_bulk_delete_categories_title' => 'حذف عدة فئات؟',
    'confirm_bulk_delete_categories_text' => 'هل أنت متأكد من حذف {count} فئة؟',
    
    // Channel
    'channel_info' => 'معلومات القناة',
    'source_channel' => 'قناة المصدر',
    
    // Assignment
    'status_assignment' => 'الحالة والتعيين',
    'assign_agent' => 'تعيين وكيل',
    'handled_by' => 'تمت المعالجة بواسطة',
    'resolved_by' => 'تم الحل بواسطة',
    'assigned_to' => 'تم التعيين إلى',
    'no_agent' => 'بدون وكيل',
    'no_agent_assigned' => 'لم يتم تعيين وكيل',
    'not_handled' => 'لم تتم المعالجة',
    'hold_ctrl_multiple' => 'اضغط Ctrl/Cmd لاختيار عدة وكلاء',
    
    // Label
    'label' => 'التسمية',
    'select_label' => 'اختر التسمية',
    'label_name' => 'اسم التسمية',
    'label_name_placeholder' => 'أدخل اسم التسمية',
    'label_tag' => 'وسم/كلمة مفتاحية',
    'label_tag_desc' => 'الكلمات المفتاحية التي ستؤدي إلى تعيين هذه التسمية',
    'label_tag_placeholder' => 'مثال: عاجل، شكوى، فوترة',
    'label_position' => 'رقم الموضع',
    'label_color' => 'لون التسمية',
    'label_selection_hint' => 'سيتم تحديد التسمية تلقائياً بناءً على الكلمات المفتاحية إذا لم يتم تحديدها',
    'add_label' => 'إضافة تسمية',
    'edit_label' => 'تعديل التسمية',
    'delete_label' => 'حذف التسمية',
    'update_label' => 'تحديث التسمية',
    'save_label' => 'حفظ التسمية',
    'label_created' => 'تم إنشاء التسمية بنجاح',
    'label_updated' => 'تم تحديث التسمية بنجاح',
    'label_deleted' => 'تم حذف التسمية بنجاح',
    'failed_create_label' => 'فشل إنشاء التسمية',
    'failed_update_label' => 'فشل تحديث التسمية',
    'failed_delete_label' => 'فشل حذف التسمية',
    'confirm_delete_label_title' => 'حذف التسمية؟',
    'confirm_delete_label_text' => 'هل أنت متأكد من حذف هذه التسمية؟ سيتم نقل جميع التذاكر في هذه التسمية إلى "التذاكر الجديدة".',
    'yes_delete_label' => 'نعم، احذف التسمية!',
    'label_changed' => 'تم تغيير التسمية',
    'move_to_label' => 'نقل إلى تسمية',
    'ticket_moved' => 'تم نقل التذكرة بنجاح',
    'failed_move_ticket' => 'فشل نقل التذكرة',
    
    // Ticket Actions
    'add_ticket' => 'إنشاء تذكرة',
    'edit_ticket' => 'تعديل التذكرة',
    'create_ticket' => 'إنشاء تذكرة',
    'update_ticket' => 'تحديث التذكرة',
    'select_contact' => 'اختر جهة الاتصال',
    
    // Notes
    'notes' => 'الملاحظات',
    'notes_placeholder' => 'أدخل الملاحظات أو الوصف',
    'add' => 'إضافة',
    'add_note_placeholder' => 'إضافة ملاحظة...',
    'no_notes_yet' => 'لا توجد ملاحظات بعد. كن أول من يضيف واحدة!',
    'ctrl_enter_to_send' => 'اضغط Ctrl+Enter للإرسال',
    
    // Activity
    'activity_history' => 'سجل النشاط',
    'no_activity_logs' => 'لا توجد سجلات نشاط متاحة',
    
    // Attachment
    'attachment' => 'المرفق',
    'file_upload_hint' => 'التنسيقات المدعومة: JPG, PNG, PDF, DOC, DOCX, TXT',
    
    // Timestamps
    'timestamps' => 'الطوابع الزمنية',
    'created_at' => 'تم الإنشاء في',
    'updated_at' => 'تم التحديث في',
    
    // Actions
    'actions' => 'الإجراءات',
    'view_detail' => 'عرض التفاصيل',
    'edit' => 'تعديل',
    'delete' => 'حذف',
    'close' => 'إغلاق',
    'cancel' => 'إلغاء',
    'save' => 'حفظ',
    'manage' => 'إدارة',
    'quick_actions' => 'إجراءات سريعة',
    
    // Pagination & Loading
    'loading_data' => 'جاري تحميل البيانات...',
    'loading_more' => 'جاري تحميل المزيد...',
    'load_more' => 'تحميل المزيد',
    'remaining' => 'المتبقي',
    'contact_per_column' => 'جهات الاتصال لكل عمود',
    'contact_loaded' => 'تم تحميل جهات الاتصال',
    
    // Status Messages
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'selected' => 'محدد',
    'delete_selected' => 'حذف المحدد',
    
    // Confirmations
    'confirm_delete_title' => 'هل أنت متأكد؟',
    'confirm_delete_text' => 'لن تتمكن من التراجع عن هذا!',
    'yes_delete' => 'نعم، احذف!',
    'contact_deleted' => 'تم حذف جهة الاتصال.',
    
    // Error Messages
    'failed_delete_contact' => 'فشل حذف جهة الاتصال',
    'failed_load_data' => 'فشل تحميل البيانات',
    'error_load_data' => 'خطأ في تحميل البيانات',
];
