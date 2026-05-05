<?php

return [
    'maps' => [
        // Page Titles & Navigation
        'page_title' => 'Google Maps Scraping',
        'create_title' => 'Create New Scraping',
        'update_title' => 'Edit Scraping',
        'back_to' => 'Back to Scraping',

        // Card Headers
        'scraping_info' => 'Scraping Information',
        'location_target' => 'Scraping Location Target',

        // Form Labels
        'category' => 'Business Category',
        'name' => 'Scraping Name',
        'schedule' => 'Scraping Schedule',
        'province' => 'Province',
        'city' => 'City / Regency',
        'district' => 'District',

        // Placeholders
        'name_placeholder' => 'Example: Jakarta Restaurant Scraping',
        'choose_category' => 'Select a business category',

        // Helper Texts
        'category_help' => 'Business category to be scraped from Google Maps',
        'name_help' => 'Name used to identify this scraping process',
        'schedule_help' => 'Schedule for when the scraping process will run',
        'province_help' => 'Filter scraping by a specific province',
        'city_help' => 'Filter scraping by a specific city or regency',
        'district_help' => 'Filter scraping by a specific district',

        // Badges & Labels
        'optional' => 'Optional',
        'required' => 'Required',
        'required_field' => 'This field is required',

        // Tips & Alerts
        'tips_title' => 'Google Maps Scraping Tips:',
        'tip_location' => 'The more specific the location, the more focused the scraping result will be',
        'tip_category' => 'Choose a category that matches your target business',
        'tip_schedule' => 'Schedule scraping during low-traffic hours for optimal performance',
        'tip_auto' => 'The scraping process will run automatically based on the defined schedule',

        // Buttons
        'btn_create' => 'Start Scraping',
        'btn_update' => 'Save Changes',

        // Status
        'status_pending' => 'Pending',
        'status_processing' => 'Processing',
        'status_completed' => 'Completed',
        'status_failed' => 'Failed',

        // Messages
        'success_create' => 'Scraping has been successfully scheduled',
        'success_update' => 'Scraping has been successfully updated',
        'success_delete' => 'Scraping has been successfully deleted',
        'error_create' => 'Failed to create scraping',
        'error_update' => 'Failed to update scraping',
        'error_delete' => 'Failed to delete scraping',

        // Meta
        'title' => 'Google Maps Scraping',
        'description' => 'Extract business data from Google Maps based on category and location',
    ],

    'contact' => [
        // Page Titles & Navigation
        'page_title' => 'WhatsApp Contact Scraping',
        'create_title' => 'Add Contact Scraping',
        'update_title' => 'Edit Contact Scraping',
        'back_to' => 'Back to Scraping',
        'list_title' => 'Contact Scraping Data List',


        // Card Headers
        'scraping_info' => 'Scraping Configuration',

        // Form Labels
        'devices' => 'Device',
        'category' => 'Category',
        'name' => 'Scraping Name',
        'schedule' => 'Scraping Schedule',

        // Placeholders
        'name_placeholder' => 'Example: Marketing Group Contact Scraping',
        'choose_devices' => 'Select device...',
        'choose_category' => 'Select category',

        // Helper Texts
        'devices_help' => 'Select the WhatsApp device to be used for scraping',
        'category_help' => 'Category for classifying scraped contacts',
        'name_help' => 'Name used to identify this contact scraping process',
        'schedule_help' => 'Schedule for when the contact scraping process will run',

        // Labels
        'required' => 'Required',
        'required_field' => 'This field is required',

        // Tips & Alerts
        'tips_title' => 'WhatsApp Contact Scraping Tips:',
        'tip_device' => 'Use multiple devices to scrape faster and more efficiently',
        'tip_category' => 'Choose an appropriate category for easier contact grouping',
        'tip_schedule' => 'Schedule scraping at suitable times for best performance',
        'tip_auto' => 'Successfully scraped contacts will be automatically saved to the database',

        // Buttons
        'btn_create' => 'Start Scraping',
        'btn_update' => 'Save Changes',

        // Status
        'status_pending' => 'Pending',
        'status_processing' => 'Processing',
        'status_completed' => 'Completed',
        'status_failed' => 'Failed',

        // Messages
        'success_create' => 'Contact scraping has been successfully scheduled',
        'success_update' => 'Contact scraping has been successfully updated',
        'success_delete' => 'Contact scraping has been successfully deleted',
        'error_create' => 'Failed to create contact scraping',
        'error_update' => 'Failed to update contact scraping',
        'error_delete' => 'Failed to delete contact scraping',

        // Meta
        'title' => 'Contact Scraping',
        'description' => 'Extract contacts from WhatsApp based on device and category',
    ],

    'group' => [
        // Page Titles & Navigation
        'page_title' => 'Group Scraping',
        'create_title' => 'Add Group Scraping',
        'update_title' => 'Edit Group Scraping',
        'back_to' => 'Back to Scraping',
        'list_title' => 'Group Scraping Data List',
        'whatsapp_group' => 'WhatsApp Group',
        'group_list' => 'Group List',

        // table columns
        'device' => 'Device',
        'group_id' => 'Group ID',
        'contacts' => 'Contacts',
        'syncron' => 'Syncron',

        // Card Headers
        'scraping_info' => 'Scraping Configuration',

        // Form Labels
        'devices' => 'Device',
        'name' => 'Scraping Name',
        'schedule' => 'Scraping Schedule',

        // Placeholders
        'name_placeholder' => 'Example: Sales Team Group Scraping',
        'choose_devices' => 'Select device...',

        // Helper Texts
        'devices_help' => 'Select the WhatsApp device whose groups will be scraped',
        'name_help' => 'Name used to identify this group scraping process',
        'schedule_help' => 'Schedule for when the group scraping process will run',

        // Labels
        'required' => 'Required',
        'required_field' => 'This field is required',

        // Tips & Alerts
        'tips_title' => 'WhatsApp Group Scraping Tips:',
        'tip_device' => 'This process will retrieve the list of all groups from the selected device',
        'tip_category' => 'Use multiple devices to collect more group data',
        'tip_schedule' => 'Group data will be stored for use in broadcast campaigns',
        'tip_auto' => 'Make sure the device is online during the scraping process',

        // Buttons
        'btn_create' => 'Start Scraping',
        'btn_update' => 'Save Changes',

        // Status
        'status_pending' => 'Pending',
        'status_processing' => 'Processing',
        'status_completed' => 'Completed',
        'status_failed' => 'Failed',

        // Messages
        'success_create' => 'Group scraping has been successfully scheduled',
        'success_update' => 'Group scraping has been successfully updated',
        'success_delete' => 'Group scraping has been successfully deleted',
        'error_create' => 'Failed to create group scraping',
        'error_update' => 'Failed to update group scraping',
        'error_delete' => 'Failed to delete group scraping',

        // Meta
        'title' => 'Group Scraping',
        'description' => 'Extract data from WhatsApp groups and communities',
    ],
];
