<?php

return [
    'password_must_same'            => 'A senha e a confirmação da senha devem corresponder.',
    'numeric'                       => 'O campo :attribute deve ser um número.',
    'password' => [
        'letters'   => 'O campo :attribute deve conter pelo menos uma letra.',
        'mixed'     => 'O campo :attribute deve conter pelo menos uma letra maiúscula e uma letra minúscula.',
        'numbers'   => 'O campo :attribute deve conter pelo menos um número.',
        'symbols'   => 'O campo :attribute deve conter pelo menos um símbolo.',
        'uncompromised' => ':attribute fornecido apareceu em uma violação de dados. Escolha um :attribute diferente.',
    ],
    'present'                       => 'O campo :attribute deve estar presente.',
    'prohibited'                    => 'O campo :attribute é proibido.',
    'prohibited_if'                 => 'O campo :attribute é proibido quando :other é :value.',
    'prohibited_unless'             => 'O campo :attribute é proibido a menos que :other esteja em :values.',
    'prohibits'                     => 'O campo :attribute proíbe :other de estar presente.',
    'regex'                         => 'O formato do :attribute é inválido.',
    'required'                      => 'O campo :attribute é obrigatório.',
    'required_array_keys'           => 'O campo :attribute deve conter entradas para: :values.',
    'required_if'                   => 'O campo :attribute é obrigatório quando :other é :value.',
    'required_if_accepted'          => 'O campo :attribute é obrigatório quando :other é aceito.',
    'required_unless'               => 'O campo :attribute é obrigatório a menos que :other esteja em :values.',
    'required_with'                 => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_with_all'             => 'O campo :attribute é obrigatório quando todos os :values estão presentes.',
    'required_without'              => 'O campo :attribute é obrigatório quando :values não está presente.',
    'required_without_all'          => 'O campo :attribute é obrigatório quando nenhum dos :values está presente.',
    'same'                          => 'O campo :attribute deve corresponder a :other.',
    'size' => [
        'array'   => 'O campo :attribute deve conter :size itens.',
        'file'    => 'O arquivo :attribute deve ter :size kilobytes.',
        'numeric' => 'O campo :attribute deve ter :size.',
        'string'  => 'O campo :attribute deve ter :size caracteres.',
    ],
    'starts_with'                  => 'O campo :attribute deve começar com um dos seguintes: :values.',
    'string'                       => 'O campo :attribute deve ser uma string.',
    'timezone'                     => 'O campo :attribute deve ser um fuso horário válido.',
    'device_limit'          => 'Desculpe, você atingiu o limite de dispositivos que podem ser criados. Entre em contato com o provedor de serviços',
    'chatbot_limit'         => 'Desculpe, você atingiu o limite de chatbots que podem ser criados. Entre em contato com o provedor de serviços',
    'finetunnel_read_doc'   => 'Ocorreu um erro, não foi possível ler o documento',
    'template_limit'        => 'Desculpe, você atingiu o limite de templates que podem ser criados. Entre em contato com o provedor de serviços'

];
