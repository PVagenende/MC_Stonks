$( function() {
    $("#searchbar").autocomplete({
        minLength: 2,
        delay: 500,
        source: function (request, response) {
            $.ajax( {
                url: "helpers/data.php",
                data: {
                    key: 'bleh',
                    type: 'index',
                    p: request.term
                },
                type: "POST",
                dataType: "json",
                success: function( data ) {
                    $("#resultbox").html(""),
                    $.each( data, function( i, l ){
                        $("#resultbox").append("<div class='searchresult' id='"+i+"'>"+l+"</div>")
                    });
                }
            });
        }
    });


});
$("#resultbox").on('click', '.searchresult', function(){
    var redirect = 'details.php';
    $.redirectPost(redirect, {
        key: 'bleh',
        type: 'detail',
        p : event.target.id
    });
});

$("#item_details").on('click', '.usersearch', function(){
    var redirect = 'details.php';
    $.redirectPost(redirect, {
        key: 'bleh',
        type: 'detail',
        p : event.target.id
    });
});

$("#item_details").on('click', '.itemsearch', function(){
    var redirect = 'details.php';
    $.redirectPost(redirect, {
        key: 'bleh',
        type: 'detail',
        p : event.target.id
    });
});

$(".card").on('click', '.specials', function(){
    var redirect = 'details.php';
    $.redirectPost(redirect, {
        key: 'bleh',
        type: 'detail',
        p : event.target.id
    });
});
/*
$(function() {
    $.ajax( {
        url: "helpers/data.php",
        data: {
            key: 'bleh',
            type: 'chart',
            p: 'killed'
        },
        type: "POST",
        dataType: "json",
        success: function( data ) {
            var options = {
                animationEnabled: true,
                title:{
                    text: "Mobs killed"
                },
                data:[{
                    type: "stackedBar100",
                    toolTipContent: "{label}<br><b>{name}:</b> {y} (#percent%)",
                    showInLegend: true,
                    name: "April",
                    dataPoints: [
                        { y: 550, label: "Water Filter" },
                        { y: 450, label: "Modern Chair" },
                        { y: 70, label: "VOIP Phone" },
                        { y: 200, label: "Microwave" },
                        { y: 70, label: "Water Filter" },
                        { y: 324, label: "Expresso Machine" },
                        { y: 300, label: "Lobby Chair" }
                    ]
                }]
            }
            $("#chart_killed").CanvasJSChart(options);


        }
    });

});
*/


$.extend(
    {
        redirectPost: function(location, args)
        {
            var form = '';
            $.each( args, function( key, value ) {
                value = value.split('"').join('\"')
                form += '<input type="hidden" name="'+key+'" value="'+value+'">';
            });
            $('<form action="' + location + '" method="POST">' + form + '</form>').appendTo($(document.body)).submit();
        }
    });