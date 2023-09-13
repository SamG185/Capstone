<?php
//include connection data
	include_once('C:/xampp/htdocs/api/index.php');
    //assign the value sent by the get request to $id
	$parentid = isset($_GET['parentid']) ? mysqli_real_escape_string($conn, $_GET['parentid']) :  "";
	
    //create the sql statement with the sent id data. 
	$sql = "SELECT * FROM app.student WHERE parentID='{$parentid}';";

    //query the server using the sql statement above.
	$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)!=0){
		$result = array();
		
		while($r = mysqli_fetch_array($get_data_query)){
			extract($r);
			$result[] = array("FirstName" => $FirstName, "SecondName" => $SecondName, 'ID' => $id, 'DOB' => $DOB, 'Contact' => $Contact, 'SchoolID' => $schoolID);
		}
		$json = array("status" => 1, "data" => $result);
	}
	else{
		$json = array("status" => 0, "error" => "Student record not found!");
	}
@mysqli_close($conn); 
// Set Content-type to JSON
header('Content-type: application/json');
echo json_encode($json);