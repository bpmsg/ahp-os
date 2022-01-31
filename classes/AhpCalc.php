<?php
/* Analytic Hierarchy Process base AHP class 2014-01-02
 *
 * $LastChangedDate$
 * $Rev$
 *
 * Solves eigenvector of decision matrix based on pairwise
 * comparisons of n criteria
 *
 * @author Klaus D. Goepel
 * @copyright 2014 Klaus D. Goepel
 * @version 2014-01-09 
 * @version 2017-03-29 confirmation when default pwc added in get_pair_comp
 * @version 2017-06-02 added getPwcFromMatrix($dm)
 * @version 2017-07-13 removed scale function and scale parameter
 * @version 2017-09-30 reworked get pairw comp with formatting
 * @version 2018-08-29 line 587 added "&& $m_pc > 1" no confirm for 2 crit/alt
 * @version 2018-08-31 get_inconsistency exclude >9 and <1/9
 * @version 2019-05-08 get_criteria, get_pwc, get_emv_evec, get_emv_eval get_dm taken out
 *                     set_evm_evec private, code cleanup: remove function parameters
 * @version 2019-05-10 set_evm paramter $n removed
 * @version 2019-05-13 EVM error calculation setEvmErrors() added
 *
 * public function get_npc($n)
 * public function get_n($n)
 * public function setNamesFromGet( &$n, &$t, &$names, $nmax, $nameDef)
 * public function set_pwc($n)
 * public function set_pwc_def($n)
 * public function getMatrixFromPwc($pwc)
 * 
 * public function set_evm($pwc)
 * public function get_evm()
 * public function getUrlCode($myUrl, $n, $title, $crit)
 * public function get_pair_comp($act, $submit, $errPost, $compTxt, $pwc)
 *
 * private function setDms($para)
 * private function setDm()
 * private function getVsqNorm($v1,$v2)
 * private function vScale($v,$s)
 * private function set_evm_evec()
 * private function setEvmEval()
 * private function get_inconsistency()
 * private function setEvmErrors()
 * private function setAlonsoCr()
 * private function setSaatyCr()
 * private function getPwcFromMatrix($dm)
 * private function mb_rawurlencode($url)
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

class AhpCalc {
/* Class Constants */
	const ERR = 1.E-7;		/** accepted error for eigenvalue calculation */
	const ITMAX = 20;		/** max. number of iterations for power method */
	const CRTH = 0.1;		/** threshold for consistency ratio */
	const NMIN = 2;		/** min number of criteria/alternatives */

/** AHP properties */
	public $header;			/** header: e.g. goal */
	public $n, $npc; 		/** number of criteria & pairwise comparisons */
	public $criteria = array();	/** criteria names */
	public $pwc = array(); 	/** pairwise comparison array */
	public $dm = array();		/** decision matrix */
	public $evm_evec, $evm_eval; 	/** eigenvector and eigenvalue (Saaty) */
	public $evm_err, $evm_tol;	/** absolute error for eigenvector method */
	public $evm_it, $evm_dt; 	/** no of iterations and delta for power method */
	public $cr_ahp, $cr_alo; 	/** consistency ratio Saaty and Alonso */

	private $dm_string = array();
	public $ahpCalcTxt;				// language class

/** Methods */

/** Initialize: n, npc, default header and criteria names */
public function __construct($n){
	mb_internal_encoding('UTF-8');
	global $lang;
	$class = get_class() . $lang;
	$this->ahpCalcTxt = new $class;

	$this->n = $n;
	$this->npc = $this->get_npc($n);
	$this->header = 'AHP calculator';
	for ( $i=0; $i < $n; $i++ )
		$this->criteria[$i] = 'Crit-' . ($i+1);
	$this->set_pwc_def($n);
}


/** Calculation of number of pairwise comparisons as function of number of criteria */
public function get_npc($n){
	return $this->npc = ($n*$n-$n)/2;
}


/** Calculation of number of criteria as function of number of pairwise comparisons */
public function get_n($n){
	return( (int)(0.5+sqrt(2*$n+0.25)));
}


