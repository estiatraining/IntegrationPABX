<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
    include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
    $__autoload = new LoadClass();
    $__autoload->carregar('Excecoes,Logs,Utilitarios,UsuarioNegocios');
    $UsuarioNegocios = new UsuarioNegocios();
    //$UsuarioNegocios->invasor();
    $UsuarioNegocios->login($_POST);
?>
