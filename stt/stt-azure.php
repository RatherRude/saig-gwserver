<?php
$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
require_once($path . 'conf.php'); // API KEY must be there

function stt($file)
{
    global $AZURETTS_CONF;
    
    $region = $AZURETTS_CONF['region'];
    $apiKey = $GLOBALS['AZURE_API_KEY'];
    $lang=($GLOBALS['TTSLANGUAGE_AZURE'])?$GLOBALS['TTSLANGUAGE_AZURE']: 'en-US';
    
   // URL and request body
$url = "https://$region.stt.speech.microsoft.com/speech/recognition/conversation/cognitiveservices/v1?language=$lang";
$fileData = file_get_contents($file);

// Request headers
$headers = [
    'Content-Type: audio/wav',
    "Ocp-Apim-Subscription-Key: $apiKey"
];

// Context configuration
$contextOptions = [
    'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", $headers),
        'content' => $fileData
    ]
];

$context = stream_context_create($contextOptions);

// Make the request
$response = file_get_contents($url, false, $context);

// Handle the response
if ($response === false) {
    // Error handling
} else {
    // Process the response
    
}
$reponseParsed=json_decode($response);

    
return $reponseParsed->DisplayText;

    
}


