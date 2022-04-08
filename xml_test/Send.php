<?php
// /* XML To JSON */

// // ini_set('display_errors', '1');
// // ini_set('display_startup_errors', '1');
// // error_reporting(E_ALL);
// date_default_timezone_set('Europe/Berlin');
// require_once __DIR__ . '/../config.php';
// require_once 'jsontoxml.php';


// $Processing = __DIR__ . "/Processing";
// $jsonFiles = glob("$Processing/*.json");
// if(empty($jsonFiles)){    
//     echo "<b>No any json files in Processing folder<b>"."\n";
// }else{

//     foreach ($jsonFiles as $key => $jsonData) {
//         //echo "<br>".$jsonData.'<br>';
//         //echo file_get_contents($jsonData).'<br>';
//         $pathInfo = pathinfo($jsonData);
//         if($pathInfo['extension'] == 'json'){
            
//             $jsonFilePath = $pathInfo['dirname']."/".$pathInfo['basename'];            
//             //$value = jsonToArrayConvert($data);
//             //$jsonFileName = basename($jsonFilePath); 
//             $jsondata = file_get_contents($jsonFilePath);
//             //$result = convertJsonToGmk($jsondata);    
//             //var_dump($result);
//         }
//     }    
// }
$zip = new ZipArchive;
$zipStatus = $zip->open(rand().".zip", ZipArchive::CREATE);
if($zipStatus == 1 || $zipStatus == true){
    $contentXMLFile = "content.xml";
    $dataXMLFileName = "data_00001.xml";
    $files = array($contentXMLFile, $dataXMLFileName);
    foreach ($files as $file) {   
        echo $file.'<br>++' ;
        $zip->addFile($file, basename(rand().".xml"));  
    }
    $test = $zip->close();
    var_dump($zip);
}
?> 