/** Read n, title and names from $_GET parameters or set to default if not given
* 
* @param int $n              number of names
* @param string $t           title
* @param array string $names names
* @param int $nmax           max. of n
* @param strong $nameDef     default string for names
* @return int 0 ok., bit 1: n reset to default, bit 2: number of alternatives does not match n
* @uses const NMLEN          max length of names
*/
public function setNamesFromGet( &$n, &$t, &$names, $nmax, $nameDef){
	if(!defined("WLMAX")) 
		define("WLMAX", 25);

	$nOpt = array( 'options' => array('min_range' => self::NMIN, 'max_range' =>$nmax,));
	$err=0;

	// first set default n and alternative names
	$t = "AHP priorities";
	for ( $i=0; $i < $n; $i++ )
		$names[$i] = $nameDef . ($i+1);

	if (filter_has_var(INPUT_GET, 'c') || filter_has_var(INPUT_GET, 'n') || filter_has_var(INPUT_GET, 't') ){
		// get complete parameter string
		$para = filter_input_array(INPUT_GET,FILTER_SANITIZE_STRING);
		// check for n
		if ( isset($para['n']) )
			if (filter_var( $para['n'] ,FILTER_VALIDATE_INT, $nOpt )){
				$n = $para['n'];
				for ( $i=0; $i < $n; $i++ )
					$names[$i] = $nameDef . ($i+1);
			} else {
				$n = self::NMIN;
				$err = ($err | 1);
			}
		// check for names and replace default names by given names
		if ( isset($para['c']) && !empty($para['c']) ){
			if ( count($para['c']) != $n)
				$err = ($err | 2);
			for ($i=0; $i<$n; $i++){
			if(isset($para['c'][$i]) && $para['c'][$i]!="")
					$names[$i] = mb_substr($para['c'][$i],0, WLMAX);
				else {
					$names[$i] = $nameDef . ($i+1);
					$err = ($err | 2);
				}		
			}
		}
		// check for title
		if ( isset($para['t']) ){
			$t = mb_substr($para['t'],0, WLMAX);
		}
	}
	return($err);
}


/* Set pairwise comparison input and from $_POST parameters 
 * @return array pwc (A, Intensity) or int 1 (start pwc), int 2 (error in parameters)
 * called from ahp_calc, ahp_altcalc and ahp-hiercalc
 * @todo $n not in use
 */
public function set_pwc($n) {
	$args = array(
		'A'		=> array('filter' => FILTER_VALIDATE_INT,
					  'flags' => FILTER_REQUIRE_ARRAY,
					  'options'=> array('min_range' => 0, 'max_range' => 1,)
				   ),
		'Intense'	=> array('filter' => FILTER_VALIDATE_INT,
					  'flags' => FILTER_REQUIRE_ARRAY,
					  'options'=> array('min_range' => 1, 'max_range' => 9,)
				   )
		);
	$para = filter_input_array(INPUT_POST, $args);

	if($para == NULL ){
		// variables not set - start pairwise comparison
		return 1;
	} elseif ($para == false || count($para, COUNT_RECURSIVE) != 2* $this->npc + 2){
		// variables not valid - error
		return 2;
	} else {
		// ok - set pwc
		$this->pwc = $para;
		return 0;
	}
}


/* Fills pairwise comparison array pwc with default values
 * @param int $n number of criteria
 * @return void
 */
public function set_pwc_def($n){
	$this->n = $n;
	$this->npc = $this->get_npc($n);
	for($i=0; $i < $this->npc; $i++){
		$this->pwc['A'][$i] = 0;
		$this->pwc['Intense'][$i] =1;
	}
	return $this->pwc;
}


/* Get decision matrix from pwc array
 * @return array $dm decision matrix nxn, 0 on error
 * todo: is defined as private function in ahpDbClass too!
 */
