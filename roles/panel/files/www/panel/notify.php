<?php
require '/config/www/panel/inc/functions.php';  
require '/config/www/config.php';
if ($_GET['token'] != $token) {
    die("Nope, No Hack Pls!");
} else {			
    $query = "SELECT * FROM users ORDER by name;";
    $sqltran = mysqli_query($con, $query)or die(mysqli_error($con));
    while ($user = mysqli_fetch_array($sqltran)) {	
        $allplanscost = 0;
        $id = mysqli_real_escape_string($con,$user['id']);						
        $query2 = "SELECT * FROM user_plans INNER JOIN plans ON plans.id=user_plans.plan_id WHERE user_id = $id AND NOT quantity = 0;";
        $sqltran2 = mysqli_query($con, $query2)or die(mysqli_error($con));
        while ($plans = mysqli_fetch_array($sqltran2)) {
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
            }
        }
        $totalcharge += $allplanscost;

        if (HasPaidRaw($totalpaidthismonth,$allplanscost) == 1) {
            $arr['notpaid']['embeds'][] = array("title" => $user['name'] . " (£".$totalpaidthismonth." / £". $allplanscost . ")", "color" => 16711680);
        } elseif (HasPaidRaw($totalpaidthismonth,$allplanscost) == 2) { 
            $arr['partial']['embeds'][] = array("title" => $user['name'] . " (£".$totalpaidthismonth." / £". $allplanscost . ")", "color" => 16753920);
        } elseif (HasPaidRaw($totalpaidthismonth,$allplanscost) == 3) { 
            $arr['paid']['embeds'][] = array("title" => $user['name'] . " (£".$totalpaidthismonth." / £". $allplanscost . ")", "color" => 65280);
        } elseif (HasPaidRaw($totalpaidthismonth,$allplanscost) == 4) {
            $arr['overpaid']['embeds'][] = array("title" => $user['name'] . " (£".$totalpaidthismonth." / £". $allplanscost . ")", "color" => 255);
        }
    }    
    $arr['notpaid']['content'] = "Completely Outstanding CoolFlix Payments";
    $arr['partial']['content'] = "Partially Outstanding CoolFlix Payments";
    $arr['paid']['content'] = "Completed CoolFlix Payments";
    $arr['overpaid']['content'] = "Coolflix Payments In Excess";
    
    if (!empty($arr['notpaid']['embeds'])) {
        $json = json_encode($arr['notpaid']);
        discord_notification($json,$discord_webhook);
    }
    if (!empty($arr['partial']['embeds'])) {
        $json = json_encode($arr['partial']);
        discord_notification($json,$discord_webhook);
    }
    if (!empty($arr['paid']['embeds'])) {
        $json = json_encode($arr['paid']);
        discord_notification($json,$discord_webhook);
    }
    if (!empty($arr['overpaid']['embeds'])) {
        $json = json_encode($arr['overpaid']);
        discord_notification($json,$discord_webhook);
    }
}

?>