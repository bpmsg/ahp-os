<?php

class JsCheck
{
    /*
     *   jsCheck - By Gustav Eklundh
     *   A PHP-class to check if JavaScript is activated or not.
     *
     *   Copyright (C) 2009  Gustav Eklundh
     *
     *   This program is free software: you can redistribute it and/or modify
     *   it under the terms of the GNU General Public License v3 as published
     *   by the Free Software Foundation.
     *
     *   This program is distributed in the hope that it will be useful,
     *   but WITHOUT ANY WARRANTY; without even the implied warranty of
     *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *   GNU General Public License for more details.
     *
     *   You should have received a copy of the GNU General Public License
     *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
     *
     *   Homepage:  http://threebyte.eu/
     *   Email:     gustav@xcoders.info
    **/

    public function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['javascript'])) {
            $_SESSION['javascript'] = false;
        }
        if (!isset($_SESSION['loaded'])) {
            $_SESSION['loaded'] = 1;
        }
    }

    public $js = false;

    public function checkJsByCookies()
    {
        if ($_SESSION['loaded'] < 2) {
            if (isset($_COOKIE['javascript']) && $_COOKIE['javascript'] == true) {
                $_SESSION['javascript'] = true;
            }
            if ($_SESSION['javascript'] == false) {
                echo <<<JS
	<script>
		document.cookie = "javascript=true";
	</script>
JS;
                $_SESSION['loaded']++;
            }
        }
        $this->js = $_SESSION['javascript'];
    }

    public function checkJsByForm()
    {
        if (isset($_POST['javascript'])) {
            $_SESSION['javascript'] = true;
        }
        if ($_SESSION['loaded'] < 2 && $_SESSION['javascript'] != true) {
            $_SESSION['loaded']++;
            echo <<<JS
	<form name="javascript" id="javascript" method="post" style="display:none" action="">
		<input name="javascript" type="text" value="true" >
		<script>
			document.javascript.submit();
		</script>
	</form>
JS;
        }
        $this->js = $_SESSION['javascript'];
    }

    public function isJsActivated()
    {
        if ($this->js == true || $_SESSION['javascript'] == true) {
            return true;
        } else {
            return false;
        }
    }
}
