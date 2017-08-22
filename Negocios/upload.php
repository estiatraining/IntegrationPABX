<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
    ini_set('max_execution_time','0');
    ini_set('upload_max_filesize', '-1');
    ini_set('post_max_size','-1');
    ini_set('memory_limit','-1');
    include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Negocios/UploadNegocios.class.php";
    $UploadNegocios = new UploadNegocios();
    if(!empty ($_FILES)){
        $UploadNegocios->upload($_FILES, $_SERVER['DOCUMENT_ROOT']."/System/TMP/");        
    }
?>
