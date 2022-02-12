<?php

class AhpHierarchyPT
{
    public $titles = array(
    "pageTitle" 	=>	" Hierarquia AHP - AHP-OS",
    "h1title" 		=>	"<h1>Hierarquia AHP</h1>",
    "h2subTitle"	=>	"<h2>Decisão Hierárquica AHP-OS </h2>",
    "h4pDescr"		=>	"<h4>Descrição do Projeto</h4>",
    "h3hInfo"			=>	"\n<h3>Informação da Hierarquia</h3>",
    "h3Proj"			=>	"<h3>Projeto: <span class= 'var'>%s</span></h3>",
    "h2ieHier"		=>	"<h2>Entrar/Editar Hierarquia</h2>"
);
    public $err = array(
    "giH"			=>	"Erro ao entrar a hierarquia"
);
    public $msg = array(
    "lgin"		=>	"<span class='msg'>Para todas as funcionalidades,favor realizar seu cadastro e acessar sua conta.</span>",
    "pInp"		=>	"<p class='msg'>Entrada para o projeto <span class='var'>%s</span></p>",
    "pMod"		=>	"<p class='msg'>Modificação do projeto <span class='var'>%s</span></p>",
    "pNew"		=>	"<p class='msg'>Novo projeto</p>",
    "hMode"		=>	"<p class='msg'>Modo:Avaliação da Hierarquia</p>",
    "aMode"		=>	"<p class='msg'>Modo:Avaliação da Alternativa <span class='var'>%g</span> alternativas</p>",
    "giUpd"		=>	"<span class='msg'> %g julgamento(s) atualizados. </span>",
    "giIns"		=>	"<span class='msg'> %g julgamento(s) inseridos. </span>",
    "giTu"		=>	"Obrigado pela sua participação!",
    "giNcmpl"	=>	"A comparação entre pares ainda não está completa!",
    "giNds"		=>	"Nenhum dado armazenado. ",
    "giPcmpl"	=>	"Por favor , completar todas as comparações entre pares primeiro. "
);

    public $info = array(
    "intro"		=>	"<div class='entry-content'><p style='text-align:justify;'>
								Defina uma hierarquia de decisão para um critério e calcule os seus pesos com base nas comparações entre pares utilizando o Processo Hierárquico Análitico AHP. 
								Em seguida defina o grupo de alternativas e as avalie de acordo com a lista de critérios que você considera como a melhor alternativa para resolver os seu problema de decisão.
								</p><p style='text-align:justify;'>
								Para um cálculo simples baseado na comparação entre pares você pode usar a <a href='ahp-calc.php'> calculadora de prioridades AHP </a>. 
								Se você gostou da ferramenta e a achou útil, clique no botão  <i>gostei</i> no final da página. Obrigado!</p></div>",
    "clkH"		=>	"Clique em <input type='button' class='btnr' value='AHP'> para completar a comparação entre pares. ",
    "clkA"		=>	"Clique em <b>Alternativas</b>, depois<b>AHP</b> para completar a comparação entre pares.",
    "clkS"		=>	"Clique em <input type='button' value='Salvar julgamentos'>para finalizar e salvar seus julgamentos.",
    "txtfld"	=>	"Entre ou edit seu texto na área de texto abaixo,depois clique em enviar. (See <a href='ahp-examples.php'>examples</a>)",
    "synHelp"	=>	"<br><span style='text-align:justify; font-size:small;'>
								Na área de texto acima você pode definir uma nova hierárquica. 
								Os nós são seguido de  <b><i>dois pontos</i></b>, folhas são separadas por <b><i>ponto e vírgula</i></b>, e cada ramos deve ser finalizado por um <b><i>ponto e vírgula</i></b>. 
								O acento  (~) é descartado. Nome de categorias e subcategorias precisam ser únicos. Nenhum número é permitido como nome de categoria,<i>e.g.</i> utilize \"100 $\" ao invés de \"100\". Uma categoria não pode ter somente uma subcategoria. Por padrão,todas as prioridades estão definidas igualmente para somarem 100% em cada categoria ou subcategoria. Nota:A entrada de dados é sensível a letras maiúsculas e minúsculas.</span>",
    "nlg"			=>	"<p class='hl'>Como um usuário cadastrado você poderá baixar as prioridades e salvar a hierarquia definida como projeto.</p>",
    "lgi"			=>	"<p class='msg'>Para a avaliação da prioridade  AHP <i>Salve/Atualize</i> e abra seu projeto pela página do projeto para iniciar a comparação entre pares. 
								Para a avaliação das alternativas use hierarquias já avaliádas, defina prioridades ou defina nomes para as alternativas e <i>Salve</i> através do menu de alternativas.</p>",
    "giPcmpl"	=>	"Clique em <input type='button' value='Alternativas'> depois <input class='btnr ' type='button' value='AHP'>"
);

    public $mnu	= array(
    "lgd11"		=>	"Menu de entrada da Hierarquia",
    "btn11"		=>	"Enviar",
    "btn12"		=>	"Salvar/Atualizar",
    "btn13"		=>	"Download (.csv)",
    "lbl11"		=>	"dec. comma",
    "btn14"		=>	"Resetar Prioridades",
    "btn15"		=>	"Resetar Tudo",
    "btn16"		=>	"Finalizar",
    "lgd21"		=>	"Menu de Entrada do Grupo",
    "btn21"		=>	"Salvar Julgamentos",
    "btn22"		=>	"Vizualizar Resultado do Grupo",
);
}
