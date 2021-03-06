# laravel-db-email-log
Save logs handled by Laravel in Mysql, and send mail when it is over specified level.

[![Travis](https://img.shields.io/travis/rust-lang/rust.svg)]()
[![composer version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/kaoken/laravel-db-email-log)
[![licence](https://img.shields.io/badge/licence-MIT-blue.svg)](https://github.com/kaoken/laravel-db-email-log)
[![laravel version](https://img.shields.io/badge/Laravel%20version-≧8.77.1-red.svg)](https://github.com/kaoken/laravel-db-email-log)



__Table of content__

- [Install](#install)
- [Setting](#setting)
- [Event](#event)
- [License](#license)

## Install

**composer**:

```bash
composer require kaoken/laravel-db-email-log
```

or, add `composer.json`  

```json 
  "require": {
    ...
    "kaoken/laravel-db-email-log":"^1.0"
  }
```

## Setting

###  Add to **`config\app.php`** as follows:

```php
    'providers' => [
        ...
        // Add
        Kaoken\LaravelDBEmailLog\LaravelDBEmailLogServiceProvider::class
    ],
```
  
    
### Add to **`config\database.php`** as follows:

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
        // Add (Copy 'mysql' above)
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
Copy the above `['connections']['mysql']` and set the driver name to `db_log`.
This is necessary to prevent the log from being lost due to rollback after writing 
the log when doing transaction processing (`DB :: transaction`,` DB :: beginTransaction` etc.) 
with the driver name `mysql`.

  
  

###  Add to **`config\logging.php`** as follows:

- `connection` is the driver name for the data. See `config\database.php`.
- `model` is a log model.
- `email` sends a mail according to` email_send_level` if it is `true`.
In case of `false`, do not send anything.
- `email_send_level` specifies the log level and send from the specified log level or higher.
Since the priority is low, `DEBUG`、`INFO`、`NOTICE`、`WARNING`、
`ERROR`、`CRITICAL`、`ALERT`、`EMERGENCY`.Capital letters and lower case letters are not distinguished.
- `email_log` should modify the class derived from [Mailable](https://laravel.com/docs/8.x/mail) as necessary.
Send log mail. 
- `email_send_limit` should modify the class derived from [Mailable](https://laravel.com/docs/8.x/mail) as necessary.
Send when e-mail transmission limit `max_email_send_count` is exceeded. 
- `max_email_send_count`, the log e-mail that can be transmitted in one day.
 A simple warning mail is sent when the number exceeds the number of transmissions. See `email_send_level`.
- `to` is the destination of the mail.
  
It is good to add it under the `'log_level' => env('APP_LOG_LEVEL', 'debug'),` of the `config\app.php` file.


```php  
    'default' => env('LOG_CHANNEL', 'db_log'),
    // Add
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

### Command
```bash
php artisan vendor:publish --tag=db-email-log
```
  
After execution, the following directories and files are added.  

* **`database`**
  * **`migrations`**
    * `2021_01_01_000001_create_logs_table.php`
* **`resources`**
  * **`views`**
    * **`vendor`**
      * **`mysql_email_log`**
        * `log.blade.php`
        * `over_limit.blade.php`
     
### Migration
Migration file `2021_01_01_000001_create_logs_table.php` should be modified as necessary.  

```bash
php artisan migrate
```

### E-Mail
In the configuration `config\logging.php` of the above setting,
The `Kaoken\LaravelDBEmailLog\Mail\ConfirmationMailToUser::class` of `email_log` is used as the log mail of the target level or higher.
The template uses `views\vendor\mysql_email_log\log.blade.php`. Change according to the specifications of the application.  
  
The `Kaoken\LaravelDBEmailLog\Mail\ConfirmationMailToUser::class` of `email_send_limit` is used when the log above the target level reaches the send limit.
The template uses `views\vendor\mysql_email_log\over_limit.blade.php`. Change according to the specifications of the application.  






## Event
See inside the `vendor\laravel-db-email-log\src\Events` directory!

#### `BeforeWriteLogEvent`
Called before writing the log.




## License

[MIT](https://github.com/kaoken/laravel-confirmation-email/blob/master/LICENSE.txt)
