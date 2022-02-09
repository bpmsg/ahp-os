<?php
/** AHP on-line calculator 
* @author Klaus D. Goepel 
* @copyright 2014 Klaus D. Goepel
* @since first version 2013-11-11
* @version 2019-05-13 last version w/o SVN
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
*
*/
	include 'includes/config.php';
	$login = new Login();

	// sets the session variable for language
	$lang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING);
	if($lang != null && $lang != false && in_array($lang, $languages) ){
		$lang = strtoupper($lang);
		setcookie('lang', $lang, time() + COOKIE_RUNTIME, "/", COOKIE_DOMAIN);
		$_SESSION['lang'] = $lang;
	} elseif(isset($_COOKIE['lang'])
				&& in_array(strtolower($_COOKIE['lang']),$languages ))
		$lang = $_COOKIE['lang'];
	else
		$lang ='EN';
	$class = 'AhpPrioCalc' . $lang;
	$ahpPrioCalc = new $class;

	// reset in case back from edit form
	if (isset($_SESSION['REFERER']))
		unset($_SESSION['REFERER']);

	$version = substr(
	'$LastChangedDate: 2022-02-09 12:39:27 +0800 (Wed, 09 Feb 2022) $',18,10);
	$rev = trim('$Rev: 116 $', "$");

	$criteria = array();
	$dta = array();	// for graphic display
	$data = array(); 
	$n = 3;

	$ahp = new AhpCalcIo($n);
	$ahp->header = "My Objective";

	//sets url string for form action new n
	function get_url_newn($urlAct){
		return substr_replace($urlAct,"1.php",-4);
	}

	// exit
	if(isset($_POST['DONE'])){
		header("Location: " . $urlAhp);		
		exit();
	}

	// --- START MAIN PROGRAM
	$cr=0.;
	$txtout = "";	// Textstring fuer file output
	$err_get =  $ahp->setNamesFromGet(
	 $ahp->n, $ahp->header, $ahp->criteria, CRITMAX, "Crit-");
	$n = $ahp->n;
	if ($n == 2)
		unset($ahp->criteria[2]);
	$criteria = $ahp->criteria;
	// number of pair-wise comparison for n criteria
	$m_pc = $ahp->npc = $ahp->get_npc($n);

	$new = get_url_newn($urlAct);
	$act = $ahp->getUrlCode($urlAct, $n, $ahp->header, $criteria);	
	$submit = array( 
		'txt' => 'Download_(.csv)',
		'var' => 'download'
		);
			
	// check POST parameter
	$err_post = $ahp->set_pwc($n);

	if($err_post == 0){
		// ok - Start calculation of results
		$pwc = $ahp->pwc;
		// solve eigen vector with evm
		$ahp->set_evm($pwc);
		$cr = $ahp->cr_alo;
	} else {
		// set default pairwise comparison
		$pwc = $ahp->set_pwc_def($n);
	}
	$pwc = $ahp->pwc;


	// download text file and track with Piwik as outlink
	if ( isset($_POST['download'])){
		$dec = (isset($_POST['csv']) ? ',' : '.'); 
		$txtout = $ahp->set_txtbuf($dec);
		/* track download in Piwik
			if(is_object($webHtml->t))
			$webHtml->t->doTrackAction($myUrl, 'download');
		*/
		$err = $ahp->txtDownload('ahp.csv',$txtout);
		die;
	}

/* 
 * --- Web Page HTML OUTPUT --- 
 */
	$webHtml = new WebHtml($ahpPrioCalc->titles1['pageTitle']);
	echo '<div style="display:block;float:left">',$loginHeaderText,
		'</div><div style="clear:both;"></div>';

	echo "\n<!-- INTRO and GET NEW N -->\n";
	echo $ahpPrioCalc->titles1['h1title'];
	if(!(isset($_SESSION['lang'])  || isset($_COOKIE['lang'])))
		echo "<p>Language: <a href='",$urlAct, "?lang=en'>English</a>
              &nbsp;&nbsp;<a href='", $urlAct, "?lang=de'>Deutsch</a>
              &nbsp;&nbsp;<a href='", $urlAct, "?lang=es'>Español</a>
              &nbsp;&nbsp;<a href='", $urlAct, "?lang=pt'>Português</a>
              </p>";
	echo $ahpPrioCalc->titles1['h2subTitle'];
	echo "<p class='entry-content'>",$ahpPrioCalc->info['intro'],"</p>";
	echo $ahpPrioCalc->info['selC'];
	$ahp->ahpHtmlGetNewNames($n, $ahp->header, $new, CRITMAX, $err_get);

	echo "\n<!-- DO COMPARISON -->\n";
	printf($ahpPrioCalc->titles1['h3Pwc'],"");
	printf($ahpPrioCalc->msg['nPwc'],$m_pc);
	echo $ahpPrioCalc->info['doPwc'];
	if( (float) $cr >= 0.1 )
		echo $ahpPrioCalc->info['adj'];
	printf( $ahpPrioCalc->info['pwcQ'], $ahp->header, "?");

	$hdl = sprintf($ahpPrioCalc->msg['pwcAB'],$ahp->header);
	$ahp->get_pair_comp($act, $submit, $err_post, $hdl, $pwc); 
	echo $ahpPrioCalc->info['scale'];

	if(!$err_post){
		echo "\n<!-- DISPLAY RESULT -->\n";
		echo $ahpPrioCalc->titles1['h3Res'];
		$ahp->showResult();

		// prepare graphic
		$dta = array_combine($ahp->criteria, $ahp->evm_evec);
		foreach($dta as $k=>$val){
			$data['nom'][$k] = round(100*$val,1);
		}
		foreach($ahp->evm_tol['min'] as $k=>$val){
			$data['min'][$k] = round(100 * $val,1);
			$data['max'][$k] = round(100 * $ahp->evm_tol['max'][$k],1);
		}
		$data = urlencode(serialize($data));
		// Graphic
		echo "<div class='ofl'>
				<div style='margin-left:auto;margin-right:auto;width:700px;'>";
		echo "<img src='ahp-group-graph.php?dta=$data' alt='Ahp-dia'>";
		echo "</div></div>";
	} 

	include 'views/ahpCalcMenu.html';
	echo "<p></p>";
	$webHtml->webHtmlFooter($version);
