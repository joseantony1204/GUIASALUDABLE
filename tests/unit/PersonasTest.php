<?php
require_once('C:\wamp\www\GUIASALUDABLE\tests\fixtures\Personas.php');
 class PersonasTest extends PHPUnit_Framework_TestCase
 {
        
        public function testConnection()
        {
           $this->assertTrue(true);
        }
		
		public function testComparation()
        {          
		  $Personas = new Personas(1460);
		  $this->assertEquals(1119836516,$Personas->identificacion);
        }
		
		public function testInsert()
        {
          $Personas = new Personas();
          $idpersona = 8080;
          $identificacion = '8080';
		  $fechaingreso =date("Y-m-y");
		  $correo = '8080@gmail.com';
		  $direccion= '14a';
		  $telefono = '3002945168';
		  $tipoidentificacion= 1;
		  $tiporegimen = 1;
		  $pais = NULL;
		  $dpto=NULL;
		  $municipio = NULL;
		  $Personas->agregarPersona(NULL,$identificacion, $fechaingreso, $correo, $direccion, $telefono, $tipoidentificacion, $tiporegimen, $pais, $dpto, $municipio);
		  
		  //$Persona = new Personas(8080);
		  //$this->assertEquals(8080,$Persona->identificacion);
		  //$this->assertContains('admin',$nombre);
        }
 }
?>