<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utilitarios
 * Classe utilitaria que contém os metodos que sao usados para funcionalidades especificas no sistema.
 * @author Cleison Ferreira
 * @date 24/05/2010
 */
include_once "Sessoes.class.php";
ini_set('max_execution_time','0');
ini_set('upload_max_filesize', '-1');
ini_set('post_max_size','-1');
ini_set('memory_limit','-1');
class Utilitarios extends Sessoes {
    public function __construct(){

    }
    //@return metodo para fazer upload de arquivos
    //@param $_arquivo deve ser o valor $_FILE[ 'arquivo' ]
    //@param $_caminho e o lugar onde ficara o arquivo depois do upload
    public function uploadFile($_arquivo, $_caminho)
    {
        $tamanho = $_arquivo['arquivo'][ 'size' ];
        $tipo = $_arquivo['arquivo'][ 'type' ];
        $temp = $_arquivo['arquivo']['tmp_name'];
        $nome = $_arquivo['arquivo']['name'];
	$targetFile =  str_replace('//','/',$_caminho) . $nome;
        if($tamanho <= 90000000000)
        {
             if(move_uploaded_file($temp, $targetFile))
             {
                 return true;
             }
             else
                 return false;
        }
    }
    public function getRamais(){
        if(file_exists($_SERVER[ 'DOCUMENT_ROOT' ]."/System/Configuracao/ramais.ini")){
            $dados = parse_ini_file($_SERVER[ 'DOCUMENT_ROOT' ]."/System/Configuracao/ramais.ini");
            return $dados;
        }
    }
    public function getRamaisPABX(){
        if(file_exists($_SERVER[ 'DOCUMENT_ROOT' ]."/System/Configuracao/ramaisPABX.ini")){
            $dados = parse_ini_file($_SERVER[ 'DOCUMENT_ROOT' ]."/System/Configuracao/ramaisPABX.ini");
            return $dados;
        }
    }
    public function getAmbiente(){
        if(file_exists($_SERVER[ 'DOCUMENT_ROOT' ]."/System/Configuracao/conf.ini")){
            $dados = parse_ini_file($_SERVER[ 'DOCUMENT_ROOT' ]."/System/Configuracao/conf.ini");
            if( $this->stringMinusculo( $dados[ 'ambiente' ] ) == "teste" ){
                return ini_set("display_errors","on");
            }
            else if( $this->stringMinusculo( $dados[ 'ambiente' ] ) == "desenvolvimento" ){
                return ini_set("display_errors","on");
            }
            else if( $this->stringMinusculo( $dados[ 'ambiente' ] ) == "producao" ){
                return ini_set("display_errors","off");
            }
        }
    }
    /*
     * Extrai os arquivos de um arquivo zipado para um diretorio escolhido
     * @param $file= é o arquivo que zip que vai ser descomprimido
     * @param $path= destino dos aquivos extraídos
     */
    public function extractZip($file, $path){
        $Zip = new ZipArchive();
        if($Zip->open($file)){
            $return = $Zip->extractTo($path);
            $Zip->close();
            return true;
        }
        return false;
    }
    public function whiteFile( $_arquivo, $_dados = array() ){
        $handle = fopen( $_SERVER['DOCUMENT_ROOT'].$_arquivo, "wb");
        if(fwrite( $handle, "" ) != 0){
           fclose($handle);
           return false;
        }
        fclose($handle);
        $handle = fopen( $_SERVER['DOCUMENT_ROOT'].$_arquivo, "ab");
        foreach ($_dados as $key => $value){
            if(!fwrite( $handle, $key." = ".$value.";\r\n" )){
               fclose($handle);
               return false;
            }
        }
        fclose($handle);
        return true;
    }
    public function openFile($_arquivo){
        if(file_exists($_arquivo)){
            return $file = file_get_contents($_arquivo);
        }
    }
    public function formatFile($_arquivo){
        return nl2br(str_replace(",", "\t", $_arquivo));
    }
    //@return metodo que retorna a data do dia do mes e ano formatada para faciu compreen�ao do usuario
    public function setDataFormatada()
    {
        $semana = array( "Domingo", "Segunda-Feira", "Terça-Feira", "Quarta-Feira", "Quinta-Feira", "Sexta-Feira", "Sábado"  );
        $meses  = array( "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho","Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro" );
        return $semana[ date( 'w' ) ].", ".date('d')." de ".$meses[ intval( date( 'm' ) ) - 1 ]." de ".date( 'Y' ).".";
    }
    //@return metodo que retorna a data atual no formato dd/mm/aaaa
    public function dateServer()
    {
         return date("d/m/Y");
    }
    //@return metodo que retorna a hora atual no formato hh:mm:ss
    public function timeServer()
    {
         return date('H:m:s');
    }
    //@return metodo que formata o formato dd/mm/aaaa para o formato do banco de dados aaaa-mm-dd
    //@param $_data � a data que vai ser formatada
    public function formatDataBank($_data)
    {
        $vetor = split("/", $_data);
        return ($vetor[2].'-'.$vetor[1].'-'.$vetor[0]);
    }
    //@return metodo que formata o formato aaaa-mm-dd do banco de dados para o formato de sistema dd/mm/aaaa
    //@param $_data � a data que vai ser formatada
    public function formatSystem($_data)
    {
        if($_data == NULL)
            return "00/00/0000";
        else
        {
            $vetor = split("-", $_data);
            return ($vetor[2].'/'.$vetor[1].'/'.$vetor[0]);
        }
    }
    //@return metodo pra imprimir no browser uma mensagem de recep�ao se baseando nos peridos do dia
    public function setSaldacao()
    {
        if( date('H') < 12 and date('H') >= 7 )
        {
            return $nome = "Bom Dia";
        }
        else if( date('H') >= 12 and date('H') < 18)
        {
            return $nome = "Boa Tarde";
        }
        else
        {
            return $nome = "Boa Noite";
        }
    }
    //@return metodo que transforma uma string em seus caracteres maiusculos
    //@param $_string que vai sofrer a açao
    public function stringMaiusculo($_string)
    {
        return strtoupper($_string);
    }
    //@return metodo que transforma uma string em seus caracteres minusculos
    //@param $_string que vai sofrer a a�ao
    public function stringMinusculo($_string)
    {
        return strtolower($_string);
    }
    //@return metodo que previne a sql-injection
    //@param $_string � a string que vai ser tratada
    public function antiInjection($_string)
    {
        $sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|%|\\\\)/"),"",$_string);
        return trim(strip_tags(addslashes($_string)));
    }
    //@return o desenho disposto na horizontal
    //@param $_tipo � o tipo de desenho
    //@param $_quantidade � a quantidade de repeti�oes
    public function Risco( $_tipo, $_quantidade )
    {
        $desenho = "";
        for( $i = 0; $i < $_quantidade; $i++ )
        {
            $desenho .= $_tipo;
        }
        return $desenho;
    }
    //Padrão UNIX com datas iniciadas em 1 de janeiro de 1970
    //@return a data e hora no padrao humano
    //@param $_timeStamp é o valor em milessegundos
    public function timestampForTime($_timeStamp) {
        $date = getdate($_timeStamp);
        $year = $date["year"];
        $month = $date["mon"];
        $day = $date["mday"];
        $hours = $date["hours"];
        $minutes = $date["minutes"];
        $seconds = $date["seconds"];
        return date("H:i:s", mktime($hours,$minutes,$seconds, $month,$day,$year));
    }
    public function timestampForDate($_timeStamp) {
        $date = getdate($_timeStamp);
        $year = $date["year"];
        $month = $date["mon"];
        $day = $date["mday"];
        $hours = $date["hours"];
        $minutes = $date["minutes"];
        $seconds = $date["seconds"];
        return date("d/m/Y", mktime($hours,$minutes,$seconds, $month,$day,$year));
    }
    public function intForIPAdress($_int){
        $hexaDec = dechex($_int);
        $hexaDec = str_split($hexaDec, 1);
        $aux = "";
        $cont = 1;
        $cont2 = 0;
        $j = (sizeof($hexaDec) - 1);
        for ($i = 0 ; $i < 4; $i++) {
            if($i == 3)
                $ponto = "";
            else
                $ponto = ".";
            $aux .= @hexdec( $hexaDec[$j - $cont] . $hexaDec[$j - $cont2] ).$ponto;
            $cont = $cont + 1;
            $cont2 = $cont2 + 1;
            $j--;
        }
        return $aux;
    }
    public function removCaracter( $_text, $_type = 'N' ){
        if( $_type == 'N' )
            return ereg_replace("[\*,A-Za-z]", "", $_text);
        else
            return ereg_replace("[0-9]", "", $_text);
    }
    public function findString($_text, $_string){
        if( preg_match("/".$_string."/", $_text) == 1 )
            return true;
        else
            return false;
    }
    public function transfSeconds( $_seconds ){
        if( $_seconds <= 59 && $_seconds > 0 )
            return "00:00:" . ( ( strlen( (int)$_seconds ) == 1 ) ? "0" . (int)$_seconds : (int)$_seconds );
        else if( $_seconds > 59 && $_seconds <= 3600 ){
            $minutos = $_seconds / 60;
            $seconds = $_seconds % 60;
            return "00:" . ( ( strlen( (int)$minutos ) == 1 ) ? "0" . (int)$minutos : (int)$minutos ) . ":" . ( ( strlen( ((int)$seconds) ) == 1 ) ? "0" . (int)$seconds : (int)$seconds );
        }
        else if( $_seconds > 3600 ){
            $hora = $_seconds / 3600;
            $minutos = $_seconds % 3600;
            if( $minutos <= 59 )
                return ( ( strlen( (int)$hora ) == 1 ) ? "0" . (int)$hora : (int)$hora ) . ":" . ( ( strlen( (int)$minutos ) == 1 ) ? "0" . (int)$minutos : (int)$minutos ) . ":00" ;
            else{
                $aux = $minutos / 60;
                $seconds = $minutos % 60;
                $minutos = $aux;
                return ( ( strlen( (int)$hora ) == 1 ) ? "0" . (int)$hora : (int)$hora ) . ":" . ( ( strlen( (int)$minutos ) == 1 ) ? "0" . (int)$minutos : (int)$minutos ) . ":" . ( ( strlen( (int)$seconds ) == 1 ) ? "0" . (int)$seconds : (int)$seconds ) ;
            }
        }
        return "00:00:00";
    }
}
?>
