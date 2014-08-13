<?php
require_once("Conexion.php");

/*ESTRUCTURA PARA EL OBJETO PeriodosAcademicos */
class Personas{
 var $tabla, $alias, $pref, $idpersona, $identificacion, $fechaingreso, $correo, $direccion, $telefono, $tipoidentificacion, $tiporegimen, $pais, $dpto, $municipio;
 var $listadoPersonas;
 var $error;
 
 public function  __construct($idpersona=NULL){
	$args = func_num_args();
	$this->tabla = "TBL_PERSONAS"; $this->alias="p"; $this->pref="PERS"; 
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new Conexion();
		  
          $conexion->conectar(); 
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_ID=$idpersona;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idpersona=$idpersona;
		  $this->identificacion=$result_query[1];
		  $this->fechaingreso=$result_query[2];
		  $this->correo=$result_query[3];
		  $this->direccion=$result_query[4];
		  $this->telefono=$result_query[5];
		  $this->tipoidentificacion=$result_query[6];
		  $this->tiporegimen=$result_query[7];
		  $this->pais=$result_query[8];
		  $this->dpto=$result_query[9];
		  $this->municipio=$result_query[10];
		 }
 }

 public function agregarPersona($idpersona, $identificacion, $fechaingreso, $correo, $direccion, $telefono, $tipoidentificacion, $tiporegimen, $pais, $dpto, $municipio){
  $conexion= new Conexion();  
  $conexion->conectar();
   $string="INSERT INTO ".$this->tabla." () VALUES (NULL, '$identificacion', '$fechaingreso', '$correo', '$direccion', '$telefono', $tipoidentificacion, $tiporegimen, $pais, $dpto, $municipio);";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al insertar los datos ". mysql_errno()." - ".mysql_error(). " ";
   if(mysql_errno()==1062){ $this->error= "Lo sentimos, solo se pueden agregar <strong>dos (2)</strong> periodos academicos en un mismo año.";}
  }else{
		  
		  $this->identificacion=$identificacion;
		  $this->fechaingreso=$fechaingreso;
		  $this->correo=$correo;
		  $this->direccion=$direccion;
		  $this->telefono=$telefono;
		  $this->tipoidentificacion=$tipoidentificacion;
		  $this->tiporegimen=$tiporegimen;
		  $this->pais=$pais;
		  $this->dpto=$dpto;
		  $this->municipio=$municipio;	
       } 
  $conexion->desconectar();
 }
}
?>