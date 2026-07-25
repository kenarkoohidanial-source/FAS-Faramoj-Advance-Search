<?php

$file = 'includes/class-fas-core.php';
$content = file_get_contents($file);

$old_block = '        // Auto-inject Floating Trigger button into footer if enabled
        add_action( \'wp_footer\', array( $this, \'maybe_inject_floating_trigger\' ), 9 );
    }';

$new_block = '        // Auto-inject Floating Trigger button into footer if enabled
        add_action( \'wp_footer\', array( $this, \'maybe_inject_floating_trigger\' ), 9 );

        // Automated cache invalidation hooks
        add_action( \'save_post\', array( $this, \'flush_search_cache_global\' ) );
        add_action( \'delete_post\', array( $this, \'flush_search_cache_global\' ) );
        add_action( \'edit_term\', array( $this, \'flush_search_cache_global\' ) );
        add_action( \'delete_term\', array( $this, \'flush_search_cache_global\' ) );
    }

    /**
     * Flush global search cache by incrementing the cache version option.
     * This instantly invalidates all transient keys dependent on the version hash.
     *
     * @return void
     */
    public function flush_search_cache_global() {
        update_option( \'fas_cache_version\', time() );
    }';

$content = str_replace($old_block, $new_block, $content);
file_put_contents($file, $content);
echo "Done\n";
