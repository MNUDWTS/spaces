<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Spaces_MNU_Resource_Admin_Menu')) {
    class Spaces_MNU_Resource_Admin_Menu
    {
        public function __construct()
        {
            add_action('admin_menu', array($this, 'add_resource_menu'));
        }
        public function add_resource_menu()
        {
            add_menu_page(
                __('Resources', 'spaces_mnu_plugin'),
                __('Resources', 'spaces_mnu_plugin'),
                'edit_resources',
                'edit.php?post_type=resource',
                '',
                'dashicons-portfolio',
                1
            );
            add_submenu_page(
                'edit.php?post_type=resource',
                __('Resource Types', 'spaces_mnu_plugin'),
                __('Resource Types', 'spaces_mnu_plugin'),
                'manage_categories',
                'edit-tags.php?taxonomy=resource_type&post_type=resource'
            );
            add_submenu_page(
                'edit.php?post_type=resource',
                __('Resource Categories', 'spaces_mnu_plugin'),
                __('Resource Categories', 'spaces_mnu_plugin'),
                'manage_categories',
                'edit-tags.php?taxonomy=resource_category&post_type=resource'
            );
        }
    }
    new Spaces_MNU_Resource_Admin_Menu();
}
