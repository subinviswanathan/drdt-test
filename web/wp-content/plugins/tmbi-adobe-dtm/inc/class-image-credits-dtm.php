<?php
/**
 * Class Image_Credits_DTM
 */
class Image_Credits_DTM {
	/**
	 * Store supported post-types
	 *
	 * @var array $supported_types
	 */
	public static $supported_types = array( 'listicle', 'slicklist', 'collection' );

	public function __construct() {
		add_action( 'the_content', array( $this, 'add_image_credit_dtm' ), 20 );
	}

	/*
	 * insert analytics attributed to images in the content using the_content hook
	 * @param $content string WP_Content
	 * @return $content string WP_Content
	 */
	public function add_image_credit_dtm( $content ) {
		global $post;
		if ( isset( $post ) && in_array( $post->post_type, self::$supported_types ) ) {
			$image_pattern = '/<(img.*wp-image-(\d+)*\b[^>]*)[\/|>]/';
			if ( preg_match_all( $image_pattern, $content, $matches ) ) {
				foreach ( $matches[1] as $key => $match ) {
					if ( ! strpos( 'data-image-analytics', $match ) ) {
						$data_analytics = self::get_image_credits_json( $matches[2][$key] );
						$new_data = trim( $match, '/ ' ) . ' ' . $data_analytics . ' /';
						$content  = str_replace( $match, $new_data, $content );
					}
				}
			}
		}
		return $content;
	}



	/*
	 * Get image licensor name and credits in JSON
	 *
	 * @param $post_id int|null
	 * @return $json_string string
	 */
	public static function get_image_credits_json( $thumb_id = null ) {
		$default_text = 'not available';
		if ( ! $thumb_id ) {
			global $post;
			$thumb_id = get_post_thumbnail_id( $post->ID );
		}

		$img_credits_array = array(
			'licensorName' => ( $name = get_post_meta( $thumb_id, '_image_licensor_name', true ) ) ? $name : $default_text,
			'credits' => ( $credits = get_post_meta( $thumb_id, 'photographer_credit_name', true ) ) ? $credits : $default_text,
		);
		$json_string = 'data-image-analytics=\'' . json_encode( $img_credits_array ) . '\'';
		return $json_string;
	}

	/*
	 * Image licensor name for specific post featured image
	 * These data are comes from MSN feed plugin and saved in meta
	 *
	 * @param int $post_id current post id
	 * @return string licensor name
	 */
	public static function get_image_licensor_name( $post_id = null ) {
		$licensor_name = 'not available';
		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}

		if ( $image_id = get_post_thumbnail_id( $post_id ) ) {
			if ( $lic_name = get_post_meta( $image_id, '_image_licensor_name', true ) ) {
				$licensor_name = $lic_name;
			}
		}

		return $licensor_name;
	}

	/*
	 * Image credits for specific post featured image
	 * These data are comes from Better Image Credits plugin and saved in meta
	 *
	 * @param int $post_id current post id
	 * @return string $credit_name
	 */
	public static function get_image_credits( $post_id = null ) {
		$credits_name = 'not available';
		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}

		if ( $image_id = get_post_thumbnail_id( $post_id ) ) {
			if ( $cred_name = get_post_meta( $image_id, 'photographer_credit_name', true ) ) {
				$credits_name = $cred_name;
			}
		}

		return $credits_name;
	}
}
