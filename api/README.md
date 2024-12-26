Server Requirements
===================
```php
PHP >= 5.6.4
OpenSSL PHP Extension
PDO PHP Extension
Mbstring PHP Extension
Tokenizer PHP Extension
XML PHP Extension
```

Installing platform
===================

Step 1:
-------
```bash
$ composer install
```

Ignore error.

Go to: http://localhost/hocodau/public/

Step 2:
-------
Rename .env.example to .env
Configure database in .env file

Step 3:
-------
```bash
$ php artisan migrate

```

Step 4:
-------
```bash
$ php artisan vendor:publish
```

```bash
$ php artisan module:migrate
```

Step 5:
-------
```bash
$ chmod -R 777 config public storage vendor
$ cp -rf Overrides/resources/views/vendor/latrell/swagger/index.blade.php resources/views/vendor/latrell/swagger/index.blade.php
```
* The ``config`` folder (necessary for writing the Platform config files).
* The ``public/cache`` folder and its sub-folders.
* The ``public/media`` folder and its sub-folders.
* The ``storage`` folder required by Laravel and its sub-folders.
* The ``vendor`` folder required by Laravel.

Step 6:
-------
Seed.
```bash
$ php artisan module:seed Core
$ php artisan module:seed Location
$ php artisan module:seed Product
```

Done!

Wiki
====
- [Latest](http://ibss.io:5495/docs/bssinsight/en/latest/index.html)
- [Modules](https://nwidart.com/laravel-modules/v3/introduction)
- [Artisan commands](https://nwidart.com/laravel-modules/v3/advanced-tools/artisan-commands)

View route list
====
```bash
$ php artisan route:list
```

Swagger Publish Provider
```bash
$ php artisan vendor:publish --provider="Latrell\Swagger\SwaggerServiceProvider"
```

Elastic Search Publish Provider
```bash
$ php artisan vendor:publish --provider="Elasticquent\ElasticquentServiceProvider"
```

Cors publish
```bash
$ php artisan vendor:publish --provider="Barryvdh\Cors\ServiceProvider"
```

Notification Core publish
```bash
$ php artisan vendor:publish --provider="Core\Push\Providers\PushServiceProvider" --tag="config"
```

Common
====
```bash
$ php artisan key:generate
$ php artisan vendor:publish
$ php artisan cache:clear
$ php artisan config:clear
$ php artisan route:clear
```

Artisan Commands
====
Generate a new module.
```bash
$ php artisan module:make Blog
```
Migrate & Seed.
```bash
$ php artisan module:migrate Blog
$ php artisan module:migrate-rollback Blog
$ php artisan module:migrate-refresh Blog
$ php artisan module:seed Blog
```
Generate a migration for specified module.
```bash
$ php artisan module:make-migration create_posts_table Blog
```
Generator commands
```bash
$ php artisan module:make-command CreatePostCommand Blog
```
Generator Other
```bash
$ php artisan module:make-controller PostsController Blog
$ php artisan module:make-model Post Blog
$ php artisan module:make-rule ValidationRule Blog
```
Run mysql command line:
```bash
cd C:\xampp\mysql\bin
mysql.exe -u root --password
use [Datatase Name]
set character_set_client='utf8';
set character_set_connection='utf8';
set character_set_database='utf8';
set character_set_results='utf8';
set character_set_server='utf8';
show variables like 'char%';
source [file path]/users.sql
```

Run queue:
```bash
QUEUE_DRIVER=database
php artisan queue:table
php artisan queue:failed-table
php artisan migrate
php artisan make:job SendWelcomeEmail
php artisan queue:work
php artisan queue:work --sleep=1 --tries=1
php artisan queue:restart
```
### Supervisor Configuration
```bash
sudo yum install epel-release
sudo yum update
sudo yum -y install supervisor
nano /etc/supervisor/conf.d/laravel-worker.conf or nano /etc/supervisord.d/laravel-worker.ini
systemctl enable supervisord
systemctl restart supervisord
systemctl start supervisord
systemctl status supervisord
```

Send SMS:
```bash
$this->sms->send($phone_number, "Ma xac thuc: $code")
```

Send Email:
```bash
$this->email->send('place::verify', ['code' => $code], function ($message) use ($model) {
  $message->to($model->email)->subject('Xác thực địa điểm');
});
```

Push notification
```bash
$pushData['place_id'] = 1;
$pushType = 1;
$pushMessage = 'Test';
$user = $this->model_repository->find(1);
$this->pushNotifications($user, $pushMessage, $pushType, $pushData);
```

Onepay VN
```bash
https://mtf.onepay.vn/developer/?page=modul_noidia
https://mtf.onepay.vn/developer/?page=modul_quocte
```
https://mtf.onepay.vn/paygate/vpcpay.op
với atm thì thêm param vpc_CardList=DOMESTIC,QR 
Quốc tế: vpc_CardList=INTERNATIONAL

Merchant id : TESTONEPAY27
Accescode : 6BEB2566
Hash Code : 6D0870CDE5F24F34F3915FB0045120D6

Thẻ nội địa test : 
Thẻ An Binh Bank:
Số thẻ: 9704250000000001
Tháng/Năm phát hành: 01/13
Tên: NGUYEN VAN A
Mã OTP: 123456

Vietcombank
Card number: 9704360000000000002
Card Name: NGUYEN VAN A       
Issue date: 01-13             
OTP: 123456
= = =
Thẻ quốc tế
Master: 5473500160001018 05/24 123
Visa: 4440000009900010 05/24 123
Amex: 340000099900036 05/24 1234

Outlook mail config: outlook mail allow less secure apps
-------
Use the Microsoft 365 [admin center](https://admin.microsoft.com/) to enable or disable SMTP AUTH on specific mailboxes
Open the Microsoft 365 admin center and go to Users > Active users.
Select the user, and in the flyout that appears, click Mail.
In the Email apps section, click Manage email apps.
Verify the Authenticated SMTP setting: unchecked = disabled, checked = enabled.
When you're finished, click Save changes.

Full crontab
-------
```
export EDITOR=/bin/nano
crontab -e
```
Lệnh này sẽ đặt nano làm trình chỉnh sửa mặc định và bây giờ nó có thể chỉnh sửa crontab:
```
0 0 1,15 * * /usr/bin/certbot renew
* * * * *  php /home/sweetgirlbeauty.com/public_html/api/artisan schedule:run
```
