<?php
$keys = array();
array_push($keys, "last-ip");
array_push($keys, "last-update");

$kv = new SaeKV();

if (($ret=$kv->init()) && ($ret=$kv->mget($keys)))
{
    echo '<strong>IP was updated to ' . $ret["last-ip"] . ' at ' . $ret["last-update"] . '!</strong><br/>';
} else
{
    echo "<strong>Can't get IP, not updated?</strong><br/>";
}

echo "<strong>Remote address: </strong>" . $_SERVER['REMOTE_ADDR'] . "<br/>";

//$ret = $kv->get("last-request");
//echo "<strong>Last request:</strong><br/>";
//echo "<pre>";
//var_dump(unserialize($ret));
//echo "</pre>";
