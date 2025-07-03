<?php
if(!$_POST || empty($_POST)) {
    exit('nothing to see here . . . ');
} else if(!isset($_POST['action']) || $_POST['action'] !== 'convert'){
    exit('this is the convert endpoint . . .');
}

require_once(__DIR__ . '/../utils/loadConfig.php');
require_once(__DIR__ . '/../utils/db_connect.php');
require_once(__DIR__ . '/../utils/m_pad.php');
$config = loadConfig();
// require_once($org_config_dir."config.php");

$response = array(
    'action' => 'convert',
    'status' => '',
    'data' => NULL
);
$response_body = array(
    'success' => array(),
    'fail'    => array()
);
$db = db_connect("main");
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
    $padded_id = m_pad($m['id']);
    $files_arr[] = $config['media_root'] . '/' . $padded_id . '.' . $m['type'];
}
if(empty($files_arr)) {
    $response['status'] = 'nothing-to-convert';
    echo json_encode($response, true);
    exit();
}
$files_str = implode(' ', $files_arr);
$log = __DIR__ . '/../bash/logs/log';
exec(__DIR__ . '/../bash/media-converter.sh ' . $files_str);
$converted = array();
foreach($result as $m) {
    $padded_id = m_pad($m['id']);
    $f = $padded_id . '.' . $m['type'];
    
    if(file_exists($config['media_root'] . '/' . $padded_id . '.' . 'webp') || file_exists($config['media_root'] . '/' . $padded_id . '.' . 'webm')){
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
if($n === 0) {
    $response['status'] = 'error';
    $response['data'] = $files_str . '<br> None of the files were converted: <br>' . implode('<br>', $ids);
    echo json_encode($response, true);
    exit();
}
$q = implode(", ", array_fill(0, $n, "?"));
$s = str_repeat("s", $n);
$sql = "UPDATE `media` SET `weight` = 1.0 WHERE id IN ($q)";

try{
    $stmt = $db->prepare($sql);
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['data'] = $e->getMessage();
    echo json_encode($response, true);
    exit();
}
$stmt = $db->prepare($sql);
if(!$stmt) {
    $response['status'] = 'error';
    $response['data'] = $db->error;
    echo json_encode($response, true);
    exit();
}
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
