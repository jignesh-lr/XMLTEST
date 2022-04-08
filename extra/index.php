<?php 

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$isApiMappingFileAvailable = true;
$mappingXmlFile = 'Hundestation.xml';

function isApiMappingFileAvailable($category){

    $customerId = $_SESSION['customer_id'];

    $adminXmlPath = 'Hundestation.xml';//'C:\\Inetpub\\vhosts\\spatialcontrol.de\\httpdocs\\'.$customerId.'\\'."\userdata\admin.xml";
    $adminfields = simplexml_load_file($adminXmlPath);
    //$apiMappingFile = false;
    $mappingAvailable = false;
    if (file_exists($adminXmlPath)){
        $adminfields = simplexml_load_file($adminXmlPath);
        $apiMappingFile = (string)$adminfields->$category->apiMappingFile;

    }
    else {
        //writelog("Can't open xml: ".$adminXmlPath);
        //$apiMappingFile = false;
    }

    if(!empty($apiMappingFile)){
        $mappingAvailable = $apiMappingFile;//true;
        //deleteMappingField();
    }else{
        $mappingAvailable = false;
        //addMappingField();
    }
    return $mappingAvailable;
}

function mappingXMLRead($mappingXmlFile){

    if(!empty($mappingXmlFile)){

        $xmlFile = simplexml_load_file($mappingXmlFile,'SimpleXMLElement', LIBXML_NOWARNING);  
        $mappingTableNames = (array)$xmlFile->tableName;    
        $mappingTableName = $mappingTableNames[0];
        $mappingFileArray = (array)$xmlFile->attributes;
        return array($mappingTableName, $mappingFileArray);

    }else{
        //writelog("mapping file not available.");
        return false;
    }
    

}

function addMappingField($addMappingField, $addValue, $mappingXmlFile){

    $xml = new DOMDocument("1.0"); 
    $xml->formatOutput = true;

    

    $xml->preserveWhiteSpace = false;    
    $xml = simplexml_load_file($mappingXmlFile);
    $attributes = $xml->attributes;
    $isValid = '';
    //foreach ($addValue as $key => $value) {         
        //echo $key.":".$value.'<br>+++';
        //$val = iconv('UTF-8', 'ISO-8859-1', $value);
        //$val = utf8_encode($addValue);


        if(!isset($attributes->$addValue) && ($addValue != 'primarykey')){
            //echo $value.'<br>++';
            // $escapedString = $val;
            // $replacedSpecialChars = replaceSpecialChars($escapedString);
            // $removedSpecialChars = preg_replace("/[^a-zA-Z0-9_]/", "", $replacedSpecialChars);
            // $dbFieldName = strtolower (substr($removedSpecialChars, 0, 64));

            $attributes->addChild($addMappingField,$addValue);
            $isValid = 1;
        }else{
            //echo "else";
            //return 400;
            $isValid = 0;
        }
        //return $val;
    //}
    $xml->asXML($mappingXmlFile);
    return $isValid;
}

function deleteMappingField($deleteKey, $mappingXmlFile){
    
    $xml = new DOMDocument("1.0"); 
    $xml->formatOutput = true;
    $xml->preserveWhiteSpace = false;    
    $xml = simplexml_load_file($mappingXmlFile);

    //foreach ($deleteValue as $key => $value) {
        //echo "deleteKey".$deleteKey." : ".$deleteKey.'<br>';      

        //if(!isset($xml->attributes->$value)){
        if($deleteKey != 'primarykey'){
            unset($xml->attributes->$deleteKey);
        }
        //}       
        //file_put_contents('Hundestation.xml', $xml->asXML());
        //return $key;
    //}
    $xml->asXML($mappingXmlFile);

}

