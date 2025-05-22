<?php
    if(!isset($_ENV['AUTH_USERNAME']) || !isset($_ENV['AUTH_PASSWORD']) ){
        echo 'Please add auth user and password to .env.';
        exit();
    }
    $username = $_ENV['AUTH_USERNAME'];
    $password = $_ENV['AUTH_PASSWORD'];

    $extra_msg = '';
    if($environment === 'development'){
        $extra_msg .= 'Please make sure the owner of the media files align with the bash script user: ' . exec('whoami');
    }

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