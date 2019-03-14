<?php
/**
 * Created by PhpStorm.
 * User: jeysaravana
 * Date: 2017-03-24
 * Time: 9:04 AM
 */

/**
 * Class Video_Secondary_Player
 *
 * Settings for Secondary player
 *
 * Secondary player adds video Shortcodes for specific category or default video
 * On single page, it renders Video PLayer for post's category
 */
class Video_Secondary_Player {
	/**
	 * Option Slug for secondary settings
	 */
	const OPTION_SLUG = 'secondary_player_settings';

	/**
	 * Video_Secondary_Player constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'secondary_player_menu' ) );

		/**
		 * Adds secondary video player for contents
		 */
		add_action( 'render_secondary_player', array( $this, 'render_secondary_player' ) );
	}

	/**
	 * add submenu under video post type
	 */
	public function secondary_player_menu() {
		add_submenu_page(
			'edit.php?post_type=video',
			'Secondary Player Settings',
			'Secondary Player',
			'manage_options',
			'secondary_player',
			array( $this, 'secondary_player_callback' )
		);
	}

	/**
	 * Callback function for secondary player setting submenu in CMS
	 */
	public function secondary_player_callback() {
		if ( wp_verify_nonce( $_POST['secondary_player_noncename'], plugin_basename( __FILE__ ) ) ) {
			//Todo Capture post type and store in options
			$this->update_secondary_player_settings( $_POST['item'] );
		}
		$all_secondary_players    = $this->get_secondary_players();
		$default_secondary_player = $all_secondary_players['default'];
		?>
		<style>
			.delete_button {
				color: #a00;
				cursor: pointer;
			}
			.video_item_control input, .video_item_control select {
				height: 28px;
				padding: 2px;
				width: 100%;
			}
		</style>
		<script>
			jQuery(document).ready(function($) {
				//appending input to table
				$('#add-video').click(function(e){
					var $video_id = $('#video_id');
					var $category_dropdown = $('#category_dropdown option:selected');
					var $category_name = $category_dropdown.text();
					var $category_id = $category_dropdown.val();
					var $finding_category = $( '#view_item_list' ).find('div#'+$category_id);
					var $video_shortcode = $video_id.val();
					var $video_item_html = '<tr id="'+$category_id+'">' +
						'<td class="sec_category"><label>'+$category_name+'</label><input type="hidden" name="item['+$category_id+'][category]" value="'+$category_id+'"/></td>' +
						'<td class="sec_video"><label>'+$video_shortcode+'</label><input type="hidden" name="item['+$category_id+'][video]" value='+ "'" + $video_shortcode + "'" + '"/></td>' +
						'<td class="sec_control"><span class="delete_button">Delete</span></td>' +
						'</tr>';

					//only add if there is no category
					if($finding_category.length === 0) {
						$('#view_item_list tbody').append($video_item_html);
						$("#category_dropdown option[value=" + $category_id + "]").remove();

						// Reset the form
						$("#categeory_dropdown option:first-child").attr("selected", "selected");
						$video_id.val('');
					}
				});

				//deleting input from table
				$('.delete_button').live('click', function(){
					$tr = $(this).closest('tr');
					cat_name = $tr.find('.sec_category label').text();
					cat_id = $tr.find('.sec_category input').val();

					// TODO: Sort
					$("#category_dropdown").append('<option value="' + cat_id + '">' + cat_name + '</option>')
					$tr.remove();
				});

			});
		</script>
		<form method="post" action="edit.php?post_type=video&page=secondary_player&submit_form" class="wrap">
			<h1 class="wp-heading-inline">Secondary Player Settings</h1>
			<input id="secondary_player_noncename" type="hidden" name="secondary_player_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />

			<h2>Default secondary player</h2>
			<div class="form-field">
				<input type="hidden" name="item[default][category]" value="default">
				<textarea name="item[default][video]"><?php echo $default_secondary_player; ?></textarea>
				<p class="description"><?php esc_html_e( 'Markup for a video player, or [rd-video] shortcode', 'video-post-type' ); ?></p>
			</div>

			<?php if ( class_exists( 'RD_Jokes_Videos_Controller' ) ) : ?>
				<h2>Jokes videos</h2>
				<p>
					<a href="/wp-admin/edit.php?post_type=joke&page=rd-jokes-videos/admin.php">Go to Jokes > Videos</a> to configure the Jokes Homepage Player.
				</p>
				<p>
					<a href="/wp-admin/edit-tags.php?taxonomy=Joke-Topic&post_type=joke">Go to Jokes > Topics</a> and edit a topic to change the player for a specific Topic.
				</p>
			<?php endif; ?>

			<h2>Category videos</h2>
			<table class="wp-list-table widefat fixed striped" id="view_item_list">
				<thead>
					<tr>
						<th scope="col">Category</th>
						<th scope="col">Video Shortcode</th>
						<th scope="col">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php $this->render_category_videos(); ?>
				</tbody>
				<tfoot>
					<tr class="video_item_control">
						<td class="sec_category_title"><?php $this->get_category_dropdown(); ?></td>
						<td class="sec_video_title"><input type="text" id="video_id" name="video_id" value=""/></td>
						<td class="sec_control_title"><span class="button" id="add-video">Add Video</span></td>
					</tr>
				</tfoot>
			</table>

			<p class="submit">
				<input class="button button-primary button-large" type="submit" value="Save Settings">
			</p>
		</form>
		<?php
	}

	/**
	 * Render secondary player in the front end
	 *
	 * Renders Category specific secondary player
	 */
	public function render_secondary_player() {
		$category_id = 'default';

		if ( is_category() ) {
			$category    = get_category( get_query_var( 'cat' ) );
			$category_id = $category->cat_ID;
		}

		if ( is_single() ) {
			global $post;
			$categories       = get_the_category( $post->ID );
			$primary_category = '';
			if ( class_exists( 'WPSEO_Primary_Term' ) ) {
				$wpseo_primary_term = new WPSEO_Primary_Term( 'category', $post->ID );
				$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
				$primary_category   = get_term( $wpseo_primary_term );
			}
			if ( is_wp_error( $primary_category ) ) {
				$primary_category = $categories[0];
			}

			$category_id = $primary_category->term_id;
		}

		if ( ! $category_id ) {
			return;
		}

		$video_shortcode          = false;
		$secondary_player_options = get_option( self::OPTION_SLUG );

		// Try to use the current category, or the current post's category
		if ( ! $video_shortcode ) {
			if ( array_key_exists( $category_id, $secondary_player_options ) ) {
				$video_shortcode = $secondary_player_options[$category_id];
			}
		}

		// Traverse the category ancestor, and try to use the first one with a playr
		if ( ! $video_shortcode ) {
			$category_ancestors = get_ancestors( $category_id, 'category' );
			foreach ( $category_ancestors as $ancestor ) {
				if ( array_key_exists( $ancestor, $secondary_player_options ) ) {
					$video_shortcode = $secondary_player_options[$ancestor];
					break;
				}
			}
		}

		// Fall back to global player, if nothing else was found
		if ( ! $video_shortcode ) {
			$video_shortcode = $secondary_player_options['default'];
		}

		$embed_video = $video_shortcode;

		echo '<div class="secondary-player"><div class="secondary-player-container">' . do_shortcode( $embed_video ) . '</div></div>';
	}

	/**
	 * Get category for settings
	 *
	 * @return array
	 */
	private function get_secondary_players() {
		$settings = get_option( self::OPTION_SLUG );

		$response = array(
			'default'    => '',
			'categories' => array(),
		);

		if ( $settings['default'] ) {
			$response['default'] = stripslashes( $settings['default'] );
			unset( $settings['default'] );
		}

		if ( $settings ) {
			$response['categories'] = array_map( 'stripslashes', $settings );
		}
		return $response;
	}

	/**
	 * get secondary player list for categories
	 * @return mixed
	 */
	private function get_secondary_players_for_categories() {
		$all_secondary_players = $this->get_secondary_players();
		return $all_secondary_players['categories'];
	}

	/**
	 * render Category with video shortcode
	 */
	public function render_category_videos() {
		$category_videos = $this->get_secondary_players_for_categories();

		foreach ( $category_videos as $category_id => $video_shortcode ) {
			$category_name = get_cat_name( $category_id );

			if ( $category_id === 'default' ) {
				$category_name = $category_id;
			}

			echo '<tr id="' . $category_id . '">
					<td class="sec_category"><label>' . $category_name . '</label><input type="hidden" name="item[' . $category_id . '][category]" value="' . $category_id . '"/></td>
					<td class="sec_video"><label>' . $video_shortcode . '</label><input type="hidden" name="item[' . $category_id . '][video]" value=' . "'" . $video_shortcode . "'" . '"/></td>
					<td class="sec_control"><span class="delete_button" >Delete</span></td>
				</tr>';
		}
	}

	/**
	 * Update option table for secondary player
	 * @param $post_data
	 */
	public function update_secondary_player_settings( $post_data ) {
		$player_settings = array();

		foreach ( $post_data as $item ) {
			$video_category                   = sanitize_text_field( $item['category'] );
			$video_code                       = sanitize_text_field( $item['video'] );
			$player_settings[$video_category] = $video_code;
		}

		update_option( self::OPTION_SLUG, $player_settings );
	}

	/**
	 * Get categories
	 *
	 * @return array
	 */
	private function get_categories_without_player() {
		$categories                = get_categories();
		$categories_with_player    = $this->get_secondary_players_for_categories();
		$categories_without_player = [];
		foreach ( $categories as $category ) {
			if ( ! array_key_exists( $category->term_id, $categories_with_player ) ) {
				$categories_without_player[] = $category;
			}
		}
		return $categories_without_player;
	}

	/**
	 * Get dropdown html for setting
	 * @param bool $print
	 *
	 * @return bool|string
	 */
	public function get_category_dropdown( $print = true ) {
		$categories = $this->get_categories_without_player();
		$html       = '<select id="category_dropdown">' . PHP_EOL;
		foreach ( $categories as $category ) {
			$html .= '<option value="' . $category->term_id . '">' . $category->name . '</option>' . PHP_EOL;
		}
		$html .= '</select>' . PHP_EOL;
		if ( $print ) {
			echo $html;
			return true;
		}
		return $html;
	}
}
