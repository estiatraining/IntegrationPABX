<?php
/*
    Arquivo excecoes.class.php � o arquivo que seta as escecoes do sistema
    Autor: Cleison Ferreira de Melo.
*/
include_once "LoadClass.class.php";
$__autoload = new LoadClass();
$__autoload->carregar('MinhaExcecao');
class Excecoes extends MinhaExcecao
{
    public $var = "";
    const ERRO_BANCO = 1;
    const ERRO_CONSULTA = 2;
    const ERRO_INCLUSAO = 3;
    const ERRO_EXCLUSAO = 4;
    const ERRO_ALTERACAO = 5;
    const ERRO_SINTAXE = 6;
    const ERRO_ENVIO = 7;
    const ERRO_RECEBIMENTO = 8;
    const ERRO_CUSTOM = 9;
    const ERRO_FILE = 10;
    const ERRO_FILE_VAZIO = 11;
    const ERRO_SQL = 12;
    const ERRO_FILE_ABRIR = 13;
    const ERRO_DIVERSOS = 14;
    const ERRO_LOGAR = 15;
    const ERRO_EXTRACT = 16;
    const CARREGAR_DADOS = 17;
    function __construct($_erro = self::ERRO_DIVERSOS)
    {
         switch($_erro)
         {
            case self::CARREGAR_DADOS :
                throw new MinhaExcecao("<b><i>Erro, ao carregar arquivos no upload!</i></b>", 1);
                break;
            case self::ERRO_EXTRACT :
                throw new MinhaExcecao("<b><i>Erro, ao extrair arquivos zipados!</i></b>", 1);
                break;
            case self::ERRO_BANCO :
                throw new MinhaExcecao("<b><i>Erro, ao conectar ao banco de dados!</i></b>", 1);
                break;
            case self::ERRO_CONSULTA :
                throw new MinhaExcecao("<b><i>Erro, ao fazer uma consulta no banco de dados!</i></b>", 2);
                break;
            case self::ERRO_INCLUSAO :
                throw new MinhaExcecao("<b><i>Erro, ao incluir algo no banco de dados!</i></b>", 3);
                break;
            case self::ERRO_EXCLUSAO :
                throw new MinhaExcecao("<b><i>Erro, ao excluir algo no banco de dados!</i></b>", 4);
                break;
            case self::ERRO_ALTERACAO :
                throw new MinhaExcecao("<b><i>Erro, ao alterar algo no banco de dados!</i></b>", 5);
                break;
            case self::ERRO_SINTAXE :
                throw new MinhaExcecao("<b><i>Erro, de sintaxe no script de comando!</i></b>", 6);
                break;
            case self::ERRO_ENVIO :
                throw new MinhaExcecao("<b><i>Erro, no envio de email!</i></b>", 7);
                break;
            case self::ERRO_RECEBIMENTO :
                throw new MinhaExcecao("<b><i>Erro, no recebimento de email!</i></b>", 8);
                break;
            case self::ERRO_CUSTOM :
                throw new MinhaExcecao("<b><i>Erro, ao buscar arquivos!</i></b>", 9);
                break;
            case self::ERRO_FILE :
                throw new MinhaExcecao("<b><i>Erro, ao abrir arquivo!</i></b>", 10);
                break;
            case self::ERRO_FILE_VAZIO :
                throw new MinhaExcecao("<b><i>Erro, arquivo de configura��o vazio!</i></b>", 11);
                break;
            case self::ERRO_SQL :
                throw new MinhaExcecao("<b><i>Erro, entrada sql ivalida ou incorreta!</i></b>", 12);
                break;
            case self::ERRO_FILE_ABRIR :
                throw new MinhaExcecao("<b><i>Erro, n�o foi possivel escrever no arquivo!</i></b>", 13);
                break;
            case self::ERRO_LOGAR :
                throw new MinhaExcecao("<b><i>Erro, não foi possivel logar!</i></b>", 15);
                break;
            default:
                $this->var = $_erro;
                break;
         }
    }
}
?>