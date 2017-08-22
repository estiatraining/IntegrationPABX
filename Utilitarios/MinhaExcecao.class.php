<?php
/*
    Arquivo minhaExecao.class.php � o arquivo que instancia a classe Exception e a usa para gerar erros de excecoes!
    Autor: Cleison Ferreira de Melo.
*/
class MinhaExcecao extends Exception
{
    /* Redefine a exce��o para que a mensagem n�o seja opcional */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
    /* Representa��o do objeto personalizada no formato string */
    public function __toString()
    {
        return "{$this->message}\n";
    }
}
?>
