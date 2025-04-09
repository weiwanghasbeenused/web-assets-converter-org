<?php
    if(!isset($_ENV['AUTH_USERNAME']) || !isset($_ENV['AUTH_PASSWORD']) ){
        echo 'Please add auth user and password to .env.';
        exit();
    }
    $username = $_ENV['AUTH_USERNAME'];
    $password = $_ENV['AUTH_PASSWORD'];

    $items = callAPI('GET', $endpoints['list'], "$username:$password")['data'];
?>
<div id="app">

</div>
<!-- <script src="<?php echo $wac_url; ?>/static/js/class/WACForm.js" type="module"></script> -->
<script type="module">
    import WACForm from "./../web-assets-converter/static/js/class/WACForm.js"
    const app = document.getElementById('app');
    const meta = {
        'username': '<?php echo $username; ?>',
        'password': '<?php echo $password; ?>',
        'imgFormats': <?php echo json_encode($supportedImgFormats); ?>,
        'vidFormats': <?php echo json_encode($supportedVidFormats); ?>
    }
    const filter = {
        'converted': '<?php echo isset($_GET['converted']) ? $_GET['converted'] : 'all'?>',
        'type': '<?php echo isset($_GET['type']) ? $_GET['type'] : 'all'?>',
    }
    const form = new WACForm(app, '', meta, filter);
</script>