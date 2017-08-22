<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PersistenceDadosclass
 *
 * @author CleisonFerreira
 * @
 */
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
$__autoload = new LoadClass();
$__autoload->carregar('Excecoes,Conexao,Logs,Utilitarios');
class PersistenceDados{
    private $sql = "";
    private $conn;
    private $find = false;
    public function __construct()
    {
    }
    protected function connect(){
        $Conexao = new Conexao();
        return $this->conn = $Conexao->connect();
    }
    //metodo que limpa a sql apos ser commitada
    public function limpaSql()
    {
        //echo $this->sql;
        $this->sql = '';
    }
    //metodo que carrega as sql�s de insert
    //@param $_tabela e o nome da tabela que vai receber a insercao
    //@param $_campos e o nome dos campos que vai receber a insercao
    //@param $_valores e o nome dos valores dos campos
    public function insert($_tabela, $_campos, $_valores)
    {
        $this->sql .= " INSERT INTO ".$_tabela." ( ".$_campos." ) VALUES ( ".$_valores." ); ";
        $this->find = false;
    }
    //metodo que carrega as sql�s de update
    //@param $_tabela e o nome da tabela que vai receber a atualizacao
    //@param $_alteracoes e o nome dos campos com as alteracaoes que vai receber a atualizacao
    //@param $_condicoes e o nome das condicoes
    public function update($_tabela,$_alteracoes,$_condicoes)
    {
        $this->sql .= " UPDATE ".$_tabela." SET ".$_alteracoes." ".$_condicoes."; ";
        $this->find = false;
    }
    //metodo que carrega as sql�s de delete
    //@param $_tabela e o nome da tabela que vai ser apagado o dado
    //@param $_condicoes e o nome das condicoes para exclusao
    public function delete($_tabela,$_condicoes)
    {
        $this->sql .= " DELETE FROM ".$_tabela." ".$_condicoes."; ";
        $this->find = false;
    }
    //metodo que carrega as sql�s de select
    //@param $_tabela e o nome da tabela que vai receber a
    //@param $_campos e o nome dos campos que vai receber a insercao
    //@param $_condicoes e o nome das condicoes
    public function select($_tabela,$_campos,$_condicoes)
    {
        $Logs = new Logs($_SERVER[ 'DOCUMENT_ROOT' ]."/System/TMP/");
        if($this->find == true)
            $this->sql = "";
        $this->sql .= " SELECT ".$_campos." FROM ".$_tabela." ".$_condicoes."; ";
        //$Logs->escrever($this->sql);
        $this->find = true;
    }
    //metodo que executa os metodos da classe PersistenceDados para consultas onde nao exige controle de transaçoes
    public function executeS()
    {
        $Logs = new Logs($_SERVER[ 'DOCUMENT_ROOT' ]."/System/TMP/");
        $Conexao = new Conexao();
        $this->conn = $Conexao->connect();
        if($this->find){
            //$Logs->escrever($this->sql);
            try
            {
                if( mysql_query($this->sql) == false )
                {
                    throw new Excecoes(Excecoes::ERRO_SQL);
                }
                else{
                    $result = mysql_query($this->sql);
                    $Conexao->desConnect();
                    $this->limpaSql();
                    return $result;
                }
            }
            catch (Exception $e){
                $mensagem = "<error>".mysql_error()."</error>";
                $mensagem .= $this->sql."\n";
                $this->close();
                $mensagem .= $e."<b><i> Linha: ".$e->getLine()."<br />Arquivo: ".$_SERVER[ 'PHP_SELF' ]."</i></b><br />";
                $Logs->escrever($mensagem);
            }
        }
    }
    //metodo para tratar e fazer o controle de transacoes
    public function executeP($commit){
        $Logs = new Logs($_SERVER[ 'DOCUMENT_ROOT' ]."/System/TMP/");
        $Conexao = new Conexao();
        $this->conn = $Conexao->connect();
        try
        {
            $dados = explode(";", $this->sql);
            for($i = 0; $i < sizeof($dados) - 1; $i++){
                if(mysql_query($dados[$i].";") == false)
                {
                    throw new Excecoes(Excecoes::ERRO_SQL);
                }
            }
            if($commit)
                $this->commit();
            else
                $this->limpaSql();
            //$Logs->escrever($this->sql);
            return true;
        }
        catch (Exception $e){
            $mensagem = "<error>".mysql_error()."</error>";
            $mensagem .= $this->sql."\n";
            $this->rollback();
            $mensagem .= " ROLLBACK ;\n";
            $mensagem .= $e."<b><i> Linha: ".$e->getLine()."<br />Arquivo: ".$_SERVER[ 'PHP_SELF' ]."</i></b><br />";
            $Logs->escrever($mensagem);
        }
    }
    //estarta uma transacao
    //obs: só é utilizado para o controle de transacoes
    public function start(){
        $this->limpaSql();
        $this->sql .= " START TRANSACTION ;";
    }
    //fecha uma conexao do banco de dados
    //obs: nao precisa chamalo pois o sistema o faz automaticamente
    private function close(){
        $Conexao = new Conexao();
        $this->limpaSql();
        $Conexao->desConnect();
    }
    //faz um commit na transacao se nao houver erros nas sql
    //obs: nao precisa chamalo pois o sistema o faz automaticamente
    private function commit(){
        $Conexao = new Conexao();
        $this->conn = $Conexao->connect();
        $this->sql = " COMMIT ";
        mysql_query($this->sql);
        $this->close();
        return true;
    }
    //faz um rollback na transacao se houver erros nas sql
    //obs: nao precisa chamalo pois o sistema o faz automaticamente
    private function rollback(){
        $Conexao = new Conexao();
        $this->conn = $Conexao->connect();
        $this->sql = " ROLLBACK ";
        mysql_query($this->sql);
        $this->close();
    }
}
?>
