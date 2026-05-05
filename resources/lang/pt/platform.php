<?php

return [
    // ===== WHATSAPP DEVICE =====
    'whatsapp' => [
        // Section Titles
        'device_info'                   => 'Informações do Dispositivo',
        'ai_config'                     => 'Configuração do Agente AI',
        'schedule_limits'               => 'Horários e Limites',
        
        // Device Information
        'device_name'                   => 'Nome do Dispositivo',
        'device_name_placeholder'       => 'Exemplo: Dispositivo Admin',
        'device_name_hint'              => 'Nome para identificar este dispositivo WhatsApp',
        'wa_number'                     => 'Nº WhatsApp',
        'wa_number_placeholder'         => '+351 xxx xxx xxx',
        'wa_number_hint'                => 'Número WhatsApp que será conectado ao sistema (formato: +351)',
        'team_member'                   => 'Membro da Equipa',
        'team_member_hint'              => 'Selecione o membro da equipa que gerenciará este dispositivo',
        'team_member_placeholder'       => 'Selecionar membro da equipa...',
        
        // Device Settings
        'device_notification'           => 'Notificações do Dispositivo',
        'device_notification_hint'      => 'Desativar se não quiser receber notificações no seu dispositivo',
        'save_chat_history'             => 'Guardar Histórico de Mensagens',
        'save_chat_history_hint'        => 'O histórico será guardado automaticamente sem reset diário',
        'auto_read_before_reply'        => 'Ler chat primeiro antes do ChatBot responder',
        'auto_read_before_reply_hint'   => 'Ativar se quiser ler o chat primeiro antes da IA responder automaticamente. As notificações para o telemóvel serão desativadas.',
        'webhook_url'                   => 'URL WebHook (Opcional)',
        'webhook_url_placeholder'       => 'https://exemplo.com/webhook',
        'webhook_url_hint'              => 'URL webhook para receber notificações de mensagens recebidas (opcional)',
        
        // AI Configuration
        'chatbot_method'                => 'Método Chatbot',
        'chatbot_method_hint'           => 'Escolha o método de resposta automática utilizado',
        'method_all'                    => 'Todos (Manual + AI)',
        'method_chatbot'                => 'Chatbot (Manual)',
        'method_ai'                     => 'AI (Fine Tunnel)',
        'ai_training'                   => 'Treino AI',
        'ai_training_hint'              => 'Selecione os dados de treino AI para este dispositivo',
        'ai_training_full_hint'         => 'Selecione o dataset de treino AI para melhorar a precisão das respostas',
        'ai_training_placeholder'       => 'Selecionar Treino AI...',
        'select_ai_training'            => 'Selecionar Treino AI',
        'choose_ai_training'            => 'Escolher Treino AI',
        'auto_reply_option'             => 'Chatbot Ativo em',
        'auto_reply_option_hint'        => 'Determine onde o chatbot estará ativo para responder mensagens',
        'reply_all'                     => 'Todos (Pessoal e Grupo)',
        'reply_personal'                => 'Pessoal',
        'reply_group'                   => 'Grupo',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'Chatbot Inativo em Determinados Dias',
        'inactive_certain_day_hint'     => 'Ativar se quiser que o chatbot seja inativo em determinados dias',
        'select_days'                   => 'Selecionar Dias',
        'select_days_placeholder'       => 'Selecionar dias...',
        'day_monday'                    => 'Segunda',
        'day_tuesday'                   => 'Terça',
        'day_wednesday'                 => 'Quarta',
        'day_thursday'                  => 'Quinta',
        'day_friday'                    => 'Sexta',
        'day_saturday'                  => 'Sábado',
        'day_sunday'                    => 'Domingo',
        
        'inactive_certain_time'         => 'Chatbot Inativo em Determinadas Horas',
        'inactive_certain_time_hint'    => 'Ativar se quiser que o chatbot seja inativo em determinadas horas',
        'start_time'                    => 'Hora de Início Inativo',
        'start_time_hint'               => 'Hora em que o chatbot fica inativo',
        'end_time'                      => 'Hora de Fim Inativo',
        'end_time_hint'                 => 'Hora em que o chatbot termina de estar inativo',
        
        'daily_broadcast_limit'         => 'Limite Diário de Broadcast',
        'daily_broadcast_limit_hint'    => 'Ativar se o chatbot tiver limite de envio de mensagens por dia',
        'enter_daily_limit'             => 'Inserir Limite Diário',
        'daily_limit_placeholder'       => 'Exemplo: 100',
        'daily_limit_suffix'            => 'mensagens/dia',
        'daily_limit_hint'              => 'Número máximo de mensagens que podem ser enviadas por dia',
        
        // Actions
        'save_device'                   => 'Guardar Dispositivo',
        'update_device'                 => 'Atualizar Dispositivo',
        'cancel'                        => 'Cancelar',
        'required_fields'               => 'Campos com * são obrigatórios',
        
        // Messages
        'device_created'                => 'Dispositivo WhatsApp adicionado com sucesso',
        'device_updated'                => 'Dispositivo WhatsApp atualizado com sucesso',
        'device_deleted'                => 'Dispositivo WhatsApp eliminado com sucesso',
        
        // List/Index Page
        'add_connection'                => 'Adicionar Ligação WhatsApp',
        'total_device'                  => 'Total de Dispositivos',
        'not_connected'                 => 'Não Ligado',
        'device_connected'              => 'Dispositivo Ligado',
        'connection_list'               => 'Lista de Ligações WhatsApp',
        'broadcast_sent_today'          => 'Broadcasts Enviados Hoje',
        'daily_broadcast_limit_label'   => 'Limite de Broadcast Diário',
        'device_name_label'             => 'Nome do Dispositivo',
        'phone_number'                  => 'Número de Telefone',
        
        // Actions
        'scan_qr'                       => 'Digitalizar QR',
        'copy_id'                       => 'Copiar ID',
        'settings'                      => 'Definições',
        'edit_device'                   => 'Editar Dispositivo',
        'delete_device'                 => 'Eliminar Dispositivo',
        'copied_device_id'              => 'ID do Dispositivo copiado com sucesso',
        'search_device'                 => 'Procurar dispositivo...',
        
        // Status
        'status_active'                 => 'Ativo',
        'status_inactive'               => 'Inativo',
    ],
    // ===== TELEGRAM =====
    'telegram' => [
        // Section Titles
        'device_info'                   => 'Informações do Dispositivo Telegram',
        'ai_config'                     => 'Configuração do Agente AI',
        'schedule_limits'               => 'Horários e Limites',
        'integrated_telegram_list'      => 'Lista de Telegram Integrados',
        'add_telegram'                  => 'Adicionar Telegram',
        'edit_telegram'                 => 'Editar Telegram',
        
        // Device Information
        'device_name'                   => 'Nome do Dispositivo',
        'device_name_placeholder'       => 'Exemplo: Bot Atendimento ao Cliente',
        'device_name_hint'              => 'Nome para identificar este bot Telegram',
        'bot_token'                     => 'Token do Bot',
        'bot_token_placeholder'         => 'Inserir token do bot do @BotFather',
        'bot_token_hint'                => 'Token do bot obtido do @BotFather no Telegram',
        'team_member'                   => 'Membro da Equipa',
        'team_member_hint'              => 'Selecione o membro da equipa que gerenciará este bot',
        'team_member_placeholder'       => 'Selecionar membro da equipa...',
        
        // AI Configuration
        'auto_reply_method'             => 'Método AutoReply',
        'auto_reply_method_hint'        => 'Escolha o método de resposta automática utilizado',
        'method_all'                    => 'Todos (Manual + AI)',
        'method_chatbot'                => 'ChatBot (Manual)',
        'method_ai'                     => 'AI (Fine Tunnel)',
        'ai_training'                   => 'Treino AI',
        'ai_training_hint'              => 'Selecione os dados de treino AI para este bot',
        'ai_training_placeholder'       => 'Selecionar Treino AI...',
        'choose_ai_training'            => 'Escolher Treino AI',
        
        // Status & Options
        'status'                        => 'Estado',
        'status_hint'                   => 'Estado ativo/inativo do bot Telegram',
        'status_active'                 => 'Ativo',
        'status_inactive'               => 'Inativo',
        'auto_reply_option'             => 'Bot Ativo em',
        'auto_reply_option_hint'        => 'Determine onde o bot estará ativo para responder mensagens',
        'reply_all'                     => 'Todos (Pessoal e Grupo)',
        'reply_personal'                => 'Pessoal',
        'reply_group'                   => 'Grupo',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'Bot Inativo em Determinados Dias',
        'inactive_certain_day_hint'     => 'Ativar se quiser que o bot seja inativo em determinados dias',
        'inactive_certain_day_no'       => 'Não, Ativo Todos os Dias',
        'inactive_certain_day_yes'      => 'Sim, Inativo em Determinados Dias',
        'select_days'                   => 'Selecionar Dias',
        'select_days_placeholder'       => 'Selecionar dias...',
        'day_monday'                    => 'Segunda',
        'day_tuesday'                   => 'Terça',
        'day_wednesday'                 => 'Quarta',
        'day_thursday'                  => 'Quinta',
        'day_friday'                    => 'Sexta',
        'day_saturday'                  => 'Sábado',
        'day_sunday'                    => 'Domingo',
        
        'inactive_certain_time'         => 'Bot Inativo em Determinadas Horas',
        'inactive_certain_time_hint'    => 'Ativar se quiser que o bot seja inativo em determinadas horas',
        'inactive_certain_time_no'      => 'Não, Ativo 24 Horas',
        'inactive_certain_time_yes'     => 'Sim, Inativo em Determinadas Horas',
        'start_time'                    => 'Hora de Início Inativo',
        'start_time_hint'               => 'Hora em que o bot fica inativo',
        'end_time'                      => 'Hora de Fim Inativo',
        'end_time_hint'                 => 'Hora em que o bot termina de estar inativo',
        
        'daily_limit'                   => 'Limite Diário',
        'daily_limit_hint'              => 'Ativar se o bot tiver limite de envio de mensagens por dia',
        'daily_limit_no'                => 'Sem Limite',
        'daily_limit_yes'               => 'Com Limite Diário',
        'enter_daily_limit'             => 'Inserir Limite Diário',
        'daily_limit_placeholder'       => 'Exemplo: 1000',
        'daily_limit_suffix'            => 'mensagens/dia',
        'daily_limit_hint_input'        => 'Número máximo de mensagens que podem ser enviadas por dia',
        
        // Actions
        'back_to_list'                  => 'Voltar à Página do Telegram',
        'add_device'                    => 'Adicionar Dispositivo',
        'save_device'                   => 'Guardar Dispositivo',
        'update_device'                 => 'Atualizar Dispositivo',
        'cancel'                        => 'Cancelar',
        'required_fields'               => 'Campos com * são obrigatórios',
        
        // Messages
        'device_created'                => 'Bot Telegram adicionado com sucesso',
        'device_updated'                => 'Bot Telegram atualizado com sucesso',
        'device_deleted'                => 'Bot Telegram eliminado com sucesso',
        
        // List/Index Page
        'add_connection'                => 'Adicionar Ligação Telegram',
        'total_bot'                     => 'Total Telegram',
        'not_connected'                 => 'Não Ligado',
        'bot_connected'                 => 'Telegram Ligado',
        'connection_list'               => 'Lista de Ligações Telegram',
        'bot_name'                      => 'Nome do Bot',
        'broadcast_sent_today'          => 'Broadcasts Enviados Hoje',
        'daily_broadcast_limit_label'   => 'Limite de Broadcast Diário',
        
        // Actions List
        'copy_id'                       => 'Copiar ID',
        'edit_bot'                      => 'Editar Bot',
        'delete_bot'                    => 'Eliminar Bot',
        'copied_bot_id'                 => 'ID do Bot copiado com sucesso',
        'search_bot'                    => 'Procurar bot telegram...',
    ],
    
    // ===== FACEBOOK =====
    'facebook' => [
        'add_account'                   => 'Adicionar Conta',
        'account_list'                  => 'Lista de Contas do Facebook',
        'account_connected'             => 'Conta do Facebook conectada com sucesso.',
        'login_failed'                  => 'Falha ao fazer login no Facebook: ',
    ],

    // ===== INSTAGRAM =====
    'instagram' => [
        'add_account'                   => 'Adicionar Conta',
        'account_list'                  => 'Lista de Contas do Instagram',
        'account_connected'             => 'Conta do Instagram conectada com sucesso.',
        'login_failed'                  => 'Falha ao fazer login no Instagram: ',
    ],

];