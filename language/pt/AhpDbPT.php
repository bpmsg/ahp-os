<?php

class AhpDbPT
{
    public $titles = array(
    "h3pDat"		=>	"<h3>Data do Projeto</h3>",
    "h3pPart"		=>	"<h3>Participantes do Projeto</h3>\n",
    "h3pAlt"		=>	"<h3>Alternativas do Projeto</h3>"
    );

    public $err = array(
    "dbType"		=>	"Não existe este tipo de base de dados SQL : ",
    "scInv"			=>	"Código inválido da Sessão ",
    "scInUse"		=>	"Código de sessão em uso ",
    "dbWrite"		=>	"O dado não pode ser inserido na base de dados . Por favor tente novamente, mais tarde.",
    "dbWriteA"	=>	"Erro na data base, não foi possível armazenar as alternativas ",
    "dbUpd"			=>	"O dado não pode ser atualizado. Por favor tente novamente mais tarde.",
    "dbSubmit"	=>	"O dado já foi enviado ",
    "noSess"		=>	"Não há sessões salvas ",
    "dbReadSc"	=>	" Erro na base de dados ao incluir o dado da ",
    "pClosed"		=>	"O Projeto está fechado. Não é permitido a entrada de novas comparações entre pares.",
    "pNoMod"		=>	"O Projeto já possui participantes, não é possível modificar a hierarquia."
    );

    public $msg = array(
    "noSess" 		=> "Não há sessões salvas"
    );

    public $tbl = array(
    "scTblTh"		=> "<thead><tr>
										<th>Não</th>
										<th>Sessão</th>
										<th>Projeto</th>
										<th>Tipo<sup>1</sup></th>
										<th>Status</th>
										<th>Descrição</th>
										<th>Part.<sup>2</sup></th>
										<th>Criado</th></tr></thead>",
    "scTblFoot"	=> 	"<tfoot><tr><td colspan='8'>
									<sup>1</sup> H: Prioridade de avaliação da hierarquia, A:Avaliação da Alternativa, 
									<sup>2</sup> Número de participantes</td>
									</tr></tfoot>",
    "pdTblTh"		=>	"<thead><tr>
										<th>Campo</th>
										<th>Conteúdo</th></tr></thead>\n",
    "pdTblR1"		=>	"<tr><td>Código da Sessão</td><td class='res'>%s</td></tr>\n",
    "pdTblR2"		=>	"<tr><td>Nome do Projeto</td><td class='res'>%s</td></tr>\n",
    "pdTblR3"		=>	"<tr><td>Descrição</td><td class='res'>%s</td></tr>\n",
    "pdTblR4"		=>	"<tr><td>Autor</td><td class='res'>%s</td></tr>\n",
    "pdTblR5"		=>	"<tr><td>Data</td><td class='res'>%s</td></tr>\n",
    "pdTblR6"		=>	"<tr><td>Status</td><td class='res'>%s</td></tr>\n",
    "pdTblR7"		=>	"<tr><td>Tipo</td><td class='res'>%s</td></tr>\n",
    "paTblTh"		=>	"<thead><tr>
										<th>Não</th>
										<th>Alternativas</th>
									</tr></thead>\n",
    "ppTblTh"		=>	"<thead><tr>
										<th>Não</th>
										<th>Selecionar</th>
										<th>Nome</th>
										<th>Data</th>
									</tr></thead>\n",
    "ppTblLr1"	=>	"<tr><td colspan='4'><input id='sbm0' type='submeter' name='selecionar p' value='Atualizar Seleção'>&nbsp;<small>
									<input class='onclk0' type='checkbox' name='ptick' value='0' ",
    "ppTblLr2"	=>	">&nbsp;selecionar todos&nbsp;<input class='onclk0' type='checkbox' name='ntick' value='0' ",
    "ppTblLr3"	=>	">&nbsp; tirar  todas as seleções</small></td></tr>",
    "ppTblFoot"	=>	"<tfoot><tr><td colspan='4'>
										<small>Se nenhum for selecionado todos serão incluídos.</small>
									</td></tr></tfoot>"
    );

    public $info = array(
    "shPart"		=> "<p><span class='var'>%g</span> participantes. <button class='toggle'>Mostrar/Ocultar</button> all.</p>"
    );
}
