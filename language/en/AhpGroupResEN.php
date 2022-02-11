<?php

class AhpGroupResEN
{
    // for ahp-group.php AND  ahp-g-input.php
    /* Titles and headings */
    public $titles = array(
    "pageTitle1"	=>	"AHP Group Results - AHP-OS",
    "h1title1"		=>	"<h1>AHP Group Results</h1>",
    "h2subTitle1"	=>	"<h2>Project Result Data</h2>",

    "pageTitle2"	=>	"AHP Project Input data - AHP-OS",
    "h1title2"		=>	"<h1>AHP Group Results</h1>",
    "h2subTitle2"	=>	"<h2>Project Input Data</h2>",

    "h2hier"			=>	"<h2>Hierarchy with Consolidated Priorities</h2>",
    "h2consP"			=>	"<h2>Consolidated Global Priorities</h2>",
    "h2consA"			=>	"<h2>Consolidated Weights of Alternatives</h2>",
    "h2sens"			=>	"<h2>Sensitivity Analysis</h2>",
    "h3wUncrt"		=>	"<h3>Weight Uncertainties</h3>",
    "h2nodes"			=>	"\n<h2>Breakdown by Nodes</h2>",
    "h4wCons"			=>	"<h4>Consolidated Priorities</h4>",
    "h4mCons"			=>	"<h4>Consolidated Decision Matrix</h4>",
    "h4part"			=>	"<h4>Group Result and Priorities of Individual Participants</h4>",
    "h2pGlob"			=>	"<h2>Global Priorities</h2>",
    "h3rob"				=>	"<h3>Robustness</h3>",
    "h2alt"				=>	"<h2>Alternatives by Participants</h2>",
    "h2crit"			=>	"<h2>Breakdown by Criteria</h2>",
    "h4group"			=>	"<h4>Group Result and Priorities of Individual Participants</h4>",
    "h2grMenu"		=>	"<h2>Group Result Menu</h2>",

    "h2dm"				=>	"<h2>Pairwise Comparison Decision Matrices</h2>",
    "h4dm"				=>	"<h4>Decision Matrix</h4>",
    "h4crit"			=>	"<h4>Criterion: <span class='res'>%s</span></h4>",
    "h3part"			=>	"<h3>Participant <span class='res'>%s</span></h3>",
    "h4nd"				=>	"<h4>Node: <span class='res'>%s</span></h4>"
);

    /* Individual words */
    public $wrd	 = array(
    "crit"			=>	"criteria",
    "alt"				=>	"alternatives"
);

    /* Result output */
    public $res  = array(
    "cr"					=>	"Consistency Ratio CR: <span class='res'>%02.1f%%</span>",
    "consens1"		=>	"<p>AHP group consensus: <span class='res'>%02.1f%%</span> ",
    "consens2"		=>	" Criterion: <span class='res'>%s</span> - CR: <span class='res'>%g%%</span>",
    "gCons"				=>	" - AHP group consensus: <span class='res'>%02.1f%%</span> ",
    "consens4"		=>	"<p><small>Consensus in evaluating the alternatives wrt to the criterion 
										<span class='res'>%s</span>: <span class='res'>%02.1f%%</span>",
    "nodeCr"			=>	" Node: <span class='res'>%s</span> - CR: <span class='res'>%g%%</span>",
    "ovlp"				=>	"Following %s are without overlap:<br>",
    "ovlpNo"			=>	"No overlap of %s within uncertainties",
    "ovlpAll"			=>	"All %s are overlapping wthin uncertainties.",
    "ovlpGrp"			=>	"Following group(s) of %s are overlapping within uncertainties:<br>",
    "rtrb"				=>	"<p class='msg'>1. The solution for the top alternative <span class='res'>%s</span> is robust.<br>",
    "rt10"				=>	"<p class='msg'>1. The <i>percent-top</i> critical criterion is <span class='res'>%s</span>: 
										a change from <span class='res'>%g%%</span> by absolute <span class='res'>%g%%</span> will change 
										the ranking between alternatives <span class='res'>%s</span> and <span class='res'>%s</span>.<br>",
    "rt11"				=>	"2. The <i>percent-any</i> critical criterion is <span class='res'>%s</span>: 
										a change from <span class='res'>%g%%</span> by absolute <span class='res'>%g %%</span> 
										will change the ranking between alternatives <span class='res'>%s</span> and 
										<span class='res'>%s</span>.<br>",
    "rt11s"				=>	"2. The <i>percent-any</i> critical criterion is the same as above.<br>",
    "rt20"				=>	"3. The <i>percent-any</i> critical performance measure is for alternative <span class='res'>%s</span> 
										under criterion <span class='res'>%s</span>. A change from <span class='res'>%g%%</span> by absolute 
										<span class='res'>%g%%</span> will change the ranking between <span class='res'>%s</span> and 
										<span class='res'>%s</span>."
    );

    /* Messages */
    public $msg  = array(
    "scaleSel"		=>	"<p class='msg'>Selected scale: <span class ='hl'>%s</span></p>",
    "wMethod"			=>	"<p>Method: <span class ='hl'>Weighted product method (WPM)</span></p>",
    "rMethod"			=>	"<p>Random variation: <span class ='hl'>based on standard deviation</span></p>",
    "mcVar"				=>	"<p class='msg'>Estimated weight uncertainties based on <span class='res'>%g</span> judgment variations.",
    "pSel"				=>	"<p>Selected participants: <span class='res'>%s</span></p>",
    "noSens"			=>	"<p class='msg'>No sensitivity analysis possible.</p>",
    "noPwc1"			=>	"<span class='msg'> - No pairwise comparison data.</span>",
    "noPwc2"			=>	"<p class='msg'>No pairwise comparison data from participants</p>",
    "noPwc3"			=>	" - No pairwise comparison data from participants.",
    "noPwc4"			=>	"<p>Warning: <span class='msg'>%s</span></p>",
    "noRt"				=>	"<p class='msg'>No robustness test possible.</p>",
    "pCnt"				=>	"Aggregation of individual judgments for %g Participant(s)",
    "nlgin"				=>	"<p class='msg'>You need to be a registered user and login to handle projects.</p>"
);

    /* Errors */
    public $err  = array(
    "incompl"			=>	"<p class='err'>Project evaluation is incomplete</p>",
    "consens0"		=>	"<p>AHP group consensus: <span class='err'>n/a</span>",
    "consens1"		=>	" - Consensus <span class='res err'>n/a</span>",
    "consens2"		=>	"<p><small>in evaluating the alternatives wrt to the criterion <span class='res err'>n/a</span>"
);

    /* Information output */
    public $info = array(
    "sensDl"			=>	"<p><small>Note: complete analysis via download.</small></p>",
    "cpbd"				=>	"Consolidated preferences for alternatives with respect to each criterion",
    "pwcfor"			=>	"Pairwise comparisons for: <br>"
);

    /* Menu and buttons */
    public $mnu = array(
    "btnNdD"	=> 	"<p><button href='#%s' class='nav-toggle'>Details</button>",
    "lgd1"		=>	"Group Result Menu",
    "lbl4"		=>	"dec. comma",
    "btn1"		=>	"Refresh",
    "btn2"		=>	"View Input Data",
    "btn3"		=> 	"Download (.csv)",
    "btn4"		=>	"Define Alternatives",
    "btn5"		=>	"Done",
    "lgd2"		=>	"Project Input Data Menu"
);
}
