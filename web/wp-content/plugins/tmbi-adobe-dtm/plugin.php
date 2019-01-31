<?php
/*
Plugin Name: RD Adobe DTM
Version: 1.3.9
Description: Adobe DTM <a href='https://readersdigest.atlassian.net/browse/WPDT-3395' target='_blank'>Read more at WPDT-3395</a>
Author: Jey
Plugin URI: https://readersdigest.atlassian.net/browse/WPDT-3395
Text Domain: rd-adobe-dtm
*/

require_once 'legacy.php';

require 'inc/rd-dtm-settings.php';
require 'inc/image-credits-dtm.php';
require 'inc/adobe_dtm_utils.php';
//require 'inc/amp.php';

class RD_Adobe_DTM {
	const VERSION         = '2.0.0';
	const PRIORITY        = '5';
	const PROCESSOR_SLUG  = 'rd_adobe_dtm';
	const JS_FILE         = 'js/adobe_dtm.js';
	const FILE_SPEC       = __FILE__;
	const DATA_LAYER_NAME = 'digitalData';

	private $adobe_dtm_slug;
	public $depends         = array( 'jquery' );
	public $data_layer      = array();
	public $data_layer_json = '';


	/**
	 * RD_Adobe_DTM constructor.
	 */
	public function __construct() {

		if ( class_exists( 'RD_DTM_Settings' ) ) {
			$rd_dtm_settings      = new RD_DTM_Settings();
			$this->adobe_dtm_slug = $rd_dtm_settings::RD_DTM_SLUG;
		}
		new Image_Credits_DTM();
		//new ADTM_AMP();

		/*
		 * Instantiate Adobe DTM utils class
		 */
		new Adobe_DTM_Utils();

		add_action( 'wp_head', array( $this, 'page_header' ), static::PRIORITY );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

	}

	/**
	 * Enqueuing script to process tagging
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		if ( ! get_option( RD_DTM_Settings::RD_DTM_SLUG ) ) {
			return;
		}

		// Madatory for all sites.
		wp_register_script(
			self::PROCESSOR_SLUG,
			plugins_url( self::JS_FILE, __FILE__ ),
			$this->depends,
			self::VERSION,
			true
		);
		wp_enqueue_script( self::PROCESSOR_SLUG );

	}

	/**
	 * Enqueue Adobe Code and associated data
	 */
	public function page_header() {

		if ( ! get_option( RD_DTM_Settings::RD_DTM_SLUG ) ) {
			return;
		}

		if ( ! is_admin() ) {
			$this->build_data_layer();
		}

		$parsed_array = self::parse_data_layer_config( $this->data_layer );

		$parsed_array = apply_filters( 'dtm_data_layer', array() );

		wp_register_script(
			'adobe-dtm-js',
			get_option( RD_DTM_Settings::RD_DTM_SLUG ),
			array(),
			self::VERSION,
			false
		);
		wp_enqueue_script( 'adobe-dtm-js' );

		wp_localize_script( 'adobe-dtm-js', self::DATA_LAYER_NAME, $parsed_array );

	}


