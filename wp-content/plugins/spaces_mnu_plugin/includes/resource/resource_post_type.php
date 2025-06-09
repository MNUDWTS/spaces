<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Spaces_MNU_Resource_Post_Type')) {
    class Spaces_MNU_Resource_Post_Type
    {
        public function __construct()
        {
            add_action('init', array($this, 'register_resource_cpt'));
            add_action('init', array($this, 'register_custom_taxonomies'));
            add_action('add_attachment', array($this, 'add_media_owner_metadata'));
            add_filter('map_meta_cap', array($this, 'restrict_media_delete_based_on_owner'), 10, 4);
        }

        public function register_resource_cpt()
        {
            $labels = array(
                'name'                  => __('Resources', 'spaces_mnu_plugin'),
                'singular_name'         => __('Resource', 'spaces_mnu_plugin'),
                'add_new'               => __('Add New Resource', 'spaces_mnu_plugin'),
                'add_new_item'          => __('Add New Resource', 'spaces_mnu_plugin'),
                'edit_item'             => __('Edit Resource', 'spaces_mnu_plugin'),
                'new_item'              => __('New Resource', 'spaces_mnu_plugin'),
                'view_item'             => __('View Resource', 'spaces_mnu_plugin'),
                'search_items'          => __('Search Resources', 'spaces_mnu_plugin'),
                'not_found'             => __('No resources found', 'spaces_mnu_plugin'),
                'not_found_in_trash'    => __('No resources found in Trash', 'spaces_mnu_plugin'),
                'menu_name'             => __('Resources', 'spaces_mnu_plugin'),
            );

            $args = array(
                'labels'             => $labels,
                'public'             => true,
                'show_in_menu'       => false,
                'capability_type'    => 'resource',
                'map_meta_cap'       => true,
                'supports' => array('title', 'author'),
                'has_archive'        => true,
                'rewrite'            => array('slug' => 'resources'),
                'menu_position'      => 5,
                'publicly_queryable' => true,
                'show_in_rest'       => true,
            );

            register_post_type('resource', $args);
            $this->add_resource_caps();
        }

        public function register_custom_taxonomies()
        {
            $type_labels = array(
                'name'              => __('Resource Types', 'spaces_mnu_plugin'),
                'singular_name'     => __('Resource Type', 'spaces_mnu_plugin'),
                'search_items'      => __('Search Resource Types', 'spaces_mnu_plugin'),
                'all_items'         => __('All Resource Types', 'spaces_mnu_plugin'),
                'edit_item'         => __('Edit Resource Type', 'spaces_mnu_plugin'),
                'update_item'       => __('Update Resource Type', 'spaces_mnu_plugin'),
                'add_new_item'      => __('Add New Resource Type', 'spaces_mnu_plugin'),
                'new_item_name'     => __('New Resource Type Name', 'spaces_mnu_plugin'),
                'menu_name'         => __('Resource Types', 'spaces_mnu_plugin'),
            );

            $type_args = array(
                'hierarchical'      => true,
                'labels'            => $type_labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'resource-type'),
            );

            register_taxonomy('resource_type', array('resource'), $type_args);

            $category_labels = array(
                'name'              => __('Resource Categories', 'spaces_mnu_plugin'),
                'singular_name'     => __('Resource Category', 'spaces_mnu_plugin'),
                'search_items'      => __('Search Resource Categories', 'spaces_mnu_plugin'),
                'all_items'         => __('All Resource Categories', 'spaces_mnu_plugin'),
                'edit_item'         => __('Edit Resource Category', 'spaces_mnu_plugin'),
                'update_item'       => __('Update Resource Category', 'spaces_mnu_plugin'),
                'add_new_item'      => __('Add New Resource Category', 'spaces_mnu_plugin'),
                'new_item_name'     => __('New Resource Category Name', 'spaces_mnu_plugin'),
                'menu_name'         => __('Resource Categories', 'spaces_mnu_plugin'),
            );

            $category_args = array(
                'hierarchical'      => true,
                'labels'            => $category_labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'resource-category'),
            );

            register_taxonomy('resource_category', array('resource'), $category_args);
        }

        public function add_resource_caps()
        {
            $roles = ['administrator', 'editor', 'author', 'subscriber'];
            $caps = [
                'edit_resource',
                'read_resource',
                'delete_resource',
                'edit_resources',
                'edit_others_resources',
                'delete_resources',
                'publish_resources',
                'read_private_resources',
                'read',
                'delete_private_resources',
                'delete_published_resources',
                'delete_others_resources',
                'edit_private_resources',
                'edit_published_resources',
                'create_resources',
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

    new Spaces_MNU_Resource_Post_Type();
}
