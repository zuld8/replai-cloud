<?php

return [
    // Existing translations
    'search'                => 'Search Contacts....',
    'import'                => 'Import Contact Data',
    'kanban_view'           => 'Kanban Mode',
    'list_view'             => 'List Mode',
    'back_to'               => 'Back to Contact List',

    // Kanban View
    'contact_list'          => 'Contact List',
    'total'                 => 'Total',
    'contact'               => 'contacts',
    'search_contact'        => 'Search contacts...',
    'all_categories'        => 'All Categories',
    'contact_per_column'    => 'contacts per column',
    'loading_data'          => 'Loading data...',
    'load_more'             => 'Load More',
    'remaining'             => 'remaining',
    'loading_more'          => 'Loading more...',

    // Card Actions
    'view_detail'           => 'View Detail',
    'edit'                  => 'Edit',
    'delete'                => 'Delete',
    'not_handled'           => 'Not Handled',
    'unassigned'            => 'Unassigned',
    'virtual'               => 'Virtual',

    // Detail Modal
    'contact_detail'        => 'Contact Detail',
    'basic_info'            => 'Basic Information',
    'name'                  => 'Name',
    'email'                 => 'Email',
    'phone'                 => 'Phone',
    'category'              => 'Category',
    'channel_info'          => 'Channel Information',
    'source_channel'        => 'Source Channel',
    'status_assignment'     => 'Status & Assignment',
    'status'                => 'Status',
    'label'                 => 'Label',
    'handled_by'            => 'Handled By',
    'resolved_by'           => 'Resolved By',
    'timestamps'            => 'Timestamps',
    'created_at'            => 'Created At',
    'updated_at'            => 'Updated At',
    'close'                 => 'Close',

    // Pipeline Management
    'crm_pipeline'          => 'CRM Pipeline',
    'total_contacts_stages' => 'contacts in',
    'stages'                => 'stages',
    'pipeline'              => 'Pipeline',
    'search_contact_placeholder' => 'Name, email, or phone...',
    'show'                  => 'Show',
    'add_contact'           => 'Add Contact',
    'loading_pipeline'      => 'Loading pipeline data...',
    'no_pipeline_yet'       => 'No pipeline yet',
    'no_pipeline_desc'      => 'Create your first pipeline to start managing leads',
    'create_pipeline'       => 'Create Pipeline',
    'add_pipeline'          => 'Add Pipeline',
    'edit_pipeline'         => 'Edit Pipeline',
    'add_label'             => 'Add Label',
    'arrange_labels'        => 'Arrange Labels',
    'delete_pipeline'       => 'Delete Pipeline',

    // Pipeline Modal
    'create_new_pipeline'   => 'Create New Pipeline',
    'pipeline_name'         => 'Pipeline Name',
    'pipeline_name_placeholder' => 'Example: Sales Pipeline, Support Pipeline',
    'template'              => 'Template',
    'template_default'      => 'Default (Lead → Negotiation → Closing)',
    'template_sales'        => 'Sales (Lead → Qualified → Proposal → Won/Lost)',
    'template_service'      => 'Service (Ticket → Progress → Resolved)',
    'template_custom'       => 'Custom (Empty)',
    'template_info'         => 'Template will automatically create default labels',
    'pipeline_color'        => 'Pipeline Color',
    'save_pipeline'         => 'Save Pipeline',

    // Label/Stage Management
    'add_stage'             => 'Add New Stage',
    'edit_stage'            => 'Edit Stage',
    'stage_name'            => 'Stage Name',
    'stage_name_placeholder' => 'Example: Follow Up, Negotiation',
    'position'              => 'Position',
    'position_placeholder'  => 'Stage order (1, 2, 3...)',
    'position_info'         => 'Stage will be inserted at this position. Other stages will shift automatically.',
    'color'                 => 'Color',
    'virtual_stage'         => 'Virtual stage for contacts without label',
    'closing_stage'         => 'Closing stage',
    'closing_stage_info'    => 'Closing stage cannot be deleted, only name and position can be edited.',
    'add_first_stage'       => 'Add First Stage',

    // Quick Add Contact
    'quick_add_contact'     => 'Quick Add Contact',
    'contact_name_quick'    => 'Contact name',
    'phone_number'          => 'Phone Number',

    // Reorder Modal
    'reorder_labels'        => 'Arrange Label Order',
    'how_to_use'            => 'How to use:',
    'drag_drop_info'        => 'Drag & drop labels to change order',
    'locked_stages_info'    => 'Initial Contact and Closing Stage cannot be moved',
    'save_order'            => 'Save Order',

    // Start Chat Modal
    'start_conversation'    => 'Start Conversation',
    'select_device'         => 'Select Device / Channel',
    'select_device_placeholder' => 'Select Device',
    'device_info'           => 'Device will be used to send messages',
    'start_chat'            => 'Start Chat',
    'processing'            => 'Processing...',

    // Button States
    'cancel'                => 'Cancel',
    'save'                  => 'Save',
    'saving'                => 'Saving...',
    'loading'               => 'Loading...',

    // Status
    'status_open'           => 'Open',
    'status_resolved'       => 'Resolved',
    'status_pending'        => 'Pending',
    'status_block'          => 'Blocked',

    // Notifications
    'contact_loaded'        => 'contacts successfully loaded',
    'label_updated'         => 'Label successfully updated',
    'contact_deleted'       => 'Contact successfully deleted',
    'failed_load_data'      => 'Failed to load data',
    'error_load_data'       => 'An error occurred while loading data',
    'failed_update_label'   => 'Failed to update label',
    'failed_delete_contact' => 'Failed to delete contact',

    // Confirm Dialog
    'confirm_delete_title'  => 'Delete Contact?',
    'confirm_delete_text'   => 'Deleted data cannot be recovered!',
    'yes_delete'            => 'Yes, Delete!',

    // Form Create & Edit
    'contact_information'   => 'Contact Information',
    'contact_name'          => 'Contact Name',
    'contact_name_placeholder' => 'Example: John Doe',
    'contact_name_help'     => 'Enter full contact name',
    'full_name_help'        => 'Full contact name',
    'select_category'       => 'Select Category',
    'category_help'         => 'Select category to group contacts',
    'whatsapp_number'       => 'WhatsApp Number',
    'phone_placeholder'     => '+62 xxx xxx xxx',
    'whatsapp_help'         => 'Contact WhatsApp number (format: +62)',
    'email_optional'        => 'Email (Optional)',
    'email_placeholder'     => 'example@email.com',
    'email_help'            => 'Contact email address (optional)',
    'select_label'          => 'Select Label',
    'label_help'            => 'Select label to group contacts',
    'regional_info'         => 'Regional Information (Optional)',
    'province'              => 'Province',
    'select_province'       => 'Select Province',
    'province_help'         => 'Select the province where the contact is located',
    'city'                  => 'City/Regency',
    'select_city'           => 'Select City/Regency',
    'city_help'             => 'Select city or regency (select province first)',
    'district'              => 'District',
    'select_district'       => 'Select District',
    'district_help'         => 'Select district (select city/regency first)',
    'required_fields'       => 'Fields marked with',
    'must_be_filled'        => 'must be filled',
    'cancel_button'         => 'Cancel',
    'save_contact'          => 'Save Contact',
    'active'                => 'Active',
    'inactive'              => 'Inactive',
    'status_help'           => 'Active/inactive contact status',

    // index table
    'handle_by'             => 'Handled By',
    'source'                => 'Source',
];