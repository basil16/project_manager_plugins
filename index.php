<?php 
/*
Plugin Name: Project Manager
Version: 0.1
Description: Project manager list.
Author: Basil
Author URI: http://localhost/myfiles/wordpress/plugins/
Plugin URI: http://localhost/myfiles/wordpress/plugins/
wp-digg-this
*/

wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('jquery-ui-dialog');
wp_enqueue_style('jquery-style', 'http://ajax.aspnetcdn.com/ajax/jquery.ui/1.10.0/themes/smoothness/jquery-ui.css');


function flat_ui_kit() {
wp_enqueue_style('flat-ui-kit', plugins_url('css/flat-ui.min.css', __FILE__));
 
}
 
add_action('wp_enqueue_scripts', 'flat_ui_kit');

function my_project() {
ob_start();
create_project_list();
return ob_get_clean();
}

function my_invoice() {
ob_start();
create_invoice_list();
return ob_get_clean();
}

add_shortcode('project_table', 'my_project');
add_shortcode('invoice_table', 'my_invoice');

function create_invoice_list()
{
	global $wpdb; 
	$results = $wpdb->get_results("SELECT * FROM invoices ORDER by date DESC");
?>
	<button id="add-invoice-btn" style="float: right;font-size: 12px;margin: 10px 0;">Add invoice</button>
	<div id="basil-project-panel" style="background-color: #eee; padding: 15px;position: relative;width: 100%;border: 1px solid #ccc;overflow: auto;">
		<div class="header-table" style="overflow: auto;border-bottom: 1px solid #fff;">
			<div style="width: 20%; float: left;">
				<div><b>#</b></div>
			</div>
			<div style="width: 20%; float: left;">
				<div><b>Date</b></div>
			</div>
			<div style="width: 20%; float: left;">
				<div><b>Number</b></div>
			</div>
			<div style="width: 20%; float: left;">
				<div><b>Amount</b></div>
			</div>
			<div style="width: 20%; float: left;">
				<div><b>Status</b></div>
			</div>
		</div>
		<div class="body-table" style="color: gray;">
		<?php foreach ($results as $invoice) : ?>
		<div style="font-size: 14px;" class="">
			<div style="width: 20%; float: left;">
				<div><?php echo $invoice->id; ?></div>
			</div>
			<div style="width: 20%; float: left;">
				<div><?php echo $invoice->date; ?></div>
			</div>
			<div style="width: 20%; float: left;">
				<div><?php echo $invoice->number; ?></div>
			</div>
			<div style="width: 20%; float: left;">
				<div>&euro;<?php echo $invoice->amount; ?></div>
			</div>
			<div style="width: 20%; float: left;">
				<div data-id="<?php echo $invoice->id; ?>" id="<?php echo $invoice->id; ?>">
					<?php $status = $invoice->status; ?>
					<?php if ($status == 'received') : ?>
					Received
					<?php else: ?>
					<select id="select_status">
						<option value="open">Open</option>
						<option value="received"> Received</option>
					</select>
					<a style="cursor: pointer; display: none; margin-left: 5px;" class="btn-save-status">Save</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
			endforeach;
		?>
		</div>
	</div>
	<div id="dialog-invoice" style="display: none;">
		<p>Add invoice.</p>
		<div>
			<label>Invoice #</label>
			<input style="font-size: 12px;" type="text" id="invoice-number">
		</div>
		<div>
			<label>Date</label>
			<input style="font-size: 12px;" type="text" id="invoice-date">
		</div>
		
		<div>
			<label>Amount</label>
			<input style="font-size: 12px;" type="text" id="invoice-amount">
		</div>
		<div>
			<p>Status<br>
			<input style="margin-left: 5px;cursor: pointer;" id="open" type="radio" value="open" name="invoice-status"> <label style="cursor: pointer;" for="open">Open</label> <br>
			<input style="margin-left: 5px;cursor: pointer;" id="received" type="radio" value="received" name="invoice-status"><label style="cursor: pointer;" for="received"> Received</label>
			</p>
		</div>
	</div>
	<script>
		
		function addInvoice()
		{
			var status = '';
			var invoice_number = jQuery( '#invoice-number' ).val();
			var invoice_date   = jQuery( '#invoice-date' ).val();
			var invoice_amount = jQuery( '#invoice-amount' ).val();
			
			if (jQuery('#open').is(':checked')) {
				status = "open";
			}
			if (jQuery('#received').is(':checked')) {
				status = "received";
			}
			
			if (status == '' || invoice_number == '' || invoice_date == '' || invoice_amount == '') {
				alert('Error, All fields are required.');
			} 
			else if (isNaN(invoice_amount)) {
				alert('Amount must be a number.');
			}
			else {
				
				var con = confirm('Are you sure to save the invoice?');
			
				if (con) {
					jQuery.ajax({
						type : 'POST',
						url  : '<?php echo plugins_url().'/project_manager/saveInvoice.php'; ?>',
						data : {
							invoice_number : invoice_number,
							invoice_date   : invoice_date,
							invoice_amount : invoice_amount,
							status 		   : status
						},
						success: function(data) {
							if (data) {
								alert("Invoice successfully added.");
								location.reload();
							} else {
								alert('Error occured. Pleas try again later.');
							}
						}
					});
				}
			}
		}
	
		jQuery('#select_status').on('change', function() {
			jQuery(this).next().toggle();
		});
		
		jQuery('.btn-save-status').click(function() {
			
			var con = confirm("Are you sure to update?");
			
			if (con) {
				var parent_div = jQuery(this).parent();
				var id = parent_div.attr('data-id');
				
				jQuery.ajax({
					type: 'POST',
					url : '<?php echo plugins_url().'/project_manager/changeStatus.php'; ?>',
					data: {
						status: jQuery('#select_status').val(),
						id: id
					},
					success: function(data) {
						
						if (data) {
							jQuery('#'+id).html('');
							jQuery('#'+id).append('Received');
							alert('Update successful.');
							location.reload();
						} else {
							alert("An error occured. Please Try Again.");
						} 
					}
				});
			} 
		});
		
		jQuery('#add-invoice-btn').click(function() {
			
			jQuery('#dialog-invoice').dialog({
			width: '600',
			modal: true,
			buttons: {
				'Save Invoice': function() {
					addInvoice();
				}	
			}
			});
			
			jQuery('.ui-dialog-titlebar').css({
				'background': 'none',
				'border': 'none'
			});
			jQuery('.ui-widget-header').css({
				'background-color': 'none',
				'border': 'none'
			});
			jQuery('.ui-button').css({
				'font-size' : '14px',
			});
			
			jQuery('#invoice-date').datepicker({dateFormat: "yy-mm-dd"});
		});
	</script>
<?php
}

