<?php
/*
Plugin Name: WP Slide-up box
Plugin URI: http://arachnoidea.com/wp-slide-up-box/
Description: Add jQuery-driven fancy slideboxes to your WordPress pages and Posts. With this plugin a layer will slide on an inserted picture on mouse hover.
Version: 0.21
Author: Ervin Domonkos
Author URI: http://arachnoidea.com
*/


add_action( 'wp_enqueue_scripts' , 'sjq_script' );
add_action( 'wp_enqueue_scripts' , 'slideup_script' );
add_action( 'wp_enqueue_scripts', 'sup_add_my_stylesheet' );
add_shortcode( 'slideupbox', 'sup_shortcode' );
register_activation_hook(__FILE__, 'slideupbox_install');

if ( is_admin() )
	require_once 'slideupbox-admin.php';

	
	
function slideupbox_install () {
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
	$sup_db_version = "1.0";

	$table_name = $wpdb->prefix . "slideupbox";
	
	if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
		if ( ! empty( $wpdb->charset ) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty( $wpdb->collate ) )
			$charset_collate .= " COLLATE $wpdb->collate";
			
	$sql = "CREATE TABLE " . $table_name . " (
		id int UNSIGNED NOT NULL,
		stitle VARCHAR(40),
		sdesc VARCHAR(150),
		target VARCHAR(255),
		PRIMARY KEY id (id)
		) $charset_collate;";

	dbDelta($sql);

	add_option("sup_db_version", $sup_db_version);
   }
     
}
	

// Get a row from db table specified by id
function get_slideupbox_row ( $id ) {
global $wpdb;

$table_name = $wpdb->prefix . "slideupbox";

$supdb_row = $wpdb->get_row( "
	SELECT *
	FROM $table_name
	WHERE id=$id
	" , ARRAY_A );

return $supdb_row;
}



// Shortcode handler function
function sup_shortcode ( $atts ) {
extract( shortcode_atts( array(
	'id' => '',
	'mode' => 'bottom-up-full',
	'class' => '',
	'title' => '',
	'desc' => '',
	'alt' => ''
), $atts ) );
$imgsrc = wp_get_attachment_image_src( $id, "full" );
$row = get_slideupbox_row ( $id, ARRAY_A );

$image_attributes = wp_get_attachment_image_src( $id, 'full' );
	if ( $image_attributes == false ) {
		$image_url = 'Invalid image ID';
		}
		else {
		$image_width = $image_attributes[1];
		$image_height = $image_attributes[2];
		}

// If provided, set the non-default values for title & desc
if ( $title != '' ) $row["stitle"] = $title;
if ( $desc != '' ) $row["sdesc"] = $desc;
if ( $alt == '' ) $alt = $row["stitle"];

		
$rtext = '<div class="boxgrid caption" style="width: '.$image_width.'px; height: '.$image_height.'px; overflow: hidden; position: relative;">';
if ( $row["target"] != '' ) {
	$rtext .= '<a href="'.$row["target"].'">';
}

if ( $id == '' ) {
	$rtext .= '';
	}
	else {
	$rtext .= '<img src="'.$imgsrc[0].'" style="position: absolute; top: 0; left: 0; border: 0;" alt="'.$alt.'" />';
	}

if ($class != '') $class = ' ' . $class;	
		
switch ($mode) {
	case 'bottom-up-full':
		$rtext .= '<div class="cover slideup_'.$id.' '.$mode.$class.'" style="top: '.$image_height.'px; left: 0; position: absolute; height: '.$image_height.'px; width: '.$image_width.'px;">';
		break;
	case 'bottom-up-part':
		$starter = $image_height * 0.25;
		$rtext .= '<div class="cover slideup_'.$id.' '.$mode.$class.'" style="top: '.($image_height-$starter).'px; left: 0; position: absolute; height: '.$image_height.'px; width: '.$image_width.'px;">';
		break;
	case 'left-right':
		$rtext .= '<div class="cover slideup_'.$id.' '.$mode.$class.'" style="top: 0px; left: -'.$image_width.'px; position: absolute; height: '.$image_height.'px; width: '.$image_width.'px;">';
		break;
	case 'left-right-uncover':
		$rtext .= '<div class="cover slideup_'.$id.' '.$mode.$class.'" style="top: 0px; left: 0px; position: absolute; height: '.$image_height.'px; width: '.$image_width.'px;">';
		break;
	case 'right-left':
		$rtext .= '<div class="cover slideup_'.$id.' '.$mode.$class.'" style="top: 0px; left: '.$image_width.'px; position: absolute; height: '.$image_height.'px; width: '.$image_width.'px;">';
		break;
	case 'right-left-uncover':
		$rtext .= '<div class="cover slideup_'.$id.' '.$mode.$class.'" style="top: 0px; left: 0px; position: absolute; height: '.$image_height.'px; width: '.$image_width.'px;">';
		break;
	case 'fade-in':
		$rtext .= '<div class="cover slideup_'.$id.' '.$mode.$class.'" style="top: 0px; left: 0px; position: absolute; height: '.$image_height.'px; width: '.$image_width.'px; display: none;">';
		break;
	case 'fade-out':
		$rtext .= '<div class="cover slideup_'.$id.' '.$mode.$class.'" style="top: 0px; left: 0px; position: absolute; height: '.$image_height.'px; width: '.$image_width.'px;">';
		break;
	case 'top-down-full':
		$rtext .= '<div class="cover slideup_'.$id.' '.$mode.$class.'" style="top: -'.$image_height.'px; left: 0; position: absolute; height: '.$image_height.'px; width: '.$image_width.'px;">';
		break;
	case 'top-down-part':
		$starter = $image_height * 0.25;
		$rtext .= '<div class="cover slideup_'.$id.' '.$mode.$class.'" style="top: -'.($image_height-$starter).'px; left: 0; position: absolute; height: '.$image_height.'px; width: '.$image_width.'px;">';
		break;
}
		
		
$rtext .= '<h3 style="width: '.($image_width).'px;">'.$row["stitle"].'</h3>';
$rtext .= '<p>'.$row["sdesc"].'</p>';
$rtext .= '</div>';
if ( $row["target"] != '' ) {
	$rtext .= '</a>';
}
$rtext .= '</div>
';	
	
return $rtext;	
}



// Use this function, if you'd like to insert Slide-up box into your themes, loops, etc.
function slideupbox ( $arr ) {
	if ( $arr['mode'] == '' ) $arr['mode'] = 'bottom-up-full';
	echo do_shortcode('[slideupbox id="'.$arr['id'].'" mode="'.$arr['mode'].'" class="'.$arr['class'].'" title="'.$arr['title'].'" desc="'.$arr['desc'].'" alt="'.$arr['alt'].'"]');
}



// Load the required JavaScript files
function slideup_script () {
	wp_enqueue_script(
		'slideup-script',
		plugins_url('', __FILE__) . '/js/slideup.js',
		array('jquery')
	);
}

function sjq_script () {
	wp_enqueue_script('jquery');	
}

function sup_add_my_stylesheet () {
    wp_register_style( 'sup-style', plugins_url('slideupbox.css', __FILE__) );
    wp_enqueue_style( 'sup-style' );
}


?>