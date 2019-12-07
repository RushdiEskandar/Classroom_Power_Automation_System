 <?php
/*
Template Name: Real Time Page
*/
 get_header();
   ?>


		<script type="text/javascript" src="jquery-1.11.0.min.js"></script>
	
	
	
	
		<style type="text/css">
		
			p
			{
				margin-left: 20px;
			}
		
select {
  -webkit-appearance: none;
  -moz-appearance: none;
  -ms-appearance: none;
  appearance: none;
  outline: 0;
  box-shadow: none;
  border: 0 !important;
  background: #2c3e50;
  background-image: none;
}
/* Custom Select */
.select {
  position: relative;
  display: block;
  width: 10em;
  height: 1.8em;
  line-height: 2;
  background: #2c3e50;
  overflow: hidden;
  border-radius: .25em;
}
select {
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0 0 0 .5em;
  color: #fff;
  cursor: pointer;
}
select::-ms-expand {
  display: none;
}
/* Arrow */
.select::after {
  content: '\25BC';
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  padding: 0 1em;
  background: #34495e;
  pointer-events: none;
}
/* Transition */
.select:hover::after {
  color: #f39c12;
}
.select::after {
  -webkit-transition: .25s all ease;
  -o-transition: .25s all ease;
  transition: .25s all ease;
}

 
		
			.group1
			{
				border: 3px solid red;
				margin: 20px;
				border-radius: 3px;
			}	
			.group2
			{
				border: 3px solid blue;
				margin: 20px;
				border-radius: 3px;
			}
		.group3
			{
				border: 3px solid orange;
				margin: 20px;
				border-radius: 3px;
			}
		</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<?php
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$conn= mysqli_connect("localhost", "root", "rushdi", "ControlDB");
 
// Check connection
if($conn=== false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
 

if (isset($_POST['LightOn1']))
{

$setmode4 = shell_exec("/usr/local/bin/gpio -g mode 4 out");
$gpio_on = shell_exec("/usr/local/bin/gpio -g write 4 1");
$temp1= $_POST['temp1'];
$sql = "UPDATE rooms SET room_status='On', temp='$temp1' WHERE room_name='Room1'";
if(mysqli_query($conn , $sql)){
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}

}
if (isset($_POST['LightOff1']))
{
$setmode4 = shell_exec("/usr/local/bin/gpio -g mode 4 out");
$gpio_off = shell_exec("/usr/local/bin/gpio -g write 4 0");

$temp1= $_POST['temp1'];
$sql = "UPDATE rooms SET room_status='Off', temp='$temp1' WHERE room_name='Room1'";
if(mysqli_query($conn , $sql)){
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
  
}
if (isset($_POST['LightOn2']))
{
$setmode17 = shell_exec("/usr/local/bin/gpio -g mode 17 out");
$gpio_on = shell_exec("/usr/local/bin/gpio -g write 17 1");
$temp2= $_POST['temp2'];
$sql = "UPDATE rooms SET room_status='On', temp='$temp2' WHERE room_name='Room2'";
if(mysqli_query($conn , $sql)){
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
}
if (isset($_POST['LightOff2']))
{
$setmode17 = shell_exec("/usr/local/bin/gpio -g mode 17 out");
$gpio_off = shell_exec("/usr/local/bin/gpio -g write 17 0");
$temp2= $_POST['temp2'];
$sql = "UPDATE rooms SET room_status='Off', temp='$temp2' WHERE room_name='Room2'";
if(mysqli_query($conn , $sql)){
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}

}
if (isset($_POST['LightOn3']))
{
$temp3= $_POST['temp3'];
$setmode27 = shell_exec("/usr/local/bin/gpio -g mode 27 out");
$gpio_on = shell_exec("/usr/local/bin/gpio -g write 27 1");
$temp3= $_POST['temp3'];
$sql = "UPDATE rooms SET room_status='On', temp='$temp3' WHERE room_name='Room3'";
if(mysqli_query($conn , $sql)){
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}

}
if (isset($_POST['LightOff3']))
{
$setmode27 = shell_exec("/usr/local/bin/gpio -g mode 27 out");
$gpio_off = shell_exec("/usr/local/bin/gpio -g write 27 0");
$temp3= $_POST['temp3'];
$sql = "UPDATE rooms SET room_status='Off', temp='$temp3' WHERE room_name='Room3'";
if(mysqli_query($conn , $sql)){
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}

}
 
// Close connection
mysqli_close($link);

?>

<div id="content" class="site-content">
<div class="ak-container-right ak-container">
            <div id="primary" class="content-area right">
    	<main id="main" class="site-main" role="main">

<article id="post-7" class="post-7 page type-page status-publish hentry">
		<div class="entry-content">


		<?php

 
 $dbhost = '127.0.0.1:3306';
   $dbuser = 'root';
   $dbpass = 'rushdi';
   
   $conn = mysql_connect($dbhost, $dbuser, $dbpass);
   
   if(! $conn ) {
      die('Could not connect: ' . mysql_error());
   }
   $sql = "SELECT * FROM rooms where room_name='Room1'";
   mysql_select_db('ControlDB');
   $retval = mysql_query( $sql, $conn );
   
   
   if(! $retval ) {
      die('Could not get data: ' . mysql_error());
   }
   while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) { 

      $room1_status=$row['room_status'];
      $room1_temp=$row['temp'];
}

 $sql = "SELECT * FROM rooms where room_name='Room2'";
   mysql_select_db('ControlDB');
   $retval = mysql_query( $sql, $conn );
   
   if(! $retval ) {
      die('Could not get data: ' . mysql_error());
   }
   while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) { 

      $room2_status=$row['room_status'];
      $room2_temp=$row['temp'];
}

$sql = "SELECT * FROM rooms where room_name='Room3'";
   mysql_select_db('ControlDB');
   $retval = mysql_query( $sql, $conn );
   
   if(! $retval ) {
      die('Could not get data: ' . mysql_error());
   }
   while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) { 

      $room3_status=$row['room_status'];
      $room3_temp=$row['temp'];
}

