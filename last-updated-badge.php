<?php
/**
 * Plugin Name:       Last Updated Badge
 * Plugin URI:        https://github.com/jdyach/last-updated-badge
 * Description:       Displays a "Last updated: [date]" notice on posts. Configurable date format, display position, and which post types to show it on.
 * Version:           1.0.0
 * Author:            Jacqui Dyachenko
 * Author URI:        https://jacquid.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       last-updated-badge
 * Requires at least: 6.0
 * Requires PHP:      8.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'LUB_VERSION',    '1.0.0' );
define( 'LUB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LUB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LUB_OPTIONS_KEY', 'lub_settings' );

require_once LUB_PLUGIN_DIR . 'includes/class-lub-output.php';
require_once LUB_PLUGIN_DIR . 'includes/class-lub-settings.php';

function lub_init(): void {
    LUB_Output::init();
    LUB_Settings::init();
}
add_action( 'plugins_loaded', 'lub_init' );

register_activation_hook( __FILE__, function (): void {
    if ( false === get_option( LUB_OPTIONS_KEY ) ) {
        add_option( LUB_OPTIONS_KEY, [
            'date_format' => 'F j, Y',
            'position'    => 'before',
            'post_types'  => ['post'],
            'label'       => 'Last updated: {date}',
        ] );
    }
} );
