<?php
/**
 * Legacy functionality for the Marquee plugin.
 *
 * @package marquee
 */

/*
add_filter( 'parse_query', array( $this, 'featured_filter' ) );
add_action( 'restrict_manage_posts', array( $this, 'featured_filter_view' ) );
add_action( 'init', array( $this, 'add_marquee_override_support' ) );
*/

/**
 * Set _marquee_featured meta query when `marquee_filter_featured` GET parameter exists.
 *
 * @filter parse_query
 */
function featured_filter( $query ) {
	global $pagenow;

	if ( is_admin() && $pagenow == 'edit.php' && ! empty( $_GET['marquee_filter_featured'] ) ) {
		$query->query_vars['meta_key'] = self::FEATURED_META;
		$query->query_vars['meta_value'] = 'Yes';
	}
}


/**
 * Add post list filter for featured view
 *
 * @filter restrict_manage_posts
 */
function featured_filter_view() {
	if ( get_query_var( 'post_type' ) != $this->post_type() ) {
		return;
	} ?>

	<label>
	<input type="checkbox" name="marquee_filter_featured" value="1" <?php checked( get_query_var( 'meta_key' ), self::FEATURED_META ); ?> />
		Show only featured items.
	</label>
<?php
}


/**
 * Add columns to WP_List_Table display for this post type
 */
function columns( $columns ) {
	$columns['title'] = 'Title';
	$columns['featured'] = 'Featured';
	$columns['description'] = 'Description';
	$columns['thumbnail'] = 'Marquee Image';

	return $columns;
}

/**
 * Render custom columns
 */
function render_column( $column, $id ) {
	switch ( $column ) {
		case 'featured':
			$featured = get_post_meta( $id, self::FEATURED_META, true );
			if ( 'Yes' === $featured ) {
				echo '&#x25cf;'; // Big black bullet.
			}
		break;
		case 'description':
			$post = get_post( $id );
			echo wp_kses_post( $post->post_content );
		break;
		case 'thumbnail':
			the_post_thumbnail( 'thumbnail', array( 'style' => 'max-width: 100%; height: auto' ) );
		break;
	}
}

/*
 * Adds top level taxonomy/cpt for marquee post type
 */
public function add_marquee_override_support() {

	//Process only for TOH Site
	//TODO : Update for other sites also if requested later

	if ( ! WP_Base::is_toh() ) {
		return '';
	}

	add_action( 'add_meta_boxes', array( $this, 'add_top_level_taxonomy_metabox' ), 11 ); //Adds metabox for admin marquee page
	add_action( 'add_meta_boxes', array( $this, 'add_top_level_cpt_metabox' ), 12 );
	add_action( 'save_post', array( $this, 'save_taxonomy_values' ) ); // save updated values to the database

}

/*
* Adds custom taxonomy metabox
*/
public function add_top_level_taxonomy_metabox() {
	add_meta_box(
		'top_level_taxonomies',
		__( 'Select Top Level Taxonomies' ),
		array( $this, 'top_level_taxonomies_handler' ),
		array( 'marquee' ),
		'side',
		'high'
	);
}

/*
* Adds custom cpt metabox
*/
public function add_top_level_cpt_metabox() {
	add_meta_box(
		'top_level_cpt',
		__( 'Select Top Level CPT' ),
		array( $this, 'top_level_cpt_handler' ),
		array( 'marquee' ),
		'side',
		'high'
	);
}

/*
* Populate the taxonomies to the metabox
*/
public function top_level_taxonomies_handler() {

	global $post;
	$taxonomies = $this->get_top_level_taxonomies();

	if ( $taxonomies != '' ) {
		$top_level_taxonomies = array();
		foreach ( $taxonomies as $tax ) {
			if ( $tax->name != 'exclude_feed' ) {
				$top_level_taxonomies[$tax->query_var] = $tax->label;
			}
		}
		wp_nonce_field( basename( __FILE__ ), 'marquee_override_nonce' );
		$postmeta = maybe_unserialize( get_post_meta( $post->ID, '_parent_tax_marquee', true ) );
		foreach ( $top_level_taxonomies as $id => $element ) {
			if ( is_array( $postmeta ) && in_array( trim( $id ), $postmeta ) ) {
				$checked = 'checked="checked"';
			} else {
				$checked = null;
			}
			echo '<p><input type="checkbox" name="marquee_override_val[]" value="' . trim( $id ) . '"' . $checked . ' />';
			echo $element . '</p>';
		}
	}
}


/*
* Populate the cpts to the metabox
*/
public function top_level_cpt_handler() {

	global $post;
	$post_types = $this->get_top_level_post_types();

	if ( $post_types != '' ) {
		$top_level_cpts = array();
		foreach ( $post_types as $pt ) {
			if ( $pt->name === 'recipe' ) {
				$top_level_cpts[$pt->query_var] = $pt->label;
			}
		}
		wp_nonce_field( basename( __FILE__ ), 'marquee_override_nonce' );
		$postmeta = maybe_unserialize( get_post_meta( $post->ID, '_parent_tax_marquee', true ) );
		foreach ( $top_level_cpts as $id => $element ) {
			if ( is_array( $postmeta ) && in_array( trim( $id ), $postmeta ) ) {
				$checked = 'checked="checked"';
			} else {
				$checked = null;
			}
			echo '<p><input type="checkbox" name="marquee_override_val[]" value="' . trim( $id ) . '"' . $checked . ' />';
			echo $element . '</p>';
		}
	}
}

/*
 * Returns the list of top level taxonomies
 */
public function get_top_level_taxonomies() {
	$args = array(
		'public'   => true,
		'_builtin' => false,
	);
	$output = 'object'; // or objects
	$operator = 'and'; // 'and' or 'or'
	$taxonomies = get_taxonomies( $args, $output, $operator );
	if ( ! empty( $taxonomies ) ) {
		return $taxonomies;
	} else {
		return '';
	}
}

/*
* Returns the list of top level cpts
*/
public function get_top_level_post_types() {
	$args = array(
		'public'   => true,
		'_builtin' => false,
	);
	$output = 'object'; // or objects
	$operator = 'and'; // 'and' or 'or'
	$post_types = get_post_types( $args, $output, $operator );
	if ( ! empty( $post_types ) ) {
		return $post_types;
	} else {
		return '';
	}
}

/*
 * Save the meta values to the Database
 */
public function save_taxonomy_values( $post_id ) {

	$is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST['marquee_override_nonce'] ) && wp_verify_nonce( $_POST['marquee_override_nonce'], basename( __FILE__ ) ) ) ? 'true' : 'false';
	if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
		return;
	}

	// If the checkbox was not empty, save it as array in post meta
	if ( ! empty( $_POST['marquee_override_val'] ) ) {
		update_post_meta( $post_id, '_parent_tax_marquee', $_POST['marquee_override_val'] );
		// Otherwise just delete it if its blank value.
	} else {
		delete_post_meta( $post_id, '_parent_tax_marquee' );
	}

}