# WordPress の共通ライブラリ

## lib ディレクトリ内のファイルの説明

### editor.php 

[エディタ設定](https://redmine.e-2.jp/projects/wp/wiki/%E3%82%A8%E3%83%87%E3%82%A3%E3%82%BF%E8%A8%AD%E5%AE%9A)

- 投稿画面でHTMLタグが消える不具合の対応
- オートフォーマット関連の無効化

### security.php 

[セキュリティ関連の設定](https://redmine.e-2.jp/projects/wp/wiki/%E3%82%BB%E3%82%AD%E3%83%A5%E3%83%AA%E3%83%86%E3%82%A3%E5%AF%BE%E7%AD%96)

- author ページを無効化
- ログイン詳細エラーメッセージの無効化
- 不要なヘッダーの削除、REST API の無効化など
 
### version.php 

[更新関連の設定](https://redmine.e-2.jp/projects/wp/wiki/%E8%87%AA%E5%8B%95%E6%9B%B4%E6%96%B0%E3%81%AE%E5%AF%BE%E5%BF%9C)

- 自動更新の無効化
- 更新通知の無効化

## 使い方

1. `lib` ディレクトリ内のファイルを `wp-content/themes/使用しているテーマ/lib` にコピーします。
2. `functions.php` に以下のように記述します。
```php
require_once __DIR__ . '/lib/editor.php';
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/version.php';
```
3. 使用しない機能は適宜コメントアウトしてください。
