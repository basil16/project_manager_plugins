<?php
include('../../../wp-includes/wp-db.php');
require_once('../../../wp-load.php');

$project_id = $_POST['id'];

global $wpdb;

$result = $wpdb->get_results("SELECT * FROM project_payments WHERE project_id='$project_id'", OBJECT);

echo json_encode($result);