<?php include("inc/header.php"); 
if (!is_numeric($_GET['id']) && !is_numeric($_POST['id'])) {
	die('DEAD');
}
$id = mysqli_real_escape_string($con,$_POST['id']);
if (isset($_POST['submit'])) {
	$name = mysqli_real_escape_string($con,$_POST['name']);
	$id = mysqli_real_escape_string($con,$_POST['id']);
	$cost = mysqli_real_escape_string($con,$_POST['cost']);
	$details = mysqli_real_escape_string($con,$_POST['details']);
	$array = $_POST['plans'];
	$enabled = isset($_POST['enabled']) ? 1 : 0;
	$query = "UPDATE plans SET plan='$name', cost='$cost', details='$details' WHERE id=$id;";
	if (mysqli_query($con, $query)) {
		echo '<div id="status" class="alert alert-success" role="alert">Plan updated successfully</div>';
	} else {
		echo '<div id="status" class="alert alert-danger" role="alert">Error updating Plan: ' . mysqli_error($con) .'</div>';
	}
} 
	$id = mysqli_real_escape_string($con,$_GET['id']);
	$query = "SELECT * FROM plans WHERE id = $id LIMIT 1;";
	if ($result = mysqli_query($con, $query)) {
		while ($plan = mysqli_fetch_assoc($result)) {
			
			$status = $plan['enabled'] == 1 ? "Yes" : "No";
?>
<div>
	<div class='panel panel-info'>
		<form style="float: right;" action="<?=$webpath?>/inc/delete.php" method="post">
			<input id="delete" onclick="return confirm('Are you sure you want to delete this plan?');" type="submit" value="Delete" name="delete" style="vertical-align: bottom;" class="btn btn-danger btn-lg"/>
			<input type="hidden" name="id" value="<?=$plan['id']?>"/>
			<input type="hidden" name="type" value="plan"/>
		</form>
		<form class="form-horizontal" action="" method="post">
			<input name="id" type="hidden" value="<?=$plan['id']?>"/>
			<div class='panel-heading'>
				<h3 class='panel-title'><span style='font-size: 20pt;'>Plan: <?=$plan['plan']; ?></span> &nbsp; <button style="vertical-align: bottom;" type="button" id="edit" class="btn btn-primary">Edit</button>&nbsp;<input id="submit" type="submit" value="Submit" name="submit" style="vertical-align: bottom;" class="btn btn-success"/> &nbsp; <button type="button" id="cancel" style="vertical-align: bottom;" class="btn btn-danger">Cancel</button></h3>
			</div>
			<div class='panel-body'>
				<div class='well well-sm'><Strong>Name: </Strong><span name="name" class="editable"><?=$plan['plan']?></span></div>
				<div class='well well-sm'><Strong>Cost: </Strong>£<span name="cost" class="editablenum"><?=number_format($plan['cost'],2)?></span></div>
				<div class='well well-sm'><Strong>Enabled: </Strong><span name="enabled" class="editablecheckbox"><?=$status?></span></div> 
				<div class='well well-sm' style='height:280px;'><Strong>Notes: </Strong><br/><span name="details" class="editabletextarea"><?=$plan['details']?></span></div>
			</div>
		</form>
	</div>
</div>		
<?php }
}
include("inc/footer.php"); 
?>