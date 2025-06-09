<?php if (! defined('ABSPATH')) {
    exit;
}

class Spaces_MNU_Plugin_User_Fields
{
    public function __construct()
    {
        add_action('show_user_profile', [$this, 'add_custom_user_profile_fields']);
        add_action('edit_user_profile', [$this, 'add_custom_user_profile_fields']);

        add_action('personal_options_update', [$this, 'save_custom_user_profile_fields']);
        add_action('edit_user_profile_update', [$this, 'save_custom_user_profile_fields']);
    }

    /**
     * @param WP_User $user Объект пользователя.
     */
    public function add_custom_user_profile_fields($user)
    {
        $departments = get_option('spaces_mnu_plugin_departments', []);
        $roles = get_option('spaces_mnu_plugin_roles', []);
?>
        <div class="tw-my-12">
            <h3><?php esc_html_e('Additional information', 'spaces_mnu_plugin'); ?></h3>
            <table class="form-table">
                <?php wp_nonce_field('spaces_mnu_save_user_fields', 'spaces_mnu_user_fields_nonce'); ?>
                <tr>
                    <th><label for="job_title"><?php esc_html_e('Job Title', 'spaces_mnu_plugin'); ?></label></th>
                    <td>
                        <input type="text" name="job_title" id="job_title" value="<?php echo esc_attr(get_the_author_meta('job_title', $user->ID)); ?>" class="regular-text" />
                        <br /><span class="description"><?php esc_html_e("Enter the user's job title", 'spaces_mnu_plugin'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="department"><?php esc_html_e('Department', 'spaces_mnu_plugin'); ?></label></th>
                    <td>
                        <select name="department" id="department">
                            <option value=""><?php esc_html_e('Select a department', 'spaces_mnu_plugin'); ?></option>
                            <?php foreach ($departments as $value => $label) : ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected(get_the_author_meta('department', $user->ID), $value); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <br /><span class="description"><?php esc_html_e('Select the department the user works in', 'spaces_mnu_plugin'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="mnu_role"><?php esc_html_e('Role', 'spaces_mnu_plugin'); ?></label></th>
                    <td>
                        <select name="mnu_role" id="mnu_role">
                            <option value=""><?php esc_html_e('Select a role', 'spaces_mnu_plugin'); ?></option>
                            <?php
                            $parent_roles = [];
                            $sub_roles = [];
                            foreach ($roles as $role_key => $role_data) {
                                if (empty($role_data['parent'])) {
                                    $parent_roles[$role_key] = $role_data['name'];
                                } else {
                                    $sub_roles[$role_data['parent']][] = [
                                        'key' => $role_key,
                                        'name' => $role_data['name']
                                    ];
                                }
                            }
                            foreach ($parent_roles as $parent_key => $parent_name) : ?>
                                <option value="<?php echo esc_attr($parent_key); ?>" <?php selected(get_the_author_meta('mnu_role', $user->ID), $parent_key); ?>>
                                    <?php echo esc_html__($parent_name); ?>
                                </option>

                                <?php if (isset($sub_roles[$parent_key])) : ?>
                                    <optgroup label="&nbsp;&nbsp;<?php echo esc_html($parent_name); ?> sub-roles">
                                        <?php foreach ($sub_roles[$parent_key] as $subrole) : ?>
                                            <option value="<?php echo esc_attr($subrole['key']); ?>" <?php selected(get_the_author_meta('mnu_role', $user->ID), $subrole['key']); ?>>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<?php echo esc_html__($subrole['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <br /><span class="description"><?php esc_html_e('Select user role', 'spaces_mnu_plugin'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="phone"><?php esc_html_e('Phone', 'spaces_mnu_plugin'); ?></label></th>
                    <td>
                        <input type="text" name="phone" id="phone" value="<?php echo esc_attr(get_the_author_meta('phone', $user->ID)); ?>" class="regular-text" />
                        <br /><span class="description"><?php esc_html_e('Enter phone number', 'spaces_mnu_plugin'); ?></span>
                    </td>
                </tr>

            </table>
        </div>
<?php
    }

    /**
     * @param int $user_id ID пользователя.
     */
    public function save_custom_user_profile_fields($user_id)
    {
        if (! current_user_can('edit_user', $user_id)) {
            return false;
        }
        if (! isset($_POST['spaces_mnu_user_fields_nonce']) || ! wp_verify_nonce($_POST['spaces_mnu_user_fields_nonce'], 'spaces_mnu_save_user_fields')) {
            return false;
        }

        $departments = get_option('spaces_mnu_plugin_departments', []);
        $roles = get_option('spaces_mnu_plugin_roles', []);

        if (isset($_POST['job_title'])) {
            update_user_meta($user_id, 'job_title', sanitize_text_field($_POST['job_title']));
        }

        if (isset($_POST['department'])) {
            $department = sanitize_text_field($_POST['department']);
            if (array_key_exists($department, $departments)) {
                update_user_meta($user_id, 'department', $department);
            }
        }

        if (isset($_POST['mnu_role'])) {
            $mnu_role = sanitize_text_field($_POST['mnu_role']);
            if (array_key_exists($mnu_role, $roles)) {
                update_user_meta($user_id, 'mnu_role', $mnu_role);
            }
        }

        // if (defined('WP_DEBUG') && WP_DEBUG) {
        //     error_log("Spaces MNU Plugin: Saved custom user fields for user ID {$user_id}");
        // }
    }
}

new Spaces_MNU_Plugin_User_Fields();
