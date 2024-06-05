<?php
declare(strict_types=1);

//
// 更新関連
//


// 全ての自動更新を無効化
add_filter('automatic_updater_disabled', '__return_true');


// 種別毎の対応する場合
// コア、テーマ、プラグイン、翻訳ファイルは自動更新を行い、テーマごとに自動更新を無効化
// add_filter('auto_update_core', '__return_true');
// add_filter('auto_update_theme', '__return_false');
// add_filter('auto_update_plugin', '__return_true');
// add_filter('auto_update_translation', '__return_true');

// 開発リリースは自動更新せず、マイナーリリース、メジャーリリースは自動更新する場合
// add_filter('allow_dev_auto_core_updates', '__return_false');
// add_filter('allow_minor_auto_core_updates', '__return_true');
// add_filter('allow_major_auto_core_updates', '__return_true');


//
// 更新通知関連
//


// 更新通知（バージョンアップ情報）の非表示設定
if (!current_user_can('administrator')) { // 管理者以外は非表示にする
  //本体の更新通知を非表示
  add_filter('pre_site_transient_update_core', '__return_null');

  //プラグインの更新通知を非表示
  add_filter('pre_site_transient_update_plugins', '__return_null');

  //テーマの更新通知を非表示
  add_filter('pre_site_transient_update_themes', '__return_null');
}

// 管理画面上部ツールバーに更新アイコンを非表示
add_action('wp_before_admin_bar_render', function() {
  if (!current_user_can('administrator')) { // 管理者以外は非表示にする
    global $wp_admin_bar; $wp_admin_bar->remove_menu('updates');
  }
});

// ダッシュボードにある「更新」を非表示
add_action('admin_menu', function() {
  if (!current_user_can('administrator')) { // 管理者以外は非表示にする
    remove_submenu_page('index.php', 'update-core.php');
  }
});

// WordPress本体の更新通知を非表示
add_action('admin_init', function() {
  if (!current_user_can('administrator')) { // 管理者以外は非表示にする
    remove_action( 'admin_notices', 'update_nag', 3  );
  }
});
