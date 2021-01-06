class CartBack_Settings {

public function __construct() {

	add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	add_action( 'admin_init', array( $this, 'init_settings'  ) );

}

public function add_admin_menu() {

	add_submenu_page(
		'woocommerce',
		esc_html__( 'CartBack Settings', 'cartback' ),
		esc_html__( 'CartBack', 'cartback' ),
		'manage_options',
		'cartback',
		array( $this, 'cartback_settings_page' )
	);

}

public function init_settings() {

	register_setting(
		'cartback_settings',
		'cartback_setting'
	);

	add_settings_section(
		'cartback_setting_section',
		'',
		false,
		'cartback_setting'
	);

	add_settings_field(
		'api_key',
		__( 'Mailchimp API Key', 'cartback' ),
		array( $this, 'render_api_key_field' ),
		'cartback_setting',
		'cartback_setting_section'
	);
	add_settings_field(
		'list_id',
		__( 'Mailchimp List ID', 'cartback' ),
		array( $this, 'render_list_id_field' ),
		'cartback_setting',
		'cartback_setting_section'
	);
	add_settings_field(
		'mailchimp_tag',
		__( 'Mailchimp Tag', 'cartback' ),
		array( $this, 'render_mailchimp_tag_field' ),
		'cartback_setting',
		'cartback_setting_section'
	);
	add_settings_field(
		'uninstall_remove',
		__( 'Delete Settings?', 'cartback' ),
		array( $this, 'render_uninstall_remove_field' ),
		'cartback_setting',
		'cartback_setting_section'
	);

}

public function cartback_settings_page() {

	// Check required user capability
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'cartback' ) );
	}

	// Admin Page Layout
	echo '<div class="wrap">';
	echo '	<h1>' . get_admin_page_title() . '</h1>';
	echo '	<form action="options.php" method="post">';

	settings_fields( 'cartback_settings' );
	do_settings_sections( 'cartback_setting' );
	submit_button();

	echo '	</form>';
	echo '</div>';

}

function render_api_key_field() {

	// Retrieve data from the database.
	$options = get_option( 'cartback_setting' );

	// Set default value.
	$value = isset( $options['api_key'] ) ? $options['api_key'] : '';

	// Field output.
	echo '<input type="text" name="cartback_setting[api_key]" class="regular-text api_key_field" placeholder="' . esc_attr__( 'API Key', 'cartback' ) . '" value="' . esc_attr( $value ) . '">';
	echo '<p class="description">' . __( 'Enter your Mailchimp API Key', 'cartback' ) . '</p>';

}

function render_list_id_field() {

	// Retrieve data from the database.
	$options = get_option( 'cartback_setting' );

	// Set default value.
	$value = isset( $options['list_id'] ) ? $options['list_id'] : '';

	// Field output.
	echo '<input type="text" name="cartback_setting[list_id]" class="regular-text list_id_field" placeholder="' . esc_attr__( 'List ID', 'cartback' ) . '" value="' . esc_attr( $value ) . '">';
	echo '<p class="description">' . __( 'Enter your Mailchimp list ID', 'cartback' ) . '</p>';

}

function render_mailchimp_tag_field() {

	// Retrieve data from the database.
	$options = get_option( 'cartback_setting' );

	// Set default value.
	$value = isset( $options['mailchimp_tag'] ) ? $options['mailchimp_tag'] : 'tmp-checking-out';

	// Field output.
	echo '<input type="text" name="cartback_setting[mailchimp_tag]" class="regular-text mailchimp_tag_field" placeholder="' . esc_attr__( 'Your Tag', 'cartback' ) . '" value="' . esc_attr( $value ) . '">';
	echo '<p class="description">' . __( 'Enter the tag to assign to the customer in Mailchimp', 'cartback' ) . '</p>';

}

function render_uninstall_remove_field() {

	// Retrieve data from the database.
	$options = get_option( 'cartback_setting' );

	// Set default value.
			$value = isset( $options['uninstall_remove'] ) ? $options['uninstall_remove'] : array();

	// Field output.
	echo '<input type="checkbox" name="cartback_setting[uninstall_remove][]" class="uninstall_remove_field" value="' . esc_attr( '' ) . '" ' . ( in_array( '', $value )? 'checked="checked"' : '' ) . '> ' . __( '', 'cartback' ) . '<br>';
	echo '<p class="description">' . __( 'Check this box if you'd like these settings to be cleared and removed form the database when you uninstall this plugin.', 'cartback' ) . '</p>';

}

}

new CartBack_Settings;