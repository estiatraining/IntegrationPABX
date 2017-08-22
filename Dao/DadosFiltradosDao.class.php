<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DadosFiltradosDao
 *
 * @author CleisonFerreira
 * @
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/System/Utilitarios/Ambiente.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/System/Utilitarios/LoadClass.class.php";
$__autoload = new LoadClass();
$__autoload->carregar('Excecoes,Logs,Utilitarios,Dao');

class DadosFiltradosDao extends Dao {

    public function __construct() {

    }

    public function findDadosGeraisExcel($_data = array()) {
        $Utilitarios = new Utilitarios();
        $_data['dI'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dI']));
        $_data['dF'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dF']));
        $this->select("DADOSFILTRADOSCONVERTIDOS", "IDENTIFICADORCHAMADA, DATALIGACAO, HORAINICIOATENDIMENTO,HORAINICIOLIGACAO, HORAFIMLIGACAO, ATENDENTE, RAMAL, TEMPOATENDIMENTO, TEMPOLIGACAO, NUMEROTELEFONE, NUMEROTRANSFERIDO, NOMETRANSFERIDO, NOMECLIENTE, ORIGINALCAUSA, DESTINOCAUSA", "WHERE dataligacao BETWEEN '" . $_data['dI'] . "' AND '".$_data['dF']."' ORDER BY dataligacao, atendente");
        $result = $this->executeS();
        if ($result)
            return $result;
        else
            return false;
    }

    public function chamadasAtendidasPerdidasHora($_data = array()) {
        $Utilitarios = new Utilitarios();
        $vetorDados = $Utilitarios->getRamais();
        $ramaisPABX = NULL;
        $countRamais = 0;
        $countRamais2 = 0;
        foreach ($vetorDados as $ramal => $nome) {
            $countRamais++;
        }
        foreach ($vetorDados as $ramal => $nome) {
            if ($countRamais2 == ( $countRamais - 1 ))
                $auxVirg = "";
            else
                $auxVirg = ", ";
            $ramaisPABX .= $ramal . $auxVirg;
            $countRamais2++;
        }
        $_data['dI'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dI']));
        $_data['dF'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dF']));
        $_data['hI'] = $Utilitarios->antiInjection($_data['hI']);
        $_data['hF'] = $Utilitarios->antiInjection($_data['hF']);
        $_data['ramal'] = ( $Utilitarios->antiInjection($_data['ramal']) == "" or $Utilitarios->antiInjection($_data['ramal']) == null ) ? $ramaisPABX : $Utilitarios->antiInjection($_data['ramal']);
        $sql = "";
        /*
         * Filtros
         */
        $sqlRamal = "";
        if ($_data['dI'] != "" && $_data['dF'] != "") {
            $sql = "WHERE dataligacao BETWEEN '" . $_data['dI'] . "' AND '" . $_data['dF'] . "' ";
        }
        if ($_data['ramal'] != "") {
            $sql .= "AND ramal IN (" . $_data['ramal'] . ") ";
            //$sqlRamal = "AND ramal IN (" . $_data['ramal'] . ") ";
        }
        if ($_data['hI'] != "" && $_data['hF'] != "") {
            $sql .= "AND horainicioatendimento BETWEEN '" . $_data['hI'] . "' AND '" . $_data['hF'] . "' ";
        }
        /*
         * Total de ligaçoes
         */
        $sql1 = $sql . "AND destinocausa IN (0, 16, 17, 18, 19, 21) ";
        $result = array();
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade", $sql1);
        $result['totalLigacoes'] = $this->executeS();
        /*
         * Total de ligações recebidas
         */
        $sql2 = $sql . "AND destinocausa IN (0, 16) ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade", $sql2);
        $result['totalLigacoesRecebidas'] = $this->executeS();
        /*
         * Total de ligaçoes perdidas
         */
        $sql3 = $sql . "AND destinocausa IN (17, 18, 19, 21) ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade", $sql3);
        $result['totalLigacoesPerdidas'] = $this->executeS();
        /*
         * Total de ligaçoes recebidas por ramal
         */
        $sql4 = $sql . "AND destinocausa IN (0, 16) GROUP BY ramal ORDER BY quantidade";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(ramal) AS quantidade, ramal", $sql4);
        $result['totalLigacoesRecebidasRamal'] = $this->executeS();
        /*
         * Total de ligaçoes perdidas por ramal
         */
        $sql5 = $sql . "AND destinocausa IN (17, 18, 19, 21)  GROUP BY ramal ORDER BY quantidade";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(ramal) AS quantidade, ramal", $sql5);
        $result['totalLigacoesPerdidasRamal'] = $this->executeS();
        /*
         * Total de ligaçoes Recebidas por dia
         */
        $sql7 = $sql . "AND destinocausa IN (0, 16)  GROUP BY EXTRACT(DAY FROM DATALIGACAO ) ORDER BY quantidade";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade, EXTRACT(DAY FROM DATALIGACAO ) AS dia, EXTRACT(MONTH FROM DATALIGACAO ) AS mes, EXTRACT(YEAR FROM DATALIGACAO ) AS ano", $sql7);
        $result['totalLigacoesRecebidasDia'] = $this->executeS();
        /*
         * Quantidade de ligaçoes atendidas por hora
         */
        $sql6 = $sql . "AND destinocausa IN (0, 16)  GROUP BY EXTRACT(HOUR FROM horainicioatendimento ) ORDER BY hora, ramal";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT( * ) AS quantidade, EXTRACT(HOUR FROM HORAINICIOATENDIMENTO ) AS hora ", $sql6);
        $result['totalLigacoesRecebidasHora'] = $this->executeS();
        /*
         * Quantidade de ligaçoes perdidas por hora
         */
        $sql8 = $sql . "AND destinocausa IN (17, 18, 19, 21) GROUP BY EXTRACT(HOUR FROM horainicioatendimento ) ORDER BY hora, ramal";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT( * ) AS quantidade, EXTRACT(HOUR FROM HORAINICIOATENDIMENTO ) AS hora ", $sql8);
        $result['totalLigacoesPerdidasHora'] = $this->executeS();
        /*
         * Quantidade de ligaçoes perdidas por hora
         */
        $sql9 = $sql . "AND A.destinocausa IN (0, 16, 17, 18, 19, 21) GROUP BY A.DATALIGACAO ORDER BY A.DATALIGACAO";
        $this->select("dadosfiltradosconvertidos A", "COUNT(*) AS QUANT_CHAMADAS, (SELECT COUNT(*) AS quantidade FROM TELEFONIA.dadosfiltradosconvertidos B WHERE B.dataligacao = A.dataligacao
                    AND destinocausa IN (0, 16) " . $sqlRamal . " GROUP BY EXTRACT(DAY FROM DATALIGACAO )) AS QUANT_RECEBIDAS,
                    (SELECT COUNT(*) AS quantidade FROM TELEFONIA.dadosfiltradosconvertidos B WHERE B.dataligacao = A.dataligacao AND destinocausa IN (17, 18, 19, 21) " . $sqlRamal . " GROUP BY EXTRACT(DAY FROM DATALIGACAO )) AS QUANT_PERDIDAS, SEC_TO_TIME( SUM( TIME_TO_SEC( tempoligacao )  ) / COUNT(*) )
                    AS ESPERA, SEC_TO_TIME( SUM( TIME_TO_SEC( tempoatendimento ) ) / COUNT(*) ) AS  DURACAO,
                    EXTRACT(DAY FROM A.DATALIGACAO ) AS dia, EXTRACT(MONTH FROM A.DATALIGACAO ) AS mes, EXTRACT(YEAR FROM A.DATALIGACAO ) AS ano ", $sql9);
        $result['listagemLigacoesDia'] = $this->executeS();
        /*
         * Quantidade de ligaçoes perdidas por hora
         */
        $sql10 = $sql . "AND destinocausa IN (0, 16, 17, 18, 19, 21) GROUP BY A.DATALIGACAO, EXTRACT(HOUR FROM horainicioatendimento ) ORDER BY dataligacao, hora";
        $this->select("dadosfiltradosconvertidos A", "dataligacao, EXTRACT(HOUR FROM HORAINICIOATENDIMENTO ) AS hora,COUNT( * ) AS QUANT_CHAMADAS,
                    (SELECT COUNT(*) AS QUANTIDADE FROM TELEFONIA.dadosfiltradosconvertidos B WHERE A.dataligacao = B.dataligacao  " . $sqlRamal . "
                    AND EXTRACT(HOUR FROM A.horainicioatendimento ) = EXTRACT(HOUR FROM B.horainicioatendimento )
                    AND ( destinocausa IN (0, 16)   ) GROUP BY EXTRACT(DAY FROM A.DATALIGACAO ),
                    EXTRACT(HOUR FROM horainicioatendimento ) ) AS QUANT_RECEBIDAS,
                    (SELECT COUNT(*) AS QUANTIDADE FROM TELEFONIA.dadosfiltradosconvertidos B WHERE A.dataligacao = B.dataligacao  " . $sqlRamal . "
                    AND EXTRACT(HOUR FROM A.horainicioatendimento ) = EXTRACT(HOUR FROM B.horainicioatendimento )
                    AND ( destinocausa IN (17, 18, 19, 21)   ) GROUP BY EXTRACT(DAY FROM A.DATALIGACAO ),
                    EXTRACT(HOUR FROM horainicioatendimento ) ) AS QUANT_PERDIDAS ", $sql10);
        $result['listagemLigacoesHora'] = $this->executeS();
        $sql11 = $sql . "AND destinocausa IN (0, 16)  GROUP BY EXTRACT(HOUR FROM horainicioatendimento ) ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT( * ) AS quantidade, EXTRACT(HOUR FROM HORAINICIOATENDIMENTO ) AS hora ,
                    SEC_TO_TIME( SUM( TIME_TO_SEC( tempoatendimento ) ) / COUNT(*) ) AS  DURACAO ", $sql11);
        $result['totalLigacoesHora'] = $this->executeS();
        $sql12 = $sql . "AND destinocausa IN (0, 16)  AND tempoligacao >= '00:00:30' GROUP BY EXTRACT(HOUR FROM horainicioatendimento ) ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT( * ) AS quantidade, EXTRACT(HOUR FROM HORAINICIOATENDIMENTO ) AS hora ,
                    SEC_TO_TIME( SUM( TIME_TO_SEC( tempoatendimento ) ) / COUNT(*) ) AS  DURACAO ", $sql12);
        $result['totalLigacoesHoraEspera'] = $this->executeS();
        if ($result)
            return $result;
        else
            return false;
    }

    public function taxasDesempenho($_data = array()) {
        $Utilitarios = new Utilitarios();
        $vetorDados = $Utilitarios->getRamais();
        $ramaisPABX = NULL;
        $countRamais = 0;
        $countRamais2 = 0;
        foreach ($vetorDados as $ramal => $nome) {
            $countRamais++;
        }
        foreach ($vetorDados as $ramal => $nome) {
            if ($countRamais2 == ( $countRamais - 1 ))
                $auxVirg = "";
            else
                $auxVirg = ", ";
            $ramaisPABX .= $ramal . $auxVirg;
            $countRamais2++;
        }
        $_data['dI'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dI']));
        $_data['dF'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dF']));
        $_data['hI'] = $Utilitarios->antiInjection($_data['hI']);
        $_data['hF'] = $Utilitarios->antiInjection($_data['hF']);
        $_data['ramal'] = ( $Utilitarios->antiInjection($_data['ramal']) == "" or $Utilitarios->antiInjection($_data['ramal']) == null ) ? $ramaisPABX : $Utilitarios->antiInjection($_data['ramal']);
        $sql = "";
        if ($_data['dI'] != "" && $_data['dF'] != "") {
            $sql = "WHERE dataligacao BETWEEN '" . $_data['dI'] . "' AND '" . $_data['dF'] . "' ";
        }
        if ($_data['hI'] != "" && $_data['hF'] != "") {
            $sql .= "AND horainicioatendimento BETWEEN '" . $_data['hI'] . "' AND '" . $_data['hF'] . "' ";
        }
        if ($_data['ramal'] != "") {
            $sql .= "AND ramal IN (" . $_data['ramal'] . ") ";
        }
        $result = array();
        $sql2 = $sql . "AND destinocausa IN (0, 16)  AND TEMPOLIGACAO > '00:00:30' ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade", $sql2);
        $result['superior'] = $this->executeS();

        $sql3 = $sql . "AND destinocausa IN (0, 16)  AND TEMPOLIGACAO <= '00:00:30' ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade", $sql3);
        $result['inferior'] = $this->executeS();
        $values = array();
        if ($result) {
            while ($Superior = $this->resultObject($result['superior'])) {
                $values['superior'] = $Superior->quantidade;
                break;
            }
            while ($Inferior = $this->resultObject($result['inferior'])) {
                $values['inferior'] = $Inferior->quantidade;
                break;
            }
            return $values;
        }
        else
            return false;
    }

    public function relAnalitRamal($_data = array()) {
        $Utilitarios = new Utilitarios();
        $vetorDados = $Utilitarios->getRamais();
        $ramaisPABX = NULL;
        $countRamais = 0;
        $countRamais2 = 0;
        foreach ($vetorDados as $ramal => $nome) {
            $countRamais++;
        }
        foreach ($vetorDados as $ramal => $nome) {
            if ($countRamais2 == ( $countRamais - 1 ))
                $auxVirg = "";
            else
                $auxVirg = ", ";
            $ramaisPABX .= $ramal . $auxVirg;
            $countRamais2++;
        }
        $_data['dI'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dI']));
        $_data['dF'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dF']));
        $_data['hI'] = $Utilitarios->antiInjection($_data['hI']);
        $_data['hF'] = $Utilitarios->antiInjection($_data['hF']);
        $_data['ramal'] = ( $Utilitarios->antiInjection($_data['ramal']) == "" or $Utilitarios->antiInjection($_data['ramal']) == null ) ? $ramaisPABX : $Utilitarios->antiInjection($_data['ramal']);
        $_data['lim'] = $Utilitarios->transfSeconds($Utilitarios->antiInjection($_data['lim']));
        $sql = "";
        if ($_data['dI'] != "" && $_data['dF'] != "") {
            $sql = "WHERE dataligacao BETWEEN '" . $_data['dI'] . "' AND '" . $_data['dF'] . "' ";
        }
        if ($_data['ramal'] != "") {
            $sql .= "AND ramal IN (" . $_data['ramal'] . ") ";
        }
        if ($_data['hI'] != "" && $_data['hF'] != "") {
            $sql .= "AND horainicioatendimento BETWEEN '" . $_data['hI'] . "' AND '" . $_data['hF'] . "' ";
        }
        if ($_data['lim'] != "") {
            $sql .= "AND (( tempoligacao >= '" . $_data['lim'] . "' )) ";
        }
        $sql .= "AND destinocausa IN (0, 16) ORDER BY dataligacao, horainicioatendimento";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "*", $sql);
        $result = $this->executeS();
        if ($result)
            return $result;
        else
            return false;
    }

    public function relSintetRamal($_data = array()) {
        $Utilitarios = new Utilitarios();
        $vetorDados = $Utilitarios->getRamais();
        $ramaisPABX = NULL;
        $countRamais = 0;
        $countRamais2 = 0;
        foreach ($vetorDados as $ramal => $nome) {
            $countRamais++;
        }
        foreach ($vetorDados as $ramal => $nome) {
            if ($countRamais2 == ( $countRamais - 1 ))
                $auxVirg = "";
            else
                $auxVirg = ", ";
            $ramaisPABX .= $ramal . $auxVirg;
            $countRamais2++;
        }
        $_data['dI'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dI']));
        $_data['dF'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dF']));
        $_data['hI'] = $Utilitarios->antiInjection($_data['hI']);
        $_data['hF'] = $Utilitarios->antiInjection($_data['hF']);
        $_data['ramal'] = ( $Utilitarios->antiInjection($_data['ramal']) == "" or $Utilitarios->antiInjection($_data['ramal']) == null ) ? $ramaisPABX : $Utilitarios->antiInjection($_data['ramal']);
        $_data['lim'] = $Utilitarios->transfSeconds($Utilitarios->antiInjection($_data['lim']));
        if ($_data['dI'] == $_data['dF']) {
            $media = 16;
        } else {
            $dI = explode("-", $_data['dI']);
            $dF = explode("-", $_data['dF']);
            $dia1 = mktime(0, 0, 0, $dI[2], $dI[1], $dI[0]);
            $dia2 = mktime(0, 0, 0, $dF[2], $dF[1], $dF[0]);
            $d3 = ( $dia2 - $dia1 );
            $dias = round(($d3 / 60 / 60 / 24));
            $media = ( $dias * 16 );
        }
        $sql = "";
        if ($_data['dI'] != "" && $_data['dF'] != "") {
            $sql = "WHERE dataligacao BETWEEN '" . $_data['dI'] . "' AND '" . $_data['dF'] . "' ";
        }
        if ($_data['ramal'] != "") {
            $sql .= "AND ramal IN (" . $_data['ramal'] . ") ";
        }
        if ($_data['hI'] != "" && $_data['hF'] != "") {
            $sql .= "AND horainicioatendimento BETWEEN '" . $_data['hI'] . "' AND '" . $_data['hF'] . "' ";
        }
        if ($_data['lim'] != "") {
            $sql .= "AND (( tempoligacao >= '" . $_data['lim'] . "' )) ";
        }
        $result = array();
        $sql2 = $sql . "AND destinocausa IN (0, 16)  ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade", $sql2);
        $result['totalLigacoesRecebidas'] = $this->executeS();

        $sql3 = $sql . "AND destinocausa IN (17, 18, 19, 21)  ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade", $sql3);
        $result['totalLigacoesPerdidas'] = $this->executeS();

        $sql4 = $sql . "AND destinocausa IN (0, 16)  ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "SEC_TO_TIME( SUM( TIME_TO_SEC( tempoligacao ) ) / COUNT(*) ) AS media", $sql4);
        $result['mediaAtendimento'] = $this->executeS();

        $sql5 = $sql . "AND destinocausa IN (0, 16)  ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "SEC_TO_TIME( SUM( TIME_TO_SEC( tempoatendimento ) ) / COUNT(*) ) AS media", $sql5);
        $result['mediaLigacao'] = $this->executeS();

        $sql6 = $sql . "AND destinocausa IN (0, 16)  GROUP BY EXTRACT(HOUR FROM horainicioligacao ) ORDER BY quantidade DESC LIMIT 1 ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade, EXTRACT(DAY FROM dataligacao ) AS dia,  EXTRACT(MONTH FROM dataligacao ) AS mes, EXTRACT(YEAR FROM dataligacao ) AS ano", $sql6);
        $result['totalLigacaoDiaMais'] = $this->executeS();

        $sql7 = $sql . "AND destinocausa IN (0, 16)  GROUP BY EXTRACT(HOUR FROM horainicioligacao ) ORDER BY quantidade ASC LIMIT 1 ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade, EXTRACT(DAY FROM dataligacao ) AS dia,  EXTRACT(MONTH FROM dataligacao ) AS mes, EXTRACT(YEAR FROM dataligacao ) AS ano", $sql7);
        $result['totalLigacaoDiaMenos'] = $this->executeS();
        if ($result)
            return $result;
        else
            return false;
    }

    public function relSintetCompleto($_data = array()) {
        $Utilitarios = new Utilitarios();
        $vetorDados = $Utilitarios->getRamais();
        $ramaisPABX = NULL;
        $countRamais = 0;
        $countRamais2 = 0;
        foreach ($vetorDados as $ramal => $nome) {
            $countRamais++;
        }
        foreach ($vetorDados as $ramal => $nome) {
            if ($countRamais2 == ( $countRamais - 1 ))
                $auxVirg = "";
            else
                $auxVirg = ", ";
            $ramaisPABX .= $ramal . $auxVirg;
            $countRamais2++;
        }
        $_data['dI'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dI']));
        $_data['dF'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dF']));
        $_data['hI'] = $Utilitarios->antiInjection($_data['hI']);
        $_data['hF'] = $Utilitarios->antiInjection($_data['hF']);
        $_data['ramal'] = ( $Utilitarios->antiInjection($_data['ramal']) == "" or $Utilitarios->antiInjection($_data['ramal']) == null ) ? $ramaisPABX : $Utilitarios->antiInjection($_data['ramal']);
        $_data['lim'] = $Utilitarios->transfSeconds($Utilitarios->antiInjection($_data['lim']));
        $sql = "";
        if ($_data['dI'] != "" && $_data['dF'] != "") {
            $sql = "WHERE dataligacao BETWEEN '" . $_data['dI'] . "' AND '" . $_data['dF'] . "' ";
        }
        if ($_data['hI'] != "" && $_data['hF'] != "") {
            $sql .= "AND horainicioatendimento BETWEEN '" . $_data['hI'] . "' AND '" . $_data['hF'] . "' ";
        }
        if ($_data['lim'] != "") {
            $sql .= "AND (( tempoligacao >= '" . $_data['lim'] . "' )) ";
        }
        if ($_data['ramal'] != "") {
            $sql .= "AND ramal IN (" . $_data['ramal'] . ") ";
        }
        /*
         * Quantidade de ligaçoes
         */
        $sql1 = $sql . "AND destinocausa IN (0, 16) ";
        $result = array();
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade", $sql1);
        $result['totalLigacoes'] = $this->executeS();
        /*
         * Quantidade de ligaçoes recebidas
         */
        $sql2 = $sql . "AND destinocausa IN (0, 16)  ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade", $sql2);
        $result['totalLigacoesRecebidas'] = $this->executeS();
        /*
         * Quantidade ligaçoes perdidas
         */
        $sql3 = $sql . "AND destinocausa IN (17, 18, 19, 21) ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade", $sql3);
        $result['totalLigacoesPerdidas'] = $this->executeS();
        /*
         * Total de ligaçoes recebidas por ramal
         */
        $sql4 = $sql . "AND destinocausa IN (0, 16)  GROUP BY ramal ORDER BY quantidade DESC LIMIT 8 ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(ramal) AS quantidade, ramal", $sql4);
        $result['totalLigacoesRecebidasRamalMaior'] = $this->executeS();
        /*
         * Total de ligaçoes perdidas por ramal
         */
        $sql5 = $sql . "AND destinocausa IN (17, 18, 19, 21)  GROUP BY ramal ORDER BY quantidade DESC LIMIT 8 ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(ramal) AS quantidade, ramal", $sql5);
        $result['totalLigacoesPerdidasRamal'] = $this->executeS();
        /*
         * Total de
         */
        $sql6 = $sql . "AND destinocausa IN (0, 16)  GROUP BY ramal ORDER BY quantidade ASC LIMIT 8 ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(ramal) AS quantidade, ramal", $sql6);
        $result['totalLigacoesRecebidasRamalMenor'] = $this->executeS();

