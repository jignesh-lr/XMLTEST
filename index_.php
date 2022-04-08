<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function mappingXMLRead($mappingXmlFile){

    if(!empty($mappingXmlFile)){

        $xmlFile = simplexml_load_file($mappingXmlFile,'SimpleXMLElement', LIBXML_NOWARNING);  
        $mappingTableNames = (array)$xmlFile->tableName;    
        $mappingTableName = $mappingTableNames[0];
        $mappingFileArray = (array)$xmlFile->attributes;
        return array($mappingTableName, $mappingFileArray);

    }else{
        echo "mapping file not available."."\n";
        return false;
    }
    

}

function addMappingField($addValue, $mappingXmlFile){

    $xml = new DOMDocument("1.0"); 
    $xml->formatOutput = true;

    

    $xml->preserveWhiteSpace = false;    
    $xml = simplexml_load_file($mappingXmlFile);
    $attributes = $xml->attributes;
    foreach ($addValue as $key => $value) {        

        if(!isset($attributes->$value) && ($value != 'primarykey')){
            $attributes->addChild($value,$value);
        }
        //return $value;
    }
    $xml->asXML($mappingXmlFile);
}

function deleteMappingField($deleteValue, $mappingXmlFile){
    
    $xml = new DOMDocument("1.0"); 
    $xml->formatOutput = true;
    $xml->preserveWhiteSpace = false;    
    $xml = simplexml_load_file($mappingXmlFile);

    foreach ($deleteValue as $key => $value) {
        //echo $key." : ".$value.'<br>';      
        if($value != 'primarykey'){
            unset($xml->attributes->$key);
        }
        //if(!isset($xml->attributes->$value)){
            
        //}       
        //file_put_contents('Hundestation.xml', $xml->asXML());
        //return $key;
    }
    $xml->asXML($mappingXmlFile);

}

// function compareDbFields($jsonToArray){
    
//     $customerDataTypes = array('chosenRegcard'=>0, 'dlist'=>0, 'size'=>1, 'selectStatus'=>'write', 'placeholder'=>'Please insert', 'selectUnit'=>null, 'checkbox'=>0, 'selectDecimalPlaces'=>0.01);

//     // $eineStadtDataTypes = array('chosenRegcard'=>0, 'dlist'=>0, 'size'=>1, 'selectStatus'=>'write', 'placeholder'=>'Please insert', 'selectUnit'=>null, 'checkbox'=>0, 'selectDecimalPlaces'=>0.01);
//     // //$defaultDataTypes = array('chosenRegcard'=>0, 'selectStatus'=>'write');

//     // $textDataTypes = array('dlist'=>0, 'placeholder'=>'Please insert','chosenRegcard'=>0, 'selectStatus'=>'write');
//     // $intDataTypes = array('checkbox'=>0, 'size'=>0,'chosenRegcard'=>0, 'selectStatus'=>'write');
//     // $numberDataTypes = array('selectUnit'=>null, 'selectDecimalPlaces'=>0.01,'chosenRegcard'=>0, 'selectStatus'=>'write');

//     $schemaTableCategory = $jsonToArray['category'];
//     $schemaTableDatas = $jsonToArray['table'];

//     // echo $schemaTableCategory;
//     // $isApiMappingFileAvailable = isApiMappingFileAvailable($schemaTableCategory);
    
//     $isApiMappingFileAvailable = true;

//     if(!empty($isApiMappingFileAvailable)){

//         echo "mapping file available."."\n";

//         $mappingXmlFile = 'Hundestation.xml';

//         $mappingFileArrays = mappingXMLRead($mappingXmlFile);
//         $mappingFileCategory[] = $mappingFileArrays[0];
//         $mappingFileAttribute = $mappingFileArrays[1];

//         if(!empty($schemaTableDatas)){

//             $table_name = array();
//             $getDbSchemasData = array();
//             //$isValidField = false;
//             $data_type = array();

//             foreach ($schemaTableDatas as $key => $schemaTableData) {
                
                

//                 $table_name[] = $schemaTableData['table_name'];
//                 //$getDbSchemasData = $schemaTableData['column_name'];

//                 if(strlen($schemaTableData['column_name']) >= 3){
//                     $isValidField = true;

//                 }else{
//                     echo "column name is to shrot".'<br>';
//                     $isValidField = false;
//                     return 400;
//                 }
//                 $column_name = $schemaTableData['column_name'];
//                 $getDbSchemasData[] = $schemaTableData['column_name'];
                
//                 $dataType = $schemaTableData['data_type'];
//                 if($dataType == 'interger'){
//                     $dataType = 'int';
//                 }

