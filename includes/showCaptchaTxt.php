<?php
/*
 * Generates text captcha challange using api of
 * https://textcaptcha.com/
 * 
 */

$version = substr('$LastChangedDate: 2022-02-06 09:58:40 +0800 (Sun, 06 Feb 2022) $',18,10);
$rev = trim('$Rev: 106 $', "$");

$cptUrl = 'http://api.textcaptcha.com/bpmsg.com.json';
$cpt = json_decode(file_get_contents($cptUrl),true);

// --- TODO: extend fallback challenge
if (!$cpt) {
 $cpt = array( // fallback challenge
  'q'=>"Is ice hot or cold?",
  'a'=>array(md5("cold"))
 );
 trigger_error("showCaptchaTxt.php: no result from api.textcaptcha.com", E_WARNING);
}

$_SESSION['captcha'] = $cpt['a'];
echo htmlentities($cpt['q']);
