<?php
/**
 * Bumblebee customizer options
 *
 * @link https://developer.wordpress.org/themes/customize-api/customizer-objects/
 *
 * @package bumblebee
 */

/**
 * Custom fonts
 */
function bumblebee_get_font_url() {
	$font_url = '';

	/*
	Translators: If there are characters in your language that are not supported by Open Sans, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'bumblebee' ) ) {
		$subsets = 'latin,latin-ext';

		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)', 'bumblebee' );

		if ( 'cyrillic' !== $subset ) {
			$subsets .= ',cyrillic,cyrillic-ext'; } elseif ( 'greek' !== $subset ) {
			$subsets .= ',greek,greek-ext'; } elseif ( 'vietnamese' !== $subset ) {
				$subsets .= ',vietnamese'; }

			$font_option = str_replace( ' ', '+', get_theme_mod( 'bumblebee_fonts', 'Open Sans' ) );
			$query_args  = array(
				'family' => $font_option . ':400italic,700italic,400,700',
				'subset' => $subsets,
			);
			$font_url    = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return $font_url;
}
/**
 * Sanitize custom fonts
 *
 * @param string $input font options.
 */
function bumblebee_sanitize_fonts( $input ) {
	$valid = array(
		'Open Sans'        => 'Open Sans',
		'Cormorant'        => 'Cormorant',
		'Lato'             => 'Lato',
		'Playfair Display' => 'Playfair Display',
		'Roboto Slab'      => 'Roboto Slab',
		'Raleway'          => 'Raleway',
		'Titillium Web'    => 'Titillium Web',
		'Ubuntu'           => 'Ubuntu',

	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	} else {
		return '';
	}
}

add_action( 'wp_enqueue_scripts', 'bumblebee_enqueue_scripts_styles' );
/**
 * Enqueue scripts
 */
function bumblebee_enqueue_scripts_styles() {
	$font_url = bumblebee_get_font_url();
	if ( ! empty( $font_url ) ) {
		wp_enqueue_style( 'bumblebee-fonts', esc_url_raw( $font_url ), array(), '1.0.0' ); }
}

/**
 * Default customizer color
 */
function bumblebee_customizer_get_default_accent_color() {
	return '#ffffff';
}

/**
 * Putting it all together
 *
 * @param string $mce_css editor styles.
 */
function bumblebee_mce_css( $mce_css ) {
	$font_url = bumblebee_get_font_url();

	if ( empty( $font_url ) ) {
		return $mce_css; }

	if ( ! empty( $mce_css ) ) {
		$mce_css .= ','; }

	$mce_css .= esc_url_raw( str_replace( ',', '%2C', $font_url ) );

	return $mce_css;
}
add_filter( 'mce_css', 'bumblebee_mce_css' );

/**
 * Customizer options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function bumblebee_custom_customize_register( $wp_customize ) {

	$wp_customize->add_setting(
		'bumblebee_default_color',
		array(
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'bumblebee_default_color',
			array(
				'description' => __( 'Site wide font color, used on interior pages' ),
				'label'       => __( 'Font Color' ),
				'section'     => 'colors',
				'settings'    => 'bumblebee_default_color',
				'priority'    => '40',
			)
		)
	);

	$wp_customize->add_setting(
		'bumblebee_accent_hover_color',
		array(
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'bumblebee_accent_hover_color',
			array(
				'description' => __( 'Link Color' ),
				'label'       => __( 'Link & Anchor Color' ),
				'section'     => 'colors',
				'settings'    => 'bumblebee_accent_hover_color',
				'priority'    => '75',
			)
		)
	);

	$wp_customize->add_setting(
		'bumblebee_nav_bg_color',
		array(
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'bumblebee_nav_bg_color',
			array(
				'description' => __( 'Change the nav background color' ),
				'label'       => __( 'Navigation Background Color' ),
				'section'     => 'colors',
				'settings'    => 'bumblebee_nav_bg_color',
				'priority'    => '75',
			)
		)
	);

	$wp_customize->add_setting(
		'bumblebee_nav_color',
		array(
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'bumblebee_nav_color',
			array(
				'description' => __( 'Change the nav text color' ),
				'label'       => __( 'Navigation Text Color' ),
				'section'     => 'colors',
				'settings'    => 'bumblebee_nav_color',
				'priority'    => '76',
			)
		)
	);

	$wp_customize->add_setting(
		'bumblebee_footer_bg_color',
		array(
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'bumblebee_footer_bg_color',
			array(
				'description' => __( 'Change the footer background color' ),
				'label'       => __( 'Footer Background Color' ),
				'section'     => 'colors',
				'settings'    => 'bumblebee_footer_bg_color',
				'priority'    => '77',
			)
		)
	);

	$wp_customize->add_setting(
		'bumblebee_footer_text_color',
		array(
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'bumblebee_footer_text_color',
			array(
				'description' => __( 'Change the footer text color' ),
				'label'       => __( 'Footer Text Color' ),
				'section'     => 'colors',
				'settings'    => 'bumblebee_footer_text_color',
				'priority'    => '78',
			)
		)
	);

	$wp_customize->add_section(
		'bumblebee_fonts',
		array(
			'title'       => __( 'Font Options', 'bumblebee' ),
			'description' => __( 'Change the Heading & Body Fonts' ),
			'priority'    => 33,
		)
	);

	$wp_customize->add_setting(
		'bumblebee_menu_fonts',
		array(
			'default'           => 'Open Sans',
			'sanitize_callback' => 'bumblebee_sanitize_fonts',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'bumblebee_menu_fonts',
			array(
				'label'    => __( 'Select Menu Font', 'bumblebee' ),
				'section'  => 'bumblebee_fonts',
				'settings' => 'bumblebee_menu_fonts',
				'type'     => 'select',
				'choices'  => array(
					'Open Sans'        => 'Open Sans',
					'Cormorant'        => 'Cormorant',
					'Lato'             => 'Lato',
					'Playfair Display' => 'Playfair Display',
					'Roboto Slab'      => 'Roboto Slab',
					'Raleway'          => 'Raleway',
					'Titillium Web'    => 'Titillium Web',
					'Ubuntu'           => 'Ubuntu',
				),
			)
		)
	);

	$wp_customize->add_setting(
		'bumblebee_fonts',
		array(
			'default'           => 'Open Sans',
			'sanitize_callback' => 'bumblebee_sanitize_fonts',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'bumblebee_fonts',
			array(
				'label'    => __( 'Select Heading Font', 'bumblebee' ),
				'section'  => 'bumblebee_fonts',
				'settings' => 'bumblebee_fonts',
				'type'     => 'select',
				'choices'  => array(
					'Open Sans'        => 'Open Sans',
					'Cormorant'        => 'Cormorant',
					'Lato'             => 'Lato',
					'Playfair Display' => 'Playfair Display',
					'Roboto Slab'      => 'Roboto Slab',
					'Raleway'          => 'Raleway',
					'Titillium Web'    => 'Titillium Web',
					'Ubuntu'           => 'Ubuntu',
				),
			)
		)
	);

	$wp_customize->add_setting(
		'bumblebee_body_fonts',
		array(
			'default'           => 'Open Sans',
			'sanitize_callback' => 'bumblebee_sanitize_fonts',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'bumblebee_body_fonts',
			array(
				'label'    => __( 'Select Body Font', 'bumblebee' ),
				'section'  => 'bumblebee_fonts',
				'settings' => 'bumblebee_body_fonts',
				'type'     => 'select',
				'choices'  => array(
					'Open Sans'        => 'Open Sans',
					'Cormorant'        => 'Cormorant',
					'Lato'             => 'Lato',
					'Playfair Display' => 'Playfair Display',
					'Roboto Slab'      => 'Roboto Slab',
					'Raleway'          => 'Raleway',
					'Titillium Web'    => 'Titillium Web',
					'Ubuntu'           => 'Ubuntu',
				),
			)
		)
	);

	$wp_customize->add_section(
		'bumblebee_post_nav_banner',
		array(
			'title'       => __( 'Under Nav Banner', 'bumblebee' ),
			'description' => __( 'Customize the banner, under the main navigation' ),
			'priority'    => 31,
		)
	);

	$wp_customize->add_setting(
		'bumblebee_banner_text',
		array(
			'capability'        => 'edit_theme_options',
			'default'           => 'Sign Up For Our Newsletters',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'bumblebee_banner_text',
		array(
			'type'        => 'text',
			'section'     => 'bumblebee_post_nav_banner',
			'label'       => __( 'Banner Text' ),
			'description' => __( 'Enter the text here, i.e: Sign Up For Newsletters' ),
			'settings'    => 'bumblebee_banner_text',
		)
	);

	$wp_customize->add_setting(
		'bumblebee_banner_text_color',
		array(
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_setting(
		'bumblebee_banner_url',
		array(
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'bumblebee_banner_url',
		array(
			'type'        => 'url',
			'section'     => 'bumblebee_post_nav_banner',
			'label'       => __( 'Banner Link URL' ),
			'description' => __( 'Add the URL' ),
			'input_attrs' => array(
				'placeholder' => __( 'https://www.tmbi.com' ),
			),
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'bumblebee_banner_text_color',
			array(
				'description' => __( 'Change the color if the banner text' ),
				'label'       => __( 'Banner Text Color' ),
				'section'     => 'bumblebee_post_nav_banner',
				'settings'    => 'bumblebee_banner_text_color',
				'priority'    => '77',
			)
		)
	);

	$wp_customize->add_section(
		'bumblebee_logos',
		array(
			'title'       => __( 'Custom Logos', 'bumblebee' ),
			'description' => __( 'Add custom logos' ),
			'priority'    => 32,
		)
	);

	$wp_customize->add_setting( 'bumblebee_header_logo' );

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'bumblebee_header_logo',
			array(
				'title'    => __( 'Header Logo', 'bumblebee' ),
				'label'    => __( 'Upload a header logo', 'bumblebee' ),
				'section'  => 'bumblebee_logos',
				'settings' => 'bumblebee_header_logo',
			)
		)
	);

	$wp_customize->add_setting(
		'bumblebee_header_logo_width',
		array(
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'bumblebee_header_logo_width',
		array(
			'type'        => 'number',
			'section'     => 'bumblebee_logos',
			'label'       => __( 'Header Logo Width' ),
			'description' => __( 'Enter only numbers, i.e: 200' ),
			'settings'    => 'bumblebee_header_logo_width',
		)
	);

	$wp_customize->add_setting( 'bumblebee_footer_logo' );

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'bumblebee_footer_logo',
			array(
				'title'    => __( 'Footer Logo', 'bumblebee' ),
				'label'    => __( 'Upload a footer logo', 'bumblebee' ),
				'section'  => 'bumblebee_logos',
				'settings' => 'bumblebee_footer_logo',
			)
		)
	);

	$wp_customize->add_setting(
		'bumblebee_footer_logo_width',
		array(
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'bumblebee_footer_logo_width',
		array(
			'type'        => 'number',
			'section'     => 'bumblebee_logos',
			'label'       => __( 'Footer Logo Width' ),
			'description' => __( 'Enter only numbers, i.e: 100' ),
			'settings'    => 'bumblebee_footer_logo_width',
		)
	);

	$wp_customize->add_section(
		'bumblebee_buttons',
		array(
			'title'       => __( 'Buttons', 'bumblebee' ),
			'description' => __( 'Customize the button colors' ),
			'priority'    => 35,
		)
	);

	$wp_customize->add_setting( 'bumblebee_button_bg_color' );

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'bumblebee_button_bg_color',
			array(
				'label'    => __( 'Button BG Color' ),
				'section'  => 'bumblebee_buttons',
				'settings' => 'bumblebee_button_bg_color',
				'priority' => '77',
			)
		)
	);

	$wp_customize->add_setting( 'bumblebee_button_bg_color_hover' );

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'bumblebee_button_bg_color_hover',
			array(
				'label'    => __( 'Button BG Hover Color' ),
				'section'  => 'bumblebee_buttons',
				'settings' => 'bumblebee_button_bg_color_hover',
				'priority' => '77',
			)
		)
	);

	$wp_customize->add_setting( 'bumblebee_button_text_color' );

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'bumblebee_button_text_color',
			array(
				'label'    => __( 'Button Text Color' ),
				'section'  => 'bumblebee_buttons',
				'settings' => 'bumblebee_button_text_color',
				'priority' => '77',
			)
		)
	);

	$wp_customize->add_section(
		'bumblebee_footer_nl',
		array(
			'title'       => __( 'Footer Newsletter', 'bumblebee' ),
			'description' => __( 'Customize what the newsletter section should look like' ),
			'priority'    => 40,
		)
	);

	$wp_customize->add_setting(
		'bumblebee_footer_nl_heading_text',
		array(
			'capability'        => 'edit_theme_options',
			'default'           => 'Sign Up For Our Newsletters',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'bumblebee_footer_nl_heading_text',
		array(
			'type'        => 'text',
			'section'     => 'bumblebee_footer_nl',
			'label'       => __( 'Newsletter Heading Text' ),
			'description' => __( 'Enter the text here, i.e: Sign Up For Newsletters' ),
			'settings'    => 'bumblebee_footer_nl_heading_text',
		)
	);

	$wp_customize->add_setting( 'bumblebee_footer_nl_heading_text_color' );

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'bumblebee_footer_nl_heading_text_color',
			array(
				'label'    => __( 'Newsletter Heading Text Color' ),
				'section'  => 'bumblebee_footer_nl',
				'settings' => 'bumblebee_footer_nl_heading_text_color',
				'priority' => '10',
			)
		)
	);

	$wp_customize->add_setting( 'bumblebee_footer_nl_subscribe_image' );

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'bumblebee_footer_nl_subscribe_image',
			array(
				'title'    => __( 'Newsletter Image', 'bumblebee' ),
				'label'    => __( 'Add the "Subscribe to Newsletter" Image', 'bumblebee' ),
				'section'  => 'bumblebee_footer_nl',
				'settings' => 'bumblebee_footer_nl_subscribe_image',
				'priority' => '20',
			)
		)
	);

	$wp_customize->add_setting(
		'bumblebee_footer_nl_subscribe_image_width',
		array(
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'bumblebee_footer_nl_subscribe_image_width',
		array(
			'type'        => 'number',
			'section'     => 'bumblebee_footer_nl',
			'label'       => __( 'Newsletter Image Width' ),
			'description' => __( 'Enter only numbers, i.e: 300' ),
			'settings'    => 'bumblebee_footer_nl_subscribe_image_width',
			'priority'    => '30',
		)
	);

	$wp_customize->add_setting(
		'bumblebee_footer_nl_subscribe_url',
		array(
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'bumblebee_footer_nl_subscribe_url',
		array(
			'type'        => 'url',
			'section'     => 'bumblebee_footer_nl',
			'label'       => __( 'Newsletter Link URL' ),
			'description' => __( 'Add the URL' ),
			'priority'    => '35',
			'input_attrs' => array(
				'placeholder' => __( 'https://www.tmbi.com' ),
			),
		)
	);
}
add_action( 'customize_register', 'bumblebee_custom_customize_register' );

/**
 * Custom styles into head
 */
function bumblebee_add_customizer_styles() {

	$accent_hover_color    = get_theme_mod( 'bumblebee_accent_hover_color' );
	$nav_bg_color          = get_theme_mod( 'bumblebee_nav_bg_color' );
	$nav_text_color        = get_theme_mod( 'bumblebee_nav_color' );
	$banner_text_color     = get_theme_mod( 'bumblebee_banner_text_color' );
	$footer_bg_color       = get_theme_mod( 'bumblebee_footer_bg_color' );
	$footer_text_color     = get_theme_mod( 'bumblebee_footer_text_color' );
	$footer_nl_text_color  = get_theme_mod( 'bumblebee_footer_nl_heading_text_color' );
	$font_default_color    = get_theme_mod( 'bumblebee_default_color' );
	$font_menu_option      = get_theme_mod( 'bumblebee_menu_fonts' );
	$font_heading_option   = get_theme_mod( 'bumblebee_fonts' );
	$font_body_option      = get_theme_mod( 'bumblebee_body_fonts' );
	$button_bg_color       = get_theme_mod( 'bumblebee_button_bg_color' );
	$button_bg_hover_color = get_theme_mod( 'bumblebee_button_bg_color_hover' );
	$button_text_color     = get_theme_mod( 'bumblebee_button_text_color' );

	?>
	<style>
		.main-navigation,
		.newsletter-sign-below-header {
			font-family: "<?php echo esc_html( $font_menu_option ); ?>" !important;
		}

		.header .main-navigation,
		.header .pure-menu-list ul {
			background: <?php echo esc_html( $nav_bg_color ); ?>;
		}

		.header .pure-menu-list ul {
			z-index: 1;
		}

		.header .main-navigation .menu-desktop-focus-menu-container ul li a,
		.header .menu-text,
		.header .menu-text a,
		.header .pure-menu-list ul li a span {
			color: <?php echo esc_html( $nav_text_color ); ?>;
		}

		.header .hamburger-menu {
			background-color: <?php echo esc_html( $nav_text_color ); ?>;
		}

		.header .pure-menu-list ul li:not(:last-child) {
			border-bottom: 2px dotted <?php echo esc_html( $nav_text_color ); ?>;
		}

		.newsletter-sign-below-header.hide-on-mobile .nl-signup-link h4,
		.newsletter-sign-below-header.hide-on-mobile .nl-signup-link h4 .nl-right-arrow {
			color: <?php echo esc_html( $banner_text_color ); ?>;
			fill: <?php echo esc_html( $banner_text_color ); ?>;
		}

		main {
			font-family: "<?php echo esc_html( $font_body_option ); ?>" !important;
		}

		h1, h2, h3, h4, h5, h6 {
			font-family: "<?php echo esc_html( $font_heading_option ); ?>" !important;
		}

		main.site-content {
			color: <?php echo esc_html( $font_default_color ); ?>;
		}

		a {
			color: <?php echo esc_html( $accent_hover_color ); ?>;
		}

		.post-content a {
			color: <?php echo esc_html( $accent_hover_color ); ?> !important;
			border-bottom: 1px solid <?php echo esc_html( $accent_hover_color ); ?> !important;
		}

		.post-category-label {
			border-bottom: none !important;
		}

		.footer,
		.no-js .accessibility-menu,
		.no-js .accessibility-menu .menu-hamburger-menu-container #menu {
			background: <?php echo esc_html( $footer_bg_color ); ?> !important;
		}

		.footer ul li a,
		.footer .footer-brand-links-container ul.footer-brand-links li:not(:last-child):after {
			color: <?php echo esc_html( $footer_text_color ); ?> !important;
		}

		.read-more,
		.more {
			background: <?php echo esc_html( $button_bg_color ); ?>;
			color: <?php echo esc_html( $button_text_color ); ?>;
		}

		.read-more:hover,
		.more:hover {
			background: <?php echo esc_html( $button_bg_hover_color ); ?>;
		}

		footer .newsletter h3 {
			color: <?php echo esc_html( $footer_nl_text_color ); ?>;
		}

		.footer .newsletter form button {
			background-color: <?php echo esc_html( $button_bg_color ); ?>;
			color: <?php echo esc_html( $button_text_color ); ?>;
		}

		.footer .newsletter form button:hover {
			background-color: <?php echo esc_html( $button_bg_hover_color ); ?>;
		}

		@media screen and (max-width: 767px) {
			.header .hamburger-menu {
				background-color: <?php echo esc_html( $nav_bg_color ); ?>;
			}
		}
	}
	</style>

	<?php
}
add_action( 'wp_head', 'bumblebee_add_customizer_styles' );
