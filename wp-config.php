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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', '62c');

/** MySQL database username */
define('DB_USER', '62c');

/** MySQL database password */
define('DB_PASSWORD', 'LS87TpSD7edhRbzc');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         'ya=.#QC|0=A/2I},96GfVpbuPPRWxPF~|,.C<ZrW82)K>gmARAE|]Ah&63=`M$@$');
define('SECURE_AUTH_KEY',  'x0$U681Tgv~CgN|l~.kSV*a|(8?77HqHj;$<v:MuqloY-B+C|gN<~dmtu3peCpe|');
define('LOGGED_IN_KEY',    'iD;|Vh&EZm<baFIn05Xh@hqS^6+yV`UtD<F0h1Dl &HJaJQqwQu.%=u2(I;^htEf');
define('NONCE_KEY',        'G]61#Do1`,jc-otK^83!WNc*+Y.{h]]/6cD*.RnS~}64icZzQ#GOl; ^ji$6?)BZ');
define('AUTH_SALT',        '=-88QzZS`$|4D[$5]tQQ5OL|[*O8nZ3PQM.tD.j];G9$8Nv4T+[(EM;y4v,+$4*?');
define('SECURE_AUTH_SALT', 'O+]}dyYBM!.XkHA}!z|oBQ?Tw6Pin?yXTkQk2Za{6bYj8P10zH6tZwX:+OH|}_bE');
define('LOGGED_IN_SALT',   ')4+`~RCCIA)|#z8q-k00g/QNo!fgIXUyuIz*,;`5uLy(F=CU-(<J+Dbp@|jq/87]');
define('NONCE_SALT',       'NH/MN<Z*,9w~/!YQo|LbJ|ZD/i=@+Io xt@Gnga<Ahxbag/Cc1*|J|DyO>h!p<Mv');

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
define('WPLANG', '');

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

