<?php

function send_custom_email($to_email, $template_name, $template_data = [])
{
    if (empty($to_email)) {
        return new WP_Error('no_email', __('Email address is required.', 'spaces_mnu_plugin'));
    }

    if (empty($template_name)) {
        return new WP_Error('no_template', __('Template name is required.', 'spaces_mnu_plugin'));
    }

    $template_path = plugin_dir_path(__FILE__) . 'email-templates/' . $template_name . '.php';

    if (!file_exists($template_path)) {
        return new WP_Error('template_not_found', __('Template not found: ', 'spaces_mnu_plugin') . $template_name);
    }

    ob_start();
    extract($template_data);
    include $template_path;
    $message = ob_get_clean();

    $headers = ['Content-Type: text/html; charset=UTF-8'];

    $subject = isset($template_data['subject']) ? $template_data['subject'] : __('Notification', 'spaces_mnu_plugin');

    $result = wp_mail($to_email, $subject, $message, $headers);

    if (!$result) {
        return new WP_Error('email_failed', __('Failed to send email.', 'spaces_mnu_plugin'));
    }

    return true;
}
