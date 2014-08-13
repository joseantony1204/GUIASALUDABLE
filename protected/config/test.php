<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
			//uncomment the following to provide test database connection
			'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=BD_GESTION_UNG',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '120487',
			'charset' => 'utf8',
			'enableProfiling' => true,
		    ),
			
		),
		
		'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning, trace, info',
                    'categories'=>'application.*',
                ),
 
            ),
        ),
	)
);
