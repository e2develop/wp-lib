<?php
declare(strict_types=1);

// 管理側固定ページエディター自動挿入のPタグBRタグ削除
// オートフォーマット関連の無効化
add_action('init', function() {
    remove_filter('the_content', 'wpautop');
    remove_filter('the_content', 'wptexturize');
});

// 固定ページのみ、オートフォーマット関連を無効化する
// add_action('wp', function() {
//     if (is_page()) {
//         remove_filter('the_content', 'wpautop');
//         remove_filter('the_excerpt', 'wpautop');
//         remove_filter('the_content', 'wptexturize');
//     }
// });

// 特定のスラッグのページだけ pタグの自動挿入を解除する
// add_filter('the_post', function($post) {
//     $targets = [
//         'foo',
//         'bar',
//     ];
//
//     $slug = $post->post_name;
//     if (in_array($slug, $targets, true)) {
//         remove_filter('the_content', 'wpautop');
//         remove_filter('the_excerpt', 'wpautop');
//     };
// });


// mainタグや一部の属性が削除されてしまう場合の対応
add_filter('wp_kses_allowed_html', function ($tags, $context = '') {
    switch ($context) {
    case 'post':
        // $tags['有効にしたいタグ'] = [
        // '有効にしたい属性'    => true,
        // ];

        // mainタグを有効にする
        $tags['main'] = [
            'id' => true,
            'class' => true,
            'role' => true,
        ];

        $attrs = [
            'itemprop',
            'itemscope',
            'itemtype',
        ];
        // 各タグのパンくず関連の属性を有効にする
        foreach ($attrs as $attr) {
            $tags['a'][$attr] = true;
            $tags['nav'][$attr] = true;
            $tags['span'][$attr] = true;
            $tags['ul'][$attr] = true;
            $tags['li'][$attr] = true;
            $tags['meta'][$attr] = true;
        }

        break;
    }

    return $tags;
});

// ビジュアルモードとテキストモードを切り替え対応
// TinyMCEの設定
add_filter('tiny_mce_before_init', function ($init) {
    global $allowedposttags;

    // すべてのタグを許可する
    $init['valid_elements'] = '*[*]';
    $init['extended_valid_elements'] = '*[*]';

    // aタグ内にすべてのタグを含められるようにする
    // i.e. <a href="#"><h1>header</h1></a>
    $init['valid_children'] = '+a[' . implode('|', array_keys($allowedposttags)) . ']';

    // bodyタグ内にmeta, styleタグを含められるようにする
    // liタグ内にmetaタグを含められるようにする
    // mateタグに設定できる属性を設定
    $init['valid_children'] .= ',+body[meta|style],+li[meta],+meta[itemprop|content]';

    // javascriptから始まるurlを有効にする
    $init['allow_script_urls'] = true;

    // 改行が<p>にならないようにする
    $init['forced_root_block'] = '';

    // テキスト -> ビジュアルエディタ変換時に、pタグや、brタグ以外のタグが消えてしまうのをやめさせる
    $init['indent'] = true;
    $init['wpautop'] = false;

    return $init;
});
