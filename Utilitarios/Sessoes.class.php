<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sessoesclass
 *
 * @author CleisonFerreira
 * @
 */
    class Sessoes
    {
        private $nome = '';
        private $valor = '';
        public function __construct() {  }
        public function startSessao()
        {
            @session_start();
        }
        public function setSessao($_nome, $_valor)
        {
            $this->nome = $_nome;
            $this->valor = $_valor;
            session_register("'".$this->nome."'");
            $_SESSION["'".$this->nome."'"] = $this->valor;
        }
        public function getSessao($_nome)
        {
            return $_SESSION["'".$_nome."'"];
        }
        public function destroySessao($_nome)
        {
            @$_SESSION["'".$_nome."'"];
            session_destroy();
        }
    }
?>
