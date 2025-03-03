<?php
$action = 'delete';
if(empty($_POST)) :

require_once(__DIR__ . '/../static/php/class/webpManagerFrontend.php');
$wmf = new WebpManagerFrontend($action, array('img' => $supportedImgFormats, 'vid' => $supportedVidFormats));

$sql_to_delete = 'SELECT * FROM `media` WHERE `active` = "1" AND `weight` IS NOT NULL';
$result = $db->query($sql_to_delete);
$media = array();
while($obj = $result->fetch_assoc()) 
    $media[] = $obj;

$img_files = array();
$vid_files = array();
$img_html = '';
$vid_html = '';

foreach($media as $m) {
    if( in_array($m['type'], $supportedImgFormats) ) {
        $img_files[] = m_pad($m['id']) . '.webp';
        continue;
    }
    else if(in_array($m['type'], $supportedVidFormats)) {
        $vid_files[] = m_pad($m['id']) . '.webm';
        continue;
    }
}

?>
<section class="main-section">
<p id="instruction">You can delete the webp/webm files here. If you want to delete media of other types, please use <a class="prevent-link-shadow" href="/open-records-generator">open-records-generator</a></p>
</section>
<section class="main-section">
<form id="delete-form" method="POST">
    <h2>Images</h2>
    <?php
        echo $wmf->renderImgList($img_files);
    ?>
    <h2>Videos</h2>
    <?php
        echo $wmf->renderVidList($vid_files);
    ?>
    <button id="delete-form-btn" class="main-action fixed-btn btn">Delete</button>
</form>
</section>
<?php
else:
    require_once(__DIR__ . '/../static/php/webpManagerBackend.php');
    $wmb = new WebpManagerBackend($action, $media_root);
    if(isset($_POST[$wmb->name])) {
        $filenames = $_POST[$wmb->name];
        $html = $wmb->delete($filenames);
    }
    else {
        $html =  'The data is not retrieved. . . please refresh and try again.';
    }
    ?><?php echo $html; ?><?php
endif;
