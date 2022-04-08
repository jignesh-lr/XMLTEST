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

// function compareDbFields($jsonToArray){

//     $schemaTableCategory = $jsonToArray['category'];
//     $schemaTableDatas = $jsonToArray['table'];

//     if(!empty($schemaTableDatas)){

//         $getDbSchemasData = array();
//         foreach ($schemaTableDatas as $key => $schemaTableData) {
   
//             $table_name = $schemaTableData['table_name'];
//             $getDbSchemasData[] = $schemaTableData['column_name'];
//             $data_type = $schemaTableData['data_type'];

//         }

//     }else{

//         echo "table schema not available."."\n";
//     }
    
//     // echo $schemaTableCategory;
//     // $isApiMappingFileAvailable = isApiMappingFileAvailable($schemaTableCategory);
//     $isApiMappingFileAvailable = true;

//     if(!empty($isApiMappingFileAvailable)){

//         $mappingXmlFile = 'Hundestation.xml';
       
//         $mappingFileArrays = mappingXMLRead($mappingXmlFile);
//         $mappingFileCategory = $mappingFileArrays[0];
//         $mappingFileAttribute = $mappingFileArrays[1];

//         $compareField = array_diff($getDbSchemasData, $mappingFileAttribute);
//         $deleteValue = array_diff($mappingFileAttribute,$getDbSchemasData);

//         // echo "========== mappingFileAttribute ===============".'<br>';
//         // echo '<pre>';
//         // print_r($mappingFileAttribute);
//         // echo '</pre>';
//         // echo "========== End mappingFileAttribute ===============".'<br>';
//         // echo '<br>';

//         // echo "--------------- getDbSchemasData ----------".'<br>';
//         // echo '<pre>';
//         // print_r($getDbSchemasData);
//         // echo '</pre>';
//         // echo "--------------- getDbSchemasData ----------".'<br>';
//         // echo '<br>';

//         // echo "*************** addValue ***********".'<br>';
//         // echo '<pre>';
//         // print_r($compareField);
//         // echo '</pre>';
//         // echo "*************** addValue ***********".'<br>';
//         // echo '<br>';

//         // echo "/////////// deleteValue ////////// ".'<br>';
//         // echo '<pre>';
//         // print_r($deleteValue);
//         // echo '</pre>';
//         // echo "////////// deleteValue ////////// ".'<br>';
//         // echo '<br>';

//         //if(!empty($compareField)){

//             $addValue = array_diff($getDbSchemasData,$mappingFileAttribute);
//             $deleteValue = array_diff($mappingFileAttribute,$getDbSchemasData);

//             addMappingField($addValue, $mappingXmlFile);
//             //deleteMappingField($deleteValue, $mappingXmlFile);

//             return true;
//         //}else{
//             //echo "No any fields available for mapping".'<br>';
//             //return false;
//         //}

//     }else{

//         echo "mapping file not available."."\n";
//     }
// }

function addMappingField($addValue, $mappingXmlFile){

    $xml = new DOMDocument("1.0"); 
    $xml->formatOutput = true;

    

    $xml->preserveWhiteSpace = false;    
    $xml = simplexml_load_file($mappingXmlFile);
    $attributes = $xml->attributes;
    foreach ($addValue as $key => $value) {        

        if(!isset($attributes->$value)){
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
        
        //if(!isset($xml->attributes->$value)){
            unset($xml->attributes->$key);
        //}       
        //file_put_contents('Hundestation.xml', $xml->asXML());
        //return $key;
    }
    $xml->asXML($mappingXmlFile);

}

function compareDbFields($jsonToArray){
    
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

                $getDbSchemasData[] = $schemaTableData['column_name'];
                $data_type = $schemaTableData['data_type'];
            }
            //return $isValidField;
        }
        $matchTableName = array_diff($table_name,$mappingFileCategory);

        
        if(empty($matchTableName)){

            $addValue = array_diff($getDbSchemasData,$mappingFileAttribute);
            $deleteValue = array_diff($mappingFileAttribute,$getDbSchemasData);

            //var_dump($isValidField);

            if( (!empty($addValue)) && (!empty($isValidField)) ){

                echo "start insert field".print_r($addValue)."\n";
                addMappingField($addValue, $mappingXmlFile);    

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


