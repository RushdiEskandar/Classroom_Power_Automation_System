<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'ControlDB');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'rushdi');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'v,N<`/>h^>ig v(cnBqU0o.1#I[rDJtKQ1dr=ydzBJP)]4D97bZSQ)Ys:<wP]$_m');
define('SECURE_AUTH_KEY',  '$pHa$}Kb#*[Et^U`6U2o1XMyyMTw;fACFE[E2oN2uEMvK59wwbX*Bq`sT|d#}UC8');
define('LOGGED_IN_KEY',    'mV5xKuyj6sW=05m$8zLJOR<1Fyoo+H}Z4#7,`p!E8 00f5u06$2kv3>1/{)#v(-0');
define('NONCE_KEY',        'b^7*lksxbY.j+{rQ)d$0sU?ldW;Uk?t3m9)OFN]`0dV:+4m;^rEr-13};4|cIQYq');
define('AUTH_SALT',        '0AxIv2`MfhGYlek1qsMSc0T?i&*}Vm_<4V?tYIT<hEF)wq7Zywze>$WYULQ;lDj,');
define('SECURE_AUTH_SALT', '1s0OWW}p|tz(E)<vz`@0%FCTzWGX<>?Yz<qwI(]vqSM4lD8A5PL!9lK1y3@Wff8.');
define('LOGGED_IN_SALT',   'p2S:x2gqc^y7.vOM~8hT~zlico#z[n*65es3Aw]EY(MbON-Pz Cox<JiY?e(6Hy9');
define('NONCE_SALT',       '|AtXGom/CM:5*syzcyPJO|y~J:J,?*@K%JH#G~Fw?*P<6qnMSxg9=Z^/mTLd ^$7');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');


