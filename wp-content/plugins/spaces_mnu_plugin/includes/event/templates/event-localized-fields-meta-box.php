<?php
// event-localized-fields-meta-box.php

if (isset($this)) {
    $locales = ['ru' => 'Русский', 'kk' => 'Қазақша', 'en' => 'English'];
    $post_id = isset($post->ID) ? $post->ID : 0;
}
?>
<div class="tw-grid tw-gap-4 tw-mb-4">
    <div class="tw-relative tw-my-6">
        <div class="tw-absolute tw-inset-0 tw-flex tw-items-center" aria-hidden="true">
            <div class="tw-w-full tw-border-t tw-border-gray-300"></div>
        </div>
        <div class="tw-relative tw-flex tw-justify-center">
            <span class="tw-bg-white tw-p-2 tw-rounded tw-text-sm tw-text-gray-500"><?= __('Localized Fields', 'spaces_mnu_plugin'); ?></span>
        </div>
    </div>

    <div id="event-localization-tabs" class="tw-mb-4">
        <ul class="tw-flex tw-border-b">
            <?php foreach ($locales as $locale_key => $locale_name) : ?>
                <li><a href="#tab-<?= esc_attr($locale_key); ?>" class="tw-px-4 tw-py-2"><?= esc_html($locale_name); ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?php foreach ($locales as $locale_key => $locale_name) : ?>
            <div id="tab-<?= esc_attr($locale_key); ?>" class="tw-p-4">
                <?php if ($locale_key != 'ru') : ?>
                    <button type="button" class="tw-bg-blue-500 tw-text-white tw-px-4 tw-py-2 tw-rounded duplicate-from-ru" data-locale="<?= esc_attr($locale_key); ?>"><?= __('Duplicate from Russian', 'spaces_mnu_plugin'); ?></button>
                <?php endif; ?>

                <!-- Name -->
                <div class="tw-mt-4">
                    <label for="event_name_<?= esc_attr($locale_key); ?>" class="tw-block tw-font-medium tw-mb-1"><?= __('Name', 'spaces_mnu_plugin'); ?> (<?= esc_html($locale_name); ?>)</label>
                    <input type="text" id="event_name_<?= esc_attr($locale_key); ?>" name="event_name_<?= esc_attr($locale_key); ?>" value="<?= esc_attr(get_post_meta($post_id, '_event_name_' . $locale_key, true)); ?>" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" />
                </div>

                <!-- Description -->
                <div class="tw-mt-4">
                    <label for="event_description_<?= esc_attr($locale_key); ?>" class="tw-block tw-font-medium tw-mb-1">
                        <?= __('Description', 'spaces_mnu_plugin'); ?> (<?= esc_html($locale_name); ?>)
                    </label>
                    <?php $saved_description = get_post_meta($post_id, '_event_description_' . $locale_key, true); ?>
                    <!-- <textarea id="event_description_<?= esc_attr($locale_key); ?>" name="event_description_<?= esc_attr($locale_key); ?>" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" maxlength="500" rows="5"><?= esc_textarea($saved_description); ?></textarea> -->
                    <?php
                    $editor_id = 'event_description_' . esc_attr($locale_key);
                    $settings  = array(
                        'wpautop'          => true,   // Whether to use wpautop for adding in paragraphs. Note that the paragraphs are added automatically when wpautop is false.
                        'media_buttons'    => false,   // Whether to display media insert/upload buttons
                        'textarea_name'    => $editor_id,   // The name assigned to the generated textarea and passed parameter when the form is submitted.
                        'textarea_rows'    => get_option('default_post_edit_rows', 10),  // The number of rows to display for the textarea
                        'tabindex'         => '',     // The tabindex value used for the form field
                        'editor_css'       => '',     // Additional CSS styling applied for both visual and HTML editors buttons, needs to include <style> tags, can use "scoped"
                        'editor_class'     => 'tw-w-full tw-border tw-rounded tw-px-3 tw-py-2',     // Any extra CSS Classes to append to the Editor textarea
                        'teeny'            => false,  // Whether to output the minimal editor configuration used in PressThis
                        'dfw'              => true,  // Whether to replace the default fullscreen editor with DFW (needs specific DOM elements and CSS)
                        'tinymce'          => true,   // Load TinyMCE, can be used to pass settings directly to TinyMCE using an array
                        'quicktags'        => false,   // Load Quicktags, can be used to pass settings directly to Quicktags using an array. Set to false to remove your editor's Visual and Text tabs.
                        'drag_drop_upload' => true    // Enable Drag & Drop Upload Support (since WordPress 3.9)
                    );
                    wp_editor($saved_description, $editor_id, $settings);


                    ?>
                    <!-- <div class="tw-text-right tw-text-gray-500 tw-text-sm tw-mt-1">
                        <span id="char-count-<?= esc_attr($locale_key); ?>"><?= strlen($saved_description); ?></span>/500 <?= __('characters', 'spaces_mnu_plugin'); ?>
                    </div> -->
                </div>

                <!-- Metadata -->
                <div class="tw-mt-4">
                    <label class="tw-block tw-font-medium tw-mb-1"><?= __('Metadata', 'spaces_mnu_plugin'); ?> (<?= esc_html($locale_name); ?>)</label>
                    <div class="metadata-container">
                        <?php
                        $metadata = get_post_meta($post_id, '_event_metadata_' . $locale_key, true);
                        if (!empty($metadata) && is_array($metadata)) :
                            foreach ($metadata as $key => $value) :
                        ?>
                                <div class="metadata-item tw-flex tw-gap-2 tw-mb-2">
                                    <input type="text" name="event_metadata_<?= esc_attr($locale_key); ?>_key[]" value="<?= esc_attr($key); ?>" placeholder="Key" class="tw-border tw-rounded tw-px-3 tw-py-2" />
                                    <input type="text" name="event_metadata_<?= esc_attr($locale_key); ?>_value[]" value="<?= esc_attr($value); ?>" placeholder="Value" class="tw-border tw-rounded tw-px-3 tw-py-2" />
                                    <button type="button" class="remove-metadata tw-bg-red-500 tw-text-white tw-px-3 tw-py-2 tw-rounded"><?= __('Remove', 'spaces_mnu_plugin'); ?></button>
                                </div>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                    <button type="button" class="add-metadata tw-bg-blue-500 tw-text-white tw-px-4 tw-py-2 tw-rounded tw-mt-2" data-locale="<?= esc_attr($locale_key); ?>"><?= __('Add Metadata', 'spaces_mnu_plugin'); ?></button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>