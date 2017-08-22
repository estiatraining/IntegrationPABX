<?php
/*
    Arquivo carregar.class.php � o arquivo que carrega as classes do sistema
    Autor: Cleison Ferreira de Melo.
*/
include_once "Ambiente.php";
include_once "Excecoes.class.php";
class LoadClass {
    private $classes = '';
    private $temp = '';
    private $pastas = '';
    private $pasta = '';
    public function __construct() {}
    public final function carregar($_vetor) {
        try {            
            if(file_exists($_SERVER[ 'DOCUMENT_ROOT' ]."/System/Configuracao/diretorios.ini")) {                
                $dados = parse_ini_file($_SERVER[ 'DOCUMENT_ROOT' ]."/System/Configuracao/diretorios.ini");
                $this->classes = explode(',',$_vetor);
                $this->temp = $_SERVER[ 'DOCUMENT_ROOT' ]."/System/";
                $this->pastas = array($dados);
                $j = 0;
                for($i = 0; $i < sizeof($this->classes); $i++) {
                    foreach($this->pastas as $this->pasta) {                        
                        for($j = 0; $j < sizeof($this->pasta); $j++) {
                            if(file_exists($this->temp."{$this->pasta[$j]}/{$this->classes[$i]}.class.php")) {
                                include_once $this->temp."{$this->pasta[$j]}/{$this->classes[$i]}.class.php";
                                //echo $this->temp."{$this->pasta[$j]}/{$this->classes[$i]}.class.php<br />";
                            }
                        }
                    }
                }
            }
            else
                throw new Excecoes(Excecoes::ERRO_FILE);
        }catch(Exception $e){
            echo $e."<b><i> Linha: ".$e->getLine()."<br />Arquivo: ".$_SERVER[ ' PHP_SELF' ]."</i></b>";
        }
    }
}
?>
