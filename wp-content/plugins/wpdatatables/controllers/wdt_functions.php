<?php
/**
 * @package wpDataTables
 * @version 1.5.6
 */
/**
 * Main wpDataTables functions
 */
?>
<?php

	/**
	 * The installation/activation method, installs the plugin table
	 */
	function wpdatatables_activation(){
		global $wpdb;
		$tables_table_name = $wpdb->prefix .'wpdatatables';
		$tables_sql = "CREATE TABLE {$tables_table_name} (
						id INT( 11 ) NOT NULL AUTO_INCREMENT,
						title varchar(255) NOT NULL,
						table_type enum('mysql','xml','json','csv','xls','serialized') NOT NULL,
						content text NOT NULL,
						filtering tinyint(1) NOT NULL default '1',
						filtering_form tinyint(1) NOT NULL default '0',
						sorting tinyint(1) NOT NULL default '1',
						tools tinyint(1) NOT NULL default '1',
						server_side tinyint(1) NOT NULL default '0',
						editable tinyint(1) NOT NULL default '0',
						editor_roles varchar(255) NOT NULL default '',
						mysql_table_name varchar(255) NOT NULL default '',
						display_length int(3) NOT NULL default '10',
						fixed_columns tinyint(1) NOT NULL default '-1',
						chart enum('none','area','bar','column','line','pie') NOT NULL,
						chart_title varchar(255) NOT NULL,
						fixed_layout tinyint(1) NOT NULL default '0',
						responsive tinyint(1) NOT NULL default '0',
						word_wrap tinyint(1) NOT NULL default '0',
						hide_before_load tinyint(1) NOT NULL default '0',
						UNIQUE KEY id (id)
						)";
		$columns_table_name = $wpdb->prefix.'wpdatatables_columns';
		$columns_sql = "CREATE TABLE {$columns_table_name} (
						id INT( 11 ) NOT NULL AUTO_INCREMENT,
						table_id int(11) NOT NULL,
						orig_header varchar(255) NOT NULL,
						display_header varchar(255) NOT NULL,
						filter_type enum('null_str','text','number','number-range','date-range','select','checkbox') NOT NULL,
						column_type enum('autodetect','string','int','float','date','link','email','image') NOT NULL,
						input_type enum('none','text','textarea','date','link','email','selectbox','multi-selectbox','attachment') NOT NULL default 'text',
						id_column tinyint(1) NOT NULL default '0',
						group_column tinyint(1) NOT NULL default '0',
						sort_column tinyint(1) NOT NULL default '0',
						hide_on_phones tinyint(1) NOT NULL default '0',
						hide_on_tablets tinyint(1) NOT NULL default '0',
						use_in_chart tinyint(1) NOT NULL default '0',
						chart_horiz_axis tinyint(1) NOT NULL default '0',
						visible tinyint(1) NOT NULL default '1',
						width VARCHAR( 4 ) NOT NULL default '',
						possible_values TEXT NOT NULL default '',
						default_value VARCHAR(100) NOT NULL default '',
						css_class VARCHAR(255) NOT NULL default '',
						pos int(11) NOT NULL default '0',
						UNIQUE KEY id (id)
						)";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($tables_sql);		
		dbDelta($columns_sql);		
		if(!get_option('wdtUseSeparateCon')){
			update_option('wdtUseSeparateCon', false);
		}
		if(!get_option('wdtMySqlHost')){
			update_option('wdtMySqlHost', '');
		}
		if(!get_option('wdtMySqlDB')){
			update_option('wdtMySqlDB', '');
		}
		if(!get_option('wdtMySqlUser')){
			update_option('wdtMySqlUser', '');
		}
		if(!get_option('wdtMySqlPwd')){
			update_option('wdtMySqlPwd', '');
		}
		if(!get_option('wdtRenderCharts')){
			update_option('wdtRenderCharts', 'below');
		}
		if(!get_option('wdtRenderFilter')){
			update_option('wdtRenderFilter', 'footer');
		}
		if(!get_option('wdtRenderFilter')){
			update_option('wdtTopOffset', '0');
		}
		if(!get_option('wdtLeftOffset')){
			update_option('wdtLeftOffset', '0');
		}
		if(!get_option('wdtDateFormat')){
			update_option('wdtDateFormat', 'd/m/Y');
		}
		if(!get_option('wdtInterfaceLanguage')){
			update_option('wdtInterfaceLanguage', '');
		}
		if(!get_option('wdtTablesPerPage')){
			update_option('wdtTablesPerPage', 10);
		}
		if(!get_option('wdtNumberFormat')){
			update_option('wdtNumberFormat', 1);
		}
		if(!get_option('wdtDecimalPlaces')){
			update_option('wdtDecimalPlaces', 2);
		}
		if(!get_option('wdtNumbersAlign')){
			update_option('wdtNumbersAlign', true);
		}
		if(!get_option('wdtCustomJs')){
			update_option('wdtCustomJs', '');
		}
		if(!get_option('wdtCustomCss')){
			update_option('wdtCustomCss', '');
		}		
		if(!get_option('wdtTabletWidth')){
			update_option('wdtTabletWidth', 1024);
		}
		if(!get_option('wdtMobileWidth')){
			update_option('wdtMobileWidth', 480);
		}
		if(!get_option('wdtPurchaseCode')){
			update_option('wdtPurchaseCode', '');
		}
	}
	
	function wpdatatables_deactivation(){
	}
	
	/**
	 * Uninstall hook
	 */
	function wpdatatables_uninstall(){
		global $wpdb;
		
		delete_option('wdtUseSeparateCon');
		delete_option('wdtMySqlHost');
		delete_option('wdtMySqlDB');
		delete_option('wdtMySqlUser');
		delete_option('wdtMySqlPwd');
		delete_option('wdtRenderCharts');
		delete_option('wdtTopOffset');
		delete_option('wdtLeftOffset');
		delete_option('wdtDateFormat');
		delete_option('wdtInterfaceLanguage');
		
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wpdatatables");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wpdatatables_columns");
	}	
	
	// Make sure we don't expose any info if called directly
	if ( !function_exists( 'add_action' ) ) {
		echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
		exit;
	}
	
	/**
	 * Helper method which gets the columns from DB
	 * for a provided table ID
	 */
	 function wdt_get_columns_by_table_id( $table_id ) {
	 	global $wpdb;
		// get the columns from DB
		
		do_action( 'wpdatatables_before_get_columns_metadata', $table_id );
		
		$query = 'SELECT *
					FROM '.$wpdb->prefix.'wpdatatables_columns
				WHERE table_id='.$table_id.'
				ORDER BY pos';
		$columns = $wpdb->get_results( $query );
		$columns = stripslashes_deep( $columns );
		
		$columns = apply_filters( 'wpdatatables_filter_columns_metadata', $columns, $table_id );
		
		return $columns;
	 }	
	 
	 /**
	  * Helper function which returns all data for a table
	  */
	  function wdt_get_table_by_id( $table_id ){
	  	global $wpdb;
	  	
	  	do_action( 'wpdatatables_before_get_table_metadata', $table_id );
	  	
	  	$query = "SELECT * 
	  				FROM {$wpdb->prefix}wpdatatables
	  				WHERE id={$table_id}";
	  	$data = $wpdb->get_row( $query, ARRAY_A );
	  	$data['content'] = stripslashes($data['content']);
	  	
	  	$data = apply_filters( 'wpdatatables_filter_table_metadata', $data, $table_id );
	  	
	  	return $data;
	  }
	  
	  /**
	   * Helper func that prints out the table
	   */
	  function wdt_output_table( $id, $no_scripts = 0 ) {
	  	global $wp_scripts;
	  	echo wpdatatable_shortcode_handler( array('id'=>$id, 'no_scripts' => $no_scripts ) );
	  }
	  
	  /**
	   * Handler for the shortcode
	   */
	  function wpdatatable_shortcode_handler( $atts, $content = null ) {
		global $wpdb, $wdt_var1, $wdt_var2, $wdt_var3;
		   extract( shortcode_atts( array(
		      'id' => '0',
		      'show_only_chart' => false,
		      'no_scripts' => 0,
		      'var1' => '',
		      'var2' => '',
		      'var3' => ''
		      ), $atts ) );
		   
		   // Protection
		   if(!$id){ return false; }
		   $table_data = wdt_get_table_by_id( $id );
		   $column_data = wdt_get_columns_by_table_id( $id );
		   
		   // Do action before rendering a table
		   do_action( 'wpdatatables_before_render_table', $id );
		   
		   // Shortcode variables
		   $wdt_var1 = $var1;
		   $wdt_var2 = $var2;
		   $wdt_var3 = $var3;
		   
		   // Preparing column properties
		   $column_order = array();
		   $column_titles = array();
		   $column_widths = array();
		   $column_types = array();
		   $column_possible_values = array();
		   foreach($column_data as $column){
		   		$column_order[(int)$column->pos] = $column->orig_header;
		   		if($column->display_header){
			   		$column_titles[$column->orig_header] = $column->display_header;
		   		}
		   		if($column->width){
					$column_widths[$column->orig_header] = $column->width;
		   		}
		   		if($column->column_type != 'autodetect'){
			   		$column_types[$column->orig_header] = $column->column_type;
		   		}
			   $column_possible_values[$column->orig_header] = $column->possible_values;
		   }
		   $tbl = new WPDataTable();
		   $tbl->setWpId( $id );
		   if(empty($table_data['content'])){
		   		return __('wpDataTable with provided ID not found!','wpdatatables');
		   }
		   switch($table_data['table_type']){
		   		case 'mysql' : 
	   				if($table_data['server_side']){
	   					$tbl->enableServerProcessing();
	   				}
	   				if($table_data['editable']){
	   					if(wdt_current_user_can_edit( $table_data['editor_roles'], $id )){
		   					$tbl->enableEditing();
	   					}
	   				}
		   			$tbl->queryBasedConstruct($table_data['content'], array(),
		   				array(
		   					'data_types'=>$column_types,
		   					'column_titles'=>$column_titles
		   					)
	   					);
		   			break;
		   		case 'xls':
		   		case 'csv':
		   			$tbl->excelBasedConstruct($table_data['content'], 
		   				array(
		   					'data_types'=>$column_types,
		   					'column_titles'=>$column_titles
		   					)
		   			);
		   			break;
		   		case 'xml':
		   			$tbl->XMLBasedConstruct($table_data['content'], 
		   				array(
		   					'data_types'=>$column_types,
		   					'column_titles'=>$column_titles
		   					)
		   			);
		   			break;
		   		case 'json':
		   			$tbl->jsonBasedConstruct($table_data['content'], 
		   				array(
		   					'data_types'=>$column_types,
		   					'column_titles'=>$column_titles
		   					)
		   			);
		   			break;
		   		case 'serialized':
					$array = unserialize( file_get_contents ( $table_data['content'] ) );
		   			$tbl->arrayBasedConstruct( $array, 
		   				array(
		   					'data_types'=>$column_types,
		   					'column_titles'=>$column_titles
		   					)
		   			);
		   			break;
		   }
		   if($table_data['hide_before_load']){
		   		$tbl->hideBeforeLoad();
		   }else{
		   		$tbl->showBeforeLoad();
		   }
		   $tbl->reorderColumns( $column_order );
		   $tbl->wdtDefineColumnsWidth( $column_widths );
		   $tbl->setColumnsPossibleValues( $column_possible_values );
		   // Applying form
		   $tbl->setFilteringForm($table_data['filtering_form']);
		   // Applying responsiveness
		   if($table_data['responsive']){
		   		$tbl->setResponsive(true);
		   }
		   // Applying filter, if enabled
		   if($table_data['filtering']){
		   		$tbl->enableAdvancedFilter();
		   }
		    if(!$no_scripts){
				wp_enqueue_script('jquery-ui-core');
				wp_enqueue_script('jquery-ui-progressbar');
				wp_enqueue_script('jquery-ui-datepicker');
				wp_enqueue_script('jquery-ui-button');
				wp_enqueue_style( 'dashicons' );
				wp_enqueue_script('wdt_google_charts','https://www.google.com/jsapi');
				wp_enqueue_script('formstone-selecter',WDT_JS_PATH.'selecter/jquery.fs.selecter.min.js');
				wp_enqueue_style('formstone-selecter',WDT_CSS_PATH.'jquery.fs.selecter.css');
				wp_enqueue_script('formstone-picker',WDT_JS_PATH.'picker/jquery.fs.picker.min.js');
				wp_enqueue_style('formstone-picker',WDT_CSS_PATH.'jquery.fs.picker.css');
				wp_enqueue_script('remodal-popup',WDT_JS_PATH.'popup/jquery.remodal.min.js');
				wp_enqueue_style('remodal-popup',WDT_CSS_PATH.'jquery.remodal.css');
				wp_enqueue_script('pickadate-main',WDT_JS_PATH.'datepicker/picker.js');
				wp_enqueue_script('pickadate-date',WDT_JS_PATH.'datepicker/picker.date.js');
				wp_enqueue_style('pickadate-main',WDT_CSS_PATH.'datepicker.default.css');
				wp_enqueue_style('pickadate-date',WDT_CSS_PATH.'datepicker.default.date.css');
		    }else{
		    	$tbl->disableScripts();
		    }
		   foreach($column_data as $column){
			    // Set filter types
		   		$tbl->getColumn($column->orig_header)->setFilterType($column->filter_type);
		   		// Set CSS class
		   		$tbl->getColumn($column->orig_header)->addCSSClass($column->css_class);
		   		// set visibility
		   		if(!$column->visible){
		   			$tbl->getColumn($column->orig_header)->hide();
				}
				// Set default value
				$tbl->getColumn($column->orig_header)->setDefaultValue($column->default_value);
				
				// Check the default values passed from URL
				if(isset($_GET['wdt_column_filter'])){
					foreach($_GET['wdt_column_filter'] as $fltColKey => $fltDefVal){
						$tbl->getColumn($fltColKey)->setDefaultValue($fltDefVal);
					}
				}
				
				// Set hiding on phones and tablets for responsiveness
				if($tbl->isResponsive()){
					if($column->hide_on_phones){
						$tbl->getColumn($column->orig_header)->hideOnPhones();
					}
					if($column->hide_on_tablets){
						$tbl->getColumn($column->orig_header)->hideOnTablets();
					}
				}
				// if grouping enabled for this column, passing it to table class
				if($column->group_column){
					$tbl->groupByColumn($column->orig_header);
				}
				if($column->sort_column !== '0'){
					$tbl->setDefaultSortColumn($column->orig_header);
					if($column->sort_column == '1'){
						$tbl->setDefaultSortDirection('ASC');
					}elseif($column->sort_column == '2'){
						$tbl->setDefaultSortDirection('DESC');
					}
				}
  			    if($table_data['chart']!='none'){
			   		if($column->use_in_chart){
			   			$tbl->addChartSeries($column->orig_header);
			   		}
			   		if($column->chart_horiz_axis){
			   			$tbl->setChartHorizontalAxis($column->orig_header);
					}
 			    }
 			    // Set ID column if specified
 			    if($column->id_column){
 			    	$tbl->setIdColumnKey($column->orig_header);
 			    }
 			    // Set front-end editor input type
 			    $tbl->getColumn($column->orig_header)->setInputType($column->input_type);
		   }
		   $output_str = '';
		   if(!$show_only_chart){
			   if($table_data['title']){
                                $output_str .= apply_filters('wpdatatables_filter_table_title', (empty($table_data['title']) ? '' : '<h2>'. $table_data['title'] .'</h2>'), $id );
			   }
			   if(!$table_data['sorting']){
                                $tbl->sortDisable();
			   }
			   if(!$table_data['tools']){
                                $tbl->disableTT();
			   }
			   // display length
			   if($table_data['display_length'] != 0) {
			   	$tbl->setDisplayLength($table_data['display_length']);
			   } else {
			   	$tbl->disablePagination();
			   }

                           if(get_option('wdtInterfaceLanguage') != ''){
					$tbl->setInterfaceLanguage(get_option('wdtInterfaceLanguage'));
			   }
			   
			   $output_str .= $tbl->generateTable();
		   }

		   if($table_data['chart'] != 'none') {
		   		$tbl->setChartType(ucfirst($table_data['chart']));
		   		$tbl->setChartTitle($table_data['chart_title']);
		   		$tbl->printChart('wdt_'.$tbl->getId().'_chart');
		   		if(get_option('wdtRenderCharts')=='above'){
		   			$output_str = '<div id="wdt_'.$tbl->getId().'_chart" class="wpDataTables wdt_chart"></div>'.$output_str;
		   		}else{
		   			$output_str .= '<div id="wdt_'.$tbl->getId().'_chart" class="wpDataTables wdt_chart"></div>';
		   		}
		   }
		   // Generate the style block
	   	   $output_str .= "<style>\n";
	   	   // Table layout
	   	   $customCss = get_option('wdtCustomCss');
		   if($table_data['fixed_layout'] || $table_data['word_wrap']) {
		   		$output_str .= ($table_data['fixed_layout'] ? "table.wpDataTable { table-layout: fixed !important; }\n" : '');
		   		$output_str .= ($table_data['word_wrap'] ? "table.wpDataTable td, table.wpDataTable th { white-space: normal !important; }\n" : '');
		   }
	   		if($customCss){
	   			$output_str .= stripslashes_deep($customCss);
	   		}
	   	   if(get_option('wdtNumbersAlign')){
	   	   		$output_str .= "table.wpDataTable td.numdata { text-align: right !important; }\n";
	   	   }
	   	   $output_str .= "</style>\n";
	   	   $customJs = get_option('wdtCustomJs');
	   	   if($customJs){
	   			$output_str .= '<script type="text/javascript">'.stripslashes_deep($customJs).'</script>';
	   	   }
	   	   
		    // Color and font settings
			$wdtFontColorSettings = get_option('wdtFontColorSettings');
			if(!empty($wdtFontColorSettings)){
				$wdtFontColorSettings = unserialize($wdtFontColorSettings);
				$tpl = new PDTTpl();
				$tpl->addData('wdtFontColorSettings',$wdtFontColorSettings);
				$tpl->setTemplate( 'style_block.inc.php' );
				$style_block_html = $tpl->returnData();
				$style_block_html = apply_filters('wpdatatables_filter_style_block', $style_block_html, $id);
				$output_str .= $style_block_html;
			}
			
			$output_str = apply_filters( 'wpdatatables_filter_rendered_table', $output_str, $id );
			
		   return $output_str;
		}

	/**
	 * Returns system fonts
	 */
	function wdt_get_system_fonts(){
		$system_fonts = array(
			'Georgia, serif',
			'Palatino Linotype, Book Antiqua, Palatino, serif',
			'Times New Roman, Times, serif',
			'Arial, Helvetica, sans-serif',
			'Impact, Charcoal, sans-serif',
			'Lucida Sans Unicode, Lucida Grande, sans-serif',
			'Tahoma, Geneva, sans-serif',
			'Verdana, Geneva, sans-serif',
			'Courier New, Courier, monospace',
			'Lucida Console, Monaco, monospace'
		);
		
		$system_fonts = apply_filters( 'wpdatatables_get_system_fonts', $system_fonts );
		
		return $system_fonts;
	}
	
	/**
	 * Checks if current user can edit
	 */
	 function wdt_current_user_can_edit( $table_editor_roles, $id ){
	 	$user_can_edit = false;
	 	
	 	$table_editor_roles = strtolower($table_editor_roles);
	 	$editor_roles_arr = array();
	 	
	 	if(empty($table_editor_roles)){
	 		$user_can_edit = true;
	 	}else{
		 	$editor_roles_arr = explode(',',$table_editor_roles);
		 	$current_user = wp_get_current_user();
		 	if(!($current_user instanceof WP_User)){
		 		return false;
		 	}
		 	
		 	foreach($current_user->roles as $user_role){
		 		if(in_array(strtolower($user_role), $editor_roles_arr)){
		 			$user_can_edit = true;
		 			break;
		 		}
		 	}
	 	}
	 	return apply_filters('wpdatatables_allow_edit_table', $user_can_edit, $editor_roles_arr, $id);
	 }
	 
	 /**
	  * Removes all dangerous strings from query
	  */
	  function wpdatatables_sanitize_query( $query ){
	  	
        $query = str_replace('DELETE', '', $query);
        $query = str_replace('DROP', '', $query);
        $query = str_replace('INSERT', '', $query);
       	$query = stripslashes($query);
	  	
	  	return $query;
	  }
	  
	  /**
	   * Loads the translations
	   */
	   function wpdatatables_load_textdomain(){
	   		load_plugin_textdomain( 'wpdatatables', false, WDT_ROOT_URL.'/languages/'.get_locale().'/');
	   }
	   
	   /**
	    * Workaround for NULLs in WP
	    */
	    add_filter( 'query', 'wpdatatables_support_nulls' );
	    
	    function wpdatatables_support_nulls( $query ){
	    	$query = str_ireplace( "'NULL'", "NULL", $query );
	    	$query = str_replace('null_str','null',$query);
	    	return $query;
	    }
            
            /**
             * Auto update function
             */
            
            if('' !== get_option('wdtPurchaseCode')){
                
                $file_path = plugin_basename( __FILE__ );
                $file_path_arr = explode( '/', $file_path );
                global $wdt_plugin_slug;
                $wdt_plugin_slug = $file_path_arr[0].'/wpdatatables.php';
                
                add_filter ('pre_set_site_transient_update_plugins', 'wpdatatables_transient_update');
                function wpdatatables_transient_update($transient){
                    global $wdt_plugin_slug;
                    
                    // Remote version
                    $remote_version = WDTTools::checkRemoteVersion();

                    if(version_compare(WDT_CURRENT_VERSION, $remote_version, '<')){
                        $obj = new stdClass();
                        $obj->slug = $wdt_plugin_slug;
                        $obj->new_version = $remote_version;
                        $obj->url = 'http://wpdatatables.com/verified-download.php?purchase_code='.get_option('wdtPurchaseCode');
                        $obj->package = 'http://wpdatatables.com/verified-download.php?purchase_code='.get_option('wdtPurchaseCode');
                        $transient->response[ $wdt_plugin_slug ] = $obj;
                    }
                    return $transient;                

                }

                add_filter( 'plugins_api', 'wpdatatables_plugins_api', 10, 3 );
                function wpdatatables_plugins_api( $false, $action, $arg ){
                    global $wdt_plugin_slug;

					if ( property_exists($arg, 'slug') && ($arg->slug === $wdt_plugin_slug || $arg->slug == 'wpdatatables.php')) {
                        $information = WDTTools::checkRemoteInfo();
                        return $information;
                    }
                    return false;
                }
                
            }

?>