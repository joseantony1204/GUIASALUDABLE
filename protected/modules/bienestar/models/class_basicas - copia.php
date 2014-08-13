<?php
require_once("class_conexion.php");
require_once("classnominalogger.php");

/*ESTRUCTURA PARA EL OBJETO PeriodosAcademicos */
class PeriodosAcademicos{
 var $pref0, $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idPeriodo, $nombre, $fechaInicio, $fechaFinal, $estado;
 var $PeriodoAcademicoActual;
 var $error;

 public function  __construct($idPeriodo=NULL){
	$args = func_num_args();
	$this->pref0 = "esta";
	$this->tabla = "tbl_periodos_academicos"; $this->alias="peac"; $this->pref="peac"; 
	$this->tabla2 = "tbl_catedras"; $this->alias2="cate"; $this->pref2="cate";
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar(); 
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id=$idPeriodo;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idPeriodo=$idPeriodo;
		  $this->nombre=$result_query[1];
		  $this->fechaInicio=$result_query[2];
		  $this->fechaFinal=$result_query[3];
		  $this->estado = new Estados($result_query[4]);
		 }
 }
 
 public function agregarPeriodoAcademico($nombre,$fechaInicio,$fechaFinal,$estado){
  $conexion= new conexion();
  
  $conexion->conectar();
   $anoActual = date("Y")."01";
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $anoActual;";
   $query=mysql_query($string);
   
   if((mysql_num_rows($query))==0){
   $idPeriodo = $anoActual;
   }else{
	    $anoActual = date("Y")."02";
		$idPeriodo = $anoActual;
		}
   $string="INSERT INTO ".$this->tabla." () VALUES ($idPeriodo, '$nombre', '$fechaInicio', '$fechaFinal', $estado);";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al insertar los datos ". mysql_errno()." - ".mysql_error(). " ";
   if(mysql_errno()==1062){ $this->error= "Lo sentimos, solo se pueden agregar <strong>dos (2)</strong> periodos academicos en un mismo año.";}
  }else{
	    $this->idPeriodo=$idPeriodo;
		$this->nombre=$nombre;
		$this->fechaInicio=$fechaInicio;
		$this->fechaFinal=$fechaFinal;
		$this->estado=$estado;
		
		/* INICIO cambiar el estado anterios si existe a INHABILITADO */
		$string="SELECT * FROM ".$this->tabla."  ORDER BY(".$this->pref."_id) DESC LIMIT 1 OFFSET 1;";
	    $query=mysql_query($string);
		$result_query = mysql_fetch_array($query);
		if(mysql_num_rows($query)>0){
	    $string="UPDATE ".$this->tabla."  SET ".$this->pref0."_id=1 WHERE ".$this->pref."_id = $result_query[0];";
	    $query=mysql_query($string);
	    }
		/* FIN cambiar el estado anterios si existe a INHABILITADO */		
       } 
  $conexion->desconectar();
 }
 
 public function actualizarPeriodoAcademico($nombre,$fechaInicio,$fechaFinal,$estado){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE ".$this->tabla." SET ".$this->pref."_nombre = '$nombre', ".$this->pref."_fecha_inicio = '$fechaInicio', 
   ".$this->pref."_fecha_final = '$fechaFinal', ".$this->pref0."_id = $estado  WHERE ".$this->pref."_id = $this->idPeriodo;";
   $query=mysql_query($string); 
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idPeriodo=$this->idPeriodo;
		$this->nombre=$nombre;
		$this->fechaInicio=$fechaInicio;
		$this->fechaFinal=$fechaFinal;
		$this->estado=$estado;
       } 
  $conexion->desconectar();
 }
 
 public function eliminarPeriodoAcademico(){
  $conexion= new conexion();
  
  $conexion->conectar();  
   $string="SELECT count(ct.peac_id) AS PERIODO_ACADEMICO FROM tbl_contratos ct WHERE ct.peac_id =".$this->idPeriodo;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." CONTRATO(s)</strong> relacionada(s) con el PERIODO ACADEMICO : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminado.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idPeriodo;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar el PERIODO ACADEMICO : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectPeriodoAcademico(){
  $conexion= new conexion();
  
  $conexion->conectar();
  $conexion->desconectar();
 
 }
 
 public function obtenerPeriodoAcademicoActual(){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla."  WHERE ".$this->pref0."_id=0 ORDER BY(".$this->pref."_id) DESC LIMIT 1;";
   $query=mysql_query($string);
   $totalPer = mysql_num_rows($query);
   if($totalPer==1){
    $this->PeriodoAcademicoActual = mysql_fetch_array($query);
   }elseif($totalPer==0){
	      $this->PeriodoAcademicoActual[1] = "<font color='#FF0000'>¡ Disculpe, para crear una nueva catedra debe 
		  haber y estar activado un periodo academico correspondiente al semestre actual !</font>";
		 }	 
   
  $conexion->desconectar();
 
 } 
 public function cambiarEstadoPeriodoAcademico($idPeriodo, $estado){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla."  WHERE ".$this->pref0."_id = 0 ORDER BY(".$this->pref."_id) DESC LIMIT 1;";
   $query=mysql_query($string);
   $result_query = mysql_fetch_array($query);  
   if(mysql_num_rows($query)>0){
	$string="UPDATE ".$this->tabla."  SET ".$this->pref0."_id = 1 WHERE ".$this->pref."_id = $result_query[0];";
	$query=mysql_query($string);
   }
   $string="UPDATE ".$this->tabla."  SET ".$this->pref0."_id = $estado WHERE ".$this->pref."_id = $idPeriodo;";
   $query=mysql_query($string);
   if (!$query){
   $this->error= "Error al actualizar el periodo academico ". mysql_errno()." - ".mysql_error(). " ";
   }
   $conexion->desconectar();
 }
 
}

/*ESTRUCTURA PARA EL OBJETO Universidades */
class Universidades{
 var $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idUniversidad,$nit, $nombre, $telefono, $direccion;
 var $resultGSUniversidades;
 var $error;
 
 public function  __construct($idUniversidad=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_universidad"; $this->alias="univ"; $this->pref="univ"; 
    $this->tabla2 = "tbl_facultades"; $this->alias2="facu"; $this->pref2="facu";
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar(); 
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idUniversidad;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idUniversidad=$idUniversidad;
		  $this->nit=$result_query[1];
		  $this->nombre=$result_query[2];
		  $this->telefono=$result_query[3];
		  $this->direccion=$result_query[4];
		 }
 }
 
 public function agregarUniversidades($nit, $nombre, $telefono, $direccion){
  $conexion= new conexion();
  
  $conexion->conectar();  
    $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
	GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
    $query=mysql_query($string);
	$max_num = mysql_fetch_array($query);
    $idUniversidad=$max_num['UltimoInsertado'];
    if($max_num!="")
      {	
       $idUniversidad=$idUniversidad + 1;
	  }else{
            $idUniversidad=1; 
           }
   $string="INSERT INTO ".$this->tabla."  () VALUES ($idUniversidad, '$nit', '$nombre', '$telefono', '$direccion');";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al insertar los datos en la tabla : ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idUniversidad=$idUniversidad;
		$this->nit=$nit;
		$this->nombre=$nombre;
		$this->telefono=$telefono;
		$this->direccion=$direccion;
       } 
  $conexion->desconectar();
 }
 
 public function actualizarUniversidades($nit, $nombre, $telefono, $direccion){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE ".$this->tabla."  SET ".$this->pref."_nit = '$nit', ".$this->pref."_nombre = '$nombre', ".$this->pref."_telefono = '$telefono', 
   ".$this->pref."_direccion = '$direccion' WHERE ".$this->pref."_id = $this->idUniversidad;";
   $query=mysql_query($string); 
   
  if (!$query){
   $this->error= "Error al actualizar los datos en la tabla : ".$this->tabla."  ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idUniversidad=$idUniversidad;
		$this->nit=$nit;
		$this->nombre=$nombre;
		$this->telefono=$telefono;
		$this->direccion=$direccion;
       } 
 $conexion->desconectar();
 }
 
 public function eliminarUniversidades(){
  $conexion= new conexion();
  
  $conexion->conectar();  
   $string="SELECT count(".$this->alias2.".".$this->pref."_id) AS UNIVERSIDADES FROM ".$this->tabla2." ".$this->alias2." 
   WHERE ".$this->alias2.".".$this->pref."_id = ".$this->idUniversidad;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." FACULTAD(es)</strong> relacionada(s) con la UNIVERSIDAD : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminada.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idUniversidad;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar el PERIODO ACADEMICO : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectUniversidades(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombre) ASC;";
   $this->resultGSUniversidades = mysql_query($string);
  $conexion->desconectar();
 
 } 
}

/*ESTRUCTURA PARA EL OBJETO Facultades */
class Facultades{
 var $pref0, $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idFacultad, $nombre, $universidad, $seccion;
 var $resultGSFacultades;
 var $error;

 public function  __construct($idFacultad=NULL){
	$args = func_num_args();
	 $this->pref0 = "univ";
	 $this->tabla = "tbl_facultades"; $this->alias = "facu"; $this->pref="facu"; 
	 $this->tabla2 = "tbl_programas"; $this->alias2 = "prog"; $this->pref2="prog";
	if ($args==0){
	  $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar(); 
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idFacultad;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idFacultad=$idFacultad;
		  $this->nombre=$result_query[1];
		  $this->universidad = new Universidades($result_query[2]);
		  $this->seccion = $result_query[3];
		 }
 }
 
 public function agregarFacultades($nombre, $universidad, $seccion){
  $conexion= new conexion();
  
  $conexion->conectar(); 
  $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
   GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
   $query=mysql_query($string);
   $max_num = mysql_fetch_array($query);
   $idFacultad=$max_num['UltimoInsertado'];
   if($max_num!=""){	
    $idFacultad=$idFacultad + 1;
    }else{
          $idFacultad=1; 
         }    
   $string="INSERT INTO ".$this->tabla." () VALUES ($idFacultad, '$nombre', $universidad, '$seccion'); ";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al insertar los datos : ".mysql_errno()." - ".mysql_error();
  }else{
		$this->idFacultad = $idFacultad;
		$this->nombre = $nombre;
		$this->universidad = $universidad;
		$this->seccion = $seccion;
       } 
 $conexion->desconectar();
 }
 
 public function actualizarFacultades($idFacu, $nombre, $universidad, $seccion){
  $conexion= new conexion();
  
  $conexion->conectar();
  $string="UPDATE ".$this->tabla."   SET ".$this->pref."_id = $idFacu, ".$this->pref."_nombre = '$nombre', ".$this->pref0."_id = $universidad, 
   ".$this->pref."_seccion = '$seccion'  WHERE ".$this->pref."_id = ".$this->idFacultad;
   $query=mysql_query($string); 
   
 if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idFacultad=$idFacu;
		$this->nombre=$nombre;
		$this->universidad=$universidad;
		$this->seccion = $seccion;
       } 
 $conexion->desconectar();
 }
 
 public function eliminarFacultades(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT count(".$this->alias2.".".$this->pref."_id) AS FACULTADES FROM ".$this->tabla2." ".$this->alias2." 
   WHERE ".$this->alias2.".".$this->pref."_id = ".$this->idFacultad;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." PROGRAMA</strong>(s) relacionado(s) con la FACULTAD : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminada.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idFacultad;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar la FACULTAD : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectFacultades(){
  $conexion= new conexion();
    
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombre) ASC;";
   $this->resultGSFacultades = mysql_query($string);
  $conexion->desconectar();
 
 } 
}

/*ESTRUCTURA PARA EL OBJETO Programas */
class Programas{
 var $pref0, $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idPrograma, $nombre, $facultad;
 var $resultGSProgramas;
 var $error;

