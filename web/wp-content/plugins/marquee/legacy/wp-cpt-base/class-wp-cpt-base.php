<?php
/**
 * A Wordpress Custom Post Type abstract base class.
 */
abstract class WP_CPT_Base {
	private static $instance = array();

	// The post type slug
	const post_type = '';

	// Is this CPT sticky-able?
	const stickable = false;

	// Does this CPT have a primary category?
	const primary_cat = false;

	// Does a featured image have to be set to publish?
	const require_featured_image = false;
	const require_primary_cat = false;

	// Any additional arguments to register_post_type
	var $post_type_args = array();

	// Set to post_type (or array of types) that can
	// act as a parent to this post type. This relationship
	// is one-to-and and automatically reflexive.
	var $parent_post_type = '';

	// Set to post_types which can be associated to this
	// type through a many-to-many relationship.
	var $related_post_type = '';

	/**
	 * Taxonomies that this post type can be assigned terms from
	 */
	var $taxonomies = array();

	// Meta form for types that want a "Featured Post" checkbox
	var $feature_meta_form = array(
		'#label' => 'Featured Post',
		'#context' => 'side',
		'featured' => array(
			'#type' => 'checkbox',
			'#label' => 'Featured Post',
			'#description' => 'If checked, this post will be displayed more prominently.',
		),
	);

	// Meta form for sticky-able post types
	var $sticky_meta_form = array(
		'#label' => 'Sticky',
		'#context' => 'side',
		'sticky' => array(
			'#type' => 'checkbox',
			'#label' => 'Sticky?',
			'#description' => 'If checked, stick this post to the front of query results.',
		),
	);

	// Meta form for sticky-able post types
	var $primary_cat_meta_form = array(
		'#label' => 'Primary Category',
		'#context' => 'side',
		'primary_cat' => array(
			'#type' => 'term_select',
			'#class' => array( 'widefat' ),
			'#taxonomy' => 'category',
			'#placeholder' => 'Select Category...',
			'#required' => true,
		),
	);

	// Notices used in the base class
	const notice_cookie = 'wp-cpt-notices';

	var $_notices = array(
		'image-required' => array(
			'level' => 'error',
			'message' => 'A featured image is required to publish this post.',
		),
		'primary-cat-required' => array(
			'level' => 'error',
			'message' => 'A primary category must be selected to publish this post.',
		),
	);

	// Notices used in derived classes
	var $notices = array();

	// Notices that will be displayed in admin_notices
	var $display_notices = array();

	public static function instance() {
		$class = get_called_class();

		if ( ! isset( self::$instance[ $class ] ) ) {
			self::$instance[ $class ] = new $class();
		}

		return self::$instance[ $class ];
	}

	/**
	 * Return all of the CPT instances
	 */
	public static function types() {
		return self::$instance;
	}

	private function __clone() {
	}

