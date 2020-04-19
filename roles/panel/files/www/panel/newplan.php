<?php include("inc/header.php");

if (isset($_POST['submit'])) {
	$name = mysqli_real_escape_string($con,$_POST['name']);
	$cost = mysqli_real_escape_string($con,$_POST['cost']);
	$details = mysqli_real_escape_string($con,$_POST['details']);

	$query = "INSERT INTO plans (plan, cost, details) VALUES ('$name', '$cost', '$details');";
	if (mysqli_query($con, $query)) {
		echo '<div id="status" class="alert alert-success" role="alert">Plan created successfully</div>';
	} else {
		echo '<div id="status" class="alert alert-danger" role="alert">Error creating plan: ' . mysqli_error($con) .'</div>';
	}
} 
?>

<div>
	<div class='panel panel-info'>
		<form class="form-horizontal" action="" method="post">
			<div class='panel-heading'>
				<h3 class='panel-title'><span style='font-size: 20pt;'>New Plan</span></h3>
			</div>
			<div class='panel-body'>
				<div class='col-12 col-md-12'>
					<div class='well well-sm'><Strong>Name: </Strong><br /><input name="name" class="form-control"/></div>
					<div class='well well-sm'><Strong>Cost: </Strong><br /><input name="cost" type="number" step="0.01" class="form-control"/></div>				
					<div class='well well-sm' style='height:180px;'><Strong>Details: </Strong><br/><textarea id="notes" name="details" class="form-control"></textarea></div>
					<input id="submitnew" type="submit" value="Submit" name="submit" style="vertical-align: bottom;" class="btn btn-success"/>
				</div>
			</div>
		</form>
	</div>
</div>		
<?php
include("inc/footer.php"); 
?>