	public function build_data_layer() {

		$this->data_layer['page.theme']    = get_stylesheet();
		$this->data_layer['page.sitename'] = self::get_nickname();
		return;
		$this->data_layer['page.pageName'] = html_entity_decode( strip_tags( wp_title( '|', false, 'right' ) ), ENT_QUOTES );
		$this->data_layer['page.content.contentName'] = html_entity_decode( strip_tags( wp_title( '|', false, 'right' ) ), ENT_QUOTES );

		if ( is_home() ) {
			$this->data_layer['page.category.pageType'] = 'Homepage'; // at page 5
			$this->data_layer['page.pageName']          = self::get_pagename( array( self::get_nickname(), 'Homepage' ) );
			$this->data_layer['page.content.contentType'] = 'homepage';
		}

		$design_var = 'redesign:design_v1';
		if ( WP_Base::is_tmbi_theme_v3() ) {
			$design_var = 'redesign:design_v2';
		}
		$this->data_layer['ab.testing'] = $design_var;

		if ( is_archive() ) {

			$this->data_layer['page.category.pageType'] = get_the_archive_title();
			if ( WP_Base::is_tmbi_theme_v3() ) {
				$this->data_layer['page.content.contentType'] = 'hubs';
			}

			$categories  = self::get_archive_categories();

			// Assign only 3 deep categories
			$sub_category = array();
			$sub_category[0] = isset( $categories[0] ) ? $categories[0] : '';
			$sub_category[1] = isset( $categories[1] ) ? $categories[1] : '';
			$sub_category[2] = isset( $categories[2] ) ? $categories[2] : '';

			$this->data_layer['page.subCategory']                = $sub_category[0];
			$this->data_layer['page.subsubCategory']             = $sub_category[1];
			$this->data_layer['page.subsubsubCategory']          = $sub_category[2];
			$this->data_layer['page.category.subCategory']       = $sub_category[0];
			$this->data_layer['page.category.subsubCategory']    = $sub_category[1];
			$this->data_layer['page.category.subsubsubCategory'] = $sub_category[2];

			array_unshift( $categories, self::get_nickname() );

			$this->data_layer['page.pageName'] = self::get_pagename( $categories );
		}

		if ( is_single() ) {
			global $post, $numpages;

			$this->set_source( $post->ID );
			$this->set_tmbi_brand();
			$this->set_magazine_issue( $post->ID );
			$this->set_newsletter_campaign();
			$categories = $this->get_sub_categories( $post->ID );
			$this->data_layer['page.sitename']                  = self::get_nickname();
			$this->data_layer['page.subCategory']               = $categories['subcategory'];
			$this->data_layer['page.subsubCategory']            = $categories['subsubcategory'];
			$this->data_layer['page.category.subCategory']      = $categories['subcategory'];
			$this->data_layer['page.category.subsubCategory']   = $categories['subsubcategory'];
			$this->data_layer['page.category.pageType']         = $this->get_categories( $post->ID ); // at page 5
			$this->data_layer['page.content.contentName']       = $post->post_title;
			$this->data_layer['page.content.contentID']         = self::get_post_id( $post->post_type, $post->ID );
			$this->data_layer['page.content.wpContentID']       = self::get_toh_wordpress_content_id( $post->post_type, $post->ID );
			$this->data_layer['page.content.contentType']       = self::get_post_type( $post->post_type );
			$this->data_layer['page.content.category']          = $this->get_categories( $post->ID );
			$this->data_layer['page.content.tags']              = self::get_tags( $post->ID );
			$this->data_layer['page.content.contentCost']       = $this->get_content_cost( $post->ID );
			$this->data_layer['page.content.publishedDate']     = Adobe_DTM_Utils::get_original_published_date( $post->ID ); // at page 15
			$this->data_layer['page.content.modifiedDate']      = get_the_modified_date( 'Y-m-d' ); // at page 15
			$this->data_layer['page.content.image.licensorName'] = Image_Credits_DTM::get_image_licensor_name( $post->ID );
			$this->data_layer['page.content.image.credits']      = Image_Credits_DTM::get_image_credits( $post->ID );

			if ( ! $this->set_author_guest( $post->ID ) ) {
				$this->data_layer['page.content.author']        = $this->get_author_name( $post->post_author );
				$this->data_layer['page.content.authorRole']    = $this->get_author_roles( $post->post_author );
			}

			$this->data_layer['page.pageName']                = self::get_pagename( array(
				self::get_nickname(),
				$categories['subcategory'],
				$categories['subsubcategory'],
				self::get_post_type( $post->post_type ),
				$post->post_title,
				)
			);

			if ( $this->is_slideshow() ) {

				$this->data_layer['page.content.slideShowEvent'] = true;
				$page = (int) get_query_var( 'page' );
				if ( $page ) {
					$this->data_layer['page.content.slideNo'] = $page;
					$this->data_layer['page.content.slideShowMulti'] = true;
				} else {
					$this->data_layer['page.content.slideShowSingle'] = true;
				}
			}

			if ( static::is_listicle() || static::is_collection() ) {

				$this->data_layer['page.content.listicleEvent'] = true;
				$page = (int) get_query_var( 'page' );

				// Bypassing end variables in url for fhm
				if ( ( self::get_nickname() === ( 'fhm' ) || self::get_nickname() === ( 'toh' ) ) && ! $page && ! strpos( $_SERVER['REQUEST_URI'], '/view-all' ) ) {
					$page = 1;
				}

				if ( $page ) {
					$this->data_layer['page.content.cardNo'] = $page;
					$this->data_layer['page.content.slideShowMulti'] = true;
				} else {
					$this->data_layer['page.content.slideShowSingle'] = true;
					$numpages = count( $content_pages = explode( '<!--nextpage-->', $post->post_content ) );
					if ( $numpages ) {
						$this->data_layer['page.content.slideTotal']  = $numpages;
					}
				}
			}

			if ( $this->is_nicestplace() ) {
				$this->set_author_guest( $post->ID );
				$this->data_layer['page.content.category'] = 'Nicest Place';
				$this->data_layer['page.content.contentID'] = $post->ID;
			}

			if ( WP_Base::is_recipe() ) {
				$original_source = get_post_meta( $post->ID, 'rms_original_source', true );
				if ( $original_source ) {
					$this->data_layer['page.content.partnerName'] = $original_source;
				}
			}

			if ( WP_Base::is_fhm() ) {
				$this->data_layer['page.content.class'] = $this->fhm_get_class_name( $post->ID );
			}
		}
		if ( is_page( 'fbia-dax' ) ) {
			if ( ! empty( $_GET['contentId'] ) && $_GET['contentId'] > 0  ) {
				$content_id = intval( $_GET['contentId'] );
				$post       = get_post( $content_id );
				setup_postdata( $post );

				$meta = get_post_meta( $post->ID, '', true );
				if ( ! empty( $meta['_yoast_wpseo_title'][0] ) ) {
					$page_name = trim( $meta['_yoast_wpseo_title'][0] );
				}else {
					$page_name = $post->post_title;
				}

				$this->data_layer['page.pageName']                = $page_name;
				$this->set_source( $post->ID );
				$this->set_tmbi_brand();
				$this->set_magazine_issue( $post->ID );

				$this->data_layer['page.category.pageType']       = $this->get_categories( $post->ID ); // at page 5
				$this->data_layer['page.content.contentID']       = $post->ID;
				$this->data_layer['page.content.contentName']     = $post->post_title;
				$this->data_layer['page.content.contentType']     = self::get_post_type( $post->post_type );
				$this->data_layer['page.content.category']        = $this->get_categories( $post->ID );
				$this->data_layer['page.content.tags']            = self::get_tags( $post->ID );
				$this->data_layer['page.content.contentCost']     = $this->get_content_cost( $post->ID );
				$this->data_layer['page.content.author']          = $this->get_author_name( $post->post_author );
				$this->data_layer['page.content.authorRole']      = $this->get_author_roles( $post->post_author );
				$this->data_layer['page.content.publishedDate']   = get_the_date( 'Y-m-d' ); // at page 15
				$this->data_layer['page.content.modifiedDate']    = get_the_modified_date( 'Y-m-d' ); // at page 15
				if ( $this->data_layer['page.content.contentType'] == 'listicle' ) {
					$this->data_layer['page.content.slideShowMulti'] = true;
				}
			}
		}

		if ( is_page( 'newslettersignuppage-updatepreference' ) ) {
			$dtm_data = apply_filters( 'newsletter_dtm_data', array() );
			if ( $dtm_data ) {
				$this->data_layer['subscription.selectAllEvent'] = $dtm_data['select_all'];
				$this->data_layer['subscription.signupEvent'] = $dtm_data['signup_event'];
				$this->data_layer['subscription.signupCount'] = $dtm_data['signup_count'];
				$this->data_layer['subscription.formName'] = $dtm_data['signup_form'];
			}
		}
	}

