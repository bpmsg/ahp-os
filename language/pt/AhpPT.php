<?php

class AhpPT
{
    public $titles = array(
    'pageTitle'     =>    "Sistema Online AHP - AHP-OS",
    'h1title'       =>    "<h1>Sistema Online AHP - AHP-OS</h1>",
    'h2subTitle'    =>    "<h2>Tomada de decisão multicritério usando o processo analítico hierárquico</h2>",
    'h2contact'     =>    "<h2>Contato e Feedback</h2>"
);

    public $msg = array(
    'tu'    =>    "Obrigada!",
    'cont'  =>    "Continuar"
);

    public $info = array(
    'contact'    =>    "<p>Sinta-se à vontade para deixar um <a href='%s'>comentário</a>.</p>",
    'intro11'    =>    "<div class='entry-content'><p style='text-align:justify;'>
                            Esta  <b> solução AHP gratuita</b> é uma ferramenta web de suporte no processo de tomada de decisão.
                            Os programas podem ser úteis em seu trabalho diário para problemas de decisão simples e também apoiar 
                            complexos problemas de tomada de decisão. Participe de uma sessão de grupo e experimente 
                            <a href='https://bpmsg.com/participate-in-an-ahp-group-session-ahp-practical-example/'>para exemplos práticos</a>. 
                            Baixe o <a href='docs/BPMSG-AHP-OS-QuickReference.pdf' target='_blank'>quick reference guide</a> 
                            ou o <a href='docs/BPMSG-AHP-OS.pdf' target='_blank'> Manual AHP-OS</a>. 
                            Para obter a funcionalidade completa, você precisa fazer o login. 
                            Por favor <a href='includes/login/do/do-register.php'>faça seu cadastro</a> 
                            como novo usuário, se você ainda não tem uma conta. Seu acesso é grátuito!
                        </p></div>",
    'intro12'    =>    "<ol style='line-height:150%;'>
                            <li><span style='cursor:help;' 
                            title='Gerenciar projetos AHP completos e sessões de grupo. Você precisa estar cadastrado e efetuar seu login.' >
                                <a href='ahp-session-admin.php'>Meus projetos AHP</a></span></li>
                            <li><span style='cursor:help;' 
                            title='A calculadora de prioridade AHP calcula prioridades ou pesos para um conjunto de 
                            critérios com base em comparações de pares' >
                                <a href='ahp-calc.php'>AHP Priority Calculator</a></span></li>
                            <li><span style='cursor:help;' 
                            title='Lide com problemas de decisão completos no AHP. Defina uma hierarquia de critérios e avalie alternativas.' >
                                <a href='ahp-hierarchy.php'>AHP Hierarchies</a></span></li>
                            <li><span style='cursor:help;' 
                            title='Participar de sessões de grupo AHP para avaliar critérios ou alternativas como membro de um grupo' >
                            <a href='ahp-hiergini.php'>Sessão do Grupo AHP</a></span></li>
                        </ol>",
    'intro13'    =>    "<p style='text-align:justify;'>
                            Para os programas 2 e 3, você pode exportar os resultados como arquivos csv (valores 
                            separados por vírgula) para processá-las em excel. 
                            </p>",
    'intro14'    =>    "<p style='text-align:justify;'>
                            <b>Para os termos de uso, consulte nosso </b> 
                            <a href='https://bpmsg.com/about/user-agreement-and-privacy-policy/' >
                            acordo do usuário e política de privacidade.</a></p>",
    'intro15'    =>    "<p style='text-align:justify;'>
                            Se você gosta do programa, <span class='err'>por favor ajude realizando
                            <a href='ahp-news.php'>uma doação</a> para manter o site</span>.</p>",
    'intro16'    =>    "<p><b> Em o seu trabalho cite:</b><br>
                            <code>Goepel, K.D. (2018). Implementation of an Online Software Tool for the Analytic Hierarchy 
                            Process (AHP-OS). <i>International Journal of the Analytic Hierarchy Process</i>, Vol. 10 Issue 3 2018, pp 469-487,
                            <br><a href='https://doi.org/10.13033/ijahp.v10i3.590'>https://doi.org/10.13033/ijahp.v10i3.590</a>
                            </code></p>",

    'intro21'    =>     "<h3>Introduction</h3>
                            <div style='display:inline;'>
                            <img src='images/AHP-icon-150x150.png' alt='AHP' style='float: left; height:15%; width:15%; padding:5px;'>
                            </div><div class='entry-summary'><p style='text-align:justify;'>
                            AHP refere-se ao <i> Processo Analítico Hierárquico</i>. É um método para apoiar tomada 
                            de decisão segundo critérios múltiplos, e foi originalmente desenvolvido pelo Prof. 
                            Thomas L. Saaty. AHP deriva de<i>escalas de comparação</i> de comparações emparelhadas de 
                            critérios e permite algumas pequenas inconsistências em julgamentos. As entradas podem ser 
                            medidas reais, mas também opiniões subjetivas. Como resultado,
                            <i>prioridades</i> (weightings) e <i> razões de consistência </i> serão calculadas. 
                            Internacionalmente, o AHP é usado em uma ampla gama de aplicações, por exemplo, para avaliação de  
                            fornecedores, na gestão de projetos, no processo de contratação ou na avaliação de desempenho da empresa. </p></div>",

    'intro22'    =>    "<div style='display:block;clear:both;'>
                            <h3>Benefícios do AHP</h3>
                            <p style='text-align:justify;'>
                            Usar o AHP como ferramenta de apoio para a tomada de decisão ajudará a obter <i>ma visão melhor em 
                            problemas de decisão complexos</i>. Como você precisa estruturar o problema de forma hierárquica, 
                            ele o força a pensar sobre o problema, considerar possíveis critérios de decisão e selecionar
                            os critérios mais significativos em relação ao objetivo da decisão. Usando comparações de pares
                            ajuda a descobrir e corrigir inconsistências lógicas. O método também permite\"traduzir\" 
                            opiniões subjetivas, como preferências ou sentimentos, em relações numéricas mensuráveis.
                            O AHP ajuda a tomar decisões de forma mais racional e a torná-las mais transparentes e
                            mais compreensíveis.
                            </p>",

    'intro23'    =>    "<h3>Método</h3>
                            <p style='text-align:justify;'>
                            Matematicamente, o método é baseado na solução de
                            um problema de valor próprio. Os resultados das comparações entre pares são organizados em uma matriz.
                            O primeiro vetor Eigen direito normalizado (dominante) da matriz fornece a escala de razão (ponderação), o
                            O valor próprio determina a relação de consistência.
                            </p>",

    'intro24'    =>    "<h3>Exemplos AHP</h3>
                            <p style='text-align:justify;'>
                            A fim de tornar o método mais fácil de entender e mostrar o
                            ampla gama de aplicações possíveis, damos alguns <a href='ahp-examples.php' >exemplos</a> 
                            para diferentes hierarquias de decisão.
                            </p>
                            <p style='text-align:justify;'>
                            Uma introdução simples
                            ao método é dado em <a href='docs/AHP-articel.Goepel.en.pdf' target='_blank'>here</a>.
                            </p></div>"
);

    public $tbl    = array(
    'grTblTh'    =>     "\n<thead><tr class='header'><th>Participante</th>",
    'grTblTd1'   =>    "<td><strong>Resultado do grupo</strong></td>"

);
}