	/**
	 * Register hooks
	 */
	protected function __construct() {
		$c = get_called_class();

		require_once( __DIR__ . '/template/related-meta-form.php' );

		add_action( 'init', array( $this, 'register_cpts' ), 10 );
		add_action( 'save_post', array( $this, '_save_post' ) );

		add_filter( 'manage_posts_custom_column', array( $this, '_render_column' ), 10, 2 );

		add_action( 'add_meta_boxes_' . $this->post_type(), array( $this, '_register_meta_boxes' ) );

		// Add child post boxes to parent types
		if ( $this->parent_post_type ) {
			add_action( 'add_meta_boxes', array( $this, '_register_children_meta_boxes' ) );
		}

		// Ensure related post types can be referenced with the 'relation' taxonomy
		if ( $this->related_post_type ) {
			$post_types = (array) $this->related_post_type;

			foreach ( $post_types as $post_type ) {
				register_taxonomy_for_object_type( 'relation', $post_type );
			}

			add_filter( 'related_post_types', array( $this, 'related_post_types' ), 10, 2 );
		}

		// Allow easy management of custom columns
		if ( is_callable( array( $this, 'columns' ) ) ) {
			add_filter( 'manage_edit-' . $this->post_type() . '_columns', array( $this, 'columns' ) );
		}

		if ( is_callable( array( $this, 'sortable_columns' ) ) ) {
			add_filter( 'manage_edit-' . $this->post_type() . '_sortable_columns', array( $this, 'sortable_columns' ) );
		}

		if ( is_admin() && is_callable( array( $this, 'process_sort' ) ) ) {
			add_filter( 'request', array( $this, 'process_sort' ) );
		}

		// Customize permalinks if get_permalink method is defined
		if ( is_callable( array( $this, 'get_permalink' ) ) ) {
			add_filter( 'post_type_link', array( $this, '_get_permalink' ), 10, 4 );
		}

		add_action( 'transition_post_status', array( $this, '_enforce_required_fields' ), 10, 3 );

		// Add 'featured' post class to posts marked as featured
		add_filter( 'post_class', array( $this, 'add_featured_post_class' ), 10, 3 );

		// Show notices to the user when appropriate
		add_action( 'current_screen', array( $this, 'check_notice_cookie' ) );
		add_action( 'admin_notices', array( $this, 'display_notices' ) );

		$this->init();
	}

	/**
	 * State relation to other post types
	 */
	function related_post_types( $post_types, $post_type ) {
		if ( in_array( $post_type, (array) $this->related_post_type ) ) {
			$post_type_object = get_post_type_object( $this->post_type() );

			if ( $post_type_object ) {
				$post_types[ $this->post_type() ] = $post_type_object;
			}
		}

		return $post_types;
	}

	/**
	 * Get post type this class is representing.
	 *
	 * @return string The post type slug, found in the 'post_type' class constant.
	 */
	function post_type() {
		$c = get_called_class();

		return $c::post_type;
	}

	/**
	 * Get whether or not this post type is stickable
	 *
	 * @return bool
	 */
	function stickable() {
		$c = get_called_class();

		return $c::stickable;
	}

	/**
	 * Get whether or not this post type has a primary category
	 *
	 * @return bool
	 */
	function primary_cat() {
		$c = get_called_class();

		return $c::primary_cat;
	}

	/**
	 * Get whether or not this post type requires a primary category. Is always
	 * false if primary_cat is false
	 *
	 * @return bool
	 */
	function requires_primary_category() {
		$c = get_called_class();

		return $c::primary_cat && $c::require_primary_cat;
	}

	/**
	 * Get whether or not this post type requires a featured image
	 *
	 * @return bool
	 */
	function requires_featured_image() {
		$c = get_called_class();

		return $c::require_featured_image;
	}

	/**
	 * Render columns for this CPT
	 */
	function _render_column( $column, $id ) {
		$post = get_post( $id );

		if ( $post->post_type == $this->post_type() ) {
			if ( is_callable( array( $this, 'render_column' ) ) ) {
				$this->render_column( $column, $id );
			}
		}
	}

	/**
	 * Register CPT, but only if $this->label_singular is set.
	 */
	function register_cpts() {
		if ( ! isset( $this->label_singular ) ) {
			return;
		} else if ( ! isset( $this->label_plural ) ) {
			$this->label_plural = $this->label_singular . 's';
		}

		$args = wp_parse_args( $this->post_type_args, array(
			'labels' => array(
				'name' => $this->label_plural,
				'singular_name' => $this->label_singular,
				'add_new_item' => 'Add New ' . $this->label_singular,
				'edit_item' => 'Edit ' . $this->label_singular,
				'new_item' => 'New ' . $this->label_singular,
				'view_item' => 'View ' . $this->label_singular,
				'search_items' => 'Search ' . $this->label_plural,
				'not_found' => 'No ' . strtolower( $this->label_plural ) . ' found',
			),
			'show_ui' => true,
			'public' => true,
			'has_archive' => true,
			'show_in_nav_menus' => true,
			'menu_position' => 20,
			'map_meta_cap' => true,
			'supports' => array(
				'title',
				'editor',
				'thumbnail',
			),
			'hierarchical' => false,
			'rewrite' => array( 'slug' => $this->post_type(), 'with_front' => false, 'feeds' => true ),
			'capability_type' => 'post',
			'taxonomies' => array(),
		) );

		$result = register_post_type( $this->post_type(), $args );

		$this->register_taxonomies();
		// Assign applicable taxonomies
		foreach ( $this->taxonomies as $taxonomy ) {
			register_taxonomy_for_object_type( $taxonomy, $this->post_type() );
		}
	}

