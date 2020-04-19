<?php include("inc/header.php");

if (isset($_POST['submit'])) {
	$name = mysqli_real_escape_string($con,$_POST['name']);
	$starlingid = mysqli_real_escape_string($con,$_POST['starlingid']);
        $plex = mysqli_real_escape_string($con,$_POST['plex']);
        $cost = mysqli_real_escape_string($con,$_POST['cost']);
	$date = mysqli_real_escape_string($con,$_POST['date']);
	$notes = mysqli_real_escape_string($con,$_POST['notes']);
	$array = $_POST['plans'];

	$total_array = [];
	foreach ($array as $arr) {
		if (array_key_exists($arr['id'], $total_array) == FALSE) $total_array[$arr['id']] = 0;
		$total_array[$arr['id']] = ($total_array[$arr['id']] + $arr['quantity']);
	}

	$query = "INSERT INTO users (name, starlingid, notes, plex) VALUES ('$name', '$starlingid', '$notes', '$plex');";
	if (mysqli_query($con, $query)) {
		$id = mysqli_insert_id($con);
	} else {
		echo '<div id="status" class="alert alert-danger" role="alert">Error creating user: ' . mysqli_error($con) .'</div>';
		die();
	}
	foreach ($total_array as $planid => $quantity) {	
		$planid = mysqli_real_escape_string($con,$planid);
		$planqty = mysqli_real_escape_string($con,$quantity);
		$query = "INSERT INTO user_plans (user_id, plan_id, quantity) VALUES ('$id', '$planid', '$planqty');";
		if (!mysqli_query($con, $query)) {
			echo '<div id="status" class="alert alert-danger" role="alert">Error assigning plans to user: ' . mysqli_error($con) .'</div>';
			die();
		}
	}
	echo '<div id="status" class="alert alert-success" role="alert">User updated successfully</div>';
} 
$id = mysqli_real_escape_string($con,$_POST['id']);
$query = "SELECT * FROM `plans`;";
$planHTML .= "<select class='form-control' name=plans[0][id]>";
if ($result = mysqli_query($con, $query)) {
	while ($allplans = mysqli_fetch_assoc($result)) {					
		$planHTML .= "<option value=".$allplans['id'].">".$allplans['plan']."</option>";
	}
}
$planHTML .= "</select>";
?>
<div>
	<div class='panel panel-info'>
		<form class="form-inline" action="" method="post">
			<div class='panel-heading'>
				<h3 class='panel-title'><span style='font-size: 20pt;'>New User</span></h3>
			</div>
			<div class='panel-body'>
				<div class='col-12 col-md-12'>
					<div class='well well-sm'><Strong>Name: </Strong><br /><input name="name" class="form-control fullwidthinput"/></div>
					<div class='well well-sm'><Strong>Starling Account ID: </Strong><br /><input name="starlingid" class="form-control fullwidthinput"/></div>
					<div class='well well-sm'><Strong>Plex Username: </Strong><br /><input name="plex" class="form-control fullwidthinput"/></div>
					<div class='well well-sm plans'><Strong>Plans: </Strong>
							<div class="plan">
								<div class="row">
									<div class=" col-12 col-md-12">
										<?=$planHTML?>
										<input class="form-control" type="number" step="0.01" data-id="<?=$plan['qty']?>" name="plans[0][quantity]" value="0"/></span>
									</div>
								</div>
							</div>
						<span id="planbuttons-new">
							<button style="vertical-align: bottom;" type="button" id="add" class="btn btn-success">Add</button>
							<button style="vertical-align: bottom; display:none !important;" type="button" id="remove"  class="btn btn-danger">Remove</button>	
						</span>
					</div>
					<div class='well well-sm' style='height:280px;'><Strong>Notes: </Strong><br/><textarea id="notes" name="notes" class="form-control"></textarea></div>
					<input id="submitnew" type="submit" value="Submit" name="submit" style="vertical-align: bottom;" class="btn btn-success"/>
				</div>
			</div>
		</form>
	</div>
</div>		
<?php
include("inc/footer.php"); 
?>
