<?php include("inc/header.php"); 
if (!is_numeric($_GET['id']) && !is_numeric($_POST['id'])) {
	die('DEAD');
}
$id = mysqli_real_escape_string($con,$_POST['id']);
if (isset($_POST['submit'])) {
	$name = mysqli_real_escape_string($con,$_POST['name']);
	$id = mysqli_real_escape_string($con,$_POST['id']);
	if (!empty($_POST['date'])) {
		$date = mysqli_real_escape_string($con,$_POST['date']);
	} else {
		$date = 0;
	}
	$cost = mysqli_real_escape_string($con,$_POST['cost']);
	$currency = mysqli_real_escape_string($con,$_POST['currency']);
	$details = mysqli_real_escape_string($con,$_POST['details']);
	$monthly = (isset($_POST['monthly'])) ? 1 : 0;
	$enabled = isset($_POST['enabled']) ? 1 : 0;
	$array = $_POST['plans'];
	
	$query = "UPDATE expenses SET expense='$name', cost='$cost', date='$date', details='$details', currency='$currency' , monthly='$monthly' , enabled='$enabled' WHERE id=$id;";
	if (mysqli_query($con, $query)) {
		echo '<div id="status" class="alert alert-success" role="alert">Expense updated successfully</div>';
	} else {
		echo '<div id="status" class="alert alert-danger" role="alert">Error updating Expense: ' . mysqli_error($con) .'</div>';
	}
} 
	$id = mysqli_real_escape_string($con,$_GET['id']);
	$query = "SELECT * FROM expenses WHERE id = $id LIMIT 1;";
	if ($result = mysqli_query($con, $query)) {
		
		$USDFX = currencyConversion("USD");
		$EURFX = currencyConversion("EUR"); 
		
		while ($expense = mysqli_fetch_assoc($result)) {
		if($expense['currency'] == "EUR") {
			$fxamount = number_format(round($expense['cost'] * $EURFX + ($expense['cost'] / 100), 2),2);
			$symbol = "€";
		} elseif($expense['currency'] == "USD") {
			$fxamount = number_format(round($expense['cost'] * $USDFX + ($expense['cost'] / 100), 2),2);
			$symbol = "$";
		} else {
			$fxamount = number_format($expense['cost'],2);
			$symbol = "£";
		}
		
		$status = $expense['enabled'] == 1 ? "Yes" : "No";
?>
<div>
	<div class='panel panel-info'>
		<form style="float: right;" action="<?=$webpath?>/inc/delete.php" method="post">
			<input id="delete" onclick="return confirm('Are you sure you want to delete this expense?');" type="submit" value="Delete" name="delete" style="vertical-align: bottom;" class="btn btn-danger btn-lg"/>
			<input type="hidden" name="id" value="<?=$expense['id']?>"/>
			<input type="hidden" name="type" value="expense"/>
		</form>
		<form class="form-horizontal" action="" method="post">
			<input name="id" type="hidden" value="<?=$expense['id']?>"/>
			<div class='panel-heading'>
				<h3 class='panel-title'><span style='font-size: 20pt;'>Expense: <?=$expense['expense']; ?> </span> &nbsp; <button style="vertical-align: bottom;" type="button" id="edit" class="btn btn-primary">Edit</button>&nbsp;<input id="submit" type="submit" value="Submit" name="submit" style="vertical-align: bottom;" class="btn btn-success"/> &nbsp; <button type="button" id="cancel" style="vertical-align: bottom;" class="btn btn-danger">Cancel</button></h3>
			</div>
			<div class='panel-body'>
				<div class='well well-sm'><Strong>Name: </Strong><span name="name" class="editable"><?=$expense['expense']?></span></div>
				<div class='well well-sm'><Strong>Cost: </Strong><?=$symbol?><span name="cost" class="editablenum"><?=number_format($expense['cost'],2)?></span> <span class="fxamount">(£<?=$fxamount?> GBP)</span></div>
				<div class='well well-sm'><Strong>Currency: </Strong>
					<span id="currencytext" name="currency"><?=$expense['currency']?></span>
					<select id="currencyselect" class='form-control' name='currency'>
						<option value="GBP" <?=($expense['currency']=="GBP" ? 'selected' : '')?>>GBP</option>
						<option value="EUR" <?=($expense['currency']=="EUR" ? 'selected' : '')?>>EUR</option>
						<option value="USD" <?=($expense['currency']=="USD" ? 'selected' : '')?>>USD</option>
					</select>
				</div>
				<div class='well well-sm'><Strong>Payment Due Date: </Strong><span name="date" class="editablenum"><?=($expense['date']==0 ? 'N/A' : $expense['date'])?></span><span class="datesuffix"><?=($expense['date']!=0 ? str_replace($expense['date'],'',ordinal_suffix($expense['date'])) : '')?></span></div>
				<div class='well well-sm'><Strong>Monthly Payment: </Strong><input type="checkbox" class="form-check-input" name="monthly" <?=($expense['monthly']==1 ? 'checked' : '')?> value="1" ></div>
				<div class='well well-sm'><Strong>Enabled: </Strong><span name="enabled" class="editablecheckbox"><?=$status?></span></div> 
				<div class='well well-sm' style='height:280px;'><Strong>Notes: </Strong><br/><span name="details" class="editabletextarea"><?=$expense['details']?></span></div>
			</div>
		</form>
	</div>
</div>		
<?php }
}
include("inc/footer.php"); 
?>