<?php

/**
 * Template for displaying a single event post
 */

get_header();
$all_meta = get_post_meta(get_the_ID());
?>
<?php
the_post();
$post_id = get_the_ID();
$event_type_terms = wp_get_post_terms($post_id, 'event_type', array('fields' => 'names'));
if (!empty($event_type_terms) && !is_wp_error($event_type_terms)) {
    $event_type = $event_type_terms[0];
}
$unfiltered_event_categories = wp_get_post_terms($post_id, 'event_category');
$excluded_categories = array('all', 'general-events', 'private-events');
$event_categories = array_filter($unfiltered_event_categories, function ($term) use ($excluded_categories) {
    return !in_array($term->slug, $excluded_categories);
});
$event_images = get_post_meta($post_id, '_event_images', true);
$responsible_users = get_post_meta($post_id, '_event_responsible', true);
$event_access_roles = get_post_meta($post_id, '_event_access', true);
$event_floor = get_post_meta($post_id, '_event_floor', true);

$event_location = get_post_meta($post_id, '_event_location', true);
$resource_name = sanitize_text_field($event_location);
$resource_id = null;
// Проверяем, является ли $event_location ID ресурса
if (is_numeric($event_location)) {
    $resource_id = intval($event_location);

    // Проверяем, существует ли запись типа resource с данным ID
    $resource_post = get_post($resource_id);
    if ($resource_post && $resource_post->post_type === 'resource') {
        $resource_name = null; // Оставляем ResourceName пустым, если это ID
    } else {
        // Если ID указан, но соответствующей записи нет
        $resource_id = null;
        $resource_name = 'Invalid resource ID'; // Или выполните fallback-действие
    }
}
// echo $resource_name;

// error_log('Event Location Check: ' . print_r(compact('resource_id', 'resource_name'), true));

$event_date = get_post_meta($post_id, '_event_date', true);
$event_start_time = get_post_meta($post_id, '_event_start_time', true);
$event_end_time = get_post_meta($post_id, '_event_end_time', true);
$event_public_start_time = get_post_meta($post_id, '_event_public_start_time', true);
$event_seat_count = get_post_meta($post_id, '_event_seat_count', true);
// echo $event_public_start_time;
$event_name_ru = get_post_meta($post_id, '_event_name_ru', true);
$event_name_kk = get_post_meta($post_id, '_event_name_kk', true);
$event_name_en = get_post_meta($post_id, '_event_name_en', true);
$event_description_ru = get_post_meta($post_id, '_event_description_ru', true);
$event_description_kk = get_post_meta($post_id, '_event_description_kk', true);
$event_description_en = get_post_meta($post_id, '_event_description_en', true);
$event_metadata_ru = get_post_meta($post_id, '_event_metadata_ru', true);
$event_metadata_kk = get_post_meta($post_id, '_event_metadata_kk', true);
$event_metadata_en = get_post_meta($post_id, '_event_metadata_en', true);
if ($event_location) {
    $location_name_ru = get_post_meta($event_location, '_resource_name_ru', true);
    $location_name_kk = get_post_meta($event_location, '_resource_name_kk', true);
    $location_name_en = get_post_meta($event_location, '_resource_name_en', true);
}
$event_availability_enabled = get_post_meta($post_id, '_event_availability_enabled', true);
$event_available_time = get_post_meta($post_id, '_event_available_time', true);
$event_available_days = get_post_meta($post_id, '_event_available_days', true);

$current_language = get_locale();
$event_name = $event_description = $event_metadata = '';
$location_name = '';
if ($current_language === 'en_US') {
    $event_name = $event_name_en;
    $event_description = $event_description_en;
    $event_metadata = $event_metadata_en;
    $location_name = $location_name_en;
} elseif ($current_language === 'kk') {
    $event_name = $event_name_kk;
    $event_description = $event_description_kk;
    $event_metadata = $event_metadata_kk;
    $location_name = $location_name_kk;
} else {
    $event_name = $event_name_ru;
    $event_description = $event_description_ru;
    $event_metadata = $event_metadata_ru;
    $location_name = $location_name_ru;
}


$current_user_id = get_current_user_id();

// echo print_r($responsible_users, true);
// echo $current_user_id;

