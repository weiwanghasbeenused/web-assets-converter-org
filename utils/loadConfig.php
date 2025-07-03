<?php

function loadConfig(){
    $config_path = __DIR__ . '/../config/config.json';
    if(!file_exists($config_path))
        die('config.json is not found');
    $config = file_get_contents($config_path);
    $config = json_decode($config, true);
    $config['media_root'] = __DIR__ . '/../..' . $config['media_root'];
    return $config;
}