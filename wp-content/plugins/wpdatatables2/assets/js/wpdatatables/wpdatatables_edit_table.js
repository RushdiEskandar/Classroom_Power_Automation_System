var columnTypes = [
	{ name: 'string', value: 'String' },
	{ name: 'int', value: 'Integer' },
	{ name: 'float', value: 'Float' },
	{ name: 'date', value: 'Date' },
    { name: 'datetime', value: 'DateTime' },
    { name: 'time', value: 'Time' },
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
    { name: 'datetime-range', value: 'Datetime range' },
    { name: 'time-range', value: 'Time range' },
	{ name: 'select', value: 'Select box' },
	{ name: 'checkbox', value: 'Checkbox' }
];

var editorTypes = [
	{ name: 'none', value: 'None' },
	{ name: 'text', value: 'One-line edit' },
	{ name: 'textarea', value: 'Multi-line edit' },
    { name: 'mce-editor', value: 'HTML editor' },
	{ name: 'selectbox', value: 'Single-value selectbox' },
	{ name: 'multi-selectbox', value: 'Multi-value selectbox' },
	{ name: 'date', value: 'Date' },
    { name: 'datetime', value: 'Datetime' },
    { name: 'time', value: 'Time' },
	{ name: 'link', value: 'URL link' },
	{ name: 'email', value: 'E-mail link' },
	{ name: 'attachment', value: 'Attachment' }
];

var createColumnsBlock;
var updateColumnPositions;
var custom_uploader;
var aceEditor = null;
var wdtFormulaColumnsCount = 0;
var $currentFormattingRulesContainer = null;
var $currentFormulaContainer = null;
var columnsToDelete = [];
var wdtDateFormat = jQuery('#wdt_date_format').val().replace(/y/g, 'yy').replace(/Y/g, 'yyyy').replace(/M/g, 'mmm');
var $clickedSaveButton = null;
var wdtConfigChanged = false;
var additional_options;

