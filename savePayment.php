<?php
include('../../../wp-includes/wp-db.php');
require_once('../../../wp-load.php');

$value       = $_POST['value'];
$date        = $_POST['date'];
$description = $_POST['description'];
$user        = $_POST['user'];
$project_id  = $_POST['project_id'];

global $wpdb;

$result = $wpdb->insert(
	'project_payments',
	array(
		'value'       => $value,
		'date'        => $date,
		'description' => $description,
		'user'        => $user,
		'project_id'  => $project_id
	)
);

if ($result) {
	echo "Payment successfully added";
} else {
	echo "An error occured. Please Try Again.";
}