<?php
/**
 * The following variables are available in this template:
 * - $this: the BootCrudCode object
 */
?>
<?php
echo "<?php\n";
$label=($this->class2name($this->modelClass));
$controlador = $this->class2id($this->modelClass);
echo "\$this->breadcrumbs=array(
	'$label'=>array('index'),
	'Crear',
);\n";
?>
?>
<table width="60" border="0" align="left" class="">
  <tr>
    <td><table width="820" border="0" align="center">
      <tr>
        <td width="60" align="left">
            <?php 
			 echo "<?php "; echo"			 
			 \$imageUrl = Yii::app()->request->baseUrl . '/images/user.png';
			  echo \$image = CHtml::image(\$imageUrl); 
			  ?>         
			 "; ?>
        </td>
        <td align="left">
        <strong style="border-bottom-style:groove">PROCESO DE CREACIÒN DE REGISTROS [ 
		<?php echo strtoupper($this->class2name($this->modelClass)); ?>  : Nuevo ] </strong></td>
        <td width="80" align="center">
         <?php 
		 echo "<?php\n";
		 echo"
         
		 \$imageUrl = Yii::app()->request->baseUrl . '/images/regresar.png';
         \$htmlOptions = array('class' => 'thumbnail','rel' => 'tooltip','data-title' => 'Regresar');
         \$image = CHtml::image(\$imageUrl);
         echo CHtml::link(\$image, array('$controlador/admin',),\$htmlOptions ); \n?>         
		 ";
		 ?>
        
        </td>
        <td width="80" align="center">
        <?php 
		 echo "<?php\n";
		 echo"
         
		 \$imageUrl = Yii::app()->request->baseUrl . '/images/refrescar.png';
         \$htmlOptions = array('class' => 'thumbnail','rel' => 'tooltip','data-title' => 'Creaciòn de registros');
         \$image = CHtml::image(\$imageUrl);
         echo CHtml::link(\$image, array('$controlador/create',),\$htmlOptions ); \n?>         
		 ";
		 ?>
         </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><p><?php echo "<?php echo \$this->renderPartial('_form', array('model'=>\$model)); ?>"; ?></p></td>
  </tr>
</table>
