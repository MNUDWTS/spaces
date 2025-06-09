<?php

if (! defined('ABSPATH')) {
    exit;
}

class Spaces_MNU_Plugin_User_Register
{
    public function __construct()
    {
        add_action('user_register', [$this, 'set_email_from_username'], 10, 1);
        add_action('user_register', [$this, 'set_default_mnu_role_on_registration'], 10, 1);
    }

    public function set_email_from_username($user_id)
    {
        $user = get_userdata($user_id);
        if (empty($user->user_email) && is_email($user->user_login)) {
            wp_update_user([
                'ID' => $user_id,
                'user_email' => sanitize_email($user->user_login)
            ]);
        }
        // $first_name = strstr($user->user_login, '@', true);
        $first_name = explode('@', $user->user_login)[0];
        update_user_meta($user_id, 'first_name', sanitize_text_field($first_name));
    }

    public function set_default_mnu_role_on_registration($user_id)
    {
        update_user_meta($user_id, 'mnu_role', 'student');
    }
}

new Spaces_MNU_Plugin_User_Register();
