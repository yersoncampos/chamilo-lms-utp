<?php 

require_once '../../../main/inc/conf/configuration.dist.php';

// Load configuration database Chamilo
$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection(array(
    'driver'    => 'mysql',
    'host'      => $_configuration['db_host'],
    'database'  => $_configuration['main_database'],
    'username'  => $_configuration['db_user'],
    'password'  => $_configuration['db_password'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => $_configuration['db_prefix'],
));
// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();
// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

/* End of file database.php */
/* Location: .//var/www/html/lms-utp/chamilo-lms-utp/dev/chamilo/configuration/database.php */