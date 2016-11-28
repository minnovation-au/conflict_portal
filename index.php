<?php 
/**
 * Plugin Name: Conflict Portal
 * Plugin URI: http://minnovation.com.au
 * Description: This is a plugin created for the City of Casey to manage Conflicts of Interest among councillors and other staff. Distributed under GNU licensing.
 * Version: 1.0
 * Author: M-innovation Australia
 * Author URI: http://minnovation.com.au

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 M-innovation Australia

*/

session_start();
ob_start();

//$old_sessionid = session_id();
global $wp_query;

// register all hooks on plugin install
register_activation_hook(__FILE__, 'ciportal_install');
register_deactivation_hook(__FILE__, 'ciportal_uninstall');

function ciportal_install(){
	global $wpdb;
	global $ciportal_db_version;
	
	// DB wp_conflict table
	$wp_conflict_table = $wpdb->prefix."conflict";
	
	$conflict_tbl = "CREATE TABLE ".$wp_conflict_table." (id int(20) unsigned NOT NULL AUTO_INCREMENT, 
	session_id VARCHAR(128) DEFAULT NULL, 
	conflict_id int(12) DEFAULT NULL,
	declaration int(2) DEFAULT NULL,
	PRIMARY KEY id (id)
	);"; 
		
	require_once(ABSPATH. 'wp-admin/includes/upgrade.php');
	dbDelta($conflict_tbl);
	
	// DB wp_store table
	$wp_store_table = $wpdb->prefix."store";
	
	$store_tbl = "CREATE TABLE ".$wp_store_table." (id int(11) unsigned NOT NULL AUTO_INCREMENT,
  session_id varchar(128) DEFAULT NULL,
  name varchar(128) DEFAULT NULL,
  email varchar(128) DEFAULT NULL,
  issue varchar(128) DEFAULT NULL,
  agenda varchar(128) DEFAULT NULL,
  item varchar(128) DEFAULT NULL,
  date timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
	);"; 
		
	require_once(ABSPATH. 'wp-admin/includes/upgrade.php');
	dbDelta($store_tbl);

	
	add_option("ciportal_db_version", $ciportal_db_version);
}

function ciportal_uninstall(){
	global $wpdb;
	global $ciportal_db_version;
	
	//DB wp_conflict table
	$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'conflict');
	$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'store');
		
}


/************ REGISTERING EXTERNAL FILES FOR PLUGIN ************************************************/

function conflict_add_init() {
    if ( is_admin('conflict') ) {
        wp_enqueue_style("search_css", plugins_url('/css/conflict.css', __FILE__));
        wp_enqueue_script("search_script", plugins_url('/js/conflict.js', __FILE__));
    }
}

add_action( 'admin_enqueue_scripts', 'conflict_add_init' );



/*********** Insert the data into the wp_conflict table **********/

function conflict_db($session_id, $conflict_id, $declaration){
	
	$session_id=$session_id;
	$conflict_id=$conflict_id;
	$declaration=$declaration;
	global $wpdb;
	
		
	$records = $wpdb->get_var("SELECT count(*) FROM wp_conflict WHERE session_id='$session_id' AND conflict_id=$conflict_id");
	
	if ($records == 0) {
	
	$wpdb->query("INSERT INTO wp_conflict (session_id,conflict_id,declaration) VALUES ('$session_id','$conflict_id','$declaration') "); 
		
    } else {

	$wpdb->query("UPDATE wp_conflict SET declaration='$declaration' where session_id='$session_id' and conflict_id='$conflict_id'"); 
    
    }
            
}

/***********Insert the data into the wp_store table **********/

