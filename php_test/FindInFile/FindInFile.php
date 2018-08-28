<?php

require_once(__DIR__.'/Spyc.php');


/*
 * Базовый класс поиска по файлу
 * */
class FindInFile{
	
	var $Settings = [];
	var $File_Stream = NULL;
	var $To_Find = NULL;


	function __construct($settings_file = 'settings.yml'){
		$this->load_settings($settings_file);
	}
	
	/* 
	 * Обработка файла с настройками
	 * Для каждой строки массива должна существовать функция вида check_$key($file_name, $value)
	 * Она должна проверять файл для поиска как ей вздумается
	 * Если такой функции нет, то выводится предупреждение, и данная настройка не обрабатывается в дальнейшем
	 * */
	function load_settings( $file = 'settings.yml' ){
		
		$this->Settings = spyc_load_file( $file );
		
		foreach( $this->Settings as $key => $val ){

			if ( !method_exists( $this , "check_$key" ) ) {
				
				unset( $this->Settings[$key] );
				trigger_error("Настройка $key не будет применена в дальнейшем");
			
			}
		
		}
	}
	
	/*
	 * Открытие файла на чтение
	 * С предварительной проверкой настроек
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
	 * Поиск чего-либо в файле
	 * Можно указать свою функцию (Только если она определена в классе) и аргументы к ней
	 * По умолчанию вызывается find_in_str(0,0);
	 * $to_find Сохраняется во внутреннее поле To_Find
	 * */
	function find_in_file($to_find, $str_func="find_in_str", ...$func_args){
			
		if($this->File_Stream == NULL){
			trigger_error("Файл не был открыт");
			return FALSE;
		}


		if(empty($str_func)){
			$str_func = "find_in_str";
		}
		
		if($str_func == "find_in_str" && empty($func_args)){
			$func_args = [0,0];
		}
		
		if ( !method_exists( $this , $str_func)){
			trigger_error("Функция $str_func не определена в данном классе");
			return false;
		}
		
		$this->To_Find = $to_find;

		return $this->$str_func(...$func_args);
	
	}
	
	/*
	 * Закрытие открытого файла
	 * */
	function close_file(){
		if($this->File_Stream !== NULL){
			fclose($this->File_Stream);
		}
		else{
			trigger_error("Файл не был открыт");
		}
		$this->File_Stream = NULL;
	}
	
	/*
	 * Стандартная функция поиска вхождения в последовательных строках.
	 * Пропускает $offset первых вхождений
	 * Выдаёт не более $max_items вхождений если их столько есть
	 * По умолчанию выдаёт последовательно все вхождения
	 * */
	private function find_in_str($max_items=0, $offset=0){
		if($this->File_Stream == NULL){
			trigger_error("Файл не был открыт");
			return FALSE;
		}	
		$str = fgets($this->File_Stream);
		$str_count = 0;
		$items = 0;
		$founds = [];
		
		while( $str !== FALSE ){
			$pos = strpos($str, $this->To_Find);
			while( $pos !== FALSE){
				$items++;
				if( ( $max_items == 0 OR $items <= $max_items ) AND $items > $offset){
					$founds[] = [$str_count, $pos];
				}
				else{
					break;
				}
				$pos = strpos($str, $this->To_Find, $pos+1);
			}
			if( $items >= $max_items AND $max_items >0){
				break;
			}
			$str_count++;
			$str = fgets($this->File_Stream);
		}

		return $founds;
	}
}
