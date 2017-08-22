<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsuarioDaoclass
 *
 * @author CleisonFerreira
 * @
 */
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
$__autoload = new LoadClass();
$__autoload->carregar('Excecoes,Logs,Utilitarios,Dao');
class UsuarioDao extends Dao{
    public function __construct()
    {
    }
    public function findIdUsuario($_id){
        $Utilitarios = new Utilitarios();
        $_id = $Utilitarios->antiInjection($_id);
        $this->select("USUARIO", "*", "WHERE usrid = ".$_id);
        $result = $this->executeS();
        if($result)
            return $this->resultObject($result);
        else
            return false;
    }
    public function findStatus($_status){
        $Utilitarios = new Utilitarios();
        $_status = $Utilitarios->stringMaiusculo( $Utilitarios->antiInjection($_status) );
        $this->select("USUARIO", "*", "WHERE usrsts = '".$_status."'");
        $result = $this->executeS();
        if($result)
            return $this->resultObject($result);
        else
            return false;
    }
    public function findLogin( $_values = array() ){
        $Utilitarios = new Utilitarios();
        $_values[ 'usrlogin' ] = $Utilitarios->stringMinusculo( $Utilitarios->antiInjection($_values[ 'usrlogin' ]) );
        $_values[ 'usrsenha' ] = $Utilitarios->stringMinusculo( $Utilitarios->antiInjection($_values[ 'usrsenha' ]) );
        $this->select("USUARIO", "*", "WHERE usrlogin LIKE '%".$_values[ 'usrlogin' ]."%' AND usrsenha = md5('".$_values[ 'usrsenha' ]."') AND usrsts = 'A'");
        $result = $this->executeS();
        if($result)
            return $this->linesFind($result);
        else
            return false;
    }
    public function findNome($_nome){
        $Utilitarios = new Utilitarios();
        $_nome = $Utilitarios->stringMaiusculo( $Utilitarios->antiInjection($_nome) );
        $this->select("USUARIO", "*", "WHERE usrnome LIKE '%".$_nome."%'");
        $result = $this->executeS();
        if($result)
            return $this->resultObject($result);
        else
            return false;
    }
    public function findData(){
        $this->select("USUARIO", "*", "ORDER BY usrlogin");
        $result = $this->executeS();
        if($result)
            return $this->linesFind($result);
        else
            return false;
    }
    public function insertData($_values = array()){
        $Utilitarios = new Utilitarios();
        $_values[ 'usrlogin' ] = $Utilitarios->stringMinusculo( $Utilitarios->antiInjection($_values[ 'usrlogin' ]) );
        $_values[ 'usrnome' ] = $Utilitarios->stringMaiusculo( $Utilitarios->antiInjection($_values[ 'usrnome' ]) );
        $_values[ 'usrsts' ] = "A";
        $_values[ 'usrsenha' ] = $Utilitarios->stringMinusculo( $Utilitarios->antiInjection($_values[ 'usrsenha' ]) );
        $this->insert("USUARIO", "usrlogin, usrnome, usrsts, usrsenha", "'".$_values[ 'usrlogin' ]."', '".$_values[ 'usrnome' ]."', '".$_values[ 'usrsts' ]."', md5('".$_values[ 'usrsenha' ]."')");
    }
    public function updateData($_values = array()){
        $Utilitarios = new Utilitarios();
        $_values[ 'usrid' ] = $Utilitarios->antiInjection($_values[ 'usrid' ]);
        $_values[ 'usrlogin' ] = $Utilitarios->stringMinusculo( $Utilitarios->antiInjection($_values[ 'usrlogin' ]) );
        $_values[ 'usrnome' ] = $Utilitarios->stringMaiusculo( $Utilitarios->antiInjection($_values[ 'usrnome' ]) );
        $_values[ 'usrsts' ] = $Utilitarios->stringMaiusculo( $Utilitarios->antiInjection($_values[ 'usrsts' ]) );
        $_values[ 'usrsenha' ] = $Utilitarios->stringMinusculo( $Utilitarios->antiInjection($_values[ 'usrsenha' ]) );
        $this->update("USUARIO", "usrlogin = '".$_values[ 'usrlogin' ]."', usrnome = '".$_values[ 'usrnome' ]."', usrsts = '".$_values[ 'usrsts' ]."', usrsenha = md5('".$_values[ 'usrlogin' ]."')", "WHERE usrid = ".$_values[ 'usrid' ]);
    }
    public function deleteData($_id){
        $Utilitarios = new Utilitarios();
        $_id = $Utilitarios->antiInjection($_id);
        $this->delete("USUARIO", "WHERE usrid = ".$_id);
    }
}
?>
