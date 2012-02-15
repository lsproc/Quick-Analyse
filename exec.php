<?php
mysql_connect('localhost', 'root', '');
mysql_select_db('test');
$sql = 'SELECT * FROM sexsurvey_data';
$res = mysql_query($sql);

if($argc != 3) { die("Insufficient parameters\n"); }

$filter = $argv[1];
$field = $argv[2];

$title = $field.' by '.$filter."\n";

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

foreach($filter_options as $option) {
	echo strtoupper($option."\n\n");

?>
Option		Count		Percentage
======		=====		==========
<?php

	foreach($field_options as $field_opt) {
		if(!array_key_exists($field_opt, $data[$option])) {
			$data[$option][$field_opt] = 0;
		}

		echo $field_opt."\t".$data[$option][$field_opt]."\t".round(($data[$option][$field_opt]/$data_count[$option])*100,2)."\n";
	}
	echo "\n";
}
?>