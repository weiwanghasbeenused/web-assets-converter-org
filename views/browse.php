<div id="main-container">
<?php
require_once(__DIR__ . '/../static/php/functions-webp-manager.php');
$view = isset($uri[2]) && $uri[2] ? $uri[2] : 'overview';
$views = array(
    array(
        'display' => 'Overview',
        'slug' => 'overview',
        'url' => ''
    ),
    array(
        'display' => 'Convert',
        'slug' => 'convert',
        'url' => 'convert'
    ),
    array(
        'display' => 'Upload',
        'slug' => 'upload',
        'url' => 'upload'
    ),
    array(
        'display' => 'Delete',
        'slug' => 'delete',
        'url' => 'delete'
    )
);
?><nav id="main-nav" class="main-padding-wrapper"><?php 
    foreach($views as $v) { 
        $class = $v['slug'] == $view ? 'nav-tab active' : 'nav-tab';
        $id = "nav-tab-" . $v['slug'];
        ?><a id="<?php echo $id; ?>" href="/<?php echo $uri[1]; ?>/<?php echo $v['url']; ?>" class="<?php echo $class; ?>"><?php echo $v['display']; ?></a><?php }?>
</nav>
<main id="<?php echo $uri[2] . '-main-content'; ?>" class="main-content main-padding-wrapper">

<section class="pseudo-select-wrapper main-section"></section>
<?php require_once('wm-' . $view . '.php'); ?>
</main>
<script src="/static/js/Select.js"></script>
<script>
    (function(){
        let wrapper = document.querySelector('.pseudo-select-wrapper');
        let options = [
            {
                'display': 'Rows',
                'value'  : 'rows',
                'callback': function(){
                    console.log('cb of rows');
                    let lists = document.querySelectorAll('[mm-list-view]');
                    for(let i = 0; i < lists.length; i++) {
                        lists[i].setAttribute('mm-list-view', 'rows');
                    }
                    createCookie('list-view', 'rows');
                }
            },
            {
                'display': 'Icons',
                'value'  : 'icons',
                'callback': function(){
                    console.log('cb of icons');
                    let lists = document.querySelectorAll('[mm-list-view]');
                    for(let i = 0; i < lists.length; i++) {
                        lists[i].setAttribute('mm-list-view', 'icons');
                    }
                    createCookie('list-view', 'icons');
                }
            }
        ]
        let dft = readCookie('list-view') ? readCookie('list-view') : 'rows';
        console.log(dft);
        let msgs = {
            'intro': 'View list as:'
        };
        new Select(wrapper, options, '', dft, msgs);
    }());
    // let intro = 'View list as:';
    
</script>
</div>
