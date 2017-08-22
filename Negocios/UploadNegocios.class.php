<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UploadNegocios
 *
 * @author CleisonFerreira
 * @date 24/05/2010
 */
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
include_once "UploadNegociosInterface.php";
$__autoload = new LoadClass();
$__autoload->carregar('Excecoes,Logs,Utilitarios,DadosFiltradosDao');
class UploadNegocios extends Utilitarios implements UploadNegociosInterface {
    public function __construct(){}
    public function listFile($_arquivo){
        $string = $this->openFile($_arquivo);
        return $arquivo = trim($string);
    }
    public function transfArray($_arquivo){
        $string = str_replace("\"", " ", $_arquivo);
        $arrayLinhas = explode("\n", $string);
        $arrayColunas = array();
        $matrizFile = array();
        for($i = 0; $i < sizeof($arrayLinhas); $i++){
            $arrayColunas[] = explode(",", $arrayLinhas[$i]);
        }
        return $arrayColunas;
    }
    public function carregarDados($file){
        $Logs = new Logs($_SERVER[ 'DOCUMENT_ROOT' ]."/System/TMP/");
        $diretorio = explode(".", $file);
        if(!$this->extrairArquivos($file))
            return false;
        try {
            unlink($_SERVER['DOCUMENT_ROOT']."/System/TMP/".$file);
            $dir = opendir($_SERVER['DOCUMENT_ROOT']."/System/TMP/".$diretorio[0]);
            while(($arquivo = readdir($dir)) !== false)
            {
                if($arquivo != "" and $arquivo != "." and $arquivo != ".."){
                    $lista = $this->listFile($_SERVER['DOCUMENT_ROOT']."/System/TMP/".$diretorio[0]."/".$arquivo);
                    $matrizDados = $this->transfArray($lista);
                    //echo "<script>". $this->tabelaHTML2($_matrizDados)."</script>";
                    if($this->dataFile($matrizDados, $arquivo))
                        unlink($_SERVER['DOCUMENT_ROOT']."/System/TMP/".$diretorio[0]."/".$arquivo);
                }
            }
            rmdir($_SERVER['DOCUMENT_ROOT']."/System/TMP/".$diretorio[0]);
            rmdir($_SERVER['DOCUMENT_ROOT']."/System/Facade/".$diretorio[0]);
            return true;
        } catch (Exception $e) {
            $_mensagem = $e."<b><i> Linha: ".$e->getLine()."<br />Arquivo: ".$_SERVER[ ' PHP_SELF' ]."</i></b>";
            $Logs->escrever($_mensagem);
            return false;
        }
    }
    public function extrairArquivos($file){
        $Logs = new Logs($_SERVER[ 'DOCUMENT_ROOT' ]."/System/TMP/");
        try {
            $dir = explode(".", $file);
            chmod($_SERVER['DOCUMENT_ROOT']."/System/TMP", 0777);
            mkdir($dir[0], 0777);
            $path = $_SERVER['DOCUMENT_ROOT']."/System/TMP/".$dir[0];
            if($this->extractZip($_SERVER['DOCUMENT_ROOT']."/System/TMP/".$file, $_SERVER['DOCUMENT_ROOT']."/System/TMP/".$dir[0])){
                return true;
            }
            else{
                throw new Excecoes(Excecoes::ERRO_EXTRACT);
                return false;
            }
        } catch (Exception $e) {
            $_mensagem = $e."<b><i> Linha: ".$e->getLine()."<br />Arquivo: ".$_SERVER[ ' PHP_SELF' ]."</i></b>";
            $Logs->escrever($_mensagem);
            return false;
        }
    }
    public function upload($_arquivo, $_caminho){
        if($this->uploadFile($_arquivo, $_caminho))
            echo "1";
        else
            return false;
    }
    public function tabelaHTML2($_matrizDados){
        $dadosTabela = null;
        $tabela = "<table align=\"center\" cellpadding=\"1\" cellspacing=\"1\" id=\"tabela\" border=\"1\" style='margin:0;' >";
        $tabelaF = "</table>";
        $thead = "<thead align='center' style='text-align:center;width:250px; background-color:#AFFFC9;'>";
        $theadF = "</thead>";
        $tbody = "<tbody>";
        $tbodyF = "</tbody>";
        $td = "<td>";
        $tdF = "</td>";
        $tr = "<tr>";
        $trF = "</tr>";
        foreach ($_matrizDados as $number_variable => $variable) {
            if($number_variable == 0){
                $dadosTabela .= $tabela . $thead . $tr;
            }
            else if($number_variable == 2){
                $dadosTabela .= $trF . $theadF;
            }
            else if($number_variable == 3 ){
                $dadosTabela .= $tbody . $tr;
            }
            else if($number_variable > 3 ){
                $dadosTabela .= $trF;
                $dadosTabela .= $tr;
            }
            foreach ($variable as $numero => $valor){
                if( $numero == 2  ){//GLOBALCALLID_CALLID
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo N-> " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 3 ){//ORIGLEGCALLIDENTIFIER
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo N-> " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 4 ){//DATETIMEORIGINATION

                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $this->timestampForDate( $valor ) ) . " - " . $this->stringMaiusculo( $this->timestampForTime( $valor ) ) . $tdF;
                    }
                }
                else if( $numero == 7 ){//ORIGIPADDR
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $this->intForIPAdress( $valor ) ) . $tdF;
                    }
                }
                else if( $numero == 8 ){//CALLINGPARTYNUMBER
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 23 ){//DESTLEGIDENTIFIER
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 24 ){//DESTNODEID
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 25 ){//DESTSPAN
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 26 ){//DESTIPADDR
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $this->intForIPAdress( $valor ) ) . $tdF;
                    }
                }
                else if( $numero == 27 ){//ORIGINALCALLEDPARTYNUMBER
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 28 ){//FINALCALLEDPARTYNUMBER
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 43 ){//DATETIMECONNECT
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $this->timestampForTime( $valor ) ) . $tdF;
                    }
                }
                else if( $numero == 44 ){//DATETIMEDISCONNECT
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $this->timestampForTime( $valor ) ) . $tdF;
                    }
                }
                else if( $numero == 45 ){//LASTREDIRECTDN
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 47 ){//ORIGINALCALLEDPARTYNUMBERPARTITION
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 48 ){//CALLINGPARTYNUMBERPARTITION
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 49 ){//FINALCALLEDPARTYNUMBERPARTITION
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 50 ){//LASTREDIRECTDNPARTITION
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 51 ){//DURATION
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $this->transfSeconds( $valor ) ) . $tdF;
                    }
                }
                else if( $numero == 52 ){//ORIGDEVICENAME
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
                else if( $numero == 53 ){//DESTDEVICENAME
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
                }
            }
        }
        $dadosTabela .= $trF . $tbodyF . $tabelaF;
        return $dadosTabela;
    }
    public function tabelaHTML($_matrizDados){
        $dadosTabela = null;
        $tabela = "<table align=\"center\" cellpadding=\"1\" cellspacing=\"1\" id=\"tabela\" border=\"1\" style='margin:0;' >";
        $tabelaF = "</table>";
        $thead = "<thead align='center' style='text-align:center;width:250px; background-color:#AFFFC9;'>";
        $theadF = "</thead>";
        $tbody = "<tbody>";
        $tbodyF = "</tbody>";
        $td = "<td>";
        $tdF = "</td>";
        $tr = "<tr>";
        $trF = "</tr>";
        foreach ($_matrizDados as $number_variable => $variable) {
            if($number_variable == 0){
                $dadosTabela .= $tabela . $thead . $tr;
            }
            else if($number_variable == 2){
                $dadosTabela .= $trF . $theadF;
            }
            else if($number_variable == 3 ){
                $dadosTabela .= $tbody . $tr;
            }
            else if($number_variable > 3 ){
                $dadosTabela .= $trF;
                $dadosTabela .= $tr;
            }
            foreach ($variable as $numero => $valor){
                    if($number_variable == 0){
                        $dadosTabela .= "<td style='text-align:center; height:45px;width:250px;color:red; font-size:14px;letter-spacing:2px;'><b>Campo Nº " . $numero . "  " . $this->stringMaiusculo( $valor ) . "</b></td>";
                    }
                    else if($number_variable > 1){
                        $dadosTabela .= $td . $this->stringMaiusculo( $valor ) . $tdF;
                    }
            }
        }
        $dadosTabela .= $trF . $tbodyF . $tabelaF;
        return $dadosTabela;
    }
    public function dataFile($_matrizDados, $_arquivo){
        $Logs = new Logs($_SERVER[ 'DOCUMENT_ROOT' ]."/System/TMP/");
        $DadosFiltradosDao = new DadosFiltradosDao();        
        $vetorString = null;
        $values = array();
        $auxTime = "";
        $tempoLigacao = 0;
        $execute = false;
        $commit = false;
        try{
            foreach ($_matrizDados as $number_variable => $variable) {
                foreach ($variable as $numero => $valor){
                    if($number_variable > 1){
                        $vetorDados = $this->getRamais();
                        foreach( $vetorDados as $ramal => $nome ){
                            if( $variable[27] == $ramal ){
                                $execute = true;
                                break;
                            }
                        }
                        if( $execute ){
                            if( $numero == 2  ){//GLOBALCALLID_CALLID - 2
                                $values['identificadorchamada'] = is_numeric($valor) ? $valor : null;
                            }
                            else if( $numero == 4 ){//DATETIMEORIGINATION - 4
                                $auxTime = $valor;
                                $values['dataligacao'] = $this->formatDataBank( $this->timestampForDate( $valor ) );
                                $values['horainicioligacao'] = $this->timestampForTime( $valor );
                            }
                            else if( $numero == 8 ){//CALLINGPARTYNUMBER - 8
                                $values['numerotelefone'] = $valor;
                            }
                            else if( $numero == 11 ){//ORIGCAUSE_VALUE - 11
                                $values['originalcausa'] = $valor;
                            }
                            else if( $numero == 27 ){//ORIGINALCALLEDPARTYNUMBER - 27
                                $values['ramal'] = $valor;
                            }
                            /*
                             * Tempo que a ligação levou para ser atendida
                            */
                            else if( $numero == 31 ){//DESTCAUSE_VALUE - 31
                                $values['destinocausa'] = $valor;
                            }
                            else if( $numero == 43 ){//DATETIMECONNECT
                                if( $valor == 0 ){
                                    $tempoLigacao = 0;
                                    $values['horainicioatendimento'] = $this->timestampForTime( $auxTime );
                                    $values['tempoligacao'] = $this->transfSeconds( $tempoLigacao );
                                }
                                else{
                                    $tempoLigacao = $valor - $auxTime;
                                    $values['horainicioatendimento'] = $this->timestampForTime( $valor );
                                    $values['tempoligacao'] = $this->transfSeconds( $tempoLigacao );
                                    $auxTime = $valor;
                                }
                            }
                            else if( $numero == 44 ){//DATETIMEDISCONNECT
                                if( $valor == 0 ){
                                    $values['horafimligacao'] = $this->timestampForTime( $auxTime );
                                }
                                else{
                                    $values['horafimligacao'] = $this->timestampForTime( $valor );
                                }
                            }
                            else if( $numero == 45 ){//LASTREDIRECTDN
                                $values['numerotransferido'] = $valor;
                            }
                            else if( $numero == 47 ){//ORIGINALCALLEDPARTYNUMBERPARTITION
                                $values['nomecliente'] = $valor;
                            }
                            else if( $numero == 48 ){//CALLINGPARTYNUMBERPARTITION
                                $values['atendente'] = $nome;
                            }
                            else if( $numero == 50 ){//LASTREDIRECTDNPARTITION
                                $values['nometransferido'] = $valor;
                            }/*                             
                             * Duração da chamada
                             */
                            else if( $numero == 51 ){//DURATION
                                $values['tempoatendimento'] = $this->transfSeconds( $valor );
                            }
                            else if( $numero == 63 ){//COMMENT
                                $values['observacao'] = $valor;
                            }
                        }
                    }
                }
                if($number_variable > 1 && $execute){
                    $execute = false;
                    @$Dados = $DadosFiltradosDao->findIdentificadorDadosFiltrados($values[ 'identificadorchamada' ]);
                    if( @$Dados->DAFCID == "0" ){
                        $values['arquivo'] = $_arquivo;
                        $DadosFiltradosDao->insertData( $values );
                        $DadosFiltradosDao->executeP($commit);
                    }
                    else
                        echo "";
                } 
            }
            //throw new Excecoes(Excecoes::ERRO_SQL);
            return true;
        }
        catch (Exception $e){
            $mensagem = $e."<b><i>".$values['ramal']." Linha: ".$e->getLine()."<br />Arquivo: ".$_SERVER[ 'PHP_SELF' ]."</i></b><br />";
            $Logs->escrever($mensagem);
            return false;
        }
    }
}
?>
