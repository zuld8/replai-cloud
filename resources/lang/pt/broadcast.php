<?php

return [
    'wa' => [
        // Index/List Page
        'create_schedule_button' => 'Criar Agendamento de Transmissão',
        'wa_group' => 'Grupo WhatsApp',
        'page_title' => 'Lista de Transmissões WhatsApp',
        
        // Card Headers
        'broadcast_info' => 'Informações da Transmissão',
        'target_template' => 'Público & Template',
        
        // Form Labels
        'name' => 'Título da Transmissão',
        'schedule' => 'Agendamento de Envio',
        'template' => 'Template da Mensagem',
        'devices' => 'Dispositivos',
        'device_option' => 'Opção de Uso de Dispositivo',
        'delay' => 'Intervalo Entre Envios',
        'stop_sending' => 'Parar Envio Após',
        'rest_sending' => 'Intervalo de Descanso',
        'category' => 'Categoria de Negócios',
        'whatsapp_group' => 'Grupo WhatsApp',
        'location_target' => 'Localização Alvo',
        
        // Placeholders
        'name_placeholder' => 'Exemplo: Promoção Final de Ano 2024',
        'choose_group' => 'Escolher Grupo WhatsApp',
        'choose_device' => 'Escolher Dispositivo WhatsApp',
        
        // Device Options
        'device_sequence' => 'Dispositivo Único (Sequencial)',
        'device_spin' => 'Escolha por IA (Rotativo)',
        'device_random' => 'Aleatório',
        
        // Units
        'seconds' => 'segundos',
        'numbers' => 'números',
        
        // Helper Texts
        'name_help' => 'Nome da campanha de transmissão para identificação',
        'schedule_help' => 'Agendamento de envio da mensagem de transmissão',
        'template_help' => 'Template da mensagem que será enviada',
        'devices_help' => 'Escolha o dispositivo WhatsApp para enviar a transmissão',
        'device_option_help' => 'Método de seleção de dispositivo para envio',
        'delay_help' => 'Recomendação: 30-300 segundos. Quanto menor, maior o risco de bloqueio',
        'stop_sending_help' => 'Parar de enviar após quantos números',
        'rest_sending_help' => 'Intervalo antes de voltar a enviar mensagens',
        'category_help' => 'Filtrar público por categoria de negócios',
        'whatsapp_group_help' => 'Público alvo de grupo WhatsApp específico',
        'province_help' => 'Filtrar público por estado',
        'city_help' => 'Filtrar público por cidade/município',
        'district_help' => 'Filtrar público por distrito',
        
        // Badges & Labels
        'optional' => 'Opcional',
        'required_field' => 'Campo obrigatório',
        
        // Alert & Tips
        'safe_sending_tips' => 'Dicas de Envio Seguro:',
        'tip_delay' => 'Use intervalo mínimo de 30 segundos para evitar bloqueio',
        'tip_batch' => 'Limite o envio a máximo de 50-100 mensagens por lote',
        'tip_rest' => 'Dê um intervalo de descanso adequado entre lotes',
        'tip_multiple' => 'Use múltiplos dispositivos para balanceamento de carga',
    ],
    
    'email' => [
        // Form Fields
        'category' => 'Categoria de Negócios',
        'template' => 'Template de Email',
        'name' => 'Título da Transmissão',
        'schedule' => 'Agendamento de Envio',
        
        // Placeholders
        'name_placeholder' => 'Exemplo: Newsletter Mensal Outubro 2024',
        
        // Helper Texts
        'category_help' => 'Filtrar destinatários por categoria de negócios',
        'template_help' => 'Template de email que será enviado aos destinatários',
        'name_help' => 'Nome da campanha de transmissão para identificação',
        'schedule_help' => 'Agendamento de envio da transmissão de email',
        
        // Badges & Labels
        'optional' => 'Opcional',
        'required_field' => 'Campo obrigatório',
        
        // Alert & Tips
        'email_sending_tips' => 'Dicas de Envio de Email:',
        'tip_test_template' => 'Certifique-se de que o template de email foi testado antes da transmissão',
        'tip_optimal_time' => 'Escolha o horário de envio ideal para máximo engajamento',
        'tip_use_category' => 'Use categorias para segmentação mais específica',
        'tip_check_spam' => 'Verifique novamente o conteúdo para evitar cair no spam',
    ],
    
    'upselling' => [
        // List/Index Page
        'page_title' => 'Lista de Campanhas de Upselling',
        'create_campaign_button' => 'Criar Nova Campanha',
        'refresh_button' => 'Atualizar',
        
        // Table Headers
        'campaign_info' => 'Info da Campanha',
        'schedule_frequency' => 'Agendamento & Frequência',
        'target_category' => 'Público & Categoria',
        'method_template' => 'Método & Template',
        'status' => 'Status',
        'action' => 'Ação',
        
        // Filter Options
        'all_status' => 'Todos os Status',
        'status_active' => 'Ativo',
        'status_inactive' => 'Inativo',
        'status_scheduled' => 'Agendado',
        'status_completed' => 'Concluído',
        'all_frequency' => 'Todas as Frequências',
        'frequency_once' => 'Uma vez',
        'frequency_daily' => 'Diário',
        'frequency_monthly' => 'Mensal',
        'frequency_yearly' => 'Anual',
        
        // DataTable Language
        'search_placeholder' => 'Buscar campanha...',
        'length_menu' => 'Mostrar _MENU_ campanhas por página',
        'info' => 'Mostrando _START_ até _END_ de _TOTAL_ campanhas',
        'info_empty' => 'Nenhuma campanha',
        'info_filtered' => '(filtrado de _MAX_ campanhas totais)',
        'paginate_first' => 'Primeira',
        'paginate_last' => 'Última',
        'paginate_next' => 'Próxima',
        'paginate_previous' => 'Anterior',
        
        // DataTable Content
        'devices_count' => 'dispositivos',
        'delay_label' => 'Intervalo',
        'once_send' => 'Envio único',
        'date_prefix' => 'Data',
        'every_month' => 'todo mês',
        'every_year' => 'todo ano',
        'ongoing' => 'Contínuo',
        'all_categories' => 'Todas as categorias',
        'labels_count' => 'etiquetas',
        'template_label' => 'Template',
        'ai_generated' => 'Gerado por IA',
        
        // Day Short Labels
        'day_monday_short' => 'Seg',
        'day_tuesday_short' => 'Ter',
        'day_wednesday_short' => 'Qua',
        'day_thursday_short' => 'Qui',
        'day_friday_short' => 'Sex',
        'day_saturday_short' => 'Sáb',
        'day_sunday_short' => 'Dom',
        
        // Month Short Labels
        'month_jan' => 'Jan',
        'month_feb' => 'Fev',
        'month_mar' => 'Mar',
        'month_apr' => 'Abr',
        'month_may' => 'Mai',
        'month_jun' => 'Jun',
        'month_jul' => 'Jul',
        'month_aug' => 'Ago',
        'month_sep' => 'Set',
        'month_oct' => 'Out',
        'month_nov' => 'Nov',
        'month_dec' => 'Dez',
        
        // Action Button Tooltips
        'btn_view_detail' => 'Ver Detalhes',
        'btn_edit_campaign' => 'Editar Campanha',
        'btn_duplicate_campaign' => 'Duplicar Campanha',
        'btn_delete_campaign' => 'Excluir Campanha',
        
        // Confirmation Messages
        'confirm_activate' => 'Tem certeza de que deseja ativar esta campanha?',
        'confirm_deactivate' => 'Tem certeza de que deseja desativar esta campanha?',
        'confirm_delete' => 'Tem certeza de que deseja excluir esta campanha? Esta ação não pode ser desfeita.',
        
        // Success Messages
        'success_title' => 'Sucesso',
        'success_delete' => 'Campanha excluída com sucesso',
        
        // Error Messages
        'error_title' => 'Erro',
        'error_status_change' => 'Erro ao alterar o status da campanha',
        'error_delete' => 'Erro ao excluir campanha',
        
        // Back Button
        'back_to_campaign' => 'Voltar para Campanhas de Upselling',
        
        // Card Headers (Form)
        'basic_info' => 'Informações Básicas da Campanha',
        'schedule_config' => 'Opções de Agendamento de Envio & Configuração de Mensagem',
        
        // Form Fields
        'campaign_title' => 'Título da Campanha de Upselling',
        'delay' => 'Intervalo Entre Envios',
        'devices' => 'Dispositivos',
        'device_option' => 'Opção de Uso de Dispositivo',
        'contact_category' => 'Categoria de Contatos',
        'contact_labels' => 'Etiquetas de Contatos',
        'schedule_frequency' => 'Frequência de Envio',
        'select_days' => 'Selecionar Dias',
        'date_in_month' => 'Data do Mês',
        'specific_date' => 'Data Específica',
        'sending_time' => 'Horário de Envio',
        'start_date' => 'Data de Início',
        'end_date' => 'Data de Término',
        'broadcast_method' => 'Método Utilizado',
        'ai_prompt' => 'Prompt da IA',
        'template_message' => 'Template da Mensagem',
        
        // Placeholders
        'name_placeholder' => 'Exemplo: Campanha Promoção Final de Ano',
        'ai_prompt_placeholder' => 'Exemplo: Crie uma mensagem de upselling para produto novo com tom amigável e atrativo...',
        'select_category' => 'Selecionar categoria...',
        'select_template' => 'Selecionar template...',
        'select_device' => 'Selecionar dispositivo...',
        'select_day' => 'Selecionar dia...',
        
        // Helper Texts
        'name_help' => 'Nome da campanha de upselling para identificação interna',
        'delay_help' => 'Intervalo de tempo entre envios de mensagens',
        'devices_help' => 'Escolha um ou mais dispositivos para enviar mensagens',
        'device_option_help' => 'Método de seleção de dispositivo para envio de mensagens',
        'category_help' => 'Filtrar contatos por categoria específica',
        'labels_help' => 'Escolha etiquetas de contatos que receberão mensagens (múltipla seleção)',
        'frequency_help' => 'Determine com que frequência as mensagens serão enviadas',
        'days_help' => 'Escolha os dias para envio',
        'date_help' => 'Escolha a data do mês para envio',
        'yearly_help' => 'Escolha o mês e data para envio anual',
        'time_help' => 'Horário de envio da mensagem',
        'start_date_help' => 'Data de início da campanha',
        'end_date_help' => 'Deixe em branco se não houver limite de tempo',
        'method_help' => 'Escolha o método de criação de mensagem da campanha',
        'ai_prompt_help' => 'A IA usará este prompt para criar mensagens personalizadas para cada contato',
        'template_help' => 'Escolha um template de mensagem criado anteriormente',
        
        // Frequency Options
        'freq_once' => 'Enviar Uma Vez',
        'freq_daily' => 'Diário',
        'freq_monthly' => 'Mensal',
        'freq_yearly' => 'Anual',
        
        // Days
        'monday' => 'Segunda-feira',
        'tuesday' => 'Terça-feira',
        'wednesday' => 'Quarta-feira',
        'thursday' => 'Quinta-feira',
        'friday' => 'Sexta-feira',
        'saturday' => 'Sábado',
        'sunday' => 'Domingo',
        'last_day' => 'Último Dia',
        
        // Months
        'january' => 'Janeiro',
        'february' => 'Fevereiro',
        'march' => 'Março',
        'april' => 'Abril',
        'may' => 'Maio',
        'june' => 'Junho',
        'july' => 'Julho',
        'august' => 'Agosto',
        'september' => 'Setembro',
        'october' => 'Outubro',
        'november' => 'Novembro',
        'december' => 'Dezembro',
        
        // Broadcast Methods
        'method_template_option' => 'Usar Template',
        'method_ai_option' => 'Usar Prompt de IA',
        
        // Device Options
        'device_sequence' => 'Dispositivo Único (Sequencial)',
        'device_spin' => 'Escolha por IA (Seleção Automática)',
        'device_random' => 'Aleatório',
        
        // Units
        'seconds' => 'segundos',
        
        // Badges & Labels
        'optional' => 'Opcional',
        'required_field' => 'Campo obrigatório',
        
        // Buttons
        'create_campaign' => 'Criar Campanha',
        'update_campaign' => 'Atualizar Campanha',
        
        // Alert & Tips
        'campaign_tips' => 'Dicas de Campanha de Upselling:',
        'tip_ai_prompt' => 'Use prompts de IA para mensagens mais personalizadas e dinâmicas',
        'tip_optimal_time' => 'Escolha o horário de envio adequado para máximo engajamento',
        'tip_use_labels' => 'Utilize etiquetas para segmentação mais específica',
        'tip_frequency' => 'Use frequência diária/mensal para follow-up automático',
    ]
];