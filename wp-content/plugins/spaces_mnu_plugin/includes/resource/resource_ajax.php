<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Spaces_MNU_Resource_AJAX')) {
    class Spaces_MNU_Resource_AJAX
    {
        public function __construct()
        {
            add_action('init', array($this, 'register_ajax_actions'));
        }
        function filter_categories_by_type()
        {
            $selected_type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
            if (!$selected_type) {
                wp_send_json_error('No type provided.');
                wp_die();
            }
            $suffix = '';
            $taxonomy = '';
            if ($selected_type == 'space') {
                $suffix = '-space';
                $taxonomy = 'resource_category';
            } elseif ($selected_type == 'equipment') {
                $suffix = '-equipment';
                $taxonomy = 'resource_category';
            } elseif ($selected_type == 'event') {
                $suffix = '-event';
                $taxonomy = 'event_category';
            }
            if (!$taxonomy) {
                wp_send_json_error('Invalid type provided.');
                wp_die();
            }
            $all_categories = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ]);
            $categories = [];
            foreach ($all_categories as $category) {
                if ($taxonomy === 'resource_category' && str_ends_with($category->slug, $suffix)) {
                    $categories[] = [
                        'slug' => $category->slug,
                        'name' => __($category->name, 'spaces_mnu_plugin'),
                    ];
                } elseif ($taxonomy === 'event_category') {
                    $categories[] = [
                        'slug' => $category->slug,
                        'name' => __($category->name, 'spaces_mnu_plugin'),
                    ];
                }
            }
            wp_send_json_success(['categories' => $categories]);
            wp_die();
        }
        public function filter_resources()
        {
            $selected_type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
            $search_query = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
            $selected_category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
            $selected_floor = isset($_POST['floor']) ? sanitize_text_field($_POST['floor']) : '';
            $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
            $current_user_id = get_current_user_id();
            if ($selected_type === 'event') {
                $args = [
                    'post_type' => 'event',
                    'posts_per_page' => 12,
                    'paged' => $paged,
                    's' => $search_query,
                    'tax_query' => [],
                    'meta_query' => [
                        [
                            'relation' => 'OR',
                            [
                                'key' => '_event_availability_enabled',
                                'value' => 'true',
                                'compare' => '='
                            ],
                            [
                                'key' => '_event_availability_enabled',
                                'value' => true,
                                'compare' => '='
                            ]
                        ],
                        [
                            'key' => '_event_date',
                            'value' => current_time('Y-m-d'),
                            'compare' => '>=',
                            'type' => 'DATE',
                        ],
                    ]
                ];
                if (!empty($selected_category)) {
                    $args['tax_query'] = [
                        [
                            'taxonomy' => 'event_category',
                            'field' => 'slug',
                            'terms' => explode(',', $selected_category),
                            'operator' => 'IN',
                        ]
                    ];
                }
            } else {
                $args = [
                    'post_type' => 'resource',
                    'posts_per_page' => 12,
                    'paged' => $paged,
                    's' => $search_query,
                    'tax_query' => [],
                    'meta_query' => [
                        [
                            'relation' => 'OR',
                            [
                                'key' => '_resource_availability_enabled',
                                'value' => 'true',
                                'compare' => '='
                            ],
                            [
                                'key' => '_resource_availability_enabled',
                                'value' => true,
                                'compare' => '='
                            ],
                        ],
                    ],
                ];
                if (!empty($selected_type) && $selected_type !== 'all') {
                    $args['tax_query'][] = [
                        'taxonomy' => 'resource_type',
                        'field' => 'slug',
                        'terms' => $selected_type,
                    ];
                }
                if (!empty($selected_category)) {
                    $args['tax_query'][] = [
                        'taxonomy' => 'resource_category',
                        'field' => 'slug',
                        'terms' => explode(',', $selected_category),
                        'operator' => 'IN',
                    ];
                }
                if (is_numeric($selected_floor)) {
                    $args['meta_query'][] = [
                        'key' => '_resource_floor',
                        'value' => $selected_floor,
                        'compare' => '='
                    ];
                }
            }
            $post_query = new WP_Query($args);
            $posts = [];
            if ($post_query->have_posts()) {
                while ($post_query->have_posts()) {
                    $post_query->the_post();
                    $post_id = get_the_ID();
                    if ($selected_type == 'event') {
                        $event_access = get_post_meta($post_id, '_event_access', true);
                        if (check_access($current_user_id, $event_access)) {
                            $post_name_ru = get_post_meta($post_id, '_event_name_ru', true);
                            $post_name_kk = get_post_meta($post_id, '_event_name_kk', true);
                            $post_name_en = get_post_meta($post_id, '_event_name_en', true);
                            $post_description_ru = get_post_meta($post_id, '_event_description_ru', true);
                            $post_description_kk = get_post_meta($post_id, '_event_description_kk', true);
                            $post_description_en = get_post_meta($post_id, '_event_description_en', true);
                            $post_images = get_post_meta($post_id, '_event_images', true);
                            $image_url = (!empty($post_images) && is_array($post_images))
                                ? wp_get_attachment_url($post_images[0])
                                : SPACES_MNU_PLUGIN_URL . 'assets/img/image_placeholder.png';
                            $posts[] = [
                                'name' => [
                                    'ru' => $post_name_ru,
                                    'kk' => $post_name_kk,
                                    'en' => $post_name_en,
                                ],
                                'description' => [
                                    'ru' => $post_description_ru,
                                    'kk' => $post_description_kk,
                                    'en' => $post_description_en,
                                ],
                                'image' => $image_url,
                                'link' => get_permalink(),
                            ];
                        }
                    } else {
                        // $resource_access = get_post_meta($post_id, '_resource_access', true);
                        // if (check_access($current_user_id, $resource_access)) {
                        $post_name_ru = get_post_meta($post_id, '_resource_name_ru', true);
                        $post_name_kk = get_post_meta($post_id, '_resource_name_kk', true);
                        $post_name_en = get_post_meta($post_id, '_resource_name_en', true);
                        $post_description_ru = get_post_meta($post_id, '_resource_description_ru', true);
                        $post_description_kk = get_post_meta($post_id, '_resource_description_kk', true);
                        $post_description_en = get_post_meta($post_id, '_resource_description_en', true);
                        $post_images = get_post_meta($post_id, '_resource_images', true);
                        $image_url = (!empty($post_images) && is_array($post_images))
                            ? wp_get_attachment_url($post_images[0])
                            : SPACES_MNU_PLUGIN_URL . 'assets/img/image_placeholder.png';
                        $posts[] = [
                            'name' => [
                                'ru' => $post_name_ru,
                                'kk' => $post_name_kk,
                                'en' => $post_name_en,
                            ],
                            'description' => [
                                'ru' => $post_description_ru,
                                'kk' => $post_description_kk,
                                'en' => $post_description_en,
                            ],
                            'image' => $image_url,
                            'link' => get_permalink(),
                        ];
                        // }
                    }
                }
            }
            wp_reset_postdata();
            wp_send_json_success([
                'resources' => $posts,
                'max_num_pages' => $post_query->max_num_pages,
                'current_page' => $paged,
            ]);
        }
        function get_all_resources()
        {
            $args = [
                'post_type' => 'resource',
                'posts_per_page' => -1,
            ];
            $resource_query = new WP_Query($args);
            $resources = [];
            if ($resource_query->have_posts()) {
                while ($resource_query->have_posts()) {
                    $resource_query->the_post();
                    $post_id = get_the_ID();
                    $resources[] = [
                        'id' => $post_id,
                        'title' => get_the_title(),
                        'excerpt' => get_the_excerpt(),
                        'link' => get_permalink(),
                        'type' => wp_get_post_terms($post_id, 'resource_type', array('fields' => 'slugs')),
                        'category' => wp_get_post_terms($post_id, 'resource_category', array('fields' => 'slugs')),
                        'floor' => get_post_meta($post_id, '_resource_floor', true),
                    ];
                }
            }
            wp_reset_postdata();
            wp_send_json_success($resources);
        }
        public function save_resource_via_ajax()
        {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                wp_send_json_error(array('message' => __('Autosave prevented.', 'spaces_mnu_plugin')));
                return;
            }
            if (!isset($_POST['post_type']) || $_POST['post_type'] !== 'resource') {
                wp_send_json_error(array('message' => __('Invalid post type.', 'spaces_mnu_plugin')));
                return;
            }
            $resource_id = isset($_POST['editing_resource_id']) ? intval($_POST['editing_resource_id']) : 0;
            if ($resource_id > 0) {
                if (!current_user_can('edit_post', $resource_id)) {
                    wp_send_json_error(array('message' => __('You do not have permission to edit this resource.', 'spaces_mnu_plugin')));
                    return;
                }
                $post_data = array(
                    'ID'           => $resource_id,
                    'post_title'   => isset($_POST['resource_name_ru']) ? sanitize_text_field($_POST['resource_name_ru']) : __('Unnamed Resource', 'spaces_mnu_plugin'),
                );
                $post_id = wp_update_post($post_data);
                if (is_wp_error($post_id)) {
                    wp_send_json_error(array('message' => __('An error occurred while updating the resource.', 'spaces_mnu_plugin')));
                    return;
                }
                $action_message = __('Resource updated successfully.', 'spaces_mnu_plugin');
            } else {
                $post_data = array(
                    'post_title'  => isset($_POST['resource_name_ru']) ? sanitize_text_field($_POST['resource_name_ru']) : __('Unnamed Resource', 'spaces_mnu_plugin'),
                    'post_status' => 'publish',
                    'post_type'   => 'resource',
                    'post_author' => get_current_user_id(),
                );
                $post_id = wp_insert_post($post_data);
                if (is_wp_error($post_id)) {
                    wp_send_json_error(array('message' => __('An error occurred while creating the resource.', 'spaces_mnu_plugin')));
                    return;
                }
                $action_message = __('Resource created successfully.', 'spaces_mnu_plugin');
            }
            Spaces_MNU_Resource_Utils::save_resource_meta_fields($post_id, $_POST);
            wp_send_json_success(array('message' => $action_message));
        }
        private function str_ends_with($haystack, $needle)
        {
            return substr($haystack, -strlen($needle)) === $needle;
        }
        public function search_categories_ajax()
        {
            $term = sanitize_text_field($_POST['term']);
            $resource_type = isset($_POST['resource_type']) ? sanitize_text_field($_POST['resource_type']) : '';
            $selected_categories = isset($_POST['selected_categories']) ? explode(',', sanitize_text_field($_POST['selected_categories'])) : array();
            $args = array(
                'taxonomy'   => 'resource_category',
                'hide_empty' => false,
            );
            $categories = get_terms($args);
            $results = array();
            if (!empty($categories) && !is_wp_error($categories)) {
                foreach ($categories as $category) {
                    if (in_array($category->slug, array('all', 'all-space', 'all-equipment'))) {
                        continue;
                    }
                    if (in_array($category->slug, $selected_categories)) {
                        continue;
                    }
                    if ($resource_type && !$this->str_ends_with($category->slug, '-' . $resource_type)) {
                        continue;
                    }
                    if (empty($term) || preg_match('/' . preg_quote($term, '/') . '/i', $category->name)) {
                        $results[] = array(
                            'slug'  => $category->slug,
                            'label' => __($category->name, 'spaces_mnu_plugin'),
                        );
                    }
                }
                if (!empty($term)) {
                    usort($results, function ($a, $b) use ($term) {
                        $pattern = '/' . preg_quote($term, '/') . '/i';
                        $posA = preg_match($pattern, $a['label']) ? 1 : 0;
                        $posB = preg_match($pattern, $b['label']) ? 1 : 0;
                        return $posA - $posB;
                    });
                }
            }
            wp_send_json($results);
        }
        public function search_users_ajax()
        {
            $term = sanitize_text_field($_POST['term']);
            $current_user_id = get_current_user_id();
            $user_query = new WP_User_Query(array(
                'search'         => '*' . esc_attr($term) . '*',
                'search_columns' => array('user_login', 'user_nicename', 'user_email', 'display_name', 'first_name', 'last_name'),
                'number'         => 10,
                'exclude'        => array($current_user_id),
                'meta_query'     => array(
                    array(
                        'key'     => 'mnu_role',
                        'value'   => 'management_staff',
                        'compare' => '='
                    ),
                ),
            ));
            $results = array();
            if (!empty($user_query->get_results())) {
                foreach ($user_query->get_results() as $user) {
                    $results[] = array(
                        'id'    => $user->ID,
                        'label' => $user->display_name,
                    );
                }
            }
            wp_send_json($results);
        }
        public function search_roles_ajax()
        {
            $term = sanitize_text_field($_POST['term']);
            $selected_roles = isset($_POST['selected_roles']) ? explode(',', sanitize_text_field($_POST['selected_roles'])) : array();
            $roles = get_option('spaces_mnu_plugin_roles', array());
            $results = array();
            foreach ($roles as $role_id => $role_data) {
                if (in_array($role_data['name'], ['Security Staff', 'Management Staff', 'Senior Management Staff'])) {
                    continue;
                }
                if (in_array($role_id, $selected_roles)) {
                    continue;
                }
                if (empty($term) || preg_match('/' . preg_quote($term, '/') . '/i', $role_data['name'])) {
                    $results[] = array(
                        'id'    => $role_id,
                        'label' => __($role_data['name'], 'spaces_mnu_plugin'),
                    );
                }
            }
            wp_send_json($results);
        }
        public function load_resource_create_form_ajax()
        {
            check_ajax_referer('load_resource_create_form', 'nonce');
            $frontend_forms = new Spaces_MNU_Resource_Frontend_Forms();
            ob_start();
            $frontend_forms->render_frontend_resource_form(array('resource_id' => 0));
            $form_html = ob_get_clean();
            $new_nonce = wp_create_nonce('load_resource_create_form');
            wp_send_json_success(array(
                'form_html' => $form_html,
                'nonce' => $new_nonce,
            ));
        }
        public function get_resource_edit_form_ajax()
        {
            if (!check_ajax_referer('get_resource_edit_form', 'nonce', false)) {
                wp_send_json_error(array('message' => __('Nonce verification failed.', 'spaces_mnu_plugin')));
                return;
            }
            $resource_id = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
            if (!$resource_id || !current_user_can('edit_post', $resource_id)) {
                wp_send_json_error(array('message' => __('Invalid resource ID or insufficient permissions.', 'spaces_mnu_plugin')));
                return;
            }
            ob_start();
            $frontend_forms = new Spaces_MNU_Resource_Frontend_Forms();
            $frontend_forms->render_frontend_resource_form(array('resource_id' => $resource_id));
            $form_html = ob_get_clean();
            wp_send_json_success(array('form_html' => $form_html));
        }
        public function handle_delete_resource()
        {
            check_ajax_referer('resource_nonce', 'nonce');
            if (!current_user_can('delete_posts')) {
                wp_send_json_error(['message' => 'У вас нет прав для удаления ресурса.']);
            }
            $resource_id = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
            if (!$resource_id) {
                wp_send_json_error(['message' => 'Неверный ID ресурса.']);
            }
            $deleted = wp_delete_post($resource_id, true);
            if ($deleted) {
                wp_send_json_success(['message' => 'Ресурс успешно удален.']);
            } else {
                wp_send_json_error(['message' => 'Ошибка при удалении ресурса.']);
            }
        }
        private function get_resource_name_by_locale($resource_id)
        {
            $resource_name_ru = get_post_meta($resource_id, '_resource_name_ru', true);
            $resource_name_kk = get_post_meta($resource_id, '_resource_name_kk', true);
            $resource_name_en = get_post_meta($resource_id, '_resource_name_en', true);
            $current_language = get_locale();
            if ($current_language === 'en_US') {
                return $resource_name_en;
            } elseif ($current_language === 'kk') {
                return $resource_name_kk;
            } else {
                return $resource_name_ru;
            }
        }
        private function get_resource_responsible_data($resource_id)
        {
            $responsible_id = get_post_meta($resource_id, '_resource_responsible', true)[0];
            $first_name = get_user_meta($responsible_id, 'first_name', true);
            $last_name = get_user_meta($responsible_id, 'last_name', true);
            $email = get_userdata($responsible_id)->user_email;
            $phone = get_user_meta($responsible_id, 'phone', true);
            return [
                'name' => trim("{$first_name} {$last_name}"),
                'email' => $email,
                'phone' => $phone,
            ];
        }
        public function handle_delete_resource_and_cancel_bookings()
        {
            check_ajax_referer('resource_nonce', 'nonce');
            if (!current_user_can('delete_posts')) {
                wp_send_json_error(['message' => 'У вас нет прав для удаления ресурса.']);
                wp_die();
            }
            $resource_id = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
            if (!$resource_id) {
                wp_send_json_error(['message' => 'Неверный ID ресурса.']);
                wp_die();
            }
            global $wpdb;
            $table_bookings = $wpdb->prefix . 'spaces_bookings';
            $current_date = current_time('mysql');
            $bookings_to_cancel = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT ID, RequestedBy, BookingDate, SlotStart, SlotEnd FROM $table_bookings WHERE ResourceID = %d AND Status = 'approved' AND BookingDate >= %s",
                    $resource_id,
                    $current_date
                )
            );
            if (empty($bookings_to_cancel)) {
                wp_send_json_error(['message' => __('No upcoming bookings to cancel.', 'spaces_mnu_plugin')]);
                wp_die();
            }
            $user_bookings = [];
            foreach ($bookings_to_cancel as $booking) {
                $updated = $wpdb->update(
                    $table_bookings,
                    [
                        'Status' => 'cancelled',
                        'CancelledBy' => get_current_user_id(),
                        'CancelledAt' => $current_date,
                        'CancelComment' => __('Cancelled due to resource deletion', 'spaces_mnu_plugin')
                    ],
                    [
                        'ID' => $booking->ID
                    ]
                );
                if ($updated !== false) {
                    $slot_time = date('H:i', strtotime($booking->SlotStart)) . ' - ' . date('H:i', strtotime($booking->SlotEnd));
                    $user_bookings[$booking->RequestedBy][$booking->BookingDate][] = $slot_time;
                }
            }
            if (empty($user_bookings)) {
                wp_send_json_error(['message' => __('Error canceling resource bookings.', 'spaces_mnu_plugin')]);
                wp_die();
            }
            if ($bookings_to_cancel) {
                $resource_name = $this->get_resource_name_by_locale($resource_id);
                $responsible_data = $this->get_resource_responsible_data($resource_id);
                foreach ($user_bookings as $user_id => $bookings_by_date) {
                    $user_info = get_userdata($user_id);
                    $email_data = [
                        'name' => $user_info->first_name,
                        'email' => $user_info->user_email,
                        'subject' => __('Bookings Cancellation', 'spaces_mnu_plugin'),
                        'resource_responsible_email' => $responsible_data['email'],
                        'resource_responsible_name' => $responsible_data['name'],
                        'resource_responsible_phone' => $responsible_data['phone'],
                        'resource_name' => $resource_name,
                        'bookings' => [],
                    ];
                    foreach ($bookings_by_date as $date => $slots) {
                        $email_data['bookings'][] = [
                            'date' => date('d.m.Y', strtotime($date)),
                            'slots' => $slots,
                        ];
                    }
                    $result = send_custom_email($user_info->user_email, 'cancellation-all-bookings-resource', $email_data);
                    if (is_wp_error($result)) {
                        error_log($result->get_error_message());
                    }
                }
            }
            $deleted = wp_delete_post($resource_id, true);
            if ($deleted) {
                wp_send_json_success(['message' => __('Resource successfully deleted, associated upcoming bookings cancelled.', 'spaces_mnu_plugin')]);
            } else {
                wp_send_json_error(['message' => __('Error deleting the resource.', 'spaces_mnu_plugin')]);
            }
            wp_die();
        }
        function check_resource_bookings()
        {
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'resource_nonce')) {
                wp_send_json_error(['message' => 'Ошибка безопасности.']);
                wp_die();
            }
            $resource_id = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
            if (!$resource_id) {
                wp_send_json_error(['message' => 'Неверный идентификатор ресурса.']);
                wp_die();
            }
            global $wpdb;
            $table_bookings = $wpdb->prefix . 'spaces_bookings';
            $bookings = $wpdb->get_results($wpdb->prepare(
                "SELECT ID, BookingDate, SlotStart, SlotEnd, RequestedBy FROM $table_bookings WHERE ResourceID = %d AND Status = 'approved' AND IsDeleted = 0",
                $resource_id
            ));
            if (!empty($bookings)) {
                $formatted_bookings = array_map(function ($booking) {
                    return [
                        'id' => $booking->ID,
                        'date' => $booking->BookingDate,
                        'start_time' => $booking->SlotStart,
                        'end_time' => $booking->SlotEnd,
                        'user' => get_userdata($booking->RequestedBy)->display_name,
                    ];
                }, $bookings);
                wp_send_json_success(['hasBookings' => true, 'bookings' => $formatted_bookings]);
            } else {
                wp_send_json_success(['hasBookings' => false]);
            }
            wp_die();
        }

        public function register_ajax_actions()
        {
            add_action('wp_ajax_search_categories', array($this, 'search_categories_ajax'));
            add_action('wp_ajax_search_users', array($this, 'search_users_ajax'));
            add_action('wp_ajax_search_roles', array($this, 'search_roles_ajax'));
            add_action('wp_ajax_get_resource_edit_form', array($this, 'get_resource_edit_form_ajax'));
            add_action('wp_ajax_load_resource_create_form', array($this, 'load_resource_create_form_ajax'));
            add_action('wp_ajax_save_resource', array($this, 'save_resource_via_ajax'));
            add_action('wp_ajax_nopriv_save_resource', array($this, 'save_resource_via_ajax'));
            add_action('wp_ajax_filter_resources', array($this, 'filter_resources'));
            add_action('wp_ajax_nopriv_filter_resources', array($this, 'filter_resources'));
            add_action('wp_ajax_filter_categories_by_type', array($this, 'filter_categories_by_type'));
            add_action('wp_ajax_nopriv_filter_categories_by_type', array($this, 'filter_categories_by_type'));
            add_action('wp_ajax_delete_resource', [$this, 'handle_delete_resource']);
            add_action('wp_ajax_delete_resource_and_cancel_bookings', array($this, 'handle_delete_resource_and_cancel_bookings'));
            add_action('wp_ajax_check_resource_bookings', array($this, 'check_resource_bookings'));
        }
    }

    new Spaces_MNU_Resource_AJAX();
}
