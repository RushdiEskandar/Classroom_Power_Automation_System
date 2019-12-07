<?php
/**
 * @package wpDataTables
 * @version 1.5.6
 */
/**
 * Controller for admin panel AJAX actions
 */
?>
<?php

	/**
	 * Handler which returns the AJAX preview
	 */
	 function wdt_get_ajax_preview(){
	 	$no_scripts = !empty($_POST['no_scripts']) ? 1 : 0;
	 	if(!$no_scripts){
		 	$scripts = array(
			 	WDT_JS_PATH.'jquery-datatables/jquery.dataTables.min.js',
				WDT_JS_PATH.'datepicker/picker.js',
				WDT_JS_PATH.'datepicker/picker.date.js',
				WDT_JS_PATH.'responsive/lodash.min.js',
				WDT_JS_PATH.'responsive/datatables.responsive.js',
			 	WDT_JS_PATH.'jquery-datatables/TableTools.min.js',
			 	WDT_JS_PATH.'php-datatables/wpdatatables.funcs.min.js',
			 	WDT_JS_PATH.'jquery-datatables/jquery.dataTables.rowGrouping.js',
				WDT_JS_PATH.'jquery-datatables/jquery.dataTables.columnFilter.js',
				WDT_JS_PATH.'fileupload/jquery.iframe-transport.js',
				WDT_JS_PATH.'fileupload/jquery.fileupload.js',
				WDT_JS_PATH.'maskmoney/jquery.maskMoney.js',
				WDT_JS_PATH.'wpdatatables/wpdatatables.min.js'
		 	);
	 	}else{
			$scripts = array(WDT_JS_PATH.'wpdatatables/wpdatatables.js');
	 	}
	 	echo wdt_output_table($_POST['table_id'], $no_scripts);
	 	foreach($scripts as $script){
			echo '<script type="text/javascript" src="'.$script.'"></script>';
	 	}
	 	exit();
	 }	 
	add_action( 'wp_ajax_wdt_get_preview', 'wdt_get_ajax_preview' );
	
	
	
	/**
	 * Function which saves the global settings for the plugin
	 */
	function wdt_save_settings(){
		
		$_POST = apply_filters( 'wpdatatables_before_save_settings', $_POST );
		
		// Get and write main settings
		$wpUseSeparateCon = ($_POST['wpUseSeparateCon'] == 'true');
		$wpMySqlHost = $_POST['wpMySqlHost'];
		$wpMySqlDB = $_POST['wpMySqlDB'];
		$wpMySqlUser = $_POST['wpMySqlUser'];
		$wpMySqlPwd = $_POST['wpMySqlPwd'];
		$wpRenderCharts = $_POST['wpRenderCharts'];
		$wpRenderFilter = $_POST['wpRenderFilter'];
		$wpInterfaceLanguage = $_POST['wpInterfaceLanguage'];
		$wpDateFormat = $_POST['wpDateFormat'];
		$wpTopOffset = $_POST['wpTopOffset'];
		$wpLeftOffset = $_POST['wpLeftOffset'];
		$wdtBaseSkin = $_POST['wdtBaseSkin'];
		$wdtTablesPerPage = $_POST['wdtTablesPerPage'];
		$wdtNumberFormat = $_POST['wdtNumberFormat'];
		$wdtDecimalPlaces = $_POST['wdtDecimalPlaces'];
		$wdtNumbersAlign = $_POST['wdtNumbersAlign'];
		$wdtCustomJs = $_POST['wdtCustomJs'];
		$wdtCustomCss = $_POST['wdtCustomCss'];
		$wdtMobileWidth = $_POST['wdtMobileWidth'];
		$wdtTabletWidth = $_POST['wdtTabletWidth'];
                $wdtPurchaseCode = $_POST['wdtPurchaseCode'];
		
		update_option('wdtUseSeparateCon', $wpUseSeparateCon);
		update_option('wdtMySqlHost', $wpMySqlHost);
		update_option('wdtMySqlDB', $wpMySqlDB);
		update_option('wdtMySqlUser', $wpMySqlUser);
		update_option('wdtMySqlPwd', $wpMySqlPwd);
		update_option('wdtRenderCharts', $wpRenderCharts);
		update_option('wdtRenderFilter', $wpRenderFilter);
		update_option('wdtInterfaceLanguage', $wpInterfaceLanguage);
		update_option('wdtDateFormat', $wpDateFormat);
		update_option('wdtTopOffset', $wpTopOffset);
		update_option('wdtLeftOffset', $wpLeftOffset);
		update_option('wdtBaseSkin', $wdtBaseSkin);
		update_option('wdtTablesPerPage', $wdtTablesPerPage);
		update_option('wdtNumberFormat', $wdtNumberFormat);
		update_option('wdtDecimalPlaces', $wdtDecimalPlaces);
		update_option('wdtNumbersAlign', $wdtNumbersAlign);
		update_option('wdtCustomJs', $wdtCustomJs);
		update_option('wdtCustomCss', $wdtCustomCss);
		update_option('wdtMobileWidth', $wdtMobileWidth);
		update_option('wdtTabletWidth', $wdtTabletWidth);
                update_option('wdtPurchaseCode',$wdtPurchaseCode);
		
		// Get font and color settings
		$wdtFontColorSettings = array();
		$wdtFontColorSettings['wdtHeaderBaseColor'] = $_POST['wdtHeaderBaseColor'];
		$wdtFontColorSettings['wdtHeaderActiveColor'] = $_POST['wdtHeaderActiveColor'];
		$wdtFontColorSettings['wdtHeaderFontColor'] = $_POST['wdtHeaderFontColor'];
		$wdtFontColorSettings['wdtHeaderBorderColor'] = $_POST['wdtHeaderBorderColor'];
		$wdtFontColorSettings['wdtTableOuterBorderColor'] = $_POST['wdtTableOuterBorderColor'];
		$wdtFontColorSettings['wdtTableInnerBorderColor'] = $_POST['wdtTableInnerBorderColor'];
		$wdtFontColorSettings['wdtTableFontColor'] = $_POST['wdtTableFontColor'];
		$wdtFontColorSettings['wdtTableFont'] = $_POST['wdtTableFont'];
		$wdtFontColorSettings['wdtHoverRowColor'] = $_POST['wdtHoverRowColor'];
		$wdtFontColorSettings['wdtOddRowColor'] = $_POST['wdtOddRowColor'];
		$wdtFontColorSettings['wdtEvenRowColor'] = $_POST['wdtEvenRowColor'];
		$wdtFontColorSettings['wdtActiveOddCellColor'] = $_POST['wdtActiveOddCellColor'];
		$wdtFontColorSettings['wdtActiveEvenCellColor'] = $_POST['wdtActiveEvenCellColor'];
		$wdtFontColorSettings['wdtSelectedRowColor'] = $_POST['wdtSelectedRowColor'];
		$wdtFontColorSettings['wdtButtonColor'] = $_POST['wdtButtonColor'];
		$wdtFontColorSettings['wdtButtonBorderColor'] = $_POST['wdtButtonBorderColor'];
		$wdtFontColorSettings['wdtButtonFontColor'] = $_POST['wdtButtonFontColor'];
		$wdtFontColorSettings['wdtButtonBackgroundHoverColor'] = $_POST['wdtButtonBackgroundHoverColor'];
		$wdtFontColorSettings['wdtButtonBorderHoverColor'] = $_POST['wdtButtonBorderHoverColor'];
		$wdtFontColorSettings['wdtButtonFontHoverColor'] = $_POST['wdtButtonFontHoverColor'];
		$wdtFontColorSettings['wdtModalFontColor'] = $_POST['wdtModalFontColor'];
		$wdtFontColorSettings['wdtModalBackgroundColor'] = $_POST['wdtModalBackgroundColor'];
		$wdtFontColorSettings['wdtOverlayColor'] = $_POST['wdtOverlayColor'];
		$wdtFontColorSettings['wdtBorderRadius'] = $_POST['wdtBorderRadius'];
		
		// Serialize settings and save to DB
		update_option('wdtFontColorSettings',serialize($wdtFontColorSettings));
		
		do_action( 'wpdatatables_after_save_settings' );
		
	}
	add_action( 'wp_ajax_wdt_save_settings', 'wdt_save_settings');
	
	/**
	 * Saves the general settings for the table, tries to generate the table 
	 * and default settings for the columns
	 */
	function wdt_save_table(){
		global $wpdb;
		
		$_POST = apply_filters( 'wpdatatables_before_save_table', $_POST );
		
		$table_id = $_POST['table_id'];
		$table_title = $_POST['table_title'];
		$table_type = $_POST['table_type'];
		if(($table_type == 'csv') || ($table_type == 'xls')){
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			    $table_content = str_replace(site_url(), str_replace('\\', '/', ABSPATH), $_POST['table_content']); 
			}else{
				$table_content = str_replace(site_url(), ABSPATH, $_POST['table_content']);
			}
		}else{
			$table_content = $_POST['table_content'];
		}
		$table_hide_before_loaded = ($_POST['hide_before_loaded'] == 'true');
		$table_advanced_filtering = ($_POST['table_advanced_filtering'] == 'true');
		$table_filter_form = ($_POST['table_filter_form'] == 'true');
		$table_tools = ($_POST['table_tools'] == 'true');
		$table_sorting = ($_POST['table_sorting'] == 'true');
		$table_fixed_layout = ($_POST['fixed_layout'] == 'true');
		$table_word_wrap = ($_POST['word_wrap'] == 'true');
		$table_display_length = $_POST['table_display_length'];
		$table_fixheader = ($_POST['table_fixheader'] == 'true');
		$table_fixcolumns = $_POST['table_fixcolumns'];
		$table_chart = $_POST['table_chart'];
		$table_charttitle = $_POST['table_charttitle'];
		$table_serverside = ($_POST['table_serverside'] == 'true');
		$table_editable = $table_type == 'mysql' ? ($_POST['table_editable'] == 'true') : false;
		$table_mysql_name = $table_editable ? $_POST['table_mysql_name'] : '';
		$table_responsive = $_POST['responsive'] == 'true';
		$table_editor_roles = !empty($_POST['editor_roles']) ? $_POST['editor_roles'] : '';
		
		if(!$table_fixheader){
			$table_fixcolumns = -1;
		}else{
			$table_fixcolumns = (int)$table_fixcolumns;
		}
		if(!$table_id){
			// adding new table
			// trying to generate a WPDataTable
			$res = wdt_try_generate_table( $table_type, $table_content );
			if(!empty($res['error'])){
				// if WPDataTables returns an error, replying to the page
				echo json_encode( $res ); die();
			}else{
				// if no problem reported, first saving the table parameters to DB
				$table_array = array(
									'title' => $table_title,
									'table_type' => $table_type,
									'content' => $table_content,
									'filtering' => (int)$table_advanced_filtering,
									'filtering_form' => (int)$table_filter_form,
									'sorting' => (int)$table_sorting,
									'fixed_layout' => (int)$table_fixed_layout,
									'responsive' => (int)$table_responsive,
									'word_wrap' => (int)$table_word_wrap,
									'tools' => (int)$table_tools,
									'display_length' => $table_display_length,
									'fixed_columns' => $table_fixcolumns,
									'chart' => $table_chart,
									'chart_title' => $table_charttitle,
									'server_side' => (int)$table_serverside,
									'editable' => (int)$table_editable,
									'editor_roles' => $table_editor_roles,
									'mysql_table_name' => $table_mysql_name,
									'hide_before_load' => $table_hide_before_loaded
									);
				
				$table_array = apply_filters('wpdatatables_filter_insert_table_array', $table_array);
				
				$wpdb->insert($wpdb->prefix .'wpdatatables', $table_array);
				// get the newly generated table ID
				$table_id = $wpdb->insert_id;
				$res['table_id'] = $table_id;
				// creating default columns for the new table
				$res['columns'] = wdt_create_columns_from_table($res['table'], $table_id);
				do_action( 'wpdatatables_after_save_table', $table_id );
				echo json_encode($res); die();
			}
		}else{
			// editing existing table
			$query = 'SELECT * 
						FROM '.$wpdb->prefix.'wpdatatables
						WHERE id='.$table_id;
			$table_data = $wpdb->get_row($query, ARRAY_A);
			// checking if table type or content has changed
			if(($table_data['content'] == $table_content)
				&& ($table_data['table_type'] == $table_type)){
				// if it didn't change only updating the record
				// and receiving the columnset
				$table_array = array(
									'title' => $table_title,
									'table_type' => $table_type,
									'content' => $table_content,
									'filtering' => (int)$table_advanced_filtering,
									'filtering_form' => (int)$table_filter_form,
									'sorting' => (int)$table_sorting,
									'fixed_layout' => (int)$table_fixed_layout,
									'word_wrap' => (int)$table_word_wrap,
									'responsive' => (int)$table_responsive,
									'tools' => (int)$table_tools,
									'display_length' => $table_display_length,
									'fixed_columns' => $table_fixcolumns,
									'chart' => $table_chart,
									'chart_title' => $table_charttitle,
									'server_side' => (int)$table_serverside,
									'editable' => (int)$table_editable,
									'editor_roles' => $table_editor_roles,
									'mysql_table_name' => $table_mysql_name,
									'hide_before_load' => $table_hide_before_loaded
									);
				
				$table_array = apply_filters( 'wpdatatables_filter_update_table_array', $table_array, $table_id );
				
				$wpdb->update($wpdb->prefix.'wpdatatables',
								$table_array,
								array(
									'id' => $table_id
									)
								);
								
				 $res['table_id'] = $table_id;
				 $res['columns']  = wdt_get_columns_by_table_id( $table_id );
				 do_action( 'wpdatatables_after_save_table' );
				 echo json_encode($res); die();
			}else{
				// if it changed trying to rebuild the table and reloading the columnset
				$res = wdt_try_generate_table( $table_type, $table_content );
				if(!empty($res['error'])){
					// if WPDataTables returns an error, replying to the page
					do_action( 'wpdatatables_after_save_table' );
					echo json_encode( $res ); die();
				}else{
					// otherwise updating the table
					$table_array = array(
									'title' => $table_title,
									'table_type' => $table_type,
									'content' => $table_content,
									'filtering' => (int)$table_advanced_filtering,
									'filtering_form' => (int)$table_filter_form,
									'sorting' => (int)$table_sorting,
									'fixed_layout' => (int)$table_fixed_layout,
									'word_wrap' => (int)$table_word_wrap,
									'responsive' => (int)$table_responsive,
									'tools' => (int)$table_tools,
									'display_length' => $table_display_length,
									'fixed_columns' => $table_fixcolumns,
									'chart' => $table_chart,
									'chart_title' => $table_charttitle,
									'server_side' => (int)$table_serverside,
									'editable' => (int)$table_editable,
									'editor_roles' => $table_editor_roles,
									'mysql_table_name' => $table_mysql_name,
									'hide_before_load' => $table_hide_before_loaded
									);
									
					$table_array = apply_filters( 'wpdatatables_filter_update_table_array', $table_array, $table_id );
					
					$wpdb->update($wpdb->prefix.'wpdatatables',
								$table_array,
								array(
									'id' => $table_id
									)
								);
				 	$res['table_id'] = $table_id;
				 	// rebuilding the columnset
					$res['columns'] = wdt_create_columns_from_table($res['table'], $table_id);
					do_action( 'wpdatatables_after_save_table' );
					echo json_encode($res); die();				 	
				}
				
			}
		}
		
	}
	add_action( 'wp_ajax_wdt_save_table', 'wdt_save_table');
	
	/**
	 * Saves the settings for columns
	 */
	function wdt_save_columns(){
		global $wpdb;
		
		$_POST = apply_filters( 'wpdatatables_before_save_columns', $_POST );
		
		$table_id = $_POST['table_id'];
		$columns = $_POST['columns'];
		foreach($columns as $column){
			$wpdb->update($wpdb->prefix.'wpdatatables_columns',
							array(
								'display_header' => $column['display_header'],
								'css_class' => $column['css_class'],
								'possible_values' => $column['possible_values'],
								'default_value' => $column['default_value'],
								'input_type' => isset($column['input_type']) ? $column['input_type'] : '',
								'filter_type' => $column['filter_type'],
								'column_type' => $column['column_type'],
								'id_column' => (int)($column['id_column'] == 'true'),
								'group_column' => (int)($column['group_column'] == 'true'),
								'sort_column' => (int)($column['sort_column']),
								'hide_on_phones' => (int)($column['hide_on_phones'] == 'true'),
								'hide_on_tablets' => (int)($column['hide_on_tablets'] == 'true'),
								'use_in_chart' => (int)($column['use_in_chart'] == 'true'),
								'chart_horiz_axis' => (int)($column['chart_horiz_axis'] == 'true'),
								'visible' => (int)($column['visible'] == 'true'),
								'width' => $column['width'],
								'pos' => $column['pos']
								),
							array(
								'id' => $column['id']
								)
							);
		}
		$res['columns'] = wdt_get_columns_by_table_id( $table_id );
		
		do_action( 'wpdatatables_after_save_columns' );
		
		echo json_encode($res); exit();
	}
	add_action( 'wp_ajax_wdt_save_columns', 'wdt_save_columns');
	
	/**
	 * Duplicate the table
	 */
	 function wpdatatables_duplicate_table(){
	 	global $wpdb;
	 	
	 	$table_id = $query = wpdatatables_sanitize_query( $_POST['table_id'] );
	 	$new_table_name = wpdatatables_sanitize_query( $_POST['new_table_name'] );
	 	
	 	// Getting the table data
	 	$table_data = wdt_get_table_by_id( $table_id );
	 	
	 	// Creating new table
	 	$wpdb->insert(
	 		$wpdb->prefix.'wpdatatables',
	 		array(
	 			'title' => $new_table_name,
	 			'table_type' => $table_data['table_type'],
	 			'content' => $table_data['content'],
	 			'filtering' => $table_data['filtering'],
	 			'sorting' => $table_data['sorting'],
	 			'tools' => $table_data['tools'],
	 			'display_length' => $table_data['display_length'],
	 			'fixed_columns' => $table_data['fixed_columns'],
	 			'chart' => $table_data['chart'],
	 			'chart_title' => $table_data['chart_title'],
	 			'server_side' => $table_data['server_side'],
	 			'fixed_layout' => $table_data['fixed_layout'],
	 			'word_wrap' => $table_data['word_wrap'],
	 			'editable' => $table_data['editable'],
	 			'mysql_table_name' => $table_data['mysql_table_name'],
	 			'responsive' => $table_data['responsive'],
	 			'filtering_form' => $table_data['filtering_form'],
	 			'editor_roles' => $table_data['editor_roles']
	 		)
	 	);
	 	
	 	$new_table_id = $wpdb->insert_id;
	 	
	 	// Getting the column data
	 	$columns = wdt_get_columns_by_table_id( $table_id );
	 	
	 	// Creating new columns
	 	foreach($columns as $column){
	 		$wpdb->insert(
	 			$wpdb->prefix.'wpdatatables_columns',
	 			array(
	 				'table_id' => $new_table_id,
	 				'orig_header' => $column->orig_header,
					'css_class' => $column->css_class,
	 				'display_header' => $column->display_header,
	 				'filter_type' => $column->filter_type,
	 				'column_type' => $column->column_type,
	 				'group_column' => $column->group_column,
	 				'use_in_chart' => $column->use_in_chart,
	 				'chart_horiz_axis' => $column->chart_horiz_axis,
	 				'visible' => $column->visible,
	 				'width' => $column->width,
	 				'pos' => $column->pos,
	 				'input_type' => $column->input_type,
	 				'id_column' => $column->id_column,
	 				'sort_column' => $column->sort_column,
	 				'possible_values' => $column->possible_values,
	 				'hide_on_phones' => $column->hide_on_phones,
	 				'hide_on_tablets' => $column->hide_on_tablets,
	 				'default_value' => $column->default_value
	 			)
	 		);
	 	}
	 	
	 	exit();
	 	
	 }
	add_action( 'wp_ajax_wpdatatables_duplicate_table', 'wpdatatables_duplicate_table');

?>