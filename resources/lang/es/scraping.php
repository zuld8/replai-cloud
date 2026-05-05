<?php

return [
    'maps' => [
        // Page Titles & Navigation
        'page_title' => 'Scraping de Google Maps',
        'create_title' => 'Crear Nuevo Scraping',
        'update_title' => 'Editar Scraping',
        'back_to' => 'Volver a Scraping',
        
        // Card Headers
        'scraping_info' => 'Información de Scraping',
        'location_target' => 'Ubicación Objetivo del Scraping',
        
        // Form Labels
        'category' => 'Categoría de Negocio',
        'name' => 'Nombre del Scraping',
        'schedule' => 'Horario del Scraping',
        'province' => 'Provincia',
        'city' => 'Ciudad/Municipio',
        'district' => 'Distrito',
        
        // Placeholders
        'name_placeholder' => 'Ejemplo: Scraping Restaurantes Madrid',
        'choose_category' => 'Seleccionar categoría de negocio',
        
        // Helper Texts
        'category_help' => 'Categoría de negocio que se extraerá de Google Maps',
        'name_help' => 'Nombre para identificar este proceso de scraping',
        'schedule_help' => 'Horario en que se ejecutará el proceso de scraping',
        'province_help' => 'Filtrar scraping por provincia específica',
        'city_help' => 'Filtrar scraping por ciudad/municipio',
        'district_help' => 'Filtrar scraping por distrito específico',
        
        // Badges & Labels
        'optional' => 'Opcional',
        'required' => 'Obligatorio',
        'required_field' => 'Campo obligatorio',
        
        // Tips & Alerts
        'tips_title' => 'Consejos para Scraping de Google Maps:',
        'tip_location' => 'Cuanto más específica sea la ubicación seleccionada, más enfocados serán los resultados',
        'tip_category' => 'Seleccione una categoría que coincida con su negocio objetivo',
        'tip_schedule' => 'Programe el scraping en horarios de bajo tráfico para resultados óptimos',
        'tip_auto' => 'El proceso de scraping se ejecutará automáticamente según el horario establecido',
        
        // Buttons
        'btn_create' => 'Iniciar Scraping',
        'btn_update' => 'Guardar Cambios',
        
        // Status
        'status_pending' => 'Pendiente',
        'status_processing' => 'Procesando',
        'status_completed' => 'Completado',
        'status_failed' => 'Fallido',
        
        // Messages
        'success_create' => 'Scraping programado exitosamente',
        'success_update' => 'Scraping actualizado exitosamente',
        'success_delete' => 'Scraping eliminado exitosamente',
        'error_create' => 'Error al crear el scraping',
        'error_update' => 'Error al actualizar el scraping',
        'error_delete' => 'Error al eliminar el scraping',
        
        // Meta
        'title' => 'Scraping de Google Maps',
        'description' => 'Extraer datos de negocios de Google Maps por categoría y ubicación',
    ],
    
    'contact' => [
        // Page Titles & Navigation
        'page_title' => 'Scraping de Contactos WhatsApp',
        'create_title' => 'Agregar Scraping de Contactos',
        'update_title' => 'Editar Scraping de Contactos',
        'back_to' => 'Volver a Scraping',
        'list_title' => 'Lista de Datos de Scraping de Contactos',
        
        // Card Headers
        'scraping_info' => 'Configuración de Scraping',
        
        // Form Labels
        'devices' => 'Dispositivos',
        'category' => 'Categoría',
        'name' => 'Nombre del Scraping',
        'schedule' => 'Horario del Scraping',
        
        // Placeholders
        'name_placeholder' => 'Ejemplo: Scraping Contactos Grupo Marketing',
        'choose_devices' => 'Seleccionar dispositivos...',
        'choose_category' => 'Seleccionar categoría',
        
        // Helper Texts
        'devices_help' => 'Seleccionar dispositivos WhatsApp que se usarán para el scraping',
        'category_help' => 'Categoría para clasificar los contactos extraídos',
        'name_help' => 'Nombre para identificar este proceso de scraping',
        'schedule_help' => 'Horario en que se ejecutará el scraping de contactos',
        
        // Labels
        'required' => 'Obligatorio',
        'required_field' => 'Campo obligatorio',
        
        // Tips & Alerts
        'tips_title' => 'Consejos para Scraping de Contactos WhatsApp:',
        'tip_device' => 'Use múltiples dispositivos para scraping más rápido y eficiente',
        'tip_category' => 'Seleccione la categoría adecuada para facilitar la agrupación de contactos',
        'tip_schedule' => 'Programe el scraping en horarios apropiados para rendimiento óptimo',
        'tip_auto' => 'Los contactos extraídos se guardarán automáticamente en la base de datos',
        
        // Buttons
        'btn_create' => 'Iniciar Scraping',
        'btn_update' => 'Guardar Cambios',
        
        // Status
        'status_pending' => 'Pendiente',
        'status_processing' => 'Procesando',
        'status_completed' => 'Completado',
        'status_failed' => 'Fallido',
        
        // Messages
        'success_create' => 'Scraping de contactos programado exitosamente',
        'success_update' => 'Scraping de contactos actualizado exitosamente',
        'success_delete' => 'Scraping de contactos eliminado exitosamente',
        'error_create' => 'Error al crear el scraping de contactos',
        'error_update' => 'Error al actualizar el scraping de contactos',
        'error_delete' => 'Error al eliminar el scraping de contactos',
        
        // Meta
        'title' => 'Scraping de Contactos',
        'description' => 'Extraer contactos de WhatsApp por dispositivo y categoría',
    ],
    
    'group' => [
        // Page Titles & Navigation
        'page_title' => 'Scraping de Grupos',
        'create_title' => 'Agregar Scraping de Grupos',
        'update_title' => 'Editar Scraping de Grupos',
        'back_to' => 'Volver a Scraping',
        'list_title' => 'Lista de Datos de Scraping de Grupos',
        'whatsapp_group' => 'Grupo de WhatsApp',
        'group_list' => 'Lista de Grupos',

        // table columns
        'device' => 'Dispositivo',
        'group_id' => 'ID del Grupo',
        'contacts' => 'Contactos',
        'syncron' => 'Sincronización',
        
        // Card Headers
        'scraping_info' => 'Configuración de Scraping',
        
        // Form Labels
        'devices' => 'Dispositivos',
        'name' => 'Nombre del Scraping',
        'schedule' => 'Horario del Scraping',
        
        // Placeholders
        'name_placeholder' => 'Ejemplo: Scraping Grupo Equipo Ventas',
        'choose_devices' => 'Seleccionar dispositivos...',
        
        // Helper Texts
        'devices_help' => 'Seleccionar dispositivos WhatsApp de los cuales extraer grupos',
        'name_help' => 'Nombre para identificar este proceso de scraping de grupos',
        'schedule_help' => 'Horario en que se ejecutará el scraping de grupos',
        
        // Labels
        'required' => 'Obligatorio',
        'required_field' => 'Campo obligatorio',
        
        // Tips & Alerts
        'tips_title' => 'Consejos para Scraping de Grupos WhatsApp:',
        'tip_device' => 'El proceso extraerá la lista de todos los grupos del dispositivo seleccionado',
        'tip_category' => 'Use múltiples dispositivos para obtener más grupos',
        'tip_schedule' => 'Los datos de grupos se guardarán para usar en campañas de difusión',
        'tip_auto' => 'Asegúrese de que el dispositivo esté en línea durante el proceso de scraping',
        
        // Buttons
        'btn_create' => 'Iniciar Scraping',
        'btn_update' => 'Guardar Cambios',
        
        // Status
        'status_pending' => 'Pendiente',
        'status_processing' => 'Procesando',
        'status_completed' => 'Completado',
        'status_failed' => 'Fallido',
        
        // Messages
        'success_create' => 'Scraping de grupos programado exitosamente',
        'success_update' => 'Scraping de grupos actualizado exitosamente',
        'success_delete' => 'Scraping de grupos eliminado exitosamente',
        'error_create' => 'Error al crear el scraping de grupos',
        'error_update' => 'Error al actualizar el scraping de grupos',
        'error_delete' => 'Error al eliminar el scraping de grupos',
        
        // Meta
        'title' => 'Scraping de Grupos',
        'description' => 'Extraer datos de grupos y comunidades de WhatsApp',
    ],
];