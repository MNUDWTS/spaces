<?php

if (! defined('ABSPATH')) {
    exit;
}

class Spaces_MNU_Department_Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_post_spaces_mnu_add_department', [$this, 'handle_add_department']);
        add_action('admin_post_spaces_mnu_edit_department', [$this, 'handle_edit_department']);
        add_action('admin_post_spaces_mnu_delete_department', [$this, 'handle_delete_department']);
    }
    public function add_admin_menu()
    {
        add_users_page(
            __('Departments', 'spaces_mnu_plugin'),
            __('Departments', 'spaces_mnu_plugin'),
            'manage_options',
            'spaces-mnu-departments',
            [$this, 'render_departments_page']
        );
    }

    public function render_departments_page()
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        $departments = get_option('spaces_mnu_plugin_departments', []);
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

?>
        <div class="tw-p-8 tw-bg-white tw-shadow-lg tw-rounded-lg">
            <h1 class="tw-text-3xl tw-font-bold tw-mb-6"><?php esc_html_e('Department Management', 'spaces_mnu_plugin'); ?></h1>
            <?php if ($status == 'added') : ?>
                <div class="tw-bg-green-100 tw-border-l-4 tw-border-green-500 tw-text-green-700 tw-p-4 tw-mb-4">
                    <p><?php esc_html_e('Department added successfully', 'spaces_mnu_plugin'); ?></p>
                </div>
            <?php elseif ($status == 'edited') : ?>
                <div class="tw-bg-green-100 tw-border-l-4 tw-border-green-500 tw-text-green-700 tw-p-4 tw-mb-4">
                    <p><?php esc_html_e('The department has been successfully changed', 'spaces_mnu_plugin'); ?></p>
                </div>
            <?php elseif ($status == 'deleted') : ?>
                <div class="tw-bg-green-100 tw-border-l-4 tw-border-green-500 tw-text-green-700 tw-p-4 tw-mb-4">
                    <p><?php esc_html_e('The department has been successfully removed', 'spaces_mnu_plugin'); ?></p>
                </div>
            <?php elseif ($status == 'exists') : ?>
                <div class="tw-bg-red-100 tw-border-l-4 tw-border-red-500 tw-text-red-700 tw-p-4 tw-mb-4">
                    <p><?php esc_html_e('A department with this slug already exists', 'spaces_mnu_plugin'); ?></p>
                </div>
            <?php elseif ($status == 'invalid_slug') : ?>
                <div class="tw-bg-red-100 tw-border-l-4 tw-border-red-500 tw-text-red-700 tw-p-4 tw-mb-4">
                    <p><?php esc_html_e('The department slug contains invalid characters', 'spaces_mnu_plugin'); ?></p>
                </div>
            <?php endif; ?>

            <h2 class="tw-text-xl tw-font-semibold tw-mb-4"><?php esc_html_e('Add New Department', 'spaces_mnu_plugin'); ?></h2>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="tw-bg-gray-50 tw-p-6 tw-rounded-lg tw-mb-6 tw-shadow-md">
                <?php wp_nonce_field('spaces_mnu_add_department', 'spaces_mnu_add_department_nonce'); ?>
                <input type="hidden" name="action" value="spaces_mnu_add_department">
                <div class="tw-grid tw-gap-6 tw-grid-cols-1 md:tw-grid-cols-2 tw-mb-4">
                    <div>
                        <label for="new_department_name" class="tw-block tw-font-medium tw-mb-2"><?php esc_html_e('Name of the Department', 'spaces_mnu_plugin'); ?></label>
                        <input name="new_department_name" type="text" id="new_department_name" class="tw-w-full tw-border tw-border-gray-300 tw-p-3 tw-rounded-lg" required>
                    </div>
                    <div>
                        <label for="new_department_slug" class="tw-block tw-font-medium tw-mb-2"><?php esc_html_e('Department Slug', 'spaces_mnu_plugin'); ?></label>
                        <input name="new_department_slug" type="text" id="new_department_slug" class="tw-w-full tw-border tw-border-gray-300 tw-p-3 tw-rounded-lg" required pattern="[a-z0-9\-]+" title="<?php esc_attr_e('The slug can only contain lowercase Latin letters, numbers and hyphens.', 'spaces_mnu_plugin'); ?>">
                    </div>
                </div>
                <button type="submit" class="tw-bg-blue-600 tw-text-white tw-py-2 tw-px-6 tw-rounded-lg hover:tw-bg-blue-700"><?php esc_html_e('Add Department', 'spaces_mnu_plugin'); ?></button>
            </form>

            <h2 class="tw-text-xl tw-font-semibold tw-mb-4"><?php esc_html_e('Existing Departments', 'spaces_mnu_plugin'); ?></h2>
            <div class="tw-overflow-x-auto">
                <table class="tw-min-w-full tw-bg-white tw-border tw-rounded-lg tw-shadow-md">
                    <thead class="tw-bg-gray-100">
                        <tr>
                            <th class="tw-px-6 tw-py-3 tw-border-b tw-text-left"><?php esc_html_e('Slug', 'spaces_mnu_plugin'); ?></th>
                            <th class="tw-px-6 tw-py-3 tw-border-b tw-text-left"><?php esc_html_e('Title', 'spaces_mnu_plugin'); ?></th>
                            <th class="tw-px-6 tw-py-3 tw-border-b tw-text-left"><?php esc_html_e('Actions', 'spaces_mnu_plugin'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($departments)) : ?>
                            <?php foreach ($departments as $slug => $department) : ?>
                                <tr class="tw-border-b">
                                    <td class="tw-px-6 tw-py-4"><?php echo esc_html($slug); ?></td>
                                    <td class="tw-px-6 tw-py-4"><?php echo esc_html($department); ?></td>
                                    <td class="tw-px-6 tw-py-4 tw-flex tw-gap-4 tw-justify-start tw-items-center">
                                        <form style="display:inline;" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="tw-inline">
                                            <?php wp_nonce_field('spaces_mnu_edit_department', 'spaces_mnu_edit_department_nonce'); ?>
                                            <input type="hidden" name="action" value="spaces_mnu_edit_department">
                                            <input type="hidden" name="department_slug" value="<?php echo esc_attr($slug); ?>">
                                            <div class="tw-flex tw-gap-4">
                                                <input type="text" name="department_name" value="<?php echo esc_attr($department); ?>" required class="tw-border tw-border-gray-300 tw-rounded-lg tw-px-3 tw-py-2">
                                                <input type="text" name="department_slug_new" value="<?php echo esc_attr($slug); ?>" required pattern="[a-z0-9\-]+" class="tw-border tw-border-gray-300 tw-rounded-lg tw-px-3 tw-py-2" title="<?php esc_attr_e('The slug can only contain lowercase Latin letters, numbers and hyphens.', 'spaces_mnu_plugin'); ?>">
                                                <button type="submit" class="tw-bg-green-500 tw-text-white tw-py-2 tw-px-4 tw-rounded-lg hover:tw-bg-green-600"><?php esc_html_e('Save', 'spaces_mnu_plugin'); ?></button>
                                            </div>
                                        </form>
                                        <form style="display:inline;" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('<?php esc_attr_e('Are you sure you want to delete this department?', 'spaces_mnu_plugin'); ?>');" class="tw-inline tw-ml-4">
                                            <?php wp_nonce_field('spaces_mnu_delete_department', 'spaces_mnu_delete_department_nonce'); ?>
                                            <input type="hidden" name="action" value="spaces_mnu_delete_department">
                                            <input type="hidden" name="department_slug" value="<?php echo esc_attr($slug); ?>">
                                            <button type="submit" class="tw-bg-red-500 tw-text-white tw-py-2 tw-px-4 tw-rounded-lg hover:tw-bg-red-600"><?php esc_html_e('Delete', 'spaces_mnu_plugin'); ?></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3" class="tw-px-6 tw-py-4 tw-text-center"><?php esc_html_e('There are no departments to display.', 'spaces_mnu_plugin'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

