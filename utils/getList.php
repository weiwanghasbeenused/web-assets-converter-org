<?php
require_once(__DIR__ . '/../utils/isEditableByUser.php');
require_once(__DIR__ . '/../utils/db_connect.php');
function getList($offset, $limit, $img_formats, $vid_formats, $filter = array('type'=>'', 'converted'=>'')){
    $db = db_connect("guest");
    $types = $filter['type'] === '' ? array_merge($img_formats, $vid_formats) : ($filter['type'] === 'images' ? $img_formats : $vid_formats );
    $types_clause = "('" . implode("','", $types) . "')"; 
    $weight_clause = $filter['converted'] === '' ? '' : ($filter['converted'] == 1 ? ' AND `converted` = 1.0' : ' AND `weight` IS NULL');
    $sql = 
        "SELECT `id`, `type`, `spec`,
        CASE 
            WHEN `type` IN ('webp', 'webm') THEN -1
            WHEN `weight` IS NULL THEN 0
            ELSE 1
        END AS `converted` 
        FROM `media` WHERE `active` = 1 AND `type` IN $types_clause $weight_clause
        $limit $offset
        ";

    $result = $db->query($sql);
    $items = $result->fetch_all(MYSQLI_ASSOC);
    $valid_owner = exec('whoami');
    foreach($items as $key => &$item) {
        $id_padded = strval($item['id']);
        while(strlen($id_padded) < 5)
            $id_padded = '0' . $id_padded;
        $file_path = __DIR__ . "/../../media/" . $id_padded . "." . $item['type'];
        if(!file_exists($file_path)) {
            $item['missing-file'] = true;
            continue;
        }
        $item['writable'] = is_editable_by_user($file_path, $valid_owner);
    }
    unset($item);
    return $items;
}