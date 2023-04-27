<?php
error_reporting(E_ERROR);
require_once("lib/sql.class.php");
require_once("lib/Misc.php");
require_once("conf.php");
ob_start();
include("tmpl/head.html");
$db = new sql();


/* Actions */
if ($_GET["clean"]) {
    $db->delete("responselog","sent=1");

}
if ($_GET["reset"]) {
    $db->delete("eventlog","true");

}

if ($_GET["export"]) {
    while(@ob_end_clean());
    $data=$db->fetchAll("select case when type='book' then 'The party find a book: '||data else data end as data  from eventlog a where type<>'combatend' and type<>'location' and type<>'quest' order by ts desc");
    header('Content-type: text/plain');
    
    foreach (array_reverse($data) as $row) {
        echo $row["data"]."\r\n";
    }
    ob_end_clean();
    die();
}

if ($_GET["reinstall"]) {
    require_once("cmd/install-db.php");
    header("Location: index.php?table=response");
}

if ($_POST["prompt"]) {
    require_once("chat/generic.php");
    $GLOBALS["DEBUG_MODE"]=true;
    requestGeneric($_POST["prompt"],$_POST["preprompt"], $_POST["queue"],10);
    header("Location: index.php?table=response");
}

if ($_POST["command"]) {
    $db->insert(
        'responselog',
        array(
            'localts' => time(),
            'sent' => 0,
            'text' => $_POST["command"],
            'actor' => "Herika",
            'action' => 'command'
        )
    );
    header("Location: index.php?table=response");
}

if ($_POST["animation"]) {
    $db->insert(
        'responselog',
        array(
            'localts' => time(),
            'sent' => 0,
            'text' => trim($_POST["animation"]),
            'actor' => "Herika",
            'action' => 'animation'
        )
    );
    header("Location: index.php?table=response");
}

/* Actions */


echo "<h1>Gateway Server CP for {$GLOBALS["PLAYER_NAME"]}".(($_GET["autorefresh"])?" (autorefreshes every 5 secs)":"")." </h1>";
echo "
<div class='menupane'>
<a href='index.php?table=response' class='buttonify'>Responses</a> ::
<a href='index.php?table=event'  class='buttonify'>Events</a> ::
<a href='index.php?table=log'  class='buttonify'>Log</a> ::
<a href='index.php?table=event&autorefresh=true'  class='buttonify'>Monitor events</a> ::::
<a href='index.php?clean=true&table=response'  class='buttonify' onclick=\"return confirm('Sure?')\">Clean sent</a> ::
<a href='index.php?reset=true&table=event'  class='buttonify' onclick=\"return confirm('Sure?')\">Reset events</a> ::
<a href='index.php?reinstall=true'  class='buttonify' onclick=\"return confirm('Sure?')\">Reinstall</a> ::
<a href='index.php?export=true'  class='buttonify' target='_blank'>Export Adventure</a> ::
<span onclick='toggleDP()' class='buttonify'>Debug Pane</span> 

<!--<a href='index.php?openai=true'  class='buttonify'>OpenAI API Usage</a> -->
</div>

<script>
function toggleDP() {document.getElementsByClassName('debugpane')[0].style.display=document.getElementsByClassName('debugpane')[0].style.display=='block'?'none':'block'}
</script>

<div style='border:1px solid grey' class='debugpane'>
<form action='index.php' method='post'>
    <input type='text' name='prompt' value='(Chat as Herika)'>
    <input type='text' size='128' name='preprompt' value='{$GLOBALS["PLAYER_NAME"]}: What...?'>
    <select name='queue'>
        <option value='AASPGDialogueHerika1WhatTopic'>What do you think about?</option>
        <option value='AASPGDialogueHerika2Branch1Topic'>What we should do?</option>
        <option value='AASPGDialogueHerika3Branch1Topic'>What do you know about this place?</option>
        <option value='AASPGQuestDialogue2Topic1B1Topic'>Tell me something (priority)</option>
    </select>
    <input type='submit' value='Request Chat'>
</form>
<form action='index.php' method='post'>
    <select name='command'>
        <option value='IdleLookFar'>IdleLookFar</option>
    </select>
    <input type='submit' value='Post command'>
</form>
<form action='index.php' method='post'>
<input type='text' name='animation' value=''>
<input type='submit' value='Post animation'>
</form>
</div>
";

if ($_GET["table"] == "response") {
    $results = $db->fetchAll("select  A.*,ROWID FROM responselog a order by ROWID asc");
    echo "<p>Response queue</p>";
    print_array_as_table($results);
}

if ($_GET["table"] == "event") {
    $results = $db->fetchAll("select  A.*,ROWID FROM eventlog a order by ts  desc");
    echo "<p>Event log</p>";
    print_array_as_table($results);
    if ($_GET["autorefresh"]) {
        header("Refresh:5");
        header("Refresh:5");
    }
}
if ($_GET["table"] == "cache") {
    $results = $db->fetchAll("select  A.*,ROWID FROM eventlog a order by ts  desc");
    echo "<p>Event log</p>";
    print_array_as_table($results);
}
if ($_GET["table"] == "log") {
    $results = $db->fetchAll("select  A.*,ROWID FROM log a order by localts  desc");
    echo "<p>Repsonse log</p>";
    print_array_as_table($results);
}

$buffer=ob_get_contents();
ob_end_clean();
$title = "Gateway Server CP for {$GLOBALS["PLAYER_NAME"]}";
$title.=(($_GET["autorefresh"])?" (autorefreshes every 5 secs)":"");
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);
echo $buffer;
    
?>
