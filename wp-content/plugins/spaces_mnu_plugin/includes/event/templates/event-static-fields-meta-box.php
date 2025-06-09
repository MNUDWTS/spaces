<?php
// event-static-fields-meta-box.php

$floor = isset($variables['floor']) ? $variables['floor'] : '';
$images = isset($variables['images']) ? $variables['images'] : [];
$responsible_users = isset($variables['responsible_users']) ? $variables['responsible_users'] : [];
$access_roles = isset($variables['access_roles']) ? $variables['access_roles'] : [];
$type_terms = isset($variables['type_terms']) ? $variables['type_terms'] : [];
$selected_type = isset($variables['selected_type']) ? $variables['selected_type'] : '';
$selected_categories = isset($variables['selected_categories']) ? $variables['selected_categories'] : [];
$roles = isset($variables['roles']) ? $variables['roles'] : [];
$sorted_roles = isset($variables['sorted_roles']) ? $variables['sorted_roles'] : [];
$current_user_id = isset($variables['current_user_id']) ? $variables['current_user_id'] : 0;
$availability_enabled = isset($variables['availability_enabled']) ? $variables['availability_enabled'] : false;
$available_time = isset($variables['available_time']) ? $variables['available_time'] : [];
$all_days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
$available_days = isset($variables['available_days']) ? $variables['available_days'] : ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

$location = isset($variables['location']) ? $variables['location'] : '';

// Получаем название ресурса на разных языках
$resource_name_ru = get_post_meta($location, '_resource_name_ru', true); // Название на русском
$resource_name_kk = get_post_meta($location, '_resource_name_kk', true); // Название на казахском
$resource_name_en = get_post_meta($location, '_resource_name_en', true); // Название на английском
$resource_name = '';
// Выбираем правильное название в зависимости от текущего языка
$current_language = get_locale(); // Получаем текущий язык сайта

switch ($current_language) {
    case 'en_US':
        $resource_name = $resource_name_en;
        break;
    case 'kk_KZ':
        $resource_name = $resource_name_kk;
        break;
    case 'ru_RU':
    default:
        $resource_name = $resource_name_ru;
        break;
}

// Если название ресурса пустое (например, если ресурс не найден), показываем сам текст из $location
if (empty($resource_name)) {
    $resource_name = $location; // Значение location будет использоваться как текст
}




$date = isset($variables['date']) ? $variables['date'] : '';
$start_time = isset($variables['start_time']) ? $variables['start_time'] : '';
$end_time = isset($variables['end_time']) ? $variables['end_time'] : '';
$public_start_time = isset($variables['public_start_time']) ? $variables['public_start_time'] : '';
$seat_count = isset($variables['seat_count']) ? $variables['seat_count'] : '';


$resources = get_posts([
    'post_type' => 'resource',
    'numberposts' => -1,
    'post_status' => 'publish',
    'fields' => 'ids',
]);

$resource_options = [];
foreach ($resources as $resource_id) {
    $resource_options[] = [
        'id' => $resource_id,
        'title' => get_the_title($resource_id)
    ];
}

// echo '<pre>';
// var_dump($variables);
// echo '</pre>';
// echo 'here: ' . $location . ' ' . $date . ' ' . $start_time . ' ' . $end_time . ' ' . $seat_count;
?>
<style>
    .ui-autocomplete-loading {
        background: url('https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/images/loading.gif') right center no-repeat;
    }
