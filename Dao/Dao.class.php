<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Daoclass
 *
 * @author CleisonFerreira
 * @
 */
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
$__autoload = new LoadClass();
$__autoload->carregar('FuncoesDB');
abstract class Dao extends FuncoesDB {
    abstract public function findData();
    abstract public function insertData($_values = array());
    abstract public function updateData($_values = array());
    abstract public function deleteData($_id);
}
?>
