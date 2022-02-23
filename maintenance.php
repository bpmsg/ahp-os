<?php
/* Maintenance */

$version = substr('$LastChangedDate: 2022-02-23 09:04:00 +0800 (Mi, 23 Feb 2022) $',18,10);
$rev = trim('$Rev: 167 $', "$");

/* --- Web Page HTML OUTPUT --- */
$webHtml = new WebHtml( APP . " Maintenance");

    echo $hlNoLogin;
    echo "<h1>" . APP . " Maintenance</h1>";
    echo "<h2>Sorry, the web site is temporarily unavailable.</h2>";;
    echo "<p class='hl'>We are currently performing scheduled maintenance. <br>Site will be back
				soon.</p>";
    echo "<p class='msg'>We apologize for any inconvenience.</p>";
    echo "<p>Thank You!</p>";
 
$webHtml->webHtmlFooter($version);
