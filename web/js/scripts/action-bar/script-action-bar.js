
;
jQuery(document).ready(function(){
    actionBarManage();
   
});


jQuery(window).resize(function(){
    actionBarResize();
});


/*****************************************************************************************************************/
//Menu de droite affichï¿½ sur la page post
function actionBarManage(){
    var $ = jQuery;

    commentManage();
    postRating();
    actionBarResize();
    filterManage();
    addRemoveToStream();
    
    pressManage();



    //FOLLOW BUTTON
    //On va faire autrement : Je met follow action comme callback dans data dans la balise , et Ã§a s'apellera automatiquement quand ajax ok
    $(window).bind('follow-action',function(){
        $('#action-bar-follow a').toggleClass('hidden');
    });
    $(window).bind('backToPostsListener',function(){
        $('.content-modifier').not('.ajax_form_force').addClass('ajax_form_force');
        $('.content-modifier.current-action-bar-item').removeClass('ajax_form_force');
    });
    //Je te laisse faire, j'ai pas le tps, je suis dÃ©solÃ©
    // $('#action-bar-follow .to-follow ').click(function(){
    // $(this).hide();
    // $('#action-bar-follow .followed').show().css('display','block'); //oui je sais
    // });


        
        
    //force l'ouverture de l'action bar si un item de l'action est marqu� comme current. page author notament
    if (!$('body').hasClass('mobile'))
    {
        
    
        if($('.action-bar-autostart').length ){
            var delay = 0;
            if ($('.action-bar-autostart').attr('data-delay'))
            {
                delay = $('.action-bar-autostart').attr('data-delay')*1000;
            }
            
            setTimeout(function(){openactionBar($('.action-bar-autostart'));},delay);
        }
        else if ($('.current-action-bar-item').length)
        {
            openactionBar($('.current-action-bar-item'));
        }
        
    }
    else
    {
        $('#actionbar-doorknob').css('right',(($(window).width()-$('#actionbar-doorknob').width())/2)+'px');
    }
        
//        $('.action-bar-link')
        
	      
   actionBarBindButtons();    
    hashManage();
    topMenu();
    log('manage');
    
    $('.teleclick')
        .click(function(e){
           $('#'+$(this).data('target')).click(); 
            e.preventDefault();
        });

};

function actionBarBindButtonsRefresh()
{
    log('refresh');
    //click sur un bouton présent dans l'action bar, on simule un click dans l'onglet de l'action bar
    $('.action-bar-btn[data-toggle="action-bar"]').off('click');
    $('#actionbar-doorknob .icon-logo').off('click',openActionBarMobileAnimation);

    $('.action-bar-link[data-toggle="action-bar"]').off('click');
    $('#action-bar-remove').off('click',closeactionBar);
    actionBarBindButtons();
}

function actionBarBindButtons()
{
    

    //click sur un bouton présent dans l'action bar, on simule un click dans l'onglet de l'action bar
    $('.action-bar-btn[data-toggle="action-bar"]').bind('click',function(){
        $('.action-bar-link[data-target="'+$(this).attr('data-target')+'"]').click();
    });


    $('.action-bar-link[data-toggle="action-bar"]').bind('click',function(){
       
        log($(this).attr('data-target')+'scrollbar'+$('.action-bar-section.ps-container').length);
        //post menu section n'est pas ouvert
        //menu ouvert et click sur l'item d�j� selected
//        if (!$(this).hasClass('current-action-bar-item'))
//        {

            $('.action-bar-section.ps-container').perfectScrollbar('destroy');
            $($(this).attr('data-target')).perfectScrollbar();
//        }
        if($('#action-bar-sections-wrap').attr('data-status')==='open' && $(this).attr('data-target')===$('.current-action-bar-item').attr('data-target')){
           
            closeactionBar();	
        }else{
            openactionBar($(this));
        }
    });
    $('#action-bar-remove').on('click',closeactionBar);
    
    $('#actionbar-doorknob .icon-logo').on('click',openActionBarMobileAnimation);
}

function closeActionBarMobile()
{
    log('close');
    $('#action-bar-wrap').animate({right:'-100%'});
    $('header').animate({top:'-48px'});
    $('#action-bar-sections-wrap').attr('data-status','close');
    $('#actionbar-doorknob').removeClass('pfm-red');
}

