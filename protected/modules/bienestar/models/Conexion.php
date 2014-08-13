<?php
session_start();
class Conexion{   
var $host='localhost';
var $user='root';
var $pass='120487';
var $db='BD_GESTION_UNG';
 public function conectar() {  
  mysql_connect($this->host,$this->user,$this->pass) or die('Ocurrió un error al intentar conectar');
  mysql_select_db($this->db) or die ('Error al seleccionar la base de datos: '.mysql_error());
 }  
 public function desconectar() {  
  mysql_close();
 }  
}   
?>