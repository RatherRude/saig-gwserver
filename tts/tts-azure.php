<?php
$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
require_once($path . 'conf.php'); // API KEY must be there
require_once($path . 'lib/sharedmem.class.php'); // Caching token

function tts($textString, $mood = 'default', $stringforhash)
{
    global $AZURETTS_CONF;

    $region = $AZURETTS_CONF['region'];
    $AccessTokenUri = 'https://' . $region . '.api.cognitive.microsoft.com/sts/v1.0/issueToken';
    $apiKey = $GLOBALS['AZURE_API_KEY'];

    if (empty(trim($mood)))
        $mood = 'default';

    if ($GLOBALS['AZURETTS_CONF']['validMoods'])
        $valid_tokens = $GLOBALS['AZURETTS_CONF']['validMoods'];
    else
        $valid_tokens = ['angry', 'cheerful', 'assistant', 'calm', 'embarrassed', 'excited', 'lyrical', 'sad', 'shouting', 'whispering', 'terrified'];


    if (in_array($mood, $valid_tokens))
        $validMood = $mood;
    else
        $validMood = 'default';
    /*
    $distancia_minima = PHP_INT_MAX;
    $token_mas_cercano = '';

    // Iteramos sobre cada token del array
    foreach ($valid_tokens as $token) {
        $distancia = levenshtein($mood, $token);
        if ($distancia < $distancia_minima) {
            $distancia_minima = $distancia;
            $token_mas_cercano = $token;
        }
    }
    $validMood = $token_mas_cercano;
    */
    $starTime = microtime(true);

    $cache = new CacheManager();

    if (!$cache->get_cache()) {


        $options = [
            'http' => [
                'header' => 'Ocp-Apim-Subscription-Key: ' . $apiKey . "\r\n" .
                "content-length: 0\r\n",
                'method' => 'POST',
            ],
        ];

        $context = stream_context_create($options);

        //get the Access Token
        $access_token = file_get_contents($AccessTokenUri, false, $context);
        $cache->save_cache($access_token);
        $cacheUsed = 'false';
    } else {
        $access_token = $cache->get_cache();
        $cacheUsed = 'yes';
    }


    if (!$access_token) {
        return false;
    } else {
        //echo "Access Token: ". $access_token. "<br>";


        $ttsServiceUri = 'https://' . $region . '.tts.speech.microsoft.com/cognitiveservices/v1';

        //$SsmlTemplate = "<speak version='1.0' xml:lang='en-us'><voice xml:lang='%s' xml:gender='%s' name='%s'>%s</voice></speak>";
        $doc = new DOMDocument();

        $root = $doc->createElement('speak');
        $root->setAttribute('version', '1.0');
        $root->setAttribute('xml:lang', 'en-us');
        $root->setAttribute('xmlns:mstts', 'https://www.w3.org/2001/mstts');


        $voice = $doc->createElement('voice');
        //$voice->setAttribute( "xml:lang" , "en-us" );
        $voice->setAttribute('xml:gender', 'Female');
        $voice->setAttribute('name', $AZURETTS_CONF['voice']); // Read https://learn.microsoft.com/es-es/azure/cognitive-services/speech-service/language-support?tabs=tts

        $text = $doc->createTextNode($textString);


        $prosody = $doc->createElement('prosody');
        $prosody->setAttribute('rate', $AZURETTS_CONF['rate']); //https://learn.microsoft.com/en-us/azure/cognitive-services/speech-service/speech-synthesis-markup-voice#adjust-prosody
        $prosody->setAttribute('volume', $AZURETTS_CONF['volume']);
        if ($AZURETTS_CONF['countour'])
            $prosody->setAttribute('contour', $AZURETTS_CONF['countour']);



        $prosody->appendChild($text);

        $style = $doc->createElement('mstts:express-as');
        if ($AZURETTS_CONF['fixedMood'])
            $style->setAttribute('style', $AZURETTS_CONF['fixedMood']); // not supported for all voices
        else
            $style->setAttribute('style', $validMood); // not supported for all voices

        $style->setAttribute('styledegree', '2'); // not supported for all voices
        //$style->setAttribute( "role" , "YoungAdultFemale" );  // not supported for all voices
        $style->appendChild($prosody);

        $voice->appendChild($style);
        $root->appendChild($voice);
        $doc->appendChild($root);
        $data = $doc->saveXML();

        //echo "tts post data: ". $data . "<br>";

        $options = [
            'http' => [
                'header' => "Content-type: application/ssml+xml\r\n" .
                "X-Microsoft-OutputFormat: riff-24khz-16bit-mono-pcm\r\n" .
                    'Authorization: ' . 'Bearer ' . $access_token . "\r\n" .
                "X-Search-AppId: 07D3234E49CE426DAA29772419F436CA\r\n" .
                "X-Search-ClientID: 1ECFAE91408841A480F00935DC390960\r\n" .
                "User-Agent: TTSPHP\r\n" .
                    'content-length: ' . strlen($data) . "\r\n",
                'method' => 'POST',
                'content' => $data,
            ],
        ];

        $context = stream_context_create($options);

        // get the wave data
        $result = file_get_contents($ttsServiceUri, false, $context);
        if (!$result) {
            file_put_contents(dirname((__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'soundcache/' . md5(trim($stringforhash)) . '.err', trim($data));
            return false;
            //throw new Exception("Problem with $ttsServiceUri, $php_errormsg");
        } else {
            //echo "Wave data length: ". strlen($result);
        }
        //fwrite(STDOUT, $result);

        // Trying to avoid sync problems.
        $stream = fopen(dirname((__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'soundcache/' . md5(trim($stringforhash)) . '.wav', 'w');
        $size = fwrite($stream, $result);
        fsync($stream);
        fclose($stream);

        //file_put_contents(dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR."soundcache/" . md5(trim($stringforhash)) . ".wav", $result);
        file_put_contents(dirname((__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'soundcache/' . md5(trim($stringforhash)) . '.txt', trim($data) . "\n\rCache:$cacheUsed\n\rtotal call time:" . (microtime(true) - $starTime) . " ms\n\rsize of wav ($size)\n\rfunction tts($textString,$mood / $validMood ,$stringforhash)");
    }
}
