<?php

return [
    // ===== WHATSAPP DEVICE =====
    'whatsapp' => [
        // Section Titles
        'device_info'                   => 'Información del Dispositivo',
        'ai_config'                     => 'Configuración del Agente IA',
        'schedule_limits'               => 'Horarios y Límites',
        
        // Device Information
        'device_name'                   => 'Nombre del Dispositivo',
        'device_name_placeholder'       => 'Ejemplo: Dispositivo Admin',
        'device_name_hint'              => 'Nombre para identificar este dispositivo WhatsApp',
        'wa_number'                     => 'Número WhatsApp',
        'wa_number_placeholder'         => '+34 xxx xxx xxx',
        'wa_number_hint'                => 'Número de WhatsApp que se conectará al sistema (formato: +34)',
        'team_member'                   => 'Miembro del Equipo',
        'team_member_hint'              => 'Selecciona el miembro del equipo que gestionará este dispositivo',
        'team_member_placeholder'       => 'Seleccionar miembro del equipo...',
        
        // Device Settings
        'device_notification'           => 'Notificaciones del Dispositivo',
        'device_notification_hint'      => 'Desactivar si no deseas recibir notificaciones en tu dispositivo',
        'save_chat_history'             => 'Guardar Historial de Mensajes',
        'save_chat_history_hint'        => 'El historial se guardará automáticamente sin reinicio diario',
        'auto_read_before_reply'        => 'Leer chat antes de que el ChatBot responda',
        'auto_read_before_reply_hint'   => 'Activar si deseas leer el chat antes de que la IA responda automáticamente. Las notificaciones al móvil se desactivarán.',
        'webhook_url'                   => 'URL WebHook (Opcional)',
        'webhook_url_placeholder'       => 'https://ejemplo.com/webhook',
        'webhook_url_hint'              => 'URL webhook para recibir notificaciones de mensajes entrantes (opcional)',
        
        // AI Configuration
        'chatbot_method'                => 'Método del Chatbot',
        'chatbot_method_hint'           => 'Selecciona el método de respuesta automática a utilizar',
        'method_all'                    => 'Todos (Manual + IA)',
        'method_chatbot'                => 'Chatbot (Manual)',
        'method_ai'                     => 'IA (Fine Tunnel)',
        'ai_training'                   => 'Entrenamiento IA',
        'ai_training_hint'              => 'Selecciona los datos de entrenamiento IA para este dispositivo',
        'ai_training_full_hint'         => 'Selecciona el conjunto de datos de entrenamiento IA para mejorar la precisión de las respuestas',
        'ai_training_placeholder'       => 'Seleccionar Entrenamiento IA...',
        'select_ai_training'            => 'Seleccionar Entrenamiento IA',
        'choose_ai_training'            => 'Elegir Entrenamiento IA',
        'auto_reply_option'             => 'Chatbot Activo en',
        'auto_reply_option_hint'        => 'Determina dónde el chatbot estará activo respondiendo mensajes',
        'reply_all'                     => 'Todos (Personal y Grupo)',
        'reply_personal'                => 'Personal',
        'reply_group'                   => 'Grupo',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'Chatbot Inactivo en Días Específicos',
        'inactive_certain_day_hint'     => 'Activar si deseas que el chatbot esté inactivo en días específicos',
        'select_days'                   => 'Seleccionar Días',
        'select_days_placeholder'       => 'Seleccionar días...',
        'day_monday'                    => 'Lunes',
        'day_tuesday'                   => 'Martes',
        'day_wednesday'                 => 'Miércoles',
        'day_thursday'                  => 'Jueves',
        'day_friday'                    => 'Viernes',
        'day_saturday'                  => 'Sábado',
        'day_sunday'                    => 'Domingo',
        
        'inactive_certain_time'         => 'Chatbot Inactivo en Horas Específicas',
        'inactive_certain_time_hint'    => 'Activar si deseas que el chatbot esté inactivo en horas específicas',
        'start_time'                    => 'Hora de Inicio de Inactividad',
        'start_time_hint'               => 'Hora de inicio de inactividad del chatbot',
        'end_time'                      => 'Hora de Fin de Inactividad',
        'end_time_hint'                 => 'Hora de fin de inactividad del chatbot',
        
        'daily_broadcast_limit'         => 'Límite Diario de Difusión',
        'daily_broadcast_limit_hint'    => 'Activar si el chatbot tiene límite de envío de mensajes por día',
        'enter_daily_limit'             => 'Introducir Límite Diario',
        'daily_limit_placeholder'       => 'Ejemplo: 100',
        'daily_limit_suffix'            => 'mensajes/día',
        'daily_limit_hint'              => 'Número máximo de mensajes que se pueden enviar por día',
        
        // Actions
        'save_device'                   => 'Guardar Dispositivo',
        'update_device'                 => 'Actualizar Dispositivo',
        'cancel'                        => 'Cancelar',
        'required_fields'               => 'Los campos marcados con * son obligatorios',
        
        // Messages
        'device_created'                => 'Dispositivo WhatsApp agregado exitosamente',
        'device_updated'                => 'Dispositivo WhatsApp actualizado exitosamente',
        'device_deleted'                => 'Dispositivo WhatsApp eliminado exitosamente',
        
        // List/Index Page
        'add_connection'                => 'Agregar Conexión WhatsApp',
        'total_device'                  => 'Total de Dispositivos',
        'not_connected'                 => 'No Conectado',
        'device_connected'              => 'Dispositivo Conectado',
        'connection_list'               => 'Lista de Conexiones WhatsApp',
        'broadcast_sent_today'          => 'Difusiones Enviadas Hoy',
        'daily_broadcast_limit_label'   => 'Límite de Difusión Diaria',
        'device_name_label'             => 'Nombre del Dispositivo',
        'phone_number'                  => 'Número de Teléfono',
        
        // Actions
        'scan_qr'                       => 'Escanear QR',
        'copy_id'                       => 'Copiar ID',
        'settings'                      => 'Configuraciones',
        'edit_device'                   => 'Editar Dispositivo',
        'delete_device'                 => 'Eliminar Dispositivo',
        'copied_device_id'              => 'ID del dispositivo copiado exitosamente',
        'search_device'                 => 'Buscar dispositivo...',
        
        // Status
        'status_active'                 => 'Activo',
        'status_inactive'               => 'Inactivo',
    ],
    // ===== TELEGRAM =====
    'telegram' => [
        // Section Titles
        'device_info'                   => 'Información del Dispositivo Telegram',
        'ai_config'                     => 'Configuración del Agente IA',
        'schedule_limits'               => 'Horarios y Límites',
        'integrated_telegram_list'      => 'Lista de Telegram Integrados',
        'add_telegram'                  => 'Agregar Telegram',
        'edit_telegram'                 => 'Editar Telegram',
        
        // Device Information
        'device_name'                   => 'Nombre del Dispositivo',
        'device_name_placeholder'       => 'Ejemplo: Bot Atención al Cliente',
        'device_name_hint'              => 'Nombre para identificar este bot de Telegram',
        'bot_token'                     => 'Token del Bot',
        'bot_token_placeholder'         => 'Introducir token del bot de @BotFather',
        'bot_token_hint'                => 'Token del bot obtenido de @BotFather en Telegram',
        'team_member'                   => 'Miembro del Equipo',
        'team_member_hint'              => 'Selecciona el miembro del equipo que gestionará este bot',
        'team_member_placeholder'       => 'Seleccionar miembro del equipo...',
        
        // AI Configuration
        'auto_reply_method'             => 'Método de Respuesta Automática',
        'auto_reply_method_hint'        => 'Selecciona el método de respuesta automática a utilizar',
        'method_all'                    => 'Todos (Manual + IA)',
        'method_chatbot'                => 'ChatBot (Manual)',
        'method_ai'                     => 'IA (Fine Tunnel)',
        'ai_training'                   => 'Entrenamiento IA',
        'ai_training_hint'              => 'Selecciona los datos de entrenamiento IA para este bot',
        'ai_training_placeholder'       => 'Seleccionar Entrenamiento IA...',
        'choose_ai_training'            => 'Elegir Entrenamiento IA',
        
        // Status & Options
        'status'                        => 'Estado',
        'status_hint'                   => 'Estado activo/inactivo del bot de Telegram',
        'status_active'                 => 'Activo',
        'status_inactive'               => 'Inactivo',
        'auto_reply_option'             => 'Bot Activo en',
        'auto_reply_option_hint'        => 'Determina dónde el bot estará activo respondiendo mensajes',
        'reply_all'                     => 'Todos (Personal y Grupo)',
        'reply_personal'                => 'Personal',
        'reply_group'                   => 'Grupo',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'Bot Inactivo en Días Específicos',
        'inactive_certain_day_hint'     => 'Activar si deseas que el bot esté inactivo en días específicos',
        'inactive_certain_day_no'       => 'No, Activo Todos los Días',
        'inactive_certain_day_yes'      => 'Sí, Inactivo en Días Específicos',
        'select_days'                   => 'Seleccionar Días',
        'select_days_placeholder'       => 'Seleccionar días...',
        'day_monday'                    => 'Lunes',
        'day_tuesday'                   => 'Martes',
        'day_wednesday'                 => 'Miércoles',
        'day_thursday'                  => 'Jueves',
        'day_friday'                    => 'Viernes',
        'day_saturday'                  => 'Sábado',
        'day_sunday'                    => 'Domingo',
        
        'inactive_certain_time'         => 'Bot Inactivo en Horas Específicas',
        'inactive_certain_time_hint'    => 'Activar si deseas que el bot esté inactivo en horas específicas',
        'inactive_certain_time_no'      => 'No, Activo 24 Horas',
        'inactive_certain_time_yes'     => 'Sí, Inactivo en Horas Específicas',
        'start_time'                    => 'Hora de Inicio de Inactividad',
        'start_time_hint'               => 'Hora de inicio de inactividad del bot',
        'end_time'                      => 'Hora de Fin de Inactividad',
        'end_time_hint'                 => 'Hora de fin de inactividad del bot',
        
        'daily_limit'                   => 'Límite Diario',
        'daily_limit_hint'              => 'Activar si el bot tiene límite de envío de mensajes por día',
        'daily_limit_no'                => 'Sin Límite',
        'daily_limit_yes'               => 'Con Límite Diario',
        'enter_daily_limit'             => 'Introducir Límite Diario',
        'daily_limit_placeholder'       => 'Ejemplo: 1000',
        'daily_limit_suffix'            => 'mensajes/día',
        'daily_limit_hint_input'        => 'Número máximo de mensajes que se pueden enviar por día',
        
        // Actions
        'back_to_list'                  => 'Volver a la Página de Telegram',
        'add_device'                    => 'Agregar Dispositivo',
        'save_device'                   => 'Guardar Dispositivo',
        'update_device'                 => 'Actualizar Dispositivo',
        'cancel'                        => 'Cancelar',
        'required_fields'               => 'Los campos marcados con * son obligatorios',
        
        // Messages
        'device_created'                => 'Bot de Telegram agregado exitosamente',
        'device_updated'                => 'Bot de Telegram actualizado exitosamente',
        'device_deleted'                => 'Bot de Telegram eliminado exitosamente',
        
        // List/Index Page
        'add_connection'                => 'Agregar Conexión Telegram',
        'total_bot'                     => 'Total Telegram',
        'not_connected'                 => 'No Conectado',
        'bot_connected'                 => 'Telegram Conectado',
        'connection_list'               => 'Lista de Conexiones Telegram',
        'bot_name'                      => 'Nombre del Bot',
        'broadcast_sent_today'          => 'Difusiones Enviadas Hoy',
        'daily_broadcast_limit_label'   => 'Límite de Difusión Diaria',
        
        // Actions List
        'copy_id'                       => 'Copiar ID',
        'edit_bot'                      => 'Editar Bot',
        'delete_bot'                    => 'Eliminar Bot',
        'copied_bot_id'                 => 'ID del bot copiado exitosamente',
        'search_bot'                    => 'Buscar bot de telegram...',
    ],
    // ===== FACEBOOK =====
    'facebook' => [
        'add_account'                   => 'Agregar Cuenta',
        'account_list'                  => 'Lista de Cuentas de Facebook',
        'account_connected'             => 'Cuenta de Facebook conectada exitosamente.',
        'login_failed'                  => 'Error al iniciar sesión en Facebook: ',
    ],
    // ===== INSTAGRAM =====
    'instagram' => [
        'add_account'                   => 'Agregar Cuenta',
        'account_list'                  => 'Lista de Cuentas de Instagram',
        'account_connected'             => 'Cuenta de Instagram conectada exitosamente.',
        'login_failed'                  => 'Error al iniciar sesión en Instagram: ',
    ],
];