 public function  __construct($idPrograma=NULL){
	$args = func_num_args();
	$this->pref0 = "facu";
	$this->tabla = "tbl_programas"; $this->alias = "prog"; $this->pref="prog"; 
	$this->tabla2 = "tbl_catedras"; $this->alias2 = "cate"; $this->pref2="cate";
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idPrograma;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idPrograma=$idPrograma;
		  $this->nombre=$result_query[1];
		  $this->facultad = new Facultades($result_query[2]);
		 }
 }
 
 public function agregarProgramas($nombre, $facultad){
  $conexion= new conexion();
  
  $conexion->conectar(); 
   $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
   GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
   $query=mysql_query($string);
   $max_num = mysql_fetch_array($query);
   $idPrograma=$max_num['UltimoInsertado'];
   if($max_num!=""){	
    $idPrograma=$idPrograma + 1;
    }else{
          $idPrograma=1; 
         }   
   $string="INSERT INTO ".$this->tabla." () VALUES ($idPrograma, '$nombre', $facultad);";
   $query=mysql_query($string);  
  
  if (!$query){
   $this->error= "Error al insertar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idPrograma=$idPrograma;
		$this->nombre=$nombre;
		$this->facultad=$facultad;
       }
  $conexion->desconectar(); 
 }
 
 public function actualizarProgramas( $nombre, $facultad){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE ".$this->tabla."   SET  ".$this->pref."_nombre = '$nombre', ".$this->pref0."_id = $facultad 
   WHERE ".$this->pref."_id = ".$this->idPrograma;
   $query=mysql_query($string); 

  if (!$query){
   $this->error= "Error al actualizar los datos  ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idPrograma=$this->idPrograma;
		$this->nombre=$nombre;
		$this->facultad=$facultad;
       } 
  $conexion->desconectar();
 }
 
 public function eliminarProgramas(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT count(".$this->alias2.".".$this->pref."_id) AS PROGRAMAS FROM ".$this->tabla2." ".$this->alias2." 
   WHERE ".$this->alias2.".".$this->pref."_id = ".$this->idPrograma;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." CATEDRA</strong>(s) relacionada(s) con el PROGRAMA : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminado.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idPrograma;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar el PROGRAMA : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectProgramas(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombre) ASC;";
   $this->resultGSProgramas = mysql_query($string);
  $conexion->desconectar();
 
 } 
}

/*ESTRUCTURA PARA EL OBJETO Categorias */
class Categorias{
 var $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idCategoria, $nombre, $valor, $valor_letras;
 var $resultGSCategorias;
 var $error;

 public function  __construct($idCategoria=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_categorias"; $this->alias="categ"; $this->pref="categ"; 
	$this->tabla2 = "tbl_docentes"; $this->alias2="doce"; $this->pref2="doce";
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idCategoria;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idCategoria=$idCategoria;
		  $this->nombre=$result_query[1];
		  $this->valor=$result_query[2];
		  $this->valor_letras=$result_query[3];
		 }
 }
 
 public function agregarCategorias($nombre, $valor, $valor_letras){
  $conexion= new conexion();
  
  $conexion->conectar();  
    $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
	GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1 OFFSET 1";
    $query=mysql_query($string);
	$max_num = mysql_fetch_array($query);
    $idCategoria=$max_num['UltimoInsertado'];
    if($max_num!="")
      {	
       $idCategoria=$idCategoria + 1;
	  }else{
            $idCategoria=1; 
           }
   $string="INSERT INTO ".$this->tabla."  () VALUES ($idCategoria, '$nombre', '$valor', '$valor_letras');";
   $query=mysql_query($string);
    
  if (!$query){
   $this->error= "Error al insertar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idCategoria=$idCategoria;
		$this->nombre=$nombre;
		$this->valor=$valor;
		$this->valor_letras=$valor_letras;
       } 
  $conexion->desconectar();
 }
 
 public function actualizarCategorias($nombre,  $valor, $valor_letras){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE ".$this->tabla."  SET ".$this->pref."_nombre = '$nombre', ".$this->pref."_valor = '$valor', 
   ".$this->pref."_valor_letras = '$valor_letras' WHERE ".$this->pref."_id = $this->idCategoria;";
   $query=mysql_query($string); 
  
  if (!$query){
   $this->error= "Error al actualizar los datos  :   ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idCategoria=$this->idCategoria;
		$this->nombre=$nombre;
		$this->valor=$valor;
		$this->valor_letras=$valor_letras;
       } 
 $conexion->desconectar();
 }
 
 public function eliminarCategorias(){
  $conexion= new conexion();
  
  $conexion->conectar();  
   $string="SELECT count(".$this->alias2.".".$this->pref."_id) AS CATEGORIAS FROM ".$this->tabla2." ".$this->alias2." 
   WHERE ".$this->alias2.".".$this->pref."_id = ".$this->idCategoria;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." DOCENTE(s)</strong> relacionado(s) con la CATEGORIA : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminada.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idCategoria;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar la CATEGORIA : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectCategorias(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombre) ASC;";
   $this->resultGSCategorias = mysql_query($string);
  $conexion->desconectar();
 
 } 
}


/*ESTRUCTURA PARA EL OBJETO Pension */
class Pension{
 var  $idpension, $pension;
 var $resultGSPension;
 var $error;

 public function  __construct($idpension=NULL){
	$args = func_num_args();
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		   $string="SELECT * FROM pension  WHERE idpension = '$idpension'";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idpension=$idpension;
		  $this->pension=$result_query[1];
		 }
 }
 
 public function generarSelectPension(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT * FROM pension  ORDER BY (pension) ASC;";
   $this->resultGSPension = mysql_query($string);
  $conexion->desconectar();
 
 } 
}


/*ESTRUCTURA PARA EL OBJETO Seguro */
class Seguro{
 var  $idseguro, $seguro;
 var $resultGSSeguro;
 var $error;

 public function  __construct($idseguro=NULL){
	$args = func_num_args();
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		   $string="SELECT * FROM seguro  WHERE idseguro = '$idseguro'";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idseguro=$idseguro;
		  $this->seguro=$result_query[1];
		 }
 }
 
 

 public function generarSelectSeguro(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT * FROM seguro  ORDER BY (seguro) ASC;";
   $this->resultGSSeguro = mysql_query($string);
  $conexion->desconectar();
 
 } 
}

/*ESTRUCTURA PARA EL OBJETO Materias */
class Materias{
 var $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idMateria, $nombre;
 var $error;

 public function  __construct($idMateria=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_materias"; $this->alias = "mate"; $this->pref="mate"; 
	$this->tabla2 = "tbl_materias_catedras"; $this->alias2 = "maca"; $this->pref2="mate";
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar(); 
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idMateria;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idMateria=$idMateria;
		  $this->nombre=$result_query[1];
		 }
 }
 
 public function agregarMaterias($nombre){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
   GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
   $query=mysql_query($string);
   $max_num = mysql_fetch_array($query);
   $idMateria=$max_num['UltimoInsertado'];
   if($max_num!=""){	
    $idMateria=$idMateria + 1;
    }else{
          $idMateria=1; 
         }   
   $string="INSERT INTO ".$this->tabla."  () VALUES ($idMateria, '$nombre');";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al insertar los datos  ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idMateria=$idMateria;
		$this->nombre=$nombre;
       } 
  $conexion->desconectar();
 }
 
 public function actualizarMaterias($nombre){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE ".$this->tabla."   SET ".$this->pref."_nombre = '$nombre'   WHERE ".$this->pref."_id = ".$this->idMateria;
   $query=mysql_query($string); 
  
   if (!$query){
   $this->error= "Error al actualizar los datos  ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idMateria=$this->idMateria;
		$this->nombre=$nombre;
       } 
  $conexion->desconectar();
 }
 
 public function eliminarMaterias(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT count(".$this->alias2.".".$this->pref."_id) AS MATERIAS FROM ".$this->tabla2." ".$this->alias2." 
   WHERE ".$this->alias2.".".$this->pref."_id = ".$this->idMateria;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." CATEDRA</strong>(s) relacionada(s) con la MATERIA : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminada.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idMateria;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar la MATERIA : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectMaterias(){
  $conexion= new conexion();
  
  $conexion->conectar();
  $conexion->desconectar();
 
 } 
}

/*ESTRUCTURA PARA EL OBJETO Estudios */
class Estudios{
 var $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idEstudio, $nombre;
 var $error;

 public function  __construct($idEstudio=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_estudios"; $this->alias = "est"; $this->pref="estu"; 
	$this->tabla2 = "tbl_docentes_estudios"; $this->alias2 = "does"; $this->pref2="estu"; 
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idEstudio;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idEstudio=$idEstudio;
		  $this->nombre=$result_query[1];
		 }
 }
 
 public function agregarEstudios($nombre){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
   GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
   $query=mysql_query($string);
   $max_num = mysql_fetch_array($query);
   $idEstudio=$max_num['UltimoInsertado'];
   if($max_num!=""){	
    $idEstudio=$idEstudio + 1;
    }else{
          $idEstudio=1; 
         }   
   $string="INSERT INTO ".$this->tabla."  () VALUES ($idEstudio, '$nombre');";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al insertar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idEstudio=$idEstudio;
		$this->nombre=$nombre;
       } 
  $conexion->desconectar();
 }
 
 public function actualizarEstudios($nombre){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE ".$this->tabla."   SET ".$this->pref."_nombre = '$nombre'   WHERE ".$this->pref."_id = ".$this->idEstudio;
   $query=mysql_query($string); 
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idEstudio=$this->idEstudio;
		$this->nombre=$nombre;
       } 
  $conexion->desconectar();
 }
 
 public function eliminarEstudios(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT count(".$this->alias2.".".$this->pref."_id) AS ESTUDIOS FROM ".$this->tabla2." ".$this->alias2." 
   WHERE ".$this->alias2.".".$this->pref."_id = ".$this->idEstudio;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." DOCENTE</strong>(s) relacionado(s) con el ESTUDIO : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminado.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idEstudio;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar el ESTUDIO : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectEstudios(){
  $conexion= new conexion();
  
  $conexion->conectar();
  $conexion->desconectar();
 
 } 
}

/*ESTRUCTURA PARA EL OBJETO EstadosPeriodosAcademicos */
class Estados{
 var $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idEstado, $nombre;
  var $resultGSEstados;
 var $error;

 public function  __construct($idEstado=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_estados"; $this->alias = "esta"; $this->pref="esta"; 
	$this->tabla2 = "tbl_periodos_academicos"; $this->alias2 = "peac"; $this->pref2="peac"; 
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idEstado;";
		   $query=mysql_query($string);
		  //$conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idEstado=$idEstado;
		  $this->nombre=$result_query[1];
		 }
 }
 
 public function generarSelectEstados(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombre) ASC;";
   $this->resultGSEstados = mysql_query($string);
  $conexion->desconectar();
 
 } 
}


/*ESTRUCTURA PARA EL OBJETO Rector */
class Rector{
 var $tabla, $alias, $pref, $identificacion, $nombres, $apellidos, $descripcion, $actual;
 var $error;

