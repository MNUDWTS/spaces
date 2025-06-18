<?php

/**
 * Template Name: Home Page
 */

get_header();
$current_language = get_locale();
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$selected_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';
$search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$selected_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
$selected_floor = isset($_GET['floor']) ? sanitize_text_field($_GET['floor']) : '';
?>
<main class="tw-min-h-screen tw-w-full">
    <section class="tw-text-left tw-my-10 tw-w-full tw-max-w-7xl tw-mx-auto tw-px-6 md:tw-px-12">
        <?php $university_name = '<span class="tw-text-[#E2474C] tw-whitespace-nowrap">Maqsut Narikbayev University</span>'; ?>
        <h1 class="tw-text-4xl tw-font-bold tw-mb-4"><?= __('Spaces MNU ', 'spaces_mnu_plugin'); ?></h1>
        <p class="tw-w-fulltw-text-left tw-text-2xl tw-text-gray-700">
            <?= sprintf(__('Online platform for optimization and management of internal processes of %s', 'spaces_mnu_plugin'), $university_name); ?>
        </p>
    </section>

<style>
    @media (max-width: 767px) {
        section.tw-flex-col {
            background-image: none !important;
            background-color: #ffffff !important;
        }
    }

    section.tw-flex-col {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
</style>
<section class="tw-w-full tw-max-w-7xl tw-mx-auto tw-space-y-4 tw-mb-8 tw-px-6 md:tw-px-12 tw-py-4 tw-rounded-xl tw-flex tw-flex-col tw-justify-center tw-items-start"
    style="background-image: url('<?php echo SPACES_MNU_PLUGIN_URL . 'assets/img/home_bg.jpg' ?>');">
    <div class="">
        <label class="tw-sr-only">Select Resource Type:</label>
        <div class="tw-flex tw-flex-wrap tw-justify-start md:tw-justify-center tw-items-center tw-space-x-2 tw-overflow-x-auto tw-border-b tw-w-fit tw-p-1 tw-bg-white tw-rounded-xl">
            <?php
            $resource_types = get_terms([
                'taxonomy' => 'resource_type',
                'hide_empty' => true,
            ]);
            $space_term = null;
            foreach ($resource_types as $key => $type) {
                if ($type->slug === 'space') {
                    $space_term = $type;
                    unset($resource_types[$key]); // Удаляем его из массива, чтобы не выводить дважды
                    break;
                }
            }
            if ($space_term): ?>
                <button class="resource-tab tw-px-8 tw-py-4 tw-rounded-lg tw-text-sm <?= $selected_type === $space_term->slug ? 'tw-bg-[#E2474C] tw-text-white' : 'tw-bg-transparent tw-text-[#ACACAE]'; ?> tw-transition-all tw-duration-300 tw-ease-in-out"
                    data-type="<?= esc_attr($space_term->slug); ?>">
                    <?= esc_html(__($space_term->name, 'spaces_mnu_plugin')); ?>
                </button>
            <?php endif; ?>
            <?php foreach ($resource_types as $type) : ?>
                <button class="resource-tab tw-px-8 tw-py-4 tw-rounded-lg tw-text-sm <?= $selected_type === $type->slug ? 'tw-bg-[#E2474C] tw-text-white' : 'tw-bg-transparent tw-text-[#ACACAE]'; ?> tw-transition-all tw-duration-300 tw-ease-in-out"
                    data-type="<?= esc_attr($type->slug); ?>">
                    <?= esc_html(__($type->name, 'spaces_mnu_plugin')); ?>
                </button>
            <?php endforeach; ?>
            <button class="resource-tab tw-px-8 tw-py-4 tw-rounded-lg tw-text-sm <?= $selected_type === 'event' ? 'tw-bg-[#E2474C] tw-text-white' : 'tw-bg-transparent tw-text-[#ACACAE]'; ?> tw-transition-all tw-duration-300 tw-ease-in-out"
                data-type="event">
                <?= esc_html(__('Event', 'spaces_mnu_plugin')); ?>
            </button>
        </div>
    </div>
    <div class="tw-w-full">
        <label class="tw-sr-only">Advanced Filter:</label>
        <div class="tw-grid tw-grid-cols-12 tw-justify-center tw-items-center tw-gap-4 tw-w-full">
            <div class="tw-col-span-12 tw-bg-white tw-rounded-lg tw-border tw-p-0 tw-shadow-sm tw-flex tw-flex-col sm:tw-flex-row tw-gap-4">
                <div class="tw-relative tw-flex-grow">
                    <label class="tw-sr-only tw-hidden tw-absolute tw-left-12 tw-top-2 tw-text-xs tw-text-gray-500"><?= __('Resource Name', 'spaces_mnu_plugin'); ?></label>
                    <span class="tw-absolute tw-left-4 tw-bottom-3 tw-text-gray-400">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 8.01562C0 12.4353 3.59598 16.0312 8.01562 16.0312C9.76337 16.0312 11.3605 15.4688 12.6764 14.5245L17.6183 19.4766C17.8493 19.7076 18.1507 19.8181 18.4721 19.8181C19.1552 19.8181 19.6272 19.3058 19.6272 18.6328C19.6272 18.3114 19.5067 18.0201 19.2957 17.8091L14.3839 12.8672C15.4185 11.5212 16.0312 9.84377 16.0312 8.01562C16.0312 3.59598 12.4353 0 8.01562 0C3.59598 0 0 3.59598 0 8.01562ZM1.71764 8.01562C1.71764 4.54018 4.54018 1.71764 8.01562 1.71764C11.491 1.71764 14.3136 4.54018 14.3136 8.01562C14.3136 11.491 11.491 14.3136 8.01562 14.3136C4.54018 14.3136 1.71764 11.491 1.71764 8.01562Z" fill="#262626" />
                        </svg>
                    </span>
                    <input type="text" id="resourceSearch" class="focus:tw-outline-none focus:tw-ring-0 focus:tw-border-transparent tw-h-full tw-pl-12 tw-pr-4 tw-pt-3 tw-pb-3 tw-border-none tw-rounded-lg tw-w-full tw-bg-white" placeholder="<?php echo __('Enter the resource name', 'spaces_mnu_plugin') ?>" value="<?= esc_attr($search_query); ?>">
                </div>
                <div class="tw-hidden sm:tw-block tw-h-10 tw-my-auto tw-w-px tw-bg-gray-300"></div>
                <div class="tw-relative tw-flex-grow">
                    <label class="tw-sr-only tw-hidden tw-absolute tw-left-12 tw-top-2 tw-text-xs tw-text-gray-500"><?= __('Resource Category', 'spaces_mnu_plugin'); ?></label>
                    <span class="tw-absolute tw-left-4 tw-bottom-3 tw-text-gray-400">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_541_10737)">
                                <path d="M7.31529 17.9631H17.7215C18.1736 17.9631 18.5351 17.6116 18.5351 17.1595C18.5351 16.7076 18.1736 16.3459 17.7215 16.3459H7.31529C6.86328 16.3459 6.51172 16.7076 6.51172 17.1595C6.51172 17.6116 6.86328 17.9631 7.31529 17.9631Z" fill="#262626" />
                                <path d="M5.25698 12.7903H19.7916C20.2436 12.7903 20.5952 12.4387 20.5952 11.9867C20.5952 11.5347 20.2436 11.1731 19.7916 11.1731H5.25698C4.80496 11.1731 4.44336 11.5347 4.44336 11.9867C4.44336 12.4387 4.80496 12.7903 5.25698 12.7903Z" fill="#262626" />
                                <path d="M3.13756 7.61719H21.8507C22.3028 7.61719 22.6543 7.26562 22.6543 6.81362C22.6543 6.36161 22.3028 6 21.8507 6H3.13756C2.68555 6 2.33398 6.36161 2.33398 6.81362C2.33398 7.26562 2.68555 7.61719 3.13756 7.61719Z" fill="#262626" />
                            </g>
                            <defs>
                                <clipPath id="clip0_541_10737">
                                    <rect width="20.3203" height="11.9833" fill="white" transform="translate(2.33398 6)" />
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <select id="categoryDropdown" class="tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-transparent tw-h-full tw-pl-12 tw-pr-4 tw-pt-3 tw-pb-3 tw-border-none tw-rounded-lg tw-w-full tw-bg-white">
                        <option value="" disabled selected class="tw-appearance-none" style="color: #A1A1A1; font-size: 0.875rem;">
                            <?= __('Select Category', 'spaces_mnu_plugin'); ?>
                        </option>
                        <?php
                        $categories = get_terms([
                            'taxonomy' => 'resource_category',
                            'hide_empty' => true,
                        ]);
                        foreach ($categories as $category) : ?>
                            <option value="<?= esc_attr($category->slug); ?>" <?= $selected_category === $category->slug ? 'selected' : ''; ?>><?= esc_html(__($category->name, 'spaces_mnu_plugin')); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button id="searchButton" class="tw-bg-[#262626] hover:tw-bg-[#333333] tw-text-white tw-my-auto tw-px-8 tw-py-4 tw-rounded tw-h-full"><?= __('Search', 'spaces_mnu_plugin') ?></button>
            </div>
        </div>
        <div class="tw-w-fit tw-p-4 tw-bg-white tw-rounded-lg" id="floorFilter">
            <label class="tw-sr-only">Floor:</label>
            <div class="tw-flex tw-flex-wrap tw-gap-6">
                <button class="floor-btn tw-text-base md:tw-text-xl tw-bg-transparent tw-border-none tw-text-[#ACACAE]" style="text-underline-offset: 6px;" data-floor="all" <?= $selected_floor === 'all' ? 'tw-underline tw-text-[#E2474C]' : ''; ?>><?= __('All', 'spaces_mnu_plugin'); ?></button>
                <?php for ($i = 0; $i <= 7; $i++) : ?>
                    <button class="floor-btn tw-text-base md:tw-text-xl tw-bg-transparent tw-border-none tw-text-[#ACACAE]" style="text-underline-offset: 6px;" data-floor="<?= $i; ?>" <?= $selected_floor == $i ? 'tw-underline tw-text-[#E2474C]' : ''; ?>><?= $i . ' ' . __('floor', 'spaces_mnu_plugin'); ?></button>
                <?php endfor; ?>
            </div>
        </div>
    </section>
    <div id="resources-preloader" class="tw-justify-center tw-items-center tw-w-full tw-h-40 tw-hidden">
        <div class="tw-loader tw-border-4 tw-border-[#E2474C] tw-border-t-transparent tw-rounded-full tw-w-10 tw-h-10 tw-animate-spin"></div>
    </div>
    <section id="rendered-resources" class="tw-w-full tw-max-w-7xl tw-mx-auto tw-px-6 md:tw-px-12 tw-grid tw-grid-cols-1 tw-gap-y-4 sm:tw-grid-cols-2 sm:tw-gap-x-6 sm:tw-gap-y-10 lg:tw-grid-cols-4 lg:tw-gap-x-8">
    </section>
    <div id="pagination-container" class="tw-my-10 tw-text-center"></div>
