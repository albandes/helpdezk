
$(document).ready(function () {

    countdown.start(timesession);
    console.log('acd: main.js');
    new gnMenu( document.getElementById( 'gn-menu' ) );


    /** Gráfico de Médias por Ano Letivo **/

    /** Dados para criação do gráfico **/
    /*var graphData = $.ajax({
        type: "POST",
        url: path+"/acd/home/ajaxMediasGraph",
        data: {cmbYear: $('#cmbYear').val()},
        async: false,
        dataType: 'json'
    }).responseJSON;*/

    /** configurações do gráfico**/
    var optionMedia = {
        responsive: true,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    };

    /** Criação gráfico **/
    //var ctx = document.getElementById("mediaChart").getContext("2d");
    //var mediaChart = new Chart(ctx,{type: 'bar',data: graphData,options: optionMedia});

    /** Atualiza gráfico ao mudar o ano letivo **/
    $("#cmbYear").change(function(){
        var graphData = $.ajax({
            type: "POST",
            url: path+"/acd/home/ajaxMediasGraph",
            data: {cmbYear: $('#cmbYear').val()},
            async: false,
            dataType: 'json'
        }).responseJSON;

        mediaChart.data = graphData;
        mediaChart.update();
    });

    /** Gráfico de Médias das Áreas **/

    /** Dados para criação do gráfico **/
    /*var areaData = $.ajax({
        type: "POST",
        url: path+"/acd/home/ajaxAreaGraph",
        async: false,
        dataType: 'json'
    }).responseJSON;*/

    /** configurações do gráfico**/
    var optionArea = {
        responsive: true,
        legend: {
            position: 'bottom',
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    };

    /** Criação gráfico **/
    //var ctx = document.getElementById("areaChart").getContext("2d");
    //var areaChart = new Chart(ctx,{type: 'bar',data: areaData,options: optionArea});



});

