<?php

require_once 'legacy/parse_data_layer.php';
require_once 'legacy/global_data_layer.php';
require_once 'legacy/home_data_layer.php';
require_once 'legacy/newsletter_data_layer.php';
require_once 'legacy/source_data_layer.php';
require_once 'legacy/magazine_issue_data_layer.php';
require_once 'legacy/listicle_data_layer.php';
require_once 'legacy/single_data_layer.php';
require_once 'legacy/archive_data_layer.php';
require_once 'legacy/guest_authors_data_layer.php';

function dtm_get_post_type( $post_type ) {
	if ( $post_type === 'post' ) {
		$post_type = 'article';
	}
	if ( $post_type === 'slicklist' ) {
		$post_type = 'listicle';
	}
	if ( $post_type === 'collection' ) {
		$post_type = 'listicle';
	}
	return $post_type;
}

function dtm_get_tags( $post_id ) {
	$tag_name_array = array();
	$tags           = get_the_tags( $post_id );
	$tag_string     = '';
	if ( $tags ) {
		foreach ( $tags as $tag ) {
			$tag_name_array[] = $tag->name;
		}
		$tag_string = implode( ', ', $tag_name_array );
	}

	return( $tag_string );
}

function dtm_get_categories( $post_id ) {
	$category_name_array = array();
	$categories          = get_the_category( $post_id );
	$category_string     = '';
	if ( $categories ) {
		foreach ( $categories as $category ) {
			$category_name_array[] = $category->slug;
		}
		$category_string = implode( ', ', $category_name_array );
	}

	return( $category_string );
}

function dtm_get_content_cost( $post_id ) {
	$content_cost = get_post_meta( $post_id, 'cost-meta', true );
	return( $content_cost );
}

function dtm_get_author_name( $author_id ) {
	$author = get_userdata( $author_id );
	if ( $author ) {
		$author_name = $author->display_name;
		return ( $author_name );
	}
	return ( '' );
}

function dtm_get_author_roles( $user_id ) {
	$author_role = '';
	$user = get_userdata( $user_id );
	if ( ! empty( $user->roles ) ) {
		$author_role = implode( ', ', $user->roles );
	}
	return( $author_role );
}

function dtm_get_sub_categories( $post_id ) {
	$categories     = get_the_category( $post_id );
	$category_array = array(
		'subcategory'    => '',
		'subsubcategory' => '',
	);
	if ( $categories ) {
		$primary_category = '';
		if ( class_exists( 'WPSEO_Primary_Term' ) ) {
			$wpseo_primary_term = new WPSEO_Primary_Term( 'category', $post_id );
			$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
			$primary_category   = get_term( $wpseo_primary_term );
		}

		if ( is_wp_error( $primary_category ) ) {
			$primary_category = $categories[0];
		}

		if ( ! empty( $primary_category ) ) {
			$category_array['subcategory'] = htmlspecialchars_decode( $primary_category->name );
			$parent_cat = get_category( $primary_category->parent );
			if ( $primary_category->parent > 0 && $parent_cat ) {
				$category_array['subsubcategory'] = htmlspecialchars_decode( $parent_cat->name );
			}
		}
	}
	return( $category_array );
}

function dtm_get_nickname() {
	$page_name = get_option( 'rd-dtm-nickname' );
	if ( ! $page_name ) {
		$page_name = get_bloginfo();
	}
	return( $page_name );
}

function dtm_get_original_published_date( $post_id = null ) {
	global $post;
	if ( ! $post_id && isset( $post ) ) {
		$post_id = $post->ID;
	}

	if ( ! $post_id ) {
		return false;
	}

	$original_pub_date = get_post_meta( $post_id, Adobe_DTM_Utils::PUBLISH_META_NAME, true );
	if ( $original_pub_date ) {
		$date = date_create( $original_pub_date );
		return date_format( $date, 'Y-m-d' );
	}

	if ( ! isset( $post ) ) {
		$post = get_post( $post_id );
	}
	$date = date_create( $post->post_date );
	return date_format( $date, 'Y-m-d' );
}

function dtm_get_pagename( $pagename_array = array() ) {
	if ( $pagename_array ) {
		$page_name_string = ( implode( ':', array_filter( $pagename_array ) ) );
		if ( get_query_var( 'custom_tax' ) ) { //custom_tax passed in v2 theme taxonomy archive
			$page_name_string .= ':' . get_query_var( 'custom_tax' );
		} elseif ( is_post_type_archive() ) {
			$page_name_string .= ':' . get_queried_object()->name;
		}
		return $page_name_string;
	}
}

function dtm_get_site_section() {
	$query_string = $_SERVER['REQUEST_URI'];
	$query_array  = explode( '/', $query_string );
	if ( $query_array ) {
		return ( $query_array[1] );
	}
	return( null );
}

function dtm_get_wordpress_content_id( $post_type, $post_id ) {
	//$accepted_post_type = array( 'article', 'listicle', 'recipe' );
	//$post_type          = self::get_post_type( $post_type );
	//if ( WP_Base::is_toh() ) {
	//	if ( in_array( $post_type, $accepted_post_type ) ) {
	//		return ( $post_id );
	//	}
	//}
	$content_id = apply_filters( 'dtm_wordpress_content_id', '' );

	return $content_id;
}

/*
 * if it is toh and article/listicle this will return empty
 *
 * @param string $post_type
 * @param int $post_id
 *
 * @return int/empty $post_id
 */
function dtm_get_post_id( $post_type, $post_id ) {
	//$accepted_post_type = array( 'article', 'listicle' );
	//$post_type          = self::get_post_type( $post_type );
	//if ( WP_Base::is_toh() ) {
	//	if ( in_array( $post_type, $accepted_post_type ) ) {
	//		return ( '' );
	//	}
	//
	//	if ( WP_Base::is_recipe() ) {
	//		return get_post_meta( $post_id, 'rms_legacy_id', true );
	//	}
	//}
	$post_id = apply_filters( 'dtm_wordpress_content_id', $post_id );

	return $post_id;
}

