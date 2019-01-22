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

wp_enqueue_style( 'bumblebee-style-footer', get_stylesheet_directory_uri() . '/footer.css', [], '1.0.2' );

?>
<footer class="footer">
	<div class="container">
		<div class="pure-g">
			<div class="pure-u-1 desktop-hide-footer newsletter">
				<h5 class="newsletter-cta-description">Sign-up for posts to your inbox</h5>
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/diyu-header-sticky.png"></img>
			</div>
		</div>
		<!--<div class="pure-g border-bottom-mobile"></div>-->
		<div class="pure-g">
			<div class="pure-u-sm-7-12">
				<div class="footer-left-top border-right">
					<div class="pure-g">
						<div class="pure-u-lg-5-12">
							<div class="ipad-hide">
								<a href=" <?php echo esc_url( site_url() ); ?> " class="footer-logo"><img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/cpt-footer-logo.svg" width="100px" alt="Construction Pro Tips Square Logo"></img></a>
								<?php
								if ( has_nav_menu( 'v2-footer-social-links' ) ) {
									wp_nav_menu(
										array(
											'theme_location' => 'v2-footer-social-links',
											'menu_class' => 'footer-social-links',
											'container'  => false,
										)
									);
								}
								?>
							</div>
						</div>
						<div class="pure-u-lg-7-12">
							<div class="site-links">
								<?php
								if ( has_nav_menu( 'v2-footer-site-links' ) ) {
									wp_nav_menu(
										array(
											'theme_location' => 'v2-footer-site-links',
											'menu_class' => 'footer-site-links',
											'container'  => false,
										)
									);
								}
								?>
							</div>
						</div>
						<!--<div class="pure-g border-bottom-mobile"></div>-->
					</div>
				</div>
			</div>
			<div class="pure-u-sm-5-12">
				<div class="newsletter">
					<h4 class="desktop-hide-footer">Sign-up for posts to your inbox</h4>
					<h4 class="mobile-hide-footer">Join over 25,000 subscribers!</h4>
					<form action="<?php echo esc_url( get_site_url() ); ?>/newslettersignuppage/" method="post">
						<input type="text" id="email" placeholder="Email Address"></input>
						<button type="submit" id="subscribe">Sign Up</button>
					</form>
				</div>
			</div>
		</div>
		<div class="pure-g">
			<div class="pure-u-sm-7-12">
				<div class="border-right">
					<div class="footer-brand-links">
						<p>Our Brands</p>
						<?php
						if ( has_nav_menu( 'v2-footer-brand-links' ) ) {
							wp_nav_menu(
								array(
									'theme_location' => 'v2-footer-brand-links',
									'menu_class'     => 'footer-brand-links',
									'container'      => false,
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
							)
						);
					}
					?>
					<div class="copyright">&copy; <?php echo esc_html( $copyright_year ); ?> <?php echo esc_html( __( 'Home Service Publications, Inc', 'tmbi-theme-v3' ) ); ?></div>
				</div>
			</div>
			<div class="pure-u-5-12 newsletter-desktop mobile-hide-footer">
				<a href=""><img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/diyu-header-sticky.svg" width="300px" alt="DIY University Logo"></img></a>
			</div>
		</div>
	</div>
</footer>

<footer class="footer" style="background-color: #424242;">
	<div class="container">
		<div class="pure-g">
			<div class="pure-u-1 pure-u-md-3-5 left-foot">
				<div class="ipad-hide logo-socials">
					<a href=" <?php echo esc_url( site_url() ); ?> " class="footer-logo"><img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/cpt-footer-logo.svg" width="100px" alt="Construction Pro Tips Square Logo"></img></a>
					<?php
					if ( has_nav_menu( 'v2-footer-social-links' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'v2-footer-social-links',
								'menu_class'     => 'footer-social-links',
								'container'      => false,
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
					<form action="<?php echo esc_url( get_site_url() ); ?>/newslettersignuppage/" method="post">
						<input type="text" id="email" placeholder="Email Address"></input>
						<button type="submit" id="subscribe">Sign Up</button>
					</form>
				</div>
				<div class="diyu-logo">
					<a href="https://www.mydiyuniversity.com/" target="_blank" rel="noopener noreferrer">
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/diyu-header-sticky.svg" width="300px" alt="DIY University Logo"></img>
					</a>
				</div>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
