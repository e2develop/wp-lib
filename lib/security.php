<?php
declare(strict_types=1);

// ログインエラーメッセージを変更
add_filter('login_errors', function () {
    return '入力内容を確認の上、もう一度送信してください。';
});

// /?author=1 でアクセスされた際にURLにユーザー名が表示される機能を無効化)
add_action('init', function () {
    if (isset($_SERVER['REQUEST_URI'])) {
        if (! is_admin() && preg_match('/[?&]author=[0-9]+/i', $_SERVER['REQUEST_URI'])) {
            wp_safe_redirect(home_url(), 301);
            exit;
        }
    }
});

// 作成者アーカイブを無効化
add_action('template_redirect', function () {
    if (!is_admin() && is_author()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
    }
});


// WordPressのバージョン情報の非表示
remove_action('wp_head', 'wp_generator');

// WordPressのバージョン情報の非表示
foreach (['atom', 'comment', 'html', 'rdf', 'rss2', 'xhtml'] as $type) {
    add_filter(
        "get_the_generator_{$type}",
        function () {
            return '';
        }
    );
}

// wlwmanifestWindows Live Writerを使った記事投稿URLの非表示
remove_action('wp_head', 'wlwmanifest_link');

// 外部ツールを使ったブログ更新用のURLの非表示
remove_action('wp_head', 'rsd_link');

// oEmbed関連のタグの非表示
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');

// リクエストヘッダーに REST API のエンドポイントを出力させない
remove_action('template_redirect', 'rest_output_link_header', 11);

// デフォルトパーマリンクのURLの非表示
remove_action('wp_head', 'wp_shortlink_wp_head');

// 前の記事と後の記事のURLの非表示
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

// 現在の文書に対する索引（インデックス）を示すリンクタグの非表示
remove_action('wp_head', 'index_rel_link');

// RSSフィードのURLの非表示
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);

// RSS フィード自体を表示させない
foreach (['atom', 'rdf', 'rss2'] as $type) {
    add_action(
        "do_feed_{$type}",
        function (): void {
            wp_die(__('Feed not available'), '', ['response' => '404']);
        },
        1
    );
}

// 管理画面の含め絵文字機能を無効化
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

// xmlrpc.phpの無効化
add_filter('xmlrpc_enabled', '__return_false');

// X-Pingbackのヘッダー情報を削除
add_filter(
    'wp_headers',
    function ($headers) {
        unset($headers['X-Pingback']);

        return $headers;
    }
);

// <link rel="dns-prefetch"> を非表示
add_filter(
    'wp_resource_hints',
    function ($hints, $relation_type) {
        if ($relation_type === 'dns-prefetch') {
            return array_diff(wp_dependencies_unique_hosts(), $hints);
        }

        return $hints;
    },
    10,
    2
);

// REST API 無効化
// 特定のプラグインを除外する場合は、$namespaces にプラグインの名前空間を追加する
add_filter(
    'rest_pre_dispatch',
    function ($result, $server, $request) {
        // Basic 認証時は無効化しない
        if (isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
            return $result;
        }

        if (current_user_can('edit_posts') || current_user_can('edit_pages')) {
            return $result;
        }

        // 特定のプラグインを除外する場合は REST URL の /wp-json/ と次のスラッシュの間の文字列を設定する
        $namespaces = [
            'aioseo',          // All in One SEO
            'contact-form-7',  // Contact Form 7
            'yoast',           // Yoast SEO
        ];
        $route = trim($request->get_route(), '/');
        foreach ($namespaces as $namespace) {
            if (strpos($route, $namespace) === 0) {
                return $result;
            }
        }
        $status = [
            'status' => rest_authorization_required_code(),
        ];

        return new WP_Error('rest_disabled', __('The REST API on this site has been disabled.'), $status);
    },
    10,
    3
);

// contain-intrinsic-size の css 削除
remove_action('wp_head', 'wp_print_auto_sizes_contain_css_fix', 1);

add_action('wp_enqueue_scripts', function (): void {
    // wp-block-library-css を読み込まない
    // ブロックエディタ(Gutenberg)のcss読み込み
    wp_dequeue_style('wp-block-library');

    // ブロックエディターのスタイル削除
    wp_dequeue_style('global-styles');

    // クラシックエディターのスタイル削除
    wp_dequeue_style('classic-theme-styles');

    // QueryMonitor がインストールされていない場合のみ、 WordPress の jquery.js を読み込まない
    if (!defined('QM_VERSION')) {
        wp_deregister_script('jquery');
    }

    // wp-embed.min.js を読み込まない
    wp_deregister_script('wp-embed');

    // 有効なプラグインで生成されるcssの読み込みを削除する
    wp_dequeue_style('toc-screen');
});


// CSS や JavaScript ファイルの読み込み時にバージョン情報を削除する
function _removeVer($src)
{

    if (!is_string($src)) {
        return $src;
    }

    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }

    return $src;
}
add_filter('style_loader_src', '_removeVer', 9999);
add_filter('script_loader_src', '_removeVer', 9999);
