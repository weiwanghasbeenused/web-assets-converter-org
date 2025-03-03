<?php

function sliceFilename($fn) {
    $p = strrpos($fn, '.');
    $output = array(
        'ext'  => substr($fn, $p + 1),
        'name' => substr($fn, 0, $p)
    );
    return $output;
}
function renderMediaListItem($fn, $id, $mediaType='img'){
    $output = $mediaType == 'img' ? renderImgRow($fn) : renderVidRow($fn);
    $name = 'mediaToConvert[]';
    $e_id = 'img-'.$id;
    $output = '<li class="list-item"><input id="'.$e_id.'" class="converter-item-checkbox" name="'.$name.'" type="checkbox" value="'.$fn.'"><label class="checkbox-label" for="'. $e_id .'"><div class="item-media">' . $output . '</div><div class="item-name">' . $fn . '</div></label></li>';
    return $output;
}
function renderImgRow($fn){
    $src = '/media/' . $fn;
    return '<img src="'.$src.'">';
}
function renderVidRow($fn){
    $src = '/media/' . $fn;
    return '<video muted playsinline controls src="'.$src.'"></video>';
}
// function commandExist($cmd) {
//     $return = shell_exec(sprintf("which %s", escapeshellarg($cmd)));
//     // var_dump($return);
//     return !empty($return);
// }
function commandExist($command_name)
{
   return (null !== shell_exec("command -v $command_name"));
}