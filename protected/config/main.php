<?php

// Define a path alias for the Bootstrap extension as it's used internally.
// In this example we assume that you unzipped the extension under protected/extensions.
Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap');
return array(
    //'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'theme'=>'bootstrap', // requires you to copy the theme under your themes directory
    'name'=>'GUNIG - UNIVERSIDAD DE LA GUAJIRA',
	'language'=>'es',
	'sourceLanguage'=>'en',
	'charset'=>'utf-8',
	'homeUrl'=>array('site/indexx'),
	//'defaultController'=>'site/login',
	
	// preloading 'log' component
	'preload'=>array('log'),
	
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.vendors.phpexcel.PHPExcel',
		'application.extensions.*',		
	),
	'aliases' => array(
    'xupload' => 'ext.xupload'
     ), 
		'modules'=>array(
		'contratacion',
		'gestiondocumental',
		'financiero',
		'academico',
		'general',
		'seguridad',
		'usuario',
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123456',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
			'generatorPaths'=>array(
                      'bootstrap.gii',
                     ),
		),
		
	),
	
    'components'=>array(
        'bootstrap'=>array(
            'class'=>'bootstrap.components.Bootstrap',
			
        ),
						
		    //
			//  IMPORTANTE:  asegurate de que la entrada 'user' (y format) que por defecto trae Yii
			//               sea sustituida por estas a continuación:
			//
			'user'=>array(
			 'allowAutoLogin'=>true,
             'class' => 'WebUser',
             ),
			
			'format' => array(
				'datetimeFormat'=>"d M, Y h:m:s a",
			),
		
		// uncomment the following to enable URLs in path-format
		/*
		'authManager' => array(
				'class' => 'CDbAuthManager',
				'connectionID'=>'db',
				'itemTable'=>'auth_item',
				'itemChildTable'=>'auth_relacion',
				'assignmentTable'=>'auth_asignacion',
			),
		*/
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'urlSuffix'=>'.faces',
			'caseSensitive'=>false,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				'<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
			),
		),
		
		// uncomment the following to use a MySQL database
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=BD_GESTION_UNG',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '120487',
			'charset' => 'utf8',
			'enableProfiling' => true,
		),
		
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
					'categories'=>'system.*',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),*/				
			),
		),
    ),
);
?>