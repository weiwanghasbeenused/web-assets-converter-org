<?php
$action = 'overview';
require_once(__DIR__ . '/../static/php/class/webpManagerFrontend.php');
$wmf = new WebpManagerFrontend($action, array());

$imgFormatsStr = "'" . implode("','", $supportedImgFormats) . "'";
$vidFormatsStr = "'" . implode("','", $supportedVidFormats) . "'";

$sql = "
    SELECT *,
        CASE 
            WHEN type IN ($imgFormatsStr) THEN 'img'
            WHEN type IN ($vidFormatsStr) THEN 'vid'
            ELSE 'other'
        END AS media_category
    FROM media
    WHERE (type IN ($imgFormatsStr) OR type IN ($vidFormatsStr)) AND `active` = 1
";

$img_files = array();
$vid_files = array();

// Execute the query
$result = $db->query($sql);
while($m = $result->fetch_assoc()) {
    $f = m_pad($m['id']) . '.' . $m['type'];
    if($m['media_category'] === 'img') $img_files[] = $f;
    else $vid_files[] = $f;
}
$img_html = '';
$vid_html = '';
$compressed_files = array(
    'img' => array(),
    'vid' => array()
);

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