</main>
<div id="install-popup" class="tw-fixed tw-bottom-0 tw-left-1/2 tw-transform tw--translate-x-1/2 tw--translate-y-0 md:tw-bottom-0 md:tw-right-0 md:tw-top-auto md:tw-left-auto md:tw-translate-x-0 md:tw-translate-y-0 tw-bg-black md:tw-bg-black tw-bg-clip-padding md:tw-bg-clip-padding tw-backdrop-filter md:tw-backdrop-filter tw-backdrop-blur-lg md:tw-backdrop-blur-sm tw-bg-opacity-50 md:tw-bg-opacity-50 tw-text-[#333] tw-p-5 tw-rounded-t-lg md:tw-rounded-tl-lg tw-z-[1000] tw-w-[90%] tw-max-w-[400px] tw-hidden">
    <div class="popup-header tw-flex tw-justify-end tw-items-center">
        <button class="tw-text-white close-button tw-cursor-pointer tw-bg-none tw-border-none tw-text-xl" onclick="closeInstallPopup()">×</button>
    </div>
    <div class="popup-content tw-space-y-6 tw-mb-10" id="install-popup-content">
        <div id="chrome-install" class="tw-space-y-4">
            <p class="tw-text-white"><?= __('Click the button below to install the application:', 'spaces_mnu_plugin'); ?></p>
            <button class="tw-bg-[#0073aa] hover:tw-bg-[#005f8f] tw-text-white tw-py-2.5 tw-px-5 tw-border-none tw-rounded-md tw-cursor-pointer tw-text-base tw-w-full tw-mt-2.5 tw-flex tw-gap-4 tw-justify-center tw-items-center" id="install-app"><?= __('Install', 'spaces_mnu_plugin'); ?>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_1068_33951)">
                        <path d="M19.1496 16.8288H4.8504C3.7056 16.8288 3 16.1232 3 14.9784V6.72003C3 5.57523 3.7056 4.86963 4.8504 4.86963H14.3813C14.7014 4.86963 14.9609 5.12912 14.9609 5.44923C14.9609 5.76933 14.7014 6.02883 14.3813 6.02883H4.872C4.4256 6.02883 4.1592 6.29523 4.1592 6.73443V14.964C4.1592 15.4032 4.4256 15.6696 4.872 15.6696H19.128C19.5744 15.6696 19.8408 15.4032 19.8408 14.964V9.44922C19.8408 9.12912 20.1003 8.86963 20.4204 8.86963C20.7405 8.86963 21 9.12912 21 9.44922V14.9784C21 16.1232 20.3016 16.8288 19.1496 16.8288Z" fill="currentColor" fill-opacity="0.85" />
                        <path d="M15.6216 19.2696H8.3784C8.06159 19.2696 7.7952 19.0104 7.7952 18.6864C7.7952 18.3624 8.06159 18.1032 8.3784 18.1032H15.6216C15.9384 18.1032 16.2048 18.3624 16.2048 18.6864C16.2048 19.0104 15.9384 19.2696 15.6216 19.2696Z" fill="currentColor" fill-opacity="0.85" />
                        <path d="M15.9208 5.36963V8.77822L15.9637 9.84462L15.4771 9.34363L14.3964 8.18422C14.2962 8.0697 14.1459 8.01242 14.0099 8.01242C13.7093 8.01242 13.4874 8.22715 13.4874 8.5134C13.4874 8.67085 13.5447 8.78536 13.652 8.89273L16.0711 11.2259C16.2143 11.3691 16.3431 11.4192 16.4862 11.4192C16.6365 11.4192 16.7582 11.3691 16.9013 11.2259L19.3204 8.89273C19.4278 8.78536 19.4922 8.67085 19.4922 8.5134C19.4922 8.22715 19.2775 8.01242 18.9769 8.01242C18.8337 8.01242 18.6834 8.0697 18.5832 8.18422L17.4953 9.34363L17.0087 9.85177L17.0588 8.77822V5.36963C17.0588 5.06903 16.794 4.81136 16.4862 4.81136C16.1785 4.81136 15.9208 5.06903 15.9208 5.36963Z" fill="currentColor" fill-opacity="0.85" />
                    </g>
                    <defs>
                        <clipPath id="clip0_1068_33951">
                            <rect width="18" height="14.4072" fill="white" transform="translate(3 4.86963)" />
                        </clipPath>
                    </defs>
                </svg>
            </button>
        </div>
        <div id="ios-install" class="tw-space-y-4">
            <p class="tw-text-white"><?= __('Or follow these steps:', 'spaces_mnu_plugin'); ?></p>
            <ul class="tw-text-white tw-space-y-2 tw-ml-2">
                <li class="tw-w-full tw-flex tw-gap-2 tw-justify-between tw-items-center">
                    <p class="tw-w-full">1. <?= __('Click on the Share button', 'spaces_mnu_plugin'); ?></p>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_1068_33926)">
                            <path d="M19.4931 11.1882V18.7889C19.4931 20.2633 18.7488 21.0076 17.253 21.0076H6.39575C4.90709 21.0076 4.14844 20.2633 4.14844 18.7889V11.1882C4.14844 9.70664 4.90709 8.96948 6.39575 8.96948H9.29398C9.61217 8.96948 9.87012 9.22743 9.87012 9.54562C9.87012 9.86381 9.61217 10.1218 9.29398 10.1218H6.41722C5.70152 10.1218 5.30072 10.5011 5.30072 11.2454V18.7245C5.30072 19.4688 5.70152 19.8553 6.41722 19.8553H17.2315C17.9473 19.8553 18.3409 19.4688 18.3409 18.7245V11.2454C18.3409 10.5011 17.9473 10.1218 17.2315 10.1218H14.354C14.0358 10.1218 13.7779 9.86381 13.7779 9.54562C13.7779 9.22743 14.0358 8.96948 14.354 8.96948H17.253C18.7488 8.96948 19.4931 9.70664 19.4931 11.1882Z" fill="currentColor" fill-opacity="0.85" />
                            <path d="M12.3839 14.3773V7.02702L12.3409 5.96062L12.8276 6.4616L13.9083 7.62102C14.0085 7.73553 14.1588 7.79282 14.2948 7.79282C14.5954 7.79282 14.8173 7.57809 14.8173 7.29184C14.8173 7.13439 14.76 7.01988 14.6526 6.91251L12.2336 4.57932C12.0904 4.43616 11.9616 4.38608 11.8185 4.38608C11.6682 4.38608 11.5465 4.43616 11.4033 4.57932L8.98427 6.91251C8.87691 7.01988 8.8125 7.13439 8.8125 7.29184C8.8125 7.57809 9.02722 7.79282 9.32781 7.79282C9.47095 7.79282 9.62125 7.73553 9.72145 7.62102L10.8093 6.4616L11.296 5.95347L11.2459 7.02702V14.3773C11.2459 14.6779 11.5107 14.9355 11.8185 14.9355C12.1262 14.9355 12.3839 14.6779 12.3839 14.3773Z" fill="currentColor" fill-opacity="0.85" />
                        </g>
                        <defs>
                            <clipPath id="clip0_1068_33926">
                                <rect width="18" height="15.3519" fill="white" transform="matrix(0 -1 1 0 4.14844 21)" />
                            </clipPath>
                        </defs>
                    </svg>
                </li>
                <li class="tw-w-full tw-flex tw-gap-2 tw-justify-between tw-items-center">
                    <p class="tw-w-full">2. <?= __('Select Add to Home Screen', 'spaces_mnu_plugin'); ?></p>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_1068_33977)">
                            <path d="M16.8657 1.15184C15.8701 0.156182 14.4644 0 12.7952 0H5.19111C3.5512 0 2.14556 0.156182 1.14989 1.15184C0.154233 2.1475 0.0078125 3.54338 0.0078125 5.18329V12.7874C0.0078125 14.4566 0.154233 15.8524 1.14989 16.8482C2.14556 17.8438 3.5512 18 5.21062 18H12.7952C14.4644 18 15.8701 17.8438 16.8657 16.8482C17.8614 15.8524 18.0078 14.4566 18.0078 12.7874V5.20281C18.0078 3.53362 17.8614 2.1475 16.8657 1.15184ZM16.4362 4.93926V13.0607C16.4362 14.0661 16.3093 15.1106 15.7139 15.6963C15.1281 16.2917 14.0739 16.4284 13.0685 16.4284H4.94707C3.94165 16.4284 2.88742 16.2917 2.29198 15.6963C1.70629 15.1106 1.5794 14.0661 1.5794 13.0607V4.96854C1.5794 3.93384 1.70629 2.88937 2.29198 2.29393C2.88742 1.70824 3.95141 1.57158 4.97635 1.57158H13.0685C14.0739 1.57158 15.1281 1.70824 15.7139 2.30368C16.3093 2.88937 16.4362 3.93384 16.4362 4.93926Z" fill="currentColor" fill-opacity="0.85" />
                            <path d="M4.49219 8.99999C4.49219 9.47828 4.83383 9.80039 5.33166 9.80039H8.20151V12.6801C8.20151 13.1681 8.52367 13.5098 9.00197 13.5098C9.49 13.5098 9.83169 13.1778 9.83169 12.6801V9.80039H12.7113C13.1993 9.80039 13.541 9.47828 13.541 8.99999C13.541 8.51195 13.1993 8.17026 12.7113 8.17026H9.83169V5.30041C9.83169 4.80258 9.49 4.46094 9.00197 4.46094C8.52367 4.46094 8.20151 4.80258 8.20151 5.30041V8.17026H5.33166C4.82407 8.17026 4.49219 8.51195 4.49219 8.99999Z" fill="currentColor" fill-opacity="0.85" />
                        </g>
                        <defs>
                            <clipPath id="clip0_1068_33977">
                                <rect width="18" height="18.0097" fill="white" />
                            </clipPath>
                        </defs>
                    </svg>
                </li>
                <li>3. <?= __('Click on Add', 'spaces_mnu_plugin'); ?></li>
            </ul>
        </div>
    </div>
