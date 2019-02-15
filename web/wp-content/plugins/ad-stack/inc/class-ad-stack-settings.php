<?php
/**
 * IX Header Bidder Settings
 *
 * @package     Ad Stack Settings
 *  This is for the settings page of Ad Stack.
 */

/**
 * Provides a settings page to configure the Ad Stack
 *
 * Retrieve options with:
 * $ad_stack_options = get_option( 'ad_stack' ); // Array of All Options
 * $dfp_property = $ad_stack_options['dfp_property']; // DFP Property
 * $dfp_site_id = $ad_stack_options['dfp_site_id']; // DFP Site ID
 */
class Ad_Stack_Settings {
	/**
	 * This will have the value of the script
	 *
	 * @var Object
	 */
	private static $ad_stack_options;

	/**
	 * Init.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'ad_stack_add_plugin_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'ad_stack_page_init' ) );
	}

	/**
	 * Register the admin page and menu item.
	 */
	public function ad_stack_add_plugin_page() {
		add_options_page(
			'Ad Stack', // page_title.
			'Ad Stack', // menu_title.
			'manage_options', // capability.
			'ad-stack', // menu_slug.
			array( __CLASS__, 'ad_stack_create_admin_page' ) // function.
		);
	}

	/**
	 * Print the admin page markup.
	 */
	public function ad_stack_create_admin_page() {
		self::$ad_stack_options = get_option( 'ad_stack' ); ?>

		<div class="wrap">
			<h2>Ad Stack</h2>
			<p>Configure the Ad Stack behavior</p>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'ad_stack_option_group' );
				do_settings_sections( 'ad-stack-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register the settings groups and fields.
	 */
	public static function ad_stack_page_init() {
		register_setting(
			'ad_stack_option_group', // option_group.
			'ad_stack' // option_name.
		);
		add_settings_section(
			'ad_stack_dfp_settings', // id.
			'DFP Settings', // title.
			array( __CLASS__, 'ad_stack_dfp_settings_info' ), // callback.
			'ad-stack-admin' // page.
		);

		add_settings_field(
			'dfp_property', // id.
			'DFP Property', // title.
			array( __CLASS__, 'dfp_property_callback' ), // callback.
			'ad-stack-admin', // page.
			'ad_stack_dfp_settings' // section.
		);

		add_settings_field(
			'dfp_site_id', // id.
			'DFP Site ID', // title.
			array( __CLASS__, 'dfp_site_id_callback' ), // callback.
			'ad-stack-admin', // page.
			'ad_stack_dfp_settings' // section.
		);
	}

	/**
	 * Print the DFP Settings section help text.
	 */
	public static function ad_stack_dfp_settings_info() {
		echo '<p>Configure DFP integration</p>';
	}

	/**
	 * Print the DFP Property settings field.
	 */
	public function dfp_property_callback() {
		printf(
			'<input class="regular-text" type="text" name="ad_stack[dfp_property]" id="dfp_property" value="%s">',
			isset( self::$ad_stack_options['dfp_property'] ) ? esc_attr( self::$ad_stack_options['dfp_property'] ) : ''
		);
	}

	/**
	 * Print the DFP Site ID settings field.
	 */
	public function dfp_site_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="ad_stack[dfp_site_id]" id="dfp_site_id" value="%s">',
			isset( self::$ad_stack_options['dfp_site_id'] ) ? esc_attr( self::$ad_stack_options['dfp_site_id'] ) : ''
		);
	}
}
add_action( 'init', array( 'Ad_Stack_Settings', 'init' ) );