 public function  __construct($identificacion=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_rectores"; $this->alias = "rec"; $this->pref="rect"; 
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." 
		   WHERE ".$this->alias.".".$this->pref."_identificacion = $identificacion;";
		   $query=mysql_query($string);
		  //$conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->identificacion=$result_query[0];
		  $this->nombres=$result_query[1];
		  $this->apellidos=$result_query[2];
		  $this->descripcion = $result_query[3];
		  $this->actual=$result_query[4];
		 }
 }
 public function obtenerRectorActual(){
 $conexion= new conexion();
		  
  $conexion->conectar();
  $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_actual = 0;";
  $query=mysql_query($string);
  
  $conexion->desconectar();
  $result_query=mysql_fetch_array($query);
  $this->identificacion=$result_query[0];
  $this->nombres=$result_query[1];
  $this->apellidos=$result_query[2];
  $this->descripcion = $result_query[3];
  $this->actual=$result_query[4];
 }
 
 public function actualizarRector($identificacion, $nombre, $apellido, $descripcion){
 $conexion= new conexion();
		  
  $conexion->conectar();
  $string="UPDATE ".$this->tabla." SET ".$this->pref."_nombres = '$nombre', ".$this->pref."_apellidos = '$apellido', 
   ".$this->pref."_identificacion = $identificacion, ".$this->pref."_descripcion = '$descripcion'   
   WHERE ".$this->pref."_identificacion =".$this->identificacion;
   $query=mysql_query($string); 
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->identificacion=$identificacion;
		$this->nombres=$nombre;
		$this->apellidos=$apellido;
		$this->descripcion = $descripcion;		
       }
  $conexion->desconectar();
 
 }
 
 
}

class Configuraciones{
 var $tabla, $alias, $pref, $idConfig, $descripcion;
 var $error;

 public function  cargarConfiguraciones(){
	$this->tabla = "tbl_configuraciones"; $this->alias = "conf"; $this->pref="conf"; 
	$conexion= new conexion();
	  
    $conexion->conectar();
	$string="SELECT * FROM ".$this->tabla." ".$this->alias." LIMIT 1";
	$query=mysql_query($string);
	$result_query=mysql_fetch_array($query);
	$this->idConfig=$result_query[0];
	$this->descripcion=$result_query[1];
 }
 
  public function  actualizarConfiguraciones($idConfig, $descripcion){
  $this->tabla = "tbl_configuraciones"; $this->alias = "conf"; $this->pref="conf"; 
  $conexion= new conexion();
	  
  $conexion->conectar();
  $string="UPDATE ".$this->tabla." SET ".$this->pref."_descripcion = '$descripcion'  WHERE ".$this->pref."_id =$idConfig";
  $query=mysql_query($string); 
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idConfig=$idConfig;
		$this->descripcion=$descripcion;		
       }
	   $conexion->desconectar();
 }
}

///////////////////////////////////////CLASES PARA LA ADMINISTRACION DEL SISTEMA////////////////////////////////////////////////
class Administrator
{
var $identificacion, $nombre, $telefono, $correo, $ultimoAcceso, $estado;
var $error;
var $tabla, $clase, $metodo, $IdregistroAfectado;

public function  __construct($identificacion=NULL)
 {
  $args = func_num_args();
  //Si no se le pasa argumentos
  if ($args==0)
   {
    $this->error="";
   }else{
	 	 $conexion= new conexion();
         $conexion->conectar();
		 $busqueda=mysql_query("SELECT ad.idadmin, ad.nombres, ad.telefono, ad.correo, ad.ultimoacceso, ad.estado
		 FROM administrador ad WHERE ad.idadmin = '$identificacion'");
		 $lista=mysql_fetch_array($busqueda);
		 $this->identificacion=$identificacion;
		 $this->nombre=$lista[1];
		 $this->telefono=$lista[2];
		 $this->correo=$lista[3];
		 $this->ultimoAcceso=$lista[4];
		 $this->estado=$lista[5];
		 //desconectar();
	    }
 }

 public function SetAdministrator($identificacion, $nombre, $telefono, $correo, $ultimoAcceso, $estado){
   $this->error="";
   
   //interactua con la base de datos
   $conexion= new conexion();
   $conexion->conectar();
   if($_SESSION["Id_Perfil"]!=1){
	$this->error="Lo sentimos, usted no tiene permisos para realizar esta operacion";
	 return ;
   }
   mysql_query("INSERT INTO  administrador VALUES ($identificacion, '$nombre', $telefono, '$correo', '$ultimoAcceso', $estado)");
	
	if (mysql_errno()!=0)
	{
	 $this->error= "Error al insertar los datos ". mysql_errno()." - ".mysql_error(). " ";
	}else{
		  $this->identificacion=$identificacion;
		  $this->nombre=$nombre;
		  $this->telefono=$telefono;
		  $this->correo=$correo;
		  $this->ultimoAcceso=$ultimoAcceso;
		  $this->estado=$estado;
/**
-------Registrar movimientos del uso de este metodo en la db-------
*/
$Nominalogger= new NominaLogger();
$tabla="administrador";
$metodo=1;
$Nominalogger->registrarInserts($tabla,$metodo,$identificacion); 
/**
-------Fin registrar movimientos del uso de este metodo en la db---
*/		  		  
		 }	
		//desconectar();	  
	  
  }
 public function ActualizarAdministrator($nombre, $telefono, $correo, $ultimoAcceso, $estado, $id){
   $this->error="";
   //interactua con la base de datos
   $conexion= new conexion();
   $conexion->conectar();
   if($_SESSION["Id_Perfil"]!=1)
   {
	$this->error="Lo sentimos, usted no tiene permisos para realizar esta operacion";
	 return ;
   }
$camp_ant=mysql_query("SELECT idadmin,nombres,telefono,correo, estado FROM administrador WHERE idadmin=$this->identificacion");  
   mysql_query("UPDATE administrador SET idadmin='$id', nombres='$nombre', telefono=$telefono, correo='$correo', ultimoacceso='$ultimoAcceso', estado=$estado WHERE idadmin=$this->identificacion");
   	if (mysql_errno()!=0)
	{
	 $this->error= "Error al Actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
	}else{
		  $this->identificacion=$identificacion;
		  $this->nombre=$nombre;
		  $this->telefono=$telefono;
		  $this->correo=$correo;
		  $this->ultimoAcceso=$ultimoAcceso;
		  $this->estado=$estado;
/**
-------Registrar movimientos del uso de esta clase y este metodo en la db-------
*/
$Nominalogger= new NominaLogger();
$tabla="administrador";
$metodo=2;
$pk=array("idadmin");
$columnas=array("idadmin","nombres","telefono","correo","estado");
$campos=array($id,$nombre, $telefono, $correo, $estado);
$Nominalogger->registrarUpdates($tabla,$metodo,$pk,$id,$columnas,$campos,$camp_ant); 

/**
-------Fin registrar movimientos del uso de esta clase y este metodo en la db---
*/
		
		}	
		//desconectar();
  
  }
 public function EliminarAdministrator()
  {
  	  
  $this->error="";
  //interactua con la base de datos
  $conexion= new conexion();
  $conexion->conectar();
   if($_SESSION["Id_Perfil"]!=1)
   {
	$this->error="Lo sentimos, usted no tiene permisos para realizar esta operacion";
	 return ;
   }
  mysql_query("DELETE FROM administrador where idadmin='$this->identificacion';");
/**
-------Registrar movimientos del uso de este metodo en la db-------
*/
$Nominalogger= new NominaLogger();
$tabla="administrador";
$metodo=3;
$Nominalogger->registrarDeletes($tabla,$metodo,$this->identificacion); 

/**
-------Fin registrar movimientos del uso de este metodo en la db---
*/  
  if (mysql_errno()!=0)
	{
	 $this->error= "Error al eliminar los datos ". mysql_errno()." - ".mysql_error(). " ";
	}
 } 
 
public function CambiarEstado($estado)
 {
   $conexion= new conexion();
   $conexion->conectar();
    if($_SESSION["Id_Perfil"]!=1)
   {
	$this->error="Lo sentimos, usted no tiene permisos para realizar esta operacion";
	 return ;
   } 
    mysql_query("UPDATE administrador SET estado=$estado WHERE idadmin=$this->identificacion");
   if (mysql_errno()!=0)
	{
	 $this->error= "Error al Actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
	}
  }
  public function VerificarClave($identificacion,$clave,$tipoAdmin)
  {
   $conexion= new conexion();
   $conexion->conectar();
   $claveNew=sha1($clave);
   $sql=mysql_query("SELECT a.idadmin, a.nombres, p.clave, t.idtipoadmin FROM administrador a, permisos p, tipoadmin t, 
   roles r WHERE a.idadmin = p.idadmin AND a.idadmin = r.idadmin AND r.idtipoadmin = t.idtipoadmin
   AND  a.idadmin=$identificacion AND p.clave='$claveNew' AND t.idtipoadmin=$tipoAdmin");
   $resul=mysql_fetch_array($sql);
   $this->existeUsuario=mysql_num_rows($sql);
   $conexion->desconectar();
  }  
}

class TipoAdministrator{
var $id, $nombre;
var $error;
 public function  __construct($id=NULL){
  $args = func_num_args();
  //Si no se le pasa argumentos
  if ($args==0)
  {
   $this->error="";
  }else{
	   $conexion= new conexion();
       $conexion->conectar();
	   $busqueda=mysql_query("SELECT * FROM tipoadmin WHERE idtipoadmin='$id';");
	   $lista=mysql_fetch_array($busqueda);
       $this->id=$id;
	   $this->nombre=$lista[1];
		//desconectar();
	  }
 }
public function GenerarLista($idtipo)
 {
   if($idtipo=="")
     {	 
      $conexion= new conexion();
      $conexion->conectar();
      $consulta=mysql_query("SELECT * FROM tipoadmin");
      $numero=mysql_num_rows($consulta);
      $size=$numero+1;
      echo "<select name='tipoadmin'  size='$size' id='tipoadmin'>";
       while($registro=mysql_fetch_array($consulta))
	    {
	     echo "<option value='".$registro[0]."' selected='selected'>".$registro[1]."</option> "; 
	    }
	     echo "</select>";
     }else{
		   $conexion= new conexion();
           $conexion->conectar();
	       $consulta=mysql_query("SELECT * FROM tipoadmin"); 
		   $numero=mysql_num_rows($consulta);
		   $size=$numero+1;
           echo "<select name='tipoadmin'  size='$size' id='tipoadmin'>";
            while($registro=mysql_fetch_array($consulta))
	         {
	          echo "<option value='".$registro[0]."'";if (!(strcmp($registro[0], $idtipo))) {echo "SELECTED";} echo">".$registro[1]."</option> "; 
	         }
	         echo "</select>";
		  }
 }
}
class Permisos{
var $idadmin, $usuario, $clave;
var $error;
 public function  __construct($id=NULL)
 {
  $args = func_num_args();
  //Si no se le pasa argumentos
  if ($args==0)
  {
   $this->error="";
  }else{
	   $conexion= new conexion();
       $conexion->conectar();
	   $busqueda=mysql_query("SELECT * FROM permisos WHERE idadmin='$id';");
	   $lista=mysql_fetch_array($busqueda);
       $this->idadmin = $id;
	   $this->usuario = $lista[1];
	   $this->clave = $lista[2];
		//desconectar();
      }
 }
 public function SetPermisos($usuario, $clave){
   $this->error="";
   //interactua con la base de datos
   $conexion= new conexion();
   $conexion->conectar();
   if($_SESSION["Id_Perfil"]!=1){
	$this->error="Lo sentimos, usted no tiene permisos para realizar esta operacion";
	 return ;
   }
   mysql_query("INSERT INTO  permisos () VALUES ($this->idadmin, '$usuario', '$clave')");
	if (mysql_errno()!=0){
	 $this->error= "Error al insertar los datos ". mysql_errno()." - ".mysql_error(). " ";
	}else{
		  $this->usuario=$usuario;
		  $this->clave=$clave;
/**
-------Registrar movimientos del uso de este metodo en la db-------
*/
$Nominalogger= new NominaLogger();
$tabla="permisos";
$metodo=1;
$Nominalogger->registrarInserts($tabla,$metodo,$this->idadmin); 
/**
-------Fin registrar movimientos del uso de este metodo en la db---
*/		  	  
		 }	
 }

 public function ActualizarPermisos($usuario, $clave)
  {
   $this->error="";
   //interactua con la base de datos
   $conexion= new conexion();
   $conexion->conectar();
   if($_SESSION["Id_Perfil"]!=1)
   {
	$this->error="Lo sentimos, usted no tiene permisos para realizar esta operacion";
	 return ;
   }  
   $camp_ant=mysql_query("SELECT usuario, clave FROM permisos WHERE idadmin=$this->idadmin");
   mysql_query("UPDATE permisos SET usuario='$usuario', clave='$clave' WHERE idadmin=$this->idadmin");
   	if (mysql_errno()!=0)
	{
	 $this->error= "Error al Actualizars los datos ". mysql_errno()." - ".mysql_error(). " ";
	}else{
		  $this->usuario=$usuario;
		  $this->clave=$clave;

/**
-------Registrar movimientos del uso de esta clase y este metodo en la db-------
*/
$Nominalogger= new NominaLogger();
$tabla="permisos";
$metodo=2;
//pasar el campo key primary en la primera posicion de array
$pk=array("idadmin");
$columnas=array("usuario","clave");
$campos=array($usuario,$clave);
$Nominalogger->registrarUpdates($tabla,$metodo,$pk,$this->idadmin,$columnas,$campos,$camp_ant); 

/**
-------Fin registrar movimientos del uso de esta clase y este metodo en la db---
*/		  		  
		 }	
  } 
}


class Roles{
var $idrol, $rol, $tipoAdmin, $identificacion;
var $error;

public function  __construct($identificacion=NULL){
  $args = func_num_args();
  //Si no se le pasa argumentos
  if ($args==0){
    $this->error="";
   }else{
	 	 $conexion= new conexion();
         $conexion->conectar();
		 $busqueda=mysql_query("SELECT * FROM roles r WHERE r.idadmin = '$identificacion'");
		 $lista=mysql_fetch_array($busqueda);
		 $this->idrol = $lista[0];
		 $this->rol = $lista[1];
		 $this->tipoAdmin = $lista[2];
		 $this->identificacion = $identificacion;		 
	    }
 }

 public function setRol($rol, $identificacion, $tipoAdmin){
   $this->error="";

   $conexion= new conexion();
   $conexion->conectar();
   $string="SELECT max(idrol) As UltimoInsertado FROM roles  GROUP BY (idrol) DESC LIMIT 1";
   $query=mysql_query($string);
   $max_num = mysql_fetch_array($query);
   $idrol=$max_num['UltimoInsertado'];
   if($max_num!=""){	
    $idrol=$idrol + 1;
    }else{
          $idrol=1; 
         } 
   
   if($_SESSION["Id_Perfil"]!=1){
	$this->error="Lo sentimos, usted no tiene permisos para realizar esta operacion";
	 return ;
   }
   mysql_query("INSERT INTO  roles VALUES ($idrol, $rol, $tipoAdmin, $identificacion )");
	
	if (mysql_errno()!=0){
	 $this->error= "Error al insertar los datos ". mysql_errno()." - ".mysql_error(). " ";
	}else{
		  $this->idrol = $idrol;
		  $this->rol = $rol;
		  $this->tipoAdmin = $tipoAdmin;
		  $this->identificacion = $identificacion;
		  
	     }
  }
  
 public function actualizarRol($rol, $tipoAdmin, $identificacion){
   $this->error="";
   //interactua con la base de datos
   $conexion= new conexion();
   $conexion->conectar();
   if($_SESSION["Id_Perfil"]!=1)
   {
	$this->error="Lo sentimos, usted no tiene permisos para realizar esta operacion";
	 return ;
   }
 mysql_query("UPDATE rol SET rol='$rol', idtipoadmin = $tipoAdmin, idamin = $identificacion WHERE idadmin = $this->identificacion");
   	if (mysql_errno()!=0){
	 $this->error= "Error al Actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
	}else{
          $this->idrol = $idrol;
		  $this->rol = $rol;
		  $this->tipoAdmin = $tipoAdmin;
		  $this->identificacion = $identificacion;
		}	  
  }
}
/////////////////////////FIN DE LAS CLASES PARA LA ADMINISTRACION DEL SISTEMA////////////////////////////////////////////////



class Menulista{
 public function generarMenuLista(){
  echo'
  <form id="form1" name="form1" method="post" action="">
    <label>Mostrar núm.</label>&nbsp;
	<div>
    <select name="registrospag" id="registrospag" onchange="submit();">
	
	 	 
	 <option  value="15"'; if (!(strcmp(15, $_SESSION["registros"]))) {echo "SELECTED";} echo'>15</option>
	 
	 <option value="20"'; if (!(strcmp(20, $_SESSION["registros"]))) {echo "SELECTED";} echo'>20</option>
	 
	 <option value="25"'; if (!(strcmp(25, $_SESSION["registros"]))) {echo "SELECTED";} echo'>25</option>
	 
	 <option value="35"'; if (!(strcmp(35, $_SESSION["registros"]))) {echo "SELECTED";} echo'>35</option>
	 
	 <option value="50"'; if (!(strcmp(50, $_SESSION["registros"]))) {echo "SELECTED";} echo'>50</option>
	 
	 <option value="100"'; if (!(strcmp(100, $_SESSION["registros"]))){echo "SELECTED";} echo'>100</option>
	 
	 <option value="200"'; if (!(strcmp(200, $_SESSION["registros"]))){echo "SELECTED";} echo'>200</option>
    </select>
   </div>	
  </form>';
 }
}


/*ESTRUCTURA PARA EL OBJETO PresupuestosCatedraticos */
class PresupuestosCatedras{
 var $tabla, $alias, $pref, $idPresupuesto, $NumCertificado, $facultad, $fechaV, $fechaIngreso;
 var $resultGSPresupuestos, $resultGSPresupuestosFacultades;
 var $error;

 public function  __construct($idPresupuesto=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_catedras_presupuestos"; $this->alias = "pres"; $this->pref="pres"; 
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  $conexion->conectar();
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idPresupuesto;";
		   $query=mysql_query($string);
		   $conexion->desconectar();		   
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idPresupuesto=$idPresupuesto;
		  $this->NumCertificado=$result_query[1];
		  $this->facultad = new Facultades($result_query[2]);
		  $this->fechaV=$result_query[3];
		  $this->fechaIngreso=$result_query[4];		   
		 }
 }
  
public function agregarPresupuestos($certificado, $facultad, $fechavigencia){
  $conexion= new conexion();
  
  $conexion->conectar(); 
  $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
   GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
   $query=mysql_query($string);
   $max_num = mysql_fetch_array($query);
   $idPresupuesto=$max_num['UltimoInsertado'];
   if($max_num!=""){	
    $idPresupuesto=$idPresupuesto + 1;
    }else{
          $idPresupuesto=1; 
         }   
  $fechaIngreso = date("Y-m-d");
  $string="INSERT INTO tbl_catedras_presupuestos () VALUES ($idPresupuesto, '$certificado', $facultad, 
  '$fechavigencia', '$fechaIngreso');";
   $query=mysql_query($string);	
  
  if (!$query){
   $this->error= "Error al insertar los datos, Detalles: ". mysql_errno()." - ".mysql_error(). " ";
  }else{
 	    $this->idPresupuesto=$idPresupuesto;
		$this->NumCertificado=$certificado;
		$this->facultad=$facultad;
		$this->fechaV=$fechavigencia;	    		
       } 	  
  $conexion->desconectar();
 }  
 
