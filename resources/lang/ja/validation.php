<?php

return [
    'password_must_same'            => 'パスワードとパスワード確認は一致する必要があります。',
    'numeric'                       => ':attribute フィールドは数字でなければなりません。',
    'password' => [
        'letters'   => ':attribute フィールドは少なくとも1文字の文字を含む必要があります。',
        'mixed'     => ':attribute フィールドは少なくとも1つの大文字と1つの小文字を含む必要があります。',
        'numbers'   => ':attribute フィールドは少なくとも1つの数字を含む必要があります。',
        'symbols'   => ':attribute フィールドは少なくとも1つの記号を含む必要があります。',
        'uncompromised' => '提供された :attribute はデータ漏洩に現れました。別の :attribute を選択してください。',
    ],
    'present'                       => ':attribute フィールドは存在する必要があります。',
    'prohibited'                    => ':attribute フィールドは禁止されています。',
    'prohibited_if'                 => ':other が :value の場合、:attribute フィールドは禁止されています。',
    'prohibited_unless'             => ':other が :values にない限り、:attribute フィールドは禁止されています。',
    'prohibits'                     => ':attribute フィールドは :other が存在することを禁止します。',
    'regex'                         => ':attribute の形式は無効です。',
    'required'                      => ':attribute フィールドは必須です。',
    'required_array_keys'           => ':attribute フィールドには :values のエントリが含まれている必要があります。',
    'required_if'                   => ':other が :value の場合、:attribute フィールドは必須です。',
    'required_if_accepted'          => ':other が承認された場合、:attribute フィールドは必須です。',
    'required_unless'               => ':other が :values に含まれていない限り、:attribute フィールドは必須です。',
    'required_with'                 => ':values が存在する場合、:attribute フィールドは必須です。',
    'required_with_all'             => ':values がすべて存在する場合、:attribute フィールドは必須です。',
    'required_without'              => ':values が存在しない場合、:attribute フィールドは必須です。',
    'required_without_all'          => ':values がすべて存在しない場合、:attribute フィールドは必須です。',
    'same'                          => ':attribute と :other は一致する必要があります。',
    'size' => [
        'array'   => ':attribute フィールドには :size アイテムが含まれている必要があります。',
        'file'    => ':attribute ファイルのサイズは :size キロバイトでなければなりません。',
        'numeric' => ':attribute フィールドのサイズは :size でなければなりません。',
        'string'  => ':attribute フィールドのサイズは :size 文字でなければなりません。',
    ],
    'starts_with'                  => ':attribute フィールドは次のいずれかで始まる必要があります: :values。',
    'string'                       => ':attribute フィールドは文字列でなければなりません。',
    'timezone'                     => ':attribute フィールドは有効なタイムゾーンでなければなりません。',
    'device_limit'          => '申し訳ありませんが、作成可能なデバイスの上限に達しました。サービス提供者に連絡してください',
    'chatbot_limit'         => '申し訳ありませんが、作成可能なチャットボットの上限に達しました。サービス提供者に連絡してください',
    'finetunnel_read_doc'   => 'エラーが発生しました。ドキュメントを読み取ることができません',
    'template_limit'        => '申し訳ありませんが、作成可能なテンプレートの上限に達しました。サービス提供者に連絡してください'

];
