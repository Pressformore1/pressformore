
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
;

flagPopoverEvents = false;
jQuery(document).ready(function(){
        
    tooltipManage();
    modalConfirm();
    loading();
    homeManage();
        
    var $ = jQuery;
    
    //Plugin timeago
    
    $('time').timeago();
    
     $('.main-select2').select2({});
     
      $('#loading-zone,#loading-zone-content').trigger('hide-loading');
      
      $('.submitOnChange').change(function(){$(this).closest('form').submit();});

    $('.external-link').click(function(e){window.open(this.href,'_blank');e.preventDefault();});

});

jQuery(window).load(function(){
    var $ = jQuery;
    
    
    //Page wall, et author
    if($('#content-wall').length){
        $('#content-wall').bind('loaded', function(){
            $('#loading-zone,#loading-zone-content').trigger('hide-loading');
            $('#content-wall').unbind('loaded');
            //DÃ©clenche l'Ã©venement resize sur l'action bar, sans Ã§a le plugin jscroolpane ne prend pas la hauteur exact
            $('#action-bar').trigger('resize');
        });


    //Page normal
    }else{
       
        //DÃ©clenche l'Ã©venement resize sur l'action bar, sans Ã§a le plugin jscroolpane ne prend pas la hauteur exact
        $('#action-bar').trigger('resize');
    }

    alignAllThumb();
    
    setPopover();
    
});


function log(msg){
    try{
//        console.log(msg);
    }catch(e){

    }
}

function tooltipManage(){
    var $ = jQuery;    
    //Tootltip
    $('a[data-toggle="toolip"]').tooltip({
//        placement : 'bottom'
    });
    $('.tooltip-type-tiles, .tooltip-menu-icon, .create-wall-form-item category-item button').tooltip({
        placement : 'bottom'
    });

    $('.action-bar-link, .action-bar-tooltip').tooltip({
        placement : 'left',
        container: 'body'
    });
};
	
	
	
//Changement des icones couleur/gris, l'image est passï¿½ en paramï¿½tre, en de status on affiche la grise ou la colorï¿½
function iconColor(img, status){
    var $ = jQuery;
//        status 1 : selected
//        status 0 : unselected
    if($(img).length){
        if(status===1){
                $(img).attr('src',$(img).attr('data-selected'));
                
        }else if(status===0){
               $(img).attr('src',$(img).attr('data-unselected'));
        }
    }
};


function resizeContainer(){
    var $ = jQuery;

    if (!$('body').hasClass('mobile')) // To be removed when a true mobile template will be created
    {
        

    if($('#action-bar-sections-wrap').attr('data-status')=='open'){
        actionBarWidth = $('#action-bar-sections-wrap').width();
    }else{ 
        actionBarWidth = 0;
    }


    $('#content-wrap,#loading-zone-content').width($(window).width()-$('#action-bar-wrap').width()-actionBarWidth-4);
    $('#content-wrap,#loading-zone-content').height($('#action-bar-wrap').height());
    }

    return $('#content-wrap').width();

};


//Affichage de la loading zone
function loading(){
	
    
    $('#loading-zone,#loading-zone-content').bind('show-loading',function(e){
            $(this).fadeIn();	
    });

    $('#loading-zone,#loading-zone-content').bind('hide-loading',function(e){

            $(this).fadeOut();	
    });
}

function alignAllThumb(){
    $('.thumb-align').css('visibility','hidden').each(function(i,img){
        alignThumb($(this));
    });
}