  public function actualizarPresupuestos($certificado, $facultad, $fechavigencia){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE tbl_catedras_presupuestos  SET pres_ncertificado='$certificado', facu_id=$facultad, 
   pres_fecha_vigencia='$fechavigencia' WHERE pres_id = ".$this->idPresupuesto;
   $query=mysql_query($string); 
   
  if (!$query){
   $this->error= "Error al actualizar los datos, Detalles: ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	     $this->idPresupuesto=$this->idPresupuesto;
		 $this->NumCertificado=$certificado;
		 $this->facultad=$facultad;
		 $this->fechaV=$fechavigencia;
		
       } 
 $conexion->desconectar();
 }
 
 public function eliminarPresupuestos(){
  $conexion= new conexion();
  
  $conexion->conectar();  
  echo $string="select count(*) from tbl_catedras where pres_id = ".$this->idPresupuesto;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." CATEDRAS 
	 </strong>asociados a este la Presupuesto: <strong>".$this->nombre."</strong> por lo tanto no puede ser eliminado.";
   }else{
		 $string="DELETE FROM tbl_catedras_presupuestos WHERE pres_id=".$this->idPresupuesto;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar la Presupuesto: <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 }
 
 public function generarSelectPresupuestos(){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_id) ASC;";
   $this->resultGSPresupuestos = mysql_query($string);
   $conexion->desconectar();
 
 }
 
  public function generarSelectPresupuestosFacultades($idFacultad){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT pst.pres_id, pst.pres_ncertificado, f.facu_nombre, pst.pres_fecha_vigencia 
   FROM tbl_catedras_presupuestos pst, tbl_facultades f WHERE f.facu_id = pst.facu_id 
   AND f.facu_id = $idFacultad ORDER BY (pst.pres_fecha_entrada) DESC LIMIT 2";
   $this->resultGSPresupuestosFacultades = mysql_query($string);
   $conexion->desconectar();
 
 }  
 
}

/*ESTRUCTURA PARA EL OBJETO ContratantesCatedras */
class ContratantesCatedras{
 var $tabla, $alias, $pref, $identificacion, $nombres, $apellidos, $descripcion, $estado;
 var $error, $resultGSContratantes, $ContratanteActual;

 public function  __construct($identificacion=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_catedras_contratantes"; $this->alias = "contr"; $this->pref="contr"; 
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		  $string="SELECT * FROM ".$this->tabla." ".$this->alias." 
		   WHERE ".$this->alias.".".$this->pref."_identificacion = $identificacion;";
		   $query=mysql_query($string);
		  //$conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->identificacion=$result_query[0];
		  $this->nombres=$result_query[1];
		  $this->apellidos=$result_query[2];
		  $this->descripcion=$result_query[3];
		  $this->estado=$result_query[4];
		 }
 }
 
 public function agregarContratantes($identificacion, $nombre, $apellido, $descripcion, $estado){
 $conexion= new conexion();
		  
  $conexion->conectar();
  $string="INSERT INTO ".$this->tabla." () VALUES ($identificacion, '$nombre', '$apellido', '$descripcion', $estado); ";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->identificacion=$identificacion;
		$this->nombres=$nombre;
		$this->apellidos=$apellido;
		$this->descripcion= $descripcion;
		$this->estado=$estado;
		
		/* INICIO cambiar el estado anterios si existe a INHABILITADO */
		$string="SELECT * FROM ".$this->tabla."  ORDER BY(".$this->pref."_identificacion) DESC LIMIT 1 OFFSET 1;";
	    $query=mysql_query($string);
		$result_query = mysql_fetch_array($query);
		if(mysql_num_rows($query)>0){
	    $string="UPDATE ".$this->tabla."  SET ".$this->pref."_estado = 1 WHERE ".$this->pref."_identificacion = $result_query[0];";
	    $query=mysql_query($string);
	    }
		 /* FIN cambiar el estado anterios si existe a INHABILITADO */
				
       }
  $conexion->desconectar();
 
 } 


 public function actualizarContratantes($identificacion, $nombre, $apellido, $descripcion, $estado){
 $conexion= new conexion();
		  
  $conexion->conectar();
  $string="UPDATE ".$this->tabla." SET ".$this->pref."_nombres = '$nombre', ".$this->pref."_apellidos = '$apellido', 
   ".$this->pref."_identificacion = $identificacion, ".$this->pref."_descripcion = '$descripcion', ".$this->pref."_estado = $estado
    WHERE ".$this->pref."_identificacion =".$this->identificacion;
   $query=mysql_query($string); 
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->identificacion=$identificacion;
		$this->nombres=$nombre;
		$this->apellidos=$apellido;
		$this->descripcion= $descripcion;
		$this->estado=$estado;			
       }
  $conexion->desconectar();
 
 }
 
 public function eliminarContratantes(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT COUNT(c.contr_identificacion) AS RECTOR FROM tbl_contratos c 
   WHERE c.contr_identificacion = ".$this->identificacion;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." CONTRATO(s) </strong>(s) relacionado(s) 
	 con este CONTRATANTE : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminado.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_identificacion = ".$this->identificacion;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar la CONTRATANTE : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 }
   
 public function generarSelectContratantes(){
  $conexion= new conexion();
    
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombres) ASC;";
   $this->resultGSContratantes = mysql_query($string);
  $conexion->desconectar();
 
 }
 public function obtenerContratanteActual(){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla."  WHERE ".$this->pref."_estado=0 ORDER BY(".$this->pref."_identificacion) DESC LIMIT 1;";
   $query=mysql_query($string);
   $totalPer = mysql_num_rows($query);
   if($totalPer==1){
    $this->ContratanteActual = mysql_fetch_array($query);
   }elseif($totalPer==0){
	      $this->ContratanteActual[1] = "<font color='#FF0000'>¡ Disculpe, para crear una nueva contrato debe 
		  haber y estar activo un contratante !</font>";
		 }	 
   
  $conexion->desconectar();
 
 } 
 public function cambiarEstadoContratante($identificacion, $estado){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla."  WHERE ".$this->pref."_estado = 0 ORDER BY(".$this->pref."_identificacion) DESC LIMIT 1;";
   $query=mysql_query($string);
   $result_query = mysql_fetch_array($query);  
   if(mysql_num_rows($query)>0){
	$string="UPDATE ".$this->tabla."  SET ".$this->pref."_estado = 1 WHERE ".$this->pref."_identificacion = $result_query[0];";
	$query=mysql_query($string);
   }
   $string="UPDATE ".$this->tabla."  SET ".$this->pref."_estado = $estado WHERE ".$this->pref."_identificacion = $identificacion;";
   $query=mysql_query($string);
   if (!$query){
   $this->error= "Error al actualizar el contratante ". mysql_errno()." - ".mysql_error(). " ";
   }
   $conexion->desconectar();
 }   
}






