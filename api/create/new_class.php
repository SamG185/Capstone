<?php
include_once('C:/xampp/htdocs/api/index.php');
if($_SERVER['REQUEST_METHOD'] == "POST"){
	// Get data from the REST client
	$duration = isset($_POST['Duration']) ? mysqli_real_escape_string($conn, $_POST['Duration']) : "";
	$name = isset($_POST['Name']) ? mysqli_real_escape_string($conn, $_POST['Name']) : "";
    $room = isset($_POST['Room']) ? mysqli_real_escape_string($conn, $_POST['Room']) : "";
    $schoolid = isset($_POST['schoolID']) ? mysqli_real_escape_string($conn, $_POST['schoolID']) : "";
    $starttime = isset($_POST['StartTime']) ? mysqli_real_escape_string($conn, $_POST['StartTime']) : "";
    $teacherID = isset($_POST['teacherID']) ? mysqli_real_escape_string($conn, $_POST['teacherID']) : "";

	// Insert data into database
	$sql = "INSERT INTO app.class(Duration, Name, Room, schoolID, StartTime, teacherID) VALUES ('$duration', '$name', '$room', '$schoolid', '$starttime', '$teacherID');";
	$post_data_query = mysqli_query($conn, $sql);
	if($post_data_query){
		$json = array("status" => 1, "Success" => "New class added successfully");
	}
	else{
		$json = array("status" => 0, "Error" => "Failure, please try again");
	}
}
else{
	$json = array("status" => 0, "Info" => "Unavailable request method used.");
}
@mysqli_close($conn);
// Set Content-type to JSON
header('Content-type: application/json');
echo json_encode($json);