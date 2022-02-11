<?php

/* used for both login and registration */

class LoginES
{
    public $titles = array(
    "h1edit"	=>	"Editá tus datos",
    "h2info"	=>	"Información de la cuenta",
    "h2act"		=>	"Últimas actividades de la cuenta",
    "h2lgin"	=>	"Por favor hacé login",
    "h1reg"		=>	"Registro de usuario",
    "h2reg"		=>	"Formulario para registrarse",
    "h1pwR"		=>	"Reseteo de Password"
);

    public $err  = array(
    "aNact"		=>	"Tu cuenta aún no está activada. Haga clic en el enlace de confirmación enviado a su mail.",
    "dbCon"		=>	"Problema de conexión a la base de datos.",
    "emlE"		=>	"El email no puede quedar vacío",
    "emlL"		=>	"El email no puede superar los 64 caracteres",
    "emlD"		=>	"Lo sentimos, esa dirección de correo electrónico es la misma que la actual. Por favor elije otro.",
    "emlI"		=>	"Su dirección de correo electrónico no tiene un formato de correo electrónico válido.",
    "emlR"		=>	"Esta dirección de correo electrónico ya está registrada. Utilice la página \"Olvidé mi password\" si no la recuerda.",
    "emlNc"		=>	"Lo sentimos, no se pudo cambiar tu correo electrónico",
    "emlVns"	=>	"Lo sentimos, no pudimos enviarle un correo de verificación. Su cuenta NO ha sido creada.",
    "emlNs"		=>	"¡El mail de verificación NO se envió correctamente! Error: ",
    "pwW"			=>	"Error de inicio de sesion. Inténtalo de nuevo.",
    "pwW3"		=>	"Ya ingresó una contraseña incorrecta 3 o más veces. Espere 30 segundos para volver a intentarlo.",
    "pwS"			=>	"El password tiene una longitud mínima de 6 caracteres",
    "pwE"			=>	"El campo de password está vacío",
    "pwNi"		=>	"Los password no coinciden",
    "pwCf"		=>	"Disculpas su cambio de password NO se logró.",
    "pwRf"		=>	"¡El correo para reestablecer su Password NO se envió correctamente! Error: ",
    "pwOw"		=>	"Su password ANTERIOR es incorrecto",
    "unNe"		=>	"Este usuario o mail no existe",
    "unTk"		=>	"Disculpas este nombre de usuario ya está en uso. Por favor, elige otro",
    "unIv"		=>	"El nombre de usuario no se ajusta al esquema de nombres: solo se permiten números y números de la A a la Z, de 2 a 64 caracteres",
    "unDb"		=>	"Lo sentimos, ese nombre de usuario es el mismo que el actual. Por favor elije otro.",
    "unF"			=>	"Lo sentimos, no se pudo cambiar el nombre de su nombre de usuario elegido",
    "unE"			=>	"El campo de nombre de usuario estaba vacío",
    "unL"			=>	"El nombre de usuario no puede tener menos de 2 ni más de 64 caracteres",
    "lnkExp"	=>	"Su enlace de restablecimiento ha caducado. Utilice el enlace de restablecimiento dentro de una hora.",
    "lnkE"		=>	"Datos de parámetros de enlace vacíos.",
    "regF"		=>	"Lo sentimos, su registro falló. Por favor, regrese y vuelva a intentarlo.",
    "wVc"			=>	"Lo sentimos, no hay tal combinación de identificación / código de verificación aquí ...",
    "iCk"			=>	"Cookie inválida ",
    "wCp"			=>	"¡Captcha incorrecta!"
);

    public $msg = array(
    "lgOut"	=>	"Has sido desconectado.",
    "emlN"	=>	"¡Por favor ingrese una dirección de correo electrónico válida!",
    "emlCok"	=>	"Su dirección de correo electrónico se ha cambiado correctamente. La nueva dirección de correo electrónico es %s",
    "unCok"	=>	"Su nombre de usuario se ha cambiado correctamente. El nuevo nombre de usuario es",
    "pwCok"	=>	"Password cambiado exitosamente!",
    "pwRms"	=>	"El mail para resetear su password se envió exitosamente",
    "aOk"		=>	"Su cuenta se activó con éxito. ¡Inicia sesión para completar el proceso!",
    "regOk"	=>	"Su cuenta se ha creado correctamente y le hemos enviado un correo electrónico (verifique su carpeta de correo no deseado).
						  Haga clic en el VÍNCULO DE VERIFICACIÓN dentro de ese correo para activar su cuenta.",
    "verOk"	=>	"Su cuenta fue creada exitosamente ",
    "deact"	=>	"La cuenta de %s fue desactivada",
    "deactm" => " y se envió un correo electrónico de reactivación."
);

    public $info = array(
    "reg"		=>	"Por favor completa el formulario para registrarte e indica un mail válido.",
    "delA"	=>	"<p><span class='err'>Eliminar cuenta y todos los datos relacionados</span>. 
							Su cuenta se desactivará inmediatamente y recibirá un correo electrónico con un enlace de reactivación. 
							Si no vuelve a activarla, la información de su cuenta y todos los datos relacionados se eliminarán por completo después de dos días.</p>",
    "conf"	=>	"Con mi registro, acepto recibir correos electrónicos de reactivación después de cada período de tres meses de inactividad de la cuenta.
							Si no se reactiva en un plazo de 48 horas, mi cuenta y todos los datos se eliminarán automáticamente.",
    "pwRes"	=>	"Ingrese su dirección de correo electrónico y recibirá un correo con instrucciones:<br>",
    "nlgin" =>  "Necesita una cuenta para acceder a este sitio web.",
    "nReg"	=>	"Por favor contactá al <a href='mailto:webmaster@bpmsg.com'>Webmaster</a> para registrar una cuenta."
);

    public $wrd = array(
    "crC"		=>	"Edite sus datos aquí:",
    "emlC"	=>	"Cambio de email",
    "emlN"	=>	"Nuevo email:",
    "pwC"		=>	"Cambio de password",
    "pwO"		=>	"ANTERIOR Password:",
    "pwN"		=>	"Nuevo password:",
    "pwNr"	=>	"Repita el nuevo password:",
    "unC"		=>	"Cambie el nombre de usuario",
    "unN"		=>	"Nuevo nombre de usuario (2-30 car, azAZ09):",
    "delA"	=>	"Borrar mi cuent",
    "cont"	=>	"Continuar",
    "done"	=>	"Hecho",
    "eml"		=>	"Correo electrónico del usuario (proporcione una dirección de correo electrónico real, recibirá un correo de verificación con un enlace de activación)",
    "pw"		=>	"Password (min. 6 caracteres)",
    "pwr"		=>	"Repita el password",
    "un"		=>	"Nombre de usuario (solo letras y números, de 2 a 30 caracteres)",
    "pwRes"	=>	"Resetear mi password",
    "pwSbm"	=>	"Enviar el nuevo password",
    "hlPw"	=>	"password",
    "hlUn"	=>	"nombre de usuario o email",
    "hlAc"	=>	"Cuenta",
    "hlLo"	=>	"Desconectarse",
    "hlFg"	=>	"Olvidó?",
    "hlReg"	=>	"Registrarse",
    "hlWlc"	=>	"Bienvenido "
);

    public $tbl = array(
    "tbEdTd1"	=>	"ID usuario:",
    "tbEdTd2"	=>	"Nombre usuario:",
    "tbEdTd3"	=>	"E-mail:",
    "tbEdTd4"	=>	"Registrado desde:",
    "tbEdTd5"	=>	"Recordar Cookie:"
);
}