////////////////////////////////////////////////////CLASES BASICAS PARA OCACIONALES//////////////////////////////////////////

/*ESTRUCTURA PARA EL OBJETO PeriodosAcademicos */
class PeriodosAcademicosOcacionales{
 var $pref0, $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idPeriodo, $nombre, $fechaInicio, $fechaFinal, $estado;
 var $PeriodoAcademicoActual;
 var $error;

 public function  __construct($idPeriodo=NULL){
	$args = func_num_args();
	$this->pref0 = "esta";
	$this->tabla = "tbl_ocacionales_periodos_academicos"; $this->alias="peac"; $this->pref="peac"; 
	$this->tabla2 = "tbl_docentes_ocacionales"; $this->alias2="dooc"; $this->pref2="dooc";
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar(); 
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id=$idPeriodo;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idPeriodo=$idPeriodo;
		  $this->nombre=$result_query[1];
		  $this->fechaInicio=$result_query[2];
		  $this->fechaFinal=$result_query[3];
		  $this->estado = new EstadosOcacionales($result_query[4]);
		 }
 }
 
 public function agregarPeriodoAcademico($nombre,$fechaInicio,$fechaFinal,$estado){
  $conexion= new conexion();
  
  $conexion->conectar();
   $anoActual = date("Y")."01";
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $anoActual;";
   $query=mysql_query($string);
   
   if((mysql_num_rows($query))==0){
   $idPeriodo = $anoActual;
   }else{
	    $anoActual = date("Y")."02";
		$idPeriodo = $anoActual;
		}
   $string="INSERT INTO ".$this->tabla." () VALUES ($idPeriodo, '$nombre', '$fechaInicio', '$fechaFinal', $estado);";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al insertar los datos ". mysql_errno()." - ".mysql_error(). " ";
   if(mysql_errno()==1062){ $this->error= "Lo sentimos, solo se pueden agregar <strong>dos (2)</strong> periodos academicos en un mismo año.";}
  }else{
	    $this->idPeriodo=$idPeriodo;
		$this->nombre=$nombre;
		$this->fechaInicio=$fechaInicio;
		$this->fechaFinal=$fechaFinal;
		$this->estado=$estado;
		
		/* INICIO cambiar el estado anterios si existe a INHABILITADO */
		$string="SELECT * FROM ".$this->tabla."  ORDER BY(".$this->pref."_id) DESC LIMIT 1 OFFSET 1;";
	    $query=mysql_query($string);
		$result_query = mysql_fetch_array($query);
		if(mysql_num_rows($query)>0){
	    $string="UPDATE ".$this->tabla."  SET ".$this->pref0."_id=1 WHERE ".$this->pref."_id = $result_query[0];";
	    $query=mysql_query($string);
	    }
		/* FIN cambiar el estado anterios si existe a INHABILITADO */		
       } 
  $conexion->desconectar();
 }
 
 public function actualizarPeriodoAcademico($nombre,$fechaInicio,$fechaFinal,$estado){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE ".$this->tabla." SET ".$this->pref."_nombre = '$nombre', ".$this->pref."_fecha_inicio = '$fechaInicio', 
   ".$this->pref."_fecha_final = '$fechaFinal', ".$this->pref0."_id = $estado  WHERE ".$this->pref."_id = $this->idPeriodo;";
   $query=mysql_query($string); 
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idPeriodo=$this->idPeriodo;
		$this->nombre=$nombre;
		$this->fechaInicio=$fechaInicio;
		$this->fechaFinal=$fechaFinal;
		$this->estado=$estado;
       } 
  $conexion->desconectar();
 }
 
 public function eliminarPeriodoAcademico(){
  $conexion= new conexion();
  
  $conexion->conectar();  
   $string="SELECT count(dooc.peac_id) AS PERIODO_ACADEMICO FROM tbl_docentes_ocacionales dooc WHERE dooc.peac_id =".$this->idPeriodo;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." DOCENTE(s) OCACIONAL(es)</strong> relacionado(s) con el PERIODO ACADEMICO : <strong>".$this->nombre."</strong> y no puede ser eliminado.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idPeriodo;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar el PERIODO ACADEMICO : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectPeriodoAcademico(){
  $conexion= new conexion();
  
  $conexion->conectar();
  $conexion->desconectar();
 
 }
 
 public function obtenerPeriodoAcademicoActual(){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla."  WHERE ".$this->pref0."_id=0 ORDER BY(".$this->pref."_id) DESC LIMIT 1;";
   $query=mysql_query($string);
   $totalPer = mysql_num_rows($query);
   if($totalPer==1){
    $this->PeriodoAcademicoActual = mysql_fetch_array($query);
   }elseif($totalPer==0){
	      $this->PeriodoAcademicoActual[1] = "<font color='#FF0000'>¡ Disculpe, para crear una nueva catedra debe 
		  haber y estar activado un periodo academico correspondiente al semestre actual !</font>";
		 }	 
   
  $conexion->desconectar();
 
 }
 
 public function cambiarEstadoPeriodoAcademico($idPeriodo, $estado){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla."  WHERE ".$this->pref0."_id = 0 ORDER BY(".$this->pref."_id) DESC LIMIT 1;";
   $query=mysql_query($string);
   $result_query = mysql_fetch_array($query);  
   if(mysql_num_rows($query)>0){
	$string="UPDATE ".$this->tabla."  SET ".$this->pref0."_id = 1 WHERE ".$this->pref."_id = $result_query[0];";
	$query=mysql_query($string);
   }
   $string="UPDATE ".$this->tabla."  SET ".$this->pref0."_id = $estado WHERE ".$this->pref."_id = $idPeriodo;";
   $query=mysql_query($string);
   if (!$query){
   $this->error= "Error al actualizar el periodo academico ". mysql_errno()." - ".mysql_error(). " ";
   }
   $conexion->desconectar();
 }
 
}


/*ESTRUCTURA PARA EL OBJETO EstadosPeriodosAcademicos */
class EstadosOcacionales{
 var $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idEstado, $nombre;
  var $resultGSEstados;
 var $error;

 public function  __construct($idEstado=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_ocacionales_estados"; $this->alias = "esta"; $this->pref="esta"; 
	$this->tabla2 = "tbl_ocacionales_periodos_academicos"; $this->alias2 = "peac"; $this->pref2="peac"; 
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idEstado;";
		   $query=mysql_query($string);
		  //$conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idEstado=$idEstado;
		  $this->nombre=$result_query[1];
		 }
 }
 
 public function generarSelectEstados(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombre) ASC;";
   $this->resultGSEstados = mysql_query($string);
  $conexion->desconectar();
 
 } 
}



/*ESTRUCTURA PARA EL OBJETO Facultades */
class FacultadesOcacionales{
 var $pref0, $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idFacultad, $nombre, $seccion;
 var $resultGSFacultades;
 var $error;

 public function  __construct($idFacultad=NULL){
	$args = func_num_args();
	 $this->tabla = "tbl_ocacionales_facultades"; $this->alias = "faoc"; $this->pref="faoc"; 
	 $this->tabla2 = "tbl_docentes_ocacionales"; $this->alias2 = "dooc"; $this->pref2="dooc";
	if ($args==0){
	  $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar(); 
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idFacultad;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idFacultad=$idFacultad;
		  $this->nombre=$result_query[1];
		  $this->seccion = $result_query[2];
		 }
 }
 
 public function agregarFacultades($nombre, $seccion){
  $conexion= new conexion();
  
  $conexion->conectar(); 
  $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
   GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
   $query=mysql_query($string);
   $max_num = mysql_fetch_array($query);
   $idFacultad=$max_num['UltimoInsertado'];
   if($max_num!=""){	
    $idFacultad=$idFacultad + 1;
    }else{
          $idFacultad=1; 
         }    
   $string="INSERT INTO ".$this->tabla." () VALUES ($idFacultad, '$nombre', '$seccion'); ";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al insertar los datos : ".mysql_errno()." - ".mysql_error();
  }else{
		$this->idFacultad = $idFacultad;
		$this->nombre = $nombre;
		$this->seccion = $seccion;
       } 
 $conexion->desconectar();
 }
 
 public function actualizarFacultades($idFacu, $nombre, $seccion){
  $conexion= new conexion();
  
  $conexion->conectar();
  $string="UPDATE ".$this->tabla."   SET ".$this->pref."_id = $idFacu, ".$this->pref."_nombre = '$nombre', 
  ".$this->pref."_seccion = '$seccion'  WHERE ".$this->pref."_id = ".$this->idFacultad;
   $query=mysql_query($string); 
   
 if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idFacultad=$idFacu;
		$this->nombre=$nombre;
		$this->seccion = $seccion;
       } 
 $conexion->desconectar();
 }
 
 public function eliminarFacultades(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT count(".$this->alias2.".".$this->pref."_id) AS FACULTADES FROM ".$this->tabla2." ".$this->alias2." 
   WHERE ".$this->alias2.".".$this->pref."_id = ".$this->idFacultad;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." DOCENTE(s) OCACIONAL(es)</strong>(s) relacionado(s) 
	 con la FACULTAD : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminada.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idFacultad;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar la FACULTAD : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectFacultades(){
  $conexion= new conexion();
    
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombre) ASC;";
   $this->resultGSFacultades = mysql_query($string);
  $conexion->desconectar();
 
 } 
}

class Puntos{
 var $tabla, $alias, $pref, $idPunto, $valor;
 var $error;

 public function  cargarPuntos(){
	$this->tabla = "tbl_ocacionales_valor_puntos"; 
	$conexion= new conexion();
	  
    $conexion->conectar();
	$string="SELECT * FROM ".$this->tabla." LIMIT 1";
	$query=mysql_query($string);
	$result_query=mysql_fetch_array($query);
	$this->idPunto=$result_query[0];
	$this->valor=$result_query[1];
 }
 
