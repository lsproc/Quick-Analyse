<?php
mysql_connect('localhost', 'root', '');
mysql_select_db('test');
$sql = 'SELECT * FROM sexsurvey_data';
$res = mysql_query($sql);

$female = array('yes' => 0, 'no' => 0);
$male = array('yes' => 0, 'no' => 0);

$count_female = 0;
$count_male = 0;

while ($row = mysql_fetch_assoc($res)) {
	if ($row['gender'] == 'Male') {
		if ($row['relationship'] == 'Yes') {
			$male['yes']++;
		} else {
			$male['no']++;
		}

		$count_male++;
	} else {
		if ($row['relationship'] == 'Yes') {
			$female['yes']++;
		} else {
			$female['no']++;
		}

		$count_female++;
	}
}

?>
<h1>In a relationship vs gender</h1>
<h2>Female</h2>
<table>
<tr><th>Option</th><th>Count</th><th>Percentage</th></tr>
<tr><td>Yes</td><td><?php echo $female['yes']; ?></td><td><?php echo ($female['yes']/$count_female)*100; ?></td></tr>
<tr><td>No</td><td><?php echo $female['no']; ?></td><td><?php echo ($female['no']/$count_female)*100; ?></td></tr>
</table>
<h2>Male</h2>
<table>
<tr><th>Option</th><th>Count</th><th>Percentage</th></tr>
<tr><td>Yes</td><td><?php echo $male['yes']; ?></td><td><?php echo ($male['yes']/$count_male)*100; ?></td></tr>
<tr><td>No</td><td><?php echo $male['no']; ?></td><td><?php echo ($male['no']/$count_male)*100; ?></td></tr>
</table>
<?php 
 /* CAT:Bar Chart */ 

 /* pChart library inclusions */ 
 include("pchart/class/pData.class.php"); 
 include("pchart/class/pDraw.class.php"); 
 include("pchart/class/pImage.class.php"); 

 /* Create and populate the pData object */ 
 $MyData = new pData();   
 $MyData->addPoints(array(round(($male['yes']/$count_male)*100, 2),round(($male['no']/$count_male)*100, 2)),"Male"); 
 $MyData->addPoints(array(round(($female['yes']/$count_female)*100, 2),round(($female['no']/$count_female)*100, 2)),"Female"); 
 $MyData->setAxisName(0,"%"); 
 $MyData->addPoints(array("Yes", "No"),"Labels"); 
// $MyData->setSerieDescription("Labels","Choice"); 
 $MyData->setAbscissa("Labels"); 

 /* Create the pChart object */ 
 $myPicture = new pImage(1500,500,$MyData);
 $myPicture->setFontProperties(array("FontName"=>"./fonts/verdana.ttf","FontSize"=>11));
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
 $RectangleSettings = array("R"=>180,"G"=>180,"B"=>180,"Alpha"=>40,"Dash"=>TRUE,"DashR"=>240,"DashG"=>240,"DashB"=>240,"BorderR"=>100, "BorderG"=>100,"BorderB"=>100); 
 $myPicture->drawFilledRectangle(-5,-5,1600,75,$RectangleSettings);
 $myPicture->drawText(20, 40,"In a relationship by Gender",array("FontSize"=>14,"FontWeight" => "Bold"));
 $myPicture->setShadow(FALSE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
 $myPicture->drawFromJPG(1440,10,'cat.jpg');
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));

 /* Draw the scale and the 1st chart */ 
 $myPicture->setGraphArea(50,100,1450,450);
 $AxisBoundaries = array(0=>array("Min"=>0,"Max"=>100));
 $ScaleSettings  = array("Mode"=>SCALE_MODE_MANUAL,"ManualScale"=>$AxisBoundaries,"DrawSubTicks"=>TRUE,"DrawArrows"=>TRUE,"ArrowSize"=>6);
 $myPicture->drawScale($ScaleSettings); 
 $myPicture->drawBarChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO,"Rounded"=>TRUE,"Surrounding"=>30)); 

 /* Write the chart legend */ 
 $myPicture->drawLegend(10, 480,array("Style"=>LEGEND_NOBORDER, "Mode"=>LEGEND_HORIZONTAL)); 

 /* Render the picture (choose the best way) */ 
 $myPicture->render("gender.png"); 
?>