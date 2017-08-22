<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('max_execution_time','0');
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
include_once "RelatorioNegociosInterface.php";
require_once $_SERVER[ 'DOCUMENT_ROOT' ].'/System/PHPGrafics/phplot.php';
require_once $_SERVER[ 'DOCUMENT_ROOT' ].'/System/FPDF/Relatorio.class.php';
$__autoload = new LoadClass();
$__autoload->carregar('Excecoes,Logs,Utilitarios,DadosFiltradosDao,RelatorioGraficosRecebidasPerdidas,RelatorioGraficosTempoMedio,RelatorioGraficosTempoMedioEspera,RelatorioGraficoPorcentagem');
class RelatorioGeral extends Utilitarios implements RelatorioNegociosInterface {
    public function __construct(){}
    public function gerarRelGeral( $_dateIni, $_dateFim, $_timeIni , $_timeFim , $_ramal , $_timeLim , $_tipo ){
        $DadosFiltradosDao = new DadosFiltradosDao();
        $RelatorioGraficosRecebidasPerdidas = new RelatorioGraficosRecebidasPerdidas();
        $RelatorioGraficosTempoMedio = new RelatorioGraficosTempoMedio();
        $RelatorioGraficosTempoMedioEspera = new RelatorioGraficosTempoMedioEspera();
        $RelatorioGraficoPorcentagem = new RelatorioGraficoPorcentagem();        
        $values = array( 'dI' => $_dateIni, 'dF' => $_dateFim, 'hI' => $_timeIni, 'hF' => $_timeFim, 'ramal' => $_ramal, 'lim' => $_timeLim );
        $Relatorio = new Relatorio('P','mm', array(210, 311));
        $RelatorioGraficosRecebidasPerdidas->gerarGraficosRecebidasPerdidas($_dateIni, $_dateFim, $_timeIni, $_timeFim, $_ramal);
        $RelatorioGraficosTempoMedio->gerarGraficosTempoMedio($_dateIni, $_dateFim, $_timeIni, $_timeFim, $_ramal);
        $RelatorioGraficosTempoMedioEspera->gerarGraficosTempoMedioEspera($_dateIni, $_dateFim, $_timeIni, $_timeFim, $_ramal);
        $RelatorioGraficoPorcentagem->gerarGraficoPorcentagem($_dateIni, $_dateFim, $_timeIni, $_timeFim);
        define('FPDF_FONTPATH', $_SERVER[ 'DOCUMENT_ROOT' ].'/System/FPDF/font/');
        /*
         * Cabeçalho do relatorio geral
         */
        $texto = "Relatório Geral Descritivo do Sistema de Telefonia.\n";
        $texto .= "No Período de ".$_dateIni." à ".$_dateFim;
        $Relatorio->cabecalho( utf8_decode( "Relatório" ), utf8_decode( $texto ) );
        /*
         * Corpo do relatorio geral
         */
        $Relatorio->corpo();
        $result = array();
        $result = $DadosFiltradosDao->chamadasAtendidasPerdidasHora( $values );
        if( $DadosFiltradosDao->linesFind($result[ 'totalLigacoes' ]) == 0 ){
            $Relatorio->SetFont( 'Arial', 'b', 9 );
            $Relatorio->SetXY( 6, $Relatorio->GetY() + 5 );
            $Relatorio->SetWidths( array( 196 ) );
            $Relatorio->SetAligns( array( "C" ) );
            $Relatorio->Linha( array( utf8_decode( "Não foi Encontrado Nenhum Dado Para a Sua Busca!" ) ), 5, 0 );
        }
        while($Object = $DadosFiltradosDao->resultObject($result[ 'totalLigacoes' ])){
            $totalLigacoes = $Object->quantidade;
        }
        while($Object = $DadosFiltradosDao->resultObject($result[ 'totalLigacoesRecebidas' ])){
            $totalLigacoesRecebidas = $Object->quantidade;
        }
        while($Object = $DadosFiltradosDao->resultObject($result[ 'totalLigacoesPerdidas' ])){
            $totalLigacoesPerdidas = $Object->quantidade;
        }
        $aux1 = ($totalLigacoesRecebidas * 100) / $totalLigacoes;
        $aux2 = ($totalLigacoesPerdidas * 100) / $totalLigacoes;
        /*
         * Índices de Ligações por Mês.
         */
        $Relatorio->SetFont( 'Arial', 'b', 10 );
        $Relatorio->SetXY( 7, $Relatorio->GetY() + 2 );
        $Relatorio->SetWidths( array( 200 ) );
        $Relatorio->SetAligns( array( "C" ) );
        $Relatorio->Linha( array( utf8_decode( "Índices de Ligações do Período." )), 5, 0 );
        $Relatorio->pular(5);
        $Relatorio->SetFont( 'Arial', 'b', 8 );
        $Relatorio->SetXY( 19.5, $Relatorio->GetY() + 2 );
        $Relatorio->SetWidths( array( 35, 24.5, 24.5, 30, 24.5, 30 ) );
        $Relatorio->SetAligns( array( "C", "C", "C", "C", "C", "C" ) );
        $Relatorio->Linha( array( utf8_decode("Período (Data)"), utf8_decode("Recebidas"), utf8_decode("Atendidas"),
                                  utf8_decode("Não Atendidas"), utf8_decode("Atendidas (%)"), utf8_decode("Não Atendidas (%)")), 5, 1 );
        $Relatorio->pular(5);
        $Relatorio->SetFont( 'Arial', '', 7 );
        $Relatorio->SetXY( 19.5, $Relatorio->GetY() );
        $Relatorio->SetWidths( array( 35, 24.5, 24.5, 30, 24.5, 30 ) );
        $Relatorio->SetAligns( array( "C", "C", "C", "C", "C", "C" ) );
        $Relatorio->Linha( array( utf8_decode( $_dateIni." à ".$_dateFim ), utf8_decode( $totalLigacoes ), utf8_decode( $totalLigacoesRecebidas ),
                                  utf8_decode( $totalLigacoesPerdidas ), utf8_decode( number_format($aux1, 2,".", "")."%" ), utf8_decode( number_format($aux2, 2,".", "")."%" )), 5, 1 );
        /*
         * Imagem 1
         */
        $Relatorio->pular(5);
        $Relatorio->imagem($_SERVER[ 'DOCUMENT_ROOT' ]."/System/IMG/grafico01.png", 8, $Relatorio->GetY() + 5 );
        $Relatorio->pular(5);
        $Relatorio->SetFont( 'Arial', '', 7 );
        $Relatorio->SetXY( 3.2, $Relatorio->GetY() + 80.5 );
        $Relatorio->SetWidths( array( 70 ) );
        $Relatorio->SetAligns( array( "C" ) );
        $Relatorio->Linha( array( utf8_decode( "Gráfico de Ligações Atendidas/Não Atendidas por Hora." )), 5, 0 );
        $Relatorio->pular(5);
        /*
         * Índices de Ligações por Dia.
         */
        $Relatorio->SetFont( 'Arial', 'b', 10 );
        $Relatorio->SetXY( 7, $Relatorio->GetY() + 2 );
        $Relatorio->SetWidths( array( 200 ) );
        $Relatorio->SetAligns( array( "C" ) );
        $Relatorio->Linha( array( utf8_decode( "Índices de Ligações por Dia." )), 5, 0 );
        $Relatorio->pular(5);
        $Relatorio->SetFont( 'Arial', 'b', 8 );
        $Relatorio->SetXY( 7.5, $Relatorio->GetY() + 2 );
        $Relatorio->SetWidths( array( 21, 20, 20, 28, 21, 30, 28, 27 ) );
        $Relatorio->SetAligns( array( "C", "C", "C", "C", "C", "C", "C", "C" ) );
        $Relatorio->Linha( array( utf8_decode("Data"), utf8_decode("Recebidas"), utf8_decode("Atendidas"),
                                  utf8_decode("Não Atendidas"), utf8_decode("Atendidas (%)"), utf8_decode("Não Atendidas (%)"),
                                  utf8_decode("Média de Duração"), utf8_decode("Média de Espera") ), 5, 1 );
        $Relatorio->pular(5);
        while($Object = $DadosFiltradosDao->resultObject($result[ 'listagemLigacoesDia' ])){
            /*
             * Verififica o limite da página e cria uma nova página
             */
            if( $Relatorio->GetY() >= 280 ){
                $Relatorio->rodape( "Central IT" );
                $Relatorio->cabecalho( utf8_decode( "Relatório" ), utf8_decode( $texto ) );
                $Relatorio->corpo();
                $Relatorio->SetFont( 'Arial', 'b', 8 );
                $Relatorio->SetXY( 7.5, $Relatorio->GetY() + 2 );
                $Relatorio->SetWidths( array( 21, 20, 20, 28, 21, 30, 28, 27 ) );
                $Relatorio->SetAligns( array( "C", "C", "C", "C", "C", "C", "C", "C" ) );
                $Relatorio->Linha( array( utf8_decode("Data"), utf8_decode("Recebidas"), utf8_decode("Atendidas"),
                                          utf8_decode("Não Atendidas"), utf8_decode("Atendidas (%)"), utf8_decode("Não Atendidas (%)"),
                                          utf8_decode("Média de Duração"), utf8_decode("Média de Espera") ), 5, 1 );
                $Relatorio->pular(5);
                $Relatorio->rodape( "Central IT" );
            }
            $aux = ($Object->QUANT_RECEBIDAS * 100) / $Object->QUANT_CHAMADAS;
            $aux2 = ($Object->QUANT_PERDIDAS * 100) / $Object->QUANT_CHAMADAS;
            $data = $Object->dia . "/" . $Object->mes . "/" . $Object->ano;
            $Relatorio->SetFont( 'Arial', '', 7 );
            $Relatorio->SetXY( 7.5, $Relatorio->GetY() );
            $Relatorio->SetWidths( array( 21, 20, 20, 28, 21, 30, 28, 27 ) );
            $Relatorio->SetAligns( array( "C", "C", "C", "C", "C", "C", "C", "C" ) );
            $Relatorio->Linha( array( utf8_decode($data), utf8_decode($Object->QUANT_CHAMADAS), utf8_decode($Object->QUANT_RECEBIDAS),
                                      utf8_decode($Object->QUANT_PERDIDAS), number_format($aux, 2,".", "")."%", number_format($aux2, 2,".", "")."%",
                                      utf8_decode($Object->DURACAO), utf8_decode($Object->ESPERA) ), 5, 1 );
            $Relatorio->pular(5);
        }
        $Relatorio->pular(5);
        if( $Relatorio->GetY() >= 280 ){
            $Relatorio->rodape( "Central IT" );
            $Relatorio->cabecalho( utf8_decode( "Relatório" ), utf8_decode( $texto ) );
            $Relatorio->corpo();
            $Relatorio->rodape( "Central IT" );
        }
        /*
         * Índices de Ligações por Hora.
         */
        $Relatorio->SetFont( 'Arial', 'b', 10 );
        $Relatorio->SetXY( 7, $Relatorio->GetY() + 2 );
        $Relatorio->SetWidths( array( 200 ) );
        $Relatorio->SetAligns( array( "C" ) );
        $Relatorio->Linha( array( utf8_decode( "Índices de Ligações por Hora." )), 5, 0 );
        $Relatorio->pular(5);
        $Relatorio->SetFont( 'Arial', 'b', 8 );
        $Relatorio->SetXY( 46, $Relatorio->GetY() + 2 );
        $Relatorio->SetWidths( array( 22, 22, 22, 22, 28 ) );
        $Relatorio->SetAligns( array( "C", "C", "C", "C", "C" ) );
        $Relatorio->Linha( array( "Data",utf8_decode("Hora"), utf8_decode("Recebidas"), utf8_decode("Atendidas"),
                                  utf8_decode("Não Atendidas") ), 5, 1 );
        $Relatorio->pular(5);
        while($Object = $DadosFiltradosDao->resultObject($result[ 'listagemLigacoesHora' ])){
            /*
             * Verififica o limite da página e cria uma nova página
             */
            if( $Relatorio->GetY() >= 280 ){
                $Relatorio->rodape( "Central IT" );
                $Relatorio->cabecalho( utf8_decode( "Relatório" ), utf8_decode( $texto ) );
                $Relatorio->corpo();
                $Relatorio->SetFont( 'Arial', 'b', 8 );
                $Relatorio->SetXY( 46, $Relatorio->GetY() + 2 );
                $Relatorio->SetWidths( array( 22, 22, 22, 22, 28 ) );
                $Relatorio->SetAligns( array( "C", "C", "C", "C", "C" ) );
                $Relatorio->Linha( array( "Data",utf8_decode("Hora"), utf8_decode("Recebidas"), utf8_decode("Atendidas"),
                                          utf8_decode("Não Atendidas") ), 5, 1 );
                $Relatorio->pular(5);
                $Relatorio->rodape( "Central IT" );
            }
            $Relatorio->SetFont( 'Arial', '', 7 );
            $Relatorio->SetXY( 46, $Relatorio->GetY() );
            $Relatorio->SetWidths( array( 22, 22, 22, 22, 28 ) );
            $Relatorio->SetAligns( array( "C", "C", "C", "C", "C" ) );
            $Relatorio->Linha( array( $this->formatSystem($Object->dataligacao) ,utf8_decode($Object->hora).":00 - " . $Object->hora . ":59" , utf8_decode($Object->QUANT_CHAMADAS), utf8_decode($Object->QUANT_RECEBIDAS), utf8_decode($Object->QUANT_PERDIDAS) ), 5, 1 );
            $Relatorio->pular(5);
        }
        /*
         * Imagem 2
         */
        /*
         * Verififica o limite da página e cria uma nova página
         */
        if( $Relatorio->GetY() >= 150 ){
            $Relatorio->rodape( "Central IT" );
            $Relatorio->cabecalho( utf8_decode( "Relatório" ), utf8_decode( $texto ) );
            $Relatorio->corpo();
            $Relatorio->rodape( "Central IT" );
        }
        $Relatorio->pular(5);
        $Relatorio->SetFont( 'Arial', '', 5 );
        $Relatorio->imagem($_SERVER[ 'DOCUMENT_ROOT' ]."/System/IMG/grafico02.png", 8, $Relatorio->GetY() + 5 );
        $Relatorio->pular(5);
        $Relatorio->SetFont( 'Arial', '', 7 );
        $Relatorio->SetXY( 3.2, $Relatorio->GetY() + 80.5 );
        $Relatorio->SetWidths( array( 70 ) );
        $Relatorio->SetAligns( array( "C" ) );
        $Relatorio->Linha( array( utf8_decode( "Gráfico da Quantidade de Chamadas por Hora." )), 5, 0 );
        $Relatorio->pular(5);
        /*
         * Índices de Tempo de Ligação.
         */
        /*
         * Verififica o limite da página e cria uma nova página
         */
        if( $Relatorio->GetY() >= 280 ){
            $Relatorio->rodape( "Central IT" );
            $Relatorio->cabecalho( utf8_decode( "Relatório" ), utf8_decode( $texto ) );
            $Relatorio->corpo();
            $Relatorio->rodape( "Central IT" );
        }
        $Relatorio->SetFont( 'Arial', 'b', 10 );
        $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
        $Relatorio->SetWidths( array( 200 ) );
        $Relatorio->SetAligns( array( "C" ) );
        $Relatorio->Linha( array( utf8_decode( "Índices de Tempo de Ligação." )), 5, 0 );
        $Relatorio->pular(5);
        $Relatorio->SetFont( 'Arial', 'b', 8 );
        $Relatorio->SetXY( 84, $Relatorio->GetY() + 2 );
        $Relatorio->SetWidths( array( 22, 22 ) );
        $Relatorio->SetAligns( array( "C", "C" ) );
        $Relatorio->Linha( array( utf8_decode("Hora"), utf8_decode("Média") ), 5, 1 );
        $Relatorio->pular(5);
        while($Object = $DadosFiltradosDao->resultObject($result[ 'totalLigacoesHora' ])){
            /*
             * Verififica o limite da página e cria uma nova página
             */
            if( $Relatorio->GetY() >= 280 ){
                $Relatorio->rodape( "Central IT" );
                $Relatorio->cabecalho( utf8_decode( "Relatório" ), utf8_decode( $texto ) );
                $Relatorio->corpo();
                $Relatorio->SetFont( 'Arial', 'b', 8 );
                $Relatorio->SetXY( 84, $Relatorio->GetY() + 2 );
                $Relatorio->SetWidths( array( 22, 22 ) );
                $Relatorio->SetAligns( array( "C", "C" ) );
                $Relatorio->Linha( array( utf8_decode("Hora"), utf8_decode("Média") ), 5, 1 );
                $Relatorio->pular(5);
                $Relatorio->rodape( "Central IT" );
            }
            $Relatorio->SetFont( 'Arial', '', 7 );
            $Relatorio->SetXY( 84, $Relatorio->GetY() );
            $Relatorio->SetWidths( array( 22, 22 ) );
            $Relatorio->SetAligns( array( "C", "C" ) );
            $Relatorio->Linha( array( utf8_decode($Object->hora).":00 - " . $Object->hora . ":59", utf8_decode($Object->DURACAO) ), 5, 1 );
            $Relatorio->pular(5);
        }
        /*
         * Imagem 3
         */
        if( $Relatorio->GetY() >= 150 ){
            $Relatorio->rodape( "Central IT" );
            $Relatorio->cabecalho( utf8_decode( "Relatório" ), utf8_decode( $texto ) );
            $Relatorio->corpo();
            $Relatorio->rodape( "Central IT" );
        }
        /*
         * Índices de Atraso Gerenciável.
         */
        $Relatorio->SetFont( 'Arial', 'b', 10 );
        $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
        $Relatorio->SetWidths( array( 200 ) );
        $Relatorio->SetAligns( array( "C" ) );
        $Relatorio->Linha( array( utf8_decode( "Índices de Atraso Gerenciável." )), 5, 0 );
        $Relatorio->pular(5);
        $Relatorio->SetFont( 'Arial', '', 5 );
        $Relatorio->imagem($_SERVER[ 'DOCUMENT_ROOT' ]."/System/IMG/grafico03.png", 8, $Relatorio->GetY() + 5 );
        $Relatorio->pular(5);
        $Relatorio->SetFont( 'Arial', '', 7 );
        $Relatorio->SetXY( 3.2, $Relatorio->GetY() + 80.5 );
        $Relatorio->SetWidths( array( 70 ) );
        $Relatorio->SetAligns( array( "C" ) );
        $Relatorio->Linha( array( utf8_decode( "Gráfico de Tempo Médio de Espera por Hora." )), 5, 0 );
        $Relatorio->pular(5);
        /*
         * Imagem 4
         */
        if( $Relatorio->GetY() >= 150 ){
            $Relatorio->rodape( "Central IT" );
            $Relatorio->cabecalho( utf8_decode( "Relatório" ), utf8_decode( $texto ) );
            $Relatorio->corpo();
            $Relatorio->rodape( "Central IT" );
        }
        $Relatorio->SetFont( 'Arial', '', 5 );
        $Relatorio->imagem($_SERVER[ 'DOCUMENT_ROOT' ]."/System/IMG/grafico04.png", 8, $Relatorio->GetY() + 5 );
        $Relatorio->pular(5);
        $Relatorio->SetFont( 'Arial', '', 7 );
        $Relatorio->SetXY( 3.2, $Relatorio->GetY() + 80.5 );
        $Relatorio->SetWidths( array( 90 ) );
        $Relatorio->SetAligns( array( "C" ) );
        $Relatorio->Linha( array( utf8_decode( "Gráfico de Percentual de taxas de espera inferior e superior a 30 segundos." )), 5, 0 );
        $Relatorio->pular(5);
        /*
         * Rodape do relatorio geral
         */
        $Relatorio->rodape( "Central IT" );
        $Relatorio->Output("arquivo","I");

    }
}
?>