public function getMatrixFromPwc($pwc){
$dm = array();
$dmstring = array();
	$pwcCnt = count($pwc['A']);
	if (count($pwc['Intense']) != $pwcCnt)
		return 0;
	$n = $this->get_n($pwcCnt);
	$dmstring = array_fill(0,$pwcCnt,0.);
	$m = 0;
	for($i = 0; $i < $n-1; $i++){
		for ($j= $i+1; $j<$n; $j++)	{
			$dmstring[$m] = ( $pwc['A'][$m] == 0 ? $pwc['Intense'][$m] : 1/$pwc['Intense'][$m]);
			$m++;
		}
	}
	for($i = 0; $i < $n; $i++)
		$dm[] = array_fill(0,$n,1.);
	$m = 0;
	for ($i = 0; $i< $n-1; $i++){
		for ($j = $i+1; $j < $n; $j++){
			$dm[$i][$j] = (float) $dmstring[$m];
			$dm[$j][$i] = (float) 1/$dmstring[$m];
			$m++;
		}
	}
	// Fill diagonal
	for ($i = 0; $i<$n; $i++)
		$dm[$i][$i] = 1.00;
	return $this->dm = $dm;
}


/* Get pwc from decision matrix */
private function getPwcFromMatrix($dm){
	$pwc = array();
	$n = count($dm[0]);
	if (count($dm[0]) < 2)
		return array();
	$m = 0;
	for($i = 0; $i < $n ; $i++){
		for ($j= $i+$m+1; $j < $n; $j++)	{
			$pwc['A'][] = ( $dm[$i][$j] >= 1 ? 0 : 1 );
			$pwc['Intense'][] = ( $dm[$i][$j] > 1 ? $dm[$i][$j] : 1/$dm[$i][$j]);
		}
	}
	return($pwc);
}


/* Function to fill the npc pairwise comparisons with values
 * @param  array $para contains pairwise comparisons ['A'],['Intense']
 * $dm_string linear array with npc pairwise comparisons of values (1/9 ... 1 ... 9)
 */
private function setDms($para){ // Dms = decision matrix string
	if ( $para != "" && is_array($para)){ 
		// form was submitted
		$m = 0;
		for($i = 0; $i < $this->n-1 ; $i++){
			for ($j= $i+1; $j<$this->n; $j++)	{
				$this->dm_string[$m] = ($para['A'][$m]== 0 ? $para['Intense'][$m] : 1/$para['Intense'][$m]);
				$m++;
			}
		}
	} else {
		// set default all comparisons to 1
		$this->dm_string = array_fill(0, $this->npc, 1);
	}
}


/* Fill decision matrix using array of pairwise comparisons
 * @param array $dmS Array with result of pairwise comparisons
 */
private function setDm(){
	$m = 0;
	for ( $i = 0; $i < $this->n-1; $i++){
		for ($j = $i+1; $j < $this->n; $j++){
			$this->dm[$i][$j] = (float) $this->dm_string[$m];
			$this->dm[$j][$i] = (float) 1/$this->dm_string[$m];
			$m++;
		}
	}
	// Fill diagonal
	for ($i = 0; $i< $this->n; $i++)
		$this->dm[$i][$i] = 1.00;
}


/* Calculates squared vector distance d of two vectors
 * @param array $v1 vector 1
 * @param array $v2 vector 2
 * @return float $d squared vector distance 
 */
private function getVsqNorm($v1,$v2){
	if(count($v1) != count($v2))
		echo "<span class='err'>error in v_norm - vectors have different dimensions</span>";
	$d = 0.0;
	foreach ($v1 as $i => $val1){
		$t = pow(($val1 - $v2[$i]),2);
		$d += $t;
	}
	return($d);
}


/* Vector scaling
 * @param array $v vector
 * @param float $s scaling factor
 * @return $v scaled vector s*v
 */
private function vScale($v,$s){
	if( count($v) == 0 || !isset($s))
		echo "<span class='err'>Error input parameter vScale</span>";
	foreach ($v as $key => $val)
		$v[$key] = $val/$s;
	return($v);
}


