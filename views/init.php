<?php
require_once(__DIR__ . '/../utils/db_connect.php');
$db = db_connect();
$sql = "CREATE TABLE `meow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `converted` TINYINT NOT NULL DEFAULT 0,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `ext` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
)";
$result = $db->query($sql);
if ($result === TRUE) {
    echo "Table 'meow' created successfully.<br>";
} else {
    echo "Error creating table 'meow': " . $db->error . '<br>';
}