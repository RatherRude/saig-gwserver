<?php
$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
require_once($path . "conf.php"); // API KEY must be there

require_once($path . "vendor/autoload.php");

// Auth
use Google\Cloud\VideoIntelligence\V1\VideoIntelligenceServiceClient;

// [START texttospeech_v1_generated_TextToSpeech_SynthesizeSpeech_sync]
use Google\ApiCore\ApiException;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\SynthesizeSpeechResponse;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;



function tts($textString, $mood = "default", $stringforhash) {

//	    global $ELEVEN_LABS,$ELEVENLABS_API_KEY;
        // $apiKey = $GLOBALS["GCP_SA_KEY"];

//        $url = "https://us-central1-texttospeech.googleapis.com/v1beta1/text:synthesize";
        // $url = "https://texttospeech.googleapis.com/v1beta1/text:synthesize";
/*
        $request = [
            "audioConfig" => [
                "audioEncoding" => "LINEAR16",
                "effectsProfileId" => [
                    "small-bluetooth-speaker-class-device"
                ],
                "pitch"=> 0,
                "speakingRate"=> 1
            ],
            "input" => [
                "text" => $textString
            ],
            "voice" => [
                "languageCode"=> "en-US",
                "name"=> "en-US-Studio-O"
            ]
        ];*/

    $startTime = microtime(true);

    $textToSpeechClient = new TextToSpeechClient([
        'credentials' => json_decode(file_get_contents("../{$GLOBALS["GCP_SA_KEY"]}"), true)
    ]);

    $input = new SynthesisInput();
    $input->setText($textString);
    $voice = new VoiceSelectionParams();
    $voice->setLanguageCode("en-US");
    $voice->setName("en-US-Studio-O");
    $audioConfig = new AudioConfig();
    $audioConfig->setAudioEncoding(AudioEncoding::LINEAR16);

    $resp = $textToSpeechClient->synthesizeSpeech($input, $voice, $audioConfig);
    // file_put_contents("test.wav", $resp->getAudioContent());

    // Trying to avoid sync problems.
    $stream = fopen(dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "soundcache/" . md5(trim($stringforhash)) . ".wav", 'w');
    $size = fwrite($stream, $resp);
    fsync($stream);
    fclose($stream);

    file_put_contents(dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "soundcache/" . md5(trim($stringforhash)) . ".txt", trim($textString) . "\n\rsize of wav ($size)\n\rexecution time: " . (microtime(true) - $startTime) . ") secs  function tts($textString,$mood=\"cheerful\",$stringforhash)");




    /*
		$apiKey=$ELEVENLABS_API_KEY;

	    $starTime = microtime(true);

		$url = "https://api.elevenlabs.io/v1/text-to-speech/{$ELEVEN_LABS["voice_id"]}?{$ELEVEN_LABS["optimize_streaming_latency"]}=1";

		// Request headers
		$headers = array(
			'Accept: audio/mpeg',
			"xi-api-key: $apiKey",
			'Content-Type: application/json'
		);
		// Request data
		$data = array(
			'text' => $textString,
			'model_id' => 'eleven_monolingual_v1',
			'voice_settings' => array(
				'stability' => $ELEVEN_LABS["stability"]+0.0,
				'similarity_boost' => $ELEVEN_LABS["similarity_boost"]+0.0
			)
		);

		// Create stream context options
		$options = array(
			'http' => array(
				'method' => 'POST',
				'header' => implode("\r\n", $headers),
				'content' => json_encode($data)
			)
		);

		// Create stream context
		$context = stream_context_create($options);

		// Send the request
		$response = file_get_contents($url, false, $context);

		// Handle the response
		if ($response !== false ) {
			// Handle the successful response
			require_once(__DIR__.DIRECTORY_SEPARATOR."../lib/mp3riffer.php");
			$finalData=MP3toWav($response,strlen($response));

			file_put_contents(
				dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "soundcache/" . md5(trim($stringforhash)) . ".wav"
				, $finalData); // Save the audio response to a file

            file_put_contents(dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "soundcache/" . md5(trim($stringforhash)) . ".txt", trim($textString) . "\n\rCache:$cacheUsed\n\rtotal call time:" . (microtime(true) - $starTime) . " ms\n\rsize of wav ($size)\n\rfunction tts($textString,$mood=\"cheerful\",$stringforhash)");
			$GLOBALS["DEBUG_DATA"][]=(microtime(true) - $starTime)." secs in 11labs call and mp3riffer";
			return true;
			
		} else {
			$textString.=print_r($http_response_header,true);
			file_put_contents(dirname((__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "soundcache/" . md5(trim($stringforhash)) . ".err", trim($textString));
            return false;
			
		}*/

}
