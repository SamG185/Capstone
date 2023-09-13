<?php
include_once('C:/xampp/htdocs/api/index.php');


if($_SERVER['REQUEST_METHOD'] == "POST"){
	// Get data from the REST client
	$username = isset($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : "";
	$password = isset($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : "";
 
	
    $sql = "SELECT * FROM `auth` WHERE auth.user='".$username."';"; 
    $post_data_query = mysqli_query($conn, $sql);


    //Check if the request is correct
	if($post_data_query){
		//$json = array("status" => 1, "Success" => "Attendance data updated");
		$rows = mysqli_fetch_all($post_data_query, MYSQLI_NUM);
		
		$check = $rows[0][1];
		$cat = $rows[0][2];
		
		
		if(password_verify($password, $check) && $cat == 1){
			$json = array("status" => 1, "category" => "student", "id" => $rows[0][4]);
			
		}
		else if (password_verify($password, $check) && $cat == 2){

			$json = array("status" => 1, "category" => "parent", "id" => $rows[0][3]);
		}
		else if (password_verify($password, $check) && $cat == 3){

			$json = array("status" => 1, "category" => "teacher", "id" => $rows[0][5]);
		}
		else{
			$json = array("status" => 0, "category" => null);
		}
		
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