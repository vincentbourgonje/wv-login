<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.webvise.nl
 * @since      1.0.0
 *
 * @package    wv_Login
 * @subpackage wv_Login/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    wv_Login
 * @subpackage wv_Login/admin
 * @author     Webvise <info@webvise.nl>
 */
class wv_Login_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;


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
		 * defined in wv_Login_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wv_Login_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wv-login-admin.css', array(), $this->version, 'all' );

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
		 * defined in wv_Login_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wv_Login_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wv-login-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Loader for the optionmenu
     */
	public function wv_login_optionmenu() {
        add_options_page( __( 'WV Login', 'wv-login' ), __( 'WV Login', 'wv-login' ), 'manage_options', 'wvlogin_options', array($this, 'wv_login_settings_page') );
    }

    /**
     * Optionmenu: Show options for this plugin
     */
    public function wv_login_settings_page() {

        if (isset($_POST["wvlogin-settings-email-submitted"]) ) {
            $form_is_submitted = $_POST["wvlogin-settings-email-submitted"];
            if ( isset( $_POST['wvlogin-settings-email-nonce'] )) {
                $verify_result = wp_verify_nonce( $_POST['wvlogin-settings-email-nonce'], 'save-wvlogin-settings');
                if ($verify_result == false) {
?>
                <div class="notice notice-error inline">
                    <p><?php _e('Error found in security key','wv-login');	?></p>
                </div>
<?php
                return;
                }
            }

            if ($form_is_submitted == "1") {
                if (isset($_POST["wvlogin-settings-roles"])) { $setting_roles = $_POST["wvlogin-settings-roles"]; } else { $setting_roles = ""; }
                if (isset($_POST["wvlogin-settings-email-sender"])) { $setting_name = $_POST["wvlogin-settings-email-sender"]; } else { $setting_name = ""; }
                if (isset($_POST["wvlogin-settings-email-emailaddress"])) { $setting_email = $_POST["wvlogin-settings-email-emailaddress"]; } else { $setting_email = ""; }
                if (isset($_POST["wvlogin-settings-email-subject"])) { $setting_subject = $_POST["wvlogin-settings-email-subject"]; } else { $setting_subject = ""; }
                if (isset($_POST["wvlogin-settings-email-text"])) { $setting_text = $_POST["wvlogin-settings-email-text"]; } else { $setting_text = ""; }
                if (isset($_POST["wvlogin-settings-redirect-to"])) { $setting_redirect_url = $_POST["wvlogin-settings-redirect-to"]; } else { $setting_redirect_url = ""; }

                // update the options
                update_option('wvlogin-settings-roles', $setting_roles);
                update_option('wvlogin-settings-email-sender', $setting_name);
                update_option('wvlogin-settings-email-emailaddress', $setting_email);
                update_option('wvlogin-settings-email-subject', $setting_subject);
                update_option('wvlogin-settings-email-text', html_entity_decode(stripslashes($setting_text)));
                update_option('wvlogin-settings-redirect-to', $setting_redirect_url);
            }
        }

        $setting_roles = get_option('wvlogin-settings-roles');
        $setting_name = get_option('wvlogin-settings-email-sender');
        $setting_email = get_option('wvlogin-settings-email-emailaddress');
        $setting_subject = get_option('wvlogin-settings-email-subject');
        $setting_text = html_entity_decode(stripslashes(get_option('wvlogin-settings-email-text')));
        $setting_redirect_url = get_option('wvlogin-settings-redirect-to');
        ?>
        <div class="wrap">
            <h1><?php _e("Webvise Login settings","wv-login"); ?></h1>
            <?php wp_nonce_field( 'save-wvlogin-settings', 'wvlogin-settings-email-nonce' ); ?>
            <form method="post" action="/wp-admin/options-general.php?page=wvlogin_options">
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="wvlogin-settings-roles"><?php esc_attr_e("Only the following roles can login per link (seperate roles with comma)","wv-login"); ?></label>
                    </th>
                    <td>
                        <input name="wvlogin-settings-roles" type="text" id="wvlogin-settings-roles" class="regular-text" value="<?php echo $setting_roles; ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="wvlogin-settings-email-sender"><?php esc_attr_e("Email Sender Name","wv-login"); ?></label>
                    </th>
                    <td>
                        <input name="wvlogin-settings-email-sender" type="text" id="wvlogin-settings-email-sender" class="regular-text" value="<?php echo $setting_name; ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="wvlogin-settings-email-emailaddress"><?php esc_attr_e("Email Sender emailaddress","wv-login"); ?></label>
                    </th>
                    <td>
                        <input name="wvlogin-settings-email-emailaddress" type="text" id="wvlogin-settings-email-emailaddress" class="regular-text" value="<?php echo $setting_email; ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="wvlogin-settings-email-subject"><?php esc_attr_e("Email Subject","wv-login"); ?></label>
                    </th>
                    <td>
                        <input name="wvlogin-settings-email-subject" type="text" id="wvlogin-settings-email-subject" class="regular-text" value="<?php echo $setting_subject; ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="wvlogin-settings-email-text"><?php esc_attr_e("Email Text (use #LINK# for the generated link)","wv-login"); ?></label>
                    </th>
                    <td>
                        <textarea name="wvlogin-settings-email-text" type="text" id="wvlogin-settings-email-text" class="large-text" style="height:350px;"><?php echo $setting_text; ?></textarea>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="wvlogin-settings-redirect-to"><?php esc_attr_e("Redirect to url after login (enter the full url)","wv-login"); ?></label>
                    </th>
                    <td>
                        <input name="wvlogin-settings-redirect-to" type="url" id="wvlogin-settings-redirect-to" class="regular-text" value="<?php echo $setting_redirect_url; ?>">
                    </td>
                </tr>

            </table>
            <input type="hidden" value="1" name="wvlogin-settings-email-submitted" id="wvlogin-settings-email-submitted">
            <input class="button-primary" type="submit" name="wvlogin-settings-email-submit" id="wvlogin-settings-email-submit" value="<?php esc_attr_e( 'Save changes', 'wv-login' ); ?>">
            </form>
        </div>
        <?php
    }

}
