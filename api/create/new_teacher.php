<?php
include_once('C:/xampp/htdocs/api/index.php');
if($_SERVER['REQUEST_METHOD'] == "POST"){
	// Get data from the REST client
	$contact = isset($_POST['Contact']) ? mysqli_real_escape_string($conn, $_POST['Contact']) : "";
	$firstname = isset($_POST['FirstName']) ? mysqli_real_escape_string($conn, $_POST['FirstName']) : "";
    $schoolid = isset($_POST['schoolID']) ? mysqli_real_escape_string($conn, $_POST['schoolID']) : "";
    $secondname = isset($_POST['SecondName']) ? mysqli_real_escape_string($conn, $_POST['SecondName']) : "";

	// Insert data into database
	$sql = "INSERT INTO app.teacher(Contact, FirstName, schoolID, SecondName) VALUES ('$contact', '$firstname', '$schoolid', '$secondname');";
	$post_data_query = mysqli_query($conn, $sql);
	if($post_data_query){
		$json = array("status" => 1, "Success" => "New teacher added successfully");
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