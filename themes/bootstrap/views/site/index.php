<?php 
if(!Yii::app()->user->isGuest){  
$nombreUsuario = Yii::app()->user->nombres;
$nombreUsuario = ucwords(strtoupper($nombreUsuario));
$modulos = Yii::app()->user->modulosUsuarios;
$porcInterno = 20;
$filas = count($modulos); 
$colspan = $filas*2;
$porcInterno = $porcInterno*$filas;  
?>
<table width="70%" border="0">
  <tr>
  <td height="28" align="left">
  <fieldset>
  
<table width="100%" border="0">
  <tr>
    <td height="28" align="left">
    <fieldset>
      <table width="100%" border="0" align="center">
        <tr>
          <td width="60" align="left">
          <?php $imageUrl = Yii::app()->request->baseUrl . '/images/user.png'; echo $image = CHtml::image($imageUrl); ?></td>
         <td width="750" align="left"><h6>Hola, <strong><?php echo $nombreUsuario; ?></strong>, bienvenido(a) a GESTIÓN UNIGUAJIRA</h6></td>
        </tr>
      </table>
    </fieldset>
    </td>
  </tr> 
  <tr>
    <td><hr /></td>
  </tr>
  <tr>
   <td>

  <table width="<?php echo $porcInterno;?>%" border="0">
   <tr>
<?php 
foreach($modulos as $rows){
	
?>    
    <td>
         <?php 
		 $nombreModulo = $rows['USMO_NOMBRE'];
		 $descripcionModulo = $rows['USMO_URL'].'/';
		 $urlImage = $rows['USMO_URL'];
		 $imageUrl = Yii::app()->request->baseUrl . '/images/mod_'.$urlImage.'.png';
		 $htmlOptions = array('class' => 'thumbnail','rel' => 'tooltip','data-title' => 'Ir al '.$nombreModulo, 'target'=>'_blank',);
         $image = CHtml::image($imageUrl);
         echo CHtml::link($image, array($descripcionModulo,),$htmlOptions ); 
        ?>    
    
<?php /*
$nombreModulo = $rows['USMO_NOMBRE'];
$descripcionModulo = $rows['USMO_URL'].'/';
$urlImage = $rows['USMO_URL'];
$imageUrl = Yii::app()->request->baseUrl . '/images/mod_'.$urlImage.'.png';
$htmlOptions = array('class' => 'thumbnail','rel' => 'tooltip','data-title' => 'Ir al '.$nombreModulo);
$image = CHtml::image($imageUrl);

$this->widget('ext.popup.JPopupWindow', array(
    'content'=>$image,
	'name'=>"_blank",
    'url'=>array($descripcionModulo),
    'htmlOptions'=>$htmlOptions, // array('title'=>"Ir al ".$nombreModulo),
    'options'=>array(
        'height'=>2000,
        'width'=>2000,
        'top'=>0,
        'left'=>0,
		'centerScreen'=>1,
		'centerBrowser'=>1,
		'status'=>1,
		'scrollbars'=>1,
		'windowURL'=>NULL,
    ),
));*/
?>
    </td>
    <td align="left">&nbsp;</td>
<?php
$filasModulos = $filasModulos +1;
}
?>    
   </tr>
  </table>

    
    </td>
  </tr>
  <tr>
    <td><hr /></td>
  </tr>
</table>

</fieldset>
   </td>
  </tr>
</table>
<?php
}else{
?>
</p>
<h1>BIENVENIDO A</h1>
<h2><?php echo CHtml::encode(Yii::app()->name); ?> </h2>
<br/>
<h5>Para iniciar sesiòn haz clic en el vinculo que esta en la parte superior derecha de tu pantalla</h5>
<br/><br/>
<h3 align="center">
<?php 			 
	$imageUrl = Yii::app()->request->baseUrl . '/images/uniguajira.jpg';
	echo $image = CHtml::image($imageUrl); 
	?>
</h3>
<?php
}
?>
