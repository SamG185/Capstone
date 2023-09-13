<?php
include_once('C:/xampp/htdocs/api/index.php');
if($_SERVER['REQUEST_METHOD'] == "POST"){
	// Get data from the REST client
	$contact = isset($_POST['Contact']) ? mysqli_real_escape_string($conn, $_POST['Contact']) : "";
	$firstname = isset($_POST['FirstName']) ? mysqli_real_escape_string($conn, $_POST['FirstName']) : "";
    $childid = isset($_POST['childID']) ? mysqli_real_escape_string($conn, $_POST['childID']) : "";
    $secondname = isset($_POST['SecondName']) ? mysqli_real_escape_string($conn, $_POST['SecondName']) : "";

	// Insert data into database (.= operator concatenates the string)
	$sql = "SET FOREIGN_KEY_CHECKS=0;"; 
    $sql .= "INSERT INTO app.parent(FirstName, SecondName, Contact, childID) VALUES ('$firstname', '$secondname','$contact','$childid');"; 
    $sql .= "SET FOREIGN_KEY_CHECKS=1;";
    $post_data_query = mysqli_multi_query($conn, $sql);

    //Check if the request is correct
	if($post_data_query){
		$json = array("status" => 1, "Success" => "New parent added successfully");
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