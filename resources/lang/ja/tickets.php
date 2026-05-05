<?php

return [
    // Ticket Management
    'ticket_management' => 'チケット管理',
    'contact_list' => 'チケットリスト',
    'total' => '合計',
    'contact' => '連絡先',
    'search_contact' => '連絡先を検索',
    
    // Filters
    'start_date' => '開始日',
    'end_date' => '終了日',
    'level' => 'レベル',
    'agent' => 'エージェント',
    'tickets' => 'チケット',
    'all_categories' => 'すべてのカテゴリ',
    'all_levels' => 'すべてのレベル',
    'all_agents' => 'すべてのエージェント',
    'all_status' => 'すべてのステータス',
    'no_ticket_in_column' => 'この列にチケットはありません',
    
    // Ticket Levels
    'level_low' => '低',
    'level_medium' => '中',
    'level_high' => '高',
    'level_urgent' => '緊急',
    
    // Ticket Information
    'ticket_id' => 'チケットID',
    'ticket_level' => 'チケットレベル',
    'ticket_name' => 'チケット名',
    'ticket_name_placeholder' => 'チケット名を入力',
    'ticket_detail' => 'チケット詳細',
    
    // Status
    'status' => 'ステータス',
    'status_open' => 'オープン',
    'status_resolved' => '解決済み',
    'status_pending' => '保留中',
    'status_block' => 'ブロック',
    'status_in_progress' => '進行中',
    'status_closed' => 'クローズ',
    
    // Basic Info
    'basic_info' => '基本情報',
    'name' => '名前',
    'email' => 'メール',
    'phone' => '電話',
    'title' => 'タイトル',
    'title_placeholder' => 'チケットタイトルを入力',
    'priority' => '優先度',
    
    // Category
    'category' => 'カテゴリ',
    'select_category' => 'カテゴリを選択',
    'category_name' => 'カテゴリ名',
    'category_name_placeholder' => 'カテゴリ名を入力',
    'category_slug' => 'カテゴリスラッグ',
    'category_slug_placeholder' => '自動生成スラッグ',
    'category_slug_hint' => '名前から自動生成するには空白のままにします',
    'category_description' => '説明',
    'category_description_placeholder' => 'カテゴリの説明を入力',
    'category_active' => 'アクティブ',
    'category_management' => 'カテゴリ管理',
    'manage_categories' => 'カテゴリを管理',
    'add_category' => 'カテゴリを追加',
    'edit_category' => 'カテゴリを編集',
    'create_category' => 'カテゴリを作成',
    'update_category' => 'カテゴリを更新',
    'search_category' => 'カテゴリを検索...',
    'no_categories' => 'カテゴリが見つかりません',
    'category_deleted' => 'カテゴリが正常に削除されました',
    'categories_deleted' => 'カテゴリが正常に削除されました',
    'category_updated' => 'カテゴリが正常に更新されました',
    'failed_delete_category' => 'カテゴリの削除に失敗しました',
    'failed_update_category' => 'カテゴリの更新に失敗しました',
    'confirm_delete_category_title' => 'カテゴリを削除？',
    'confirm_delete_category_text' => 'このカテゴリを削除してもよろしいですか？',
    'confirm_bulk_delete_categories_title' => '複数のカテゴリを削除？',
    'confirm_bulk_delete_categories_text' => '{count}個のカテゴリを削除してもよろしいですか？',
    
    // Channel
    'channel_info' => 'チャネル情報',
    'source_channel' => 'ソースチャネル',
    
    // Assignment
    'status_assignment' => 'ステータスと割り当て',
    'assign_agent' => 'エージェントを割り当て',
    'handled_by' => '担当者',
    'resolved_by' => '解決者',
    'assigned_to' => '割り当て先',
    'no_agent' => 'エージェントなし',
    'no_agent_assigned' => 'エージェントが割り当てられていません',
    'not_handled' => '未対応',
    'hold_ctrl_multiple' => '複数のエージェントを選択するにはCtrl/Cmdを押し続けます',
    
    // Label
    'label' => 'ラベル',
    'select_label' => 'ラベルを選択',
    'label_name' => 'ラベル名',
    'label_name_placeholder' => 'ラベル名を入力',
    'label_tag' => 'ラベルタグ/キーワード',
    'label_tag_desc' => 'このラベル割り当てをトリガーするキーワード',
    'label_tag_placeholder' => '例：緊急、クレーム、請求',
    'label_position' => '位置インデックス',
    'label_color' => 'ラベルカラー',
    'label_selection_hint' => '選択されていない場合、ラベルはキーワードに基づいて自動的に決定されます',
    'add_label' => 'ラベルを追加',
    'edit_label' => 'ラベルを編集',
    'delete_label' => 'ラベルを削除',
    'update_label' => 'ラベルを更新',
    'save_label' => 'ラベルを保存',
    'label_created' => 'ラベルが正常に作成されました',
    'label_updated' => 'ラベルが正常に更新されました',
    'label_deleted' => 'ラベルが正常に削除されました',
    'failed_create_label' => 'ラベルの作成に失敗しました',
    'failed_update_label' => 'ラベルの更新に失敗しました',
    'failed_delete_label' => 'ラベルの削除に失敗しました',
    'confirm_delete_label_title' => 'ラベルを削除？',
    'confirm_delete_label_text' => 'このラベルを削除してもよろしいですか？このラベルのすべてのチケットは「新規チケット」に移動されます。',
    'yes_delete_label' => 'はい、ラベルを削除！',
    'label_changed' => 'ラベルが変更されました',
    'move_to_label' => 'ラベルに移動',
    'ticket_moved' => 'チケットが正常に移動されました',
    'failed_move_ticket' => 'チケットの移動に失敗しました',
    
    // Ticket Actions
    'add_ticket' => 'チケットを作成',
    'edit_ticket' => 'チケットを編集',
    'create_ticket' => 'チケットを作成',
    'update_ticket' => 'チケットを更新',
    'select_contact' => '連絡先を選択',
    
    // Notes
    'notes' => 'メモ',
    'notes_placeholder' => 'メモまたは説明を入力',
    'add' => '追加',
    'add_note_placeholder' => 'メモを追加...',
    'no_notes_yet' => 'まだメモはありません。最初に追加してください！',
    'ctrl_enter_to_send' => '送信するにはCtrl+Enterを押してください',
    
    // Activity
    'activity_history' => 'アクティビティ履歴',
    'no_activity_logs' => 'アクティビティログはありません',
    
    // Attachment
    'attachment' => '添付ファイル',
    'file_upload_hint' => 'サポートされている形式：JPG、PNG、PDF、DOC、DOCX、TXT',
    
    // Timestamps
    'timestamps' => 'タイムスタンプ',
    'created_at' => '作成日時',
    'updated_at' => '更新日時',
    
    // Actions
    'actions' => 'アクション',
    'view_detail' => '詳細を表示',
    'edit' => '編集',
    'delete' => '削除',
    'close' => '閉じる',
    'cancel' => 'キャンセル',
    'save' => '保存',
    'manage' => '管理',
    'quick_actions' => 'クイックアクション',
    
    // Pagination & Loading
    'loading_data' => 'データを読み込み中...',
    'loading_more' => 'さらに読み込み中...',
    'load_more' => 'さらに読み込む',
    'remaining' => '残り',
    'contact_per_column' => '列あたりの連絡先',
    'contact_loaded' => '連絡先を読み込みました',
    
    // Status Messages
    'active' => 'アクティブ',
    'inactive' => '非アクティブ',
    'selected' => '選択済み',
    'delete_selected' => '選択項目を削除',
    
    // Confirmations
    'confirm_delete_title' => 'よろしいですか？',
    'confirm_delete_text' => 'この操作は元に戻せません！',
    'yes_delete' => 'はい、削除します！',
    'contact_deleted' => '連絡先が削除されました。',
    
    // Error Messages
    'failed_delete_contact' => '連絡先の削除に失敗しました',
    'failed_load_data' => 'データの読み込みに失敗しました',
    'error_load_data' => 'データ読み込みエラー',
];
