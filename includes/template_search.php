<?php 
	
/**

The template for displaying search results using the Conflict Plugin.

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

?>

<div style="width:90%;margin:auto;padding-bottom:36px">

<form method="post" id="searchform">
	
	<table style="border-collapse:collapse; ">

<tr><td colspan="3">	

<h2>Search Registered Conflicts</h2>
<p> Enter search criteria into one or more fields and press search to filter the registered Conflicts </p>
</td></tr>

<tr>
		<td style="width:50%">
		<label for="name">Name: </label><br/>
    	<input type="text" name="name" class="field" placeholder="Search by Name">
		</td>
    	
    	<td style="width:50%">
		<label for="postcode">Email: </label><br/>
		<input  type="text" name="email" class="field" placeholder="Search by Email">
    	</td>
</tr><tr>    	
    	<td style="width:33%">
		<label for="issue">Issue: </label><br/>
		<input  type="text" name="issue" class="field" placeholder="Search by Issue">
    	</td>
		
		<td style="width:33%">
		<label for="agenda">Agenda: </label><br/>
		<input  type="text" name="agenda" class="field" placeholder="Search by Agenda">
    	</td>
		
    	<td style="width:33%">
		<label for="item">Item: </label><br/>
		<input  type="text" name="item" class="field" placeholder="Search by Item">
    	</td>
</tr>

<tr><td colspan="3" style="padding-top:20px">	

<input class="button" type="submit" name="search_conflict" value="Search" >

</td></tr>

	</table>
		
 </form>

</div>

<div style='width:90%;margin:auto;padding-bottom:72px'>

<?php 


	
if(isset($_POST['search_conflict'])){
	
	  $name=$_POST['name'];
	  $email=$_POST['email'];
	  $issue=$_POST['issue'];  
	  $agenda=$_POST['agenda']; 
	  $item=$_POST['item']; 
	  
	  echo "You Searched for: ". $name . " " . $email . " " . $issue. " " . $agenda. " " . $item;
	  echo "<br/>";
	  echo "<br/>";
	      
  //-query  the database table
     
         	
    global $wpdb;
  	$result = $wpdb->get_results('SELECT * FROM wp_store where name like "%'.$name.'%" and email like "%'.$email.'%" and issue like "%'.$issue.'%" and agenda like "%'.$agenda.'%" and item like "%'.$item.'%"');

  	if ($wpdb->num_rows == 0) {
	  	echo "No Results Found";
  	}
  	
  	foreach($result as $row) {
	  	
	  	echo "<button class='accordion'>".$row->date.": ".$row->name."<br/>";
	  	echo "<table style='font-size:small'>";
		echo "<tr><td colspan=3>Email: ".$row->email."</td></tr>";
		echo "<tr><td style='width:200px'>Issue: ".$row->issue."</td>";
		echo "<td style='width:200px'>Agenda: ".$row->agenda."</td>";
		echo "<td style='width:200px'>Item: ".$row->item."</td></tr>";
		echo "</table>";
	  	echo "</button>";
	  	
	  	
	  	echo "<div class='panel'>";
		echo "<table>";
		
		global $wpdb;
    	$group = $row->session_id;
		$result2 = $wpdb->get_results("select post_title,if(declaration>0,'Yes','No') as declaration from wp_store,wp_conflict,wp_posts where wp_store.session_id=wp_conflict.session_id and wp_posts.id = wp_conflict.conflict_id and wp_store.session_id='$group'");
		foreach($result2 as $row2) {
		 
		 echo "<tr><td style='width:200px'><p>".$row2->post_title."</p></td>";
		 echo "<td><p>".$row2->declaration."</p></td></tr>";

   }
   
   		echo "</table></div>";
   		echo "<script>var acc = document.getElementsByClassName('accordion');var i;for (i = 0; i < acc.length; i++) {acc[i].onclick = function(){this.classList.toggle('active');this.nextElementSibling.classList.toggle('show');}}</script>";
   
   }
}
?>

</div>
