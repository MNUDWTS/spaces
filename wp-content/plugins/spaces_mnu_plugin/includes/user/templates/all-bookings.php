<?php
$current_language = get_locale();

?>
<div>
    <h2 class="tw-text-2xl tw-font-bold tw-mb-4"><?= __('All Bookings', 'spaces_mnu_plugin'); ?></h2>
    <div class="tw-space-y-2">
        <div id='all-bookings-calendar'></div>
    </div>
    <div class="tw-space-y-2 tw-mt-8">
        <h3 id="all-bookings-title" class="tw-text-2xl tw-text-black tw-font-bold"></h3>
        <div id="users-all-bookings-preloader" class="tw-justify-center tw-items-center tw-w-full tw-h-40 tw-hidden">
            <div class="tw-loader tw-border-4 tw-border-[#E2474C] tw-border-t-transparent tw-rounded-full tw-w-10 tw-h-10 tw-animate-spin"></div>
        </div>
    </div>
</div>

<div id="cancelModalAllBookings" class="tw-z-[99999] tw-fixed tw-inset-0 tw-bg-gray-800 tw-bg-opacity-50 tw-items-center tw-justify-center tw-hidden">
    <div class="tw-bg-white tw-rounded-lg tw-w-3xl tw-p-6 tw-shadow-lg">
        <h3 class="tw-text-xl tw-font-bold tw-mb-4"><?= __('Cancel Booking', 'spaces_mnu_plugin'); ?></h3>
        <p class="tw-text-sm tw-mb-4"><?= __('Are you sure you want to cancel this booking? Please provide a reason for the cancellation.', 'spaces_mnu_plugin'); ?></p>
        <textarea id="cancelCommentAllBookings" maxlength="150" class="tw-w-full tw-p-2 tw-border tw-rounded" rows="4" placeholder="<?= __('Reason for cancellation', 'spaces_mnu_plugin'); ?>"></textarea>
        <div class="tw-flex tw-justify-end tw-gap-2 tw-mt-4">
            <button id="cancelModalCloseAllBookings" class="tw-bg-gray-300 tw-text-gray-700 tw-px-4 tw-py-2 tw-rounded"><?= __('Close', 'spaces_mnu_plugin'); ?></button>
            <button id="confirmCancelAllBookings" class="tw-bg-red-500 tw-text-white tw-px-4 tw-py-2 tw-rounded"><?= __('Confirm', 'spaces_mnu_plugin'); ?></button>
        </div>
    </div>
</div>

<div id="bookingsModal" class="tw-z-[99999] tw-fixed tw-inset-0 tw-bg-gray-800 tw-bg-opacity-50 tw-items-center tw-justify-center tw-hidden">
    <div class="tw-bg-white tw-rounded-lg tw-w-[90%] tw-max-w-5xl tw-p-6 tw-shadow-lg tw-h-[80vh] tw-overflow-y-auto">
        <h3 id="modal-title" class="tw-text-xl tw-font-bold tw-mb-4"></h3>
        <div id="all-bookings-filters" class="tw-grid tw-grid-cols-4 tw-gap-4 tw-p-4 tw-mb-4">
            <div class="tw-col-span-2">
                <label for="filter-requestedBy" class="tw-block tw-text-sm tw-font-medium tw-leading-6 tw-text-gray-900"><?= __('Requested By', 'spaces_mnu_plugin'); ?></label>
                <input type="text" name="filter-requestedBy" id="filter-requestedBy" placeholder="<?= __('Requested By', 'spaces_mnu_plugin'); ?>" class="tw-block tw-w-full tw-rounded-md tw-border-0 tw-p-[16px] tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 placeholder:tw-text-gray-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-indigo-600 sm:tw-text-sm sm:tw-leading-6">
            </div>
            <div class="tw-col-span-2">
                <label for="filter-resourceName" class="tw-block tw-text-sm tw-font-medium tw-leading-6 tw-text-gray-900"><?= __('Resource Name', 'spaces_mnu_plugin'); ?></label>
                <input type="text" name="filter-resourceName" id="filter-resourceName" placeholder="<?= __('Resource Name', 'spaces_mnu_plugin'); ?>" class="tw-block tw-w-full tw-rounded-md tw-border-0 tw-p-[16px] tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 placeholder:tw-text-gray-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-indigo-600 sm:tw-text-sm sm:tw-leading-6">
            </div>
            <div class="tw-col-span-2">
                <label for="filter-status" class="tw-block tw-text-sm tw-font-medium tw-leading-6 tw-text-gray-900"><?= __('Status', 'spaces_mnu_plugin'); ?></label>
                <select id="filter-status" name="filter-status" class="tw-appearance-none tw-block tw-w-full tw-rounded-md tw-border-0 tw-p-[16px] tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 placeholder:tw-text-gray-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-indigo-600 sm:tw-text-sm sm:tw-leading-6">
                    <option value=""><?= __('All Statuses', 'spaces_mnu_plugin'); ?></option>
                    <option value="approved"><?= __('Approved', 'spaces_mnu_plugin'); ?></option>
                    <option value="cancelled"><?= __('Cancelled', 'spaces_mnu_plugin'); ?></option>
                </select>
            </div>
            <div class="tw-col-span-2">
                <label for="filter-slot" class="tw-block tw-text-sm tw-font-medium tw-leading-6 tw-text-gray-900"><?= __('Slot start', 'spaces_mnu_plugin'); ?></label>
                <input type="time" name="filter-slot" id="filter-slot" placeholder="Slot (e.g., 08:00)" class="tw-col-span-2 tw-block tw-w-full tw-rounded-md tw-border-0 tw-p-[16px] tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 placeholder:tw-text-gray-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-indigo-600 sm:tw-text-sm sm:tw-leading-6">
            </div>
            <button id="apply-filters" class="tw-col-span-4 tw-bg-[#262626] hover:tw-bg-[#333333] tw-text-white tw-my-auto tw-px-8 tw-py-4 tw-rounded-md tw-text-base tw-font-semibold tw-shadow-sm"><?= __('Apply Filters', 'spaces_mnu_plugin'); ?></button>
        </div>
        <div id="modal-users-all-bookings" class="tw-block"></div>
        <div id="modal-users-all-bookings-preloader" class="tw-justify-center tw-items-center tw-w-full tw-h-40 tw-hidden">
            <div class="tw-loader tw-border-4 tw-border-[#E2474C] tw-border-t-transparent tw-rounded-full tw-w-10 tw-h-10 tw-animate-spin"></div>
        </div>
        <div class="tw-flex tw-justify-end tw-mt-4">
            <button id="closeModal" class="tw-bg-gray-300 tw-text-gray-700 tw-px-4 tw-py-2 tw-rounded"><?= __('Close', 'spaces_mnu_plugin'); ?></button>
        </div>
    </div>
</div>