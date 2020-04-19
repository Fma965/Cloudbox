<?php 
include("inc/header.php"); 
if (isset($_GET['success'])) {
	echo '<div id="status" class="alert alert-success" role="alert">Action completed successfully</div>';
} elseif (isset($_GET['error'])) {
	echo '<div id="status" class="alert alert-danger" role="alert">Error completing action.</div>';
}
?>
<div class="row">
	<!-- Active Users -->
	<div class='well homesections col-12 col-md-6'>
		<h2>Active Users</h2>		
		<div id="user-table"  class="table">
			 <table class="table table-striped">
				<thead>
					<tr>
						<th>Name</th>
						<th>Plans</th>
						<th>Monthly Charge</th>
						<th>Paid this Month</th>
						<th>Fully Paid</th>
						<th>Last Payment Date</th>
					</tr>
				</thead>
				<tbody>
					<?php	
					$totalpaid = 0;					
					$query = "SELECT * FROM users ORDER by name;";
					$sqltran = mysqli_query($con, $query)or die(mysqli_error($con));
					while ($user = mysqli_fetch_array($sqltran)) {	
						$allplans = [];
						$allplanscost = 0;
						$id = mysqli_real_escape_string($con,$user['id']);						
						$query2 = "SELECT * FROM user_plans INNER JOIN plans ON plans.id=user_plans.plan_id WHERE user_id = $id AND NOT quantity = 0;";
						$sqltran2 = mysqli_query($con, $query2)or die(mysqli_error($con));
						while ($plans = mysqli_fetch_array($sqltran2)) {
							$allplans[] .= $plans['plan'] . " (". $plans['quantity'].")";
							if ($user['enabled']) {
								$allplanscost += $plans['cost'] * $plans['quantity'];
							}
						}
						$first = mktime(0,0,0,date('n'),$startdate,date('Y'));
						$last = mktime(23,59,59,(date('n') + 1 ), $finishdate,date('Y'));
						
						$query3 = "SELECT * FROM transactions WHERE (created BETWEEN $first AND $last) AND sendingContactId = '" . $user['starlingid']."';";
						$sqltran3 = mysqli_query($con, $query3)or die(mysqli_error($con));
						
						$totalpaidthismonth = 0;
						while ($transaction = mysqli_fetch_array($sqltran3)) {
							if ($user['enabled']) {
								$totalpaidthismonth += $transaction['amount'];
								$totalpaid += $transaction['amount'];
							}
						}
						$totalcharge += $allplanscost;
						$allplans = implode(', ', $allplans);

					?>
					<tr class="active-<?=$user['enabled']?>">
						<td><a href='user.php?id=<?=$user['id']?>'><?=$user['name']?></a></td>	
						<td><?=$allplans?></td>
						<td>£<?=number_format($allplanscost,2)?></td>
						<td>£<?=number_format($totalpaidthismonth,2)?></td>
						<td><?=HasPaid($totalpaidthismonth,$allplanscost)?></td>
						<td><?=formatDate($user['lastpaymentdate'])?></td>					
					</tr>
					<?php }	?>
				</tbody>
			</table>
			</div>
			<div id="users-total"><span><strong>Total Monthly Charge:</strong> £<?=$totalcharge?></span><br />
				<span><strong>Total Paid This Month:</strong>
					<?php if($totalpaid >= $totalcharge): ?>
						<span class="paidyes"> £<?=$totalpaid?></span>
					<?php else: ?>
						<span class="paidno"> £<?=$totalpaid?></span> <br />(Payment is below total charge)
					<?php endif; ?>
				</span>
			</div>
	</div>
	
	<!-- Plans -->
	<div class='well homesections col-12 col-md-6'>
		<h2>Available Plans<br /></h2>
		<div class="table" id="plan-table">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Plan</th>
						<th>Cost</th>
						<th>Details</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$query = mysqli_real_escape_string($con,"SELECT * FROM plans;");
				$sqltran = mysqli_query($con, $query)or die(mysqli_error($con));
				
				while ($row = mysqli_fetch_array($sqltran)) {	
				?>
					<tr class="active-<?=$row['enabled']?>">
						<td><a href='plan.php?id=<?=$row['id']?>'><?=$row['plan']?></a></td>
						<td>£<?=number_format(round($row['cost'], 2),2)?></td>
						<td><?=nl2br($row['details'])?></td>
					</tr>
				<?php }	?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="row">

