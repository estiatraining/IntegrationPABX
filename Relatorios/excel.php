<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    header("Content-type: application/msexcel");
    header("Content-Disposition: attachment; filename=relExcelTelefonia.xls");
    include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
    include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
    ini_set('max_execution_time','0');
    $__autoload = new LoadClass();
    $__autoload->carregar('RelatorioExcel,UsuarioNegocios');
    $UsuarioNegocios = new UsuarioNegocios();
    $UsuarioNegocios->invasor();
    $dados = $_GET;
    $RelatorioExcel = new RelatorioExcel();
    echo $RelatorioExcel->gerarRelExcel( $dados['dI'], $dados['dF'], $dados['hI'], $dados['hF'], $dados['ramal'], $dados['lim'], $dados['tipo'] );
?>
