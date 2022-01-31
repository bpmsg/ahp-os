<?php
class AhpGroupResPT {
// for ahp-group.php AND  ahp-g-input.php
/* Titles and headings */
public $titles = array(
	"pageTitle1"	=>	" Resultados do Grupo AHP - AHP-OS",
	"h1title1"		=>	"<h1>Resultados do Grupo AHP</h1>",
	"h2subTitle1"	=>	"<h2>Dados  da Resolução do Projeto</h2>",

	"pageTitle2"	=>	" Data de entrada do Projeto AHP - AHP-OS",
	"h1title2"		=>	"<h1>Resultados do Grupo AHP</h1>",
	"h2subTitle2"	=>	"<h2>Data de entrada do Projeto</h2>",

	"h2hier"			=>	"<h2>Hierarquia  com as prioridades consolidadas</h2>",
	"h2consP"			=>	"<h2>Prioridades Globais Consolidadas</h2>",
	"h2consA"			=>	"<h2>Pesos Consolidados das Alternativas</h2>",
	"h2sens"			=>	"<h2>Análise de Sensibilidade</h2>",
	"h3wUncrt"		=>	"<h3>Incertezas ponderadas</h3>",
	"h2nodes"			=>	"\n<h2>Divisão por nós</h2>",
	"h4wCons"			=>	"<h4>Prioridades Consolidadas</h4>",
	"h4mCons"			=>	"<h4>Matriz de Decisão Consolidada </h4>",
	"h4part"			=>	"<h4> Resultato do Grupo e Prioridades Individuais dos Participantes</h4>",
	"h2pGlob"			=>	"<h2>Prioridades Globais</h2>",
	"h3rob"				=>	"<h3>Robustez</h3>",
	"h2alt"				=>	"<h2>Alternativas por Participantes</h2>",
	"h2crit"			=>	"<h2>Divisão por Critérios</h2>",
	"h4group"			=>	"<h4>Resultato do Grupo e Prioridades Individuais dos Participantes</h4>",
	"h2grMenu"		=>	"<h2>Menu de Resultado do Grupo</h2>",
	
	"h2dm"				=>	"<h2>Matrizes de decisão das comparações entre pares</h2>",
	"h4dm"				=>	"<h4>Matriz de Decisão</h4>",
	"h4crit"			=>	"<h4>Critério: <span class='res'>%s</span></h4>",
	"h3part"			=>	"<h3>Participante <span class='res'>%s</span></h3>",
	"h4nd"				=>	"<h4>Nó: <span class='res'>%s</span></h4>"
);

/* Individual words */
public $wrd	 = array(
	"crit"			=>	"criterio",
	"alt"				=>	"alternativas"
);

/* Result output */
public $res  = array(
	"cr"					=>	"Razão de Consistência CR: <span class='res'>%02.1f%%</span>",
	"consens1"		=>	"<p> consenso do grupo AHP: <span class='res'>%02.1f%%</span> ",
	"consens2"		=>	" Critério: <span class='res'>%s</span> - CR: <span class='res'>%g%%</span>",
	"gCons"				=>	" - consenso do grupo AHP: <span class='res'>%02.1f%%</span> ",
	"consens4"		=>	"<p><small>Consenso na avaliação das alternativas wrt ao critério 
								<span class='res'>%s</span>: <span class='res'>%02.1f%%</span>",
	"nodeCr"			=>	" Nó: <span class='res'>%s</span> - CR: <span class='res'>%g%%</span>",
	"ovlp"				=>	"A seguir %s sem sobreposição:<br>",
	"ovlpNo"			=>	"Sem sobreposição do %s dentro das incertezas",
	"ovlpAll"			=>	"Todos %s se sobrepõem dentro das incertezas.",
	"ovlpGrp"			=>	" Os grupos a seguir(s) de %s estão sobrepostos dentro das incertezas:<br>",
	"rtrb"				=>	"<p class='msg'>1. A solução para a melhor alternativa <span class='res'>%s</span> é robusta.<br>",
	"rt10"				=>	"<p class='msg'>1. O <i>percent-top</i> do critério crítico é <span class='res'>%s</span>: uma mundança do <span class='res'>%g%%</span> por absoluto <span class='res'>%g%%</span> modificará  o ranking entre as alternativas <span class='res'>%s</span> e <span class='res'%s</span>.<br>",
	"rt11"				=>	"2. O <i>por cento qualquer</i> do critério crítico é <span class='res'>%s</span>: 
										uma mundança do <span class='res'>%g%%</span> por absoluto <span class='res'>%g %%</span> modificará  o ranking entre as alternativas <span class='res'>%s</span> e 
										<span class='res'>%s</span>.<br>",
	"rt11s"				=>	"2. O <i>por cento qualquer</i> do critério crítico é o mesmo acima.<br>",
	"rt20"				=>	"3. O<i>por cento qualquer</i> da medida crítica de performance para alternativa <span class='res'>%s</span>sobre o critério <span class='res'>%s</span>. Uma mudança do <span class='res'>%g%%</span> por absoluto
										<span class='res'>%g%%</span> modificará  o ranking entre as alternativas<span class='res'>%s</span> e <span class='res'>%s</span>.");

/* Messages */
public $msg  = array(
	"scaleSel"		=>	"<p class='msg'>Escala Selecionada: <span class ='hl'>%s</span></p>",
	"wMethod"			=>	"<p>Método: <span class ='hl'>Método do produto ponderado (MPP)</span></p>",
	"rMethod"			=>	"<p>Variação aleatória: <span class ='hl'>baseada no desvio padrão</span></p>",
	"mcVar"				=>	"<p class='msg'>Peso estimado das incertezas baseado nas <span class='res'>%g</span> variações dos julgamentos.",
	"pSel"				=>	"<p>Participantes Selecionados: <span class='res'>%s</span></p>",
	"noSens"			=>	"<p class='msg'>Não foi possivel realizar nenhum análise de sensibilidade.</p>",
	"noPwc1"			=>	"<span class='msg'> - Não há dados de comparação entre pares.</span>",
	"noPwc2"			=>	"<p class='msg'>Não há dados de comparação entre pares dos participantes</p>",
	"noPwc3"			=>	" -Não há dados de comparação entre pares dos participantes.",
	"noPwc4"			=>	"<p>Atenção: <span class='msg'>%s</span></p>",
	"noRt"				=>	"<p class='msg'>Não foi possível realizar nenhum teste de robustez.</p>",
	"pCnt"				=>	"Agregação individual dos julgamentos para %g Participante(s)",
	"nlgin"				=>	"<p class='msg'> Você precisa estar cadastrado e efetuar o login para editar seus projetos.</p>"
);

/* Errors */
public $err  = array(
	"incompl"			=>	"<p class='err'>Avaliação do Projeto Incompleta</p>",
	"consens0"		=>	"<p>consenso do grupo AHP: <span class='err'>n/a</span>",
	"consens1"		=>	" - Consenso <span class='res err'>n/a</span>",
	"consens2"		=>	"<p><small>na avaliação das alternativas wrt para o critério <span class='res err'>n/a</span>"
);

/* Information output */
public $info = array(
	"sensDl"			=>	"<p><small>Nota:análise completa via download.</small></p>",
	"cpbd"				=>	"Preferências consolidas das alternativas para seus respectivos critérios",
	"pwcfor"			=>	"Comparação entre pares para: <br>"
);

/* Menu and buttons */
public $mnu = array(
	"btnNdD"	=> 	"<p><button href='#%s' class='nav-toggle'>Detalhes</button>",
	"lgd1"		=>	"Menu de Resultado em grupo",
	"lbl4"		=>	"dec. comma",
	"btn1"		=>	"Atualizar",
	"btn2"		=>	"Vizualizar entrada de dados",
	"btn3"		=> 	"Download (.csv)",
	"btn4"		=>	"Definir Alternativas",
	"btn5"		=>	"Feito",
	"lgd2"		=>	"Menu de Entrada de Dados do Projeto"
);
}

