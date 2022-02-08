<!-- Registration Form -->
	<h2><?php echo $registration->rgTxt->titles['h2reg']; ?></h2>
	<p style='text-align:justify;'><?php echo $registration->rgTxt->info['reg']; ?></p>
	<form method="post" action="do-register.php" name="registerform">
		<input type='hidden' name='formToken' value='<?php echo $formToken; ?>' />

 		<label for="login_input_username"><?php echo $registration->rgTxt->wrd['un']; ?></label><br>
	 		<input id="login_input_username" type="text" pattern="[a-zA-Z0-9]{2,30}" name="user_name" value='<?php echo $registration->un; ?>' required /><br>

 		<label for="login_input_email"><?php echo $registration->rgTxt->wrd['eml']; ?></label><br>
 			<input id="login_input_email" type="email" name="user_email" value='<?php echo $registration->eml; ?>' required /><br>

		<label for="login_input_password_new"><?php echo $registration->rgTxt->wrd['pw'] ?></label><br>
 			<input id="login_input_password_new" class="login_input" type="password" 
 			name="user_password_new" pattern=".{6,}" value='<?php echo $registration->pw; ?>' required autocomplete="off" /><br>

 		<label for="login_input_password_repeat"><?php echo $registration->rgTxt->wrd['pwr']; ?></label><br>
 			<input id="login_input_password_repeat" class="login_input" type="password"
 		 	name="user_password_repeat" pattern=".{6,}" value='<?php echo $registration->pwr; ?>' required autocomplete="off" />

			<input id="website" type="text" name="website" />

			<?php if( CAPTCHA ){ 
					if ( defined('CPTTXT') && CPTTXT){ // Text captcha
						echo "<p>"; include ('../../showCaptchaTxt.php'); echo "</p>";
						echo "<p><label for='captcha'>Your answer?</label><br>";
					} else { // Image captcha
						echo "<p><img alt='captcha' src='../../showCaptcha.php' ></p>";
						echo "<p><label for='captcha'>CAPTCHA code</label><br>";
					}
				}?>				
 			<input id="captcha" class="login_input" type="text" pattern="[a-zA-Z0-9]{1,15}" name="captcha" required></p>
		<p class='sm'><input id='dpAgr' type=checkbox required ><?php echo $registration->rgTxt->info['conf']; ?></p>
		<p class='ca'><input type="submit" name="register" value="Register" ></p>
	</form>

