/** New JS controller for wpDataTables **/

var wpDataTables = {};
var wpDataTableDialogs = {};
var wpDataTablesSelRows = {};
var wpDataTablesFunctions = {};
var wpDataTablesUpdatingFlags = {};
var wpDataTablesResponsiveHelpers = {};
var wdtBreakpointDefinition = {
    tablet: 1024,
    phone : 480
};

(function($) {
	$(function() {
		
		$('table.wpDataTable').each(function(){
			var tableDescription = $(this).data('description');
			
			// Parse the DataTable init options
			var dataTableOptions = tableDescription.dataTableParams;
	
			// Responsive-mode related stuff
			if(tableDescription.responsive){
				wpDataTablesResponsiveHelpers[tableDescription.tableId] = false;
				dataTableOptions.fnPreDrawCallback = function(){
					if (!wpDataTablesResponsiveHelpers[tableDescription.tableId]) {
						if( typeof tableDescription.mobileWidth !== 'undefined' ){
							wdtBreakpointDefinition.phone = parseInt(tableDescription.mobileWidth);
						}
						if( typeof tableDescription.tabletWidth !== 'undefined' ){
							wdtBreakpointDefinition.tablet = parseInt(tableDescription.tabletWidth);
						}
			            wpDataTablesResponsiveHelpers[tableDescription.tableId] = new ResponsiveDatatablesHelper($(tableDescription.selector).dataTable(), wdtBreakpointDefinition);
			        }
			        wdtAddOverlay('#'+tableDescription.tableId);
				}
				dataTableOptions.fnRowCallback = function(nRow, aData, iDisplayIndex, iDisplayIndexFull){
					wpDataTablesResponsiveHelpers[tableDescription.tableId].createExpandIcon(nRow);
				}
				if(!tableDescription.editable){
					dataTableOptions.fnDrawCallback = function(){
						wpDataTablesResponsiveHelpers[tableDescription.tableId].respond();
				        wdtRemoveOverlay('#'+tableDescription.tableId);
					}
				}
			}else{
				dataTableOptions.fnPreDrawCallback = function(){
			        wdtAddOverlay('#'+tableDescription.tableId);
				}
			}

			if(tableDescription.editable){

				if(typeof wpDataTablesFunctions[tableDescription.tableId] === 'undefined'){
					wpDataTablesFunctions[tableDescription.tableId] = {};
				}
				
				wpDataTablesSelRows[tableDescription.tableId] = -1;
				dataTableOptions.fnDrawCallback = function(){
			        wdtRemoveOverlay('#'+tableDescription.tableId);
					if(tableDescription.responsive){
						wpDataTablesResponsiveHelpers[tableDescription.tableId].respond();
					}
					
					if(wpDataTablesSelRows[tableDescription.tableId] == -2){
						var sel_row_index = wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength-1;
						$(tableDescription.selector+' > tbody > tr').removeClass('selected');
						wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector+' > tbody > tr:eq('+sel_row_index+')').get(0));
					}else if (wpDataTablesSelRows[tableDescription.tableId] == -3){
						var sel_row_index = 0;
						$(tableDescription.selector+' > tbody > tr').removeClass('selected');
						wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector+' > tbody > tr:eq('+sel_row_index+')').get(0));
					}
					if(!$(tableDescription.selector+'_edit_dialog').hasClass('remodal')) { return; } 

					if($(tableDescription.selector+'_edit_dialog').is(':visible')){
						var data = wpDataTables[tableDescription.tableId].fnGetData(wpDataTablesSelRows[tableDescription.tableId]);
						wpDataTablesFunctions[tableDescription.tableId].applyData(data);
					}	
					
					if(wpDataTablesUpdatingFlags[tableDescription.tableId]){
						$(tableDescription.selector+' > tbody > tr:eq('+wpDataTablesSelRows[tableDescription.tableId]+')').click();
					}
					wpDataTablesUpdatingFlags[tableDescription.tableId] = false;
				}
				
				// Data apply function for editable tables
				wpDataTablesFunctions[tableDescription.tableId].applyData = function(data){
				 	$(data).each(function(index, el){
				 		if(el) {
					 		var val = el.toString();
				 		}else{
				 			var val = '';
				 		}
						if(val.indexOf('span') != -1){ val = val.replace(/<span>/g, '').replace(/<\/span>/g,''); }
						if(val.indexOf('<br/>') != -1) {  val = val.replace(/<br\/>/g,"\n");}
						if($('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+index+')').data('input_type')=='multi-selectbox'){
							$('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+index+') option').removeAttr('selected');
							var values = val.split(', ');
							$('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+index+')').val(values);
							$('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+index+')').selecter('refresh');
						}else {
							if($('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+index+')').data('input_type')=='attachment'){
								if(val!=''){
									val = $(val).attr('href');
								}
							}else{
								if(val.indexOf('<a href') != -1){ val = $(val).html(); }
							}
							if($('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+index+')').data('column_type')=='int'){
								val = val.replace(/\,/g, '').replace(/\./g,'');
							}
							$('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+index+')').val(val).css('border','');
							if($('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+index+')').data('input_type')=='selectbox'){
								$('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+index+')').val(val);
								$('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+index+')').selecter('destroy').selecter();
							}
						}
				 	});
					if($('.fileupload_'+tableDescription.tableId+'').length){
						var $fileupload_el = $('.fileupload_'+tableDescription.tableId+'');
				 		$fileupload_el.closest('.fileinput-button').show();
				 		$fileupload_el.closest('td').find('.progress').show();
			 	   		var id_key = $('#'+tableDescription.tableId+'_edit_dialog tr.idRow .editDialogInput').data('key');
			 	   		var id_val = $('#'+tableDescription.tableId+'_edit_dialog tr.idRow .editDialogInput').val();
						$('.progress-bar').css('width', '0%');
			 	   		$('.fileupload_'+tableDescription.tableId+'').each(function(){
					 	   	$(this).fileupload('option', 'url', tableDescription.fileUploadBaseUrl+'&key='+$(this).data('key')+'&id_key='+id_key+'&id_val='+id_val);
					 	   	var display_val = $('#'+tableDescription.tableId+'_'+$(this).data('key')).val();
					 	   	if(display_val.indexOf('/') !== -1){
								display_val = display_val.substring(display_val.lastIndexOf('/')+1);
					 	   	}
		 		            $('#files_'+tableDescription.tableId+'_'+$(this).data('key')).html('<p>'+display_val+' [<a href="#" data-key="'+$(this).data('key')+'" class="wdtdeleteFile">remove</a>]</p>');
				 	   		if($('#'+tableDescription.tableId+'_'+$(this).data('key')).val()==''){
				 	   			$('#files_'+tableDescription.tableId+'_'+$(this).data('key')).hide();
				 	   		}else{
				 	   			$('#files_'+tableDescription.tableId+'_'+$(this).data('key')).show();
				 	   		}
			 	   		});
					}
				}
				
				// Saving of the table data for frontend 
				wpDataTablesFunctions[tableDescription.tableId].saveTableData = function(forceRedraw){
						if(typeof(forceRedraw) === undefined){
							forceRedraw = false;
						}
						wpDataTablesUpdatingFlags[tableDescription.tableId] = true;
						var formdata = {table_id: tableDescription.tableWpId};
						var valid = true;	
						
						$(tableDescription.selector+'_edit_dialog .editDialogInput').each(function(){
							// validation
							if($(this).data('input_type') == 'email'){
								if($(this).val()!=''){
									var field_valid = wdtValidateEmail($(this).val());
									if(!field_valid){
										valid = false;
										$(this).css('border','2px solid #f00');
									}else{
										$(this).css('border','');
									}
								}
							}else if($(this).data('input_type') == 'link'){
								if($(this).val()!=''){
									var field_valid = wdtValidateURL($(this).val());
									if(!field_valid){
										valid = false;
										$(this).css('border','2px solid #f00');
									}else{
										$(this).css('border','');
									}
								}
							}
							if($(this).hasClass('datepicker')){
								formdata[$(this).data('key')] = $.datepicker.formatDate(tableDescription.datepickFormat, $.datepicker.parseDate(tableDescription.datepickFormat,$(this).val()));
							}else if($(this).data('input_type')=='multi-selectbox'){
								if($(this).val()){
									formdata[$(this).data('key')] = $(this).val().join(', ');
								}
							}else{
								formdata[$(this).data('key')] = $(this).val();
							}
						});
						if(!valid){ return false; }
						wpDataTablesUpdatingFlags[tableDescription.tableId] = true;		
						$.ajax({
							url: tableDescription.adminAjaxBaseUrl,
							type: 'POST',
							data: {
								action: 'wdt_save_table_frontend',
								formdata: formdata
							},
							success: function(insert_id){
								if(insert_id){
									$(tableDescription.selector+'_edit_dialog tr.idRow .editDialogInput').val(insert_id);
								 	if($('.fileupload_'+tableDescription.tableId).length){
								 		$('.fileupload_'+tableDescription.tableId).closest('.fileinput-button').show();
								 		$('.fileupload_'+tableDescription.tableId).closest('td').find('.progress').show();
								 	}
								 	if(forceRedraw){
										wpDataTables[tableDescription.tableId].fnDraw(false);
										$('#edit_'+tableDescription.tableId).attr('disabled','disabled');
								 	}
								}else{
									wpDataTables[tableDescription.tableId].fnDraw(false);
									$('#edit_'+tableDescription.tableId).attr('disabled','disabled');
								}
							}
						});
						return true;
					}
				
			}
			
			// Remove overlay if the table is not responsive nor editable
			if(!tableDescription.responsive 
				&& !tableDescription.editable){
				dataTableOptions.fnDrawCallback = function(){
			        wdtRemoveOverlay('#'+tableDescription.tableId);
				}
			}
						
			// Apply the selecter to show entries
			dataTableOptions.fnInitComplete = function(){
				jQuery('#'+tableDescription.tableId+'_length select').selecter();
			}
			
			// Init the DataTable itself
			wpDataTables[tableDescription.tableId] = $(tableDescription.selector).dataTable(dataTableOptions);
			
			// Init table grouping if enabled
			if((tableDescription.columnsFixed == 0) && (tableDescription.groupingEnabled)){
				wpDataTables[tableDescription.tableId].rowGrouping({ iGroupingColumnIndex: tableDescription.groupingColumnIndex });
			}
			
			// Init the advanced filtering if enabled
			if(tableDescription.advancedFilterEnabled){
				wpDataTables[tableDescription.tableId].columnFilter(tableDescription.advancedFilterOptions);
				$.datepicker.regional[""].dateFormat = tableDescription.datepickFormat;
			    $.datepicker.setDefaults($.datepicker.regional['']);
			}
			
			if(tableDescription.editable){
					/**
					 * Init edit dialog on page load
					 */
					 wpDataTableDialogs[tableDescription.tableId] = wdtDialog('','Edit');
					 wpDataTableDialogs[tableDescription.tableId].addClass('wdtEditDialog');
					 $(tableDescription.selector+'_edit_dialog').appendTo(wpDataTableDialogs[tableDescription.tableId]).show();
					 $(tableDescription.selector+'_edit_dialog select').selecter();
					 
					 /**
					  * Close button in edit dialog
					  */
					  $(tableDescription.selector+'_close_edit_dialog').click(function(e){
							e.preventDefault();
							$.remodal.lookup[wpDataTableDialogs[tableDescription.tableId].data('remodal')].close();
					  });
					  
					  /**
					   * Prev button in edit dialog
					   */
					  $(tableDescription.selector+'_prev_edit_dialog').click(function(e){
							e.preventDefault();
							var sel_row_index = $(tableDescription.selector+' > tbody > tr.selected').index();
							if(sel_row_index > 0) {
								$(tableDescription.selector+' > tbody > tr.selected').removeClass('selected');
								$(tableDescription.selector+' > tbody > tr:eq('+(sel_row_index-1)+')').addClass('selected', 300);
								wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector+' > tbody > tr.selected').get(0));
								var data = wpDataTables[tableDescription.tableId].fnGetData(wpDataTablesSelRows[tableDescription.tableId]);
								wpDataTablesFunctions[tableDescription.tableId].applyData(data);
							}else{
								var cur_page = Math.ceil( wpDataTables[tableDescription.tableId].fnSettings()._iDisplayStart /  wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength) + 1;
								if(cur_page == 1) return;
								wpDataTablesSelRows[tableDescription.tableId] = -2;
								wpDataTablesUpdatingFlags[tableDescription.tableId] = true;
								wpDataTables[tableDescription.tableId].fnPageChange( 'previous' );
							}
					  });
					  
					  /**
					   * Next button in edit dialog
					   */
					  $(tableDescription.selector+'_next_edit_dialog').click(function(e){
					  		e.preventDefault();
				    		var sel_row_index = $(tableDescription.selector+' > tbody > tr.selected').index();
							if(sel_row_index < wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength-1) {
								$(tableDescription.selector+' > tbody > tr.selected').removeClass('selected');
								$(tableDescription.selector+' > tbody > tr:eq('+(sel_row_index+1)+')').addClass('selected', 300);
								wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector+' > tbody > tr.selected').get(0));
								var data = wpDataTables[tableDescription.tableId].fnGetData(wpDataTablesSelRows[tableDescription.tableId]);
								wpDataTablesFunctions[tableDescription.tableId].applyData(data);
							}else{
								var cur_page = Math.ceil( wpDataTables[tableDescription.tableId].fnSettings()._iDisplayStart / wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength) + 1;
								var total_pages = Math.ceil( wpDataTables[tableDescription.tableId].fnSettings()._iRecordsTotal /  wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength);
								if(cur_page == total_pages) return;
								wpDataTablesSelRows[tableDescription.tableId] = -3;
								wpDataTablesUpdatingFlags[tableDescription.tableId] = true;
								wpDataTables[tableDescription.tableId].fnPageChange( 'next' );
								wpDataTables[tableDescription.tableId].fnDraw(false);
							}
					  });
					  
					  /**
					   * Apply button in edit dialog
					   */
					  $(tableDescription.selector+'_apply_edit_dialog').click(function(e){
					  		e.preventDefault();
				    		wpDataTablesFunctions[tableDescription.tableId].saveTableData();
					  });					  

					  /**
					   * OK button in edit dialog
					   */
					  $(tableDescription.selector+'_ok_edit_dialog').click(function(e){
					  		e.preventDefault();
				    		if(wpDataTablesFunctions[tableDescription.tableId].saveTableData(true)){
					    		$.remodal.lookup[wpDataTableDialogs[tableDescription.tableId].data('remodal')].close();
				    		}
					  });					  
				 	 
				 	 /**
				 	  * Apply maskmoney
				 	  */
				 	  if(tableDescription.number_format == 1){
					 	  $(tableDescription.selector+'_edit_dialog input.maskMoney').maskMoney({
					 	  		thousands: '.',
					 	  		decimal: ',',
					 	  		precision: parseInt(tableDescription.decimal_places)
					 	  	});
				 	  }else{
					 	  $(tableDescription.selector+'_edit_dialog input.maskMoney').maskMoney({
					 	  		thousands: ',',
					 	  		decimal: '.',
					 	  		precision: parseInt(tableDescription.decimal_places)
					 	  	});
				 	  }
			
				 	 /**
				 	  * Apply pickadate
				 	  */
				 	  var dateFormat = tableDescription.datepickFormat.replace(/y/g,'yy').replace(/M/g,'mmm');
				 	  $(tableDescription.selector+'_edit_dialog input.datepicker').pickadate({
					 	  	format: dateFormat, 
					 	  	formatSubmit: dateFormat, 
					 	  	editable: true,
					 	  	selectYears: true,
					 	  	selectMonths: true
			 	  		});
				 	  
				 	  /**
				 	   * Apply fileuploaders
				 	   */
				 	   if($('.fileupload_'+tableDescription.tableId).length){
					 	   $('.fileupload_'+tableDescription.tableId).parent().button();
					 	   $('.fileupload_'+tableDescription.tableId).each(function(){
					 	   		var key = $(this).data('key');
						 	   	$(this).fileupload({
							        dataType: 'json',
							        done: function (e, data) {
								            $.each(data.result.files, function (index, file) {
								                $('#files_'+tableDescription.tableId+'_'+key).html('<p>'+file.name+' [<a href="#" data-key="'+key+'" class="wdtdeleteFile">remove</a>]</p>');
								                $(tableDescription.selector+'_'+key).val(file.url);
									 	   		if($(tableDescription.selector+'_'+key).val()==''){
									 	   			$(tableDescription.selector+'_'+key).hide();
									 	   		}else{
									 	   			$(tableDescription.selector+'_'+key).show();
									 	   		}
								            });
								        },
								        progressall: function (e, data) {
								            var progress = parseInt(data.loaded / data.total * 100, 10);
								            $('#progress_'+tableDescription.tableId+'_'+key+' .progress-bar').css(
								                'width',
								                progress + '%'
								            );
								        }
						 	   	});
					 	   });
				 	   }
			
					
					/**
					 * Show edit dialog
					 */
					 $('#edit_'+tableDescription.tableId).click(function(){
					 	var row = $(tableDescription.selector+' tr.selected').get(0);
					 	var data = wpDataTables[tableDescription.tableId].fnGetData(row);
						wpDataTablesFunctions[tableDescription.tableId].applyData(data);
						$.remodal.lookup[wpDataTableDialogs[tableDescription.tableId].data('remodal')].open();
					 });
					 
					/**
					 * Add new entry dialog
					 */
					 $('#new_'+tableDescription.tableId).click(function(){
					 	$(tableDescription.selector+'_edit_dialog .editDialogInput').val('').css('border','');
					 	$(tableDescription.selector+'_edit_dialog tr.idRow .editDialogInput').val('0');
                                                
                                                $('#'+tableDescription.tableId+'_edit_dialog .editDialogInput').each(function(index){

                                                    if($(this).is('select')){
                                                            $(this).find('option:first').attr('selected','selected');
                                                            $(this).selecter('refresh');
                                                    }
                                                });
					 	
					 	// Set the default values
					 	if(tableDescription.advancedFilterEnabled){
						 	for(var i in tableDescription.advancedFilterOptions.aoColumns){
						 		var defaultValue = tableDescription.advancedFilterOptions.aoColumns[i].defaultValue;
						 		if(defaultValue != ''){
						 			$('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+i+')').val(defaultValue).change();
						 			if($('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+i+')').is('select')){
						 				$('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+i+')').val(defaultValue);
						 				$('#'+tableDescription.tableId+'_edit_dialog .editDialogInput:eq('+i+')').selecter('refresh');
						 			}
						 		}
						 	}
					 	}
					 	
						$.remodal.lookup[wpDataTableDialogs[tableDescription.tableId].data('remodal')].open();
					 	if($('.fileupload_'+tableDescription.tableId).length){
					 		var $fileupload_el = $('.fileupload_'+tableDescription.tableId);
				 	   		var id_key = $('#'+tableDescription.tableId+'_edit_dialog tr.idRow .editDialogInput').data('key');
				 	   		var id_val = $('#'+tableDescription.tableId+'_edit_dialog tr.idRow .editDialogInput').val();
							$('.progress-bar').css('width', '0%');
				 	   		$('.fileupload_'+tableDescription.tableId+'').each(function(){
						 	   	$(this).fileupload('option', 'url', tableDescription.fileUploadBaseUrl+'&key='+$(this).data('key')+'&id_key='+id_key+'&id_val='+id_val);
			 		            $('#files_'+tableDescription.tableId+'_'+$(this).data('key')).html('<p>'+$('#'+tableDescription.tableId+'_'+$(this).data('key')).val()+' [<a href="#" data-key="'+$(this).data('key')+'" class="wdtdeleteFile">remove</a>]</p>');
				 	   		});
					 		$('#files_'+tableDescription.tableId+'_'+$fileupload_el.data('key')).html('');
					 	}
					 	
					 });
					 
					 /**
					  * Delete an entry dialog
					  */
					  $('#delete_'+tableDescription.tableId).click(function(){
					  	var confirm_dialog_str = '<div id="delete_dialog_'+tableDescription.tableId+'">Delete this entry?</div>';
					  	$deleteDialog = wdtDialog(confirm_dialog_str, 'Are you sure?');
					  	$deleteDialog.append('<button class="btn deleteBtn">Delete</button>');
					  	$deleteDialog.append('<button class="btn cancelBtn">Cancel</button>');
					  	$deleteDialog.find('.deleteBtn').click(function(e){
					  		e.preventDefault();
						 	var row = $(tableDescription.selector+' tr.selected').get(0);
						 	var data = wpDataTables[tableDescription.tableId].fnGetData(row);
						 	var id_val = data[tableDescription.idColumnIndex];
						 	var that = this;
						 	$.ajax({
								url: tableDescription.adminAjaxBaseUrl,
								type: 'POST',
								data: {
									action: 'wdt_delete_table_row',
									id_key: tableDescription.idColumnKey,
									id_val: id_val,
									table_id: tableDescription.tableWpId	
								},
								success: function(){
									wpDataTables[tableDescription.tableId].fnDraw(false);
					  				$.remodal.lookup[$deleteDialog.data('remodal')].close();
					  				$deleteDialog.remove();
								}					 		
					 		});
					  	});
					  	$deleteDialog.find('.cancelBtn').click(function(e){
			  				$.remodal.lookup[$deleteDialog.data('remodal')].close();
			  				$deleteDialog.remove();
					  	});
					  	$.remodal.lookup[$deleteDialog.data('remodal')].open();
					  });
					  
					if($(tableDescription.selector+'_wrapper .DTTT_container').length == 0){
						$('<div class="DTTT_container"></div>').prependTo(tableDescription.selector+'_wrapper');
					}
					$('#edit_'+tableDescription.tableId).prependTo(tableDescription.selector+'_wrapper .DTTT_container').css('float','right');	
					$('#new_'+tableDescription.tableId).prependTo(tableDescription.selector+'_wrapper .DTTT_container').css('float','right');	
					$('#delete_'+tableDescription.tableId).prependTo(tableDescription.selector+'_wrapper .DTTT_container').css('float','right');
					 
					var clickEvent = function(e){
						if($(this).hasClass('selected')) {
							$(tableDescription.selector+' tbody tr').removeClass('selected');
							wpDataTablesSelRows[tableDescription.tableId] = -1;
						}else{
							$(tableDescription.selector+'  tbody tr').removeClass('selected');
							$(this).addClass('selected');
							wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector+' tbody tr.selected').get(0));
						}
						if($(tableDescription.selector+' tbody tr.selected').length > 0){
							$('#edit_'+tableDescription.tableId).removeAttr('disabled');
							$('#delete_'+tableDescription.tableId).removeAttr('disabled');
						}else{
							$('#edit_'+tableDescription.tableId).attr('disabled','disabled');
							$('#delete_'+tableDescription.tableId).attr('disabled','disabled');
						}
					}
					
					var ua = navigator.userAgent,
				    event = (ua.match(/iPad/i)) ? "touchstart" : "click";
					
					$(document).on(event, tableDescription.selector+' tbody tr', clickEvent);
					
					/**
					 * Delete uploaded attachment
					 */
					$(document).on('click', tableDescription.selector+'_edit_dialog a.wdtdeleteFile', function(e){
						e.preventDefault();
						e.stopImmediatePropagation();
						  	var confirm_dialog_str = '<div>Delete this file?</div>';
						  	var key = $(this).data('key');
						  	var hiddeninput = $(tableDescription.selector+'_'+key);
						  	var filesblock = $(this).closest('.files');
						  	$deleteConfirmDialog = wdtDialog(confirm_dialog_str, 'Are you sure?');
						  	$deleteConfirmDialog.append('<button class="btn deleteBtn">Delete</button>');
						  	$deleteConfirmDialog.append('<button class="btn cancelBtn">Cancel</button>');
						  	$deleteConfirmDialog.find('.deleteBtn').click(function(e){
						  		e.preventDefault();
							 	var id_val =  $(tableDescription.selector+'_edit_dialog tr.idRow .editDialogInput').val();
							 	var that = this;
							 	$.ajax({
									url: tableDescription.adminAjaxBaseUrl,
									type: 'POST',
									data: {
										action: 'wdt_delete_uploaded_file',
										id_key: tableDescription.idColumnKey,
										id_val: id_val,
										table_id: tableDescription.tableWpId,
										key: key
									},
									success: function(){
										filesblock.html('');
										hiddeninput.val('');
						  				$.remodal.lookup[$deleteConfirmDialog.data('remodal')].close();
						  				$deleteConfirmDialog.remove();
						  				$.remodal.lookup[wpDataTableDialogs[tableDescription.tableId].data('remodal')].open()
									}					 		
						 		});
						  	});
						  	$deleteConfirmDialog.find('.cancelBtn').click(function(e){
				  				$.remodal.lookup[$deleteConfirmDialog.data('remodal')].close();
				  				$deleteConfirmDialog.remove();
				  				$.remodal.lookup[wpDataTableDialogs[tableDescription.tableId].data('remodal')].open()
						  	});
						  	$.remodal.lookup[$deleteConfirmDialog.data('remodal')].open();
					});
			 
			}
			
			// Show the filter box if enabled in the widget if it is present
			if(tableDescription.externalFilter == true){
				if($('#wdtFilterWidget').length){
					$('.wpDataTablesFilter').appendTo('#wdtFilterWidget');
				}
			}
			
			$(window).load(function(){
				// Show table if it was hidden
				if(tableDescription.hideBeforeLoad){
					$(tableDescription.selector).show(300);
				}
			});
			 
		});
		/**
		 * Charts
		 */
		if(typeof wpDataTablesCharts !== 'undefined'){
			google.load("visualization", "1", {packages:["corechart"], callback: function(){
				for(var chartId in wpDataTablesCharts){
					switch(wpDataTablesCharts[chartId].type){
						case 'Line':
							var chart = new google.visualization.LineChart(document.getElementById(wpDataTablesCharts[chartId].container));
							break;
						case 'Area':
							var chart = new google.visualization.AreaChart(document.getElementById(wpDataTablesCharts[chartId].container));
							break;
						case 'Bar':
							var chart = new google.visualization.BarChart(document.getElementById(wpDataTablesCharts[chartId].container));
							break;
						case 'Column':
							var chart = new google.visualization.ColumnChart(document.getElementById(wpDataTablesCharts[chartId].container));
							break;
						case 'Pie':
							var chart = new google.visualization.PieChart(document.getElementById(wpDataTablesCharts[chartId].container));
							break;
					}
				}
				chart.draw(google.visualization.arrayToDataTable(wpDataTablesCharts[chartId].values), wpDataTablesCharts[chartId].options);
			}
			});
		}
	})
	/**
	 * Clear filters button
	 */
	$('button.wdtClearFilters').click(function(e){
		e.preventDefault();
		$('.wpDataTableFilterSection input:text').val('');
		$('.wpDataTableFilterSection select').val('').selecter('update');
		$('.wpDataTableFilterSection input:checkbox').removeAttr('checked').picker('update');
		for(var i in wpDataTables){
			wpDataTables[i].fnFilterClear();
		}
	});

})(jQuery);


function wdtDialog(str, title){
			var dialogId = Math.floor((Math.random()*1000)+1);
        	var dialog_str = '<div class="remodal wpDataTables wdtRemodal" id="remodal-'+dialogId+'"><h1>'+title+'</h1>';
        	dialog_str += str;
        	dialog_str += '</div>';
        	jQuery(dialog_str).remodal({
        		type: 'inline',
        		preloader: false
        	});
            return jQuery('#remodal-'+dialogId);
}

function wdtAddOverlay(table_selector){
	jQuery(table_selector).addClass('overlayed');
}

function wdtRemoveOverlay(table_selector){
	jQuery(table_selector).removeClass('overlayed');
}

jQuery.fn.dataTableExt.oStdClasses.sWrapper = "wpDataTables wpDataTablesWrapper";