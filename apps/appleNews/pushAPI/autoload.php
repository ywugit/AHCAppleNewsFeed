<?php
/**
 * Autoload push API classes.
 */
/*works on MAC, not linux
spl_autoload_register( function ( $class ) {
	
	$path = strtolower( $class );
	$path = str_replace( '_', '-',  $path );
	$path = explode( '\\', $path );
	$file = array_pop( $path );
	$path = implode( '/', $path ) . '/class-' . $file . '.php';
	$path = realpath( __DIR__ . '/../' . $path );
	//var_export("helllllll");
   // var_export($path);
	if ( file_exists( $path ) ) {
		require_once $path;
	}
} );
*/
/*works both for MAC and linux*/
spl_autoload_register( function ( $class ) {
	//$x = "hello\r\n";
	//var_export($x);
	//var_export("passedin: " . $class);
	$path = strtolower( $class );
	//var_export("\r\nafter lower class\r\n");
	//var_export($path);
	$path = str_replace( '_', '-',  $path );

	$path = explode( '\\', $path );
	//var_export("\r\nafter explode\r\n");
	// var_export($path);
	$file = array_pop( $path );
	//var_export("\r\nafter pop\r\n");
	//var_export($file);
	$path = '/class-' . $file . '.php';
	//var_export("\r\nafter implode\r\n");
	//var_export($path);
	//var_export("will export dir");
	//var_export(__DIR__  . $path);
	$path = realpath( __DIR__  . $path );
	//var_export("loading classes:" . $path . "\r\n");
	if ( file_exists( $path ) ) {
		require_once $path;
	}
} );
