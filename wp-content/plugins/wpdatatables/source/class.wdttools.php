<?php

class WDTTools {
    
    public static $remote_path = 'http://wpdatatables.com/version-info.php';
    
    public static function defineDefaultValue( $possible, $index, $default = '' ){
        return isset($possible[$index]) ? $possible[$index] : $default;
    }
    
    public static function extractHeaders( $rawDataArr ){
        reset($rawDataArr);        
        if( !is_array( $rawDataArr[ key($rawDataArr) ] ) ){
            throw new WDTException('Please provide a valid 2-dimensional array.');
        }
        return array_keys( $rawDataArr[ key( $rawDataArr ) ] );
    }    
    
    public static function detectColumnDataTypes( $rawDataArr, $headerArr ){
        $autodetectData = array();
        $autodetectRowsCount = (10 > count( $rawDataArr )) ? count( $rawDataArr )-1 : 9;
        $wdtColumnTypes = array();
        for( $i = 0; $i <= $autodetectRowsCount; $i++ ){
            foreach($headerArr as $key) {
                $cur_val = current( $rawDataArr );
                if(!is_array($cur_val[$key])){
                    $autodetectData[$key][] = $cur_val[$key];
                }else{
                    if(array_key_exists('value',$cur_val[$key])){
                        $autodetectData[$key][] = $cur_val[$key]['value'];
                    }else{
                        throw new WDTException('Please provide a correct format for the cell.');
                    }
                }
            }
            next( $rawDataArr );
        }
        foreach( $headerArr as $key ){  $wdtColumnTypes[$key] = self::_wdtDetectColumnType( $autodetectData[$key] ); }
        return $wdtColumnTypes;
    }
    
    public static function convertXMLtoArr( $xml, $root = true ) {
	    if (!$xml->children()) {
		    return (string)$xml;
	    }

	    $array = array();
	    foreach ($xml->children() as $element => $node) {
		    $totalElement = count($xml->{$element});

		    if (!isset($array[$element])) {
			    $array[$element] = "";
		    }

		    // Has attributes
		    if ($attributes = $node->attributes()) {
			    $data = array(
				    'attributes' => array(),
				    'value' => (count($node) > 0) ? xmlToArray($node, false) : (string)$node
			    );

			    foreach ($attributes as $attr => $value) {
				    $data['attributes'][$attr] = (string)$value;
			    }

			    if ($totalElement > 1) {
				    $array[$element][] = $data;
			    } else {
				    $array[$element] = $data;
			    }

		    // Just a value
		    } else {
			    if ($totalElement > 1) {
				    $array[$element][] = self::convertXMLtoArr($node, false);
			    } else {
				    $array[$element] = self::convertXMLtoArr($node, false);
			    }
                    }
            }
    
            if ($root) {
                return array($xml->getName() => $array);
            } else {
                return $array;
            }
    }    
    
    public static function isArrayAssoc($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }    
    
    private static function _wdtDetectColumnType( $values ) {
        if ( self::_detect( $values, 'ctype_digit' ) ) { 
            return 'int'; 
        }
        if ( self::_detect( $values, 'is_numeric' ) ) { 
            return 'float'; 
        }
        if ( self::_detect( $values, 'strtotime' ) ) { return 'date'; }
        if ( self::_detect( $values, 'preg_match', WDT_EMAIL_REGEX ) ) { return 'email'; }
        if ( self::_detect( $values, 'preg_match', WDT_URL_REGEX ) ) { return 'link'; }
        return 'string';
    }
    
    private static function _detect( 
                $valuesArray, 
                $checkFunction, 
                $regularExpression = '' 
            ) {
        if( !is_callable( $checkFunction ) ){
            throw new WDTException( 'Please provide a valid type detection function for wpDataTables' ); 
        }
        $count = 0;
        for( $i=0; $i<count($valuesArray); $i++) {
            if( $regularExpression != '' ) {
                if( call_user_func( 
                        $checkFunction, 
                        $regularExpression, 
                        $valuesArray[$i]
                    ) 
                ) { 
                    $count++; 
                }
                else { return false; }
            } else {
                if( call_user_func( 
                        $checkFunction, 
                        $valuesArray[$i]
                        ) 
                    ) { 
                    $count++; 
                }
                else { return false; }
            }
        }
        if( $count == count( $valuesArray ) ) {
            return true;
        }
    }
    
    public static function checkRemoteVersion(){
        $request = wp_remote_post(self::$remote_path, array('body' => array('action' => 'version', 'purchase_code' => get_option('wdtPurchaseCode'))));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return $request['body'];
        }
        return false;        
    }
    
    public static function checkRemoteInfo(){
        $request = wp_remote_post(self::$remote_path, array('body' => array('action' => 'info', 'purchase_code' => get_option('wdtPurchaseCode'))));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return unserialize($request['body']);
        }
        return false;        
    }    
    
}

?>