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

$mappingXmlFile = 'Hundestation.xml';
$mappingFileArrays = mappingXMLRead($mappingXmlFile);
$mappingFileCategory = $mappingFileArrays[0];
$mappingFileAttribute = $mappingFileArrays[1];


function addMappingField($getDbSchemasData,$mappingFileAttribute){
    //return 'addMappingField';
    $addValue = array_diff($getDbSchemasData,$mappingFileAttribute);

    return $addValue;
}

function deleteMappingField($getDbSchemasData,$mappingFileAttribute){

	$deleteValue = array_diff($mappingFileAttribute,$getDbSchemasData);
	return $deleteValue;
    //return 'deleteMappingField';   
}

$jsonFile = 'dbSchema.json';
$jsonContent = file_get_contents($jsonFile);
$jsonToArray = json_decode($jsonContent, true);

$schemaTableCategory = $jsonToArray['category'];
$schemaTableDatas = $jsonToArray['table'];
// echo '<pre>';
// print_r($schemaTableDatas);
// echo '</pre>';

if(!empty($schemaTableDatas)){
    $column_name = array();
    foreach ($schemaTableDatas as $key => $schemaTableData) {
        
        
        $table_name = $schemaTableData['table_name'];
        $column_name[$key] = $schemaTableData['column_name'];
        $data_type = $schemaTableData['data_type'];
        //echo $column_name ." : ".$data_type.'<br>';
        $getDbSchemasData = $column_name ; //array("nfc_id","position","note","sampleKey","sampleKey2");
        
    }
}

$getDbSchemasData = $column_name;   //array("nfc_id","position","note","sampleKey","sampleKey2");

// echo "<b>getDbSchemasData : </b> ";
// echo '<pre>';
// print_r($getDbSchemasData);
// echo '</pre>';
// echo '<br>';

// echo "<b>mappingFileAttribute : </b> ";
// echo '<pre>';
// print_r($mappingFileAttribute);
// echo '</pre>';
// echo '<br>';

//$addValue=array_diff($getDbSchemasData,$mappingFileAttribute);
$addValue = addMappingField($getDbSchemasData,$mappingFileAttribute);
echo "<b>addMappingField Result : </b>";
echo '<pre>';
print_r($addValue);
echo '</pre>';
echo '</br>';



$xmlFile = simplexml_load_file($mappingXmlFile,'SimpleXMLElement', LIBXML_NOWARNING);  
$mappingFileArray = (array)$xmlFile->attributes;

/*if(!empty($addValue)){
    $addNewAttribute = array_merge($mappingFileArray, $addValue);
}else{
    $addNewAttribute = $addValue;
    echo "No any changes"."\n";
}*/



if(!empty($addValue)){
    foreach ($addValue as $key => $value) {
        $xml = new DOMDocument("1.0", "utf-8"); 
        $xml = simplexml_load_file('Hundestation.xml');

        if(!isset($xml->attributes->$value)){
            $xml->attributes->addChild($value,$value);
        }
        
        file_put_contents('Hundestation.xml', $xml->asXML());

        echo "<b>xml File : </b>";
        echo '<pre>';
        print_r($xml);
        echo '</pre>';
        echo '</br>';
    }
}else{
   echo "addMappingField No any changes"."<br>"; 
}





//$deleteValue=array_diff($mappingFileAttribute,$getDbSchemasData);
$deleteValue=deleteMappingField($getDbSchemasData, $mappingFileAttribute);

if(!empty($deleteValue)){
    foreach ($deleteValue as $key => $value) {
        //echo $key." : ".$value.'<br>';      
        $xml = new DOMDocument("1.0", "utf-8"); 
        $xml = simplexml_load_file('Hundestation.xml');
        unset($xml->attributes->$key);
       
        file_put_contents('Hundestation.xml', $xml->asXML());

        
    }
}else{
   echo "deleteMappingField No any changes"."<br>"; 
}

?>


