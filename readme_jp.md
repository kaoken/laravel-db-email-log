# laravel-db-email-log
Laravelで扱うログをMysqlに保存し、指定レベル以上の場合メールを送信する。

[![Travis](https://img.shields.io/travis/rust-lang/rust.svg)]()
[![composer version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/kaoken/laravel-db-email-log)
[![licence](https://img.shields.io/badge/licence-MIT-blue.svg)](https://github.com/kaoken/laravel-db-email-log)
[![laravel version](https://img.shields.io/badge/Laravel%20version-≧8.77.1-red.svg)](https://github.com/kaoken/laravel-db-email-log)


__コンテンツの一覧__

- [インストール](#インストール)
- [設定](#設定)
- [イベント](#イベント)
- [ライセンス](#ライセンス)

## インストール

**composer**:

```bash
composer require kaoken/laravel-db-email-log
```

または、`composer.json`へ追加

```json 
  "require": {
    ...
    "kaoken/laravel-db-email-log":"^1.0"
  }
```

## 設定

### `config\app.php` に以下のように追加：

```php
    'providers' => [
        ...
        // 追加
        Kaoken\LaravelDBEmailLog\LaravelDBEmailLogServiceProvider::class
    ],
```
  
    
### `config\database.php` に以下のように追加：
mysqlの例
```php
    'connections' => [
        ...
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        // 追加(上記'mysql'をコピー)
        'db_log' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        ...
```
上記の`['connections']['mysql']`をコピーして、ドライバー名を`db_log`とする。
これは、ドライバー名`mysql`で、トランザクション処理(`DB::transaction`,`DB::beginTransaction`など)をしたとき、ログを書き込んだ後ロールバックによる
ログの消滅を阻止するために必要。
  
  

### `config\logging.php`へ追加する例

- `connection`は、データーばべーすドライバ名。`config\database.php`を参照。
- `model`は、ログモデル。
- `email`は、`true`の場合、`email_send_level`に応じて、メールを送信する。`false`の場合、一切送信しない。
- `email_send_level`は、ログレベルを指定して、指定したログレベル以上から送信する。優先順位は低いものから、`DEBUG`、`INFO`、`NOTICE`、`WARNING`、
`ERROR`、`CRITICAL`、`ALERT`、`EMERGENCY` である。大文字小文字は区別しない。
- `email_log`は、 [Mailable](https://readouble.com/laravel/8.x/ja/mail) で派生したクラスを必要に応じて変更すること。
ログメールを送る。  
- `email_send_limit`は、 [Mailable](https://readouble.com/laravel/8.x/ja/mail) で派生したクラスを必要に応じて変更すること。
メールの送信制限`max_email_send_count`を超えた時に送る。  
- `max_email_send_count`は、1日に送れるログメール。送信数を超えると簡単な警告メールが送られてくる。`email_send_level`参照。
- `to`は、メールの送信先。
  
`config\app.php` の `'log_level' => env('APP_LOG_LEVEL', 'debug'),`の下あたりに追加するのが良い。  

```php  
    'default' => env('LOG_CHANNEL', 'db_log'),
    // 追加
    'db_log' => [
        'connection' => 'db_log',
        'model' => Kaoken\LaravelDBEmailLog\Model\Log::class,
        'email' => true,
        'email_send_level' => 'ERROR',
        'email_log' => Kaoken\LaravelDBEmailLog\Mail\LogMailToAdmin::class,
        'email_send_limit' => Kaoken\LaravelDBEmailLog\Mail\SendLimitMailToAdmin::class,
        'max_email_send_count' => 64,
        'to' => 'hoge@hoge.com'
    ],
```

### コマンドの実行
```bash
php artisan vendor:publish --tag=db-email-log
```
実行後、以下のディレクトリやファイルが追加される。   

* **`database`**
  * **`migrations`**
    * `2021_01_01_000001_create_logs_table.php`
* **`resources`**
  * **`views`**
    * **`vendor`**
      * **`db_email_log`**
        * `log.blade.php`
        * `over_limit.blade.php`
     
### マイグレーション
マイグレーションファイル`2021_01_01_000001_create_logs_table.php`は、必要に応じて
追加修正すること。

```bash
php artisan migrate
```

### メール
上記の設定の `config\logging.php`設定では、  
`email_log`の`Kaoken\LaravelDBEmailLog\Mail\ConfirmationMailToUser::class`は、
対象レベル以上のログメールとして使用する。
テンプレートは、`views\vendor\db_email_log\log.blade.php`
を使用している。アプリの仕様に合わせて変更すること。
  
`email_send_limit`の`Kaoken\LaravelDBEmailLog\Mail\ConfirmationMailToUser::class`は、
対象レベル以上のログが送信制限に達したときに使用する。
テンプレートは、`views\vendor\db_email_log\over_limit.blade.php`
を使用している。アプリの仕様に合わせて変更すること。



## イベント
`vendor\laravel-db-email-log\src\Events`ディレクトリ内を参照!  

#### `BeforeWriteLogEvent`
ログを書き込む前に呼び出される。  





## ライセンス

[MIT](https://github.com/kaoken/laravel-db-email-log/blob/master/LICENSE.txt)