	/**
	 * Register additional hooks
	 */
	function init() { }

	/**
	 * Register taxonomies.
	 */
	function register_taxonomies() { }

	/**
	 * Register meta boxes for this post type.
	 */
	function register_meta_boxes( $post ) { }

	/**
	 * Define forms, stealing so many ideas from Drupal Forms API.
	 *
	 * This structure gets iterated recursively. Any
	 * level with a '#type' is considered a form input,
	 * otherwise it's just a group. This is explained in greater
	 * detail below. Each subclass defines their own version of this
	 * structure, which in turn is munged and transformed into
	 * automatic meta forms, which is very useful.
	 */
	var $meta_forms = array();

	/**
	 * Register meta boxes for this CPT
	 */
	function _register_meta_boxes( $post ) {
		add_action( 'post_submitbox_misc_actions', array( $this, 'add_nonce' ) );
		add_action( 'attachment_submitbox_misc_actions', array( $this, 'add_nonce' ) );

		$this->_prepare_meta_form();

		// Add sticky meta for stickable post types
		if ( $this->stickable() ) {
			$this->meta_forms['stickable'] = $this->sticky_meta_form;
		}

		// each element in $this->meta_forms gets its own meta box
		foreach ( WP_Forms_API::get_elements( $this->meta_forms ) as $key => $form ) {
			$form += array( '#context' => 'normal', '#priority' => 'default' );

			add_meta_box( $this->post_type() . '-' . $key, $form['#label'], array( $this, 'meta_boxes' ), $this->post_type(), $form['#context'], $form['#priority'], $form );
		}

		// If a parent post type is defined, then add a meta box
		// to capture that. Since only one post can be a parent, consolidate
		// all the types into a single field.
		if ( $this->parent_post_type ) {
			$post_types = (array) $this->parent_post_type;

			foreach ( $post_types as $post_type ) {
				$post_type_object = get_post_type_object( $post_type );
				$labels[] = $post_type_object->labels->singular_name;
			}

			$title = sprintf( __( 'Parent %s', 'wcb' ), join( ' / ', $labels ) );

			$this->add_parent_meta_box( $title, $post, $post_types );
		}

		// If related post types are defined, register a box for each type.
		if ( $this->related_post_type ) {
			$post_types = (array) $this->related_post_type;

			foreach ( $post_types as $post_type ) {
				$post_type_object = get_post_type_object( $post_type );
				$labels[] = $post_type_object->labels->singular_name;
				$title = sprintf( __( 'Related %s', 'wcb' ), $post_type_object->labels->name );

				$this->add_related_meta_box( $title, $post, $post_type );
			}
		}

		// Allow subclasses to register additional meta box
		$this->register_meta_boxes( $post );
	}

	/**
	 * Render the meta boxes in $meta_forms
	 */
	function meta_boxes( $post, $args ) {
		global $post;

		$this->render_meta_form( $args['args'], $post );
	}

	/**
	 * Register a "Parent Post" meta box.
	 *
	 * @param string $title - The title of the meta box
	 * @param WP_Post $post - The post to attach the meta box to
	 * @param array $post_type - The post types to allow to use as parents
	 */
	function add_parent_meta_box( $title, $post, $post_types = array() ) {
		$key = substr( md5( $this->post_type() . serialize( $post_types ) ), 0, 5 );

		add_meta_box( 'parent-' . $key, $title, array( $this, 'parent_meta_box' ), $this->post_type(), 'side', 'default', $post_types );
	}

