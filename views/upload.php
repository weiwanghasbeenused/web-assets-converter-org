<?php
$action = 'upload';
if(empty($_FILES)) :
    
require_once(__DIR__ . '/../static/php/webpManagerFrontend.php');
$wmf = new WebpManagerFrontend($action, array());
?>
<section class="main-section">
<p id="instruction">Upload the webp/webm files converted by yourself. Please name the file after its counterpart. <br>E.g., if you're uploading the webp for 00001.jpg, please name it 00001.webp</p>
</section>
<section class="main-section">
<form id="upload-form" method="POST" enctype="multipart/form-data">
    <input id="upload-input" type="file" multiple name="<?php echo $wmf->name; ?>[]">
    <label for="upload-input" class="btn small-btn">Click to select files</label>
    <button id="convert-form-btn" class="main-action fixed-btn btn">Upload</button>
</form>
</section>
<section class="main-section">
<ul id="upload-preview-container" mm-list-view="sheet"></ul>
</section>
<script>
(function(){
const supportedImgFormats = <?php echo json_encode($supportedImgFormats); ?>;
const supportedVidFormats = <?php echo json_encode($supportedVidFormats); ?>;
function sliceFilename(fn){
    let dotPos = fn.lastIndexOf('.');
    return {
        'ext': fn.substring(dotPos + 1),
        'name': fn.substring(0, dotPos)
    };
}
function renderPreviewItem(file){
    let output = document.createElement('li');
    output.className = 'list-item';
    let filename = sliceFilename(file.name);
    let m = document.createElement('div');
    m.className = 'item-media';
    if(filename.ext === 'webp') {
        let img = document.createElement('img');
        img.onload = function(){
            URL.revokeObjectURL(img.src);  // no longer needed, free memory
        }
        img.src = URL.createObjectURL(file); // set src to blob url

        let reader = new FileReader();
        reader.readAsDataURL(file); 
        reader.onloadend = function() {
            img.setAttribute('base64', reader.result);
        }
        m.appendChild(img);
    } else if(filename.ext === 'webm') {
        m.innerHTML = '<p>preview is not supported for this type of file</p>';
    } else return false;
    let name = document.createElement('p');
    name.innerText = file.name;
    output.appendChild(m);
    output.appendChild(name);

    return output;
}
const form = document.getElementById('upload-form');
const input = document.getElementById('upload-input');
const preview = document.getElementById('upload-preview-container');

input.addEventListener('change', function(){
    preview.innerHTML = '';
    console.log('change');
    for(let i = 0; i < this.files.length; i++) {
        let item = renderPreviewItem(this.files[i]);
        console.log(item);
        if(item === false){
            console.log('not webm/webp!');
            alert('You can only upload webp and webm here.');
            input.value = null;
            preview.innerHTML = '';
            break;
        }
        preview.appendChild(item);
    }
});

}())
</script>
<style>
    input[type="file"] {
        display: none;
    }
</style>
<?php 

else:
require_once(__DIR__ . '/../static/php/webpManagerBackend.php');
$wmb = new WebpManagerBackend($action, $media_root);
if(isset($_FILES[$wmb->name])) {
    $files = $_FILES[$wmb->name];
    $html = $wmb->upload($files);
}
else {
    $html = 'The data is not retrieved. . . please refresh and try again.';
}
echo $html;

endif;