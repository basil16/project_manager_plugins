<?php
/*
	This script will change the invoice status.
*/

include('../../../wp-includes/wp-db.php');
require_once('../../../wp-load.php');

$status = $_POST['status'];
$id     = $_POST['id'];

global $wpdb;

$result = $wpdb->update('invoices', 
	array('status' => $status),
	array('id' => $id)
);

if ($result) {
	echo true;
} else {
	echo $result;
}