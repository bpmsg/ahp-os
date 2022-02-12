<?php

class AhpHierginiPT
{
// Errors
    public $err = array(
    "pExc"		=>	"Número de projetos excedido,o dado não pode ser salvo. Cancela e delete alguns de seus projetos. ",
    "noSc"		=>	"Favor inserir o código da sessão. ",
    "noName"	=>	"Favor inserir o seu nome. ",
    "pwcCompl"=>	"Comparações entre pares sobre <span class='var'>%s</span> completas.",
    "hDefP"		=>	"A Hierarquia não definiu as prioridades. O Projeto não pode ser atualizado.",
    "unknw"		=>	"Erro desconhecido  - administrador: %s retFlg: %g"
);

    public $titles = array(
    "pageTitle" =>	"Entrada da Sessão AHP-OS",
    "h1Title" 	=>	"<h1> Entrada da Sessão AHP</h1>",
    "subTitle1" =>	"AHP-OS Entrada de Participante(s)",
    "subTitle2"	=>	"Salvar/Atualizar  Projeto AHP",
    "subTitle3"	=>	"Entrada da Comparação entre pares ",
    "h3Pwc"			=>	"<h3>Comparação entre pares <span class='var'>%s</span></h3>",
    "h3Res"			=>	"<h3 align='center'>Prioridades Resultantes</h3>",
    "h2siMnu"		=>	"<h2>Menu de Entrada da Sessão AHP </h2>"
);

    // Messages
    public $msg = array(
    "nProj"		=>	"Novo projeto, clique \"Ir\" para salvar",
    "pMod"		=>	" O projeto existente será modificado e substituído!"
);

    // Information
    public $info= array(
    "intro"		=>	"<div class='entry-content'>
								<p style='text-align:justify;'>AHP-OS é uma ferramenta online para auxiliar na tomada de decisão com base no <i>Processo Analítico Hierárquico</i> (AHP). 
								Como participante selecionado pedimos, por gentileza <b>inserir o código da sesão e seu nome, responda o questionário e envie suas respostas para avaliação do grupo</b>. Isto ira auxiliar na reflexão dos seus critérios para a tomada final de decisão. Obrigado!</p>
								</div>",
    "act1"		=>	"Código da Sessão do novo projeto %s. ",
    "act2"		=>	"Atualizar projeto. ",
    "act3"		=>	"O Projeto possui %g participante(s). ",
    "ok"			=>	"<p class='msg'>Ok. Clique em \"Ir\" para continuar</p>",
    "siSc"		=>	"Por favor, insira o seu código de sessão para participar da sessão de grupo AHP",
    "siNm1"		=>	"<a href='%s?logout'>Desconect-se</a> como administrador da sessão para incluir o nome de outro participante.",
    "siNm2"		=>	"Seu nome será incluso na sessão do grupo(3 - 25 alpha num char).",
    "pName"		=>	" Nome do Projeto AHP:",
    "pStat"		=>	"Status do Projeto:",
    "pDescr"	=>	"Breve Descrição do Projeto:",
    "descr"	=>	"</br><small>Texto a ser diponibilizado para os demais membros da sessão, 400 chars max. 
							Você pode usa tags HTML, como &lt;em&gt; or &lt;font&gt; para enfatizar ou grifar seu texto.</small>"
);

    // Menu and buttons
    public $mnu = array(
    "lgd1"		=>	"Entrada da Sessão AHP",
    "lgd2"		=>	"Menu de Entrada da Sessão",
    "sc"			=>	"Código da Sessão:",
    "nm"			=>	"Seu Nome:",
    "btn1"		=>	"Ir",
    "btn2"		=>	"Revisar Entrada",
    "btn3"		=>	"Vizualizar resultado do grupo",
    "btn4"		=>	"Resetar",
    "btn5"		=>	"Cancelar"
);
}
