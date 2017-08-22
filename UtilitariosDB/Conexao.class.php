<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Conexao
 *
 * @author CleisonFerreira
 * @
 */
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
$__autoload = new LoadClass();
$__autoload->carregar('Excecoes');
class Conexao {
    private $usr = '';
    private $password = '';
    private $host = '';
    private $port = '';
    private $bank = '';
    function __construct() { }
    public function connect(){
        try{
            if(file_exists($_SERVER[ 'DOCUMENT_ROOT' ]."/System/Configuracao/conf.ini")){
                $dados = parse_ini_file($_SERVER[ 'DOCUMENT_ROOT' ]."/System/Configuracao/conf.ini");
                $this->usr = $dados[ 'user' ];
                $this->password = $dados[ 'password' ];
                $this->host = $dados[ 'host' ];
                $this->port = $dados[ 'port' ];
                $this->bank = $dados[ 'bank' ];
                $conn = mysql_connect( $this->host , $this->usr , $this->password);
                $db = mysql_select_db($this->bank, $conn);
                if(!$conn or !$db){
                    throw new Excecoes(Excecoes::ERRO_BANCO);
                }
                else
                    return $conn;
            }
            else
                throw new Excecoes(Excecoes::ERRO_FILE);
        }
        catch(Exception $e){
            $e."<b><i> Linha: ".$e->getLine()."<br />Arquivo: ".$_SERVER[ 'PHP_SELF' ]."</i></b>";
            header("Location: /System/index.phtml?msg=fail_banco");
        }
    }
    public function desConnect(){
        mysql_close();
    }
}
?>
