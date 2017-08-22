<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('max_execution_time', '0');
include_once $_SERVER['DOCUMENT_ROOT'] . "/System/Utilitarios/Ambiente.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/System/Utilitarios/LoadClass.class.php";
include_once "RelatorioNegociosInterface.php";
$__autoload = new LoadClass();
$__autoload->carregar('Excecoes,Logs,Utilitarios,DadosFiltradosDao');
class RelatorioExcel extends Utilitarios implements RelatorioNegociosInterface {
    public function __construct() {

    }
    public function gerarRelExcel($_dateIni, $_dateFim, $_timeIni, $_timeFim, $_ramal, $_timeLim, $_tipo) {
        $values = array('dI' => $_dateIni, 'dF' => $_dateFim, 'hI' => $_timeIni, 'hF' => $_timeFim, 'ramal' => $_ramal, 'lim' => $_timeLim);
        $DadosFiltradosDao = new DadosFiltradosDao();
        $dadosTabela = null;
        $tabela = "<table border='1' cellpadding='1' cellspacing='1'>";
        $tabelaF = "</table>";
        $tr = "<tr>";
        $trF = "</tr>";
        $dadosTabela = $tabela;
        $corpo = "";
        $topo = "";
        $number_variable = 0;
        $topo = $tr;
        //$topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "IDENTIFICADOR DE CHAMADA" . "</td>";
        $topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "RAMAL/TELEFONE CLIENTE" . "</td>";
        $topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "DATA DA LIGAÇÃO" . "</td>";
        $topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "HORA INICIO DA LIGAÇÃO" . "</td>";
        //$topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "ATENDENTE" . "</td>";
        $topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "CÓDIGO DO ATENDIMENTO" . "</td>";
        $topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "RAMAL DE DESTINO" . "</td>";
        $topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "HORA INICIO DO ATENDIMENTO" . "</td>";
        $topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "HORA FIM DA LIGAÇÃO" . "</td>";
        /*$topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "TEMPO DO ATENDIMENTO" . "</td>";
        $topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "TEMPO DA LIGAÇÃO" . "</td>";
        $topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "NOME DO CLIENTE" . "</td>";
        $topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "NUMERO DO CLIENTE" . "</td>";
        $topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "NOME TRANSFERIDO" . "</td>";
        $topo .= "<td style='text-align: center; font-size: 18px; background-color: #EF1D39; color: #FFFFFF;  padding: 5px;' >" . "NUMERO TRANSFERIDO" . "</td>";*/
        $topo .= $trF;
        $resultado = $DadosFiltradosDao->findDadosGeraisExcel($values);
        $cor = "";
        $statusLigacao = "";
        while ($Object = $DadosFiltradosDao->resultObject($resultado)) {
            $cor = (($number_variable % 2) == 0) ? "#D0DCE0" : "";
            $number_variable++;
            $corpo .= $tr;
            if($Object->DESTINOCAUSA == "0" || $Object->DESTINOCAUSA == "16")
                $statusLigacao = "RECEBIDA";
            else if($Object->DESTINOCAUSA == "17")
                $statusLigacao = "OCUPADO";
            else if($Object->DESTINOCAUSA == "18")
                $statusLigacao = "NENHUM USUÁRIO RESPONDEU";
            else if($Object->DESTINOCAUSA == "19")
                $statusLigacao = "NÃO ATENDIDA";
            else if($Object->DESTINOCAUSA == "21")
                $statusLigacao = "RECUSADA";
            else if($Object->DESTINOCAUSA == "126")
                $statusLigacao = "ERRO NA TRANSFERENCIA";
            else
                $statusLigacao = "DESCONHECIDO";
            //$corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $Object->IDENTIFICADORCHAMADA . "</td>";
            //$corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $Object->ATENDENTE . "</td>";
            $corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $Object->NUMEROTELEFONE . "</td>";
            $corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $this->formatSystem($Object->DATALIGACAO) . "</td>";
            $corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $Object->HORAINICIOLIGACAO . "</td>";
            $corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $statusLigacao . "</td>";
            $corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $Object->RAMAL . "</td>";
            $corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $Object->HORAINICIOATENDIMENTO . "</td>";
            $corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $Object->HORAFIMLIGACAO . "</td>";
            /*$corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $Object->TEMPOATENDIMENTO . "</td>";
            $corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $Object->TEMPOLIGACAO . "</td>";
            $corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $Object->NOMECLIENTE . "</td>";
            $corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $Object->NOMETRANSFERIDO . "</td>";
            $corpo .= "<td style='text-align: center; font-size: 15px; background-color: ".$cor."; padding: 5px;' >" . $Object->NUMEROTRANSFERIDO . "</td>";*/
            $corpo .= $trF;
        }
        $corpo .= $trF;
        $dadosTabela .= $topo . $corpo . $tabelaF;
        return $dadosTabela;
    }
}
?>
