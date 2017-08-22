<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FuncoesDBclass
 *
 * @author CleisonFerreira
 * @
 */
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
$__autoload = new LoadClass();
$__autoload->carregar('PersistenceDados');
class FuncoesDB extends PersistenceDados {
    private static $conn;
    public function __construct()
    {
        self::$conn = $this->connect();
    }
    public function linesFind($result){
        return mysql_num_rows($result);
    }
    public function linesAffected(){
        return mysql_affected_rows();
    }
    public function resultArray($result){
        if($this->linesFind($result) != 0){
           return mysql_fetch_assoc($result);
        }
        else
            return false;
    }
    public function resultLines($result, $row, $field){
        return mysql_result($result, $row, $field);
    }
    public function resultObject($result){
        if($this->linesFind($result) != 0){
           return mysql_fetch_object($result);
        }
        else
            return false;
    }
    public function freeResult($result){
        return mysql_free_result($result);
    }
}
?>
