<?php
/**
 * Phase 4 page inventory, quality, linking and SEO tests.
 */

require __DIR__ . '/bootstrap.php';

$tests = 0;
$failed = 0;

function jsc_phase4_test( $description, $condition, $detail = '' ) {
    global $tests, $failed;
    ++$tests;
    if ( $condition ) {
        echo "PASS: {$description}\n";
        return;
    }
    ++$failed;
    echo "FAIL: {$description}" . ( $detail ? " ({$detail})" : '' ) . "\n";
}

$definitions = JSC_Content_Installer::get_page_definitions();
jsc_phase4_test( 'content inventory contains 30 intentional pages', 30 === count( $definitions ), (string) count( $definitions ) );

$type_counts = array_count_values( array_column( $definitions, 'content_type' ) );
jsc_phase4_test( 'inventory contains five checker/tool pages', 5 === ( $type_counts['tool'] ?? 0 ) );
jsc_phase4_test( 'inventory contains two content hubs', 2 === ( $type_counts['hub'] ?? 0 ) );
jsc_phase4_test( 'inventory contains ten scam articles', 10 === ( $type_counts['scam_article'] ?? 0 ) );
jsc_phase4_test( 'inventory contains seven guides', 7 === ( $type_counts['guide'] ?? 0 ) );
jsc_phase4_test( 'inventory contains five trust/legal pages', 5 === ( $type_counts['trust'] ?? 0 ) + ( $type_counts['legal'] ?? 0 ) );

$titles = array_column( $definitions, 'seo_title' );
$descriptions = array_column( $definitions, 'description' );
jsc_phase4_test( 'all SEO titles are unique', count( $titles ) === count( array_unique( $titles ) ) );
jsc_phase4_test( 'all meta descriptions are unique', count( $descriptions ) === count( array_unique( $descriptions ) ) );

$paths = array();
foreach ( $definitions as $key => $definition ) {
    $path = '/' . ( ! empty( $definition['parent'] ) ? $definitions[ $definition['parent'] ]['slug'] . '/' : '' ) . $definition['slug'] . '/';
    $paths[ $key ] = $path;
}
$known_paths = array_flip( array_values( $paths ) );
$known_paths['/'] = true;
jsc_phase4_test( 'every page path is unique', count( $paths ) === count( array_unique( $paths ) ) );

$missing_related = array();
foreach ( $definitions as $key => $definition ) {
    foreach ( $definition['related'] ?? array() as $related_key ) {
        if ( ! isset( $definitions[ $related_key ] ) ) {
            $missing_related[] = $key . ':' . $related_key;
        }
    }
}
jsc_phase4_test( 'every curated related-content key resolves', empty( $missing_related ), implode( ', ', $missing_related ) );
jsc_phase4_test( 'foundation pages declare safe exact-content upgrade paths', ! empty( $definitions['home']['upgrade_from'] ) && ! empty( $definitions['job_scam_checker']['upgrade_from'] ) );

$broken = array();
$thin = array();
$bad_headings = array();
foreach ( $definitions as $key => $definition ) {
    preg_match_all( '/href="(\/[^"]*)"/', $definition['content'], $matches );
    foreach ( $matches[1] as $href ) {
        $path = (string) parse_url( $href, PHP_URL_PATH );
        if ( ! isset( $known_paths[ $path ] ) ) {
            $broken[] = $key . ':' . $path;
        }
    }
    if ( in_array( $definition['content_type'], array( 'scam_article', 'guide' ), true ) ) {
        $words = str_word_count( wp_strip_all_tags( strip_shortcodes_for_test( $definition['content'] ) ) );
        if ( $words < 350 ) {
            $thin[] = $key . ':' . $words;
        }
        if ( false === strpos( $definition['content'], '<h2>' ) || false === strpos( $definition['content'], '<h3>' ) || false !== stripos( $definition['content'], '<h1' ) ) {
            $bad_headings[] = $key;
        }
    }
}
jsc_phase4_test( 'all hard-coded internal links resolve to installed paths', empty( $broken ), implode( ', ', $broken ) );
jsc_phase4_test( 'all scam and guide articles exceed thin-content floor', empty( $thin ), implode( ', ', $thin ) );
jsc_phase4_test( 'article bodies maintain h2/h3 hierarchy without duplicate h1', empty( $bad_headings ), implode( ', ', $bad_headings ) );

