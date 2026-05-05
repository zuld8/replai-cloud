<?php

return [
    // Ticket Management
    'ticket_management' => 'Gestão de Tickets',
    'contact_list' => 'Lista de Tickets',
    'total' => 'Total',
    'contact' => 'Contato',
    'search_contact' => 'Pesquisar Contato',
    
    // Filters
    'start_date' => 'Data de Início',
    'end_date' => 'Data de Término',
    'level' => 'Nível',
    'agent' => 'Agente',
    'tickets' => 'tickets',
    'all_categories' => 'Todas as Categorias',
    'all_levels' => 'Todos os Níveis',
    'all_agents' => 'Todos os Agentes',
    'all_status' => 'Todos os Status',
    'no_ticket_in_column' => 'Nenhum ticket nesta coluna',
    
    // Ticket Levels
    'level_low' => 'Baixo',
    'level_medium' => 'Médio',
    'level_high' => 'Alto',
    'level_urgent' => 'Urgente',
    
    // Ticket Information
    'ticket_id' => 'ID do Ticket',
    'ticket_level' => 'Nível do Ticket',
    'ticket_name' => 'Nome do Ticket',
    'ticket_name_placeholder' => 'Digite o nome do ticket',
    'ticket_detail' => 'Detalhes do Ticket',
    
    // Status
    'status' => 'Status',
    'status_open' => 'Aberto',
    'status_resolved' => 'Resolvido',
    'status_pending' => 'Pendente',
    'status_block' => 'Bloqueado',
    'status_in_progress' => 'Em Andamento',
    'status_closed' => 'Fechado',
    
    // Basic Info
    'basic_info' => 'Informações Básicas',
    'name' => 'Nome',
    'email' => 'E-mail',
    'phone' => 'Telefone',
    'title' => 'Título',
    'title_placeholder' => 'Digite o título do ticket',
    'priority' => 'Prioridade',
    
    // Category
    'category' => 'Categoria',
    'select_category' => 'Selecionar Categoria',
    'category_name' => 'Nome da Categoria',
    'category_name_placeholder' => 'Digite o nome da categoria',
    'category_slug' => 'Slug da Categoria',
    'category_slug_placeholder' => 'slug-gerado-automaticamente',
    'category_slug_hint' => 'Deixe em branco para gerar automaticamente a partir do nome',
    'category_description' => 'Descrição',
    'category_description_placeholder' => 'Digite a descrição da categoria',
    'category_active' => 'Ativo',
    'category_management' => 'Gestão de Categorias',
    'manage_categories' => 'Gerenciar Categorias',
    'add_category' => 'Adicionar Categoria',
    'edit_category' => 'Editar Categoria',
    'create_category' => 'Criar Categoria',
    'update_category' => 'Atualizar Categoria',
    'search_category' => 'Pesquisar categorias...',
    'no_categories' => 'Nenhuma categoria encontrada',
    'category_deleted' => 'Categoria excluída com sucesso',
    'categories_deleted' => 'Categorias excluídas com sucesso',
    'category_updated' => 'Categoria atualizada com sucesso',
    'failed_delete_category' => 'Falha ao excluir categoria',
    'failed_update_category' => 'Falha ao atualizar categoria',
    'confirm_delete_category_title' => 'Excluir Categoria?',
    'confirm_delete_category_text' => 'Tem certeza de que deseja excluir esta categoria?',
    'confirm_bulk_delete_categories_title' => 'Excluir Múltiplas Categorias?',
    'confirm_bulk_delete_categories_text' => 'Tem certeza de que deseja excluir {count} categorias?',
    
    // Channel
    'channel_info' => 'Informações do Canal',
    'source_channel' => 'Canal de Origem',
    
    // Assignment
    'status_assignment' => 'Status e Atribuição',
    'assign_agent' => 'Atribuir Agente',
    'handled_by' => 'Tratado Por',
    'resolved_by' => 'Resolvido Por',
    'assigned_to' => 'Atribuído a',
    'no_agent' => 'Sem Agente',
    'no_agent_assigned' => 'Nenhum agente atribuído',
    'not_handled' => 'Não Tratado',
    'hold_ctrl_multiple' => 'Segure Ctrl/Cmd para selecionar múltiplos agentes',
    
    // Label
    'label' => 'Etiqueta',
    'select_label' => 'Selecionar Etiqueta',
    'label_name' => 'Nome da Etiqueta',
    'label_name_placeholder' => 'Digite o nome da etiqueta',
    'label_tag' => 'Tag/Palavra-chave da Etiqueta',
    'label_tag_desc' => 'Palavras-chave que acionarão esta atribuição de etiqueta',
    'label_tag_placeholder' => 'ex: urgente, reclamação, faturamento',
    'label_position' => 'Índice de Posição',
    'label_color' => 'Cor da Etiqueta',
    'label_selection_hint' => 'A etiqueta será determinada automaticamente com base nas palavras-chave se não for selecionada',
    'add_label' => 'Adicionar Etiqueta',
    'edit_label' => 'Editar Etiqueta',
    'delete_label' => 'Excluir Etiqueta',
    'update_label' => 'Atualizar Etiqueta',
    'save_label' => 'Salvar Etiqueta',
    'label_created' => 'Etiqueta criada com sucesso',
    'label_updated' => 'Etiqueta atualizada com sucesso',
    'label_deleted' => 'Etiqueta excluída com sucesso',
    'failed_create_label' => 'Falha ao criar etiqueta',
    'failed_update_label' => 'Falha ao atualizar etiqueta',
    'failed_delete_label' => 'Falha ao excluir etiqueta',
    'confirm_delete_label_title' => 'Excluir Etiqueta?',
    'confirm_delete_label_text' => 'Tem certeza de que deseja excluir esta etiqueta? Todos os tickets nesta etiqueta serão movidos para "Novos Tickets".',
    'yes_delete_label' => 'Sim, Excluir Etiqueta!',
    'label_changed' => 'Etiqueta Alterada',
    'move_to_label' => 'Mover para Etiqueta',
    'ticket_moved' => 'Ticket movido com sucesso',
    'failed_move_ticket' => 'Falha ao mover ticket',
    
    // Ticket Actions
    'add_ticket' => 'Criar Ticket',
    'edit_ticket' => 'Editar Ticket',
    'create_ticket' => 'Criar Ticket',
    'update_ticket' => 'Atualizar Ticket',
    'select_contact' => 'Selecionar Contato',
    
    // Notes
    'notes' => 'Notas',
    'notes_placeholder' => 'Digite notas ou descrição',
    'add' => 'Adicionar',
    'add_note_placeholder' => 'Adicionar uma nota...',
    'no_notes_yet' => 'Ainda não há notas. Seja o primeiro a adicionar uma!',
    'ctrl_enter_to_send' => 'Pressione Ctrl+Enter para enviar',
    
    // Activity
    'activity_history' => 'Histórico de Atividades',
    'no_activity_logs' => 'Nenhum registro de atividade disponível',
    
    // Attachment
    'attachment' => 'Anexo',
    'file_upload_hint' => 'Formatos suportados: JPG, PNG, PDF, DOC, DOCX, TXT',
    
    // Timestamps
    'timestamps' => 'Timestamps',
    'created_at' => 'Criado Em',
    'updated_at' => 'Atualizado Em',
    
    // Actions
    'actions' => 'Ações',
    'view_detail' => 'Ver Detalhes',
    'edit' => 'Editar',
    'delete' => 'Excluir',
    'close' => 'Fechar',
    'cancel' => 'Cancelar',
    'save' => 'Salvar',
    'manage' => 'Gerenciar',
    'quick_actions' => 'Ações Rápidas',
    
    // Pagination & Loading
    'loading_data' => 'Carregando Dados...',
    'loading_more' => 'Carregando Mais...',
    'load_more' => 'Carregar Mais',
    'remaining' => 'Restante',
    'contact_per_column' => 'Contatos por Coluna',
    'contact_loaded' => 'contatos carregados',
    
    // Status Messages
    'active' => 'Ativo',
    'inactive' => 'Inativo',
    'selected' => 'selecionado',
    'delete_selected' => 'Excluir Selecionados',
    
    // Confirmations
    'confirm_delete_title' => 'Tem certeza?',
    'confirm_delete_text' => 'Você não poderá reverter isso!',
    'yes_delete' => 'Sim, excluir!',
    'contact_deleted' => 'O contato foi excluído.',
    
    // Error Messages
    'failed_delete_contact' => 'Falha ao excluir contato',
    'failed_load_data' => 'Falha ao carregar dados',
    'error_load_data' => 'Erro ao carregar dados',
];
