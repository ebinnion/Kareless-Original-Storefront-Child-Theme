<?php

add_filter( 'body_class', 'kareless_full_width' );
function kareless_full_width( $classes ) {
	if ( is_product() ) {
		$classes[] = 'page-template-template-fullwidth-php';
	}

	return $classes;
}

add_action( 'after_setup_theme', 'kareless_after_theme_setup', 11 );
function kareless_after_theme_setup() {
	remove_theme_support( 'custom-logo' );
	remove_theme_support( 'site-logo' );
}

add_action( 'init', 'kareless_init', 11 );
function kareless_init() {
	// Remove Storefront actions.
	remove_action( 'storefront_single_post', 'storefront_post_meta', 20 );
	remove_action( 'storefront_loop_post', 'storefront_post_meta', 20 );
	remove_action( 'storefront_footer', 'storefront_credit', 20 );
	remove_action( 'storefront_before_content', 'storefront_header_widget_region', 10 );
	remove_action( 'storefront_header', 'storefront_site_branding',                20 );
	remove_action( 'storefront_header', 'storefront_secondary_navigation',         30 );
	remove_action( 'storefront_header', 'storefront_product_search',               40 );
	remove_action( 'storefront_header', 'storefront_header_cart',                  60 );

	// Yea, remove all storefront actions on the homepage so we can do our thing.
	remove_all_actions( 'homepage' );

	// Custom actions for theme output.
	add_action( 'homepage', 'kareless_homepage' );
	add_action( 'storefront_footer', 'kareless_credit', 20 );
	add_action( 'storefront_header', 'kareless_site_branding', 20 );

	// Various other "doing" actions.
	add_action( 'wp_enqueue_scripts', 'kareless_remove_sticky_footer', 99 );
}

function kareless_credit() {
?>
	<div class="site-info">
		<?php echo esc_html( apply_filters( 'storefront_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . date( 'Y' ) ) ); ?>
	</div>
<?php
}

add_action( 'get_header', 'remove_storefront_sidebar' );
function remove_storefront_sidebar() {
	if ( is_cart() || is_checkout() || is_product() ) {
		remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
	}
}

function kareless_homepage() { ?>
	<?php if ( function_exists( 'putRevSlider' ) ) : ?>
	<div id="kareless-instagram-footer">
		<?php putRevSlider( 'slider1' ); ?>
	</div>
	<?php endif; ?>

<?php }

function kareless_eight_recent( $args ) {
	$args['limit'] = 8;
	return $args;
}

function kareless_remove_sticky_footer() {
	wp_dequeue_script( 'storefront-sticky-payment' );
}

function kareless_site_branding() { ?>
	<div class="site-branding">
		<a href="<?php echo esc_url( get_home_url() ); ?>" class="custom-logo-link" rel="home" itemprop="url">
			<img
				src="<?php echo esc_url(
					sprintf(
						'%s/assets/images/kareless_wolf_logo.png',
						untrailingslashit( get_stylesheet_directory_uri() )
					)
				); ?>"
				alt="Kareless Logo"
				class="custom-logo"
			/>
		</a>
	</div>
<?php }

// ------------------
// 1. Register new endpoint to use for My Account page
// Note: Resave Permalinks or it will give 404 error

function kareless_add_aff_wp_endpoint() {
	add_rewrite_endpoint( 'aff', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'kareless_add_aff_wp_endpoint' );

// ------------------
// 2. Add new query var
function kareless_aff_wp_query_vars( $vars ) {
	$vars[] = 'aff';
	return $vars;
}
// add_filter( 'query_vars', 'kareless_aff_wp_query_vars', 0 );

// ------------------
// 3. Insert the new endpoint into the My Account menu
function kareless_add_aff_wp_link_my_account( $items ) {
	if ( function_exists( 'affwp_is_affiliate' ) && affwp_is_affiliate() ) {
		$logout = array_pop( $items );
		$items['aff'] = 'Affiliate Area';
		$items[] = $logout;
	}

	unset( $items['downloads'] );
	return $items;
}
add_filter( 'woocommerce_account_menu_items', 'kareless_add_aff_wp_link_my_account' );

// ------------------
// 4. Add content to the new endpoint

function kareless_aff_wp_content() {
	if ( ! class_exists( 'Affiliate_WP_Shortcodes' ) ) {
		return;
	}

	$shortcode = new Affiliate_WP_Shortcodes;
	echo $shortcode->affiliate_area();
}
add_action( 'woocommerce_account_aff_endpoint', 'kareless_aff_wp_content' );

function kareless_filter_aff_tabs( $url, $page_id, $tab ) {
	return esc_url_raw( add_query_arg( 'tab', $tab ) );
}
add_filter( 'affwp_affiliate_area_page_url', 'kareless_filter_aff_tabs', 10, 3 );

function kareless_header_cart() {
	if ( storefront_is_woocommerce_activated() ) {
		if ( is_cart() ) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
	?>
	<ul id="site-header-cart" class="site-header-cart menu">
		<li class="<?php echo esc_attr( $class ); ?>">
			<?php storefront_cart_link(); ?>
		</li>
	</ul>
	<?php
	}
}

function ds_checkout_analytics( $order_id ) {
	$order = new WC_Order( $order_id );
	$currency = $order->get_order_currency();
	$total = $order->get_total();
	$date = $order->order_date;
	?>
	<!-- Paste Tracking Code Under Here -->
<script>
  fbq('track', 'Purchase', {
    value: 0.2,
    currency: USD,
  });
</script>
	<!-- End Tracking Code -->
	<?php
}
add_action( 'woocommerce_thankyou', 'ds_checkout_analytics' );
