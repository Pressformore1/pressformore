;
jQuery(document).ready(function(){
    
    
});

jQuery(window).load(function(){
    
    postManage();
    
});

jQuery(window).resize(function(){
    resizeContainer();
});

function postManage(){
    var $ = jQuery;    
    
    log($('#post_iframe').height());
    
    resizeContainer();
    
    //var actionBarScroll = $('#content-post').jScrollPane(); 
    //apiScrollBar = actionBarScroll.data('jsp');
     
    $('#action-bar').bind('resize',function(){
		resizeContainer();
    });        
};


function wantPressformed()
{
    $('#wantPressformTitle').hide(100);
    $('#wantPressformLink').hide(100);
    $('#wantPressformForm').hide().removeClass('hidden').show(100);
}
function wantPressformedInfo()
{
    $('#wantPressformForm').hide(100);
    $('#wantPressformThanks').hide().removeClass('hidden').show(100);
}