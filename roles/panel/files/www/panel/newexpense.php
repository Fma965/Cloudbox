<?php include("inc/header.php");

if (isset($_POST['submit'])) {
	$name = mysqli_real_escape_string($con,$_POST['name']);
	$cost = mysqli_real_escape_string($con,$_POST['cost']);
	$details = mysqli_real_escape_string($con,$_POST['details']);
	$currency = mysqli_real_escape_string($con,$_POST['currency']);
	if (!empty($_POST['date'])) {
		$date = mysqli_real_escape_string($con,$_POST['date']);
		$monthly = 1;
	} else {
		$date = 0;
		$monthly = 0;
	}

	$query = "INSERT INTO expenses (expense, cost, date, currency, details, monthly) VALUES ('$name', '$cost', '$date', '$currency', '$details', '$monthly');";
	if (mysqli_query($con, $query)) {
		echo '<div id="status" class="alert alert-success" role="alert">Expense created successfully</div>';
	} else {
		echo '<div id="status" class="alert alert-danger" role="alert">Error creating expense: ' . mysqli_error($con) .'</div>';
	}
} 
?>

<div>
	<div class='panel panel-info'>
		<form class="form-horizontal" action="" method="post">
			<div class='panel-heading'>
				<h3 class='panel-title'><span style='font-size: 20pt;'>New Expense</span></h3>
			</div>
			<div class='panel-body'>
				<div class='col-12 col-md-12'>
					<div class='well well-sm'><Strong>Name: </Strong><br /><input name="name" class="form-control"/></div>
					<div class='well well-sm'><Strong>Cost: </Strong><br /><input name="cost" type="number" step="0.01" class="form-control"/></div>
					<div class='well well-sm'><Strong>Currency: </Strong><br />
						<select id="currencyselectnew" class='form-control' name='currency'>
							<option value="GBP">GBP</option>
							<option value="EUR">EUR</option>
							<option value="USD">USD</option>
						</select>
					</div>				
					<div class='well well-sm'><Strong>Monthly Payment Date: </Strong>(Enter 0 for non recurring payments)<br /><input name="date" type="number" max="31" class="form-control"/></div>					
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