<?php
    // include Wordpress
    require('../../../wp-load.php');

    global $wpdb;

    $token_id = $_GET["tokenid"];
    $setting_redirect_url = get_option('wvlogin-settings-redirect-to');
    $now_date = date_i18n("Y-m-d H:i:s.u");

    $tablename = $wpdb->prefix . 'usermeta';
    $sql = "SELECT user_id FROM $tablename WHERE meta_value='$token_id' AND meta_key='wvlogintoken'";
    $user_id = $wpdb->get_var($sql);

    $token_valid_until = get_user_meta($user_id, 'wvlogindate');

    if ( !is_user_logged_in() && $now_date <= $token_valid_until ) {
        clean_user_cache($user_id);
        wp_clear_auth_cookie();
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true, false);

        $user = get_user_by('id', $user_id);
        update_user_caches($user);
        update_user_meta($user_id, 'wvlogindate', '');
        update_user_meta($user_id, 'wvlogintoken', '');
        wp_redirect($setting_redirect_url);
    } else {
        echo '<p>' . __('Invalid login link!','wv-login') . '</p>';
        if ($now_date > $token_valid_until) {
            echo '<p>' . __('Token has expired!','wv-login') . '</p>';
        }

    }
