<?php
/**
 * Bumblebee functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package bumblebee
 */

if ( !function_exists( 'bumblebee_setup' ) ) :
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
	wp_enqueue_style( 'pure-css-grids', 'https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css', ['pure-css'], '1.0.0' );

	wp_enqueue_style( 'bumblebee-style', get_stylesheet_uri(), [], '1.0.2' );

	wp_enqueue_script( 'bumblebee-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'bumblebee-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'bumblebee_scripts' );

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

add_action( 'wp_head', 'ads_global_targeting_parameters' );
function ads_global_targeting_parameters() {
	$top_level_categories = '';
	$sub_categories       = '';
	$categories           = get_category_data();
	if ( is_array( $categories ) && array_key_exists( 'parent_categories', $categories ) ) {
		$top_level_categories = format_data( $categories['parent_categories'] );
	}
	if ( is_array( $categories ) && array_key_exists( 'sub_categories', $categories ) ) {
		$sub_categories = format_data( $categories['sub_categories'] );
	}

	// Page type
	$page_type    = get_page_type();
	$script_open  = '<script type="text/javascript">';
	$script_close = '</script>';
	$url_part     = $_SERVER['REQUEST_URI'];
	$g_targeting  = array(
		'property' => '6178',
		'siteId'   => 'cpt',
		'pageType' => $page_type,
		'urlPath'  => $url_part,
		'keyWords' => get_post_tags(),
		'category' => $top_level_categories,
		'topic'    => $sub_categories,
	);
	printf( PHP_EOL . $script_open . PHP_EOL . 'var tmbi_ad_data = %s' . PHP_EOL . $script_close . PHP_EOL, json_encode( $g_targeting ) );
}

/**
 * Get the keywords/tags associated with the post
 *
 * @return string of tags
 *
 **/
function get_post_tags() {

	if ( is_singular() ) {
		$post_tags = get_the_tags( get_the_ID() );
		if ( !empty( $post_tags ) ) {
			$tag_names  = array_column( $post_tags, 'name' );
			$tag_string = implode( ', ', $tag_names );
		} else {
			$tag_string = 'no_keywords';
		}
	} else {
		$tag_string = 'no_keywords';
	}
	$value = format_data( $tag_string );
	return ( $value );
}

function format_data( $data ) {
	if ( !is_array( $data ) ) {
		$data = explode( ',', $data );
	}
	for ( $i = 0; $i < count( $data ); $i++ ) {
		$text       = remove_nonalphanum( $data[ $i ] );
		$data[ $i ] = $text;
	}
	$value = implode( '', $data );
	if ( count( $data ) > 1 ) {
		$value = implode( ',', $data );
	}
	return $value;
}

function remove_nonalphanum( $data ) {
	$text = trim( $data, ' ' );
	$text = str_replace( ' ', '-', $text );
	$text = preg_replace( '/[^A-Za-z0-9-]/', '', $text );
	return strtolower( $text );
}

/**
 * Get all the Parent Category and the Child category
 *
 * @return array of the categories
 **/
function get_category_data( $adunit = false ) {
	$exposed_values                      = array();
	$parent_categories                   = array();
	$sub_categories                      = array();
	$exposed_values['parent_categories'] = 'no_value';
	$exposed_values['sub_categories']    = 'no_value';
	if ( is_single() ) {
		global $post;
		$category_tax = 'category';
		$categories   = get_the_terms( $post->ID, $category_tax );
		if ( !empty( $categories ) ) {
			foreach ( $categories as $term ) {
				$ancestors = get_ancestors( $term->term_id, $category_tax );
				if ( !empty( $ancestors ) ) {
					foreach ( $ancestors as $ancestor ) {
						$remove[] = "'";
						if ( !$adunit ) {
							$remove[] = '-';
						}
						$ancestor_term = get_term( $ancestor );
						array_push( $parent_categories, str_replace( $remove, '', $ancestor_term->slug ) );
						array_push( $sub_categories, str_replace( $remove, '', $term->slug ) );
					}
				} else {
					$term_slug = $term->slug;
					array_push( $parent_categories, $term_slug );
				}
			}
			$filter_similar_parent_cats = array_unique( $parent_categories, SORT_STRING );
			$parent_categories          = implode( ', ', $filter_similar_parent_cats );
			$filter_similar_sub_cats    = array_unique( $sub_categories, SORT_STRING );
			$sub_categories             = implode( ', ', $filter_similar_sub_cats );
			if ( !empty( $parent_categories ) ) {
				$exposed_values['parent_categories'] = $parent_categories;
			}
			if ( !empty( $sub_categories ) ) {
				$exposed_values['sub_categories'] = $sub_categories;
			}
		}
	} elseif ( is_category() || is_archive() ) {
		$q_object = get_queried_object();
		// WP_Post_Type archives don't have a "slug" property
		if ( !empty( $q_object->slug ) ) {
			$cat_slug                            = $q_object->slug;
			$exposed_values['parent_categories'] = $cat_slug;
			$exposed_values['sub_categories']    = $cat_slug;
		}
		if ( is_post_type_archive( 'joke' ) ) {
			$exposed_values['parent_categories'] = 'jokes';
			$exposed_values['sub_categories']    = 'jokes';
		}
	} elseif ( is_page() ) {
		$exposed_values['parent_categories'] = 'misc';
		$exposed_values['sub_categories']    = 'misc';
	} elseif ( is_home() && is_front_page() ) {
		$exposed_values['parent_categories'] = 'homepage';
		$exposed_values['sub_categories']    = 'homepage';
	}
	return $exposed_values;
}

function get_page_type() {
	$post_type = 'post';
	if ( is_front_page() && is_home() ) {
		$post_type = 'homepage';
	} elseif ( is_archive() ) {
		$post_type = 'archive';
	} elseif ( is_singular( 'listicle' ) ) {
		$post_type = 'archive';
	}
	return ( $post_type );
}