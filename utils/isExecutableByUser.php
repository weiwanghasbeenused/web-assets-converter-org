<?php
function is_executable_by_user($file_path, $valid_user) {
    $file_perms = fileperms($file_path);
    $owner_id = fileowner($file_path);
    $group_id = filegroup($file_path);

    $owner_info = posix_getpwuid($owner_id);
    $group_info = posix_getgrgid($group_id);

    // Get user's info
    $user_info = posix_getpwnam($valid_user);
    if (!$user_info) return false;
    $user_uid = $user_info['uid'];
    $user_gid = $user_info['gid'];
    $user_groups = [$user_gid];

    // Get all groups the user belongs to
    $all_groups = posix_getgroups();
    if ($all_groups) {
        $user_groups = array_unique(array_merge($user_groups, $all_groups));
    }

    // Owner executable
    if ($user_uid === $owner_id && ($file_perms & 0x0040)) {
        return true;
    }
    // Group executable
    if (in_array($group_id, $user_groups) && ($file_perms & 0x0008)) {
        return true;
    }
    // World executable
    if ($file_perms & 0x0001) {
        return true;
    }
    return false;
}