function alignThumb(img){
    
    img.css('visibility','hidden');
    var hiddenParentDisplay ='';
     var hiddenParent = '';
    if (img.width() == 0 && img.height() == 0 && !img.is(':hidden'))
    {
        log('wait for image to Load');
        img.on('load',function(){log('loaded');lignThumb(img);});
        return;
    }
    else if (img.is(':hidden'))
    {
        
        hiddenParent = findHiddenParent(img);
        hiddenParentDisplay = hiddenParent.css('display');
       
        
    }
    
    
    
    if (hiddenParent.length)
    {
//        log('hide');
//        log(hiddenParent);
        
        hiddenParent.show().css('visibility','hidden');
    }
    
    
    ratioImg = img.width()/img.height();    
    
    
//    img.hide();
    
    wrap = img.parent();
//    img = $(img);
//    log('resize '+img.closest('article').attr('id')+' dimensions : width:'+img.width()+' height:'+img.height());
    ratioWrap = wrap.innerWidth()/wrap.innerHeight();    

    
    img.addClass('resize-test');
    //Image Ã©tnedu en largeur, on force la hauteur    
    if(ratioImg>=ratioWrap){
//        log('hauteur');
        //Hauteur de l'image Ã©gal au wrap
        img.height(wrap.height()).width('auto');

        //centre l'image en largeur
        marge = parseInt((img.width()-wrap.innerWidth())/2);
        img.css({'left': '-'+marge+'px','top':0});

    //Image Ã©tnedu en hauteur, on force la largeur
    }else if(ratioImg<ratioWrap){
//        log('largeur');
        //l'image prend la largeur du contenair
        img.width(wrap.width()).height('auto');

        //centre l'image en hauteur
        marge = parseInt((img.height()-wrap.innerWidth())/2);
        img.css({'top':'-'+marge+'px','left':0});

    }
    
    
    //on vÃ©rifie que l'image soit chargÃ© avant de la marquÃ© comme redimensionnÃ©
    if(!img.width() === 0 && !img.height()===0){
        wrap.attr('data-resize', 'resized');
    }
    
    if (hiddenParent.length)
    {
//        log('show');
//        log(hiddenParent);
        hiddenParent.css('visibility','visible');
//        if (hiddenParent != img)
//        {
            hiddenParent.css('display',hiddenParentDisplay);
//        }
    }
    
//    img.show();
    img.css('visibility','visible');
}

function findHiddenParent(element)
{
    if (element.parent().is(':hidden'))
    {
        return findHiddenParent(element.parent());
    }
    else
    {
        return element;
    }
}

/**
 * Instancie le plugin popover (bulle au survol cat/tag) Il est appelÃ© Ã  nouveau une fois des nouveaux Ã©lÃ©ments chargÃ©s en ajax
 */



function setPopover(){
    
    
    var options = {
        placement: "auto",
        html : true,
        trigger : 'manual',
        delay: 50000,
        container : 'body',
        content : function() {
            return $(this).next('.add-to-box-contenair').html();
        }
    };

    
    
    $('.add-to-box').each(function(i, addTobox){
        
        var popover = $(addTobox).popover(options);
        
//        $(addTobox).bind('mousenter',popoverListener)
//        log('each'+$(this).attr('id'));
        $(this)
            //survol du button addToBox on affiche la popover
            .on('mouseenter',function(){
                var currentPopover = this;
                $(currentPopover).popover('show');
//                log('popover');
                //la souris quitte le popover on la cache
                $('.popover').on('mouseleave',function(){
                    
//                      log(popover.options.content);
                    $(currentPopover).popover('hide');
                });
            })
            
            .on('mouseleave',function(e){
                var currentPopover = this;
                //On cache la popover seulement si la souris n'est pas dessus
                setTimeout(function () {
                    if (!$(".popover:hover").length) {
//                         log(popover.options.content);
                        $(currentPopover).popover("hide")
                    }
                }, 100);
//                log($(e.currentTarget));
//                if(!$(e.currentTarget).hasClass("popover") && !$(e.currentTarget).parents(".popover").length) {
//                    log('BLOE');
//                   $(currentPopover).popover('hide');
//                }
                })
            });
//             $(addTobox).on('hidden.bs.popover', function () {
////            log($(_this).next('.add-to-box-contenair').find('input.addToWallCheckbox ').prop('checked'));
//                log($('.popover-content').html());
//                $(this).next('.add-to-box-contenair').html($(this).find('.popover-content').html());
//            });
        if (flagPopoverEvents == false)
        {
            popoverEvents();
        }
        
        
        
        
       
//    })
//            .on("mouseleave", function () {
//        var _this = this;
//        setTimeout(function () {
//            if (!$(".popover:hover").length) {
//                $(_this).popover("destroy")
//            }
//        }, 100);
//    });


    var options = {
        placement: "bottom",
        html : true,
        trigger : 'manual',
        delay: 0,
        container : 'body',
        content : function() {
            return $(this).next('.hidden').html();
        }
        
        
    };
    $('#menu-social-links')
            .popover(options)
            .on('mouseenter',function(){
                var currentPopover = this;
                $(currentPopover).popover('show');
//                log('popover');
                //la souris quitte le popover on la cache
                $('.popover').on('mouseleave',function(){
                    
//                      log(popover.options.content);
                    $(currentPopover).popover('hide');
                });
            });
    
}


