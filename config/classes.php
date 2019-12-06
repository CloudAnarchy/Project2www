<?php

class Board{
    public $width;
    public $height;
    public $rows_height;
    public $n_rows;

    function __construct($width, $rows_height, $n_rows){
        $this->width = $width;
        $this->rows_height = $rows_height;
        $this->n_rows = $n_rows;
        $this->height = $rows_height * $n_rows;
    }


    function getWidth(){
        return $this->width;
    }

    function getHeight(){
        return $this->height;
    }

    function getRows_height(){
        return $this->rows_height;
    }

    function getN_rows(){
        return $this->n_rows;
    }
}

class Node{

    public $width;
    public $height;
    public $name;
    public $x;
    public $y;
    public $nets;
    public $isTerminal;


    function __construct($name, $width, $height){
        $this->width = $width;
        $this->height = $height;
        $this->name = $name;
        $this->isTerminal = preg_match('~^p\d+$~', $this->name) ? true : false;
        $this->nets = array();

    }

    function setCordinates($x, $y){
        $this->x = $x;
        $this->y = $y;
    }

    function setNet($net){
        array_push($this->nets, $net);
    }

    function getCordinates(){
        return ['x' => $this->x, 'y' =>  $this->y];
    }

    function getNets(){
        return $this->nets;
    }

    function isTerminal(){
        return $this->isTerminal;
    }

    function getName(){
        return $this->name;
    }

    function getWidth(){
        return $this->width;
    }

    function getHeight(){
        return $this->height;
    }

}




?>