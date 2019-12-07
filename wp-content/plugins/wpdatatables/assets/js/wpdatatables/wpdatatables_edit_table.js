var columnTypes = [
	{ name: 'string', value: 'String' },
	{ name: 'int', value: 'Integer' },
	{ name: 'float', value: 'Float' },
	{ name: 'date', value: 'Date' },
	{ name: 'link', value: 'URL link' },
	{ name: 'email', value: 'E-mail link' },
	{ name: 'image', value: 'Image' }
];

var filterTypes = [
	{ name: 'none', value: 'None' },
	{ name: 'text', value: 'Text' },
	{ name: 'number', value: 'Number' },
	{ name: 'number-range', value: 'Number range' },
	{ name: 'date-range', value: 'Date range' },
	{ name: 'select', value: 'Select box' },
	{ name: 'checkbox', value: 'Checkbox' }
];

var editorTypes = [
	{ name: 'none', value: 'None' },
	{ name: 'text', value: 'One-line edit' },
	{ name: 'textarea', value: 'Multi-line edit' },
	{ name: 'selectbox', value: 'Single-value selectbox' },
	{ name: 'multi-selectbox', value: 'Multi-value selectbox' },
	{ name: 'date', value: 'Date' },
	{ name: 'link', value: 'URL link' },
	{ name: 'email', value: 'E-mail link' },
	{ name: 'attachment', value: 'Attachment' }
];

var createColumnsBlock;
var tableChanged = false;
var columnsChanged = false;
var custom_uploader;

