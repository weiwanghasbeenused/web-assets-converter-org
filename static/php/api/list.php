<?php
require_once(__DIR__ . '/../../../config/config.php');
require_once($org_config_dir."config.php");
$response = array(
    'action' => 'list',
    'status' => '',
    'data' => NULL
);
$db = db_connect("guest");

$filter_converted = isset($_GET['converted']) ? $_GET['converted'] : '';
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$limit = isset($_GET['limit']) ? ' LIMIT ' . $_GET['limit'] : '';
$offset = isset($_GET['offset']) ? ' OFFSET ' . $_GET['offset'] : '';
$types = $filter_type === '' ? array_merge($supportedImgFormats, $supportedVidFormats) : ($filter_type === 'images' ? $supportedImgFormats : $supportedVidFormats );
$types_clause = "('" . implode("','", $types) . "')"; 
$weight_clause = $filter_converted === '' ? '' : ($filter_converted == 1 ? ' AND `weight` = 1.0' : ' AND `weight` IS NULL');
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

$response['status'] = 'success';
$response['data'] = $items;
$response['debug'] = $sql;
echo json_encode($response, true);
