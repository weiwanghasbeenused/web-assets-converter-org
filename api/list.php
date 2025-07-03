<?php
require_once(__DIR__ . '/../utils/loadConfig.php');
require_once(__DIR__ . '/../utils/getList.php');
$config = loadConfig();
$response = array(
    'action' => 'list',
    'status' => '',
    'data' => NULL
);
$filter = array();
$filter['converted'] = isset($_GET['converted']) ? $_GET['converted'] : '';
$filter['type'] = isset($_GET['type']) ? $_GET['type'] : '';
$limit = isset($_GET['limit']) ? ' LIMIT ' . $_GET['limit'] : '';
$offset = isset($_GET['offset']) ? ' OFFSET ' . $_GET['offset'] : '';
$items = getList($offset, $limit, $config['supportedImgFormats'], $config['supportedVidFormats'], $filter);
// $types = $filter_type === '' ? array_merge($supportedImgFormats, $supportedVidFormats) : ($filter_type === 'images' ? $supportedImgFormats : $supportedVidFormats );
// $types_clause = "('" . implode("','", $types) . "')"; 
// $weight_clause = $filter_converted === '' ? '' : ($filter_converted == 1 ? ' AND `converted` = 1.0' : ' AND `weight` IS NULL');
// $sql = 
//     "SELECT `id`, `type`, `converted`, `width`, `height`,
//     FROM `media` WHERE `active` = 1 AND `type` IN $types_clause $weight_clause
//     $limit $offset
//     ";

// $result = $db->query($sql);
// $items = $result->fetch_all(MYSQLI_ASSOC);
// $valid_owner = exec('whoami');
// foreach($items as $key => &$item) {
//     $file_path = __DIR__ . "/../../media/" . m_pad($item['id']) . "." . $item['type'];
//     if(!file_exists($file_path)) {
//         $item['missing-file'] = true;
//         continue;
//     }
//     $item['writable'] = is_editable_by_user($file_path, $valid_owner);
// }
$response['status'] = 'success';
$response['data'] = $items;
echo json_encode($response, true);
