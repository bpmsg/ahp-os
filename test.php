<?php
/* Sample Page with WebHtml class */

include 'includes/config.php';

$class = 'Ahp' . $lang;
$ahpOs = new $class();
$_SESSION['lang'] = $lang;

$login = new Login();

$pageTitle ='AHP Sample Test Page';
$title = "AHP Sample Test Page";
$subTitle = "Test page for development purposes";
$version = substr('$LastChangedDate: 2022-02-27 13:29:54 +0800 (So, 27 Feb 2022) $', 18, 10);
$rev = trim('$Rev: 173 $', "$");

/* --- Web Page HTML OUTPUT --- */
$webHtml = new WebHtml($pageTitle);
    include 'includes/login/form.login-hl.php';
    if (!empty($login->errors)) {
        echo $login->getErrors();
    }
    echo "<h1>$title</h1>";
    echo "<h2>$subTitle</h2>";
    echo "<p>Output here</p>";
    echo phpinfo();
$webHtml->webHtmlFooter($version);
