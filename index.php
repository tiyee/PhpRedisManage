<?php
/**
 *
 * @authors tiyee (tiyee@live.com)
 * @link http://www.tiyee.net
 * @date    2015-03-12 16:26:59
 * @version $Id$
 */

define('APP_NAME', 'application');
define('ROOT_PATH', __DIR__);
define('APP_PATH',ROOT_PATH.'/'.APP_NAME );

define('LOG_PATH',ROOT_PATH.'/logs' );

define('DIR_CACHE',ROOT_PATH.'/Cache' );

define('DEBUG',1);
define('PATH_INFO',2 );


require APP_PATH.'/route.php';
Route::init();
