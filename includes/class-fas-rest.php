<?php
/**
 * Multilingual REST API Endpoint Logic
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FAS_Rest {

    public function register_routes() {
        register_rest_route( 'fas/v1', '/search', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_search_results' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'fas/v1', '/track-click', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'track_search_click' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'fas/v1', '/preview', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'render_live_preview' ),
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
        ) );
    }

    public function render_live_preview( WP_REST_Request $request ) {
        $settings = $request->get_params();
        if ( empty( $settings ) ) {
            $settings = $request->get_json_params();
        }
        if ( empty( $settings ) ) {
            $settings = $request->get_body_params();
        }

        $active_lang = isset( $settings['active_lang'] ) ? sanitize_text_field( $settings['active_lang'] ) : 'en';
        $suffix = '_' . $active_lang;

        // Force the get_lang_suffix to return our targeted language for the template
        add_filter( 'pre_option_fas_forced_lang_suffix', function() use ( $suffix ) { return $suffix; } );
        add_filter( 'fas_lang_suffix', function() use ( $suffix ) { return $suffix; } );

        // Dynamically hook pre_option filters for ALL settings passed from form
        foreach ( $settings as $key => $val ) {
            $sanitized_val = is_array( $val ) ? array_map( 'sanitize_text_field', $val ) : sanitize_text_field( $val );
            
            add_filter( 'pre_option_' . $key, function() use ( $sanitized_val ) {
                return $sanitized_val;
            } );

            if ( substr( $key, -strlen( $suffix ) ) === $suffix ) {
                $base_key = substr( $key, 0, -strlen( $suffix ) );
                add_filter( 'pre_option_' . $base_key, function() use ( $sanitized_val ) {
                    return $sanitized_val;
                } );
            }
        }

        // Retrieve dynamic option values
        $floating_bg          = FAS_Core::get_option( 'fas_floating_bg', '#0066cc' );
        $popup_width          = FAS_Core::get_option( 'fas_popup_width', 750 );
        $popup_max_height     = FAS_Core::get_option( 'fas_popup_max_height', 600 );
        $title_size_desktop   = FAS_Core::get_option( 'fas_title_size_desktop', 15 );
        $title_size_mobile    = FAS_Core::get_option( 'fas_title_size_mobile', 14 );
        $excerpt_size_desktop = FAS_Core::get_option( 'fas_excerpt_size_desktop', 13 );
        $excerpt_size_mobile  = FAS_Core::get_option( 'fas_excerpt_size_mobile', 12 );
        $history_bg           = FAS_Core::get_option( 'fas_history_bg', 'rgba(255, 255, 255, 0.1)' );
        $history_hover_bg     = FAS_Core::get_option( 'fas_history_hover_bg', 'rgba(255, 255, 255, 0.2)' );
        $history_text_size    = FAS_Core::get_option( 'fas_history_text_size', 13 );
        $history_count        = FAS_Core::get_option( 'fas_history_count', 5 );

        ob_start();
        ?>
        <!DOCTYPE html>
        <html dir="<?php echo ( strpos( $suffix, 'fa' ) !== false ) ? 'rtl' : 'ltr'; ?>">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" id="dashicons-css" href="<?php echo esc_url( includes_url( 'css/dashicons.min.css' ) ); ?>" media="all" />
            <link rel="stylesheet" id="fas-public-css" href="<?php echo esc_url( plugins_url( 'public/css/fas-public.css', dirname( __FILE__ ) ) ); ?>" media="all" />
            <style>
                body { margin: 0; background: transparent; overflow: hidden; }
                .fas-search-overlay {
                    position: absolute !important;
                    height: 100vh !important;
                }
                :root { 
                    --fas-primary: <?php echo esc_attr( $floating_bg ); ?>; 
                    --fas-popup-width: <?php echo esc_attr( $popup_width ); ?>px;
                    --fas-popup-max-height: <?php echo esc_attr( $popup_max_height ); ?>px;
                    --fas-title-size-desktop: <?php echo esc_attr( $title_size_desktop ); ?>px;
                    --fas-title-size-mobile: <?php echo esc_attr( $title_size_mobile ); ?>px;
                    --fas-excerpt-size-desktop: <?php echo esc_attr( $excerpt_size_desktop ); ?>px;
                    --fas-excerpt-size-mobile: <?php echo esc_attr( $excerpt_size_mobile ); ?>px;
                    --fas-history-bg: <?php echo esc_attr( $history_bg ); ?>;
                    --fas-history-hover-bg: <?php echo esc_attr( $history_hover_bg ); ?>;
                    --fas-history-text-size: <?php echo esc_attr( $history_text_size ); ?>px;
                }
            </style>
        </head>
        <body>
            <?php
            $original_template = locate_template( 'faramoj-advanced-search/search-modal.php' );
            if ( ! $original_template ) {
                $original_template = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/search-modal.php';
            }
            include $original_template;
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const overlay = document.querySelector('.fas-search-overlay');
                    if(overlay) {
                        overlay.classList.add('is-open');
                        
                        const tabs = document.querySelectorAll('.fas-tab-btn');
                        if (tabs.length > 0) {
                            const firstTab = tabs[0];
                            firstTab.classList.add('is-active');
                            const targetTab = firstTab.getAttribute('data-tab');
                            
                            const content = document.getElementById('fas-tab-' + targetTab);
                            if (content) {
                                content.classList.add('is-active');
                                const isRtl = document.documentElement.dir === 'rtl';
                                const dirStr = isRtl ? 'row-reverse' : 'row';
                                const alignStr = isRtl ? 'right' : 'left';
                                
                                let html = '<div class="fas-result-item" style="display: flex; align-items: center; gap: 16px; padding: 12px; border-radius: 12px; margin-bottom: 8px; flex-direction: '+dirStr+';">';
                                html += '<div style="width: 48px; height: 48px; border-radius: 8px; background: rgba(100,116,139,0.1); display:flex; align-items:center; justify-content:center; color:#64748b; flex-shrink:0;">';
                                html += '<span class="dashicons dashicons-cart" style="font-size: 22px; width:22px; height:22px;"></span>';
                                html += '</div>';
                                html += '<div style="text-align: '+alignStr+'; flex-grow:1;">';
                                html += '<h4 class="fas-result-title" style="font-size: var(--fas-title-size-desktop); margin: 0 0 4px 0;">' + (isRtl ? 'آنتن فوق پیشرفته Phase-30ISO' : 'Phase-30ISO Antenna') + '</h4>';
                                html += '<p class="fas-result-excerpt" style="font-size: var(--fas-excerpt-size-desktop); margin:0;">' + (isRtl ? 'محصول مخابراتی دو بانده فوق پیشرفته...' : 'Premium dual-band technical product spec...') + '</p>';
                                html += '</div>';
                                html += '</div>';
                                content.innerHTML = html;
                            }
                        }

                        const histContainer = document.querySelector('.fas-search-history');
                        if (histContainer) {
                            const isRtl = document.documentElement.dir === 'rtl';
                            const titleText = isRtl ? 'تاریخچه جستجو' : 'Search History';
                            const clearText = isRtl ? 'پاک کردن' : 'Clear';
                            let html = `
                                <div class="fas-search-history-header">
                                    <span class="fas-search-history-title">${titleText}</span>
                                    <button type="button" class="fas-search-history-clear">${clearText}</button>
                                </div>
                                <div class="fas-search-history-items">
                            `;
                            const mockTerms = ['phase-30', 'antenna', 'radio', 'modem', 'wifi'];
                            const maxHist = <?php echo intval( $history_count ); ?>;
                            for(let i=0; i < Math.min(mockTerms.length, maxHist); i++) {
                                html += `<button type="button" class="fas-search-history-item" style="flex-direction: ${isRtl ? 'row-reverse' : 'row'};">
                                    <span class="fas-search-history-item-text">${mockTerms[i]}</span>
                                    <span class="fas-search-history-item-remove">&times;</span>
                                </button>`;
                            }
                            html += `</div>`;
                            histContainer.innerHTML = html;
                            histContainer.style.display = 'block';
                            
                            histContainer.style.direction = document.documentElement.dir;
                            const header = histContainer.querySelector('.fas-search-history-header');
                            if(header) { header.style.flexDirection = isRtl ? 'row-reverse' : 'row'; }
                        }
                    }
                    
                    const tabBtns = document.querySelectorAll('.fas-tab-btn');
                    tabBtns.forEach(btn => {
                        btn.addEventListener('click', () => {
                            const target = btn.getAttribute('data-tab');
                            const accentColor = btn.getAttribute('data-accent-color');
                            
                            tabBtns.forEach(b => { b.classList.remove('is-active'); b.style.color = ''; b.style.borderBottomColor = ''; b.style.background = ''; b.style.borderColor = ''; const icon = b.querySelector('.dashicons'); if (icon) icon.style.color = ''; });
                            document.querySelectorAll('.fas-tab-content').forEach(c => c.classList.remove('is-active'));
                            
                            btn.classList.add('is-active');
                            btn.style.background = accentColor;
                            btn.style.borderColor = accentColor;
                            
                            const targetContent = document.getElementById('fas-tab-' + target);
                            if (targetContent) targetContent.classList.add('is-active');
                        });
                    });
                });
            </script>
        </body>
        </html>
        <?php
        $html = ob_get_clean();

        return new WP_REST_Response( array( 'success' => true, 'html' => $html ), 200 );
    }

    public function track_search_click( WP_REST_Request $request ) {
        $post_id = intval( $request->get_param( 'post_id' ) );
        $title   = sanitize_text_field( $request->get_param( 'title' ) );
        $term    = sanitize_text_field( $request->get_param( 'term' ) );
        $month   = current_time( 'Y-m' );

        if ( empty( $post_id ) ) {
            return new WP_REST_Response( array( 'error' => 'Empty post ID' ), 400 );
        }

        $stats = get_option( 'fas_search_stats', array( 'total_count' => 0, 'terms' => [], 'clicks' => [], 'monthly' => [] ) );
        
        if ( ! isset( $stats['clicks'] ) ) {
            $stats['clicks'] = array();
        }

        // Use post_id as key, but store title for display purposes
        if ( ! isset( $stats['clicks'][ $post_id ] ) ) {
            $stats['clicks'][ $post_id ] = array(
                'count' => 0,
                'title' => $title
            );
        }
        
        $stats['clicks'][ $post_id ]['count']++;
        
        // Keep top 100 clicked items to protect option storage
        uasort( $stats['clicks'], function( $a, $b ) {
            return $b['count'] <=> $a['count'];
        } );

        if ( count( $stats['clicks'] ) > 100 ) {
            $stats['clicks'] = array_slice( $stats['clicks'], 0, 100, true );
        }

        // Track clicks per term
        if ( ! empty( $term ) ) {
            $term_clean = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( $term ) ) : strtolower( trim( $term ) );
            if ( isset( $stats['terms'][ $term_clean ] ) && is_array( $stats['terms'][ $term_clean ] ) ) {
                if ( ! isset( $stats['terms'][ $term_clean ]['click_count'] ) ) {
                    $stats['terms'][ $term_clean ]['click_count'] = 0;
                }
                $stats['terms'][ $term_clean ]['click_count']++;
            }

            // Monthly stats tracking
            if ( ! isset( $stats['monthly'] ) ) {
                $stats['monthly'] = array();
            }
            if ( ! isset( $stats['monthly'][ $month ] ) ) {
                $stats['monthly'][ $month ] = array( 'terms' => array() );
            }
            if ( ! isset( $stats['monthly'][ $month ]['terms'][ $term_clean ] ) ) {
                $stats['monthly'][ $month ]['terms'][ $term_clean ] = array( 'count' => 0, 'click_count' => 0 );
            }
            if ( ! isset( $stats['monthly'][ $month ]['terms'][ $term_clean ]['click_count'] ) ) {
                $stats['monthly'][ $month ]['terms'][ $term_clean ]['click_count'] = 0;
            }
            $stats['monthly'][ $month ]['terms'][ $term_clean ]['click_count']++;
        }

        update_option( 'fas_search_stats', $stats );

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    public function get_search_results( WP_REST_Request $request ) {
        $raw_term = sanitize_text_field( $request->get_param( 's' ) );
        $lang = sanitize_text_field( $request->get_param( 'lang' ) );

        if ( empty( $raw_term ) ) {
            return new WP_REST_Response( array( 'error' => 'Empty search term' ), 400 );
        }

        // Normalize term for less strict caching and searching
        $term = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( $raw_term ) ) : strtolower( trim( $raw_term ) );
        $term = preg_replace( '/\s+/', ' ', $term );

        // Log search query metrics dynamically for our Statistics submenu using raw term for accurate logging
        $this->log_search_stats( $raw_term );

        // Switch language context if WPML is active
        if ( function_exists( 'wpml_object_id_filter' ) && ! empty( $lang ) ) {
            do_action( 'wpml_switch_language', $lang );
        }

        // Switch language context if Polylang is active
        if ( function_exists( 'PLL' ) && ! empty( $lang ) ) {
            PLL()->curlang = PLL()->model->get_language( $lang );
        }

        // Custom Transient Cache Duration from Settings (default 1 hour) via unified get_option
        $cache_duration = FAS_Core::get_option( 'fas_cache_duration', HOUR_IN_SECONDS );
        if ( empty( $cache_duration ) && $cache_duration !== '0' ) {
            $cache_duration = HOUR_IN_SECONDS;
        }

        // Optimized Cache Key based on term and language, using cache versioning
        $cache_version = get_option( 'fas_cache_version', 1 );
        $cache_key = 'fas_search_' . md5( $term . '_' . $lang . '_' . $cache_version );
        
        $results = false;
        if ( $cache_duration > 0 ) {
            $results = get_transient( $cache_key );
        }

        if ( false === $results ) {
            $results = $this->execute_db_query( $term, $lang );
            if ( $cache_duration > 0 ) {
                set_transient( $cache_key, $results, intval( $cache_duration ) );
            }
        }

        // Check for Zero-Result Search
        if ( empty( $results['all'] ) ) {
            $this->log_zero_result_stats( $term );
        }

        return new WP_REST_Response( $results, 200 );
    }

    /**
     * Log zero-result search queries
     */
    private function log_zero_result_stats( $term ) {
        if ( empty( $term ) || strlen( $term ) < 3 ) {
            return;
        }

        $term_clean = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( $term ) ) : strtolower( trim( $term ) );

        add_action( 'shutdown', function() use ( $term_clean ) {
            $stats = get_option( 'fas_search_stats', array( 'total_count' => 0, 'terms' => [], 'clicks' => [], 'monthly' => [], 'zero_terms' => [] ) );

            if ( ! isset( $stats['zero_terms'] ) ) {
                $stats['zero_terms'] = array();
            }

            if ( ! isset( $stats['zero_terms'][ $term_clean ] ) ) {
                $stats['zero_terms'][ $term_clean ] = 0;
            }
            $stats['zero_terms'][ $term_clean ]++;

            arsort( $stats['zero_terms'] );

            if ( count( $stats['zero_terms'] ) > 100 ) {
                $stats['zero_terms'] = array_slice( $stats['zero_terms'], 0, 100, true );
            }

            update_option( 'fas_search_stats', $stats );
        } );
    }

    /**
     * Log search queries statistics (total count & popular terms tracking)
     */
    private function log_search_stats( $term ) {
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
        $month = current_time( 'Y-m' );
        
        // Defer database writing to the shutdown hook so it does not block the REST API response
        add_action( 'shutdown', function() use ( $term_clean, $ip, $timestamp, $month ) {
            $stats = get_option( 'fas_search_stats', array( 'total_count' => 0, 'terms' => [], 'clicks' => [], 'monthly' => [] ) );

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
            
            if ( ! isset( $stats['terms'][ $term_clean ]['click_count'] ) ) {
                $stats['terms'][ $term_clean ]['click_count'] = 0;
            }

            // Monthly Tracking
            if ( ! isset( $stats['monthly'] ) ) {
                $stats['monthly'] = array();
            }
            if ( ! isset( $stats['monthly'][ $month ] ) ) {
                $stats['monthly'][ $month ] = array( 'terms' => array() );
            }
            if ( ! isset( $stats['monthly'][ $month ]['terms'][ $term_clean ] ) ) {
                $stats['monthly'][ $month ]['terms'][ $term_clean ] = array( 'count' => 0, 'click_count' => 0 );
            }
            $stats['monthly'][ $month ]['terms'][ $term_clean ]['count']++;

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

    /**
     * Convert Persian and Arabic numbers to English digits.
     */
    private function convert_persian_to_english_digits( $string ) {
        $persian = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
        $arabic  = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );
        $english = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
        
        $string = str_replace( $persian, $english, $string );
        return str_replace( $arabic, $english, $string );
    }

    /**
     * Convert English numbers to Persian digits.
     */
    private function convert_english_to_persian_digits( $string ) {
        $persian = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
        $english = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
        
        return str_replace( $english, $persian, $string );
    }

    /**
     * Helper to highlight the query in a text safely.
     */
    private function highlight_text( $text, $term ) {
        $escaped = esc_html( $text );
        if ( empty( $term ) ) {
            return $escaped;
        }

        // Generate highlight patterns for raw, English, and Persian digit normalized variations
        $terms = array_unique( array_filter( array(
            $term,
            $this->convert_persian_to_english_digits( $term ),
            $this->convert_english_to_persian_digits( $term )
        ) ) );

        foreach ( $terms as $t ) {
            $quoted_term = preg_quote( $t, '/' );
            $escaped = preg_replace(
                '/(' . $quoted_term . ')/i',
                '<mark class="fas-highlight">$1</mark>',
                $escaped
            );
        }

        return $escaped;
    }

    /**
     * Fetch all unique post IDs matching search terms across title, content, and specified metadata.
     * Re-written for performance using $wpdb and smart cross-field matching.
     */
    private function get_matched_post_ids( $post_type, $search_terms, $meta_keys = [] ) {
        global $wpdb;
        $all_ids = array();

        $post_type_sql = $wpdb->prepare( "post_type = %s", $post_type );

        // Generate query for each normalized term format (Original, English digits, Persian digits)
        foreach ( $search_terms as $term ) {
            $words = array_filter( explode( ' ', $term ) );
            if ( empty( $words ) ) {
                continue;
            }

            $word_queries = array();
            foreach ( $words as $word ) {
                $like = '%' . $wpdb->esc_like( $word ) . '%';

                $title_content_sql = $wpdb->prepare( "(p.post_title LIKE %s OR p.post_content LIKE %s)", $like, $like );

                if ( ! empty( $meta_keys ) ) {
                    $meta_keys_in = "'" . implode( "','", array_map( 'esc_sql', $meta_keys ) ) . "'";
                    $meta_sql = $wpdb->prepare( "
                        OR EXISTS (
                            SELECT 1 FROM {$wpdb->postmeta} pm
                            WHERE pm.post_id = p.ID
                            AND pm.meta_key IN ($meta_keys_in)
                            AND pm.meta_value LIKE %s
                        )", $like );

                    $word_queries[] = "($title_content_sql $meta_sql)";
                } else {
                    $word_queries[] = $title_content_sql;
                }
            }

            // All words must match *somewhere* (title, content, or meta)
            $term_query = implode( " AND ", $word_queries );

            $sql = "
                SELECT p.ID
                FROM {$wpdb->posts} p
                WHERE p.post_status = 'publish'
                AND $post_type_sql
                AND ($term_query)
                LIMIT 50
            ";

            $results = $wpdb->get_col( $sql );
            if ( ! empty( $results ) ) {
                $all_ids = array_merge( $all_ids, $results );
            }
        }

        return array_unique( $all_ids );
    }

    private function execute_db_query( $term, $lang ) {
        // Normalize common punctuation to space for less strict English matching (e.g., "Wi-Fi" -> "Wi Fi")
        $term_no_punct = str_replace( array( '-', '_', '.', ',' ), ' ', $term );
        $term_no_punct = preg_replace( '/\s+/', ' ', trim( $term_no_punct ) );

        // Smart regex: add a space between numbers and letters if they are adjacent (e.g., "آنتن25" -> "آنتن 25")
        $term_spaced = preg_replace( '/([a-zA-Z\x{0600}-\x{06FF}])(\d+)/u', '$1 $2', $term_no_punct );
        $term_spaced = preg_replace( '/(\d+)([a-zA-Z\x{0600}-\x{06FF}])/u', '$1 $2', $term_spaced );

        $normalized_terms = array_unique( array_filter( array(
            $term,
            $term_no_punct,
            $term_spaced,
            $this->convert_persian_to_english_digits( $term ),
            $this->convert_english_to_persian_digits( $term ),
            $this->convert_persian_to_english_digits( $term_spaced ),
            $this->convert_english_to_persian_digits( $term_spaced )
        ) ) );

        // Split queries exactly by post type to isolate products (WooCommerce) vs posts vs pages
        $product_ids = $this->get_matched_post_ids( 'product', $normalized_terms, array( 'technical_specifications', 'frequency_range' ) );
        $post_ids    = $this->get_matched_post_ids( 'post', $normalized_terms );
        $page_ids    = $this->get_matched_post_ids( 'page', $normalized_terms );

        $formatted_results = array(
            'all'      => [], // Combined list of all matches
            'products' => [],
            'posts'    => [],
            'docs'     => []
        );

        $limit = FAS_Core::get_option( 'fas_results_count', 15 );
        if ( empty( $limit ) || $limit < 1 ) {
            $limit = 15;
        }

        // Limit each specific tab results
        $final_product_ids = array_slice( $product_ids, 0, $limit );
        $final_post_ids    = array_slice( $post_ids, 0, $limit );
        $final_page_ids    = array_slice( $page_ids, 0, $limit );

        // Render function helper
        $render_items = function( $ids, $default_category ) use ( $term ) {
            $items = array();
            if ( empty( $ids ) ) {
                return $items;
            }

            $query = new WP_Query( array(
                'post_type' => array( 'product', 'post', 'page' ),
                'post__in'  => $ids,
                'orderby'   => 'post__in',
            ) );

            if ( $query->have_posts() ) {
                $template_path = locate_template( 'faramoj-advanced-search/search-result-item.php' );
                if ( ! $template_path ) {
                    $template_path = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/search-result-item.php';
                }

                while ( $query->have_posts() ) {
                    $query->the_post();
                    $id = get_the_ID();
                    $type = get_post_type();
                    
                    $title   = get_the_title();
                    $excerpt = wp_trim_words( get_the_excerpt(), 15, '...' );

                    $item = array(
                        'id'           => $id,
                        'title'        => $title,
                        'permalink'    => get_permalink(),
                        'image'        => get_the_post_thumbnail_url( $id, 'thumbnail' ) ?: '',
                        'excerpt'      => $excerpt,
                        'title_html'   => $this->highlight_text( $title, $term ),
                        'excerpt_html' => $this->highlight_text( $excerpt, $term ),
                    );

                    $category = $default_category;
                    if ( 'all' === $default_category ) {
                        if ( $type === 'product' ) {
                            $category = 'products';
                        } elseif ( $type === 'post' ) {
                            $category = 'posts';
                        } else {
                            $category = 'docs';
                        }
                    }

                    ob_start();
                    $args = array( 'item' => $item, 'category' => $category );
                    include $template_path;
                    $rendered_html = ob_get_clean();

                    $items[] = array( 'html' => $rendered_html );
                }
                wp_reset_postdata();
            }

            return $items;
        };

        $formatted_results['products'] = $render_items( $final_product_ids, 'products' );
        $formatted_results['posts']    = $render_items( $final_post_ids, 'posts' );
        $formatted_results['docs']     = $render_items( $final_page_ids, 'docs' );

        // Merge pre-rendered results directly in PHP for the combined 'all' tab.
        // This is 100% reliable, uses zero extra SQL queries, and avoids WPML/Polylang multi-CPT filter conflicts.
        $all_items = array_merge(
            $formatted_results['products'],
            $formatted_results['posts'],
            $formatted_results['docs']
        );
        $formatted_results['all'] = array_slice( $all_items, 0, 20 );

        // "Did you mean" logic if no results
        if ( empty( $formatted_results['all'] ) ) {
            $formatted_results['did_you_mean'] = $this->get_did_you_mean_suggestion( $term );
        }

        return $formatted_results;
    }

    /**
     * Calculate Levenshtein distance for multi-byte strings (e.g. Persian)
     */
    private function mb_levenshtein( $str1, $str2 ) {
        if ( ! function_exists( 'mb_strlen' ) || ! function_exists( 'mb_substr' ) ) {
            return levenshtein( $str1, $str2 );
        }

        $len1 = mb_strlen( $str1, 'UTF-8' );
        $len2 = mb_strlen( $str2, 'UTF-8' );

        if ( $len1 == 0 ) return $len2;
        if ( $len2 == 0 ) return $len1;

        $v0 = range( 0, $len2 );
        $v1 = array();

        for ( $i = 0; $i < $len1; $i++ ) {
            $v1[0] = $i + 1;
            $c1 = mb_substr( $str1, $i, 1, 'UTF-8' );

            for ( $j = 0; $j < $len2; $j++ ) {
                $c2 = mb_substr( $str2, $j, 1, 'UTF-8' );
                $cost = ( $c1 == $c2 ) ? 0 : 1;
                $v1[$j + 1] = min( $v1[$j] + 1, $v0[$j + 1] + 1, $v0[$j] + $cost );
            }

            for ( $j = 0; $j <= $len2; $j++ ) {
                $v0[$j] = $v1[$j];
            }
        }

        return $v1[$len2];
    }

    /**
     * Find the closest matching successfully searched term from stats and database.
     */
    private function get_did_you_mean_suggestion( $query ) {
        global $wpdb;

        $query_clean = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( $query ), 'UTF-8' ) : strtolower( trim( $query ) );
        $best_match = '';
        $shortest_dist = -1;

        // 1. Gather dictionary terms
        $dictionary = array();

        // Add history terms
        $stats = get_option( 'fas_search_stats', array() );
        if ( ! empty( $stats['terms'] ) && is_array( $stats['terms'] ) ) {
            foreach ( $stats['terms'] as $term => $data ) {
                $dictionary[$term] = true;
            }
        }

        // Add DB terms (posts, products, terms) - cache this to avoid heavy queries on every missed search
        $db_terms = get_transient( 'fas_did_you_mean_dict' );
        if ( false === $db_terms ) {
            $db_terms = array();
            
            // Get recent published posts/products titles
            $titles = $wpdb->get_col( "SELECT post_title FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ('post', 'page', 'product') ORDER BY post_date DESC LIMIT 500" );
            if ( $titles ) {
                foreach ( $titles as $title ) {
                    // Split title into words to build a richer dictionary
                    $words = explode( ' ', $title );
                    foreach ( $words as $word ) {
                        $word = trim( $word );
                        if ( function_exists('mb_strlen') ? mb_strlen($word, 'UTF-8') > 2 : strlen($word) > 2 ) {
                             $db_terms[] = function_exists( 'mb_strtolower' ) ? mb_strtolower( $word, 'UTF-8' ) : strtolower( $word );
                        }
                    }
                    $db_terms[] = function_exists( 'mb_strtolower' ) ? mb_strtolower( $title, 'UTF-8' ) : strtolower( $title );
                }
            }

            // Get popular terms (categories, tags)
            $tax_terms = $wpdb->get_col( "SELECT t.name FROM {$wpdb->terms} t INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE tt.count > 0 LIMIT 100" );
            if ( $tax_terms ) {
                foreach ( $tax_terms as $term ) {
                    $db_terms[] = function_exists( 'mb_strtolower' ) ? mb_strtolower( $term, 'UTF-8' ) : strtolower( $term );
                }
            }
            
            $db_terms = array_unique( $db_terms );
            set_transient( 'fas_did_you_mean_dict', $db_terms, 12 * HOUR_IN_SECONDS );
        }

        if ( is_array( $db_terms ) ) {
            foreach ( $db_terms as $term ) {
                $dictionary[$term] = true;
            }
        }

        if ( empty( $dictionary ) ) {
            return '';
        }

        // 2. Find closest match
        foreach ( $dictionary as $term => $dummy ) {
            // mb_levenshtein can be slow on very long strings, restrict length
            $q_len = function_exists('mb_strlen') ? mb_strlen($query_clean, 'UTF-8') : strlen($query_clean);
            $t_len = function_exists('mb_strlen') ? mb_strlen($term, 'UTF-8') : strlen($term);
            
            if ( $q_len > 100 || $t_len > 100 ) {
                continue;
            }

            // Calculate similarity (Levenshtein distance)
            $dist = $this->mb_levenshtein( $query_clean, $term );
            
            // Allow for typos (distance up to 3 for longer words, 1 for short words)
            $max_dist = $q_len <= 4 ? 1 : 3;
            
            // Ensure distance is valid and within threshold
            if ( $dist >= 0 && $dist <= $max_dist ) {
                if ( $shortest_dist === -1 || $dist < $shortest_dist ) {
                    // Make sure it's not the exact same query
                    if ( $query_clean !== $term ) {
                        $best_match = $term;
                        $shortest_dist = $dist;
                    }
                }
            }
        }
        
        return $best_match;
    }
}