</style>
<div class="tw-grid tw-gap-4 tw-mb-4">

    <div class="tw-relative tw-my-6">
        <div class="tw-absolute tw-inset-0 tw-flex tw-items-center" aria-hidden="true">
            <div class="tw-w-full tw-border-t tw-border-gray-300"></div>
        </div>
        <div class="tw-relative tw-flex tw-justify-center">
            <span class="tw-bg-white tw-p-2 tw-rounded tw-text-sm tw-text-gray-500"><?= __('Static Fields', 'spaces_mnu_plugin'); ?></span>
        </div>
    </div>

    <!-- Event Type -->
    <!-- <div>
        <label for="event_type" class="tw-block tw-font-medium tw-mb-1"><?= __('Event Type', 'spaces_mnu_plugin'); ?></label>
        <select name="event_type" id="event_type" class="tw-appearance-none tw-bg-slate-50 tw-w-full tw-border tw-rounded tw-px-3 tw-py-2">
            <option value=""><?= __('Select Event Type', 'spaces_mnu_plugin'); ?></option>
            <?php foreach ($type_terms as $term) : ?>
                <option value="<?= esc_attr($term->slug); ?>" <?= selected($term->slug, $selected_type); ?>>
                    <?= esc_html(__($term->name, 'spaces_mnu_plugin')); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div> -->

    <!-- Event Categories -->
    <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Event Categories', 'spaces_mnu_plugin'); ?></label>
        <div id="event_categories_container" class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-2">
            <?php
            foreach ($selected_categories as $category_slug) :
                $term = get_term_by('slug', $category_slug, 'event_category');
                $category_name = $term ? __($term->name, 'spaces_mnu_plugin') : __($category_slug, 'spaces_mnu_plugin');
                $is_base_category = in_array($category_slug, array('all', 'all-events', 'general-events'));
            ?>
                <div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">
                    <?= esc_html($category_name); ?>
                    <?php if (!$is_base_category) : ?>
                        <span class="tw-ml-2 tw-cursor-pointer remove-event-category" data-category-slug="<?= esc_attr($category_slug); ?>">×</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" id="event_categories" name="event_categories" value="<?= esc_attr(implode(',', $selected_categories)); ?>" />
        <input type="text" id="event_category_search" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" placeholder="<?= __('Search categories...', 'spaces_mnu_plugin'); ?>" />
    </div>

    <!-- Images -->
    <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Images', 'spaces_mnu_plugin'); ?></label>
        <button type="button" id="upload_event_images_button" class="tw-bg-blue-500 tw-text-white tw-px-4 tw-py-2 tw-rounded"><?= __('Upload Images', 'spaces_mnu_plugin'); ?></button>
        <input type="hidden" id="event_images" name="event_images" value="<?= esc_attr(implode(',', $images)); ?>" />
        <div id="event_images_preview" class="tw-mt-2 tw-flex tw-flex-wrap">
            <?php foreach ($images as $image_id) : ?>
                <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                <div class="tw-relative tw-mr-2 tw-mb-2">
                    <img src="<?= esc_url($image_url); ?>" alt="" class="tw-w-20 tw-h-20 tw-object-cover tw-rounded" />
                    <span class="tw-absolute tw-top-0 tw-right-0 tw-bg-red-500 tw-text-white tw-rounded-full tw-w-5 tw-h-5 tw-flex tw-items-center tw-justify-center tw-cursor-pointer remove-event-image" data-image-id="<?= esc_attr($image_id); ?>">×</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- responsible_users -->
    <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Responsible Users', 'spaces_mnu_plugin'); ?></label>
        <div id="event_responsible_users_container" class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-2">
            <?php
            // Добавляем текущего пользователя, если его нет в списке
            if (!in_array($current_user_id, $responsible_users)) {
                $responsible_users[] = $current_user_id;
            }

            // Отображаем пользователей
            foreach ($responsible_users as $user_id) :
                $user_info = get_userdata($user_id);
                if ($user_info) :
            ?>
                    <div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">
                        <?= esc_html($user_info->display_name); ?>
                        <?php if ($user_id != $current_user_id) : ?>
                            <span class="tw-ml-2 tw-cursor-pointer remove-event-responsible-user" data-user-id="<?= esc_attr($user_id); ?>">×</span>
                        <?php endif; ?>
                    </div>
            <?php
                endif;
            endforeach;
            ?>
        </div>
        <input type="hidden" id="event_responsible" name="event_responsible" value="<?= esc_attr(implode(',', $responsible_users)); ?>" />
        <input type="text" id="event_responsible_user_search" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" placeholder="<?= __('Search Responsible Users...', 'spaces_mnu_plugin'); ?>" />
    </div>


    <!-- Access Roles -->
    <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Access Roles', 'spaces_mnu_plugin'); ?></label>
        <div id="event_access_roles_container" class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-2">
            <?php
            $excluded_roles = ['security_staff', 'management_staff', 'senior_management_staff'];

            foreach ($access_roles as $role_id) :
                if (in_array($role_id, $excluded_roles, true)) {
                    continue;
                }
                if (isset($roles[$role_id])) :
            ?>
                    <div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">
                        <?= esc_html(__($roles[$role_id]['name'], 'spaces_mnu_plugin')); ?>
                        <span class="tw-ml-2 tw-cursor-pointer remove-event-access-role" data-role-id="<?= esc_attr($role_id); ?>">×</span>
                    </div>
            <?php
                endif;
            endforeach;
            ?>
        </div>
        <input type="hidden" id="event_access" name="event_access" value="<?= esc_attr(implode(',', $access_roles)); ?>" />
        <input type="text" id="event_access_role_search" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" placeholder="<?= __('Search roles...', 'spaces_mnu_plugin'); ?>" />
    </div>

    <!-- Floor Number -->
    <!-- <div>
        <label for="event_floor" class="tw-block tw-font-medium tw-mb-1"><?= __('Floor Number', 'spaces_mnu_plugin'); ?></label>
        <input type="number" id="event_floor" name="event_floor" value="<?= esc_attr($floor); ?>" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" min="0" max="7" />
    </div> -->

    <!-- Availability Settings -->
    <div class="tw-relative tw-my-6">
        <div class="tw-absolute tw-inset-0 tw-flex tw-items-center" aria-hidden="true">
            <div class="tw-w-full tw-border-t tw-border-gray-300"></div>
        </div>
        <div class="tw-relative tw-flex tw-justify-center">
            <span class="tw-bg-white tw-p-2 tw-rounded tw-text-sm tw-text-gray-500"><?= __('Availability Settings', 'spaces_mnu_plugin'); ?></span>
        </div>
    </div>

    <!-- Availability Enabled -->
    <div>
        <label for="availability_enabled" class="tw-block tw-font-medium tw-mb-1">
            <?= __('Enable Availability for Registration', 'spaces_mnu_plugin'); ?>
        </label>
        <label class="tw-relative tw-inline-flex tw-cursor-pointer tw-items-center">
            <input type="checkbox" id="availability_enabled" name="event_availability_enabled" value="true" <?= $availability_enabled ? 'checked' : ''; ?> class="tw-peer tw-sr-only" />
            <div class="tw-peer tw-h-6 tw-w-11 tw-rounded-full tw-border tw-bg-slate-200 after:tw-absolute after:tw-left-[2px] after:tw-top-0.5 after:tw-h-5 after:tw-w-5 after:tw-rounded-full after:tw-border after:tw-border-gray-300 after:tw-bg-white after:tw-transition-all after:tw-content-[''] peer-checked:tw-bg-slate-800 peer-checked:after:tw-translate-x-full peer-checked:after:tw-border-white peer-focus:tw-ring-green-300"></div>
        </label>
    </div>

    <!-- Available days and times -->
    <!-- <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Available days and times', 'spaces_mnu_plugin'); ?></label>
        <?php foreach ($all_days as $day) : ?>
            <div class="tw-grid tw-grid-cols-3 tw-items-start tw-mb-2">
                <label class="tw-flex items-center tw-mr-4">
                    <input type="checkbox" name="event_available_days[]" value="<?= $day; ?>" <?= in_array($day, $available_days) ? 'checked' : ''; ?> class="tw-mr-2" /> <?= __(ucfirst($day), 'spaces_mnu_plugin'); ?>
                </label>
                <div class="tw-col-span-2 tw-flex tw-items-center tw-mb-2">
                    <input type="time" name="event_available_time[<?= $day; ?>][from]" value="<?= $available_time[$day]['from'] ? esc_attr($available_time[$day]['from']) : "09:00"; ?>" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-mr-2" />
                    <span class="tw-mr-2">-</span>
                    <input type="time" name="event_available_time[<?= $day; ?>][to]" value="<?= $available_time[$day]['to'] ? esc_attr($available_time[$day]['to']) : "21:00"; ?>" class="tw-border tw-rounded tw-px-3 tw-py-2" />
                </div>
            </div>
        <?php endforeach; ?>
        <small class="tw-text-gray-500"><?= __('Check the box to make the day available for registration. Set the time for each available day.', 'spaces_mnu_plugin'); ?></small>
    </div> -->

    <!-- Event Location -->
    <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Event Location', 'spaces_mnu_plugin'); ?></label>
        <div id="location_tags_container" class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-2">
            <?php
            // Check if there are any existing locations to display as tags
            $locations = explode(',', esc_attr($location)); // Assuming $location is a CSV of IDs or text
            foreach ($locations as $loc) :
                if ($loc) :

                    $resource_name = '';

                    // Check if $loc is a numeric ID (likely a resource ID)
                    if (is_numeric($loc)) {
                        // Try to get the resource name based on current language
                        $resource_name_ru = get_post_meta($loc, '_resource_name_ru', true); // Название на русском
                        $resource_name_kk = get_post_meta($loc, '_resource_name_kk', true); // Название на казахском
                        $resource_name_en = get_post_meta($loc, '_resource_name_en', true); // Название на английском

                        // Выбираем правильное название в зависимости от текущего языка
                        $current_language = get_locale(); // Получаем текущий язык сайта
                        switch ($current_language) {
                            case 'en_US':
                                $resource_name = $resource_name_en;
                                break;
                            case 'kk_KZ':
                                $resource_name = $resource_name_kk;
                                break;
                            case 'ru_RU':
                            default:
                                $resource_name = $resource_name_ru;
                                break;
                        }
                    }

                    // If we couldn't get a resource name (i.e., it's custom text), use $loc as is
                    if (!$resource_name) {
                        $resource_name = $loc;
                    }
            ?>
                    <div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">
                        <?= esc_html($resource_name); ?>
                        <span class="tw-ml-2 tw-cursor-pointer remove-location" data-location-id="<?= esc_attr($loc); ?>">×</span>
                    </div>
            <?php
                endif;
            endforeach;
            ?>
        </div>
        <input type="hidden" id="event_location" name="event_location" value="<?= esc_attr($location); ?>" />
        <input type="text" id="event_location_search" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" placeholder="<?= __('Search resources or enter location', 'spaces_mnu_plugin'); ?>" value="<?= esc_attr($resource_name); ?>" />
        <small class="tw-text-gray-500"><?= __('Choose an existing resource or enter a custom location.', 'spaces_mnu_plugin'); ?></small>
    </div>


    <!-- Date -->
    <div>
        <label for="event_date" class="tw-block tw-font-medium tw-mb-1"><?= __('Event Date', 'spaces_mnu_plugin'); ?></label>
        <input type="date" id="event_date" name="event_date" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" value="<?= esc_attr($date); ?>" />
    </div>

    <!-- Duration (Start and End Time) -->
    <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Event Duration', 'spaces_mnu_plugin'); ?></label>
        <div class="tw-flex tw-items-center tw-space-x-2">
            <input type="time" id="event_start_time" name="event_start_time" class="tw-border tw-rounded tw-px-3 tw-py-2" value="<?= esc_attr($start_time); ?>" />
            <span class="tw-mx-2">-</span>
            <input type="time" id="event_end_time" name="event_end_time" class="tw-border tw-rounded tw-px-3 tw-py-2" value="<?= esc_attr($end_time); ?>" />
        </div>
        <small class="tw-text-gray-500"><?= __('Select the technical start and end time of the event.', 'spaces_mnu_plugin'); ?></small>
    </div>

    <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Event Start Time', 'spaces_mnu_plugin'); ?></label>
        <div class="tw-flex tw-items-center tw-space-x-2">
            <input type="time" id="event_public_start_time" name="event_public_start_time" class="tw-border tw-rounded tw-px-3 tw-py-2" value="<?= esc_attr($public_start_time); ?>" />
        </div>
        <small class="tw-text-gray-500"><?= __('Select the public start time of the event.', 'spaces_mnu_plugin'); ?></small>
    </div>

    <!-- Seat Count -->
    <div>
        <label for="event_seat_count" class="tw-block tw-font-medium tw-mb-1"><?= __('Seat Count', 'spaces_mnu_plugin'); ?></label>
        <input type="number" id="event_seat_count" name="event_seat_count" min="1" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" value="<?= esc_attr($seat_count); ?>" />
        <small class="tw-text-gray-500"><?= __('Specify the number of available seats for the event.', 'spaces_mnu_plugin'); ?></small>
    </div>


</div>