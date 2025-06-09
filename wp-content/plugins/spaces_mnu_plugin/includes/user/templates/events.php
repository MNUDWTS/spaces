<?php
?>
<div class='tw-relative'>
    <div class="tw-w-full tw-flex tw-justify-between tw-items-center tw-mb-4">
        <h2 class="tw-text-2xl tw-font-bold "><?= __('My Events', 'spaces_mnu_plugin'); ?></h2>
        <a href="#" id="event-create-button" class="tw-bg-transparent tw-border-none tw-flex tw-justify-center tw-items-center tw-gap-2">
            <h4 class="tw-hidden md:tw-block"><?= __('Add New Event', 'spaces_mnu_plugin'); ?></h4>
            <svg width="24px" height="24px" viewBox="0 0 24 24" stroke="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
    </div>
    <?php
    $current_user_id = get_current_user_id();
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args = array(
        'post_type' => 'event',
        'posts_per_page' => 10,
        'paged' => $paged,
        'meta_query' => array(
            array(
                'key' => '_event_responsible',
                'value' => sprintf('i:%d;', $current_user_id),
                'compare' => 'LIKE'
            ),
        ),
    );
    $user_events = new WP_Query($args);
    if ($user_events->have_posts()) : ?>
        <div class='tw-relative tw-flex tw-flex-col tw-w-full tw-overflow-scroll tw-text-gray-700 tw-bg-transparent tw-shadow-md tw-rounded-lg tw-bg-clip-border'>
            <table class='tw-w-full tw-text-left tw-table-auto tw-min-w-max '>
                <thead>
                    <tr>
                        <th class="tw-p-4 tw-border-b tw-border-slate-200 tw-bg-slate-50 tw-text-center">
                            <p class="tw-text-sm tw-font-normal tw-leading-none tw-text-slate-500"><?= __('Name', 'spaces_mnu_plugin'); ?></p>
                        </th>
                        <th class="tw-p-4 tw-border-b tw-border-slate-200 tw-bg-slate-50 tw-text-center">
                            <p class="tw-text-sm tw-font-normal tw-leading-none tw-text-slate-500"><?= __('Avalability', 'spaces_mnu_plugin'); ?></p>
                        </th>
                        <th class="tw-p-4 tw-border-b tw-border-slate-200 tw-bg-slate-50 tw-text-center">
                            <p class="tw-text-sm tw-font-normal tw-leading-none tw-text-slate-500"><?= __('Actions', 'spaces_mnu_plugin'); ?></p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user_events->have_posts()) : $user_events->the_post(); ?>
                        <?php
                        $post_id = get_the_ID();
                        $unfiltered_event_categories = wp_get_post_terms($post_id, 'event_category');
                        $excluded_categories = array('all');
                        $event_categories = array_filter($unfiltered_event_categories, function ($term) use ($excluded_categories) {
                            return !in_array($term->slug, $excluded_categories);
                        });
                        $event_images = get_post_meta($post_id, '_event_images', true);
                        $event_responsible = get_post_meta($post_id, '_event_responsible_users', true);
                        $event_access = get_post_meta($post_id, '_event_access', true);
                        $event_availability_enabled = get_post_meta($post_id, '_event_availability_enabled', true);
                        $event_name_ru = get_post_meta($post_id, '_event_name_ru', true);
                        $event_name_kk = get_post_meta($post_id, '_event_name_kk', true);
                        $event_name_en = get_post_meta($post_id, '_event_name_en', true);
                        ?>
                        <tr class="hover:tw-bg-slate-50 tw-border-b tw-border-slate-200">
                            <td class="tw-p-4 tw-py-5">
                                <a href="<?php the_permalink(); ?>" class="tw-text-blue-500 tw-flex-grow tw-col-span-1 tw-flex tw-gap-2">
                                    <?php
                                    $images = [];
                                    foreach ($event_images as $image_id) {
                                        $image_url = wp_get_attachment_url($image_id);
                                        if ($image_url) {
                                            array_push($images, $image_url);
                                        }
                                    } ?>
                                    <?php if ($images): ?>
                                        <div>
                                            <img class="tw-aspect-square tw-rounded-xl tw-object-cover tw-object-center" width="100px" height="100px" src="<?php echo $images[0]; ?>" alt="Resource Image">
                                        </div>
                                    <?php endif; ?>
                                    <div class="tw-space-y-2">
                                        <p class="tw-block tw-font-semibold tw-text-sm tw-w-full tw-max-w-[250px] tw-text-slate-800">
                                            <?php the_title(); ?>
                                        </p>
                                        <?php
                                        if (is_array($event_categories)) {
                                            foreach ($event_categories as $category) {
                                                echo '<p class="tw-bg-white tw-px-4 tw-py-2 tw-rounded-xl tw-text-sm">' . esc_html(__($category->name, 'spaces_mnu_plugin')) . '</p>';
                                            }
                                        } ?>
                                    </div>
                                </a>
                            </td>
                            <td class="tw-p-4 tw-py-5 tw-text-center">
                                <p class="tw-block tw-font-semibold tw-text-sm tw-text-slate-800">
                                    <?= $event_availability_enabled === 'true' ? __('Available', 'spaces_mnu_plugin') : __('Not Available', 'spaces_mnu_plugin'); ?></p>
                            </td>
                            <td class="tw-relative">
                                <div class="tw-flex tw-justify-end tw-items-center tw-w-full tw-h-full">
                                    <svg class="event-action-button" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="11.5887" cy="4.58824" r="1.58824" fill="#717171" />
                                        <circle cx="11.5887" cy="11.9999" r="1.58824" fill="#717171" />
                                        <circle cx="11.5887" cy="19.412" r="1.58824" fill="#717171" />
                                    </svg>
                                </div>
                                <div class="event-action-menu tw-absolute tw-space-y-2 tw-right-6 tw-top-0 tw-z-10 tw-mt-2 tw-w-fit tw-p-2 tw-origin-top-right tw-rounded-md tw-bg-white tw-shadow-lg tw-ring-1 tw-ring-black tw-ring-opacity-5 focus:tw-outline-none tw-hidden" role="menu" aria-orientation="vertical" aria-labelledby="event-action-button" tabindex="-1">
                                    <button class="edit-event-button tw-w-full tw-bg-yellow-500 tw-text-white tw-px-2 tw-py-1 tw-rounded" data-event-id="<?php echo get_the_ID(); ?>">
                                        <?= __('Edit', 'spaces_mnu_plugin'); ?>
                                    </button>
                                    <?php $nonce = wp_create_nonce('event_nonce'); ?>
                                    <button data-event-id="<?php echo get_the_ID(); ?>" data-nonce="<?php echo esc_attr($nonce); ?>" class="delete-event-button tw-w-full tw-bg-red-500 tw-text-white tw-px-1 tw-py-1 tw-rounded">
                                        <?= __('Delete', 'spaces_mnu_plugin'); ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <script>
                        document.querySelectorAll('.event-action-button').forEach(function(actionButton) {
                            const actionMenu = actionButton.closest('td').querySelector('.event-action-menu');

                            function closeAllMenus() {
                                document.querySelectorAll('.event-action-menu').forEach(function(menu) {
                                    if (!menu.classList.contains('tw-hidden')) {
                                        menu.classList.add('tw-hidden');
                                    }
                                });
                            }
                            actionButton.addEventListener('click', function(event) {
                                event.stopPropagation();
                                if (!actionMenu.classList.contains('tw-hidden')) {
                                    actionMenu.classList.add('tw-hidden');
                                } else {
                                    closeAllMenus();
                                    actionMenu.classList.remove('tw-hidden');
                                }
                            });
                            window.addEventListener('click', function(e) {
                                if (!actionButton.contains(e.target) && !actionMenu.contains(e.target)) {
                                    closeAllMenus();
                                }
                            });
                        });
                    </script>
                </tbody>
            </table>
        </div>
        <div class="tw-flex tw-justify-end tw-items-center tw-px-4 tw-py-3">
            <?php
            $total_pages = $user_events->max_num_pages;
            $current_page = max(1, get_query_var('paged'));
            $section = 'events';
            ?>
            <div class="tw-flex tw-space-x-1">
                <?php
                if ($current_page > 1) : ?>
                    <a href="<?= esc_url(add_query_arg(array('paged' => $current_page - 1, 'section' => $section), get_pagenum_link())) ?>" class="tw-flex tw-justify-center tw-items-center tw-px-3 tw-py-1 tw-min-w-9 tw-min-h-9 tw-text-sm tw-font-normal tw-text-slate-500 tw-bg-white tw-border tw-border-slate-200 tw-rounded hover:tw-bg-slate-50 hover:tw-border-slate-400 tw-transition tw-duration-200 tw-ease">
                        <?= __('Prev', 'spaces_mnu_plugin'); ?>
                    </a>
                <?php endif; ?>
                <?php
                for ($i = 1; $i <= $total_pages; $i++) :
                    if ($i == $current_page) : ?>
                        <span class="tw-flex tw-justify-center tw-items-center tw-px-3 tw-py-1 tw-min-w-9 tw-min-h-9 tw-text-sm tw-font-normal tw-text-white tw-bg-slate-800 tw-border tw-border-slate-800 tw-rounded">
                            <?= $i; ?>
                        </span>
                    <?php else : ?>
                        <a href="<?= esc_url(add_query_arg(array('paged' => $i, 'section' => $section), get_pagenum_link())) ?>" class="tw-flex tw-justify-center tw-items-center tw-px-3 tw-py-1 tw-min-w-9 tw-min-h-9 tw-text-sm tw-font-normal tw-text-slate-500 tw-bg-white tw-border tw-border-slate-200 tw-rounded hover:tw-bg-slate-50 hover:tw-border-slate-400 tw-transition tw-duration-200 tw-ease">
                            <?= $i; ?>
                        </a>
                <?php endif;
                endfor; ?>
                <?php
                if ($current_page < $total_pages) : ?>
                    <a href="<?= esc_url(add_query_arg(array('paged' => $current_page + 1, 'section' => $section), get_pagenum_link())) ?>" class="tw-flex tw-justify-center tw-items-center tw-px-3 tw-py-1 tw-min-w-9 tw-min-h-9 tw-text-sm tw-font-normal tw-text-slate-500 tw-bg-white tw-border tw-border-slate-200 tw-rounded hover:tw-bg-slate-50 hover:tw-border-slate-400 tw-transition tw-duration-200 tw-ease">
                        <?= __('Next', 'spaces_mnu_plugin'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else : ?>
        <p><?= __('No events found.', 'spaces_mnu_plugin'); ?></p>
    <?php endif; ?>

    <?php
    wp_reset_postdata();
    ?>
</div>
<div id="confirmationModalEvent" class="tw-fixed tw-inset-0 tw-z-50 tw-hidden tw-flex tw-items-center tw-justify-center tw-bg-black tw-bg-opacity-50">
    <div class="tw-bg-white tw-rounded-lg tw-overflow-hidden tw-shadow-lg tw-w-11/12 tw-max-w-lg">
        <div class="tw-p-6 tw-border-b tw-border-gray-200">
            <h3 class="tw-text-lg tw-font-medium tw-text-gray-900" id="modalEventTitle"><?= __('Confirm', 'spaces_mnu_plugin'); ?></h3>
            <p class="tw-text-sm tw-text-gray-600 tw-mt-2" id="modalEventMessage"></p>
        </div>
        <div class="tw-flex tw-justify-end tw-space-x-3 tw-p-4">
            <button id="cancelButton" class="tw-bg-gray-200 tw-text-gray-700 tw-px-4 tw-py-2 tw-rounded-lg tw-font-semibold" onclick="hideModalEvent()"><?= __('Cancel', 'spaces_mnu_plugin'); ?></button>
            <button id="confirmEventButton" class="tw-bg-red-500 tw-text-white tw-px-4 tw-py-2 tw-rounded-lg tw-font-semibold"><?= __('Delete', 'spaces_mnu_plugin'); ?></button>
        </div>
    </div>
</div>
<script>
    function showModalEvent(title, message, onConfirm) {
        document.getElementById("modalEventTitle").textContent = title;
        document.getElementById("modalEventMessage").textContent = message;
        const confirmEventButton = document.getElementById("confirmEventButton");
        confirmEventButton.onclick = function() {
            hideModalEvent();
            if (onConfirm) onConfirm();
        };
        document.getElementById("confirmationModalEvent").classList.remove("tw-hidden");
    }

    function hideModalEvent() {
        document.getElementById("confirmationModalEvent").classList.add("tw-hidden");
    }
</script>