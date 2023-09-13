<?php
include_once('C:/xampp/htdocs/api/index.php');
if($_SERVER['REQUEST_METHOD'] == "POST"){
	// Get data from the REST client
	$contact = isset($_POST['Contact']) ? mysqli_real_escape_string($conn, $_POST['Contact']) : "";
	$firstname = isset($_POST['FirstName']) ? mysqli_real_escape_string($conn, $_POST['FirstName']) : "";
    $secondname = isset($_POST['SecondName']) ? mysqli_real_escape_string($conn, $_POST['SecondName']) : "";
    $dob = isset($_POST['DOB']) ? mysqli_real_escape_string($conn, $_POST['DOB']) : "";
    $parentid = isset($_POST['parentID']) ? mysqli_real_escape_string($conn, $_POST['parentID']) : "";
    $schoolid = isset($_POST['schoolID']) ? mysqli_real_escape_string($conn, $_POST['schoolID']) : "";
    
	// Insert data into database (.= operator concatenates the string) 
    $sql = "INSERT INTO app.student(Contact, DOB, FirstName, parentID, schoolID, SecondName) VALUES ('$contact', '$dob', '$firstname', '$parentid', '$schoolid', '$secondname');"; 
    $post_data_query = mysqli_query($conn, $sql);

    //Check if the request is correct
	if($post_data_query){
		$json = array("status" => 1, "Success" => "New student added successfully");
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