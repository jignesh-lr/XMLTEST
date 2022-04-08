<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*if (!function_exists('xmlToArray')) {

    function xmlToArray($xml, $options = array()) {
        $defaults = array(
            'namespaceSeparator' => ':', //you may want this to be something other than a colon
            'attributePrefix' => '', //to distinguish between attributes and nodes with the same name
            'alwaysArray' => array(), //array of xml tag names which should always become arrays
            'autoArray' => true, //only create arrays for tags which appear more than once
            'textContent' => '', //key used for the text content of elements
            'autoText' => true, //skip textContent key if node has no attributes or child nodes
            'keySearch' => false, //optional search and replace on tag and attribute names
            'keyReplace' => false, //replace values for above search values (as passed to str_replace())
        );
        $options = array_merge($defaults, $options);
        $namespaces = $xml->getDocNamespaces();
        $namespaces[''] = null; //add base (empty) namespace

        //get attributes from all namespaces
        $attributesArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                //replace characters in attribute name
                if ($options['keySearch']) {
                    $attributeName =
                        str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                }

                $attributeKey = $options['attributePrefix']
                    . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                    . $attributeName;
                $attributesArray[$attributeKey] = (string) $attribute;
            }
        }

        //get child nodes from all namespaces
        $tagsArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = xmlToArray($childXml, $options);
                list($childTagName, $childProperties) = each($childArray);

                //replace characters in tag name
                if ($options['keySearch']) {
                    $childTagName =
                        str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                }

                //add namespace prefix, if any
                if ($prefix) {
                    $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
                }

                if (!isset($tagsArray[$childTagName])) {
                    //only entry with this key
                    //test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] =
                    in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                    ? array($childProperties) : $childProperties;
                } elseif (
                    is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                    === range(0, count($tagsArray[$childTagName]) - 1)
                ) {
                    //key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                } else {
                    //key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }

        //get text content of node
        $textContentArray = array();
        $plainText = trim((string) $xml);
        if ($plainText !== '') {
            $textContentArray[$options['textContent']] = $plainText;
        }

        //stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
        ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

        //return node as array
        return array(
            $xml->getName() => $propertiesArray,
        );
    }

}*/

// $xmlInputFile = 'Hundestation.xml';
// $xmlFile = simplexml_load_file($xmlInputFile,'SimpleXMLElement', LIBXML_NOWARNING);  
// $mappingFileArray = (array)$xmlFile->attributes;

// echo '<pre>';
// print_r($mappingFileArray);
// echo '</pre>';

//$xmlDataFileArr = xmlToArray($xmlFile);

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

function deleteMappingField($mappingFileAttribute,$getDbSchemasData){

	$deleteValue = array_diff($mappingFileAttribute,$getDbSchemasData);
	return $deleteValue;
    //return 'deleteMappingField';   
}

//$getDbSchemasDatas = array("nfc_id"=>"123456","position"=>"test position","note"=>"test note","sampleKey"=>"test sample","sampleKey2"=>"test sample2");


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


//$getDbSchemasData = array_flip($getDbSchemasDatas);

//$mappingFileAttribute = $xmlDataFileArr['values']['attributes'];//array("g"=>"green","d"=>"blue","a"=>"red","e"=>"blue2","f"=>"blue3");

// echo "<b>getDbSchemasData : </b> ".'("a"=>"red","b"=>"green","c"=>"blue","d"=>"yellow")'.'<br>';
// echo "<b>mappingFileAttribute : </b> ".var_dump($xmlDataFileArr['values']['attributes']);
//'("g"=>"green","d"=>"blue","a"=>"red","e"=>"blue2","f"=>"blue3")'.'<br><br>';
echo "<b>getDbSchemasData : </b> ";
echo '<pre>';
print_r($getDbSchemasData);
echo '</pre>';
echo '<br>';

echo "<b>mappingFileAttribute : </b> ";
echo '<pre>';
print_r($mappingFileAttribute);
echo '</pre>';
echo '<br>';

