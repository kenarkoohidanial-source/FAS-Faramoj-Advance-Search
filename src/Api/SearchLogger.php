<?php
namespace Faramoj\AdvancedSearch\Api;

class SearchLogger {
    /**
     * Log search queries statistics (total count & popular terms tracking)
     */
    public function log_search_stats( $term ) {
        if ( empty( $term ) || strlen( $term ) < 3 ) {
            return;
        }

        // Get IP
        $ip = '';
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }

        $timestamp = current_time( 'mysql' );

        // Standardize lowercase matching
        $term_clean = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( $term ) ) : strtolower( trim( $term ) );

        // Defer database writing to the shutdown hook so it does not block the REST API response
        add_action( 'shutdown', function() use ( $term_clean, $ip, $timestamp ) {
            $stats = get_option( 'fas_search_stats', array( 'total_count' => 0, 'terms' => [] ) );

            if ( ! isset( $stats['total_count'] ) ) {
                $stats['total_count'] = 0;
            }
            $stats['total_count']++;

            if ( ! isset( $stats['terms'] ) ) {
                $stats['terms'] = array();
            }

            if ( ! isset( $stats['terms'][ $term_clean ] ) ) {
                $stats['terms'][ $term_clean ] = array(
                    'count' => 0,
                    'logs'  => array()
                );
            } else if ( ! is_array( $stats['terms'][ $term_clean ] ) ) {
                // Migrate old format
                $old_count = $stats['terms'][ $term_clean ];
                $stats['terms'][ $term_clean ] = array(
                    'count' => $old_count,
                    'logs'  => array()
                );
            }

            $stats['terms'][ $term_clean ]['count']++;

            // Add IP log, keep max 10 recent logs per term to prevent bloat
            array_unshift( $stats['terms'][ $term_clean ]['logs'], array( 'ip' => $ip, 'time' => $timestamp ) );
            if ( count( $stats['terms'][ $term_clean ]['logs'] ) > 10 ) {
                $stats['terms'][ $term_clean ]['logs'] = array_slice( $stats['terms'][ $term_clean ]['logs'], 0, 10 );
            }

            // Custom sort function to sort by count
            uasort( $stats['terms'], function( $a, $b ) {
                $count_a = is_array($a) ? $a['count'] : $a;
                $count_b = is_array($b) ? $b['count'] : $b;
                return $count_b <=> $count_a;
            } );

            // Keep top 100 popular terms to protect option storage
            if ( count( $stats['terms'] ) > 100 ) {
                $stats['terms'] = array_slice( $stats['terms'], 0, 100, true );
            }

            update_option( 'fas_search_stats', $stats );
        } );
    }
}
