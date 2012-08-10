<?php

defined('SYSPATH') or die('No direct script access.');

$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'To: ' . $message->to->nom_complet() . ' <' . $message->to->email . '>' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
$headers .= "From: " . $message->from ? $message->from->nom_complet() . " <" . $message->from->email . ">" : "noreply@saveinteam.com";
echo $headers;
?>