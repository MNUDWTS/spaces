<?php

?>
<div>
    <h2 class="tw-text-2xl tw-font-bold tw-mb-4"><?= esc_html__('Our Students', 'spaces_mnu_plugin'); ?></h2>
    <form id="students-filter-form" class="tw-w-full tw-mb-4 tw-grid md:tw-grid-cols-5 tw-gap-1">
        <?php
        $current_user = wp_get_current_user();
        $current_user_mnu_role = get_user_meta($current_user->ID, 'mnu_role', true);
        ?>
        <input type="text" name="student_search" placeholder="<?= esc_attr__('Search by name...', 'spaces_mnu_plugin'); ?>" class="tw-w-full tw-rounded md:tw-col-span-2 tw-border tw-px-4 tw-py-2 tw-mr-2" />
        <select name="student_mnu_role" class="tw-w-full tw-rounded md:tw-col-span-2 tw-appearance-none tw-border tw-px-4 tw-py-2 tw-mr-2">
            <option value="" <?= empty($current_user_mnu_role) ? 'selected' : ''; ?>><?= esc_html__('All Students', 'spaces_mnu_plugin'); ?></option>
            <?php foreach (Spaces_MNU_Plugin_User_Public::get_students_mnu_roles() as $mnu_role_key => $mnu_role_data): ?>
                <?php if (
                    isset($mnu_role_data['parent'])
                    && $mnu_role_data['parent'] === 'student'
                ) : ?>
                    <option value="<?= esc_attr($mnu_role_key); ?>" <?= selected($current_user_mnu_role, $mnu_role_key, false); ?>>
                        <?= esc_html(__($mnu_role_data['name'], 'spaces_mnu_plugin')); ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="tw-w-full tw-rounded tw-bg-blue-500 tw-text-white tw-px-4 tw-py-2"><?= esc_html__('Filter', 'spaces_mnu_plugin'); ?></button>
    </form>
    <div id="students-list" class="tw-overflow-auto tw-w-full tw-h-full tw-max-h-[70vh] tw-overflow-h-scroll">
    </div>
</div>