function popoverEvents()
{
//    log('event');
    flagPopoverEvents = true;
    $('.add-to-box').on('shown.bs.popover', function () {
        
//            log($(_this).next('.add-to-box-contenair').find('input.addToWallCheckbox ').prop('checked'));
//           log('shown :'+$(this).next('.add-to-box-contenair').html());
           $('.popover-content').html($(this).next('.add-to-box-contenair').html());
           $('.popover-content').find('input').each
           (
               function()
               {
                   $('.popover-content').find('label[for='+$(this).attr('id')+']').attr('for',$(this).attr('id')+'a');
                   $(this)
                           .attr('data-checkboxId',$(this).attr('id'))
                           .attr('id',$(this).attr('id')+'a')
                           .change
                           (
                               function()
                               {
//                                   log('change'+$(this).attr('data-checkboxId'));
                                   var regex = /(\S+[0-9])a?/gi;
                                   var match = regex.exec($(this).attr('data-checkboxId'));
                                   log('match '+match);
                                   if ($('#'+match).is(':checked'))
                                   {
//                                       log('match checked');
                                      $('#'+match).removeAttr('checked'); 
                                      $('#'+match).prop('checked',false);
                                   }
                                   else
                                   {
//                                       log('match not checkd');
                                       $('#'+match).attr('checked','checked');
                                       $('#'+match).prop('checked',true);
                                   }
                                   $('#'+match).change();
                               }
                           )
                   ;
//                        
               }
           );
//           ajaxUIRefresh();

//                $(this).next('.add-to-box-contenair').html($(this).find('.popover-content').html());
       });
    
}

function refreshAddToWall(params)
{
//    log('callBack');
    $('#'+params.formId).closest('.add-to-box-contenair').html(params.view);
    addRemoveToStreamRefresh();
}
/**
 * Gestion des tiles catÃ©gory dans la tutoriel de crÃ©ation de strew
 */
function createStrewManage() {
    var $ = jQuery;
    
    var $viewContainer;
    
    $('.create-wall-form-item').on('click',removeStrewElement);
    
    $('#changePictureWrapper')
        .hover(function(){
            $(this).find('.overlay').stop(true,true).fadeIn('200');
        },
        function()
        {
            $(this).find('.overlay').stop(true,true).fadeOut('200');
        })
        .off('click',changePictureClicked)
        .on('click',changePictureClicked);
    
    if($('.tile-category').length || $('.tile-tag').length){
        
        //Picked or ban categorie
        $('.tile').click(function(e){
            
            var itemId = $(this).parents('.masonry-block').attr('id');
            
            //Au click sur la croix on ajoute la tiles dans les banned
            //Le contenair peut etre tag ou categorie
            if($(e.target).parents('.tile-remove').length){
//                log('exclude cat');
                if($('.tile-category').length){
                    $formContenair = $('#p4m_corebundle_wall_excludedCategories');
                    $viewContainer = $('#banned-categories');
                }else if($('.tile-tag').length){
                    $formContenair = $('#p4m_corebundle_wall_excludedTags');
                    $viewContainer = $('#banned-tags');
                }
                
                
                
            //click à  un autre endroit ajoute la tiles dans picked
            //Le contenair peut etre tag ou categorie
            }else{
                if($('.tile-category').length){
//                    log('incude cat');
                    $formContenair = $('#p4m_corebundle_wall_includedCategories');
                    $viewContainer = $('#picked-categories');
                }else if($('.tile-tag').length){
                    $formContenair = $('#p4m_corebundle_wall_includedTags');
                    $viewContainer = $('#picked-tags');
                }
                
            }
            
            //checked la catÃ©gorie dans le form de l'action bar
            
            var proto = $formContenair.attr('data-prototype');
            proto = proto.replace(/%_elementId_%/g,itemId.replace( /^\D+/g, ''));
            proto = proto.replace(/%_elementName_%/g,$(this).find('h2:first').text());
//            log($(this).find('h2').length);
           if (proto.match(/%_elementIcon_%/) != null)
           {
               proto = proto.replace(/%_elementIcon_%/g,$(this).find('.category-tile-icon:first').attr('src'));
           }
            
            $formContenair.append(proto);
        
            var proto = $viewContainer.attr('data-prototype');
            proto = proto.replace(/%_elementId_%/g,itemId.replace( /^\D+/g, ''));
            proto = proto.replace(/%_elementName_%/g,$(this).find('h2:first').text());
//            log($(this).find('h2').length);
            if (proto.match(/%_elementIcon_%/) != null)
            {
                proto = proto.replace(/%_elementIcon_%/g,$(this).find('.category-tile-icon:first').attr('src'));
            }
            
            $viewContainer.append(proto);
            
            
            $viewContainer.find('[data-tileId="'+itemId+'"]').removeClass('hidden').fadeIn();
            
            $('.create-wall-form-item').off('click',removeStrewElement);
            $('.create-wall-form-item').on('click',removeStrewElement);
            
            //hide la tiles
            $(this).parents('.masonry-block').hide();
            $container.masonry();
        });
        
            
    }
}
function changePictureClicked(e)
{
//    log('form append');
    $('#changePictureWrapper').off('click',changePictureClicked);
    $(this).parent('#changePicture').append($(this).parent('#changePicture').attr('data-prototype'));
    $("#changePictureCancel").click(function(){
        $(this).closest('#changePicture').find('input, button,br').remove();    
        $('#changePictureWrapper').on('click',changePictureClicked);
    });
            
}

