<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
$httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '' ;	
switch ($httpHost) {	
    case 'w7dev1' : 
    case 'w7dev1.thegoodagency.co.uk' : 
    case 'localhost' : 
        define('WP_HOME','http://localhost/doe');
        define('WP_SITEURL','http://localhost/doe');	
        define('DB_NAME', 'doe');
        define('DB_USER', 'doe');
        define('DB_PASSWORD', 'zhap1091');
        define('DB_HOST', 'localhost');
    break;
     
    case 'doe.thegoodagencydigital.co.uk' : 
        define('WP_HOME','http://doe.thegoodagencydigital.co.uk');
        define('WP_SITEURL','http://doe.thegoodagencydigital.co.uk');	
        define('DB_NAME', 'doe');
        define('DB_USER', 'doe');
        define('DB_PASSWORD', 'zhap1091');
        define('DB_HOST', 'localhost');
    break;

    case 'www.singyourheartout.org.uk' : 
    case 'rvswebsolution.cloudapp.net' : 
    default:
        define('WP_HOME','http://www.goodagency.co.uk');
        define('WP_SITEURL','http://www.goodagency.co.uk');	
        define('DB_NAME', 'cruk_undierun');
        define('DB_USER', 'und13run');
        define('DB_PASSWORD', 's9ueDzDUs');
        define('DB_HOST', 'localhost');
    break;
}
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */


/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */

define('AUTH_KEY',         'f_BX,4YxD$~GU y,g=]PG7cq19J!=vH,5qCr-?E5@ /?&*^yw|q(A3?_j/rM`Av#');
define('SECURE_AUTH_KEY',  'zb+.x/lF%kreL2*fV}HZGL<aku;#b=VHmBBGQ;.:?(`<YRzaD2IvL<S]d2IoZDY-');
define('LOGGED_IN_KEY',    'k<xDJNM,1)XH2tsvZL6|EcMEU+_fPN{$+D8#Z+9mX!W*aJp/c$*W+4;9g^kVkJk|');
define('NONCE_KEY',        'dyhUdpvu^kWV1IGzPJCSuTp)}!8:)m|w:0W9IUg@w:F2wq>1b{BjzzzG!9LuHGtN');
define('AUTH_SALT',        '0R4A$6)(~H<-3F-b&z1~hw@X&k/$-6FvID!+|M8Bq.oj{Jd)XUY(CN6[QCU?AW!_');
define('SECURE_AUTH_SALT', '@`jN&4I@J-V%d@Jk$(t0Tb7>+#)jr~@,|#3J8bj<hEPlD@t3TQfV5f^0]wIPppub');
define('LOGGED_IN_SALT',   'c>7xS[vXJc.u!Nw.-yGX1YcwF2u^^R[P~[{ady+1uDsHtpNda%T!!rPx^[q~s ^C');
define('NONCE_SALT',       'a|ht`Fki:2;|6L ]#q1+:<!&h!3WHlD_A[X1iS<?1= rVSmvM<I?|?JXvzj*%Qq6');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
