<?php

add_action( 'admin_init' , 'slideupbox_admin_init' );
add_action( 'admin_menu' , 'slideupbox_menu' );
add_action( 'admin_enqueue_scripts', 'sup_admin_scripts' );
if ( !empty ( $_POST['sup_imgid'] )) add_slideupbox ();
if ( !empty ( $_POST['mod'] )) upd_slideupbox ();



function slideupbox_admin_init() {
       /* Register our stylesheet. */
       wp_register_style( 'slideupbox_style', plugins_url('slideupbox-admin.css', __FILE__) );
   }

   
   
   
function slideupbox_menu() {
	$page = add_submenu_page( 'themes.php',
		'WP Slide-up box',
		'WP Slide-up box',
		'manage_options',
		'wp-slideup-box',
		'slideupbox_options' );
	
	add_action( 'admin_print_styles-' . $page, 'slideupbox_admin_styles' );
}




// JS functions for admin panel
function sup_admin_scripts () {
	wp_enqueue_script(
		'sup-admin-scripts',
		plugins_url('', __FILE__) . '/js/sup_admin.js',
		array('jquery')
	);
}





// Load admin page style
function slideupbox_admin_styles() {
       /*
        * It will be called only on your plugin admin page, enqueue our stylesheet here
        */
       wp_enqueue_style( 'slideupbox_style' );
   }

   
   

	

