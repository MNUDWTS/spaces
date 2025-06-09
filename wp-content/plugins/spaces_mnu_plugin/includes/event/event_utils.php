<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Spaces_MNU_Event_Utils')) {
    class Spaces_MNU_Event_Utils
    {
        public static function initialize_form_variables($post)
        {
            $floor = get_post_meta($post->ID, '_event_floor', true) ?: '';
            $images = get_post_meta($post->ID, '_event_images', true);
            $images = is_array($images) ? $images : [];
            $responsible_users = get_post_meta($post->ID, '_event_responsible', true);
            $responsible_users = is_array($responsible_users) ? $responsible_users : [];
            $access_roles = get_post_meta($post->ID, '_event_access', true);
            $access_roles = is_array($access_roles) ? $access_roles : [];
            $location = get_post_meta($post->ID, '_event_location', true) ?: '';
            $date = get_post_meta($post->ID, '_event_date', true);
            $start_time = get_post_meta($post->ID, '_event_start_time', true);
            $end_time = get_post_meta($post->ID, '_event_end_time', true);
            $public_start_time = get_post_meta($post->ID, '_event_public_start_time', true);
            $seat_count = get_post_meta($post->ID, '_event_seat_count', true) ?: '';
            $availability_enabled = get_post_meta($post->ID, '_event_availability_enabled', true) === 'true';
            $available_time = get_post_meta($post->ID, '_event_available_time', true);
            $available_time = is_array($available_time) ? $available_time : [
                'monday' => ['from' => '', 'to' => ''],
                'tuesday' => ['from' => '', 'to' => ''],
                'wednesday' => ['from' => '', 'to' => ''],
                'thursday' => ['from' => '', 'to' => ''],
                'friday' => ['from' => '', 'to' => ''],
                'saturday' => ['from' => '', 'to' => ''],
                'sunday' => ['from' => '', 'to' => '']
            ];
            $available_days = get_post_meta($post->ID, '_event_available_days', true);
            $available_days = is_array($available_days) ? $available_days : ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            $type_terms = get_terms(['taxonomy' => 'event_type', 'hide_empty' => false]);
            $selected_types = wp_get_post_terms($post->ID, 'event_type', ['fields' => 'slugs']);
            $selected_type = $selected_types[0] ?? '';
            $selected_categories = wp_get_post_terms($post->ID, 'event_category', ['fields' => 'slugs']);
            $selected_categories = is_array($selected_categories) ? $selected_categories : [];
            $roles = get_option('spaces_mnu_plugin_roles', []);
            $sorted_roles = spaces_mnu_sort_roles($roles);
            $current_user_id = $post->post_author;
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
                'location' => $location,
                'date' => $date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'public_start_time' => $public_start_time,
                'seat_count' => $seat_count,
            ];
        }
        public static function save_event_meta_fields($post_id, $post_data)
        {
            if (isset($post_data['event_location'])) {
                update_post_meta($post_id, '_event_location', sanitize_text_field($post_data['event_location']));
            }
            if (isset($post_data['event_date'])) {
                update_post_meta($post_id, '_event_date', sanitize_text_field($post_data['event_date']));
            }
            if (isset($post_data['event_start_time'])) {
                update_post_meta($post_id, '_event_start_time', sanitize_text_field($post_data['event_start_time']));
            }
            if (isset($post_data['event_end_time'])) {
                update_post_meta($post_id, '_event_end_time', sanitize_text_field($post_data['event_end_time']));
            }
            if (isset($post_data['event_public_start_time'])) {
                update_post_meta($post_id, '_event_public_start_time', sanitize_text_field($post_data['event_public_start_time']));
            }
            $event_seat_count = isset($post_data['event_seat_count']) ? $post_data['event_seat_count'] : '';
            update_post_meta(
                $post_id,
                '_event_seat_count',
                sanitize_text_field($event_seat_count)
            );
            $event_floor = isset($post_data['event_floor']) ? $post_data['event_floor'] : '';
            update_post_meta(
                $post_id,
                '_event_floor',
                sanitize_text_field($event_floor)
            );
            if (isset($post_data['event_images']) && !empty($post_data['event_images'])) {
                $image_ids = explode(',', $post_data['event_images']);
                $image_ids = array_filter(array_map('intval', $image_ids));
            } else {
                $image_ids = array();
            }
            update_post_meta($post_id, '_event_images', $image_ids);
            $responsible_users = isset($post_data['event_responsible']) ? explode(',', sanitize_text_field($post_data['event_responsible'])) : array();
            $responsible_users = array_filter(array_map('intval', $responsible_users));
            update_post_meta($post_id, '_event_responsible', $responsible_users);
            if (isset($post_data['event_access']) && !empty($post_data['event_access'])) {
                $access_roles = explode(',', $post_data['event_access']);
                $access_roles = array_filter($access_roles);
            } else {
                $access_roles = array();
            }
            $default_roles = ['security_staff', 'management_staff', 'senior_management_staff'];
            $access_roles = array_unique(array_merge($access_roles, $default_roles));
            update_post_meta($post_id, '_event_access', $access_roles);
            $availability_enabled = isset($post_data['event_availability_enabled']) && $post_data['event_availability_enabled'] === 'true' ? 'true' : 'false';
            update_post_meta(
                $post_id,
                '_event_availability_enabled',
                $availability_enabled
            );
            if (isset($post_data['event_available_time']) && is_array($post_data['event_available_time'])) {
                $available_time = array_map(function ($day) {
                    return [
                        'from' => sanitize_text_field($day['from'] ?? ''),
                        'to' => sanitize_text_field($day['to'] ?? '')
                    ];
                }, $post_data['event_available_time']);
                update_post_meta($post_id, '_event_available_time', $available_time);
            }
            if (isset($post_data['event_available_days']) && is_array($post_data['event_available_days'])) {
                $available_days = array_map('sanitize_text_field', $post_data['event_available_days']);
                update_post_meta($post_id, '_event_available_days', $available_days);
            } else {
                update_post_meta($post_id, '_event_available_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            }
            $selected_type = isset($post_data['event_type']) ? sanitize_text_field($post_data['event_type']) : '';
            if (!empty($selected_type)) {
                $type_term = get_term_by('slug', $selected_type, 'event_type');
                if ($type_term) {
                    wp_set_post_terms($post_id, [$type_term->term_id], 'event_type', false);
                }
            } else {
                wp_set_post_terms($post_id, [], 'event_type', false);
            }
            if (isset($post_data['event_categories']) && !empty($post_data['event_categories'])) {
                $selected_categories = explode(',', sanitize_text_field($post_data['event_categories']));
                $selected_categories = array_map('sanitize_text_field', $selected_categories);
            } else {
                $selected_categories = [];
            }
            $category_term_ids = [];
            foreach ($selected_categories as $category_slug) {
                $category_term = get_term_by('slug', $category_slug, 'event_category');
                if ($category_term) {
                    $category_term_ids[] = intval($category_term->term_id);
                }
            }
            if (!empty($category_term_ids)) {
                wp_set_post_terms($post_id, $category_term_ids, 'event_category', false);
            } else {
                wp_set_post_terms($post_id, [], 'event_category', false);
            }
            $locales = ['ru', 'kk', 'en'];
            foreach ($locales as $locale_key) {
                if (isset($post_data['event_name_' . $locale_key])) {
                    update_post_meta($post_id, '_event_name_' . $locale_key, sanitize_text_field($post_data['event_name_' . $locale_key]));
                } else {
                    update_post_meta($post_id, '_event_name_' . $locale_key, '');
                }
                if (isset($post_data['event_description_' . $locale_key])) {
                    update_post_meta($post_id, '_event_description_' . $locale_key, wp_kses_post($post_data['event_description_' . $locale_key]));
                } else {
                    update_post_meta($post_id, '_event_description_' . $locale_key, '');
                }
                if (isset($post_data['event_metadata_' . $locale_key . '_key']) && isset($post_data['event_metadata_' . $locale_key . '_value'])) {
                    $metadata_keys = $post_data['event_metadata_' . $locale_key . '_key'];
                    $metadata_values = $post_data['event_metadata_' . $locale_key . '_value'];
                    $metadata = array();
                    for ($i = 0; $i < count($metadata_keys); $i++) {
                        $key = sanitize_text_field($metadata_keys[$i]);
                        $value = sanitize_text_field($metadata_values[$i]);
                        if (!empty($key)) {
                            $metadata[$key] = $value;
                        }
                    }
                    update_post_meta($post_id, '_event_metadata_' . $locale_key, $metadata);
                } else {
                    delete_post_meta($post_id, '_event_metadata_' . $locale_key);
                }
            }
        }
    }
}
