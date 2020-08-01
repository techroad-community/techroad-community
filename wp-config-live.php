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
define('DB_NAME', 'techroad');

/** MySQL database username */
define('DB_USER', 'techroad');

/** MySQL database password */
define('DB_PASSWORD', 'dzAexmpNAWtkYzKm');

/** MySQL hostname */
define('DB_HOST', 'db01.shoplic.internal');

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
define('AUTH_KEY',         '&;xdY}j*=(RVQ`J5H077%i{~d)IRorh3w>;%%5#Q2Ht+r~^.J[//5).0]@mevU5/');
define('SECURE_AUTH_KEY',  '3XE;N<GfUic-}3BsjqobC]{O%<x>/#bP+.I]`r,F8z-tL<p1qLUjdK^ey621}bYl');
define('LOGGED_IN_KEY',    't>PqbQ!zR%nkTkRLvAG7}oV*kA5G&>:?cPD7?)~cly~b:(2GY}iahXt5Q}A3xB,W');
define('NONCE_KEY',        ',%~i&l.On(2ny5w^@7a:=esZK/FP}yPATDM?bD[;[JD.h-IC)T}eM-gDa{~F^#Bz');
define('AUTH_SALT',        '!Z)`[i-SW;M&@BErm!=vTX%ICxj2%@go1PdzR_eo0zQ/R)Zwk&XtA?liOt1Q4fM+');
define('SECURE_AUTH_SALT', 'y4]oSfq}U7ZF13(H.+O*?ocp}[G2%YnKVsq_9{eY{QCMq(J#XG^, Bj)~mZO|B|n');
define('LOGGED_IN_SALT',   ':gro@Wk1^47jV,m|*0MD&[`P%4Q,+L3FNdW[SG@};sGmJ*{7zL5/jhog[G4(8J-x');
define('NONCE_SALT',       'I@I$eBns[f}zo0MzVd;Z0dbNRUPL|<uXv}_d[ws_o$aJzErzAIJOU1Vx:j*FU)8Q');

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
