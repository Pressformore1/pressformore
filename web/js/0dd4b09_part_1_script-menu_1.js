;

jQuery(document).ready(function(){
    menuManage();
});

//Affichage du menu permanent, swicth entre position relative et fixed
jQuery(window).scroll(function(){
    fixedTopBar();
});
jQuery(window).load(function(){
    //NotificationPics
    $('.user-pic-thumb').each(function(){log('align please');alignThumb($(this));});
});



//Affic
function menuManage(){
    var $ = jQuery;
	
    
    sidebarMenu();
        
    notificationManage();
    
    fixedTopBar();
    
    //survol du logo affichage du menu public
    $('#center-menu').hover(
        function(){
            $(this).find('.hide-menu-item').fadeIn();
        },function(){
            $(this).find('.hide-menu-item').fadeOut();    
        }
    );
	
    	
    //Barre de recherche dans le menu
    $('#search-icon').click(function(){
        //Affichage du champ de recherche
        $('#menu-search-form').animate({width:'show'},function(){
            $('#menu-search-form').attr('data-status','open')
        });

        //on force le focus sur le champ de recherche, au focusOut, on cache le champ
        $('#menu-search-form').find('input.menu-form-field').focus();
    });
    //Instancie le plugin select2 utilisé dans la barre de recherche
    $('.menu-form-select').select2({
        minimumResultsForSearch: -1,
        formatResult: format,
        formatSelection: format
    });
    
    //a la fermeture du select, on force le focus sur l'input du champ de recherhce
    $('.menu-form-select').bind('change',function(){
        $('#menu-search-form').find('input.menu-form-field').focus();
    });
    //Signal que le form search est ouvert (cas de la page search ou le form est toujours ouvert)
    if($('#menu-search-form').hasClass('force-open')){
        $('#menu-search-form').attr('data-status', "open");
    }
        
    //btn add post
    $('#add-post-icon').click(function(){
        $('#add-post-form').animate({width:'show'}, function(){
            $('#add-post-form').attr('data-status',"open");
        });//Affichage du champ de recherche
        $('#add-post-form').find('input.menu-form-field').focus();
            
    });
	
	
	
    //Clique à tout endroit, utiliser pour fermer les élements ouverts
    $(document).click(function (e)
    {
       log('Click anywhere -> ');
        log($(e.target));
//        log($(document.activeElement));
//    
        //Clic extérieur champ de recherhce, si oui fermeture du champ de recherhce
        if ($(e.target).parents('#menu-search-form').length == 0 && $(e.target).parents('#search-icon').length == 0){
            //Vérifie que le champ de recherche est bien ouvert
            if($('#menu-search-form').attr('data-status')=="open"){
                    $('#menu-search-form').animate({width:'hide'}).attr('data-status','close');//Cahce le champ de recherche
            }
        }

        //Clic extérieur add, si oui fermeture du champ add post
        if ($(e.target).parents('#add-post-form').length == 0 && $(e.target).parents('#add-post-icon').length == 0){
            //Vérifie que le champ de recherche est bien ouvert
            if($('#add-post-form').attr('data-status')=="open"){
                    $('#add-post-form').animate({width:'hide'}).attr('data-status','close');//Cahce le champ de recherche
            }
        }

        //click au dehors de notifications box
        if ($(e.target).parents('#notifications-box').length==0 && $(e.target).parents('#menu-notifications').length==0){
            $('#notifications-box').fadeOut().attr('data-status','close');
        }
    });
    
    $('.sidebar-wall-name').each(function(i,name){
        if ($(name).parent('a').find('.author').length)
        {
//            log('followed')
//            limitChar($(name), 18);
        }
        else
        {
            limitChar($(name), 25);
        }
        
    });
	
    
    
    
	
};

//Menu personnalisé de gauche affiché au click sur l'icone en haut à gauche du menu
function sidebarMenu(){
    var $ = jQuery;

    //Affciahge de la sidebar
    sidebarPfmHeight = $('#user-sidebar').height();

    $('#sidebar-btn').parent()
        .mouseenter(function(){
            $('#user-sidebar').stop().slideDown(100).attr('data-status','open');
        })
        .mouseleave(function(){
            log('Niche');
            $('#user-sidebar').stop().slideUp(100).attr('data-status','close');
        });    
    
    //Btn previous 
    $('#sidebar-btn').click(function(){
       var oldURL = document.referrer;
       window.location.href = oldURL;
    });
//    limitChar($('.sidebar-wall-name'), 15);
};

/**
 * Notication dans la top bar, ouverture fermeture, et changemenbt d'onglet message/noitif
 */
function notificationManage() {
    var $ = jQuery;

    $('#menu-notifications .menu-item-icon').click(function(){

        //'longlet notification est ouvert, on le ferme
        if($('#notifications-box').attr('data-status')=='open'){
            $('#notifications-box').fadeOut().attr('data-status','close');

        //'longlet notification est fermé, on l'ouvre
        }else if($('#notifications-box').attr('data-status')=='close'){
            $('#notifications-box').fadeIn().attr('data-status','open');
        }
    });
}

/**
 * Affichage permanent de la top bar, swicth entre fixed et relative
 */
function fixedTopBar(){
    var $ = jQuery;
    
    if($(window).scrollTop()>0){
        $('#menu').css('position', 'fixed');
    }else{
        $('#menu').css('position', 'relative');
    }
}

/**
 * 
 */
function notificationRead(notificationMenu)
{
    $('.notificication-count').text('0').addClass('hidden');
    
}