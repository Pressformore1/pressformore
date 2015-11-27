function connectAll() {
//    log('connect');
    // connect all the paths you want!
    connectElements($("#svg1"), $("#path1"), $("#col1"),   $("#menu-sidebar-btn"));
    connectElements($("#svg1"), $("#path2"), $("#col2"),   $("#search-icon"));
    connectElements($("#svg1"), $("#path3"), $("#col3"),   $("#add-post-icon"));
    connectElements($("#svg1"), $("#path4"), $("#col4"),   $("#user-menu-pic"));
    connectElements($("#svg1"), $("#path5"), $("#col5"),   $("#menu-notifications"));
    connectElements($("#svg1"), $("#path6"), $("#col6"),   $("#action-bar li:first"),{y:"middle"});
//    connectElements($("#svg1"), $("#path2"), $("#red"),    $("#orange"));
//    connectElements($("#svg1"), $("#path3"), $("#teal"),   $("#aqua")  );
//    connectElements($("#svg1"), $("#path4"), $("#red"),    $("#aqua")  ); 
//    connectElements($("#svg1"), $("#path5"), $("#purple"), $("#teal")  );
//    connectElements($("#svg1"), $("#path6"), $("#orange"), $("#green") );

}

function launchSVG()
{
    $("#svg1").attr("height", "0");
    $("#svg1").attr("width", "0");
    connectAll();
}

$(document).ready(function() {
    $('#help-me').click
    (
            function(){
                $('#tuto_page').fadeIn(300,launchSVG)
            });
    $('#tuto_page').click(function(){$('#tuto_page').fadeOut()})
    
    connectAll();
});

$(window).resize(function () {
    // reset svg each time 
    $("#svg1").attr("height", "0");
    $("#svg1").attr("width", "0");
    connectAll();
});