(function($){

    createColumnsBlock = function( columns, singleColumn ){
		var block_html = '';
        if( typeof singleColumn === 'undefined' ){ singleColumn = false; }

		var columnsTableTmpl= $.templates("#columnsTableTmpl")
		block_html = columnsTableTmpl.render({ 
                                        columns_data: columns,
                                        filterTypes: filterTypes,
                                        columnTypes: columnTypes,
                                        editorTypes: editorTypes,
                                        singleColumn: singleColumn,
                                        tableEditable: $('#wpTableEditable').is(':checked') || $('#wdt_table_manual').val() == '1' ? 1 : 0,
                                        tableServerSide: $('#wpServerSide').is(':checked') || $('#wdt_table_manual').val() == '1' ? 1 : 0,
                                        tableType: ( $('#wdt_table_manual').val() == '1' )? 'manual': $('#wpTableType').val()
                                    });

        // Also updating colums dropdowns for Editable tables
        if( !singleColumn ){
            var id_column = $('#wdtIdColumn').val();
            var user_id_column = $('#wpUserIdColumn').val();
            $('#wdtIdColumn').html('<option value="">'+wpdatatables_edit_strings.choose_id_column+'</option>');
            $('#wpUserIdColumn').html('<option value="">'+wpdatatables_edit_strings.choose_user_id_column+'</option>');
            wdtFormulaColumnsCount = 0;
            for( var i in columns ){
                $('#wdtIdColumn').append('<option value="'+columns[i].id+'">'+columns[i].orig_header+'</option>');
                $('#wpUserIdColumn').append('<option value="'+columns[i].id+'">'+columns[i].orig_header+'</option>');
                if( columns[i].column_type == 'formula' ){ wdtFormulaColumnsCount++; }
            }
            $('#wdtIdColumn').val( id_column ).selecter('update');
            $('#wpUserIdColumn').val( user_id_column ).selecter('update');
        }

		return block_html;
    }

    function applyColumnsBlock( columns_block ) {
        $('tr.step2_row td.columnsBlock').html(columns_block);

        $('tr.step2_row td.columnsBlock input.possibleValues').tagsInput({
            'height':'',
            'width':'auto',
            'defaultText':'',
            'delimiter': ['|'],   // Or a string with a single delimiter. Ex: ';'
        });
    }
    
    updateColumnPositions = function(){
        $('tr.sort_columns_block > td').each(function(){
            $(this).find('input.columnPos').val($('tr.sort_columns_block > td').index(this));
        });
        wdtConfigChanged = true;
    }

    function applySortable(){
        $('tr.sort_columns_block').sortable({
            stop: updateColumnPositions
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
                if( $(this).val() == 'google_spreadsheet' ){
                    $('#wpUploadFileBtn').hide();
                }else{
                    $('#wpUploadFileBtn').show();
                }
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
                $('tr.editing_own_rows_row').show();
                $('tr.id_column_row').show();
                $('tr.table_inline_editing_row').show();
                $('tr.table_popover_tools_row').show();
                // Try to guess what is the ID columns for editing
                if( $('#wdtIdColumn').val() == '' ){
                    $('#wdtIdColumn option').each(function(){
                        if( $.inArray( $.trim($(this).text()), ['id','ID', 'wdt_ID']) !== -1 ){
                            $(this).prop('selected', true);
                            $('#wdtIdColumn').selecter('update');
                            return false;
                        }
                    });
                }
            }else{
                $('tr.table_mysql_name_row').hide();
                $('tr.editable_table_column_row').hide();
                $('tr.editor_roles_row').hide();
                $('tr.editing_own_rows_row').hide();
                $('tr.user_id_row').hide();
                $('tr.id_column_row').hide();
                $('tr.table_inline_editing_row').hide();
                $('tr.table_popover_tools_row').hide();
                $('#wpTableEditingOwnRowsOnly').prop('checked',false).change();
            }
        });

        $('#wpTableEditingOwnRowsOnly').change(function(){
            if( $(this).is(':checked') ){
                $('tr.user_id_row').show();                        
            }else{
                $('tr.user_id_row').hide();                        
            }
        })

        /**
         * Trigger change flag on any change
         */
        $(document).on('change','#wpDataTablesSettings input,#wpDataTablesSettings select,#wpDataTablesSettings textarea', function(e){
            e.preventDefault();
            wdtConfigChanged = true;
        });

        $('#wpServerSide').change(function(){
            if(!$(this).is(':checked')){
                $('#wpTableEditable').removeAttr('checked').change();
                $('#wpTableInlineEditing').removeAttr('checked').change();
                $('#wpTablePopoverTools').removeAttr('checked').change();
                $('tr.autorefresh_row').hide();
            }else{
                $('tr.autorefresh_row').show();
            }
        });
        $('#wpServerSide').change();
        if( $('#wdt_table_manual').val() == '1' ){
            $('tr.autorefresh_row').show();
        }

        $('#wpTableEditable').change();

        $('#wdtResponsive').change(function(){
            if($(this).is(':checked')){
                $('tr.responsive_table_column_row').show();
            }else{
                $('tr.responsive_table_column_row').hide();
            }
        });

        $('#wdtResponsive').change();

        /**
         * Hide Limit Table Layout when Scrollable is checked
         */
        $( '#wdtScrollable' ).change(function() {
            if(($(this).is(':checked'))){
                $('#wpFixedLayout').removeAttr('checked').change();
                $('tr.limit_table_layout').hide();
            }else{
                $('tr.limit_table_layout').show();
            }
        });
        /**
         * Hide Word wrap when Limit table layout and scrollable checked
         * Show Word wrap and Width  when Limit table tayout is checked
         */
        $('#wpFixedLayout').change(function() {
            if(($('#wpFixedLayout').is(':checked'))){
                $('tr.word_wrap_row').show();
                $('tr.column_width').show();
            }else{
                $('#wpWordWrap').removeAttr('checked').change();
                $('tr.word_wrap_row').hide();
                $('tr.column_width').hide();
                $('input.columnWidth').val("");
            }
        });




        // Show editor roles picker for manually created tables
        if( $('#wdt_table_manual').val() == '1' ){
            $('.table_editable_row').show();
            $('#wpTableMysqlName').attr('disabled','disabled');
        }

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

        //[<-- Full version insertion #24 -->]//

        window.send_to_editor = function(html) {
            // adding a wrapper so $ could find the element
            html = '<span>'+html+'</span>';
            file_url = $('a',html).attr('href');
            $('#wpInputFile').val(file_url).change();
            tb_remove();
        };

        alertChooseInput = function () {
            if ($('#wpTableEditable').is(':checked')){
                var isNONE = true;
                $(".editable_table_column_row span.selecter-selected").each(function() {
                    if ($(this).text()!=='None') {
                        isNONE = false;
                    }
                });
                if (isNONE) {
                    wdtAlertDialog(wpdatatables_edit_strings.modal_text, wpdatatables_edit_strings.modal_title);
                }
            }
        }

        $('.submitStep1').click(function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            $clickedSaveButton = $(this);
            saveTable();
            alertChooseInput();
        });

        $('.submitStep2').click(function(e){
            e.preventDefault();
            $clickedSaveButton = $(this);
            e.stopImmediatePropagation();
            saveTable();
            alertChooseInput();

        });
        
        // Toggle 'show SUM' checkbox for float and integer columns
        $(document).on('change','select.columnType',function(){
            if( ( $(this).val() == 'float' ) || ( $(this).val() == 'int' ) ){
                $(this).closest('tbody').find('tr.sum_row').show();
                if( $(this).val() == 'int' ){
                    $(this).closest('tbody').find('tr.skip_ts_row').show();
                }else{
                    $(this).closest('tbody').find('tr.skip_ts_row').hide();
                }
            }else{
                $(this).closest('tbody').find('tr.sum_row').hide();
                $(this).closest('tbody').find('tr.sum_row input').prop('checked', false);
                $(this).closest('tbody').find('tr.skip_ts_row').hide();
                $(this).closest('tbody').find('tr.skip_ts_row input').prop('checked', false);
            }

        })

        /**
         * Collect columns data from block
         */
         var collectColumnsData = function(){
            var columns = [];
            $('td.columnsBlock table.column_table').each(function(){
                var column = {};
                column.id = $(this).attr('rel');
                column.orig_header = $(this).find('tr.columnHeaderRow td b').text();
                column.css_class = $(this).find('input.cssClasses').val();
                column.display_header = $(this).find('input.displayHeader').val();
                column.possible_values = $(this).find('input.possibleValues').val();
                column.default_value = $(this).find('input.defaultValue').val();
                column.filter_type = $(this).find('select.filterType').val();
                if( $(this).hasClass('formula_column') ){
                    column.column_type = 'formula';
                    column.calc_formula = $(this).find('input.calc_formula').val();
                    column.input_type = 'none';
                    column.input_mandatory = 0;
                }else{
                    column.column_type = $(this).find('select.columnType').val();
                    column.calc_formula = '';
                    column.input_type = $(this).find('select.inputType').val();
                    column.input_mandatory = $(this).find('input.mandatoryInput').is(':checked') ? 1 : 0;
                }
                column.id_column = ($('#wpTableEditable').is(':checked') && ( $('#wdtIdColumn').val() != '' ) && ( column.id == $('#wdtIdColumn').val() ));
                column.group_column = $(this).find('input.groupColumn').is(':checked');
                column.sort_column = $(this).find('input.sortColumn').is(':checked') ? $(this).find('input.sortColumn:checked').val() : 0;
                column.hide_on_phones = $(this).find('.hideOnPhones').is(':checked');
                column.hide_on_tablets = $(this).find('.hideOnTablets').is(':checked');
                column.use_in_chart = false; // deprecated
                column.chart_horiz_axis = false; // deprecated
                column.pos = $(this).find('input.columnPos').val();
                column.width = $(this).find('input.columnWidth').val();
                column.text_before = $(this).find('input.textBefore').val();
                column.text_after = $(this).find('input.textAfter').val();
                column.formatting_rules = $(this).find('input.formatting_rules').val();
                column.visible = $(this).find('input.columnVisible').is(':checked');
                column.color = $(this).find('input.color').val();
                column.sum_column = $(this).find('input.sumColumn').is(':checked') ? 1 : 0;
                column.skip_thousands_separator = $(this).find('input.skip_thousands_separator').is(':checked') ? 1 : 0;
                columns.push(column);
            });
            return columns;
         }

        /**
         * Save table
         */
         var saveTable = function( callback ){
            if( typeof callback == 'undefined' ){ callback = false; }
            var valid = true;
            if($('#wpTableType').val()==''){
                wdtAlertDialog(wpdatatables_edit_strings.table_type_not_empty,wpdatatables_edit_strings.error_label);
                valid = false;
                $('#wpTableType').closest('tr').addClass('error');
            }else{
                $('#wpTableType').closest('tr').removeClass('error');
            }
            if( $('#wpTableType').val() == 'mysql' ){
                if( aceEditor.getValue() == '' ){
                    wdtAlertDialog(wpdatatables_edit_strings.mysql_query_cannot_be_empty,wpdatatables_edit_strings.error_label);
                    $('tr.mysqlquery_row').addClass('error');
                    valid = false;
                }else{
                    $('tr.mysqlquery_row').removeClass('error');
                }
            }else if($('#wpTableType').val()!=''){
                if( $('#wpInputFile').val() == '' ){
                    wdtAlertDialog( wpdatatables_edit_strings.table_input_source_not_empty, wpdatatables_edit_strings.error_label );
                    valid = false;
                    $('tr.inputfile_row').addClass('error');
                }else{
                    $('tr.inputfile_row').removeClass('error');
                }
            }
            if($('#wpTableEditable').is(':checked')){
                if($('#wpTableMysqlName').val()==''){
                    wdtAlertDialog(wpdatatables_edit_strings.mysql_table_name_not_set,wpdatatables_edit_strings.error_label); 
                    $('tr.table_mysql_name_row').addClass('error');
                    valid = false;
                }
            }else{
                $('tr.table_mysql_name_row').removeClass('error');
            }
            if( $('#wpTableEditingOwnRowsOnly').is(':checked') 
                    && ( $('#wpUserIdColumn').val() == '' ) ){
                    wdtAlertDialog(wpdatatables_edit_strings.userid_column_not_set,wpdatatables_edit_strings.error_label); 
                    $('tr.user_id_row').addClass('error');
                    valid = false;
            }else{
                $('tr.user_id_row').removeClass('error');
            }

            if( !valid ){ return; }

            // collecting table settings data
            var data = { };
            data.action = 'wdt_save_table';
            data.wdtNonce = $('#wdtNonce').val();
            data.table_title = $('#wpTableTitle').val();
            data.show_title = $('#wpShowTableTitle').is(':checked') ? 1 : 0;
            data.table_type = $('#wpTableType').val();
            data.columns_to_delete = columnsToDelete;

            if( $( '#wdt_table_manual' ).val() == '1' ){
                data.table_type = 'manual';
            }

            if(data.table_type == 'mysql'){
                data.table_content = aceEditor.getValue();
            }else{
                data.table_content = $('#wpInputFile').val();
            }
            data.table_editable = $('#wpTableEditable').is(':checked');
            data.table_inline_editing = $('#wpTableInlineEditing').is(':checked');
            data.table_popover_tools = $('#wpTablePopoverTools').is(':checked');
            data.responsive = $('#wdtResponsive').is(':checked');
            data.scrollable = $('#wdtScrollable').is(':checked');
            data.table_mysql_name = $('#wpTableMysqlName').val();
            data.edit_only_own_rows = $('#wpTableEditingOwnRowsOnly').is(':checked') ? 1 : 0;
            data.userid_column_id = $('#wpUserIdColumn').val();
            data.editor_roles = $('#wpTableEditorRoles').html()
            data.table_advanced_filtering = $('#wpAdvancedFilter').is(':checked');
            data.table_filter_form = $('#wpAdvancedFilterForm').is(':checked');
            data.table_tools = $('#wpTableTools').is(':checked');
            if( data.table_tools ){
                data.table_tools_config = {
                    columns: $('#wdtTTColumns').is(':checked') ? 1 : 0,
                    print: $('#wdtTTPrint').is(':checked') ? 1 : 0,
                    copy: $('#wdtTTCopy').is(':checked') ? 1 : 0,
                    excel: $('#wdtTTExcel').is(':checked') ? 1 : 0,
                    csv: $('#wdtTTCSV').is(':checked') ? 1 : 0,
                    pdf: $('#wdtTTPDF').is(':checked') ? 1 : 0
                }
            }
            data.table_sorting = $('#wpSortByColumn').is(':checked');
            data.fixed_layout = $('#wpFixedLayout').is(':checked');
            data.word_wrap = $('#wpWordWrap').is(':checked');
            data.table_display_length = $('#wpDisplayLength').val();
            data.table_fixheader = false;
            data.table_fixcolumns = 0;
            data.table_chart = 'none';
            data.table_charttitle = '';
            data.table_serverside = $('#wpServerSide').is(':checked');
            if( data.table_serverside ){
                data.table_auto_refresh = $('#wdtAutoRefresh').val();
            }else{
                data.table_auto_refresh = 0;
            }
            data.hide_before_loaded = $('#wdtHideBeforeLoaded').is(':checked');
            data.table_id = $('#wpDataTableId').val();
            data.current_user_placeholder = $('#wdtCurrentUserIdPlaceholderDefault').val();
            data.var1_placeholder = $('#wdtVar1PlaceholderDefault').val();
            data.var2_placeholder = $('#wdtVar2PlaceholderDefault').val();
            data.var3_placeholder = $('#wdtVar3PlaceholderDefault').val();

            if ( typeof additional_options != 'undefined' ) {
                for ( var i = 0; i < additional_options.length; i++ ) {
                    var option_name = additional_options[i]['option_name'];
                    var option_value = additional_options[i]['option_value']();
                    data[additional_options[i]['option_name']] = option_value;
                }
            }

            $('#wdtPreloadLayer').show();
            $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data: data,
                    dataType: 'json',
                    success: function(response){
                        if(response.error) {
                            $('#wdtPreloadLayer').hide();
                            if(response.error.indexOf('No data') !== -1){
                                if( $( '#wdt_table_manual' ).val() != '1' ){
                                    response.error += '<br/>';
                                    response.error += wpdatatables_edit_strings.empty_result_error;
                                }else{
                                    response.error = wpdatatables_edit_strings.empty_manual_table_error;
                                }
                            }
                            wdtAlertDialog(wpdatatables_edit_strings.backend_error_report+response.error,wpdatatables_edit_strings.error_label);
                        }else{
                            // Redirect to Edit page if the table has been just created
                            if( $('#wpDataTableId').val() == '' ){
                                window.location = 'admin.php?page=wpdatatables-administration&action=edit&table_id='+response.table_id;
                            }

                            columnsToDelete = [];

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
                                                frontendColumns.splice(j,1);
                                                break;
                                            }
                                        }
                                    }
                                    if( !columnAdded ){
                                        mergedColumns.push( response.columns[i] );
                                    }
                            }
                            // Adding the formula columns
                            for(var j in frontendColumns){
                                if(frontendColumns[j].column_type == 'formula'){
                                    mergedColumns.push(frontendColumns[j]);
                                }
                            }


                            var columns_block = createColumnsBlock(mergedColumns);

                            $('#step2-postbox').show();
                            $('tr.step2_row').show();
                            applyColumnsBlock( columns_block );
                            $('tr.step2_row td.columnsBlock td input.color').each(function(){
                                $(this).wpColorPicker();
                            });
                            applySortable();
                             $('tr.step2_row td.columnsBlock select.columnType').each(function(){
                                $(this).change();
                            });

                            $('.previewButton').show();
                            $('#wdtResponsive').change();
                            $('#wpTableEditable').change();
                            $('#wpTableInlineEditing').change();
                            $('#wpTablePopoverTools').change();
                            applySelecter();

                            // Check for duplicated column positions and reordering columns if necessary
                            var column_positions = {};
                            $('td.columnsBlock table.column_table input.columnPos').each(function(){
                                if( typeof( column_positions[$(this).val()] ) !== 'undefined' ){
                                    updateColumnPositions();
                                    return false;
                                }else{
                                    column_positions[$(this).val()] = true;
                                }
                            });

                            // If columns are defined - saving them as well
                            if($('td.columnsBlock table.column_table').length > 0){

                                if($('#wpTableEditable').is(':checked')){
                                    if($('#wdtIdColumn').val() == ''){
                                        wdtAlertDialog(wpdatatables_edit_strings.id_column_not_set,wpdatatables_edit_strings.error_label);
                                        return;
                                    }
                                }
                                var data = { };
                                data.action = 'wdt_save_columns';
                                data.table_id = $('#wpDataTableId').val();
                                data.wdtColumnsNonce = $('#wdtColumnsNonce').val();
                                data.columns = JSON.stringify( collectColumnsData() );

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

                                            var columns_block = createColumnsBlock(response.columns);
                                            $('#step2-postbox').show();
                                            $('tr.step2_row').show();
                                            applyColumnsBlock( columns_block );
                                            $('tr.step2_row td.columnsBlock td input.color').each(function(){
                                                $(this).wpColorPicker();
                                            });
                                            $('tr.step2_row td.columnsBlock select.columnType').each(function(){
                                               $(this).change();
                                           });
                                            applySortable();
                                            $('#wdtResponsive').change();
                                            $('#wpTableEditable').change();
                                            $('#wpTableInlineEditing').change();
                                            $('#wpTablePopoverTools').change();
                                            $('.submitStep2').removeAttr('disabled');
                                            applySelecter();

                                            if($('#wpFixedLayout').is(':checked')){
                                                $('tr.column_width').show();
                                                $('tr.word_wrap_row').show();
                                            }else{
                                                $('#wpWordWrap').removeAttr('checked').change();
                                                $('tr.word_wrap_row').hide();
                                                $('tr.column_width').hide();
                                                $('input.columnWidth').val("");
                                            }

                                            $( $.templates("#wdtSaveDoneTemplate").render() ).insertBefore($clickedSaveButton).fadeIn( 300 );
                                            setTimeout(
                                                function(){
                                                    $('#wdtSaveConfirmationPopover').fadeOut(
                                                        300,
                                                        function(){ $('#wdtSaveConfirmationPopover').remove(); }
                                                    )
                                                },
                                                3000
                                            );
                                            $clickedSaveButton = null;
                                            wdtConfigChanged = false;
                                            if( callback ){ callback(); }
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
//[<-- Full version -->]//
        /**
         * Init editor roles dialog
         */
        $('#wdtUserRoles').dialog({
                autoOpen: false,
                modal: true,
                buttons: [
                        {
                            text: wpdatatables_edit_strings.ok,
                            click: function(){
                                var editorRoles = [];
                                $('input.wdtRoleCheckbox:checked').each(function(){
                                    editorRoles.push($(this).val());
                                });
                                var editorRolesStr = editorRoles.join(',');
                                $('#wpTableEditorRoles').html(editorRolesStr);
                                $(this).dialog('close');
                            }
                        },
                        {
                            text: wpdatatables_edit_strings.cancel,
                            click: function(){
                                $(this).dialog('close');
                            }
                        }
                ]
        });

        /**
         * Init formula dialog
         */
        $('#wdtFormulaBuilderDialog').dialog({
            autoOpen: false,
            modal: true,
            width: 750,
            buttons: [
                {
                    text: wpdatatables_edit_strings.ok,
                    click: function(){
                        $('#wdt_formula_preview_block div.preview_container').html('').hide();
                        if( $currentFormulaContainer !== null ){
                            $currentFormulaContainer.val( $('#wdt_formula_edit').val() );
                            $('#wdt_formula_edit').val('');
                            $currentFormulaContainer = null;
                        }
                        $(this).dialog('close');
                    }
                },
                {
                    text: wpdatatables_edit_strings.cancel,
                    click: function(){
                        $currentFormulaContainer = null;
                        $('#wdt_formula_preview_block div.preview_container').html('').hide();
                        $('#wdt_formula_edit').val('');
                        $(this).dialog('close');
                    }
                }
            ]
        });

        /**
         * Init formatting rules dialog
         */
        $('#wdtConditionalFormattingDialog').dialog({
                autoOpen: false,
                modal: true,
                width: 970,
                buttons: [
                    {
                        text: wpdatatables_edit_strings.ok,
                        click: function(){
                            if( $currentFormattingRulesContainer !== null ){
                                var columnType = $currentFormattingRulesContainer.closest('tbody').find('select.columnType').val();
                                var formattingRules = [];
                                jQuery( '#formattingRules div.formattingRuleBlock' ).each(function(){
                                    if( ( columnType == 'int' ) || ( columnType == 'float' ) ){
                                        var cellVal = parseFloat( jQuery(this).find('input.cellVal').val() );
                                    }else{
                                        var cellVal = jQuery(this).find('input.cellVal').val().replace('"',"'");
                                    }
                                    formattingRules.push({
                                        ifClause: jQuery(this).find('select.formatting_rule_if_clause').val(),
                                        cellVal: cellVal,
                                        action: jQuery(this).find('select.formatting_rule_action').val(),
                                        setVal:  jQuery(this).find('input.setVal').val()
                                    });
                                });
                                if( formattingRules.length ){
                                    $currentFormattingRulesContainer.val( JSON.stringify( formattingRules ) );
                                }else{
                                    $currentFormattingRulesContainer.val( '' );
                                }
                            }
                            $currentFormattingRulesContainer = null;
                            $(this).dialog('close');
                        }
                    },
                    {
                        text: wpdatatables_edit_strings.cancel,
                        click: function(){
                            $currentFormattingRulesContainer = null;
                            $(this).dialog('close');
                        }
                    }
                ]
        });
        //[<--/ Full version -->]//
        /**
         * Allow making editor mandatory
         */
        $(document).on('change','select.inputType',function(e){
            e.preventDefault();
            if( $(this).val() == 'none' ){
                $(this).closest('table.column_table').find('input.mandatoryInput').prop( 'disabled', true );
            }else{
                $(this).closest('table.column_table').find('input.mandatoryInput').prop( 'disabled', false );
            }
        });

        /**
         * Toggle placeholders table
         */
        $('#wdtPlaceholdersTableToggle').click(function(e){
            e.preventDefault();
            if( !$('#wdtPlaceholdersTable').is(':visible') ){
                $('#wdtPlaceholdersTable').show(300);
                $(this).find('span').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
            }else{
                $('#wdtPlaceholdersTable').hide(300);
                $(this).find('span').addClass('dashicons-arrow-down-alt2').removeClass('dashicons-arrow-up-alt2');
            }
        });

        /**
         * Open editor roles dialog
         */
        $('#selectEditorRoles').click(function(e){
            e.preventDefault();
            $('#wdtUserRoles').dialog('open');
        });

        //[<-- Full version -->]//
        /**
         * Add a formula column
         */
        $('button.addFormulaButton').click(function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            wdtFormulaColumnsCount++;
            var column_name = 'formula_'+wdtFormulaColumnsCount;
            if( $( 'table.formula_column b:contains("' + column_name + '")').length ){
                column_name = column_name+'_1';
            }
            var formulaBlockHtml = createColumnsBlock([{
                orig_header: column_name,
                display_header: column_name,
                calc_formula: '',
                chart_horiz_axis: 0,
                color: '',
                column_type: 'formula',
                css_class: '',
                default_value: '',
                filter_type: 'text',
                formatting_rules: '[]',
                group_column: 0,
                hide_on_phones: 0,
                hide_on_tablets: 0,
                id: '',
                id_column: 0,
                input_mandatory: 0,
                input_type: '',
                pos: $('.columnsBlock table.column_table').length,
                possible_values: '',
                skip_thousands_separator: 0,
                sort_column: 0,
                sum_column: 0,
                table_id: $('#wpdatatable_id').val(),
                text_after: '',
                text_before: '',
                use_in_chart: 0,
                visible: 1
            }], true);
            $('td.columnsBlock tr.sort_columns_block').append( formulaBlockHtml );
            applySelecter();
            $('#step2-postbox .inside').animate({scrollLeft: $('#step2-postbox .inside')[0].scrollWidth}, 200);
        });

        /**
         * Open formula editor
         */
        $(document).on('click','button.define_formula',function(e){
            e.preventDefault();
            $currentFormulaContainer = $(this).closest('tr').find('input.calc_formula');
            var table_columns = [];
            var columns_block_str = '';
            $('table.column_table:not(.formula_column)').each(function(){
                var columnType = $(this).find('select.columnType').val();
                if( columnType == 'int' || columnType == 'float' ) {
                    columns_block_str += '<div class="column_block" >' + $(this).find('tr.columnHeaderRow td b').html() + '</div>';
                }
            });
            $('#wdt_formula_editor_columns div.columns_container').html( columns_block_str );
            $('#wdt_formula_edit').val( $currentFormulaContainer.val() );
            $('#wdtFormulaBuilderDialog').dialog('open');
        });

        /**
         * Remove a formula column
         */
        $(document).on('click','button.removeColumnBlock',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            columnsToDelete.push( $(this).closest('td').find('b').html() );
            $(this).closest('table.column_table').parent().remove();
        });

        /**
         * Insert column header in formula editor
         */
        $(document).on('click','#wdt_formula_editor_columns div.column_block', function(e){
            e.preventDefault();
            $('#wdt_formula_edit').insertAtCaret( $(this).text() );
        });

        /**
         * Insert operator in formula editor
         */
        $(document).on('click','#wdt_formula_editor_operators button', function(e){
            e.preventDefault();
            $('#wdt_formula_edit').insertAtCaret( $(this).text() );
        });

        /**
         * Preview formula result for 5 rows
         */
        $(document).on('click','#wdt_formula_preview_block button.preview_formula_result',function(e){
            e.preventDefault();
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'wpdatatables_preview_formula_result',
                    table_id: $('#wpdatatable_id').val(),
                    formula: $('#wdt_formula_edit').val()
                },
                success: function(data){
                    $('#wdt_formula_preview_block div.preview_container').html( data ).show();
                }
            })
        });
        //[<--/ Full version -->]//

        /**
         * Preview handlder
         */
        var wdtPreviewTable = function(){
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
                        height: 'auto',
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
        }

        /**
         * Preview the table in the popup - trigger save if something changed
         */
        $('button.previewButton').click(function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            if( wdtConfigChanged ){
                saveTable( wdtPreviewTable );
            }else{
                wdtPreviewTable();
            }
        });

        //[<-- Full version -->]//
        /**
         * Conditional formatting dialog for columns
         */
        $(document).on('click', 'button.define_formatting_rules', function(e){
            e.preventDefault();
            $('#formattingRules').html('');
            // Render the rules
            $currentFormattingRulesContainer = $(this).parent().find('input.formatting_rules');
            var formattingRules = $.parseJSON( $currentFormattingRulesContainer.val() );
            var columnType = $currentFormattingRulesContainer.closest('tbody').find('select.columnType').val();
            if( formattingRules ){
                $('#formattingRules').html('');
                for( var i in formattingRules ){
                    var formattingRuleHtml = generateFormattingRuleHTML({
                                                    ifClause: formattingRules[i].ifClause,
                                                    cellVal: formattingRules[i].cellVal,
                                                    action: formattingRules[i].action,
                                                    setVal: formattingRules[i].setVal
                                                });
                    jQuery('#formattingRules').append( formattingRuleHtml );
                }
                if( columnType == 'date'  ){
                    applyPickadate( $('#formattingRules input.cellVal') );
                }
                $('#formattingRules select.formatting_rule_action').change();
            }else{
                $('#formattingRules').html(wpdatatables_edit_strings.no_formatting_rules);
            }
            $('#wdtConditionalFormattingDialog').dialog('open');
        });