function conflict_store($session_id, $name,$email,$issue,$agenda,$item){
	
	$session_id=$session_id;
	$name=$name;
	$email=$email;
	$issue=$issue;
	$agenda=$agenda;
	$item=$item;
	$date=current_time('mysql',1);
		
	global $wpdb;
	
	$records = $wpdb->get_var("SELECT count(*) FROM wp_store WHERE session_id='$session_id'");
	
	if ($records == 0) {
		
	$wpdb->query("INSERT INTO wp_store (session_id,name,email,issue,agenda,item,date) VALUES ('$session_id','$name','$email','$issue','$agenda','$item','$date') "); 
	
	} else {
		
	$wpdb->query("UPDATE wp_store set session_id='$session_id', name='$name', email='$email', issue='$issue', agenda='$agenda', item='$item', date='$date')") ;
     
	}

// -- Set Up Email Details ------------------------------------------>

global  $wpdb;
		$email_body = $wpdb->get_results("select post_title,if(declaration>0,'Yes','No') as declaration from wp_store,wp_conflict,wp_posts where wp_store.session_id=wp_conflict.session_id and wp_posts.id = wp_conflict.conflict_id and wp_store.session_id='$session_id'");
		

 ob_start();
 
 	echo "<table>";
 	echo "<tr><td colspan=2>Details of the results you submitted</td></tr>";
	foreach($email_body as $row_email) {
		 
		 echo "<tr><td style='width:200px'>".$row_email->post_title."</td>";
		 echo "<td>".$row_email->declaration."</td></tr>";

   }
		
	echo "</table>"	;
$out = ob_get_clean();

$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
$headers .= "From: City of Casey <caseycc@casey.vic.gov.au>" . "\r\n";
$subject = "Conflict of Interest submitted on ".$date."";
$message = "<p>Hello ".$name."</p>

<p>Here are the details of the conflict of interest you submitted on ".$date." </p>

<p>Name: $name<br/>
Email: $email</p>

<p>Issue: $issue<br/>
Agenda: $agenda<br/>
Item: $item</p>

<p>$out</p>

<p>Thankyou for using the Conflict of Interest portal</p>
<p>Please provide feedback to the City of Casey Digital Team</p>";
    
// -- Send Email to User on Record Store--------------------------------------->

wp_mail($email,$subject,$message,$headers); // Email the Form
     
            }


/******************display the multiple records from the database ***************/

function get_outcome($session_id){
	global $wpdb;

$row = $wpdb->get_results( "select meta_value from wp_postmeta inner join wp_conflict on post_id=conflict_id where declaration =1 and session_id ='$session_id' and meta_key='wp_conflict_outcome'");

    foreach ( $row as $row ) 
    { 	    
	    echo "<p>".$row->meta_value."</p>"; //$row->your_column_name in table
	    
	}
	
	
}


/***********Create the own custom post type in wordpress **********/

add_action('init', 'conflict');
 