  public function  actualizarPuntos($idPunto, $valor){
  $conexion= new conexion();
	  
  $conexion->conectar();
  $string="UPDATE ".$this->tabla." SET valor_punto = '$valor' WHERE id_punto =$idPunto";
  $query=mysql_query($string); 
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
		$this->idPunto = $idPunto;
		$this->valor = $valor;		
       }
	   $conexion->desconectar();
 }
}

/*ESTRUCTURA PARA EL OBJETO Rector */
class RectorOcacionales{
 var $tabla, $alias, $pref, $identificacion, $nombres, $apellidos, $descripcion;
 var $error, $resultGSRectores;

 public function  __construct($identificacion=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_ocacionales_contratantes"; $this->alias = "rec"; $this->pref="rect"; 
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." 
		   WHERE ".$this->alias.".".$this->pref."_identificacion = $identificacion;";
		   $query=mysql_query($string);
		  //$conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->identificacion=$result_query[0];
		  $this->nombres=$result_query[1];
		  $this->apellidos=$result_query[2];
		  $this->descripcion=$result_query[3];
		 }
 }
 
 public function agregarRector($identificacion, $nombre, $apellido, $descripcion){
 $conexion= new conexion();
		  
  $conexion->conectar();
  $string="INSERT INTO ".$this->tabla." () VALUES ($identificacion, '$nombre', '$apellido', '$descripcion'); ";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->identificacion=$identificacion;
		$this->nombres=$nombre;
		$this->apellidos=$apellido;
		$this->descripcion= $descripcion;		
       }
  $conexion->desconectar();
 
 } 


 public function actualizarRector($identificacion, $nombre, $apellido, $descripcion){
 $conexion= new conexion();
		  
  $conexion->conectar();
  $string="UPDATE ".$this->tabla." SET ".$this->pref."_nombres = '$nombre', ".$this->pref."_apellidos = '$apellido', 
   ".$this->pref."_identificacion = $identificacion, ".$this->pref."_descripcion = '$descripcion'   
   WHERE ".$this->pref."_identificacion =".$this->identificacion;
   $query=mysql_query($string); 
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->identificacion=$identificacion;
		$this->nombres=$nombre;
		$this->apellidos=$apellido;
		$this->descripcion= $descripcion;		
       }
  $conexion->desconectar();
 
 }
 
 public function eliminarRector(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT COUNT(c.rect_identificacion) AS RECTOR FROM tbl_ocacionales_contratos c 
   WHERE c.rect_identificacion = ".$this->identificacion;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." CONTRATO(s) </strong>(s) relacionado(s) 
	 con este CONTRATANTE : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminado.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_identificacion = ".$this->identificacion;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar la CONTRATANTE : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 }
   
 public function generarSelectRector(){
  $conexion= new conexion();
    
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombres) ASC;";
   $this->resultGSRectores = mysql_query($string);
  $conexion->desconectar();
 
 }   
   
}

/*ESTRUCTURA PARA EL OBJETO PresupuestosOcacionales */
class PresupuestosOcacionales{
 var $tabla, $alias, $pref, $idPresupuesto, $NumCertificado, $facultad, $fechaV, $fechaIngreso;
 var $resultGSPresupuestos, $resultGSPresupuestosFacultades;
 var $error;

 public function  __construct($idPresupuesto=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_ocacionales_presupuestos"; $this->alias = "pres"; $this->pref="pres"; 
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  $conexion->conectar();
		 $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idPresupuesto;";
		   $query=mysql_query($string);
		   $conexion->desconectar();		   
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idPresupuesto=$idPresupuesto;
		  $this->NumCertificado=$result_query[1];
		  $this->facultad = new FacultadesOcacionales($result_query[2]);
		  $this->fechaV=$result_query[3];
		  $this->fechaIngreso=$result_query[4];		   
		 }
 }
  
public function agregarPresupuestos($certificado, $facultad, $fechavigencia){
  $conexion= new conexion();
  
  $conexion->conectar(); 
  $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
   GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
   $query=mysql_query($string);
   $max_num = mysql_fetch_array($query);
   $idPresupuesto=$max_num['UltimoInsertado'];
   if($max_num!=""){	
    $idPresupuesto=$idPresupuesto + 1;
    }else{
          $idPresupuesto=1; 
         }   
  $fechaIngreso = date("Y-m-d");
  $string="INSERT INTO tbl_ocacionales_presupuestos () VALUES ($idPresupuesto, '$certificado', $facultad, 
  '$fechavigencia', '$fechaIngreso');";
   $query=mysql_query($string);	
  
  if (!$query){
   $this->error= "Error al insertar los datos, Detalles: ". mysql_errno()." - ".mysql_error(). " ";
  }else{
 	    $this->idPresupuesto=$idPresupuesto;
		$this->NumCertificado=$certificado;
		$this->facultad=$facultad;
		$this->fechaV=$fechavigencia;	    		
       } 	  
  $conexion->desconectar();
 }  
 
  public function actualizarPresupuestos($certificado, $facultad, $fechavigencia){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE tbl_ocacionales_presupuestos  SET pres_ncertificado='$certificado', faoc_id=$facultad, 
   pres_fecha_vigencia='$fechavigencia' WHERE pres_id = ".$this->idPresupuesto;
   $query=mysql_query($string); 
   
  if (!$query){
   $this->error= "Error al actualizar los datos, Detalles: ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	     $this->idPresupuesto=$this->idPresupuesto;
		 $this->NumCertificado=$certificado;
		 $this->facultad=$facultad;
		 $this->fechaV=$fechavigencia;
		
       } 
 $conexion->desconectar();
 }
 
 public function eliminarPresupuestos(){
  $conexion= new conexion();
  
  $conexion->conectar();  
  $string="select count(*) from tbl_ocacionales_contratos where pres_id = ".$this->idPresupuesto;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." CONTRATOS 
	 </strong>asociados a este la Presupuesto: <strong>".$this->nombre."</strong> por lo tanto no puede ser eliminado.";
   }else{
		 $string="DELETE FROM tbl_ocacionales_presupuestos WHERE pres_id=".$this->idPresupuesto;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar la Presupuesto: <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 }
 
 public function generarSelectPresupuestos(){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_id) ASC;";
   $this->resultGSPresupuestos = mysql_query($string);
   $conexion->desconectar();
 
 }
 
  public function generarSelectPresupuestosFacultades($idFacultad){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT pst.pres_id, pst.pres_ncertificado, f.faoc_nombre, pst.pres_fecha_vigencia 
   FROM tbl_ocacionales_presupuestos pst, tbl_ocacionales_facultades f WHERE f.faoc_id = pst.faoc_id 
   AND f.faoc_id = $idFacultad ORDER BY (pst.pres_fecha_entrada) DESC LIMIT 2";
   $this->resultGSPresupuestosFacultades = mysql_query($string);
   $conexion->desconectar();
 
 }  
 
}




////////////////////////////////////////////////////CLASES BASICAS PARA TUTORIAS//////////////////////////////////////////

/*ESTRUCTURA PARA EL OBJETO PeriodosAcademicos */
class PeriodosAcademicosTutorias{
 var $pref0, $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idPeriodo, $nombre, $fechaInicio, $fechaFinal, $estado;
 var $PeriodoAcademicoActual;
 var $error;

 public function  __construct($idPeriodo=NULL){
	$args = func_num_args();
	$this->pref0 = "esta";
	$this->tabla = "tbl_tutorias_periodos_academicos"; $this->alias="peac"; $this->pref="peac"; 
	$this->tabla2 = "tbl_tutorias_contratos"; $this->alias2="tucon"; $this->pref2="tucon";
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar(); 
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id=$idPeriodo;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idPeriodo=$idPeriodo;
		  $this->nombre=$result_query[1];
		  $this->fechaInicio=$result_query[2];
		  $this->fechaFinal=$result_query[3];
		  $this->estado = new EstadosTutorias($result_query[4]);
		 }
 }
 
 public function agregarPeriodoAcademico($nombre,$fechaInicio,$fechaFinal,$estado){
  $conexion= new conexion();
  
  $conexion->conectar();
   $anoActual = date("Y")."01";
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $anoActual;";
   $query=mysql_query($string);
   
   if((mysql_num_rows($query))==0){
   $idPeriodo = $anoActual;
   }else{
	    $anoActual = date("Y")."02";
		$idPeriodo = $anoActual;
		}
   $string="INSERT INTO ".$this->tabla." () VALUES ($idPeriodo, '$nombre', '$fechaInicio', '$fechaFinal', $estado);";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al insertar los datos ". mysql_errno()." - ".mysql_error(). " ";
   if(mysql_errno()==1062){ $this->error= "Lo sentimos, solo se pueden agregar <strong>dos (2)</strong> periodos academicos en un mismo año.";}
  }else{
	    $this->idPeriodo=$idPeriodo;
		$this->nombre=$nombre;
		$this->fechaInicio=$fechaInicio;
		$this->fechaFinal=$fechaFinal;
		$this->estado=$estado;
		
		/* INICIO cambiar el estado anterios si existe a INHABILITADO */
		$string="SELECT * FROM ".$this->tabla."  ORDER BY(".$this->pref."_id) DESC LIMIT 1 OFFSET 1;";
	    $query=mysql_query($string);
		$result_query = mysql_fetch_array($query);
		if(mysql_num_rows($query)>0){
	    $string="UPDATE ".$this->tabla."  SET ".$this->pref0."_id=1 WHERE ".$this->pref."_id = $result_query[0];";
	    $query=mysql_query($string);
	    }
		/* FIN cambiar el estado anterios si existe a INHABILITADO */		
       } 
  $conexion->desconectar();
 }
 
 public function actualizarPeriodoAcademico($nombre,$fechaInicio,$fechaFinal,$estado){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE ".$this->tabla." SET ".$this->pref."_nombre = '$nombre', ".$this->pref."_fecha_inicio = '$fechaInicio', 
   ".$this->pref."_fecha_final = '$fechaFinal', ".$this->pref0."_id = $estado  WHERE ".$this->pref."_id = $this->idPeriodo;";
   $query=mysql_query($string); 
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idPeriodo=$this->idPeriodo;
		$this->nombre=$nombre;
		$this->fechaInicio=$fechaInicio;
		$this->fechaFinal=$fechaFinal;
		$this->estado=$estado;
       } 
  $conexion->desconectar();
 }
 
 public function eliminarPeriodoAcademico(){
  $conexion= new conexion();
  
  $conexion->conectar();  
   $string="SELECT count(dooc.peac_id) AS PERIODO_ACADEMICO FROM tbl_tutorias_contratos ct WHERE ct.peac_id =".$this->idPeriodo;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." DOCENTE(s) TUTORES(es)</strong> relacionado(s) con el 
	 PERIODO ACADEMICO : <strong>".$this->nombre."</strong> y no puede ser eliminado.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idPeriodo;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar el PERIODO ACADEMICO : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectPeriodoAcademico(){
  $conexion= new conexion();
  
  $conexion->conectar();
  $conexion->desconectar();
 
 }
 
 public function obtenerPeriodoAcademicoActual(){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla."  WHERE ".$this->pref0."_id=0 ORDER BY(".$this->pref."_id) DESC LIMIT 1;";
   $query=mysql_query($string);
   $totalPer = mysql_num_rows($query);
   if($totalPer==1){
    $this->PeriodoAcademicoActual = mysql_fetch_array($query);
   }elseif($totalPer==0){
	      $this->PeriodoAcademicoActual[1] = "<font color='#FF0000'>¡ Disculpe, para crear una nueva catedra debe 
		  haber y estar activado un periodo academico correspondiente al semestre actual !</font>";
		 }	 
   
  $conexion->desconectar();
 
 }
 
 public function cambiarEstadoPeriodoAcademico($idPeriodo, $estado){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla."  WHERE ".$this->pref0."_id = 0 ORDER BY(".$this->pref."_id) DESC LIMIT 1;";
   $query=mysql_query($string);
   $result_query = mysql_fetch_array($query);  
   if(mysql_num_rows($query)>0){
	$string="UPDATE ".$this->tabla."  SET ".$this->pref0."_id = 1 WHERE ".$this->pref."_id = $result_query[0];";
	$query=mysql_query($string);
   }
   $string="UPDATE ".$this->tabla."  SET ".$this->pref0."_id = $estado WHERE ".$this->pref."_id = $idPeriodo;";
   $query=mysql_query($string);
   if (!$query){
   $this->error= "Error al actualizar el periodo academico ". mysql_errno()." - ".mysql_error(). " ";
   }
   $conexion->desconectar();
 }
 
}