?>
<h1>Control a ClassRoom</h1>

<div align="center">
<label for='Updateinfo'><h4></h4></label>
<span id='ct' ></span>

<form method="post">
<div class="group1">

<h3>Room 1:</h3> 
<table>
<tr>
<td aligh="center" valign="middle" style="text-align:center; vertical-align:middle;">
<center>
<button class="btn" name="LightOn1" id="LightOn1" <?php if ($room1_status== 'On'){ ?> value="Light is On and this button is disabled" disabled style=" WIDTH: 140px; HEIGHT: 60px;" <?php   }else{ ?> value="On" style="WIDTH: 120px; HEIGHT: 60px;"<?php } ?>><?php if ($room1_status== 'On'){ ?>Light is On and this button is disabled"<?php   }else{ ?><h3>Light On</h3><?php } ?></button>
</center>

</td>
<td align="center"  style="text-align:center; vertical-align:middle;">

<button class="btn" name="LightOff1" id="LightOff1" <?php if ($room1_status== 'Off'){ ?> value="Light is Off and this button is disabled" disabled style=" WIDTH: 140px; HEIGHT: 60px;" <?php   }else{ ?> value="On" style="WIDTH: 120px; HEIGHT: 60px;"<?php } ?>><?php if ($room1_status== 'Off'){ ?>Light is Off and this button is disabled"<?php   }else{ ?><h3>Light Off</h3><?php } ?></button>

<br/>
</td>
</tr>
<tr><td colspan="2"  style="text-align:center; vertical-align:middle;">
<center><label for='temp1' <?php if ($room1_status== 'On'){ ?>style="display:none;"<?php   }?>><h4>Select a Tempratue: </h4></label>
 <div class='select' <?php if ($room1_status== 'On'){ ?>style="display:none;"<?php   }?>>
  <select  id='temp1'  name='temp1'  onchange='get_form(this).submit(); return false;' <?php if ($room1_status== 'On'){ ?>disabled<?php   }?>>
     <option value='Off'>Off</option>
     <option value='20'>20</option>
     <option value='21'>21</option>
     <option value='22'>22</option>
     <option value='23'>23</option>
     <option value='24'>24</option>
     <option value='25'>25</option>
     <option value='26'>26</option>
     <option value='27'>27</option>
     <option value='28'>28</option>
     <option value='29'>29</option>
     <option value='30'>30</option>

</select></div></center></td>

</tr>
<tr>
<td colspan="2" valign="middle" style="text-align:center; vertical-align:middle;">
<center><?php if ($room1_status== 'On'){ ?><center><label><h4 style="color:White; background:gray;">Room Light is On and Tempratue is <?php echo $room1_temp?></h4></label></center><?php }else{ ?><center><label><h4>Room Light is Off</h4></label></center><?php } ?></center>
</td></tr>

</table>

</div>
<div id="dtBox2" data-parentelement=".group2"></div>
<div class="group2">
<h3>Room 2:</h3> 
<table>
<tr>
<td aligh="center" valign="middle" style="text-align:center; vertical-align:middle;">
<center>
<button class="btn" name="LightOn2" id="LightOn2" <?php if ($room2_status== 'On'){ ?> value="Light is On and this button is disabled" disabled style=" WIDTH: 140px; HEIGHT: 60px;" <?php   }else{ ?> value="On" style="WIDTH: 120px; HEIGHT: 60px;"<?php } ?>><?php if ($room2_status== 'On'){ ?>Light is On and this button is disabled"<?php   }else{ ?><h3>Light On</h3><?php } ?></button>
</center>

</td>
<td align="center"  style="text-align:center; vertical-align:middle;">

<button class="btn" name="LightOff2" id="LightOff2" <?php if ($room2_status== 'Off'){ ?> value="Light is Off and this button is disabled" disabled style=" WIDTH: 140px; HEIGHT: 60px;" <?php   }else{ ?> value="On" style="WIDTH: 120px; HEIGHT: 60px;"<?php } ?>><?php if ($room2_status== 'Off'){ ?>Light is Off and this button is disabled"<?php   }else{ ?><h3>Light Off</h3><?php } ?></button>

<br/>
</td>
</tr>
<tr><td colspan="2"  style="text-align:center; vertical-align:middle;">
<center><label for='temp2' <?php if ($room2_status== 'On'){ ?>style="display:none;"<?php   }?>><h4>Select a Tempratue: </h4></label>
 <div class='select' <?php if ($room2_status== 'On'){ ?>style="display:none;"<?php   }?>>
  <select  id='temp2'  name='temp2'  onchange='get_form(this).submit(); return false;' <?php if ($room2_status== 'On'){ ?>disabled<?php   }?>>
     <option value='Off'>Off</option>
     <option value='20'>20</option>
     <option value='21'>21</option>
     <option value='22'>22</option>
     <option value='23'>23</option>
     <option value='24'>24</option>
     <option value='25'>25</option>
     <option value='26'>26</option>
     <option value='27'>27</option>
     <option value='28'>28</option>
     <option value='29'>29</option>
     <option value='30'>30</option>

</select></div></center></td>

</tr>
<tr>
<td colspan="2" valign="middle" style="text-align:center; vertical-align:middle;">
<center><?php if ($room2_status== 'On'){ ?><center><label><h4 style="color:White; background:gray;">Room Light is On and Tempratue is <?php echo $room2_temp?></h4></label></center><?php }else{ ?><center><label><h4>Room Light is Off</h4></label></center><?php } ?></center>
</td></tr>

</table>
</div>
<div class="group3">
<h3>Room 3:</h3> 
<table>
<tr>
<td aligh="center" valign="middle" style="text-align:center; vertical-align:middle;">
<center>
<button class="btn" name="LightOn3" id="LightOn3" <?php if ($room3_status== 'On'){ ?> value="Light is On and this button is disabled" disabled style=" WIDTH: 140px; HEIGHT: 60px;" <?php   }else{ ?> value="On" style="WIDTH: 120px; HEIGHT: 60px;"<?php } ?>><?php if ($room3_status== 'On'){ ?>Light is On and this button is disabled"<?php   }else{ ?><h3>Light On</h3><?php } ?></button>
</center>

</td>
<td align="center"  style="text-align:center; vertical-align:middle;">

<button class="btn" name="LightOff3" id="LightOff3" <?php if ($room3_status== 'Off'){ ?> value="Light is Off and this button is disabled" disabled style=" WIDTH: 140px; HEIGHT: 60px;" <?php   }else{ ?> value="On" style="WIDTH: 120px; HEIGHT: 60px;"<?php } ?>><?php if ($room3_status== 'Off'){ ?>Light is Off and this button is disabled"<?php   }else{ ?><h3>Light Off</h3><?php } ?></button>

<br/>
</td>
</tr>
<tr><td colspan="2"  style="text-align:center; vertical-align:middle;">
<center><label for='temp3' <?php if ($room3_status== 'On'){ ?>style="display:none;"<?php   }?>><h4>Select a Tempratue: </h4></label>
 <div class='select' <?php if ($room3_status== 'On'){ ?>style="display:none;"<?php   }?>>
  <select  id='temp3'  name='temp3'  onchange='get_form(this).submit(); return false;' <?php if ($room3_status== 'On'){ ?>disabled<?php   }?>>
     <option value='Off'>Off</option>
     <option value='20'>20</option>
     <option value='21'>21</option>
     <option value='22'>22</option>
     <option value='23'>23</option>
     <option value='24'>24</option>
     <option value='25'>25</option>
     <option value='26'>26</option>
     <option value='27'>27</option>
     <option value='28'>28</option>
     <option value='29'>29</option>
     <option value='30'>30</option>

</select></div></center></td>

</tr>
<tr>
<td colspan="2" valign="middle" style="text-align:center; vertical-align:middle;">
<center><?php if ($room3_status== 'On'){ ?><center><label><h4 style="color:White; background:gray;">Room Light is On and Tempratue is <?php echo $room3_temp?></h4></label></center><?php }else{ ?><center><label><h4>Room Light is Off</h4></label></center><?php } ?></center>
</td></tr>

</table>



</div>
</form>

</div>
</article>
</main>
</div>
</div>
</div>

<?php
get_sidebar();
get_footer();
