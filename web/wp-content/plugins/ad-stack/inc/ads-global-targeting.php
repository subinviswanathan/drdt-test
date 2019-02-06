<?php
/**
 * Bumblebee functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package bumblebee
 */

add_action( 'wp_head', 'ads_global_targeting_parameters' );

/**
 * Global Targeting Parameters for DFP ads.
 */
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

	// Page type.
	$unslash_url = '';
	$page_type   = get_page_type();
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$unslash_url = filter_var( wp_unslash( $_SERVER['REQUEST_URI'] ), FILTER_SANITIZE_STRING );
	}
	$url_part = $unslash_url;

	$g_targeting = array(
		'property' => '6178',
		'siteId'   => 'cpt',
		'pageType' => $page_type,
		'urlPath'  => $url_part,
		'keyWords' => get_post_tags(),
		'category' => $top_level_categories,
		'topic'    => $sub_categories,
	);
	printf( PHP_EOL . '<script type="text/javascript"> var tmbi_ad_data = %s </script>' . PHP_EOL, wp_json_encode( $g_targeting ) );
}

/**
 * Get the keywords/tags associated with the post
 *
 * @return string of tags
 **/
function get_post_tags() {

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
 * Formatting data by passing it through.
 *
 * @param string $data to format.
 * @return string of the formatted data.
 */
function format_data( $data ) {
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
 * Formatting data consisting nonalphanumeric characters using.
 *
 * @param string $data to clean.
 * @return string of the sanitized data.
 */
function remove_nonalphanum( $data ) {
	$text = trim( $data, ' ' );
	$text = str_replace( ' ', '-', $text );
	$text = preg_replace( '/[^A-Za-z0-9-]/', '', $text );
	return strtolower( $text );
}

/**
 * Get all the Parent Category and the Child category.
 *
 * @param boolean $adunit for displaying an ad.
 * @return array of the categories.
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
 * Detect page type.
 */
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
