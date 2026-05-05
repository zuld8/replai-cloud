<?php

return [
    // Sidebar
    'sidebar' => [
        'welcome' => 'CRMへようこそ',
        'select_contact' => 'サイドバーから連絡先を選択して会話を開始してください',
        'back_to_dashboard' => 'ダッシュボードに戻る',
        'manage_chat' => 'チャット管理',
        'realtime_chat' => 'リアルタイムチャット',
        'manage_contact' => '連絡先データベースの管理',
        'use_sidebar_info' => '左側のサイドバーを使用して会話リストを表示します',
        'new_conversation' => '新しい会話を開始',
        'all_channel' => 'すべてのチャネル',
        'all_device' => 'すべてのデバイス',
        'search_message' => 'メッセージを検索',
        'no_conversation' => '会話がありません',
    ],

    // Chat Room
    'chat' => [
        'bot_on' => 'ボット ON',
        'bot_off' => 'ボット OFF',
        'takeover_conversation' => '会話を引き継ぐ',
        'type_message' => 'メッセージを入力...',
        'quick_reply_hint' => '（「/」キーでクイック返信）',
        'reply_to' => '返信先:',
        'delete_message_confirm' => 'このメッセージを削除しますか？',
        'send' => '送信',
        'sending' => '送信中...',
    ],

    // Contact Modal
    'contact' => [
        'select_contact' => '連絡先を選択',
        'search_contact' => '連絡先を検索...',
        'contact_not_found' => '連絡先が見つかりません',
        'add_new_contact'   => '新しい連絡先を追加',
        'name'  => '氏名',
        'device'    => 'デバイス',
        'name_placeholder'   => '氏名を入力してください',
        'select_device'     => 'デバイスを選択',
        'new_chat' => '新しいチャット',
        'from_contacts' => '連絡先から選択',
        'by_phone' => '電話番号で',
        'phone_number' => '電話番号',
        'phone_placeholder' => '例: 628123456789',
        'phone_hint' => '国番号付きで入力してください（例: 628xxx）',
        'start_chat' => 'チャットを開始',
        'phone_required' => '電話番号は必須です！',
    ],

    // Filter
    'filter' => [
        'filter_data' => 'データをフィルタ',
        'start_date' => '開始日',
        'end_date' => '終了日',
        'chat_status' => 'チャットステータス',
        'platform' => 'プラットフォーム',
        'all' => 'すべて',
        'apply_filter' => 'フィルタを適用',
        'reset_filter' => 'リセット',
    ],

    // Status
    'status' => [
        'open' => 'オープン',
        'resolved' => '解決済み',
        'block' => 'ブロック',
    ],

    // Platform/Channel
    'channel' => [
        'whatsapp' => 'WhatsApp',
        'waba' => 'WhatsAppビジネス',
        'livechat' => 'ライブチャット',
        'telegram' => 'Telegram',
        'instagram' => 'Instagram',
        'messenger' => 'Messenger',
    ],

    // Right Sidebar (Info)
    'info' => [
        'information' => '情報',
        'handle_by' => '担当者',
        'collaborator' => '共同担当者',
        'add_team' => '+ チームを追加',
        'select_collaborator' => '共同担当者を選択',
        'label' => 'ラベル',
        'add_label' => '+ ラベル追加',
        'select_label' => 'ラベルを選択',
        'additional_data' => '追加データ',
        'add_data' => '+ データ追加',
        'no_additional_data' => '追加データはありません',
        'notes' => 'メモ',
        'detail_data' => '詳細データ',
        'assigned_by' => '割り当て者',
        'handled_by' => '対応者',
        'resolved_by' => '解決者',
        'created_at' => '作成日時',
        'resolved_at' => '解決日時',
        'block_user' => 'このユーザーをブロック',
        'unblock_user' => 'ブロック解除',
    ],

    // Quick Reply
    'quick_reply' => [
        'title' => 'クイック返信',
        'manage' => 'クイック返信を管理',
        'add' => 'クイック返信を追加',
        'edit' => '編集',
        'name' => '名前',
        'content' => '内容',
        'file_optional' => 'ファイル（任意）',
        'save' => '保存',
        'saving' => '保存中...',
        'delete_confirm' => 'このクイック返信を削除しますか？',
        'action' => '操作',
    ],

    // Additional Data Modal
    'additional' => [
        'add_field' => '追加データを追加',
        'field_name' => 'フィールド名',
        'field_type' => 'タイプ',
        'type_text' => 'テキスト',
        'type_number' => '数値',
        'type_date' => '日付',
        'type_options' => 'オプション',
        'options_hint' => 'オプション（カンマ区切り）',
        'options_placeholder' => 'オプション1, オプション2, オプション3',
    ],

    // File Upload
    'file' => [
        'preview' => 'ファイルプレビュー',
        'add_caption' => 'キャプションを追加...',
    ],

    // Common
    'common' => [
        'loading' => '読み込み中...',
        'delete' => '削除',
        'reply' => '返信',
        'close' => '閉じる',
        'save' => '保存',
        'cancel' => 'キャンセル',
        'copy' => 'コピー',
        'copied' => 'コピーしました！',
        'phone_copied' => '電話番号をコピーしました！',
    ],

    // Messages
    'message' => [
        'delete_chat_confirm' => 'この会話を削除しますか？',
    ],
];
