<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Spaces_MNU_Booking_AJAX')) {
    class Spaces_MNU_Booking_AJAX
    {
        public function __construct()
        {
            add_action('wp_ajax_create_booking', [$this, 'create_booking']);
            add_action('wp_ajax_create_event_booking', [$this, 'create_event_booking']);
            add_action('wp_ajax_get_booked_slots', [$this, 'get_booked_slots']);
            add_action('wp_ajax_nopriv_get_booked_slots', [$this, 'get_booked_slots']);
            add_action('wp_ajax_get_booked_seats', [$this, 'get_booked_seats']);
            add_action('wp_ajax_nopriv_get_booked_seats', [$this, 'get_booked_seats']);
            add_action('wp_ajax_update_booking_status', [$this, 'update_booking_status']);
            add_action('wp_ajax_load_bookings_for_date', [$this, 'load_bookings_for_date']);
            add_action('wp_ajax_load_my_bookings_for_date', [$this, 'load_my_bookings_for_date']);
            add_action('wp_ajax_load_all_my_bookings', [$this, 'load_all_my_bookings']);
            add_action('wp_ajax_load_all_bookings_for_date', [$this, 'load_all_bookings_for_date']);
            add_action('wp_ajax_load_all_bookings', [$this, 'load_all_bookings']);
            add_action('wp_enqueue_scripts', array($this, 'register_scripts'), 20);
        }
        public function load_bookings_for_date()
        {
            check_ajax_referer('spaces_mnu_nonce', 'nonce');
            $date = sanitize_text_field($_POST['date']);
            $dateObj = DateTime::createFromFormat('d.m.Y', $date);
            if (!$dateObj) {
                wp_send_json_error(['message' => __('Invalid date format.', 'spaces_mnu_plugin')]);
            }
            $formattedDate = $dateObj->format('Y-m-d');
            $current_user_id = get_current_user_id();
            global $wpdb;
            $days = $wpdb->get_results($wpdb->prepare(
                "SELECT d.ID FROM {$wpdb->prefix}spaces_days d
         LEFT JOIN {$wpdb->prefix}posts p ON d.ResourceID = p.ID
         WHERE DATE(d.Day) = %s AND (p.post_type = 'resource' OR p.ID IS NULL)",
                $formattedDate
            ));
            if (empty($days)) {
                wp_send_json_error(['message' => __('No bookings found for this date.', 'spaces_mnu_plugin')]);
            }
            $day_ids = array_map(fn($d) => $d->ID, $days);
            $placeholders = implode(',', array_fill(0, count($day_ids), '%d'));
            $query_args = $day_ids;
            $query = "
        SELECT b.ID as id, b.SlotStart as slot_start, b.SlotEnd as slot_end, b.RequestReason as reason, b.CancelComment as comment, b.ResourceName as resourceName,
               b.BookingDate as bookingDate, b.RequestedBy as requestedBy, b.Status as status, b.CancelledBy as cancelledBy, b.CancelledAt as cancelledAt,
               b.Priority as priority, b.CreatedAt as createdAt, b.ModifiedAt as modifiedAt, b.IsDeleted as isDeleted, 
               u.display_name as requested_by, u.user_email as reqByEmail,
               job_meta.meta_value AS job_title, dept_meta.meta_value AS department,
               fname_meta.meta_value AS first_name, lname_meta.meta_value AS last_name,
               IF(p.ID IS NULL, 'The resource has been removed', p.post_title) as resource, 
               IF(p.ID IS NULL, NULL, p.guid) as resourceLink,
               cancel_user_fname.meta_value AS cancelledByFirstName, cancel_user_lname.meta_value AS cancelledByLastName,
               p.ID as resource_id
        FROM {$wpdb->prefix}spaces_bookings b
        INNER JOIN {$wpdb->prefix}spaces_days d ON b.DayID = d.ID
        INNER JOIN {$wpdb->prefix}users u ON b.RequestedBy = u.ID
        LEFT JOIN {$wpdb->prefix}posts p ON b.ResourceID = p.ID
        LEFT JOIN {$wpdb->prefix}usermeta job_meta ON u.ID = job_meta.user_id AND job_meta.meta_key = 'job_title'
        LEFT JOIN {$wpdb->prefix}usermeta dept_meta ON u.ID = dept_meta.user_id AND dept_meta.meta_key = 'department'
        LEFT JOIN {$wpdb->prefix}usermeta fname_meta ON u.ID = fname_meta.user_id AND fname_meta.meta_key = 'first_name'
        LEFT JOIN {$wpdb->prefix}usermeta lname_meta ON u.ID = lname_meta.user_id AND lname_meta.meta_key = 'last_name'
        LEFT JOIN {$wpdb->prefix}usermeta cancel_user_fname ON b.CancelledBy = cancel_user_fname.user_id AND cancel_user_fname.meta_key = 'first_name'
        LEFT JOIN {$wpdb->prefix}usermeta cancel_user_lname ON b.CancelledBy = cancel_user_lname.user_id AND cancel_user_lname.meta_key = 'last_name'
        WHERE d.ID IN ($placeholders)
        ORDER BY b.ID, b.Status ASC, b.SlotStart ASC
    ";
            $bookings = $wpdb->get_results($wpdb->prepare($query, ...$query_args));
            $filtered_bookings = array_filter($bookings, function ($booking) use ($current_user_id) {
                if ($booking->resource_id === null) {
                    return true;
                }
                $responsible_users = get_post_meta($booking->resource_id, '_resource_responsible', true);
                if (is_serialized($responsible_users)) {
                    $responsible_users = unserialize($responsible_users);
                }
                return is_array($responsible_users) && in_array($current_user_id, $responsible_users);
            });
            foreach ($filtered_bookings as $booking) {
                $booking->avatar_url = get_user_meta($booking->requestedBy, 'custom_avatar', true);
            }
            $bookings_data = [];
            foreach ($filtered_bookings as $booking) {
                $cancelled_by_name = trim("{$booking->cancelledByFirstName} {$booking->cancelledByLastName}");
                $bookings_data[] = [
                    'id' => $booking->id,
                    'date' => $formattedDate,
                    'slot' => "{$booking->slot_start} - {$booking->slot_end}",
                    'resource' => $booking->resource,
                    'resourceLink' => $booking->resourceLink,
                    'resourceName' => $booking->resourceName,
                    'requestedBy' => $booking->requested_by,
                    'reason' => $booking->reason,
                    'bookingDate' => $booking->bookingDate,
                    'status' => $booking->status,
                    'cancelledBy' => $cancelled_by_name,
                    'cancelledAt' => $booking->cancelledAt,
                    'priority' => $booking->priority,
                    'createdAt' => $booking->createdAt,
                    'modifiedAt' => $booking->modifiedAt,
                    'isDeleted' => $booking->isDeleted,
                    'comment' => $booking->comment,
                    'reqByEmail' => $booking->reqByEmail,
                    'job_title' => $booking->job_title,
                    'department' => $booking->department,
                    'avatar_url' => $booking->avatar_url,
                    'first_name' => $booking->first_name,
                    'last_name' => $booking->last_name
                ];
            }
            if (empty($bookings_data)) {
                wp_send_json_error(['message' => __('No bookings found for this date.', 'spaces_mnu_plugin')]);
            }
            wp_send_json_success(['bookings' => $bookings_data]);
        }
        public function load_my_bookings_for_date()
        {
            check_ajax_referer('spaces_mnu_nonce', 'nonce');
            $date = sanitize_text_field($_POST['date']);
            $dateObj = DateTime::createFromFormat('d.m.Y', $date);
            if (!$dateObj) {
                wp_send_json_error(['message' => __('Invalid date format.', 'spaces_mnu_plugin')]);
            }
            $formattedDate = $dateObj->format('Y-m-d');
            $current_user_id = get_current_user_id();
            global $wpdb;
            $days = $wpdb->get_results($wpdb->prepare(
                "SELECT d.ID FROM {$wpdb->prefix}spaces_days d
         INNER JOIN {$wpdb->prefix}spaces_bookings b ON d.ID = b.DayID
         WHERE DATE(d.Day) = %s AND b.RequestedBy = %d",
                $formattedDate,
                $current_user_id
            ));
            if (empty($days)) {
                error_log('No day records found for the specified date: ' . $formattedDate);
                wp_send_json_error(['message' => __('No bookings found for this date.', 'spaces_mnu_plugin')]);
            }
            $day_ids = array_map(fn($d) => $d->ID, $days);
            $placeholders = implode(',', array_fill(0, count($day_ids), '%d'));
            $query_args = array_merge($day_ids);
            $query = "
        SELECT b.ID as id, b.SlotStart as slot_start, b.SlotEnd as slot_end, b.RequestReason as reason, b.CancelComment as comment, b.ResourceName as resourceName,
               b.BookingDate as bookingDate, b.RequestedBy as requestedBy, b.Status as status, b.CancelledBy as cancelledBy, b.CancelledAt as cancelledAt,
               b.Priority as priority, b.CreatedAt as createdAt, b.ModifiedAt as modifiedAt, b.IsDeleted as isDeleted, 
               u.display_name as requested_by, u.user_email as reqByEmail,
               job_meta.meta_value AS job_title, dept_meta.meta_value AS department,
               fname_meta.meta_value AS first_name, lname_meta.meta_value AS last_name,
               IF(p.ID IS NULL, 'The resource has been removed', p.post_title) as resource, 
               IF(p.ID IS NULL, NULL, p.guid) as resourceLink,
               cancel_user_fname.meta_value AS cancelledByFirstName, cancel_user_lname.meta_value AS cancelledByLastName
        FROM {$wpdb->prefix}spaces_bookings b
        INNER JOIN {$wpdb->prefix}spaces_days d ON b.DayID = d.ID
        INNER JOIN {$wpdb->prefix}users u ON b.RequestedBy = u.ID
        LEFT JOIN {$wpdb->prefix}posts p ON b.ResourceID = p.ID
        LEFT JOIN {$wpdb->prefix}usermeta job_meta ON u.ID = job_meta.user_id AND job_meta.meta_key = 'job_title'
        LEFT JOIN {$wpdb->prefix}usermeta dept_meta ON u.ID = dept_meta.user_id AND dept_meta.meta_key = 'department'
        LEFT JOIN {$wpdb->prefix}usermeta fname_meta ON u.ID = fname_meta.user_id AND fname_meta.meta_key = 'first_name'
        LEFT JOIN {$wpdb->prefix}usermeta lname_meta ON u.ID = lname_meta.user_id AND lname_meta.meta_key = 'last_name'
        LEFT JOIN {$wpdb->prefix}usermeta cancel_user_fname ON b.CancelledBy = cancel_user_fname.user_id AND cancel_user_fname.meta_key = 'first_name'
        LEFT JOIN {$wpdb->prefix}usermeta cancel_user_lname ON b.CancelledBy = cancel_user_lname.user_id AND cancel_user_lname.meta_key = 'last_name'
        WHERE d.ID IN ($placeholders) AND b.RequestedBy = %d
        ORDER BY b.ID, b.Status ASC, b.SlotStart ASC
    ";
            $query_args[] = $current_user_id;
            $bookings = $wpdb->get_results($wpdb->prepare($query, $query_args));
            foreach ($bookings as $booking) {
                $booking->avatar_url = get_user_meta($booking->requestedBy, 'custom_avatar', true);
            }
            $bookings_data = [];
            foreach ($bookings as $booking) {
                $cancelled_by_name = trim("{$booking->cancelledByFirstName} {$booking->cancelledByLastName}");
                $bookings_data[] = [
                    'id' => $booking->id,
                    'date' => $formattedDate,
                    'slot' => "{$booking->slot_start} - {$booking->slot_end}",
                    'resource' => $booking->resource,
                    'resourceLink' => $booking->resourceLink,
                    'resourceName' => $booking->resourceName,
                    'requestedBy' => $booking->requested_by,
                    'reason' => $booking->reason,
                    'bookingDate' => $booking->bookingDate,
                    'status' => $booking->status,
                    'cancelledBy' => $cancelled_by_name,
                    'cancelledAt' => $booking->cancelledAt,
                    'priority' => $booking->priority,
                    'createdAt' => $booking->createdAt,
                    'modifiedAt' => $booking->modifiedAt,
                    'isDeleted' => $booking->isDeleted,
                    'comment' => $booking->comment,
                    'reqByEmail' => $booking->reqByEmail,
                    'job_title' => $booking->job_title,
                    'department' => $booking->department,
                    'avatar_url' => $booking->avatar_url,
                    'first_name' => $booking->first_name,
                    'last_name' => $booking->last_name
                ];
            }
            if (empty($bookings_data)) {
                wp_send_json_error(['message' => __('No bookings found for this date.', 'spaces_mnu_plugin')]);
            }
            wp_send_json_success(['bookings' => $bookings_data]);
        }
        public function load_all_my_bookings()
        {
            check_ajax_referer('spaces_mnu_nonce', 'nonce');
            $current_user_id = get_current_user_id();
            global $wpdb;
            $removed_resource_text = __('The resource has been removed', 'spaces_mnu_plugin');
            $query = "
        SELECT b.ID as id, b.BookingDate as bookingDate, b.Status as status,
               IF(p.ID IS NULL, %s, p.post_title) as resource
        FROM {$wpdb->prefix}spaces_bookings b
        INNER JOIN {$wpdb->prefix}spaces_days d ON b.DayID = d.ID
        LEFT JOIN {$wpdb->prefix}posts p ON b.ResourceID = p.ID
        WHERE b.RequestedBy = %d
        ORDER BY b.BookingDate ASC
    ";
            $bookings = $wpdb->get_results($wpdb->prepare($query, $removed_resource_text, $current_user_id));
            $bookings_data = [];
            foreach ($bookings as $booking) {
                $bookings_data[] = [
                    'id' => $booking->id,
                    'bookingDate' => $booking->bookingDate,
                    'status' => $booking->status,
                    'resource' => $booking->resource
                ];
            }
            if (empty($bookings_data)) {
                wp_send_json_error(['message' => __('No bookings found.', 'spaces_mnu_plugin')]);
            }
            wp_send_json_success(['bookings' => $bookings_data]);
        }
        public function load_all_bookings_for_date()
        {
            check_ajax_referer('spaces_mnu_nonce', 'nonce');
            $date = sanitize_text_field($_POST['date']);
            $dateObj = DateTime::createFromFormat('d.m.Y', $date);
            if (!$dateObj) {
                wp_send_json_error(['message' => __('Invalid date format.', 'spaces_mnu_plugin')]);
            }
            $formattedDate = $dateObj->format('Y-m-d');
            global $wpdb;
            $days = $wpdb->get_results($wpdb->prepare(
                "SELECT d.ID FROM {$wpdb->prefix}spaces_days d
                INNER JOIN {$wpdb->prefix}spaces_bookings b ON d.ID = b.DayID
                WHERE DATE(d.Day) = %s",
                $formattedDate
            ));
            if (empty($days)) {
                error_log('No day records found for the specified date: ' . $formattedDate);
                wp_send_json_error(['message' => __('No bookings found for this date.', 'spaces_mnu_plugin')]);
            }
            $day_ids = array_map(fn($d) => $d->ID, $days);
            $placeholders = implode(',', array_fill(0, count($day_ids), '%d'));
            $query_args = $day_ids;
            $query = "
        SELECT b.ID as id, b.SlotStart as slot_start, b.SlotEnd as slot_end, b.RequestReason as reason, b.CancelComment as comment, b.ResourceName as resourceName,
               b.BookingDate as bookingDate, b.RequestedBy as requestedBy, b.Status as status, b.CancelledBy as cancelledBy, b.CancelledAt as cancelledAt,
               b.Priority as priority, b.CreatedAt as createdAt, b.ModifiedAt as modifiedAt, b.IsDeleted as isDeleted, 
               u.display_name as requested_by, u.user_email as reqByEmail,
               job_meta.meta_value AS job_title, dept_meta.meta_value AS department,
               fname_meta.meta_value AS first_name, lname_meta.meta_value AS last_name,
               IF(p.ID IS NULL, 'The resource has been removed', p.post_title) as resource, 
               IF(p.ID IS NULL, NULL, p.guid) as resourceLink,
               cancel_user_fname.meta_value AS cancelledByFirstName, cancel_user_lname.meta_value AS cancelledByLastName
        FROM {$wpdb->prefix}spaces_bookings b
        INNER JOIN {$wpdb->prefix}spaces_days d ON b.DayID = d.ID
        INNER JOIN {$wpdb->prefix}users u ON b.RequestedBy = u.ID
        LEFT JOIN {$wpdb->prefix}posts p ON b.ResourceID = p.ID
        LEFT JOIN {$wpdb->prefix}usermeta job_meta ON u.ID = job_meta.user_id AND job_meta.meta_key = 'job_title'
        LEFT JOIN {$wpdb->prefix}usermeta dept_meta ON u.ID = dept_meta.user_id AND dept_meta.meta_key = 'department'
        LEFT JOIN {$wpdb->prefix}usermeta fname_meta ON u.ID = fname_meta.user_id AND fname_meta.meta_key = 'first_name'
        LEFT JOIN {$wpdb->prefix}usermeta lname_meta ON u.ID = lname_meta.user_id AND lname_meta.meta_key = 'last_name'
        LEFT JOIN {$wpdb->prefix}usermeta cancel_user_fname ON b.CancelledBy = cancel_user_fname.user_id AND cancel_user_fname.meta_key = 'first_name'
        LEFT JOIN {$wpdb->prefix}usermeta cancel_user_lname ON b.CancelledBy = cancel_user_lname.user_id AND cancel_user_lname.meta_key = 'last_name'
        WHERE d.ID IN ($placeholders)
        ORDER BY b.ID, b.Status ASC, b.SlotStart ASC
    ";
            $bookings = $wpdb->get_results($wpdb->prepare($query, $query_args));
            foreach ($bookings as $booking) {
                $booking->avatar_url = get_user_meta($booking->requestedBy, 'custom_avatar', true);
            }
            $bookings_data = [];
            foreach ($bookings as $booking) {
                $cancelled_by_name = trim("{$booking->cancelledByFirstName} {$booking->cancelledByLastName}");
                $bookings_data[] = [
                    'id' => $booking->id,
                    'date' => $formattedDate,
                    'slot' => "{$booking->slot_start} - {$booking->slot_end}",
                    'resource' => $booking->resource,
                    'resourceLink' => $booking->resourceLink,
                    'resourceName' => $booking->resourceName,
                    'requestedBy' => $booking->requested_by,
                    'reason' => $booking->reason,
                    'bookingDate' => $booking->bookingDate,
                    'status' => $booking->status,
                    'cancelledBy' => $cancelled_by_name,
                    'cancelledAt' => $booking->cancelledAt,
                    'priority' => $booking->priority,
                    'createdAt' => $booking->createdAt,
                    'modifiedAt' => $booking->modifiedAt,
                    'isDeleted' => $booking->isDeleted,
                    'comment' => $booking->comment,
                    'reqByEmail' => $booking->reqByEmail,
                    'job_title' => $booking->job_title,
                    'department' => $booking->department,
                    'avatar_url' => $booking->avatar_url,
                    'first_name' => $booking->first_name,
                    'last_name' => $booking->last_name
                ];
            }
            if (empty($bookings_data)) {
                wp_send_json_error(['message' => __('No bookings found for this date.', 'spaces_mnu_plugin')]);
            }
            wp_send_json_success(['bookings' => $bookings_data]);
        }
        public function load_all_bookings()
        {
            check_ajax_referer('spaces_mnu_nonce', 'nonce');
            $current_user_id = get_current_user_id();
            global $wpdb;
            $removed_resource_text = __('The resource has been removed', 'spaces_mnu_plugin');
            $query = "
                SELECT b.ID as id, b.BookingDate as bookingDate, b.Status as status,
                    IF(p.ID IS NULL, %s, p.post_title) as resource
                FROM {$wpdb->prefix}spaces_bookings b
                INNER JOIN {$wpdb->prefix}spaces_days d ON b.DayID = d.ID
                LEFT JOIN {$wpdb->prefix}posts p ON b.ResourceID = p.ID
                ORDER BY b.BookingDate ASC
            ";
            $bookings = $wpdb->get_results($wpdb->prepare($query, $removed_resource_text));
            $bookings_data = [];
            foreach ($bookings as $booking) {
                $bookings_data[] = [
                    'id' => $booking->id,
                    'bookingDate' => $booking->bookingDate,
                    'status' => $booking->status,
                    'resource' => $booking->resource
                ];
            }
            if (empty($bookings_data)) {
                wp_send_json_error(['message' => __('No bookings found.', 'spaces_mnu_plugin')]);
            }
            wp_send_json_success(['bookings' => $bookings_data]);
        }
        public function update_booking_status()
        {
            check_ajax_referer('spaces_mnu_nonce', 'nonce');
            $booking_ids = isset($_POST['booking_ids']) ? explode(',', sanitize_text_field($_POST['booking_ids'])) : [];
            if (empty($booking_ids)) {
                wp_send_json_error(['message' => 'No bookings selected for cancellation.']);
            }
            global $wpdb;
            $cancel_comment = sanitize_text_field($_POST['comment']);
            $current_user_id = get_current_user_id();
            $current_time = current_time('mysql');
            foreach ($booking_ids as $booking_id) {
                $update_data = [
                    'Status' => 'cancelled',
                    'CancelComment' => $cancel_comment,
                    'CancelledBy' => $current_user_id,
                    'CancelledAt' => $current_time,
                ];
                $wpdb->update(
                    "{$wpdb->prefix}spaces_bookings",
                    $update_data,
                    ['ID' => intval($booking_id)]
                );
            }
            $placeholders = implode(',', array_fill(0, count($booking_ids), '%d'));
            $sql = $wpdb->prepare(
                "SELECT ResourceID, RequestedBy, BookingDate, SlotStart, SlotEnd, EventID, ResourceName 
                    FROM {$wpdb->prefix}spaces_bookings 
                    WHERE ID IN ($placeholders)",
                ...$booking_ids
            );
            $results = $wpdb->get_results($sql);
            $resource_id = null;
            $date = null;
            $requested_by = null;
            $slots = [];
            $event_id = null;
            $resource_name = null;
            if (!empty($results)) {
                foreach ($results as $index => $row) {
                    if ($index === 0) {
                        $resource_id = intval($row->ResourceID);
                        $date = esc_html($row->BookingDate);
                        $requested_by = esc_html($row->RequestedBy);
                        $event_id = esc_html($row->EventID);
                        $resource_name = esc_html($row->ResourceName);
                    }
                    $slot_start = date('H:i', strtotime($row->SlotStart));
                    $slot_end = date('H:i', strtotime($row->SlotEnd));
                    $slots[] = $slot_start . '-' . $slot_end;
                }
            }
            $recipient_id = $requested_by;
            $recipient_first_name = get_user_meta($recipient_id, 'first_name', true);
            $recipient_email = get_userdata($recipient_id)->user_email;
            if (!$event_id) {
                $resource_name = $this->get_resource_name_by_locale($resource_id);
                $responsible_data = $this->get_resource_responsible_data($resource_id);
                $email_data = [
                    'name' => $recipient_first_name,
                    'email' => $recipient_email,
                    'subject' => __('Booking Cancellation', 'spaces_mnu_plugin'),
                    'resource_responsible_email' => $responsible_data['email'],
                    'resource_responsible_name' => $responsible_data['name'],
                    'resource_responsible_phone' => $responsible_data['phone'],
                    'resource_name' => $resource_name,
                    'cancel_comment' => $cancel_comment,
                    'date' => date('d.m.Y', strtotime($date)),
                    'slots' => $slots,
                ];
                $result = send_custom_email($recipient_email, 'cancellation-booking-resource', $email_data);
                if (is_wp_error($result)) {
                    error_log($result->get_error_message());
                }
            } else {
                $event_name_ru = get_post_meta($event_id, '_event_name_ru', true);
                $event_name_kk = get_post_meta($event_id, '_event_name_kk', true);
                $event_name_en = get_post_meta($event_id, '_event_name_en', true);
                if ($resource_id) {
                    $location_name_ru = get_post_meta($resource_id, '_resource_name_ru', true);
                    $location_name_kk = get_post_meta($resource_id, '_resource_name_kk', true);
                    $location_name_en = get_post_meta($resource_id, '_resource_name_en', true);
                }
                $current_language = get_locale();
                $location_name = '';
                if ($current_language === 'en_US') {
                    $event_name = $event_name_en;
                    $location_name = $location_name_en;
                } elseif ($current_language === 'kk_KZ') {
                    $event_name = $event_name_kk;
                    $location_name = $location_name_kk;
                } else {
                    $event_name = $event_name_ru;
                    $location_name = $location_name_ru;
                }
                $event_location = $location_name ? $location_name : $resource_name;
                $resource_name = $this->get_resource_name_by_locale($resource_id);
                $responsible_data = $this->get_event_responsible_data($event_id);
                $email_data = [
                    'name' => $recipient_first_name,
                    'email' => $recipient_email,
                    'subject' => __('Event Registration Cancellation', 'spaces_mnu_plugin'),
                    'event_responsible_email' => $responsible_data['email'],
                    'event_responsible_name' => $responsible_data['name'],
                    'event_responsible_phone' => $responsible_data['phone'],
                    'event_name' => $event_name,
                    'event_location' => $event_location,
                    'cancel_comment' => $cancel_comment,
                    'date' => date('d.m.Y', strtotime($date)),
                ];
                $result = send_custom_email(
                    $recipient_email,
                    'cancellation-booking-event',
                    $email_data
                );
                if (is_wp_error($result)) {
                    error_log($result->get_error_message());
                } else {
                    // error_log('Email sent successfully!');
                }
            }
            wp_send_json_success(['message' => 'Booking(s) cancelled successfully.']);
        }
        private function get_resource_name_by_locale($resource_id)
        {
            $resource_name_ru = get_post_meta($resource_id, '_resource_name_ru', true);
            $resource_name_kk = get_post_meta($resource_id, '_resource_name_kk', true);
            $resource_name_en = get_post_meta($resource_id, '_resource_name_en', true);
            $current_language = get_locale();
            if ($current_language === 'en_US') {
                return $resource_name_en;
            } elseif ($current_language === 'kk_KZ') {
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
        private function get_event_responsible_data($event_id)
        {
            $responsible_id = get_post_meta($event_id, '_event_responsible', true)[0];
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
        public function get_booked_slots()
        {
            global $wpdb;
            $resource_id = intval($_POST['resource_id']);
            $date = sanitize_text_field($_POST['date']);
            if (!isset($_POST['resource_id'], $_POST['date'])) {
                wp_send_json_error(['message' => 'Invalid input.']);
                wp_die();
            }
            $formatted_date = date('Y-m-d', strtotime($date));
            if (!$formatted_date) {
                wp_send_json_error(['message' => 'Invalid date format.']);
                wp_die();
            }
            $booked_slots = $wpdb->get_results($wpdb->prepare(
                "SELECT b.SlotStart, b.SlotEnd
                        FROM {$wpdb->prefix}spaces_bookings b
                        JOIN {$wpdb->prefix}spaces_days d ON b.DayID = d.ID
                        WHERE b.ResourceID = %d AND d.Day = %s AND b.Status = 'approved'",
                $resource_id,
                $formatted_date
            ));
            if ($booked_slots) {
                $raw_slots = array_map(function ($slot) {
                    return $slot->SlotStart . '-' . $slot->SlotEnd;
                }, $booked_slots);
                $slots = $this->format_slots($raw_slots);
                wp_send_json_success(['booked_slots' => $slots]);
            } else {
                wp_send_json_success(['booked_slots' => []]);
            }
            wp_die();
        }
        public function get_booked_seats()
        {
            global $wpdb;
            $event_id = intval($_POST['event_id']);
            if (!$event_id) {
                wp_send_json_error(['message' => 'Invalid Event ID']);
            }
            $booked_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}spaces_bookings
        WHERE EventID = %d
        AND Status = 'approved'",
                $event_id
            ));
            wp_send_json_success(['booked_count' => intval($booked_count)]);
            wp_die();
        }
        public function create_booking()
        {
            if (!$this->verify_nonce()) {
                wp_send_json_error(['message' => 'Invalid nonce.']);
            }
            if (!$this->validate_request()) {
                wp_send_json_error(['message' => 'Invalid request.']);
            }
            $resource_id = intval($_POST['resource_id']);
            $selected_slots = array_map('sanitize_text_field', $_POST['selected_slots']);
            $reason = sanitize_text_field($_POST['reason']);
            $resource = get_post($resource_id);
            if (!$resource || $resource->post_status !== 'publish' || $resource_id == 0 || $resource_id == null || !$selected_slots || !$reason) {
                wp_send_json_error(['message' => 'Invalid request.']);
            }
            $user_id = get_current_user_id();
            $date = sanitize_text_field($_POST['date']);
            $formatted_date = $this->format_date($date);
            $now = new DateTime();
            $now->setTimezone(new DateTimeZone('Asia/Yekaterinburg'));
            $clickedDate = DateTime::createFromFormat('Y-m-d', $formatted_date, new DateTimeZone('Asia/Yekaterinburg'));
            if (!$clickedDate) {
                wp_send_json_error(['message' => 'Invalid date format. Please use Y-m-d.']);
            }
            $isToday = $clickedDate->format('Y-m-d') === $now->format('Y-m-d');
            foreach ($selected_slots as $slot) {
                list(
                    $start_time,
                    $end_time
                ) = explode('-', $slot);
                $slot_start = DateTime::createFromFormat('H:i', $start_time, new DateTimeZone('Asia/Yekaterinburg'));
                if (!$slot_start) {
                    wp_send_json_error(['message' => 'Invalid slot time format.']);
                }
                if ($isToday) {
                    $slot_start->setDate($now->format('Y'), $now->format('m'), $now->format('d'));
                    if (
                        $slot_start <= $now
                    ) {
                        wp_send_json_error(['message' => 'One or more selected slots are in the past or have already started.']);
                    }
                }
            }
            $day_id = $this->get_or_create_day_id($formatted_date, $resource_id);
            if (!$day_id) {
                wp_send_json_error(['message' => 'Failed to create or find the day.']);
            }
            $approved_slots = $this->get_approved_slots($day_id, $resource_id, $user_id);
            $selected_slots = $this->format_slots($selected_slots);
            // $this->log_debug_info($approved_slots, $selected_slots);
            $slots_to_create = $this->filter_slots_to_create($selected_slots, $approved_slots);
            if (empty($slots_to_create)) {
                wp_send_json_error(['message' => 'You have already booked slots for this date.']);
            }
            $errors = $this->create_bookings($slots_to_create, $day_id, $formatted_date, $resource_id, $user_id, $reason);
            if (empty($errors)) {
                $recipient_id = get_current_user_id();
                $recipient_first_name = get_user_meta($recipient_id, 'first_name', true);
                $recipient_email = wp_get_current_user()->user_email;
                $resource_name = $this->get_resource_name_by_locale($resource_id);
                $responsible_data = $this->get_resource_responsible_data($resource_id);
                $email_data = [
                    'name' => $recipient_first_name,
                    'email' => $recipient_email,
                    'subject' => __('Successful Resource Booking', 'spaces_mnu_plugin'),
                    'resource_responsible_email' => $responsible_data['email'],
                    'resource_responsible_name' => $responsible_data['name'],
                    'resource_responsible_phone' => $responsible_data['phone'],
                    'resource_name' => $resource_name,
                    'date' => date('d.m.Y', strtotime($date)),
                    'slots' => $slots_to_create,
                ];
                $result = send_custom_email(
                    $recipient_email,
                    'success-booking-resource',
                    $email_data
                );
                if (is_wp_error($result)) {
                    error_log($result->get_error_message());
                }
                wp_send_json_success(['message' => 'Booking created successfully.']);
            } else {
                wp_send_json_error(['message' => 'Failed to create booking.', 'errors' => $errors]);
            }
        }
        private function verify_nonce()
        {
            return isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'spaces_mnu_nonce');
        }
        private function validate_request()
        {
            return isset($_POST['resource_id'], $_POST['selected_slots'], $_POST['date']) && is_user_logged_in();
        }
        private function format_date($date)
        {
            return date('Y-m-d', strtotime($date));
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
        private function get_approved_slots($day_id, $resource_id, $user_id)
        {
            global $wpdb;
            $approved_bookings = $wpdb->get_results($wpdb->prepare(
                "SELECT SlotStart, SlotEnd FROM {$wpdb->prefix}spaces_bookings 
         WHERE DayID = %d AND ResourceID = %d AND RequestedBy = %d AND Status = 'approved'",
                $day_id,
                $resource_id,
                $user_id
            ));
            $approved_slots = [];
            foreach ($approved_bookings as $booking) {
                $slot_start = date('H:i', strtotime($booking->SlotStart));
                $slot_end = date('H:i', strtotime($booking->SlotEnd));
                $approved_slots[] = $slot_start . '-' . $slot_end;
            }
            return $approved_slots;
        }
        private function format_slots($slots)
        {
            return array_map(function ($slot) {
                $slot_parts = explode('-', $slot);
                if (count($slot_parts) === 2) {
                    $slot_start = date('H:i', strtotime($slot_parts[0]));
                    $slot_end = date('H:i', strtotime($slot_parts[1]));
                    return $slot_start . '-' . $slot_end;
                }
                return $slot;
            }, $slots);
        }
        private function filter_slots_to_create($selected_slots, $approved_slots)
        {
            return array_filter($selected_slots, function ($slot) use ($approved_slots) {
                return !in_array($slot, $approved_slots);
            });
        }
        private function log_debug_info($approved_slots, $selected_slots)
        {
            error_log('Approved slots: ' . print_r($approved_slots, true));
            error_log('Selected slots: ' . print_r($selected_slots, true));
        }
        private function create_bookings($slots_to_create, $day_id, $booking_date, $resource_id, $user_id, $reason) //$comment
        {
            global $wpdb;
            $errors = [];
            foreach ($slots_to_create as $slot) {
                $slot_parts = explode('-', $slot);
                if (count($slot_parts) !== 2) {
                    $errors[] = 'Invalid slot format.';
                    continue;
                }
                $slot_start = $slot_parts[0];
                $slot_end = $slot_parts[1];
                $existing_booking = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}spaces_bookings WHERE DayID = %d AND SlotStart = %s AND SlotEnd = %s AND ResourceID = %d AND Status = 'approved'",
                    $day_id,
                    $slot_start,
                    $slot_end,
                    $resource_id
                ));
                if ($existing_booking) {
                    $errors[] = "Slot $slot is already booked.";
                    continue;
                }
                $result = $wpdb->insert("{$wpdb->prefix}spaces_bookings", [
                    'DayID' => $day_id,
                    'BookingDate' => $booking_date,
                    'SlotStart' => $slot_start,
                    'SlotEnd' => $slot_end,
                    'ResourceID' => $resource_id,
                    'RequestedBy' => $user_id,
                    'RequestReason' => $reason,
                    'Status' => 'approved'
                ], [
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s'
                ]);
                if ($result === false) {
                    $errors[] = $wpdb->last_error;
                }
            }
            return $errors;
        }
        private function validate_event_booking_request()
        {
            if (!isset($_POST['event_id']) || !is_numeric($_POST['event_id'])) {
                error_log('Validation failed: Missing or invalid event_id');
                return false;
            }
            if (!isset($_POST['reason']) || empty(trim($_POST['reason']))) {
                error_log('Validation failed: Missing or empty reason');
                return false;
            }
            if (
                (!isset($_POST['resource_id']) || empty(trim($_POST['resource_id']))) &&
                (!isset($_POST['resource_name']) || empty(trim($_POST['resource_name'])))
            ) {
                error_log('Validation failed: Both resource_id and resource_name are missing or empty');
                return false;
            }
            return true;
        }
        public function create_event_booking()
        {
            if (!$this->verify_nonce()) {
                wp_send_json_error(['message' => 'Invalid nonce.']);
            }
            if (!$this->validate_event_booking_request()) {
                wp_send_json_error(['message' => 'Invalid request.']);
            }
            $resource_id = sanitize_text_field($_POST['resource_id']);
            $resource_name = sanitize_text_field($_POST['resource_name']);
            $event_id = intval($_POST['event_id']);
            $reason = sanitize_text_field($_POST['reason']);
            $user_id = get_current_user_id();
            $event_date = sanitize_text_field($_POST['event_date']);
            $formatted_event_date = $this->format_date($event_date);
            $event_public_start_time = sanitize_text_field($_POST['event_public_start_time']);
            $day_id = $this->get_or_create_day_id($formatted_event_date, $resource_id);
            if (!$day_id) {
                wp_send_json_error(['message' => 'Failed to create or find the day.']);
            }
            global $wpdb;
            $event_date_obj = DateTime::createFromFormat('Y-m-d', $formatted_event_date, new DateTimeZone('Asia/Yekaterinburg'));
            if (!$event_date_obj) {
                wp_send_json_error(['message' => 'Invalid event date format. Please use Y-m-d.']);
            }
            $existing_booking = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}spaces_bookings WHERE EventID = %d AND RequestedBy = %d AND Status = 'approved'",
                $event_id,
                $user_id
            ));
            if ($existing_booking) {
                wp_send_json_error(['message' => 'You have already booked this event.']);
            }
            $result = $wpdb->insert("{$wpdb->prefix}spaces_bookings", [
                'EventID' => $event_id,
                'BookingDate' => $formatted_event_date,
                'RequestedBy' => $user_id,
                'RequestReason' => $reason,
                'Status' => 'approved',
                'DayID' => $day_id,
                'ResourceID' => $resource_id,
                'ResourceName' => $resource_name
            ], [
                '%d',  // EventID
                '%s',  // BookingDate
                '%d',  // RequestedBy
                '%s',  // RequestReason
                '%s',  // Status
                '%s',  // DayID
                '%d',  // ResourceID
                '%s'   // ResourceName
            ]);

            if ($result === false) {
                wp_send_json_error(['message' => $wpdb->last_error]);
            }
            $event_name_ru = get_post_meta($event_id, '_event_name_ru', true);
            $event_name_kk = get_post_meta($event_id, '_event_name_kk', true);
            $event_name_en = get_post_meta($event_id, '_event_name_en', true);
            if ($resource_id) {
                $location_name_ru = get_post_meta($resource_id, '_resource_name_ru', true);
                $location_name_kk = get_post_meta($resource_id, '_resource_name_kk', true);
                $location_name_en = get_post_meta($resource_id, '_resource_name_en', true);
            }
            $current_language = get_locale();
            $location_name = '';
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
            $event_location = $location_name ? $location_name : $resource_name;
            $recipient_id = get_current_user_id();
            $recipient_first_name = get_user_meta($recipient_id, 'first_name', true);
            $recipient_email = wp_get_current_user()->user_email;
            $resource_name = $this->get_resource_name_by_locale($resource_id);
            $responsible_data = $this->get_event_responsible_data($event_id);
            $email_data = [
                'name' => $recipient_first_name,
                'email' => $recipient_email,
                'subject' => __('Successful Event Booking', 'spaces_mnu_plugin'),
                'event_responsible_email' => $responsible_data['email'],
                'event_responsible_name' => $responsible_data['name'],
                'event_responsible_phone' => $responsible_data['phone'],
                'event_name' => $event_name,
                'event_location' => $event_location,
                'date' => date('d.m.Y', strtotime($formatted_event_date)),
                'event_public_start_time' => $event_public_start_time,
            ];
            $result = send_custom_email(
                $recipient_email,
                'success-booking-event',
                $email_data
            );
            if (is_wp_error($result)) {
                error_log($result->get_error_message());
            }
            wp_send_json_success(['message' => 'Booking created successfully.']);
        }
        public function register_scripts()
        {
            $current_user = wp_get_current_user();
            $mnu_role = get_user_meta($current_user->ID, 'mnu_role', true);

            // error_log('CURRENT User Role: ' . $mnu_role); // Проверка в debug.log


            wp_enqueue_script('toastify-js', SPACES_MNU_PLUGIN_URL . 'assets/js/toastify.min.js', [], '1.0', true);
            wp_enqueue_script('fullcalendar-js', SPACES_MNU_PLUGIN_URL . 'assets/js/fullcalendar.main.min.js', [], '1.0', true);
            wp_enqueue_script('fullcalendar-locales-all-js', SPACES_MNU_PLUGIN_URL . 'assets/js/fullcalendar.locales-all.min.js', [], '1.0', true);
            wp_enqueue_script('fancybox-js', SPACES_MNU_PLUGIN_URL . 'assets/js/fancybox.min.js', [], '1.0', true);
            wp_enqueue_script('carousel-js', SPACES_MNU_PLUGIN_URL . 'assets/js/carousel.min.js', [], '1.0', true);
            wp_enqueue_style('fullcalendar-css', SPACES_MNU_PLUGIN_URL . 'assets/css/fullcalendar.main.min.css');
            wp_enqueue_style('toastify-css', SPACES_MNU_PLUGIN_URL . 'assets/css/toastify.min.css');
            wp_enqueue_style('carousel-css', SPACES_MNU_PLUGIN_URL . 'assets/css/carousel.min.css');
            wp_enqueue_style('fancybox-css', SPACES_MNU_PLUGIN_URL . 'assets/css/fancybox.min.css');
            wp_localize_script('fullcalendar-js', 'spacesMnuData', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('spaces_mnu_nonce'),
                'current_users_role' => $mnu_role,
                'current_language' => get_locale(),
                'i18n' => [
                    'bookingCreated' => __('Booking(s) created successfully!', 'spaces_mnu_plugin'),
                    'alreadyStarted' => __('One or more selected slots are in the past or have already started.', 'spaces_mnu_plugin'),
                    'alreadyBooked' => __('You have already booked slots for this date.', 'spaces_mnu_plugin'),
                    'bookingFailed' => __('Failed to create booking(s).', 'spaces_mnu_plugin'),
                    'dayFailed' => __('Failed to create or find the day.', 'spaces_mnu_plugin'),
                    'bookingCancelled' => __('Booking(s) cancelled successfully!', 'spaces_mnu_plugin'),
                    'cancellationFailed' => __('Failed to cancel booking(s).', 'spaces_mnu_plugin'),
                    'enterCancellationReason' => __('Please enter a reason for cancellation.', 'spaces_mnu_plugin'),
                    'bookingsForDate' => __('Bookings for date', 'spaces_mnu_plugin'),
                    'noBookingsFound' => __('No bookings found for this date.', 'spaces_mnu_plugin'),
                    'cancel' => __('Cancel', 'spaces_mnu_plugin'),
                    'requestedBy' => __('Requested By', 'spaces_mnu_plugin'),
                    'resource' => __('Resource', 'spaces_mnu_plugin'),
                    'slots' => __('Slots', 'spaces_mnu_plugin'),
                    'reason' => __('Reason for booking', 'spaces_mnu_plugin'),
                    'comment' => __('Reason for cancellation', 'spaces_mnu_plugin'),
                    'actions' => __('Actions', 'spaces_mnu_plugin'),
                ]
            ]);
        }
        public static function install()
        {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $table_bookings = $wpdb->prefix . 'spaces_bookings';
            $sql_bookings = "CREATE TABLE $table_bookings (
                ID BIGINT(20) NOT NULL AUTO_INCREMENT,
                DayID BIGINT(20) NOT NULL,
                BookingDate DATE NOT NULL,
                SlotStart TIME NOT NULL,
                SlotEnd TIME NOT NULL,
                ResourceID BIGINT(20) NOT NULL,
                ResourceName TEXT,
                EventID BIGINT(20) NOT NULL,
                RequestedBy BIGINT(20) NOT NULL,
                Status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
                CancelledBy BIGINT(20),
                CancelledAt DATETIME,
                RequestReason TEXT,
                CancelComment TEXT,
                Priority BOOLEAN DEFAULT FALSE,
                CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
                ModifiedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                IsDeleted BOOLEAN DEFAULT FALSE,
                PRIMARY KEY (ID),
                INDEX idx_day_id (DayID),
                INDEX idx_resource_id (ResourceID),
                INDEX idx_event_id (EventID),
                INDEX idx_requested_by (RequestedBy),
                INDEX idx_status (Status),
                FOREIGN KEY (DayID) REFERENCES {$wpdb->prefix}spaces_days(ID) ON DELETE CASCADE
            ) $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql_bookings);
        }
    }
}
$spaces_booking_manager = new Spaces_MNU_Booking_AJAX();
