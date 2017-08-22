<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
    ini_set('max_execution_time','0');
    include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
    $__autoload = new LoadClass();
    $__autoload->carregar('UploadNegocios,UsuarioNegocios');
    $UsuarioNegocios = new UsuarioNegocios();
    $UsuarioNegocios->invasor();
    $UploadNegocios = new UploadNegocios();
    if($UploadNegocios->carregarDados($_POST[ 'id' ])){
        echo "<script>alert('Processo terminado com sucesso!');</script>";
        echo "<script>selectBox('../relatorio/relatorio.phtml', '".$_POST[ 'acao' ]."', '".$_POST[ 'id' ]."', '#relatorio');</script>";
    }
    else
        echo "<script>$('#gravandoDados').hide();alert('Não foi possivel abrir o arquivo enviado!');</script>";
?>
