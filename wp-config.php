<?php
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
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',          'tL_53J5.Q(^`+a1!HCmw:X~#cRJvl(L;@.sxAR>oU5AJEQgjVAk>><kw1uyN #3?' );
define( 'SECURE_AUTH_KEY',   '_Q@8=DEW0rk4`xtZ:D&k<Bue~Q[y<~3S@zPw]t#}U8W/~IZjkY!wgXj:Py{4Tl4>' );
define( 'LOGGED_IN_KEY',     '$=er)rF`^~np:$z SEMUm{>mAhk$sWE4|p|ne;XA!O?N)RTAY;t.S_K+Jf%Ee9UZ' );
define( 'NONCE_KEY',         ':dWT{;Zz4ectfUOzZ&7OjB:fC)bwD0XWBwnR.)u?eZcCZlI&7{b|Y+5.aMBBJQ}~' );
define( 'AUTH_SALT',         '![<+p):SdjnjX,(_w@}hVr8=;:W|o=,i|(q`tzT:joVuTj3B(!l/-=<pj|Pf}iRP' );
define( 'SECURE_AUTH_SALT',  'T(G!}nh_V2Td&`yW^S9?s:d&F]iM9H,>lBOkr@qs69Q}DY@]9<8w&r.)2E;wH#Yc' );
define( 'LOGGED_IN_SALT',    'Tpp~`yCL/GEXiTJdb^%SSvYeSF0QI9s_(r+I}CIm][`@&vW:+CGX%j,gB3%7%>hR' );
define( 'NONCE_SALT',        'VYE^.pc)TPV AZE$(cex?bm-;T4&zF;sU ]+J1!{EIDeSZFOc&s=nR^L!!Se59qo' );
define( 'WP_CACHE_KEY_SALT', '&]|=hX2,{r3fBESn~fS#tVL55R~lWjEQ@/F9s=<l]gHe_Y%@DCqpk.0WM*K+8#K$' );


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

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
