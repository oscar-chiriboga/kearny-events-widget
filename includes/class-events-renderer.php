<?php
namespace Kearny_Events;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Renders the events grid HTML. Used by both the Elementor widget and the shortcode.
 */
class Events_Renderer {

    /**
     * Render the events grid.
     *
     * @param array $settings {
     *     @type int    $count            Number of events to show.
     *     @type int    $columns          Columns (1-4).
     *     @type string $category         Category slug filter.
     *     @type bool   $show_image       Whether to show featured image.
     *     @type bool   $show_date        Whether to show event date.
     *     @type bool   $show_venue       Whether to show venue.
     *     @type bool   $show_excerpt     Whether to show excerpt.
     *     @type int    $excerpt_length   Excerpt word count.
     *     @type string $image_ratio      Aspect ratio (e.g. '4/3', '16/9', '1/1').
     *     @type bool   $show_button      Whether to show "more events" button.
     *     @type string $button_text      Button text.
     *     @type string $button_url       Button URL.
     *     @type string $empty_message    Text shown when no upcoming events.
     * }
     * @return string HTML output.
     */
    public static function render( $settings ) {
        // Only enqueue on real front-end page loads, not AJAX or REST requests.
        if ( ! wp_doing_ajax() && ! defined( 'REST_REQUEST' ) ) {
            wp_enqueue_style( 'kearny-events-widget' );
            wp_enqueue_script( 'kearny-events-widget' );
        }

        $defaults = [
            'count'                  => 8,
            'columns'                => 4,
            'category'               => '',
            'show_image'             => true,
            'show_date'              => true,
            'show_venue'             => false,
            'show_excerpt'           => true,
            'excerpt_length'         => 20,
            'image_ratio'            => '3/2',
            'show_button'            => true,
            'button_text'            => 'View All Events',
            'button_url'             => '/events',
            'empty_message'          => 'No upcoming events. Check back soon!',
            // Submit event button (top of widget)
            'show_submit_button'     => true,
            'submit_button_text'     => 'Submit an Event',
            'submit_button_url'      => '/events/community/add/',
            'submit_button_align'    => 'right',
            'submit_logged_in_only'  => false,
            // Layout mode
            'layout_mode'            => 'carousel',
            'show_arrows'            => true,
            // Placeholder card colors (cycled when no featured image)
            'placeholder_colors'     => [ '#1e4d8c', '#145c38', '#4a2880', '#8c3d00', '#6b1212' ],
        ];
        $settings = wp_parse_args( $settings, $defaults );

        $events = Events_Query::get_upcoming( [
            'count'    => $settings['count'],
            'category' => $settings['category'],
        ] );

        // Fall back to TEC's archive link only if no URL is set at all.
        if ( empty( $settings['button_url'] ) && function_exists( 'tribe_get_events_link' ) ) {
            $settings['button_url'] = tribe_get_events_link();
        }

        ob_start();

        if ( empty( $events ) ) {
            printf(
                '<div class="kearny-events kearny-events--empty"><p>%s</p></div>',
                esc_html( $settings['empty_message'] )
            );
            return ob_get_clean();
        }

        $columns = max( 1, min( 4, absint( $settings['columns'] ) ) );
        // Never show more columns than we have events — avoids orphaned empty space.
        $columns = min( $columns, count( $events ) );
        $ratio   = self::sanitize_ratio( $settings['image_ratio'] );

        $wrapper_style = sprintf(
            '--kearny-columns: %d; --kearny-image-ratio: %s;',
            $columns,
            $ratio
        );

        // Determine whether to render the submit button.
        $render_submit = $settings['show_submit_button'] && ! empty( $settings['submit_button_url'] );
        if ( $render_submit && $settings['submit_logged_in_only'] && ! is_user_logged_in() ) {
            $render_submit = false;
        }

        $submit_align_class = 'kearny-events__header--' . self::sanitize_align( $settings['submit_button_align'] );

        $layout_mode = ( 'carousel' === $settings['layout_mode'] ) ? 'carousel' : 'grid';
        $show_arrows = $settings['show_arrows'] && 'carousel' === $layout_mode;
        ?>
        <div class="kearny-events kearny-events--<?php echo esc_attr( $layout_mode ); ?>" style="<?php echo esc_attr( $wrapper_style ); ?>">

            <?php if ( $render_submit ) : ?>
                <div class="kearny-events__header <?php echo esc_attr( $submit_align_class ); ?>">
                    <a class="kearny-events__submit-button" href="<?php echo esc_url( $settings['submit_button_url'] ); ?>">
                        <svg class="kearny-events__submit-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        <span><?php echo esc_html( $settings['submit_button_text'] ); ?></span>
                    </a>
                </div>
            <?php endif; ?>
            <?php if ( 'carousel' === $layout_mode ) : ?>
                <div class="kearny-events__carousel-wrapper">
                    <?php if ( $show_arrows ) : ?>
                        <button type="button" class="kearny-events__arrow kearny-events__arrow--prev" aria-label="<?php esc_attr_e( 'Previous events', 'kearny-events' ); ?>" data-kearny-arrow="prev">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>
                    <?php endif; ?>
                    <div class="kearny-events__track" role="region" aria-label="<?php esc_attr_e( 'Upcoming events', 'kearny-events' ); ?>" tabindex="0">
            <?php else : ?>
            <div class="kearny-events__grid">
            <?php endif; ?>
                <?php foreach ( $events as $event ) :
                    $meta        = Events_Query::get_event_meta( $event->ID );
                    $permalink   = get_permalink( $event->ID );
                    $has_thumb   = has_post_thumbnail( $event->ID );
                    $date_string = Events_Query::format_event_date(
                        $meta['start_ts'],
                        $meta['end_ts'],
                        $meta['all_day']
                    );
                    ?>
                    <article class="kearny-events__card">
                        <?php if ( $settings['show_image'] ) : ?>
                            <a class="kearny-events__image-link" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( get_the_title( $event ) ); ?>">
                                <?php if ( $has_thumb ) : ?>
                                    <?php echo get_the_post_thumbnail(
                                        $event->ID,
                                        'large',
                                        [
                                            'class'   => 'kearny-events__image',
                                            'loading' => 'lazy',
                                            'alt'     => esc_attr( get_the_title( $event ) ),
                                            'style'   => 'position:absolute;inset:0;width:100%;height:100%;max-width:none;object-fit:cover;',
                                            'width'   => false,
                                            'height'  => false,
                                        ]
                                    ); ?>
                                <?php else : ?>
                                    <?php
                                    $colors      = $settings['placeholder_colors'];
                                    $color_index = array_search( $event->ID, array_column( $events, 'ID' ) );
                                    $bg_color    = $colors[ $color_index % count( $colors ) ];
                                    $categories  = get_the_terms( $event->ID, 'tribe_events_cat' );
                                    $cat_name    = ( $categories && ! is_wp_error( $categories ) ) ? $categories[0]->name : '';
                                    ?>
                                    <div class="kearny-events__image kearny-events__image--placeholder" style="background-color: <?php echo esc_attr( $bg_color ); ?>" aria-hidden="true">
                                        <?php if ( $cat_name ) : ?>
                                            <span class="kearny-events__placeholder-cat"><?php echo esc_html( strtoupper( $cat_name ) ); ?></span>
                                        <?php endif; ?>
                                        <span class="kearny-events__placeholder-title"><?php echo esc_html( get_the_title( $event ) ); ?></span>
                                    </div>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>

                        <div class="kearny-events__body">
                            <h3 class="kearny-events__title">
                                <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $event ) ); ?></a>
                            </h3>

                            <?php if ( $settings['show_date'] && $date_string ) : ?>
                                <p class="kearny-events__date"><?php echo esc_html( $date_string ); ?></p>
                            <?php endif; ?>

                            <?php if ( $settings['show_venue'] && ! empty( $meta['venue_name'] ) ) : ?>
                                <p class="kearny-events__venue"><?php echo esc_html( $meta['venue_name'] ); ?></p>
                            <?php endif; ?>

                            <?php if ( $settings['show_excerpt'] ) :
                                $excerpt = self::get_trimmed_excerpt( $event, absint( $settings['excerpt_length'] ) );
                                if ( $excerpt ) : ?>
                                    <p class="kearny-events__excerpt"><?php echo esc_html( $excerpt ); ?></p>
                                <?php endif;
                            endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php if ( 'carousel' === $layout_mode ) : ?>
                    </div><!-- .kearny-events__track -->
                    <?php if ( $show_arrows ) : ?>
                        <button type="button" class="kearny-events__arrow kearny-events__arrow--next" aria-label="<?php esc_attr_e( 'Next events', 'kearny-events' ); ?>" data-kearny-arrow="next">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>
                    <?php endif; ?>
                </div><!-- .kearny-events__carousel-wrapper -->
            <?php else : ?>
            </div><!-- .kearny-events__grid -->
            <?php endif; ?>

            <?php if ( $settings['show_button'] && ! empty( $settings['button_url'] ) ) : ?>
                <div class="kearny-events__button-wrap">
                    <a class="kearny-events__button" href="<?php echo esc_url( $settings['button_url'] ); ?>">
                        <?php echo esc_html( $settings['button_text'] ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * Ensure the ratio string is safe for inline CSS.
     */
    private static function sanitize_ratio( $ratio ) {
        $ratio = trim( (string) $ratio );
        // Allow values like "4/3", "16/9", "1/1", "3/2".
        if ( preg_match( '#^\d{1,2}\s*/\s*\d{1,2}$#', $ratio ) ) {
            return preg_replace( '/\s+/', '', $ratio );
        }
        return '4/3';
    }

    /**
     * Ensure alignment is one of the allowed values.
     */
    private static function sanitize_align( $align ) {
        $allowed = [ 'left', 'center', 'right' ];
        return in_array( $align, $allowed, true ) ? $align : 'right';
    }

    /**
     * Get a trimmed excerpt, preferring the manual excerpt if set.
     */
    private static function get_trimmed_excerpt( $post, $word_count ) {
        if ( ! empty( $post->post_excerpt ) ) {
            return wp_trim_words( $post->post_excerpt, $word_count, '…' );
        }
        return wp_trim_words( wp_strip_all_tags( $post->post_content ), $word_count, '…' );
    }
}
