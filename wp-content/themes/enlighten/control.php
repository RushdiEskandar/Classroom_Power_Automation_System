 <?php
/*
Template Name: Smart Control
*/
 get_header();
   ?>




<!--Requirement jQuery-->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<!--Load Script and Stylesheet -->
	<script type="text/javascript" src="jquery.simple-dtpicker.js"></script>
	<link type="text/css" href="jquery.simple-dtpicker.css" rel="stylesheet" />
	<!---->
  <style>
 

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
				border: 3px solid #2c3e50;
				margin: 20px;
				border-radius: 3px;
padding-top:20px;
padding-left:100px;
padding-right:120px;

padding-bottom:20px;

			}	

  </style>


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
   
   $sql = 'SELECT room_id, room_name FROM rooms';
   mysql_select_db('ControlDB');
   $retval = mysql_query( $sql, $conn );
   
   if(! $retval ) {
      die('Could not get data: ' . mysql_error());
   }
echo "<form method='POST' >
<h1>Schedule a Control</h1>
   
<div class='group1'>

<table><tr><td>
<label for='room_name'><h4>Select a Room : </h4></label>
 <div class='select'>
  <select  id='room_name'  name='room_name'  onchange='get_form(this).submit(); return false;'>
     <option value='1'>Select Room</option>


";
   while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) { 

       echo "<option value='".$row['room_id']."'>".$row['room_name']."</option>";

}


   echo "</select></div></td>


<td>
<label for='temp'><h4>Select a Tempratue: </h4></label>
 <div class='select'>
  <select  id='temp'  name='temp'  onchange='get_form(this).submit(); return false;'>
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

</select></div></td>

</tr>
	

<tr>
<td><br/>
<h4>Start time:</h4>
<input type='datetime-local' name='startdate' name='startdate' pattern='(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}T[0-9]{2}:[0-9]{2}' >

</td>
<td><br/>
<h4>End time:</h4>
<input type='datetime-local' name='enddate' name='enddate' pattern='(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}T[0-9]{2}:[0-9]{2}' >

</td></tr>
<tr>
<td><br/>
  <input type='submit' name='submit' value='Submit' style='height:40px; width:120px' />
</td></tr>





</table>
</div>

</form>
";

     echo '<script type="text/javascript">
        //<![CDATA[
        function get_form( element )
        {
            while( element )
            {
                element = element.parentNode
                if( element.tagName.toLowerCase() == "form" )
                {
                    return element
                }
            }
            return 0; //error: no form found in ancestors
        }
        //]]>
    </script>';
   
   mysql_close($conn);
?>




 <?php

if(isset($_POST['submit']))
{include_once 'dbconfig.php';

 // make value
	// variables for input data
$room = $_POST['room_name'];

if($room > 1) {
	
$end_time = $_POST['enddate'];
$start_time = $_POST['startdate'];
$temp= $_POST['temp'];

if($start_time != '') {
	if($end_time != '') {

 $dbhost = '127.0.0.1:3306';
   $dbuser = 'root';
   $dbpass = 'rushdi';
   
   $conn = mysql_connect($dbhost, $dbuser, $dbpass);
   
   if(! $conn ) {
      die('Could not connect: ' . mysql_error());
   }
$sql = 'SELECT * FROM rooms where room_id='.$room;
   mysql_select_db('ControlDB');
   $retval = mysql_query( $sql, $conn );
   
   if(! $retval ) {
      die('Could not get data: ' . mysql_error());
   }

  
   while($row = mysql_fetch_assoc($retval)) {
$room_name = $row['room_name'];
}


//$start_time=date('Y-m-d\TH:i:s');
$start_time=str_replace("T"," ",$start_time).":00";
$end_time =str_replace("T"," ",$end_time ).":00";
$today = date("Y-m-d H:i:00");

//$sql2 = "SELECT * FROM ControlRoom where room_name='". $room_name ."' and cntrl_time >='". $start_time ."' and  cntrl_time<='". $end_time ."'";
//$sql2 = "SELECT COUNT(room_id) as count FROM ControlRoom where room_name='". $room_name ."' and cntrl_time >='".$start_time."'";
  

$sql2 = "SELECT room_id as sch_no FROM ControlRoom where room_name='". $room_name ."' and cntrl_time between '". $start_time ."' and '". $end_time ."'";


   mysql_select_db('ControlDB');
   $retval2 = mysql_query( $sql2, $conn );

$num_rows_start=0;

    while($row = mysql_fetch_assoc($retval2)) {
$sch_no= $row['sch_no'];
$num_rows_start=$num_rows_start+1;
}


   if(! $retval2 ) {
      die('Could not get data: ' . mysql_error());
   }

if ($start_time> $today AND  $end_time> $today)  {
			if ($start_time< $end_time) {

if ($num_rows_start> 0) {

?>
		<script type="text/javascript">
		alert('Sorry, There is some conflicts in the start time, remove some of them and check Schedule No. <?php echo $sch_no ?> ');
		</script>
		<?php	
}elseif ($num_rows_start <= 0) {


$sql4 = "SELECT room_id as sch_no2 FROM ControlRoom where room_name='". $room_name ."' and end_time between '". $start_time ."' and '". $end_time ."'";


   mysql_select_db('ControlDB');
   $retval3 = mysql_query( $sql4, $conn );
$sch_no2= $row['sch_no2'];

$num_rows_end=0;

    while($row = mysql_fetch_assoc($retval3)) {
$sch_no2= $row['sch_no2'];
$num_rows_end=$num_rows_end+1;
}


   if(! $retval3 ) {
      die('Could not get data: ' . mysql_error());
   }




if ($num_rows_end> 0) {

?>
		<script type="text/javascript">
		alert('Sorry, There is some conflicts in the end time, remove some of them and check Schedule No. <?php echo $sch_no2 ?> ');
		</script>
		<?php	
}elseif ($num_rows_end <= 0){



	
// variables for input data
	

	// sql query for inserting data into database
	$sql_query = "INSERT INTO ControlRoom(room_name,cntrl_time,end_time,temp) VALUES('$room_name','$start_time','$end_time','$temp')";
	// sql query for inserting data into database
	
	// sql query execution function
	if(mysql_query($sql_query))
	{
		?>
		<script type="text/javascript">
		alert('Data Are Inserted Successfully ');
		window.location.href='http://192.168.0.100/rushdi/index.php/new-schedule/';
		</script>
		<?php
	}
	else
	{
		?>
		<script type="text/javascript">
		alert('error occured while inserting your data');
		</script>
		<?php
	}
	// sql query execution function



}
	}}else{
		?>
		<script type="text/javascript">
		alert('"Start" Date&Time must be greater than the "End" Date&Time !');
		</script>
		<?php	
	}
	}else{
		
		?>
		<script type="text/javascript">
		alert('You must select a "Start or End" Date&Time greater than Today, Check them!');
		</script>
		<?php	
	}
	}else{
?>
		<script type="text/javascript">
		alert('You must select an End Date !');
		</script>
		<?php	

}
}else{
?>
		<script type="text/javascript">
		alert('You must select a Start Date !');
		</script>
		<?php	

}

}else{
?>
		<script type="text/javascript">
		alert('You must select a room first !');
		</script>
		<?php	

}
}

	
 ?>




</div>
</article>
</main>
</div>
</div>
</div>


<?php

get_sidebar();
get_footer();