//                 $data_type[$column_name]['selectDatatype'] = $dataType;//$schemaTableData['data_type'];
//                 $data_type[$column_name]['selectTextareaSize'] = 1;
//                 $data_type[$column_name]['selectTitleSize'] = 4;
//                 if(isset($schemaTableData['regcard'])){                   
//                     $data_type[$column_name]['chosenRegcard'] = $schemaTableData['regcard'];    
//                 }   
//                 if(isset($schemaTableData['dlist'])){                   
//                     $data_type[$column_name]['dlist'] = $schemaTableData['dlist'];    
//                 }

//                 if(isset($schemaTableData['checkbox'])){                   
//                     $data_type[$column_name]['checkbox'] = $schemaTableData['checkbox'];    
//                 }
//                 if(isset($schemaTableData['size'])){                   
//                     $data_type[$column_name]['size'] = $schemaTableData['size'];    
//                 }

//                 if(isset($schemaTableData['status'])){                   
//                     $data_type[$column_name]['selectStatus'] = $schemaTableData['status'];    
//                 }

//                 if(isset($schemaTableData['placeholder'])){                   
//                     $data_type[$column_name] = array("placeholder" => $schemaTableData['placeholder']);    
//                 }
//                 if(isset($schemaTableData['unit'])){                   
//                     $data_type[$column_name]['selectUnit'] = $schemaTableData['unit'];    
//                 }
//                 if(isset($schemaTableData['decimalplaces'])){                   
//                     $data_type[$column_name]['selectDecimalPlaces'] = $schemaTableData['decimalplaces'];    
//                 }


//             }
//             //return $isValidField;
//         }
//         $matchTableName = array_diff($table_name,$mappingFileCategory);

        
//         // echo '<pre>';
//         // print_r($data_type);
//         // echo '</pre>';

//         if(empty($matchTableName)){

//             $addValue = array_diff($getDbSchemasData,$mappingFileAttribute);
//             $deleteValue = array_diff($mappingFileAttribute,$getDbSchemasData);

//             echo '<pre>';
//             print_r($addValue);
//             echo '</pre>';

//             echo '<pre>';
//             print_r($deleteValue);
//             echo '</pre>';

//             //var_dump($isValidField);
//             $defaultFields = array();
//             $fieldArray = array();

//             foreach ($addValue as $key => $value) {

//                 if (array_key_exists($value,$data_type))
//                 {

                    
//                     $diffKey = array_diff_key($customerDataTypes, $data_type[$value]);

//                     $fieldArray = array_merge($customerDataTypes,$data_type[$value]);

//                     $fieldArray['fieldName'] = $value;
//                     $fieldArray['label'] = $value;

//                     if(isset($fieldArray['selectDatatype'])){
//                         if($fieldArray['selectDatatype'] == 'varchar'){
//                             $fieldArray['selectDatatype'] = 'text';
//                         }    
//                     }
                    

//                     if($fieldArray['selectDatatype'] == 'varchar' && $fieldArray['dlist'] !='' ){
//                         $fieldArray['selectDatatype'] = 'dlist';
//                     }
//                     if($fieldArray['selectDatatype'] == 'samllint' || $fieldArray['selectDatatype'] == 'interger' || $fieldArray['selectDatatype'] == 'bigint' || $fieldArray['selectDatatype'] == 'bit' || $fieldArray['selectDatatype'] == 'numeric' || $fieldArray['selectDatatype'] == 'decimal' || $fieldArray['selectDatatype'] == 'double precision'){

//                         $fieldArray['selectDatatype'] = 'number';
//                     }

//                     if($fieldArray['selectDatatype'] == 'boolean' || $fieldArray['selectDatatype'] == 'tinyint' ){
//                         $fieldArray['selectDatatype'] = 'boolean';
//                     }

//                     if($fieldArray['selectDatatype'] == 'date' || $fieldArray['selectDatatype'] == 'time' || $fieldArray['selectDatatype'] == 'timestamp' ){
//                         $fieldArray['selectDatatype'] = 'date';
//                     }


//                     // echo '<pre>';
//                     // print_r($data_type[$value]);
//                     // echo '</pre>';

//                     // echo '<pre>';
//                     // print_r($customerDataTypes);
//                     // echo '</pre>';

//                     // echo '<pre>';
//                     // print_r($diffKey);
//                     // echo '</pre>';

//                     echo '<pre>';
//                     print_r($fieldArray);
//                     echo '</pre>';
                    
//                     // foreach ($data_type[$value] as $k => $val) {
//                     //     echo "<b>".$k."</b> : <i>".$val."</i><br>"; 

