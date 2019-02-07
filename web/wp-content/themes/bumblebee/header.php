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
		<a href="/" class="pure-u-sm-1 pure-u-md-2-5 logo">
			<img src="<?php echo esc_html( get_theme_mod( 'bumblebee_header_logo' ) ); ?>" alt="<?php echo esc_html( get_bloginfo( 'name' ) ); ?>" style="width:<?php echo esc_html( get_theme_mod( 'bumblebee_header_logo_width' ) ); ?>px"></img>
		</a>

		<div class="pure-u-1-4 mobile-hide">
		</div>
		<div class="pure-u-md-3-5 newsletter-signup-header mobile-hide">
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
					<li class="pure-menu-item menu-text pure-menu-has-children"><a href="#menu" onclick="toggleMenu();">MENU</a>
					</li>
				</ul>
				<a href="/" class="sticky-logo">
					<img src="<?php echo esc_html( get_theme_mod( 'bumblebee_sticky_logo' ) ); ?>" alt="<?php echo esc_html( get_bloginfo( 'name' ) ); ?>" style="width:<?php echo esc_html( get_theme_mod( 'bumblebee_sticky_logo_width' ) ); ?>px"></img>
				</a>
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
			<div class="search-form">
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
		<a href="<?php echo esc_url( get_theme_mod( 'bumblebee_banner_url' ) ); ?>">
			<h4><?php echo esc_html( get_theme_mod( 'bumblebee_banner_text' ) ); ?> 
				<svg aria-hidden="true" data-prefix="fas" data-icon="chevron-right" class="nl-right-arrow svg-inline--fa fa-chevron-right fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
					<path fill="" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg>
			</h4>
		</a>
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
