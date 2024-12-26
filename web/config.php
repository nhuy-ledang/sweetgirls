<?php
// Run the site as http or https, requires ssl certificate
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) {
    define('SITE_PROTOCOL', 'https://');
} else {
    define('SITE_PROTOCOL', 'http://');
}

$domain = $_SERVER['SERVER_NAME'];

// ENV
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    $env = 'prod';
} else {
    $env = 'local';
}

define('APP_ENV', $env);
require_once('config/' . $env . '.php');

// HTTP
define('HTTP_SERVER', 'http://' . $domain . '/');

// HTTPS
define('HTTPS_SERVER', 'https://' . $domain . '/');

// MEDIA
define('MEDIA_URL', SITE_PROTOCOL . $domain . '/upload/');

// API
define('API_URL', SITE_PROTOCOL . $domain . '/api/v1/');

// DIR
define('DIR_ROOT', realpath(__DIR__) . DIRECTORY_SEPARATOR);
define('DIR_APPLICATION', DIR_ROOT . 'catalog' . DIRECTORY_SEPARATOR);
define('DIR_SYSTEM', DIR_ROOT . 'system' . DIRECTORY_SEPARATOR);
define('DIR_IMAGE', DIR_ROOT . 'image' . DIRECTORY_SEPARATOR);
define('DIR_STORAGE', DIR_ROOT . 'storage' . DIRECTORY_SEPARATOR);
define('DIR_LANGUAGE', DIR_APPLICATION . 'language' . DIRECTORY_SEPARATOR);
define('DIR_TEMPLATE', DIR_ROOT . 'catalog/view');
define('DIR_CONFIG', DIR_SYSTEM . 'config' . DIRECTORY_SEPARATOR);
define('DIR_CACHE', DIR_STORAGE . 'cache' . DIRECTORY_SEPARATOR);
define('DIR_DOWNLOAD', DIR_STORAGE . 'download' . DIRECTORY_SEPARATOR);
define('DIR_LOGS', DIR_STORAGE . 'logs' . DIRECTORY_SEPARATOR);
define('DIR_MODIFICATION', DIR_STORAGE . 'modification' . DIRECTORY_SEPARATOR);
define('DIR_SESSION', DIR_STORAGE . 'session' . DIRECTORY_SEPARATOR);
define('DIR_UPLOAD', DIR_STORAGE . 'upload' . DIRECTORY_SEPARATOR);

# ONEPAY live
define('ONEPAY_URL_PAYGATE', 'https://onepay.vn/paygate/vpcpay.op');
define('ONEPAY_MERCHANTID', '');
define('ONEPAY_ACCESSCODE', '');
define('ONEPAY_SECURE_SECRET', '');

// OTHER
define('reCAPTCHA_SITE_KEY', '6Lcsq9wZAAAAAM_PS0sKL0k0YGJmKGnbYiEwGJb-');
