<?php
error_reporting(E_ERROR);

require_once('lib/sql.class.php');
require_once('lib/Misc.php');
require_once('conf.php');
require_once('chat/generic.php');
$GLOBALS['DEBUG_MODE'] = true;

$db = new sql();


$validBots = ['botrix', 'Botrix', 'nightbot', 'Nightbot', 'streamlabs', 'Streamlabs', 'moobot', 'Moobot', 'streamelements', 'StreamElements', 'wizebot', 'Wizebot']; // Name of bot (botrix, streamelements, nightbot,..)
/* Bot Message Markup
 * {user/name} just followed!
 * {user/name} just tipped {amount}! Message: {msg}
 * {user/name} just hosted the stream for {amount} viewers!
 * {user/name} just subscribed!
 * {user/name} just subscribed for {amount} months in a row!
 * {user/name} cheered {amount} bits! {msg}
 * {user/name} just redeemed {item}!
 * {user/name} raids with {amount} viewers!
 * {user/name} just ordered {item}! */

// Make sure POST Data is there
if (isset($_POST['message']) && isset($_POST['user'])) {
  $playername = $GLOBALS['PLAYER_NAME'];
  $user = $_POST['user'];
  $message = $_POST['message'];
  $platform = $_POST['platform'] ?? 'Streaming'; // POST Param: 'Kick' or 'Twitch'
  //$platform = 'Twitch'; // POST Param: 'Kick' or 'Twitch'
  $instruction = '(Chat as Herika)';

  error_log($message);
  // Reading out twitch messages and reacting to them
  if (!in_array($user, $validBots)) {
    $contextHistory = 25;
    // Use the original Twitch User Message
    $summary = "$user just said:'$message'.";
    $message = "$user(on Twitch): $message";
    $instruction .= 'Herika:';
  } // Prompting Herika for the appropriate Twitch Event by reacting to Bot Messages
  else {
    $contextHistory = 1;
    if (preg_match('/^(.+) just followed!\s*$/', $message, $matches)) {
      $message = "{$matches[1]} just followed '.$playername.'\'s '.$platform.' channel!";
      $instruction .= " (Show appreciation by greeting {$matches[1]} and thanking them for the follow.)";
    } elseif (preg_match('/^(.+) just tipped (\d+)! Message: (.+)\s*$/', $message, $matches)) {
      $message = "{$matches[1]} donated {$matches[2]} on '.$playername.'\'s '.$platform.' channel! The message was: '{$matches[3]}'";
      $instruction .= " (Acknowledge {$matches[1]}'s donation, express gratitude for their support and mention their message.)";
    } elseif (preg_match('/^(.+) just hosted the stream for (\d+) viewers!\s*$/', $message, $matches)) {
      $message = "{$matches[1]} hosted '.$playername.'\'s stream for {$matches[2]} viewers!";
      $instruction .= " (Give a shout-out to {$matches[1]} for hosting '.$playername.'\'s stream and thank them for the viewers.)";
      $contextHistory = 50;
    } elseif (preg_match('/^(.+) just subscribed!\s*$/', $message, $matches)) {
      $message = "{$matches[1]} subscribed to '.$playername.'\'s '.$platform.' channel!";
      $instruction .= " (Celebrate {$matches[1]}'s subscription and give them a warm welcome.)";
    } elseif (preg_match('/^(.+) just subscribed for (\d+) months in a row!\s*$/', $message, $matches)) {
      $message = "{$matches[1]} subscribed for {$matches[2]} months in a row to '.$playername.'\'s '.$platform.' channel!";
      $instruction .= " (Acknowledge {$matches[1]}'s loyalty and express your gratitude for their continued support.)";
    } elseif (preg_match('/^(.+) just gifted a subscription!\s*$/', $message, $matches)) {
      $message = "{$matches[1]} gifted a subscription on '.$playername.'\'s '.$platform.' channel!";
      $instruction .= " (Acknowledge {$matches[1]}'s generosity and express your gratitude.)";
    } elseif (preg_match('/^(.+) cheered (\d+) bits! (.+)\s*$/', $message, $matches)) {
      $message = "{$matches[1]} cheered {$matches[2]} bits on '.$playername.'\'s '.$platform.' channel! The message was: '{$matches[3]}'";
      $instruction .= " (Give a big thank you to {$matches[1]} for the bits and mention their message.)";
    } elseif (preg_match('/^(.+) just redeemed (\w+)!\s*$/', $message, $matches)) {
      $message = "{$matches[1]} redeemed '{$matches[2]}' on '.$playername.'\'s '.$platform.' channel!";
      $instruction .= " (Acknowledge {$matches[1]}'s redemption and fulfill their request if applicable.)";
    } elseif (preg_match('/^(.+) raids with (\d+) viewers!\s*$/', $message, $matches)) {
      $message = "{$matches[1]} raided '.$playername.'\'s '.$platform.' channel with {$matches[2]} viewers!";
      $instruction .= " (Welcome the raiding party and express your appreciation to {$matches[1]} for the raid.)";
      $contextHistory = 50;
    } elseif (preg_match('/^(.+) just ordered (\w+)!\s*$/', $message, $matches)) {
      $message = "{$matches[1]} placed an order for '{$matches[2]}' on '.$playername.'\'s '.$platform.' channel!";
      $instruction .= " (Acknowledge {$matches[1]}'s order and let them know when it will be fulfilled.)";
    } else {
      // No matching pattern
      error_log('missing markup');
      die();

    }
  }

  // Ask ChatGPT and save the response
  $responseText = requestGeneric($instruction, $message, 'AASPGQuestDialogue2Topic1B1Topic', $contextHistory, true, true);
  // Trigger subtitles and TTS
  parseResponseV2(isset($summary) ? $summary . $responseText : $responseText, '', 'AASPGQuestDialogue2Topic1B1Topic', 'Herika');
}

?>
