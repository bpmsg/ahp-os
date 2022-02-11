<?php

class AhpPrioCalcPT
{
    public $wrd = array(
    "crit"	=>  "Criterio",
    "alt"		=>	"Alternativas"
);

    // Errors
    public $err = array(
    "pgm"					=>	"<br><span class='err'>Erro do Programa                                                      r</span>",
    "pwcInc"			=>	"<span class='err'>As comparações entre pares ainda não estão completa !</span>"
);

    // calc (priority calculator)
    public $titles1 = array(
    "pageTitle" 	=>	" Calculadora AHP  - AHP-OS",
    "h1title" 		=>	"<h1> Calculadora de Prioridade AHP </h1>",
    "h2subTitle" 	=>	"<h2> Critério AHP </h2>",
    "h3Pwc"				=>	"<h3>Comparação entre pares <span class='var'>%s</span></h3>",
    "h3Res"				=>	"<h3 align='center'>Prioridades Resultantes</h3>"
);

    // hiercalc
    public $titles2 = array(
    "pageTitle" 	=>	"PWC Crit AHP-OS",
    "h1title" 		=>	"<h1>Comparação entre Pares AHP-OS</h1>",
    "h2subTitle" 	=>	"<h2>Critério de Avaliação para  <span class='var'>%s</span></h2>",
);

    // altcalc
    public $titles3 = array(
    "pageTitle" 	=>	"PWC Alt AHP-OS",
    "h1title" 		=>	"<h1>Comparação entre Pares AHP-OS</h1>",
    "h2subTitle" 	=>	"<h2>Avaliação das Alternativas para <span class='var'>%s</span></h2>",
    "h2alt"				=>	"<h2>Alternativas</h2>",
    "h3Mnu"				=>	"<h3>Menu de Alternativas</h3>",
    "h3tblA"			=>	"<h3>Estrutura do Projeto</h3>",
    "h3Res"				=>	"<h3>Resultado para as Alternativas</h3>",
    "h4Res"				=>	"<h4>Prioridades e ranking</h4>"
);

    // calc1
    public $titles4 = array(
    "pageTitle" 	=>	"AHP Criteria",
    "h1title" 		=>	"<h1 class='ca' >Nomes do Critérios AHP </h1>"
);

    // alt1
    public $titles5 = array(
    "pageTitle" 	=>	"AHP Alternatives",
    "h1title" 		=>	"<h1 class='ca' > Nomes das Alternativas AHP </h1>"
);

    // Messages
    public $msg = array(
    "nPwc"		=>	"<span class='msg'>%g comparação entre o(s) par(es). </span>",
    "pwcAB"		=>	"A - wrt <span class='var'>%s</span> - or B?",
    "noPwc1"	=>	"<span class='msg'>Por favor complete todas as comparações entre os pares antes de passar para a próxima etapa. Em seguida clique em  ",
    "noPwc2"	=>	"<input type='button' value='Alternativas'> then ",
    "noPwc3"	=>	"<input class='btnr ' type='button' value='AHP'></span>",
    "tu"			=>	"Obrigado pela sua participação!",
    "giUpd"		=>	"<span class='msg'> %g julgamento(s) atualizado. </span>",
    "giIns"		=>	"<span class='msg'> %g julgamento(s) inserido. </span>",
    "inpA"		=>	"<p class='ca' >Por favor preencher todos os campos</p>"
);

    // Information
    public $info= array(
    "intro"		=>	"Selecione o numero e nomes do critério,em seguida inicie a comparação entre pares para calcular as prioridades utilizando o Processo Analítico Hierárquico.",
    "pwcQ"		=>	"<p><span class='hl'>Com relação a 
								<i><span class='var'>%s</span></i>, qual critério é mais importante,e quanto mais em uma escala de 1 a 9%s</span></p>",
    "pwcQA"		=>	"<p><span class='hl'>Com relação a  
								<i><span class='var'>%s</span></i>, qual alternativa se encaixa melhor ou é mais preferível, e quanto mais em uma escala de 1 a 9%s</span></p>",
    "selC"		=>	"Selecione o número de critérios:",
    "scale"		=>	"<p style='font-size:small'> Escala AHP : 1-Mesma importância, 3- Importância Moderada,
 								5- Alta importância, 7- Muito alta importância, 9- Extrema importância 
 								(2,4,6,8 valores entre este inverlavo).</p>",
    "doPwc"		=>	"Faça a comparação entre pares de todos os critérios. Quando completo,
								clique em <i>Verificar Consistência</i>para obter as prioridades.<br>",
    "doPwcA"	=>	"Por favor, faça a comparação de pares de todas as alternativas para indicar o quão bem elas atendem a cada critério. Quando terminar, clique em <i>Verificar Consistência</i>para obter os pesos, e <i>Enviar Prioridades</i> para continuar. ",
    "doPwcA1"	=>	"<p>Compare as alternativas em relação aos critérios (clique em AHP). 
Quão bom é o ajuste das alternativas para cada critério?</p>",
    "adj"			=>	"<p class='msg'>Para melhorar a consistência, ajuste levemente os julgamentos destacados em mais ou menos um ou dois pontos na escala.</p>",
    "inpAlt"	=>	"Aqui você pode inserir o número e os nomes de suas alternativas.",
    "pSave"				=>	"<p>Clique em <i>Salvar como projeto</i> para salvar o projeto com as alternativas definidas e  para avaliar as alternativas.</p>"
);

    // Menu and buttons
    public $mnu = array(
    "btnSbm"	=>	"Enviar",
    "lgd1"		=>	"Calculadora de prioridade AHP",
    "done"		=>	"Feito",
    "next"		=>	"Próximo",
    "lgd2"		=>	"Menu de Alternativas",
    "btn1"		=>      "Salvar Julgamentos",
    "btn2"		=>	"Redefinir alternativas",
    "btn3"		=>	"Salvar como projeto"
);
}
