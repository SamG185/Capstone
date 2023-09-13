<?php
include_once('C:/xampp/htdocs/api/index.php');
if($_SERVER['REQUEST_METHOD'] == "POST"){
	// Get data from the REST client
	$contact = isset($_POST['Contact']) ? mysqli_real_escape_string($conn, $_POST['Contact']) : "";
	$name = isset($_POST['schName']) ? mysqli_real_escape_string($conn, $_POST['schName']) : "";

	// Insert data into database
	$sql = "INSERT INTO app.school(schName,Contact) VALUES ('$name', '$contact');";
	$post_data_query = mysqli_query($conn, $sql);
	if($post_data_query){
		$json = array("status" => 1, "Success" => "New school added successfully");
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