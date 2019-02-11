<?php
/**
 * Created by PhpStorm.
 * User: miking
 * Date: 6/16/15
 * Time: 11:49 AM
 * Version: 1.3
 */

class WP_Base extends Base_Plugin {
	const SF_MSG_FMT = "<!-- %s I'm sorry but you don't seem to have a site. Please check the number and try again. -->\n";
	const PRIORITY   = 10;
	const RD_ID      = 2;
	const REM_ID     = 3;
	const CW_ID      = 4;
	const CM_ID      = 5;
	const FRL_ID     = 6;
	const BNB_ID     = 7;
	const BHU_ID     = 8;
	const BCP_ID     = 9;
	const CPT_ID     = 9;
	const FHM_ID     = 10;
	const TOH_ID     = 11;
	const TMB_ID     = 12;
	const RDC_ID     = 14;
	const SRD_ID     = 15;
	const BHC_ID     = 16;

	public static $post_type = '';
	public static $single_wp_base = false;
	public $current_site_id;

	public function __construct() {
		self::set_tz();
		self::set_post_type();

		/**
		 * Execute functions at one time
		 */
		if ( ! self::$single_wp_base ) {
			self::$single_wp_base = true;
			/*
			 * Forcing to use coauthor-plus plugin users instead of WP_Users
			 */
			//add_filter( 'coauthors_guest_authors_force', '__return_true' );
			add_filter( 'coauthors_default_author', array( $this, 'get_coauthor_if_available' ) );
		}
	}


	/**
	 * get coauthor if available. This would fix the issue when skyword push content with guest author where post_author
	 * @param $default_user
	 *
	 * @return mixed
	 */
	public function get_coauthor_if_available( $default_user ) {
		global $post;
		if ( ! (int) $post->post_author ) {
			$co_authors = get_coauthors();

			if ( $co_authors ) {
				$default_user = $co_authors[0];
			}
		}
		return $default_user;
	}

	/**
	 * Ultimately this construct needs o be refactored and simplified by
	 * utilizing the static $post_type var.
	 * @return bool
	 */
	public static function is_slideshow() {
		if ( get_post_type( get_the_ID() ) === 'slideshows' ) {
			return ( true );
		}
		return ( false );
	}

	public static function is_collection() {
		if ( get_post_type( get_the_ID() ) === 'collection' ) {
			return( true );
		}
		return( false );
	}

	/**
	 * This condition will be true for all Slideshow types
	 * using global query to determine the post-type
	 * @param WP_Post|null
	 *
	 * @return true|false
	 */
	public static function is_listicle( $post = null ) {
		$check_archive = is_archive();
		if ( $post && ( is_string( $post ) || $post instanceof WP_Post ) ) {
			$post_type = $post;
			if ( $post instanceof WP_Post ) {
				$post_type = $post->post_type;
			}
			$check_archive = false;
		} else {
			$post_type = get_post_type( get_the_ID() );
		}

		if ( ! ( $check_archive ) && ( $post_type === 'listicle' || $post_type === 'slicklist' || $post_type == 'collection' ) ) {
			return( true );
		}

		return ( false );
	}

	public static function is_tip() {
		$post_type = get_post_type( get_the_ID() );
		if ( $post_type === 'tip' ) {
			return( true );
		}
		return( false );
	}

	public static function is_marquee() {
		$post_type = get_post_type( get_the_ID() );
		if ( $post_type === 'marquee' ) {
			return( true );
		}
		return( false );
	}

	public static function is_slickist() {
		$post_type = get_post_type( get_the_ID() );
		if ( $post_type === 'slicklist' ) {
			return ( true );
		}

		return ( false );
	}

	public static function is_quiz() {
		if ( get_post_type( get_the_ID() ) === 'quiz' ) {
			return ( true );
		}

		return ( false );
	}

	public static function is_joke() {
		if ( get_post_type( get_the_ID() ) === 'joke' ) {
			return ( true );
		}

		return ( false );
	}

	public static function is_video() {
		if ( get_post_type( get_the_ID() ) === 'video' ) {
			return ( true );
		}

		return ( false );
	}

	public static function is_project() {
		if ( get_post_type( get_the_ID() ) === 'project' ) {
			return( true );
		}
		return( false );
	}

	/**
	 * Check condition for is Legacy Project
	 * @return True|False
	 */
	public static function is_legacy_project() {
		//TODO get post-meta from project post for legacy project condition
		if ( get_post_type( get_the_ID() ) === 'project' && is_page_template( 'template-legacy-project.php' ) ) {
			return true;
		}
		return false;
	}

