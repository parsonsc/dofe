<?php
define('WP_USE_THEMES', false);
require_once("../../../wp-config.php");
	if (!current_user_can('manage_options')) {
		die('do one');
	}
global $wpdb;

$storytext = $wpdb->get_results($wpdb->prepare("SHOW COLUMNS FROM ". $wpdb->prefix . 'signups'.";"), ARRAY_A);
$headers = array();

if ($storytext !== null) {
	foreach ( $storytext as $row ) 
		$headers[] = '"'. str_replace('"', '""', $row['Field']) .'"';
}

$csv_output .= join(',',$headers);
$csv_output .= "\n";

$rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM ". $wpdb->prefix . 'signups'.";"), ARRAY_A);
$data = $wpdb->get_results( 
		$wpdb->prepare( 
			"SELECT * FROM ". $wpdb->prefix . 'signups' . "  ;"
		), ARRAY_A
	);
foreach ($data as $k => $v)
{
	foreach ($v as $u => $r)
	{
		$data[$k][$u] = '"'. str_replace('"', '""', stripslashes($r)) .'"';
	}
	$csv_output .= join(',',$data[$k]);
	$csv_output .= "\n";	
}



$filename = "userdata_".date("Y-m-d_H-i",time());
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header( "Content-disposition: filename=".$filename.".csv");
print $csv_output;
exit;