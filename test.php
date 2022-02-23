<?php


/* Sample Page with WebHtml class */
include 'includes/config.php';

$class = 'app' . $lang;
$ahpOs = new $class();
$_SESSION['lang'] = $lang;

$login = new Login();
// reset in case back from edit form
if (isset($_SESSION['REFERER'])) {
    unset($_SESSION['REFERER']);
}

$pageTitle ='AHP Sample Test Page';
$title = "AHP Sample Test Page";
$subTitle = "Test page for development purposes";
$version = substr('$LastChangedDate: 2019-08-25 09:22:09 +0800 (Sun, 25 Aug 2019) $', 18, 10);
$rev = trim('$Rev: 35 $', "$");

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