function closeactionBar(){
    var $ = jQuery;
    if ($('body').hasClass('mobile'))
    {
        return closeActionBarMobile();
    }
    var $ = jQuery;
    if($('#action-bar-sections-wrap').attr('data-status')==='open'){
         
        $('#action-bar-sections-wrap').animate({right:'-550px'},500).attr('data-status', 'close');
        $('#action-bar-remove').animate({left:'75px'});
        $('.current-action-bar-item').removeClass('current-action-bar-item');
        $('#action-bar').trigger('resize');
    }
};

function topMenu()
{
    $('.menu-item-text')
        .click
        (
            function()
            {
                var splittedHref = $(this).attr('href').split('#');
                if (splittedHref.length>1)
                {
                    var hash = splittedHref[1];
                    $('#action-bar a[data-target="#section-'+hash+'"]').click();
                }
//                log(window.location.hash);
            }
        );
}

function hashManage()
{
    if(window.location.hash) 
    {
        var hash =  location.hash.substr(1);
//        log('hash : '+$('a[data-target="#section-'+hash+'"]').length);
        $('#action-bar a[data-target="#section-'+hash+'"]').click();
        
    } 
}


var apiScrollBar=false;

function openActionBarMobile(item)
{
    log('openActionBarMobile');
    var $ = jQuery;
    if($('#action-bar-sections-wrap').attr('data-status')!=='open' && $($(item).attr('data-target')).length)
    {
        openActionBarMobileAnimation();
        
    }
     //Ajoute la classe qui définit l'item courant
    $('.current-action-bar-item').removeClass('current-action-bar-item');
    $(item).addClass('current-action-bar-item');

    
    //L'action bar ouverte on replace la section sélectionné
    if($($(item).attr('data-target')).length){
        $("#action-bar-sections-wrap").scrollTo($('.current-action-bar-item').attr('data-target'), 100 );
    }
  
    //On replace le scrool body au top de la page
    $('body').scrollTo(0,100);
}

function openActionBarMobileAnimation()
{
    log('openActionBarMobileAnimation');
    if ($('#action-bar-sections-wrap').attr('data-status') != 'open')
    {
        log('open');
        $('header').animate({top:'0'});
        $('#action-bar-wrap').animate({right:'0'});
        $('#action-bar-sections-wrap').attr('data-status','open');
        $('#actionbar-doorknob').addClass('pfm-red');
    }
    else
    {
        closeActionBarMobile();
    }
    
}

function openactionBar(item){
    
    var $ = jQuery;

    if ($('body').hasClass('mobile'))
    {
        return openActionBarMobile(item);
    }

    //Vérifie que l'action bar soit ouverte et qu'il existe un panneau correspondant à l'item
    if($('#action-bar-sections-wrap').attr('data-status')!=='open' && $($(item).attr('data-target')).length){
        $('#action-bar-sections-wrap').animate({right:'50px'});
        $('#action-bar-remove').animate({left:'-425px'});
        $('#action-bar-sections-wrap').attr('data-status','open');
        $('#action-bar').trigger('resize');
    
    //l'action bar est ouverte, mais le panneau n'xiste pas, on ferme l'action bar
    }else if($('#action-bar-sections-wrap').attr('data-status')==='open' && $($(item).attr('data-target')).length===0){ 
        closeactionBar();
        
    }
    	
    //Ajoute la classe qui définit l'item courant
    $('.current-action-bar-item').removeClass('current-action-bar-item');
    $(item).addClass('current-action-bar-item');


    //L'action bar ouverte on replace la section sélectionné
    if($($(item).attr('data-target')).length){
        $("#action-bar-sections-wrap").scrollTo($('.current-action-bar-item').attr('data-target'), 100 );
    }
  
    //On replace le scrool body au top de la page
    $('body').scrollTo(0,100);

    $('.current-action-bar-item').hover(
        function(){
            $(this).find('i').attr('data-class',$(this).find('i').attr('class'))
            $(this).find('i').attr('class', 'icon-remove action-bar-icon');
        },function(){
            $(this).find('i').attr('class', $(this).find('i').attr('data-class'));
        }
    );
    	
};
function actionBarResize(){
    var $ = jQuery;
	
    //Adaptation de la hauteur de l'action bar
    var marg = parseInt($('#action-bar-wrap').css('top'));
    
    //Calcul la hauteur de l'action bar, et on force chaque section à cette hauteur
    var actionBarHeight = parseInt($(window).height()-marg);
    $('#action-bar-wrap').height(actionBarHeight+'px');
    $('.action-bar-section').outerHeight(actionBarHeight+'px');
    
    log('actionBarHeight : '+actionBarHeight);
    log('Section height : '+$('.action-bar-section').height());
    log('NOMBRE ITEM :'+$('.action-bar-item').length);
    //Réinitialise le plugin BAR DE SCROLL, pour scroller si besoin à l'intérieur des sections
    $($('.current-action-bar-item').attr('data-target')).perfectScrollbar('destroy');
    $($('.current-action-bar-item').attr('data-target')).perfectScrollbar();
    
//    log('Section height [after jscollPane] : '+$('.action-bar-section').height());
    $("#action-bar-sections-wrap").scrollTo($('.current-action-bar-item').attr('data-target'), 100 );
    $('#actionbar-doorknob').css('right',(($(window).width()-$('#actionbar-doorknob').width())/2)+'px');
    setButtonHeight(actionBarHeight);

};


