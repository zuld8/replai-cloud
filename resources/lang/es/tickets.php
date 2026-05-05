<?php

return [
    // Ticket Management
    'ticket_management' => 'Gestión de Tickets',
    'contact_list' => 'Lista de Tickets',
    'total' => 'Total',
    'contact' => 'Contacto',
    'search_contact' => 'Buscar Contacto',
    
    // Filters
    'start_date' => 'Fecha de Inicio',
    'end_date' => 'Fecha de Fin',
    'level' => 'Nivel',
    'agent' => 'Agente',
    'tickets' => 'tickets',
    'all_categories' => 'Todas las Categorías',
    'all_levels' => 'Todos los Niveles',
    'all_agents' => 'Todos los Agentes',
    'all_status' => 'Todos los Estados',
    'no_ticket_in_column' => 'No hay tickets en esta columna',
    
    // Ticket Levels
    'level_low' => 'Bajo',
    'level_medium' => 'Medio',
    'level_high' => 'Alto',
    'level_urgent' => 'Urgente',
    
    // Ticket Information
    'ticket_id' => 'ID de Ticket',
    'ticket_level' => 'Nivel de Ticket',
    'ticket_name' => 'Nombre del Ticket',
    'ticket_name_placeholder' => 'Ingrese el nombre del ticket',
    'ticket_detail' => 'Detalle del Ticket',
    
    // Status
    'status' => 'Estado',
    'status_open' => 'Abierto',
    'status_resolved' => 'Resuelto',
    'status_pending' => 'Pendiente',
    'status_block' => 'Bloqueado',
    'status_in_progress' => 'En Proceso',
    'status_closed' => 'Cerrado',
    
    // Basic Info
    'basic_info' => 'Información Básica',
    'name' => 'Nombre',
    'email' => 'Correo Electrónico',
    'phone' => 'Teléfono',
    'title' => 'Título',
    'title_placeholder' => 'Ingrese el título del ticket',
    'priority' => 'Prioridad',
    
    // Category
    'category' => 'Categoría',
    'select_category' => 'Seleccionar Categoría',
    'category_name' => 'Nombre de la Categoría',
    'category_name_placeholder' => 'Ingrese el nombre de la categoría',
    'category_slug' => 'Slug de Categoría',
    'category_slug_placeholder' => 'slug-auto-generado',
    'category_slug_hint' => 'Deje en blanco para generar automáticamente desde el nombre',
    'category_description' => 'Descripción',
    'category_description_placeholder' => 'Ingrese la descripción de la categoría',
    'category_active' => 'Activo',
    'category_management' => 'Gestión de Categorías',
    'manage_categories' => 'Administrar Categorías',
    'add_category' => 'Agregar Categoría',
    'edit_category' => 'Editar Categoría',
    'create_category' => 'Crear Categoría',
    'update_category' => 'Actualizar Categoría',
    'search_category' => 'Buscar categorías...',
    'no_categories' => 'No se encontraron categorías',
    'category_deleted' => 'Categoría eliminada exitosamente',
    'categories_deleted' => 'Categorías eliminadas exitosamente',
    'category_updated' => 'Categoría actualizada exitosamente',
    'failed_delete_category' => 'Error al eliminar la categoría',
    'failed_update_category' => 'Error al actualizar la categoría',
    'confirm_delete_category_title' => '¿Eliminar Categoría?',
    'confirm_delete_category_text' => '¿Está seguro de que desea eliminar esta categoría?',
    'confirm_bulk_delete_categories_title' => '¿Eliminar Múltiples Categorías?',
    'confirm_bulk_delete_categories_text' => '¿Está seguro de que desea eliminar {count} categorías?',
    
    // Channel
    'channel_info' => 'Información del Canal',
    'source_channel' => 'Canal de Origen',
    
    // Assignment
    'status_assignment' => 'Estado y Asignación',
    'assign_agent' => 'Asignar Agente',
    'handled_by' => 'Atendido por',
    'resolved_by' => 'Resuelto por',
    'assigned_to' => 'Asignado a',
    'no_agent' => 'Sin Agente',
    'no_agent_assigned' => 'No hay agente asignado',
    'not_handled' => 'No Atendido',
    'hold_ctrl_multiple' => 'Mantenga Ctrl/Cmd para seleccionar múltiples agentes',
    
    // Label
    'label' => 'Etiqueta',
    'select_label' => 'Seleccionar Etiqueta',
    'label_name' => 'Nombre de la Etiqueta',
    'label_name_placeholder' => 'Ingrese el nombre de la etiqueta',
    'label_tag' => 'Etiqueta/Palabra Clave',
    'label_tag_desc' => 'Palabras clave que activarán esta asignación de etiqueta',
    'label_tag_placeholder' => 'ej: urgente, queja, facturación',
    'label_position' => 'Índice de Posición',
    'label_color' => 'Color de Etiqueta',
    'label_selection_hint' => 'La etiqueta se determinará automáticamente según las palabras clave si no se selecciona',
    'add_label' => 'Agregar Etiqueta',
    'edit_label' => 'Editar Etiqueta',
    'delete_label' => 'Eliminar Etiqueta',
    'update_label' => 'Actualizar Etiqueta',
    'save_label' => 'Guardar Etiqueta',
    'label_created' => 'Etiqueta creada exitosamente',
    'label_updated' => 'Etiqueta actualizada exitosamente',
    'label_deleted' => 'Etiqueta eliminada exitosamente',
    'failed_create_label' => 'Error al crear la etiqueta',
    'failed_update_label' => 'Error al actualizar la etiqueta',
    'failed_delete_label' => 'Error al eliminar la etiqueta',
    'confirm_delete_label_title' => '¿Eliminar Etiqueta?',
    'confirm_delete_label_text' => '¿Está seguro de que desea eliminar esta etiqueta? Todos los tickets en esta etiqueta se moverán a "Nuevos Tickets".',
    'yes_delete_label' => '¡Sí, Eliminar Etiqueta!',
    'label_changed' => 'Etiqueta Cambiada',
    'move_to_label' => 'Mover a Etiqueta',
    'ticket_moved' => 'Ticket movido exitosamente',
    'failed_move_ticket' => 'Error al mover el ticket',
    
    // Ticket Actions
    'add_ticket' => 'Crear Ticket',
    'edit_ticket' => 'Editar Ticket',
    'create_ticket' => 'Crear Ticket',
    'update_ticket' => 'Actualizar Ticket',
    'select_contact' => 'Seleccionar Contacto',
    
    // Notes
    'notes' => 'Notas',
    'notes_placeholder' => 'Ingrese notas o descripción',
    'add' => 'Agregar',
    'add_note_placeholder' => 'Agregar una nota...',
    'no_notes_yet' => '¡Aún no hay notas. Sea el primero en agregar una!',
    'ctrl_enter_to_send' => 'Presione Ctrl+Enter para enviar',
    
    // Activity
    'activity_history' => 'Historial de Actividad',
    'no_activity_logs' => 'No hay registros de actividad disponibles',
    
    // Attachment
    'attachment' => 'Adjunto',
    'file_upload_hint' => 'Formatos soportados: JPG, PNG, PDF, DOC, DOCX, TXT',
    
    // Timestamps
    'timestamps' => 'Marcas de Tiempo',
    'created_at' => 'Creado en',
    'updated_at' => 'Actualizado en',
    
    // Actions
    'actions' => 'Acciones',
    'view_detail' => 'Ver Detalle',
    'edit' => 'Editar',
    'delete' => 'Eliminar',
    'close' => 'Cerrar',
    'cancel' => 'Cancelar',
    'save' => 'Guardar',
    'manage' => 'Administrar',
    'quick_actions' => 'Acciones Rápidas',
    
    // Pagination & Loading
    'loading_data' => 'Cargando Datos...',
    'loading_more' => 'Cargando Más...',
    'load_more' => 'Cargar Más',
    'remaining' => 'Restante',
    'contact_per_column' => 'Contactos por Columna',
    'contact_loaded' => 'contactos cargados',
    
    // Status Messages
    'active' => 'Activo',
    'inactive' => 'Inactivo',
    'selected' => 'seleccionado',
    'delete_selected' => 'Eliminar Seleccionados',
    
    // Confirmations
    'confirm_delete_title' => '¿Está seguro?',
    'confirm_delete_text' => '¡No podrá revertir esto!',
    'yes_delete' => '¡Sí, eliminar!',
    'contact_deleted' => 'El contacto ha sido eliminado.',
    
    // Error Messages
    'failed_delete_contact' => 'Error al eliminar el contacto',
    'failed_load_data' => 'Error al cargar los datos',
    'error_load_data' => 'Error al cargar los datos',
];
