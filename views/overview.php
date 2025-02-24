<?php
$action = 'overview';
require_once(__DIR__ . '/../static/php/webpManagerFrontend.php');
$wmf = new WebpManagerFrontend($action, array());

$sql_all = 'SELECT * FROM `media` WHERE `active` = "1"';
$result = $db->query($sql_all);
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
<!-- <p id="instruction">Select the non-webp images / non-webm videos to convert.</p> -->
</section>
<section class="main-section">
<form id="overview-form" method="POST">
    <h2>Images</h2>
    <?php echo $wmf->renderImgList($img_files); ?>
    <h2>Videos</h2>
    <?php echo $wmf->renderVidList($vid_files); ?>
</form>
</section>
<?php