function conflict() {
 
	$labels = array(
		'name' => _x('Conflict of Interest', 'post type general name'),
		'singular_name' => _x('Conflict of Interest Item', 'post type singular name'),
		'add_new' => _x('Add New', 'Conflict of Interest item'),
		'add_new_item' => __('Add New Conflict of Interest Item'),
		'edit_item' => __('Edit Conflict of Interest Item'),
		'new_item' => __('New Conflict of Interest Item'),
		'view_item' => __('View Conflict of Interest Item'),
		'search_items' => __('Search Conflict of Interest'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => 'dashicons-book',
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor')
	  ); 
 
	register_post_type( 'conflict' , $args );
}


add_action("admin_init", "admin_init");
 
function admin_init(){
  add_meta_box("next_page", "Conflict of Interest", "credits_meta", "conflict", "normal", "low");
}
  

/***********Create the editor box to input the data for the user with convenient **********/
 
function credits_meta() {
  global $post;
  $custom = get_post_custom($post->ID);
  $wp_conflict_issues=$custom["wp_conflict_issues"][0];
  $wp_conflict_outcome=$custom["wp_conflict_outcome"][0];
  $wp_conflict_next=$custom["next_page"][0];
  $myeditor5=$custom["post_id"][0];
  
  
?>
 
 
  <p><label><strong>Issues to Consider</strong></label><br />
  <?php 
		  $wp_issues_content = '';
		  $editor_id2= 'wp_issues_content';
		  wp_editor( $wp_conflict_issues, $editor_id2 );
		  //echo $myeditor1;
		  ?>
  <p><label><strong>Outcome</strong></label><br />
  <?php 
		  $wp_outcome_content = '';
		  $editor_id3= 'wp_outcome_content';
		  wp_editor( $wp_conflict_outcome, $editor_id3 );
		  //echo $myeditor1;
		  ?>  
		 

<br>

<!***********Create the drop down box to select the page that needs to be displayed when pressed Next from the current page **********!>

<p><label><strong>Next URL</strong></label><br />

 <select name="next_page"> 
 <option value="<?php echo $wp_conflict_next ?>">
 
<?php echo get_the_title(bwp_url_to_postid($wp_conflict_next)); ?></option> 
 
<?php 
  
  $posts = get_posts(array('post_type'=> 'conflict', 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));

  foreach ( $posts as $post ) {
  	$option = '<option value="' . get_permalink( $post->ID ) . '">';
	$option .= $post->post_title;
	$option .= '</option>';
	echo $option;
	
  }
 ?>
</select>

  <?php
	  
}

/***********Save the input data from the editors into database **********/

add_action('save_post', 'save_details');
function save_details(){
  global $post;
  update_post_meta($post->ID, "wp_conflict_issues", $_POST["wp_issues_content"]);
  update_post_meta($post->ID, "wp_conflict_outcome", $_POST["wp_outcome_content"]);
  update_post_meta($post->ID, "next_page", $_POST["next_page"]);
  
}

function save_name(){
  global $post;
  update_post_meta($post->ID, "first_name", $_POST["first_name"]);

  
}


/***********Create additional column  **********/

add_action("manage_posts_custom_column",  "conflict_custom_columns");
add_filter("manage_edit-conflict_columns", "conflict_edit_columns");
 
function conflict_edit_columns($columns){
  $columns = array(
    "title" => "Title",
    "the_excerpt" => "Conflict",
    "next_page" => "Next Page"
  );
 
  return $columns;
}

function conflict_custom_columns($column){
  
  global $post;
 
  switch ($column) {
    case "next_page":
      echo get_the_title(bwp_url_to_postid(get_post_meta( get_the_ID(), 'next_page', true )));
      break;
	case "the_excerpt":
	echo get_the_excerpt();
	break;
  }
}

add_action( 'pre_get_posts', 'add_my_post_types_to_query' );

function add_my_post_types_to_query( $query ) {
	if ( is_home() && $query->is_main_query() )
		$query->set( 'post_type', array( 'post', 'conflict' ) );
	return $query;
}

function add_custom_post_type_to_query( $query ) {
    if ( $query->is_home() && $query->is_main_query() ) {
        $query->set( 'post_type', array('post', 'article') );
    }
}
add_action( 'pre_get_posts', 'add_custom_post_type_to_query' );

add_action( 'admin_init', 'conflict_admin_init' ); // initiate admin hook

//function conflict_admin_init() {
    // if mode is not set redirect to a default mode.
//    if(!isset($_GET['mode'])) {
//        
 //           wp_redirect( admin_url( 'edit.php?mode=excerpt&' . http_build_query( $_GET ) ) );
//            exit;
        
 //   }}}




/* Post URLs to IDs function, supports custom post types - borrowed and modified from url_to_postid() in wp-includes/rewrite.php */

function bwp_url_to_postid($url)
{
	global $wp_rewrite;

	$url = apply_filters('url_to_postid', $url);

	// First, check to see if there is a 'p=N' or 'page_id=N' to match against
	if ( preg_match('#[?&](p|page_id|attachment_id)=(\d+)#', $url, $values) )	{
		$id = absint($values[2]);
		if ( $id )
			return $id;
	}

	// Check to see if we are using rewrite rules
	$rewrite = $wp_rewrite->wp_rewrite_rules();

	// Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options
	if ( empty($rewrite) )
		return 0;

	// Get rid of the #anchor
	$url_split = explode('#', $url);
	$url = $url_split[0];

	// Get rid of URL ?query=string
	$url_split = explode('?', $url);
	$url = $url_split[0];

	// Add 'www.' if it is absent and should be there
	if ( false !== strpos(home_url(), '://www.') && false === strpos($url, '://www.') )
		$url = str_replace('://', '://www.', $url);

	// Strip 'www.' if it is present and shouldn't be
	if ( false === strpos(home_url(), '://www.') )
		$url = str_replace('://www.', '://', $url);

	// Strip 'index.php/' if we're not using path info permalinks
	if ( !$wp_rewrite->using_index_permalinks() )
		$url = str_replace('index.php/', '', $url);

	if ( false !== strpos($url, home_url()) ) {
		// Chop off http://domain.com
		$url = str_replace(home_url(), '', $url);
	} else {
		// Chop off /path/to/blog
		$home_path = parse_url(home_url());
		$home_path = isset( $home_path['path'] ) ? $home_path['path'] : '' ;
		$url = str_replace($home_path, '', $url);
	}

	// Trim leading and lagging slashes
	$url = trim($url, '/');

	$request = $url;
	// Look for matches.
	$request_match = $request;
	foreach ( (array)$rewrite as $match => $query) {
		// If the requesting file is the anchor of the match, prepend it
		// to the path info.
		if ( !empty($url) && ($url != $request) && (strpos($match, $url) === 0) )
			$request_match = $url . '/' . $request;

		if ( preg_match("!^$match!", $request_match, $matches) ) {
			// Got a match.
			// Trim the query of everything up to the '?'.
			$query = preg_replace("!^.+\?!", '', $query);

			// Substitute the substring matches into the query.
			$query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

			// Filter out non-public query vars
			global $wp;
			parse_str($query, $query_vars);
			$query = array();
			foreach ( (array) $query_vars as $key => $value ) {
				if ( in_array($key, $wp->public_query_vars) )
					$query[$key] = $value;
			}

		// Taken from class-wp.php
		foreach ( $GLOBALS['wp_post_types'] as $post_type => $t )
			if ( $t->query_var )
				$post_type_query_vars[$t->query_var] = $post_type;

		foreach ( $wp->public_query_vars as $wpvar ) {
			if ( isset( $wp->extra_query_vars[$wpvar] ) )
				$query[$wpvar] = $wp->extra_query_vars[$wpvar];
			elseif ( isset( $_POST[$wpvar] ) )
				$query[$wpvar] = $_POST[$wpvar];
			elseif ( isset( $_GET[$wpvar] ) )
				$query[$wpvar] = $_GET[$wpvar];
			elseif ( isset( $query_vars[$wpvar] ) )
				$query[$wpvar] = $query_vars[$wpvar];

			if ( !empty( $query[$wpvar] ) ) {
				if ( ! is_array( $query[$wpvar] ) ) {
					$query[$wpvar] = (string) $query[$wpvar];
				} else {
					foreach ( $query[$wpvar] as $vkey => $v ) {
						if ( !is_object( $v ) ) {
							$query[$wpvar][$vkey] = (string) $v;
						}
					}
				}

				if ( isset($post_type_query_vars[$wpvar] ) ) {
					$query['post_type'] = $post_type_query_vars[$wpvar];
					$query['name'] = $query[$wpvar];
				}
			}
		}

			// Do the query
			$query = new WP_Query($query);
			if ( !empty($query->posts) && $query->is_singular )
				return $query->post->ID;
			else
				return 0;
		}
	}
	return 0;
}

/* Filter the single_template with our custom function*/
add_filter('single_template', 'conflict_template');

function conflict_template($single) {
    global $wp_query, $post;

    /* Checks for single template by post type */
    if ($post->post_type == "conflict"){
        include_once( plugin_dir_path( __FILE__ ) . '/includes/template_conflict.php' );    }
    return $single;
}

// create custom plugin settings menu
add_action('admin_menu', 'conflict_search_menu');

function conflict_search_menu() {

//create new sub-level menu under conlifcts
add_submenu_page('edit.php?post_type=conflict', 'Search Registered Conflicts', 'Search Conflicts', 'administrator', __FILE__, 'conflict_search_page' , plugins_url('/images/icon.png', __FILE__) );

}

/* Filter the single_template for our custom search function*/
function conflict_search_page() {
	
	include_once( plugin_dir_path( __FILE__ ) . '/includes/template_search.php' );
	
}

add_action('admin_head', 'hidey_admin_head');

function hidey_admin_head() {
   
}

?>
 
 
