
;
/**
 * 
 * setCommentsListener doit bouger d'ici idéalement
 */

var descriptionPostCaracterLimit = 300;
jQuery(document).ready(
        function()
        {
            ajaxLoader();
            setCommentsListener();
            
        }        
);


function ajaxLoader()
{
    ajaxEventListener();
    ajaxUIListeners();
    
};

function ajaxUIListeners()
{
    var $ = jQuery;
    $('.ajax_action').bind('click',ajaxHandler);
    $('.ajax_form').bind('submit',ajaxHandler);
    $('.ajax_form_force').bind('click',ajaxForceForm);
       
};

function ajaxUIRefresh()
{
    var $ = jQuery;
    $('.ajax_action').unbind('click',ajaxHandler);
    $('.ajax_form').unbind('submit',ajaxHandler);
    $('.ajax_form_force').unbind('click',ajaxForceForm);
    ajaxUIListeners();
     $('.popoverLink').click(
        function()
        {
            $('.unfollow').popover('hide');
        });
};


function ajaxForceForm(e)
{
    console.log($(this).attr('data-ajaxTarget'));
//     e.preventDefault();
     if ($(this).hasClass('ajax_form_force'))
     {
         if ($(this).prop('data-customFormFields') != undefined)
         {
//             console.log('customFormFields');
             var customFields = JSON.parse($(this).attr('data-customFormFields'));
             for (var key in customFields) 
             {
//                 console.log(key)
                  $('#'+$(this).attr('data-ajaxTarget')).append('<input type="hidden" name="'+key+'" value="'+customFields[key]+'" />');
             }
             
         }
        
        $('#'+$(this).attr('data-ajaxTarget')).submit();
     }
     
}
function ajaxHandler(e)
{
    console.log('ajax call');
    var $ = jQuery;
    
    var targetLink = $(this);
    
    if (targetLink.hasClass('current-content-modifier') && targetLink.hasClass('content-modifier'))
    {
        return;
    }
    else if (!targetLink.hasClass('current-content-modifier') && targetLink.hasClass('content-modifier'))
    {
        $('.current-content-modifier').removeClass('current-content-modifier');
        targetLink.addClass('current-content-modifier');
    }
   
    
    
    
    
    switch (targetLink.attr('data-loader'))
    {
        case 'content': 
            $('#loading-zone-content').trigger('show-loading');
            break;
        case 'full':
            $('#loading-zone').trigger('show-loading');
            break;
        case 'none':
            break;
        default:
            $('#loading-zone').trigger('show-loading');
            break;
    }
//    //Attribut data-loader sur le form posté
//    if(targetLink.attr('data-loader')=='off'){
//        //Do nothing
//    }else if($(e.target).attr('data-loader')=='content'){
//        //TO DO, loader uniquement sur la div content
//    
//    //ALL
//    }else{
//        //Active le loader sur toutes la window
//        $('#loading-zone').trigger('show-loading');
//    }
//    log('tagName : '+e.target.tagName);
    if (e.target.tagName == 'FORM' || e.target.tagName != 'A')
    {
        e.preventDefault();
    }
//    else
//    {
//        log('tagName : '+e.target.tagName);
//        e.preventDefault();
//    }
//    
    var params = targetLink.attr('data-params');
    if (typeof params == 'string') params = JSON.parse(params);
    
    if (targetLink.attr('data-target') && targetLink.attr('data-target')[0]!= '#')
    {   
        var params ;
        if (targetLink.attr('data-target') == 'serialize')
        {
//            console.log(e);
            params =targetLink.MytoJson();
            
        }
        else
        {
            params = {};
            for (var i = 0;i< targetLink.data('target').length ;i++)
            {
//                //console.log(targetLink.data('target')[i]);
                var target = targetLink.data('target')[i];
                params[target] = $('#'+target).val();
            }
        }
        
//        $targetArray = JSON.parse()
        
    }
    //console.log('Calling url : '+targetLink.data('url'));
    var ajaxUrl = targetLink.attr('data-url')
    $.ajax
    ({
        url : ajaxUrl,
        type : "post",
//        data : {'params':JSON.stringify(params),'key':targetLink.data('key')},
        data : {'params':JSON.stringify(params),'key':targetLink.attr('data-key'),'action':targetLink.attr('data-action')},
        success : function(response)
        {
//            console.log('response');
            $(document).trigger('ajax_loaded',[response]);
        },
        error : function(e)
        {
            $('#loading-zone,#loading-zone-content').trigger('hide-loading');
            show_error('A problem occured, we\'ll work on it. Sorry for the inconvenience');
//            console.log('send error to '+ajaxUrl)
            params.error = e;
            $.ajax
            ({
               
                url : ajaxUrl,
                type : "post",
                data : {'params':JSON.stringify(params),'action':'reportAjaxError',key:'no-key'},
                success : function(response)
                {
                    $(document).trigger('ajax_loaded',[response]);
                },
            })
        }
        
    });
};
 
 
function ajaxEventListener()
{
    var $ = jQuery;
    $(document).on('ajax_loaded',function(event,json_params)
    {
        
        $('#loading-zone,#loading-zone-content').trigger('hide-loading');
        
        var params = JSON.parse(json_params);
        
        if (params.status ==0 )
        {
            if (params.modalButtons != undefined)
            {
                show_error(params.error,params.modalButtons);
            }
            else
            {
            show_error(params.error);
        }
        
        }
        
        
        
        
        
        
        
        switch (params.action)
        {
            case 'follow':
                if (params.status)
                {
                    $('.user-follow').addClass('hidden');
                    $('.user-unfollow').removeClass('hidden');
                    $('#nombre_followers').html(parseInt($('#nombre_followers').html())+1);
//                    $('.followed_info').addClass('following');
//                    //console.log('updated');
                }
            break;
            case 'unfollow':
                if (params.status)
                {
                    $('.user-unfollow').addClass('hidden');
                    $('.user-follow').removeClass('hidden');
//                    //console.log(parseInt($('#nombre_followers').html())+1);
                    $('#nombre_followers').html(parseInt($('#nombre_followers').html())-1);
//                    $('.followed_info').removeClass('following');
                }
            break;
            case 'wallComment':
                if (params.status)
                {
                    if (params.parent)
                    {
                        $('#li-comment-'+params.parent).find('ol').append(params.data);
                    }
                    else
                    {
                        $('.actcion-bar-comments').append(params.data);
                    }
                    
                    $('#modal-addComment').modal('hide');
                    resetCommentsListener();
                }
            break;
            case 'postComment':
                if (params.status)
                {
                    if (params.parent)
                    {
                        $('#li-comment-'+params.parent).find('ol').append(params.data);
                    }
                    else
                    {
                        $('.actcion-bar-comments').append(params.data);
                    }
                    
                    $('#modal-addComment').modal('hide');
                    resetCommentsListener();
                }
            break;
            case 'vote':
                if (params.status)
                {
                    log(params);
                    $('#rating_less').attr('data-vote-number', params.data.negativeVotesNumber).removeClass('rating-selected');
                
                    $('#rating_more').attr('data-vote-number', params.data.positiveVotesNumber).removeClass('rating-selected');

                    

                    if (params.data.scoreVoted>0)
                    {
                        $('#rating_more').addClass('rating-selected');
                    }
                    else
                    {
                        $('#rating_less').addClass('rating-selected');
                    }
                    
                    $('#total-votes').html(parseInt(params.data.positiveVotesNumber)+parseInt(params.data.negativeVotesNumber));
                    
                    ratioPostRating();
                }
            break;
            case 'wallVote':
                if (params.status)
                {
//                    log(params);
//                    $('.ratio-plus-number').text('a');
                    $('.ratio-plus-number').text(params.data.positiveVotesNumber);
                
                    $('.ratio-less-number').text(params.data.negativeVotesNumber);

//                    if (params.data.scoreVoted>0)
//                    {
//                        $('#rating_more').removeClass('rating-unselected');
//                        $('#rating_less').addClass('rating-unselected');
//                    }
//                    else
//                    {
//                        $('#rating_less').removeClass('rating-unselected');
//                        $('#rating_more').addClass('rating-unselected');
//                    }
//                    
//                    $('#total-votes').html(parseInt(params.data.positiveVotesNumber)+parseInt(params.data.negativeVotesNumber));
//                    resizePostRating();
                }
            break;
            case 'reportPost':
                if (params.status)
                {
                    $('#section-report form').remove();
                    $('#section-report p:first').html(params.data.message);
                }
            case 'flaggedPost':
                if (params.status)
                {
                    $('#section-report form').remove();
                    $('#section-report p:first').html(params.data.message);
                }
            break;
            case 'commentVote':
                if (params.status)
                {
                    var positivePercent =0;
                    var negativePercent =0;
                    var total = parseInt(params.data.positiveVotesNumber) + parseInt(params.data.negativeVotesNumber) ;
                    
                    if (params.data.positiveVotesNumber > 0 && total>0)
                    {
                        positivePercent = parseInt(100/(total/params.data.positiveVotesNumber));
                        negativePercent = 100 - positivePercent;
                    }
                    else if (total>0)
                    {
                        negativePercent= parseInt(100/(total/params.data.negativeVotesNumber));
                        positivePercent  = 100 - negativePercent;
                    }
                    
                    log('length : ' + $('#li-comment-'+params.data.commentId).find('.rating-horizontal-bar-plus').length);
                    $('#li-comment-'+params.data.commentId).find('.rating-horizontal-bar-plus')
                            .attr('width',positivePercent+'%')
                            .css('width',positivePercent+'%');
                    $('#li-comment-'+params.data.commentId).find('.rating-horizontal-bar-less')
                            .attr('width',negativePercent+'%')
                            .css('width',negativePercent+'%');
                    $('#li-comment-'+params.data.commentId).find('.comment-rating-total').html(total);
                    
                    
                    $('#li-comment-'+params.data.commentId).find('.comment-vote').removeClass('selected');
                    if (params.data.score>0)
                    {
                        $('#li-comment-'+params.data.commentId).find('.comment-vote-up').addClass('selected');
                    }
                    else
                    {
                        $('#li-comment-'+params.data.commentId).find('.comment-vote-down').addClass('selected');
                    }
                    
                    
                    
                    
                }
            break;
        case 'addPost': // Quand quelqu'un vient de poster une url
            if (params.status === 1)
            {
                $('#addPostZone').html(params.data);
                 $('#modal-addPost').on('shown.bs.modal', function () {
                      $('#p4m_corebundle_post_pictureList').parent('div').hide();
                    addPostThumb();
                  });
                $('#modal-addPost').modal('show');
                $("#p4m_corebundle_post_categories").select2({
                    formatResult: categoryListFormat,
                    formatSelection: categoryListFormat,
                    escapeMarkup: function(m) { return m; }
                });
                
                $('#p4m_corebundle_post_tags').tagsinput({
                        //possibilitÃ© d'ajoutÃ© un json :
                                //typeahead: {
                                //       source: function(query) {
                                //	       return $.get('http://someservice.com');
                                //	}
                                //}

//                        typeahead: {
//                                source: ['Amsterdam', 'Washington', 'Sydney', 'Beijing', 'Cairo']
//                        }
                });
                $('#p4m_corebundle_post_tags').css({top: '20px',left:'30px',position: 'absolute',visibility: 'hidden',display:'block'});
               
                
                
                
                var content = $("#p4m_corebundle_post_content");
                content.limiter(descriptionPostCaracterLimit, $('#p4m_corebundle_post_content_chars_limit'));
                //Supprime les derniers caractères du textarea
                if(content.html().length>descriptionPostCaracterLimit){
                    content.html($("#p4m_corebundle_post_content").html().substr(0,300) +'[...]');
                }
                
                $('#addPostUrl').val();
            }
            
        break;
        case 'addPostForm':
            if (params.status == 0)
            {
                $('#addPostZone').html(params.data);
                $("#p4m_corebundle_post_categories").select2({
                    formatResult: categoryListFormat,
                    formatSelection: categoryListFormat,
                    escapeMarkup: function(m) { return m; }
                });
                    
                $('#p4m_corebundle_post_tags').tagsinput({
                    //possibilitÃ© d'ajoutÃ© un json :
                            //typeahead: {
                            //       source: function(query) {
                            //	       return $.get('http://someservice.com');
                            //	}
                            //}

//                        typeahead: {
//                                source: ['Amsterdam', 'Washington', 'Sydney', 'Beijing', 'Cairo']
//                        }
                }).css({top: '20px',left:'30px',position: 'absolute',visibility: 'hidden',display:'block'});
                    
                addPostThumb();
                
                var content = $("#p4m_corebundle_post_content");
                content.limiter(descriptionPostCaracterLimit, $('#p4m_corebundle_post_content_chars_limit'));
                //Supprime les derniers caractères du textarea
                if(content.html().length>descriptionPostCaracterLimit){
                    content.html($("#p4m_corebundle_post_content").html().substr(0,300) +'[...]');
                }
            }
            else
            {
                if (params.data.modal)
                {
                    $('#modal-addPost').before(params.data.modal);
                     $('#modal-addPostConfirm').modal('show');
                     
                }
                
                $('#modal-addPost').modal('hide');
               
                
                
//                window.location.href=params.data;
            }
        break;
        case 'postEdited':
            $('#modal-editPost').modal('hide');
            $('#modal-addPost').before(params.data.modal);
            $('#modal-editPostConfirm').modal('show');
            $('#section-post-meta').html(params.data.infos);
            setPopover();
//            window.location.href=params.data;
        break;
        case 'deletePost':
                $('#modal-addPost').before(params.data.modal);
                $('#modal-addPostConfirm').modal('hide');
                $('#modal-deletePost').modal('show');
                
                
        break;
        case 'editPost':
            if (params.status === 1)
            {
                
                $('#editPostZone').html(params.data);
                $('#modal-editPost').on('shown.bs.modal', function () {
                console.log('edit Post Callback');
                    addPostThumb();
                  });
                $('#modal-editPost').modal('show');
                $("#p4m_corebundle_post_categories").select2({
                        formatResult: categoryListFormat,
                        formatSelection: categoryListFormat,
                        escapeMarkup: function(m) { return m; }
                });
    
                $('#p4m_corebundle_post_tags').tagsinput({
                        //possibilitÃ© d'ajoutÃ© un json :
                                //typeahead: {
                                //       source: function(query) {
                                //	       return $.get('http://someservice.com');
                                //	}
                                //}
    
    //                        typeahead: {
    //                                source: ['Amsterdam', 'Washington', 'Sydney', 'Beijing', 'Cairo']
    //                        }
                }).css({top: '20px',left:'30px',position: 'absolute',visibility: 'hidden',display:'block'});
                
            }
        break;
        case 'banPost':
            if (params.status === 1)
            {
//                $('#post_'+params.data.postId).hide(100);
                $container.masonry('remove', $('#post_'+params.data.postId));
                $container.masonry();

            }
        break;
        case 'readPostLater':
            if (params.status === 1)
            {
                $('#post_'+params.data.postId).find('.read-later-wrap').hide();
            }
        break;
        case 'setWallPost':
            if (params.status === 1)
            {
                $container.masonry('destroy');
                //posts -> 
                $container.html(params.data.posts);
                $('.pagination').html(params.data.pagination);
                resizeContentWall();
                setWall();
            }
        break;
        case 'followWall':
            if (params.status === 1)
            {
                $('#user-sidebar').replaceWith(params.data.shortcutBar);
            }
        break;
//        //console.log('le truc de dingue');
//        
//        case 'unfollowWall':
////            //console.log('le truc de dingue');
//            if (params.status === 1)
//            {
//                
//                $('.user-unfollow, .user-follow').toggleClass('hidden');
////                //console.log('tet'+$('nav#user-sidebar').length);
////                $('#user-sidebar').replaceWith('');
//                $('#user-sidebar').replaceWith(params.data.shortcutBar);
//            }
//        break;
        case 'unfollowWall':
//            //console.log('le truc de dingue');
            if (params.status === 1)
            {
                
                $('.user-unfollow, .user-follow').toggleClass('hidden');
//                //console.log('tet'+$('nav#user-sidebar').length);
//                $('#user-sidebar').replaceWith('');
                $('#user-sidebar').replaceWith(params.data.shortcutBar);
            }
        break;
        case 'loadWallMembers':
           if (params.status === 1)
            {
                                
            }
        break;
        case 'updateActionBar':
           if (params.status === 1)
            {
                log('updateActionBar');
                $('#action-bar-sections').html(params.data);                
            }
        break;
        case 'homeRefresh':
           if (params.status === 1)
            {
                //Iframe ajout de class pr ajouter overflow hidden sur body
                if(!$('body').hasClass('post-page')){
                    $('body').addClass('post-page');
                }
                
                $('#content-wrap').html(params.data.content);         
                fixedTopBar();
                resizeContainer();
            }
        break;
        case 'homeRefreshPosts':
           if (params.status === 1)
            {
                try
                {
                    $container.masonry('destroy');
                }
                catch(e)
                {
                    
                }
                
                //si wall on supprime la class qui met un overflow hidden sur le body
                if($('body').hasClass('post-page')){
                    $('body').removeClass('post-page');
                }
                
                    
                //posts -> 
                $('#content-wrap').html(params.data.content);
                $('.pagination').html(params.data.pagination);
              
                resizeContentWall();
                setWall();      
            }
        break;
        
        case 'deleteWall':
            if (params.status === 1)
            {
                $container.masonry('remove', $('#wall_'+params.data.wallId));
                $container.masonry();
            }
        break;
        
        case 'createInvitation':
            if (params.status === 1)
            {
                $('#modal-join-us').modal('hide');
                $('#modal-confirm .btn.btn-default.confirm-btn').hide();
                $('#modal-confirm .btn.btn-default').text('OK');
                $('#modal-confirm .modal-body').html(params.data.message);
                $('#modal-confirm').modal('show');
            }
        break;
        
    }
//    log(params);
    
    
    if (params.callBack)
    {

        if (typeof window[params.callBack] == 'function')
        {
            if (params.callBackParams)
            {
                window[params.callBack](params.callBackParams);
            }
            else
            {
                window[params.callBack]();
            }
            
        }
        else
        {
            $(window).trigger(params.callBack);
        }

    }
    
    actionBarBindButtonsRefresh();
    ajaxUIRefresh();
        
    });
    
    
    
};