	/*
	 * if it is toh and article/listicle this will return empty
	 *
	 * @param string $post_type
	 * @param int $post_id
	 *
	 * @return int/empty $post_id
	 */
	public static function get_post_id( $post_type, $post_id ) {
		$accepted_post_type = array( 'article', 'listicle' );
		$post_type          = self::get_post_type( $post_type );
		if ( WP_Base::is_toh() ) {
			if ( in_array( $post_type, $accepted_post_type ) ) {
				return ( '' );
			}

			if ( WP_Base::is_recipe() ) {
				return get_post_meta( $post_id, 'rms_legacy_id', true );
			}
		}
		return ( $post_id );
	}

	/*
	 * Return values only on TOH articles/collections. Adobe DTM JS will ignore empty strings
	 *
	 * @param string $post_type
	 * @param int $post_id
	 *
	 * @return int/empty
	 */
	public static function get_toh_wordpress_content_id( $post_type, $post_id ) {
		$accepted_post_type = array( 'article', 'listicle', 'recipe' );
		$post_type          = self::get_post_type( $post_type );
		if ( WP_Base::is_toh() ) {
			if ( in_array( $post_type, $accepted_post_type ) ) {
				return ( $post_id );
			}
		}
		return ( '' );
	}

	/*
	 * Get archive categories from queried variable
	 * @return array $term_array
	 */
	public static function get_archive_categories() {
		$term_array = array();

		if ( is_archive() ) {
			$term = get_queried_object();
			if ( $term instanceof WP_Term ) {
				$parent_terms = get_ancestors( $term->term_id, $term->taxonomy );
				foreach ( array_reverse( $parent_terms ) as $parent_term ) {
					$term_array[] = get_term( $parent_term )->slug;
				}
				array_push( $term_array, $term->slug );
			}
		}
		return $term_array;
	}


