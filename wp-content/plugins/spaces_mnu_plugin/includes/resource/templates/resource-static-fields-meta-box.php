<?php
// resource-static-fields-meta-box.php

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
// $available_days = array_diff($all_days, $excluded_days);
// echo print_r($excluded_days);
// echo '<br/>';
// echo print_r($available_days);
?>
<style>
    /* Добавление спиннера к полю ввода при загрузке */
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


    <!-- Resource Type -->
    <div>
        <label for="resource_type" class="tw-block tw-font-medium tw-mb-1"><?= __('Resource Type', 'spaces_mnu_plugin'); ?></label>
        <select required name="resource_type" id="resource_type" class="tw-appearance-none tw-bg-slate-50 tw-w-full tw-border tw-rounded tw-px-3 tw-py-2">
            <option value=""><?= __('Select Resource Type', 'spaces_mnu_plugin'); ?></option>
            <?php foreach ($type_terms as $term) : ?>
                <option value="<?= esc_attr($term->slug); ?>" <?= selected($term->slug, $selected_type); ?>>
                    <?= esc_html(__($term->name, 'spaces_mnu_plugin')); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Resource Categories -->
    <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Resource Categories', 'spaces_mnu_plugin'); ?></label>
        <div id="resource_categories_container" class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-2">
            <?php
            foreach ($selected_categories as $category_slug) :
                $term = get_term_by('slug', $category_slug, 'resource_category');
                $category_name = $term ? __($term->name, 'spaces_mnu_plugin') : __($category_slug, 'spaces_mnu_plugin');
                $is_base_category = in_array($category_slug, array('all', 'all-space', 'all-equipment'));
            ?>
                <div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">
                    <?= esc_html($category_name); ?>
                    <?php if (!$is_base_category) : ?>
                        <span class="tw-ml-2 tw-cursor-pointer remove-resource-category" data-category-slug="<?= esc_attr($category_slug); ?>">×</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" id="resource_categories" name="resource_categories" value="<?= esc_attr(implode(',', $selected_categories)); ?>" />


        <input type="text" id="resource_category_search" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" placeholder="<?= __('Search categories...', 'spaces_mnu_plugin'); ?>" />
    </div>

    <!-- Images -->
    <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Images', 'spaces_mnu_plugin'); ?></label>
        <button type="button" id="upload_images_button" class="tw-bg-blue-500 tw-text-white tw-px-4 tw-py-2 tw-rounded"><?= __('Upload Images', 'spaces_mnu_plugin'); ?></button>
        <input type="hidden" id="resource_images" name="resource_images" value="<?= esc_attr(implode(',', $images)); ?>" />
        <div id="images_preview" class="tw-mt-2 tw-flex tw-flex-wrap">
            <?php foreach ($images as $image_id) : ?>
                <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                <div class="tw-relative tw-mr-2 tw-mb-2">
                    <img src="<?= esc_url($image_url); ?>" alt="" class="tw-w-20 tw-h-20 tw-object-cover tw-rounded" />
                    <span class="tw-absolute tw-top-0 tw-right-0 tw-bg-red-500 tw-text-white tw-rounded-full tw-w-5 tw-h-5 tw-flex tw-items-center tw-justify-center tw-cursor-pointer remove-image" data-image-id="<?= esc_attr($image_id); ?>">×</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Responsible Users -->
    <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Responsible Users', 'spaces_mnu_plugin'); ?></label>
        <div id="responsible_users_container" class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-2">
            <?php
            if (!in_array($current_user_id, $responsible_users)) {
                $responsible_users[] = $current_user_id;
            }

            foreach ($responsible_users as $user_id) :
                $user_info = get_userdata($user_id);
                if ($user_info) :
            ?>
                    <div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">
                        <?= esc_html($user_info->display_name); ?>
                        <?php if ($user_id != $current_user_id) : ?>
                            <span class="tw-ml-2 tw-cursor-pointer remove-responsible-user" data-user-id="<?= esc_attr($user_id); ?>">×</span>
                        <?php endif; ?>
                    </div>
            <?php
                endif;
            endforeach;
            ?>
        </div>
        <input type="hidden" id="resource_responsible" name="resource_responsible" value="<?= esc_attr(implode(',', $responsible_users)); ?>" />
        <input type="text" id="responsible_user_search" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" placeholder="<?= __('Search users...', 'spaces_mnu_plugin'); ?>" />
    </div>

    <!-- Access Roles -->
    <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Access Roles', 'spaces_mnu_plugin'); ?></label>
        <div id="access_roles_container" class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-2">
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
                        <span class="tw-ml-2 tw-cursor-pointer remove-access-role" data-role-id="<?= esc_attr($role_id); ?>">×</span>
                    </div>
            <?php
                endif;
            endforeach;
            ?>
        </div>
        <input type="hidden" id="resource_access" name="resource_access" value="<?= esc_attr(implode(',', $access_roles)); ?>" />
        <input type="text" id="access_role_search" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" placeholder="<?= __('Search roles...', 'spaces_mnu_plugin'); ?>" />
    </div>

    <!-- Floor Number -->
    <div>
        <label for="resource_floor" class="tw-block tw-font-medium tw-mb-1"><?= __('Floor Number', 'spaces_mnu_plugin'); ?></label>
        <input type="number" id="resource_floor" name="resource_floor" value="<?= esc_attr($floor); ?>" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" min="0" max="7" />
    </div>


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
            <?= __('Enable Availability for Booking', 'spaces_mnu_plugin'); ?>
        </label>
        <label class="tw-relative tw-inline-flex tw-cursor-pointer tw-items-center">
            <input type="checkbox" id="availability_enabled" name="resource_availability_enabled" value="true" <?= $availability_enabled ? 'checked' : ''; ?> class="tw-peer tw-sr-only" />
            <div class="tw-peer tw-h-6 tw-w-11 tw-rounded-full tw-border tw-bg-slate-200 after:tw-absolute after:tw-left-[2px] after:tw-top-0.5 after:tw-h-5 after:tw-w-5 after:tw-rounded-full after:tw-border after:tw-border-gray-300 after:tw-bg-white after:tw-transition-all after:tw-content-[''] peer-checked:tw-bg-slate-800 peer-checked:after:tw-translate-x-full peer-checked:after:tw-border-white peer-focus:tw-ring-green-300"></div>
        </label>
    </div>

    <!-- Available days and times -->
    <div>
        <label class="tw-block tw-font-medium tw-mb-1"><?= __('Available days and times', 'spaces_mnu_plugin'); ?></label>
        <?php foreach ($all_days as $day) : ?>
            <div class="tw-grid tw-grid-cols-3 tw-items-start tw-mb-2">
                <label class="tw-flex items-center tw-mr-4">
                    <input type="checkbox" name="resource_available_days[]" value="<?= $day; ?>" <?= in_array($day, $available_days) ? 'checked' : ''; ?> class="tw-mr-2" /> <?= __(ucfirst($day), 'spaces_mnu_plugin'); ?>
                </label>
                <div class="tw-col-span-2 tw-flex tw-items-center tw-mb-2">
                    <input type="time" name="resource_available_time[<?= $day; ?>][from]" value="<?= $available_time[$day]['from'] ?  esc_attr($available_time[$day]['from']) : "09:00"; ?>" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-mr-2" />
                    <span class="tw-mr-2">-</span>
                    <input type="time" name="resource_available_time[<?= $day; ?>][to]" value="<?= $available_time[$day]['to'] ? esc_attr($available_time[$day]['to']) : "21:00"; ?>" class="tw-border tw-rounded tw-px-3 tw-py-2" />
                </div>
            </div>
        <?php endforeach; ?>
        <small class="tw-text-gray-500"><?= __('Check the box to make the day available for booking. Set the time for each available day.', 'spaces_mnu_plugin'); ?></small>
    </div>


</div>