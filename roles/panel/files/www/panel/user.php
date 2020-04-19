<?php include("inc/header.php");
if (!is_numeric($_GET['id']) && !is_numeric($_POST['id'])) {
	die('DEAD');
}
$id = mysqli_real_escape_string($con,$_POST['id']);

if (isset($_POST['submit'])) {
	$name = mysqli_real_escape_string($con,$_POST['name']);
	$id = mysqli_real_escape_string($con,$_POST['id']);
	$starlingid = mysqli_real_escape_string($con,$_POST['starlingid']);
	$cost = mysqli_real_escape_string($con,$_POST['cost']);
	$date = mysqli_real_escape_string($con,$_POST['date']);
	$notes = mysqli_real_escape_string($con,$_POST['notes']);
        $plex = mysqli_real_escape_string($con,$_POST['plex']);
	$enabled = isset($_POST['enabled']) ? 1 : 0;
	$array = $_POST['plans'];
	
	$total_array = [];
	foreach ($array as $arr) {
		if (array_key_exists($arr['id'], $total_array) == FALSE) $total_array[$arr['id']] = 0;
		$total_array[$arr['id']] = ($total_array[$arr['id']] + $arr['quantity']);
	}
	
	$query = "DELETE FROM user_plans WHERE user_id=$id";
	if (!mysqli_query($con, $query)) echo '<div id="status" class="alert alert-danger" role="alert">Error updating plans: ' . mysqli_error($con);

	foreach ($total_array as $planid => $quantity) {	
		$planid = mysqli_real_escape_string($con,$planid);
		$planqty = mysqli_real_escape_string($con,$quantity);
		$query = "INSERT INTO user_plans (user_id, plan_id, quantity) VALUES ('$id', '$planid', '$planqty');";
		if (!mysqli_query($con, $query)) echo '<div id="status" class="alert alert-danger" role="alert">Error updating plans: ' . mysqli_error($con);
	}

	$query = "UPDATE users SET name='$name', starlingid='$starlingid', notes='$notes', plex='$plex',  enabled='$enabled' WHERE id=$id;";
	if (mysqli_query($con, $query)) {
		echo '<div id="status" class="alert alert-success" role="alert">User updated successfully</div>';
	} else {
		echo '<div id="status" class="alert alert-danger" role="alert">Error updating User: ' . mysqli_error($con) .'</div>';
	}
}

	$id = mysqli_real_escape_string($con,$_GET['id']);
	$query = "SELECT * FROM users WHERE id = $id LIMIT 1;";
	if ($result = mysqli_query($con, $query)) {
		while ($user = mysqli_fetch_assoc($result)) {
			$query = "SELECT * FROM `plans`;";
			//$sqltran = mysqli_query($con, $query);
			$planHTML .= "<select id='placeholder' class='form-control' name=".$allplans['plan_id'].">";
			if ($result = mysqli_query($con, $query)) {
				while ($allplans = mysqli_fetch_assoc($result)) {
					
					$planHTML .= "<option value=".$allplans['id'].">".$allplans['plan']."</option>";
					
				}
			}
			$planHTML .= "</select>";
			$query = "SELECT * FROM `transactions` WHERE `sendingContactId` = '".$user['starlingid']."' ORDER BY `created` DESC;";
			$sqltran = mysqli_query($con, $query);
			$transactions = mysqli_fetch_all($sqltran, MYSQLI_ASSOC);
			foreach ($transactions as $transaction ) {
				
				$first = mktime(0,0,0,date('n'),$startdate,date('Y'));
				$last = mktime(23,59,59,(date('n') + 1 ), $finishdate,date('Y'));
				if ($transaction['created'] > $first && $transaction['created'] < $last){
				$totalpaidthismonth += $transaction['amount'];
				}
				
				$transactionhtml .= 
				"<tr>
					<td>".formatDate($transaction['created'])."</td>
					<td >£".number_format($transaction['amount'],2)."</td>
				</tr>";
			}
?>
<div>
	<div class='panel panel-info'>
		<form style="float: right;" action="/delete.php" method="post">
			<input id="delete" onclick="return confirm('Are you sure you want to delete this user?');" type="submit" value="Delete" name="delete" style="vertical-align: bottom;" class="btn btn-danger btn-lg"/>
			<input type="hidden" name="id" value="<?=$user['id']?>"/>
			<input type="hidden" name="type" value="user"/>
		</form>
		<form class="form-inline" action="" method="post">
			<input name="id" type="hidden" value="<?=$user['id']?>"/>
			<div class='panel-heading'>
				<h3 class='panel-title'><span style='font-size: 20pt;'>User: <?=$user['name']; ?></span> &nbsp; <button style="vertical-align: bottom;" type="button" id="edit" class="btn btn-primary">Edit</button>&nbsp;<input id="submit" type="submit" value="Submit" name="submit" style="vertical-align: bottom;" class="btn btn-success"/> &nbsp; <button type="button" id="cancel" style="vertical-align: bottom;" class="btn btn-danger">Cancel</button> &nbsp; <a style="color:white; vertical-align: bottom;" type="button" href="https://api.coolflix.stream/public/<?=$user['plex']?>" id="edit" class="btn btn-primary"><?=$user['plex']?>'s Prices</a></h3>
			</div>
			<div class='panel-body'>
				<div class='col-12 col-md-6'>
					<div class='well well-sm'><Strong>Name: </Strong><span name="name" class="editable"><?=$user['name']?></span></div>
					<div class='well well-sm'><Strong>Starling ID: </Strong><span name="starlingid" class="editable"><?=$user['starlingid']?></span></div>
                                        <div class='well well-sm'><Strong>Plex Username: </Strong><span name="plex" class="editable"><?=$user['plex']?></span></div>
					<div class='well well-sm plans'><Strong>Plans: </Strong>
					<?=$planHTML?>
					<?php 						
						$query = "SELECT * FROM user_plans INNER JOIN plans ON plans.id=user_plans.plan_id WHERE user_id = ".$user['id'].";";
						$sqltran = mysqli_query($con, $query);
						$plans = mysqli_fetch_all($sqltran, MYSQLI_ASSOC);
						$i = 0;
						foreach ($plans as $plan ) {
							$i++;							
							$totalcost += $plan['cost'] * $plan['quantity'];
							$status = $user['enabled'] == 1 ? "Yes" : "No";
					?>
							<div class="plan">
								<div class="row">
									<div class=" col-12 col-md-6">
										<span class="plan-name inline" data-id="<?=$plan['id']?>" name="plans[<?=$i?>][id]"><?=$plan['plan']?></span>
										<span class="inline badge editablenum" data-id="<?=$plan['qty']?>" name="plans[<?=$i?>][quantity]"><?=$plan['quantity']?></span>
									</div>
								</div>
							</div>
					<?php
						}
					?>
						<span id="planbuttons">
							<button style="vertical-align: bottom;" type="button" id="add" class="btn btn-success">Add</button>
							<button style="vertical-align: bottom;" type="button" id="remove" class="btn btn-danger">Remove</button>	
						</span>
					</div>
					<div class='well well-sm'><Strong>Monthly Cost: </Strong> £<span name="cost" ><?=$totalcost?></span></div>
					<div class='well well-sm'><Strong>Paid This Month: </Strong> £<span name="amount" class=""><?=$totalpaidthismonth?></span></div>	
					<div class='well well-sm'><Strong>Fully Paid: </Strong><span name="amount" class=""><?=HasPaid($totalpaidthismonth,$totalcost)?></span></div>	
					<div class='well well-sm'><Strong>Last Payment Date: </Strong><span name="date" class=""><?=formatDate($user['lastpaymentdate'])?></span></div> 
					<div class='well well-sm'><Strong>Enabled: </Strong><span name="enabled" class="editablecheckbox"><?=$status?></span></div> 
					<div class='well well-sm' style='height:280px;'><Strong>Notes: </Strong><br/><span name="notes" class="editabletextarea"><?=$user['notes']?></span></div>
				</div>
				<div class='col-12 col-md-6 well well-sm'>
					<h4>Transaction History</h4> 
					<table class='table'>
						<thead>
						  <tr>
							<th class='col-lg-4'>Payment Date</th>
							<th class='col-lg-4'>Payment Amount</th>
						 </tr>
						</thead>
						<tbody>
							<?=$transactionhtml?>
						</tbody>
					</table>
				</div>
			</div>
		</form>
	</div>
</div>		
<?php }
}
include("inc/footer.php"); 
?>
