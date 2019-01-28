<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package bumblebee
 */

wp_enqueue_style( 'bumblebee-style-header', get_stylesheet_directory_uri() . '/header.css', [], '1.0.2' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<a class="skip-link screen-reader-text" href="#content"></a>
<header class="header">
	<div id="gpt-postcribe">

	</div>
	<div class="content-wrapper">
		<div class="hamburger-wrapper desktop-hide">
			<?php get_hamburger_markup(); ?>
			<ul class="pure-menu-list ">
				<li class="pure-menu-item pure-menu-has-children">
				</li>
			</ul>
		</div>
		<a href="/" class="pure-u-1-5 logo">
			<img src="<?php echo get_theme_mod( 'bumblebee_header_logo' ); ?>" alt="" style="width:<?php echo get_theme_mod( 'bumblebee_header_logo_width' ); ?>px"></img>
			
		</a>

		<div class="pure-u-1-4 mobile-hide">
		</div>
		<div class="pure-u-4-5 newsletter-signup-header mobile-hide">
			<a class="subscribe-header" target="_blank" rel="noopener" href="https://www.constructionprotips.com/newsletters/">
				<img class="subscribe-logo initial loaded" alt="Subscribe" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/cpt-newsletter-header.svg" data-was-processed="true" style="width:180px">
			</a>
		</div>
	</div>
	<nav class="main-navigation">
		<div class="pure-menu pure-menu-horizontal">
			<div class="hamburger-wrapper mobile-hide">
				<?php get_hamburger_markup(); ?>
				<ul class="pure-menu-list ">
					<li class="pure-menu-item menu-text pure-menu-has-children">MENU
					</li>
				</ul>
			</div>
	<?php
	if ( has_nav_menu( 'desktop-focus-menu' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'desktop-focus-menu',
				'menu_class'     => 'focus-menu',
			)
		);
	}
	?>
			<div class="search-form mobile-hide">
				<form class="pure-form" action=" <?php echo esc_url( site_url() ); ?>/search/index ">
					<fieldset>
						<input type="text" placeholder="Search">
						<button class="search-button"></button>
					</fieldset>
				</form>
			</div>
		</div>
	</nav>
</header>

<div class="pure-g newsletter-sign-below-header hide-on-mobile">
	<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1 nl-signup-link">
		<a href="#"><h4>Sign Up for Our Newsletters <img class="nl-right-arrow" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/chevron-right-solid.svg"/></h4></a>
	</div>
</div>
<!-- #site-navigation -->

<?php

/**
 *  Getting hamburger markup
 */
function get_hamburger_markup() {
	$hamburger  = '<div class="hamburger">';
	$hamburger .= '<div class="hamburger-menu"></div>';
	$hamburger .= '<div class="hamburger-menu"></div>';
	$hamburger .= '<div class="hamburger-menu"></div>';
	$hamburger .= '</div>';
	$hamburger .= '<div class="hamburger-close hide-ham-sign">';
	$hamburger .= '</div>';
	echo wp_kses_post( $hamburger );
}
?>
