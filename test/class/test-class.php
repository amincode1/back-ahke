<?php
class TestClass{
	public $text;
	public function __construct($text){
       $this->text = $text;
	}
	public function re(){
		return $this->text;
	}
}

$TestClass = new TestClass("amin");
echo $TestClass->re();
?>