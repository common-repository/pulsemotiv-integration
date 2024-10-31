<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.pulsemotiv.com/
 * @since      1.0.0
 *
 * @package    Pulsemotiv
 * @subpackage Pulsemotiv/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pulsemotiv
 * @subpackage Pulsemotiv/admin
 * @author     pulsemotiv <support@pulsemotiv.com>
 */
class Pulsemotiv_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
     * The api domain to call for the api key endpoints
	 * @var string
	 */
	private $api_domain;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->api_domain = 'https://insights.pulsemotiv.com/api/';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pulsemotiv_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pulsemotiv_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pulsemotiv-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pulsemotiv_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pulsemotiv_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pulsemotiv-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Add menu page function hooked from class-pulsemotiv.php
	 */
	public function pulse_plugin_menu() {
		$pluginPath = new Pulsemotiv();
		$pluginPath = $pluginPath->get_plugin_url();
		add_menu_page( 'Pulsemotiv integration', 'Pulsemotiv for WP', 'manage_options',
			$this->plugin_name, array($this, 'show_main_page'), $pluginPath . '/admin/img/icon.svg' );
	}

	/**
	 * Show the main admin page hooked from pulse_plugin_menu function
	 */
	public function show_main_page() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$options = get_option('pulse4wp');
		$pulse_api_key = $options['api_key'];
		$verified = ! empty($pulse_api_key);

		if ($verified) {
			$response = $this->verifyApi($pulse_api_key);
			if (is_array($response) && ! is_wp_error($response)) {
				$header = $response['headers'];
				$body = json_decode($response['body']);
				$statusCode = wp_remote_retrieve_response_code($response);

				if ($statusCode === 200) {
					$api_valid = true;
					$obfuscated_api_key = $this->obfuscate_api_key($pulse_api_key);
				} else {
					$api_valid = false;
					$obfuscated_api_key = $this->obfuscate_api_key($pulse_api_key);
				}
			} else {
				$api_valid = false;
				$obfuscated_api_key = $this->obfuscate_api_key($pulse_api_key);
            }
		} else {
			$api_valid = false;
			$obfuscated_api_key = $pulse_api_key;
        }

		include_once 'partials/pulsemotiv-admin-display.php';
	}

	/**
	 * Register setting for api form hooked from class-pulsemotiv.php
	 */
	public function pulse_settings() {
		register_setting('pulse4wp_settings', 'pulse4wp', array($this, 'save_pulse_settings'));
	}

	/**
	 *
	 * Settings function called to save the input in the api form to wp database
	 * @param $input
	 * @return mixed
	 */
	public function save_pulse_settings($input) {
		$options = get_option( 'pulse4wp' );
		// Make sure not to use obfuscated key
		if ( isset( $input['api_key'] ) && strpos( $input['api_key'], '*' ) === false ) {
			$options['api_key'] = sanitize_text_field( trim( $input['api_key'] ) );
		}

		$verified = ! empty( $options['api_key'] );

		if ( $verified ) {
			$response = $this->verifyApi( $options['api_key'] );
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$header     = $response['headers'];
				$body       = json_decode( $response['body'] );
				$statusCode = wp_remote_retrieve_response_code( $response );

				if ( $statusCode !== 200 ) {
					$message = $body->message;
					$type    = 'error';
					add_settings_error( 'pulse4wp_settings', 'api-key', $message, $type );
				}
			} elseif (is_wp_error($response)) {
				$message = $response->get_error_message();
				$type    = 'error';
			    add_settings_error('pulse4wp_settings', 'api-key', $message, $type);
            }
		} else {
			$message = 'Invalid API Key Provided';
			$type    = 'error';
			add_settings_error( 'pulse4wp_settings', 'api-key', $message, $type );
		}

		return $options;
	}

	/**
	 * Verify api key provided by user
	 * @param $api_key
	 *
	 * @return array|WP_Error
	 */
	public function verifyApi($api_key) {
		$params = array(
			'method' => 'POST',
			'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
			'sslverify' => false,
            'body' => json_encode(array(
                    'api_key' => $api_key,
                    'site_url' => get_site_url())
            )
		);

		return wp_remote_post($this->api_domain.'verify-api-key', $params);
	}

	/**
	 * This will replace the first half of a string with "*" characters.
	 * @param $key
	 *
	 * @return string
	 */
	public function obfuscate_api_key($key): string {
		$length = strlen($key);
		$obfuscated_length = ceil($length / 2);
		$key = str_repeat('*', $obfuscated_length) . substr($key, $obfuscated_length);
		return $key;
	}

	/**
	 * Initializer for the short code
	 */
	public function pulse_shortcode_init() {
		function pulse_shortcode($atts) {
			$a = shortcode_atts(array(
				'uniqueid' => '',
				'id' => '0',
			), $atts);

			$uuid = $a['uniqueid'];
			$id = $a['id'];

			if($uuid !== '')
			{
				$pulse ='<script src="//insights.pulsemotiv.com/api/pulse_player?pulse='. $uuid .'" id="rsPulseScript'. $id .'">  </script>';
			}
			else
			{
				$pulse = '<div>Please enter a valid pulse id.</div>';
			}

			return $pulse;
		}

		add_shortcode('pulse', 'pulse_shortcode');

	}

	/**
	 * Function invoked on publish or update of a page or a post
	 * checks the short code exists in it and publish the pulse in pulsemotiv backend
	 * @param $id
	 * @param $post
	 */
	public function on_publish($id, $post) {
		if (has_shortcode($post->post_content, 'pulse')) {
			$pattern = get_shortcode_regex();
			if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
			       && array_key_exists( 2, $matches )
			       && in_array( 'pulse', $matches[2], false ) )
			{
				$codeMatched = array_filter($matches[2], function ($value) {
					return $value === 'pulse';
				});

				foreach ($codeMatched as $key => $code) {
					$codeAttribs = shortcode_parse_atts($matches[3][$key]);
					$this->changePulseStatus($codeAttribs['uniqueid']);
				}
			}
		}
	}

	/**
	 * function to make a request to backend pulsemotiv
	 * @param $uniqueid
	 */
	public function changePulseStatus($uniqueid) {
		$options = get_option('pulse4wp');

		$params = array(
			'method' => 'POST',
			'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
			'sslverify' => false,
			'body' => json_encode(array(
					'api_key' => $options['api_key'],
					'site_url' => get_site_url(),
                    'pulseUniqueId' => $uniqueid
                )
			)
		);

		wp_remote_post($this->api_domain.'wordpress-publish', $params);
	}

	/**
	 * Dropdown button setup for pulsemotiv pulses to embed in the visual editor
	 */
	public function pulsemotiv_add_dropdown_button() {
		global $typenow;
			// check user permissions
			if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
				return;
			}
	    // verify the post type
	    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
		    return;
	    // check if WYSIWYG is enabled
	    if ( get_user_option('rich_editing') === 'true') {
		    add_filter( 'mce_external_plugins', array($this, 'pulsemotiv_add_tinymce_plugin'));
		    add_filter('mce_buttons', array($this, 'pulsemotiv_register_my_tc_button'));
	    }
	}

	/**
	 * include the scripts to generate the dropdown for pulsemotiv
	 * @param $plugin_array
	 *
	 * @return mixed
	 */
	public function pulsemotiv_add_tinymce_plugin($plugin_array) {
		$plugin_array['pulsemotiv_button'] = plugins_url( '/js/pulse-dropdown.js', __FILE__ ); // CHANGE THE BUTTON SCRIPT HERE

		$options = get_option('pulse4wp');

		$verified = ! empty($options['api_key']);
		if ($verified) {
			$response = $this->verifyApi($options['api_key']);
			if (is_array($response) && ! is_wp_error($response)) {
				$header = $response['headers'];
				$body = json_decode($response['body']);
				$statusCode = wp_remote_retrieve_response_code($response);

				if ($statusCode === 200 && count($body->pulses) > 0) {
					$pulses =  $body->pulses;
				} else {
					$pulses = null;
				}
			}
		}
		?>
		<script type="text/javascript">
			var pulses = <?php echo json_encode($pulses) ?>;
		</script>
		<?php
		return $plugin_array;
	}

	/**
	 * Function to add the button
	 * @param $buttons
	 *
	 * @return array
	 */
	public function pulsemotiv_register_my_tc_button($buttons) {
		$buttons[] = 'pulsemotiv_button';
		return $buttons;
	}
}