?>
<main id="primary" class="site-main">
    <div class="event-page-container tw-max-w-7xl tw-w-full tw-mx-auto tw-px-2 tw-py-4 sm:tw-p-8">
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-12 tw-gap-8">

            <!-- Sidebar -->
            <div class="md:tw-col-span-4 tw-w-full tw-flex tw-flex-col tw-gap-4">
                <div class="tw-w-full tw-bg-[#F2F2F2] tw-p-4 tw-rounded-xl tw-h-fit tw-space-y-4">

                    <!-- Event images -->
                    <div id="eventGalleryContainer tw-my-8">
                        <div id="productCarousel" class="f-carousel tw-md:order-last">
                            <?php
                            $images = [];
                            foreach ($event_images as $image_id) {
                                $image_url = wp_get_attachment_url($image_id);
                                if ($image_url) {
                                    $images[$image_url] = $image_url;
                                }
                            }
                            foreach ($images as $thumb => $large) {
                                echo '<div class="f-carousel__slide" data-fancybox="gallery" data-src="' . esc_url($large) . '">';
                                echo '<img alt="" src="' . esc_url($thumb) . '"/>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Event information -->
                    <div class="event-info tw-space-y-4">
                        <?php


                        if ($event_name) {
                            echo '<h3 class="tw-text-2xl tw-font-bold">' . esc_html($event_name) . '</h3>';
                        }

                        if ($location_name) {
                            echo '<div class="tw-flex tw-gap-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_767_11599)">
                                <path d="M4.2609 12.5968L11.2116 12.6253C11.3546 12.6253 11.4023 12.673 11.4023 12.816L11.4214 19.7096C11.4214 21.1302 13.1281 21.4639 13.7669 20.0814L20.813 4.93094C21.4517 3.53889 20.3553 2.62357 19.0205 3.24332L3.78417 10.3085C2.56374 10.871 2.8021 12.5872 4.2609 12.5968Z" fill="#999999"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_767_11599">
                                <rect width="18" height="17.9628" fill="white" transform="translate(3 3)"/>
                                </clipPath>
                                </defs>
                            </svg><h3 class="tw-text-xl">' .
                                esc_html($location_name) . '</h3>
                            </div>';
                        } else {
                            echo '<div class="tw-flex tw-gap-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_767_11599)">
                                <path d="M4.2609 12.5968L11.2116 12.6253C11.3546 12.6253 11.4023 12.673 11.4023 12.816L11.4214 19.7096C11.4214 21.1302 13.1281 21.4639 13.7669 20.0814L20.813 4.93094C21.4517 3.53889 20.3553 2.62357 19.0205 3.24332L3.78417 10.3085C2.56374 10.871 2.8021 12.5872 4.2609 12.5968Z" fill="#999999"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_767_11599">
                                <rect width="18" height="17.9628" fill="white" transform="translate(3 3)"/>
                                </clipPath>
                                </defs>
                            </svg><h3 class="tw-text-xl">' .
                                esc_html($resource_name) . '</h3>
                            </div>';
                        }

                        if ($event_date) {
                            $formatted_date = date('d.m.Y', strtotime($event_date));
                            echo
                            '<div class="tw-flex tw-gap-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_767_11585)">
                                <path d="M5.83592 20.6448H18.1731C20.0607 20.6448 21 19.7055 21 17.8449V6.81737C21 4.95687 20.0607 4.01758 18.1731 4.01758H5.83592C3.94832 4.01758 3 4.94783 3 6.81737V17.8449C3 19.7145 3.94832 20.6448 5.83592 20.6448ZM5.70045 19.1906C4.89664 19.1906 4.45409 18.7662 4.45409 17.9262V9.40944C4.45409 8.57854 4.89664 8.14502 5.70045 8.14502H18.2905C19.0943 8.14502 19.5459 8.57854 19.5459 9.40944V17.9262C19.5459 18.7662 19.0943 19.1906 18.2905 19.1906H5.70045ZM10.2433 11.3874H10.7762C11.0923 11.3874 11.1917 11.297 11.1917 10.9809V10.4481C11.1917 10.132 11.0923 10.0326 10.7762 10.0326H10.2433C9.92724 10.0326 9.81886 10.132 9.81886 10.4481V10.9809C9.81886 11.297 9.92724 11.3874 10.2433 11.3874ZM13.2419 11.3874H13.7747C14.0908 11.3874 14.1991 11.297 14.1991 10.9809V10.4481C14.1991 10.132 14.0908 10.0326 13.7747 10.0326H13.2419C12.9257 10.0326 12.8174 10.132 12.8174 10.4481V10.9809C12.8174 11.297 12.9257 11.3874 13.2419 11.3874ZM16.2403 11.3874H16.7732C17.0893 11.3874 17.1977 11.297 17.1977 10.9809V10.4481C17.1977 10.132 17.0893 10.0326 16.7732 10.0326H16.2403C15.9242 10.0326 15.8249 10.132 15.8249 10.4481V10.9809C15.8249 11.297 15.9242 11.3874 16.2403 11.3874ZM7.24485 14.3407H7.76868C8.09383 14.3407 8.19317 14.2504 8.19317 13.9343V13.4014C8.19317 13.0853 8.09383 12.995 7.76868 12.995H7.24485C6.91972 12.995 6.82037 13.0853 6.82037 13.4014V13.9343C6.82037 14.2504 6.91972 14.3407 7.24485 14.3407ZM10.2433 14.3407H10.7762C11.0923 14.3407 11.1917 14.2504 11.1917 13.9343V13.4014C11.1917 13.0853 11.0923 12.995 10.7762 12.995H10.2433C9.92724 12.995 9.81886 13.0853 9.81886 13.4014V13.9343C9.81886 14.2504 9.92724 14.3407 10.2433 14.3407ZM13.2419 14.3407H13.7747C14.0908 14.3407 14.1991 14.2504 14.1991 13.9343V13.4014C14.1991 13.0853 14.0908 12.995 13.7747 12.995H13.2419C12.9257 12.995 12.8174 13.0853 12.8174 13.4014V13.9343C12.8174 14.2504 12.9257 14.3407 13.2419 14.3407ZM16.2403 14.3407H16.7732C17.0893 14.3407 17.1977 14.2504 17.1977 13.9343V13.4014C17.1977 13.0853 17.0893 12.995 16.7732 12.995H16.2403C15.9242 12.995 15.8249 13.0853 15.8249 13.4014V13.9343C15.8249 14.2504 15.9242 14.3407 16.2403 14.3407ZM7.24485 17.303H7.76868C8.09383 17.303 8.19317 17.2037 8.19317 16.8876V16.3547C8.19317 16.0386 8.09383 15.9483 7.76868 15.9483H7.24485C6.91972 15.9483 6.82037 16.0386 6.82037 16.3547V16.8876C6.82037 17.2037 6.91972 17.303 7.24485 17.303ZM10.2433 17.303H10.7762C11.0923 17.303 11.1917 17.2037 11.1917 16.8876V16.3547C11.1917 16.0386 11.0923 15.9483 10.7762 15.9483H10.2433C9.92724 15.9483 9.81886 16.0386 9.81886 16.3547V16.8876C9.81886 17.2037 9.92724 17.303 10.2433 17.303ZM13.2419 17.303H13.7747C14.0908 17.303 14.1991 17.2037 14.1991 16.8876V16.3547C14.1991 16.0386 14.0908 15.9483 13.7747 15.9483H13.2419C12.9257 15.9483 12.8174 16.0386 12.8174 16.3547V16.8876C12.8174 17.2037 12.9257 17.303 13.2419 17.303Z" fill="#999999"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_767_11585">
                                <rect width="18" height="16.6453" fill="white" transform="translate(3 4)"/>
                                </clipPath>
                                </defs>
                                </svg><h3 class="tw-text-xl">' .
                                esc_html($formatted_date) . '</h3>
                            </div>';
                        }

                        if ($event_public_start_time) {
                            echo
                            '<div class="tw-flex tw-gap-4">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_882_11661)">
                                    <path d="M7.3853 12.9481C7.03236 12.9481 6.76766 12.6835 6.76766 12.3307C6.76766 11.9868 7.03236 11.7222 7.3853 11.7222H11.3824V6.38657C11.3824 6.04262 11.6471 5.77805 11.9912 5.77805C12.3353 5.77805 12.6088 6.04262 12.6088 6.38657V12.3307C12.6088 12.6835 12.3353 12.9481 11.9912 12.9481H7.3853ZM12 20.9911C16.9236 20.9911 21 16.9079 21 11.9956C21 7.07447 16.9148 3 11.9912 3C7.07648 3 3 7.07447 3 11.9956C3 16.9079 7.0853 20.9911 12 20.9911Z" fill="#999999"/>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_882_11661">
                                    <rect width="18" height="18" fill="white" transform="translate(3 3)"/>
                                    </clipPath>
                                    </defs>
                                </svg><h3 class="tw-text-xl">' .
                                esc_html($event_public_start_time) . '</h3>
                            </div>';
                        }

                        if ($event_seat_count) {
                            echo
                            '<div class="tw-flex tw-gap-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_767_11606)">
                                <path d="M7.12927 12.1975C8.42903 12.1975 9.56115 11.0236 9.56115 9.50711C9.56115 7.99767 8.42206 6.89355 7.12927 6.89355C5.82949 6.89355 4.69042 8.02563 4.69042 9.52108C4.69042 11.0236 5.8225 12.1975 7.12927 12.1975ZM3.22991 18.5078H11.0147C11.9022 18.5078 12.2446 18.1584 12.2446 17.5225C12.2446 15.5937 10.2739 13.2667 7.12228 13.2667C3.97065 13.2667 2 15.5937 2 17.5225C2 18.1584 2.34941 18.5078 3.22991 18.5078Z" fill="#999999"/>
                                <path d="M10.7768 19.4588H19.5608C21.3986 19.4588 21.9997 18.5713 21.9997 17.579C21.9997 15.2309 19.3092 12.3379 15.1653 12.3379C11.0283 12.3379 8.33789 15.2309 8.33789 17.579C8.33789 18.5713 8.93886 19.4588 10.7768 19.4588Z" fill="#EBECF8"/>
                                <path d="M15.173 12.037C16.6755 12.037 17.9682 10.6953 17.9682 8.94825C17.9682 7.22218 16.6685 5.94336 15.173 5.94336C13.6776 5.94336 12.3778 7.25014 12.3778 8.96222C12.3778 10.6953 13.6706 12.037 15.173 12.037ZM10.7775 18.508H19.5616C20.6587 18.508 21.05 18.1935 21.05 17.5786C21.05 15.7756 18.7929 13.2878 15.166 13.2878C11.5462 13.2878 9.28906 15.7756 9.28906 17.5786C9.28906 18.1935 9.68037 18.508 10.7775 18.508Z" fill="#999999"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_767_11606">
                                <rect width="20" height="14.4584" fill="white" transform="translate(2 5)"/>
                                </clipPath>
                                </defs>
                                </svg>
                                <h3 class="tw-text-xl"><span id="bookedSeatsCount" class="tw-text-[#999999]"></span><span class="tw-text-[#999999]"> / </span>' .
                                esc_html($event_seat_count) . '</h3>
                            </div>';
                        }

                        // if (isset($event_floor)) {
                        //     echo '<p class="tw-text-gray-700 tw-mb-4"><strong>' . esc_html__('Floor', 'spaces_mnu_plugin') . ': </strong> ' . esc_html($event_floor) . '</p>';
                        // }

                        if (is_array($event_metadata)) {
                            echo '<ul class="tw-list-inside tw-space-y-4">';
                            foreach ($event_metadata as $key => $value) {
                                echo '<li><strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '</li>';
                            }
                            echo '</ul>';
                        }

                        if (is_array($event_categories)) {
                            echo '<div class="tw-flex tw-flex-wrap tw-gap-4">';
                            foreach ($event_categories as $category) {
                                echo '<div class="tw-bg-white tw-px-4 tw-py-2 tw-rounded-xl tw-text-sm">' . esc_html(__($category->name, 'spaces_mnu_plugin')) . '</div>';
                            }
                            echo '</div>';
                        }

                        ?>
                    </div>

                </div>
            </div>

            <!-- Content Area -->
            <div class="md:tw-col-span-8 tw-w-full tw-bg-[#F2F2F2] tw-p-4 tw-rounded-xl tw-space-y-8">
                <?php if ($event_description) : ?>
                    <div class="tw-space-y-4"><?= apply_filters('the_content', $event_description) ?></div>
                <?php endif; ?>
                <?php if (!in_array($current_user_id, $responsible_users)) : ?>
                    <?php if ($event_availability_enabled == 'true'): ?>
                        <div class="tw-space-y-2">
                            <?php if (is_user_logged_in()): ?>
                                <?php if (check_access($current_user_id, $event_access_roles)): ?>
                                    <?php if ($event_seat_count): ?>
                                        <form id="event-booking-form">
                                            <input type="hidden" id="event_id" name="event_id" value="<?= $post_id; ?>">
                                            <input type="hidden" id="event_date" name="event_date" value="<?= $event_date; ?>">
                                            <input type="hidden" id="event_public_start_time" name="event_public_start_time" value="<?= $event_public_start_time; ?>">
                                            <input type="hidden" id="reason" name="reason" value="<?= $event_name; ?>">
                                            <input type="hidden" id="resource_id" name="resource_id" value="<?= $resource_id; ?>">
                                            <input type="hidden" id="resource_name" name="resource_name" value="<?= $resource_name; ?>">

                                            <button type="submit" id="book-slots-btn" class="tw-flex tw-gap-4 tw-justify-center tw-items-center tw-w-full tw-mt-4 tw-px-2 tw-py-4 tw-bg-[#262626] hover:tw-bg-[#333333] tw-text-white tw-rounded">
                                                <svg id="book-slots-btn-loader" style="display: none;" class="tw-h-8 tw-w-8 tw-animate-spin tw-text-white" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="16" height="16">
                                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="tw-text-gray-500"></path>
                                                </svg>
                                                <?= __('Book', 'spaces_mnu_plugin'); ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <h3 class="tw-text-lg tw-text-[#717171]"><?= __('The event does not require registration to participate.', 'spaces_mnu_plugin'); ?></h3>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <h3 class="tw-text-lg tw-text-[#717171]"><?= __('You do not have access to this event.', 'spaces_mnu_plugin'); ?></h3>
                                <?php endif; ?>

                            <?php else: ?>
                                <h3 class="tw-text-lg tw-text-[#717171]"><?= __('You must be logged in to register for this event.', 'spaces_mnu_plugin'); ?></h3>
                            <?php endif; ?>
                        </div>

                    <?php else: ?>
                        <h3 class="tw-text-lg tw-text-[#717171]"><?= __('This event is not available for booking', 'spaces_mnu_plugin'); ?></h3>
                    <?php endif; ?>
                <?php else: ?>
                    <?php
                    $current_event_date = get_post_meta($post_id, '_event_date', true);
                    // echo $current_event_date;
                    global $wpdb;
                    $participants = $wpdb->get_results(
                        $wpdb->prepare(
                            "SELECT RequestedBy FROM {$wpdb->prefix}spaces_bookings 
                                WHERE EventID = %d
                                AND BookingDate = %s
                                AND Status = 'approved'",
                            $post_id,
                            $current_event_date
                        )
                    );
                    // echo 'participants: ';
                    // echo print_r($participants, true);
                    ?>
                    <div class="tw-relative tw-my-24 tw-mx-4 tw-p-4 tw-w-full tw-divide-y tw-h-[400px] tw-overflow-y-scroll">
                        <div class="tw-my-4 tw-w-full">
                            <?php
                            if ($participants) : ?>
                                <h3 class="tw-text-lg tw-text-[#717171] tw-my-4"><?= __('Participants', 'spaces_mnu_plugin'); ?>:</h3>
                                <ul class="tw-h-[500px] tw-overflow-y-auto">

                                    <?php foreach ($participants as $participant) :
                                        $user_info = get_userdata($participant->RequestedBy);
                                        if ($user_info) { ?>
                                            <li><?= esc_html($user_info->display_name) . ': ' . esc_html($user_info->user_email); ?></li>
                                    <?php }
                                    endforeach; ?>

                                </ul>
                            <?php else : ?>
                                <h3 class="tw-text-lg tw-text-[#717171]"><?= __('No participants yet.', 'spaces_mnu_plugin'); ?></h3>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var availability_enabled = <?= json_encode($event_availability_enabled); ?>;
        var eventID = <?= json_encode($post_id); ?>;
        const seatCount = <?= json_encode($event_seat_count) ?>;
        const preloader = document.getElementById('slots-preloader');
        const container = document.getElementById('slots');

        function showPreloader() {
            container.classList.remove('tw-block');
            container.classList.add('tw-hidden');
            preloader.classList.remove('tw-hidden');
            preloader.classList.add('tw-flex')
        }

        function hidePreloader() {
            setTimeout(() => {
                preloader.classList.add('tw-hidden');
                preloader.classList.remove('tw-flex');
                container.classList.add('tw-block');
                container.classList.remove('tw-hidden');
            }, 200)
        }

        if (availability_enabled == 'true') {
            var available_days = <?= json_encode($event_available_days); ?>;
            var currentLanguage = '<?= $current_language; ?>';
        }





        function fetchBookedSeats(eventId, callback) {
            var bookingData = {
                action: 'get_booked_seats',
                event_id: eventId,
                // nonce: spacesMnuData.nonce
            };
            jQuery.post(spacesMnuData.ajax_url, bookingData, function(response) {
                if (response.success) {
                    callback(response.data.booked_count);
                } else {
                    callback(0); // Возвращаем 0 в случае ошибки
                }
            });
        }
        if (seatCount) {
            fetchBookedSeats(eventID, function(bookedCount) {
                document.getElementById('bookedSeatsCount').textContent = seatCount - bookedCount;
            });
        }

        function showToast(message) {
            Toastify({
                text: message,
                duration: 5000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                    background: "#E2474CCC"
                },
            }).showToast();
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Инициализация карусели
        const productCarousel = new Carousel(document.getElementById('productCarousel'), {
            transition: 'slide',
            preload: 1,
            infinite: true,
            Dots: false,
        });

        // Инициализация Fancybox для лайтбокса
        Fancybox.bind('[data-fancybox="gallery"]', {
            compact: false,
            idle: false,
            dragToClose: false,
            contentClick: () =>
                window.matchMedia('(max-width: 578px), (max-height: 578px)').matches ?
                'toggleMax' : 'toggleCover',

            animated: false,
            showClass: false,
            hideClass: false,

            Hash: false,
            Thumbs: false,

            Toolbar: {
                display: {
                    left: [],
                    middle: [],
                    right: ['close'],
                },
            },

            Carousel: {
                transition: 'fadeFast',
                preload: 3,
            },

            Images: {
                zoom: false,
                Panzoom: {
                    panMode: 'mousemove',
                    mouseMoveFactor: 1.1,
                },
            },
        });

    });