/*ESTRUCTURA PARA EL OBJETO EstadosPeriodosAcademicos */
class EstadosTutorias{
 var $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idEstado, $nombre;
  var $resultGSEstados;
 var $error;

 public function  __construct($idEstado=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_tutorias_estados"; $this->alias = "esta"; $this->pref="esta"; 
	$this->tabla2 = "tbl_tutorias_periodos_academicos"; $this->alias2 = "peac"; $this->pref2="peac"; 
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idEstado;";
		   $query=mysql_query($string);
		  //$conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idEstado=$idEstado;
		  $this->nombre=$result_query[1];
		 }
 }
 
 public function generarSelectEstados(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombre) ASC;";
   $this->resultGSEstados = mysql_query($string);
  $conexion->desconectar();
 
 } 
}

/*ESTRUCTURA PARA EL OBJETO Contratante */
class ContratantesTutorias{
 var $tabla, $alias, $pref, $identificacion, $nombres, $apellidos, $descripcion, $estado;
 var $error, $resultGSContratantes, $ContratanteActual;

 public function  __construct($identificacion=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_tutorias_contratantes"; $this->alias = "cont"; $this->pref="cont"; 
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		  $string="SELECT * FROM ".$this->tabla." ".$this->alias." 
		   WHERE ".$this->alias.".".$this->pref."_identificacion = $identificacion;";
		   $query=mysql_query($string);
		  //$conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->identificacion=$result_query[0];
		  $this->nombres=$result_query[1];
		  $this->apellidos=$result_query[2];
		  $this->descripcion=$result_query[3];
		  $this->estado=$result_query[4];
		 }
 }
 
 public function agregarContratantes($identificacion, $nombre, $apellido, $descripcion, $estado){
 $conexion= new conexion();
		  
  $conexion->conectar();
  $string="INSERT INTO ".$this->tabla." () VALUES ($identificacion, '$nombre', '$apellido', '$descripcion', $estado); ";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->identificacion=$identificacion;
		$this->nombres=$nombre;
		$this->apellidos=$apellido;
		$this->descripcion= $descripcion;
		$this->estado=$estado;		
       
		/* INICIO cambiar el estado anterios si existe a INHABILITADO */
		$string="SELECT * FROM ".$this->tabla."  ORDER BY(".$this->pref."_identificacion) DESC LIMIT 1 OFFSET 1;";
	    $query=mysql_query($string);
		$result_query = mysql_fetch_array($query);
		if(mysql_num_rows($query)>0){
	   $string="UPDATE ".$this->tabla."  SET ".$this->pref."_estado = 1 WHERE ".$this->pref."_identificacion = $result_query[0];";
	    $query=mysql_query($string);
	    }
	
		 /* FIN cambiar el estado anterios si existe a INHABILITADO */	   
	}	 
  $conexion->desconectar();
 
 } 


 public function actualizarContratantes($identificacion, $nombre, $apellido, $descripcion, $estado){
 $conexion= new conexion();
		  
  $conexion->conectar();
  $string="UPDATE ".$this->tabla." SET ".$this->pref."_nombres = '$nombre', ".$this->pref."_apellidos = '$apellido', 
   ".$this->pref."_identificacion = $identificacion, ".$this->pref."_descripcion = '$descripcion',  ".$this->pref."_estado = $estado 
   WHERE ".$this->pref."_identificacion =".$this->identificacion;
   $query=mysql_query($string); 
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->identificacion=$identificacion;
		$this->nombres=$nombre;
		$this->apellidos=$apellido;
		$this->descripcion= $descripcion;
		$this->estado=$estado;		
       }
  $conexion->desconectar();
 
 }
 
 public function eliminarContratantes(){
  $conexion= new conexion();
  
  $conexion->conectar();
  $string="SELECT COUNT(c.cont_identificacion) AS RECTOR FROM tbl_tutorias_contratos c 
   WHERE c.cont_identificacion = ".$this->identificacion;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." CONTRATO(s) </strong>(s) relacionado(s) 
	 con este CONTRATANTE : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminado.";
   }else{
		$string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_identificacion = ".$this->identificacion;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar la CONTRATANTE : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 }
   
 public function generarSelectContratantes(){
  $conexion= new conexion();
    
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombres) ASC;";
   $this->resultGSContratantes = mysql_query($string);
  $conexion->desconectar();
 
 } 
 
  public function obtenerContratanteActual(){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla."  WHERE ".$this->pref."_estado=0 ORDER BY(".$this->pref."_identificacion) DESC LIMIT 1;";
   $query=mysql_query($string);
   $totalPer = mysql_num_rows($query);
   if($totalPer==1){
    $this->ContratanteActual = mysql_fetch_array($query);
   }elseif($totalPer==0){
	      $this->ContratanteActual[1] = "<font color='#FF0000'>¡ Disculpe, para crear un nuevo contrato debe 
		  haber y estar activo un contratante !</font>";
		 }	 
   
  $conexion->desconectar();
 
 }
 
  public function cambiarEstadoContratante($identificacion, $estado){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla."  WHERE ".$this->pref."_estado = 0 ORDER BY(".$this->pref."_identificacion) DESC LIMIT 1;";
   $query=mysql_query($string);
   $result_query = mysql_fetch_array($query);  
   if(mysql_num_rows($query)>0){
	$string="UPDATE ".$this->tabla."  SET ".$this->pref."_estado = 1 WHERE ".$this->pref."_identificacion = $result_query[0];";
	$query=mysql_query($string);
   }
   $string="UPDATE ".$this->tabla."  SET ".$this->pref."_estado = $estado WHERE ".$this->pref."_identificacion = $identificacion;";
   $query=mysql_query($string);
   if (!$query){
   $this->error= "Error al actualizar el contratante ". mysql_errno()." - ".mysql_error(). " ";
   }
   $conexion->desconectar();
 }  
   
}


/*ESTRUCTURA PARA EL OBJETO Presupuestos */
class Presupuestos{
 var $tabla, $alias, $pref, $idPresupuesto, $NumCertificado, $seccion, $codigo, $fechaV, $nombre, $fechaIngreso;
 var $resultGSPresupuestos;
 var $error;

 public function  __construct($idPresupuesto=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_tutorias_presupuestos"; $this->alias = "pres"; $this->pref="pres"; 
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  $conexion->conectar();
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idPresupuesto;";
		   $query=mysql_query($string);
		   $conexion->desconectar();		   
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idPresupuesto=$idPresupuesto;
		  $this->NumCertificado=$result_query[1];
		  $this->seccion=$result_query[2];
		  $this->codigo=$result_query[3];
		  $this->fechaV=$result_query[4];
	  	  $this->nombre=$result_query[5];
		  $this->fechaIngreso=$result_query[6];		   
		 }
 }
  
public function agregarPresupuestos($certificado, $seccion, $codigo, $fecha, $nombre){
  $conexion= new conexion();
  
  $conexion->conectar(); 
  $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
   GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
   $query=mysql_query($string);
   $max_num = mysql_fetch_array($query);
   $idPresupuesto=$max_num['UltimoInsertado'];
   if($max_num!=""){	
    $idPresupuesto=$idPresupuesto + 1;
    }else{
          $idPresupuesto=1; 
         }   
  $fechaIngreso = date("Y-m-d");
  $string="INSERT INTO tbl_tutorias_presupuestos () VALUES ($idPresupuesto, '$certificado', '$seccion', '$codigo', '$fecha', 
  '$nombre', '$fechaIngreso');";
   $query=mysql_query($string);	
  
  if (!$query){
   $this->error= "Error al insertar los datos, Detalles: ". mysql_errno()." - ".mysql_error(). " ";
  }else{
 	    $this->idPresupuesto=$idPresupuesto;
		$this->NumCertificado=$certificado;
		$this->seccion=$seccion;
		$this->codigo=$codigo;
		$this->fechaV=$fecha;
	  	$this->nombre=$nombre;	    		
       } 	  
  $conexion->desconectar();
 }  
 
  public function actualizarPresupuestos($certificado, $seccion, $codigo, $fechaV, $nombre){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE tbl_tutorias_presupuestos  SET pres_ncertificado='$certificado', pres_seccion='$seccion', 
   pres_codigo='$codigo', pres_fecha_vigencia='$fechaV', pres_nombre='$nombre' WHERE pres_id = ".$this->idPresupuesto;
   $query=mysql_query($string); 
   
  if (!$query){
   $this->error= "Error al actualizar los datos, Detalles: ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	     $this->idPresupuesto=$this->idPresupuesto;
		  $this->NumCertificado=$certificado;
		  $this->seccion=$seccion;
		  $this->codigo=$codigo;
		  $this->fechaV=$fecha;
	  	  $this->nombre=$nombre;
		
       } 
 $conexion->desconectar();
 }
 
 public function eliminarPresupuestos(){
  $conexion= new conexion();
  
  $conexion->conectar();  
   $string="select count(*) from tbl_tutorias_tutorias where pres_id = ".$this->idPresupuesto;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." TUTORIAS </strong>asociados a esta la Presupuesto: <strong>".$this->nombre."</strong> por lo tanto no puede ser eliminado.";
   }else{
		 $string="DELETE FROM tbl_tutorias_presupuestos WHERE pres_id=".$this->idPresupuesto;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar la Presupuesto: <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 }
 
 public function generarSelectPresupuestos(){
  $conexion= new conexion();
  
   $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_id) ASC;";
   $this->resultGSPresupuestos = mysql_query($string);
   $conexion->desconectar();
  
 
 }  
 
}


/*ESTRUCTURA PARA EL OBJETO Tutorias_Sedes */
class SedesTutorias{
 var $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idSede,$nombre;
 var $resultGSSedes;
 var $error;
 
 public function  __construct($idSede=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_tutorias_sedes"; $this->alias="setu"; $this->pref="setu"; 
    $this->tabla2 = "tbl_tutorias_programas"; $this->alias2="prog"; $this->pref2="prog";
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar(); 
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idSede;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idSede=$idSede;
		  $this->nombre=$result_query[1];
		 }
 }
 
 public function agregarSedesTutorias($nombre){
  $conexion= new conexion();
  
  $conexion->conectar();  
    $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
	GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
    $query=mysql_query($string);
	$max_num = mysql_fetch_array($query);
    $idSede=$max_num['UltimoInsertado'];
    if($max_num!="")
      {	
       $idSede=$idSede + 1;
	  }else{
            $idSede=1; 
           }
   $string="INSERT INTO ".$this->tabla."  () VALUES ($idSede, '$nombre');";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al insertar los datos en la tabla : ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idSede=$idSede;
		$this->nombre=$nombre;
       } 
  $conexion->desconectar();
 }
 
 public function actualizarSedesTutorias($nombre){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE ".$this->tabla."  SET ".$this->pref."_nombre = '$nombre' WHERE ".$this->pref."_id = $this->idSede;";
   $query=mysql_query($string); 
   
  if (!$query){
   $this->error= "Error al actualizar los datos en la tabla : ".$this->tabla."  ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idSede=$idSede;
		$this->nombre=$nombre;
       } 
 $conexion->desconectar();
 }
 
 public function eliminarSedesTutorias(){
  $conexion= new conexion();
  
  $conexion->conectar();  
   $string="SELECT count(t.setu_id) AS SEDES FROM tbl_tutorias_tutorias t WHERE t.setu_id = ".$this->idSede;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." TUTORIAS(s)</strong> relacionado(s) con la SEDE : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminada.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idSede;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar la SEDE : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectSedesTutorias(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombre) ASC;";
   $this->resultGSSedes = mysql_query($string);
  $conexion->desconectar();
 
 } 
}

