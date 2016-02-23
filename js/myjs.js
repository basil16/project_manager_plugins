jQuery( document ).ready(function() {
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
			+'<div style="padding: 10px;text-align: right;"><button onclick="saveProject()" style="margin: 5px">Save</button>'
			+'<button onclick="ref()" style="margin-right: 0;">Cancel</button></div>'
			+'</div>'
			);
		} else {
			jQuery("#new-project").show();
		}
	});	
	
	function ref() 
	{
		jQuery("#new-project").hide();
		jQuery('#add-project-btn').removeAttr('disabled');
	}
	
	function saveProject()
	{
		integer_is_valid();
		
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
			},
			error: function( error ) {
				alert(error);
			}
		});
	}
	
	function integer_is_valid()
	{
		if (isNaN(jQuery('#project-value'))) {
			alert('Invalid Amount');
		}
	}
});