<!-- Monthly Expenses -->
	<div class='well homesections col-12 col-md-6'>
		<h2>Monthly Expenses<br /></h2>
		<div class="table expense-table">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Expense</th>
						<th>Monthly Cost</th>
						<th>Monthly Cost in GBP</th>
						<th>Payment Due Date</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$USDFX = currencyConversion("USD");;
				$EURFX = currencyConversion("EUR");;
				$query = mysqli_real_escape_string($con,"SELECT * FROM expenses WHERE monthly = true ORDER by date;");
				$sqltran = mysqli_query($con, $query)or die(mysqli_error($con));
				
				while ($row = mysqli_fetch_array($sqltran)) {	
						if($row['currency'] == "EUR") {
							$fxamount = number_format(round($row['cost'] * $EURFX + ($row['cost'] / 100), 2),2);
							$symbol = "€";
						} elseif($row['currency'] == "USD") {
							$fxamount = number_format(round($row['cost'] * $USDFX + ($row['cost'] / 100), 2),2);
							$symbol = "$";
						} else {
							$fxamount = number_format($row['cost'],2);
							$symbol = "£";
						}
						if ($row['enabled']) {
						$totalexpenses += $fxamount;
						}
				?>
					
					<tr class="active-<?=$row['enabled']?>">
						<td ><a href='expense.php?id=<?=$row['id']?>'><?=$row['expense']?></a></td>
						<td ><?=$symbol . number_format(round($row['cost'], 2),2) . " " . $row['currency']?></td>
						<td >£<?=$fxamount?></td>
						<td ><?=ordinal_suffix($row['date'])?></td>
					</tr>
					
				<?php }	?>
				</tbody>
			</table>
		</div>
		<div id="expenses-total">
			<span><strong>Total Monthly Expense: </strong>£<?=$totalexpenses?></span><br />
			<span><strong>Monthly Excess:</strong>
				<?php if($totalcharge >= $totalexpenses): ?>
					<span class="paidyes"> £<?=round($totalcharge - $totalexpenses,2);?></span> <br />(Total expense is less than total charge)
				<?php else: ?>
					<span style="paidno"> £<?=round($totalcharge - $totalexpenses,2);?></span> <br />(Payment is below total charge)
				<?php endif; ?>
			</span>
		</div>
	</div>

	<!-- Additional Expenses -->
	<div class='well homesections col-12 col-md-6'>
		<h2>Additional Expenses<br /></h2>
		<div class="table expense-table">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Expense</th>
						<th>Cost</th>
						<th>Cost in GBP</th>
						<th>Details</th>
					</tr>
				</thead>
				<tbody>
			<?php
				$query = mysqli_real_escape_string($con,"SELECT * FROM expenses WHERE monthly = false ORDER by expense;");
				$sqltran = mysqli_query($con, $query)or die(mysqli_error($con));
				
				while ($row = mysqli_fetch_array($sqltran)) {	
					if($row['currency'] == "EUR") {
						$fxamount = number_format(round($row['cost'] * $EURFX + ($row['cost'] / 100), 2),2);
						$symbol = "€";
					} elseif($row['currency'] == "USD") {
						$fxamount = number_format(round($row['cost'] * $USDFX + ($row['cost'] / 100), 2),2);
						$symbol = "$";
					} else {
						$fxamount = number_format($row['cost'],2);
						$symbol = "£";
					}
			?>
					<tr class="active-<?=$row['enabled']?>">
						<td><a href='expense.php?id=<?=$row['id']?>'><?=$row['expense']?></a></td>
						<td><?=$symbol . number_format(round($row['cost'], 2),2) . " " . $row['currency']?></td>
						<td>£<?=$fxamount?></td>
						<td><?=$row['details']?></td>
					</tr>
			<?php }	?>
				</tbody>
			</table>
		</div>
	</div>
</div>
 <?php include("inc/footer.php"); ?>