function removeStrewElement(e)
{
    
            //show la tiles
            $('#'+$(this).attr('data-tileId')).show();
            $container.masonry();
    $('.non-form-item[data-tileId='+$(this).attr('data-tileId')+']').fadeOut(function(){$(this).remove()});
    $(this).fadeOut(function(){$(this).remove()});    
            
    }

//Gestion de la home et des ancres Ã  l'intÃ©rieur de l'iframe
function homeManage() {
    $('.link-iframe').click(function(){
        if($('iframe').attr('src').indexOf('#')!=-1){
//            log('why');
            url = $('iframe').attr('src').substr(0,$('iframe').attr('src').indexOf('#'));
        }else{
            url = $('iframe').attr('src');
        }
//        log(url);
        $('iframe').attr('src', url+$(this).attr('href'));
        
    });
}

//UtilisÃ© par le plugiin select2 (select dans la barre de recherhce)
function format(opt) {
    return "<span class='"+$(opt.element).attr('data-icon')+"'></span>"+opt.text;
}

//+ Jonas Raoni Soares Silva
//@ http://jsfromhell.com/array/shuffle [v1.0]
function shuffle(o){ //v1.0
    for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
    return o;
};

//Affichage des images de catgégories, dans le plugin add post
function categoryListFormat(category) {
    return "<img class='addPost-category-thumbnail' src='" + $(category.element).attr('data-img') +"'/>" + category.text;
}

//limite le nombre de caractère de l'objet enoyé
function limitChar(obj, nbrChar){
//    log('limit-char '+nbrChar);
//    log('limit-char '+$(obj).html().length);
    if($(obj).length && $(obj).html().length){
        if($(obj).html().length > nbrChar){
            $(obj).html($(obj).html().substr(0,nbrChar-3)+'...');
        }
    }
}

var myArray = ['#B186DE','#B3DE86','#DEB386','#DE86B1'];
function imgError(img){
//    log($(img));
    newArray = shuffle(myArray);
    $(img).parent().css('background',newArray[0]);
}

function show_error(error,modalButtons)
{
    $('#modal-error .modal-body').html(error);
    if (modalButtons != undefined )
    {
        log('modalButtons');
       $('#modal-error .modal-footer').append(modalButtons);
       
    }
    
    
    $('#modal-error').modal('show');
}



function modalConfirm()
{
    $('.confirm_ajax_action').each(function(i,btn){
        $(btn).click(function(){
            $('#modal-confirm .modal-body').html($(btn).attr('data-confirm-text'));
            $('#modal-confirm .confirm-btn').addClass('ajax_action');
            $('#modal-confirm .confirm-btn').attr('data-url', $(btn).attr('data-url'));
            $('#modal-confirm .confirm-btn').attr('data-loader', 'full');
            $('#modal-confirm .confirm-btn').attr('data-params', $(btn).attr('data-params'));
            $('#modal-confirm .confirm-btn').attr('data-key', $(btn).attr('data-key'));
            $('#modal-confirm .confirm-btn').attr('data-action', $(btn).attr('data-action'));
            $('#modal-confirm').modal('show');
            ajaxUIRefresh();
        });
        
    });
}