$scam_pages = array_filter( $definitions, static function ( $item ) { return 'scam_article' === $item['content_type']; } );
foreach ( $scam_pages as $key => $definition ) {
    jsc_phase4_test( $key . ' includes fictional example disclosure', false !== strpos( $definition['content'], 'Fictional example:' ) );
    jsc_phase4_test( $key . ' includes verification and contacted guidance', false !== strpos( $definition['content'], 'How to verify the opportunity' ) && false !== strpos( $definition['content'], 'already been contacted' ) );
}

$high_similarity = array();
$article_keys = array_keys( $scam_pages );
for ( $i = 0; $i < count( $article_keys ); ++$i ) {
    for ( $j = $i + 1; $j < count( $article_keys ); ++$j ) {
        similar_text( wp_strip_all_tags( $scam_pages[ $article_keys[ $i ] ]['content'] ), wp_strip_all_tags( $scam_pages[ $article_keys[ $j ] ]['content'] ), $percent );
        if ( $percent > 55 ) {
            $high_similarity[] = $article_keys[ $i ] . '/' . $article_keys[ $j ] . ':' . round( $percent );
        }
    }
}
jsc_phase4_test( 'scam articles are not near-duplicate doorway pages', empty( $high_similarity ), implode( ', ', $high_similarity ) );

$public = new JSC_Public();
$ad_slot = $public->render_ad_slot( array( 'position' => 'mid_article' ) );
jsc_phase4_test( 'article ad component is empty and hidden', false !== strpos( $ad_slot, 'hidden' ) && false === strpos( $ad_slot, '<script' ) && false === strpos( $ad_slot, 'ad data' ) );

$metadata_source = file_get_contents( dirname( __DIR__, 2 ) . '/wordpress/themes/job-scam-checker-theme/inc/metadata.php' );
$schema_source = file_get_contents( dirname( __DIR__, 2 ) . '/wordpress/themes/job-scam-checker-theme/inc/structured-data.php' );
$breadcrumb_source = file_get_contents( dirname( __DIR__, 2 ) . '/wordpress/themes/job-scam-checker-theme/inc/content-navigation.php' );
jsc_phase4_test( 'metadata uses curated title and description fields', false !== strpos( $metadata_source, '_jsc_seo_title' ) && false !== strpos( $metadata_source, '_jsc_seo_description' ) );
jsc_phase4_test( 'robots handling noindexes search and 404 pages', false !== strpos( $metadata_source, 'is_search() || is_404()' ) );
jsc_phase4_test( 'robots advertises native WordPress sitemap', false !== strpos( $metadata_source, '/wp-sitemap.xml' ) );
jsc_phase4_test( 'schema contains only Article and BreadcrumbList editorial types', false !== strpos( $schema_source, "'Article'" ) && false !== strpos( $schema_source, "'BreadcrumbList'" ) && false === strpos( $schema_source, 'AggregateRating' ) && false === strpos( $schema_source, 'Review' ) );
jsc_phase4_test( 'visible breadcrumbs identify the current page', false !== strpos( $breadcrumb_source, 'aria-current="page"' ) );
jsc_phase4_test( 'related content is curated from page metadata', false !== strpos( $breadcrumb_source, '_jsc_related_pages' ) );

$privacy = $definitions['privacy']['content'];
jsc_phase4_test( 'privacy policy accurately states local processing and no permanent message storage', false !== strpos( $privacy, 'own WordPress endpoint' ) && false !== strpos( $privacy, 'does not permanently store the message' ) );
jsc_phase4_test( 'contact page does not invent contact details', false !== strpos( $definitions['contact']['content'], 'no email address, postal address or organization details are invented' ) );
jsc_phase4_test( 'content contains no rating or review schema claims', false === stripos( json_encode( $definitions ), 'aggregateRating' ) && false === stripos( json_encode( $definitions ), 'reviewRating' ) );
jsc_phase4_test( 'scam hub visibly links all ten scam articles', 10 === substr_count( $definitions['scam_categories']['content'], '<article><h2><a href="/job-scams/' ) );
jsc_phase4_test( 'guide hub visibly links all seven guides', 7 === substr_count( $definitions['guides']['content'], '<article><h2><a href="/guides/' ) );

echo "\n{$tests} tests, {$failed} failures.\n";
exit( $failed > 0 ? 1 : 0 );

function strip_shortcodes_for_test( $content ) {
    return preg_replace( '/\[[^\]]+\]/', ' ', $content );
}
