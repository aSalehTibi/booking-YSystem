<?php

define('ROOT_DIR', '../');

if (!file_exists(ROOT_DIR . 'config/config.php'))
{
	die('Missing config/config.php. Please refer to the installation instructions.');
}

include(ROOT_DIR . 'config/config.php');

function format_sec_to_hours($time){
	return number_format((float)$time / (60*60),2,',','');
}

function format_price($price){
	return number_format($price,2,',','')."€";
}

function format_datum($date){
	return date("d.m.Y H:i", strtotime($date));
}

$date_start = strtotime($_POST['startDate']);
$date_end = strtotime($_POST['endDate']);

$output_format = (isset($_POST['format']) ? $_POST['format'] : 1);

#var_dump($date_start);

header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8");

$sql_start = date("Y-m-d H:i:s", $date_start);
$sql_end = date("Y-m-d H:i:s", $date_end);

$mysql = mysqli_connect($conf['settings']['database']['hostspec'], $conf['settings']['database']['user'], $conf['settings']['database']['password'], $conf['settings']['database']['name']);

$all_resources = array();
$all_resources_query = mysqli_query($mysql, "SELECT * FROM resources");
while ($row = $all_resources_query->fetch_assoc()) {
	$all_resources[$row['resource_id']] = $row;
}

$resources_prices_query = mysqli_query($mysql, "SELECT * FROM custom_attribute_values WHERE custom_attribute_id IN (1,2)");
$resources_prices = array();
while($row_p = $resources_prices_query->fetch_assoc()){
	if($row_p['custom_attribute_id'] == 1){
		$key = "intern";
	} else {
		$key = "extern";
	}	
	$resources_prices[$row_p['entity_id']][$key] = (int)$row_p['attribute_value'];
}

$all_users = array();
$all_users_query = mysqli_query($mysql, "SELECT * FROM users");
while ($row = $all_users_query->fetch_assoc()) {
	$all_users[$row['user_id']] = $row;
}

$all_projects = array();

if(isset($_POST['group_id']) and $_POST['group_id'] > 0){
	$filter = " WHERE group_id = '".(int)$_POST['group_id']."'";
} else {
	$filter = "";
	header('Content-Disposition: attachment; filename="TestPurpose.xlsx"');
}

echo "Abrechnungs-Start;".date("d.m.Y", $date_start)."\n";
echo "Abrechnungs-Ende;".date("d.m.Y", $date_end)."\n\n";


