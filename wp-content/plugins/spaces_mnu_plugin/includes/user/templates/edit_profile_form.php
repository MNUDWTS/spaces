<?php
$user = wp_get_current_user();
$user_role = get_the_author_meta('mnu_role', $user->ID);
$user_phone = get_the_author_meta('phone', $user->ID);
$user_job_title = !empty(get_the_author_meta('job_title', $user->ID)) ?
    get_the_author_meta('job_title', $user->ID) :
    __('Not specified', 'spaces_mnu_plugin');
$department_key = get_the_author_meta('department', $user->ID);
$departments = get_option('spaces_mnu_plugin_departments');
$user_department = isset($departments[$department_key]) ? $departments[$department_key] : __('Not specified', 'spaces_mnu_plugin');
$avatar_url = get_user_meta($user->ID, 'custom_avatar', true);
?>
<div>
    <h2 class="tw-text-2xl tw-font-bold tw-mb-4"><?= __('Edit Profile', 'spaces_mnu_plugin'); ?></h2>
    <form method="post" enctype="multipart/form-data" class="tw-space-y-4">
        <?php wp_nonce_field('spaces_mnu_save_user_profile', 'spaces_mnu_user_profile_nonce'); ?>
        <div class="tw-space-y-12">
            <div class="tw-border-b tw-border-gray-900/10 tw-pb-12">
                <h3 class="tw-text-base tw-font-semibold tw-leading-7 tw-text-gray-900"><?= __('Profile Photo', 'spaces_mnu_plugin'); ?></h3>
                <div class="tw-mt-4 tw-flex tw-flex-col tw-items-center tw-gap-y-2">
                    <img id="avatar-preview" src="<?php echo $avatar_url ? esc_url($avatar_url) : get_avatar_url($user->ID, ['size' => 150]); ?>" alt="Avatar" class="tw-mx-auto tw-w-[150px] tw-object-cover tw-aspect-square tw-rounded-full" />
                    <div class="tw-relative tw-cursor-pointer tw-rounded-md tw-bg-white tw-px-2.5 tw-py-1.5 tw-text-sm tw-font-semibold tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 hover:tw-bg-gray-50" onclick="document.getElementById('avatar').click()">
                        <span><?= __('Upload file', 'spaces_mnu_plugin'); ?></span>
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="tw-sr-only" />
                    </div>
                    <p id="file-error" class="tw-text-red-500 tw-hidden"><?= __('File size exceeds 2MB.', 'spaces_mnu_plugin'); ?></p>
                </div>
            </div>
            <div class="tw-border-b tw-border-gray-900/10 tw-pb-12">
                <div class="tw-mt-10 tw-grid tw-grid-cols-1 tw-gap-x-6 tw-gap-y-8 sm:tw-grid-cols-6">
                    <div class="sm:tw-col-span-3">
                        <label for="first_name" class="tw-block tw-text-sm tw-font-medium tw-leading-6 tw-text-gray-900"><?= __('First Name', 'spaces_mnu_plugin'); ?></label>
                        <div class="tw-mt-2">
                            <input minlength="2" type="text" name="first_name" id="first_name" value="<?php echo esc_attr($user->first_name); ?>" autocomplete="given-name" class="tw-block tw-w-full tw-rounded-md tw-border-0 tw-p-[16px] tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 placeholder:tw-text-gray-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-indigo-600 sm:tw-text-sm sm:tw-leading-6">
                        </div>
                    </div>
                    <div class="sm:tw-col-span-3">
                        <label for="last_name" class="tw-block tw-text-sm tw-font-medium tw-leading-6 tw-text-gray-900"><?= __('Last Name', 'spaces_mnu_plugin'); ?></label>
                        <div class="tw-mt-2">
                            <input minlength="2" type="text" name="last_name" id="last_name" value="<?php echo esc_attr($user->last_name); ?>" autocomplete="family-name" class="tw-block tw-w-full tw-rounded-md tw-border-0 tw-p-[16px] tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 placeholder:tw-text-gray-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-indigo-600 sm:tw-text-sm sm:tw-leading-6">
                        </div>
                    </div>
                    <div class="sm:tw-col-span-3">
                        <label for="phone" class="tw-block tw-text-sm tw-font-medium tw-leading-6 tw-text-gray-900"><?= __('Phone', 'spaces_mnu_plugin'); ?></label>
                        <div class="tw-mt-2">
                            <input id="phone" name="phone" type="tel" value="<?php echo esc_attr($user_phone); ?>" autocomplete="tel" class="tw-block tw-w-full tw-rounded-md tw-border-0 tw-p-[16px] tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 placeholder:tw-text-gray-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-indigo-600 sm:tw-text-sm sm:tw-leading-6">
                        </div>
                    </div>
                    <div class="sm:tw-col-span-3">
                        <label for="email" class="tw-block tw-text-sm tw-font-medium tw-leading-6 tw-text-gray-900"><?= __('Email', 'spaces_mnu_plugin'); ?></label>
                        <div class="tw-mt-2">
                            <input disabled id="email" name="email" type="email" value="<?php echo esc_attr($user->user_email); ?>" autocomplete="email" class="tw-block tw-w-full tw-rounded-md tw-border-0 tw-p-[16px] tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 placeholder:tw-text-gray-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-indigo-600 sm:tw-text-sm sm:tw-leading-6">
                        </div>
                    </div>
                    <?php if (strpos($user_role, 'staff') !== false): ?>
                        <div class="sm:tw-col-span-3">
                            <label for="job_title" class="tw-block tw-text-sm tw-font-medium tw-leading-6 tw-text-gray-900"><?= __('Job Title', 'spaces_mnu_plugin'); ?></label>
                            <div class="tw-mt-2">
                                <input id="job_title" name="job_title" type="text" value="<?php echo esc_attr($user_job_title); ?>" class="tw-block tw-w-full tw-rounded-md tw-border-0 tw-p-[16px] tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 placeholder:tw-text-gray-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-indigo-600 sm:tw-text-sm sm:tw-leading-6">
                            </div>
                        </div>
                        <div class="sm:tw-col-span-3">
                            <label for="department" class="tw-block tw-text-sm tw-font-medium tw-leading-6 tw-text-gray-900"><?= __('Department', 'spaces_mnu_plugin'); ?></label>
                            <div class="tw-mt-2">
                                <input disabled id="department" name="department" type="text" value="<?php echo esc_attr($user_department); ?>" class="tw-block tw-w-full tw-rounded-md tw-border-0 tw-p-[16px] tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 placeholder:tw-text-gray-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-indigo-600 sm:tw-text-sm sm:tw-leading-6">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="tw-mt-6 tw-flex tw-items-center tw-justify-end tw-gap-x-6">
            <button type="submit" class="tw-bg-[#262626] hover:tw-bg-[#333333] tw-text-white tw-my-auto tw-px-8 tw-py-4 tw-rounded-md tw-text-base tw-font-semibold tw-shadow-sm"><?= __('Save', 'spaces_mnu_plugin'); ?></button>
        </div>
    </form>

    <script>
        document.getElementById('phone').addEventListener('input', function(event) {
            let phone = event.target.value;
            phone = phone.replace(/\D/g, '');
            if (!phone.startsWith('7')) {
                phone = '7' + phone;
            }
            if (phone.length <= 1) {
                phone = '+' + phone;
            } else if (phone.length <= 4) {
                phone = '+7 (' + phone.slice(1);
            } else if (phone.length <= 7) {
                phone = '+7 (' + phone.slice(1, 4) + ') ' + phone.slice(4);
            } else if (phone.length <= 10) {
                phone = '+7 (' + phone.slice(1, 4) + ') ' + phone.slice(4, 7) + ' ' + phone.slice(7);
            } else if (phone.length <= 12) {
                phone = '+7 (' + phone.slice(1, 4) + ') ' + phone.slice(4, 7) + ' ' + phone.slice(7, 9) + ' ' + phone.slice(9);
            } else {
                phone = '+7 (' + phone.slice(1, 4) + ') ' + phone.slice(4, 7) + ' ' + phone.slice(7, 9) + ' ' + phone.slice(9, 11);
            }
            phone = phone.slice(0, 18);
            event.target.value = phone;
        });
        document.querySelector('form').addEventListener('submit', function(event) {
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            if (firstName.length < 2 || lastName.length < 2) {
                event.preventDefault();
                Toastify({
                    text: "<?= __('First and last name must be at least 2 characters long.', 'spaces_mnu_plugin'); ?>",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "#E2474CCC",
                    },
                }).showToast();
                return false;
            }
        });

        const maxFileSize = 2 * 1024 * 1024;
        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatar-preview');
        const errorText = document.getElementById('file-error');
        avatarInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > maxFileSize) {
                    errorText.classList.remove('tw-hidden');
                    avatarInput.value = '';
                    avatarPreview.src = '<?php echo $avatar_url ? esc_url($avatar_url) : get_avatar_url($user->ID, ['size' => 150]); ?>';
                } else {
                    errorText.classList.add('tw-hidden');
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
        avatarPreview.addEventListener('click', function() {
            avatarInput.click();
        });
    </script>
</div>