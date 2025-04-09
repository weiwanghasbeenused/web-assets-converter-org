<?php
if(!$_POST || empty($_POST)) {
    exit('nothing to see here . . . ');
} else if(!isset($_POST['action']) || $_POST['action'] !== 'convert'){
    exit('this is the convert endpoint . . .');
}

require_once(__DIR__ . '/../../../config/config.php');
require_once($org_config_dir."config.php");
$response = array(
    'action' => 'convert',
    'status' => '',
    'data' => NULL
);
$response_body = array(
    'success' => array(),
    'fail'    => array()
);
$db = db_connect("guest");
$ids = $_POST['ids'];
$n = count($ids);
$q = implode(", ", array_fill(0, $n, "?"));
$s = str_repeat("s", $n);
$sql = "SELECT * FROM media WHERE id IN ($q)";
$stmt = $db->prepare($sql);
$stmt->bind_param($s, ...$ids);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$files_arr = array();
foreach($result as $m) {
    if($m['weight'] != null || in_array($m['type'], array('webp', 'webm'))) {
        $response_body['fail'][] = $m['id'];
        continue;
    }
    $files_arr[] = $media_root . m_pad(intval($m['id'])) . '.' . $m['type'];
}
if(empty($files_arr)) {
    $response['status'] = 'nothing-to-convert';
    echo json_encode($response, true);
    exit();
}
$files_str = implode(' ', $files_arr);
$log = __DIR__ . '/../../bash/logs/log';
exec(__DIR__ . '/../../bash/media-converter.sh ' . $files_str);
$converted = array();

foreach($result as $m) {
    $padded_id = m_pad($m['id']);
    $f = m_pad($m['id']) . '.' . $m['type'];
    
    if(file_exists($media_root . $padded_id . '.' . 'webp') || file_exists($media_root . $padded_id . '.' . 'webm')){
        $converted[] = $m['id'];
        $response_body['success'][] = array(
            'id' => $m['id'],
            'filename' => $f
        );
    } else  {
        $response_body['fail'][] = array(
            'id' => $m['id'],
            'filename' => $f
        );
    }
}
// $response_body = implode('<br>', $response_body);
$n = count($converted);
$q = implode(", ", array_fill(0, $n, "?"));
$s = str_repeat("s", $n);
$sql = "UPDATE `media` SET `weight` = 1.0 WHERE id IN ($q)";
$stmt = $db->prepare($sql);
$stmt->bind_param($s, ...$converted);
$stmt->execute();
$response = array(
    'action' => 'convert',
    'status' => 'success',
    'data' => $response_body
);
echo json_encode($response, true);
exit();
// $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
