<?php $jsc_search_id = wp_unique_id( 'jsc-search-' ); ?>
<form role="search" method="get" class="site-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label for="<?php echo esc_attr( $jsc_search_id ); ?>"><?php esc_html_e( 'Search scam types and guides', 'job-scam-checker-theme' ); ?></label>
    <div><input type="search" id="<?php echo esc_attr( $jsc_search_id ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr__( 'e.g. fake check or Telegram', 'job-scam-checker-theme' ); ?>" required><button type="submit"><?php esc_html_e( 'Search', 'job-scam-checker-theme' ); ?></button></div>
</form>