	public static function is_post() {
		if ( get_post_type( get_the_ID() ) === 'post' ) {
			return ( true );
		}

		return ( false );
	}

	public static function is_nicestplace() {
		if ( get_post_type( get_the_ID() ) === 'nicestplace' ) {
			return( true );
		}
		return( false );
	}

	public static function is_recipe() {
		if ( get_post_type( get_the_ID() ) === 'recipe' ) {
			return( true );
		}
		return( false );
	}

	public static function is_video_recipe() {
		$pid = get_the_ID();
		if ( get_post_type( $pid ) === 'recipe' ) {
			$recipe_data = new RD_Toh\Resource\RecipeResource();
			$video_code = $recipe_data->get_recipe_video();
			if ( $video_code ) {
				return true;
			}
		}
		return( false );
	}

	public static function is_cms_user() {
		if ( is_array( $_COOKIE ) && ! empty( $_COOKIE ) ) {
			foreach ( array_keys( $_COOKIE ) as $cookie ) {
				if ( $cookie != 'wordpress_test_cookie' &&
					( substr( $cookie, 0, 2 ) == 'wp' ||
						substr( $cookie, 0, 9 ) == 'wordpress' ||
						substr( $cookie, 0, 14 ) == 'comment_author' ) ) {
							return(true);
				}
			}
		}
	}

	public function get_url_to_dir( $asset_path ) {
		return ( plugins_url( $asset_path, __DIR__ ) );
	}

	public function get_url_to_file( $asset_path ) {
		return ( plugins_url( $asset_path, __FILE__ ) );
	}

	/**
	 * check queried slideshow/listicle/collection is a paginated version.
	 * @return bool
	 */
	public static function is_paginated() {
		global $wp_query;

		if ( ( static::is_rd() || static::is_cpt() || static::is_canadian_site() ) && get_query_var( 'page' ) > 0 ) {
			return ( true );
		} elseif ( ( static::is_toh() || static::is_fhm() ) && $wp_query->is_main_query() && ! isset( $wp_query->query['view-all'] ) ) {
			return ( true );
		}

		return ( false );
	}

	/**
	 * @return bool
	 */
	public static function is_card() {
		if ( self::is_listicle() && self::is_paginated() ) {
			return ( true );
		}

		return ( false );
	}

