<?php

class AhpNewsPT
{
    public $titles1 = array(
    'pageTitle' =>    "Notícias AHP-OS",
    'h1title'   =>    "<h1>Sistema Online AHP - BPMSG</h1>",
    'h2welc'    =>    "<h2>Bem vindo %s!</h2>",
    'h3release' =>    "<h3>Lançamento AHP-OS %s (%s)</h3>",
    'h3news2'   =>    "<h3>AHP-OS em outras línguas</h3>",
    'h3don'     =>    "<h3>Sua doação</h3>"
);

    public $msg = array(
    'tu'        =>    "Obrigada!",
    'cont'      =>    "Continuar"
);

    public $info = array(
    'news1'     =>    "<p>Esta versão mais recente do AHP-OS inclui uma funcionalidade para analisar decisões de grupo. 
                        No âmbito da <span class='hl'>Análise do Cluster de Consenso do Grupo</span> na página principal 
                        da AHP-OS, pode aceder à página AHP Consensus. O programa tenta agrupar um grupo de decisores 
                        em subgrupos mais pequenos com um consenso mais elevado. Para cada par de decisores, 
                        <span class='hl'>Shannon α e β entropia</span> é usada para calcular a semelhança de prioridades. 
                        Esta análise pode ser útil se num grupo de quatro ou mais participantes o consenso geral do grupo 
                        é baixo, mas você quer ver se o grupo pode ser dividido em subgrupos menores de participantes 
                        com maior consenso.</p>
                        <p>Mais informações <a href='https://bpmsg.com/group-consensus-cluster-analysis/' target='_blank'>aqui</a></p>",
    'news2'     =>    "<p>
                        Ainda estamos procurando voluntários para uma tradução de todos os resultados do AHP-OS para outras línguas. 
                        No momento, há suporte para Inglês, Alemão, Espanhol e Português. 
                        Se você estiver disposto a apoiar o programa,entre em contato comigo através do link de 
                        contato na parte inferior desta página.
                        </p>",
    'don'       =>    "<p>
                        Antes de começar: se você é um usuário ativo ou gosta do programa, ajude com uma doação para 
                        manter este site está vivo. Tenho custos de manutenção para hospedagem na web, certificado, 
                        proteção contra spam e manutenção e desejo manter o AHP-OS gratuito para todos os usuários. 
                        Como um doador, sua conta será mantida ativa sem a solicitação de reativação, mesmo que você 
                        não acesse por um período superior a 3 meses.
                         </p>"
);
}
