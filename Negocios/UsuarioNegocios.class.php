<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsuarioNegociosclass
 *
 * @author CleisonFerreira
 * @
 */
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
include_once "UsuarioNegociosInterface.php";
$__autoload = new LoadClass();
$__autoload->carregar('Excecoes,Logs,Utilitarios,UsuarioDao,UsuarioNegocios');
class UsuarioNegocios extends Utilitarios implements UsuarioNegociosInterface {
    public function __construct(){}
    public function login( $_values = array() ){
        $UsuarioDao = new UsuarioDao();
        $values = array( 'usrlogin' => $_values['logim'], 'usrsenha' => $_values['senha'], 'usrnome' => $_values['logim'] );
        if($UsuarioDao->findData() != 0){
            if($UsuarioDao->findLogin($values) != 0){
                $this->startSessao();
                $this->setSessao("Log_Deus_Conosco", "Jesus");
                header("Location: /System/pages/principal/principal.phtml");
            }
            else{
                header("Location: /System/index.phtml?msg=fail_login");
            }
        }
        else{
            if( $this->criar($values) ){
                $this->startSessao();
                $this->setSessao("Log_Deus_Conosco", "Jesus");
                header("Location: /System/pages/principal/principal.phtml");
            }
            else
                header("Location: /System/index.phtml?msg=erro_file");
        }
    }
    public function logout(){
        $this->destroySessao("Log_Deus_Conosco");
        header("Location: /System/index.phtml?msg=invasor");
    }
    public function invasor(){
        $this->startSessao();
        if( $this->getSessao("Log_Deus_Conosco") != "Jesus" ){
            $this->logout();
        }
        return true;
    }
    private function criar( $_values = array() ){
        $UsuarioDao = new UsuarioDao();
        $Logs = new Logs($_SERVER[ 'DOCUMENT_ROOT' ]."/System/TMP/");
        try{
            if(file_exists($_SERVER[ 'DOCUMENT_ROOT' ]."/System/Configuracao/usuario.ini")){
                $dados = array('usrlogin' => $_values['usrlogin'], 'usrsenha' => $_values['usrsenha'], 'data' => $this->dateServer() );
                if($this->whiteFile( "/System/Configuracao/usuario.ini", $dados )){
                    $UsuarioDao->start();
                    $UsuarioDao->insertData($_values);
                    $commit = true;
                    if($UsuarioDao->executeP($commit))
                        return true;
                    else
                    {
                        throw new Excecoes(Excecoes::ERRO_INCLUSAO);
                        return false;
                    }
                }
                else
                    return false;
            }
            else{
                throw new Excecoes(Excecoes::ERRO_FILE);
            }
        }catch (Excecoes $e){
            $mensagem = $e."<b><i> Linha: ".$e->getLine()."<br />Arquivo: ".$_SERVER[ 'PHP_SELF' ]."</i></b><br />";
            $Logs->escrever($mensagem);
        }
    }
}
?>
