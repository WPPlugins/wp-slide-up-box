<?php

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();

else { 
	global $wpdb;
    $table = $wpdb->prefix . "slideupbox" ;

	$wpdb->query("DROP TABLE IF EXISTS $table");
}

?>