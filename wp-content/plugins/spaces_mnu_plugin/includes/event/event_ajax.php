<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Spaces_MNU_Event_AJAX')) {
    class Spaces_MNU_Event_AJAX
    {
        public function __construct()
        {
            add_action('init', array($this, 'register_ajax_actions'));
        }
        function get_event_resources_ajax()
        {
            $search_term = isset($_POST['term']) ? sanitize_text_field($_POST['term']) : '';
            $args = array(
                'post_type' => 'resource',
                'posts_per_page' => -1,
                's' => $search_term,
                'post_status' => 'publish'
            );
            $query = new WP_Query($args);
            $resources = array();
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $resources[] = array(
                        'id' => get_the_ID(),
                        'name' => get_the_title(),
                    );
                }
            }
            wp_reset_postdata();
            if (!empty($resources)) {
                wp_send_json_success($resources);
            } else {
                wp_send_json_success(array());
            }
        }
        function get_event_spaces_ajax()
        {
            $search_term = isset($_POST['term']) ? sanitize_text_field($_POST['term']) : '';
            $args = [
                'post_type' => 'resource',
                'posts_per_page' => -1,
                'tax_query' => [
                    [
                        'taxonomy' => 'resource_type',
                        'field'    => 'slug',
                        'terms'    => 'space',
                    ],
                ],
                's' => $search_term,
                'post_status' => 'publish'
            ];
            $space_query = new WP_Query($args);
            $spaces = [];
            if ($space_query->have_posts()) {
                while ($space_query->have_posts()) {
                    $space_query->the_post();
                    $post_id = get_the_ID();
                    $resource_name_ru = get_post_meta($post_id, '_resource_name_ru', true) ?: 'Нет названия на русском';
                    $resource_name_en = get_post_meta($post_id, '_resource_name_en', true) ?: 'No name in English';
                    $resource_name_kk = get_post_meta($post_id, '_resource_name_kk', true) ?: 'Қазақша атау жоқ';
                    $spaces[] = [
                        'id' => $post_id,
                        'name_ru' => $resource_name_ru,
                        'name_en' => $resource_name_en,
                        'name_kk' => $resource_name_kk,
                        'link' => get_permalink(),
                    ];
                }
            }
            wp_reset_postdata();
            if (!empty($spaces)) {
                wp_send_json_success($spaces);
            } else {
                wp_send_json_success(array());
            }
        }
        public function search_event_users_ajax()
        {
            $term = isset($_POST['term']) ? sanitize_text_field($_POST['term']) : '';
            $current_user_id = get_current_user_id();
            $args = [
                'search'         => '*' . esc_attr($term) . '*',
                'search_columns' => ['user_login', 'first_name', 'last_name', 'user_email', 'display_name'],
                'number'         => 10,
                'exclude'        => [$current_user_id],
                'meta_query'     => [
                    [
                        'key'     => 'mnu_role',
                        'value'   => 'staff',
                        'compare' => 'LIKE',
                    ],
                ],
            ];
            $user_query = new WP_User_Query($args);
            $results = [];
            if (!empty($user_query->get_results())) {
                foreach ($user_query->get_results() as $user) {
                    $results[] = [
                        'id'    => $user->ID,
                        'label' => $user->display_name,
                    ];
                }
            }
            wp_send_json($results);
        }
        public function load_event_create_form_ajax()
        {
            check_ajax_referer('load_event_create_form', 'nonce');
            $frontend_forms = new Spaces_MNU_Event_Frontend_Forms();
            ob_start();
            $frontend_forms->render_frontend_event_form(array('event_id' => 0));
            $form_html = ob_get_clean();
            $new_nonce = wp_create_nonce('load_event_create_form');
            wp_send_json_success(array(
                'form_html' => $form_html,
                'nonce' => $new_nonce,
            ));
        }
        public function get_event_edit_form_ajax()
        {
            if (!check_ajax_referer('get_event_edit_form', 'nonce', false)) {
                wp_send_json_error(array('message' => __('Nonce verification failed.', 'spaces_mnu_plugin')));
                return;
            }
            $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
            if (!$event_id || !current_user_can('edit_post', $event_id)) {
                wp_send_json_error(array('message' => __('Invalid event ID or insufficient permissions.', 'spaces_mnu_plugin')));
                return;
            }
            ob_start();
            $frontend_forms = new Spaces_MNU_Event_Frontend_Forms();
            $frontend_forms->render_frontend_event_form(array('event_id' => $event_id));
            $form_html = ob_get_clean();
            wp_send_json_success(array('form_html' => $form_html));
        }
        public function search_event_roles_ajax()
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
        public function filter_events()
        {
            $selected_type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
            $search_query = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
            $selected_category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
            $selected_floor = isset($_POST['floor']) ? sanitize_text_field($_POST['floor']) : '';
            $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
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
                    ]
                ],
            ];
            if (!empty($selected_type) && $selected_type !== 'all') {
                $args['tax_query'][] = [
                    'taxonomy' => 'event_type',
                    'field' => 'slug',
                    'terms' => $selected_type,
                ];
            }
            if (!empty($selected_category)) {
                $args['tax_query'][] = [
                    'taxonomy' => 'event_category',
                    'field' => 'slug',
                    'terms' => explode(',', $selected_category),
                    'operator' => 'IN',
                ];
            }
            if (is_numeric($selected_floor)) {
                $args['meta_query'][] = [
                    'key' => '_event_floor',
                    'value' => $selected_floor,
                    'compare' => '='
                ];
            }
            $event_query = new WP_Query($args);
            $events = [];
            if ($event_query->have_posts()) {
                while ($event_query->have_posts()) {
                    $event_query->the_post();
                    $post_id = get_the_ID();
                    $event_name_ru = get_post_meta($post_id, '_event_name_ru', true);
                    $event_name_kk = get_post_meta($post_id, '_event_name_kk', true);
                    $event_name_en = get_post_meta($post_id, '_event_name_en', true);
                    $event_description_ru = get_post_meta($post_id, '_event_description_ru', true);
                    $event_description_kk = get_post_meta($post_id, '_event_description_kk', true);
                    $event_description_en = get_post_meta($post_id, '_event_description_en', true);
                    $event_images = get_post_meta($post_id, '_event_images', true);
                    $image_url = (!empty($event_images) && is_array($event_images))
                        ? wp_get_attachment_url($event_images[0])
                        : SPACES_MNU_PLUGIN_URL . 'assets/img/image_placeholder.png';
                    $events[] = [
                        'name' => [
                            'ru' => $event_name_ru,
                            'kk' => $event_name_kk,
                            'en' => $event_name_en,
                        ],
                        'description' => [
                            'ru' => $event_description_ru,
                            'kk' => $event_description_kk,
                            'en' => $event_description_en,
                        ],
                        'image' => $image_url,
                        'link' => get_permalink(),
                    ];
                }
            }
            wp_reset_postdata();
            wp_send_json_success([
                'events' => $events,
                'max_num_pages' => $event_query->max_num_pages,
                'current_page' => $paged,
            ]);
        }
        function get_all_events()
        {
            $args = [
                'post_type' => 'event',
                'posts_per_page' => -1,
            ];

            $event_query = new WP_Query($args);
            $events = [];
            if ($event_query->have_posts()) {
                while ($event_query->have_posts()) {
                    $event_query->the_post();
                    $post_id = get_the_ID();
                    $events[] = [
                        'id' => $post_id,
                        'title' => get_the_title(),
                        'excerpt' => get_the_excerpt(),
                        'link' => get_permalink(),
                        'type' => wp_get_post_terms($post_id, 'event_type', array('fields' => 'slugs')),
                        'category' => wp_get_post_terms($post_id, 'event_category', array('fields' => 'slugs')),
                        'floor' => get_post_meta($post_id, '_event_floor', true),
                    ];
                }
            }
            wp_reset_postdata();
            wp_send_json_success($events);
        }
        private function get_or_create_day_id($formatted_date, $resource_id)
        {
            global $wpdb;
            $day_id = $wpdb->get_var($wpdb->prepare(
                "SELECT ID FROM {$wpdb->prefix}spaces_days WHERE Day = %s AND ResourceID = %d",
                $formatted_date,
                $resource_id
            ));
            if (!$day_id) {
                $wpdb->insert("{$wpdb->prefix}spaces_days", [
                    'ResourceID' => $resource_id,
                    'Day' => $formatted_date,
                    'SlotsAvailable' => json_encode([])
                ], ['%d', '%s', '%s']);

                $day_id = $wpdb->insert_id;
            }
            return $day_id;
        }
        private function format_date($date)
        {
            return date('Y-m-d', strtotime($date));
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
        private function get_event_responsible_data($user_id)
        {
            $first_name = get_user_meta($user_id, 'first_name', true);
            $last_name = get_user_meta($user_id, 'last_name', true);
            $email = get_userdata($user_id)->user_email;
            $phone = get_user_meta($user_id, 'phone', true);
            return [
                'name' => trim("{$first_name} {$last_name}"),
                'email' => $email,
                'phone' => $phone,
            ];
        }
        public function save_event_via_ajax()
        {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                wp_send_json_error(array('message' => __('Autosave prevented.', 'spaces_mnu_plugin')));
                return;
            }
            if (!isset($_POST['post_type']) || $_POST['post_type'] !== 'event') {
                wp_send_json_error(array('message' => __('Invalid post type.', 'spaces_mnu_plugin')));
                return;
            }
            global $wpdb;
            $event_location = intval($_POST['event_location']);
            $resource_name = $event_location == 0 ? sanitize_text_field($_POST['event_location']) : null;
            $event_date = sanitize_text_field($_POST['event_date']);
            $formatted_event_date = $this->format_date($event_date);
            $event_start_time = sanitize_text_field($_POST['event_start_time']);
            $event_end_time = sanitize_text_field($_POST['event_end_time']);
            $event_public_start_time = sanitize_text_field($_POST['event_public_start_time']);
            $event_title = sanitize_text_field($_POST['event_name_ru']);
            $current_time = current_time('mysql');
            $current_user_id = get_current_user_id();
            $responsible_users = isset($_POST['event_responsible']) ? $_POST['event_responsible'] : '';
            if (is_string($responsible_users) && !empty($responsible_users)) {
                $responsible_users = explode(',', $responsible_users);
            }
            if (!in_array($current_user_id, $responsible_users)) {
                $responsible_users[] = $current_user_id;
            }
            $cancelBooks = isset($_POST['cancel_books']) ? true : false;
            $event_id = isset($_POST['editing_event_id']) ? intval($_POST['editing_event_id']) : 0;
            if ($event_id > 0) {
                $current_event_location = intval(get_post_meta($event_id, '_event_location', true));
                $current_event_date = get_post_meta($event_id, '_event_date', true);
                $current_event_formatted_date = $this->format_date($current_event_date);
                $current_event_public_start_time = get_post_meta($event_id, '_event_public_start_time', true);
                $data_changed = (
                    $event_location !== $current_event_location ||
                    $formatted_event_date !== $current_event_formatted_date ||
                    $event_date !== $current_event_date ||
                    $event_public_start_time !== $current_event_public_start_time
                );
                if (!$data_changed) {
                    $cancelBooks = false;
                }
                $data_changed = (
                    $event_location !== $current_event_location ||
                    $formatted_event_date !== $current_event_formatted_date ||
                    $event_date !== $current_event_date ||
                    $event_public_start_time !== $current_event_public_start_time
                );
            }
            if ($cancelBooks) {
                $bookings_table = $wpdb->prefix . 'spaces_bookings';
                $booking_ids = $wpdb->get_col($wpdb->prepare(
                    "SELECT ID 
                            FROM $bookings_table
                            WHERE ResourceID = %d 
                            AND BookingDate = %s 
                            AND Status = 'approved'
                            AND (
                                (SlotStart < %s AND SlotEnd > %s) OR
                                (SlotStart = %s AND SlotEnd = %s)
                            )",
                    $event_location,
                    $formatted_event_date,
                    $event_end_time,
                    $event_start_time,
                    $event_start_time,
                    $event_end_time
                ));
                $wpdb->query($wpdb->prepare(
                    "UPDATE $bookings_table 
                            SET Status = 'cancelled', 
                                CancelComment = %s, 
                                CancelledAt = %s, 
                                CancelledBy = %d
                            WHERE ResourceID = %d 
                            AND BookingDate = %s 
                            AND (
                                (SlotStart < %s AND SlotEnd > %s) OR
                                (SlotStart = %s AND SlotEnd = %s)
                            )",
                    $event_title,
                    $current_time,
                    $current_user_id,
                    $event_location,
                    $formatted_event_date,
                    $event_end_time,
                    $event_start_time,
                    $event_start_time,
                    $event_end_time
                ));
                if (!empty($booking_ids)) {
                    $placeholders = implode(',', array_fill(0, count($booking_ids), '%d'));
                    $sql = $wpdb->prepare(
                        "SELECT ID, ResourceID, BookingDate, SlotStart, SlotEnd, RequestedBy 
                                FROM $bookings_table 
                                WHERE ID IN ($placeholders)",
                        ...$booking_ids
                    );
                    $results = $wpdb->get_results($sql);
                    $slots_by_user = [];
                    foreach ($results as $row) {
                        $user_id = intval($row->RequestedBy);
                        $slot_start = date('H:i', strtotime($row->SlotStart));
                        $slot_end = date('H:i', strtotime($row->SlotEnd));
                        $slots_by_user[$user_id]['slots'][] = $slot_start . '-' . $slot_end;
                        $slots_by_user[$user_id]['resource_id'] = $row->ResourceID;
                        $slots_by_user[$user_id]['date'] = $row->BookingDate;
                    }
                    foreach ($slots_by_user as $user_id => $data) {
                        $recipient = get_userdata($user_id);
                        $recipient_email = $recipient->user_email;
                        $recipient_name = get_user_meta($user_id, 'first_name', true);
                        $resource_name = $this->get_resource_name_by_locale($data['resource_id']);
                        $responsible_data = $this->get_event_responsible_data($current_user_id);
                        $email_data = [
                            'name' => $recipient_name,
                            'email' => $recipient_email,
                            'subject' => __('Booking Cancellation', 'spaces_mnu_plugin'),
                            'resource_responsible_email' => $responsible_data['email'],
                            'resource_responsible_name' => $responsible_data['name'],
                            'resource_responsible_phone' => $responsible_data['phone'],
                            'resource_name' => $resource_name,
                            'cancel_comment' => $event_title,
                            'date' => date('d.m.Y', strtotime($data['date'])),
                            'slots' => $data['slots'],
                        ];
                        $result = send_custom_email($recipient_email, 'cancellation-booking-resource', $email_data);
                        if (is_wp_error($result)) {
                            error_log('Failed to send email to ' . $recipient_email . ': ' . $result->get_error_message());
                        } else {
                            error_log('Email sent successfully to ' . $recipient_email);
                        }
                    }
                }
            }
            if ($event_id > 0) {
                if (!current_user_can('edit_post', $event_id)) {
                    wp_send_json_error(array('message' => __('You do not have permission to edit this event.', 'spaces_mnu_plugin')));
                    return;
                }
                $post_data = array(
                    'ID'           => $event_id,
                    'post_title'   => isset($_POST['event_name_ru']) ? sanitize_text_field($_POST['event_name_ru']) : __('Unnamed Event', 'spaces_mnu_plugin'),
                );
                $post_id = wp_update_post($post_data);
                if (is_wp_error($post_id)) {
                    wp_send_json_error(array('message' => __('An error occurred while updating the event.', 'spaces_mnu_plugin')));
                    return;
                }
                $action_message = __('Event updated successfully.', 'spaces_mnu_plugin');
            } else {
                $post_data = array(
                    'post_title'  => isset($_POST['event_name_ru']) ? sanitize_text_field($_POST['event_name_ru']) : __('Unnamed Event', 'spaces_mnu_plugin'),
                    'post_status' => 'publish',
                    'post_type'   => 'event',
                    'post_author' => get_current_user_id(),
                );
                $post_id = wp_insert_post($post_data);
                if (is_wp_error($post_id)) {
                    wp_send_json_error(array('message' => __('An error occurred while creating the event.', 'spaces_mnu_plugin')));
                    return;
                }
                $action_message = __('Event created successfully.', 'spaces_mnu_plugin');
            }
            $day_id = $this->get_or_create_day_id($formatted_event_date, $event_location);
            if (!$day_id) {
                wp_send_json_error(['message' => 'Failed to create or find the day.']);
            }
            foreach ($responsible_users as $user_id) {
                $result = $wpdb->insert($wpdb->prefix . 'spaces_bookings', [
                    'DayID' => $day_id,
                    'BookingDate' => $formatted_event_date,
                    'SlotStart' => $event_start_time,
                    'SlotEnd' => $event_end_time,
                    'ResourceID' => $event_location,
                    'EventID' => $post_id,
                    'ResourceName' => $resource_name,
                    'RequestedBy' => $user_id,
                    'RequestReason' => "Мероприятие: $event_title",
                    'Status' => 'approved'
                ], [
                    '%d', // DayID
                    '%s', // BookingDate
                    '%s', // SlotStart
                    '%s', // SlotEnd
                    '%d', // ResourceID
                    '%d', // EventID
                    '%s', // ResourceName
                    '%d', // RequestedBy
                    '%s', // RequestReason
                    '%s' // Status
                ]);
                if ($result === false) {
                    $error_message = $wpdb->last_error;
                    error_log("Ошибка при создании бронирования для пользователя $user_id: $error_message");
                }
                $event_name_ru = get_post_meta($post_id, '_event_name_ru', true);
                $event_name_kk = get_post_meta($post_id, '_event_name_kk', true);
                $event_name_en = get_post_meta($post_id, '_event_name_en', true);
                if ($event_location) {
                    $location_name_ru = get_post_meta($event_location, '_resource_name_ru', true);
                    $location_name_kk = get_post_meta($event_location, '_resource_name_kk', true);
                    $location_name_en = get_post_meta($event_location, '_resource_name_en', true);
                }
                $current_language = get_locale();
                $location_name = '';
                $event_name = '';
                if ($current_language === 'en_US') {
                    $event_name = $event_name_en;
                    $location_name = $location_name_en;
                } elseif ($current_language === 'kk') {
                    $event_name = $event_name_kk;
                    $location_name = $location_name_kk;
                } else {
                    $event_name = $event_name_ru;
                    $location_name = $location_name_ru;
                }
                $event_location_name = $location_name ? $location_name : $resource_name;
                $recipient_id = $user_id;
                $recipient_first_name = get_user_meta($recipient_id, 'first_name', true);
                $recipient_email = get_userdata($recipient_id)->user_email;
                $responsible_data = $this->get_event_responsible_data($responsible_users[0]);
                $email_data = [
                    'name' => $recipient_first_name,
                    'email' => $recipient_email,
                    'subject' => __('Event Invitation', 'spaces_mnu_plugin'),
                    'event_responsible_email' => $responsible_data['email'],
                    'event_responsible_name' => $responsible_data['name'],
                    'event_responsible_phone' => $responsible_data['phone'],
                    'event_name' => $event_name,
                    'event_location' => $event_location_name,
                    'date' => date('d.m.Y', strtotime($formatted_event_date)),
                    'event_public_start_time' => $event_public_start_time,
                ];
                $result = send_custom_email(
                    $recipient_email,
                    'event-invitation',
                    $email_data
                );
                if (is_wp_error($result)) {
                    error_log($result->get_error_message());
                }
            }
            Spaces_MNU_Event_Utils::save_event_meta_fields($post_id, $_POST);
            wp_send_json_success(array('message' => $action_message));
        }
        public function search_event_categories_ajax()
        {
            $term = sanitize_text_field($_POST['term']);
            $selected_categories = isset($_POST['selected_categories']) ? explode(',', sanitize_text_field($_POST['selected_categories'])) : [];
            $args = [
                'taxonomy'   => 'event_category',
                'hide_empty' => false,
            ];
            $categories = get_terms($args);
            $results = [];
            if (!empty($categories) && !is_wp_error($categories)) {
                foreach ($categories as $category) {
                    if (in_array($category->slug, $selected_categories)) {
                        continue;
                    }
                    if (empty($term) || preg_match('/' . preg_quote($term, '/') . '/i', $category->name)) {
                        $results[] = [
                            'slug'  => $category->slug,
                            'label' => __($category->name, 'spaces_mnu_plugin'),
                        ];
                    }
                }
                if (!empty($term)) {
                    usort($results, function ($a, $b) use ($term) {
                        $pattern = '/' . preg_quote($term, '/') . '/i';
                        $posA = preg_match($pattern, $a['label']) ? 1 : 0;
                        $posB = preg_match($pattern, $b['label']) ? 1 : 0;
                        return $posB - $posA;
                    });
                }
            }
            wp_send_json($results);
        }
        public function handle_delete_event()
        {
            check_ajax_referer('event_nonce', 'nonce');
            if (!current_user_can('delete_posts')) {
                wp_send_json_error(['message' => 'У вас нет прав для удаления ресурса.']);
            }
            $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
            if (!$event_id) {
                wp_send_json_error(['message' => 'Неверный ID ресурса.']);
            }
            $deleted = wp_delete_post($event_id, true);
            if ($deleted) {
                wp_send_json_success(['message' => 'Ресурс успешно удален.']);
            } else {
                wp_send_json_error(['message' => 'Ошибка при удалении ресурса.']);
            }
        }
        public function handle_delete_event_and_cancel_bookings()
        {
            check_ajax_referer('event_nonce', 'nonce');
            if (!current_user_can('delete_posts')) {
                wp_send_json_error(['message' => 'У вас нет прав для удаления ресурса.']);
                wp_die();
            }
            $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
            if (!$event_id) {
                wp_send_json_error(['message' => 'Неверный ID ресурса.']);
                wp_die();
            }
            global $wpdb;
            $table_bookings = $wpdb->prefix . 'spaces_bookings';
            $updated = $wpdb->update(
                $table_bookings,
                [
                    'Status' => 'cancelled',
                    'CancelledBy' => get_current_user_id(),
                    'CancelledAt' => current_time('mysql'),
                    'CancelComment' => 'Cancelled due to event deletion'
                ],
                [
                    'EventID' => $event_id,
                    'Status' => 'approved'
                ]
            );
            if ($updated === false) {
                wp_send_json_error(['message' => __('Error canceling event bookings.', 'spaces_mnu_plugin')]);
                wp_die();
            }
            $deleted = wp_delete_post($event_id, true);
            if ($deleted) {
                wp_send_json_success(['message' => __('Event successfully deleted, associated bookings cancelled.', 'spaces_mnu_plugin')]);
            } else {
                wp_send_json_error(['message' => __('Error deleting the event.', 'spaces_mnu_plugin')]);
            }
            wp_die();
        }
        function check_event_bookings()
        {
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'event_nonce')) {
                error_log("Nonce verification failed");
                wp_send_json_error(['message' => 'Ошибка безопасности.']);
                wp_die();
            }
            $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
            if (!$event_id) {
                error_log("Invalid event ID");
                wp_send_json_error(['message' => 'Неверный идентификатор ресурса.']);
                wp_die();
            }
            global $wpdb;
            $table_bookings = $wpdb->prefix . 'spaces_bookings';
            $bookings = $wpdb->get_results($wpdb->prepare(
                "SELECT ID, BookingDate, SlotStart, SlotEnd, RequestedBy FROM $table_bookings WHERE EventID = %d AND Status = 'approved' AND IsDeleted = 0",
                $event_id
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
        public function check_resource_availability()
        {
            global $wpdb;
            $event_location = intval($_POST['event_location']);
            $event_start_time = sanitize_text_field($_POST['event_start_time']);
            $event_end_time = sanitize_text_field($_POST['event_end_time']);
            $event_date = sanitize_text_field($_POST['event_date']);
            $resource_exists = get_post($event_location);
            if (!$resource_exists || $resource_exists->post_type !== 'resource') {
                wp_send_json_error(['message' => 'Указанный ресурс не существует.']);
            }
            $days_table = $wpdb->prefix . 'spaces_days';
            $day_exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $days_table WHERE ResourceID = %d AND Day = %s",
                $event_location,
                $event_date
            ));
            if (!$day_exists) {
                wp_send_json_success(['resource_conflict' => false]);
            }
            $bookings_table = $wpdb->prefix . 'spaces_bookings';
            $conflicting_bookings = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $bookings_table 
                WHERE ResourceID = %d
                AND BookingDate = %s
                AND (
                    (SlotStart < %s AND SlotEnd > %s) OR
                    (SlotStart = %s AND SlotEnd = %s)
                )",
                $event_location,
                $event_date,
                $event_end_time,
                $event_start_time,
                $event_start_time,
                $event_end_time,
            ));
            if (!empty($conflicting_bookings)) {
                wp_send_json_success([
                    'resource_conflict' => true,
                    'conflict_message' => 'Этот ресурс забронирован на выбранные дату и время. Хотите отменить бронирования и создать мероприятие?'
                ]);
            }
            wp_send_json_success(['resource_conflict' => false]);
        }
        public function register_ajax_actions()
        {
            add_action('wp_ajax_search_event_categories', array($this, 'search_event_categories_ajax'));
            add_action('wp_ajax_search_event_users', array($this, 'search_event_users_ajax'));
            add_action('wp_ajax_search_event_roles', array($this, 'search_event_roles_ajax'));
            add_action('wp_ajax_get_event_edit_form', array($this, 'get_event_edit_form_ajax'));
            add_action('wp_ajax_load_event_create_form', array($this, 'load_event_create_form_ajax'));
            add_action('wp_ajax_save_event', array($this, 'save_event_via_ajax'));
            add_action('wp_ajax_nopriv_save_event', array($this, 'save_event_via_ajax'));
            add_action('wp_ajax_filter_events', array($this, 'filter_events'));
            add_action('wp_ajax_nopriv_filter_events', array($this, 'filter_events'));
            add_action('wp_ajax_delete_event', [$this, 'handle_delete_event']);
            add_action('wp_ajax_delete_event_and_cancel_bookings', array($this, 'handle_delete_event_and_cancel_bookings'));
            add_action('wp_ajax_check_event_bookings', array($this, 'check_event_bookings'));
            add_action('wp_ajax_get_event_resources', array($this, 'get_event_resources_ajax'));
            add_action('wp_ajax_get_event_spaces', array($this, 'get_event_spaces_ajax'));
            add_action('wp_ajax_check_resource_availability', array($this, 'check_resource_availability'));
        }
    }

    new Spaces_MNU_Event_AJAX();
}
