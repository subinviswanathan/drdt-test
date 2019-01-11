<?php
/**
 * Bumblebee functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package bumblebee
 */

if ( ! function_exists( 'bumblebee_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function bumblebee_setup() {
		/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on bumblebee, use a find and replace
		* to change 'bumblebee' to the name of your theme in all the template files.
		*/
		load_theme_textdomain( 'bumblebee', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
		add_theme_support( 'title-tag' );

		/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'menu-1' => esc_html__( 'Primary', 'bumblebee' ),
			)
		);

		/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'bumblebee_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'bumblebee_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function bumblebee_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'bumblebee_content_width', 640 );
}

add_action( 'after_setup_theme', 'bumblebee_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function bumblebee_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'bumblebee' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'bumblebee' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}

add_action( 'widgets_init', 'bumblebee_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function bumblebee_scripts() {
	wp_enqueue_style( 'pure-css', 'https://unpkg.com/purecss@1.0.0/build/pure-min.css', [], '1.0.0' );
	wp_enqueue_style( 'pure-css-grids', 'https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css', [ 'pure-css' ], '1.0.0' );

	wp_enqueue_style( 'bumblebee-style', get_stylesheet_directory_uri() . '/style_main.css', [], '1.0.2' );


	wp_enqueue_script( 'bumblebee-skip-link-focus-fix', get_template_directory_uri() . '/js/src/skip-link-focus-fix.js', array(), '20151215', true );


	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bumblebee_scripts' );

function bumblebee_navigation_scripts() {
	// Move jQuery to footer
	wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.js' ), false, NULL, true );
	wp_enqueue_script( 'slinky', get_stylesheet_directory_uri() . '/js/util/slinky.min.js', array( 'jquery' ), '4.1.0', true );
	wp_enqueue_script( 'bumblebee-navigation', get_template_directory_uri() . '/js/src/navigation.js', array( 'slinky' ), '20151215', true );
}
add_action( 'wp_enqueue_scripts', 'bumblebee_navigation_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	include get_template_directory() . '/inc/jetpack.php';
}

/**
 * Ads.
 */
require get_template_directory() . '/inc/ads.php';

/**
 * Global Targeting Parameters for DFP Ads.
 */
require get_template_directory() . '/inc/ads-global-targeting.php';


register_nav_menu( 'v2-footer-site-links', 'V2 Footer Site Links' );
register_nav_menu( 'v2-footer-social-links', 'V2 Footer Social Links' );
register_nav_menu( 'v2-footer-brand-links', 'V2 Footer Brand Links' );
register_nav_menu( 'v2-footer-global-links', 'V2 Footer Global Links' );
register_nav_menu( 'hamburger-menu', 'hamburger menu' );
register_nav_menu( 'desktop-focus-menu', __( 'Desktop Focus Menu', 'tmbi-theme-v3' ) );
add_image_size( 'homepage-featured-big', 385, 385, true );
add_image_size( 'homepage-featured-small', 165, 165, true );
add_image_size( 'grid-thumbnail', 273, 273, true );


/**
 * Register Custom Post Type.
 */
function listicle_post_type() {

	$labels = array(
		'name'               => _x( 'Listicles', 'Post Type General Name', 'listicle-post-type' ),
		'singular_name'      => _x( 'Listicle', 'Post Type Singular Name', 'listicle-post-type' ),
		'menu_name'          => __( 'Listicles', 'listicle-post-type' ),
		'name_admin_bar'     => __( 'Listicle', 'listicle-post-type' ),
		'archives'           => __( 'Listicle Archives', 'listicle-post-type' ),
		'attributes'         => __( 'Listicle Attributes', 'listicle-post-type' ),
		'parent_item_colon'  => __( 'Parent Item:', 'listicle-post-type' ),
		'all_items'          => __( 'All Listicles', 'listicle-post-type' ),
		'add_new_item'       => __( 'Add New Listicle', 'listicle-post-type' ),
		'add_new'            => __( 'Add New', 'listicle-post-type' ),
		'new_item'           => __( 'New Listicle', 'listicle-post-type' ),
		'edit_item'          => __( 'Edit Listicle', 'listicle-post-type' ),
		'update_item'        => __( 'Update Listicle', 'listicle-post-type' ),
		'view_item'          => __( 'View Listicle', 'listicle-post-type' ),
		'view_items'         => __( 'View Listicles', 'listicle-post-type' ),
		'search_items'       => __( 'Search Listicle', 'listicle-post-type' ),
		'not_found'          => __( 'Not found', 'listicle-post-type' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'listicle-post-type' ),
	);

	$rewrite = array(
		'slug'       => 'listicle',
		'with_front' => false,
		'feeds'      => true,
	);

	$args = array(
		'label'               => __( 'Listicle', 'listicle-post-type' ),
		'description'         => __( 'Listicle post type', 'listicle-post-type' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'trackbacks', 'revisions', 'custom-fields', 'post-formats' ),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-list-view',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
		'rest_base'           => 'listicle',
	);
	register_post_type( 'listicle', $args );

}
add_action( 'init', 'listicle_post_type', 0 );




/**
 * Changes the CPT permalink structure to use %category%/%postname%
 *
 * @param String $post_link  content.
 * @param String $post  content.
 * @param String $leavename  content.
 * @param String $sample  content.
 */
function setup_url_scheme( $post_link, $post, $leavename, $sample ) {

	$cpt = 'listicle';

	if ( ! $post instanceof WP_Post ) {
		return ( $post_link );
	}

	if ( $post->post_type !== $cpt ) {
		return ( $post_link );
	}

	if ( $post->post_type === $cpt ) {
		parse_str( wp_parse_url( $post_link, PHP_URL_QUERY ), $parts );

		// Only modify "pretty" permalinks.
		if ( ! empty( $parts['p'] ) ) {
			return ( $post_link );
		}

		$category = get_post_category( $post );
		if ( $category ) {
			if ( $sample ) {
				$post_link = trailingslashit( get_term_link( $category ) ) . '%postname%';
			} else {
				$post_link = trailingslashit( get_term_link( $category ) ) . $post->post_name;
			}
		}

		$post_link = trailingslashit( $post_link );
	}
	return ( $post_link );
}


/**
 * Changes the CPT permalink structure to use %category%/%postname%.
 *
 *  @param String $post  null.
 */
function get_post_category( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return;
	}

	$categories = get_the_terms( $post->ID, 'category' );
	if ( ! empty( $categories ) ) {
		return ( $categories[0] );
	}
}
add_action( 'post_type_link', 'setup_url_scheme', 1, 4 );


/**
 * Pre get posts for listicle.
 *
 * @param String $query  query.
 */
function set_listicle_post_type( $query ) {

	if ( ! $query->is_main_query() ) {
		return;
	}

	if ( ! is_admin() && ! $query->get( 'pagename' ) && ( ! $query->get( 'post_type' ) || 'post' === $query->get( 'post_type' ) ) ) {
		$query->set( 'post_type', array( 'listicle', 'post' ) );
	}
}
add_action( 'pre_get_posts', 'set_listicle_post_type' );
