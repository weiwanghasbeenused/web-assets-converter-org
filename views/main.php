<?php
    // if(!isset($_ENV['AUTH_USERNAME']) || !isset($_ENV['AUTH_PASSWORD']) ){
    //     echo 'Please add auth user and password to .env.';
    //     exit();
    // }
    // $username = $_ENV['AUTH_USERNAME'];
    // $password = $_ENV['AUTH_PASSWORD'];
    require_once(__DIR__ . '/../utils/getList.php');
    $extra_msg = array();
    if($environment === 'development'){
        require_once(__DIR__ . '/../utils/isExecutableByUser.php');
        require_once(__DIR__ . '/../utils/isEditableByUser.php');
        $user = exec('whoami');
        $extra_msg[] = '<li>Please make sure the media files can be edited by the user/group: ' . $user . '.</li>';
        $cwebp_is_valid = trim(shell_exec('command -v cwebp'));
        if (empty($cwebp_is_valid)) {
                $extra_msg[] = '<li>cwebp is not found on the system. Please install it first.</li>';
        }
        $ffmpeg_is_valid = trim(shell_exec('command -v ffmpeg'));
        if (empty($ffmpeg_is_valid)) {
                $extra_msg[] = '<li>ffmpeg is not found on the system. Please install it first.</li>';
        }
        $bash_is_executable = is_executable_by_user(__DIR__ . '/../bash/media-converter.sh', $user);
        if(!$bash_is_executable) {
            $extra_msg[] = '<li>media-converter.sh is not executable by '.$user.'.</li>';
        }
        $log_is_writable = is_editable_by_user(__DIR__ . '/../bash/logs/log', $user);
        if(!$log_is_writable) {
            $extra_msg[] = '<li>log is not writable by '.$user.'.</li>';
        }
    }
    $extra_msg = implode('', $extra_msg);
    $filter = array();
    $filter['converted'] = isset($_GET['converted']) ? $_GET['converted'] : '';
    $filter['type'] = isset($_GET['type']) ? $_GET['type'] : '';
    $limit = isset($_GET['limit']) ? ' LIMIT ' . $_GET['limit'] : '';
    $offset = isset($_GET['offset']) ? ' OFFSET ' . $_GET['offset'] : '';
    $items = getList($offset, $limit, $config['supportedImgFormats'], $config['supportedVidFormats'], $filter);
?>
<div id="app"></div>
<!-- <script src="<?php echo $meow_url; ?>/static/js/class/WACForm.js" type="module"></script> -->
<script type="module">
    import WACForm from "./../web-assets-converter/static/js/class/WACForm.js"
    const app = document.getElementById('app');
    window.submodule_environment = '<?php echo $environment; ?>';
    const meta = {
        // 'username': 'admin',
        // 'password': 'f3f4p4ax',
        'imgFormats': <?php echo json_encode($config['supportedImgFormats']); ?>,
        'vidFormats': <?php echo json_encode($config['supportedVidFormats']); ?>,
        'environment': '<?php echo $environment; ?>',
        'environment_msg': '<?php echo $extra_msg; ?>'
    }
    const filter = {
        'converted': '<?php echo isset($_GET['converted']) ? $_GET['converted'] : 'all'?>',
        'type': '<?php echo isset($_GET['type']) ? $_GET['type'] : 'all'?>',
    }
    const form = new WACForm(app, '', meta, filter);
</script>
