<?php

require_once(__DIR__.'/FindInFile.php');

echo __DIR__."\n";

class FindStrInFile extends FindInFile{
	
	function __construct($settings_file="settings.yml"){
		parent::__construct($settings_file);
	}

	/*
	 * Проверка максимального размера из настроек
	 * */
	function check_max_size($file_name, $max_size){

		/*
		 * Обработка url зависит от Content-Length заголовка
		 * Может работать неправильно, так как он не обязательный
		 * */
		if(strpos($file_name,'://') >0){
			$headers = get_headers($file_name, TRUE);
			$filesize = $headers['Content-Length'];
		}
		else{
			$filesize = filesize($file_name);
		}
		
		if($filesize > $max_size){
			trigger_error("Размер файла слишком большой ($filesize > $max_size)");
			return 1;
		}
		return 0;
	}
	
	function find_same_md5_hash(){
		if($this->File_Stream == NULL){
			trigger_error("Файл не был открыт");
			return FALSE;
		}	
		$str = fgets($this->File_Stream);
		$str_count = 0;
		$founds = [];
		
		while( $str !== FALSE ){
			if(md5($str) == $this->To_Find){
				$founds[] = $str_count;
			}
			$str_count++;
			$str = fgets($this->File_Stream);
		}
		return $founds;
	}
}