(function($){
	

    createColumnsBlock = function(columns){
		var block_html = '';
		
		var columnsTableTmpl= $.templates("#columnsTableTmpl")
		block_html = columnsTableTmpl.render({ 
						columns_data: columns,
						filterTypes: filterTypes,
						columnTypes: columnTypes,
						editorTypes: editorTypes,
						tableEditable: $('#wpTableEditable').is(':checked')
					});
			
		return block_html;
    }

    function applySortable(){
		$('tr.sort_columns_block').sortable({
		    stop: function(){
				$('tr.sort_columns_block > td').each(function(){
				    $(this).find('input.columnPos').val($('tr.sort_columns_block > td').index(this));
				});
		    }
		});
    }

    $(document).ready(function(){
    	
		$('#wpTableType').change(function(){
		    if($(this).val()=='mysql'){
				$('tr.mysqlquery_row').show();
				$('tr.inputfile_row').hide();
				$('tr.serverside_row').show();
				$('tr.table_editable_row').show();
		    }else if($(this).val() != ''){
				$('tr.mysqlquery_row').hide();
				$('tr.inputfile_row').show();
				$('tr.serverside_row').hide();
				$('tr.table_editable_row').hide();
				$('tr.table_mysql_name_row').hide();
				$('tr.editable_table_column_row').hide();
		    }else{ 
				$('tr.mysqlquery_row').hide();
				$('tr.inputfile_row').hide();
				$('tr.serverside_row').hide();
				$('tr.table_editable_row').hide();
				$('tr.editable_table_column_row').hide();
				$('tr.table_mysql_name_row').hide();
		    }
		});
	    
		$('#wpTableEditable').change(function(){
		    if($(this).is(':checked')){
				$('tr.table_mysql_name_row').show();
				$('tr.editor_roles_row').show();
				$('tr.editable_table_column_row').show();
				$('#wpServerSide').attr('checked','checked');
				$('input.groupColumn').removeAttr('checked');
		    }else{
				$('tr.table_mysql_name_row').hide();
				$('tr.editable_table_column_row').hide();
				$('tr.editor_roles_row').hide();
		    }
		});
	    
		$('#wpServerSide').change(function(){
		    if(!$(this).is(':checked')){
				$('#wpTableEditable').removeAttr('checked').change();
		    }
		});
	    
		$('#wpTableEditable').change();
	    
		$('#wdtResponsive').change(function(){
		    if($(this).is(':checked')){
				$('tr.responsive_table_column_row').show();
		    }else{
				$('tr.responsive_table_column_row').hide();
		    }
		});
		
		$('#wdtResponsive').change();
	
		$('#wpUploadFileBtn').click(function(e) {
		    e.preventDefault();
		    e.stopImmediatePropagation();
			e.preventDefault();
	        
	        var mediaType;
	        
	        if($('#wpTableType').val() == 'xls'){
	        	mediaType =  'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	        }else if($('#wpTableType').val() == 'csv'){
	        	mediaType =  'text/csv';
	        }
	 
	        // Extend the wp.media object
	        custom_uploader = wp.media.frames.file_frame = wp.media({
	            title: wpdatatables_edit_strings.select_excel_csv,
	            button: {
	                text: wpdatatables_edit_strings.choose_file
	            },
	            multiple: false,
	            library: {
	            	type: mediaType
	            }
	        });
	 
	        // When a file is selected, grab the URL and set it as the text field's value
	        custom_uploader.on('select', function() {
	            attachment = custom_uploader.state().get('selection').first().toJSON();
	            $('#wpInputFile').val(attachment.url);
	        });
	 
	        //Open the uploader dialog
	        custom_uploader.open();
		    
		});
	    
		$('#wpAddChart').change(function(){
		    if($(this).val() != 'none'){
				$('tr.charttitle_row').show();
				$('tr.chart_row').show();
		    }else{
				$('tr.charttitle_row').hide();
				$('tr.chart_row').hide();
		    }
		});
	
		window.send_to_editor = function(html) {
		    // adding a wrapper so $ could find the element
		    html = '<span>'+html+'</span>';
		    file_url = $('a',html).attr('href');
		    $('#wpInputFile').val(file_url).change();
		    tb_remove();
		}
		
		// Trigger "Table changed" flag
		$('#wpInputFile').change(function(e){
			tableChanged = true;
		});
		
		$('#wpMySQLQuery').change(function(e){
			tableChanged = true;
		});
		
		// Trigger "Columns changed" flag
		$(document).on('change','table.column_table input',function(){
			columnsChanged = true;
		});
	    
		$('.submitStep1').click(function(e){
		    e.preventDefault();
		    e.stopImmediatePropagation();
		    saveTable();
		});
		
	    
		$('.submitStep2').click(function(e){
		    e.preventDefault();
		    e.stopImmediatePropagation();
		    saveTable();
		});
		
		/**
		 * Collect columns data from block
		 */
		 var collectColumnsData = function(){
		 	var columns = [];
		    $('td.columnsBlock table.column_table').each(function(){
				var column = {};
				column.id = $(this).attr('rel');
				column.orig_header = $(this).find('tr.columnHeaderRow td b').html();
				column.css_class = $(this).find('input.cssClasses').val();
				column.display_header = $(this).find('input.displayHeader').val();
				column.possible_values = $(this).find('input.possibleValues').val();
				column.default_value = $(this).find('input.defaultValue').val();
				column.input_type = $(this).find('select.inputType').val();
				column.filter_type = $(this).find('select.filterType').val();
				column.column_type = $(this).find('select.columnType').val();
				column.id_column = $(this).find('input.idColumn').is(':checked');
				column.group_column = $(this).find('input.groupColumn').is(':checked');
				column.sort_column = $(this).find('input.sortColumn').is(':checked') ? $(this).find('input.sortColumn:checked').val() : 0;
				column.hide_on_phones = $(this).find('.hideOnPhones').is(':checked');
				column.hide_on_tablets = $(this).find('.hideOnTablets').is(':checked');
				column.use_in_chart = $(this).find('input.useInChart').is(':checked');
				column.chart_horiz_axis = $(this).find('input.horizontalChart').is(':checked');
				column.pos = $(this).find('input.columnPos').val();
				column.width = $(this).find('input.columnWidth').val();
				column.visible = $(this).find('input.columnVisible').is(':checked');
				columns.push(column);
		    });
		    return columns;
		 }
		
		/**
		 * Save table
		 */
		 var saveTable = function(){
		    if($('#wpTableType').val()==''){
				wdtAlertDialog(wpdatatables_edit_strings.table_type_not_empty,wpdatatables_edit_strings.error_label);
				return;
		    }
		    if(($('#wpMySQLQuery').val() == '')
				&& ($('#wpInputFile').val() == '')){
					wdtAlertDialog(wpdatatables_edit_strings.table_input_source_not_empty,wpdatatables_edit_strings.error_label);
					return;
		    }
		    if($('#wpTableEditable').is(':checked')){
				if($('#wpTableMysqlName').val()==''){
				    wdtAlertDialog(wpdatatables_edit_strings.mysql_table_name_not_set,wpdatatables_edit_strings.error_label); 
				    return;
				}
		    }
		    if($('#wpServerSide').is(':checked')){
				var mysqlquery = $('#wpMySQLQuery').val().toLowerCase();
				if((mysqlquery.indexOf('where') != -1)
					|| (mysqlquery.indexOf('join') != -1)){
				    wdtAlertDialog(wpdatatables_edit_strings.mysql_server_side_query_too_complicated,wpdatatables_edit_strings.error_label); 
				    return;						
				}
		    }
		    // collecting table settings data
		    var data = { };
		    data.action = 'wdt_save_table';
		    data.table_title = $('#wpTableTitle').val();
		    data.table_type = $('#wpTableType').val();
		    if(data.table_type == 'mysql'){
				data.table_content = $('#wpMySQLQuery').val();
		    }else{
				data.table_content = $('#wpInputFile').val();
		    }
		    data.table_editable = $('#wpTableEditable').is(':checked');
		    data.responsive = $('#wdtResponsive').is(':checked');
		    data.table_mysql_name = $('#wpTableMysqlName').val();
		    data.editor_roles = $('#wpTableEditorRoles').html()
		    data.table_advanced_filtering = $('#wpAdvancedFilter').is(':checked');
		    data.table_filter_form = $('#wpAdvancedFilterForm').is(':checked');
		    data.table_tools = $('#wpTableTools').is(':checked');
		    data.table_sorting = $('#wpSortByColumn').is(':checked');
		    data.fixed_layout = $('#wpFixedLayout').is(':checked');
		    data.word_wrap = $('#wpWordWrap').is(':checked');
		    data.table_display_length = $('#wpDisplayLength').val();
		    data.table_fixheader = false;
		    data.table_fixcolumns = 0;
		    data.table_chart = $('#wpAddChart').val();
		    data.table_charttitle = $('#wpChartTitle').val();
		    data.table_serverside = $('#wpServerSide').is(':checked');
		    data.hide_before_loaded = $('#wdtHideBeforeLoaded').is(':checked');
		    data.table_id = $('#wpDataTableId').val();
		    $('#wdtPreloadLayer').show();
		    $.ajax({
				type: 'post',
				url: ajaxurl,
				data: data,
				dataType: 'json',
				success: function(response){
				    if(response.error) {
					    $('#wdtPreloadLayer').hide();
						if(response.error.indexOf('array is empty')){
						    response.error += '<br/>';
						    response.error += wpdatatables_edit_strings.empty_result_error;
						}
						wdtAlertDialog(wpdatatables_edit_strings.backend_error_report+response.error,wpdatatables_edit_strings.error_label);
				    }else{
						$('#wpDataTableId').val(response.table_id);
						$('#wdtScId strong').html('[wpdatatable id='+response.table_id+']');
						$('#message').show();
						
						var frontendColumns = collectColumnsData();
						var mergedColumns = [];
						
						// Merge columns
						for(var i in response.columns){
							var header = response.columns[i].orig_header;
							var columnAdded = false;
							if(frontendColumns.length > 0){
								for(var j in frontendColumns){
									if(frontendColumns[j].orig_header == response.columns[i].orig_header){
										mergedColumns.push(frontendColumns[j]);
										columnAdded = true;
										break;
									}
								}
							}
							if(!columnAdded){
								mergedColumns.push(response.columns[i]);
							}
						}
						
						var columns_block = createColumnsBlock(mergedColumns);

						$('#step2-postbox').show();
						$('tr.step2_row').show();
						$('tr.step2_row td.columnsBlock').html(columns_block);
						applySortable();
						if($('#wpAddChart').val() != 'none'){
						    $('tr.chart_row').show();
						}else{
						    $('tr.chart_row').hide();
						}
						$('.previewButton').show();
						$('#wdtResponsive').change();
						$('#wpTableEditable').change();
						applySelecter();
						
						tableChanged = false;
						
						// If columns are defined - saving them as well
						if($('td.columnsBlock table.column_table').length > 0){
							
						    if($('#wpTableEditable').is(':checked')){
								if($('input.idColumn:checked').length==0){
								    wdtAlertDialog(wpdatatables_edit_strings.id_column_not_set,wpdatatables_edit_strings.error_label); 
								    return;
								}
						    }
						    var data = { };
						    data.action = 'wdt_save_columns';
						    data.table_id = $('#wpDataTableId').val();
						    data.columns = collectColumnsData();

						    $.ajax({
								type: 'post',
								url: ajaxurl,
								data: data,
								dataType: 'json',
								success: function(response){
								    $('#wdtPreloadLayer').hide();
								    if(response.error) {
										wdtAlertDialog(wpdatatables_edit_strings.backend_error_report+' '+response.error,wpdatatables_edit_strings.error_label);
								    }else{
								    	
								    	columnsChanged = false;
								    	
										var columns_block = createColumnsBlock(response.columns);
										$('#step2-postbox').show();
										$('tr.step2_row').show();
										$('tr.step2_row td.columnsBlock').html(columns_block);
										applySortable();
										if($('#wpAddChart').val() != 'none'){
										    $('tr.chart_row').show();
										}else{
										    $('tr.chart_row').hide();
										}
										applySelecter();
										$('#wdtResponsive').change();
										$('#wpTableEditable').change();
										$('.submitStep2').removeAttr('disabled');
										applySelecter();
										wdtAlertDialog(wpdatatables_edit_strings.successful_save,wpdatatables_edit_strings.success_label);
								    }
								}
						    });
							
						}else{
						    $('#wdtPreloadLayer').hide();
							wdtAlertDialog(wpdatatables_edit_strings.successful_save,wpdatatables_edit_strings.success_label);
						}
						
				    }
				},
				error: function(response){
				    var errMsg = response.responseText;
				    if(errMsg.indexOf('Allowed memory size of') != -1){
					errMsg += "<br/>";
					errMsg += wpdatatables_edit_strings.file_too_large;
				    }
				    wdtAlertDialog(errMsg,wpdatatables_edit_strings.error_label);
				}
		    });
		 }
	    
		$('a.submitdelete').click(function(e){
		    e.preventDefault();
		    e.stopImmediatePropagation();
		    if(confirm(wpdatatables_edit_strings.are_you_sure_label)){
			window.location = $(this).attr('href');
		    }
		})
	    
		$('button.closeButton').click(function(e){
		    e.preventDefault();
		    e.stopImmediatePropagation();
		    if(confirm(wpdatatables_edit_strings.are_you_sure_lose_unsaved_label)){
			window.location = 'admin.php?page=wpdatatables-administration';
		    }
		})
		
		/** Init editor roles dialog **/
		$('#wdtUserRoles').dialog({
			autoOpen: false,
			modal: true,
			buttons: {
				'OK': function(){
					var editorRoles = [];
					$('input.wdtRoleCheckbox:checked').each(function(){
						editorRoles.push($(this).val());
					});
					var editorRolesStr = editorRoles.join(',');
					$('#wpTableEditorRoles').html(editorRolesStr);
					$(this).dialog('close');
				},
				'Cancel': function(){
					$(this).dialog('close');
				}
			}
		});
		
		/** Open editor roles dialog **/
		$('#selectEditorRoles').click(function(e){
			e.preventDefault();
			$('#wdtUserRoles').dialog('open');
		});
	    
		$('button.previewButton').click(function(e){
		    e.preventDefault();
		    e.stopImmediatePropagation();
		    var data = { };
		    data.action = 'wdt_get_preview';
		    data.table_id = $('#wpDataTableId').val();
		    if(preview_called){ data.no_scripts = 1; }
		    $('#wdtPreloadLayer').show();
		    $.ajax({
			type: 'post',
			url: ajaxurl,
			data: data,
			dataType: 'html',
			success: function(response){
			    var dialog_div = '<div id="preview_dialog" title="Preview" style="display: none"></div>';
			    $('body').append(dialog_div);
			    $('#preview_dialog').html(response);
			    $('#preview_dialog').find('.dataTables_wrapper').addClass('wpDataTables wpDataTablesWrapper');
			    $('#preview_dialog').find('table.wpDataTable').show();
			    $('#preview_dialog').dialog({
					modal: true,
					width: 950,
					height: 700,
					buttons: {
						    'OK': function(){
							$('#preview_dialog').find('table.wpDataTable').dataTable({bDestroy: true});
							$('#preview_dialog').html('');
							$(this).dialog('close');
							$('#preview_dialog').remove();
							$('.wdtFilterDialog').remove();
					    }
					},
					open: function(){
					    $('#wdtPreloadLayer').hide();
					    preview_called = true;
					},
					close: function(){
						$('#preview_dialog').find('table.wpDataTable').dataTable({bDestroy: true});
					    $('#preview_dialog').html('');
					    $(this).dialog('close');
					    $('#preview_dialog').remove();
					    $('.wdtFilterDialog').remove();
					}
			    });
			}
		    });
		});
	    
		$('button.ungroupButton').click(function(e){
		    e.preventDefault();
		    $('input.groupColumn').removeAttr('checked').parent().find('div.picker').removeClass('checked');
		});
	    
		$('#wpTableType').trigger('change');
	    
		if(columns_data.length > 0) {
			    var columns_block = createColumnsBlock(columns_data);
				$('#step2-postbox').show();
			    $('tr.step2_row').show();
			    $('tr.step2_row td.columnsBlock').html(columns_block);
				$('#wdtResponsive').change();
			    applySortable();
			    $('.previewButton').show();
			    if($('#wpAddChart').val() != 'none'){
				$('tr.chart_row').show();
		    }else{
				$('tr.chart_row').hide();
		    }
		}
		
		applySelecter();
    
    });

})(jQuery);