	/**
	 * Display "Parent Post" meta box
	 *
	 * @param WP_Post $post - The post to display this box for
	 * @param array $args - How to query for child posts
	 */
	function parent_meta_box( $post, $args ) {
		wcb_related_meta_box( $post->ID, 'parent', $args['args'] );
	}

	/**
	 * Register a "Post Children" meta box for parent types
	 *
	 * @param string $title - The title of the meta box
	 * @param WP_Post $post - The post to attach the meta box to
	 * @param string $post_type - The post type to relate.
	 *
	 * @action add_meta_boxes
	 */
	function _register_children_meta_boxes() {
		$post_types = (array) $this->parent_post_type;

		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $this->post_type() );
			add_meta_box( 'child-' . $this->post_type(), $post_type_object->labels->name, array( $this, 'child_meta_box' ), $post_type, 'side', 'default', $this->post_type() );
		}
	}

	/**
	 * Display "Child Post" meta box
	 *
	 * @param WP_Post $post - The post to display this box for
	 * @param array $args - Meta box arguments
	 */
	function child_meta_box( $post, $args ) {
		wcb_related_meta_box( $post->ID, 'child', $args['args'] );
	}

	/**
	 * Register a "Related Post" meta box. These are general relations.
	 *
	 * @param string $title - The title of the meta box
	 * @param WP_Post $post - The post to attach the meta box to
	 * @param string $post_type - The post type to relate
	 */
	function add_related_meta_box( $title, $post, $post_type ) {
		$key = substr( md5( $this->post_type() . $post_type ), 0, 5 );

		add_meta_box( 'related-' . $key, $title, array( $this, 'related_meta_box' ), $this->post_type(), 'side', 'default', $post_type );
	}

	/**
	 * Display a "Related Posts" meta box for a particular content type
	 *
	 * @param WP_Post $post - The post to display this box for
	 * @param array $args - Meta box arguments
	 */
	function related_meta_box( $post, $args ) {
		wcb_related_meta_box( $post->ID, 'post', $args['args'] );
	}

	/**
	 * Return the nonce key that applies for this post type, or a given post type.
	 */
	function nonce_key() {
		return $this->post_type() . '_meta_nonce';
	}

	/**
	 * Add a nonce for saving all metadata
	 */
	function add_nonce() {
		global $post;

		if ( $post->post_type == $this->post_type() ) {
			wp_nonce_field( $this->nonce_key(), $this->nonce_key(), false, true );
		}
	}

	/**
	 * Prepare meta form before it is rendered or processed
	 */
	function _prepare_meta_form() {
		if ( is_callable( array( $this, 'prepare_meta_form' ) ) ) {
			$this->prepare_meta_form();
		}

		$this->meta_forms = apply_filters( 'prepare_meta_forms', $this->meta_forms, $this->post_type() );
	}

	/**
	 * Verify the save nonce
	 */
	var $nonce_verified = false;

	function verify_nonce() {
		if ( isset( $_POST[ $this->nonce_key() ] ) && wp_verify_nonce( $_POST[ $this->nonce_key() ], $this->nonce_key() ) ) {
			// so we only do this once
			unset( $_POST[ $this->nonce_key() ] );

			$this->nonce_verified = true;
		}

		return $this->nonce_verified;
	}

	/**
	 * When a post is saved, check nonce and call save_meta() if possible
	 *
	 * @action save_post
	 */
	function _save_post( $post_id ) {
		$post = get_post( $post_id );

		// If we're saving a parent post type, then set the child posts accordingly.
		// This is a bit heavy handed, but will be pretty obvious to the user.
		if ( in_array( $post->post_type, (array) $this->parent_post_type ) && isset( $_POST['post_list_ids'][ $this->post_type() ] ) ) {
			if ( isset( $_POST[ $this->nonce_key() ] ) && wp_verify_nonce( $_POST[ $this->nonce_key() ], $this->nonce_key() ) ) {
				$id_list = $_POST['post_list_ids'][ $this->post_type() ];
				$ids = array();

				if ( ! empty( $id_list ) ) {
					$ids = array_map( 'intval', explode( ',', $id_list ) );
				}

				unset( $_POST['post_list_ids'][ $this->post_type() ] );

				foreach ( $ids as $i => $id ) {
					wp_update_post( array(
						'ID' => $id,
						'menu_order' => $i,
						'post_parent' => $post_id,
					) );
				}
			}
		}

		// otherwise save the meta boxes specifically for this post type
		if ( $post->post_type == $this->post_type() ) {
			// only save meta when posted through a save post screen
			if ( $this->verify_nonce() ) {
				// Munge the meta form
				$this->_prepare_meta_form();
				WP_Forms_API::process_form( $this->meta_forms, $meta_values );

				// if we're stickable, update the option (ick!)
				$sticky = get_option( 'sticky_posts' );
				$sticky_index = array_search( $post->ID, $sticky );

				if ( $sticky_index !== false && ( ! $this->stickable() || ! isset( $meta_values['sticky'] ) || ! $meta_values['sticky'] ) ) {
					unset( $sticky[ $sticky_index ] );
					update_option( 'sticky_posts', $sticky );
				} else if ( $sticky_index === false && $this->stickable() && $meta_values['sticky'] ) {
					$sticky[] = $post->ID;
					update_option( 'sticky_posts', $sticky );
				}

				// And since it's not really meta, get rid of its key
				unset( $meta_values['sticky'] );

				if ( isset( $meta_values ) ) {
					$this->replace_post_meta( $post->ID, $meta_values );
				}

				// If a primary category is set, add it to the post categories
				if ( $this->primary_cat() && ! empty( $meta_values['primary_cat'] ) ) {
					wp_set_object_terms( $post->ID, (int) $meta_values['primary_cat'], 'category', true );
				}

				// Save related posts
				if ( $this->related_post_type ) {
					foreach ( (array) $this->related_post_type as $post_type ) {
						// Expect comma-separated list of related IDs
						if ( isset( $_POST['post_list_ids'][ $post_type ] ) ) {
							self::set_post_relations( $post_id, $post_type, explode( ',', $_POST['post_list_ids'][ $post_type ] ) );
						}
					}
				}

				if ( is_callable( array( $this, 'save_meta' ) ) ) {
					$this->save_meta( $post, $meta_values );
				}

				// Then let subclasses do their own thing if they want
				if ( is_callable( array( $this, 'update_post' ) ) ) {
					$this->update_post( $post );
				}
			}
		}
	}

	/**
	 * Ensure that the required fields are supplied. Only do this for posts that are not currently published.
	 *
	 * @action transition_post_status
	 */
	function _enforce_required_fields( $new_status, $old_status, $post ) {
		if ( ! $this->verify_nonce() || 'publish' === $old_status ) {
			return;
		}

		// Unpublish if require_featured_image is true and there's no thumbnail ID
		if ( $this->requires_featured_image() ) {
			if ( ! get_post_meta( $post->ID, '_thumbnail_id', true ) && 'publish' === $post->post_status ) {
				$this->set_notice( 'image-required' );
				$update_status = 'draft';
			}
		}

		// Enforce primary category requirement
		///if ( $this->requires_primary_category() ) {
		//  if ( ! get_post_meta( $post->ID, 'primary_cat', true ) && 'publish' === $post->post_status ) {
		//      $this->set_notice( 'primary-cat-required' );
		//      $update_status = 'draft';
		//  }
		//}

		// Maybe we're updating the post status as a result of validations?
		if ( isset( $update_status ) ) {
			wp_update_post( array(
				'ID' => $post->ID,
				'post_status' => $update_status,
			) );
		}
	}

	/**
	 * Get the relation keys for a particular relation.
	 *
	 * @param int $post_id
	 * @param string $post_type
	 *
	 * @return array( $meta_key, $term_name )
	 */
	static function relation_keys( $post_id, $post_type ) {
		$meta_key = '_related_' . $post_type;
		$term_name = 'related-to-' . $post_id;

		return array( $meta_key, $term_name );
	}

	/**
	 * Set the post relations by adding taxonomy terms.
	 *
	 * Use the taxonomy or meta to quickly look up related posts in either direction.
	 * Order of related posts is defined by the meta value.
	 *
	 * Reflexively add metadata and terms to the related post.
	 */
	static function set_post_relations( $post_id, $post_type, $ids ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		list( $meta_key, $term_name ) = self::relation_keys( $post_id, $post_type );

		// Remove meta ordering
		delete_post_meta( $post_id, $meta_key );

		// Remove any existing terms relating posts from the specified post types to $post_id...
		$term_info = term_exists( $term_name, 'relation' );

		if ( $term_info ) {
			$term_id = (int) $term_info['term_id'];

			$current_ids = get_objects_in_term( $term_id, 'relation' );

			foreach ( $current_ids as $current_id ) {
				$related = get_post( $current_id );

				if ( $related->post_type === $post_type ) {
					wp_remove_object_terms( $current_id, $term_id, 'relation' );
				}
			}
		}

		// If there are $ids (we could just be deleting existing $ids),
		// re-assign the full set of ids and add reflexive relations.
		if ( is_array( $ids ) ) {
			foreach ( $ids as $id ) {
				self::add_post_relation( $post_id, $post_type, $id );
				self::add_post_relation( $id, $post->post_type, $post_id );
			}
		}
	}

	/**
	 * Add a single post relation from one post to another.
	 *
	 * @param int $post_id
	 * @param string $post_type
	 * @param int $id
	 *
	 * @return int The term ID for the relating term.
	 */
	static function add_post_relation( $post_id, $post_type, $id ) {
		list( $meta_key, $term_name ) = self::relation_keys( $post_id, $post_type );

		$relations = get_post_meta( $post_id, $meta_key, true );

		if ( ! is_array( $relations ) ) {
			$relations = array();
		}

		if ( ! in_array( $id, $relations ) ) {
			$relations[] = $id;
		}

		update_post_meta( $post_id, $meta_key, array_filter( $relations ) );

		$term_ids = wp_set_object_terms( $id, $term_name, 'relation', true );

		return $term_ids[0];
	}

	/**
	 * Get child IDs of a particular post_type for a post.
	 *
	 * @param int $post_idj
	 * @param string $post_type
	 *
	 * @return array of $post_ids
	 */
	function get_children( $post_id, $post_type ) {
		$query = new WP_Query( array(
			'post_type' => $post_type,
			'post_parent' => $post_id,
			'fields' => 'ids',
			'post_status' => 'any',
		) );

		return $query->posts;
	}

	/**
	 * Munge and render a metadata form.
	 *
	 * So many stolen ideas from the Drupal forms API, but not nearly as complete,
	 * and specifically designed for WordPress postmeta
	 */
	function render_meta_form( $form, $post ) {
		$post = get_post( $post );
		$meta = get_post_custom( $post->ID );
		$values = array();

		foreach ( $meta as $meta_key => $meta_values ) {
			$values[ $meta_key ] = maybe_unserialize( $meta_values[0] );
		}

		if ( isset( $form['#key'] ) && 'stickable' === $form['#key'] ) {
			$stickies = get_option( 'sticky_posts' );

			$values['sticky'] = in_array( $post->ID, $stickies );
		}

		echo WP_Forms_API::render_form( $form, $values );
	}

	/**
	 * Replace meta values for a post. Any key in $values
	 * will have the corresponding postmeta key and value
	 * replaced.
	 */
	function replace_post_meta( $post, $values ) {
		$post = get_post( $post );

		foreach ( $values as $meta_key => $meta_value ) {
			// Delete truly empty values
			if ( is_null( $meta_value ) || $meta_value === '' || $meta_value === false ) {
				delete_post_meta( $post->ID, $meta_key );
			} else {
				update_post_meta( $post->ID, $meta_key, $meta_value );
			}
		}
	}

	/**
	 * Add 'featured' class to posts who are marked featured
	 *
	 * @filter post_class
	 */
	function add_featured_post_class( $classes, $class, $post_id ) {
		if ( get_post_meta( $post_id, 'featured', true ) ) {
			$classes[] = 'featured';
		}

		return $classes;
	}

	/**
	 * Get the primary category term if one is assigned
	 *
	 * @return WP_Term|null
	 */
	function primary_category( $post = null ) {
		$post = get_post( $post );

		if ( ! $post ) {
			return;
		}

		if ( $primary_cat = get_post_meta( $post->ID, '_yoast_wpseo_primary_category', true ) ) {
			$term = get_term( $primary_cat, 'category' );

			if ( ! is_wp_error( $term ) && $term ) {
				return $term;
			}
		}

		$categories = get_the_terms( $post->ID, 'category' );

		if ( ! empty( $categories ) ) {
			return $categories[0];
		}
	}

	/**
	 * Get notice tags in a normalized structure, throwing out invalid notice structures
	 * and pulling strings into 'message' parts.
	 */
	function notice_tags() {
		$notices = array();
		$unified = $this->notices + $this->_notices;

		foreach ( $unified as $tag => $notice ) {
			// Accept strings
			if ( is_string( $notice ) ) {
				$notice = array( 'message' => $notice );
			}

			// Don't accept other types or missing 'message's
			if ( ! is_array( $notice ) || empty( $notice['message'] ) ) {
				continue;
			}

			// Default to 'updated' level
			$notices[ $tag ] = wp_parse_args( $notice, array( 'level' => 'updated' ) );
		}

		return $notices;
	}

	/**
	 * Set a nag to be displayed on next load via admin_notices.
	 */
	function set_notice( $tag ) {
		$notices = $this->notice_tags();

		if ( ! isset( $notices[ $tag ] ) ) {
			return;
		}

		// Append to existing nags if there is one
		if ( ! empty( $_COOKIE[ self::notice_cookie ] ) ) {
			$tag = $_COOKIE[ self::notice_cookie ] . ',' . $tag;
		}

		setcookie( self::notice_cookie, $tag, time() + DAY_IN_SECONDS );
	}

	/**
	 * check / unset cookie for admin notices
	 *
	 * @action current_screen
	 */
	function check_notice_cookie( $screen ) {
		if ( $screen->post_type === $this->post_type() && isset( $_COOKIE[ self::notice_cookie ] ) ) {
			$this->display_notices = explode( ',', $_COOKIE[ self::notice_cookie ] );

			setcookie( self::notice_cookie, '', 1 );

			unset( $_COOKIE[ self::notice_cookie ] );

			// Replace the default message with our nag.
			if ( ! empty( $this->display_notices ) && isset( $_GET['message'] ) ) {
				add_filter( 'post_updated_messages', function( $messages ) use ( $screen ) {
					unset( $messages[ $screen->post_type ][ $_GET['message'] ] );
					unset( $messages['post'][ $_GET['message'] ] );

					return $messages;
				} );
			}
		}
	}

	/**
	 * Display queued notices
	 *
	 * @action admin_notices
	 */
	function display_notices() {
		global $current_screen;

		$notices = $this->notice_tags();

		foreach ( $this->display_notices as $tag ) {
			if ( ! isset( $notices[ $tag ] ) ) {
				continue;
			}

			$notice = $notices[ $tag ];

			echo '<div class="' . esc_attr( $notice['level'] ) . '"><p>' . esc_html( $notice['message'] ) . '</p></div>';
		}

		unset( $this->display_notices );
	}

	/**
	 * Get the permalink for this CPT
	 *
	 * @filter post_type_link
	 */
	function _get_permalink( $post_link, $post, $leavename, $sample ) {
		if ( $post->post_type === $this->post_type() ) {
			$post_link = $this->get_permalink( $post_link, $post, $leavename, $sample );
		}

		return $post_link;
	}
}
