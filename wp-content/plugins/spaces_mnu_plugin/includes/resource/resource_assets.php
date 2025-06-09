<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Spaces_MNU_Resource_Assets')) {
    class Spaces_MNU_Resource_Assets
    {
        public function __construct()
        {
            add_action('wp_enqueue_scripts', array($this, 'spaces_mnu_enqueue_scripts'), 20);
            add_action('admin_enqueue_scripts', array($this, 'spaces_mnu_enqueue_admin_scripts'), 20);
        }
        public function spaces_mnu_enqueue_scripts()
        {
            $this->enqueue_common_scripts();
        }
        public function spaces_mnu_enqueue_admin_scripts($hook_suffix)
        {
            global $typenow;
            if ($typenow === 'resource' && ($hook_suffix === 'post.php' || $hook_suffix === 'post-new.php')) {
                $this->enqueue_common_scripts();
            }
        }
        private function enqueue_common_scripts()
        {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_media();
            wp_enqueue_script(
                'spaces-mnu-plugin-js',
                SPACES_MNU_PLUGIN_URL . 'assets/js/resource.js',
                array('jquery', 'jquery-ui-autocomplete', 'jquery-ui-tabs'),
                SPACES_MNU_PLUGIN_VERSION,
                true
            );
            wp_localize_script('spaces-mnu-plugin-js', 'spacesMnuPlugin', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => array(
                    'saveForm' => wp_create_nonce('save_resource_meta_boxes'),
                    'editForm' => wp_create_nonce('get_resource_edit_form'),
                    'createForm' => wp_create_nonce('load_resource_create_form')
                ),
                'i18n'    => array(
                    'keyPlaceholder' => __('Key', 'spaces_mnu_plugin'),
                    'valuePlaceholder' => __('Value', 'spaces_mnu_plugin'),
                    'remove' => __('Remove', 'spaces_mnu_plugin'),
                    'all' => __('All', 'spaces_mnu_plugin'),
                    'allSpaces' => __('All Spaces', 'spaces_mnu_plugin'),
                    'allEquipment' => __('All Equipment', 'spaces_mnu_plugin'),
                    'addImages' => __('Add Images', 'spaces_mnu_plugin'),
                    'errorOccurred' => __('An error occurred.', 'spaces_mnu_plugin'),
                    'errorLoadingForm' => __('Failed to load the form.', 'spaces_mnu_plugin'),
                    'selectImages' => __('Select Images', 'spaces_mnu_plugin'),
                    'addToGallery' => __('Add to gallery', 'spaces_mnu_plugin'),
                    'deleteResource' => __('Delete Resource', 'spaces_mnu_plugin'),
                    'deleteWithBookings' => __('There are bookings on this resource:', 'spaces_mnu_plugin'),
                    'confirmDeleteWithBookings' => __('Are you sure you want to delete the resource and cancel all bookings?', 'spaces_mnu_plugin'),
                    'confirmDelete' => __('Are you sure you want to delete this resource?', 'spaces_mnu_plugin'),
                    'errorCheckingBookings' => __('Error checking bookings.', 'spaces_mnu_plugin'),
                    'bookingId' => __('Booking ID:', 'spaces_mnu_plugin'),
                    'date' => __('Date:', 'spaces_mnu_plugin'),
                    'user' => __('User:', 'spaces_mnu_plugin')
                ),
            ));
            wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        }
    }

    new Spaces_MNU_Resource_Assets();
}
