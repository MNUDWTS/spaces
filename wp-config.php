<?php

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'admin_spaces');

/** Database username */
define('DB_USER', 'spaces');

/** Database password */
define('DB_PASSWORD', '2s036~cxZ');

/** Database hostname */
define('DB_HOST', 'localhost:3306');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

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
define('AUTH_KEY',         ']6fdb<kF]sSOkxioRP>$Zykt}wsKK52q462.YzfDS9`Dt a/-L}Y|Nl(-RBtN;hl');
define('SECURE_AUTH_KEY',  'Yb?WtP[{)-]>,IK(|i=]s&<:7^zikI|sY2W~Hv+Vx]b;?hs*fEx~2ZnGq!;z}5[T');
define('LOGGED_IN_KEY',    '.I[:U%9u$5z&>Xc8S/Asg4wh(C#|ghK=2ef7g|GQdX(+A7S$*B<&dDdJ7zSDo|e&');
define('NONCE_KEY',        '.eHiL3GMO%1iHme(gV5e7]k3UM+lp@ONi=wEgkZ]mpgi1(bp.C(N+b]*=&l`1/t+');
define('AUTH_SALT',        ' :`96=LnB|kxA>l{s*eH:9NQ|f^~Fz8#fyku8:BT*nvfj_;+F_)WVlhUlMYA|~2F');
define('SECURE_AUTH_SALT', '5! AH5,=Oq1{ZKRIi?@]+F~96%cayz(h9bL`x^DTpU?2fd3wz5Pf3abaJL?lzmG}');
define('LOGGED_IN_SALT',   '*T]VuCx[uZy0rWZXF^jYot:C>)m&WY*2xz3P,v -`Y+hme,uKptDV+^{;!RyI}j2');
define('NONCE_SALT',       '~[y~S0]a7eg0RA#!?:P8~VtS1_CB@}l@|H+kP(9PA2ySX_]3P$92o:WFyOgw5I(R');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
/* Add any custom values between this line and the "stop editing" line. */

define('AUTOMATIC_UPDATER_DISABLED', true);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (! defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
