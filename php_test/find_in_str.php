<?php

require_once('FindStrInFile.php');

$settings = [
	'max_size' => 200000,
	'mime_types' => ['text/plain','text/csv', 'text/php']
];


$ff = new FindStrInFile();

$ff->open_file('Spyc.php');
$ff->close_file();
//$data = spyc_load_file("settings.yml");
//print_r($data);
?>