        $sql7 = $sql . "AND destinocausa IN (0, 16)  GROUP BY EXTRACT(DAY FROM DATALIGACAO ) ORDER BY quantidade DESC LIMIT 1 ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade, EXTRACT(DAY FROM DATALIGACAO ) AS dia, EXTRACT(MONTH FROM DATALIGACAO ) AS mes, EXTRACT(YEAR FROM DATALIGACAO ) AS ano", $sql7);
        $result['totalLigacoesRecebidasDiaMaior'] = $this->executeS();

        $sql8 = $sql . "AND destinocausa IN (0, 16)  GROUP BY EXTRACT(DAY FROM DATALIGACAO ) ORDER BY quantidade ASC LIMIT 1 ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade, EXTRACT(DAY FROM DATALIGACAO ) AS dia, EXTRACT(MONTH FROM DATALIGACAO ) AS mes, EXTRACT(YEAR FROM DATALIGACAO ) AS ano", $sql8);
        $result['totalLigacoesRecebidasDiaMenor'] = $this->executeS();

        $sql9 = $sql . "AND destinocausa IN (0, 16)  GROUP BY EXTRACT(HOUR FROM horainicioligacao ) ORDER BY quantidade DESC LIMIT 5 ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade, EXTRACT(HOUR FROM horainicioligacao ) AS hora,EXTRACT(DAY FROM DATALIGACAO ) AS dia, EXTRACT(MONTH FROM DATALIGACAO ) AS mes, EXTRACT(YEAR FROM DATALIGACAO ) AS ano", $sql9);
        $result['totalLigacoesRecebidasHoraMaior'] = $this->executeS();

        $sql9 = $sql . "AND destinocausa IN (0, 16)  GROUP BY EXTRACT(HOUR FROM horainicioligacao ) ORDER BY quantidade ASC LIMIT 5 ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(*) AS quantidade, EXTRACT(HOUR FROM horainicioligacao ) AS hora,EXTRACT(DAY FROM DATALIGACAO ) AS dia, EXTRACT(MONTH FROM DATALIGACAO ) AS mes, EXTRACT(YEAR FROM DATALIGACAO ) AS ano", $sql9);
        $result['totalLigacoesRecebidasHoraMenor'] = $this->executeS();
        if ($result)
            return $result;
        else
            return false;
    }

    public function relAnalitCompleto($_data = array()) {
        $Utilitarios = new Utilitarios();
        $vetorDados = $Utilitarios->getRamais();
        $ramaisPABX = NULL;
        $countRamais = 0;
        $countRamais2 = 0;
        foreach ($vetorDados as $ramal => $nome) {
            $countRamais++;
        }
        foreach ($vetorDados as $ramal => $nome) {
            if ($countRamais2 == ( $countRamais - 1 ))
                $auxVirg = "";
            else
                $auxVirg = ", ";
            $ramaisPABX .= $ramal . $auxVirg;
            $countRamais2++;
        }
        $_data['dI'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dI']));
        $_data['dF'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dF']));
        $_data['hI'] = $Utilitarios->antiInjection($_data['hI']);
        $_data['hF'] = $Utilitarios->antiInjection($_data['hF']);
        $_data['ramal'] = ( $Utilitarios->antiInjection($_data['ramal']) == "" or $Utilitarios->antiInjection($_data['ramal']) == null ) ? $ramaisPABX : $Utilitarios->antiInjection($_data['ramal']);
        $_data['lim'] = $Utilitarios->transfSeconds($Utilitarios->antiInjection($_data['lim']));
        $sql = "";
        if ($_data['dI'] != "" && $_data['dF'] != "") {
            $sql = "WHERE dataligacao BETWEEN '" . $_data['dI'] . "' AND '" . $_data['dF'] . "' ";
        }
        if ($_data['hI'] != "" && $_data['hF'] != "") {
            $sql .= "AND horainicioatendimento BETWEEN '" . $_data['hI'] . "' AND '" . $_data['hF'] . "' ";
        }
        if ($_data['lim'] != "") {
            $sql .= "AND (( tempoligacao >= '" . $_data['lim'] . "' )) ";
        }
        if ($_data['ramal'] != "") {
            $sql .= "AND ramal IN (" . $_data['ramal'] . ") ";
        }
        $sql .= "AND destinocausa IN (0, 16) ORDER BY ramal, dataligacao, horainicioligacao ";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "*", $sql);
        $result = $this->executeS();
        if ($result)
            return $result;
        else
            return false;
    }

    public function numTotalLigacoesRamal($_quantidade) {
        $sql = "WHERE ( SELECT COUNT(*) AS quantidade FROM dadosfiltradosconvertidos d2 WHERE d1.ramal = d2.ramal GROUP BY d2.ramal ) > " . $_quantidade . " ";
        $sql .= "GROUP BY d1.ramal ORDER BY quantidade desc ";
        $this->select("dadosfiltradosconvertidos d1", "COUNT(*) AS quantidade, d1.ramal AS ramal", $sql);
        $result = $this->executeS();
        if ($result)
            return $result;
        else
            return false;
    }

    public function numTotalLigacoesRecebidasRamal($_data = array()) {
        $Utilitarios = new Utilitarios();
        $vetorDados = $Utilitarios->getRamais();
        $ramaisPABX = NULL;
        $countRamais = 0;
        $countRamais2 = 0;
        foreach ($vetorDados as $ramal => $nome) {
            $countRamais++;
        }
        foreach ($vetorDados as $ramal => $nome) {
            if ($countRamais2 == ( $countRamais - 1 ))
                $auxVirg = "";
            else
                $auxVirg = ", ";
            $ramaisPABX .= $ramal . $auxVirg;
            $countRamais2++;
        }
        $_data['dI'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dI']));
        $_data['dF'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dF']));
        $_data['hI'] = $Utilitarios->antiInjection($_data['hI']);
        $_data['hF'] = $Utilitarios->antiInjection($_data['hF']);
        $_data['ramal'] = ( $Utilitarios->antiInjection($_data['ramal']) == "" or $Utilitarios->antiInjection($_data['ramal']) == null ) ? $ramaisPABX : $Utilitarios->antiInjection($_data['ramal']);
        $_data['lim'] = $Utilitarios->transfSeconds($Utilitarios->antiInjection($_data['lim']));
        $sql = "";
        if ($_data['dI'] != "" && $_data['dF'] != "") {
            $sql = "WHERE dataligacao BETWEEN '" . $_data['dI'] . "' AND '" . $_data['dF'] . "' ";
        }
        if ($_data['hI'] != "" && $_data['hF'] != "") {
            $sql .= "AND horainicioatendimento BETWEEN '" . $_data['hI'] . "' AND '" . $_data['hF'] . "' ";
        }
        if ($_data['ramal'] != "") {
            $sql .= "AND ramal IN (" . $_data['ramal'] . ") ";
        }
        if ($_data['lim'] != "") {
            $sql .= "AND (( tempoligacao >= '" . $_data['lim'] . "' )) ";
        }
        $sql .= "AND  destinocausa IN (0, 16) ";
        $sql .= "GROUP BY DATALIGACAO ORDER BY DIA, ramal";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "count( * ) as quantidade, EXTRACT(DAY FROM DATALIGACAO ) as dia", $sql);
        $result = $this->executeS();
        if ($result)
            return $result;
        else
            return false;
    }

    public function numTotalLigacoesPerdidasRamal($_data = array()) {
        $Utilitarios = new Utilitarios();
        $vetorDados = $Utilitarios->getRamais();
        $ramaisPABX = NULL;
        $countRamais = 0;
        $countRamais2 = 0;
        foreach ($vetorDados as $ramal => $nome) {
            $countRamais++;
        }
        foreach ($vetorDados as $ramal => $nome) {
            if ($countRamais2 == ( $countRamais - 1 ))
                $auxVirg = "";
            else
                $auxVirg = ", ";
            $ramaisPABX .= $ramal . $auxVirg;
            $countRamais2++;
        }
        $_data['dI'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dI']));
        $_data['dF'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dF']));
        $_data['ramal'] = ( $Utilitarios->antiInjection($_data['ramal']) == "" or $Utilitarios->antiInjection($_data['ramal']) == null ) ? $ramaisPABX : $Utilitarios->antiInjection($_data['ramal']);
        $sql = "";
        if ($_data['dI'] != "" && $_data['dF'] != "") {
            $sql = "WHERE dataligacao BETWEEN '" . $_data['dI'] . "' AND '" . $_data['dF'] . "' ";
        }
        if ($_data['ramal'] != "") {
            $sql .= "AND ramal IN (" . $_data['ramal'] . ") ";
        }
        $sql .= "AND destinocausa IN (17, 18, 19, 21) ";
        $sql .= "GROUP BY DATALIGACAO ORDER BY DIA, ramal";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "count( * ) as quantidade, EXTRACT(DAY FROM DATALIGACAO ) as dia", $sql);
        $result = $this->executeS();
        if ($result)
            return $result;
        else
            return false;
    }

    public function numTotalLigacoesRecebidas($_data = array()) {
        $Utilitarios = new Utilitarios();
        $vetorDados = $Utilitarios->getRamais();
        $ramaisPABX = NULL;
        $countRamais = 0;
        $countRamais2 = 0;
        foreach ($vetorDados as $ramal => $nome) {
            $countRamais++;
        }
        foreach ($vetorDados as $ramal => $nome) {
            if ($countRamais2 == ( $countRamais - 1 ))
                $auxVirg = "";
            else
                $auxVirg = ", ";
            $ramaisPABX .= $ramal . $auxVirg;
            $countRamais2++;
        }
        $_data['dI'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dI']));
        $_data['dF'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dF']));
        $_data['hI'] = $Utilitarios->antiInjection($_data['hI']);
        $_data['hF'] = $Utilitarios->antiInjection($_data['hF']);
        $_data['ramal'] = ( $Utilitarios->antiInjection($_data['ramal']) == "" or $Utilitarios->antiInjection($_data['ramal']) == null ) ? $ramaisPABX : $Utilitarios->antiInjection($_data['ramal']);
        $_data['lim'] = $Utilitarios->transfSeconds($Utilitarios->antiInjection($_data['lim']));
        $sql = "";
        if ($_data['dI'] != "" && $_data['dF'] != "") {
            $sql = "WHERE dataligacao BETWEEN '" . $_data['dI'] . "' AND '" . $_data['dF'] . "' ";
        }
        if ($_data['hI'] != "" && $_data['hF'] != "") {
            $sql .= "AND horainicioatendimento BETWEEN '" . $_data['hI'] . "' AND '" . $_data['hF'] . "' ";
        }
        if ($_data['ramal'] != "") {
            $sql .= "AND ramal IN (" . $_data['ramal'] . ") ";
        }
        if ($_data['lim'] != "") {
            $sql .= "AND (( tempoligacao >= '" . $_data['lim'] . "' )) ";
        }
        $sql .= "AND destinocausa IN (0, 16) ";
        $sql .= "GROUP BY EXTRACT(HOUR FROM horainicioatendimento ) ORDER BY hora, ramal";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "count( * ) as quantidade, EXTRACT(HOUR FROM HORAINICIOATENDIMENTO ) as hora", $sql);
        $result = $this->executeS();
        if ($result)
            return $result;
        else
            return false;
    }

    public function numTotalLigacoesPerdidas($_data = array()) {
        $Utilitarios = new Utilitarios();
        $vetorDados = $Utilitarios->getRamais();
        $ramaisPABX = NULL;
        $countRamais = 0;
        $countRamais2 = 0;
        foreach ($vetorDados as $ramal => $nome) {
            $countRamais++;
        }
        foreach ($vetorDados as $ramal => $nome) {
            if ($countRamais2 == ( $countRamais - 1 ))
                $auxVirg = "";
            else
                $auxVirg = ", ";
            $ramaisPABX .= $ramal . $auxVirg;
            $countRamais2++;
        }
        $_data['dI'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dI']));
        $_data['dF'] = $Utilitarios->formatDataBank($Utilitarios->antiInjection($_data['dF']));
        $_data['ramal'] = ( $Utilitarios->antiInjection($_data['ramal']) == "" or $Utilitarios->antiInjection($_data['ramal']) == null ) ? $ramaisPABX : $Utilitarios->antiInjection($_data['ramal']);
        $sql = "";
        if ($_data['dI'] != "" && $_data['dF'] != "") {
            $sql = "WHERE dataligacao BETWEEN '" . $_data['dI'] . "' AND '" . $_data['dF'] . "' ";
        }
        if ($_data['ramal'] != "") {
            $sql .= "AND ramal IN (" . $_data['ramal'] . ") ";
        }
        $sql .= "AND  destinocausa IN (17, 18, 19, 21) ";
        $sql .= "GROUP BY EXTRACT(HOUR FROM horainicioatendimento ) ORDER BY hora, ramal";
        $this->select("DADOSFILTRADOSCONVERTIDOS", "count( * ) as quantidade, EXTRACT(HOUR FROM HORAINICIOATENDIMENTO ) as hora", $sql);
        $result = $this->executeS();
        if ($result)
            return $result;
        else
            return false;
    }

    public function findDataRamal() {
        $this->select("DADOSFILTRADOSCONVERTIDOS", "DISTINCT(ramal) AS ramal", "ORDER BY ramal");
        $result = $this->executeS();
        if ($result)
            return $result;
        else
            return false;
    }

    public function findData() {
        $this->select("DADOSFILTRADOSCONVERTIDOS", "*", "ORDER BY dataligacao");
        $result = $this->executeS();
        if ($result) {
            return $result;
        }
        else
            return false;
    }

    public function findIdDadosFiltrados($_id) {
        $Utilitarios = new Utilitarios();
        $_id = $Utilitarios->antiInjection($_id);
        $this->select("DADOSFILTRADOSCONVERTIDOS", "*", "WHERE dafcid = " . $_id);
        $result = $this->executeS();
        if ($result)
            return $this->resultObject($result);
        else
            return false;
    }

    public function findIdentificadorDadosFiltrados($_id) {
        $Utilitarios = new Utilitarios();
        $_id = $Utilitarios->antiInjection($_id);
        $this->select("DADOSFILTRADOSCONVERTIDOS", "COUNT(DAFCID) AS DAFCID ", "WHERE identificadorchamada = " . $_id);
        $result = $this->executeS();
        if ($result)
            return $this->resultObject($result);
        else
            return false;
    }

    public function insertData($_values = array()) {
        $Utilitarios = new Utilitarios();
        $_values['dataligacao'] = $Utilitarios->antiInjection($_values['dataligacao']);
        $_values['horainicioligacao'] = $Utilitarios->antiInjection($_values['horainicioligacao']);
        $_values['horainicioatendimento'] = $Utilitarios->antiInjection($_values['horainicioatendimento']);
        $_values['horafimligacao'] = $Utilitarios->antiInjection($_values['horafimligacao']);
        $_values['atendente'] = $Utilitarios->stringMaiusculo($Utilitarios->antiInjection($_values['atendente']));
        $_values['ramal'] = $Utilitarios->removCaracter($Utilitarios->antiInjection($_values['ramal']));
        $_values['tempoatendimento'] = $Utilitarios->antiInjection($_values['tempoatendimento']);
        $_values['tempoligacao'] = $Utilitarios->antiInjection($_values['tempoligacao']);
        $_values['observacao'] = $Utilitarios->stringMaiusculo($Utilitarios->antiInjection($_values['observacao']));
        $_values['numerotelefone'] = $Utilitarios->antiInjection($_values['numerotelefone']);
        $_values['numerotransferido'] = $Utilitarios->antiInjection($_values['numerotransferido']);
        $_values['nometransferido'] = $Utilitarios->stringMaiusculo($Utilitarios->antiInjection($_values['nometransferido']));
        $_values['nomecliente'] = $Utilitarios->stringMaiusculo($Utilitarios->antiInjection($_values['nomecliente']));
        $_values['identificadorchamada'] = $Utilitarios->antiInjection($_values['identificadorchamada']);
        $_values['originalcausa'] = $Utilitarios->antiInjection($_values['originalcausa']);
        $_values['destinocausa'] = $Utilitarios->antiInjection($_values['destinocausa']);
        $_values['arquivo'] = $Utilitarios->antiInjection($_values['arquivo']);
        $this->insert("DADOSFILTRADOSCONVERTIDOS", "dataligacao, horainicioligacao, horainicioatendimento, horafimligacao, atendente, ramal,
               tempoatendimento, tempoligacao, observacao, numerotelefone, numerotransferido, nometransferido, nomecliente, identificadorchamada, originalcausa, destinocausa, arquivo", "'" . $_values['dataligacao'] . "',
                '" . $_values['horainicioligacao'] . "', '" . $_values['horainicioatendimento'] . "', '" . $_values['horafimligacao'] . "',
                '" . $_values['atendente'] . "', '" . $_values['ramal'] . "', '" . $_values['tempoatendimento'] . "', '" . $_values['tempoligacao'] . "',
                '" . $_values['observacao'] . "', '" . $_values['numerotelefone'] . "', '" . $_values['numerotransferido'] . "', '" . $_values['nometransferido'] . "',
                '" . $_values['nomecliente'] . "', '" . $_values['identificadorchamada'] . "', '" . $_values['originalcausa'] . "', '" . $_values['destinocausa'] . "', '" . $_values['arquivo'] . "'");
    }

    public function updateData($_values = array()) {
        $Utilitarios = new Utilitarios();
        $_values['dafcid'] = $Utilitarios->antiInjection($_values['dafcid']);
        $_values['dataligacao'] = $Utilitarios->antiInjection($_values['dataligacao']);
        $_values['horainicioligacao'] = $Utilitarios->antiInjection($_values['horainicioligacao']);
        $_values['horainicioatendimento'] = $Utilitarios->antiInjection($_values['horainicioatendimento']);
        $_values['horafimligacao'] = $Utilitarios->antiInjection($_values['horafimligacao']);
        $_values['atendente'] = $Utilitarios->stringMaiusculo($Utilitarios->antiInjection($_values['atendente']));
        $_values['ramal'] = $Utilitarios->antiInjection($_values['ramal']);
        $_values['tempoatendimento'] = $Utilitarios->antiInjection($_values['tempoatendimento']);
        $_values['tempoligacao'] = $Utilitarios->antiInjection($_values['tempoligacao']);
        $_values['observacao'] = $Utilitarios->stringMaiusculo($Utilitarios->antiInjection($_values['observacao']));
        $_values['numerotelefone'] = $Utilitarios->antiInjection($_values['numerotelefone']);
        $_values['numerotransferido'] = $Utilitarios->antiInjection($_values['numerotransferido']);
        $_values['nometransferido'] = $Utilitarios->stringMaiusculo($Utilitarios->antiInjection($_values['nometransferido']));
        $_values['nomecliente'] = $Utilitarios->stringMaiusculo($Utilitarios->antiInjection($_values['nomecliente']));
        $_values['originalcausa'] = $Utilitarios->antiInjection($_values['originalcausa']);
        $_values['destinocausa'] = $Utilitarios->antiInjection($_values['destinocausa']);
        $this->update("DADOSFILTRADOSCONVERTIDOS", "dataligacao = '" . $_values['dataligacao'] . "', horainicioligacao =
                '" . $_values['horainicioligacao'] . "', horainicioatendimento = '" . $_values['horainicioatendimento'] . "', horafimligacao = '" . $_values['horafimligacao'] . "', atendente =
                '" . $_values['atendente'] . "', ramal = '" . $_values['ramal'] . "', tempoatendimento = '" . $_values['tempoatendimento'] . "', tempoligacao = '" . $_values['tempoligacao'] . "', observacao =
                '" . $_values['observacao'] . "', numerotelefone = '" . $_values['numerotelefone'] . "', numerotransferido = '" . $_values['numerotransferido'] . "', nometransferido = '" . $_values['nometransferido'] . "', nomecliente = '" . $_values['nomecliente'] . "'", "WHERE dafcid = " . $_values['dafcid']);
    }

    public function deleteData($_id) {
        $where = "";
        if($_id == "")
            $where = "";
        else
            $where = "WHERE dafcid = " . $_id;
        $Utilitarios = new Utilitarios();
        $_id = $Utilitarios->antiInjection($_id);
        $this->delete("DADOSFILTRADOSCONVERTIDOS", $where);
    }
}

?>
