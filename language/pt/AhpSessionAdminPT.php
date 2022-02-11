<?php

class AhpSessionAdminPT
{
    public $titles = array(
    "pageTitle" 		=>		"Projetos AHP - AHP-OS",
    "h1title" 			=>		"<h1>Gerenciamento do Projeto AHP</h1>",
    "h2subTitle" 		=>		"<h2>AHP-OS - tomada de decisão racional facilitada</h2>",
    "h2ahpProjSummary" => "<h2>Resumo do projeto</h2>" ,
    "h2ahpSessionMenu" =>	"<h2>Menu de Sessão AHP</h2>",
    "h2ahpProjectMenu" =>	"<h2>AHP Project Menu</h2>",
    "h2myProjects" 	=> 		"<h2>Meus projetos AHP</h2>",
    "h3groupInpLnk" => 		"<h3>Link de entrada do grupo</h3>" ,
    "h3projStruc" 	=> 		"<h3>Estrutura do Projeto</h3>",
    "h4hierDefTxt" 	=> 		"<h4>Texto de definição da hierarquia</h4>",
);
    public $msg = array(
    "sDel"			=> "<span class='msg'>A Sessão <span class='var'>%s</span> 
foi excluída com sucesso</span>",
    "sDelp"			=> "Participante (s) <span class='res'>%s</span> excluído com sucesso ",
    "pwcCompl"	=> "Comparações de pares sob o nome <span class='var'>%s</span>completada.",
    "pClsd"			=>	"<p class='msg'>O projeto está encerrado. Clique em<i>Alternar o Status do Projeto</i> para reabrir o projeto.</p>",
    "pStat1"		=> "
Status do projeto alterado para ",
    "pStatO"		=> "Aberto.",
    "pStatC"		=> "Encerrado.",
    "selPart"		=> "<span class='msg'>Participante (s) selecionado (s): </span><span class='var'>%s</span>",
    "hInfo1"		=> "<span class='msg'>A hierarquia de decisão definiu  as prioridades</span>",
    "hInfo2"		=> "<span class='msg'>. O projeto pode ser usado para definir alternativas. <br>Clique em<i>Utilizar a Hierarquia</i></span>",
    "hInfo3"		=> "<span class='msg'> o projeto <span class='var'>%g</span> definio as alternativas.</span>",
    "usrStat1"	=> "<p class='msg'><small>AHP-OS tem <span class='res'>%s</span> 
Usuários cadastrados, ",
    "usrStat2"	=> "<span class='res'>%g</span> usuários ativos na última %g hora.</small></p>",
    "usrStat3"	=> "<p class='msg'>%s, você tem <span class='res'>%g</span> projectos. ",
    "usrStat4"	=> "O índice de uso do seu programa é <span class=res>%g%%</span>. ",
    "usrDon1"		=> "Por favor, considere fazer uma <a href='ahp-news.php'>doação</a>",
    "usrDon2"		=> "Obrigado pela sua doação!"
);
    public $err = array(
    "invSess1"	=> "Código de sessão inválido.",
    "invSess2"	=> "Código de sessão inválido na url.",
    "noAuth"		=> "Como você não é o autor do projeto, não possui permissão para excluir participantes.",
    "pClosed"		=> "Projeto encerrado. Nenhuma entrada de comparação de pares permitida.",
    "noDel"			=> "não pôde ser excluído.",
    "sLmt"			=> "<p><span class='err'>Limite de sessões atingido.</span> 
Exclua algumas sessões antigas primeiro. </p>"
);
    public $info = array(
    "sc"				=> "O código da sessão é<span class='var'>%s</span>.",
    "scLnk1"		=> "Forneça este código de sessão ou o seguinte link para seus participantes: </span><br>",
    "scLnk2"		=> "<textarea rows='1' cols='78'>https:%s?sc=%s</textarea><br>",
    "scLnk3"		=> "Vá para o link acima: <a href='https:%s?sc=%s' >Entrada do grupo</a><br>",
    "pOpen1"		=> "Clique no link da sessão na tabela abaixo para abrir um projeto.",
    "pOpen2"		=> "<br>Criar uma <a href='%s'>nova hierarquia</a>.",
    "logout"		=> "<div class='entry-content'>
									Na página de administração do projeto AHP, você pode gerenciar seus projetos AHP:
criar novas hierarquias, abrir, editar ou excluir e visualizar projetos existentes.
									<p class='msg'>
Você precisa ser um usuário registrado e fazer login para lidar com projetos.</p>
									<p><a href='%s'>back</a></p></div>"
);
    public $mnu = array(
    "lgd1"			=> "Menu de Administração da Sessão",
    "lbl1"			=> "Código de sessão do projeto: ",
    "btnps1"		=> "Projeto aberto",
    "btnps2"		=> "Novo Projeto",
    "btnps3"		=> "Feito",
    "lgd2"			=> "Menu de Administração do Projeto",
    "btnpa1"		=> "Ver Resultado",
    "btnpa2"		=> "Entrada PWC",
    "btnpa3"		=> "Usar Hierarquia",
    "btnpa4"		=> "Renomear",
    "btnpa5"		=> "Editar",
    "btnpa6"		=> "Deletar Part.(s)  Sel.(s)",
    "btnpa7"		=> "Deletar Projeto",
    "btnpa8"		=> "Alternar Status do Projeto",
    "btnpa9"		=> "Feito"
);
}
