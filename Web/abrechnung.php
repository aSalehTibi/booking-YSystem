<?php

define('ROOT_DIR', '../');

if (!file_exists(ROOT_DIR . 'config/config.php'))
{
	die('Missing config/config.php. Please refer to the installation instructions.');
}

include(ROOT_DIR . 'config/config.php');

$date_start = "1.1.2016";
$date_end = "31.5.2016";

header("Content-type: text/csv");

$sql_start = date("Y-m-d H:i:s", strtotime($date_start));
$sql_end = date("Y-m-d H:i:s", strtotime($date_end));

$mysql = mysqli_connect($conf['settings']['database']['hostspec'], $conf['settings']['database']['user'], $conf['settings']['database']['password'], $conf['settings']['database']['name']);

$all_resources = array();
$all_resources_query = mysqli_query($mysql, "SELECT * FROM resources");
while ($row = $all_resources_query->fetch_assoc()) {
	$all_resources[$row['resource_id']] = $row;
}

$all_users = array();
$all_users_query = mysqli_query($mysql, "SELECT * FROM users");
while ($row = $all_users_query->fetch_assoc()) {
	$all_users[$row['user_id']] = $row;
}

$all_projects = array();

$all_projects_query = mysqli_query($mysql,"SELECT * FROM groups WHERE group_id=1");
$resource_usage_per_project_per_user = array();
while ($row = $all_projects_query->fetch_assoc()) {
	$all_projects[$row['group_id']] = $row;

	$ag_id = $row['group_id'];
	$users = array();
	$project_resources = array();

	$ag_users_query = mysqli_query($mysql, "SELECT * FROM user_groups WHERE group_id='".$ag_id."'");
	//print_r( mysqli_error_list($mysql));
	while ($row_u = $ag_users_query->fetch_assoc()) {
		$users[] = $row_u['user_id'];
	}

	if(!isset($resource_usage_per_project_per_user[$ag_id])){
		$resource_usage_per_project_per_user[$ag_id] = array();
		$resource_usage_per_project_per_user[$ag_id]['result'] = 0;
	}

	if(count($users) > 0){

		$reservation_series_query = mysqli_query($mysql, "SELECT * FROM reservation_series s WHERE s.owner_id IN (".implode(", ", $users).")");
		$series = array();
		$series_ids = array();
		while ($row_s = $reservation_series_query->fetch_assoc()) {
			$series[$row_s['series_id']] = $row_s;
			$series_ids[] = $row_s['series_id'];
		}

		if(count($series_ids) > 0){

			$series_ressources_query = mysqli_query($mysql, "SELECT * FROM reservation_resources WHERE series_id IN (".implode(", ",$series_ids).")");
			while ($row_rr = $series_ressources_query->fetch_assoc()) {
				$series[$row_rr['series_id']]['resource_id'] = $row_rr['resource_id'];
				$project_resources[] = $row_rr['resource_id'];
			}

			$reservation_instance_query = mysqli_query($mysql, "SELECT * FROM reservation_instances i WHERE i.series_id IN (".implode(", ",$series_ids).") AND `start_date` >= '".$sql_start."' AND `end_date` <= '".$sql_end."'");
			$resv = array();
			while ($row_r = $reservation_instance_query->fetch_assoc()) {
				$resv[$row_r['reservation_instance_id']] = $row_r;
				$resv[$row_r['reservation_instance_id']]['duration'] = strtotime($row_r['end_date']) - strtotime($row_r['start_date']);

			}

			$project_resources_unique = array_unique($project_resources);

			foreach($project_resources_unique as $resource){
				if(!isset($resource_usage_per_project_per_user[$ag_id][$resource])){
					$resource_usage_per_project_per_user[$ag_id][$resource] = array();
					$resource_usage_per_project_per_user[$ag_id][$resource]['result'] = 0;
				}
				$res_data = $all_resources[$resource];
				foreach($resv as $res){
					$s = $series[$res['series_id']];
					if($s['resource_id'] == $resource){
						if(!isset($resource_usage_per_project_per_user[$ag_id][$resource][$s['owner_id']])){
							$resource_usage_per_project_per_user[$ag_id][$resource][$s['owner_id']] = array();
							$resource_usage_per_project_per_user[$ag_id][$resource][$s['owner_id']]['result'] = 0;
						}

						$resource_usage_per_project_per_user[$ag_id][$resource][$s['owner_id']][] = array($res['start_date'], $res['end_date'], $res['duration']);
						$resource_usage_per_project_per_user[$ag_id][$resource][$s['owner_id']]['result'] += (int)$res['duration'];
						$resource_usage_per_project_per_user[$ag_id][$resource]['result'] += (int)$res['duration'];
						$resource_usage_per_project_per_user[$ag_id]['result'] += (int)$res['duration'];
					}
				}
			}
		} else {
			#echo "Projekt hat keine Reservierungen.";
		}
	} else {
		#echo "Projekt hat keine Nutzer mehr";
	}

	#echo "\n############################\n";

	foreach($resource_usage_per_project_per_user as $project_id => $used_resources){
		echo $all_projects[$project_id]['name'].";;;;;\n";
		foreach($used_resources as $resource_id => $users){
			echo $all_resources[$resource_id]['name'].";;;;;\n";
			if(is_array($users)){
				foreach($users as $userID => $reservations){
					if($userID != 'result'){
						$username = $all_users[$userID]['lname'].", ".$all_users[$userID]['fname'];
						foreach($reservations as $key =>$reservation){
							if($key != 'result'){
								echo $username.";".$reservation[0].";".$reservation[1].";".$reservation[2].";\n";
							}
						}
						echo $username.";;;;".$reservations['result']."\n";
					}
				}
			}
			echo ";;;;;".$users['result']."\n";
		}
 	}
}

?>