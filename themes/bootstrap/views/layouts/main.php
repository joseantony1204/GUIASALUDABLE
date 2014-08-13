<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xml:lang="<?php echo Yii::app()->language;?>" lang="<?php echo Yii::app()->language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="<?php echo Yii::app()->language;?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
    
	<?php Yii::app()->bootstrap->register(); ?>
</head>

<body>

<?php $this->widget('bootstrap.widgets.TbNavbar',array(
    'type'=>'inverse', 
	'fixed'=>'top',  
    'collapse'=>true, // requires bootstrap-responsive.css
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>array(                
            ),
            'class'=>'bootstrap.widgets.TbMenu',
            'htmlOptions'=>array('class'=>'pull-right'),
			'items'=>array(
             array('label'=>'ACERCA DE...', 'icon'=>'wrench', 'url'=>array('/site/page/', 'view'=>'about'), 
			       'visible'=>!Yii::app()->user->isGuest),
             array('label'=>'INGRESAR AL SISTEMA', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
             array('label'=>''.strtoupper(Yii::app()->user->nombres).'', 'icon'=>'user', 'active'=>true, 
			 'visible'=>!Yii::app()->user->isGuest, 'url'=>'',
				'items'=>array(
				    array('label'=>'ADMINISTRADOR DE URUSARIO','visible'=>!Yii::app()->user->isGuest),
					array('label'=>'Mis Datos',  'url'=>array('/usuario/userperfil/usuarioperfilusuario/admin'), 
					      'visible'=>!Yii::app()->user->isGuest),
				    array('label'=>'Salir', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
                  ),
			  ),
		  ),
	  ), 
  ),     
)); ?>



<div class="container" id="page">

	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>
    
 <div class="info"  style="text-align:left">   
 <?php $this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
	   )
    ); ?>
    <?php $this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'info'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
	   )
    ); ?>
     <?php $this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
	   )
    );  ?>     
</div>
        <div id="content">
            <?php echo $content."<br><br>"; ?>
        </div><!-- content -->   
		<div class="clear"></div>
		<div class="clear"></div>
<?php $this->widget('bootstrap.widgets.TbNavbar',array(
    'type'=>'inverse',
	'fixed'=>'bottom',
	'brand'=>'',
    'collapse'=>true,
	'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'htmlOptions'=>array('class'=>'pull-right'),
			'items'=>array(
			array('label'=>'Copyright © '.date("Y").' - UNIVERSIDAD DE LA GUAJIRA - Todos Los Derechos Reservados', 'url'=>'')
			 )
			)
		)   
)); 

?> 

</div><!-- page -->
<?php
Yii::app()->clientScript->registerScript(
   'myHideEffect',
   '$(".info").animate({opacity: 1.0}, 20000).slideUp("slow");',
   CClientScript::POS_READY
);
?>
</body>
</html>
