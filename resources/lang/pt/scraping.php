<?php

return [
    'maps' => [
        // Page Titles & Navigation
        'page_title' => 'Scraping do Google Maps',
        'create_title' => 'Criar Novo Scraping',
        'update_title' => 'Editar Scraping',
        'back_to' => 'Voltar ao Scraping',
        
        // Card Headers
        'scraping_info' => 'Informações do Scraping',
        'location_target' => 'Localização Alvo do Scraping',
        
        // Form Labels
        'category' => 'Categoria de Negócio',
        'name' => 'Nome do Scraping',
        'schedule' => 'Agendamento do Scraping',
        'province' => 'Província',
        'city' => 'Cidade/Município',
        'district' => 'Distrito',
        
        // Placeholders
        'name_placeholder' => 'Exemplo: Scraping Restaurantes Lisboa',
        'choose_category' => 'Escolha a categoria de negócio',
        
        // Helper Texts
        'category_help' => 'Categoria de negócio que será extraída do Google Maps',
        'name_help' => 'Nome para identificação deste processo de scraping',
        'schedule_help' => 'Agendamento de quando o processo de scraping será executado',
        'province_help' => 'Filtrar scraping por província específica',
        'city_help' => 'Filtrar scraping por cidade/município',
        'district_help' => 'Filtrar scraping por distrito específico',
        
        // Badges & Labels
        'optional' => 'Opcional',
        'required' => 'Obrigatório',
        'required_field' => 'Campo obrigatório',
        
        // Tips & Alerts
        'tips_title' => 'Dicas para Scraping do Google Maps:',
        'tip_location' => 'Quanto mais específica a localização escolhida, mais focados serão os resultados',
        'tip_category' => 'Escolha a categoria que corresponde ao seu negócio alvo',
        'tip_schedule' => 'Agende o scraping em horários de baixo tráfego para resultados ideais',
        'tip_auto' => 'O processo de scraping será executado automaticamente conforme o agendamento',
        
        // Buttons
        'btn_create' => 'Iniciar Scraping',
        'btn_update' => 'Salvar Alterações',
        
        // Status
        'status_pending' => 'Pendente',
        'status_processing' => 'Processando',
        'status_completed' => 'Concluído',
        'status_failed' => 'Falhou',
        
        // Messages
        'success_create' => 'Scraping agendado com sucesso',
        'success_update' => 'Scraping atualizado com sucesso',
        'success_delete' => 'Scraping excluído com sucesso',
        'error_create' => 'Falha ao criar scraping',
        'error_update' => 'Falha ao atualizar scraping',
        'error_delete' => 'Falha ao excluir scraping',
        
        // Meta
        'title' => 'Scraping do Google Maps',
        'description' => 'Extrair dados de negócios do Google Maps por categoria e localização',
    ],
    
    'contact' => [
        // Page Titles & Navigation
        'page_title' => 'Scraping de Contatos WhatsApp',
        'create_title' => 'Adicionar Scraping de Contatos',
        'update_title' => 'Editar Scraping de Contatos',
        'back_to' => 'Voltar ao Scraping',
        'list_title' => 'Lista de Dados de Scraping de Contatos',
        'whatsapp_group' => 'Grupo de WhatsApp',

        // table columns
        'device' => 'Dispositivo',
        'category' => 'Categoria',
        'contacts' => 'Contatos',
        'syncron' => 'Sincronizar',
        
        // Card Headers
        'scraping_info' => 'Configuração do Scraping',
        
        // Form Labels
        'devices' => 'Dispositivos',
        'category' => 'Categoria',
        'name' => 'Nome do Scraping',
        'schedule' => 'Agendamento do Scraping',
        
        // Placeholders
        'name_placeholder' => 'Exemplo: Scraping Contatos Grupo Marketing',
        'choose_devices' => 'Escolha os dispositivos...',
        'choose_category' => 'Escolha a categoria',
        
        // Helper Texts
        'devices_help' => 'Escolha o dispositivo WhatsApp que será usado para scraping',
        'category_help' => 'Categoria para classificar os contatos extraídos',
        'name_help' => 'Nome para identificação deste processo de scraping',
        'schedule_help' => 'Agendamento de quando o scraping de contatos será executado',
        
        // Labels
        'required' => 'Obrigatório',
        'required_field' => 'Campo obrigatório',
        
        // Tips & Alerts
        'tips_title' => 'Dicas para Scraping de Contatos WhatsApp:',
        'tip_device' => 'Use múltiplos dispositivos para scraping mais rápido e eficiente',
        'tip_category' => 'Escolha a categoria certa para facilitar o agrupamento de contatos',
        'tip_schedule' => 'Agende o scraping no horário adequado para performance ideal',
        'tip_auto' => 'Contatos extraídos com sucesso serão salvos automaticamente na base de dados',
        
        // Buttons
        'btn_create' => 'Iniciar Scraping',
        'btn_update' => 'Salvar Alterações',
        
        // Status
        'status_pending' => 'Pendente',
        'status_processing' => 'Processando',
        'status_completed' => 'Concluído',
        'status_failed' => 'Falhou',
        
        // Messages
        'success_create' => 'Scraping de contatos agendado com sucesso',
        'success_update' => 'Scraping de contatos atualizado com sucesso',
        'success_delete' => 'Scraping de contatos excluído com sucesso',
        'error_create' => 'Falha ao criar scraping de contatos',
        'error_update' => 'Falha ao atualizar scraping de contatos',
        'error_delete' => 'Falha ao excluir scraping de contatos',
        
        // Meta
        'title' => 'Scraping de Contatos',
        'description' => 'Extrair contatos do WhatsApp por dispositivo e categoria',
    ],
    
    'group' => [
        // Page Titles & Navigation
        'page_title' => 'Scraping de Grupos',
        'create_title' => 'Adicionar Scraping de Grupos',
        'update_title' => 'Editar Scraping de Grupos',
        'back_to' => 'Voltar ao Scraping',
        'list_title' => 'Lista de Dados de Scraping de Grupos',
        'whatsapp_group' => 'Grupo de WhatsApp',
        'group_list' => 'Lista de Grupos',

        // table columns
        'device' => 'Dispositivo',
        'group_id' => 'ID do Grupo',
        'contacts' => 'Contatos',
        'syncron' => 'Sincronizar',

        // Card Headers
        'scraping_info' => 'Configuração do Scraping',
        
        // Form Labels
        'devices' => 'Dispositivos',
        'name' => 'Nome do Scraping',
        'schedule' => 'Agendamento do Scraping',
        
        // Placeholders
        'name_placeholder' => 'Exemplo: Scraping Grupo Equipe Vendas',
        'choose_devices' => 'Escolha os dispositivos...',
        
        // Helper Texts
        'devices_help' => 'Escolha o dispositivo WhatsApp do qual os grupos serão extraídos',
        'name_help' => 'Nome para identificação deste processo de scraping de grupos',
        'schedule_help' => 'Agendamento de quando o scraping de grupos será executado',
        
        // Labels
        'required' => 'Obrigatório',
        'required_field' => 'Campo obrigatório',
        
        // Tips & Alerts
        'tips_title' => 'Dicas para Scraping de Grupos WhatsApp:',
        'tip_device' => 'O processo irá extrair a lista de todos os grupos do dispositivo selecionado',
        'tip_category' => 'Use múltiplos dispositivos para obter mais grupos',
        'tip_schedule' => 'Os dados dos grupos serão salvos para uso em campanhas de broadcast',
        'tip_auto' => 'Certifique-se de que o dispositivo está online durante o processo de scraping',
        
        // Buttons
        'btn_create' => 'Iniciar Scraping',
        'btn_update' => 'Salvar Alterações',
        
        // Status
        'status_pending' => 'Pendente',
        'status_processing' => 'Processando',
        'status_completed' => 'Concluído',
        'status_failed' => 'Falhou',
        
        // Messages
        'success_create' => 'Scraping de grupos agendado com sucesso',
        'success_update' => 'Scraping de grupos atualizado com sucesso',
        'success_delete' => 'Scraping de grupos excluído com sucesso',
        'error_create' => 'Falha ao criar scraping de grupos',
        'error_update' => 'Falha ao atualizar scraping de grupos',
        'error_delete' => 'Falha ao excluir scraping de grupos',
        
        // Meta
        'title' => 'Scraping de Grupos',
        'description' => 'Extrair dados de grupos e comunidades WhatsApp',
    ],
];