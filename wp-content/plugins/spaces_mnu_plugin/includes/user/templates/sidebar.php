<?php
$user = wp_get_current_user();
$user_role = get_the_author_meta('mnu_role', $user->ID);
$avatar_url = get_user_meta($user->ID, 'custom_avatar', true);
$user_role = get_the_author_meta('mnu_role', $user->ID);
$user_job_title = !empty(get_the_author_meta('job_title', $user->ID)) ?
    get_the_author_meta('job_title', $user->ID) :
    __('Not specified', 'spaces_mnu_plugin');
$department_key = get_the_author_meta('department', $user->ID);
$departments = get_option('spaces_mnu_plugin_departments');
$user_department = isset($departments[$department_key]) ? $departments[$department_key] : __('Not specified', 'spaces_mnu_plugin');
$user_phone = get_the_author_meta('phone', $user->ID);
$svg_arrow = '<svg width="24" height="24" viewBox="0 0 24 24" stroke="none" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M8 20L16 12L8 4" stroke="currentColor" fill="none" stroke-width="1.33" stroke-linecap="round"/>
</svg>';
?>
<div id="profile-sidebar" class="md:tw-col-span-4 tw-w-full tw-flex tw-flex-col tw-gap-4">
    <div class="tw-w-full tw-bg-[#F2F2F2] tw-p-4 tw-rounded-xl tw-h-fit">
        <div class="tw-w-full tw-flex tw-justify-end tw-items-center">
            <a href="#" id="edit-button" data-target="edit-profile" class="tw-border-none tw-p-2 tw-rounded-md">
                <svg width="24px" height="24px" viewBox="0 0 24 24" stroke="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>
        <div class="tw-w-full tw-flex tw-justify-between tw-items-center">
            <?php if ($avatar_url) : ?>
                <img src="<?php echo esc_url($avatar_url); ?>" alt="Аватар" class="tw-mx-auto tw-w-[150px] tw-object-cover tw-aspect-square tw-rounded-full" />
            <?php else : ?>
                <?php echo get_avatar($user->ID, 150, '', 'Аватар', [
                    'class' => 'tw-mx-auto tw-w-[150px] tw-object-cover tw-aspect-square tw-rounded-full'
                ]); ?>
            <?php endif; ?>
        </div>
        <div class="profile-info tw-space-y-4 tw-mt-2 tw-text-center">
            <h3 class="tw-text-2xl tw-font-bold"><?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></h3>
            <div class="tw-space-y-1 tw-text-center">
                <p class="tw-text-xl tw-text-[#797979] tw-font-light"><?= (strpos($user_role, 'staff') !== false) ? esc_html($user_job_title) : __('Student', 'spaces_mnu_plugin'); ?></p>
                <p class="tw-text-base tw-font-semibold tw-text-[#797979]"><?= __($user_department, 'spaces_mnu_plugin'); ?></p>
            </div>
            <div class="tw-space-y-2">
                <div class="tw-flex tw-gap-4 tw-justify-start tw-items-center tw-overflow-auto">
                    <svg class="tw-w-6 tw-h-6 tw-flex-shrink-0" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M18 4.5H6C3.79086 4.5 2 6.29086 2 8.5V17.5C2 19.7091 3.79086 21.5 6 21.5H18C20.2091 21.5 22 19.7091 22 17.5V8.5C22 6.29086 20.2091 4.5 18 4.5ZM6 6.09H18C19.0657 6.09204 20.0025 6.79663 20.3 7.82L12.76 13.41C12.5534 13.612 12.2732 13.721 11.9843 13.7115C11.6955 13.7021 11.423 13.5751 11.23 13.36L3.72 7.83C4.01175 6.80973 4.9389 6.10216 6 6.09ZM3.59 17.5C3.59 18.831 4.66899 19.91 6 19.91H18C19.3271 19.9045 20.4 18.8271 20.4 17.5V9.47L13.6 14.47C13.1654 14.8746 12.5938 15.0997 12 15.1C11.3827 15.0902 10.7911 14.8514 10.34 14.43L3.59 9.43V17.5Z" fill="#9E9E9E" />
                    </svg>

                    <p class="tw-text-start tw-w-full tw-text-black tw-text-base"><?php echo esc_html($user->user_email); ?></p>
                </div>
                <div class="tw-flex tw-gap-4 tw-justify-start tw-items-center tw-overflow-auto">
                    <svg class="tw-w-6 tw-h-6 tw-flex-shrink-0" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.6383 2.50024H8.36167C7.07941 2.48212 6.02373 3.50391 6 4.78607V20.7977C6.02373 22.0799 7.07941 23.1017 8.36167 23.0836H16.6383C17.9206 23.1017 18.9763 22.0799 19 20.7977V4.78607C18.9763 3.50391 17.9206 2.48212 16.6383 2.50024ZM11.1458 4.12524H13.8542V4.6669C13.8542 4.81648 13.7329 4.93774 13.5833 4.93774H11.4167C11.2671 4.93774 11.1458 4.81648 11.1458 4.6669V4.12524ZM16.6383 21.4586C17.0234 21.4771 17.3517 21.1825 17.375 20.7977V4.78607C17.3517 4.40129 17.0234 4.10674 16.6383 4.12524H15.4792V4.6669C15.4792 5.16971 15.2794 5.65192 14.9239 6.00746C14.5684 6.363 14.0861 6.56274 13.5833 6.56274H11.4167C10.3696 6.56274 9.52083 5.71394 9.52083 4.6669V4.12524H8.36167C7.97662 4.10674 7.64828 4.40129 7.625 4.78607V20.7977C7.64828 21.1825 7.97662 21.4771 8.36167 21.4586H16.6383Z" fill="#9E9E9E" />
                    </svg>
                    <p class="tw-text-start tw-w-full tw-text-black tw-text-base"><?php echo $user_phone ? esc_html($user_phone) : __('Not specified', 'spaces_mnu_plugin'); ?></p>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="tw-flex tw-flex-col tw-gap-4">
            <a href="#" data-target="calendar" class="sidebar-menu-item tw-w-full tw-flex tw-justify-between tw-items-center tw-p-4 tw-rounded-xl tw-h-fit">
                <h3><?= __('My Calendar', 'spaces_mnu_plugin'); ?></h3>
                <?= $svg_arrow; ?>
            </a>
            <?php if (strpos($user_role, 'management_staff') !== false && $user_role !== 'security_staff'): ?>
                <a href="#" data-target="resources" class="sidebar-menu-item tw-w-full tw-flex tw-justify-between tw-items-center tw-p-4 tw-rounded-xl tw-h-fit">
                    <h3><?= __('My Resources', 'spaces_mnu_plugin'); ?></h3>
                    <?= $svg_arrow; ?>
                </a>
                <a href="#" data-target="events" class="sidebar-menu-item tw-w-full tw-flex tw-justify-between tw-items-center tw-p-4 tw-rounded-xl tw-h-fit">
                    <h3><?= __('My Events', 'spaces_mnu_plugin'); ?></h3>
                    <?= $svg_arrow; ?>
                </a>
            <?php endif; ?>
            <?php if ($user_role === 'management_staff'): ?>
                <a href="#" data-target="bookings" class="sidebar-menu-item tw-w-full tw-flex tw-justify-between tw-items-center tw-p-4 tw-rounded-xl tw-h-fit">
                    <h3><?= __('Bookings', 'spaces_mnu_plugin'); ?></h3>
                    <?= $svg_arrow; ?>
                </a>
            <?php endif; ?>
            <?php if ($user_role === 'senior_management_staff' || $user_role === 'security_staff'): ?>
                <a href="#" data-target="all-bookings" class="sidebar-menu-item tw-w-full tw-flex tw-justify-between tw-items-center tw-p-4 tw-rounded-xl tw-h-fit">
                    <h3><?= __('All Bookings', 'spaces_mnu_plugin'); ?></h3>
                    <?= $svg_arrow; ?>
                </a>
            <?php endif; ?>
            <?php if (strpos($user_role, 'staff') !== false): ?>
                <a href="#" data-target="staff" class="sidebar-menu-item tw-w-full tw-flex tw-justify-between tw-items-center tw-p-4 tw-rounded-xl tw-h-fit">
                    <h3><?= __('Our Staff', 'spaces_mnu_plugin'); ?></h3>
                    <?= $svg_arrow; ?>
                </a>
            <?php endif; ?>
            <?php if (strpos($user_role, 'staff') !== false): ?>
                <a href="#" data-target="students" class="sidebar-menu-item tw-w-full tw-flex tw-justify-between tw-items-center tw-p-4 tw-rounded-xl tw-h-fit">
                    <h3><?= __('Our Students', 'spaces_mnu_plugin'); ?></h3>
                    <?= $svg_arrow; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>