</div>
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
            .then(() => console.log('Service Worker registered'))
            .catch(error => console.error('Service Worker registering error:', error));
    }
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        console.log('Событие beforeinstallprompt вызвано');
        deferredPrompt = e;
        showInstallPopup();
    });
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || ('standalone' in navigator && navigator.standalone);
    if (!isStandalone) {
        if (navigator.userAgent.includes('iPhone')) {
            document.getElementById('install-popup').style.display = 'block';
            document.getElementById('ios-install').style.display = 'block';
        }
    }

    function showInstallPopup() {
        const popup = document.getElementById('install-popup');
        const chromeInstall = document.getElementById('chrome-install');
        if (popup && chromeInstall) {
            popup.style.display = 'block';
            chromeInstall.style.display = 'block';

            const installButton = document.getElementById('install-app');
            installButton.onclick = async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const choiceResult = await deferredPrompt.userChoice;
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted');
                    } else {
                        console.log('User does not accepted');
                    }
                    deferredPrompt = null;
                    closeInstallPopup();
                }
            };
        }
    }

    function closeInstallPopup() {
        const popup = document.getElementById('install-popup');
        if (popup) {
            popup.style.display = 'none';
        }
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentLanguage = "<?php echo substr(get_locale(), 0, 2); ?>";
        const resourceTabs = document.querySelectorAll('.resource-tab');
        const searchButton = document.getElementById('searchButton');
        const searchInput = document.getElementById('resourceSearch');
        const categoryDropdown = document.getElementById('categoryDropdown');
        const floorContainer = document.getElementById('floorFilter');
        const floorButtons = document.querySelectorAll('.floor-btn');
        const paginationContainer = document.getElementById('pagination-container');
        const preloader = document.getElementById('resources-preloader');
        const container = document.querySelector('#rendered-resources');
        let selectedType = 'space';
        let currentPage = 1;
        let maxPages = 1;

        function updateCategoryDropdown(categories) {
            categoryDropdown.innerHTML = `
            <option value="" disabled selected style="color: #A1A1A1; font-size: 0.875rem;">
                <?php echo __('Select Category', 'spaces_mnu_plugin'); ?>
            </option>
        `;
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.slug;
                option.textContent = category.name;
                categoryDropdown.appendChild(option);
            });
        }

        function loadCategoriesByType(type) {
            const data = new FormData();
            data.append('action', 'filter_categories_by_type');
            data.append('type', type);
            // console.log('type', type);

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateCategoryDropdown(data.data.categories);
                        // console.log(data.data.categories);

                    }
                })
                .catch(error => console.error('Error loading categories:', error));
        }

        resourceTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Убираем активное состояние у всех вкладок
                resourceTabs.forEach(t => {
                    t.classList.remove('tw-bg-[#E2474C]', 'tw-text-white');
                    t.classList.add('tw-bg-transparent', 'tw-text-[#ACACAE]');
                });

                // Добавляем активное состояние на текущую вкладку
                this.classList.remove('tw-bg-transparent', 'tw-text-[#ACACAE]');
                this.classList.add('tw-bg-[#E2474C]', 'tw-text-white');
                selectedType = this.getAttribute('data-type');

                container.innerHTML = '';

                // Если выбран тип "event", скрываем фильтр по этажам
                if (selectedType === 'event') {
                    floorContainer.classList.add('tw-hidden');
                    floorButtons.forEach(btn => btn.classList.add('tw-hidden')); // Скрыть фильтр по этажам
                } else {
                    floorContainer.classList.remove('tw-hidden');
                    floorButtons.forEach(btn => btn.classList.remove('tw-hidden')); // Показать фильтр по этажам
                }

                // Загружаем ресурсы или события
                loadCategoriesByType(selectedType);
                loadResources(selectedType, searchInput.value, categoryDropdown.value, getSelectedFloor());
            });
        });

        function showPreloader() {
            container.innerHTML = '';
            preloader.classList.remove('tw-hidden');
            preloader.classList.add('tw-flex')
        }

        function hidePreloader() {
            preloader.classList.add('tw-hidden');
            preloader.classList.remove('tw-flex');
        }

        function loadResources(type, search, category, floor, page = 1) {
            showPreloader();
            const data = new FormData();
            data.append('action', 'filter_resources');
            data.append('type', type || 'resource');
            data.append('search', search || '');
            data.append('category', category || '');

            // Если это тип "event", этажи не передаем
            if (type !== 'event') {
                data.append('floor', floor || 'all');
            }

            data.append('paged', page);

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderResources(data.data.resources);
                        currentPage = data.data.current_page;
                        maxPages = data.data.max_num_pages;
                        renderPagination();
                    }
                }).finally(() => {
                    hidePreloader();
                });
        }

        function renderResources(resources) {
            // console.log('resources: ', resources);


            container.innerHTML = '';

            resources.forEach(resource => {
                const resourceItem = document.createElement('div');

                resourceItem.innerHTML = `<div class="tw-h-full tw-group tw-relative tw-flex tw-flex-col tw-overflow-hidden tw-rounded-lg tw-border tw-border-gray-200 tw-bg-white">
                                                <div class="tw-aspect-w-3 tw-aspect-h-4 tw-bg-gray-200 group-hover:tw-opacity-75 sm:tw-aspect-none sm:tw-h-72 tw-bg-white">
                                                    <img src="${resource.image}" alt="${resource.name[currentLanguage] || resource.name.ru}" class="tw-h-full tw-w-full tw-object-cover tw-object-center sm:tw-h-full sm:tw-w-full">
                                                </div>
                                                <div class="tw-flex tw-flex-1 tw-flex-col tw-space-y-2 tw-p-4 tw-bg-gray-200">
                                                    <h3 class="tw-text-sm tw-font-medium tw-text-gray-900">
                                                        <a href="${resource.link}">
                                                            <span aria-hidden="true" class="tw-absolute tw-inset-0"></span>
                                                            ${resource.name[currentLanguage] || resource.name.ru}
                                                        </a>
                                                    </h3>
                                                    <p class="tw-text-sm tw-text-gray-500">
                                                        ${resource.description[currentLanguage]?.substring(0, 100) || resource.description.ru.substring(0, 100)}${resource.description[currentLanguage]?.length > 100 || resource.description.ru.length > 100 ? '…' : ''}
                                                    </p>
                                                </div>
                                            </div>`;

                container.appendChild(resourceItem);
            });
        }

        function renderPagination() {
            // Clear previous pagination content
            paginationContainer.innerHTML = '';

            // Only render pagination if there are more than 1 page
            if (maxPages > 1) {
                const prevButton = document.createElement('button');
                prevButton.className = 'tw-px-6 tw-py-4 tw-rounded tw-mx-1';
                prevButton.textContent = '« <?php echo __('Prev', 'spaces_mnu_plugin'); ?>';
                prevButton.disabled = currentPage <= 1;

                // Apply styles based on whether the button is disabled
                if (prevButton.disabled) {
                    prevButton.classList.add('tw-bg-gray-300', 'tw-cursor-not-allowed', 'tw-opacity-50');
                } else {
                    prevButton.classList.add('tw-bg-[#262626]', 'tw-text-white', 'hover:tw-bg-[#333333]');
                    prevButton.addEventListener('click', () => {
                        if (currentPage > 1) {
                            loadResources(selectedType, searchInput.value, categoryDropdown.value, getSelectedFloor(), currentPage - 1);
                        }
                    });
                }

                const nextButton = document.createElement('button');
                nextButton.className = 'tw-px-6 tw-py-4 tw-rounded tw-mx-1';
                nextButton.textContent = '<?php echo __('Next', 'spaces_mnu_plugin'); ?> »';
                nextButton.disabled = currentPage >= maxPages;

                // Apply styles based on whether the button is disabled
                if (nextButton.disabled) {
                    nextButton.classList.add('tw-bg-gray-300', 'tw-cursor-not-allowed', 'tw-opacity-50');
                } else {
                    nextButton.classList.add('tw-bg-[#262626]', 'tw-text-white', 'hover:tw-bg-[#333333]');
                    nextButton.addEventListener('click', () => {
                        if (currentPage < maxPages) {
                            loadResources(selectedType, searchInput.value, categoryDropdown.value, getSelectedFloor(), currentPage + 1);
                        }
                    });
                }

                // Append the buttons only if there is pagination
                paginationContainer.appendChild(prevButton);
                paginationContainer.appendChild(nextButton);

                // Append the pagination container to the end of the section if it's not already there
                const renderedResources = document.querySelector('#rendered-resources');
                if (!renderedResources.nextElementSibling) {
                    renderedResources.parentNode.appendChild(paginationContainer);
                }
            }
        }

        resourceTabs[0].classList.remove('tw-bg-transparent', 'tw-text-[#ACACAE]');
        resourceTabs[0].classList.add('tw-bg-[#E2474C]', 'tw-text-white');
        resourceTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active state from all tabs
                resourceTabs.forEach(t => {
                    t.classList.remove('tw-bg-[#E2474C]', 'tw-text-white');
                    t.classList.add('tw-bg-transparent', 'tw-text-[#ACACAE]');
                });

                // Set active state on the clicked tab with smooth transition
                this.classList.remove('tw-bg-transparent', 'tw-text-[#ACACAE]');
                this.classList.add('tw-bg-[#E2474C]', 'tw-text-white');
                selectedType = this.getAttribute('data-type');

                // Call the function to load resources based on the selected tab
                loadResources(selectedType, searchInput.value, categoryDropdown.value, getSelectedFloor());
            });
        });

        searchButton.addEventListener('click', () => {
            loadResources(selectedType, searchInput.value, categoryDropdown.value, getSelectedFloor());
        });

        const defaultFloor = document.querySelector('.floor-btn[data-floor="all"]');
        defaultFloor.classList.add('tw-underline', 'tw-text-[#E2474C]');
        floorButtons.forEach(button => {
            button.addEventListener('click', function() {
                floorButtons.forEach(btn => {
                    btn.classList.remove('tw-underline', 'tw-text-[#E2474C]');
                    btn.classList.add('tw-text-[#ACACAE]')
                });
                this.classList.add('tw-underline', 'tw-text-[#E2474C]');
                loadResources(selectedType, searchInput.value, categoryDropdown.value, getSelectedFloor());
            });
        });

        function getSelectedFloor() {
            const selectedFloorBtn = document.querySelector('.floor-btn.tw-underline');
            return selectedFloorBtn ? selectedFloorBtn.getAttribute('data-floor') : 'all';
        }

        loadResources(selectedType);
        loadCategoriesByType(selectedType);
    });
</script>
<?php
wp_reset_postdata();
get_footer();
