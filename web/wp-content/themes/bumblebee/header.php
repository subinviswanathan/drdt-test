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

<body <?php body_class(); ?>
<a class="skip-link screen-reader-text" href="#content"></a>
<header class="header">
	<div class="content-wrapper">
		<div class="hamburger-mobile desktop-hide">
			<div class="hamburger">
				<div class="hamburger-menu"></div>
				<div class="hamburger-menu"></div>
				<div class="hamburger-menu"></div>
			</div>
		</div>
		<a href="/" class="pure-u-1-5 header-logo"></a>

		<div class="pure-u-1-4 mobile-hide">
		</div>
		<div class="pure-u-1-2 newsletter-signup-header mobile-hide">
			<a class="subscribe-header" target="_blank" rel="noopener" href="https://www.constructionprotips.com/newsletters/">
				<img class="subscribe-logo initial loaded" alt="Subscribe" src="http://cpt.test.rda.net/wp-content/uploads/sites/9/2018/10/cpt-newsletter-header.png" data-was-processed="true">
			</a>
		</div>
		<div class="search-mobile desktop-hide">
			<button class="search-button"></button>
		</div>
	</div>
	<nav class="main-navigation">
		<div class="pure-menu pure-menu-horizontal">
			<div class="hamburger-wrapper mobile-hide">
				<div class="hamburger">
					<div class="hamburger-menu"></div>
					<div class="hamburger-menu"></div>
					<div class="hamburger-menu"></div>
				</div>
				<span class="menu-text">MENU</span>
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
						<input type="text" placeholder="Serach">
						<button class="search-button"></button>
					</fieldset>
				</form>
			</div>
		</div>
	</nav>
</header>

<div class="pure-g newsletter-sign-below-header hide-on-mobile">
	<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1 nl-signup-link text-center">
		<a href="#"><h4>Sign Up for Our Newsletters <img class="nl-right-arrow" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/chevron-right-solid.svg"/></h4></a>
	</div>
</div>
<!-- #site-navigation -->
