/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//funcao combobox de auto complete
//@param pagina é a pagina php
//@param tipo é o tipo de entrada, em caso de um suitch no php para filtrar
//@param id é o identificaçao do combo pai, ex: this.value
function selectBox(pagina, tipo, id, container)
{
    //alert("tipo=" + tipo + "&id=" + id);
    $.ajax(
    {
        type: "POST",
        url: pagina,
        data: "acao=" + tipo + "&id=" + id,

        beforeSend: function()
        {
            // enquanto a função esta sendo processada, você
            // pode exibir na tela uma
            // msg de carregando
        },
        success: function(txt)
        {
            // pego o id da div que envolve o select com
            $(container).html(txt);
        },
        error: function(txt)
        {
            alert('Erro ajax: ' + txt);
        }
    });
}
//funcao combobox de auto complete
//@param pagina é a pagina php
//@param tipo é o tipo de entrada, em caso de um suitch no php para filtrar
//@param id é o identificaçao do combo pai, ex: this.value
function selectBox2(pagina, tipo, id, port_tipo,container)
{
    //alert("tipo=" + tipo + "&id_marca=" + id);
    $.ajax(
    {
        type: "POST",
        url: pagina,
        data: "acao=" + tipo + "&id_marca=" + id + "&port_tipo=" + port_tipo,

        beforeSend: function()
        {
            // enquanto a função esta sendo processada, você
            // pode exibir na tela uma
            // msg de carregando
        },
        success: function(txt)
        {
            // pego o id da div que envolve o select com
            $(container).html(txt);
        },
        error: function(txt)
        {
            alert('Erro ajax: ' + txt);
        }
    });
}


