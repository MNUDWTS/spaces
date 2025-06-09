<?php
?>
<div>
    <h2 class="tw-text-2xl tw-font-bold tw-mb-4"><?= esc_html__('Our Staff', 'spaces_mnu_plugin'); ?></h2>
    <form id="staff-filter-form" class="tw-w-full tw-mb-4 tw-grid md:tw-grid-cols-5 tw-gap-1">
        <?php
        $current_user = wp_get_current_user();
        $current_user_department = get_user_meta($current_user->ID, 'department', true);
        ?>
        <input type="text" name="search" placeholder="<?= esc_attr__('Search by name...', 'spaces_mnu_plugin'); ?>" class="tw-w-full tw-rounded md:tw-col-span-2 tw-border tw-px-4 tw-py-2 tw-mr-2" />
        <select name="department" class="tw-w-full tw-rounded md:tw-col-span-2 tw-appearance-none tw-border tw-px-4 tw-py-2 tw-mr-2">
            <option value="" <?= empty($current_user_department) ? 'selected' : ''; ?>><?= esc_html__('All Departments', 'spaces_mnu_plugin'); ?></option>
            <?php foreach (Spaces_MNU_Plugin_User_Public::get_staff_departments() as $department_key => $department_name): ?>
                <option value="<?= esc_attr($department_key); ?>" <?= selected($current_user_department, $department_key, false); ?>>
                    <?= esc_html(__($department_name, 'spaces_mnu_plugin')); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="tw-w-full tw-rounded tw-bg-blue-500 tw-text-white tw-px-4 tw-py-2"><?= esc_html__('Filter', 'spaces_mnu_plugin'); ?></button>
    </form>
    <div id="staff-list" class="tw-overflow-auto tw-w-full tw-h-full tw-max-h-[95vh] tw-overflow-h-scroll">
    </div>
</div>