</script>

<script>
    jQuery(document).ready(function($) {
        let btn = document.querySelector('#book-slots-btn');
        let btnLoader = document.querySelector('#book-slots-btn-loader');
        $('#event-booking-form').on('submit', function(e) {
            e.preventDefault();

            if (btn) {
                btn.disabled = true;
                btn.style.cursor = 'not-allowed';
            }

            if (btnLoader) {
                btnLoader.style.display = 'block';
            }

            const formData = $(this).serializeArray(); // Сериализуем данные формы
            formData.push({
                name: 'action',
                value: 'create_event_booking'
            }); // Добавляем действие AJAX
            formData.push({
                name: 'nonce',
                value: spacesMnuData.nonce
            }); // Добавляем nonce для защиты

            // console.log('Form Data:', formData);

            $.ajax({
                url: spacesMnuData.ajax_url, // URL для AJAX
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    // console.log(response); // Для отладки
                    if (response.success) {
                        // alert('Booking successful!');
                        Toastify({
                            text: '<?php echo esc_js(__('Booking(s) created successfully!', 'spaces_mnu_plugin')); ?>' || spacesMnuData.i18n.bookingCreated,
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            style: {
                                background: "#00b09b",
                            },
                        }).showToast();

                        btn.disabled = false;
                        btn.style.cursor = 'pointer';
                        btnLoader.style.display = 'none';
                    } else {
                        // alert('Booking failed: ' + (response.data.message || 'Unknown error'));
                        Toastify({
                            text: '<?php echo esc_js(__('Booking failed.', 'spaces_mnu_plugin')); ?>',
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            style: {
                                background: "#E2474CCC",
                            },
                        }).showToast();

                        btn.disabled = false;
                        btn.style.cursor = 'pointer';
                        btnLoader.style.display = 'none';
                    }
                },
                error: function(xhr, status, error) {
                    // console.error('AJAX Error:', status, error);
                    // alert('An unexpected error occurred.');
                    Toastify({
                        text: '<?php echo esc_js(__('An unexpected error occurred.', 'spaces_mnu_plugin')); ?>',
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#E2474CCC",
                        },
                    }).showToast();

                    btn.disabled = false;
                    btn.style.cursor = 'pointer';
                    btnLoader.style.display = 'none';
                }
            });
        });
    });
</script>

<?php
get_footer();
