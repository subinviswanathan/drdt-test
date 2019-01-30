<?php

/*
 * Walker class for Footer One
 * splits menu into two unordered list <ul>.
 */
class Footer_Nav_Walker extends Walker_Nav_Menu {

	var $current_menu = null;
	var $break_point  = null;

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		global $wp_query;

		if ( ! isset( $this->current_menu ) ) {
			$this->current_menu = wp_get_nav_menu_object( $args->menu );
		}

		if ( ! isset( $this->break_point ) ) {
			$this->break_point = ceil( $this->current_menu->count / 2 ) + 1;
		}

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		if ( $this->break_point == $item->menu_order ) {
			$class_names_ul = 'footer-site-links';
			$output        .= $indent . '</ul><ul class="'.$class_names_ul.'"><li' . $id . $value . $class_names . '>';
		} else {
			$output .= $indent . '<li' . $id . $value . $class_names . '>';
		}
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url ) .'"' : '';

		$attributes_array = array( 'link_name' => esc_attr( $item->title ),'link_module' => 'footer','link_pos' => 'navigation' );
		$json_attributes  = json_encode( $attributes_array );
		$attributes      .= ' data-analytics-metrics=\''.$json_attributes.'\'';

		$item_output  = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}


	function start_lvl(&$output, $depth = 0, $args = array()) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul>\n";
	}
}

/*
 * Adding analytic attributes to footer Social Profile
 */
class TMBI_Social_Profiles extends Walker_Nav_Menu {
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent      = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$class_names = $value = '';
		$classes     = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
		$output     .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';

		$title            = self::get_social_profile_names( $item->title );
		$attributes_array = array( 'link_name' => esc_attr( $title ),'link_module' => 'footer','link_pos' => 'follow us' );
		$json_attributes  = json_encode( $attributes_array );
		$attributes      .= ' data-analytics-metrics=\''.$json_attributes.'\'';
		$attributes      .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	function get_social_profile_names( $title ) {
		if ( strpos( $title, 'facebook' ) !== false ) {
			return( 'facebook' );
		}
		if ( strpos( $title, 'pinterest' ) !== false ) {
			return( 'pinterest' );
		}
		if ( strpos( $title, 'twitter' ) !== false ) {
			return( 'twitter' );
		}
		if ( strpos( $title, 'google-plus' ) !== false ) {
			return( 'google plus' );
		}
		if ( strpos( $title, 'instagram' ) !== false ) {
			return( 'instagram' );
		}
		if ( strpos( $title, 'youtube' ) !== false ) {
			return( 'youtube' );
		}
		if ( strpos( $title, 'envelope' ) !== false ) {
			return( 'newsletter signup' );
		}
		return( $title );
	}
}

/*
 * Adding Additional analytic attribute to Footer links
 */
class V2_Footer_Links extends Walker_Nav_Menu {
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$class_names = $value = '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';

		$attributes_array = array( 'link_name' => esc_attr( $item->title ),'link_module' => 'navigation','link_pos' => 'footer' );
		$json_attributes = json_encode( $attributes_array );
		$attributes .= ' data-analytics-metrics=\''.$json_attributes.'\'';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

/*
 * Adding Additional analytic attribute to Footer links
 */
class Menu_Links extends Walker_Nav_Menu {
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$class_names = $value = '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';

		$attributes_array = array( 'link_name' => esc_attr( $item->title ),'link_module' => 'header','link_pos' => 'main navigation' );
		$json_attributes = json_encode( $attributes_array );
		$attributes .= ' data-analytics-metrics=\''.$json_attributes.'\'';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

/*
 * Adding Additional analytic attribute to Desktop Upper Menu
 */
class Desktop_Upper_Menu extends Walker_Nav_Menu {
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$class_names = $value = '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';

		$attributes_array = array( 'link_name' => esc_attr( $item->title ),'link_module' => 'header','link_pos' => 'secondary navigation' );
		$json_attributes = json_encode( $attributes_array );
		$attributes .= ' data-analytics-metrics=\''.$json_attributes.'\'';

		$url = '';
		if ( ! empty( $item->url ) ) {
			$url = esc_attr( $item->url );
			if ( $url == 'https://www.tasteofhome.com/login' || strtolower( str_replace( ' ', '', $item->title ) ) == 'login' ) {
				$url = 'https://www.tasteofhome.com/login/index?returnurl=' . $this->page_url();
			}
		}
		$attributes .= ! empty( $item->url ) ? ' href="' . $url .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	public function page_url() {
		$page_url = 'http';
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
			$page_url .= 's';
		}

		$page_url .= '://';
		if ( $_SERVER['SERVER_PORT'] != '80' ) {
			$page_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$page_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
		return $page_url;
	}
}

/*
 * Adding Additional analytic attribute to Desktop Upper Menu
 */
class Logged_In_Menu extends Walker_Nav_Menu {
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$class_names = $value = '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';

		$attributes_array = array( 'link_name' => esc_attr( $item->title ),'link_module' => 'header','link_pos' => 'secondary navigation' );
		$json_attributes = json_encode( $attributes_array );
		$attributes .= ' data-analytics-metrics=\''.$json_attributes.'\'';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

/*
 * Extends Walker_Nav_Menu class
 * adding addition attributes to Primary navigation and mobile navigation
 */
class Main_Menu_Walker extends Walker_Nav_Menu {
	private $previous_title;
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$link_position = 'main navigation';
		$link_module   = 'header';
		if ( $depth > 0 ) {
			$link_position = 'drop down navigation';
		}

		$this->previous_title[$depth] = $item->title;

		$title            = implode( ':', array_slice( $this->previous_title, 0, $depth + 1 ) );
		$attributes_array = array( 'link_name' => esc_attr( $title ), 'link_module' => $link_module, 'link_pos' => $link_position );
		$json_attributes  = json_encode( $attributes_array );

		$indent      = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$class_names = $value = '';
		$classes     = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
		$output     .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
		if ( ! $args->walker->has_children ) {
			$attributes .= ' data-analytics-metrics=\'' . $json_attributes . '\'';
		}
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';

		$item_output  = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
