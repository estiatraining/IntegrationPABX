<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
    include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
    ini_set('max_execution_time','0');
    $__autoload = new LoadClass();
    $__autoload->carregar('RelatorioPDF,UsuarioNegocios');
    $UsuarioNegocios = new UsuarioNegocios();
    $UsuarioNegocios->invasor();
    $dados = $_GET;
    $RelatorioPDF = new RelatorioPDF();
    $RelatorioPDF->gerarPDF( $dados['dI'], $dados['dF'], $dados['hI'], $dados['hF'], $dados['ramal'], $dados['lim'], $dados['tipo'] );
?>
