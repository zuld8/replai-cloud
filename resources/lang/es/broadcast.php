<?php

return [
    'wa' => [
        // Index/List Page
        'create_schedule_button' => 'Crear Horario de Difusión',
        'wa_group' => 'Grupo de WhatsApp',
        'page_title' => 'Lista de Difusión WhatsApp',
        
        // Card Headers
        'broadcast_info' => 'Información de Difusión',
        'target_template' => 'Objetivo y Plantilla',
        
        // Form Labels
        'name' => 'Título de Difusión',
        'schedule' => 'Horario de Envío',
        'template' => 'Plantilla de Mensaje',
        'devices' => 'Dispositivos',
        'device_option' => 'Opción de Uso de Dispositivo',
        'delay' => 'Retraso Entre Envíos',
        'stop_sending' => 'Detener Envío Después',
        'rest_sending' => 'Pausa de Descanso',
        'category' => 'Categoría de Negocio',
        'whatsapp_group' => 'Grupo de WhatsApp',
        'location_target' => 'Ubicación Objetivo',
        
        // Placeholders
        'name_placeholder' => 'Ejemplo: Promoción de Fin de Año 2024',
        'choose_group' => 'Elegir Grupo de WhatsApp',
        'choose_device' => 'Elegir Dispositivo WhatsApp',
        
        // Device Options
        'device_sequence' => 'Dispositivo Único (Secuencial)',
        'device_spin' => 'Elección de IA (Giratorio)',
        'device_random' => 'Aleatorio',
        
        // Units
        'seconds' => 'segundos',
        'numbers' => 'números',
        
        // Helper Texts
        'name_help' => 'Nombre de la campaña de difusión para identificación',
        'schedule_help' => 'Horario de envío de mensajes de difusión',
        'template_help' => 'Plantilla de mensaje que se enviará',
        'devices_help' => 'Seleccionar dispositivo WhatsApp para enviar difusión',
        'device_option_help' => 'Método de selección de dispositivo para envío',
        'delay_help' => 'Recomendación: 30-300 segundos. Cuanto menor, mayor riesgo de bloqueo',
        'stop_sending_help' => 'Detener envío después de cuántos números',
        'rest_sending_help' => 'Pausa antes de continuar enviando mensajes',
        'category_help' => 'Filtrar objetivo por categoría de negocio',
        'whatsapp_group_help' => 'Contactos objetivo de grupos específicos de WhatsApp',
        'province_help' => 'Filtrar objetivo por provincia',
        'city_help' => 'Filtrar objetivo por ciudad/condado',
        'district_help' => 'Filtrar objetivo por distrito',
        
        // Badges & Labels
        'optional' => 'Opcional',
        'required_field' => 'Campo obligatorio',
        
        // Alert & Tips
        'safe_sending_tips' => 'Consejos para Envío Seguro:',
        'tip_delay' => 'Usar retraso mínimo de 30 segundos para evitar bloqueos',
        'tip_batch' => 'Limitar envío máximo de 50-100 mensajes por lote',
        'tip_rest' => 'Proporcionar suficiente pausa de descanso entre lotes',
        'tip_multiple' => 'Usar múltiples dispositivos para equilibrio de carga',
    ],
    
    'email' => [
        // Form Fields
        'category' => 'Categoría de Negocio',
        'template' => 'Plantilla de Email',
        'name' => 'Título de Difusión',
        'schedule' => 'Horario de Envío',
        
        // Placeholders
        'name_placeholder' => 'Ejemplo: Boletín Mensual Octubre 2024',
        
        // Helper Texts
        'category_help' => 'Filtrar destinatarios objetivo por categoría de negocio',
        'template_help' => 'Plantilla de email que se enviará a los destinatarios',
        'name_help' => 'Nombre de la campaña de difusión para identificación',
        'schedule_help' => 'Horario de envío de difusión por email',
        
        // Badges & Labels
        'optional' => 'Opcional',
        'required_field' => 'Campo obligatorio',
        
        // Alert & Tips
        'email_sending_tips' => 'Consejos para Envío de Email:',
        'tip_test_template' => 'Asegurar que la plantilla de email esté probada antes de la difusión',
        'tip_optimal_time' => 'Elegir tiempo de envío óptimo para máximo engagement',
        'tip_use_category' => 'Usar categorías para targeting más específico',
        'tip_check_spam' => 'Revisar contenido para evitar ir a spam',
    ],
    
    'upselling' => [
        // List/Index Page
        'page_title' => 'Lista de Campañas de Upselling',
        'create_campaign_button' => 'Crear Nueva Campaña',
        'refresh_button' => 'Actualizar',
        
        // Table Headers
        'campaign_info' => 'Información de Campaña',
        'schedule_frequency' => 'Horario y Frecuencia',
        'target_category' => 'Objetivo y Categoría',
        'method_template' => 'Método y Plantilla',
        'status' => 'Estado',
        'action' => 'Acción',
        
        // Filter Options
        'all_status' => 'Todos los Estados',
        'status_active' => 'Activo',
        'status_inactive' => 'Inactivo',
        'status_scheduled' => 'Programado',
        'status_completed' => 'Completado',
        'all_frequency' => 'Todas las Frecuencias',
        'frequency_once' => 'Una vez',
        'frequency_daily' => 'Diario',
        'frequency_monthly' => 'Mensual',
        'frequency_yearly' => 'Anual',
        
        // DataTable Language
        'search_placeholder' => 'Buscar campaña...',
        'length_menu' => 'Mostrar _MENU_ campañas por página',
        'info' => 'Mostrando _START_ a _END_ de _TOTAL_ campañas',
        'info_empty' => 'No hay campañas',
        'info_filtered' => '(filtrado de _MAX_ campañas totales)',
        'paginate_first' => 'Primero',
        'paginate_last' => 'Último',
        'paginate_next' => 'Siguiente',
        'paginate_previous' => 'Anterior',
        
        // DataTable Content
        'devices_count' => 'dispositivos',
        'delay_label' => 'Retraso',
        'once_send' => 'Envío único',
        'date_prefix' => 'Fecha',
        'every_month' => 'cada mes',
        'every_year' => 'cada año',
        'ongoing' => 'En curso',
        'all_categories' => 'Todas las categorías',
        'labels_count' => 'etiquetas',
        'template_label' => 'Plantilla',
        'ai_generated' => 'Generado por IA',
        
        // Day Short Labels
        'day_monday_short' => 'Lun',
        'day_tuesday_short' => 'Mar',
        'day_wednesday_short' => 'Mié',
        'day_thursday_short' => 'Jue',
        'day_friday_short' => 'Vie',
        'day_saturday_short' => 'Sáb',
        'day_sunday_short' => 'Dom',
        
        // Month Short Labels
        'month_jan' => 'Ene',
        'month_feb' => 'Feb',
        'month_mar' => 'Mar',
        'month_apr' => 'Abr',
        'month_may' => 'May',
        'month_jun' => 'Jun',
        'month_jul' => 'Jul',
        'month_aug' => 'Ago',
        'month_sep' => 'Sep',
        'month_oct' => 'Oct',
        'month_nov' => 'Nov',
        'month_dec' => 'Dec',
        
        // Action Button Tooltips
        'btn_view_detail' => 'Ver Detalle',
        'btn_edit_campaign' => 'Editar Campaña',
        'btn_duplicate_campaign' => 'Duplicar Campaña',
        'btn_delete_campaign' => 'Eliminar Campaña',
        
        // Confirmation Messages
        'confirm_activate' => '¿Está seguro de que desea activar esta campaña?',
        'confirm_deactivate' => '¿Está seguro de que desea desactivar esta campaña?',
        'confirm_delete' => '¿Está seguro de que desea eliminar esta campaña? Esta acción no se puede deshacer.',
        
        // Success Messages
        'success_title' => 'Éxito',
        'success_delete' => 'Campaña eliminada exitosamente',
        
        // Error Messages
        'error_title' => 'Error',
        'error_status_change' => 'Ocurrió un error al cambiar el estado de la campaña',
        'error_delete' => 'Ocurrió un error al eliminar la campaña',
        
        // Back Button
        'back_to_campaign' => 'Volver a Campañas de Upselling',
        
        // Card Headers (Form)
        'basic_info' => 'Información Básica de Campaña',
        'schedule_config' => 'Opciones de Programación de Envío y Configuración de Mensajes',
        
        // Form Fields
        'campaign_title' => 'Título de Campaña de Upselling',
        'delay' => 'Retraso Entre Envíos',
        'devices' => 'Dispositivos',
        'device_option' => 'Opción de Uso de Dispositivo',
        'contact_category' => 'Categoría de Contacto',
        'contact_labels' => 'Etiquetas de Contacto',
        'schedule_frequency' => 'Frecuencia de Envío',
        'select_days' => 'Seleccionar Días',
        'date_in_month' => 'Fecha en el Mes',
        'specific_date' => 'Fecha Específica',
        'sending_time' => 'Hora de Envío',
        'start_date' => 'Fecha de Inicio',
        'end_date' => 'Fecha de Finalización',
        'broadcast_method' => 'Método Utilizado',
        'ai_prompt' => 'Prompt de IA',
        'template_message' => 'Plantilla de Mensaje',
        
        // Placeholders
        'name_placeholder' => 'Ejemplo: Campaña Promoción de Fin de Año',
        'ai_prompt_placeholder' => 'Ejemplo: Crear mensaje de upselling para nuevo producto con tono amigable y atractivo...',
        'select_category' => 'Seleccionar categoría...',
        'select_template' => 'Seleccionar plantilla...',
        'select_device' => 'Seleccionar dispositivo...',
        'select_day' => 'Seleccionar día...',
        
        // Helper Texts
        'name_help' => 'Nombre de la campaña de upselling para identificación interna',
        'delay_help' => 'Tiempo de espera entre envíos de mensajes',
        'devices_help' => 'Seleccionar uno o más dispositivos para enviar mensajes',
        'device_option_help' => 'Método de selección de dispositivo para envío de mensajes',
        'category_help' => 'Filtrar contactos por categoría específica',
        'labels_help' => 'Seleccionar etiquetas de contacto que recibirán el mensaje (puede ser múltiple)',
        'frequency_help' => 'Determinar con qué frecuencia se enviará el mensaje',
        'days_help' => 'Seleccionar días para envío',
        'date_help' => 'Seleccionar fecha en el mes para envío',
        'yearly_help' => 'Seleccionar mes y fecha para envío anual',
        'time_help' => 'Hora de envío del mensaje',
        'start_date_help' => 'Fecha de inicio de la campaña',
        'end_date_help' => 'Dejar vacío si no hay límite de tiempo',
        'method_help' => 'Seleccionar método para crear mensaje de campaña',
        'ai_prompt_help' => 'La IA usará este prompt para crear mensajes personalizados para cada contacto',
        'template_help' => 'Seleccionar plantilla de mensaje creada previamente',
        
        // Frequency Options
        'freq_once' => 'Enviar Una Vez',
        'freq_daily' => 'Diario',
        'freq_monthly' => 'Mensual',
        'freq_yearly' => 'Anual',
        
        // Days
        'monday' => 'Lunes',
        'tuesday' => 'Martes',
        'wednesday' => 'Miércoles',
        'thursday' => 'Jueves',
        'friday' => 'Viernes',
        'saturday' => 'Sábado',
        'sunday' => 'Domingo',
        'last_day' => 'Último Día',
        
        // Months
        'january' => 'Enero',
        'february' => 'Febrero',
        'march' => 'Marzo',
        'april' => 'Abril',
        'may' => 'Mayo',
        'june' => 'Junio',
        'july' => 'Julio',
        'august' => 'Agosto',
        'september' => 'Septiembre',
        'october' => 'Octubre',
        'november' => 'Noviembre',
        'december' => 'Diciembre',
        
        // Broadcast Methods
        'method_template_option' => 'Usar Plantilla',
        'method_ai_option' => 'Usar Prompt de IA',
        
        // Device Options
        'device_sequence' => 'Dispositivo Único (Secuencial)',
        'device_spin' => 'Elección de IA (Selección Automática)',
        'device_random' => 'Aleatorio',
        
        // Units
        'seconds' => 'segundos',
        
        // Badges & Labels
        'optional' => 'Opcional',
        'required_field' => 'Campo obligatorio',
        
        // Buttons
        'create_campaign' => 'Crear Campaña',
        'update_campaign' => 'Actualizar Campaña',
        
        // Alert & Tips
        'campaign_tips' => 'Consejos para Campaña de Upselling:',
        'tip_ai_prompt' => 'Usar prompt de IA para mensajes más personales y dinámicos',
        'tip_optimal_time' => 'Elegir tiempo de envío adecuado para máximo engagement',
        'tip_use_labels' => 'Utilizar etiquetas para targeting más específico',
        'tip_frequency' => 'Usar frecuencia diaria/mensual para seguimiento automático',
    ]
];