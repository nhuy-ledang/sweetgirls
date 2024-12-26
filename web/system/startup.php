<?php
// Error Reporting
error_reporting(E_ALL);

// Check Version
if (version_compare(phpversion(), '7.0.0', '<')) {
	exit('PHP7+ Required');
}

if (!ini_get('date.timezone')) {
	date_default_timezone_set('UTC');
}

// Windows IIS Compatibility
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['SCRIPT_FILENAME'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['PATH_TRANSLATED'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

	if (isset($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	}
}

if (!isset($_SERVER['HTTP_HOST'])) {
	$_SERVER['HTTP_HOST'] = getenv('HTTP_HOST');
}

// Check if SSL
if ((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || (isset($_SERVER['HTTPS']) && (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443))) {
	$_SERVER['HTTPS'] = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
	$_SERVER['HTTPS'] = true;
} else {
	$_SERVER['HTTPS'] = false;
}

// Modification Override
function modification($filename) {
	if (defined('DIR_CATALOG')) {
		$file = DIR_MODIFICATION . 'admin/' .  substr($filename, strlen(DIR_APPLICATION));
	} else {
		$file = DIR_MODIFICATION . 'catalog/' . substr($filename, strlen(DIR_APPLICATION));
	}

	if (substr($filename, 0, strlen(DIR_SYSTEM)) == DIR_SYSTEM) {
		$file = DIR_MODIFICATION . 'system/' . substr($filename, strlen(DIR_SYSTEM));
	}

	if (is_file($file)) {
		return $file;
	}

	return $filename;
}

// Autoloader
if (is_file(DIR_ROOT . 'vendor/autoload.php')) {
	require_once(DIR_ROOT . 'vendor/autoload.php');
}

function library($class) {
	$file = DIR_SYSTEM . 'library/' . str_replace('\\', '/', strtolower($class)) . '.php';

	if (is_file($file)) {
		include_once(modification($file));

		return true;
	} else {
		return false;
	}
}

spl_autoload_register('library');
spl_autoload_extensions('.php');

// Engine
require_once(modification(DIR_SYSTEM . 'engine/action.php'));
require_once(modification(DIR_SYSTEM . 'engine/controller.php'));
require_once(modification(DIR_SYSTEM . 'engine/event.php'));
require_once(modification(DIR_SYSTEM . 'engine/router.php'));
require_once(modification(DIR_SYSTEM . 'engine/loader.php'));
require_once(modification(DIR_SYSTEM . 'engine/model.php'));
require_once(modification(DIR_SYSTEM . 'engine/registry.php'));
require_once(modification(DIR_SYSTEM . 'engine/proxy.php'));

// Helper
require_once(DIR_SYSTEM . 'helper/general.php');
require_once(DIR_SYSTEM . 'helper/colors.php');
require_once(DIR_SYSTEM . 'helper/utf8.php');
require_once(DIR_SYSTEM . 'helper/common.php');
require_once(DIR_SYSTEM . 'helper/seo_url.php');

//=== USER STATUS
define('USER_STATUS_STARTER', 'starter');
define('USER_STATUS_ACTIVATED', 'activated');
define('USER_STATUS_BANNED', 'banned');

//=== USER GENDER
define('USER_GENDER_UNKNOWN', 0);
define('USER_GENDER_MALE', 1);
define('USER_GENDER_FEMALE', 2);

//=== USER ROLE
define('USER_ROLE_SUPER_ADMIN', 1);
define('USER_ROLE_ADMIN', 2);
define('USER_ROLE_USER', 3);
define('USER_ROLE_POSTER', 4);
define('USER_ROLE_CONTENT_CREATOR', 5);
define('USER_ROLE_SEO', 6);

//=== USER LOGIN
define('USER_PASSWORD_FAILED_MAX', 5);

//=== PAYMENT METHODS
//define('PAYMENT_MT_CASH', 'cash');                // Payment in cash - Tiền mặt
define('PAYMENT_MT_BANK_TRANSFER', 'bank_transfer');// Bank transfer
define('PAYMENT_MT_DOMESTIC', 'domestic');          // Domestic ATM / Internet Banking card
define('PAYMENT_MT_FOREIGN', 'international');      // Visa, Master, JCB international card
define('PAYMENT_MT_MOMO', 'momo');                  // QR - Momo
define('PAYMENT_MT_COD', 'cod');                    // COD - Thu hộ
//define('PAYMENT_MT_DIRECT', 'direct');

//=== ORDER STATUS
define('ORDER_SS_PENDING', 'pending');
define('ORDER_SS_PROCESSING', 'processing');
define('ORDER_SS_SHIPPING', 'shipping');
define('ORDER_SS_COMPLETED', 'completed');
define('ORDER_SS_CANCELED', 'canceled');
define('ORDER_SS_RETURNING', 'returning');
define('ORDER_SS_RETURNED', 'returned');
// 'pending', 'processing', 'shipping', 'completed', 'canceled', 'returning', 'returned'

//=== PAYMENT STATUS
define('PAYMENT_SS_PENDING', 'pending');
define('PAYMENT_SS_INPROGRESS', 'in_process');
define('PAYMENT_SS_PAID', 'paid');
define('PAYMENT_SS_FAILED', 'failed');
define('PAYMENT_SS_UNKNOWN', 'unknown');
define('PAYMENT_SS_REFUNDED', 'refunded');
define('PAYMENT_SS_CANCELED', 'canceled');
// 'pending', 'in_process', 'paid', 'failed', 'unknown', 'refunded', 'canceled'

//=== SHIPPING STATUS
define('SHIPPING_SS_CREATE_ORDER', 'create_order');
define('SHIPPING_SS_DELIVERING', 'delivering');
define('SHIPPING_SS_DELIVERED', 'delivered');
define('SHIPPING_SS_RETURN', 'return');
// 'create_order', 'delivering', 'delivered', 'return'

function start($application_config) {
	require_once(DIR_SYSTEM . 'framework.php');	
}