//[<--/ Full version -->]//

        /**
         * Show 'toggle table tools config' button when table tools is enabled/disabled
         */
        $('#wpTableTools').change(function(e){
            e.preventDefault();
            if( $(this).is(':checked') ){
                $('.wdtConfigureTableToolsBlock').show();
            }else{
                $('.wdtConfigureTableToolsBlock').hide();
            }
        });
        $('#wpTableTools').change();

        /**
         * Toggle the table tools configuration block
         */
        $('#wdtConfigureTableToolsToggle').click(function(e){
            e.preventDefault();
            if( $('#wdtConfigureTableToolsBlock').is(':visible') ){
                $('#wdtConfigureTableToolsBlock').hide();
                $(this).find('span').addClass('dashicons-arrow-down-alt2').removeClass('dashicons-arrow-up-alt2');
            }else{
                $(this).find('span').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
                $('#wdtConfigureTableToolsBlock').show();
            }
        })

        /**
         * Helper function for rendering formatting rule blocks
         */
        var generateFormattingRuleHTML = function( columnData ){
            var formattingRuleTmpl = $.templates('#formattingRuleBlockTemplate');
            var $formattingRuleHtml = $( formattingRuleTmpl.render( columnData ) );
            
            var columnType = $currentFormattingRulesContainer.closest('tbody').find('select.columnType').val();
            
            if( ['date','int','float','datetime','time'].indexOf( columnType ) !== -1 ){
                $formattingRuleHtml
                        .find('select.formatting_rule_if_clause option[value="contains"],select.formatting_rule_if_clause option[value="contains_not"]')
                        .remove();
            }
            if( ['string','link','email','image'].indexOf( columnType ) !== -1 ){
                $formattingRuleHtml
                        .find('select.formatting_rule_if_clause option[value="lt"],'
                              +'select.formatting_rule_if_clause option[value="lteq"],'
                              +'select.formatting_rule_if_clause option[value="gteq"],'
                              +'select.formatting_rule_if_clause option[value="gt"]'
                              )
                        .remove();
            }

            return $formattingRuleHtml[0].outerHTML;
        }

        /**
         * Helper function to apply datepicker
         */
        function applyPickadate( $selector ){
            $selector
            .pickadate({
                format: wdtDateFormat,
                formatSubmit: wdtDateFormat,
                selectYears: 20,
                selectMonths: true,
                container: '.wpDataTables.metabox-holder'
            });
        }

        //[<-- Full version -->]//
        /**
         * Add a conditional formatting rule block
         */
        jQuery('button.addFormattingRuleBlock').click(function(e){
            e.preventDefault();
            var formattingRuleHtml = generateFormattingRuleHTML({
                                            ifClause: 'lt',
                                            cellVal: '',
                                            action: 'setCellColor',
                                            setVal: ''
                                        });
            if( jQuery('#formattingRules div.formattingRuleBlock').length == 0 ){
                $('#formattingRules').html('');
            }
            $('#formattingRules').append( formattingRuleHtml );
            var columnType = $currentFormattingRulesContainer.closest('tbody').find('select.columnType').val();
            if( columnType == 'date'  ){
                applyPickadate( $('#formattingRules input.cellVal:eq(-1)') );
            }
            $('#formattingRules select.formatting_rule_action:eq(-1)').change();
        });
        
        /**
         * Delete formatting rule
         */
        $(document).on('click','button.deleteFormattingRule',function(e){
            e.preventDefault();
            $(this).closest('div.formattingRuleBlock').remove();
        });

        /**
         * Helper function to show/hide the colorpicker in conditional formatting block
         */
        $(document).on('change','div.formattingRuleBlock select.formatting_rule_action',function(e){
            e.preventDefault();
            if( $(this).val() == 'setCellColor' || $(this).val() == 'setRowColor' || $(this).val() == 'setColumnColor' ){
                $(this).parent().find('input.setVal').wpColorPicker();
            }else{
                var val = $(this).parent().find('input.setVal').val();
                $(this).parent().find('div.wp-picker-container').replaceWith('<input class="setVal" value="'+val+'" />')
            }
        });
        //[<--/ Full version -->]//

        /**
         * Ungroup columns
         */
        $('button.ungroupButton').click(function(e){
            e.preventDefault();
            $('input.groupColumn').removeAttr('checked').parent().find('div.picker').removeClass('checked');
        });

        $('#wpTableType').trigger('change');

        if(typeof columns_data !== 'undefined' && columns_data.length > 0) {
                    var columns_block = createColumnsBlock(columns_data);
                    $('#step2-postbox').show();
                    $('tr.step2_row').show();
                    applyColumnsBlock( columns_block );
                    $('#wdtResponsive').change();
                    $('tr.step2_row td.columnsBlock td input.color').each(function(){
                        $(this).wpColorPicker();
                    });
                    $('tr.step2_row td.columnsBlock select.columnType').each(function(){
                       $(this).change();
                    });
                    applySortable();
                    $('.previewButton').show();
        }

        applySelecter();

        //[<-- Full version -->]//
        /**
         * Handler to generate possible values for columns
         */
        $(document).on('click','button.generatePossibleValues',function(e){
            e.preventDefault();

            var $this = $(this);
            var $coulmn_table_elm = $(this).closest('table.column_table');
            var data = { };
            data.action = 'wpdatatable_get_column_distinct_values';
            data.table_id = $('#wpDataTableId').val();
            data.column_id = $coulmn_table_elm.attr('rel');

            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: data,
                dataType: 'json',
                beforeSend: function() {
                    $this.closest('tr').addClass('overlayed');
                },
                complete: function() {
                    $this.closest('tr').removeClass('overlayed');
                },
                success: function( column_distinct_values ) {
                    if( !(column_distinct_values instanceof Array) ) {
                        return;
                    }

                    var $possible_values_elm = $coulmn_table_elm.find('.possibleValues');
                    if( $possible_values_elm.val().length > 0 ) {
                        //confirmation dialog
                        $( "#merge-possible-values" ).dialog({
                            resizable: false,
                            height: 'auto',
                            width: 'auto',
                            modal: true,
                            buttons: [
                                {
                                    text: wpdatatables_edit_strings.merge,
                                    click: function() {
                                        var possible_values = $possible_values_elm.val().split('|');
                                        $.merge(possible_values, column_distinct_values);

                                        //merging without duplicates
                                        column_distinct_values = column_distinct_values.concat(possible_values.filter(function (item) {
                                            return column_distinct_values.indexOf(item) < 0;
                                        }));
                                        column_distinct_values.sort();
                                        $possible_values_elm.importTags( column_distinct_values.join('|') );

                                        $( this ).dialog( "destroy" );
                                    }
                                },
                                {
                                    text: wpdatatables_edit_strings.replace,
                                    click: function () {
                                        column_distinct_values.sort();
                                        $possible_values_elm.importTags(column_distinct_values.join('|'));
                                        $(this).dialog("destroy");
                                    }
                                },
                                {
                                    text: wpdatatables_edit_strings.cancel,
                                    click: function() {
                                        $( this ).dialog( "destroy" );
                                    }
                                }
                            ]
                        });
                    } else {
                        column_distinct_values.sort();
                        $possible_values_elm.importTags( column_distinct_values.join('|') );
                    }
                }
            });

        });

        /**
         * Clear possible values
         */
        $(document).on('click','button.clearPossibleValues', function(e){
            e.preventDefault();

            var $coulmn_table_elm = $(this).closest('table.column_table').find('.possibleValues').importTags('');
        });
        //[<--/ Full version -->]//

        /**
         * Helper method to insert at textarea cursor position
         */
        jQuery.fn.extend({
            insertAtCaret: function (myValue) {
                return this.each(function (i) {
                    if (document.selection) {
                        //For browsers like Internet Explorer
                        this.focus();
                        var sel = document.selection.createRange();
                        sel.text = myValue;
                        this.focus();
                    }
                    else if (this.selectionStart || this.selectionStart == '0') {
                        //For browsers like Firefox and Webkit based
                        var startPos = this.selectionStart;
                        var endPos = this.selectionEnd;
                        var scrollTop = this.scrollTop;
                        this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
                        this.focus();
                        this.selectionStart = startPos + myValue.length;
                        this.selectionEnd = startPos + myValue.length;
                        this.scrollTop = scrollTop;
                    } else {
                        this.value += myValue;
                        this.focus();
                    }
                });
            }
        });

        /**
         * Apply syntax highlighter
         */
        if( $('#wpMySQLQuery').length ) {
            aceEditor = ace.edit('wpMySQLQuery');
            aceEditor.getSession().setMode("ace/mode/sql");
            aceEditor.setTheme("ace/theme/idle_fingers");
        }
            if($('#wdtScrollable').is(':checked')){
                $('#wpFixedLayout').removeAttr('checked').change();
                $('tr.limit_table_layout').hide();
            }
        if($('#wpFixedLayout').is(':checked')){
            $('tr.column_width').show();
            $('tr.word_wrap_row').show();
        }
    });

})(jQuery);
