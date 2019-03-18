<?php
/**
TMBI Salesforce DMP
 *
@package TMBI Salesforce DMP
Plugin Name: TMBI Salesforce DMP
Version: 1.0.0
Description: This plugin adds Salesforce DMP tags to the header and uses GPT to call Krux segments <a href='https://readersdigest.atlassian.net/browse/PLT-459' target='_blank'>Read more at PLT-459 ...</a>
Author: DRDT Team
Text Domain: tmbi-salesforce-dmp
 */

/**
 * Including file for author details.
 *
 * @file
 */
require_once 'inc/author-detail.php';
/**
 *  Class TMBI_Salesforce_DMP.
 */
class TMBI_Salesforce_DMP {
	const VERSION                = '1.0.0';
	const DEPENDS                = null;
	const PRIORITY               = 1;
	const SCRIPT_VAR             = 'krux_data';
	const SCRIPT_SLUG            = 'krux-click-tracking';
	const SCRIPT_URL             = 'js/krux-click-traking.js';
	const CONTROL_TAG_START      = '<!-- BEGIN Salesforce DMP ControlTag for "tmbi.com" -->';
	const CONTROL_TAG_END        = '<!-- END Salesforce DMP ControlTag -->';
	const CONTROL_SCRIPT_TAG     = "<script class='kxct' data-id='s9xpab5u5' data-timing='async' data-version='3.0' type='text/javascript'>%s</script>";
	const INTERCHANGE_SCRIPT_TAG = "<script class='kxint' data-namespace='trustedmediabrandsinc' type='text/javascript'>%s</script>";
	/**
	 * Variable to exclude the taxonomies
	 *
	 * @var array
	 */
	public static $excluded_taxonomies = array( 'yst_prominent_words', 'post_tag', 'category', 'exclude_feed', 'post_format', 'author' );
	/**
	 * Variable to build the krux data for localization
	 *
	 * @var array
	 */
	public static $krux_data_layer = array();
	/**
	 * Initiate the methods
	 */
	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'render_salesforce_dmp_tags' ), self::PRIORITY );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_krux_click_tracking' ) );
		add_action( 'wp_footer', array( __CLASS__, 'remove_krux_tracking' ), 1 );
	}
	/**
	 * Localize the Krux targeting data
	 */
	public static function load_krux_click_tracking() {
		wp_register_script(
			self::SCRIPT_SLUG,
			plugin_dir_url( __FILE__ ) . self::SCRIPT_URL,
			array( 'jquery' ),
			self::VERSION,
			false
		);
		if ( class_exists( 'Ad_Stack' ) ) {
			wp_localize_script(
				'ad-stack',
				self::SCRIPT_VAR,
				self::krux_build_datalayer()
			);
		}
	}
	/**
	 * Add the salesforce tags
	 */
	public static function render_salesforce_dmp_tags() {
		$control_tag_script    = PHP_EOL . "window.Krux||((Krux=function(){Krux.q.push(arguments)}).q=[]);
  (function(){
    var k=document.createElement('script');k.type='text/javascript';k.async=true;
    k.src=(location.protocol==='https:'?'https:':'http:')+'//cdn.krxd.net/controltag/s9xpab5u5.js';
    var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(k,s);
  }());" . PHP_EOL;
		$contol_tag            = sprintf( PHP_EOL . PHP_EOL . self::CONTROL_TAG_START . PHP_EOL . self::CONTROL_SCRIPT_TAG . PHP_EOL . self::CONTROL_TAG_END . PHP_EOL, $control_tag_script );
		$interchage_tag_script = PHP_EOL . "window.Krux||((Krux=function(){Krux.q.push(arguments);}).q=[]);
(function(){
  function retrieve(n){
    var k= 'kx'+'trustedmediabrandsinc_'+n, ls=(function(){
      try {
        return window.localStorage;
      } catch(e) {
        return null;
      }
    })();
    if (ls) {
        return ls[k] || '';
    } else if (navigator.cookieEnabled) {
        var m = document.cookie.match(k+'=([^;]*)');
        return (m && unescape(m[1])) || '';
    } else {
        return '';
    }
  }
  Krux.user = retrieve('user');
  Krux.segments = retrieve('segs') ? retrieve('segs').split(',') : [];
})();" . PHP_EOL;
		$allowed_html          = [
			'script' => [
				'class'          => [],
				'data-id'        => [],
				'data-timing'    => [],
				'data-version'   => [],
				'type'           => [],
				'data-namespace' => [],
			],
		];
		$interchage_tag        = sprintf( PHP_EOL . self::INTERCHANGE_SCRIPT_TAG, $interchage_tag_script );
		$variant               = get_query_var( 'variant' );
		if ( 'noads' !== $variant ) {
			echo wp_kses( $contol_tag . $interchage_tag, $allowed_html );
		}
	}
	/**
	 * Function to build the krux data layer
	 *
	 * @return array
	 */
	public static function krux_build_datalayer() {
		if ( ! is_single() ) {
			return array();
		}
		$top_level_categories = '';
		$sub_categories       = '';
		$categories           = array();
		$keywords             = '';
		if ( class_exists( 'Ad_Stack' ) ) {
			$categories = self::get_category_data();
			$keywords   = self::get_post_tags();
		}
		$content_id = get_the_ID();
		if ( is_array( $categories ) ) {
			if ( array_key_exists( 'parent_categories', $categories ) && ( class_exists( 'Ad_Stack' ) ) ) {
				$top_level_categories = self::format_data( $categories['parent_categories'] );
			}
			if ( array_key_exists( 'sub_categories', $categories ) && ( class_exists( 'Ad_Stack' ) ) ) {
				$sub_categories = self::format_data( $categories['sub_categories'] );
			}
		}
		self::$krux_data_layer['page']                  = array(
			'category' => $top_level_categories,
			'topic'    => $sub_categories,
			'keyword'  => $keywords,
		);
		self::$krux_data_layer['page']['contentid']     = $content_id;
		self::$krux_data_layer['page']['author']        = self::get_post_authors();
		self::$krux_data_layer['page']['gs_categories'] = json_decode( get_post_meta( $content_id, 'gs_channels', true ) );
		$tax_array                                      = self::get_post_taxonomy();
		if ( $tax_array && ( class_exists( 'Ad_Stack' ) ) ) {
			foreach ( $tax_array as $tax => $value ) {
				self::$krux_data_layer['page'][ $tax ] = self::format_data( $value );
			}
		}
		return self::$krux_data_layer;
	}
	/**
	 * Get the taxonomies of the posts
	 *
	 * @return array|string
	 */
	public static function get_post_taxonomy() {
		if ( ! is_single() ) {
			return '';
		}
		$taxonomies_targeting = array();
		$taxonomies           = get_object_taxonomies( get_post() );
		$relevant_taxonomies  = array_filter(
			$taxonomies,
			function( $taxonomy ) {
				return ! in_array( $taxonomy, self::$excluded_taxonomies, true );
			}
		);
		$relevant_taxonomies  = array_values( $relevant_taxonomies );
		$terms                = wp_get_post_terms( get_the_ID(), $relevant_taxonomies );
		foreach ( $terms as $term ) {
			$taxonomies_targeting[ $term->taxonomy ][] = strtolower( str_replace( ' ', '_', $term->name ) );
		}
		return $taxonomies_targeting;
	}
	/**
	 * Get the authors of the post
	 *
	 * @return array|string
	 */
	public static function get_post_authors() {
		$author_list_format = '';
		$author_list        = get_the_authors();
		if ( $author_list ) {
			$author_list_format = array_column( $author_list, 'author_name' );
		}
		$author_list_format = implode( ',', $author_list_format );
		return $author_list_format;
	}
	/**
	 * Formatting data by passing it through.
	 *
	 * @param string $data to format.
	 * @return string of the formatted data.
	 */
	public static function format_data( $data ) {
		if ( ! is_array( $data ) ) {
			$data = explode( ',', $data );
		}
		$data_count = count( $data );
		for ( $i = 0; $i < $data_count; $i++ ) {
			$text       = remove_nonalphanum( $data[ $i ] );
			$data[ $i ] = $text;
		}
		$value = implode( '', $data );
		if ( count( $data ) > 1 ) {
			$value = implode( ',', $data );
		}
		return $value;
	}
	/**
	 * Get all the Parent Category and the Child category.
	 *
	 * @param boolean $adunit for displaying an ad.
	 * @return array of the categories.
	 **/
	public static function get_category_data( $adunit = false ) {
		$exposed_values                      = array();
		$parent_categories                   = array();
		$sub_categories                      = array();
		$exposed_values['parent_categories'] = 'no_value';
		$exposed_values['sub_categories']    = 'no_value';
		if ( is_single() ) {
			global $post;
			$category_tax = 'category';
			$categories   = get_the_terms( $post->ID, $category_tax );
			if ( ! empty( $categories ) ) {
				foreach ( $categories as $term ) {
					$ancestors = get_ancestors( $term->term_id, $category_tax );
					if ( ! empty( $ancestors ) ) {
						foreach ( $ancestors as $ancestor ) {
							$remove[] = "'";
							if ( ! $adunit ) {
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
				if ( ! empty( $parent_categories ) ) {
					$exposed_values['parent_categories'] = $parent_categories;
				}
				if ( ! empty( $sub_categories ) ) {
					$exposed_values['sub_categories'] = $sub_categories;
				}
			}
		} elseif ( is_category() || is_archive() ) {
			$q_object = get_queried_object();
			// WP_Post_Type archives don't have a "slug" property.
			if ( ! empty( $q_object->slug ) ) {
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
	/**
	 * Get the keywords/tags associated with the post
	 *
	 * @return string of tags
	 **/
	public static function get_post_tags() {
		if ( is_singular() ) {
			$post_tags = get_the_tags( get_the_ID() );
			if ( ! empty( $post_tags ) ) {
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
	/**
	 * Remove Krux (for ?variant=noads).
	 */
	public static function remove_krux_tracking() {
		$variant = get_query_var( 'variant' );
		if ( 'noads' === $variant ) {
			wp_dequeue_script( self::SCRIPT_SLUG );
		}
	}
}
add_action( 'init', array( 'TMBI_Salesforce_DMP', 'init' ) );
