<?php


$servername = "127.0.0.1";
$username = "root";
$password = "rushdi";
$dbname = "ControlDB";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "SELECT room_id, room_name, cntrl_time,end_time,temp FROM ControlRoom";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
$today = date("Y-m-d H:i:00");
$Starttime = $row["cntrl_time"];
$Endtime = $row["end_time"];
$room_name = $row["room_name"];
$temp = $row["temp"];

	echo "Today: $today and cntrl: $Starttime<br/>";
echo "room_name: $room_name and temp: $temp<br/>";
$sql2 = "SELECT room_id, room_name, PyCmdOn,PyCmdOff  FROM rooms where room_name='$room_name'";
$result2 = mysqli_query($conn, $sql2);
if (mysqli_num_rows($result2) > 0) {
    while($row2 = mysqli_fetch_assoc($result2)) {
$room_name2 = $row2["room_name"];
$cmd_on = $row2["PyCmdOn"];
$cmd_off = $row2["PyCmdOff"];
echo " $cmd_on           $cmd_off";
}
}
else {
    echo "0 results in SELECT room_id, room_name, PyCmdOn,PyCmdOff  FROM rooms where room_name=";
}

if ($Starttime == $today) {
	
	
   exec($cmd_on);
$sql = "UPDATE rooms SET room_status='On', temp='$temp' WHERE room_name='$room_name2'";
if(mysqli_query($conn , $sql)){
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
}
elseif ($Endtime == $today) {
		
   exec($cmd_off);

$sql = "UPDATE rooms SET room_status='Off', temp='0' WHERE room_name='$room_name2'";
if(mysqli_query($conn , $sql)){
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
}
else {
    echo "0 results in Comparison in End";
}

  }
} else {
    echo "No results in SELECT room_id, room_name, cntrl_time,end_time,temp FROM ControlRoom";
}
	


mysqli_close($conn);
?>