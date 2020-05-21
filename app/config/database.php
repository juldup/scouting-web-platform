<?php

// Get database data from file
$databaseFilePath = __DIR__ . "/../storage/site_data/database/database-config.txt";
if (file_exists($databaseFilePath)) {
  $databaseConfigText = file_get_contents($databaseFilePath);
  $databaseConfig = json_decode($databaseConfigText, true);
} else {
  return;
}

return array(

	/*
	|--------------------------------------------------------------------------
	| PDO Fetch Style
	|--------------------------------------------------------------------------
	|
	| By default, database results will be returned as instances of the PHP
	| stdClass object; however, you may desire to retrieve records in an
	| array format for simplicity. Here you can tweak the fetch style.
	|
	*/

	'fetch' => PDO::FETCH_CLASS,

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work. Of course
	| you may use many connections at once using the Database library.
	|
	*/

	'default' => array_key_exists('driver', $databaseConfig) ? $databaseConfig['driver'] : 'mysql',

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	'connections' => array(

		'sqlite' => array(
			'driver'   => 'sqlite',
			'database' => __DIR__ . '/../storage/site_data/database/database.sqlite',
			'prefix'   => '',
		),

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => array_key_exists('host', $databaseConfig) ? $databaseConfig['host'] : 'dummy',
			'database'  => array_key_exists('database', $databaseConfig) ? $databaseConfig['database'] : 'dummy',
			'username'  => array_key_exists('username', $databaseConfig) ? $databaseConfig['username'] : 'dummy',
			'password'  => array_key_exists('password', $databaseConfig) ? $databaseConfig['password'] : 'dummy',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
                        'strict'    => false,
		),

		'pgsql' => array(
			'driver'   => 'pgsql',
			'host'      => array_key_exists('host', $databaseConfig) ? $databaseConfig['host'] : '',
			'database'  => array_key_exists('database', $databaseConfig) ? $databaseConfig['database'] : '',
			'username'  => array_key_exists('username', $databaseConfig) ? $databaseConfig['username'] : '',
			'password'  => array_key_exists('password', $databaseConfig) ? $databaseConfig['password'] : '',
			'charset'  => 'utf8',
			'prefix'   => '',
			'schema'   => 'public',
		),

		'sqlsrv' => array(
			'driver'   => 'sqlsrv',
			'host'      => array_key_exists('host', $databaseConfig) ? $databaseConfig['host'] : '',
			'database'  => array_key_exists('database', $databaseConfig) ? $databaseConfig['database'] : '',
			'username'  => array_key_exists('username', $databaseConfig) ? $databaseConfig['username'] : '',
			'password'  => array_key_exists('password', $databaseConfig) ? $databaseConfig['password'] : '',
			'prefix'   => '',
		),

	),

	/*
	|--------------------------------------------------------------------------
	| Migration Repository Table
	|--------------------------------------------------------------------------
	|
	| This table keeps track of all the migrations that have already run for
	| your application. Using this information, we can determine which of
	| the migrations on disk haven't actually been run in the database.
	|
	*/

	'migrations' => 'migrations',

	/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store that also
	| provides a richer set of commands than a typical key-value systems
	| such as APC or Memcached. Laravel makes it easy to dig right in.
	|
	*/

	'redis' => array(

		'cluster' => false,

		'default' => array(
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		),

	),

);
