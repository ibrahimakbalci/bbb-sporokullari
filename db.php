<?php
ob_start();
header('Content-Type: text/html; charset=utf-8');

session_set_cookie_params([
	'httponly' => true,
	'samesite' => 'Lax',
	'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https',
]);
session_start();

require_once(__DIR__ . '/vendor/autoload.php');
require_once('ezsql/ez_sql_core.php');
require_once('ezsql/ez_sql_mysqli.php');
require_once('fonksiyonlar.php');

// Kimlik bilgileri ortam değişkenlerinden gelir (Docker: .env, Docker'sız sunucu: Apache SetEnv)
$wpdb = new ezSQL_mysqli(getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'), getenv('DB_HOST') ?: 'localhost', 'utf8');
$wpdb->query("SET NAMES 'UTF8'");

$islem = is_string($_GET["islem"] ?? null) ? $_GET["islem"] : '';
$bolum = is_string($_GET["bolum"] ?? null) ? $_GET["bolum"] : '';
$id = (int) ($_GET["id"] ?? 0);

define('GERI_DON', '<br><a href="javascript:history.back()" class="btn btn-danger">Geri Dön</a>');
define('GERI_DON_BOS', '<br><a href="javascript:history.back()" class="btn btn-danger">Geri Dön</a>');

// Giriş yapılmamışken de oturum anahtarları tanımlı olsun (PHP 8 "Undefined array key" uyarıları)
$_SESSION += array('giris' => '', 'uye_id' => 0, 'uye_adsoyad' => '', 'uye_yetki' => array(), 'uye_admin' => '');

if ($_SESSION['giris']) {
	unset($_SESSION['uye_yetki']);
	$uye = $wpdb->get_row("select uye_yetki,uye_admin from basvuru_uyeler where uye_id = ". (int) $_SESSION['uye_id']);
	$_SESSION['uye_yetki'] = yetki_listesi($uye->uye_yetki ?? null);
	$_SESSION['uye_admin'] = $uye->uye_admin ?? '';
}
