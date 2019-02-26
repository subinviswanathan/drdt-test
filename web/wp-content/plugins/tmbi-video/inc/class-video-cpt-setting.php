<?php

/**
 * Class Video_CPT_Setting
 *
 * Helper functions for Video CPT
 */
class Video_CPT_Setting {

	/**
	 * Video_CPT_Setting constructor.
	 */
	public function __construct() {
		add_action( 'acf/init', array( $this, 'register_acf_video_fields' ) );
		add_action( 'manage_video_posts_columns', array( $this, 'add_video_shortcode_column' ) );
		add_action( 'manage_video_posts_custom_column', array( $this, 'render_video_columns' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_validate_excerpt_script' ) );
	}

	/**
	 * Adding Shortcode column to Video CPT
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public function add_video_shortcode_column( $columns ) {
		$video_column = array(
			'shortcode' => 'Shortcode',
		);

		return array_merge( $columns, $video_column );
	}

	/**
	 * Render video shortcode in column
	 *
	 * @param $column_name
	 * @param $post_id
	 */
	public function render_video_columns( $column_name, $post_id ) {
		if ( $column_name === 'shortcode' ) {
			global $post;
			echo '<pre>[rd-video id="' . $post->ID . '"]</pre>';
		}
	}

	/**
	 * Register ACF fields for Video
	 */
	public function register_acf_video_fields() {
		acf_add_local_field_group(
			array(
				'key'      => 'group_rd_video_dam',
				'title'    => 'DAM',
				'fields'   => array(
					array(
						'key'   => 'field_rd_video_dam_media_url',
						'label' => 'Media URL',
						'name'  => 'media_url',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_rd_video_dam_mime_type',
						'label' => 'MIME Type',
						'name'  => 'mime_type',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_rd_video_dam_duration',
						'label' => 'Duration (s)',
						'name'  => 'duration',
						'type'  => 'number',
					),
					array(
						'key'   => 'field_rd_video_dam_copyright',
						'label' => 'Copyright',
						'name'  => 'copyright',
						'type'  => 'text',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'video',
						),
					),
				),
				'options'  => array(
					'position' => 'acf_after_title',
				),
			)
		);
	}

	/**
	 * enqueue video excerpt field validation script
	 */
	public function enqueue_validate_excerpt_script() {
		global $post;

		/**
		 * Only add script to video admin area. Calling must included class Video_CPT.
		 */
		if ( isset( $post ) && $post->post_type === Video_CPT::LW_POST_TYPE ) {
			wp_register_script(
				'video-admin-js',
				plugins_url( '/js/excerpt-validation.js', dirname( __FILE__ ) ),
				array( 'jquery' ),
				VIDEO_PLUGIN_VER,
				true
			);

			wp_enqueue_script( 'video-admin-js' );
		}
	}
}