/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function setCommentsListener()
{
    $('.comment-reply-link').bind('click',commentReply);
    $('.leave-comment').bind('click',comment);
}
function resetCommentsListener()
{
    $('.comment-reply-link').unbind('click',commentReply);
    $('.leave-comment').unbind('click',comment);
    
    setCommentsListener();
}

function commentReply()
{
    $('#p4m_corebundle_comment_content').val('');
    $('#parentId').val($(this).data('id'));
    console.log('parent id : '+$(this).data('id'));
}

function comment()
{
     $('#p4m_corebundle_comment_content').val('');
    $('#parentId').val('');
}


//Affiche un thumb des images récupérer sur l'url distante, des fleches sont affcihés pour que le user puisse sélectionné le thumb désiré
function addPostThumb(){
    var $ = jQuery;
    imgNumber = 0;

    if ($('#p4m_corebundle_post_picture').length)
    {
        $('#p4m_corebundle_post_picture').before( '<div id="thumbnail-slider-wrap" style="display: none;"><ul id="thumbnail-slider"></ul><span id="thumbnail-slider-prevImg"><span class="glyphicon left_arrow"></span></span><span id="thumbnail-slider-number"><span class="current">0</span>/<span class="total">0</span></span><span id="thumbnail-slider-nextImg"><span class="glyphicon right_arrow"></span></span></div>' );

        if($('#thumbnail-slider-wrap').length){
            //Affichage du slider
            $('#thumbnail-slider-wrap').show();

            //crÃ©e les images dans le slider
            var selectedClass;
            var selectedAttr;
            var totalImg=0;
            $('#p4m_corebundle_post_picture option').each(function(i,img){
                selectedClass='';
                totalImg++;
                if(i==0)
                {
                    $(img).attr('selected', true);
                    selectedClass = "thumbnail-slider-selected"
                }
                $('#thumbnail-slider').append('<li class="'+selectedClass+'"><img class="thumb-align" src="'+$(img).html()+'" alt="" /></li>');	
            });
            $('#thumbnail-slider li').not('.thumbnail-slider-selected').hide();
            $('#p4m_corebundle_post_picture').css({bottom: 0,position: 'absolute',visibility: 'hidden'});

            $('#thumbnail-slider-number .total').html(totalImg);
            $('#thumbnail-slider-number .current').html('1');
            
            //le premier Ã©lÃ©ment est toujours prÃ©selectionÃ©
          
            
            log($('.thumbnail-slider-selected img'));
            //Instancie le slider (fleches permettant de naviguer entre les images)
            $('#thumbnail-slider-prevImg, #thumbnail-slider-nextImg').click(function(){

                //Image prÃ©cÃ©cdente
                if($(this).attr('id')==="thumbnail-slider-prevImg"){
                    imgNumber--;
                }
                if($(this).attr('id')==="thumbnail-slider-nextImg"){
                    imgNumber++;
                }
                if(imgNumber>$('#thumbnail-slider li').length-1)imgNumber=0;
                else if(imgNumber<0)imgNumber=$('#thumbnail-slider li').length-1;
                $('#thumbnail-slider-number .current').html(parseInt(imgNumber+1));
                $('#thumbnail-slider li.thumbnail-slider-selected').removeClass('thumbnail-slider-selected')
                $('#thumbnail-slider li:eq('+imgNumber+')').addClass('thumbnail-slider-selected');
                $('#p4m_corebundle_post_picture option:selected').attr('selected', false);
                $('#p4m_corebundle_post_picture option:eq('+imgNumber+')').attr('selected', true);
                $('#thumbnail-slider li').not('.thumbnail-slider-selected').hide();
                $('.thumbnail-slider-selected').show();
                alignThumb($('.thumbnail-slider-selected img'));
            });
            
            alignThumb($('.thumbnail-slider-selected img'));
        }
    }

}


