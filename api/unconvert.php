<?php
if(!$_POST || empty($_POST)) {
    exit('nothing to see here . . . ');
} else if(!isset($_POST['action']) || $_POST['action'] !== 'unconvert'){
    exit('this is the unconvert endpoint . . .');
}
require_once(__DIR__ . '/../utils/m_pad.php');
require_once(__DIR__ . '/../utils/loadConfig.php');
require_once(__DIR__ . '/../utils/db_connect.php');
$config = loadConfig();

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
    if($m['weight'] != 1 ) {
        $response_body['fail'][] = $m['id'];
        continue;
    }
    $padded_id = m_pad($m['id']);
    $files_arr[] = $media_root . m_pad(intval($m['id'])) . '.' . $m['type'];
}

$files_str = implode(' ', $files_arr);
$log = __DIR__ . '/../../bash/logs/log';
exec(__DIR__ . '/../../bash/media-converter.sh ' . $files_str);
$unconverted = array();

foreach($result as $m) {
    $padded_id = m_pad($m['id']);
    $f = $padded_id . '.' . (in_array($m['type'], $config['supportedImgFormats']) ? 'webp' : 'webm');
    
    if(unlink($media_root . $f)){
        $unconverted[] = $m['id'];
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
$n = count($unconverted);
$q = implode(", ", array_fill(0, $n, "?"));
$s = str_repeat("s", $n);
$sql = "UPDATE `media` SET `weight` = NULL WHERE id IN ($q)";
$stmt = $db->prepare($sql);
$stmt->bind_param($s, ...$unconverted);
$stmt->execute();
$response = array(
    'action' => 'unconvert',
    'status' => 'success',
    'data' => $response_body
);
echo json_encode($response, true);
exit();
