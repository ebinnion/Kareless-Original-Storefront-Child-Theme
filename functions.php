<?php

add_filter( 'body_class', 'kareless_full_width' );
function kareless_full_width( $classes ) {
	if ( is_product() ) {
		$classes[] = 'page-template-template-fullwidth-php';
	}

	return $classes;
}

add_action( 'init', 'kareless_init' );
function kareless_init() {
	remove_action( 'homepage', 'storefront_homepage_content', 10 );
	remove_action( 'homepage', 'storefront_product_categories', 20 );
	remove_action( 'homepage', 'storefront_featured_products', 40 );
	remove_action( 'homepage', 'storefront_popular_products', 50 );
	remove_action( 'homepage', 'storefront_on_sale_products', 60 );
	remove_action( 'storefront_single_post', 'storefront_post_meta', 20 );
	remove_action( 'storefront_loop_post', 'storefront_post_meta', 20 );
	remove_action( 'storefront_footer', 'storefront_credit', 20 );
	remove_action( 'storefront_header', 'storefront_header_cart' );

	// add_action( 'storefront_header', 'kareless_header_cart' );

	// add_action( 'homepage', 'storefront_homepage_content', 60 );

	add_action( 'storefront_before_content', 'kareless_instagram_header', 10 );
	add_action( 'storefront_footer', 'kareless_credit', 20 );

	add_filter( 'storefront_recent_products_args', 'kareless_eight_recent' );

	add_action( 'wp_enqueue_scripts', 'kareless_remove_sticky_footer', 99 );
}


function kareless_child_scripts() {
	// wp_enqueue_style( 'kareless-child', get_stylesheet_directory_uri() . '/style.css', array( 'storefront-style' ) );
	wp_dequeue_style( 'storefront-child-style-css' );
}
add_action( 'wp_enqueue_scripts', 'kareless_child_scripts', 101 );


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

function kareless_instagram_header() {
	if ( ! is_front_page() ) return;
?>
	<div id="kareless-instagram-footer">
		<?php putRevSlider( 'instagramfooter' ); ?>
	</div>
<?php
}

function kareless_eight_recent( $args ) {
	$args['limit'] = 8;
	return $args;
}


function kareless_about_page_promo() {
?>
	<div id="kareless-about-promo">

	</div>
<?php
}

function kareless_remove_sticky_footer() {
	wp_dequeue_script( 'storefront-sticky-payment' );
}

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