/* Find the dominant eigenvalue using power method */
private function set_evm_evec(){
	// getting start vector $v_gi
	for ($i=0; $i< $this->n; $i++){
		$v_gi[$i] = array_sum($this->dm[$i])/$this->n ;
	}
	// scaling
	$v_si = $this->vScale($v_gi,max($v_gi));
	// Iteration
	for ($it = 1; $it< self::ITMAX; $it++){
		for ($i=0; $i < $this->n; $i++){
			$v_it[$i]=0.;
			for ($j=0; $j< $this->n; $j++){
				$v_it[$i] += $this->dm[$i][$j] * $v_si[$j];
			}
		}
		$v_it = $this->vScale($v_it,max($v_it));
		$delta = $this->getVsqNorm($v_it, $v_si);
		if( $delta < self::ERR) break;
		$v_si = $v_it;
	}
	$this->evm_it =$it;
	$this->evm_dt = $delta;
	// Normalize
	$v_n = array_sum($v_it);
	$v_it = $this->vScale($v_it,$v_n);
	$this->evm_evec = $v_it;
}


/* Calculates Eigenvalue from Eigenvector */
private function setEvmEval(){
	$col_sum = array_fill(0,$this->n,0.);
	for ($j = 0; $j < $this->n; $j++){
		for ($i = 0; $i< $this->n; $i++)
			$col_sum[$j] += $this->dm[$i][$j];
	}
	for ($i = 0; $i < $this->n; $i++){
		$col_sum[$i] *= $this->evm_evec[$i];
	}
	$this->evm_eval = array_sum($col_sum);
}


/* Get proposal of consistent judgments for top 3 inconsistencies
 * @return array with top 3 inconsistencies, value A/B n as proposal for consistent judgment
 */
private function get_inconsistency(){
	$m = 0;
	for ($i=0; $i< $this->n-1; $i++){
		for ($j= $i+1; $j< $this->n; $j++){
			$es = $this->evm_evec[$j]/$this->evm_evec[$i];
			$cs = $this->dm[$i][$j] * $es;
			$de_s[$m] = ($es>=1. ?  min(9, round($es,0)) . " (B)" : min(9,round(1/$es,0)) . " (A)" );
			$cm_s[$m] = ( $cs >=1. ? $cs : 1/$cs);
			$m++;
		}
	}
	$sr = arsort($cm_s, SORT_NUMERIC);
	$cm_i = array_keys($cm_s);
	$cm_i = array_fill_keys($cm_i,1);
	foreach ($cm_i as $k => $val)
		$cm_i[$k] = $de_s[$k];
	$cm_i = array_slice($cm_i,0,3,true); // top three
	return($cm_i);	
}


/* Calculation of absolute error for eigenvector method 
 * to be called after ev calculation
 */
private function setEvmErrors(){
	$nlm = $this->n / $this->evm_eval;
	$sqs = 0.;
	for( $i=0; $i< $this->n; $i++){
		for( $k=0; $k < $this->n; $k++){
			$t = $this->dm[$i][$k] * $this->evm_evec[$k] * $nlm - $this->evm_evec[$i];
			$sqs += $t * $t;
		}
		$this->evm_err[$i] = sqrt($sqs/($this->n -1));
		$sqs = 0.;
	}
	$this->evm_tol['min'] = array_fill(0, $this->n, 0);
	$this->evm_tol['max'] = array_fill(0, $this->n, 0);
	for( $i=0; $i < $this->n; $i++){
		$this->evm_tol['min'][$i] = $this->evm_evec[$i] - $this->evm_err[$i];
		$this->evm_tol['max'][$i] = $this->evm_evec[$i] + $this->evm_err[$i];
	}
}


/* Calculate Alonso CR */
private function setAlonsoCr(){
	$this->cr_alo = ($this->evm_eval - $this->n)/( (2.7699 * $this->n - 4.3513) - $this->n);
}


/* Calculate Saaty CR */
private function setSaatyCr(){
	$ri = array( 3 => 0.57, 4 => 0.9, 5 => 1.12 ,6 => 1.24, 7 => 1.32, 8=> 1.41, 9=> 1.45, 10=> 1.49);
	if($this->n > 2 && $this->n <11){ // only valid for n = 3 to n = 10 
		$ci = ($this->evm_eval-$this->n)/($this->n - 1);
		$this->cr_ahp = $ci/$ri[$this->n];
	} else { // use Alonso approximation
		$this->cr_ahp = $this->setAlonsoCr();
	}
}


