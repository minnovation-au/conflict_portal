<?php 
	
/**

The template for displaying save conflict details using the Conflict Plugin.

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


<!-- FORM BLOCK -->
<div style="width:90%;margin:auto;padding-bottom:36px">


<h5>Users Details</h5>

<div style="width:80%">
		<label for="name">Full Name: <span style="color:red;font-size:xx-small">*</span></label><br/>
		<input id="name" type="text" name="name" class="field" style="width:90%" required="required" ><br>

		
		<label for="email1">Email: <span style="color:red;font-size:xx-small">*</span></label><br>
		<input id="email1" type="text" name="email1" onchange="validateEmailAdd();" style="width:90%" class="field" required="required" >  <br>


<h5>Conflict Details</h5>

	  
	   
   <label for="issue">Issue: </label>
<input id="issue" type="text" name="issue" class="field"style="width:90%"/>

<label for="agenda">Agenda: </label>
<input id="agenda" type="text" name="agenda" class="field"style="width:90%"/>  

<label for="item">Item: </label>
<input id="item" type="text" name="item" class="field"style="width:90%"/> 





<!-- ACTIVATES THE CONLFICT_STORE FUNCTION AND PASSES VARIABLES WHEN THE BUTTON IS CLICKED -->

<?php 
	
$name=$_POST['name'];
$email=$_POST['email1'];
$issue=$_POST['issue'];
$agenda=$_POST['agenda'];
$item=$_POST['item'];

if (isset($_POST['save_conflict'])) { conflict_store(session_id(),$name,$email,$issue,$agenda,$item);
	 } 

?>

<script type="text/javascript">// <![CDATA[
 function submit_form(){
 	if(typeof(validateCaptchaAndSubmit)!='undefined'){
 		validateCaptchaAndSubmit();
 	}else{
 		check_webtolead_fields();
 	}
 }
 function check_webtolead_fields(){
     if(document.getElementById('bool_id') != null){
        var reqs=document.getElementById('bool_id').value;
        bools = reqs.substring(0,reqs.lastIndexOf(';'));
        var bool_fields = new Array();
        var bool_fields = bools.split(';');
        nbr_fields = bool_fields.length;
        for(var i=0;i<nbr_fields;i++){
          if(document.getElementById(bool_fields[i]).value == 'on'){
             document.getElementById(bool_fields[i]).value = 1;
          }
          else{
             document.getElementById(bool_fields[i]).value = 0;
          }
        }
      }
    if(document.getElementById('req_id') != null){
        var reqs=document.getElementById('req_id').value;
        reqs = reqs.substring(0,reqs.lastIndexOf(';'));
        var req_fields = new Array();
        var req_fields = reqs.split(';');
        nbr_fields = req_fields.length;
        var req = true;
        for(var i=0;i<nbr_fields;i++){
          if(document.getElementById(req_fields[i]).value.length <=0 || document.getElementById(req_fields[i]).value==0){
           req = false;
           break;
          }
        }
        if(req){
            document.WebToLeadForm.submit();
            return true;
        }
        else{
          alert('Please provide all the required fields');
          return false;
         }
        return false
   }
   else{
    document.WebToLeadForm.submit();
   }
}
function validateEmailAdd(){
	if(document.getElementById('email1') && document.getElementById('email1').value.length >0) {
		if(document.getElementById('email1').value.match(/^\w+(['\.\-\+]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+$/) == null){
		  alert('Not a valid email address');
		}
	}
	if(document.getElementById('email2') && document.getElementById('email2').value.length >0) {
		if(document.getElementById('email2').value.match(/^\w+(['\.\-\+]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+$/) == null){
		  alert('Not a valid email address');
		}
	}
}
// ]]></script>
