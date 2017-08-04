<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors',TRUE);
define('ROOT_DIR', '../');

const FORMAT_CURRENCY_EUR_SIMPLE = '[$EUR ]#,##0.00_-';

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

#$output_format = (isset($_POST['format']) ? $_POST['format'] : 1);

#var_dump($date_start);

//header("Content-type: text/csv");

$sql_start = date("Y-m-d H:i:s", $date_start);
$sql_end = date("Y-m-d H:i:s", $date_end);

$mysql = mysqli_connect($conf['settings']['database']['hostspec'], $conf['settings']['database']['user'], $conf['settings']['database']['password'], $conf['settings']['database']['name']);



$all_resources = array();
$all_resources_query = mysqli_query($mysql, "SELECT * FROM resources");
while ($row = $all_resources_query->fetch_assoc()) {
	$all_resources[$row['resource_id']] = $row; // resource_id now equal to row

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
	//header('Content-Disposition: attachment; filename="Abrechnung.csv"');

}

//echo "Abrechnungs-Start;".date("d.m.Y", $date_start)."\n";
//echo "Abrechnungs-Ende;".date("d.m.Y", $date_end)."\n\n";

$gcount=0;
$all_projects_query = mysqli_query($mysql,"SELECT * FROM groups ".$filter."ORDER BY name ");
$resource_usage_per_project_per_user = array();
while ($row = $all_projects_query->fetch_assoc()) {
	if($filter != ""){
		//header('Content-Disposition: attachment; filename="Abrechnung_'.$row['name'].'.csv"');
	}
	$all_projects[$row['group_id']] = $row;
	$gcount=$gcount+1;



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
				ORDER BY  re.name ASC, u.lname ASC, i.start_date ASC";
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

		
	}
}