/* Solve principal eigenvalue from pairwise comparison
 * set decision matrix, eigen vector, eigen value and cr
 * @param int $n Number of criteria
 * @param array $pwc assosiative array with pairwise comparisons
 * @return void
 * @todo: parameter $n unnecessary
 */
public function set_evm($pwc){
	$this->setDms($pwc); // Convert pwc to float values
	$this->setDm(); // Fill decision matrix
	$this->set_evm_evec(); // Solve dominant Eigenvector (normalized)
	$this->setEvmEval(); // Calculation of eigenvalue and consistency ratio	
	$this->setEvmErrors(); // calculate errors of weights
	$this->setAlonsoCr(); // calculate CR using Alonso linear fit for RI
	$this->setSaatyCr(); // calculate Saaty CR
}


/* Get results of eigen value method */
public function get_evm(){
$evm_res = array();
	$evm_res['evm_evec'] = $this->evm_evec;
	$evm_res['evm_err'] = $this->evm_err;
	$evm_res['evm_eval'] = $this->evm_eval;
	$evm_res['cr'] = $this->cr_alo;
return $evm_res;
}


/* Url parameter encoding for multi byte strings */
private function mb_rawurlencode($url){
	$encoded='';
	$length=mb_strlen($url);
	for($i=0; $i<$length; $i++){
		$encoded.='%'.wordwrap(bin2hex(mb_substr($url,$i,1)),2,'%',true);
	}
	return $encoded;
}


/* Gets url string for form action calculate
 * @return string $url URL with added parameter string
 */
public function getUrlCode($myUrl, $n, $title, $crit){
	$as=""; $url="";
	if(is_array($crit) && !empty($crit)){
		for($i=0; $i<$n; $i++)
			$as .= "&c[" . $i . "]=" . urlencode($crit[$i]);
	}
	$as = "&t=" . urlencode($title) . $as;
	$url .= "?n=" . $n . $as;
	$url = $myUrl . $url;
	return $url;
}