jQuery.fn.MytoJson = function(options) {

    options = jQuery.extend({}, options);

    var self = this,
        json = {},
        push_counters = {},
        patterns = {
            "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
            "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
            "push":     /^$/,
            "fixed":    /^\d+$/,
            "named":    /^[a-zA-Z0-9_]+$/
        };


    this.build = function(base, key, value){
        base[key] = value;
        return base;
    };

    this.push_counter = function(key){
        if(push_counters[key] === undefined){
            push_counters[key] = 0;
        }
        return push_counters[key]++;
    };
    
    var inputs = jQuery(this).serializeArray();
//    console.log(jQuery(this).tagName);
//    console.log(jQuery(this).serializeArray());
//    console.log(jQuery(this).find('input[type=checkbox]').length);
    jQuery(this).find('input[type=checkbox]').each(
                    function() {
                        if ($(this).attr('checked') == 'checked')
                        {
                            var checkbox = $(this);
                            var present = false;
                            $(inputs).each
                            (
                                function()
                                {
                                    if (this.name == checkbox.attr('name') && this.value == checkbox.val())
                                    {
                                        present = true;
                                        return;
                                    }
                                });
                            if (!present)
                            {
                                inputs.push({'name':$(this).attr('name'),'value':$(this).val()});
                            }
                            
//                            console.log('checked');
                        }
                        
                    });
                    
    console.log(inputs);
    jQuery.each(inputs, function(){

        // skip invalid keys
        // Mis en commentaire pour le getWallPost
//        if(!patterns.validate.test(this.name)){
//            console.log(this.name);
//            return;
//        }

        var k,
            keys = this.name.match(patterns.key),
            merge = this.value,
            reverse_key = this.name;

        while((k = keys.pop()) !== undefined){

            // adjust reverse_key
            reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

            // push
            if(k.match(patterns.push)){
                merge = self.build([], self.push_counter(reverse_key), merge);
            }

            // fixed
            else if(k.match(patterns.fixed)){
                merge = self.build([], k, merge);
            }

            // named
            else if(k.match(patterns.named)){
                merge = self.build({}, k, merge);
            }
        }
//        console.log(json);

        json = jQuery.extend(true, json, merge);
    });


    return json;
}

jQuery.fn.serializeObject = function()
{
   var o = {};
   var a = this.serializeArray();
   jQuery.each(a, function() {
       if (o[this.name]) {
           if (!o[this.name].push) {
               o[this.name] = [o[this.name]];
           }
           o[this.name].push(this.value || '');
       } else {
           o[this.name] = this.value || '';
       }
   });
   return o;
};