<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Spaces_MNU_Event_Post_Type')) {
    class Spaces_MNU_Event_Post_Type
    {
        public function __construct()
        {
            add_action('init', array($this, 'register_event_cpt'));
            add_action('init', array($this, 'register_custom_taxonomies'));
            add_action('add_attachment', array($this, 'add_media_owner_metadata'));
            add_filter('map_meta_cap', array($this, 'restrict_media_delete_based_on_owner'), 10, 4);
        }
        public function register_event_cpt()
        {
            $labels = array(
                'name'                  => __('Events', 'spaces_mnu_plugin'),
                'singular_name'         => __('Event', 'spaces_mnu_plugin'),
                'add_new'               => __('Add New Event', 'spaces_mnu_plugin'),
                'add_new_item'          => __('Add New Event', 'spaces_mnu_plugin'),
                'edit_item'             => __('Edit Event', 'spaces_mnu_plugin'),
                'new_item'              => __('New Event', 'spaces_mnu_plugin'),
                'view_item'             => __('View Event', 'spaces_mnu_plugin'),
                'search_items'          => __('Search Events', 'spaces_mnu_plugin'),
                'not_found'             => __('No events found', 'spaces_mnu_plugin'),
                'not_found_in_trash'    => __('No events found in Trash', 'spaces_mnu_plugin'),
                'menu_name'             => __('Events', 'spaces_mnu_plugin'),
            );
            $args = array(
                'labels'             => $labels,
                'public'             => true,
                'show_in_menu'       => false,
                'capability_type'    => 'event',
                'map_meta_cap'       => true,
                'supports'           => array('title', 'author'),
                'has_archive'        => true,
                'rewrite'            => array('slug' => 'events'),
                'menu_position'      => 6,
                'publicly_queryable' => true,
                'show_in_rest'       => true,
            );
            register_post_type('event', $args);
            $this->add_event_caps();
        }
        public function register_custom_taxonomies()
        {
            $type_labels = array(
                'name'              => __('Event Types', 'spaces_mnu_plugin'),
                'singular_name'     => __('Event Type', 'spaces_mnu_plugin'),
                'search_items'      => __('Search Event Types', 'spaces_mnu_plugin'),
                'all_items'         => __('All Event Types', 'spaces_mnu_plugin'),
                'edit_item'         => __('Edit Event Type', 'spaces_mnu_plugin'),
                'update_item'       => __('Update Event Type', 'spaces_mnu_plugin'),
                'add_new_item'      => __('Add New Event Type', 'spaces_mnu_plugin'),
                'new_item_name'     => __('New Event Type Name', 'spaces_mnu_plugin'),
                'menu_name'         => __('Event Types', 'spaces_mnu_plugin'),
            );
            $type_args = array(
                'hierarchical'      => true,
                'labels'            => $type_labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'event-type'),
            );
            register_taxonomy('event_type', array('event'), $type_args);
            $category_labels = array(
                'name'              => __('Event Categories', 'spaces_mnu_plugin'),
                'singular_name'     => __('Event Category', 'spaces_mnu_plugin'),
                'search_items'      => __('Search Event Categories', 'spaces_mnu_plugin'),
                'all_items'         => __('All Event Categories', 'spaces_mnu_plugin'),
                'edit_item'         => __('Edit Event Category', 'spaces_mnu_plugin'),
                'update_item'       => __('Update Event Category', 'spaces_mnu_plugin'),
                'add_new_item'      => __('Add New Event Category', 'spaces_mnu_plugin'),
                'new_item_name'     => __('New Event Category Name', 'spaces_mnu_plugin'),
                'menu_name'         => __('Event Categories', 'spaces_mnu_plugin'),
            );
            $category_args = array(
                'hierarchical'      => true,
                'labels'            => $category_labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'event-category'),
            );
            register_taxonomy('event_category', array('event'), $category_args);
        }
        public function add_event_caps()
        {
            $roles = ['administrator', 'editor', 'author', 'subscriber'];
            $caps = [
                'edit_event',
                'read_event',
                'delete_event',
                'edit_events',
                'edit_others_events',
                'delete_events',
                'publish_events',
                'read_private_events',
                'read',
                'delete_private_events',
                'delete_published_events',
                'delete_others_events',
                'edit_private_events',
                'edit_published_events',
                'create_events',
            ];
            foreach ($roles as $the_role) {
                $role = get_role($the_role);
                if (!$role) {
                    continue;
                }
                foreach ($caps as $cap) {
                    $role->add_cap($cap);
                }
            }
        }
        public function add_media_owner_metadata($post_id)
        {
            if (get_post_type($post_id) === 'attachment') {
                $current_user_id = get_current_user_id();
                update_post_meta($post_id, '_media_owner', $current_user_id);
            }
        }
        public function restrict_media_delete_based_on_owner($caps, $cap, $user_id, $args)
        {
            if ($cap === 'delete_post') {
                $post = get_post($args[0]);
                if ($post->post_type === 'attachment') {
                    $media_owner = get_post_meta($post->ID, '_media_owner', true);
                    if ($media_owner && $media_owner != $user_id && !current_user_can('administrator')) {
                        $caps[] = 'do_not_allow';
                    }
                }
            }
            return $caps;
        }
    }
    new Spaces_MNU_Event_Post_Type();
}
