<?php

class AhpNewsEN
{
    public $titles1    = array(
    'pageTitle'  =>    "AHP-OS News",
    'h1title'    =>    "<h1>AHP Online System - BPMSG</h1>",
    'h2welc'     =>    "<h2>Welcome %s!</h2>",
    'h3release'  =>    "<h3>AHP-OS Release %s (%s)</h3>",
    'h3news2'    =>    "<h3>AHP-OS in other languages</h3>",
    'h3don'      =>    "<h3>Your Donation</h3>"
);

    public $msg = array(
    'tu'        =>    "Thank You!",
    'cont'      =>    "Continue"
);

    public $info = array(
    'news0'     =>  "<p>We have modified the program allowing participants to <span class='hl'>revise their 
                    judgments</span> as long as the project status is \"open\". As a project owner you can 
                    open/close projects using <i>Toggle Project Status</i> in the \"AHP Project Menu\".</p>",
    'news1'     =>  "<p>This latest version of AHP-OS includes a <i>new feature</i> to analyze group decisions. Under 
                    <span class='hl'>Group Consensus Cluster Analysis </span> on the AHP-OS main page you can open the 
                    AHP Consensus page. The program tries to cluster a group of decision makers into smaller subgroups 
                    with higher consensus. For each pair of decision makers the similarity of priorities is calculated, 
                    using <span class='hl'>Shannon &alpha;- and &beta; entropy</span>. The analysis can be helpful, 
                    when among a group of
                    four or more participants the overall group consensus is low, but you want to see, whether you can
                    identify smaller subgroups of participants with higher consensus.</p>
                    <p>More Info <a href='https://bpmsg.com/group-consensus-cluster-analysis/' target='_blank'>here</a></p>",
    'news2'     =>  "<p>
                    We are still looking to find volunteers for a translation of all AHP-OS output into other 
                    languages. At the moment English, German, Spanish and Portugese is supported. If you are willing to support the program,
                    please contact me via the contact link at the bottom of this page.
                    </p>",
    'don'       =>  "<p>
                    Before you start: If you are an active user or like the program, please help with a donation to keep 
                    this website alive. 
                    I have running costs for web hosting, certificate, spam protection and maintenance, and want to keep  
                    AHP-OS free for all users. As a donor your account will be kept active without the request for 
                    reactivation, even if you don't access it for a period longer than 3 months.
                    </p>"
);
}
