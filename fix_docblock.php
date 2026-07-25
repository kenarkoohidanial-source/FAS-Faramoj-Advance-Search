<?php

$file = 'includes/class-fas-rest.php';
$content = file_get_contents($file);

$bad_docblock = '    /**
     * Log search queries statistics (total count & popular terms tracking)
     */
    /**
     * Log search query statistics.
     *
     * @param string $term The search term to log.
     * @return void
     */';
$good_docblock = '    /**
     * Log search queries statistics (total count & popular terms tracking)
     *
     * @param string $term The search term to log.
     * @return void
     */';

$content = str_replace($bad_docblock, $good_docblock, $content);
file_put_contents($file, $content);

echo "Fixed docblock\n";
