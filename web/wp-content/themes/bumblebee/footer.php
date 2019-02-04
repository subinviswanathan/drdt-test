<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package bumblebee
 */

?>
<style type="text/css">
	<?php require get_stylesheet_directory() . '/footer.css'; ?>
</style>
<footer class="footer">
	<div class="container">
		<div class="pure-g">
			<div class="pure-u-1 pure-u-md-3-5 left-foot">
				<div class="ipad-hide logo-socials">
<<<<<<< HEAD
					<a data-analytics-metrics='{"link_name":"footer logo","link_module":"navigation","link_pos":"footer"}' href=" <?php echo esc_url( site_url() ); ?> " class="footer-logo"><img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/cpt-footer-logo.svg" width="100px" alt="Construction Pro Tips Square Logo"></img></a>
=======
					<a href=" <?php echo esc_url( site_url() ); ?> " class="footer-logo"><img src="<?php echo esc_html( get_theme_mod( 'bumblebee_footer_logo' ) ); ?>" alt="" style="width:<?php echo esc_html( get_theme_mod( 'bumblebee_footer_logo_width' ) ); ?>px"></img></a>
>>>>>>> master
					<?php
					if ( has_nav_menu( 'v2-footer-social-links' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'v2-footer-social-links',
								'menu_class'     => 'footer-social-links',
								'container'      => false,
								'walker'         => new TMBI_Social_Profiles,
							)
						);
					}
					?>
				</div> <!-- /.ipad-hide -->
				<div class="site-links">
					<?php
					if ( has_nav_menu( 'v2-footer-site-links' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'v2-footer-site-links',
								'menu_class'     => 'footer-site-links',
								'container'      => false,
								'walker'         => new Footer_Nav_Walker,
							)
						);
					}
					?>
				</div>
				<div class="footer-brand-links-container">
					<div class="footer-brand-links">
						<p>Our Brands</p>
						<?php
						if ( has_nav_menu( 'v2-footer-brand-links' ) ) {
							wp_nav_menu(
								array(
									'theme_location' => 'v2-footer-brand-links',
									'menu_class'     => 'footer-brand-links',
									'container'      => false,
									'walker'         => new V2_Footer_Links,
								)
							);
						}
						?>
					</div>
					<?php
					if ( has_nav_menu( 'v2-footer-global-links' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'v2-footer-global-links',
								'menu_class'     => 'footer-global-links',
								'container'      => false,
								'walker'         => new Menu_Links,
							)
						);
					}
					?>
					<div class="copyright">&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php echo esc_html( __( 'Home Service Publications, Inc', 'tmbi-theme-v3' ) ); ?></div>
				</div>
			</div>
			<div class="pure-u-1 pure-u-md-2-5 right-foot">
				<div class="newsletter">
					<h3 class="">Sign Up For Our Newsletter</h3>
					<form action="<?php echo esc_url( get_site_url() ); ?>/newslettersignuppage/" method="post" data-analytics-metrics='{"link_name":"newsletter signup","link_module":"newsletter signup","link_pos":"footer"}'>
						<input type="text" id="email" placeholder="Email Address"></input>
						<button type="submit" id="subscribe">Sign Up</button>
					</form>
				</div>
				<div class="diyu-logo">
					<a href="https://www.mydiyuniversity.com/"  data-analytics-metrics='{"link_name":"Subscribe link","link_module":"footer","link_pos":"magazine subscription"}' target="_blank" rel="noopener noreferrer">
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/diyu-header-sticky.svg" width="300px" alt=""></img>
					</a>
				</div>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
<?php

/**
 *  Getting hamburger menu markup
 */
function get_hamburger_menu_markup() {
	if ( has_nav_menu( 'hamburger-menu' ) ) {
		$menu = wp_nav_menu(
			array(
				'theme_location' => 'hamburger-menu',
				'menu_class'     => 'pure-menu-children hamburger-menu-items',
				'walker'         => new Main_Menu_Walker,
			)
		);
	};
}

get_hamburger_menu_markup();

?>

<script>
	document.body.className = document.body.className.replace("no-js","");
</script>
</body>
</html>
