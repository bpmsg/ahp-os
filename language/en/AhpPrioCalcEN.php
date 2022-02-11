<?php

class AhpPrioCalcEN
{
    public $wrd = array(
    "crit"	=>  "Criteria",
    "alt"		=>	"Alternatives"
);

    // Errors
    public $err = array(
    "pgm"					=>	"<br><span class='err'>Program Error</span>",
    "pwcInc"			=>	"<span class='err'>Pairwise comparisons not yet completed!</span>"
);

    // calc (priority calculator)
    public $titles1 = array(
    "pageTitle" 	=>	"AHP calculator - AHP-OS",
    "h1title" 		=>	"<h1>AHP Priority Calculator</h1>",
    "h2subTitle" 	=>	"<h2>AHP Criteria</h2>",
    "h3Pwc"				=>	"<h3>Pairwise Comparison <span class='var'>%s</span></h3>",
    "h3Res"				=>	"<h3 align='center'>Resulting Priorities</h3>"
);

    // hiercalc
    public $titles2 = array(
    "pageTitle" 	=>	"PWC Crit AHP-OS",
    "h1title" 		=>	"<h1>Pairwise Comparison AHP-OS</h1>",
    "h2subTitle" 	=>	"<h2>Evaluation of Criteria for <span class='var'>%s</span></h2>",
);

    // altcalc
    public $titles3 = array(
    "pageTitle" 	=>	"PWC Alt AHP-OS",
    "h1title" 		=>	"<h1>Pairwise Comparison AHP-OS</h1>",
    "h2subTitle" 	=>	"<h2>Evaluation of Alternatives for <span class='var'>%s</span></h2>",
    "h2alt"				=>	"<h2>Alternatives</h2>",
    "h3Mnu"				=>	"<h3>Alternative Menu</h3>",
    "h3tblA"			=>	"<h3>Project Structure</h3>",
    "h3Res"				=>	"<h3>Result for Alternatives</h3>",
    "h4Res"				=>	"<h4>Priorities and ranking</h4>"
);

    // calc1
    public $titles4 = array(
    "pageTitle" 	=>	"AHP Criteria",
    "h1title" 		=>	"<h1 class='ca' >AHP Criteria Names</h1>"
);

    // alt1
    public $titles5 = array(
    "pageTitle" 	=>	"AHP Alternatives",
    "h1title" 		=>	"<h1 class='ca' >AHP Alternative Names</h1>"
);

    // Messages
    public $msg = array(
    "nPwc"		=>	"<span class='msg'>%g pairwise comparison(s). </span>",
    "pwcAB"		=>	"A - wrt <span class='var'>%s</span> - or B?",
    "noPwc1"	=>	"<span class='msg'>Please complete all pairwise comparisons first. Click on ",
    "noPwc2"	=>	"<input type='button' value='Alternatives'> then ",
    "noPwc3"	=>	"<input class='btnr ' type='button' value='AHP'></span>",
    "tu"			=>	"Thank You for your participation!",
    "giUpd"		=>	"<span class='msg'> %g judgment(s) updated. </span>",
    "giIns"		=>	"<span class='msg'> %g judgment(s) inserted. </span>",
    "inpA"		=>	"<p class='ca' >Please fill out</p>"
);

    // Information
    public $info= array(
    "intro"		=>	"Select number and names of criteria, then start pairwise 
								comparisons to calculate priorities using
		 						the Analytic Hierarchy Process.",
    "pwcQ"		=>	"<p><span class='hl'>With respect to 
								<i><span class='var'>%s</span></i>, which criterion is more important,
								and how much more on a scale 1 to 9%s</span></p>",
    "pwcQA"		=>	"<p><span class='hl'>With respect to 
								<i><span class='var'>%s</span></i>, which alternative fits better or is 
								more preferrable, and how much more on a scale 1 to 9%s</span></p>",
    "selC"		=>	"Select number of criteria:",
    "scale"		=>	"<p style='font-size:small'>AHP Scale: 1- Equal Importance, 3- Moderate importance,
 								5- Strong importance, 7- Very strong importance, 9- Extreme importance 
 								(2,4,6,8 values in-between).</p>",
    "doPwc"		=>	"Please do the pairwise comparison of all criteria. When completed, 
								click <i>Check Consistency</i> to get the priorities.<br>",
    "doPwcA"	=>	"Please do the pairwise comparison of all alternatives to indicate, how good 
								they fulfill each criterion. Once finished, click <i>Check Consistency
								</i> to get the weights, and <i>Submit Priorities</i> to proceed. ",
    "doPwcA1"	=>	"<p>Compare alternatives with respect to criteria (click on AHP). 
								How good is the fit of alternatives with each criterion?</p>",
    "adj"			=>	"<p class='msg'>To improve consistency, slightly adjust highlighted 
								judgments by plus or minus one or two points in the scale.</p>",
    "inpAlt"	=>	"Here you can input the number and names of your alternatives.",
    "pSave"				=>	"<p>Click on <i>Save as project</i> to save the project with the defined 
								alternatives for alternative evaluation.</p>"
);

    // Menu and buttons
    public $mnu = array(
    "btnSbm"	=>	"Submit",
    "lgd1"		=>	"AHP Priority Calculator",
    "done"		=>	"Done",
    "next"		=>	"Next",
    "lgd2"		=>	"Alternative Menu",
    "btn1"		=>  "Save Judgments",
    "btn2"		=>	"Reset Alternatives",
    "btn3"		=>	"Save as Project"
);
}
