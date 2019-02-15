<?php
/**
 * IX Header Bidder Settings
 *
 * @package     IX Header Bidder
 *  This is for the settings page of header bidder.
 */

/**
 *  Class IX Header Bidder Settings.
 */
class IX_Settings {
	/**
	 * This will have the value of the script
	 *
	 * @var String
	 */
	public static $script_url   = '';
	const IX_HEADER_BIDDER_SLUG = 'ix-header-bidder';
	const SETTING_NAME          = 'IX Header Bidder';

	/**
	 *  Constructor.
	 */
	public static function init() {
		self::$script_url = get_option( self::IX_HEADER_BIDDER_SLUG );
		add_action( 'admin_menu', array( __CLASS__, 'admin_settings' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_page_init' ) );
	}

	/**
	 * Admin Settings.
	 */
	public static function admin_settings() {
		add_options_page(
			'IX Header Bidder settings',
			'IX Header Bidder',
			'manage_options',
			self::IX_HEADER_BIDDER_SLUG,
			array( __CLASS__, 'show_ix_header_bidder_admin_page' )
		);
	}

	/**
	 * Admin Page.
	 */
	public static function show_ix_header_bidder_admin_page() {
		?>
		<div class="wrap">
			<h1>IX Header Bidder Options</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'ix_header_bidder_options' );
				do_settings_sections( 'ix_header_bidder_settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Admin Page initilaization.
	 */
	public static function admin_page_init() {
		register_setting(
			'ix_header_bidder_options', // Option group.
			self::IX_HEADER_BIDDER_SLUG // Option name.
		);

		add_settings_section(
			'ix_header_bidder_main', // ID.
			'IX Header Bidder Settings', // Title.
			array( __CLASS__, 'print_section_info' ), // Callback.
			'ix_header_bidder_settings' // Page.
		);

		add_settings_field(
			self::IX_HEADER_BIDDER_SLUG, // ID.
			'IX Header Bidder URL', // Title.
			array( __CLASS__, 'ix_header_bidder_url_callback' ), // Callback.
			'ix_header_bidder_settings', // Page.
			'ix_header_bidder_main' // Section.
		);
	}

	/**
	 * Print section.
	 */
	public static function print_section_info() {
		print( '<p>Use this JS for IX Header Bidder</p>' . PHP_EOL );
	}

	/**
	 * Callback function for getting the js from cms.
	 */
	public static function ix_header_bidder_url_callback() {
		$option = isset( self::$script_url ) ? esc_attr( self::$script_url ) : '';

		$content = '<input type="text" id="' . self::IX_HEADER_BIDDER_SLUG . '" name="' . self::IX_HEADER_BIDDER_SLUG . '" value="' . $option . '" style="max-width:100%; width: 980px;" />';
		echo wp_kses(
			$content,
			[
				'input' => [
					'id'    => [],
					'name'  => [],
					'value' => [],
					'style' => [],
				],
			]
		);
	}

}

add_action( 'init', array( 'IX_Settings', 'init' ) );