// Get all the data from the db
function get_slideupbox_data () {
global $wpdb;

$table_name = $wpdb->prefix . "slideupbox";

$supdb_results = $wpdb->get_results( "
	SELECT *
	FROM $table_name
	" , ARRAY_A );

return $supdb_results;
}


   
   

// Build up admin table structure  
function slideupbox_options() {
/* This function handles the admin page of the plugin */
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	$pdata = get_plugin_data( __FILE__  );
	
	if ( $_POST["error"] == 1 ) $errtext = '<p class="errtext">There is no such image in your WordPress database.</p>';
	elseif ( $_POST["error"] == 2 ) $errtext = '<p class="errtext">Image ID already in use.</p>';
	else $errtext = '';
	unset ( $_POST["error"] );
	
	echo '<div class="wrap">
	'.$errtext.'
	<h2>WP Slide-up box</h2>
	<h3>Current images:</h3>
	<table class="sup_admintable">
		<thead>
			<tr>
				<th>Picture ID</th>
				<th>Thumb</th>
				<th>Picture URL</th>
				<th>Width</th>
				<th>Height</th>
				<th>Title</th>
				<th>Description</th>
				<th>Target URL</th>
				<th>Op.</th>
				<th>Shortcode</th>
			</tr>
		</thead>
		<tbody>'
		. write_slideupbox_data_to_table () .
		'</tbody>
	</table>
	
	<div id="addnewe">
	<h2>Add new image with the form below:</h2>
	<form name="sup_addnew" method="post" action="' . $_SERVER["REQUEST_URI"] . '" onsubmit="return supValidateForm()"><table class="sup_formtable">
		<tr>
			<td>Image ID:</td><td><input type="text" name="sup_imgid" id="sup_imgid" /></td>
		</tr>	
		<tr>
			<td>Title text:</td><td><input type="text" name="sup_title" id="sup_title" /></td>
		</tr>	
		<tr>
			<td>Description:</td><td><input type="text" name="sup_desc" id="sup_desc" /></td>
		</tr>	
		<tr>
			<td>Target URL:</td><td><input type="text" name="sup_target" id="sup_target" /></td>
		</tr>
		<tr>
			<td></td><td><input type="submit" value="Add image" /></td>
		</tr>
	</table>
	</form>
	</div>
	
	<div id="hints">
		<h2>Tips for more customization</h2>
		<p>You can use different effects <i>(called \'modes\' within this plugin)</i> adding the <b>mode</b> attribute to the slideupbox shortcode. Usage:</p>
		<p style="text-align: center; font-weight: bold;">[slideupbox id="x" mode="option"]</p>
		<p>where<br />
		<b>x</b> is the <i>id number of the image in the WordPress database</i> and<br />
		<b>option</b> is the <i>name of the mode</i>.</p>
		<h3>The following modes are supported in WP-slide-up box v' . $pdata["Version"] . '</h3>
		<ul>
			<li>bottom-up-full <i>(default)</i></li>
			<li>bottom-up-part</li>
			<li>left-right</li>
			<li>left-right-uncover</li>
			<li>right-left</li>
			<li>right-left-uncover</li>
		</ul>
		<p><b>Example:</b> <br />
		[slideupbox id="34" mode="bottom-up-full"]</p>
		<p>To see what happens using the different modes, take a look at the plugin\'s <a href="http://arachnoidea.com/modes-showcase" target="_blank">DEMO page</a>.</p>
	</div>
</div>';
}




// insert data to table
function add_slideupbox () {
global $wpdb;

	$superror = 0;

	$image_attributes = wp_get_attachment_image_src( $_POST['sup_imgid'], 'full' );
	if ( $image_attributes == false ) {
		$superror = 1;
		}
		
	$row = get_slideupbox_row ( $_POST['sup_imgid'] );
	if ( $row != null ) {
		$superror = 2;
		}
		

	if ( $superror > 0 ) {
		$_POST['error'] = $superror;
	}
	else {
		$table_name = $wpdb->prefix . "slideupbox";
		$data = array (
			"id" => $_POST['sup_imgid'],
			"stitle" => $_POST['sup_title'],
			"sdesc" => $_POST['sup_desc'],
			"target" => $_POST['sup_target'],
		);
		$wpdb->insert (	$table_name, $data );
	}

}




function upd_slideupbox () {
global $wpdb;

$table_name = $wpdb->prefix . "slideupbox";

$id = $_POST['mod'];
$st = "st".$id;
$sd = "sd".$id;
$tg = "tg".$id;

$data = array (
	"stitle" => $_POST[$st],
	"sdesc" => $_POST[$sd],
	"target" => $_POST[$tg],
);

if ( isset($_POST['modify_x']) ) {
	$wpdb->update ( $table_name, $data, array( 'id' => $_POST["mod"] ) );
}

if ( isset($_POST['delete_x']) ) {
	$sqlq = 'DELETE FROM '.$table_name.' WHERE id = '.$id;
	$wpdb->query( $sqlq );
}


}




// Admin table value rows
function write_slideupbox_data_to_table () {
$data = get_slideupbox_data ();
$purl = plugins_url('', __FILE__);
$text = "";
$ids = "";

foreach ( $data as $row ) {

	$image_attributes = wp_get_attachment_image_src( $row["id"], 'full' );
	$image_operations_links = '<a href="'.$_SERVER["REQUEST_URI"].'"><input name="modify" type="image" src="'.$purl.'/images/mod_ico.png" title="Modify" /></a><a href="'.$_SERVER["REQUEST_URI"].'"><input type="image" name="delete" src="'.$purl.'/images/del_ico.png" title="Delete" onclick="return confirm(\'Delete the Slide-up Box settings for image ID='.$row["id"].'?\');" /></a>' ;
	if ( $image_attributes == false ) {
		$image_url = 'Invalid image ID';
		}
	else {
		$image_url = $image_attributes[0];
		$image_width = $image_attributes[2];
		$image_height = $image_attributes[1];
		}
	
	$ids .= $row["id"] . ',';
		
	$text .= '			<tr><form id="f'.$row["id"].'" action="'.$_SERVER["REQUEST_URI"].'" method="post">
				<td>'.$row["id"].'</td>
				<td>'.wp_get_attachment_image( $row["id"], "thumbnail" ).'</td>
				<td>'.$image_url.'</td>
				<td>'.$image_height.'</td>
				<td>'.$image_width.'</td>
				<td><input id="st'.$row["id"].'" name="st'.$row["id"].'" value="'.$row["stitle"].'" /></td>
				<td><textarea id="sd'.$row["id"].'" name="sd'.$row["id"].'">'.$row["sdesc"].'</textarea></td>
				<td><input id="tg'.$row["id"].'" name="tg'.$row["id"].'" value="'.$row["target"].'" /></td>
				<td>'.$image_operations_links.'</td>
				<td>'.make_sup_shortcode($row["id"]).'</td>
				<input type="hidden" name="mod" value="'.$row["id"].'" /></form>
			</tr>';
	}
	
return $text;	
}





// Builds a shortcode to display in the admin menu
function make_sup_shortcode ($id) {
$sc = '[slideupbox id="'.$id.'"]';
return $sc;
}


	
?>