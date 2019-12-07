<?php
/**
 * @package wpDataTables
 * @version 1.5.6
 */
/**
 * The admin page
 */
?>
<?php

        // Labels for create/edit JS
        global $wdt_admin_translation_array;
        $wdt_admin_translation_array = array(
        		'table_type_not_empty' => __('Table type cannot be empty','wpdatatables'),
        		'error_label' => __('Error!','wpdatatables'),
        		'table_input_source_not_empty' => __('Table input data source cannot be empty','wpdatatables'),
        		'mysql_table_name_not_set' => __('MySQL table name for front-end editing is not set','wpdatatables'),
        		'empty_result_error' => __('This usually happens when the MySQL query returns an empty result. Please check the results of the query in some DB manager (e.g. PHPMyAdmin)','wpdatatables'),
        		'backend_error_report' => __('wpDataTables backend error: ','wpdatatables'),
        		'successful_save' => __('Table saved successfully!','wpdatatables'),
        		'success_label' => __('Success!','wpdatatables'),
        		'file_too_large' => __('This error is usually occuring because you are trying to load file which is too large. Please try another datasource, use a smaller file.','wpdatatables'),
        		'id_column_not_set' => __('ID column for front-end editing feature is not set','wpdatatables'),
        		'are_you_sure_label' => __('Are you sure?','wpdatatables'),
        		'are_you_sure_lose_unsaved_label' => __('Are you sure? You will lose unsaved changes!','wpdatatables'),
        		'choose_file' => __('Use in wpDataTable','wpdatatables'),
        		'select_excel_csv' => __('Select Excel or CSV file','wpdatatables'),
        		'mysql_server_side_query_too_complicated' => __('Complicated queries (with WHERE clause, conditions, or with JOINs) are not supported together with server-side processing. Please store the query in a MySQL view and then create a wpDataTable based on the view.','wpdatatables')
	        );

	// add the page to WP Admin
	add_action( 'admin_menu', 'wpdatatables_admin_menu' );
	// add the thickbox CSS and JS
	add_action('admin_print_scripts', 'wdt_admin_scripts');
	add_action('admin_print_styles', 'wdt_admin_styles');
	
	/**
	 * Generates the admin menu in admin panel sidebar
	 */
	function wpdatatables_admin_menu() {
		add_menu_page( 'wpDataTables', 'wpDataTables', 'manage_options', 'wpdatatables-administration', 'wpdatatables_browse', 'none');
		add_submenu_page( 'wpdatatables-administration', 'Add a new wpDataTable', 'Add new', 'manage_options', 'wpdatatables-addnew', 'wpdatatables_addnew');
		add_submenu_page( 'wpdatatables-administration', 'wpDataTables settings', 'Settings', 'manage_options', 'wpdatatables-settings', 'wpdatatables_settings');
	}
	
	/**
	 * Adds JS to the admin panel
	 */
	function wdt_admin_scripts() {
		wp_enqueue_media();
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('postbox');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css'); 
		wp_register_script(
			'wdt-colorpicker',
			WDT_ASSETS_PATH.'js/colorpicker/jquery.modcoder.excolor.js'
		);		
		
        do_action('wpdatatables_admin_scripts');
	}	
	
	/**
	 * Adds CSS styles in the admin
	 */
	function wdt_admin_styles() {
		wp_enqueue_style('thickbox');
        wp_register_style('wpdatatables-min',WDT_CSS_PATH.'wpdatatables.min.css');
        wp_enqueue_style('wpdatatables-min');
        wp_register_style('pickadate-default',WDT_ASSETS_PATH.'css/datepicker.default.css');
        wp_enqueue_style('pickadate-default');
        wp_register_style('pickadate-default-date',WDT_ASSETS_PATH.'css/datepicker.default.date.css');
        wp_enqueue_style('pickadate-default-date');
        wp_register_style('jquery-fileupload',WDT_CSS_PATH.'jquery.fileupload.css');
        wp_enqueue_style('jquery-fileupload');
        wp_register_style('tabletools',WDT_CSS_PATH.'TableTools.css');
        wp_enqueue_style('tabletools');
        wp_register_style('datatables-responsive',WDT_ASSETS_PATH.'css/datatables.responsive.css');
        wp_enqueue_style('datatables-responsive');
        // Get skin setting
        $skin = get_option('wdtBaseSkin');
        if(empty($skin)){ $skin = 'skin1'; }
        $renderSkin = $skin == 'skin1' ? WDT_ASSETS_PATH.'css/wpDataTablesSkin.css' : WDT_ASSETS_PATH.'css/wpDataTablesSkin_1.css';
        wp_register_style('wpdatatables-skin',$renderSkin);
        wp_enqueue_style('wpdatatables-skin');
        wp_register_style('wpdatatables-admin',WDT_ASSETS_PATH.'css/wpdatatables_admin.css');
        wp_enqueue_style('wpdatatables-admin');
        wp_enqueue_style('dashicons');
        wp_enqueue_style('wp-color-picker');
        do_action('wpdatatables_admin_styles');
	}	

	/**
	 * Get all tables for the browser
	 */
	 function wdt_get_all_tables(){
	 		global $wpdb;
	 		$query = "SELECT id, title, table_type
	 					FROM {$wpdb->prefix}wpdatatables ";
	 					
	 		if(isset($_REQUEST['s'])){
	 			$query .= " WHERE title LIKE '%".sanitize_text_field($_POST['s'])."%' ";
	 		}
	 					
	 		if(isset($_REQUEST['orderby'])){
	 			if(in_array($_REQUEST['orderby'], array('id','title','table_type'))){
	 				$query .= " ORDER BY ".$_GET['orderby'];
	 				if($_REQUEST['order'] == 'desc'){
	 					$query .= " DESC";
	 				}else{
	 					$query .= " ASC";
	 				}
	 			}
	 		}else{
	 			$query .= " ORDER BY id ASC ";
	 		}
	 		
	 		if(isset($_REQUEST['paged'])){
	 			$paged = (int) $_REQUEST['paged'];
	 		}else{
	 			$paged = 1;
	 		}
	 		
	 		$tables_per_page = get_option('wdtTablesPerPage') ? get_option('wdtTablesPerPage') : 10;
	 			
	 		$query .= " LIMIT ".($paged-1)*$tables_per_page.", ".$tables_per_page;
	 			
	 		$all_tables = $wpdb->get_results( $query, ARRAY_A );
	 		
	 		$all_tables = apply_filters('wpdatatables_filter_browse_tables', $all_tables);

	 		return $all_tables;
	 }
	 
	 /**
	  * Get table count for the browser
	  */
	  function wdt_get_table_count(){
	  	global $wpdb;
	  	
	  	$query = "SELECT COUNT(*) FROM {$wpdb->prefix}wpdatatables";
	  	
	  	$count = $wpdb->get_var( $query );
	  	
	  	return $count;
	  }
	 
	/**
	 * Helper method which creates the
	 * columnset in the DB from a WPDataTable object
	 */
	function wdt_create_columns_from_table( $table, $table_id ){
		global $wpdb;
		
		do_action('wpdatatables_before_create_columns', $table, $table_id);
		
		// Get existing columns array
		$existing_columns_query = $wpdb->prepare("SELECT orig_header 
													FROM ".$wpdb->prefix ."wpdatatables_columns 
													WHERE table_id = %d",
													$table_id);
		$columns_to_delete = $wpdb->get_col( $existing_columns_query );
		
		$columns = $table->getColumns();
		foreach($columns as $key=>&$column){
			$column->table_id = $table_id;
			$column->orig_header = $column->getTitle();
			$column->possible_values = '';
			$column->default_value = '';
			$column->input_type = 'text';
			$column->display_header = $column->getTitle();
			$column->filter_type = $column->getFilterType()->type;
			$column->column_type = $column->getDataType();
			$column->use_in_chart = false;
			$column->chart_horiz_axis = false;
			$column->group_column = false;
			$column->pos = $key;
			$column->width = '';
			$column->visible = 1;
			
			$column = apply_filters( 'wpdatatables_filter_column_before_save', $column, $table_id );
			
			if(($delete_key = array_search($column->orig_header, $columns_to_delete)) !== false) {
			    unset($columns_to_delete[$delete_key]);
			}
			
			// Check if column with this header exists in the DB
			$column_query = $wpdb->prepare("SELECT id 
											FROM ".$wpdb->prefix ."wpdatatables_columns 
											WHERE table_id = %d
											AND orig_header = %s",
											$table_id,
											$column->orig_header); 
			$column_id = $wpdb->get_var( $column_query );
			
			if(!empty($column_id)){
				// If column exists we update it
				$update_array = array(
					'display_header' => $column->display_header,
					'possible_values' => $column->possible_values,
					'default_value' => $column->default_value,
					'input_type' => $column->input_type,
					'filter_type' => $column->filter_type,
					'column_type' => $column->column_type,
					'group_column' => 0,
					'sort_column' => 0,
					'use_in_chart' => (int)$column->use_in_chart,
					'chart_horiz_axis' => (int)$column->chart_horiz_axis,
					'pos' => $column->pos,
					'width' => $column->width,
					'visible' => $column->visible
					);
			
				$update_array = apply_filters( 'wpdatatables_filter_update_column_array', $update_array, $table_id, $column );
				
				$column->id = $column_id;
				
				$wpdb->update(
								$wpdb->prefix .'wpdatatables_columns', 
								$update_array,
								array(
									'id' => $column_id
								),
								array(
									'%d'
								)
							);
			
			}else{
				// If column doesn't exist we insert it
				$insert_array = array(
					'table_id' => $table_id,
					'orig_header' => $column->orig_header,
					'display_header' => $column->display_header,
					'possible_values' => $column->possible_values,
					'default_value' => $column->default_value,
					'input_type' => $column->input_type,
					'filter_type' => $column->filter_type,
					'column_type' => $column->column_type,
					'group_column' => 0,
					'sort_column' => 0,
					'use_in_chart' => (int)$column->use_in_chart,
					'chart_horiz_axis' => (int)$column->chart_horiz_axis,
					'pos' => $column->pos,
					'width' => $column->width,
					'visible' => $column->visible
					);
			
				$insert_array = apply_filters( 'wpdatatables_filter_insert_column_array', $insert_array, $table_id, $column );
				
				$wpdb->insert($wpdb->prefix .'wpdatatables_columns', $insert_array);
				$column->id = $wpdb->insert_id;
			}
			
			do_action( 'wpdatatables_after_insert_column', $column, $table_id );
			
		}
		
		// Delete from DB all columns that do not exist any more
		foreach($columns_to_delete as $delete_header){
			$wpdb->delete( 
						$wpdb->prefix."wpdatatables_columns", 
						array(
							'orig_header' => $delete_header,
							'table_id' => $table_id
						),
						array(
							'%s',
							'%d'
						)
					);
		}
		
		return $columns;
	}
	
	/**
	 * Tries to generate a WPDataTable object by user's setiings
	 */
	function wdt_try_generate_table( $type, $content ) {
		$tbl = new WPDataTable();
		$result = array();
		
		do_action( 'wpdatatables_try_generate_table', $type, $content );
		
		$table_params = array( 'limit' => '10' );
		switch($type){
			case 'mysql' :
				try {
					$tbl->queryBasedConstruct( $content, array(), $table_params );
					$result['table'] = $tbl;
				}catch( Exception $e ) {
					$result['error'] = $e->getMessage();
					return $result;
				} 
				break;
			case 'csv' :
			case 'xls' :
				try {
					$tbl->excelBasedConstruct( $content, $table_params );
					$result['table'] = $tbl;
				} catch( Exception $e ) {
					$result['error'] = $e->getMessage();
					return $result;
				} 
				break;
			case 'xml' :
				try {
					$tbl->XMLBasedConstruct( $content, $table_params );
					$result['table'] = $tbl;
				} catch( Exception $e ) {
					$result['error'] = $e->getMessage();
					return $result;
				} 
				break;
			case 'json' :
				try {
					$tbl->jsonBasedConstruct( $content, $table_params );
					$result['table'] = $tbl;
				} catch( Exception $e ) {
					$result['error'] = $e->getMessage();
					return $result;
				} 
				break;
			case 'serialized' :
				try {
					$array = unserialize( file_get_contents ( $content ) );
					$tbl->arrayBasedConstruct( $array, $table_params );
					$result['table'] = $tbl;
				} catch( Exception $e ) {
					$result['error'] = $e->getMessage();
					return $result;
				} 
				break;
		}
		
		$result = apply_filters( 'wpdatatables_try_generate_table_result', $result );
		
		return $result;		
	}
	
	/**
	 * Renders the browser of existing tables
	 */
	function wpdatatables_browse() {
		global $wpdb, $wdt_admin_translation_array;
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) ); 
		}
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
		if($action == 'edit'){
			$id = $_GET['table_id'];
			$tpl = new PDTTpl();
			$tpl->setTemplate('edit_table.inc.php');
			// Admin JS
	        wp_enqueue_script('wpdatatables-admin',WDT_JS_PATH.'wpdatatables/wpdatatables_admin.js');
			// Google Charts
			wp_enqueue_script('wdt_google_charts','https://www.google.com/jsapi');
			// Selecter
	        wp_enqueue_script('wpdatatables-selecter',WDT_JS_PATH.'selecter/jquery.fs.selecter.min.js');
			wp_enqueue_style('wpdatatables-selecter',WDT_CSS_PATH.'jquery.fs.selecter.css');
			// Picker
	        wp_enqueue_script('wpdatatables-picker',WDT_JS_PATH.'picker/jquery.fs.picker.min.js');
			wp_enqueue_style('wpdatatables-picker',WDT_CSS_PATH.'jquery.fs.picker.css');
	        // Popup
	        wp_enqueue_script('wpdatatables-popup',WDT_JS_PATH.'popup/jquery.remodal.min.js');
			wp_enqueue_style('wpdatatables-popup',WDT_CSS_PATH.'jquery.remodal.css');
	        // JsRender
	        wp_enqueue_script('wpdatatables-jsrender',WDT_JS_PATH.'jsrender/jsrender.min.js');
	        // Table create/edit JS
	        wp_enqueue_script('wpdatatables-edit',WDT_JS_PATH.'wpdatatables/wpdatatables_edit_table.js');
			// Media upload
			wp_enqueue_script('media-upload');
			// Localization
	        wp_localize_script('wpdatatables-edit','wpdatatables_edit_strings',$wdt_admin_translation_array);
			
			$tpl->addData('wpShowTitle', __('Edit wpDataTable','wpdatatables'));
			$tpl->addData('table_id', $id);
			$tpl->addData('table_data', wdt_get_table_by_id($id));
			$tpl->addData('column_data', wdt_get_columns_by_table_id($id));
			$tpl->addData('wdtUserRoles', get_editable_roles());
			$edit_page = $tpl->returnData();
			
			$edit_page = apply_filters( 'wpdatatables_filter_edit_page', $edit_page );
			
			echo $edit_page;
			
		}else{
			if($action == 'delete') {
				$id = $_REQUEST['table_id'];
				
				if(!is_array($id)){
					$wpdb->query("DELETE 
									FROM {$wpdb->prefix}wpdatatables
									WHERE id={$id}");
				}else{
					foreach($id as $single_id){
						$wpdb->query("DELETE 
										FROM {$wpdb->prefix}wpdatatables
										WHERE id={$single_id}");
					}
				}
			}
			
			$wdtBrowseTable = new WDTBrowseTable();
			
			$wdtBrowseTable->prepare_items();
			
			ob_start();
			$wdtBrowseTable->search_box('search','search_id');
			$wdtBrowseTable->display();
			$tableHTML = ob_get_contents();
            ob_end_clean();
			
			$tpl = new PDTTpl();
			// Popup
			$tpl->addJs(WDT_JS_PATH.'popup/jquery.remodal.min.js');
			$tpl->addCss(WDT_CSS_PATH.'jquery.remodal.css');
			// Admin JS
			$tpl->addJs(WDT_JS_PATH.'wpdatatables/wpdatatables_admin.js');
			$tpl->setTemplate( 'browse.inc.php' );
			$tpl->addData( 'tableHTML', $tableHTML );
			$browse_page = $tpl->returnData();
			$browse_page = apply_filters( 'wpdatatables_filter_browse_page', $browse_page );
			
			echo $browse_page;
		}
		
		do_action( 'wpdatatables_browse_page' );
	}
	
	function wpdatatables_addnew() {
		global $wdt_admin_translation_array;
		
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$tpl = new PDTTpl();
		
		// Admin JS
                wp_enqueue_script('wpdatatables-admin',WDT_JS_PATH.'wpdatatables/wpdatatables_admin.js');
		// Selecter
                wp_enqueue_script('wpdatatables-selecter',WDT_JS_PATH.'selecter/jquery.fs.selecter.min.js');
		wp_enqueue_style('wpdatatables-selecter',WDT_CSS_PATH.'jquery.fs.selecter.css');
		// Picker
                wp_enqueue_script('wpdatatables-picker',WDT_JS_PATH.'picker/jquery.fs.picker.min.js');
		wp_enqueue_style('wpdatatables-picker',WDT_CSS_PATH.'jquery.fs.picker.css');
                // Popup
                wp_enqueue_script('wpdatatables-popup',WDT_JS_PATH.'popup/jquery.remodal.min.js');
		wp_enqueue_style('wpdatatables-popup',WDT_CSS_PATH.'jquery.remodal.css');
                // JsRender
                wp_enqueue_script('wpdatatables-jsrender',WDT_JS_PATH.'jsrender/jsrender.min.js');
                // Table create/edit JS
                wp_enqueue_script('wpdatatables-edit',WDT_JS_PATH.'wpdatatables/wpdatatables_edit_table.js');

                wp_localize_script('wpdatatables-edit','wpdatatables_edit_strings',$wdt_admin_translation_array);
		
		$tpl->addData('wdtUserRoles', get_editable_roles());
		
		$tpl->setTemplate( 'edit_table.inc.php' );
		$tpl->addData('wpShowTitle', 'Add a new wpDataTable');
		$newtable_page = $tpl->returnData();
		
		$newtable_page = apply_filters( 'wpdatatables_filter_new_table_page', $newtable_page );
		
		echo $newtable_page;
		
		do_action( 'wpdatatables_addnew_page' );
		
	}
	
	function wpdatatables_settings() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		wp_enqueue_script('wdt-colorpicker');
		
		$languages = array();
		
		foreach(glob( WDT_ROOT_PATH .'source/lang/*.inc.php') as $lang_filename) {
			$lang_filename = str_replace(WDT_ROOT_PATH .'source/lang/', '', $lang_filename);
			$name = ucwords(str_replace('_', ' ', $lang_filename));
			$name = str_replace('.inc.php', '', $name);
			$languages[] = array('file' => $lang_filename, 'name' => $name);
		}
		
		$tpl = new PDTTpl();
		$tpl->setTemplate( 'settings.inc.php' );
		$tpl->addData('wpUseSeparateCon', get_option('wdtUseSeparateCon'));
		$tpl->addData('wpMySqlHost', get_option('wdtMySqlHost'));
		$tpl->addData('wpMySqlDB', get_option('wdtMySqlDB'));
		$tpl->addData('wpMySqlUser', get_option('wdtMySqlUser'));
		$tpl->addData('wpMySqlPwd', get_option('wdtMySqlPwd'));
		$tpl->addData('wpRenderCharts', get_option('wdtRenderCharts'));
		$tpl->addData('wpRenderFilter', get_option('wdtRenderFilter'));
		$tpl->addData('wdtTablesPerPage', get_option('wdtTablesPerPage'));
		$tpl->addData('wdtNumberFormat', get_option('wdtNumberFormat'));
		$tpl->addData('wdtDecimalPlaces', get_option('wdtDecimalPlaces'));
		$tpl->addData('wdtNumbersAlign', get_option('wdtNumbersAlign'));
		$tpl->addData('wdtTabletWidth', get_option('wdtTabletWidth'));
		$tpl->addData('wdtMobileWidth', get_option('wdtMobileWidth'));
		$tpl->addData('wdtPurchaseCode', get_option('wdtPurchaseCode'));
		$tpl->addData('wdtCustomJs', get_option('wdtCustomJs'));
		$tpl->addData('wdtCustomCss', get_option('wdtCustomCss'));
		$tpl->addData('wpDateFormat', get_option('wdtDateFormat'));
		$tpl->addData('wpTopOffset', get_option('wdtTopOffset'));
		$tpl->addData('wpLeftOffset', get_option('wdtLeftOffset'));
		$tpl->addData('languages', $languages);
		$tpl->addData('wpInterfaceLanguage', get_option('wdtInterfaceLanguage'));
		$tpl->addData('wdtFonts', wdt_get_system_fonts());
		$tpl->addData('wdtBaseSkin', get_option('wdtBaseSkin'));
		$wpFontColorSettings = get_option('wdtFontColorSettings');
		if(!empty($wpFontColorSettings)){
                    $wpFontColorSettings = unserialize($wpFontColorSettings);
		}else{
                    $wpFontColorSettings = array();
		}
		// Admin JS
		$tpl->addJs(WDT_JS_PATH.'wpdatatables/wpdatatables_admin.js');
		// Selecter
		$tpl->addJs(WDT_JS_PATH.'selecter/jquery.fs.selecter.min.js');
		$tpl->addCss(WDT_CSS_PATH.'jquery.fs.selecter.css');
		// Picker
		$tpl->addJs(WDT_JS_PATH.'picker/jquery.fs.picker.min.js');
		$tpl->addCss(WDT_CSS_PATH.'jquery.fs.picker.css');
		// Popup
		$tpl->addJs(WDT_JS_PATH.'popup/jquery.remodal.min.js');
		$tpl->addCss(WDT_CSS_PATH.'jquery.remodal.css');
		
		$tpl->addData('wdtFontColorSettings', $wpFontColorSettings);
		$settings_page = $tpl->returnData();
		
		$settings_page = apply_filters( 'wpdatatables_filter_settings_page', $settings_page );
		
		echo $settings_page;
	
		do_action( 'wpdatatables_settings_page' );
	}	

?>
