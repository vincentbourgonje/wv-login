<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.webvise.nl
 * @since      1.0.0
 *
 * @package    wv_Login
 * @subpackage wv_Login/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    wv_Login
 * @subpackage wv_Login/public
 * @author     Webvise <info@webvise.nl>
 */
class wv_Login_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wv-login-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wv-login-public.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Declare custom shortcodes
     */
	public function wv_login_shortcodes() {
	    add_shortcode('WVLOGIN', array($this, 'wv_login_wvlogin_sc'));
    }

    /**
     * Process shortcode:
     * Show the alternative Login screen where people only enter their e-mailaddress
     * @param $atts
     * @return string
     */
    public function wv_login_wvlogin_sc($atts) {

        extract(shortcode_atts(array(
            'screen' => '',
        ), $atts));

        $return_html = $this->wv_login_wvlogin_fn($screen);

        return $return_html;
    }

    /**
     * Generate the alternative Login screen where people only enter their e-mailaddress
     * @param $screen
     * @return string
     */
    public function wv_login_wvlogin_fn($screen) {
        $return_html = '';
        $form_error = false;
        $form_error_msg = '';
        $showloginform = false;
        $process_complete = false;
        $user_email = '';

        // check if the form was submitted
        if (isset($_POST["wv-login-is-submitted"]) && $_POST["wv-login-is-submitted"] == '1') {
            // check the nonce
            $verify_result = wp_verify_nonce( $_POST['wvlogin-loginform-email-nonce'], 'save-wvlogin-loginform');
            if ($verify_result === false) {
                return __('An error occured when submitting the form','wv-login');
            }

            // everything ok? process the form
            // step 1: check if the user exists and if the userrole allows login per email
            $user_email = sanitize_email($_POST["wv-login-emailaddress"]);

            if ($user_email !== false) {
                $user = get_user_by('email', $user_email);

                if ($user !== false) {
                    // echo 'User is ' . $user->first_name . ' ' . $user->last_name;
                    $user_roles = $user->roles;

                    // get allowed roles
                    $setting_roles = get_option('wvlogin-settings-roles');
                    $setting_roles_array = explode(',', $setting_roles);

                    // all roles assigned to the user should be declared in the pluginsettings
                    // otherwise login per emaillink is not possible for this user -
                    $user_can_use_plugin = $this->wv_array_is_in_array($user_roles, $setting_roles_array);

                    if ($user_can_use_plugin) {

                        // Generate the secret login token for this user
                        $currentdate = new DateTime(date_i18n("Y-m-d H:i:s.u"));
                        $currentdate->add(new DateInterval('PT2H'));

                        $hashinput = $user->ID . date_i18n('YmdHis') . $user->first_name . $user->last_name;
                        $login_token = hash('md5', $hashinput, false);

                        // Write login info in profile
                        update_user_meta($user->ID, 'wvlogintoken', $login_token);
                        update_user_meta($user->ID, 'wvlogindate', $currentdate->format("Y-m-d H:i:s.u"));

                        // Generate login link
                        $login_link = WV_LOGIN_PLUGIN_URL . 'process.php?tokenid=' . $login_token;

                        // Check if the current url contains a redirect_to parameter, if so we will add it to our url
                        if (isset($_GET["redirect_to"])) {
                            $redirect_url = $_GET["redirect_to"];
                            if (strpos($redirect_url,'https://')>0 || strpos($redirect_url,'http://')>0) {
                                // assuming that the url is already encoded
                                $login_link = $login_link . '&redirect_to=' . $redirect_url;
                            }
                        }

                        // Send the E-mail
                        $setting_name = get_option('wvlogin-settings-email-sender');
                        $setting_email = get_option('wvlogin-settings-email-emailaddress');
                        $setting_subject = get_option('wvlogin-settings-email-subject');
                        $setting_text = get_option('wvlogin-settings-email-text');
                        $mailto = $user->first_name . ' ' . $user->last_name . ' <' . $user->user_email . '>';
                        $mailfrom = $setting_name . ' <' . $setting_email . '>';
                        $mailheaders[] = 'From: ' . $mailfrom;
                        $mailheaders[] = 'Content-Type: text/html; charset=UTF-8';

                        // recplace vars in the template
                        $setting_text = str_replace('#LINK#', $login_link, $setting_text);
                        $setting_text = str_replace('#FIRSTNAME#', $user->first_name, $setting_text);
                        $setting_text = str_replace('#LASTNAME#', $user->last_name, $setting_text);
                        $setting_text = str_replace('#SENDNAME#', $setting_name, $setting_text);

                        $sendmailresult = wp_mail(
                            $mailto,
                            $setting_subject,
                            html_entity_decode(stripslashes($setting_text)),
                            $mailheaders
                        );

                        if($sendmailresult === true) {
                            $process_complete = true;
                            $showloginform = true;
                        } else {
                            $form_error_msg .= __('There was a problem with sending emails, the system administrator has been notified','wv-login');
                            $process_complete = true;
                            $form_error = true;
                        }


                    } else {
                        $form_error = true;
                        $form_error_msg .= '003 ' . __('Invalid Emailaddress', 'wv-login');
                        $showloginform = true;
                    }
                } else {
                    $form_error = true;
                    $form_error_msg .= '002 ' . __('Invalid Emailaddress', 'wv-login');
                    $showloginform = true;
                }
            } else {
                $form_error = true;
                $form_error_msg .= '001 ' . __('Invalid Emailaddress', 'wv-login');
                $showloginform = true;
            }


        } else {
            $showloginform = true;
        }

        // Login form
        if ($showloginform) {
            $return_html .= '<div class="wvlogin-form">';
            $return_html .= '<h2>' . __('Magic login with e-mail', 'wv-login') . '</h2>';

            // show error messages if there are any
            if($form_error) {
                $return_html .= '<div class="wvlogin-form-error">';
                $return_html .= '<p>' . $form_error_msg . '</p>';
                $return_html .= '</div>';
            }

            // show form or completion message
            if (!$process_complete) {
                $return_html .= '<form method="post" action="">';
                $return_html .= wp_nonce_field('save-wvlogin-loginform', 'wvlogin-loginform-email-nonce');
                $return_html .= '<p>' . __('Quick login without password? Please enter your e-mail address below, if you have a valid account you will receive an e-mail with a link to log in immediately.', 'wv-login') . '</p>';
                //$return_html .= '<label for="wv-login-emailaddress">' . __('Emailaddress','wv-login') . '</label>';
                $return_html .= '<input name="wv-login-emailaddress" id="wv-login-emailaddress" type="email" placeholder="' . __('E-Mailaddress', 'wv-login') . '" value="' . $user_email . '">';
                $return_html .= '<input type="hidden" value="1" name="wv-login-is-submitted">';
                $return_html .= '<input type="submit" name="wv-login-sumbit" id="wv-login-submit" value="' . __('Get login link','wv-login') . '">';
                $return_html .= '</form>';
            } else {
                $return_html .= '<p class="wvlogin-form-success">' . __('If your Emailaddress has been found in our system you will receive an e-mail with your magic login-link shortly. Check also your Spam / Junkmail folder when you do not receive it.','wv-login') . '</p>';
            }
            $return_html .= '</div>';
        }

        return $return_html;
    }

    /**
     * Every element of the first array should exist in the second array
     * if not found an element from the first in the second the function returns false
     * @param $first_arr
     * @param $second_arr
     * @return bool
     */
    private function wv_array_is_in_array($first_arr, $second_arr) {
        $found_elm = false;

        foreach($first_arr as $elm) {
            $elm_is_found = array_search($elm, $second_arr);
            if ($elm_is_found === false) { return false; } else { $found_elm = true; }
        }

        return $found_elm;
    }

}
