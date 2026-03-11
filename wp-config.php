<?php
define( 'WP_CACHE', true );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'beyond' );

/** Database username */
define( 'DB_USER', 'admin' );

/** Database password */
define( 'DB_PASSWORD', 'admin@1223' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '<k;_YKu@-z/DNQ9kkF?qT-m!:+]m[it? FCc#0ic~Bad-_WTVU;Y^mDg,7G.TB=Z' );
define( 'SECURE_AUTH_KEY',   '=$nkQmJ&=*0:?Dit(X`WRJ&>_1ieUV[X8$E{YNmD#M~E8A~u{Lu,yb9-Gp;Pa NW' );
define( 'LOGGED_IN_KEY',     'Wim0ur)lCp$5Dd0])JJrK3~r+WVl.M(4Lm4?vIL [e@^ a9(;5g.:Zl|v$4j2t(+' );
define( 'NONCE_KEY',         'N1H^gk~w~euf@i#f+$6(`QVxd|^pP&*b;2PXL bdGKx+|>6Q.5}2q$Y2K}38Ewr=' );
define( 'AUTH_SALT',         '`!}0+6EP}zX%oy1/vRn<s4qtp_OifwY*k[9Ek$*3{H/RT[$h[*U8{K[HdT.bePs2' );
define( 'SECURE_AUTH_SALT',  '~RlB@tI)cDpzFUL-*HP7bn9TA}F,;(VT)&^`kHEQ7$#0_N9mc3~^K(:b:^%dv(jv' );
define( 'LOGGED_IN_SALT',    '5Knx9;{zvi#^$CU>Mfox<r{Os|!2tor]3;tnj~2HFsW}/*LbI5PWSUdZ,%OGop#~' );
define( 'NONCE_SALT',        '}E,w?ksUDpQS<t0SF BOTx*xskU/C,RCuntmT_eG^uWm9C3iGsXjjboHPO<p(g6W' );
define( 'WP_CACHE_KEY_SALT', 'TvB2JQ[?osw=Q^i,o;%<-Z9I`dKD=q.3X4D&8q6U)i!]UDg J#RwtP4%^1dS^h6c' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '7a435fe17afb0a180cc70258196f232e' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
