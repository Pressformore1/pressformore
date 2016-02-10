;

flagPopoverEvents = false;
jQuery(document).ready(function(){
        
    tooltipManage();
    modalConfirm();
    loading();
    homeManage();

    var $ = jQuery;

    $.cookieBar({
        fixed: true,
        bottom: true,
        zindex: 10,
        message: messageCookie,
        acceptText: acceptTextCookie
    });

    
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