//Ajout d'une cat ou d'un tag à un wall
function addRemoveToStreamRefresh()
{
    $('.addToWallCheckbox').unbind('change',addRemoveToStreamHandler);
    $('.addToWallCheckbox').bind('change',addRemoveToStreamHandler);
}
function addRemoveToStream()
{
    $('.addToWallCheckbox').bind('change',addRemoveToStreamHandler);
        
    
}
function addRemoveToStreamHandler()
{
//    log('checked'+$(this).is(':checked'));

    if($('#'+$(this).data('linked')).val() === '0')
    {
        $('#'+$(this).data('linked')).val(1);
    }
    else
    {
        $('#'+$(this).data('linked')).val(0);
    }

    $(this).closest('form').submit();
}

function commentManage(){
    var $ = jQuery;
    $('.comment-hover').css('opacity',0);
    $('.comment').hover(
        function(){
                $(this).find('.comment-hover').animate({opacity:1},50);
        },function(){
                $(this).find('.comment-hover').animate({opacity:0},50);
        }
    );
    
    $('.comment').each(function(i,comment){
        if(i%2===0){
            $(comment).addClass('gray-back');
        }
    });
};


//Calcul de la hauteur des btn de l'action bar
function setButtonHeight(actionBarHeight){
    //Calcul le nombre de boutons dans l'action bar
    buttonNumber = $('#action-bar .action-bar-item').length;
    
    log('number'+buttonNumber);
    
    //Press/ratio/follow/settings valent double
    if($('#action-bar-pfm').length)buttonNumber = buttonNumber+1;
    if($('#action-bar-rate').length)buttonNumber = buttonNumber+2;
    if($('#action-bar-follow').length)buttonNumber = buttonNumber+2;
    if($('#action-bar-settings').length)buttonNumber = buttonNumber+1;

    log('number'+buttonNumber);
    //Si report en bas de page :
//    if($('#action-bar-report').length)actionBarHeight = actionBarHeight - $('#action-bar-report').height();

    //on calcul la hauteur d'un bouton, et on applique
    buttonHeight = (actionBarHeight/(buttonNumber))-1;
    $('.action-bar-item').height(buttonHeight);
    //hauteur de l'icone pour le centrer
    paddingIcon = (buttonHeight-$('.action-bar-link').height())/2;
    //class action-bar-item avant pour éviter les confilts avec report
    $('.action-bar-item .action-bar-link').height(buttonHeight).css('line-height', buttonHeight+'px');

    if($('#action-bar-pfm').length)
    {
        $('#action-bar-pfm').height(buttonHeight*2);
        $('#action-bar-pfm').find('a').height(buttonHeight*2).css('line-height', buttonHeight*2+'px');
    }

    if($('#action-bar-follow').length){
            $('#action-bar-follow').height(buttonHeight*2);
            $('#action-bar-follow .action-bar-icon').css('line-height',parseInt(buttonHeight*2)+'px');
    }

    if($('#action-bar-settings').length){
            $('#action-bar-settings').height(buttonHeight*2);
            $('#action-bar-settings .action-bar-icon').css('line-height',parseInt(buttonHeight*2)+'px');
    }

    if($('#action-bar-rate').length){
            $('#action-bar-rate').height(buttonHeight*2);
            $('#action-bar-rate .action-bar-icon').css('line-height',buttonHeight+'px');
    }
}