//$addValue=array_diff($getDbSchemasData,$mappingFileAttribute);
$addValue = addMappingField($getDbSchemasData,$mappingFileAttribute);
echo "<b>addMappingField Result : </b>";
echo '<pre>';
print_r($addValue);
echo '</pre>';
echo '</br>';
// $dom = new DOMDocument('1.0', 'utf-8');
// $dom->formatOutput = true;

// $dom = $dom->createAttribute('TESWT');
// $dom->asXML("Sample.xml");


$xmlFile = simplexml_load_file($mappingXmlFile,'SimpleXMLElement', LIBXML_NOWARNING);  
$mappingFileArray = (array)$xmlFile->attributes;

if(!empty($addValue)){
    $addNewAttribute = array_merge($mappingFileArray, $addValue);
}else{
    $addNewAttribute = $addValue;
    echo "No any changes"."\n";
}

$xml = new DOMDocument("1.0", "utf-8"); 
$xml = simplexml_load_file('Hundestation.xml');

foreach ($addValue as $key => $value) {
    $xml->attributes->addChild($value,$value);
    file_put_contents('Hundestation.xml', $xml->asXML());
}


echo "<b>xml File : </b>";
echo '<pre>';
print_r($xml);
echo '</pre>';
echo '</br>';


//$deleteValue=array_diff($mappingFileAttribute,$getDbSchemasData);
$deleteValue=deleteMappingField($mappingFileAttribute,$getDbSchemasData);

echo "<b>deleteMappingField Result: </b>";
echo '<pre>';
print_r($deleteValue);
echo '</pre>';
echo '</br>';

$result = array();

$addArray = array_merge($mappingFileAttribute, $addValue);
foreach ($deleteValue as $key => $value) {
	unset($addArray[$key]);
}
echo "<b>Final Data Result : </b>";
echo '<pre>';
print_r($addArray);
echo '</pre>';
echo '</br>';



//if(array_search($deleteValue, $addArray)){
	
	

//}
/*foreach ($deleteValue as $key => $value) {
	echo $key.' : '.$value.'<br>';
	unset($mappingFileAttribute[$value]);
}

echo "Delete Value : ";
echo '<pre>';
print_r($deleteValue);
echo '</pre>';
echo '</br>';*/
// $result1=array_diff($getDbSchemasData,$mappingFileAttribute);
// echo "************************ array_diff ********************".'<br>';
// echo '<pre>';
// print_r($result1);
// echo '</pre>';
// echo "************************ array_diff ********************".'<br><br>';


// $result2 = array_diff_assoc($getDbSchemasData, $mappingFileAttribute);
// echo "========================= array_diff_assoc ========================".'<br>';
// echo '<pre>';
// print_r($result2);
// echo '</pre>';
// echo "========================= array_diff_assoc ========================".'<br><br>';


// $result3 = array_udiff($getDbSchemasData, $mappingFileAttribute);
// echo "~~~~~~~~~~~~~~~~~~~ array_udiff ~~~~~~~~~~~~~~~~~~~~".'<br>';
// echo '<pre>';
// print_r($result3);
// echo '</pre>';
// echo "~~~~~~~~~~~~~~~~~~~ array_udiff ~~~~~~~~~~~~~~~~~~~~".'<br><br>';



// $array_intersect = array_intersect($getDbSchemasData, $mappingFileAttribute);
// $finalArray = array_merge($array_intersect, $addValue);
// echo "_________________________ array_intersect ____________________".'<br>';
// echo '<pre>';
// print_r($array_intersect);
// echo '</pre>';
// echo "_________________________ array_intersect ____________________".'<br><br>';

// echo "_________________________// finalArray //____________________".'<br>';
// echo '<pre>';
// print_r($finalArray);
// echo '</pre>';
// echo "_________________________// finalArray //____________________".'<br><br>';

// $result5 = array_intersect_assoc($getDbSchemasData, $mappingFileAttribute);
// echo "-------------------- array_intersect_assoc -----------------".'<br>';
// echo '<pre>';
// print_r($result5);
// echo '</pre>';
// echo "-------------------- array_intersect_assoc -----------------".'<br><br>';
?>