	/**
	 * @return array
	 */
	public static function get_sub_categories( $post_id = null ) {
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
				if ( $primary_category->parent > 0 && $parent_cat = get_category( $primary_category->parent ) ) {
					$category_array['subsubcategory'] = htmlspecialchars_decode( $parent_cat->name );
				}
			}
		}
		return( $category_array );
	}

	/*
     * Google Accelerated Mobile Pages variables
	 */
	public static function rd_amp_vars() {
		global $post;

		$categories  = self::get_sub_categories( $post->ID );
		$pagename    = self::get_pagename( array(
				self::get_nickname(),
				$categories['subcategory'],
				$categories['subsubcategory'],
				self::get_post_type( $post->post_type ),
				$post->post_title,
			)
		);

		$vars = array(
			'basic' => array(
				'trackingServer' => 'trustedmediabrands.sc.omtrdc.net',
				'accounts'       => self::get_server_account(),
				'pageName'       => strtolower( $pagename ),
				'g'              => self::get_page_url(),
				'ch'             => self::get_site_section(),
				'events'         => self::get_events(),
			),
			'optional' => array_filter(
				array(
					'v1'             => strtolower( $pagename ),
					'v2'             => self::get_page_url(),
					'c4'             => self::get_categories( $post->ID ),
					'c5'             => $_SERVER['HTTP_HOST'],
					'c7'             => self::get_post_type( $post->post_type ),
					'c8'             => self::get_post_id( $post->post_type, $post->ID ),
					'c9'             => strtolower( $post->post_title ),
					'v12'            => self::get_post_type( $post->post_type ),
					'v13'            => self::get_post_id( $post->post_type, $post->ID ),
					'v14'            => strtolower( $post->post_title ),
					'c15'            => self::get_tags( $post->ID ),
					'v20'            => self::get_tags( $post->ID ),
					'c47'            => self::get_toh_wordpress_content_id( $post->post_type, $post->ID ),
					'c59'            => self::get_toh_wordpress_content_id( $post->post_type, $post->ID ),
					)
			),
		);

		return( $vars );
	}

	public static function get_vars_implode( $vars ) {
		$var_string = '';
		foreach ( $vars as $key => $var ) {
			$var_string .= $key . '=${' . $key . '}&';
		}
		return( $var_string );
	}

	/*
     * event1 => default pageload event
     * event32 => google amp view
     * event41 => single page slideshow/listicle
     * event42 => listicle event
     * @returns string event
	 */
	public static function get_events() {
		$event = 'event1,event32';
		// currently amp only supports listview
		if ( self::is_listicle() || self::is_collection() ) {
			$event .= ',event41,event42';
		}
		return( $event );
	}

	public static function get_pagename( $pagename_array = array() ) {
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

	public static function get_nickname() {
		if ( ! $page_name = get_option( RD_DTM_Settings::NICKNAME_SLUG ) ) {
			$page_name = get_bloginfo();
		}
		return( $page_name );
	}

	public static function get_server_account() {
		if ( ! $server_account = get_option( RD_DTM_Settings::AMP_SERVER_ACCOUNT ) ) {
			$server_account = 'tmbrandsdev';
		}
		return( $server_account );
	}

	public static function get_page_url() {
		$page_url = ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		return( $page_url );
	}

	public static function get_site_section() {
		$query_string = $_SERVER['REQUEST_URI'];
		$query_array  = explode( '/', $query_string );
		if ( $query_array ) {
			return ( $query_array[1] );
		}
		return( null );
	}

	public static function get_post_type( $post_type ) {
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

	public static function get_categories( $post_id ) {
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

	public static function get_tags( $post_id ) {
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

	public function array_to_string( $value ) {
		if ( is_array( $value ) && ! empty( $value ) ) {
			$value = implode( ', ', $value );
		}
		return( $value );
	}

	/*
     * Taxonomy specific functions
	 */
	public function set_source( $post_id ) {
		if ( taxonomy_exists( 'source' ) ) {
			$source_data = apply_filters( 'source_line_display_filter', 'get_source_line_display' );
			if ( ! empty( $source_data['name'] ) ) {
				$this->data_layer['page.content.source'] = $source_data['name'];
			}
		}
	}

	public function set_magazine_issue( $post_id ) {
		if ( taxonomy_exists( 'mag_issue_date' ) ) {
			$magazine_issue = wp_get_object_terms( $post_id, 'mag_issue_date', true );
			if ( ! empty( $magazine_issue[0]->slug ) ) {
				$this->data_layer['page.content.magazineIssue'] = $magazine_issue[0]->slug;
			}
		}
	}

	public function set_tmbi_brand() {
		if ( taxonomy_exists( 'tmbi_first_associated' ) ) {
			$this->data_layer['page.content.tmbiBrand'] = $this->get_brands_data();
		}
	}
	/*
     * Taxonomy specific functions END
	 */

	/*
	 * Get the brand data from different plugins.
	 *  - TMBI First Associated Taxonomy ( rd-tmbi-first-published )
	 *  - TMBI Brand Attribution Manager ( tmbi-brand-attribution )
	 * used get_first_published_brand filter for keep code in same location
	 */
	public function get_brands_data() {
		$post = get_post();
		if ( taxonomy_exists( 'brand' ) ) {
			$brand_names = wp_get_object_terms( $post->ID, 'brand' );
			if ( ! empty( $brand_names[0]->slug ) ) {
				return ( $brand_names[0]->slug );
			}
		} elseif ( $brand_slug_array = apply_filters( 'get_first_published_brand', 'get_brand_data' ) ) {
			return ( $brand_slug_array['term_slug'] );
		}
		 return ( 'no brand' );
	}

	public function get_author_name( $author_id ) {
		$author = get_userdata( $author_id );
		if ( $author ) {
			$author_name = $author->display_name;
			return ( $author_name );
		}
		return ( '' );
	}

	public function get_content_cost( $post_id ) {
		$content_cost = get_post_meta( $post_id, 'cost-meta', true );
		return( $content_cost );
	}

	private function get_author_roles( $user_id ) {
		$author_role = '';
		$user = get_userdata( $user_id );
		if ( ! empty( $user->roles ) ) {
			$author_role = implode( ', ', $user->roles );
		}
		return( $author_role );
	}

	/*
     * Newsletter campaign variables
	 */
	public function set_newsletter_campaign() {
		if ( isset( $_GET['_cmp'] ) && isset( $_GET['_ebid'] ) ) {
			$this->data_layer['newsletter.emailcampaign'] = sanitize_text_field( $_GET['_cmp'] );
			$this->data_layer['newsletter.emailblastid']  = sanitize_text_field( $_GET['_ebid'] );
		}
	}

	/*
     * Functions borrowed from Adobe DTM for Wordpress Plugin
     * get data array with dot notation and return as array
	 */
	public static function parse_data_layer_config( $config ) {

		if ( is_array( $config ) && count( $config ) > 0 ) {
			$dataLayer = array();

			foreach ( $config as $key => $value ) {
				if ( isset( $dataLayer[$key] ) ) {
					if ( is_array( $dataLayer[$key] ) && count( $dataLayer[$key] ) === count( $dataLayer[$key], COUNT_RECURSIVE ) ) {
						$dataLayer[$key][] = $value;
					} else {
						$dataLayer[$key] = $value;
					}
				} else {
					$dataLayer = array_merge_recursive( $dataLayer, self::create_element( $key, $value ) );
				}
			}
		} else {
			$dataLayer = $config;
		}
		return( $dataLayer );
	}

	// recursive function to construct an object from dot-notation
	public static function create_element( $key, $value ) {
		$element = array();
		$key = ( string ) $key;
		// if the key is a property
		if ( strpos( $key, '.' ) !== false ) {
			/**
			 * extract the first part with the name of the object, however
			 * explode() will return FALSE we should be careful to check this
			 */
			$list = explode( '.', $key );
			// the rest of the key
			$sub_key = substr_replace( $key, '', 0, strlen( $list[0] ) + 1 );
			// create the object if it doesnt exist
			if ( $list !== false && ! array_key_exists( $list[0], $element ) ) {
				$element[$list[0]] = array();
			}
			// if the key is not empty, create it in the object
			if ( $sub_key !== '' && $list !== false ) {
				$element[$list[0]] = self::create_element( $sub_key, $value );
			}
		}
		// just normal key
		else {
			$element[$key] = $value;
		}
		return( $element );
	}

	/*
	 * Get co-author names and roles when Co-Author Plugin enabled and set
	 *
	 * @param int post_id
	 *
	 * @return true/false set_author_guest
	 */
	private function set_author_guest( $post_id ) {
		if ( class_exists( 'CoAuthorsIterator' ) ) {
			$author_data = new CoAuthorsIterator( $post_id );
			if ( $author_data->count() > 0 ) {
				$author_data->iterate();
				$author_names = '';
				$author_roles = '';
				do {
					$author_names .= $author_data->current_author->display_name . ',';
					if ( $author_data->current_author->type == 'guest-author' ) {
						$author_roles .= $this->get_guest_roles( $author_data->current_author->ID );
					} elseif ( $author_data->current_author->roles ) {
						$author_roles .= implode( ', ', $author_data->current_author->roles ) . ',';
					}
				} while ( $author_data->iterate() );

				$this->data_layer['page.content.author']     = rtrim( $author_names, ',' );
				$this->data_layer['page.content.authorRole'] = rtrim( $author_roles, ',' );
				return ( true );
			}
		}
		return ( false );
	}

	private function get_guest_roles( $user_id ) {
		$coauthor_relation_id = get_post_meta( $user_id, 'cap-author_relation', true );
		$guest_relation_arr   = get_term_by( 'id', $coauthor_relation_id, 'guest_author_roles' );

		if ( ! empty( $guest_relation_arr->name ) ) {
			return trim( $guest_relation_arr->name ) . ',';
		}else {
			return ( 'Guest Author,' );
		}
	}

	public function fhm_get_class_name( $post_id ) {
		if ( ! is_single() ) {
			return '';
		}
		$class = '';
		$primary_class = yoast_get_primary_term( 'class', $post_id );
		if ( empty( $primary_class ) ) {
			$classes = wp_get_post_terms( $post_id, 'class' );
			if ( $classes && count( $classes ) > 0 ) {
				$primary_class = $classes[0]->name;
			}
		}
		if ( ! empty( $primary_class ) ) {
			$class = $primary_class;
		}
		return $class;
	}

}

$adobe_dtm_helper = new RD_Adobe_DTM();
