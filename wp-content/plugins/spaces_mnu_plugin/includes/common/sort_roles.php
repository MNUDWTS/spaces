<?php
function spaces_mnu_sort_roles($roles)
{
    uasort($roles, function ($a, $b) {
        return strcmp($a['name'], $b['name']);
    });

    $tree = [];
    $references = [];

    foreach ($roles as $role_id => $role_data) {
        $role_data['id'] = $role_id;
        $role_data['children'] = [];
        $references[$role_id] = $role_data;
    }

    foreach ($references as $role_id => &$role_data) {
        $parent_id = $role_data['parent'];
        if (!empty($parent_id) && isset($references[$parent_id])) {
            $references[$parent_id]['children'][$role_id] = &$role_data;
        } else {
            $tree[$role_id] = &$role_data;
        }
    }
    unset($role_data);

    $sorted_roles = [];

    if (!function_exists('smp_flatten_tree')) {
        function smp_flatten_tree($roles, &$sorted_roles, $depth = 0)
        {
            foreach ($roles as $role_id => $role_data) {
                $role_copy = $role_data;
                unset($role_copy['children']);
                $role_copy['depth'] = $depth;
                $sorted_roles[$role_id] = $role_copy;

                if (!empty($role_data['children'])) {
                    smp_flatten_tree($role_data['children'], $sorted_roles, $depth + 1);
                }
            }
        }
    }

    smp_flatten_tree($tree, $sorted_roles);

    return $sorted_roles;
}
