<?php

return [
    'wa' => [
        // Index/List Page
        'create_schedule_button' => 'ブロードキャストのスケジュールを作成',
        'wa_group' => 'WhatsAppグループ',
        'page_title' => 'WhatsAppブロードキャスト一覧',
        
        // Card Headers
        'broadcast_info' => 'ブロードキャスト情報',
        'target_template' => 'ターゲット＆テンプレート',
        
        // Form Labels
        'name' => 'ブロードキャストタイトル',
        'schedule' => '送信スケジュール',
        'template' => 'メッセージテンプレート',
        'devices' => 'デバイス',
        'device_option' => 'デバイス使用オプション',
        'delay' => '送信間隔（ディレイ）',
        'stop_sending' => '送信停止条件',
        'rest_sending' => '休止時間',
        'category' => 'ビジネスカテゴリ',
        'whatsapp_group' => 'WhatsAppグループ',
        'location_target' => 'ターゲット地域',
        
        // Placeholders
        'name_placeholder' => '例: 2024年 年末セールプロモーション',
        'choose_group' => 'WhatsAppグループを選択',
        'choose_device' => 'WhatsAppデバイスを選択',
        
        // Device Options
        'device_sequence' => '単一デバイス（順番に送信）',
        'device_spin' => 'AI自動選択（スピン）',
        'device_random' => 'ランダム（無作為）',
        
        // Units
        'seconds' => '秒',
        'numbers' => '番号',
        
        // Helper Texts
        'name_help' => '識別用のブロードキャストキャンペーン名',
        'schedule_help' => 'メッセージ送信のスケジュールを設定',
        'template_help' => '送信するメッセージテンプレートを選択',
        'devices_help' => '使用するWhatsAppデバイスを選択',
        'device_option_help' => '送信に使用するデバイスの選択方法',
        'delay_help' => '推奨：30〜300秒。短すぎるとブロックされる可能性があります。',
        'stop_sending_help' => '指定した件数送信後に停止',
        'rest_sending_help' => '一定件数送信後の休止時間',
        'category_help' => 'ビジネスカテゴリでターゲットを絞り込み',
        'whatsapp_group_help' => '特定のWhatsAppグループの連絡先をターゲットに',
        'province_help' => '都道府県でターゲットを絞り込み',
        'city_help' => '市区町村でターゲットを絞り込み',
        'district_help' => '地区でターゲットを絞り込み',
        
        // Badges & Labels
        'optional' => '任意',
        'required_field' => '必須項目',
        
        // Alert & Tips
        'safe_sending_tips' => '安全な送信のヒント：',
        'tip_delay' => 'ブロックを避けるために最小30秒のディレイを設定しましょう。',
        'tip_batch' => '1バッチあたり50〜100通を上限に設定してください。',
        'tip_rest' => '各バッチ間に十分な休止時間を設けましょう。',
        'tip_multiple' => '複数デバイスを使用して負荷分散を行うのがおすすめです。',
    ],
    
    'email' => [
        // Form Fields
        'category' => 'ビジネスカテゴリ',
        'template' => 'メールテンプレート',
        'name' => 'ブロードキャストタイトル',
        'schedule' => '送信スケジュール',
        
        // Placeholders
        'name_placeholder' => '例: 2024年10月 月刊ニュースレター',
        
        // Helper Texts
        'category_help' => 'ビジネスカテゴリで受信者を絞り込み',
        'template_help' => '送信するメールテンプレートを選択',
        'name_help' => '識別用のブロードキャストキャンペーン名',
        'schedule_help' => 'メール送信のスケジュールを設定',
        
        // Badges & Labels
        'optional' => '任意',
        'required_field' => '必須項目',
        
        // Alert & Tips
        'email_sending_tips' => 'メール送信のヒント：',
        'tip_test_template' => '送信前に必ずテンプレートをテストしてください。',
        'tip_optimal_time' => 'エンゲージメントが高い時間帯に送信しましょう。',
        'tip_use_category' => 'カテゴリを使ってターゲットを明確化しましょう。',
        'tip_check_spam' => 'スパム扱いされないよう内容を確認してください。',
    ],
    
    'upselling' => [
        // List/Index Page
        'page_title' => 'アップセルキャンペーン一覧',
        'create_campaign_button' => '新しいキャンペーンを作成',
        'refresh_button' => '更新',
        
        // Table Headers
        'campaign_info' => 'キャンペーン情報',
        'schedule_frequency' => 'スケジュール＆頻度',
        'target_category' => 'ターゲット＆カテゴリ',
        'method_template' => 'メソッド＆テンプレート',
        'status' => 'ステータス',
        'action' => '操作',
        
        // Filter Options
        'all_status' => 'すべてのステータス',
        'status_active' => '有効',
        'status_inactive' => '無効',
        'status_scheduled' => 'スケジュール済み',
        'status_completed' => '完了',
        'all_frequency' => 'すべての頻度',
        'frequency_once' => '1回のみ',
        'frequency_daily' => '毎日',
        'frequency_monthly' => '毎月',
        'frequency_yearly' => '毎年',
        
        // DataTable Language
        'search_placeholder' => 'キャンペーンを検索...',
        'length_menu' => '1ページあたり _MENU_ 件を表示',
        'info' => '_TOTAL_ 件中 _START_ から _END_ 件を表示',
        'info_empty' => 'キャンペーンがありません',
        'info_filtered' => '（全 _MAX_ 件からフィルタリング）',
        'paginate_first' => '最初',
        'paginate_last' => '最後',
        'paginate_next' => '次へ',
        'paginate_previous' => '前へ',
        
        // DataTable Content
        'devices_count' => 'デバイス',
        'delay_label' => 'ディレイ',
        'once_send' => '1回のみ送信',
        'date_prefix' => '日付',
        'every_month' => '毎月',
        'every_year' => '毎年',
        'ongoing' => '進行中',
        'all_categories' => 'すべてのカテゴリ',
        'labels_count' => 'ラベル',
        'template_label' => 'テンプレート',
        'ai_generated' => 'AI生成',
        
        // Day Short Labels
        'day_monday_short' => '月',
        'day_tuesday_short' => '火',
        'day_wednesday_short' => '水',
        'day_thursday_short' => '木',
        'day_friday_short' => '金',
        'day_saturday_short' => '土',
        'day_sunday_short' => '日',
        
        // Month Short Labels
        'month_jan' => '1月',
        'month_feb' => '2月',
        'month_mar' => '3月',
        'month_apr' => '4月',
        'month_may' => '5月',
        'month_jun' => '6月',
        'month_jul' => '7月',
        'month_aug' => '8月',
        'month_sep' => '9月',
        'month_oct' => '10月',
        'month_nov' => '11月',
        'month_dec' => '12月',
        
        // Action Button Tooltips
        'btn_view_detail' => '詳細を見る',
        'btn_edit_campaign' => 'キャンペーンを編集',
        'btn_duplicate_campaign' => 'キャンペーンを複製',
        'btn_delete_campaign' => 'キャンペーンを削除',
        
        // Confirmation Messages
        'confirm_activate' => 'このキャンペーンを有効化してもよろしいですか？',
        'confirm_deactivate' => 'このキャンペーンを無効化してもよろしいですか？',
        'confirm_delete' => 'このキャンペーンを削除しますか？この操作は取り消せません。',
        
        // Success Messages
        'success_title' => '成功',
        'success_delete' => 'キャンペーンを削除しました。',
        
        // Error Messages
        'error_title' => 'エラー',
        'error_status_change' => 'ステータス変更中にエラーが発生しました。',
        'error_delete' => '削除中にエラーが発生しました。',
        
        // Back Button
        'back_to_campaign' => 'アップセルキャンペーン一覧に戻る',
        
        // Card Headers (Form)
        'basic_info' => 'キャンペーン基本情報',
        'schedule_config' => '送信スケジュール＆メッセージ設定',
        
        // Form Fields
        'campaign_title' => 'アップセルキャンペーンタイトル',
        'delay' => '送信間隔（ディレイ）',
        'devices' => 'デバイス',
        'device_option' => 'デバイス使用オプション',
        'contact_category' => '連絡先カテゴリ',
        'contact_labels' => '連絡先ラベル',
        'schedule_frequency' => '送信頻度',
        'select_days' => '曜日を選択',
        'date_in_month' => '月内の日付',
        'specific_date' => '特定日付',
        'sending_time' => '送信時間',
        'start_date' => '開始日',
        'end_date' => '終了日',
        'broadcast_method' => '使用メソッド',
        'ai_prompt' => 'AIプロンプト',
        'template_message' => 'メッセージテンプレート',
        
        // Placeholders
        'name_placeholder' => '例: 年末セールアップセルキャンペーン',
        'ai_prompt_placeholder' => '例: 新製品のアップセルメッセージをフレンドリーかつ魅力的に作成してください...',
        'select_category' => 'カテゴリを選択...',
        'select_template' => 'テンプレートを選択...',
        'select_device' => 'デバイスを選択...',
        'select_day' => '曜日を選択...',
        
        // Helper Texts
        'name_help' => '社内識別用のキャンペーン名',
        'delay_help' => 'メッセージ送信間の時間',
        'devices_help' => '送信に使用するデバイスを選択',
        'device_option_help' => 'デバイス選択方法を設定',
        'category_help' => '特定のカテゴリで連絡先を絞り込み',
        'labels_help' => '送信対象のラベルを選択（複数可）',
        'frequency_help' => 'メッセージ送信頻度を設定',
        'days_help' => '送信曜日を選択',
        'date_help' => '月内の日付を選択',
        'yearly_help' => '送信月と日を選択（年単位）',
        'time_help' => 'メッセージ送信時間を指定',
        'start_date_help' => 'キャンペーン開始日',
        'end_date_help' => '終了日がない場合は空欄でOK',
        'method_help' => 'メッセージ作成方法を選択',
        'ai_prompt_help' => 'AIがこのプロンプトをもとにパーソナライズされたメッセージを生成します',
        'template_help' => '既存テンプレートを選択',
        
        // Frequency Options
        'freq_once' => '1回のみ',
        'freq_daily' => '毎日',
        'freq_monthly' => '毎月',
        'freq_yearly' => '毎年',
        
        // Days
        'monday' => '月曜日',
        'tuesday' => '火曜日',
        'wednesday' => '水曜日',
        'thursday' => '木曜日',
        'friday' => '金曜日',
        'saturday' => '土曜日',
        'sunday' => '日曜日',
        'last_day' => '月末日',
        
        // Months
        'january' => '1月',
        'february' => '2月',
        'march' => '3月',
        'april' => '4月',
        'may' => '5月',
        'june' => '6月',
        'july' => '7月',
        'august' => '8月',
        'september' => '9月',
        'october' => '10月',
        'november' => '11月',
        'december' => '12月',
        
        // Broadcast Methods
        'method_template_option' => 'テンプレートを使用',
        'method_ai_option' => 'AIプロンプトを使用',
        
        // Device Options
        'device_sequence' => '単一デバイス（順番に送信）',
        'device_spin' => 'AI自動選択',
        'device_random' => 'ランダム選択',
        
        // Units
        'seconds' => '秒',
        
        // Badges & Labels
        'optional' => '任意',
        'required_field' => '必須項目',
        
        // Buttons
        'create_campaign' => 'キャンペーンを作成',
        'update_campaign' => 'キャンペーンを更新',
        
        // Alert & Tips
        'campaign_tips' => 'アップセルキャンペーンのヒント：',
        'tip_ai_prompt' => 'AIプロンプトを使用してよりパーソナルで魅力的なメッセージを作成しましょう。',
        'tip_optimal_time' => '効果的な時間帯に送信してエンゲージメントを最大化。',
        'tip_use_labels' => 'ラベルを活用してターゲティングを最適化。',
        'tip_frequency' => '自動フォローアップには日次／月次設定がおすすめです。',
    ]
];
