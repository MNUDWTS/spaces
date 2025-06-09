<?php
?>
<div>
    <h2 class="tw-text-2xl tw-font-bold tw-mb-4"><?= __('My Calendar', 'spaces_mnu_plugin'); ?></h2>
    <div class="tw-space-y-2">
        <div id='my-calendar'>
        </div>
    </div>
    <div class="tw-space-y-2 tw-mt-4">
        <h3 id="my-calendar-title" class="tw-text-lg tw-text-[#717171]"></h3>
        <div id="my-bookings" class="tw-block"></div>
        <div id="my-bookings-preloader" class="tw-justify-center tw-items-center tw-w-full tw-h-40 tw-hidden">
            <div class="tw-loader tw-border-4 tw-border-[#E2474C] tw-border-t-transparent tw-rounded-full tw-w-10 tw-h-10 tw-animate-spin"></div>
        </div>
    </div>
</div>
<div id="cancelModalMyBookings" class="tw-z-[99999] tw-fixed tw-inset-0 tw-bg-gray-800 tw-bg-opacity-50 tw-items-center tw-justify-center tw-hidden">
    <div class="tw-bg-white tw-rounded-lg tw-w-3xl tw-p-6 tw-shadow-lg">
        <h3 class="tw-text-xl tw-font-bold tw-mb-4"><?= __('Cancel Booking', 'spaces_mnu_plugin'); ?></h3>
        <p class="tw-text-sm tw-mb-4"><?= __('Are you sure you want to cancel this booking? Please provide a reason for the cancellation.', 'spaces_mnu_plugin'); ?></p>
        <textarea id="cancelCommentMyBookings" maxlength="150" class="tw-w-full tw-p-2 tw-border tw-rounded" rows="4" placeholder="<?= __('Reason for cancellation', 'spaces_mnu_plugin'); ?>"></textarea>
        <div class="tw-flex tw-justify-end tw-gap-2 tw-mt-4">
            <button id="cancelModalCloseMyBookings" class="tw-bg-gray-300 tw-text-gray-700 tw-px-4 tw-py-2 tw-rounded"><?= __('Close', 'spaces_mnu_plugin'); ?></button>
            <button id="confirmCancelMyBookings" class="tw-bg-red-500 tw-text-white tw-px-4 tw-py-2 tw-rounded"><?= __('Confirm', 'spaces_mnu_plugin'); ?></button>
        </div>
    </div>
</div>