/** Webform and HTML display for pairwise comparisons 
* Input: post parameter array, array of indices of pairwise comparisons with highest inconsistency
* to mark the corresponding rows 
*
* @param string $act action for html form
* @parma array $submit value = ['txt'] name = ['val'] submit button in form
* @param string $err_post 1 = start comparison, 2 = input error
* @param array $pwc containing pairwise comparisons
* @uses $this->get_inconsistency to highlight inconsistent judgments
* @return $errpost
*/
public function get_pair_comp($act, $submit, $errPost, $compTxt, $pwc){

	$rb_style = "display:inline-block;text-align:center;color:grey;";
	$rb_hl =    "class='rbhl'"; // highlight radio button
	$crok = false;
	if( $compTxt=="")	$compTxt = $this->ahpCalc->info['pwcAB'];
	if( ! empty($this->evm_evec)){ 
		$cs_i = $this->get_inconsistency();
		if (is_array($cs_i)) 
			$cs_k = array_keys($cs_i);
	}
	if ( $this->cr_alo <= self::CRTH)
		$crok = true;
	echo "\n<script src='js/btnred.js'></script>";
	echo "\n<form method='POST' action='$act'><div class='ofl'>
	 <table id='cTbl'>";
	 printf($this->ahpCalcTxt->tbl['cTblTh'],$compTxt);
	 	echo "\n<tbody>";
	$mRow = 0; // counter for pairwise comparisons, each displayed in one row
	for($i = 0; $i < $this->n - 1 ; $i++){
		for ($j= $i+1; $j<$this->n; $j++){
		$pa_rbs = ""; $pb_rbs = ""; $pi_rbs=""; $proposal="";
			$mstyle=" ";
		// if CR > threshold: mark rows with highest inconsistency
			if( !$crok){
				switch ($mRow){
					case $cs_k[0]:
						$mstyle = "class='col1'";
						$proposal = $cs_i[$cs_k[0]];
						break;
					case $cs_k[1]:
						$mstyle = "class='col2'";	
						$proposal = $cs_i[$cs_k[1]];
						break;
					case $cs_k[2]:
						$mstyle = "class='col3'";
						$proposal = $cs_i[$cs_k[2]];
						break;
				}
				$pi_rbs = ltrim($proposal,"(AB)");
				$pa_rbs = (mb_substr($proposal,-2,1)== "A" ? $rb_hl : "") ;
				$pb_rbs = (mb_substr($proposal,-2,1)== "B" ? $rb_hl : "") ;
			}
			echo  "\n<tr>";
			echo "<td class='ca' $mstyle>", $mRow+1, "</td>";
			// A
			echo "<td>";
			echo "<span $pa_rbs><input class='onclk1' type='radio' name='A[", $mRow, "]' value='0'", 
					($pwc['A'][$mRow] == 0 ? " checked" : ""), "/>","</span>",
					"<label> ", $this->criteria[$i], "</label></td>";
			// B
			echo	"<td>";
			echo "<span $pb_rbs ><input class='onclk1' type='radio' name='A[", $mRow, "]' value='1'",
					($pwc['A'][$mRow] == 1 ? " checked" : ""), ">","</span>", 
				  "<label>" . $this->criteria[$j] . "</label></td>";
			// Intensity 1
			echo "<td class='ac' style='font-size:smaller;'>",
				"<span ",	($pi_rbs == 1 ? $rb_hl : ""), ">",
				"<input type='radio' class='onclk1' name='Intense[".$mRow."]' value='1'",($pwc['Intense'][$mRow] == 1 ? ' checked' : ''), ">",
				"<label>1</label></span></td>";
			echo "<td class='ca' style='min-width:250px;font-size:smaller;' >";
			// Intensities 2 to 9
				for ($rb=2; $rb<10; $rb++){
					echo  "<span ",($pi_rbs == $rb ? $rb_hl : ""), ">",
					"<input type='radio' class='onclk1' name='Intense[", $mRow, "]' value='$rb'",($pwc['Intense'][$mRow] == $rb ? ' checked' : ''), ">",
					"<label>$rb</label></span>";
				}
			echo "</td>";

			if(isset($proposal)){
				unset($proposal);
			}
			echo "</tr>";
			unset($pi_rbs,$pa_rbs,$pb_rbs);
			// every 10th row a blank row
			$mRow++;
		}
		echo "\n<tr><td colspan='5'></td></tr>";
	}
	echo "\n<tr><td class='sm' colspan='5'> CR = <span class='res'>", round($this->cr_alo * 100,1), "% </span>";
	// 	display result depending message
	switch ($errPost) {
		case 1:
			echo $this->ahpCalcTxt->msg['sPwc']; break; // start pwc
		case 2:
			echo $this->ahpCalcTxt->err['ePwc']; break; // eror
		default:
			echo ($crok ? 
				$this->ahpCalcTxt->msg['ok'] : $this->ahpCalcTxt->err['adjPwc']);
	}
	echo "</td></tr>";

	// Calculation and submission
	echo "<tr><td colspan='2' class='la'>";
	printf($this->ahpCalcTxt->mnu['btnChk'], ($crok ? "" : "class='btnr'"));
	echo "</td>";
	echo "<td colspan='2'>";
	echo "<td class='ca'>";
	if( $errPost === 0 && is_array($submit)){
		$cfm = false;
		if( $submit['var'] == "calc"){
			// check default pwc		
			if( array_sum($this->pwc['Intense']) == $this->npc && $this->npc > 1 )
				$cfm = true;
		}
		echo "<div style='display:inline;'>";
		printf( $this->ahpCalcTxt->mnu['btnSbm'], $submit['txt'], $submit['var'],
			($crok ? " class='btnr'" : ""),($cfm ? " onclick='return cfmdef()'" : "") ); 
		echo 	"<script src='js/cfmdef.js' ></script>";
		if( $submit['var'] == "download")
			echo "&nbsp;<input type='checkbox' name='csv' value='0' ><small>", $this->ahpCalcTxt->mnu['btnDl'], "</small>";
	}
	echo	"</td></tr>";
	echo "\n</tbody>\n</table></div>\n</form>";
	return $errPost;
}

} // end class ahp
