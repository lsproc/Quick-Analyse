<?php
if($argc < 3) { die("Insufficient parameters\n"); }

$filter = $argv[1];
$field = $argv[2];

if($argc < 4) { $extra_sql = $argv[3]; } else { $extra_sql = ''; }

$title = $field.' by '.$filter;
if ($extra_sql != '') {
	$title .= ' and SQL: '.$extra_sql;
}

$title .= "\n";

mysql_connect('localhost', 'root', '');
mysql_select_db('test');
$sql = 'SELECT * FROM sexsurvey '.$extra_sql;
$res = mysql_query($sql);

$data = array();
$data_count = array();

$filter_options = array();
$field_options = array();

while($row = mysql_fetch_assoc($res)) {
	if(!array_key_exists($filter, $row)) {
		die($filter." not in row\n");
	}

	if(!array_key_exists($field, $row)) {
		die($field." not in row\n");
	}
	$filter_val = $row[$filter];
	$field_val = $row[$field];

	if(!array_key_exists($filter_val, $data)) {
		$data[$filter_val] = array();
		$data_count[$filter_val] = 0;
	}

	$data_count[$filter_val]++;

	if(!array_key_exists($field_val, $data[$filter_val])) {
		$data[$filter_val][$field_val] = 0;
	}

	$data[$filter_val][$field_val]++;

	$filter_options[] = $filter_val;
	$field_options[] = $field_val;
}

echo $title."\n";

$filter_options = array_unique($filter_options);
$field_options = array_unique($field_options);

asort($filter_options);
asort($field_options);

foreach($filter_options as $option) {
	echo strtoupper($option."\n\n");

?>
Option			Count			Percentage
======			=====			==========
<?php

	foreach($field_options as $field_opt) {
		if(!array_key_exists($field_opt, $data[$option])) {
			$data[$option][$field_opt] = 0;
		}

		echo $field_opt."\t\t\t".$data[$option][$field_opt]."\t\t\t".round(($data[$option][$field_opt]/$data_count[$option])*100,2)."\n";
	}
	echo "\n";
}
return;
 /* CAT:Bar Chart */ 

 /* pChart library inclusions */ 
 include("pchart/class/pData.class.php"); 
 include("pchart/class/pDraw.class.php"); 
 include("pchart/class/pImage.class.php"); 

 /* Create and populate the pData object */ 
 $MyData = new pData();

foreach($filter_options as $option) {
 $g_values = array();
 foreach($field_options as $field_opt) {
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
 $myPicture->drawText(20, 30,"The Felix Sex Survey 2012",array("FontSize"=>12,"FontWeight" => "Bold"));
 $myPicture->drawText(20, 60,$field.' organised by '.$filter,array("FontSize"=>20,"FontWeight" => "Bold"));
 $myPicture->setShadow(FALSE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
 $myPicture->drawFromJPG(1440,10,'cat.jpg');
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
 $myPicture->drawLegend(10, 100,array("Style"=>LEGEND_NOBORDER, "Mode"=>LEGEND_VERTICAL)); 

 /* Render the picture (choose the best way) */

 if ($extra_sql != '') { $extra_sql = md5($extra_sql); }

 $myPicture->render("output/".$field.'-'.$filter.$extra_sql.".png"); 
