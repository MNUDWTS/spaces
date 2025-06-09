<?php
?>
<div>
    <h2 class="tw-text-2xl tw-font-bold tw-mb-4"><?= __('Add Event', 'spaces_mnu_plugin'); ?></h2>
    <div id="event-create-form-container"></div>
</div>
<div id="" class="confirmation-modal tw-fixed tw-inset-0 tw-z-50 tw-hidden tw-flex tw-items-center tw-justify-center tw-bg-black tw-bg-opacity-50">
    <div class="tw-bg-white tw-rounded-lg tw-overflow-hidden tw-shadow-lg tw-w-11/12 tw-max-w-lg">
        <div class="tw-p-6 tw-border-b tw-border-gray-200">
            <h3 class="confirmation-modal-label tw-text-lg tw-font-medium tw-text-gray-900" id=""><?= __('Confirm', 'spaces_mnu_plugin'); ?></h3>
            <p class="modal-message tw-text-sm tw-text-gray-600 tw-mt-2" id=""></p>
        </div>
        <div class="tw-flex tw-justify-end tw-space-x-3 tw-p-4">
            <button id="" class="confirm-no tw-bg-gray-200 tw-text-gray-700 tw-px-4 tw-py-2 tw-rounded-lg tw-font-semibold"><?= __('Cancel', 'spaces_mnu_plugin'); ?></button>
            <button id="" class="confirm-yes tw-bg-red-500 tw-text-white tw-px-4 tw-py-2 tw-rounded-lg tw-font-semibold"><?= __('Confirm', 'spaces_mnu_plugin'); ?></button>
        </div>
    </div>
</div>