try {
include ('../lib/PHPExcel/Classes/PHPExcel.php');
include ('../lib/PHPExcel/Classes/PHPExcel/IOFactory.php');
include ('../lib/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php');

	

$letters=range('B','Z');
$colors=array('218,250,244','181,255,200','255,215,179','255,180,40','255,255,153','204,255,204','255,164,164','170,221,255');
$ij=0;

$objPHPExcel = new PHPExcel;
$sheet = $objPHPExcel->getActiveSheet();
$objWorkSheet = $objPHPExcel->createSheet();
//$objWorkSheet->setTitle(date("MY", $date_start)."-".date("MY", $date_end));

if($_POST['format'] == 3){
$objWorkSheet->setTitle('statics');

// drawing  chart
//$reader = PHPExcel_IOFactory::createReader('Excel2007');
// $reader->setIncludeCharts(TRUE);

$all_data_chart = mysqli_query($mysql, "select sum(time_to_sec(timediff(i.end_date, i.start_date)) / 3600) as duration ,re.name ,re.resource_id from reservation_instances i
									                LEFT JOIN reservation_series s    ON i.series_id = s.series_id
													LEFT JOIN reservation_statuses st    ON s.status_id = st.status_id
									                LEFT JOIN reservation_resources r ON r.series_id = i.series_id
									                LEFT JOIN resources re            ON re.resource_id = r.resource_id
				                                    where ((i.start_date>='".date("y-m-d", $date_start)."' and i.end_date<='".date("y-m-d", $date_end)."') and s.status_id= 1)
									                group by re.resource_id
	    											order by re.resource_id");




$char_counter=2;
$objWorkSheet->getStyle('A1:C1')->getFont()->setBold(true);
$objWorkSheet->getColumnDimension('A')->setAutoSize(false);
$objWorkSheet->getColumnDimension('A')->setWidth("25");
$objWorkSheet->setCellValue('A1','Resources');
$objWorkSheet->setCellValue('B1','Stunde');
while($chart = $all_data_chart->fetch_assoc())
{
	
	$objWorkSheet->setCellValue('A'.$char_counter,$chart['name']);
	$objWorkSheet->setCellValue('B'.$char_counter,(double)$chart['duration']);
	$char_counter=1 + $char_counter;
}


$labels = array(
		new PHPExcel_Chart_DataSeriesValues('String', 'statics!$B$1', null, 1),
);

$categories = array(
		new PHPExcel_Chart_DataSeriesValues('String', 'statics!$A$2:$A'.$char_counter, null, $char_counter),);

$values = array(
		new PHPExcel_Chart_DataSeriesValues('Number', 'statics!$B$2:$B'.$char_counter, null, $char_counter),);

$series = new PHPExcel_Chart_DataSeries(
		PHPExcel_Chart_DataSeries::TYPE_BARCHART_3D,     // plotType
		PHPExcel_Chart_DataSeries::GROUPING_STACKED,  // plotGrouping
		array(0),                                     // plotOrder
		$labels,                                        // plotLabel
		$categories,                                    // plotCategory
		$values                                         // plotValues
		);


$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
$layout1 = new PHPExcel_Chart_Layout();    // Create object of chart layout to set data label

/*
 $layout1->setShowVal(TRUE);
 $layout1->setManual3dAlign(true);
 $layout1->setXRotation(20);
 $layout1->setYRotation(20);
 $layout1->setPerspective(15);
 $layout1->setRightAngleAxes(TRUE);
 */
$plotarea = new PHPExcel_Chart_PlotArea($layout1, array($series));
$title    = new PHPExcel_Chart_Title('Statics of resources usage');
$legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
$xTitle   = new PHPExcel_Chart_Title('Resources');
$yTitle   = new PHPExcel_Chart_Title('Stunde');
$chart    = new PHPExcel_Chart(
		'chart1',                                       // name
		$title,                                         // titles
		$legend,                                        // legend
		$plotarea,                                      // plotArea
		true,                                           // plotVisibleOnly
		0,                                              // displayBlanksAs
		$xTitle,                                        // xAxisLabel
		$yTitle                                         // yAxisLabel
		);
$chart->setTopLeftPosition('D5');
$chart->setBottomRightPosition('R34');
$objWorkSheet->addChart($chart);


/*
 foreach ($all_resources as $row_re )
 {
 $objWorkSheet->getColumnDimension($letters[$ii])->setAutoSize(false);
 $objWorkSheet->getColumnDimension($letters[$ii])->setWidth("15");
 $objWorkSheet->setCellValue($letters[$ii].'1',$row_res['name']);
 $ii=$ii+1;

 }
 */


// all groups 
foreach ($all_projects as $row )
{
	$ii=1;

	$objWorkSheet = $objPHPExcel->createSheet($row['group_id']);
	$objWorkSheet->setTitle(utf8_encode($row['name']));

	$gID=$row['group_id'];
	$all_benuzer_query = mysqli_query($mysql, "select sum(time_to_sec(timediff(i.end_date, i.start_date)) / 3600) as duration , u.user_id,u.fname, u.lname,g.name, g.group_id ,re.name ,re.resource_id from reservation_instances i
									                LEFT JOIN reservation_series s    ON i.series_id = s.series_id
													LEFT JOIN reservation_statuses st    ON s.status_id = st.status_id
									                LEFT JOIN reservation_resources r ON r.series_id = i.series_id
									                LEFT JOIN resources re            ON re.resource_id = r.resource_id
									                LEFT JOIN users u                 ON u.user_id = s.owner_id
									                left join user_groups ug          on u.user_id = ug.user_id
									                left join groups g                on g.group_id = ug.group_id
									                where g.group_id ='".$gID."' and (i.start_date>='".date("y-m-d", $date_start)."' and i.end_date<='".date("y-m-d", $date_end)."') and s.status_id= 1
									                group by re.name,u.user_id
	    											order by  u.user_id");

	$Kost_pro_resource = mysqli_query($mysql, "select  tab1.duration* attribute_value as summe, tab1.*
													FROM    (select sum(time_to_sec(timediff(i.end_date, i.start_date)) / 3600) as duration , u.user_id,u.fname, u.lname,g.name, g.group_id ,re.name as res_name,v.attribute_value  ,re.resource_id from reservation_instances i
                                                    LEFT JOIN reservation_series s    ON i.series_id = s.series_id
													LEFT JOIN reservation_statuses st    ON s.status_id = st.status_id
                                                    LEFT JOIN reservation_resources r ON r.series_id = i.series_id
                                                    LEFT JOIN resources re            ON re.resource_id = r.resource_id
                                                    LEFT JOIN users u                 ON u.user_id = s.owner_id
                                                    left join user_groups ug          on u.user_id = ug.user_id
                                                    left join groups g                on g.group_id = ug.group_id
                                                    LEFT JOIN  custom_attribute_values v  on v.entity_id= re.resource_id
                                                    where g.group_id ='".$gID."' and (i.start_date>='".date("y-m-d", $date_start)."' and i.end_date<='".date("y-m-d", $date_end)."') and s.status_id= 1
                                                    group by re.resource_id
                                                    order by re.resource_id) tab1");
	
	$objWorkSheet->getStyle('A1:A3')->getFont()->setBold(true);
	$objWorkSheet->getStyle('A4:Z4')->getFont()->setBold(true);
	$objWorkSheet->getRowDimension('1')->setRowHeight("30");
	$objWorkSheet->setCellValue('A'.'1','Datum');
	
	$objWorkSheet->setCellValue('B'.'1',date("MY", $date_start)."-".date("MY", $date_end));
	
	
	$objWorkSheet->setCellValue('A'.'2','Kostenstelle');
	
	$objWorkSheet->setCellValue('B'.'2',$row['kostNumber']);
	
	$objWorkSheet->setCellValue('A'.'3','AuftragNummer');
	$objWorkSheet->setCellValue('B'.'3',$row['steuerNumber']);
	
	$objWorkSheet->getColumnDimension('A')->setAutoSize(false);
	$objWorkSheet->getColumnDimension('A')->setWidth("15");
	$objWorkSheet->getColumnDimension('B')->setAutoSize(false);
	$objWorkSheet->getColumnDimension('B')->setWidth("18");
	
	//$objWorkSheet->getStyle('A4:Z4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	//$objWorkSheet->getStyle('A4:Z4')->getFill()->getStartColor()->setARGB('29bb04');
	$objWorkSheet->setCellValue('A'.'4','Gruppe');
	$objWorkSheet->setCellValue('B'.'4','Nutzer');

	$u=4;
	$benu_id=0;
	//$u_new=1;
	while ($row1 = $all_benuzer_query->fetch_assoc())

	{
		if ((int)$row1['user_id']> $benu_id)
		{ $u++;	$benu_id=$row1['user_id'];}

		$fName= $row1['lname'].",".$row1['fname'];
		$objWorkSheet->setCellValue('A'.$u,utf8_encode($row['name']));
		$objWorkSheet->setCellValue('B'.$u,utf8_encode($fName));
		$objWorkSheet->getStyle($letters[$row1['resource_id']].$u)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);
		$objWorkSheet->setCellValue($letters[$row1['resource_id']].$u,(double)$row1['duration']);
		$u_new=$u+1;
	}
	

	if ($objWorkSheet->getCell('B'.'5')->getValue() == '')
	{
		$sheetIndex = $objPHPExcel->getIndex($objPHPExcel-> getSheetByName(utf8_encode($row['name'])));
		$objPHPExcel->removeSheetByIndex($sheetIndex);
	
	}
	Else
	{
	
	$objWorkSheet->setCellValue('A'.$u_new,'Summe Stunden');
	// color 
	//$objWorkSheet->getStyle('A'.$u_new.':Z'.$u_new)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	//$objWorkSheet->getStyle('A'.$u_new.':Z'.$u_new)->getFill()->getStartColor()->setARGB('29bbff');

	$k=(string)((int)$u_new+1);

	$objWorkSheet->setCellValue('A'.(string)((int)$u_new+1),'Kosten');
	//$objWorkSheet->getStyle('A'. $k.':Z'. $k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	//$objWorkSheet->getStyle('A'. $k.':Z'. $k)->getFill()->getStartColor()->setRGB('218,150,244');
	 
	 
	// $objWorkSheet->setCellValue('D'.'16','=SUM(D4:D15)');
	//$objWorkSheet->setCellValue('F'.'16','=SUM(F4:F15)');
	 
	foreach ($all_resources as $row_res )
	{
		$objWorkSheet->getColumnDimension($letters[$ii])->setAutoSize(false);
		$objWorkSheet->getColumnDimension($letters[$ii])->setWidth("25");
		$objWorkSheet->setCellValue($letters[$ii].'4',$row_res['name']);
		$objWorkSheet->setCellValue($letters[$ii].$u_new,'=SUM('.$letters[$ii].'5:'.$letters[$ii].$u.')');

		/*
		 * color stuff for every resource
		 $objWorkSheet->getStyle($letters[$ii].'1:'.$letters[$ii].$u_new)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		 $objWorkSheet->getStyle($letters[$ii].'1:'.$letters[$ii].$u_new)->getFill()->getStartColor()->setRGB($colors[$ij]);
		  
		 if ($ij<8 )
		 	$ij=$ij+1;
		 	else
		 		$ij=0;
		 		
*/

		 	if ($objWorkSheet->getCell($letters[$ii].$u_new)->getValue() == '')
		 {$objWorkSheet->getColumnDimension($letters[$ii])->setVisible(false);}
		 	
		 	$ii=$ii+1;
	

     }
     $objWorkSheet->setCellValue($letters[$ii].'4','Summe');
     
     $objWorkSheet->getColumnDimension($letters[$ii])->setAutoSize(false);
     $objWorkSheet->getColumnDimension($letters[$ii])->setWidth("20");
     
     $objWorkSheet->setCellValue($letters[$ii].$u_new,'=SUM('.$letters[1].$u_new.':'.$letters[$ii-1].$u_new.')');
     
     $objWorkSheet->setCellValue($letters[$ii].$k,'=SUM('.$letters[1].$k.':'.$letters[$ii-1].$k.')');
     
     $objWorkSheet->getStyle($letters[$ii].$u_new)->getFont()->setBold(true);
     
     $objWorkSheet->getStyle($letters[$ii].$k)->getFont()->setBold(true);
    
     
     $objWorkSheet->getStyle($letters[$ii].$k)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
     
     $styleArray1 = array(
     		'borders' => array(
     				'allborders' => array(
     						'style' => PHPExcel_Style_Border::BORDER_THICK,
     						'color' => array('argb' => '00000000'),
     				),
     		),
     );
     
     $objWorkSheet->getStyle('A'.$u_new.':'.$letters[$ii].$k)->applyFromArray($styleArray1);
     
	while ($row_kost = $Kost_pro_resource->fetch_assoc())	 
	{
		$objWorkSheet->setCellValue($letters[$row_kost['resource_id']].$k,(double)$row_kost['summe']);
	
	}
	//to delete the null REsourse which not body used 
	$ii=1;

	foreach ($all_resources as $row_res )
	{
		if ($objWorkSheet->getCell($letters[$ii].$k)->getValue() == '')
		{$objWorkSheet->getColumnDimension($letters[$ii])->setVisible(false);
			
		}
		
		$ii=$ii+1;
	}

    }

}

 }
 
 if($_POST['format'] == 4){
 	
 	foreach ($all_projects as $row )
 	{
 		
 		
 		$objWorkSheet = $objPHPExcel->createSheet($row['group_id']);
 		$objWorkSheet->setTitle(utf8_encode($row['name']));
 		$gID=$row['group_id'];
 		$all_benuzer_query = mysqli_query($mysql, "select u.fname as first, u.lname as last  ,g.name ,re.name as Resource ,i.start_date as Begin, i.end_date as End,time_to_sec(timediff(i.end_date, i.start_date)) / 3600 as Duration, v.attribute_value as Cost from reservation_instances i
									                LEFT JOIN reservation_series s    ON i.series_id = s.series_id
													LEFT JOIN reservation_statuses st    ON s.status_id = st.status_id
									                LEFT JOIN reservation_resources r ON r.series_id = i.series_id
									                LEFT JOIN resources re            ON re.resource_id = r.resource_id
									                LEFT JOIN users u                 ON u.user_id = s.owner_id
									                left join user_groups ug          on u.user_id = ug.user_id
									                left join groups g                on g.group_id = ug.group_id
													left join custom_attribute_values v on v.entity_id = re.resource_id
									                where g.group_id ='".$gID."' and (i.start_date>='".date("y-m-d", $date_start)."' and i.end_date<='".date("y-m-d", $date_end)."') and s.status_id= 1
	    											order by  i.start_date");
 		$objWorkSheet->setCellValue('A'.'1','Resource');
 		$objWorkSheet->setCellValue('B'.'1','Begin');
 		$objWorkSheet->setCellValue('C'.'1','End');
 		$objWorkSheet->setCellValue('D'.'1','Duration');
 		$objWorkSheet->setCellValue('E'.'1','User ');
 		$objWorkSheet->setCellValue('F'.'1','Gruppe');
 		$objWorkSheet->setCellValue('G'.'1','Preis');
 		
 		$objWorkSheet->getColumnDimension('A')->setAutoSize(false);
 		$objWorkSheet->getColumnDimension('A')->setWidth("25");
 		
 		$objWorkSheet->getColumnDimension('B')->setAutoSize(false);
 		$objWorkSheet->getColumnDimension('B')->setWidth("25");
 		
 		$objWorkSheet->getColumnDimension('C')->setAutoSize(false);
 		$objWorkSheet->getColumnDimension('C')->setWidth("25");
 		
 		$objWorkSheet->getColumnDimension('E')->setAutoSize(false);
 		$objWorkSheet->getColumnDimension('E')->setWidth("25");
 		
 		$objWorkSheet->getColumnDimension('F')->setAutoSize(false);
 		$objWorkSheet->getColumnDimension('F')->setWidth("20");
 		
 		$u=2;
 		
 		while ($row1 = $all_benuzer_query->fetch_assoc())
 		
 		{
 			$fName= $row1['last'].",".$row1['first'];
 			
 			$objWorkSheet->setCellValue('A'.$u,$row1['Resource']);
 			$objWorkSheet->setCellValue('B'.$u,$row1['Begin']);
 			$objWorkSheet->setCellValue('C'.$u,$row1['End']);
 			$objWorkSheet->setCellValue('D'.$u,(double)$row1['Duration']);
 			$objWorkSheet->setCellValue('E'.$u,utf8_encode($fName));
 			$objWorkSheet->setCellValue('F'.$u,utf8_encode($row['name']));
 			$objWorkSheet->setCellValue('G'.$u,(double)$row1['Cost']);
 			
 			$u=$u+1;
 		}
 		
 		
 	  
 		
 	}
 	
 
 	
 }
 
 if($_POST['format'] == 5){
 	$objWorkSheet->setTitle('user with out group');
 	
 	
 	$all_benuzer_query = mysqli_query($mysql, "select u.fname as first, u.lname as last ,re.name as Resource ,i.start_date as Begin, i.end_date as End,time_to_sec(timediff(i.end_date, i.start_date)) / 3600 as Duration, v.attribute_value as Cost, s.title, s.description as des from reservation_instances i
									                LEFT JOIN reservation_series s    ON i.series_id = s.series_id
													LEFT JOIN reservation_statuses st    ON s.status_id = st.status_id
									                LEFT JOIN reservation_resources r ON r.series_id = i.series_id
									                LEFT JOIN resources re            ON re.resource_id = r.resource_id
									                LEFT JOIN users u                 ON u.user_id = s.owner_id
									                left join user_groups ug          on u.user_id = ug.user_id
									                left join groups g                on g.group_id = ug.group_id
													left join custom_attribute_values v on v.entity_id = re.resource_id
									                where ug.group_id is null and  (i.start_date>='".date("y-m-d", $date_start)."' and i.end_date<='".date("y-m-d", $date_end)."') and s.status_id= 1
	    											order by  i.start_date");
 	$objWorkSheet->setCellValue('A'.'1','Resource');
 	$objWorkSheet->setCellValue('B'.'1','Begin');
 	$objWorkSheet->setCellValue('C'.'1','End');
 	
 	$objWorkSheet->setCellValue('D'.'1','Last Name');
 	$objWorkSheet->setCellValue('E'.'1',' First Name');
 	
 	$objWorkSheet->setCellValue('F'.'1','Duration');
 	
 	$objWorkSheet->setCellValue('G'.'1','Preis');
 	
 	$objWorkSheet->setCellValue('H'.'1','title ');
 	$objWorkSheet->setCellValue('I'.'1','desc');
 	
 	$objWorkSheet->getColumnDimension('A')->setAutoSize(false);
 	$objWorkSheet->getColumnDimension('A')->setWidth("25");
 	
 	$objWorkSheet->getColumnDimension('B')->setAutoSize(false);
 	$objWorkSheet->getColumnDimension('B')->setWidth("25");
 	
 	$objWorkSheet->getColumnDimension('C')->setAutoSize(false);
 	$objWorkSheet->getColumnDimension('C')->setWidth("25");
 	
 	$objWorkSheet->getColumnDimension('E')->setAutoSize(false);
 	$objWorkSheet->getColumnDimension('E')->setWidth("20");
 	
 	$objWorkSheet->getColumnDimension('F')->setAutoSize(false);
 	$objWorkSheet->getColumnDimension('F')->setWidth("20");
 	
 	$objWorkSheet->getColumnDimension('H')->setAutoSize(false);
 	$objWorkSheet->getColumnDimension('H')->setWidth("20");
 	
 	$objWorkSheet->getColumnDimension('I')->setAutoSize(false);
 	$objWorkSheet->getColumnDimension('I')->setWidth("20");
 	
 	$u=2;
 	
 	while ($row1 = $all_benuzer_query->fetch_assoc())
 	
 	{
 		#$fName= $row1['last'].",".$row1['first'];
 		
 		$objWorkSheet->setCellValue('A'.$u,$row1['Resource']);
 		$objWorkSheet->setCellValue('B'.$u,$row1['Begin']);
 		$objWorkSheet->setCellValue('C'.$u,$row1['End']);
 		
 		$objWorkSheet->setCellValue('D'.$u,utf8_encode($row1['last']));
 		$objWorkSheet->setCellValue('E'.$u,utf8_encode($row1['first']));
 		
 		$objWorkSheet->setCellValue('F'.$u,(double)$row1['Duration']);
 	
 		$objWorkSheet->setCellValue('G'.$u,(double)$row1['Cost']);
 		
 		$objWorkSheet->setCellValue('H'.$u,utf8_encode($row1['title']));
 		$objWorkSheet->setCellValue('I'.$u,utf8_encode($row1['des']));
 		
 		$u=$u+1;
 	}
 	
 	
 }
 
 

# end if 

$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$writer->setIncludeCharts(TRUE);

ob_end_clean();
header('Content-Type: application/vnd.ms-excel;charset=utf-8');
header("Content-Type: application/download;charset=utf-8");
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");
header("Content-Disposition: attachment; filename=\"export_".date("Y-m-d").".xls\"");




$writer->save('php://output');
}
catch(Exception $e)
{
	echo $e->getMessage();
}


//echo '<p>Works fine!! </p>';
?>
