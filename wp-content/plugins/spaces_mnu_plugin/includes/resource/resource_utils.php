<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Spaces_MNU_Resource_Utils')) {
    class Spaces_MNU_Resource_Utils
    {
        public static function initialize_form_variables($post)
        {
            // Get existing values
            $floor = get_post_meta($post->ID, '_resource_floor', true) ?: ''; // Default to empty string if not set
            $images = get_post_meta($post->ID, '_resource_images', true);
            $images = is_array($images) ? $images : []; // Default to empty array if not valid
            $responsible_users = get_post_meta($post->ID, '_resource_responsible', true);
            $responsible_users = is_array($responsible_users) ? $responsible_users : [];
            $access_roles = get_post_meta($post->ID, '_resource_access', true);
            $access_roles = is_array($access_roles) ? $access_roles : [];

            // Availability settings
            $availability_enabled = get_post_meta($post->ID, '_resource_availability_enabled', true) === 'true';
            $available_time = get_post_meta($post->ID, '_resource_available_time', true);
            $available_time = is_array($available_time) ? $available_time : [
                'monday' => ['from' => '', 'to' => ''],
                'tuesday' => ['from' => '', 'to' => ''],
                'wednesday' => ['from' => '', 'to' => ''],
                'thursday' => ['from' => '', 'to' => ''],
                'friday' => ['from' => '', 'to' => ''],
                'saturday' => ['from' => '', 'to' => ''],
                'sunday' => ['from' => '', 'to' => '']
            ];
            $available_days = get_post_meta($post->ID, '_resource_available_days', true);
            $available_days = is_array($available_days) ? $available_days : ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

            // Taxonomies
            $type_terms = get_terms(['taxonomy' => 'resource_type', 'hide_empty' => false]);

            // Selected type
            $selected_types = wp_get_post_terms($post->ID, 'resource_type', ['fields' => 'slugs']);
            $selected_type = $selected_types[0] ?? '';

            // Selected categories
            $selected_categories = wp_get_post_terms($post->ID, 'resource_category', ['fields' => 'slugs']);
            $selected_categories = is_array($selected_categories) ? $selected_categories : [];

            // Roles
            $roles = get_option('spaces_mnu_plugin_roles', []);
            $sorted_roles = spaces_mnu_sort_roles($roles);

            // Current user
            $current_user_id = $post->post_author;

            // Ensure default categories are included
            if (empty($selected_categories)) {
                $selected_categories = ['all'];
                if ($selected_type == 'space') {
                    $selected_categories[] = 'all-space';
                } elseif ($selected_type == 'equipment') {
                    $selected_categories[] = 'all-equipment';
                }
            } else {
                if (!in_array('all', $selected_categories)) {
                    array_unshift($selected_categories, 'all');
                }
                if ($selected_type == 'space' && !in_array('all-space', $selected_categories)) {
                    $selected_categories[] = 'all-space';
                } elseif ($selected_type == 'equipment' && !in_array('all-equipment', $selected_categories)) {
                    $selected_categories[] = 'all-equipment';
                }
            }

            // Return initialized data
            return [
                'floor' => $floor,
                'images' => $images,
                'responsible_users' => $responsible_users,
                'access_roles' => $access_roles,
                'availability_enabled' => $availability_enabled,
                'available_time' => $available_time,
                'available_days' => $available_days,
                'type_terms' => $type_terms,
                'selected_type' => $selected_type,
                'selected_categories' => $selected_categories,
                'roles' => $roles,
                'sorted_roles' => $sorted_roles,
                'current_user_id' => $current_user_id,
            ];
        }


        public static function save_resource_meta_fields($post_id, $post_data)
        {
            // Save floor
            // if (!empty($post_data['resource_floor']) || $post_data['resource_floor'] === '0') {
            //     update_post_meta($post_id, '_resource_floor', sanitize_text_field($post_data['resource_floor']));
            // } else {
            //     update_post_meta($post_id, '_resource_floor', '');
            // }
            $resource_floor = isset($post_data['resource_floor']) ? $post_data['resource_floor'] : '';

            // Сохраняем значение
            update_post_meta($post_id, '_resource_floor', sanitize_text_field($resource_floor));

            // Save images
            if (isset($post_data['resource_images']) && !empty($post_data['resource_images'])) {
                $image_ids = explode(',', $post_data['resource_images']);
                $image_ids = array_filter(array_map('intval', $image_ids));
            } else {
                $image_ids = array();
            }
            update_post_meta($post_id, '_resource_images', $image_ids);

            // Save responsible users
            $responsible_users = isset($post_data['resource_responsible']) ? explode(',', sanitize_text_field($post_data['resource_responsible'])) : array();
            $responsible_users = array_filter(array_map('intval', $responsible_users));
            update_post_meta($post_id, '_resource_responsible', $responsible_users);

            // Save access roles
            if (isset($post_data['resource_access']) && !empty($post_data['resource_access'])) {
                $access_roles = explode(',', $post_data['resource_access']);
                $access_roles = array_filter($access_roles);
            } else {
                $access_roles = array();
            }
            $default_roles = ['security_staff', 'management_staff', 'senior_management_staff'];
            $access_roles = array_unique(array_merge($access_roles, $default_roles));
            update_post_meta($post_id, '_resource_access', $access_roles);

            // Save availability settings
            $availability_enabled = isset($post_data['resource_availability_enabled']) && $post_data['resource_availability_enabled'] === 'true' ? 'true' : 'false';
            update_post_meta(
                $post_id,
                '_resource_availability_enabled',
                $availability_enabled
            );

            if (isset($post_data['resource_available_time']) && is_array($post_data['resource_available_time'])) {
                $available_time = array_map(function ($day) {
                    return [
                        'from' => sanitize_text_field($day['from'] ?? ''),
                        'to' => sanitize_text_field($day['to'] ?? '')
                    ];
                }, $post_data['resource_available_time']);
                update_post_meta($post_id, '_resource_available_time', $available_time);
            }

            if (isset($post_data['resource_available_days']) && is_array($post_data['resource_available_days'])) {
                $available_days = array_map('sanitize_text_field', $post_data['resource_available_days']);
                update_post_meta($post_id, '_resource_available_days', $available_days);
            } else {
                update_post_meta($post_id, '_resource_available_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            }

            // Save taxonomies
            // Save selected type
            $selected_type = isset($post_data['resource_type']) ? sanitize_text_field($post_data['resource_type']) : '';

            if (!empty($selected_type)) {
                $type_term = get_term_by('slug', $selected_type, 'resource_type');
                if ($type_term) {
                    wp_set_post_terms($post_id, [$type_term->term_id], 'resource_type', false);
                }
            } else {
                wp_set_post_terms($post_id, [], 'resource_type', false);
            }

            // Save selected categories
            if (isset($post_data['resource_categories']) && !empty($post_data['resource_categories'])) {
                $selected_categories = explode(',', sanitize_text_field($post_data['resource_categories']));
                $selected_categories = array_map('sanitize_text_field', $selected_categories);
            } else {
                $selected_categories = [];
            }

            // Convert category slugs to term IDs
            $category_term_ids = [];
            foreach ($selected_categories as $category_slug) {
                $category_term = get_term_by('slug', $category_slug, 'resource_category');
                if ($category_term) {
                    $category_term_ids[] = intval($category_term->term_id);
                }
            }

            // Save categories
            if (!empty($category_term_ids)) {
                wp_set_post_terms($post_id, $category_term_ids, 'resource_category', false);
            } else {
                wp_set_post_terms($post_id, [], 'resource_category', false);
            }

            // Save localized data
            $locales = ['ru', 'kk', 'en'];
            foreach ($locales as $locale_key) {
                // Save name
                if (isset($post_data['resource_name_' . $locale_key])) {
                    update_post_meta($post_id, '_resource_name_' . $locale_key, sanitize_text_field($post_data['resource_name_' . $locale_key]));
                } else {
                    update_post_meta($post_id, '_resource_name_' . $locale_key, '');
                }

                // Save description
                if (isset($post_data['resource_description_' . $locale_key])) {
                    update_post_meta($post_id, '_resource_description_' . $locale_key, wp_kses_post($post_data['resource_description_' . $locale_key]));
                } else {
                    update_post_meta($post_id, '_resource_description_' . $locale_key, '');
                }

                // Save metadata
                if (isset($post_data['resource_metadata_' . $locale_key . '_key']) && isset($post_data['resource_metadata_' . $locale_key . '_value'])) {
                    $metadata_keys = $post_data['resource_metadata_' . $locale_key . '_key'];
                    $metadata_values = $post_data['resource_metadata_' . $locale_key . '_value'];
                    $metadata = array();
                    for ($i = 0; $i < count($metadata_keys); $i++) {
                        $key = sanitize_text_field($metadata_keys[$i]);
                        $value = sanitize_text_field($metadata_values[$i]);
                        if (!empty($key)) {
                            $metadata[$key] = $value;
                        }
                    }
                    update_post_meta($post_id, '_resource_metadata_' . $locale_key, $metadata);
                } else {
                    delete_post_meta($post_id, '_resource_metadata_' . $locale_key);
                }
            }
        }
    }
}
