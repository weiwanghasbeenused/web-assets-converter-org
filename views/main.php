<?php
    if(!isset($_ENV['AUTH_USERNAME']) || !isset($_ENV['AUTH_PASSWORD']) ){
        echo 'Please add auth user and password to .env.';
        exit();
    }
    $username = $_ENV['AUTH_USERNAME'];
    $password = $_ENV['AUTH_PASSWORD'];

    $extra_msg = array();
    if($environment === 'development'){
        $extra_msg[] = '<li>Please make sure the owner of the media files align with the bash script user: ' . exec('whoami') . '</li>';
        $cwebp_is_valid = trim(shell_exec('command -v cwebp'));
        if (empty($cwebp_is_valid)) {
                $extra_msg[] = '<li>cwebp is not found on the system. Please install it first. For more information: https://developers.go>
        }
        $ffmpeg_is_valid = trim(shell_exec('command -v ffmpeg'));
        if (empty($ffmpeg_is_valid)) {
                $extra_msg[] = '<li>ffmpeg is not found on the system. Please install it first.';
        }
    }
    $extra_msg = implode('', $extra_msg);
    $items = callAPI('GET', $endpoints['list'], "$username:$password")['data'];
?>
<div id="app"></div>
<!-- <script src="<?php echo $wac_url; ?>/static/js/class/WACForm.js" type="module"></script> -->
<script type="module">
    import WACForm from "./../web-assets-converter/static/js/class/WACForm.js"
    const app = document.getElementById('app');
    window.submodule_environment = '<?php echo $environment; ?>';
    const meta = {
        'username': '<?php echo $username; ?>',
        'password': '<?php echo $password; ?>',
        'imgFormats': <?php echo json_encode($supportedImgFormats); ?>,
        'vidFormats': <?php echo json_encode($supportedVidFormats); ?>,
        'environment': '<?php echo $environment; ?>',
        'environment_msg': '<?php echo $extra_msg; ?>'
    }
    const filter = {
        'converted': '<?php echo isset($_GET['converted']) ? $_GET['converted'] : 'all'?>',
        'type': '<?php echo isset($_GET['type']) ? $_GET['type'] : 'all'?>',
    }
    const form = new WACForm(app, '', meta, filter);
</script>
