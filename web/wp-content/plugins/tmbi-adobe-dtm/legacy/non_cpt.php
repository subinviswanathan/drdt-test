<?php

/**
 * Non-CPT code
 *
 * This code doesn't apply to CPT. Needs to be revisited and refactored when porting the new sites.
 */

/*
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
*/