<?php
if($argc < 3) { die("Insufficient parameters\n"); }

$filter = $argv[1];
$field = $argv[2];

if($argc == 4) { $extra_sql = $argv[3]; } else { $extra_sql = ''; }

$title = $field.' by '.$filter;
if ($extra_sql != '') {
	$title .= ' and SQL: '.$extra_sql;
}

$title .= "\n";

mysql_connect('localhost', 'root', '');
mysql_select_db('test');
$sql = 'SELECT * FROM sexsurvey '.$extra_sql;
$res = mysql_query($sql);

if ($res == FALSE) {
	die('Error: '.mysql_error()."\n");
}

$data = array();
$data_count = array();

/*
 * Some explanation:
 *
 * Filter values are what we compare the data to
 * Field values are the corresponding values for that field in the response
 *
 * For example, if our filter is gneder, and field is eye colour
 * filter values are Male/Female/(Don't want to say)
 * field values are blue/green/brown/hazel/...
 */

$filter_options = array();
$field_options = array();

// For every response
while($row = mysql_fetch_assoc($res)) {
	// If no column for filter
	if(!array_key_exists($filter, $row)) {
		die($filter." not in row\n");
	}

	// If no column for field
	if(!array_key_exists($field, $row)) {
		die($field." not in row\n");
	}

	// The filter and field values for this response
	$filter_val = $row[$filter];
	$field_val = $row[$field];

	// If we havent tracked any data for this filter value, start tracking
	if(!array_key_exists($filter_val, $data)) {
		$data[$filter_val] = array();
		$data_count[$filter_val] = 0;
	}

	// We have recorded a new response for this filter value
	$data_count[$filter_val]++;

	// If we havent recorded any responses for the field value of this response, for the given filter value, start tracking
	// i.e. if we havent yet recorded any men with blue eyes
	if(!array_key_exists($field_val, $data[$filter_val])) {
		$data[$filter_val][$field_val] = 0;
	}

	// Record that a new match has been made
	$data[$filter_val][$field_val]++;

	// Add the recorded options to a list
	$filter_options[] = $filter_val;
	$field_options[] = $field_val;
}

echo $title."\n";

// Only use recorded options once
$filter_options = array_unique($filter_options);
$field_options = array_unique($field_options);

// Alphabetical sort
asort($filter_options);
asort($field_options);

// For every filter value, report data
foreach($filter_options as $option) {
	echo strtoupper($option."\n\n");

?>
Option			Count			Percentage
======			=====			==========
<?php

	// For every possible field option, give the number of responses with this field value, and that as a percentage of all responses within this filter value
	foreach($field_options as $field_opt) {
		if(!array_key_exists($field_opt, $data[$option])) {
			$data[$option][$field_opt] = 0;
		}

		echo $field_opt."\t\t\t".$data[$option][$field_opt]."\t\t\t".round(($data[$option][$field_opt]/$data_count[$option])*100,2)."\n";
	}
	// Also report all responses within this filter
	echo "All responses\t\t".$data_count[$option]."\t\t\t100\n\n";
}
//return;
 /* CAT:Bar Chart */ 

 /* pChart library inclusions */ 
 include("pchart/class/pData.class.php"); 
 include("pchart/class/pDraw.class.php"); 
 include("pchart/class/pImage.class.php"); 

 /* Create and populate the pData object */

// good luck beyond this point
 $MyData = new pData();

// For every filter value (coloured sets)
foreach($filter_options as $option) {
 $g_values = array();
 foreach($field_options as $field_opt) {
	// record the perecentage for each field value
	$g_values[] = round(($data[$option][$field_opt]/$data_count[$option])*100,2);
 }
 $MyData->addPoints($g_values,$option);
}
 $MyData->setAxisName(0,"%"); 
 $MyData->addPoints($field_options,"Labels"); 
 $MyData->setSerieDescription("Labels","Choice"); 
 $MyData->setAbscissa("Labels"); 

 /* Create the pChart object */ 
 $myPicture = new pImage(1500,1000,$MyData);
 $myPicture->setFontProperties(array("FontName"=>"pchart/fonts/verdana.ttf","FontSize"=>12));
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
 $RectangleSettings = array("R"=>180,"G"=>180,"B"=>180,"Alpha"=>40,"Dash"=>TRUE,"DashR"=>240,"DashG"=>240,"DashB"=>240,"BorderR"=>100, "BorderG"=>100,"BorderB"=>100); 
 $myPicture->drawFilledRectangle(1,1,1499,75,$RectangleSettings);
 $myPicture->drawText(20, 30,"Quick Analysis",array("FontSize"=>12,"FontWeight" => "Bold"));
 $myPicture->drawText(20, 60,$field.' organised by '.$filter,array("FontSize"=>20,"FontWeight" => "Bold"));
 $myPicture->setShadow(FALSE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
 $myPicture->drawFromPNG(1300,10,'cat.png');
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
 $myPicture->drawText(20, 960,date("F j, Y, g:i a") ,array("FontSize"=>12,"FontWeight" => "Bold"));
 $myPicture->drawText(20, 980,"© Felix Imperial - www.felixonline.co.uk",array("FontSize"=>12,"FontWeight" => "Bold"));

 /* Draw the scale and the 1st chart */ 
 $myPicture->setGraphArea(400,100,1450,950);
 $AxisBoundaries = array(0=>array("Min"=>0,"Max"=>100));
 $ScaleSettings  = array("Mode"=>SCALE_MODE_START0,"DrawSubTicks"=>TRUE,"DrawArrows"=>TRUE,"ArrowSize"=>6);
 $myPicture->drawScale($ScaleSettings); 
 $myPicture->drawBarChart(array("DisplayValues"=>FALSE,"DisplayColor"=>DISPLAY_AUTO,"Surrounding"=>30)); 

 /* Write the chart legend */ 
 $myPicture->drawText(10, 105,$filter.' options',array("FontSize"=>12,"FontWeight" => "Bold"));
 $myPicture->drawLegend(10, 120,array("Style"=>LEGEND_NOBORDER, "Mode"=>LEGEND_VERTICAL)); 

 /* Render the picture (choose the best way) */

 if ($extra_sql != '') { $extra_sql = md5($extra_sql); }

 $myPicture->render("output/".$field.'-'.$filter.$extra_sql.".png"); 
