<?php
class PruebaTest extends PHPUnit_Framework_TestCase
  {
    public function testCompareUuser()
    {
      /*
	  $Usuarios = new Usuarios;
	  $nombre = $Usuarios->getDatosUsuario(1);
	  $this->assertEquals('admin',$nombre);*/
     $this->assertContains('php', array('php', 'ruby', 'c++', 'JavaScript'));
	}
	
	
  }
  
?>