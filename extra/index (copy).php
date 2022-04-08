<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$isApiMappingFileAvailable = true;
$dbschema = true;

function mappingXMLRead($mappingXmlFile){

    $xmlFile = simplexml_load_file($mappingXmlFile,'SimpleXMLElement', LIBXML_NOWARNING);  
    $mappingTableNames = (array)$xmlFile->tableName;    
    $mappingTableName = $mappingTableNames[0];
    $mappingFileArray = (array)$xmlFile->attributes;

    return array($mappingTableName, $mappingFileArray);

}

function compareDbFields($getDbSchemasData){

    $mappingXmlFile = 'Hundestation.xml';
    $mappingFileArrays = mappingXMLRead($mappingXmlFile);
    $mappingFileCategory = $mappingFileArrays[0];
    $mappingFileAttribute = $mappingFileArrays[1];

    $compareField = array_diff($getDbSchemasData,$mappingFileAttribute);
    // echo '<pre>';
    // print_r($compareField);
    // echo '<pre>';
    if(!empty($compareField)){

        $addValue = array_diff($getDbSchemasData,$mappingFileAttribute);
        $deleteValue = array_diff($mappingFileAttribute,$getDbSchemasData);

        addMappingField($addValue);
        deleteMappingField($deleteValue);

        return true;
    }else{
        echo "No any fields available for mapping".'<br>';
        return false;
    }
}

/*function getAddMappingField($getDbSchemasData,$mappingFileAttribute){
    //return 'addMappingField';
    $addValue = array_diff($getDbSchemasData,$mappingFileAttribute);

    return $addValue;
}

function getDeleteMappingField($getDbSchemasData,$mappingFileAttribute){

	$deleteValue = array_diff($mappingFileAttribute,$getDbSchemasData);
	return $deleteValue;
    //return 'deleteMappingField';   
}*/

function addMappingField($addValue){

    foreach ($addValue as $key => $value) {
        $xml = new DOMDocument("1.0", "utf-8"); 
        $xml = simplexml_load_file('Hundestation.xml');

        if(!isset($xml->attributes->$value)){
            $xml->attributes->addChild($value,$value);
        }
        
        file_put_contents('Hundestation.xml', $xml->asXML());
        return $value;
    }

}

function deleteMappingField($deleteValue){
    
    foreach ($deleteValue as $key => $value) {
        //echo $key." : ".$value.'<br>';      
        $xml = new DOMDocument("1.0", "utf-8"); 
        $xml = simplexml_load_file('Hundestation.xml');
        unset($xml->attributes->$key);
       
        file_put_contents('Hundestation.xml', $xml->asXML());

        return $key;
    }

}

if($isApiMappingFileAvailable == 1 || $isApiMappingFileAvailable == true){

    $mappingXmlFile = 'Hundestation.xml';
    $mappingFileArrays = mappingXMLRead($mappingXmlFile);
    $mappingFileCategory = $mappingFileArrays[0];
    $mappingFileAttribute = $mappingFileArrays[1];

    $jsonFile = 'dbSchema.json';
    $jsonContent = file_get_contents($jsonFile);
    $jsonToArray = json_decode($jsonContent, true);

    $schemaTableCategory = $jsonToArray['category'];
    $schemaTableDatas = $jsonToArray['table'];

    //$isApiMappingFileAvailable = isApiMappingFileAvailable($schemaTableCategory);

    if(!empty($schemaTableDatas)){
        $getDbSchemasData = array();
        foreach ($schemaTableDatas as $key => $schemaTableData) {
            
            
            $table_name = $schemaTableData['table_name'];
            $getDbSchemasData[] = $schemaTableData['column_name'];
            $data_type = $schemaTableData['data_type'];
            //echo $column_name ." : ".$data_type.'<br>';
            //$getDbSchemasData = $column_name ; //array("nfc_id","position","note","sampleKey","sampleKey2");
            
        }
    }else{

        echo "Table schema not available."."\n";
    }
    // echo '<pre>';
    // print_r($getDbSchemasData);
    // echo '</pre>';
    $compareFields = compareDbFields($getDbSchemasData);
    echo $compareFields;

    /*if((!empty($getDbSchemasData) && (!empty($mappingFileAttribute)))){
        $addValue = getAddMappingField($getDbSchemasData,$mappingFileAttribute);    
        echo "add field in mapping.xml".'<br>';
        echo '<pre>';
        print_r($addValue);
        echo '</pre>';
    }else{
        //echo 'There are something error in table schema or mapping file'."\n";
    }
    

    if(!empty($addValue)){
        addMappingField($addValue);
    }else{
       //echo "addMappingField No any changes"."<br>"; 
    }

    if((!empty($getDbSchemasData) && (!empty($mappingFileAttribute)))){
        $deleteValue = getDeleteMappingField($getDbSchemasData, $mappingFileAttribute);
        echo "Delete field in mapping.xml".'<br>';
        echo '<pre>';
        print_r($deleteValue);
        echo '</pre>';
    }else{
        //echo 'There are something error in table schema or mapping file'."\n";
    }

    

    if(!empty($deleteValue)){
        deleteMappingField($deleteValue);
    }else{
       //echo "deleteMappingField No any changes"."<br>"; 
    }*/
}
?>