function compareDbFields($jsonToArray){
    
    $customerDataTypes = array('chosenRegcard'=>0, 'dlist'=>0, 'size'=>1, 'selectStatus'=>'write', 'placeholder'=>'Please insert', 'selectUnit'=>null, 'checkbox'=>0, 'selectDecimalPlaces'=>0.01);

    // $eineStadtDataTypes = array('chosenRegcard'=>0, 'dlist'=>0, 'size'=>1, 'status'=>'write', 'placeholder'=>'Please choose', 'unit'=>null, 'checkbox'=>0, 'decimalplaces'=>0.01);
    // //$defaultDataTypes = array('chosenRegcard'=>0, 'selectStatus'=>'write');

    // $textDataTypes = array('dlist'=>0, 'placeholder'=>'Please insert','chosenRegcard'=>0, 'selectStatus'=>'write');
    // $intDataTypes = array('checkbox'=>0, 'size'=>0,'chosenRegcard'=>0, 'selectStatus'=>'write');
    // $numberDataTypes = array('unit'=>null, 'decimalplaces'=>0.01,'chosenRegcard'=>0, 'selectStatus'=>'write');

    $schemaTableCategory = $jsonToArray['category'];
    $schemaTableDatas = $jsonToArray['table'];

    
    $isApiMappingFileAvailable = isApiMappingFileAvailable($schemaTableCategory);
    //echo $isApiMappingFileAvailable.'<br>';
    //$isApiMappingFileAvailable = true;
    $responseData = array();

    if(!empty($isApiMappingFileAvailable)){

        //writelog("mapping file available.");
        
        $mappingXmlFile = $isApiMappingFileAvailable;

        $mappingFileArrays = mappingXMLRead($mappingXmlFile);
        $mappingFileCategory[] = $mappingFileArrays[0];
        $mappingFileAttribute = $mappingFileArrays[1];

        // echo '<pre>';
        // print_r($mappingFileArrays);
        // echo '</pre>';

        if(!empty($schemaTableDatas)){

            $table_name = array();
            $getDbSchemasData = array();
            $isValidField = '';
            $isValidType = '';

            foreach ($schemaTableDatas as $key => $schemaTableData) {
                
                $table_name[] = $schemaTableData['table_name'];
                //$getDbSchemasData = $schemaTableData['column_name'];
                // echo '<pre>';
                // print_r($schemaTableData);
                // echo '</pre>';
                //echo strlen($schemaTableData['column_name']).'<br>++';
                if($schemaTableData['column_name'] != 'primarykey'){
                    $isValidKey = true;

                }else{

                    $isValidKey = false;
                    //writelog("Can't create or update primarykey field");                    
                    $responseData['message'] = "Can't create or update primarykey field";
                    $responseData['column_name'] = $schemaTableData['column_name'];
                    $responseData['status'] = 400; 
                    return $responseData;
                    //return 400;
                }

                if(strlen($schemaTableData['column_name'])>= 3) {
                    $isValidField = true;

                }else{

                    $isValidField = false;
                    //writelog("column name is to shrot");                    
                    $responseData['message'] = "Didn't delete or create field because of column_name to short";
                    $responseData['column_name'] = $schemaTableData['column_name'];
                    $responseData['status'] = 400; 
                    return $responseData;
                    //return 400;
                }

                if($schemaTableData['data_type'] == 'varchar' || $schemaTableData['data_type'] == 'integer'|| $schemaTableData['data_type'] == 'smallint'|| $schemaTableData['data_type'] == 'bigint'|| $schemaTableData['data_type'] == 'bit'|| $schemaTableData['data_type'] == 'numeric'|| $schemaTableData['data_type'] == 'decimal'|| $schemaTableData['data_type'] == 'double precision'|| $schemaTableData['data_type'] == 'boolean'|| $schemaTableData['data_type'] == 'tinyint'|| $schemaTableData['data_type'] == 'date'|| $schemaTableData['data_type'] == 'datetime'|| $schemaTableData['data_type'] == 'time'|| $schemaTableData['data_type'] == 'timestamp' || $schemaTableData['data_type'] == 'character varying'){
                    $isValidType = true;                    

                }else{

                    $isValidType = false;
                    //writelog("Unknown datatype");                    
                    $responseData['message'] = "Unknown datatype";
                    $responseData['column_name'] = $schemaTableData['column_name'];
                    $responseData['status'] = 400; 
                    return $responseData;
                    //return 400;
                }

                $column_name = $schemaTableData['column_name'];
                $getDbSchemasData[] = $schemaTableData['column_name'];
                //$data_type = $schemaTableData['data_type'];
                $data_type[$column_name]['selectDatatype'] = $schemaTableData['data_type'];
                
                if(isset($schemaTableData['regcard'])){                   
                    $data_type[$column_name]['chosenRegcard'] = $schemaTableData['regcard'];    
                }
                if(isset($schemaTableData['dlist'])){                   
                    $data_type[$column_name]['dlist'] = $schemaTableData['dlist'];    
                }

                if(isset($schemaTableData['checkbox'])){                   
                    $data_type[$column_name]['checkbox'] = $schemaTableData['checkbox'];    
                }
                if(isset($schemaTableData['size'])){                   
                    $data_type[$column_name]['size'] = $schemaTableData['size'];    
                }

                if(isset($schemaTableData['status'])){                   
                    $data_type[$column_name]['selectStatus'] = $schemaTableData['status'];    
                }

                if(isset($schemaTableData['placeholder'])){                   
                    $data_type[$column_name] = array("placeholder" => $schemaTableData['placeholder']);    
                }
                if(isset($schemaTableData['unit'])){                   
                    $data_type[$column_name]['selectUnit'] = $schemaTableData['unit'];    
                }
                if(isset($schemaTableData['decimalplaces'])){                   
                    $data_type[$column_name]['selectDecimalPlaces'] = $schemaTableData['decimalplaces'];    
                }
            }
            //return $isValidField;
        }
        $matchTableName = array_diff($table_name,$mappingFileCategory);

        // echo '<pre>';
        // print_r($data_type);
        // echo '</pre>';

        // echo '<pre>';
        // print_r($matchTableName);
        // echo '</pre>';
        if(empty($matchTableName)){

            $addValue = array_diff($getDbSchemasData,$mappingFileAttribute);
            $deleteValue = array_diff($mappingFileAttribute,$getDbSchemasData);
            // echo "addValue".'<br>';
            // echo '<pre>';
            // print_r($addValue);
            // echo '</pre>';
            // echo "deleteValue".'<br>';
            // echo '<pre>';
            // print_r($deleteValue);
            // echo '</pre>';
            // echo "isValidField" .$isValidField.'<br>';
            // echo "isValidType" .$isValidType.'<br>';
            //exit;

            if( ($isValidField) && ($isValidType) ) {

                //echo "start insert field".print_r($addValue)."\n";
                //$addMapp = addMappingField($addValue, $mappingXmlFile);   
                
                // echo "addValue".'<br>';
                // echo '<pre>';
                // print_r($addValue);
                // echo '</pre>';
                // echo "addMapp".'<br>';
                // echo '<pre>';
                // print_r($addMapp);
                // echo '</pre>';
                $fieldArray = array();
                //if($addMapp == 1){    
                if(!empty($addValue)){                    

                    foreach ($addValue as $key => $addVal) {
                        //echo "addVal : " .$addVal.'<br>****';
                        if (array_key_exists($addVal, $data_type))
                        {
                            //$diffKey = array_diff_key($customerDataTypes, $data_type[$value]);
                            //$fieldArray = array_unique(array_merge($data_type[$value], $diffKey));

                            $fieldArray = array_merge($customerDataTypes,$data_type[$addVal]);

                            $val = iconv('UTF-8', 'ISO-8859-1', $addVal);
                            //$val = utf8_encode($addVal);

                            $fieldArray['fieldName'] = $addVal;
                            $fieldArray['label'] = $val;

                            if($fieldArray['selectDatatype'] == 'varchar' && $fieldArray['dlist'] !='' ){
                                $fieldArray['selectDatatype'] = 'dlist';
                                $fieldArray['placeholder'] = 'Please select';
                                
                            }
                            if($fieldArray['selectDatatype'] == 'varchar' || $fieldArray['selectDatatype'] == 'character varying' || $fieldArray['selectDatatype'] == 'double precision'){
                                $fieldArray['selectDatatype'] = 'text';
                            }
                            if($fieldArray['selectDatatype'] == 'smallint' || $fieldArray['selectDatatype'] == 'integer' || $fieldArray['selectDatatype'] == 'bigint' || $fieldArray['selectDatatype'] == 'bit' || $fieldArray['selectDatatype'] == 'numeric' || $fieldArray['selectDatatype'] == 'decimal' || $fieldArray['selectDatatype'] == 'double precision'){

                                $fieldArray['selectDatatype'] = 'number';
                            }

                            if($fieldArray['selectDatatype'] == 'boolean' || $fieldArray['selectDatatype'] == 'tinyint' ){
                                $fieldArray['selectDatatype'] = 'boolean';
                            }

                            if($fieldArray['selectDatatype'] == 'date' || $fieldArray['selectDatatype'] == 'datetime' || $fieldArray['selectDatatype'] == 'time' || $fieldArray['selectDatatype'] == 'timestamp' ){
                                $fieldArray['selectDatatype'] = 'date';
                            }

                            $fieldArray['selectTextareaSize'] = 1;
                            $fieldArray['selectTitleSize'] = 4;
                            //$createField = 85500;
                            // echo "fieldArray".'<br>';
                            // echo '<pre>';
                            // print_r($fieldArray);
                            // echo '</pre>';
                            // echo "schemaTableCategory".'<br>';
                            // echo '<pre>';
                            // print_r($schemaTableCategory);
                            // echo '</pre>';

                            $createField = createField($fieldArray,$schemaTableCategory);
                            // echo "createField".'<br>';
                            // echo '<pre>';
                            // print_r($createField);
                            // echo '</pre>';
                            // foreach ($createField as $k => $v) {
                            //     echo $k.":".$v.'<br>';
                            //     // echo '<pre>';
                            //     // print_r($v);
                            //     // echo '</pre>';
                            // }

                            if($fieldArray['selectDatatype'] == "title"){
                                $isTitle = true;
                                $dbFieldName = iconv('ISO-8859-1', 'UTF-8', $fieldArray['label']);
                            }
                            else{
                                $isTitle = false;
                                $escapedString = $fieldArray['label'];
                                $replacedSpecialChars = replaceSpecialChars($escapedString);
                                $removedSpecialChars = preg_replace("/[^a-zA-Z0-9_]/", "", $replacedSpecialChars);
                                $dbFieldName = strtolower (substr($removedSpecialChars, 0, 64));
                            }
                            // echo $dbFieldName.'<br>8888888888';
                            
                            // echo "fieldArray".'<br>';
                            // echo '<pre>';
                            // print_r($fieldArray);
                            // echo '</pre>';
                            //if(!empty($createField)){
                                addMappingField($dbFieldName, $addVal, $mappingXmlFile);       
                            //}else{
                            //     $responseData['message'] = "Unknown datatype"; 
                            //     $responseData['fieldname'] = $addVal;                             
                            //     $responseData['status'] = 400; 
                            //     return $responseData;    
                            // }
                            
                        }else{
                            //writelog("could not add ($addVal) columns in EineStadt database");
                            $responseData['message'] = "Unknown datatype"; 
                            $responseData['fieldname'] = $addVal; 
                            
                            $responseData['status'] = 400; 
                            return $responseData;
                        }
                        
                    }
                }


                if(!empty($deleteValue)){

                    $chosenRegcard = 0;
                    foreach ($deleteValue as $deleteKey => $deleteFieldsArray) {
                        //echo $deleteFieldsArray.'<br>';
                        //deleteMappingField($deleteFieldsArray, $mappingXmlFile);  
                        $deleteFields = strtolower($deleteFieldsArray);
                        $deleteField = deleteField($chosenRegcard, $deleteKey, $schemaTableCategory);
                        //if(!empty($deleteField)){
                            deleteMappingField($deleteKey, $mappingXmlFile);      
                        //}
                        
                        // echo '<pre>';
                        // print_r($deleteField);
                        // echo '</pre>';
                    }
                }
                //}

                
                
                // echo "createField".'<br>';
                // echo '<pre>';
                // print_r($createField);
                // echo '</pre>';
                // echo "deleteField".'<br>';
                // echo '<pre>';
                // print_r($deleteField);
                // echo '</pre>';
                // echo $createField.$deleteField.'<br>++';
                if( (!empty($createField)) || (!empty($deleteField)) ){

                    $responseData['message'] = "Deleted or created fields successfully"; 
                    $responseData['status'] = 200; 
                    return $responseData;
                }else{

                    //writelog("No changes to existing database in request message");
                    $responseData['message'] = "No changes when comparing request with existing database"; 
                    $responseData['status'] = 200; 
                    return $responseData;
                    
                }
            }else{

                $responseData['message'] = "Didn't delete or create field because of bad body "; 
                $responseData['status'] = 400; 
                return $responseData;
                
                //$isValidField = false;
            }
            

        }else{
            //writelog("table not matched");            
            $responseData['message'] = "Didn't delete or create field because of bad body"; 
            $responseData['status'] = 400; 
            return $responseData;
            //return 400;
            
        }

    }else{

        //writelog("Mapping file not available.");                
        $responseData['message'] = "Internal server error"; 
        $responseData['status'] = 500; 
        return $responseData;
        //return 500;
        
    }
    
    //return $responseData;
}

$jsonFile = 'dbSchema.json';
$jsonContent = file_get_contents($jsonFile);
$jsonToArray = json_decode($jsonContent, true);

$compareDbFields = compareDbFields($jsonToArray);

//var_dump($isValidColum).'<br>';

if(!empty($compareDbFields)){
    //echo $compareDbFields."\n";
    // $mappingFields = compareDbFields($jsonToArray);
    // echo '<pre>';
    // print_r($mappingFields);
    // echo '</pre>';
    //echo $isValidColum;
}else{
    //echo $compareDbFields   ."\n";
    //echo $isValidColum;
}
?>


