<?php

include_once $_SERVER['DOCUMENT_ROOT'] . "/System/Utilitarios/Ambiente.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/System/Utilitarios/LoadClass.class.php";
$__autoload = new LoadClass();
$__autoload->carregar('Excecoes,Logs,Utilitarios,DadosFiltradosDao');
$DadosFiltradosDao = new DadosFiltradosDao();
$result = $DadosFiltradosDao->findData();
$values = array();
echo "Dados sendo alterados Aguarde...<br />";
$cont = 0;
$commit = false;
$valoresArray = array();
while ($Object = $DadosFiltradosDao->resultObject($result)) {
    $values['identificadorchamada'] = $Object->IDENTIFICADORCHAMADA;
    $values['atendente'] = $Object->ATENDENTE;
    $values['numerotelefone'] = $Object->RAMAL;
    $values['dataligacao'] = $Object->DATALIGACAO;
    $values['horainicioligacao'] = $Object->HORAINICIOLIGACAO;
    $values['horainicioatendimento'] = $Object->HORAINICIOATENDIMENTO;
    $values['horafimligacao'] = $Object->HORAFIMLIGACAO;
    $values['tempoatendimento'] = $Object->TEMPOATENDIMENTO;
    $values['tempoligacao'] = $Object->TEMPOLIGACAO;
    $values['nomecliente'] = $Object->NOMECLIENTE;
    $values['ramal'] = $Object->NUMEROTELEFONE;
    $values['nometransferido'] = $Object->NOMETRANSFERIDO;
    $values['numerotransferido'] = $Object->NUMEROTRANSFERIDO;
    $values['observacao'] = $Object->OBSERVACAO;
    $cont++;
    $DadosFiltradosDao->deleteData($Object->DAFCID);
    $DadosFiltradosDao->executeP($commit);
    $DadosFiltradosDao->limpaSql();
    $DadosFiltradosDao->insertData($values);
    $DadosFiltradosDao->executeP($commit);
    $DadosFiltradosDao->limpaSql();
}
/*for ($i = 0; $i < $cont; $i++) {
    $valoresArray['identificadorchamada'] = $values[$i]['identificadorchamada'];
    $valoresArray['atendente'] = $values[$i]['atendente'];
    $valoresArray['numerotelefone'] = $values[$i]['numerotelefone'];
    $valoresArray['dataligacao'] = $values[$i]['dataligacao'];
    $valoresArray['horainicioligacao'] = $values[$i]['horainicioligacao'];
    $valoresArray['horainicioatendimento'] = $values[$i]['horainicioatendimento'];
    $valoresArray['horafimligacao'] = $values[$i]['horafimligacao'];
    $valoresArray['tempoatendimento'] = $values[$i]['tempoatendimento'];
    $valoresArray['tempoligacao'] = $values[$i]['tempoligacao'];
    $valoresArray['nomecliente'] = $values[$i]['nomecliente'];
    $valoresArray['ramal'] = $values[$i]['ramal'];
    $valoresArray['nometransferido'] = $values[$i]['nometransferido'];
    $valoresArray['numerotransferido'] = $values[$i]['numerotransferido'];
    $valoresArray['observacao'] = $values[$i]['observacao'];
    //$DadosFiltradosDao->insertData($valoresArray);
    //$DadosFiltradosDao->executeP($commit);
}*/
echo "<br /><br />Dados alterados com sucesso!";
?>
