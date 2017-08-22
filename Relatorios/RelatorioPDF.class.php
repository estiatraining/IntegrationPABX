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
$__autoload->carregar('Excecoes,Logs,Utilitarios,DadosFiltradosDao');
class RelatorioPDF extends Utilitarios implements RelatorioNegociosInterface {
    public function __construct(){}
    public function gerarPDF( $_dateIni, $_dateFim, $_timeIni, $_timeFim, $_ramal, $_timeLim, $_tipo ){
        $DadosFiltradosDao = new DadosFiltradosDao();
        $values = array( 'dI' => $_dateIni, 'dF' => $_dateFim, 'hI' => $_timeIni, 'hF' => $_timeFim, 'ramal' => $_ramal, 'lim' => $_timeLim );
        $Relatorio = new Relatorio('P','mm', array(210, 311));
        define('FPDF_FONTPATH', $_SERVER[ 'DOCUMENT_ROOT' ].'/System/FPDF/font/');
        $limite = "";
        if( $_timeLim != ""){
            $limite = " Com Tempo de Demora no Atendimento Igual ou Superior à ".$_timeLim." Segundos";
        }
        if( $_tipo == "ANC" ){
            $texto = "Relatório Analítico de Todos os Dados.\n";
            $texto .= "No Período de ".$_dateIni." à ".$_dateFim.$limite;
        }
        else if( $_tipo == "SIC" ){
            $texto = "Relatório Sintético de Todos os Dados.\n";
            $texto .= "No Período de ".$_dateIni." à ".$_dateFim.$limite;
        }
        else if( $_tipo == "ANR" ){
            $texto = "Relatório Analítico de Todos os Dados do Ramal ".$_ramal.".\n";
            $texto .= "No Período de ".$_dateIni." à ".$_dateFim.$limite;
        }
        else if( $_tipo == "SIR" ){
            $texto = "Relatório Sintético de Todos os Dados do Ramal ".$_ramal.".\n";
            $texto .= "No Período de ".$_dateIni." à ".$_dateFim.$limite;
        }
        $Relatorio->cabecalho( utf8_decode( "Relatório" ), utf8_decode( $texto ) );
        $Relatorio->corpo();
        $Relatorio->rodape( "Central IT" );
        /************************************************************************************************************************
         * RELATORIO ANALITICO COMPLETO
         ************************************************************************************************************************/
        if( $_tipo == "ANC" ){
            $result = $DadosFiltradosDao->relAnalitCompleto( $values );
            if( $DadosFiltradosDao->linesFind($result) == 0 ){
                $Relatorio->SetFont( 'Arial', 'b', 9 );
                $Relatorio->SetXY( 6, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 196 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Não foi Encontrado Nenhum Dado Para a Sua Busca!" ) ), 5, 0 );
            }
            while($Object = $DadosFiltradosDao->resultObject($result)){
                if( $Relatorio->GetY() >= 265 ){
                    $Relatorio->rodape( "Central IT" );
                    $Relatorio->cabecalho( utf8_decode( "Relatório" ), utf8_decode( $texto ) );
                    $Relatorio->corpo();
                    $Relatorio->rodape( "Central IT" );
                }
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 2 );
                $Relatorio->SetWidths( array( 24.5 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( "Identificador:" ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 31.5, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 15 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->IDENTIFICADORCHAMADA ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 46.5, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 30.5 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( "Nome do Atendente:" ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 77, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 37.5 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->ATENDENTE ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 114.5, $Relatorio->GetY()  );
                $Relatorio->SetWidths( array( 18 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( "Ramal:" ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 132.5, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 20 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->RAMAL ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 152.5, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Data da Ligação:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 182.5, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 19.5 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $this->formatSystem( $Object->DATALIGACAO ) ), 5, 1 );
///////////////////////////////////////////////////////////////////////////////////////////////////////////
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Hora da Ligação:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 37, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 20 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->HORAINICIOLIGACAO ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 57, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Hora Atendimento:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 87, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 20 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->HORAINICIOATENDIMENTO ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 107, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Hora Finalização:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 137, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 20 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->HORAFIMLIGACAO ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 157, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Tempo Atendimento:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 187, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 15 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->TEMPOLIGACAO ), 5, 1 );
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 27 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Tempo Ligação:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 34, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 18 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->TEMPOATENDIMENTO ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 52, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 27 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Telefone Cliente:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 79, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 20 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $numeroTelefone = ( strlen($Object->NUMEROTELEFONE) <= 12 ) ? $Object->NUMEROTELEFONE : substr($Object->NUMEROTELEFONE, 2, strlen($Object->NUMEROTELEFONE));
                $Relatorio->Linha( array( $numeroTelefone ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 99, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 22 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Nome Cliente:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 121, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 27 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->NOMECLIENTE ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 148, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 20 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Observação:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 168, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 34 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->OBSERVACAO ), 5, 1 );

                $Relatorio->SetXY( 6, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 296 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $this->Risco( "_", 142 ) ), 5, 0 );
                $Relatorio->pular( 5 );
                //break;
            }
            if( $DadosFiltradosDao->linesFind($result) != 0 ){
                $Relatorio->SetFont( 'Arial', 'b', 9 );
                $Relatorio->SetXY( 162, $Relatorio->GetY() + 2 );
                $Relatorio->SetWidths( array( 40 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Quantidade: ".$DadosFiltradosDao->linesFind($result) ) ), 5, 1 );
            }
        }
        /************************************************************************************************************************
         * RELATORIO SINTETICO COMPLETO
         ************************************************************************************************************************/
        else if( $_tipo == "SIC" ){
            $result = array();
            $result = $DadosFiltradosDao->relSintetCompleto( $values );
            if( $DadosFiltradosDao->linesFind( $result['totalLigacoes'] ) != 0 ){
                if( $DadosFiltradosDao->linesFind( $result['totalLigacoes'] ) == 0 ){
                    $Relatorio->SetFont( 'Arial', 'b', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 2 );
                    $Relatorio->SetWidths( array( 28 ) );
                    $Relatorio->SetAligns( array( "C" ) );
                    $Relatorio->Linha( array( utf8_decode( "Total de Ligações:" ) ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 35, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( 0 ), 5, 1 );
                }
                while( $TotalLigacoes = $DadosFiltradosDao->resultObject( $result['totalLigacoes'] ) ){
                    $Relatorio->SetFont( 'Arial', 'b', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 2 );
                    $Relatorio->SetWidths( array( 28 ) );
                    $Relatorio->SetAligns( array( "C" ) );
                    $Relatorio->Linha( array( utf8_decode( "Total de Ligações:" ) ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 35, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( $TotalLigacoes->quantidade ), 5, 1 );
                    break;
                }
                if( $DadosFiltradosDao->linesFind( $result['totalLigacoesRecebidas'] ) == 0 ){
                    $Relatorio->SetFont( 'Arial', 'b', 7 );
                    $Relatorio->SetXY( 55, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 40 ) );
                    $Relatorio->SetAligns( array( "C" ) );
                    $Relatorio->Linha( array( utf8_decode( "Total de Ligações Recebidas:" ) ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 91, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( 0 ), 5, 1 );
                }
                while( $TotalLigacoesRecebidas = $DadosFiltradosDao->resultObject( $result['totalLigacoesRecebidas'] ) ){
                    $Relatorio->SetFont( 'Arial', 'b', 7 );
                    $Relatorio->SetXY( 55, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 40 ) );
                    $Relatorio->SetAligns( array( "C" ) );
                    $Relatorio->Linha( array( utf8_decode( "Total de Ligações Recebidas:" ) ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 95, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( $TotalLigacoesRecebidas->quantidade ), 5, 1 );
                    break;
                }
                if( $DadosFiltradosDao->linesFind( $result['totalLigacoesPerdidas'] ) == 0 ){
                    $Relatorio->SetFont( 'Arial', 'b', 7 );
                    $Relatorio->SetXY( 115, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 36 ) );
                    $Relatorio->SetAligns( array( "C" ) );
                    $Relatorio->Linha( array( utf8_decode( "Total de Ligações Perdidas:" ) ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 151, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( 0 ), 5, 1 );
                }
                while( $TotalLigacoesPerdidas = $DadosFiltradosDao->resultObject( $result['totalLigacoesPerdidas'] ) ){
                    $Relatorio->SetFont( 'Arial', 'b', 7 );
                    $Relatorio->SetXY( 115, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 36 ) );
                    $Relatorio->SetAligns( array( "C" ) );
                    $Relatorio->Linha( array( utf8_decode( "Total de Ligações Perdidas:" ) ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 151, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( $TotalLigacoesPerdidas->quantidade ), 5, 1 );
                    break;
                }
                $taxasDesempenho = $DadosFiltradosDao->taxasDesempenho($values);
                $taxasDesempenho['superior'] = ( ( $taxasDesempenho['superior'] * 100 ) / $TotalLigacoesRecebidas->quantidade);
                $taxasDesempenho['inferior'] = ( ( $taxasDesempenho['inferior'] * 100 ) / $TotalLigacoesRecebidas->quantidade);
                $taxasDesempenho['superior'] = explode(".", $taxasDesempenho['superior']);
                $taxasDesempenho['superior'] = $taxasDesempenho['superior'][0].".".substr($taxasDesempenho['superior'][1], 0, 2);
                $taxasDesempenho['inferior'] = explode(".", $taxasDesempenho['inferior']);
                $taxasDesempenho['inferior'] = $taxasDesempenho['inferior'][0].".".substr($taxasDesempenho['inferior'][1], 0, 2);
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 196 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Taxas de Desempenho:" ) ), 5, 1 );
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 60 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Porcentagem de Ligações não Abandonadas com Tempo de Espera Superior à 30 Segundos:" ) ), 10, 1 );
                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 67, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( $taxasDesempenho['superior']."%" ) ), 10, 1 );
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 92, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 60 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Porcentagem de Ligações não Abandonadas com Tempo de Espera Inferior à 30 Segundos:" ) ), 10, 1 );
                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 152, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode(  $taxasDesempenho['inferior']."%" ) ), 10, 1 );
                ///////////////////////////////////////////////////////////////////////////////////////////
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 10 );
                $Relatorio->SetWidths( array( 196 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Dia com Maior Número de Ligações:" ) ), 5, 1 );
                if( $DadosFiltradosDao->linesFind( $result['totalLigacoesRecebidasDiaMaior'] ) == 0 ){
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: 0" ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Dia: " ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 52, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Mês: " ) ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 72, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Ano: " ) ), 5, 1 );
                }
                $cont = 0;
                while( $TotalLigacoesRecebidasDiaMaior = $DadosFiltradosDao->resultObject( $result['totalLigacoesRecebidasDiaMaior'] ) ){
                    $cor = ( ( $cont % 2 ) == 0 ) ? "#FFFFFF" : "#DFDFDF";
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: ".$TotalLigacoesRecebidasDiaMaior->quantidade ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Dia: ".$TotalLigacoesRecebidasDiaMaior->dia ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 52, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Mês: ".$TotalLigacoesRecebidasDiaMaior->mes ) ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 72, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Ano: ".$TotalLigacoesRecebidasDiaMaior->ano ) ), 5, 1, $cor );
                    $cont++;
                }
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 196 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Dia com Menor Número de Ligações:" ) ), 5, 1 );
                if( $DadosFiltradosDao->linesFind( $result['totalLigacoesRecebidasDiaMenor'] ) == 0 ){
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: 0" ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Dia: " ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 52, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Mês: " ) ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 72, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Ano: " ) ), 5, 1 );
                }
                $cont = 0;
                while( $TotalLigacoesRecebidasDiaMenor = $DadosFiltradosDao->resultObject( $result['totalLigacoesRecebidasDiaMenor'] ) ){
                    $cor = ( ( $cont % 2 ) == 0 ) ? "#FFFFFF" : "#DFDFDF";
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: ".$TotalLigacoesRecebidasDiaMenor->quantidade ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Dia: ".$TotalLigacoesRecebidasDiaMenor->dia ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 52, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Mês: ".$TotalLigacoesRecebidasDiaMenor->mes ) ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 72, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Ano: ".$TotalLigacoesRecebidasDiaMenor->ano ) ), 5, 1, $cor );
                    $cont++;
                }
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 196 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Horas com Maior Número de Ligações:" ) ), 5, 1 );
                if( $DadosFiltradosDao->linesFind( $result['totalLigacoesRecebidasHoraMaior'] ) == 0 ){
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: 0" ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Hora: " ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Dia: " ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 72, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Mês: " ) ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 92, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Ano: " ) ), 5, 1 );
                }
                $cont = 0;
                while( $TotalLigacoesRecebidasHoraMaior = $DadosFiltradosDao->resultObject( $result['totalLigacoesRecebidasHoraMaior'] ) ){
                    $cor = ( ( $cont % 2 ) == 0 ) ? "#FFFFFF" : "#DFDFDF";
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: ".$TotalLigacoesRecebidasHoraMaior->quantidade ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Hora: ".$TotalLigacoesRecebidasHoraMaior->hora ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 52, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Dia: ".$TotalLigacoesRecebidasHoraMaior->dia ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 72, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Mês: ".$TotalLigacoesRecebidasHoraMaior->mes ) ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 92, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Ano: ".$TotalLigacoesRecebidasHoraMaior->ano ) ), 5, 1, $cor );
                    $cont++;
                }
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 196 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Horas com Menor Número de Ligações:" ) ), 5, 1 );
                if( $DadosFiltradosDao->linesFind( $result['totalLigacoesRecebidasHoraMenor'] ) == 0 ){
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: 0" ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Hora: " ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Dia: " ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 72, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Mês: " ) ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 92, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Ano: " ) ), 5, 1 );
                }
                $cont = 0;
                while( $TotalLigacoesRecebidasHoraMenor = $DadosFiltradosDao->resultObject( $result['totalLigacoesRecebidasHoraMenor'] ) ){
                    $cor = ( ( $cont % 2 ) == 0 ) ? "#DFDFDF" : "#DFDFDF";
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: ".$TotalLigacoesRecebidasHoraMenor->quantidade ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Hora: ".$TotalLigacoesRecebidasHoraMenor->hora ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 52, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Dia: ".$TotalLigacoesRecebidasHoraMenor->dia ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 72, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Mês: ".$TotalLigacoesRecebidasHoraMenor->mes ) ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 92, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( utf8_decode( "Ano: ".$TotalLigacoesRecebidasHoraMenor->ano ) ), 5, 1, $cor );
                    $cont++;
                }
                ////////////////////////////////////////////////////////////////////////////////
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 196 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Ramais com Maior Número de Ligações Perdidas:" ) ), 5, 1 );
                if( $DadosFiltradosDao->linesFind( $result['totalLigacoesPerdidasRamal'] ) == 0 ){
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: 0" ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Ramal: " ), 5, 1 );
                }
                $cont = 0;
                while( $TotalLigacoesPerdidasRamal = $DadosFiltradosDao->resultObject( $result['totalLigacoesPerdidasRamal'] ) ){
                    $cor = ( ( $cont % 2 ) == 0 ) ? "#DFDFDF" : "#DFDFDF";
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: ".$TotalLigacoesPerdidasRamal->quantidade ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Hora: ".$TotalLigacoesPerdidasRamal->ramal ), 5, 1, $cor );
                    $cont++;
                }
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 196 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Ramais com Maior Número de Ligações Recebidas:" ) ), 5, 1 );
                if( $DadosFiltradosDao->linesFind( $result['totalLigacoesRecebidasRamalMaior'] ) == 0 ){
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: 0" ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Ramal: " ), 5, 1 );
                }
                $cont = 0;
                while( $TotalLigacoesRecebidasRamalMaior = $DadosFiltradosDao->resultObject( $result['totalLigacoesRecebidasRamalMaior'] ) ){
                    $cor = ( ( $cont % 2 ) == 0 ) ? "#DFDFDF" : "#DFDFDF";
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: ".$TotalLigacoesRecebidasRamalMaior->quantidade ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 40 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Ramal: ".$TotalLigacoesRecebidasRamalMaior->ramal ), 5, 1, $cor );
                    $cont++;
                }
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 196 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Ramais com Menor Número de Ligações Recebidas:" ) ), 5, 1 );
                if( $DadosFiltradosDao->linesFind( $result['totalLigacoesRecebidasRamalMenor'] ) == 0 ){
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: 0" ), 5, 1 );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 20 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Ramal: " ), 5, 1 );
                }
                $cont = 0;
                while( $TotalLigacoesRecebidasRamalMenor = $DadosFiltradosDao->resultObject( $result['totalLigacoesRecebidasRamalMenor'] ) ){
                    $cor = ( ( $cont % 2 ) == 0 ) ? "#DFDFDF" : "#DFDFDF";
                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                    $Relatorio->SetWidths( array( 25 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Quantidade: ".$TotalLigacoesRecebidasRamalMenor->quantidade ), 5, 1, $cor );

                    $Relatorio->SetFont( 'Arial', '', 7 );
                    $Relatorio->SetXY( 32, $Relatorio->GetY() );
                    $Relatorio->SetWidths( array( 40 ) );
                    $Relatorio->SetAligns( array( "L" ) );
                    $Relatorio->Linha( array( "Ramal: ".$TotalLigacoesRecebidasRamalMenor->ramal ), 5, 1, $cor );
                    $cont++;
                }
            }
            else{
                $Relatorio->SetFont( 'Arial', 'b', 9 );
                $Relatorio->SetXY( 6, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 196 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Não foi Encontrado Nenhum Dado Para a Sua Busca!" ) ), 5, 0 );
            }
        }
        /************************************************************************************************************************
         * RELATORIO ANALITICO RAMAL
         ************************************************************************************************************************/
        else if( $_tipo == "ANR" ){
            $result = $DadosFiltradosDao->relAnalitRamal( $values );
            if( $DadosFiltradosDao->linesFind($result) == 0 ){
                $Relatorio->SetFont( 'Arial', 'b', 9 );
                $Relatorio->SetXY( 6, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 196 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Não foi Encontrado Nenhum Dado Para a Sua Busca!" ) ), 5, 0 );
            }
            while($Object = $DadosFiltradosDao->resultObject($result)){
                if( $Relatorio->GetY() >= 265 ){
                    $Relatorio->rodape( "Central IT" );
                    $Relatorio->cabecalho( utf8_decode( "Relatório" ), utf8_decode( $texto ) );
                    $Relatorio->corpo();
                    $Relatorio->rodape( "Central IT" );
                }
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 2 );
                $Relatorio->SetWidths( array( 24.5 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( "Identificador:" ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 31.5, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 15 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->IDENTIFICADORCHAMADA ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 46.5, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 30.5 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( "Nome do Atendente:" ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 77, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 37.5 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->ATENDENTE ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 114.5, $Relatorio->GetY()  );
                $Relatorio->SetWidths( array( 18 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( "Ramal:" ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 132.5, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 20 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->RAMAL ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 152.5, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Data da Ligação:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 182.5, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 19.5 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $this->formatSystem( $Object->DATALIGACAO ) ), 5, 1 );
///////////////////////////////////////////////////////////////////////////////////////////////////////////
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Hora da Ligação:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 37, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 20 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->HORAINICIOLIGACAO ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 57, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Hora Atendimento:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 87, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 20 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->HORAINICIOATENDIMENTO ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 107, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Hora Finalização:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 137, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 20 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->HORAFIMLIGACAO ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 157, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Tempo Atendimento:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 187, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 15 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->TEMPOLIGACAO ), 5, 1 );
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 27 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Tempo Ligação:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 34, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 18 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->TEMPOATENDIMENTO ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 52, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 27 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Telefone Cliente:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 79, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 20 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $numeroTelefone = ( strlen($Object->NUMEROTELEFONE) <= 12 ) ? $Object->NUMEROTELEFONE : substr($Object->NUMEROTELEFONE, 2, strlen($Object->NUMEROTELEFONE));
                $Relatorio->Linha( array( $numeroTelefone ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 99, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 22 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Nome Cliente:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 121, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 27 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->NOMECLIENTE ), 5, 1 );

                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 148, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 20 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Observação:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 168, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 34 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $Object->OBSERVACAO ), 5, 1 );

                $Relatorio->SetXY( 6, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 296 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $this->Risco( "_", 142 ) ), 5, 0 );
                $Relatorio->pular( 5 );
                //break;
            }
            if( $DadosFiltradosDao->linesFind($result) != 0 ){
                $Relatorio->SetFont( 'Arial', 'b', 9 );
                $Relatorio->SetXY( 162, $Relatorio->GetY() + 2 );
                $Relatorio->SetWidths( array( 40 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Quantidade: ".$DadosFiltradosDao->linesFind($result) ) ), 5, 1 );
            }
        }
        /************************************************************************************************************************
         * RELATORIO SISNTITICO RAMAL
         ************************************************************************************************************************/
        else if( $_tipo == "SIR" ){
            $result = array();
            $result = $DadosFiltradosDao->relSintetRamal( $values );
            if( $DadosFiltradosDao->linesFind( $result['totalLigacoesRecebidas'] ) == 0 ){
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 2 );
                $Relatorio->SetWidths( array( 40 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Total de Ligações Recebidas:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 47, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( 0 ), 5, 1 );
            }
            while( $TotalLigacoesRecebidas = $DadosFiltradosDao->resultObject( $result['totalLigacoesRecebidas'] ) ){
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 2 );
                $Relatorio->SetWidths( array( 40 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Total de Ligações Recebidas:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 47, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $TotalLigacoesRecebidas->quantidade ), 5, 1 );
                break;
            }
            if( $DadosFiltradosDao->linesFind( $result['totalLigacoesPerdidas'] ) == 0 ){
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 72, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 40 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Total de Ligações Perdidas:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 112, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( 0 ), 5, 1 );
            }
            while( $TotalLigacoesPerdidas = $DadosFiltradosDao->resultObject( $result['totalLigacoesPerdidas'] ) ){
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 72, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 40 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Total de Ligações Perdidas:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 112, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $TotalLigacoesPerdidas->quantidade ), 5, 1 );
                break;
            }
            if( $DadosFiltradosDao->linesFind( $result['mediaAtendimento'] ) == 0 ){
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 137, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 35 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Tempo Médio para Atender:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 172, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( 0 ), 5, 1 );
            }
            while( $MediaAtendimento = $DadosFiltradosDao->resultObject( $result['mediaAtendimento'] ) ){
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 137, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 35 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Tempo Médio para Atender:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 172, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $MediaAtendimento->media ), 5, 1 );
                break;
            }
            if( $DadosFiltradosDao->linesFind( $result['mediaLigacao'] ) == 0 ){
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 40 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Tempo Médio das Ligações:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 47, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( 0 ), 5, 1 );
            }
            while( $MediaLigacao = $DadosFiltradosDao->resultObject( $result['mediaLigacao'] ) ){
                $Relatorio->SetFont( 'Arial', 'b', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 40 ) );
                $Relatorio->SetAligns( array( "C" ) );
                $Relatorio->Linha( array( utf8_decode( "Tempo Médio das Ligações:" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 47, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( $MediaLigacao->media ), 5, 1 );
                break;
            }
            $Relatorio->SetFont( 'Arial', 'b', 7 );
            $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
            $Relatorio->SetWidths( array( 195 ) );
            $Relatorio->SetAligns( array( "L" ) );
            $Relatorio->Linha( array( utf8_decode( "Dia com Maior Número de Recebimento de Ligações:" ) ), 5, 1 );
            if( $DadosFiltradosDao->linesFind( $result['totalLigacaoDiaMais'] ) == 0 ){
                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Quantidade: 0" ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 37, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( "Dia: " ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 62, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Mês: " ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 87, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Ano: " ) ), 5, 1 );
            }
            while( $TotalLigacaoDiaMais = $DadosFiltradosDao->resultObject( $result['totalLigacaoDiaMais'] ) ){
                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( "Quantidade: ".$TotalLigacaoDiaMais->quantidade ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 37, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( "Dia: ".$TotalLigacaoDiaMais->dia ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 62, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Mês: ".$TotalLigacaoDiaMais->mes ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 87, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Ano: ".$TotalLigacaoDiaMais->ano ) ), 5, 1 );
            }
            $Relatorio->SetFont( 'Arial', 'b', 7 );
            $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
            $Relatorio->SetWidths( array( 195 ) );
            $Relatorio->SetAligns( array( "L" ) );
            $Relatorio->Linha( array( utf8_decode( "Dia com Menor Número de Recebimento de Ligações:" ) ), 5, 1 );
            if( $DadosFiltradosDao->linesFind( $result['totalLigacaoDiaMenos'] ) == 0 ){
                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( "Quantidade: " ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 37, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( "Dia: " ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 62, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Mês: " ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 87, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Ano: " ) ), 5, 1 );
            }
            while( $TotalLigacaoDiaMenos = $DadosFiltradosDao->resultObject( $result['totalLigacaoDiaMenos'] ) ){
                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 7, $Relatorio->GetY() + 5 );
                $Relatorio->SetWidths( array( 30 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( "Quantidade: ".$TotalLigacaoDiaMenos->quantidade ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 37, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( "Dia: ".$TotalLigacaoDiaMenos->dia ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 62, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Mês: ".$TotalLigacaoDiaMenos->mes ) ), 5, 1 );

                $Relatorio->SetFont( 'Arial', '', 7 );
                $Relatorio->SetXY( 87, $Relatorio->GetY() );
                $Relatorio->SetWidths( array( 25 ) );
                $Relatorio->SetAligns( array( "L" ) );
                $Relatorio->Linha( array( utf8_decode( "Ano: ".$TotalLigacaoDiaMenos->ano ) ), 5, 1 );
            }
        }
        $Relatorio->Output("arquivo","I");
    }
    public function comboRamal(){
        $DadosFiltradosDao = new DadosFiltradosDao();
        $result = $DadosFiltradosDao->findDataRamal();
        if($result){
            $limit = $DadosFiltradosDao->linesFind($result);
            $tagSelect = "<select name=\"ramal\" id=\"ramal\" style=\"border:1px solid black;\">";
            $tagSelect .= "<option value=\"\">--Escolha uma Opção--</option>";
            for($i = 0; $i < $limit; $i++){
                $tagSelect .= "<option value='".$DadosFiltradosDao->resultLines($result, $i, "ramal")."'>".$DadosFiltradosDao->resultLines($result, $i, "ramal")."</option>";
            }
            $tagSelect .= "</select>";
            return $tagSelect;
        }
        else{
            $tagSelect = "<select name=\"ramal\" id=\"ramal\" style=\"border:1px solid black;\">";
            $tagSelect .= "<option value=\"\">--Escolha uma Opção--</option>";
            $tagSelect .= "</select>";
            return $tagSelect;
        }
    }
}
?>
