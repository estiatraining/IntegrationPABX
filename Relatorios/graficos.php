<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
    include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
    ini_set('max_execution_time','0');
    $__autoload = new LoadClass();
    $__autoload->carregar('RelatorioGraficos,UsuarioNegocios');
    $UsuarioNegocios = new UsuarioNegocios();
    //$UsuarioNegocios->invasor();
    //$_GET = array('dI'=>'01/05/2010','dF'=>'01/07/2010');
    $dados = $_GET;
    $RelatorioGraficos = new RelatorioGraficos();
    $RelatorioGraficos->gerarGraficos( $dados['dI'], $dados['dF'], $dados['hI'], $dados['hF'], $dados['ramal'], $dados['lim'], $dados['tipo'] );
?>
