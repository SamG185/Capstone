<?php
include_once('C:/xampp/htdocs/api/index.php');

if($_SERVER['REQUEST_METHOD'] == "POST"){
	// Get data from the REST client
	
	$classid = isset($_POST['ClassID']) ? mysqli_real_escape_string($conn, $_POST['ClassID']) : "";
	$date = isset($_POST['date']) ? mysqli_real_escape_string($conn, $_POST['date']) : "";
    $present = isset($_POST['present']) ? mysqli_real_escape_string($conn, $_POST['present']) : "";
    $studentid = isset($_POST['StudentID']) ? mysqli_real_escape_string($conn, $_POST['StudentID']) : "";
    $schoolid = isset($_POST['schoolID']) ? mysqli_real_escape_string($conn, $_POST['schoolID']) : "";
    
	// Insert data into database (.= operator concatenates the string) 
    $sql = "INSERT INTO app.attendance(ClassID, date, present, schoolID, StudentID) VALUES ('$classid', '$date', '$present', '$schoolid', '$studentid');"; 
    $post_data_query = mysqli_query($conn, $sql);

    //Check if the request is correct
	if($post_data_query){
		$json = array("status" => 1, "Success" => "Attendance data updated");
	}
	else{
		$json = array("status" => 0, "Error" => "Failure, please try again");
	}
}

//if sent by a method that isn't post
else{
	$json = array("status" => 0, "Info" => "Unavailable request method used.");
}
@mysqli_close($conn);
// Set Content-type to JSON
header('Content-type: application/json');
echo json_encode($json);