//                     //     if(array_key_exists($k, $customerDataTypes)){
//                     //         echo $k.'<br>';
//                     //     }else{
//                     //         echo "-------".'<br>';
//                     //     }
//                     //     // echo '<pre>';
//                     //     // print_r($val);
//                     //     // echo '</pre>';
//                     // }

//                    /* echo '<pre>+++++++';
//                     print_r($customerDataTypes);
//                     echo '</pre>';*/
//                 }
//                 else
//                 {
//                     $fieldArray = array_unique($customerDataTypes);
                   
//                 }
                
                
                
//             }   

//             foreach ($deleteValue as $key => $deleteVal) {
//                 // code...
//             }
//             // echo '<pre>';
//             // print_r($fieldArray);
//             // echo '</pre>';

//             if( (!empty($addValue)) && (!empty($isValidField)) ){

//                 echo "start insert field".print_r($addValue)."\n";
//                 //addMappingField($addValue, $mappingXmlFile);    

//             }else{

//                 echo "no any new field for insert in mapping file ."."\n";
//                 //$isValidField = false;
//             }
            
//             if(!empty($deleteValue)){

//                 echo "start deleting field".print_r($deleteValue)."\n";
//                 //deleteMappingField($deleteValue, $mappingXmlFile);    

//             }else{
//                 echo "no any field for delete in mapping file"."\n";
//             }

//             //echo "table matched"."\n";

//         }else{
//             echo "table not matched".print_r($mappingFileCategory)."\n";
//         }

//     }else{
//         echo "Mapping file not available."."\n";
//     }
    
    
// }

