
<?php 

define('ROOT_DIR', '../');

 

if (!file_exists(ROOT_DIR . 'config/config.php'))
{
	die('Missing config/config.php. Please refer to the installation instructions.');
}

include(ROOT_DIR . 'config/config.php');



$mysql = mysqli_connect($conf['settings']['database']['hostspec'], $conf['settings']['database']['user'], $conf['settings']['database']['password'], $conf['settings']['database']['name']) or die("Couldn't connect to MySQL:<br>" . mysql_error() . "<br>" . mysql_errno());
//select database

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

include ('/var/www/html/booked16/lib/PHPExcel/Classes/PHPExcel.php');
include ('/var/www/html/booked16/lib/PHPExcel/Classes/PHPExcel/IOFactory.php');
include ('/var/www/html/booked16/lib/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php');

$phpExcel = new PHPExcel;

// Setting font to Arial Black

$phpExcel->getDefaultStyle()->getFont()->setName('Arial Black');

// Setting font size to 14

$phpExcel->getDefaultStyle()->getFont()->setSize(14);

//Setting description, creator and title

$phpExcel ->getProperties()->setTitle("Vendor list");

$phpExcel ->getProperties()->setCreator("Robert");

$phpExcel ->getProperties()->setDescription("Excel SpreadSheet in PHP");

// Creating PHPExcel spreadsheet writer object

// We will create xlsx file (Excel 2007 and above)

$writer = PHPExcel_IOFactory::createWriter($phpExcel, "Excel2007");

// When creating the writer object, the first sheet is also created

// We will get the already created sheet

$sheet = $phpExcel ->getActiveSheet();

// Setting title of the sheet

$sheet->setTitle('first sheet');

// Creating spreadsheet header

$sheet ->getCell('A1')->setValue('Vendor');

$sheet ->getCell('B1')->setValue('Amount');

$sheet ->getCell('C1')->setValue('Cost');

// Making headers text bold and larger

$sheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(14);

// Insert product data

// Autosize the columns

$sheet->getColumnDimension('A')->setAutoSize(true);

$sheet->getColumnDimension('B')->setAutoSize(true);

$sheet->getColumnDimension('C')->setAutoSize(true);

// Save the spreadsheet

$writer->save('products.xlsx');

header('Content-Type: application/vnd.ms-excel');

header('Content-Disposition: attachment;filename="file.xlsx"');

header('Cache-Control: max-age=0');

$writer->save('php://output');

 
// Setting font to Arial Black
//.$letters[$ij].'2:'.$letters[$ij].$u.')
/*
 //$all_projects_query = mysqli_query($mysql,"SELECT * FROM groups".$filter);
 while ($row = $all_projects_query ->fetch_assoc()) {

 $all_projects[$row['group_id']] = $row;

 // Add new sheet
 $objWorkSheet = $objPHPExcel->createSheet($row['group_id']); //Setting index when creating

 //Write cells
 $objWorkSheet->setCellValue('A1', 'Hello'.$row['group_id'])
 ->setCellValue('B2', 'world!')
 ->setCellValue('C1', 'Hello')
 ->setCellValue('D2', 'world!');

 // Rename sheet
 $objWorkSheet->setTitle($row['name']);


 }
 */
//$objWorkSheet = $objPHPExcel->createSheet($row['group_id']);
//$objWorkSheet->setTitle(utf8_encode($row['name']));
//->setCellValue('A2','Abrechnungs-Ende')
//->setCellValue('B2',date("M.Y", $date_end))
       echo '<p>Connection Done!! </p>'; 

?>