function create_project_list()
{
	global $wpdb;
	
	$results_by_open_status = $wpdb->get_results("SELECT * FROM projects WHERE status = '1' ORDER BY project_start_date DESC");
	$results_by_close_status = $wpdb->get_results("SELECT * FROM projects  WHERE status = '0' ORDER BY project_start_date DESC");
?>	
	
	<span style="color: darkred;" id="basil-error"></span><br> 
	<div>
	<h3 style="float: left;">Project Overview</h3>
	<button id="add-project-btn" style="float: right;margin: 10px 0;font-size: 12px;" type="button">Add Project</button>
	</div>
	<div id="basil-project-panel" style="background-color: #eee; padding: 15px;position: relative;width: 100%;border: 1px solid #ccc;overflow: auto;">
		<div class="header-table" style="overflow: auto;border-bottom: 1px solid #fff;">
			<div style="width: 33%; float: left;">
				<div><b>Project Start</b></div>
			</div>
			<div style="width: 33%; float: left;">
				<div><b>Project Name</b></div>
			</div>
			<div style="width: 33%; float: left;">
				<div><b>Project Value</b></div>
			</div>
		</div>
		<div class="body-table" style="color: gray;font-size: 14px;">
		<?php foreach ($results_by_open_status as $project) : ?>
		<div data-status="<?php echo $project->status; ?>" class="" id="<?php echo $project->id; ?>">
			
			<div style="width: 33%; float: left;">
				<div><?php echo $project->project_start_date; ?></div>
			</div>
			<div style="width: 33%; float: left;">
				<div><?php echo $project->project_name; ?></div>
			</div>
			<div style="width: 33%; float: left;">
				<div>&euro; <b><?php echo $project->project_value; ?></b>
					<a class="add-value" data-id="<?php echo $project->id; ?>" style="cursor: pointer; font-size: 12px;float: right;text-decoration: none;">Edit<span style="float: right;margin-left: 4px;"class="ui-icon ui-icon-pencil"></span></a>
				</div>
			</div>
			
		</div>
		<?php
			endforeach;
		?>
		<?php foreach ($results_by_close_status as $project) : ?>
		<div data-status="<?php echo $project->status; ?>" class="" id="<?php echo $project->id; ?>">
			
			<div style="width: 33%; float: left;">
				<div><?php echo $project->project_start_date; ?></div>
			</div>
			<div style="width: 33%; float: left;">
				<div><?php echo $project->project_name; ?></div>
			</div>
			<div style="width: 33%; float: left;">
				<div>&euro; <b><?php echo $project->project_value; ?></b>
					<a class="add-value" data-id="<?php echo $project->id; ?>" style="cursor: pointer; font-size: 12px;float: right;text-decoration: none;">Edit<span style="float: right;margin-left: 4px;"class="ui-icon ui-icon-pencil"></span></a>
				</div>
			</div>
			
		</div>
		<?php
			endforeach;
		?>
		</div>
	</div>
	<div style="display: none;" id="dialog">
	
		<div style="font-size: 14px;" id="customer-info">
			<p>Customer:</p>
		</div>
		<div style="padding: 20px 0;">
			<button onclick="createInput()" style="float: right;margin-bottom: 10px;font-size: 10px;" id="add-new-btn">Add New Payment</button>
		</div>
		<p>Payment History</p>
		<div>
			<table id="payment-table">
				<thead style="background-color: #eee;"> 
					<tr style="font-size: 14px;">
						<th>#</th>
						<th>Value</th>
						<th>Date</th>
						<th>Description</th>
						<th>User</th>
					</tr>
				</thead>
				<tbody id="payment-table-body">
					
				</tbody>
			</table>
		</div>
	</div>
	<script>
	var project_id = '';
	
	jQuery( '#add-project-btn' ).click(function() {
		jQuery(this).attr('disabled','disabled');
		
		if ( jQuery("#new-project").length == 0 ) {
			jQuery( '#basil-project-panel .body-table' ).append(
			'<div id="new-project">'	
			+'<div style="width: 33%; float: left;" >'
				+ '<input id="project-name" type="text" placeholder="Project name">'
			+'</div>' 
			+'<div  style="width: 33%; float: left;" >'
				+ '<input id="project-start-date" type="text" placeholder="Date">'
			+'</div>'
			+'<div  style="width: 33%; float: left;" >'
				+ '<input id="project-value" type="text" placeholder="Value">'
			+'</div>'
			+'<div style="padding: 10px;text-align: right;"><button onclick="saveProject()" style="font-size: 14px;margin: 5px">Save</button>'
			+'<button onclick="ref()" style="font-size: 14px;margin-right: 0;">Cancel</button></div>'
			+'</div>'
			);
		
			jQuery( '#project-start-date' ).datepicker({
				dateFormat: "yy-mm-dd"
			});
		} else {
			jQuery("#new-project").show();
		}
		
	});	
	
	function ref() 
	{
		jQuery("#new-project").hide();
		jQuery('#add-project-btn').removeAttr('disabled');
		jQuery('#project-name').val('');
		jQuery('#project-start-date').val('');
		jQuery('#project-value').val('');
	}
	
	function saveProject()
	{
		var da = jQuery('#project-start-date').val();
		
		var amount = jQuery('#project-value').val();
		
		if (!empty()) {
			if (integer_is_valid(amount)) {
				jQuery.ajax({
					type: 'POST',
					url: '<?php echo plugins_url().'/project_manager/save.php'; ?>',
					data : {
						'project_name': jQuery('#project-name').val(),
						'project_start_date' : jQuery('#project-start-date').val(),
						'project_value' : jQuery('#project-value').val(),
					},
					success: function( data ) {
						alert(data);
						location.reload();
					},
					error: function( error ) {
						alert(error);
					}
				});
			} else {
				jQuery('#basil-error').text('Invalid amount.');
			}
		} else {
			jQuery('#basil-error').text('Please input fields.');
		}
	}
	
	function empty() 
	{
		
		var name = jQuery( '#project-name' ).val();
		var date = jQuery( '#project-start-date' ).val();
		var value = jQuery( '#project-value' ).val();
		
		if (name == '' || date == '' || value == '') {
			return true;
		}
		return false;
	}
	
	function integer_is_valid(num)
	{
		if (isNaN(num)) {
			return false;
		} 
		return true;
	}
	
	function savePayment()
	{
		jQuery.ajax({
			type: 'POST',
			url : '<?php echo plugins_url().'/project_manager/savePayment.php'; ?>',
			data: {
				value      : jQuery('#payment-value').val(),
				date       : jQuery('#payment-date').val(),
				description: jQuery('#payment-description').val(),
				user       : jQuery('#payment-user').val(),
				project_id : project_id
			},		
			success: function(data) {
				alert(data);
				location.reload();
			},
			error: function(error) {
				alert(error);
			}
		});
	}
	
	function createInput()
	{
		jQuery('#add-new-btn').attr('disabled','disabled');
		
		var inputs = '<tr>'+
					 '<td><input type="text" disabled="disabled"></td>'+
					 '<td><input id="payment-value" type="text" autofocus></td>'+
					 '<td><input id="payment-date" type="text"></td>'+
					 '<td><input id="payment-description" type="text"></td>'+
					 '<td><input id="payment-user" type="text"></td>'+
				'</tr>';
		
		var count = jQuery('#payment-table-body > tr').length;
		
		if (count > 0) {
		
			jQuery('#payment-table-body tr:last').before(inputs);
		} else {
			jQuery('#payment-table-body').append(inputs);
		}
		
		jQuery('#payment-date').datepicker({dateFormat: "yy-mm-dd"});
	}
	
	function getProjectInfoBy(id)
	{
		jQuery('#payment-table-body').html('');
		jQuery.ajax({
			type: 'POST',
			url: '<?php echo plugins_url().'/project_manager/projectDetails.php'; ?>',		
			data: {
				id : id
			},
			dataType: 'json',
			success: function(data) {
				var total = 0;
				data.forEach(function(obj) {
					total = parseInt(total) + parseInt(obj.value);
					
					var value = obj.value;
					
					if (value < 0) {
						num = value.slice(1);
						value = '- ' + '&euro;' + num;
					} else {
						value = '&nbsp;&nbsp;&euro;' + value;
					}
					
					jQuery('#payment-table-body').append(
						'<tr style="font-size: 14px;">'+
							'<td>' + obj.id + '</td>'+
							'<td>' + value + '</td>'+
							'<td>' + obj.date +'</td>'+
							'<td>' + obj.description +'</td>'+
							'<td>' + obj.user +'</td>'+
						'</tr>'
					);
				});
				
				if (total > 0) {
					jQuery('#payment-table-body').append(
						'<tr style="color: darkred;">'+
							'<td>Total</td>'+
							'<td>&nbsp;&euro;'+ total +'</td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
						'</tr>'
					);
				}
			}
		});
	}
	/*css*/
	
	jQuery('.row-project').hover(function() {
			jQuery(this).css('color','darkblue');
		}, function() {
			jQuery(this).css('color','gray');
		}
	);
	
	jQuery('.add-value').click(function() {
		
		project_id = jQuery(this).attr('data-id');
		
		// enable add payment button
		jQuery('#add-new-btn').removeAttr('disabled');
		
		// clear table
		jQuery('#payment-table-body > tr').remove();
		
		// get project information
		getProjectInfoBy(project_id);
		
		// open modal
		jQuery('#dialog').dialog({
			width: jQuery(window).width(),
			height: '700',
			modal: true,
			buttons: {
				'Save Payment': function() {
					savePayment();
				}	
			}
		});
		
		jQuery('.ui-dialog-titlebar').css({
			'background': 'none',
			'border': 'none'
		});
		jQuery('.ui-widget-header').css({
			'background-color': 'none',
			'border': 'none'
		});
		jQuery('.ui-button').css({
			'font-size' : '14px'
		}); 
		
	});
	
	jQuery('div[data-status=0]').css('color','darkred');
	jQuery('div[data-status=1]').css('color','black');
	
	</script>
<?php 
}