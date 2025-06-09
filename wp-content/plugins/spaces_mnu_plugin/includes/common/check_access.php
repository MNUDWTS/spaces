<?php
function check_access($user_id, $access_roles)
{
    if (!is_array($access_roles)) {
        if (is_string($access_roles)) {
            $access_roles = explode(',', $access_roles);
        } else {
            error_log('check_access: $access_roles должен быть массивом или строкой.');
            return false;
        }
    }
    $user_role = get_the_author_meta('mnu_role', $user_id);
    $has_access = false;
    if (in_array('student', $access_roles)) {
        if (substr($user_role, -8) === '_student') {
            $has_access = true;
        }
    }
    if (in_array('staff', $access_roles)) {
        if (substr($user_role, -6) === '_staff') {
            $has_access = true;
        }
    }
    foreach ($access_roles as $role) {
        if ($role === $user_role) {
            $has_access = true;
        }
    }
    return $has_access;
}
