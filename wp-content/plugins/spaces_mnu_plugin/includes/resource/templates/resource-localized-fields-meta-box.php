<?php
// resource-localized-fields-meta-box.php

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

    <div id="resource-localization-tabs" class="tw-mb-4">
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
                    <label for="resource_name_<?= esc_attr($locale_key); ?>" class="tw-block tw-font-medium tw-mb-1"><?= __('Name', 'spaces_mnu_plugin'); ?> (<?= esc_html($locale_name); ?>)</label>
                    <input type="text" id="resource_name_<?= esc_attr($locale_key); ?>" name="resource_name_<?= esc_attr($locale_key); ?>" value="<?= esc_attr(get_post_meta($post_id, '_resource_name_' . $locale_key, true)); ?>" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" />
                </div>

                <!-- Description -->
                <div class="tw-mt-4">
                    <label for="resource_description_<?= esc_attr($locale_key); ?>" class="tw-block tw-font-medium tw-mb-1">
                        <?= __('Description', 'spaces_mnu_plugin'); ?> (<?= esc_html($locale_name); ?>)
                    </label>
                    <?php $saved_description = get_post_meta($post_id, '_resource_description_' . $locale_key, true); ?>
                    <textarea id="resource_description_<?= esc_attr($locale_key); ?>" name="resource_description_<?= esc_attr($locale_key); ?>" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2" maxlength="500" rows="5"><?= esc_textarea($saved_description); ?></textarea>
                    <div class="tw-text-right tw-text-gray-500 tw-text-sm tw-mt-1">
                        <span id="char-count-<?= esc_attr($locale_key); ?>"><?= strlen($saved_description); ?></span>/500 <?= __('characters', 'spaces_mnu_plugin'); ?>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="tw-mt-4">
                    <label class="tw-block tw-font-medium tw-mb-1"><?= __('Metadata', 'spaces_mnu_plugin'); ?> (<?= esc_html($locale_name); ?>)</label>
                    <div class="metadata-container">
                        <?php
                        $metadata = get_post_meta($post_id, '_resource_metadata_' . $locale_key, true);
                        if (!empty($metadata) && is_array($metadata)) :
                            foreach ($metadata as $key => $value) :
                        ?>
                                <div class="metadata-item tw-flex tw-gap-2 tw-mb-2">
                                    <input type="text" name="resource_metadata_<?= esc_attr($locale_key); ?>_key[]" value="<?= esc_attr($key); ?>" placeholder="Key" class="tw-border tw-rounded tw-px-3 tw-py-2" />
                                    <input type="text" name="resource_metadata_<?= esc_attr($locale_key); ?>_value[]" value="<?= esc_attr($value); ?>" placeholder="Value" class="tw-border tw-rounded tw-px-3 tw-py-2" />
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