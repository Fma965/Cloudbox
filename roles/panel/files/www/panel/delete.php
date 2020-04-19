<?php 
include("../config.php"); 

// Check if POST contains id, if it does checks what $_POST['type'] is set to then deletes the corresponding entry from the database.
if (is_numeric($_POST['id'])) {

	$id = mysqli_real_escape_string($con,$_POST['id']);

	switch ($_POST['type']) {
		case "user":
			$query = "DELETE FROM user_plans WHERE user_id = $id;";
			if (!mysqli_query($con, $query)) {
				header("Location: /?error");
			}
			
			$query = "DELETE FROM users WHERE id = $id;";
			break;
		case "plan":
			$query = "DELETE FROM plans WHERE id = $id;";
			break;
		case "expense":
		   $query = "DELETE FROM expenses WHERE id = $id;";
			break;
		default:
			die("Error");
	}
	
	
	if (mysqli_query($con, $query)) {
		header("Location: /?success");
	} else {
		header("Location: /?error");
	}
}
?>