/*ESTRUCTURA PARA EL OBJETO Tutorias_Programas */
class TutoriasProgramas{
 var $pref0, $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idPrograma, $nombre, $supervisor;
 var $resultGSTutoriasSedes;
 var $error;

 public function  __construct($idPrograma=NULL){
	$args = func_num_args();
	 $this->tabla = "tbl_tutorias_programas"; $this->alias = "prog"; $this->pref="prog"; 
	 $this->tabla2 = "tbl_tutorias_subprogramas"; $this->alias2 = "supr"; $this->pref2="supr";
	if ($args==0){
	  $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar(); 
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idPrograma;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idPrograma=$idPrograma;
		  $this->nombre=$result_query[1];
		  $this->supervisor = $result_query[2];
		 }
 }
 
 public function agregarTutoriasProgramas($nombre, $supervisor){
  $conexion= new conexion();
  
  $conexion->conectar(); 
  $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
   GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
   $query=mysql_query($string);
   $max_num = mysql_fetch_array($query);
   $idPrograma=$max_num['UltimoInsertado'];
   if($max_num!=""){	
    $idPrograma=$idPrograma + 1;
    }else{
          $idPrograma=1; 
         }    
   $string="INSERT INTO ".$this->tabla." () VALUES ($idPrograma, '$nombre', '$supervisor'); ";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al insertar los datos : ".mysql_errno()." - ".mysql_error();
  }else{
		$this->idPrograma = $idPrograma;
		$this->nombre = $nombre;
		$this->supervisor = $supervisor;
       } 
 $conexion->desconectar();
 }
 
 public function actualizarTutoriasProgramas($idPrograma, $nombre, $supervisor){
  $conexion= new conexion();
  
  $conexion->conectar();
 $string="UPDATE ".$this->tabla."   SET ".$this->pref."_id = $idPrograma, ".$this->pref."_nombre = '$nombre', 
 ".$this->pref."_supervisor = '$supervisor'  WHERE ".$this->pref."_id = ".$this->idPrograma;
   $query=mysql_query($string); 
   
 if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idPrograma = $idPrograma;
		$this->nombre = $nombre;
		$this->supervisor = $supervisor;
       } 
 $conexion->desconectar();
 }
 
 public function eliminarTutoriasProgramas(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT count(".$this->alias2.".".$this->pref."_id) AS PROGRAMAS FROM ".$this->tabla2." ".$this->alias2." 
   WHERE ".$this->alias2.".".$this->pref."_id = ".$this->idPrograma;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." SUBPROGRAMA</strong>(s) relacionado(s) con este PROGRAMA : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminado.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idPrograma;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar la PROGRAMA : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectTutoriasProgramas(){
  $conexion= new conexion();
    
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombre) ASC;";
   $this->resultGSTutoriasSedes = mysql_query($string);
  $conexion->desconectar();
 
 } 
}

/*ESTRUCTURA PARA EL OBJETO Programas */
class TutoriasSubProgramas{
 var $pref0, $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idSubPrograma, $nombre, $programa;
 var $resultGSSubProgramas;
 var $error;

 public function  __construct($idSubPrograma=NULL){
	$args = func_num_args();
	$this->pref0 = "prog";
	$this->tabla = "tbl_tutorias_subprogramas"; $this->alias = "sp"; $this->pref="supr"; 
	$this->tabla2 = "tbl_tutorias_contratos"; $this->alias2 = "tucon"; $this->pref2="tucon";
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar();
		  $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idSubPrograma;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idSubPrograma=$idSubPrograma;
		  $this->nombre=$result_query[1];
		  $this->programa = new TutoriasProgramas($result_query[2]);
		 }
 }
 
 public function agregarTutoriasSubProgramas($nombre, $programa){
  $conexion= new conexion();
  
  $conexion->conectar(); 
   $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
   GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
   $query=mysql_query($string);
   $max_num = mysql_fetch_array($query);
   $idSubPrograma=$max_num['UltimoInsertado'];
   if($max_num!=""){	
    $idSubPrograma=$idSubPrograma + 1;
    }else{
          $idSubPrograma=1; 
         }   
   $string="INSERT INTO ".$this->tabla." () VALUES ($idSubPrograma, '$nombre', $programa);";
   $query=mysql_query($string);  
  
  if (!$query){
   $this->error= "Error al insertar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idSubPrograma=$idSubPrograma;
		$this->nombre=$nombre;
		$this->programa=$programa;
       }
  $conexion->desconectar(); 
 }
 
 public function actualizarTutoriasSubProgramas( $nombre, $programa){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE ".$this->tabla."   SET  ".$this->pref."_nombre = '$nombre', ".$this->pref0."_id = $programa 
   WHERE ".$this->pref."_id = ".$this->idSubPrograma;
   $query=mysql_query($string); 

  if (!$query){
   $this->error= "Error al actualizar los datos  ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idSubPrograma=$this->idSubPrograma;
		$this->nombre=$nombre;
		$this->programa=$programa;
       } 
  $conexion->desconectar();
 }
 
 public function eliminarTutoriasSubProgramas(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT count(t.supr_id) AS SUBPROGRAMAS FROM tbl_tutorias_tutorias t WHERE t.supr_id = ".$this->idSubPrograma;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." CONTRATOS</strong>(s) relacionada(s) con el SUBPROGRAMA : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminado.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idSubPrograma;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar el SUBPROGRAMA : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectSubProgramas(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT * FROM ".$this->tabla." ".$this->alias." ORDER BY (".$this->alias.".".$this->pref."_nombre) ASC;";
   $this->resultGSSubProgramas = mysql_query($string);
  $conexion->desconectar();
 
 } 
}

/*ESTRUCTURA PARA EL OBJETO Modulos */
class Modulos{
 var $tabla, $alias, $pref, $tabla2, $alias2, $pref2, $idModulo, $modulo;
 var $error;

 public function  __construct($idModulo=NULL){
	$args = func_num_args();
	$this->tabla = "tbl_tutorias_modulos"; $this->alias = "modu"; $this->pref="modu"; 
	$this->tabla2 = "tbl_tutorias_modulos_contratos"; $this->alias2 = "mc"; $this->pref2="modu";
	if ($args==0){
	 $this->error="";
	}else{
		  $conexion= new conexion();
		  
          $conexion->conectar(); 
		   $string="SELECT * FROM ".$this->tabla." ".$this->alias." WHERE ".$this->alias.".".$this->pref."_id = $idModulo;";
		   $query=mysql_query($string);
		  $conexion->desconectar();
		  
		  $result_query=mysql_fetch_array($query);
		  $this->idModulo=$idModulo;
		  $this->nombre=$result_query[1];
		 }
 }
 
 public function agregarModulos($nombre){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT max(".$this->alias.".".$this->pref."_id) As UltimoInsertado FROM ".$this->tabla." ".$this->alias." 
   GROUP BY (".$this->alias.".".$this->pref."_id) DESC LIMIT 1";
   $query=mysql_query($string);
   $max_num = mysql_fetch_array($query);
   $idModulo=$max_num['UltimoInsertado'];
   if($max_num!=""){	
    $idModulo=$idModulo + 1;
    }else{
          $idModulo=1; 
         }   
   $string="INSERT INTO ".$this->tabla."  () VALUES ($idModulo, '$nombre');";
   $query=mysql_query($string);
  
  if (!$query){
   $this->error= "Error al insertar los datos  ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idModulo=$idModulo;
		$this->nombre=$nombre;
       } 
  $conexion->desconectar();
 }
 
 public function actualizarModulos($nombre){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="UPDATE ".$this->tabla."   SET ".$this->pref."_nombre = '$nombre'   WHERE ".$this->pref."_id = ".$this->idModulo;
   $query=mysql_query($string); 
  
   if (!$query){
   $this->error= "Error al actualizar los datos  ". mysql_errno()." - ".mysql_error(). " ";
  }else{
	    $this->idModulo=$this->idModulo;
		$this->nombre=$nombre;
       } 
  $conexion->desconectar();
 }
 
 public function eliminarModulos(){
  $conexion= new conexion();
  
  $conexion->conectar();
   $string="SELECT count(mt.modu_id) AS MODULOS FROM tbl_tutorias_modulos_tutorias mt WHERE mt.modu_id = ".$this->idModulo;
   $query=mysql_query($string);
   $listRows=mysql_fetch_array($query);
   if($listRows[0]!=0){
	 $this->error="Lo sentimos, existe(n) <strong>".$listRows[0]." CONTRATOS</strong>(s) relacionado(s) con este MODULO : 
	 <strong>".$this->nombre."</strong> y no puede ser eliminado.";
   }else{
		 $string="DELETE FROM ".$this->tabla." WHERE ".$this->pref."_id = ".$this->idModulo;
		 $query=mysql_query($string);
		 if (!$query){
	         $this->error= "Error al eliminar el MODULO : <strong>".$this->nombre.".</strong> 
			 Detalles : ". mysql_errno()." - ".mysql_error(). " ";
		 }
	    }   
  $conexion->desconectar(); 
 } 

 public function generarSelectModulos(){
  $conexion= new conexion();
  
  $conexion->conectar();
  $conexion->desconectar();
 
 } 
}

class Horas{
 var $tabla, $alias, $pref, $idHora, $valor;
 var $error;

 public function  cargarHoras(){
	$this->tabla = "tbl_tutorias_valor_hora"; 
	$conexion= new conexion();
	  
    $conexion->conectar();
	$string="SELECT * FROM ".$this->tabla." LIMIT 1";
	$query=mysql_query($string);
	$result_query=mysql_fetch_array($query);
	$this->idHora=$result_query[0];
	$this->valor=$result_query[1];
 }
 
  public function  actualizarHora($idHora, $valor){
  $conexion= new conexion();
	  
  $conexion->conectar();
  $string="UPDATE ".$this->tabla." SET valor_hora = '$valor' WHERE id_hora =$idHora";
  $query=mysql_query($string); 
  
  if (!$query){
   $this->error= "Error al actualizar los datos ". mysql_errno()." - ".mysql_error(). " ";
  }else{
		$this->idHora = $idPunto;
		$this->valor = $valor;		
       }
	   $conexion->desconectar();
 }
}


//////////////mostra fecha mes año
class formatoFechaOps{
 var $dia, $mes, $ano, $fecha;
 
 public function NombreMes($m){
  switch ($m){
    case 1: return "Enero";
    case 2: return "Febrero";
    case 3: return "Marzo";
    case 4: return "Abril";
    case 5: return "Mayo";
    case 6: return "Junio";
    case 7: return "Julio";
    case 8: return "Agosto";
    case 9: return "Septiembre";
    case 10: return "Octubre";
    case 11: return "Noviembre";
    case 12: return "Diciembre";
   }
  
}
	
public function Fechalarga($fecha){
  
  $this->dia = date("d",strtotime($fecha));
  $this->mes= $this->NombreMes(date("m",strtotime($fecha)));
  $this->ano=date("Y",strtotime($fecha));
  $this->fecha =  $this->dia." de ".$this->mes." de ".$this->ano;
  }
}
?>