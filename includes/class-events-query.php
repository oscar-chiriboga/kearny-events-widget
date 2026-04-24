<?php
namespace Kearny_Events;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles querying upcoming events from The Events Calendar.
 */
class Events_Query {

    /**
     * Get upcoming events ordered by start date.
     *
     * @param array $args {
     *     @type int    $count    Number of events to return.
     *     @type string $category Event category slug (optional).
     * }
     * @return \WP_Post[]
     */
    public static function get_upcoming( $args = [] ) {
        $defaults = [
            'count'    => 4,
            'category' => '',
        ];
        $args = wp_parse_args( $args, $defaults );

        // Cache key includes all query-affecting params so different widgets
        // get their own cached result and the cache auto-differentiates.
        $cache_key = 'kearny_events_' . md5( $args['count'] . '|' . $args['category'] );
        $cached    = get_transient( $cache_key );
        if ( false !== $cached ) {
            return $cached;
        }

        $query_args = [
            'post_type'      => 'tribe_events',
            'post_status'    => 'publish',
            'posts_per_page' => absint( $args['count'] ),
            'meta_key'       => '_EventStartDate',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
            'meta_query'     => [
                [
                    'key'     => '_EventEndDate',
                    'value'   => current_time( 'mysql' ),
                    'compare' => '>=',
                    'type'    => 'DATETIME',
                ],
            ],
            'no_found_rows'  => true,
        ];

        if ( ! empty( $args['category'] ) ) {
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'tribe_events_cat',
                    'field'    => 'slug',
                    'terms'    => $args['category'],
                ],
            ];
        }

        $query = new \WP_Query( $query_args );
        $posts = $query->posts;

        // Cache for 10 minutes. Cleared automatically when an event is saved
        // via the hook registered in the main plugin file.
        set_transient( $cache_key, $posts, 10 * MINUTE_IN_SECONDS );

        return $posts;
    }

    /**
     * Get formatted event meta for a given event post.
     */
    public static function get_event_meta( $post_id ) {
        $start_raw = get_post_meta( $post_id, '_EventStartDate', true );
        $end_raw   = get_post_meta( $post_id, '_EventEndDate', true );
        $all_day   = get_post_meta( $post_id, '_EventAllDay', true );

        $start_ts = $start_raw ? strtotime( $start_raw ) : 0;
        $end_ts   = $end_raw ? strtotime( $end_raw ) : 0;

        // Try to use TEC's venue helper if available, otherwise fall back.
        $venue_name = '';
        if ( function_exists( 'tribe_get_venue' ) ) {
            $venue_name = tribe_get_venue( $post_id );
        }

        return [
            'start_ts'   => $start_ts,
            'end_ts'     => $end_ts,
            'all_day'    => ( 'yes' === $all_day ),
            'venue_name' => $venue_name,
        ];
    }

    /**
     * Format event date for display.
     *
     * @param int  $start_ts Unix timestamp of start.
     * @param int  $end_ts   Unix timestamp of end.
     * @param bool $all_day  Whether event is all-day.
     * @return string
     */
    public static function format_event_date( $start_ts, $end_ts, $all_day ) {
        if ( ! $start_ts ) {
            return '';
        }

        $date_format = get_option( 'date_format', 'F j, Y' );
        $time_format = get_option( 'time_format', 'g:i a' );

        $same_day = $end_ts && wp_date( 'Y-m-d', $start_ts ) === wp_date( 'Y-m-d', $end_ts );

        if ( $all_day ) {
            if ( $same_day || ! $end_ts ) {
                return date_i18n( $date_format, $start_ts );
            }
            return sprintf(
                '%s – %s',
                date_i18n( $date_format, $start_ts ),
                date_i18n( $date_format, $end_ts )
            );
        }

        if ( $same_day ) {
            return sprintf(
                '%s · %s – %s',
                date_i18n( $date_format, $start_ts ),
                date_i18n( $time_format, $start_ts ),
                date_i18n( $time_format, $end_ts )
            );
        }

        if ( ! $end_ts ) {
            return sprintf(
                '%s · %s',
                date_i18n( $date_format, $start_ts ),
                date_i18n( $time_format, $start_ts )
            );
        }

        return sprintf(
            '%s – %s',
            date_i18n( $date_format . ' · ' . $time_format, $start_ts ),
            date_i18n( $date_format . ' · ' . $time_format, $end_ts )
        );
    }
}