function compareDbFields($jsonToArray){
    
    $customerDataTypes = array('chosenRegcard'=>0, 'dlist'=>0, 'size'=>1, 'selectStatus'=>'write', 'placeholder'=>'Please insert', 'selectUnit'=>null, 'checkbox'=>0, 'selectDecimalPlaces'=>0.01);

    // $eineStadtDataTypes = array('chosenRegcard'=>0, 'dlist'=>0, 'size'=>1, 'status'=>'write', 'placeholder'=>'Please choose', 'unit'=>null, 'checkbox'=>0, 'decimalplaces'=>0.01);
    // //$defaultDataTypes = array('chosenRegcard'=>0, 'selectStatus'=>'write');

    // $textDataTypes = array('dlist'=>0, 'placeholder'=>'Please insert','chosenRegcard'=>0, 'selectStatus'=>'write');
    // $intDataTypes = array('checkbox'=>0, 'size'=>0,'chosenRegcard'=>0, 'selectStatus'=>'write');
    // $numberDataTypes = array('unit'=>null, 'decimalplaces'=>0.01,'chosenRegcard'=>0, 'selectStatus'=>'write');

    $schemaTableCategory = $jsonToArray['category'];
    $schemaTableDatas = $jsonToArray['table'];

    
    $isApiMappingFileAvailable = true;//isApiMappingFileAvailable($schemaTableCategory);
    //echo $isApiMappingFileAvailable.'<br>';
    //$isApiMappingFileAvailable = true;
    $responseData = array();

    if(!empty($isApiMappingFileAvailable)){

        //writelog("mapping file available.");
        
        $mappingXmlFile = 'Hundestation.xml';//$isApiMappingFileAvailable;

        $mappingFileArrays = mappingXMLRead($mappingXmlFile);
        $mappingFileCategory[] = $mappingFileArrays[0];
        $mappingFileAttribute = $mappingFileArrays[1];

        
        if(!empty($schemaTableDatas)){

            $table_name = array();
            $getDbSchemasData = array();
            //$isValidField = false;

            foreach ($schemaTableDatas as $key => $schemaTableData) {
                
                $table_name[] = $schemaTableData['table_name'];
                //$getDbSchemasData = $schemaTableData['column_name'];

                if(strlen($schemaTableData['column_name']) >= 3){
                    $isValidField = true;

                }else{
                    echo "column name is to shrot";
                    $isValidField = false;
                    //$responseData['message'] = "column name is to shrot";
                    return 400;
                }

                $column_name = $schemaTableData['column_name'];
                $getDbSchemasData[] = $schemaTableData['column_name'];
                //$data_type = $schemaTableData['data_type'];
                $data_type[$column_name]['selectDatatype'] = $schemaTableData['data_type'];
                // $data_type[$column_name]['selectTextareaSize'] = 1;
                // $data_type[$column_name]['selectTitleSize'] = 4;
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
        echo '<pre>';
        print_r($table_name);
        echo '</pre>';
        echo '<pre>';
        print_r($mappingFileCategory);
        echo '</pre>';
        
        if(empty($matchTableName)){

            $addValue = array_diff($getDbSchemasData,$mappingFileAttribute);
            $deleteValue = array_diff($mappingFileAttribute,$getDbSchemasData);

            echo '<pre>';
            print_r($addValue);
            echo '</pre>';

            echo '<pre>';
            print_r($deleteValue);
            echo '</pre>';

            echo "isValidField : --- ".$isValidField.'<br>';
            
            // echo '<pre>';
            // print_r($data_type);
            // echo '</pre>';

            //var_dump($deleteValue);

            if( (!empty($isValidField)) && ( (!empty($addValue)) || (!empty($deleteValue)) ) ){

                //echo "start insert field".print_r($addValue)."\n";
                addMappingField($addValue, $mappingXmlFile);   
                deleteMappingField($deleteValue, $mappingXmlFile);  

                $fieldArray = array();

                foreach ($addValue as $key => $addVal) {
                    
                    if (array_key_exists($addVal,$data_type))
                    {

                        

                        //$diffKey = array_diff_key($customerDataTypes, $data_type[$value]);
                        //$fieldArray = array_unique(array_merge($data_type[$value], $diffKey));

                        $fieldArray = array_merge($customerDataTypes,$data_type[$addVal]);

                        $fieldArray['fieldName'] = $addVal;
                        $fieldArray['label'] = $addVal;

                        if($fieldArray['selectDatatype'] == 'varchar' && $fieldArray['dlist'] !='' ){
                            $fieldArray['selectDatatype'] = 'dlist';
                            $fieldArray['placeholder'] = 'Please select';
                            
                        }
                        if($fieldArray['selectDatatype'] == 'varchar'){
                            $fieldArray['selectDatatype'] = 'text';
                        }
                        if($fieldArray['selectDatatype'] == 'samllint' || $fieldArray['selectDatatype'] == 'interger' || $fieldArray['selectDatatype'] == 'bigint' || $fieldArray['selectDatatype'] == 'bit' || $fieldArray['selectDatatype'] == 'numeric' || $fieldArray['selectDatatype'] == 'decimal' || $fieldArray['selectDatatype'] == 'double precision'){

                            $fieldArray['selectDatatype'] = 'number';
                        }

                        if($fieldArray['selectDatatype'] == 'boolean' || $fieldArray['selectDatatype'] == 'tinyint' ){
                            $fieldArray['selectDatatype'] = 'boolean';
                        }

                        if($fieldArray['selectDatatype'] == 'date' || $fieldArray['selectDatatype'] == 'time' || $fieldArray['selectDatatype'] == 'timestamp' ){
                            $fieldArray['selectDatatype'] = 'date';
                        }
                        $fieldArray['selectTextareaSize'] = 1;
                        $fieldArray['selectTitleSize'] = 4;
                        $createField = 85500;
                        //$createField = createField($createFieldArray,$schemaTableCategory);
                        //addMappingField($addVal, $mappingXmlFile);   
                    }
                    
                }

                

                $chosenRegcard = 0;
                foreach ($deleteValue as $kkk => $deleteFieldsArray) {
                    //deleteMappingField($deleteFieldsArray, $mappingXmlFile);   
                    $deleteField = 100;//deleteField($chosenRegcard, $deleteFieldsArray, $schemaTableCategory);
                }
                echo $createField.$deleteField.'<br>++';
                if( (!empty($createField)) || (!empty($deleteField)) ){
                    
                    return 200;
                }else{

                    return 400;
                }


                // if(!empty($deleteField)){
                //     return 200;
                // }else{
                //     return 400;
                // }

                //return 200;
            }else{

                //writelog("no any new field for insert in mapping file .");
                //$responseData['message'] = "no any new field for insert in mapping file ."; 
                return 401;
                //$isValidField = false;
            }

        }else{
            //writelog("table not matched");            
            echo "table not matched";
            //$responseData['message'] = "table not matched"; 
            return 400;
            
        }

    }else{
        //writelog("Mapping file not available.");        
        //$responseData['message'] = "Mapping file not available."; 
        return 400;
        
    }
    
    //return $responseData;
}

    

$jsonFile = 'dbSchema.json';
$jsonContent = file_get_contents($jsonFile);
$jsonToArray = json_decode($jsonContent, true);

$compareDbFields = compareDbFields($jsonToArray);

//var_dump($isValidColum).'<br>';

if(!empty($compareDbFields)){
    echo $compareDbFields."\n";
    // $mappingFields = compareDbFields($jsonToArray);
    // echo '<pre>';
    // print_r($mappingFields);
    // echo '</pre>';
    //echo $isValidColum;
}else{
    echo $compareDbFields   ."\n";
    //echo $isValidColum;
}   


?>


