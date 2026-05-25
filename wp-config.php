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
define( 'DB_NAME', 'u937561055_Jz1AK' );

/** Database username */
define( 'DB_USER', 'u937561055_YWVJq' );

/** Database password */
define( 'DB_PASSWORD', 'vJykHy3Tp1' );

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
define( 'AUTH_KEY',          '(0j n7CS2$O9VbTxeBqC?wv4fwJfq:+gv{@mDnBQ!(X4<#d5765+]!3M.Je!dab@' );
define( 'SECURE_AUTH_KEY',   'b*!U|~d`%54XkvEF`mbAgJ4DrSuP$.`NGC`Zd#.4cm&FfuJ6S7%!ZVHJTs7fZm *' );
define( 'LOGGED_IN_KEY',     'WsmLi; 6qAe#{$/ZhmNisk2n`7cPvt]IX#FL.$d(3V}uwu]T,@R*.&*A^XX#kOtg' );
define( 'NONCE_KEY',         'F*z=$VIcC%O/!yedlgr|<t _!F9:GtgQ{SBLWW[51=sD5NW1w|19k.Ml2KTr$}mC' );
define( 'AUTH_SALT',         '=ma;@bQVbs{3wCmB=U-y>>Duk{?I[!e1Nr X@.u]K)9(l%Dj.ptW$ZlNygh1%A(h' );
define( 'SECURE_AUTH_SALT',  '_i!&P9~Bl/Xv:R+Fg*6p25vq49NCnet)Q,jd8O}nh.!)eV2_)~fvcY-L%SBoseQD' );
define( 'LOGGED_IN_SALT',    '~u=IrH|EkDh;Gpojc3f$7yp{oJQOQ_X~1W,uH0I.;9xlhp{V0[h}Qu$t;GCx2CJ~' );
define( 'NONCE_SALT',        'e=#1wlQMN}Jrd{)UQe5M*kS4,7zL7i_%pC2)43YAX2qwj9DYp{Pvaj/I<;{W->0X' );
define( 'WP_CACHE_KEY_SALT', 'K-B#f5o20h}=,jrKfO!rv:pWm35Whif&?H.=eES.&4r,yAw;Tc=zW|2@H/0AD_*q' );


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
	define( 'WP_DEBUG', true );
}

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '454b3b34b4593318a62ee162b5fe44a8' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
