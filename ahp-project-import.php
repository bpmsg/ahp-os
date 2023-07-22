<?php
/*
 * Import AHP projects from json file
 *
 * Copyright (C) 2022  <Klaus D. Goepel>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

session_start();
// max file size
define('MAX_SIZE', 400000);

include 'includes/config.php';

$version = substr('$LastChangedDate: 2022-07-19 13:31:32 +0800 (Di, 19 Jul 2022) $', 18, 10);
$rev = trim('$Rev: 209 $', "$");

$login = new Login();

if ($login->isUserLoggedIn() === false) {
    header('HTTP/1.0 200');
    header("Location: " . $urlAhp);
}

$class = 'AhpSessionAdmin' . $lang;
$sessionAdmin = new $class();

$ahpDb = new AhpDb();
$author = $login->user_name;
$ahpPj = array();
$err = array();
$extensions = array("json", "JSON");

if (isset($_POST['CANCEL'])) {
    header('HTTP/1.0 200');
    header("Location: " . $urlSessionAdmin);
}

// --- check for upload/import
if (isset($_FILES['file'])) {
    $file_name = $_FILES['file']['name'];
    $file_size =$_FILES['file']['size'];
    $file_tmp =$_FILES['file']['tmp_name'];
    $file_type=$_FILES['file']['type'];
    $file_ext = explode('.', $file_name);
    $file_ext= strtolower(end($file_ext));

    if ($file_size < 128) {
        $err[] = "Please select a valid json file.";
    } elseif (!in_array($file_ext, $extensions)) {
        $err[]="$file_ext extension is not allowed.";
    } elseif ($file_size > MAX_SIZE) {
        $err[]="File size must not exceed " . $phpUtil->display_filesize(MAX_SIZE) . '.';
    } elseif (preg_match('/^[a-zA-Z0-9-_()\s]+\.ext$/', $file_name)) {
        $err[]="Invalid filename.";
    } else {
        $file_name = str_replace(' ', '_', $file_name);
    }

    if (isset($_POST['formToken'])
        && $_POST['formToken'] != $_SESSION['formToken']) {
        $err[] = "Form submission error.";
    } else {
        $_SESSION['formToken']= uniqid();
    }

    if (empty($err)) {
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            $flag = false;
            $err[]= "Possible file upload attack: ";
        } else {
            $flag = true;
            // file uploaded successfully
            $string = file_get_contents($_FILES['file']['tmp_name']);
            $project=json_decode($string, true, 6);
            if (json_last_error() == 0) {
                // decoding successful
                $msg = " decoded successfully.";
                // TODO: check fields in project to verify correct format!
                $text = $project['pj'][0]['project_hText'];
                $ahpH = new AhpHier($text);
                if (empty($ahpH->err)) {
                    // no error in hierarchy text
                    $ahpDb = new AhpDb();
                    // generate a new session code
                    $newSc = $ahpDb->generateSessionCode(6, 7);
                    if (empty($project['pj'][0]['project_description'])) {
                        $project['pj'][0]['project_description'] = " ";
                    }
                    $project['pj'][0]['project_description']
                        .= " - Project import from: '"
                        . $project['pj'][0]['project_sc'] . "'";
                    $project['pj'][0]['project_description']
                        .= "-"
                        . mb_substr($project['pj'][0]['project_datetime'], 0, 10);
                    if ($project['pj'][0]['project_author'] != $author) {
                        $project['pj'][0]['project_description']
                        .= " - Author: " . $project['pj'][0]['project_author'];
                    }

                    // write project
                    $ins = $ahpDb->writeProjectData(
                        $newSc,
                        $project['pj'][0]['project_name'],
                        $project['pj'][0]['project_description'],
                        $project['pj'][0]['project_hText'],
                        $author
                    );
                    if (!$ins) {
                        $err[] = "Write project data failed! ";
                        $err = array_merge($err, $ahpDb->err);
                    }
                    if (isset($project['alt'])) {
                        $ins = $ahpDb->restoreAlt($project['alt'], $newSc);
                        if (!$ins) {
                            $err[] = "Write alternatives failed! ";
                            $err = array_merge($err, $ahpDb->err);
                        }
                    }
                    if (isset($project['pwc'])) {
                        $ins = $ahpDb->restorePwc($project['pwc'], $newSc);
                        if (!$ins) {
                            $err = "Write pairwise comparisons failed! ";
                            $err = array_merge($err, $ahpDb->err);
                        }
                    }
                } else { // --- Hierarchy text has error
                    $err = array_merge($err, $ahpH->err);
                }
            } else { // --- json decode error
                $err[] = json_last_error_msg();
            }
        }
        if ($flag == false) { // --- other general error
            $err[]="Upload Error";
        }
        if (empty($err)) {
            // --- return to ahp session admin
            header('HTTP/1.0 200 ok');
            header("Location: " . $urlSessionAdmin);
        }
    }
} elseif (isset($_POST['submit'])) {
    $err[] = "Invalid file";
}


$pageTitle ="AHP-OS Import";
$title = "AHP-OS Project Import";
$subTitle = "Import project from json file";

/* --- Web Page HTML OUTPUT --- */
$webHtml = new WebHtml($pageTitle, 800);
    include 'includes/login/form.login-hl.php';
    if (!empty($login->errors)) {
        echo $login->getErrors();
    }
    echo "<h1>$title</h1>\n";
    echo "<h2>$subTitle</h2>\n";

    // --- TODO: translation and put in language class
    echo "<p>Import a project from an AHP-OS project JSON file. 
         Extension has to be <span class='res'>"
         . implode(", ", $extensions) . "</span> size must not exceed 
         <span class='res'>" . $phpUtil->display_filesize(MAX_SIZE) . "</span>. 
         Project description will be appended with the original session 
         code, author (if no yourself) and date/time of the original 
         project</p>";
    if (!empty($err)) {
        echo "<p class='err'>", implode(' ', $err), "</p>";
    }
    echo "<p class='msg'>$file_name $msg</p>";
?>

    <!--- html Menu TODO: into views --->
    <form action="<?php echo $myUrl; ?>" method="POST" enctype="multipart/form-data">
        <input type='hidden' name='formToken' value='<?php echo $_SESSION['formToken']; ?>'>
        <fieldset><legend>AHP Project Import Menu</legend>
            <div style='display:block;float:left;padding:2px;'>
                <input type="file" name="file" >
                &nbsp;&nbsp;<input type="submit" value="Import" name="submit">
            </div><div style='float:right'>
                <input type="submit" value="Cancel" name="CANCEL" >
            </div>
        </fieldset>
        <div class='msg' style='clear:both;'></div>
    </form>

<?php

$webHtml->webHtmlFooter($version);
