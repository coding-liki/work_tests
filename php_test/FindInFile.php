<?php

require_once('Spyc.php');

/*
 * Общий класс поиска по файлу
 * */
class FindInFile{
	
	var $Settings = [];
	var $File_Stream = NULL;
	
	function __construct($settings_file = 'settings.yml'){
		$this->load_settings($settings_file);
	}
	
	/* 
	 * Обработка файла с настройками
	 * Для каждой строки массива должна существовать функция вида check_$key($file_name, $set)
	 * Она должна проверять файл для поиска как ей вздумается
	 * Если такой функции нет, то выводится предупреждение, и данная настройка не обрабатывается в дальнейшем
	 * */
	function load_settings($file = 'settings.yml'){
		$this->Settings = spyc_load_file($file);
		foreach($this->Settings as $key => $val){
			if (!method_exists($this,"check_$key")) {
				unset($this->Settings[$key]);
				trigger_error("Настройка $key не будет применена в дальнейшем");
			}
		}
	}
	
	/*
	 * Открытие файла на чтение
	 * С предворительной проверкой настроек
	 * */
	function open_file($file_name){
		$check_result = 0;
		foreach($this->Settings as $key => $val){
			$method_name = "check_$key";
			$check_result += $this->$method_name($file_name, $val);
		}
		if($check_result > 0){
			trigger_error("Файл ($file_name) не прошёл проверку");
		}
		else{
			$this->File_Stream = fopen($file_name, 'r');
		}
	}

	/*
	 * Закрытие открытого файла
	 * */
	function close_file(){
		if($this->File_Stream !== NULL){
			fclose($this->File_Stream);
		}
		$this->File_Stream = NULL;
	}
}
