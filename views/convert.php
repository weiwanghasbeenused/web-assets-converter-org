<?php
$action = 'convert';
if(empty($_POST)) :

require_once(__DIR__ . '/../static/php/webpManagerFrontend.php');
$wmf = new WebpManagerFrontend($action, array());

$sql_to_convert = 'SELECT * FROM `media` WHERE `active` = "1" AND `weight` IS NULL AND `type` != "webp" AND `type` != "webm"';
$result = $db->query($sql_to_convert);
$media = array();
while($obj = $result->fetch_assoc()) 
    $media[] = $obj;

$img_files = array();
$vid_files = array();
$img_html = '';
$vid_html = '';
$compressed_files = array(
    'img' => array(),
    'vid' => array()
);

foreach($media as $m) {
    $f = m_pad($m['id']) . '.' . $m['type'];
    if( in_array($m['type'], $supportedImgFormats) ) {
        $img_files[] = $f;
        continue;
    }
    else if(in_array($m['type'], $supportedVidFormats)) {
        $vid_files[] = $f;
        continue;
    }
}

?>
<section class="main-section">
<p id="instruction">Select the non-webp images / non-webm videos to convert.</p>
</section>
<section class="main-section">
<form id="convert-form" method="POST">
    <h2>Images</h2>
    <?php echo $wmf->renderImgList($img_files); ?>
    <h2>Videos</h2>
    <?php echo $wmf->renderVidList($vid_files); ?>
    <button id="convert-form-btn" class="main-action fixed-btn btn">Convert</button>
</form>
</section>
<?php

else:
    require_once(__DIR__ . '/../static/php/webpManagerBackend.php');
    $wmb = new WebpManagerBackend($action, $media_root);
    $files = $_POST['mediaToConvert'];
    $result = $wmb->convert($files);
    ?><section class="main-section"><?php echo $result; ?></section><?php
endif;