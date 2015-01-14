<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
 
// Include local configuration
if (file_exists(dirname(__FILE__) . '/local-config.php')) {
	include(dirname(__FILE__) . '/local-config.php');
}


// Global DB config
if (!defined('DB_NAME')) {
	define('DB_NAME', '');
}
if (!defined('DB_USER')) {
	define('DB_USER', '');
}
if (!defined('DB_PASSWORD')) {
	define('DB_PASSWORD', '');
}
if (!defined('DB_HOST')) {
	define('DB_HOST', '');
}

/** Database Charset to use in creating database tables. */
if (!defined('DB_CHARSET')) {
	define('DB_CHARSET', 'utf8');
}

/** The Database Collate type. Don't change this if in doubt. */
if (!defined('DB_COLLATE')) {
	define('DB_COLLATE', '');
}

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'KnB`6-eoUudkY +lYV_4.*0PhD#z5~?aHs,]EcI7s~.4=HF?EmjJ}Uc`b6$EGZ.}');
define('SECURE_AUTH_KEY',  'o<[>9Ete*l/*5_ _R}S@>A/)xNGaI8vspC%$P+pnv3IjV=Ii2zNC*`%wDi$sn{<v');
define('LOGGED_IN_KEY',    'QrEg_}fo/WRmdt:>{|qy5Qjt@w53H|u!_nBBg18U4Dc5D.E5u~GVCr8&QE^B]W8Z');
define('NONCE_KEY',        'ZOTjD}Z>TbB9-KF^h$N<e#N}lYCw7|D3PSiFR)5Q}Hm7.bWV+#Ob0h%![Q:p=&(0');
define('AUTH_SALT',        '5:;UbMY!5vqle]{g}B5}6~x`Rdt+CTc;(H{MSvLlL4-0~xL0N=]YFocAQ-@e@:<d');
define('SECURE_AUTH_SALT', '6?`<c8=.8!x^>=f[Z|&0P?jIs:wGj(|+~#w0Jl3Li<4w<BIr}WL+M2j!)!ZAI$0C');
define('LOGGED_IN_SALT',   'b{:B7+{~-,l#z6&g~bCr5ebV|943n33(Hcc[f&zRDHab4daE]nW~WIz(`U%h-m@l');
define('NONCE_SALT',       '>7|h{`XmHy_^apsB1Hh^?@#dSYIwL~r^nK2v$+cM*8%vV!s&XVDLi-Zb)1m^Ccz{');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', 'de_DE');



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
if (!defined('WP_DEBUG')) {
	define('WP_DEBUG', false);
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');


/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
