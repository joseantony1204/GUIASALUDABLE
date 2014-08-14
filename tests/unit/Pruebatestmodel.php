<?php
/*
class PruebaTest extends PHPUnit_Framework_TestCase
{
    public function testPrueba()
    {
        $this->assertTrue(true);
    }
}
*/

class Pruebatestmodel extends CDbTestCase
  {
    public function test()
    {
      $Testmodel = new Testmodel;
      $data = 1;
	  $Testmodel->Data =  $data;
      $this->assertEquals($data,$Testmodel->Data);	  
	}
  }
  
?>