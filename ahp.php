<?php
/*
 * AHP hierarchy project calculation with alternatives
 * @author Klaus D. Goepel
 * @package AHP-OS
 * @since 2013-12-01 release first version ahp hierarchy
 *
 *  

    Copyright (C) 2022  <Klaus D. Goepel>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
 
 */
 
include 'includes/config.php';

session_start();

$version = substr('$LastChangedDate: 2022-02-08 15:35:30 +0800 (Tue, 08 Feb 2022) $',18,10);
$rev = trim('$Rev: 115 $', "$");

// sets the session variable for language
$lang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING);
	if($lang != null && $lang != false && in_array($lang, $languages) ){
		$lang = strtoupper($lang);
		setcookie('lang', $lang, time() + COOKIE_RUNTIME, "/", COOKIE_DOMAIN);
		$_SESSION['lang'] = $lang;
	} elseif(isset($_COOKIE['lang'])  && in_array(strtolower($_COOKIE['lang']),$languages )) {
		$lang = $_COOKIE['lang'];
		$_SESSION['lang'] = $lang;
	} elseif(isset($_SESSION['lang']) && in_array(strtolower($_SESSION['lang']),$languages ))
		$lang = $_SESSION['lang'];
	else
		$lang ='EN';
$class = 'Ahp' . $lang;
$ahpOs = new $class;

$js = new JsCheck;
$login = new Login();

$loggedIn = $login->IsUserLoggedIn();

// reset in case back from edit form
if (isset($_SESSION['REFERER']))
	unset($_SESSION['REFERER']);

/* 
 * --- Web Page HTML OUTPUT --- 
 */
$webHtml = new WebHtml($ahpOs->titles['pageTitle']);

if( !(isset($_SESSION['javascript']) && $_SESSION['javascript']) )
	$js->checkJsByForm();
// $js->checkJsByCookies();
$loginHeaderText = "<a href=".$urlHome.">BPMSG Home</a>&nbsp;&nbsp;<a href='ahp-news.php'>Latest News</a>";
if($js->isJsActivated() === false) { 
	$loginHeaderText .= "<span class='err'>&nbsp;For full functionality please allow JavaScript! </span>";
} else { 
	$loginHeaderText .= "<span class='msg'>&nbsp;&nbsp;Java is enabled. </span>"; 
}

include 'includes/login/form.login-hl.php';
	echo $ahpOs->titles['h1title'];

if(defined( 'SYS_MSG' ))
	echo "<p class='err'>System message: " . SYS_MSG . "</p>";

if (!empty($login->errors) || !empty($login->messages)) 
	echo $login->getErrors();

echo "<p>Language: <a href='", $urlAct, "?lang=en'>English</a>
		   &nbsp;&nbsp;<a href='", $urlAct, "?lang=de'>Deutsch</a>
		   &nbsp;&nbsp;<a href='", $urlAct, "?lang=es'>Español</a>
		   &nbsp;&nbsp;<a href='", $urlAct, "?lang=pt'>Português</a>
		  </p>";
if(DONATIONS)
	echo "<p>Donation: <a href='https://paypal.me/ahpDonation'>paypal.me/ahpDonation</a></p>";
echo $ahpOs->titles['h2subTitle'];
echo $ahpOs->info['intro11'];
echo $ahpOs->info['intro12'];
echo $ahpOs->info['intro13'];

// --- Terms of use link
echo $ahpOs->info['intro14'];

if(DONATIONS)
	echo $ahpOs->info['intro15'];

echo $ahpOs->info['intro16'];

if(!$loggedIn && ($lang == 'EN' || $lang == 'ES' || $lang == 'PT')){
	echo $ahpOs->info['intro21'];
	echo $ahpOs->info['intro22'];
	echo $ahpOs->info['intro23'];
	echo $ahpOs->info['intro24'];
}

echo "<div style='clear:both;'>";

if(!$loggedIn){
	if( defined('CMTLNK') ){
		echo $ahpOs->titles['h2contact'];
		printf($ahpOs->info['contact'], CMTLNK);
	}
	echo "<small>The AHP-OS package is realized in php and since Feb 2022 available from the author 
	as open source in <a href='https://github.com/bpmsg/ahp-os' target='_blank' >Github</a>.</small>";
} else if (in_array($_SESSION['user_id'], $admin ))
	echo "<small><a href='includes/login/do/do-user-admin.php'>AHP-OS admin</a></small>";
echo "</div>";
$webHtml->webHtmlFooter($version);