/********************************** RATING BAR *******************************************/
//barre de vote dans le actionBar, gestion du hover, vote ...
function postRating(){
    var $ = jQuery;


    $('#action-bar-rate').hover(
        function(){
            $('#action-bar-vote-wrap').stop().fadeIn(200);
        },function(){
            $('#action-bar-vote-wrap').stop().fadeOut(200);
        }
    );

    //Au click on effectue le vote, et d�sactive la possibilit� de 
    $('#rating_more, #rating_less').click(function(){
        
        if($('#rating_more, #rating_less').attr('data-status')!='disabled'){
            //$('#rating_more, #rating_less').attr('data-status', 'disabled').addClass('rating-unselected');
            $(this).removeClass('rating-unselected').addClass('rating-selected');
        }
    });
    ratioPostRating();
};

function ratioPostRating(){
    var $ = jQuery;
    
    var votePlus = parseInt($('#rating_more').attr('data-vote-number'));
    var voteLess = parseInt($('#rating_less').attr('data-vote-number'));
    
    var voteTotal =  voteLess + votePlus; 
    
    var votePlusPercent = (votePlus*100)/voteTotal;
    var voteLessPercent = (voteLess*100)/voteTotal;
            
	
    //set les chiffres en haut et en bas de la jauge
    $('#action-bar-ratio-wrap .ratio-plus-number').html(votePlus);
    $('#action-bar-ratio-wrap .ratio-less-number').html(voteLess);

    //Set la hauteur de la jauge
    if(votePlusPercent<5) votePlusPercent = 5;
    if(votePlusPercent>95) votePlusPercent = 95;
    $('#action-bar-ratio .ratio-plus').css('height', votePlusPercent+'%');
    
    if(voteLessPercent<5) voteLessPercent = 5;
    if(voteLessPercent>95) voteLessPercent = 95;
    $('#action-bar-ratio .ratio-less').css('height', voteLessPercent+'%');
		
}



/**************************************** FILTERS *************************************************/

//gestion des filtres de l'action bar
function filterManage(){
        var $ = jQuery;
       
    
//Equivalent � checkbox
    $('.filter-type-checkbox').each(function(i,list){
        $(list).find('.filter-checkbox').click(function(){
            if($(this).hasClass('selected')){
                    iconColor($(this).find('img'), 0);
                    $(this).removeClass('selected');
                    $(this).find('input').attr('checked',false);
            }else{
                    iconColor($(this).find('img'), 1);
                    $(this).addClass('selected');
                    $(this).find('input').attr('checked',true);
            }

            $(this).closest('form').submit();

        });
    });
	
//Equivalent � radiotile-tag-add
    $('.filter-type-radio').each(function(i,list){
        $(list).find('.filter-radio').click(function(){
            if(!$(this).hasClass('selected')){
                //chagement des icones
                iconColor($('.filter-radio.selected').find('img'), 0);
                iconColor($(this).find('img'), 1);

                $('.filter-radio.selected').removeClass('selected');
                $(this).addClass('selected');

                $(this).find('input').attr('checked',true);

                $(this).closest('form').submit();
            }
        });
    });
        
        
//Slider permettant de jouer sur la date des post
    $('.filter-time').each(function(i, filterTime){
//        log('filter time : '+$(this).next('.filter-time-form').find('.filter-time-start').length);
        var startObj = $(this).next('.filter-time-form').find('.filter-time-start');
        var endObj = $(this).next('.filter-time-form').find('.filter-time-end');

        $(filterTime).noUiSlider({
            range: [0, 11]
           ,start: [parseInt(startObj.val()),parseInt(endObj.val())]
           ,direction:'rtl'
           ,connect: true
           ,step:1
           ,serialization: {
                to: [ startObj, endObj ]
               ,resolution: 1
           }
           ,slide: function(){
               filterTimeChange($(this));
           }
           ,set: function(){
                $(this).closest('form').submit();
           }
        });
        filterTimeChange($(filterTime));
    });
    
};