<?php
    }
    public function handle_add_department()
    {
        if (! current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'spaces_mnu_plugin'));
        }

        if (! isset($_POST['spaces_mnu_add_department_nonce']) || ! wp_verify_nonce($_POST['spaces_mnu_add_department_nonce'], 'spaces_mnu_add_department')) {
            wp_die(__('Invalid request.', 'spaces_mnu_plugin'));
        }

        if (isset($_POST['new_department_name']) && isset($_POST['new_department_slug'])) {
            $new_department_name = sanitize_text_field($_POST['new_department_name']);
            $new_department_slug = sanitize_title($_POST['new_department_slug']);

            // Валидация слага
            if (! preg_match('/^[a-z0-9\-]+$/', $new_department_slug)) {
                wp_redirect(add_query_arg('status', 'invalid_slug', admin_url('users.php?page=spaces-mnu-departments')));
                exit;
            }

            if (! empty($new_department_name) && ! empty($new_department_slug)) {
                $departments = get_option('spaces_mnu_plugin_departments', []);

                if (! array_key_exists($new_department_slug, $departments)) {
                    $departments[$new_department_slug] = $new_department_name;
                    update_option('spaces_mnu_plugin_departments', $departments);
                    wp_redirect(add_query_arg('status', 'added', admin_url('users.php?page=spaces-mnu-departments')));
                    exit;
                } else {
                    wp_redirect(add_query_arg('status', 'exists', admin_url('users.php?page=spaces-mnu-departments')));
                    exit;
                }
            }
        }

        wp_redirect(admin_url('users.php?page=spaces-mnu-departments'));
        exit;
    }

    public function handle_edit_department()
    {
        if (! current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'spaces_mnu_plugin'));
        }

        if (! isset($_POST['spaces_mnu_edit_department_nonce']) || ! wp_verify_nonce($_POST['spaces_mnu_edit_department_nonce'], 'spaces_mnu_edit_department')) {
            wp_die(__('Invalid request.', 'spaces_mnu_plugin'));
        }

        if (isset($_POST['department_slug']) && isset($_POST['department_name']) && isset($_POST['department_slug_new'])) {
            $current_slug = sanitize_title($_POST['department_slug']);
            $new_name = sanitize_text_field($_POST['department_name']);
            $new_slug = sanitize_title($_POST['department_slug_new']);

            if (! preg_match('/^[a-z0-9\-]+$/', $new_slug)) {
                wp_redirect(add_query_arg('status', 'invalid_slug', admin_url('users.php?page=spaces-mnu-departments')));
                exit;
            }

            if (! empty($current_slug) && ! empty($new_name) && ! empty($new_slug)) {
                $departments = get_option('spaces_mnu_plugin_departments', []);

                if (array_key_exists($current_slug, $departments)) {
                    if ($current_slug !== $new_slug && array_key_exists($new_slug, $departments)) {
                        wp_redirect(add_query_arg('status', 'exists', admin_url('users.php?page=spaces-mnu-departments')));
                        exit;
                    }
                    if ($current_slug !== $new_slug) {
                        unset($departments[$current_slug]);
                        $departments[$new_slug] = $new_name;
                    } else {
                        $departments[$current_slug] = $new_name;
                    }

                    update_option('spaces_mnu_plugin_departments', $departments);
                    wp_redirect(add_query_arg('status', 'edited', admin_url('users.php?page=spaces-mnu-departments')));
                    exit;
                }
            }
        }

        wp_redirect(admin_url('users.php?page=spaces-mnu-departments'));
        exit;
    }

    public function handle_delete_department()
    {
        if (! current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'spaces_mnu_plugin'));
        }

        if (! isset($_POST['spaces_mnu_delete_department_nonce']) || ! wp_verify_nonce($_POST['spaces_mnu_delete_department_nonce'], 'spaces_mnu_delete_department')) {
            wp_die(__('Invalid request.', 'spaces_mnu_plugin'));
        }

        if (isset($_POST['department_slug'])) {
            $department_slug = sanitize_title($_POST['department_slug']);

            if (! empty($department_slug)) {
                $departments = get_option('spaces_mnu_plugin_departments', []);

                if (array_key_exists($department_slug, $departments)) {
                    unset($departments[$department_slug]);
                    update_option('spaces_mnu_plugin_departments', $departments);
                    wp_redirect(add_query_arg('status', 'deleted', admin_url('users.php?page=spaces-mnu-departments')));
                    exit;
                }
            }
        }

        wp_redirect(admin_url('users.php?page=spaces-mnu-departments'));
        exit;
    }
}

new Spaces_MNU_Department_Admin();
