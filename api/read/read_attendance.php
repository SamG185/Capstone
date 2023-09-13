<?php
//include connection data
	include_once('C:/xampp/htdocs/api/index.php');
    //assign the value sent by the get request to $id
	$id = isset($_GET['studentid']) ? mysqli_real_escape_string($conn, $_GET['studentid']) :  "";
	$classID = isset($_GET['classID']) ? mysqli_real_escape_string($conn, $_GET['classID']) :  "";
    //create the sql statement with the sent id data. 
	$sql = "SELECT * FROM app.attendance INNER JOIN app.student ON student.id = attendance.StudentID INNER JOIN app.class ON attendance.ClassID = class.id WHERE studentID='{$id}' AND classID='{$classID}';";

    //query the server using the sql statement above.
	$get_data_query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($get_data_query)!=0){
		$result = array();
		
		while($r = mysqli_fetch_array($get_data_query)){
			extract($r);
			$result[] = array("Student_ID" => $StudentID, "Present" => $present, 'Class_ID' => $ClassID, 'Class_Name' => $Name, 'Date' => $date, 'First_name' => $FirstName, 'Last_name' => $SecondName);
		}
		$json = array("status" => 1, "info" => $result);
	}
	else{
		$json = array("status" => 0, "error" => "Attendance record not found!");
	}
@mysqli_close($conn); 
// Set Content-type to JSON
header('Content-type: application/json');
echo json_encode($json);