function filterTimeChange(filterTime){
    
    var $ = jQuery;
    
    var startObj = $(filterTime).next('.filter-time-form').find('.filter-time-start');
    var startText = $(filterTime).next('.filter-time-form').find('.filter-time-start-text');
    var endObj = $(filterTime).next('.filter-time-form').find('.filter-time-end');
    var endText = $(filterTime).next('.filter-time-form').find('.filter-time-end-text');
    
	
	switch(parseInt(endObj.val()))
		{
		case 11:
			endText.html(messageActionBar['forever_short']);
			break;
		case 10:
			endText.html(messageActionBar['5yearsago']);
			break;
		case 9:
			endText.html(messageActionBar['1yearsago']);
			break;
		case 8:
			endText.html(messageActionBar['6monthsago']);
			break;
		case 7:
			endText.html(messageActionBar['3monthsago']);
			break;
		case 6:
			endText.html(messageActionBar['1monthsago']);
			break;
		case 5:
			endText.html(messageActionBar['2weeksago']);
			break;
		case 4:
			endText.html(messageActionBar['1weeksago']);
			break;
		case 3:
			endText.html(messageActionBar['3daysago']);
			break;
		case 2:
			endText.html(messageActionBar['2daysago']);
			break;
		case 1:
			endText.html(messageActionBar['Yesterday']);
			break;
		case 0:
			endText.html('');
			break;
		default:
		}
		
		switch(parseInt(startObj.val()))
		{
			case 11:
				startText.html(messageActionBar['forever_long']);
				break;
			case 10:
				startText.html(messageActionBar['5yearsago']);
				break;
			case 9:
				startText.html(messageActionBar['1yearago']);
				break;
			case 8:
				startText.html(messageActionBar['6monthsago']);
				break;
			case 7:
				startText.html(messageActionBar['3monthsago']);
				break;
			case 6:
				startText.html(messageActionBar['1monthago']);
				break;
			case 5:
				startText.html(messageActionBar['2weeksago']);
				break;
			case 4:
				startText.html(messageActionBar['1weekago']);
				break;
			case 3:
				startText.html(messageActionBar['3daysago']);
				break;
			case 2:
				startText.html(messageActionBar['2daysago']);
				break;
			case 1:
				startText.html(messageActionBar['yesterday']);
				break;
			case 0:
				startText.html(messageActionBar['today']);
				break;
			default:
		}
		
	
};

function unpressformType(params)
{
    log(params);
    $('.press-feedback').removeClass('current').addClass('pfm-grey-button');
    $('#unpressformType_'+params.id).addClass('current').removeClass('pfm-grey-button');
    $('#feedback-unpress').slideDown();
}

function pressformed()
{
   
    log('pressformed');
    if ($('#pfm-press').attr('data-action') == 'pressform')
    {
        log('to unpress');
        $('#pfm-press-menu').attr('data-action','unpressform');
        $('#pfm-press').attr('data-action','unpressform');
        $('#pfm-press').addClass('pressed');
            $('#pfm-press-menu').addClass('pressed');
            $('#action-bar-pfm').addClass('pressed');
            $('#pfm-unpress-explain,.unpressformTitle,.pressformTitleFuture').hide();
            $('.pressformTitle').show();
            $('#pressformersCount').text(parseInt($('#pressformersCount').text())+1);
            $('#press-timer').pietimer('reset');
    }
    else
    {
        log('to press');
        $('#pfm-press-menu').attr('data-action','pressform');
        $('#pfm-press').attr('data-action','pressform');
        $('#pfm-press-menu').removeClass('pressed');
            $('#pfm-press').removeClass('pressed');
            $('#action-bar-pfm').removeClass('pressed');
            $('#pfm-unpress-explain,.unpressformTitle').show();
            $('#pressformersCount').text(parseInt($('#pressformersCount').text())-1);
            $('.pressformTitle').hide();
    }
    
    
}

//Gestion du bouton press
function  pressManage(){
    
    //activation du bouton press, après 30 sec
    if($('#pfm-press-menu').hasClass('autostart')){
        
        $('#press-timer').pietimer({
            timerSeconds: 25,
            color: '#ed3237',
            fill: false,
            startAt:0,
            showPercentage: false,
            callback: function() {
                if(!$('#pfm-press').hasClass('pressed')){
                    $('#pfm-press-menu').click();
                    $('#pfm-press').click();
                }
                  $('#press-timer').pietimer('reset');
                  
                log('pie callback');
            }
        });
        log('pie');
    }
    
//    
    if ($('#pfm-button.autostart').length)
    {
//        log('autostart');
        var autostart = setTimeout(function(){$('#pfm-button.autostart').click();},25000);
        $('#pfm-button.autostart').click(function(){clearTimeout(autostart);});
    }
    
    
    
    
//    $('#pfm-button').delay(10000).click();

}


function loadUserNotifications(params)
{
    log('loadUserNotifications');
    var target = $('#'+params.target);

    target.before(params.view);
    target.closest('.notifications-list').find('img:not(.resize-test)').each
    (
        function(){

            alignThumb($(this));
        }
    )
    var ajaxParams = target.attr('data-params');
    if (typeof ajaxParams == 'string')
    {
        ajaxParams = JSON.parse(ajaxParams);
    }
    ajaxParams.page ++;
    log(ajaxParams);
    target.attr('data-params',JSON.stringify(ajaxParams));
    
}



