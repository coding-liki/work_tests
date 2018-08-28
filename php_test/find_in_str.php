<?php

require_once('FindInFile/FindStrInFile.php');

$settings = [
	'max_size' => 200000,
	'mime_types' => ['text/plain','text/csv', 'text/php']
];


$ff = new FindStrInFile();

$ff->open_file('https://raw.githubusercontent.com/mustangostang/spyc/master/Spyc.php');
$result = $ff->find_in_file("27b0dc1882abf5c9085da7994a246c8b","find_same_md5_hash");
$ff->close_file();
print_r($result);
//$data = spyc_load_file("settings.yml");
//print_r($data);
?>
