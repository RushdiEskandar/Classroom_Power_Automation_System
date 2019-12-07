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

$sql = "SELECT room_id, room_name, cmd, cntrl_time FROM ControlRoom";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
$today = date("Y-m-d H:i:00");
$date = $row["cntrl_time"];
echo "Today: $today and cntrl: $date<br/>";

if ($date == $today) {
   exec($row["cmd"]);

    }}
} else {
    echo "0 results";
}

mysqli_close($conn);
?>