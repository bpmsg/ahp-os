<?php

class AhpHierPT
{
    public $wrd = array(
    "lvl"				=>	"Nível",
    "nd"				=>	"Nó",
    "lvls"			=>	"Níveis hierárquicos",
    "lfs"				=>	"hierarquia da(s) folhas(s)",
    "nds"				=>	"hieraquia do(s) nó(s)",
    "chr"				=>	"hierarquia do(s) indivídu(os)",
    "glbP"			=>	"Glb Prio.",
    "alt"				=>	"Alternativas"
);

    public $wrn = array(
    "glbPrioS"	=> "A soma das prioridades globais diverge de 100%. Reveja a hierarquia! ",
    "prioSum"		=>	"Atenção! A soma das prioridades diverge de 100% sobre a category: %s"
);

    public $err  = array(
    "hLmt"			=>	"Limite excedido pelo programa. ",
    "hLmtLv"		=>	"Muitos níveis hierarquicos. ",
    "hLmtLf"		=>	"Muitas folhas hierárquicas. ",
    "hLmtNd"		=>	"Muitos nós hierárquicos. ",
    "hEmpty"		=>	"Campo de hierarquia vazia ou sem nó, por favor defina a hierarquia. ",
    "hSemicol"	=>	"Sinal de ponto e virgula ausente no final ",
    "hTxtlen"		=>	" Limite Max. de caractéries do texto de entra excedido! ",
    "hNoNum"		=>	"O nome das categorias/sub-categorias não pode ser um número; encontrado: ",
    "hEmptyCat"	=>	"Nome da categoria vazio ",
    "hEmptySub"	=>	"Nome da subcategoria vazio ",
    "hSubDup"		=>	"Nome(s) da(s) subcategoria(s) duplicado(s): ",
    "hNoSub"		=>	"Menos de  2 sub-categorias na categoria ",
    "hCatDup"		=>	"Nome(s) da(s) categoria(s) duplicado(s): ",
    "hColSemi"	=>	"Número diferente de <i> dois pontos </i> ae <i>pontos e virgular</i>, verifique a definição da hierarquia",
    "hHier"			=>	"Erro na hierarquia, favor verificar o texto. ",
    "hMnod"			=>	"A hierarquia começa em mais de um nó - ",
    "unkn"			=>	"<span class='err'>Unknown Error - Favor repitir a avaliação %s </span>"
);

    public $msg = array(
    "sbmPwc1"		=>	"<small><span class='msg'>Comple a comparação entre pares por favor Clique em \"AHP\")</span></small>",
    "sbmPwc2"		=>	"<small><span class='msg'>OK. Envie o grupo avaliado ou alternativa avaliada.</span></small>",
    "aPwcCmplN"	=>	"<small><span class='msg'>%g out of %g comparações  completas</span></small>",
    "aPwcCmplA"	=>	"<small><span class='msg'> Todas as avaliações estão completas.</span></small>"
);

    public $tbl	= array(
    "hTblCp"		=>	"<caption>Hierarquia de decisão</caption>",
    "aTblCp"		=>	"<caption> Hierarquia com Alternativas</caption>",
    "aTblTh"		=>	"<th>No</th><th>Node</th><th>Critério</th><th>Glb Prio.</th><th>Comparar</th>",
    "aTblTd1"		=>	"Total das alternativas ponderadas: "
);
}
