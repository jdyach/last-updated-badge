<?php
/**
 * Settings page under Settings > Last Updated Badge.
 *
 * @package Last_Updated_Badge
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class LUB_Settings {

    const PAGE_SLUG    = 'last-updated-badge';
    const OPTION_GROUP = 'lub_settings_group';

    public static function init(): void {
        add_action( 'admin_menu', [ __CLASS__, 'register_page' ] );
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
    }

    public static function register_page(): void {
        add_options_page(
            __( 'Last Updated Badge', 'last-updated-badge' ),
            __( 'Last Updated Badge', 'last-updated-badge' ),
            'manage_options',
            self::PAGE_SLUG,
            [ __CLASS__, 'render_page' ]
        );
    }

    public static function register_settings(): void {
        register_setting( self::OPTION_GROUP, LUB_OPTIONS_KEY, [ 'sanitize_callback' => [ __CLASS__, 'sanitize' ] ] );
    }

    public static function sanitize( mixed $input ): array {
        return [
            'date_format' => sanitize_text_field( $input['date_format'] ?? 'F j, Y' ),
            'position'    => in_array( $input['position'] ?? '', [ 'before', 'after' ], true ) ? $input['position'] : 'before',
            'label'       => sanitize_text_field( $input['label'] ?? 'Last updated: {date}' ),
            'post_types'  => array_map( 'sanitize_key', (array) ( $input['post_types'] ?? ['post'] ) ),
        ];
    }

    public static function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'Permission denied.', 'last-updated-badge' ) ); }
        $options   = wp_parse_args( get_option( LUB_OPTIONS_KEY, [] ), [ 'date_format' => 'F j, Y', 'position' => 'before', 'label' => 'Last updated: {date}', 'post_types' => ['post'] ] );
        $all_types = get_post_types( [ 'public' => true ], 'objects' );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Last Updated Badge', 'last-updated-badge' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( self::OPTION_GROUP ); ?>
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e( 'Label format', 'last-updated-badge' ); ?></th>
                        <td><input type="text" name="<?php echo esc_attr( LUB_OPTIONS_KEY ); ?>[label]" value="<?php echo esc_attr( $options['label'] ); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e( 'Use {date} as the placeholder. Example: "Last updated: {date}"', 'last-updated-badge' ); ?></p></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Date format', 'last-updated-badge' ); ?></th>
                        <td><input type="text" name="<?php echo esc_attr( LUB_OPTIONS_KEY ); ?>[date_format]" value="<?php echo esc_attr( $options['date_format'] ); ?>" class="regular-text" />
                        <p class="description"><?php printf( esc_html__( 'PHP date format string. Preview: %s', 'last-updated-badge' ), '<strong>' . esc_html( wp_date( $options['date_format'] ) ) . '</strong>' ); ?></p></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Position', 'last-updated-badge' ); ?></th>
                        <td>
                            <label><input type="radio" name="<?php echo esc_attr( LUB_OPTIONS_KEY ); ?>[position]" value="before" <?php checked( $options['position'], 'before' ); ?> /> <?php esc_html_e( 'Before content', 'last-updated-badge' ); ?></label><br>
                            <label><input type="radio" name="<?php echo esc_attr( LUB_OPTIONS_KEY ); ?>[position]" value="after"  <?php checked( $options['position'], 'after' );  ?> /> <?php esc_html_e( 'After content',  'last-updated-badge' ); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Post types', 'last-updated-badge' ); ?></th>
                        <td><?php foreach ( $all_types as $type ) : ?>
                            <label style="display:block;margin-bottom:4px">
                                <input type="checkbox" name="<?php echo esc_attr( LUB_OPTIONS_KEY ); ?>[post_types][]" value="<?php echo esc_attr( $type->name ); ?>" <?php checked( in_array( $type->name, (array) $options['post_types'], true ) ); ?> />
                                <?php echo esc_html( $type->label ); ?>
                            </label>
                        <?php endforeach; ?></td>
                    </tr>
                </table>
                <?php submit_button( __( 'Save Settings', 'last-updated-badge' ) ); ?>
            </form>
        </div>
        <?php
    }
}
