<?php
/*
 * User: jeysaravana
 * Date: 2019-02-21
 * Time: 4:54 PM
 */

class Video_Shortcode {

	public function __construct() {
		add_shortcode( 'rd-video', array( $this, 'video_shortcode' ) );
	}

	/**
	 * Processing rd-video shortcode
	 *
	 * if it is feed, data will be return in xml format
	 *
	 * @param $atts
	 * @param null $content
	 *
	 * @return string
	 */
	public function video_shortcode( $atts, $content = null ) {
		$options = array(
			'video_id'  => null,
			'media_url' => null,
			'mime_type' => null,
			'duration'  => null,
			'copyright' => null,
		);

		$attributes = array(
			'content' => null,
		);

		if ( $content !== null ) {
			$attributes['content'] = $content;
		}

		$options = apply_filters( 'tmbi_video_shortcode_default_attributes', $options );

		$video_post = null;
		$video_id   = null;
		if ( ! empty( $atts['id'] ) && $atts['id'] ) {
			$video_id   = preg_replace( '/[^0-9]/', '', $atts['id'] );
			$video_post = get_post( $video_id );

			$options               = array_merge( $options, get_fields( $video_id ) );
			$options['video_id']   = $video_id;
			$options['video_post'] = $video_post;
		}

		$options['video_post_title']   = '';
		$options['video_post_content'] = '';
		if ( $video_post && is_object( $video_post ) ) {
			$options['video_post_title']   = $video_post->post_title;
			$options['video_post_content'] = $video_post->post_content;
		}
		$options = array_merge( $options, $atts );

		// $is_amp = ( function_exists( 'is_amp_enpoint' ) && is_amp_endpoint() );

		$widget_content = '';

		if ( is_feed() ) {
			$widget_content .= $this->render_dam_source( $options );
		} else {
			$widget_content .= apply_filters( 'tmbi_video_shortcode', '', $options );
		}

		$widget_content .= $attributes['content'];
		return $widget_content;
	}

	/**
	 * Used for MSN feed: XML markup with URL and metadata from the DAM
	 *
	 * @param $options
	 *
	 * @return string
	 */
	private function render_dam_source( $options ) {
		$video_id  = $options['video_id'];
		$video_url = $options['media_url'];

		$video_mime_type = ( empty( $options['mime_type'] ) ? '' : $options['mime_type'] );
		$video_duration  = ( empty( $options['duration'] ) ? '' : $options['duration'] );
		$video_copyright = ( empty( $options['copyright'] ) ? '' : $options['copyright'] );

		$thumbnail_url = '';
		if ( $video_id ) {
			$thumbnail_id        = get_post_thumbnail_id( $video_id );
			$thumbnail_mime_type = get_post_mime_type( $thumbnail_id );
			$thumbnail_url       = get_the_post_thumbnail_url( $video_id );
		}

		$thumbnail_url     = ( ! empty( $options['featured_image'] ) ? $options['featured_image'] : $thumbnail_url );
		$video_title       = ( ! empty( $options['title'] ) ? $options['title'] : $options['video_post_title'] );
		$video_description = ( ! empty( $options['description'] ) ? $options['description'] : $options['video_post_content'] );

		$video_mime_type_attr = ( ! empty( $video_mime_type ) ? ' type="' . $video_mime_type . '"' : '' );
		$video_duration_attr  = ( ! empty( $video_duration ) ? ' duration="' . $video_duration . '"' : '' );


		$video_markup = '<media:content isDefault="false" url="' . $video_url . '"' . $video_mime_type_attr . ' id="' . $video_id . '"' . $video_duration_attr . '>';

		if ( ! empty( $thumbnail_url ) ) {
			$video_markup .= '<media:thumbnail url="' . $thumbnail_url . '" type="' . $thumbnail_mime_type . '" />';
		}

		if ( ! empty( $video_copyright ) ) {
			$video_markup .= '<media:copyright>' . $video_copyright . '</media:copyright>';
		}

		$video_markup .= '<media:title type="plain">' . $video_title . '</media:title>';
		$video_markup .= '<media:description type="plain">' . $video_description . '</media:description>';

		$video_markup .= '</media:content>';

		return $video_markup;
	}
}