$all_projects_query = mysqli_query($mysql,"SELECT * FROM groups".$filter);
$resource_usage_per_project_per_user = array();
while ($row = $all_projects_query->fetch_assoc()) {
	if($filter != ""){
		header('Content-Disposition: attachment; filename="TestPurpose_'.$row['name'].'.xlsx"');
	}
	$all_projects[$row['group_id']] = $row;

	$ag_id = $row['group_id'];
	$users = array();
	$project_resources = array();

	$ag_users_query = mysqli_query($mysql, "SELECT * FROM user_groups WHERE group_id='".$ag_id."'");
	#print_r( mysqli_error_list($mysql));
	while ($row_u = $ag_users_query->fetch_assoc()) {
		$users[] = $row_u['user_id'];
	}

	if(!isset($resource_usage_per_project_per_user[$ag_id])){
		$resource_usage_per_project_per_user[$ag_id] = array();
		$resource_usage_per_project_per_user[$ag_id]['result'] = array(0,0,0);
	}
	$sql = "SELECT i.*, s.*, r.*, u.*, re.*, g.* FROM reservation_instances i 
				LEFT JOIN reservation_series s ON i.series_id = s.series_id 
				LEFT JOIN reservation_resources r ON r.series_id = i.series_id
				LEFT JOIN resources re ON re.resource_id = r.resource_id
				LEFT JOIN users u ON u.user_id = s.owner_id
				LEFT JOIN user_groups ug ON ug.user_id = u.user_id
				LEFT JOIN groups g ON g.group_id = ug.group_id
				WHERE g.group_id='".$ag_id."' AND `start_date` >= '".$sql_start."' AND `end_date` <= '".$sql_end."'
				ORDER BY re.name ASC, u.lname ASC, i.start_date ASC";
	#echo $sql;
	$reservation_instance_query = mysqli_query($mysql, $sql);
	$resv = array();
	#print_r( mysqli_error_list($mysql));
	#echo $reservation_instance_query->num_rows;
	while ($row_r = $reservation_instance_query->fetch_assoc()) {
		#print_r($row_r);
		$resource = $row_r['resource_id'];
		$res_prices = $resources_prices[$resource];

		$user = $row_r['user_id'];
		$raw_dur = strtotime($row_r['end_date']) - strtotime($row_r['start_date']);
		$row_r['duration'] = format_sec_to_hours($raw_dur);
		$price_intern = $row_r['duration'] * $res_prices['intern'];
		$price_extern = $row_r['duration'] * $res_prices['extern'];

		if($format == 1){

			if(!isset($resource_usage_per_project_per_user[$ag_id][$resource])){
				$resource_usage_per_project_per_user[$ag_id][$resource] = array();
				$resource_usage_per_project_per_user[$ag_id][$resource]['result'] = array(0,0,0);
			}
			if(!isset($resource_usage_per_project_per_user[$ag_id][$resource][$user])){
				$resource_usage_per_project_per_user[$ag_id][$resource][$user] = array();
				$resource_usage_per_project_per_user[$ag_id][$resource][$user]['result'] = array(0,0,0);
			}

			// prices
			$tmp_user = $resource_usage_per_project_per_user[$ag_id][$resource][$user]['result'];
			$tmp_user[0] += $raw_dur;
			$tmp_user[1] += $price_intern;
			$tmp_user[2] += $price_extern;

			$tmp_res = $resource_usage_per_project_per_user[$ag_id][$resource]['result'];
			$tmp_res[0] += $raw_dur;
			$tmp_res[1] += $price_intern;
			$tmp_res[2] += $price_extern;

			$tmp_pro = $resource_usage_per_project_per_user[$ag_id]['result'];
			$tmp_pro[0] += $raw_dur;
			$tmp_pro[1] += $price_intern;
			$tmp_pro[2] += $price_extern;

			$resource_usage_per_project_per_user[$ag_id][$resource][$user][] = array($row_r['start_date'], $row_r['end_date'], $row_r['duration'], $price_intern, $price_extern);
			$resource_usage_per_project_per_user[$ag_id][$resource][$user]['result'] = $tmp_user;
			$resource_usage_per_project_per_user[$ag_id][$resource]['result'] = $tmp_res;
			$resource_usage_per_project_per_user[$ag_id]['result'] = $tmp_pro;
		} else {
			if(!isset($resource_usage_per_project_per_user[$ag_id]['result'][$resource])){
				$resource_usage_per_project_per_user[$ag_id]['result'][$resource] = 0 ;
			}
			if(!isset($resource_usage_per_project_per_user[$ag_id][$user][$resource])){
				$resource_usage_per_project_per_user[$ag_id][$user][$resource] = 0;
			}
			$resource_usage_per_project_per_user[$ag_id][$user][$resource] += $raw_dur;
			$resource_usage_per_project_per_user[$ag_id]['result'][$resource] += $raw_dur;
		}
	}

	#echo "\n############################\n";
}
if($format == 1){
	foreach($resource_usage_per_project_per_user as $project_id => $used_resources){
		if($used_resources['result'] > 0){
			echo utf8_encode($all_projects[$project_id]['name']).";;;;;;\n";
			echo "Gruppe;Ressoruce;Nutzer;Startzeit;Endzeit;Stunden;Preis Intern;Preis Extern;\n";
			foreach($used_resources as $resource_id => $users){
				if($users['result'] > 0){ 
					echo ";".utf8_encode($all_resources[$resource_id]['name']).";;;;;\n";
					if(is_array($users)){
						foreach($users as $userID => $reservations){
							if($userID != 'result'){
								$username = $all_users[$userID]['lname'].", ".$all_users[$userID]['fname'];
								$i = 0;
								foreach($reservations as $key =>$reservation){
									if($key !== 'result'){
										$i++;
										echo ";;".utf8_encode($username).";".format_datum($reservation[0]).";".format_datum($reservation[1]).";".$reservation[2].";".format_price($reservation[3]).";".format_price($reservation[4]).";\n";
									}
								}
								echo ";;".utf8_encode($username)." (gesamt);;;".format_sec_to_hours($reservations['result'][0]).";".format_price($reservations['result'][1]).";".format_price($reservations['result'][2]).";\n";
							}
						}
					}
					echo ";".utf8_encode($all_resources[$resource_id]['name'])." (gesamt);;;;".format_sec_to_hours($users['result'][0]).";".format_price($users['result'][1]).";".format_price($users['result'][2]).";\n\n";
				}
			}
			echo "Gesamt fürs Projekt;;;;;".format_sec_to_hours($users['result'][0]).";".format_price($users['result'][1]).";".format_price($users['result'][2]).";\n\n";
			echo "Zu bezahlen;;;;;;".format_price($users['result'][1]).";".format_price($users['result'][2]).";\n\n";
		}
 	}
} else {
	$header = array('$0'=>'Gruppe','$1'=>'Nutzer');
	$used_res = array();
	foreach($all_resources as $resID => $res_Info){
		$header[$resID] = $res_Info['name'];
		$used_res[] = $resID;
	}
	echo implode(";",array_values($header))."\n";
	foreach($resource_usage_per_project_per_user as $project_id => $users){
		foreach($users as $user_id => $resources_used){
			if($user_id != "result"){
				$username = $all_users[$user_id]['lname'].", ".$all_users[$user_id]['fname'];
				echo utf8_encode($all_projects[$project_id]['name']).";".utf8_encode($username).";";
				foreach($used_res as $res_key){
					if(isset($resources_used[$res_key])){
						echo format_sec_to_hours($resources_used[$res_key]).";";
					} else {
						echo "0;";
					}
				}
				echo "\n";
			}
		}
		$sum_price_intern = 0;
		$sum_price_extern = 0;
		$sum_hour = 0;
		foreach($used_res as $res_key){
			if(isset($resource_usage_per_project_per_user[$project_id]['result'])){
				$hours = $resource_usage_per_project_per_user[$project_id]['result'][$res_key];
				$sum_hour .= format_sec_to_hours($hours).";";
				$price_intern = $hours * $resources_prices[$res_key]['intern'] / (60*60);
				$price_extern = $resources_pricesdriv * $hours[$res_key]['extern'] / (60*60);
				$list_price_intern .= format_price($price_intern).";";
				$list_price_extern .= format_price($price_extern).";";
				$sum_price_intern += $price_intern;
				$sum_price_extern += $price_extern;
			} else {
				$sum_hour .= "0;";
				$list_price_extern .= "0;";
				$list_price_intern .= "0;"; 
				$sum_price_intern += 0;
				$sum_price_extern += 0;
			}
		}
		echo "Z: Summe Stunden;;".$sum_hour."\n";
		echo "Z: Kosten je Posten (intern);;".$list_price_intern."\n";
		echo "Z: Kosten je Posten (extern);;".$list_price_extern."\n";
		echo "Z: Kosten Gesamt (intern);".format_price($sum_price_intern)."\n";
		echo "Z: Kosten Gesamt (extern);".format_price($sum_price_extern)."\n\n\n\n";

	}

}
?>