	/**
	 * Check if site has google amp endpoint
	 * @return bool
	 */
	public static function is_amp() {
		if ( function_exists( 'is_amp_endpoint' ) ) {
			return is_amp_endpoint();
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public static function is_slide() {
		if ( self::is_slideshow() && self::is_paginated() ) {
			return ( true );
		}

		return ( false );
	}

	public static function set_post_type() {
		global $post;
		if ( ! isset( static::$post_type ) ) {
			static::$post_type = get_post_type( get_the_ID() );
			if ( empty( static::$post_type ) ) {
				static::$post_type = get_post( $post->ID )->post_type;
			} elseif ( static::$post_type === false ) {
				static::$post_type = 'false-condition';
			}
		}
	}

	public static function reset_post_type() {
		static::$post_type = get_post_type( get_the_ID() );
	}


	/**
	 * Refer to the rd-gpt plugin for similar
	 * @see https://codex.wordpress.org/Function_Reference/is_archive
	 *
	 * @return string
	 */

	public static function get_page_type() {
		$page_type = 'default';
		self::set_post_type();

		if ( is_home() || is_front_page() ) {
			$page_type = 'homepage';
		} elseif ( is_archive() ) {
			if ( is_category() ) {
				$page_type = 'category';
			} elseif ( is_tag() ) {
				$page_type = 'tag';
			} else {
				$page_type = 'archive';
			}
		} elseif ( is_search() ) {
			$page_type = 'search';
		} elseif ( is_page() ) {
			// this will require some additional coding
			$page_type = 'page';
		} elseif ( is_404() ) {
			$page_type = '404page';
		} elseif ( self::is_post() ) {
			$page_type = 'article';
		} elseif ( self::is_collection() ) {
			if ( get_query_var( 'view-all' ) ) {
				$page_type = 'listicle';
			} else {
				$page_type = 'card';
			}
		} elseif ( self::is_listicle() ) {
			$page_type = 'listicle';
			if ( self::is_card() ) {
				$page_type = 'card';
			}
		} elseif ( self::is_slideshow() ) {
			$page_type = 'slideshow';
			if ( self::is_slide() ) {
				$page_type = 'slide';
			}
		} elseif ( self::is_joke() ) {
			$page_type = 'joke';
		} elseif ( self::is_video() ) {
			$page_type = 'video';
		} elseif ( self::is_quiz() ) {
			$page_type = 'quiz';
		} elseif ( self::is_recipe() ) {
			$page_type = 'recipe';
			if ( self::is_video_recipe() ) {
				$page_type = 'videorecipe';
			}
		} elseif ( self::is_project() ) {
			$page_type = 'projectdetail';
		}

		//print( '<!-- Page type = ' . $page_type . ' -->' . PHP_EOL );
		return ( $page_type );
	}

	public static function is_bcp() {
		$site_urls = array(
			'origin-www.buildconstructpros.com',
			'www.buildconstructpros.com',
			'buildconstructpros.com',
		);

		if ( is_multisite() && get_current_blog_id() === self::BCP_ID ) {
			return ( true );
		} else {
			return ( self::site_finder( $site_urls ) );
		}

	}

	public static function is_cpt() {
		$site_urls = array(
			'origin-www.constructionprotips.com',
			'www.constructionprotips.com',
			'constructionprotips.com',
		);

		if ( is_multisite() && get_current_blog_id() === self::CPT_ID ) {
			return ( true );
		} else {
			return ( self::site_finder( $site_urls ) );
		}
	}

	public static function is_fhm() {
		$site_urls = array(
			'origin-www.familyhandyman.com',
			'www.familyhandyman.com',
			'familyhandyman.com',
		);

		if ( is_multisite() && get_current_blog_id() === self::FHM_ID ) {
			return ( true );
		} else {
			return ( self::site_finder( $site_urls ) );
		}

	}

	public static function is_toh() {
		$site_urls = array(
			'origin-www.tasteofhome.com',
			'www.tasteofhome.com',
			'tasteofhome.com',
		);

		if ( is_multisite() && get_current_blog_id() === self::TOH_ID ) {
			return ( true );
		} else {
			return ( self::site_finder( $site_urls ) );
		}
	}

	/**
	 * @return bool
	 */
	public static function is_rd() {
		$site_urls = array(
			'origin-www.rd.com',
			'www.rd.com',
			'rd.com',
		);

		if ( self::is_valid_env() ) {
			return ( true );
		} elseif ( is_multisite() && get_current_blog_id() === self::RD_ID ) {
			return ( true );
		} else {
			return ( self::site_finder( $site_urls ) );
		}
	}


	/**
	 * @return bool
	 */
	public static function is_rdc() {
		$site_urls = array(
			'origin-www.readersdigest.ca',
			'readersdigest.ca',
			'www.readersdigest.ca',
			'www.rd.ca',
			'rd.ca',
		);

		if ( is_multisite() && get_current_blog_id() === self::RDC_ID ) {
			return ( true );
		} else {
			return ( self::site_finder( $site_urls ) );
		}
	}

	/**
	 * @return bool
	 */
	public static function is_srd() {
		$site_urls = array(
			'selection.readersdigest.ca',
		);

		if ( is_multisite() && get_current_blog_id() === self::SRD_ID ) {
			return ( true );
		} else {
			return ( self::site_finder( $site_urls ) );
		}
	}

	/**
	 * @return bool
	 */
	public static function is_bhc() {
		$site_urls = array(
			'besthealthmag.ca',
			'www.besthealthmag.ca',
		);

		if ( is_multisite() && get_current_blog_id() === self::BHC_ID ) {
			return ( true );
		} else {
			return ( self::site_finder( $site_urls ) );
		}
	}

	/**
	 * @return bool
	 */
	public static function is_rem() {
		$site_urls = array(
			'origin-www.reminisce.com',
			'www.reminisce.com',
			'reminisce.com',
			'rem.rda.net',
			'rem.rda.local',
		);

		if ( is_multisite() && get_current_blog_id() === self::REM_ID ) {
			return ( true );
		} else {
			return ( self::site_finder( $site_urls ) );
		}
	}

	/**
	 * @return bool
	 */
	public static function is_cw() {
		$site_urls = array(
			'origin-www.countrywomanmagazine.com',
			'www.countrywomanmagazine.com',
			'countrywomanmagazine.com',
			'cw.rda.net',
			'cw.rda.local',
		);

		if ( is_multisite() && get_current_blog_id() === self::CW_ID ) {
			return ( true );
		} else {
			return ( self::site_finder( $site_urls ) );
		}
	}

	/**
	 * @return bool
	 */
	public static function is_cm() {
		$site_urls = array(
			'origin-www.country-magazine.com',
			'www.country-magazine.com',
			'country-magazine.com',
			'cm.rda.net',
			'cm.rda.local',
		);

		if ( is_multisite() && get_current_blog_id() === self::CM_ID ) {
			return ( true );
		} else {
			return ( self::site_finder( $site_urls ) );
		}
	}

	/**
	 * @return bool
	 */
	public static function is_frl() {
		$site_urls = array(
			'origin-www.farmandranchliving.com',
			'www.farmandranchliving.com',
			'farmandranchliving.com',
			'frl.rda.net',
			'frl.rda.local',
		);

		if ( is_multisite() && get_current_blog_id() === self::FRL_ID ) {
			return ( true );
		} else {
			return ( self::site_finder( $site_urls ) );
		}
	}

	/**
	 * get if current site is Canadian site
	 * @return True|False
	 */
	public static function is_canadian_site() {
		return ( static::is_rdc() || static::is_srd() || static::is_bhc() );
	}

	/**
	 * @return bool
	 */
	public static function is_valid_env() {
		global $scf;
		if ( isset( $scf ) &&
			$scf instanceof ServerConfig &&
			$scf->server_cfg->get_site_id() === self::RD_ID ) {
			return ( true );
		}
		return( false );
	}

	/**
	 * @param $site_urls
	 * @return bool
	 */
	public static function site_finder( $site_urls ) {
		$host = $_SERVER['HTTP_HOST'];
		foreach ( $site_urls as $url ) {
			if ( stripos( $host, $url ) !== false ) {
				return ( true );
			}
		}

		return ( false );
	}

	/**
	 * I am not certain this is ever use anymore. Need to confirm and
	 * redact if not. According to PHPStorm it's NEVER USED
	 *
	 * @return int
	 */
	public function set_site_id_by_url() {
		if ( is_multisite() ) {
			$this->current_site_id = get_current_blog_id();
		} else {
			if ( $this->is_rd() ) {
				$this->current_site_id = self::RD_ID;
			} elseif ( $this->is_fhm() ) {
				$this->current_site_id = self::FHM_ID;
			} elseif ( $this->is_bcp() || $this->is_cpt() ) {
				$this->current_site_id = self::BCP_ID;
			} elseif ( $this->is_toh() ) {
				$this->current_site_id = self::TOH_ID;
			} elseif ( $this->is_rdc() ) {
				$this->current_site_id = self::RDC_ID;
			} elseif ( $this->is_srd() ) {
				$this->current_site_id = self::SRD_ID;
			} elseif ( $this->is_bhc() ) {
				$this->current_site_id = self::BHC_ID;
			}
		}
		return( $this->current_site_id );
	}

	public static function get_site_id_by_url() {
		if ( is_multisite() ) {
			return get_current_blog_id();
		} else {
			if ( self::is_rd() ) {
				return self::RD_ID;
			} elseif ( self::is_fhm() ) {
				return self::FHM_ID;
			} elseif ( self::is_bcp() || self::is_cpt() ) {
				return self::BCP_ID;
			} elseif ( self::is_toh() ) {
				return self::TOH_ID;
			} elseif ( self::is_rdc() ) {
				return self::RDC_ID;
			} elseif ( self::is_srd() ) {
				return self::SRD_ID;
			} elseif ( self::is_bhc() ) {
				return self::BHC_ID;
			}
		}
		return false;
	}


	/**
	 * @param $path
	 */
	public function require_site_asset( $path ) {
		if ( isset( $path ) ) {
			try {
				require( $path );
			} catch ( WP_Exception $e ) {
				// I haven't determined how to fail it.
				// add the return to skip the empty catch violation.
				return;
			}
		}
	}

	/**
	 * Returns the appropriate Ad targeting prefix based upon the environment
	 */
	public function get_ad_target_prefix() {
		global $scf;
		if ( ! isset( $scf ) ) {
			return( 'test.');
		} else {
			if ( ! empty( $scf->server_cfg->AD_TARGET_PREFIX ) ) {
				return ( $scf->server_cfg->AD_TARGET_PREFIX );
			}
		}
		return '';
	}

	/**
	 *
	 */
	public function print_undefined_site_message() {
		printf( self::SF_MSG_FMT, get_class( $this ) );
	}

	/**
	 * Check for TMBI theme V3 enable
	 */
	public static function is_tmbi_theme_v3() {
		$cur_theme      = wp_get_theme();
		$cur_theme_name = $cur_theme->get( 'Name' );
		if ( $cur_theme_name === 'TMB v3 Child Theme' || $cur_theme_name === 'TMBI v3 Child Theme' ) {
			return true;
		} else {
			return false;
		}
	}

}
