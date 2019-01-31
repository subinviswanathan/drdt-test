<?php
/**
 * Created by PhpStorm.
 * User: jeysaravana
 * Date: 2017-01-20
 * Time: 12:21 PM
 */

class RD_DTM_Settings {

	const RD_DTM_SLUG        = 'rd-adobe-dtm';
	const NICKNAME_SLUG      = 'rd-dtm-nickname';
	const AMP_SERVER_ACCOUNT = 'rd-dtm-amp-server';
	const SETTING_NAME       = 'RD Adobe DTM';
	private $options = array();

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_settings' ) );
		add_action( 'admin_init', array( $this, 'admin_page_init' ) );
		add_action( 'wp_footer', array( $this, 'rd_dtm_footer' ), 100000 );

	}

	public function rd_dtm_footer() {
		$_dtm_tag  = PHP_EOL;
		$_dtm_tag .= '<script type="text/javascript">' . PHP_EOL;
		$_dtm_tag .= 'if(typeof _satellite !== "undefined"){' . PHP_EOL;
		$_dtm_tag .= '  _satellite.pageBottom()' . PHP_EOL;
		$_dtm_tag .= '}' . PHP_EOL;
		$_dtm_tag .= '</script>' . PHP_EOL;

		echo $_dtm_tag;
	}

	public function admin_settings() {
		add_options_page(
			'Adobe DTM for WordPress settings',
			self::SETTING_NAME,
			'manage_options',
			self::RD_DTM_SLUG,
			array( $this, 'show_dtm_admin_page' )
		);
	}

	public function show_dtm_admin_page() {

		$this->options[self::RD_DTM_SLUG]   = get_option( self::RD_DTM_SLUG );
		$this->options[self::NICKNAME_SLUG] = get_option( self::NICKNAME_SLUG );
		$this->options[self::AMP_SERVER_ACCOUNT] = get_option( self::AMP_SERVER_ACCOUNT );

		?>
		<div class="wrap">
			<h1>RD Adobe DTM Plugin Options</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'rd_adobe_dtm_options' );
				do_settings_sections( 'rd_adobe_dtm_setting' );
				do_settings_sections( 'rd_adobe_dtm_additional_setting' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function admin_page_init() {
		register_setting(
			'rd_adobe_dtm_options', // Option group
			self::RD_DTM_SLUG // Option name
		);
		register_setting(
			'rd_adobe_dtm_options', // Option group
			self::NICKNAME_SLUG // Option name
		);
		register_setting(
			'rd_adobe_dtm_options', // Option group
			self::AMP_SERVER_ACCOUNT // Option name
		);

		add_settings_section(
			'rd_adobe_dtm_main', // ID
			'RD Adobe DTM Settings', // Title
			array( $this, 'print_section_info' ), // Callback
			'rd_adobe_dtm_setting' // Page
		);

		add_settings_field(
			self::RD_DTM_SLUG, // ID
			'Adobe DTM Code', // Title
			array( $this, 'dtm_code_callback' ), // Callback
			'rd_adobe_dtm_setting', // Page
			'rd_adobe_dtm_main' // Section
		);

		add_settings_section(
			'rd_adobe_dtm_main', // ID
			'Additional Page Settings', // Title
			null, // Callback
			'rd_adobe_dtm_additional_setting' // Page
		);

		add_settings_field(
			self::NICKNAME_SLUG, // ID
			'Nickname/Pagename Variable', // Title
			array( $this, 'nickname_callback' ), // Callback
			'rd_adobe_dtm_additional_setting', // Page
			'rd_adobe_dtm_main' // Section
		);

		add_settings_field(
			self::AMP_SERVER_ACCOUNT, // ID
			'AMP Server Name', // Title
			array( $this, 'amp_server_callback' ), // Callback
			'rd_adobe_dtm_additional_setting', // Page
			'rd_adobe_dtm_main' // Section
		);
	}

	public function print_section_info() {
		print('<p>Use this JS for Adobe DTM</p>' . PHP_EOL);
	}

	public function dtm_code_callback() {

		$option = isset( $this->options[self::RD_DTM_SLUG] ) ? esc_attr( $this->options[self::RD_DTM_SLUG] ) : '';

		echo '<input type="text" id="' . self::RD_DTM_SLUG . '" name="' . self::RD_DTM_SLUG . '" value="' . $option . '" style="max-width:100%; width: 980px;" />';

	}

	public function nickname_callback() {

		$option = isset( $this->options[self::NICKNAME_SLUG] ) ? esc_attr( $this->options[self::NICKNAME_SLUG] ) : '';

		echo '<input type="text" id="' . self::NICKNAME_SLUG . '" name="' . self::NICKNAME_SLUG . '" value="' . $option . '" style="max-width:100%; width: 500px;" />';

	}

	public function amp_server_callback() {

		$option = isset( $this->options[self::AMP_SERVER_ACCOUNT] ) ? esc_attr( $this->options[self::AMP_SERVER_ACCOUNT] ) : '';

		echo '<input type="text" id="' . self::AMP_SERVER_ACCOUNT . '" name="' . self::AMP_SERVER_ACCOUNT . '" value="' . $option . '" style="max-width:100%; width: 500px;" />';

	}

}
