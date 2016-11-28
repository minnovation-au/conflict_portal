<?php 
	
/**

The template for displaying conflict details using the Conflict Plugin.

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

get_header();

?>

<!---------  DEFINE PHP VARIABLES  ------------>

<?php
	
$conflict_id=get_the_ID();
$declaration=$_POST['declaration'];
$next_page = get_post_meta( get_the_ID(), 'next_page', true );
$next_page_name = get_the_title(bwp_url_to_postid($next_page));

?>




<a href="<?php get_permalink( $post->ID ) ?>" title="<?php esc_attr( get_post_field( 'post_title', $post_ID ) ) ?>"> <?php esc_attr( get_post_field( 'post_title', $post_ID ) ) ?> </a>
	
	
<!---------- WORDPRESS LOOP FOR THE CONTENT ---------->	

<?php  while( have_posts() ): the_post(); ?>
 
<h1 class="page-header"><?php the_title(); ?></h1>
  
<form name="frmSite" method="post" >
	
<table style="float: left;padding-bottom:20px">
<tbody>
<tr>

<td style="width:40%">

 
<?php 

the_content();

endwhile; 

?>

<!---- LOAD THE CONFLICT DETAILS FROM THE DATABASE ------->

<?php
     
$meta_editor1 = get_post_meta( get_the_ID(), 'wp_conflict_issues', true );
// Check if the custom field has a value.
if ( ! empty( $meta_editor1 ) ) {
//    echo $meta_editor1;
}



if (( is_single( 'Outcome Page' )) || ( is_single( 'Save Details' )) || ( is_single( 'Start' ))) {
		
	} else { ?>

<p style='padding-top:30px;vertical-align: bottom'>
<input type='radio' name='declaration' value=1 checked='checked'/>Yes
<input type='radio' name='declaration' value=0 />No<br/></p>	

<?php } ?>

</td>
<td style="padding:20px 10px 20px">



<!---- PULL THE ISSUES TO CONSIDER INFORMATION FROM THE DB	--->
		
<?php
		
$meta_editor2 = get_post_meta( get_the_ID(), 'wp_conflict_issues', true );
// Check if the custom field has a value.
if ( ! empty( $meta_editor2 ) ) {
    echo $meta_editor2;
}

if ( is_single( 'Outcomes' ) ) {
	
	get_outcome(session_id());
	
	}
	
	
if ( is_single( 'Save Details' ) ) {
	
	include_once('template_save.php') ; 
	
	}
?>
	
</td>
</tr>
</tbody>
</table>


<?php if  ( is_single( 'Save Details' )) { 

			if (isset($_POST['save_conflict'])) {
				echo "<br/><br/>";
				echo "<a href='start'>Details Saved - Click here to start a new Conflict Assessment</a>";
				
				// START A NEW SECURE 
				
				session_regenerate_id();
		 
			} else { ?>

			<div style='width:100%;padding:10px 0 40px 10px'></div>
			<input class='button' type='submit' name='save_conflict' id='save_conflict' value='Register Conflict of Interest' />
			</div>

<?php }} else { ?>

<div style='width:100%;padding:10px 0 40px 10px'></div>
<input id="the-button" type="submit" class="btn" value="Next: <?php echo $next_page_name ?>" name="the-button" />
</div>

</form>


</br>
</br>



<?php } ?>

<!---- OUTPUT SECURE SESSION ID (Can be Deleted after implementation - sjm) ----->


<?php 
	
if ( !is_writable(session_save_path()) ) {
   echo "<p style='font-size:0.6em'>Unable to start secure session. Path not writable!</p>"; 
   
   } else {
	   
   echo "<p style='font-size:0.6em'>Secure ID:".session_id()."</p>";

} 

?>



<!----------- SAVE DATA TO THE DATABASE AND REDIRECT TO NEXT PAGE ------------>
  
<?php
	
	

	
	  if (isset($_POST['the-button'])) {
		  
		if (( is_single( 'Outcome Page' )) || ( is_single( 'Save Details' )) || ( is_single( 'Start' ))) {
		
	} else { 	  
		  
	  conflict_db(session_id(),$conflict_id,$declaration);	  
	  
	  }
	  
	  ?>
	  <script type="text/javascript">
	  window.location= <?php  echo "'".$next_page."'"; } 
	
	
		  
?>
	  </script>
	  


<?php  get_footer(); ?>
