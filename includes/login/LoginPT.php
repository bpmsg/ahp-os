<?php

/* used for both login and registration */

class LoginPT
{
    public $titles = array(
    "h1edit"	=>	"Edit your credentials",
    "h2info"	=>	"Account Information",
    "h2act"		=>	"Latest account activities",
    "h2lgin"	=>	"Please login",
    "h1reg"		=>	"User Registration",
    "h2reg"		=>	"Registration Form",
    "h1pwR"		=>	"Reset Password"
);

    public $err  = array(
    "aNact"		=>	"Your account is not activated yet. Please click on the confirm link in the mail.",
    "dbCon"		=>	"Database connection problem.",
    "emlE"		=>	"Email cannot be empty",
    "emlL"		=>	"Email cannot be longer than 64 characters",
    "emlD"		=>	"Sorry, that email address is the same as your current one. Please choose another one.",
    "emlI"		=>	"Your email address is not in a valid email format",
    "emlR"		=>	"This email address is already registered. Please use the \"I forgot my password\" page if you don't remember it.",
    "emlNc"		=>	"Sorry, your email changing failed.",
    "emlVns"	=>	"Sorry, we could not send you an verification mail. Your account has NOT been created.",
    "emlNs"		=>	"Verification Mail NOT successfully sent! Error: ",
    "pwW"			=>	"Login failed. Try again.",
    "pwW3"		=>	"You have entered an incorrect password 3 or more times already. Please wait 30 seconds to try again.",
    "pwS"			=>	"Password has a minimum length of 6 characters",
    "pwE"			=>	"Password field was empty",
    "pwNi"		=>	"Password and password repeat are not the same",
    "pwCf"		=>	"Sorry, your password changing failed.",
    "pwRf"		=>	"Password reset mail NOT successfully sent! Error: ",
    "pwOw"		=>	"Your OLD password was wrong.",
    "unNe"		=>	"This user/email does not exist",
    "unTk"		=>	"Sorry, that username is already taken. Please choose another one.",
    "unIv"		=>	"Username does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters",
    "unDb"		=>	"Sorry, that username is the same as your current one. Please choose another one.",
    "unF"			=>	"Sorry, your chosen username renaming failed",
    "unE"			=>	"Username field was empty",
    "unL"			=>	"Username cannot be shorter than 2 or longer than 64 characters",
    "lnkExp"	=>	"Your reset link has expired. Please use the reset link within one hour.",
    "lnkE"		=>	"Empty link parameter data.",
    "regF"		=>	"Sorry, your registration failed. Please go back and try again.",
    "wVc"			=>	"Sorry, no such id/verification code combination here...",
    "iCk"			=>	"Invalid cookie",
    "wCp"			=>	"Captcha was wrong!"
);

    public $msg = array(
    "lgOut"	=>	"You have been logged out.",
    "emlN"	=>	"Please provide a valid email address!",
    "emlCok"	=>	"Your email address has been changed successfully. New email address is %s",
    "unCok"	=>	"Your username has been changed successfully. New username is ",
    "pwCok"	=>	"Password successfully changed!",
    "pwRms"	=>	"Password reset mail successfully sent!",
    "aOk"		=>	"Your account was activated successfully. Please log in to complete the process!",
    "regOk"	=>	"Your account has been created successfully and we have sent you an email (Please check also your spam folder).
						  Click the VERIFICATION LINK within that mail to activate your account.",
    "verOk"	=>	"Your account was successfully activated. ",
    "deact"	=>	"Account of %s was successfully deactivated",
    "deactm" => " and a reactivation email was sent."
);

    public $info = array(
    "reg"		=>	"Please fill in the form below to register and provide a valid e-mail address.",
    "delA"	=>	"<p><span class='err'>Delete account and all related data</span>. 
							Your account will be deactivated immediately, and you will receive an e-mail with a re-activation link. 
							If you don't re-activate, your account information and all related data will be deleted completely after two days.</p>",
    "conf"	=>	"With my registration I agree to receive reactivation emails after each period of three months account inactivity. 
							If not reactivated within a period of 48 hours, my account and all data will be deleted automatically.",
    "pwRes"	=>	"Enter your e-mail address and you'll receive a mail with instructions:<br>",
    "nlgin" =>  "You need an account to access this website.",
    "nReg"	=>	"Please contact the <a href='mailto:webmaster@bpmsg.com'>Webmaster</a> to register for an account."
);

    public $wrd = array(
    "crC"		=>	"Edit your credentials here:",
    "emlC"	=>	"Change email",
    "emlN"	=>	"New email:",
    "pwC"		=>	"Change password",
    "pwO"		=>	"OLD Password:",
    "pwN"		=>	"New password:",
    "pwNr"	=>	"Repeat new password:",
    "unC"		=>	"Change user name",
    "unN"		=>	"New username (2-30 char, azAZ09):",
    "delA"	=>	"Delete my account",
    "cont"	=>	"Continue",
    "done"	=>	"Done",
    "eml"		=>	"User's email (please provide a real email address, you'll get a verification mail with an activation link)",
    "pw"		=>	"Password (min. 6 characters!)",
    "pwr"		=>	"Password repeat",
    "un"		=>	"Username (only letters and numbers, 2 to 30 characters)",
    "pwRes"	=>	"Reset my password",
    "pwSbm"	=>	"Submit new password",
    "hlPw"	=>	"password",
    "hlUn"	=>	"username or email",
    "hlAc"	=>	"Account",
    "hlLo"	=>	"Logout",
    "hlFg"	=>	"forgot?",
    "hlReg"	=>	"Register",
    "hlWlc"	=>	"Welcome "
);

    public $tbl = array(
    "tbEdTd1"	=>	"User ID:",
    "tbEdTd2"	=>	"User Name:",
    "tbEdTd3"	=>	"E-mail:",
    "tbEdTd4"	=>	"Registered since:",
    "tbEdTd5"	=>	"Remember Cookie:"
);
}
