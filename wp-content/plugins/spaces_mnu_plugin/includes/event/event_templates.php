<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Spaces_MNU_Event_Templates')) {
    class Spaces_MNU_Event_Templates
    {
        public function __construct()
        {
            add_filter('template_include', array($this, 'add_event_single_template'), 99);
            add_filter('template_include', array($this, 'add_event_archive_template'), 99);
            add_action('init', array($this, 'flush_rewrite_rules_on_activation'));
        }
        public function add_custom_rewrite_rules()
        {
            add_rewrite_rule('^([a-z]{2})/events/([^/]+)/?$', 'index.php?post_type=event&name=$matches[2]', 'top');
            add_rewrite_rule('^([a-z]{2})/events/?$', 'index.php?post_type=event', 'top');
        }
        public function add_event_single_template($template)
        {
            $post = get_post();
            if ($post && $post->post_type === 'event') {
                $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-event.php';
                if (file_exists($plugin_template)) {
                    return $plugin_template;
                }
            }
            return $template;
        }

        public function add_event_archive_template($template)
        {
            if (is_post_type_archive('event')) {
                $plugin_template = plugin_dir_path(__FILE__) . 'templates/archive-event.php';
                if (file_exists($plugin_template)) {
                    return $plugin_template;
                }
            }
            return $template;
        }
        public function flush_rewrite_rules_on_activation()
        {
            flush_rewrite_rules();
        }
    }
    new Spaces_MNU_Event_Templates();
}
