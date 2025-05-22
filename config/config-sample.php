<?php 
$site_url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
$site_url .= '://' . $_SERVER['SERVER_NAME'];

$supportedImgFormats = array('jpg', 'jpeg', 'png', 'webp', 'gif', 'tiff', 'heif');
$supportedVidFormats = array('mp4', 'webm', 'mov');

$org_dir = __DIR__ . '/../../open-records-generator/';
$org_config_dir = $org_dir . "config/";