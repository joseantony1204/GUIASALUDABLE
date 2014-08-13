<?php
/**
 * This is the bootstrap file for test application.
 * This file should be removed when the application is deployed for production.
 */

// change the following paths if necessary
$yii=dirname(__FILE__).'/../YII/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/test.php';

// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();

// automatically send every new message to available log routes
Yii::getLogger()->autoFlush = 1;
// when sending a message to log routes, also notify them to dump the message
// into the corresponding persistent storage (e.g. DB, email)
Yii::getLogger()->autoDump = true;
