<?php
if (!defined('ABSPATH')) {
    exit;
}
require_once plugin_dir_path(__FILE__) . 'event_utils.php';
if (!class_exists('Spaces_MNU_Event_Frontend_Forms')) {
    class Spaces_MNU_Event_Frontend_Forms
    {
        public function __construct()
        {
            add_shortcode('spaces_mnu_create_event', array($this, 'create_event_shortcode'));
            add_shortcode('spaces_mnu_edit_event', array($this, 'edit_event_shortcode'));
        }
        public function create_event_shortcode($atts)
        {
            ob_start();
            $this->render_frontend_event_form($atts);
            return ob_get_clean();
        }
        public function edit_event_shortcode($atts)
        {
            ob_start();
            $atts = shortcode_atts(array(
                'event_id' => 0,
            ), $atts, 'spaces_mnu_edit_event');
            $this->render_frontend_event_form($atts);
            return ob_get_clean();
        }
        public function render_frontend_event_form($atts)
        {
            $event_id = isset($atts['event_id']) ? intval($atts['event_id']) : 0;
            if (!is_user_logged_in() || !current_user_can('publish_events')) {
                echo '<p>' . __('You do not have permission to create or edit events.', 'spaces_mnu_plugin') . '</p>';
                return;
            }
            if ($event_id > 0) {
                $post = get_post($event_id);
                if (!$post || $post->post_type !== 'event') {
                    echo '<p>' . __('Event not found.', 'spaces_mnu_plugin') . '</p>';
                    return;
                }
                if (!current_user_can('edit_post', $post->ID)) {
                    echo '<p>' . __('You do not have permission to edit this event.', 'spaces_mnu_plugin') . '</p>';
                    return;
                }
            } else {
                $post = new stdClass();
                $post->ID = 0;
                $post->post_author = get_current_user_id();
            }
            $variables = Spaces_MNU_Event_Utils::initialize_form_variables($post);
            echo '<form id="event-form" method="post" action="">';
            if ($event_id > 0) {
                echo '<input type="hidden" name="editing_event_id" value="' . esc_attr($post->ID) . '">';
            }
            include 'templates/event-static-fields-meta-box.php';
            include 'templates/event-localized-fields-meta-box.php';
            echo '<div class="tw-mt-6 tw-flex tw-items-center tw-justify-end tw-gap-x-6">
                    <button type="submit" id="submit-button" class="tw-bg-[#262626] hover:tw-bg-[#333333] tw-text-white tw-rounded-md tw-px-3 tw-py-2 tw-text-sm tw-font-semibold tw-shadow-sm tw-flex tw-items-center tw-gap-2">'
                . __('Save', 'spaces_mnu_plugin') .
                '<span class="loader tw-hidden tw-border-2 tw-border-t-transparent tw-border-white tw-rounded-full tw-w-4 tw-h-4 tw-animate-spin"></span>'
                . '</button>
                </div>';
            echo '</form>';
        }
    }
    new Spaces_MNU_Event_Frontend_Forms();
}
