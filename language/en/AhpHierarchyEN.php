<?php
class AhpHierarchyEN {

public $titles = array(
	"pageTitle" 	=>	"AHP hierarchy - AHP-OS",
	"h1title" 		=>	"<h1>AHP Hierarchy</h1>",
	"h2subTitle"	=>	"<h2>AHP-OS Decision Hierarchy</h2>",
	"h4pDescr"		=>	"<h4>Project description</h4>",
	"h3hInfo"			=>	"\n<h3>Hiearchy Info</h3>",
	"h3Proj"			=>	"<h3>Project: <span class= 'var'>%s</span></h3>",
	"h2ieHier"		=>	"<h2>Input/Edit Hierarchy</h2>"
);
public $err = array(
	"giH"			=>	"Error in hierarchy input"
);
public $msg = array(
	"lgin"		=>	"<span class='msg'>For full functionality please register and login.</span>",
	"pInp"		=>	"<p class='msg'>Input for project <span class='var'>%s</span></p>",	
	"pMod"		=>	"<p class='msg'>Modification of project <span class='var'>%s</span></p>",
	"pNew"		=>	"<p class='msg'>New project</p>",
	"hMode"		=>	"<p class='msg'>Mode: Hierarchy evaluation</p>",
	"aMode"		=>	"<p class='msg'>Mode: Alternative evaluation <span class='var'>%g</span> alternatives</p>",
	"giUpd"		=>	"<span class='msg'> %g judgment(s) updated. </span>",
	"giIns"		=>	"<span class='msg'> %g judgment(s) inserted. </span>",
	"giTu"		=>	"Thank You for your participation!",
	"giNcmpl"	=>	"Pairwise comparisons not yet completed!",
	"giNds"		=>	"No data stored. ",
	"giPcmpl"	=>	"Please complete all pairwise comparisons first. "
);

public $info = array(
	"intro"		=>	"<div class='entry-content'><p style='text-align:justify;'>
								Define a decision hierarchy of criteria and calculate their weights 
								based on pairwise comparisons using the Analytic Hierarchy Process AHP. 
								In a next step you then define a set of alternatives and evaluate them 
								with respect to your list of criteria to find the most preferrable 
								alternative and solve your decision problem.
								</p><p style='text-align:justify;'>
								For a simple calculation of priorities based on pairwise comparisons 
								you can use the <a href='ahp-calc.php'>AHP priority calculator</a>. 
								If you like the tool and find it useful, click the <i>like</i> button 
								at the bottom of the page. Thank you!</p></div>",
	"clkH"		=>	"Click on <input type='button' class='btnr' value='AHP'> to complete pairwise comparisons. ",
	"clkA"		=>	"Click on <b>Alternatives</b>, then <b>AHP</b> to complete pairwise comparisons.",
	"clkS"		=>	"Click on <input type='button' value='Save judgments'> to finalize and save your judgments.",
	"txtfld"	=>	"Input or edit text in the text area below, then submit. (See <a href='ahp-examples.php'>examples</a>)",
	"synHelp"	=>	"<br><span style='text-align:justify; font-size:small;'>
								In the text input area above you can define a new hierarchy. 
								Nodes are followed by a <b><i>colon</i></b>, leafs are separated by <b><i>comma</i></b>, 
								and each branch has to be terminated by a <b><i>semicolon</i></b>. 
								Tilde character (~) is discarded. Names for categories and sub-categories need to be unique. No numbers 
								are allowed as category names, <i>e.g.</i> use \"100 $\" instead of \"100\". A category cannot have a 
								single sub-category. By default, all priorities are set equally to sum-up to 100% in each category 
								or sub-category. Note: input is case-sensitive.</span>",
	"nlg"			=>	"<p class='hl'>As registered user you can download priorities and save the defined hierarchy as project.</p>",
	"lgi"			=>	"<p class='msg'>For AHP priority evaluation <i>Save/Update</i> and open from your project page, to start pairwise comparisons. 
								For alternative evaluation use a hierarchy with evaluated or defined priorities to define alternative names and 
								<i>Save</i> from the alternative menu.</p>",
	"giPcmpl"	=>	"Click on <input type='button' value='Alternatives'> then <input class='btnr ' type='button' value='AHP'>"
);

public $mnu	= array(
	"lgd11"		=>	"Hierarchy Input Menu",
	"btn11"		=>	"Submit",
	"btn12"		=>	"Save/Update",
	"btn13"		=>	"Download (.csv)",
	"lbl11"		=>	"dec. comma",
	"btn14"		=>	"Reset Priorities",
	"btn15"		=>	"Reset All",
	"btn16"		=>	"Done",
	"lgd21"		=>	"Group Input Menu",
	"btn21"		=>	"Save Judgments",
	"btn22"		=>	"View Group Result",	
);

}