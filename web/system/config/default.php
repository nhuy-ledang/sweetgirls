<?php
// Site
$_['site_url']             = '';

// Url
$_['url_autostart']        = true;

// Config
$_['config_suffix_url']    = false;
$_['config_currency']      = 'VND';

// Language
$_['language_directory']   = 'vi-vn';
$_['language_autoload']    = ['vi-vn'];
$_['language_list']        = [
    ['code' => 'en', 'name' => 'English', 'directory'=>'en-gb', 'locale' => 'en_US.UTF-8,en_US,en-gb,english'],
];
$_['language_list_all']    = [
    ['code' => 'vi', 'name' => 'Tiếng Việt', 'directory' => 'vi-vn', 'locale' => 'vi_VN.UTF-8,vi_VN,vi-vn,vietnamese'],
    ['code' => 'en', 'name' => 'English', 'directory' => 'en-gb', 'locale' => 'en_US.UTF-8,en_US,en-gb,english'],
];
$_['language_code_all']    = [
    'vi' => 'vi-vn',
    'en' => 'en-gb',
];
$_['language_code_default']= 'vi';

// Date
$_['date_timezone']        = 'Asia/Ho_Chi_Minh'; //'UTC';

// Database
$_['db_engine']            = 'mysqli'; // mpdo, mssql, mysql, mysqli or postgre
$_['db_hostname']          = 'localhost';
$_['db_username']          = 'root';
$_['db_password']          = '';
$_['db_database']          = '';
$_['db_port']              = 3306;
$_['db_autostart']         = false;

// Mail
$_['mail_engine']          = 'mail'; // mail or smtp
$_['mail_from']            = ''; // Your E-Mail
$_['mail_sender']          = ''; // Your name or company name
$_['mail_reply_to']        = ''; // Reply to E-Mail
$_['mail_smtp_hostname']   = '';
$_['mail_smtp_username']   = '';
$_['mail_smtp_password']   = '';
$_['mail_smtp_port']       = 25;
$_['mail_smtp_timeout']    = 5;
$_['mail_verp']            = false;
$_['mail_parameter']       = '';

// Cache
$_['cache_engine']         = 'file'; // apc, file, mem or memcached
$_['cache_expire']         = 3600;

// Session
$_['session_engine']       = 'file';
$_['session_autostart']    = true;
$_['session_name']         = 'OCSESSID';
$_['session_expire']       = 3600;

// Template
$_['template_engine']      = 'twig';
$_['template_directory']   = '';
$_['template_cache']       = false;
$_['template_extension']   = '.twig';

// Error
$_['error_display']        = true;
$_['error_log']            = true;
$_['error_filename']       = 'error.log';

// Reponse
$_['response_header']      = array('Content-Type: text/html; charset=utf-8');
$_['response_compression'] = 0;

// Autoload Configs
$_['config_autoload']      = array();

// Autoload Libraries
$_['library_autoload']     = array();

// Autoload Libraries
$_['model_autoload']       = array();

// Actions
$_['action_default']       = 'common/home';
$_['action_router']        = 'startup/router';
$_['action_error']         = 'error/not_found';
$_['action_pre_action']    = array();
$_['action_event']         = array();
$_['action_cron']          = array();

# ONEPAY sandbox
$_['onepay_url']           = 'https://mtf.onepay.vn/paygate/vpcpay.op';
$_['onepay_merchantid']    = 'TESTONEPAY';
$_['onepay_secure_secret'] = '6D0870CDE5F24F34F3915FB0045120DB';
$_['onepay_accesscode']    = '6BEB2546';
