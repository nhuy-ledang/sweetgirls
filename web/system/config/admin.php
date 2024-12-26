<?php
// Site
$_['site_url']          = HTTP_SERVER;

// Database
$_['db_autostart']      = true;
$_['db_engine']         = DB_DRIVER; // mpdo, mssql, mysql, mysqli or postgre
$_['db_hostname']       = DB_HOSTNAME;
$_['db_username']       = DB_USERNAME;
$_['db_password']       = DB_PASSWORD;
$_['db_database']       = DB_DATABASE;
$_['db_port']           = DB_PORT;

// Session
$_['session_autostart'] = true;
$_['session_engine']    = 'db';

// Error
$_['error_display']     = true;

// Template
$_['template_cache']    = true;

// Actions
$_['action_pre_action'] = array(
	'startup/startup',
	'startup/error',
	//'startup/event',
	'startup/login',
	'startup/permission',
    //'startup/seo_url'
);

// Actions
$_['action_default'] = 'common/dashboard';

// Action Events
$_['action_event'] = array(
	'controller/*/before' => array(
		'event/language/before'
	),
	'controller/*/after' => array(
		'event/language/after'
	),
	'view/*/before' => array(
		999  => 'event/language',
		1000 => 'event/theme'
	),
	//'model/*/after' => array(
	//	'event/debug/before'
	//),
	//'model/*/after'  => array(
	//	'event/debug/after'
	//)
);
