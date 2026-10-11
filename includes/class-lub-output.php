<?php
/**
 * Injects the "Last updated" badge into post content.
 *
 * @package Last_Updated_Badge
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class LUB_Output {

    public static function init(): void {
        add_filter( 'the_content', [ __CLASS__, 'inject_badge' ] );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue' ] );
    }

    public static function inject_badge( string $content ): string {
        if ( ! is_singular() || is_admin() ) { return $content; }

        $options    = self::get_options();
        $post_types = (array) ( $options['post_types'] ?? ['post'] );

        if ( ! in_array( get_post_type(), $post_types, true ) ) { return $content; }

        $post        = get_post();
        $modified    = get_post_modified_time( 'U', false, $post );
        $published   = get_post_time( 'U', false, $post );

        // Only show when the post has actually been updated after publishing.
        if ( $modified <= $published ) { return $content; }

        $date_format = sanitize_text_field( $options['date_format'] ?? 'F j, Y' );
        $formatted   = wp_date( $date_format, $modified );
        $label_template = $options['label'] ?? 'Last updated: {date}';
        $label       = str_replace( '{date}', $formatted, $label_template );

        $badge = sprintf(
            '<p class="lub-badge"><time class="lub-time" datetime="%s">%s</time></p>',
            esc_attr( wp_date( 'c', $modified ) ),
            esc_html( $label )
        );

        return 'before' === ( $options['position'] ?? 'before' )
            ? $badge . $content
            : $content . $badge;
    }

    public static function enqueue(): void {
        if ( ! is_singular() ) { return; }
        wp_enqueue_style( 'lub-front', LUB_PLUGIN_URL . 'assets/css/front.css', [], LUB_VERSION );
    }

    private static function get_options(): array {
        return wp_parse_args( get_option( LUB_OPTIONS_KEY, [] ), [
            'date_format' => 'F j, Y',
            'position'    => 'before',
            'post_types'  => ['post'],
            'label'       => 'Last updated: {date}',
        ] );
    }
}
