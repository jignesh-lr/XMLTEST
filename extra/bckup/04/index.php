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

function compareDbFields($jsonToArray){
    
    $customerDataTypes = array('regcard'=>0, 'dlist'=>0, 'size'=>1, 'status'=>'write', 'placeholder'=>'Please insert', 'unit'=>null, 'decimalplaces'=>0.01);

    $schemaTableCategory = $jsonToArray['category'];
    $schemaTableDatas = $jsonToArray['table'];

    // echo $schemaTableCategory;
    // $isApiMappingFileAvailable = isApiMappingFileAvailable($schemaTableCategory);
    
    $isApiMappingFileAvailable = true;

    if(!empty($isApiMappingFileAvailable)){

        echo "mapping file available."."\n";

        $mappingXmlFile = 'Hundestation.xml';

        $mappingFileArrays = mappingXMLRead($mappingXmlFile);
        $mappingFileCategory[] = $mappingFileArrays[0];
        $mappingFileAttribute = $mappingFileArrays[1];

        if(!empty($schemaTableDatas)){

            $table_name = array();
            $getDbSchemasData = array();
            //$isValidField = false;
            $data_type = array();

            foreach ($schemaTableDatas as $key => $schemaTableData) {
                
                $table_name[] = $schemaTableData['table_name'];
                //$getDbSchemasData = $schemaTableData['column_name'];

                if(strlen($schemaTableData['column_name']) >= 3){
                    $isValidField = true;

                }else{
                    echo "column name is to shrot".'<br>';
                    $isValidField = false;
                    return 400;
                }
                $column_name = $schemaTableData['column_name'];
                $getDbSchemasData[] = $schemaTableData['column_name'];
                $data_type[$column_name]['data_type'] = $schemaTableData['data_type'];
                if(isset($schemaTableData['regcard'])){
                    //$data_type[$column_name]['regcard'] = $schemaTableData['regcard'] ? $schemaTableData['regcard'] : 0;
                    $data_type[$column_name]['regcard'] = $schemaTableData['regcard'];    
                }
                if(isset($schemaTableData['dlist'])){
                    //$data_type[$column_name]['dlist'] = $schemaTableData['dlist'] ? $schemaTableData['dlist'] : 0;
                    $data_type[$column_name]['dlist'] = $schemaTableData['dlist'];    
                }
                if(isset($schemaTableData['regcard'])){                   
                    $data_type[$column_name]['regcard'] = $schemaTableData['regcard'];    
                }
                if(isset($schemaTableData['regcard'])){                   
                    $data_type[$column_name]['regcard'] = $schemaTableData['regcard'];    
                }
                if(isset($schemaTableData['size'])){                   
                    $data_type[$column_name]['size'] = $schemaTableData['size'];    
                }
                if(isset($schemaTableData['status'])){                   
                    $data_type[$column_name]['status'] = $schemaTableData['status'];    
                }
                if(isset($schemaTableData['placeholder'])){                   
                    $data_type[$column_name]['placeholder'] = $schemaTableData['placeholder'];    
                }
                if(isset($schemaTableData['unit'])){                   
                    $data_type[$column_name]['unit'] = $schemaTableData['unit'];    
                }
                if(isset($schemaTableData['decimalplaces'])){                   
                    $data_type[$column_name]['decimalplaces'] = $schemaTableData['decimalplaces'];    
                }


                
            }
            //return $isValidField;
        }
        $matchTableName = array_diff($table_name,$mappingFileCategory);

        

        if(empty($matchTableName)){

            $addValue = array_diff($getDbSchemasData,$mappingFileAttribute);
            $deleteValue = array_diff($mappingFileAttribute,$getDbSchemasData);
            // echo '<pre>';
            // print_r($addValue);
            // echo '</pre>';

            foreach ($addValue as $key => $value) {
                if (array_key_exists($value,$data_type))
                {

                    // echo '<pre>';
                    // print_r($data_type[$value]);
                    // echo '</pre>';

                    $diffKey = array_diff_key($customerDataTypes, $data_type[$value]);

                    $defaultFields = array_merge($data_type[$value], $diffKey);
                    echo '<pre>';
                    print_r($defaultFields);
                    echo '</pre>';
                    // foreach ($data_type[$value] as $k => $val) {
                    //     echo "<b>".$k."</b> : <i>".$val."</i><br>"; 

                    //     if(array_key_exists($k, $customerDataTypes)){
                    //         echo $k.'<br>';
                    //     }else{
                    //         echo "-------".'<br>';
                    //     }
                    //     // echo '<pre>';
                    //     // print_r($val);
                    //     // echo '</pre>';
                    // }

                   /* echo '<pre>+++++++';
                    print_r($customerDataTypes);
                    echo '</pre>';*/
                }
                else
                {
                    echo "Key does not exist!";
                }
            }
            //var_dump($isValidField);

            if( (!empty($addValue)) && (!empty($isValidField)) ){

                echo "start insert field".print_r($addValue)."\n";
                //addMappingField($addValue, $mappingXmlFile);    

            }else{

                echo "no any new field for insert in mapping file ."."\n";
                //$isValidField = false;
            }
            
            if(!empty($deleteValue)){

                echo "start deleting field".print_r($deleteValue)."\n";
                deleteMappingField($deleteValue, $mappingXmlFile);    

            }else{
                echo "no any field for delete in mapping file"."\n";
            }

            //echo "table matched"."\n";

        }else{
            echo "table not matched".print_r($mappingFileCategory)."\n";
        }

    }else{
        echo "Mapping file not available."."\n";
    }
    
    
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


