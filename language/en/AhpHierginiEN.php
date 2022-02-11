<?php

class AhpHierginiEN
{
// Errors
    public $err = array(
    "pExc"		=>	"Number of projects exceeded, data can not be saved. Cancel and delete some of your projects. ",
    "noSc"		=>	"Please provide a session code. ",
    "noName"	=>	"Please provide your name. ",
    "pwcCompl"=>	"Pairwise comparisons under <span class='var'>%s</span> completed.",
    "hDefP"		=>	"Hierarchy has no defined priorities. Project cannot be updated.",
    "unknw"		=>	"Unknown error - owner: %s retFlg: %g"
);

    public $titles = array(
    "pageTitle" =>	"AHP Session Input AHP-OS",
    "h1Title" 	=>	"<h1>AHP Session Input</h1>",
    "subTitle1" =>	"AHP-OS Participant's Input",
    "subTitle2"	=>	"Save/Update AHP Project",
    "subTitle3"	=>	"Pairwise Comparison Input ",
    "h3Pwc"			=>	"<h3>Pairwise Comparison <span class='var'>%s</span></h3>",
    "h3Res"			=>	"<h3 align='center'>Resulting Priorities</h3>",
    "h2siMnu"		=>	"<h2>AHP Session Input Menu</h2>"
);

    // Messages
    public $msg = array(
    "nProj"		=>	"New project, click \"Go\" to save",
    "pMod"		=>	"Existing project will be modified and overwritten!"
);

    // Information
    public $info= array(
    "intro"		=>	"<div class='entry-content'>
								<p style='text-align:justify;'>AHP-OS is an online tool to support rational decision making 
								based on the <i>Analytic Hiearchy Process</i> (AHP). 
								As selected participant kindly <b>enter your session code and name,
								work through the questionnaire and submit your input for group evaluation</b>. This will
								help to reflect your inputs in the final decision. Thank you!</p>
								</div>",
    "act1"		=>	"New project. Session code %s. ",
    "act2"		=>	"Update project. ",
    "act3"		=>	"Project has %g participant(s). ",
    "ok"			=>	"<p class='msg'>Ok. Click \"Go\" to continue</p>",
    "siSc"		=>	"Please provide your session code to participate in the AHP group session",
    "siNm1"		=>	"<a href='%s?logout'>Logout</a> as session chair to input another participant's name.",
    "siNm2"		=>	"Your name as it will be reflected in the group session (3 - 25 alpha num char).",
    "pName"		=>	"AHP Project Name:",
    "pStat"		=>	"Project Status:",
    "pDescr"	=>	"Project Short Description:",
    "descr"	=>	"</br><small>Text will be displayed to participants of the group session, 400 chars max. 
							You can use HTML tags, like &lt;em&gt; or &lt;font&gt; to emphazise or highlight text.</small>"
);

    // Menu and buttons
    public $mnu = array(
    "lgd1"		=>	"AHP Session Input",
    "lgd2"		=>	"Session Input Menu",
    "sc"			=>	"Session Code:",
    "nm"			=>	"Your Name:",
    "btn1"		=>	"Go",
    "btn2"		=>	"Check input",
    "btn3"		=>	"View group result",
    "btn4"		=>	"Reset",
    "btn5"		=>	"Cancel"
);
}
