<?php
//Connect to DB etc
require '../config.php';

//Testing
//ob_start();

//If no X-HOOK-SIGNATURE Header is set then kill the script
if (empty($_SERVER['HTTP_X_HOOK_SIGNATURE'])) {	die("Nope, No Hack Pls!");}

//Get raw POST to ensure signature integrity
$post = file_get_contents('php://input');

//Generate Base64 of SHA512 Digest to validate request integrity
$recSig = base64_encode(hash("sha512",$secret.$post,true));
$reqSig = $_SERVER['HTTP_X_HOOK_SIGNATURE']; //Gets the X-HOOK-SIGNATURE Header.
//If calculated signature matches the signature included in the header then do things else do other things
if ($recSig == $reqSig) {
	
	//Decode JSON from POST
	$post = json_decode($post, true);
	
	//var_dump($post);
	
	//Run CURL on the Starling transaction API with the uid retrieved from POST
	$ch = curl_init($starlingaccount.$post["uid"]); // Initialise cURL
	$authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
	curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization)); // Inject the token into the header
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch); 
	curl_close($ch);
	
	//Decode Transaction JSON.
	$trans = json_decode($result,true);
	
	//var_dump($trans);
	
	if($trans['direction'] == "IN") {
		//Checks if SendinigContactID matches a known user in the 'users' DB table 
		$query = "SELECT starlingid FROM users WHERE starlingid = '".$trans["counterPartyUid"]."'";
		
		//If more than 0 results then user matches, insert transaction in to DB otherwise do nothing.
		$res = mysqli_query($con,$query);
		if (mysqli_num_rows($res) != 0){
			$amount = number_format($trans['amount']['minorUnits']/100, 2);
			$query = "INSERT INTO `transactions` set 
				  `transactionId`  = '".$trans['feedItemUid']."',
				  `currency`  = '".$trans['amount']['currency']."', 
				  `amount`	 = '".$amount."',
				  `created` = '".strtotime($trans["transactionTime"])."', 
				  `reference` = '".$trans["reference"]."', 
				  `sendingContactId` = '".$trans["counterPartyUid"]."', 
				  `sendingContactAccountId` = '".$trans["counterPartySubEntityUid"]."'; ";
			
			$query .= "UPDATE users SET lastpaymentdate='".strtotime($trans["transactionTime"])."', lastpaymentamount='".$amount."' WHERE starlingid = '".$trans["counterPartyUid"]."';";
			
			//Checks for errors, not much use as this script is called from webhook but good for testing.
			if (!mysqli_multi_query($con, $query)) {
				echo "Multi query failed: (" . $con->errno . ") " . $con->error;
			}
		}
	}
//Signature does not match kill the script! (potential tampering or hacking)
} else {
	die("Invalid Signature, Aborting");
	exit();
}

mysqli_close($con);

//file_put_contents("debug.txt",ob_get_clean(),FILE_APPEND);

?>