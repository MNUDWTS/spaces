<?php

if (! defined('ABSPATH')) {
    exit;
}

class Spaces_MNU_Roles_Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_post_spaces_mnu_add_role', [$this, 'handle_add_role']);
        add_action('admin_post_spaces_mnu_edit_role', [$this, 'handle_edit_role']);
        add_action('admin_post_spaces_mnu_delete_role', [$this, 'handle_delete_role']);
    }
    public function add_admin_menu()
    {
        add_users_page(
            __('Roles', 'spaces_mnu_plugin'),
            __('Roles', 'spaces_mnu_plugin'),
            'manage_options',
            'spaces-mnu-roles',
            [$this, 'render_roles_page']
        );
    }

    public function render_roles_page()
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        $roles = get_option('spaces_mnu_plugin_roles', []);
        $sorted_roles = spaces_mnu_sort_roles($roles);

        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
?>
        <div class="tw-p-8 tw-bg-white tw-shadow-lg tw-rounded-lg">
            <h1 class="tw-text-3xl tw-font-bold tw-mb-6"><?php esc_html_e('Role Management', 'spaces_mnu_plugin'); ?></h1>
            <?php if ($status == 'added') : ?>
                <div class="toast tw-bg-green-100 tw-border-l-4 tw-border-green-500 tw-text-green-700 tw-p-4 tw-mb-4" data-status="added">
                    <p><?php esc_html_e('Role added successfully.', 'spaces_mnu_plugin'); ?></p>
                </div>
            <?php elseif ($status == 'edited') : ?>
                <div class="toast tw-bg-green-100 tw-border-l-4 tw-border-green-500 tw-text-green-700 tw-p-4 tw-mb-4" data-status="edited">
                    <p><?php esc_html_e('Role changed successfully.', 'spaces_mnu_plugin'); ?></p>
                </div>
            <?php elseif ($status == 'deleted') : ?>
                <div class="toast tw-bg-green-100 tw-border-l-4 tw-border-green-500 tw-text-green-700 tw-p-4 tw-mb-4" data-status="deleted">
                    <p><?php esc_html_e('The role was successfully removed.', 'spaces_mnu_plugin'); ?></p>
                </div>
            <?php elseif ($status == 'exists') : ?>
                <div class="toast tw-bg-red-100 tw-border-l-4 tw-border-red-500 tw-text-red-700 tw-p-4 tw-mb-4" data-status="exists">
                    <p><?php esc_html_e('A role with this ID already exists.', 'spaces_mnu_plugin'); ?></p>
                </div>
            <?php elseif ($status == 'invalid_slug') : ?>
                <div class="toast tw-bg-red-100 tw-border-l-4 tw-border-red-500 tw-text-red-700 tw-p-4 tw-mb-4" data-status="invalid_slug">
                    <p><?php esc_html_e('The role ID contains invalid characters.', 'spaces_mnu_plugin'); ?></p>
                </div>
            <?php endif; ?>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const urlParams = new URLSearchParams(window.location.search);

                    if (urlParams.has('status')) {
                        const status = urlParams.get('status');
                        const toast = document.querySelector('.toast');

                        if (toast) {
                            toast.classList.add('tw-opacity-100', 'tw-transform', 'tw-translate-y-0');
                            setTimeout(() => {
                                toast.classList.add('tw-opacity-0', 'tw-translate-y-2');
                                setTimeout(() => {
                                    toast.remove();
                                }, 300);
                            }, 4000);
                        }
                        urlParams.delete('status');
                        const newUrl = window.location.pathname + '?' + urlParams.toString();
                        window.history.replaceState({}, '', newUrl);
                    }
                });
            </script>

            <h2 class="tw-text-xl tw-font-semibold tw-mb-4"><?php esc_html_e('Add New Role', 'spaces_mnu_plugin'); ?></h2>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="tw-bg-gray-50 tw-p-6 tw-rounded-lg tw-mb-6 tw-shadow-md">
                <?php
                wp_nonce_field('spaces_mnu_add_role', 'spaces_mnu_add_role_nonce');
                ?>
                <input type="hidden" name="action" value="spaces_mnu_add_role">
                <div class="tw-grid tw-gap-6 tw-grid-cols-1 md:tw-grid-cols-3">
                    <div>
                        <label for="new_role_name" class="tw-block tw-font-medium tw-mb-2"><?php esc_html_e('Role Title', 'spaces_mnu_plugin'); ?></label>
                        <input name="new_role_name" type="text" id="new_role_name" class="tw-w-full tw-border tw-border-gray-300 tw-p-3 tw-rounded-lg" required>
                    </div>
                    <div>
                        <label for="new_role_id" class="tw-block tw-font-medium tw-mb-2"><?php esc_html_e('Role ID', 'spaces_mnu_plugin'); ?></label>
                        <input name="new_role_id" type="text" id="new_role_id" class="tw-w-full tw-border tw-border-gray-300 tw-p-3 tw-rounded-lg" required pattern="[a-z0-9_]+" title="<?php esc_attr_e('The ID can only contain lowercase Latin letters, numbers, and underscores.', 'spaces_mnu_plugin'); ?>">
                    </div>
                    <div>
                        <label for="new_role_parent" class="tw-block tw-font-medium tw-mb-2"><?php esc_html_e('Parent Role (if it is a subrole)', 'spaces_mnu_plugin'); ?></label>
                        <select name="new_role_parent" id="new_role_parent" class="tw-w-full tw-border tw-border-gray-300 tw-p-3 tw-rounded-lg">
                            <option value=""><?php esc_html_e('Select a parent role', 'spaces_mnu_plugin'); ?></option>
                            <?php foreach ($roles as $role_id => $role_data) : ?>
                                <option value="<?php echo esc_attr($role_id); ?>"><?php echo esc_html($role_data['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="tw-bg-blue-600 tw-text-white tw-py-2 tw-px-6 tw-rounded-lg hover:tw-bg-blue-700 tw-mt-4"><?php esc_html_e('Add Role', 'spaces_mnu_plugin'); ?></button>
            </form>

            <h2 class="tw-text-xl tw-font-semibold tw-mb-4"><?php esc_html_e('Existing Roles', 'spaces_mnu_plugin'); ?></h2>
            <div class="tw-overflow-x-auto">
                <table class="tw-min-w-full tw-bg-white tw-border tw-rounded-lg tw-shadow-md">
                    <thead class="tw-bg-gray-100">
                        <tr>
                            <th class="tw-px-6 tw-py-3 tw-border-b tw-text-left"><?php esc_html_e('Title', 'spaces_mnu_plugin'); ?></th>
                            <th class="tw-px-6 tw-py-3 tw-border-b tw-text-left"><?php esc_html_e('ID', 'spaces_mnu_plugin'); ?></th>
                            <th class="tw-px-6 tw-py-3 tw-border-b tw-text-left"><?php esc_html_e('Parent Role', 'spaces_mnu_plugin'); ?></th>
                            <th class="tw-px-6 tw-py-3 tw-border-b tw-text-left"><?php esc_html_e('Actions', 'spaces_mnu_plugin'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sorted_roles)) : ?>
                            <?php foreach ($sorted_roles as $role_id => $role_data) : ?>
                                <tr class="tw-border-b hover:tw-bg-gray-50" id="row-<?php echo esc_attr($role_id); ?>">
                                    <td class="tw-px-6 tw-py-4">
                                        <span class="role-name-view">
                                            <?php echo str_repeat('- ', $role_data['depth']) . esc_html($role_data['name']); ?>
                                        </span>
                                        <input type="text" name="role_name" value="<?php echo esc_attr($role_data['name']); ?>" class="role-name-input tw-border tw-border-gray-300 tw-rounded-lg tw-px-3 tw-py-2" style="display:none;">
                                    </td>

                                    <td class="tw-px-6 tw-py-4">
                                        <span class="role-id-view"><?php echo esc_html($role_id); ?></span>
                                        <input type="text" name="role_id" value="<?php echo esc_attr($role_id); ?>" class="role-id-input tw-border tw-border-gray-300 tw-rounded-lg tw-px-3 tw-py-2" style="display:none;" disabled>
                                    </td>

                                    <td class="tw-px-6 tw-py-4">
                                        <span class="role-parent-view"><?php echo esc_html($role_data['parent'] ? $roles[$role_data['parent']]['name'] : '-'); ?></span>
                                        <select name="role_parent" class="role-parent-input tw-border tw-border-gray-300 tw-rounded-lg tw-px-3 tw-py-2" style="display:none;">
                                            <option value=""><?php esc_html_e('No parent role', 'spaces_mnu_plugin'); ?></option>
                                            <?php foreach ($roles as $slug => $data) : ?>
                                                <option value="<?php echo esc_attr($slug); ?>" <?php selected($role_data['parent'], $slug); ?>>
                                                    <?php echo esc_html($data['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>

                                    <td class="tw-px-6 tw-py-4 tw-flex tw-gap-4 tw-justify-start tw-items-center">
                                        <button class="tw-bg-blue-500 tw-text-white tw-py-2 tw-px-4 tw-rounded-lg hover:tw-bg-blue-600 edit-btn" onclick="editRole('<?php echo esc_attr($role_id); ?>')"><?php esc_html_e('Edit', 'spaces_mnu_plugin'); ?></button>
                                        <button class="tw-bg-green-500 tw-text-white tw-py-2 tw-px-4 tw-rounded-lg hover:tw-bg-green-600 save-btn" style="display:none;" onclick="saveRole('<?php echo esc_attr($role_id); ?>')"><?php esc_html_e('Save', 'spaces_mnu_plugin'); ?></button>
                                        <button class="tw-bg-red-500 tw-text-white tw-py-2 tw-px-4 tw-rounded-lg hover:tw-bg-red-600 remove-btn" onclick="confirmRemoveRole('<?php echo esc_attr($role_id); ?>')"><?php esc_html_e('Remove', 'spaces_mnu_plugin'); ?></button>
                                        <button class="tw-bg-gray-500 tw-text-white tw-py-2 tw-px-4 tw-rounded-lg hover:tw-bg-gray-600 cancel-btn" style="display:none;" onclick="cancelledit('<?php echo esc_attr($role_id); ?>')"><?php esc_html_e('Cancel', 'spaces_mnu_plugin'); ?></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="tw-px-6 tw-py-4 tw-text-center"><?php esc_html_e('There are no roles to display.', 'spaces_mnu_plugin'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            function editRole(roleId) {
                var row = document.getElementById('row-' + roleId);
                row.querySelector('.role-name-view').style.display = 'none';
                row.querySelector('.role-id-view').style.display = 'none';
                row.querySelector('.role-parent-view').style.display = 'none';

                row.querySelector('.role-name-input').style.display = 'block';
                row.querySelector('.role-parent-input').style.display = 'block';

                row.querySelector('.edit-btn').style.display = 'none';
                row.querySelector('.remove-btn').style.display = 'none';
                row.querySelector('.save-btn').style.display = 'inline-block';
                row.querySelector('.cancel-btn').style.display = 'inline-block';
            }

            function cancelledit(roleId) {
                var row = document.getElementById('row-' + roleId);
                row.querySelector('.role-name-view').style.display = 'block';
                row.querySelector('.role-id-view').style.display = 'block';
                row.querySelector('.role-parent-view').style.display = 'block';

                row.querySelector('.role-name-input').style.display = 'none';
                row.querySelector('.role-parent-input').style.display = 'none';

                row.querySelector('.edit-btn').style.display = 'inline-block';
                row.querySelector('.remove-btn').style.display = 'inline-block';
                row.querySelector('.save-btn').style.display = 'none';
                row.querySelector('.cancel-btn').style.display = 'none';
            }

            function saveRole(roleId) {
                var row = document.getElementById('row-' + roleId);
                var roleName = row.querySelector('.role-name-input').value;
                var roleParent = row.querySelector('.role-parent-input').value;

                var formData = new FormData();
                formData.append('action', 'spaces_mnu_edit_role');
                formData.append('role_id', roleId);
                formData.append('role_name', roleName);
                formData.append('role_parent', roleParent);
                formData.append('spaces_mnu_edit_role_nonce', '<?php echo wp_create_nonce('spaces_mnu_edit_role'); ?>');

                fetch('<?php echo admin_url('admin-post.php'); ?>', {
                        method: 'POST',
                        body: formData
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.status) {
                            window.location.href = '<?php echo admin_url('users.php?page=spaces-mnu-roles'); ?>&status=' + data.data.status;
                        } else {
                            console.error('Error in saving role:', data);
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                    });
            }


            function confirmRemoveRole(roleId) {
                if (confirm('<?php esc_attr_e('Are you sure you want to delete this role? This action is irreversible.', 'spaces_mnu_plugin'); ?>')) {
                    var formData = new FormData();
                    formData.append('action', 'spaces_mnu_delete_role');
                    formData.append('role_id', roleId);
                    formData.append('spaces_mnu_delete_role_nonce', '<?php echo wp_create_nonce('spaces_mnu_delete_role'); ?>');

                    fetch('<?php echo admin_url('admin-post.php'); ?>', {
                            method: 'POST',
                            body: formData
                        }).then(response => response.json())
                        .then(data => {
                            if (data.success && data.data.status) {
                                window.location.href = '<?php echo admin_url('users.php?page=spaces-mnu-roles'); ?>&status=' + data.data.status;
                            } else {
                                console.error('Error in deleting role:', data);
                            }
                        }).catch(error => {
                            console.error('Error:', error);
                        });
                }
            }
        </script>
<?php
    }

    public function handle_add_role()
    {
        if (! current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'spaces_mnu_plugin'));
        }

        if (! isset($_POST['spaces_mnu_add_role_nonce']) || ! wp_verify_nonce($_POST['spaces_mnu_add_role_nonce'], 'spaces_mnu_add_role')) {
            wp_die(__('Invalid request.', 'spaces_mnu_plugin'));
        }

        if (isset($_POST['new_role_name']) && isset($_POST['new_role_id'])) {
            $new_role_name = sanitize_text_field($_POST['new_role_name']);
            $new_role_id = sanitize_title($_POST['new_role_id']);
            $new_role_parent = sanitize_title($_POST['new_role_parent'] ?? '');

            $roles = get_option('spaces_mnu_plugin_roles', []);

            if (! array_key_exists($new_role_id, $roles)) {
                $roles[$new_role_id] = [
                    'name' => $new_role_name,
                    'parent' => $new_role_parent
                ];
                update_option('spaces_mnu_plugin_roles', $roles);

                wp_safe_redirect(add_query_arg('status', 'added', remove_query_arg('status', admin_url('users.php?page=spaces-mnu-roles'))));
                exit;
            } else {
                wp_safe_redirect(add_query_arg('status', 'exists', remove_query_arg('status', admin_url('users.php?page=spaces-mnu-roles'))));
                exit;
            }
        }

        wp_safe_redirect(admin_url('users.php?page=spaces-mnu-roles'));
        exit;
    }

    public function handle_edit_role()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'spaces_mnu_plugin'));
        }

        if (!isset($_POST['spaces_mnu_edit_role_nonce']) || !wp_verify_nonce($_POST['spaces_mnu_edit_role_nonce'], 'spaces_mnu_edit_role')) {
            wp_die(__('Invalid request.', 'spaces_mnu_plugin'));
        }

        if (isset($_POST['role_id']) && isset($_POST['role_name'])) {
            $role_id = sanitize_title($_POST['role_id']);
            $new_role_name = sanitize_text_field($_POST['role_name']);
            $new_role_parent = sanitize_title($_POST['role_parent']);

            $roles = get_option('spaces_mnu_plugin_roles', []);

            if (array_key_exists($role_id, $roles)) {
                $roles[$role_id] = [
                    'name' => $new_role_name,
                    'parent' => $new_role_parent
                ];
                update_option('spaces_mnu_plugin_roles', $roles);

                wp_send_json_success(['status' => 'edited']);
            }
        }

        wp_send_json_error();
    }

    public function handle_delete_role()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'spaces_mnu_plugin'));
        }

        if (!isset($_POST['spaces_mnu_delete_role_nonce']) || !wp_verify_nonce($_POST['spaces_mnu_delete_role_nonce'], 'spaces_mnu_delete_role')) {
            wp_die(__('Invalid request.', 'spaces_mnu_plugin'));
        }

        if (isset($_POST['role_id'])) {
            $role_id = sanitize_title($_POST['role_id']);

            $roles = get_option('spaces_mnu_plugin_roles', []);

            if (array_key_exists($role_id, $roles)) {
                unset($roles[$role_id]);
                update_option('spaces_mnu_plugin_roles', $roles);

                wp_send_json_success(['status' => 'deleted']);
            }
        }

        wp_send_json_error();
    }
}

new Spaces_MNU_Roles_Admin();
