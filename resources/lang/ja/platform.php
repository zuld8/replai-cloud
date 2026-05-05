<?php

return [
    // ===== WHATSAPP DEVICE =====
    'whatsapp' => [
        // Section Titles
        'device_info'                   => 'デバイス情報',
        'ai_config'                     => 'AIエージェント設定',
        'schedule_limits'               => 'スケジュール・制限',
        
        // Device Information
        'device_name'                   => 'デバイス名',
        'device_name_placeholder'       => '例：管理者デバイス',
        'device_name_hint'              => 'このWhatsAppデバイスを識別するための名前',
        'wa_number'                     => 'WhatsApp番号',
        'wa_number_placeholder'         => '+81 xxx xxx xxx',
        'wa_number_hint'                => 'システムに接続するWhatsApp番号（形式：+81）',
        'team_member'                   => 'チームメンバー',
        'team_member_hint'              => 'このデバイスを管理するチームメンバーを選択',
        'team_member_placeholder'       => 'チームメンバーを選択...',
        
        // Device Settings
        'device_notification'           => 'デバイス通知',
        'device_notification_hint'      => 'デバイスで通知を受信したくない場合は無効にしてください',
        'save_chat_history'             => 'メッセージ履歴を保存',
        'save_chat_history_hint'        => '履歴は日次リセットなしで自動保存されます',
        'auto_read_before_reply'        => 'チャットボットが返信する前にチャットを読む',
        'auto_read_before_reply_hint'   => 'AIが自動返信する前にチャットを読みたい場合は有効にしてください。スマートフォンへの通知は無効になります。',
        'webhook_url'                   => 'WebHook URL（オプション）',
        'webhook_url_placeholder'       => 'https://example.com/webhook',
        'webhook_url_hint'              => '受信メッセージ通知を受け取るためのwebhook URL（オプション）',
        
        // AI Configuration
        'chatbot_method'                => 'チャットボット方式',
        'chatbot_method_hint'           => '使用する自動返信方式を選択',
        'method_all'                    => 'すべて（手動 + AI）',
        'method_chatbot'                => 'チャットボット（手動）',
        'method_ai'                     => 'AI（ファイントンネル）',
        'ai_training'                   => 'AIトレーニング',
        'ai_training_hint'              => 'このデバイス用のAIトレーニングデータを選択',
        'ai_training_full_hint'         => '回答精度向上のためのAIトレーニングデータセットを選択',
        'ai_training_placeholder'       => 'AIトレーニングを選択...',
        'select_ai_training'            => 'AIトレーニングを選択',
        'choose_ai_training'            => 'AIトレーニングを選択',
        'auto_reply_option'             => 'チャットボット有効範囲',
        'auto_reply_option_hint'        => 'チャットボットがメッセージに返信する範囲を決定',
        'reply_all'                     => 'すべて（個人・グループ）',
        'reply_personal'                => '個人',
        'reply_group'                   => 'グループ',
        
        // Schedule & Limits
        'inactive_certain_day'          => '特定日にチャットボットを無効',
        'inactive_certain_day_hint'     => '特定の日にチャットボットを無効にしたい場合は有効にしてください',
        'select_days'                   => '日を選択',
        'select_days_placeholder'       => '日を選択...',
        'day_monday'                    => '月曜日',
        'day_tuesday'                   => '火曜日',
        'day_wednesday'                 => '水曜日',
        'day_thursday'                  => '木曜日',
        'day_friday'                    => '金曜日',
        'day_saturday'                  => '土曜日',
        'day_sunday'                    => '日曜日',
        
        'inactive_certain_time'         => '特定時間にチャットボットを無効',
        'inactive_certain_time_hint'    => '特定の時間にチャットボットを無効にしたい場合は有効にしてください',
        'start_time'                    => '無効開始時刻',
        'start_time_hint'               => 'チャットボットが無効になる開始時刻',
        'end_time'                      => '無効終了時刻',
        'end_time_hint'                 => 'チャットボットが無効になる終了時刻',
        
        'daily_broadcast_limit'         => '日次ブロードキャスト制限',
        'daily_broadcast_limit_hint'    => 'チャットボットが1日あたりのメッセージ送信制限を持つ場合は有効にしてください',
        'enter_daily_limit'             => '日次制限を入力',
        'daily_limit_placeholder'       => '例：100',
        'daily_limit_suffix'            => 'メッセージ/日',
        'daily_limit_hint'              => '1日に送信できるメッセージの最大数',
        
        // Actions
        'save_device'                   => 'デバイスを保存',
        'update_device'                 => 'デバイスを更新',
        'cancel'                        => 'キャンセル',
        'required_fields'               => '*印のフィールドは必須入力です',
        
        // Messages
        'device_created'                => 'WhatsAppデバイスが正常に追加されました',
        'device_updated'                => 'WhatsAppデバイスが正常に更新されました',
        'device_deleted'                => 'WhatsAppデバイスが正常に削除されました',
        
        // List/Index Page
        'add_connection'                => 'WhatsApp接続を追加',
        'total_device'                  => '総デバイス数',
        'not_connected'                 => '未接続',
        'device_connected'              => 'デバイス接続済み',
        'connection_list'               => 'WhatsApp接続リスト',
        'broadcast_sent_today'          => '本日のブロードキャスト送信',
        'daily_broadcast_limit_label'   => '日次ブロードキャスト制限',
        'device_name_label'             => 'デバイス名',
        'phone_number'                  => '電話番号',
        
        // Actions
        'scan_qr'                       => 'QRスキャン',
        'copy_id'                       => 'IDをコピー',
        'settings'                      => '設定',
        'edit_device'                   => 'デバイス編集',
        'delete_device'                 => 'デバイス削除',
        'copied_device_id'              => 'デバイスIDが正常にコピーされました',
        'search_device'                 => 'デバイスを検索...',
        
        // Status
        'status_active'                 => 'アクティブ',
        'status_inactive'               => '非アクティブ',
    ],
    // ===== TELEGRAM =====
    'telegram' => [
        // Section Titles
        'device_info'                   => 'Telegramデバイス情報',
        'ai_config'                     => 'AIエージェント設定',
        'schedule_limits'               => 'スケジュール・制限',
        'integrated_telegram_list'      => '統合されたTelegramリスト',
        'add_telegram'                  => 'Telegramを追加',
        'edit_telegram'                 => 'Telegramを編集',
        
        // Device Information
        'device_name'                   => 'デバイス名',
        'device_name_placeholder'       => '例：カスタマーサービスボット',
        'device_name_hint'              => 'このTelegramボットを識別するための名前',
        'bot_token'                     => 'ボットトークン',
        'bot_token_placeholder'         => '@BotFatherからのボットトークンを入力',
        'bot_token_hint'                => 'Telegramの@BotFatherから取得したボットトークン',
        'team_member'                   => 'チームメンバー',
        'team_member_hint'              => 'このボットを管理するチームメンバーを選択',
        'team_member_placeholder'       => 'チームメンバーを選択...',
        
        // AI Configuration
        'auto_reply_method'             => '自動返信方式',
        'auto_reply_method_hint'        => '使用する自動返信方式を選択',
        'method_all'                    => 'すべて（手動 + AI）',
        'method_chatbot'                => 'チャットボット（手動）',
        'method_ai'                     => 'AI（ファイントンネル）',
        'ai_training'                   => 'AIトレーニング',
        'ai_training_hint'              => 'このボット用のAIトレーニングデータを選択',
        'ai_training_placeholder'       => 'AIトレーニングを選択...',
        'choose_ai_training'            => 'AIトレーニングを選択',
        
        // Status & Options
        'status'                        => 'ステータス',
        'status_hint'                   => 'Telegramボットのアクティブ/非アクティブステータス',
        'status_active'                 => 'アクティブ',
        'status_inactive'               => '非アクティブ',
        'auto_reply_option'             => 'ボット有効範囲',
        'auto_reply_option_hint'        => 'ボットがメッセージに返信する範囲を決定',
        'reply_all'                     => 'すべて（個人・グループ）',
        'reply_personal'                => '個人',
        'reply_group'                   => 'グループ',
        
        // Schedule & Limits
        'inactive_certain_day'          => '特定日にボットを無効',
        'inactive_certain_day_hint'     => '特定の日にボットを無効にしたい場合は有効にしてください',
        'inactive_certain_day_no'       => 'いいえ、毎日アクティブ',
        'inactive_certain_day_yes'      => 'はい、特定日に無効',
        'select_days'                   => '日を選択',
        'select_days_placeholder'       => '日を選択...',
        'day_monday'                    => '月曜日',
        'day_tuesday'                   => '火曜日',
        'day_wednesday'                 => '水曜日',
        'day_thursday'                  => '木曜日',
        'day_friday'                    => '金曜日',
        'day_saturday'                  => '土曜日',
        'day_sunday'                    => '日曜日',
        
        'inactive_certain_time'         => '特定時間にボットを無効',
        'inactive_certain_time_hint'    => '特定の時間にボットを無効にしたい場合は有効にしてください',
        'inactive_certain_time_no'      => 'いいえ、24時間アクティブ',
        'inactive_certain_time_yes'     => 'はい、特定時間に無効',
        'start_time'                    => '無効開始時刻',
        'start_time_hint'               => 'ボットが無効になる開始時刻',
        'end_time'                      => '無効終了時刻',
        'end_time_hint'                 => 'ボットが無効になる終了時刻',
        
        'daily_limit'                   => '日次制限',
        'daily_limit_hint'              => 'ボットが1日あたりのメッセージ送信制限を持つ場合は有効にしてください',
        'daily_limit_no'                => '制限なし',
        'daily_limit_yes'               => '日次制限あり',
        'enter_daily_limit'             => '日次制限を入力',
        'daily_limit_placeholder'       => '例：1000',
        'daily_limit_suffix'            => 'メッセージ/日',
        'daily_limit_hint_input'        => '1日に送信できるメッセージの最大数',
        
        // Actions
        'back_to_list'                  => 'Telegramページに戻る',
        'add_device'                    => 'デバイスを追加',
        'save_device'                   => 'デバイスを保存',
        'update_device'                 => 'デバイスを更新',
        'cancel'                        => 'キャンセル',
        'required_fields'               => '*印のフィールドは必須入力です',
        
        // Messages
        'device_created'                => 'Telegramボットが正常に追加されました',
        'device_updated'                => 'Telegramボットが正常に更新されました',
        'device_deleted'                => 'Telegramボットが正常に削除されました',
        
        // List/Index Page
        'add_connection'                => 'Telegram接続を追加',
        'total_bot'                     => '総Telegram数',
        'not_connected'                 => '未接続',
        'bot_connected'                 => 'Telegram接続済み',
        'connection_list'               => 'Telegram接続リスト',
        'bot_name'                      => 'ボット名',
        'broadcast_sent_today'          => '本日のブロードキャスト送信',
        'daily_broadcast_limit_label'   => '日次ブロードキャスト制限',
        
        // Actions List
        'copy_id'                       => 'IDをコピー',
        'edit_bot'                      => 'ボット編集',
        'delete_bot'                    => 'ボット削除',
        'copied_bot_id'                 => 'ボットIDが正常にコピーされました',
        'search_bot'                    => 'telegramボットを検索...',
    ],
    'facebook' => [
        'add_account'                   => 'アカウントを追加',
        'account_list'                  => 'Facebookアカウントリスト',
        'account_connected'             => 'Facebookアカウントが正常に接続されました。',
        'login_failed'                  => 'Facebookログインに失敗しました：',
    ],
    'instagram' => [
        'add_account'                   => 'アカウントを追加',
        'account_list'                  => 'Instagramアカウントリスト',
        'account_connected'             => 'Instagramアカウントが正常に接続されました。',
        'login_failed'                  => 'Instagramログインに失敗しました：',
    ],
];