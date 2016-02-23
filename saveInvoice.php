<?php
/*
	This script will change the invoice status.
*/

include('../../../wp-includes/wp-db.php');
require_once('../../../wp-load.php');

$status = $_POST['status'];
$number = $_POST['invoice_number'];
$date   = $_POST['invoice_date'];
$amount = $_POST['invoice_amount'];

global $wpdb;

$result = $wpdb->insert('invoices', 
	array(
		'status' => $status,
		'date'   => $date,
		'number' => $number,
		'amount' => $amount
	)
);

if ($result) {
	echo true;
} else {
	echo $result;
} 