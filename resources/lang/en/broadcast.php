<?php

return [
    'wa' => [
        // Index/List Page
        'create_schedule_button' => 'Create Broadcast Schedule',
        'wa_group' => 'WhatsApp Group',
        'page_title' => 'WhatsApp Broadcast List',
        
        // Card Headers
        'broadcast_info' => 'Broadcast Information',
        'target_template' => 'Target & Template',
        
        // Form Labels
        'name' => 'Broadcast Title',
        'schedule' => 'Send Schedule',
        'template' => 'Message Template',
        'devices' => 'Device',
        'device_option' => 'Device Usage Option',
        'delay' => 'Delay Between Sends',
        'stop_sending' => 'Stop Sending After',
        'rest_sending' => 'Rest Break',
        'category' => 'Business Category',
        'whatsapp_group' => 'WhatsApp Group',
        'location_target' => 'Target Location',
        
        // Placeholders
        'name_placeholder' => 'Example: Year End Promo 2024',
        'choose_group' => 'Choose WhatsApp group',
        'choose_device' => 'Choose WhatsApp device',
        
        // Device Options
        'device_sequence' => 'Single Device (Sequential)',
        'device_spin' => 'AI Choose (Spin)',
        'device_random' => 'Random',
        
        // Units
        'seconds' => 'seconds',
        'numbers' => 'numbers',
        
        // Helper Texts
        'name_help' => 'Broadcast campaign name for identification',
        'schedule_help' => 'Broadcast message sending schedule',
        'template_help' => 'Message template to be sent',
        'devices_help' => 'Select WhatsApp device for broadcast',
        'device_option_help' => 'Device selection method for sending',
        'delay_help' => 'Recommended: 30-300 seconds. Smaller values risk being blocked',
        'stop_sending_help' => 'Stop sending after how many numbers',
        'rest_sending_help' => 'Break before resuming sending messages',
        'category_help' => 'Filter targets by business category',
        'whatsapp_group_help' => 'Target contacts from specific WhatsApp group',
        'province_help' => 'Filter targets by province',
        'city_help' => 'Filter targets by city/regency',
        'district_help' => 'Filter targets by district',
        
        // Badges & Labels
        'optional' => 'Optional',
        'required_field' => 'Required',
        
        // Alert & Tips
        'safe_sending_tips' => 'Safe Sending Tips:',
        'tip_delay' => 'Use minimum 30 seconds delay to avoid blocking',
        'tip_batch' => 'Limit sending to maximum 50-100 messages per batch',
        'tip_rest' => 'Give adequate rest break between batches',
        'tip_multiple' => 'Use multiple devices for load balancing',
    ],
    
    'email' => [
        // Form Fields
        'category' => 'Business Category',
        'template' => 'Email Template',
        'name' => 'Broadcast Title',
        'schedule' => 'Send Schedule',
        
        // Placeholders
        'name_placeholder' => 'Example: October 2024 Monthly Newsletter',
        
        // Helper Texts
        'category_help' => 'Filter target recipients by business category',
        'template_help' => 'Email template to be sent to recipients',
        'name_help' => 'Broadcast campaign name for identification',
        'schedule_help' => 'Email broadcast sending schedule',
        
        // Badges & Labels
        'optional' => 'Optional',
        'required_field' => 'Required',
        
        // Alert & Tips
        'email_sending_tips' => 'Email Sending Tips:',
        'tip_test_template' => 'Make sure email template is tested before broadcast',
        'tip_optimal_time' => 'Choose optimal sending time for maximum engagement',
        'tip_use_category' => 'Use categories for more specific targeting',
        'tip_check_spam' => 'Review content to avoid spam folder',
    ],
    
    'upselling' => [
        // List/Index Page
        'page_title' => 'Upselling Campaign List',
        'create_campaign_button' => 'Create New Campaign',
        'refresh_button' => 'Refresh',
        
        // Table Headers
        'campaign_info' => 'Campaign Info',
        'schedule_frequency' => 'Schedule & Frequency',
        'target_category' => 'Target & Category',
        'method_template' => 'Method & Template',
        'status' => 'Status',
        'action' => 'Action',
        
        // Filter Options
        'all_status' => 'All Status',
        'status_active' => 'Active',
        'status_inactive' => 'Inactive',
        'status_scheduled' => 'Scheduled',
        'status_completed' => 'Completed',
        'all_frequency' => 'All Frequency',
        'frequency_once' => 'Once',
        'frequency_daily' => 'Daily',
        'frequency_monthly' => 'Monthly',
        'frequency_yearly' => 'Yearly',
        
        // DataTable Language
        'search_placeholder' => 'Search campaign...',
        'length_menu' => 'Show _MENU_ campaigns per page',
        'info' => 'Showing _START_ to _END_ of _TOTAL_ campaigns',
        'info_empty' => 'No campaigns',
        'info_filtered' => '(filtered from _MAX_ total campaigns)',
        'paginate_first' => 'First',
        'paginate_last' => 'Last',
        'paginate_next' => 'Next',
        'paginate_previous' => 'Previous',
        
        // DataTable Content
        'devices_count' => 'devices',
        'delay_label' => 'Delay',
        'once_send' => 'Send once',
        'date_prefix' => 'Date',
        'every_month' => 'every month',
        'every_year' => 'every year',
        'ongoing' => 'Ongoing',
        'all_categories' => 'All categories',
        'labels_count' => 'labels',
        'template_label' => 'Template',
        'ai_generated' => 'AI Generated',
        
        // Day Short Labels
        'day_monday_short' => 'Mon',
        'day_tuesday_short' => 'Tue',
        'day_wednesday_short' => 'Wed',
        'day_thursday_short' => 'Thu',
        'day_friday_short' => 'Fri',
        'day_saturday_short' => 'Sat',
        'day_sunday_short' => 'Sun',
        
        // Month Short Labels
        'month_jan' => 'Jan',
        'month_feb' => 'Feb',
        'month_mar' => 'Mar',
        'month_apr' => 'Apr',
        'month_may' => 'May',
        'month_jun' => 'Jun',
        'month_jul' => 'Jul',
        'month_aug' => 'Aug',
        'month_sep' => 'Sep',
        'month_oct' => 'Oct',
        'month_nov' => 'Nov',
        'month_dec' => 'Dec',
        
        // Action Button Tooltips
        'btn_view_detail' => 'View Detail',
        'btn_edit_campaign' => 'Edit Campaign',
        'btn_duplicate_campaign' => 'Duplicate Campaign',
        'btn_delete_campaign' => 'Delete Campaign',
        
        // Confirmation Messages
        'confirm_activate' => 'Are you sure you want to activate this campaign?',
        'confirm_deactivate' => 'Are you sure you want to deactivate this campaign?',
        'confirm_delete' => 'Are you sure you want to delete this campaign? This action cannot be undone.',
        
        // Success Messages
        'success_title' => 'Success',
        'success_delete' => 'Campaign successfully deleted',
        
        // Error Messages
        'error_title' => 'Error',
        'error_status_change' => 'An error occurred while changing campaign status',
        'error_delete' => 'An error occurred while deleting campaign',
        
        // Back Button
        'back_to_campaign' => 'Back to Upselling Campaign',
        
        // Card Headers (Form)
        'basic_info' => 'Campaign Basic Information',
        'schedule_config' => 'Sending Schedule Options & Message Configuration',
        
        // Form Fields
        'campaign_title' => 'Upselling Campaign Title',
        'delay' => 'Delay Between Sends',
        'devices' => 'Device',
        'device_option' => 'Device Usage Option',
        'contact_category' => 'Contact Category',
        'contact_labels' => 'Contact Labels',
        'schedule_frequency' => 'Send Frequency',
        'select_days' => 'Select Days',
        'date_in_month' => 'Date in Month',
        'specific_date' => 'Specific Date',
        'sending_time' => 'Sending Time',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'broadcast_method' => 'Method Used',
        'ai_prompt' => 'AI Prompt',
        'template_message' => 'Message Template',
        
        // Placeholders
        'name_placeholder' => 'Example: Year End Promo Campaign',
        'ai_prompt_placeholder' => 'Example: Create an upselling message for new product with friendly and attractive tone...',
        'select_category' => 'Select category...',
        'select_template' => 'Select template...',
        'select_device' => 'Select device...',
        'select_day' => 'Select day...',
        
        // Helper Texts
        'name_help' => 'Upselling campaign name for internal identification',
        'delay_help' => 'Time delay between message sends',
        'devices_help' => 'Select one or more devices to send messages',
        'device_option_help' => 'Device selection method for message sending',
        'category_help' => 'Filter contacts by specific category',
        'labels_help' => 'Select contact labels that will receive messages (multiple allowed)',
        'frequency_help' => 'Determine how often messages will be sent',
        'days_help' => 'Select days for sending',
        'date_help' => 'Select date in month for sending',
        'yearly_help' => 'Select month and date for yearly sending',
        'time_help' => 'Message sending time',
        'start_date_help' => 'Campaign start date',
        'end_date_help' => 'Leave empty if no time limit',
        'method_help' => 'Select campaign message creation method',
        'ai_prompt_help' => 'AI will use this prompt to create messages tailored to each contact',
        'template_help' => 'Select previously created message template',
        
        // Frequency Options
        'freq_once' => 'Send Once',
        'freq_daily' => 'Daily',
        'freq_monthly' => 'Monthly',
        'freq_yearly' => 'Yearly',
        
        // Days
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
        'last_day' => 'Last Day',
        
        // Months
        'january' => 'January',
        'february' => 'February',
        'march' => 'March',
        'april' => 'April',
        'may' => 'May',
        'june' => 'June',
        'july' => 'July',
        'august' => 'August',
        'september' => 'September',
        'october' => 'October',
        'november' => 'November',
        'december' => 'December',
        
        // Broadcast Methods
        'method_template_option' => 'Use Template',
        'method_ai_option' => 'Use AI Prompt',
        
        // Device Options
        'device_sequence' => 'Single Device (Sequential)',
        'device_spin' => 'AI Choose (Auto Select)',
        'device_random' => 'Random',
        
        // Units
        'seconds' => 'seconds',
        
        // Badges & Labels
        'optional' => 'Optional',
        'required_field' => 'Required',
        
        // Buttons
        'create_campaign' => 'Create Campaign',
        'update_campaign' => 'Update Campaign',
        
        // Alert & Tips
        'campaign_tips' => 'Upselling Campaign Tips:',
        'tip_ai_prompt' => 'Use AI prompt for more personal and dynamic messages',
        'tip_optimal_time' => 'Choose the right sending time for maximum engagement',
        'tip_use_labels' => 'Utilize labels for more specific targeting',
        'tip_frequency' => 'Use daily/monthly frequency for automatic follow-ups',
    ]
];