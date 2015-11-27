;


var $container;
jQuery(document).ready(function(){
   $container = $('#content-wall');
   
   
});

jQuery(window).load(function(){
     var $ = jQuery; 
   
    createStrewManage();
    wallManage();
});



function wallManage(){
    var $ = jQuery;    
    
    $container = $('#content-wall');
    
    resizeContentWall();
    setWall();
    
    $('#action-bar').bind('resize',function(){
        resizeContentWall();
        resizeTilesImages(true);
    });

    //Le window resize est instancié dans wallManage, pour éviter d'instancier masonry avant le window load
    jQuery(window).resize(function(){
        resizeContentWall();
        resizeTilesImages(true);

    });
    
    limitChar($('.col-xs-12.masonry-block .source-text'),18);
    limitChar($('.col-xs-12.masonry-block .author-text'),10);

};


function setWall(){
    var $ = jQuery;
    //Défini la première instance ce masonry
    
    
    //signal la fin de chargement, pour la suppression du loader
    $('#content-wall').trigger('loaded');
    $container.infinitescroll('destroy');
    
    
    //une fois les images chargés
    $container.imagesLoaded( function() {
        setTiles();
	
        //Set le layout
        $container.masonry();
	//une fois le layout set on affiche les tiles
        $('.masonry-block').removeClass('is-hidden');
        
        
	//init infinitescroll
        var numberPages = $('.pagination a').length;
        $container.infinitescroll(
            {
                loading: {
		    finishedMsg: 'No more posts to load.',
		    img:  "../../../images/design/loader.gif"
		 },
                //debug: true,
                navSelector  : ".pagination",
                nextSelector : ".pagination a:first",    
                itemSelector : ".masonry-block"
            },
            //Chargement de nouvelles tiles
            function(newContent) {
                $container.append(newContent);
		
		resizeContentWall();
		
                //ajoute au #content-wall (les tiles ont la classe is-hidden)
                $container.imagesLoaded( function() {
                    
                    setTiles();
                    //on les ajoutes dans mansory
                    $container.masonry('appended', newContent );
                    
		    //reset le layout
		    $container.masonry();
		    
		    //Et on affiche les nouvelles tiles
		    $('.masonry-block').removeClass('is-hidden');
                    ajaxUIRefresh();
                    
                });
            }
        );
        ajaxUIRefresh();
    });
    
}


function resizeContentWall(){
    var $ = jQuery;

    //Resize le container du wall
    containerWidth = resizeContainer();
    //Redéfini les classes
    colsNumber = setTilesSize(containerWidth);
    //calcul la taille d'une colone
    columnWidth = containerWidth / colsNumber;


    //reset le layout de masonry
    $container.masonry({
        columnWidth:  columnWidth,
        itemSelector: '.masonry-block',
        isAnimated:true,
        isInitLayout: false,
        isResizable: false
    });
	    
}



/****************************************************************************/
/************************ RESPONSIVE MANAGE ********************************/
function setTilesSize(containerWidth){
	var $ = jQuery;
	
	//Udge Screen
	//6 columns
	if(containerWidth > 1500)
	{
		colsNumber = 6;
		
	}
	//Large Screen
	//5 columns
	else if(containerWidth <= 1500 && containerWidth > 1250)
	{
		colsNumber = 5;
	}
	
	//Normal Screen
	//5 columns
	else if(containerWidth <= 1250 && containerWidth > 1000)
	{
		colsNumber = 4;
	}
	
	//Small Screen
	//3 columns
	else if(containerWidth <= 1000 && containerWidth > 750)
	{
		colsNumber = 3;
	}
	
	//Tiny Screen
	//2 columns
	else if(containerWidth <= 750 && containerWidth > 500)
	{
		colsNumber = 2;
	}
	
	//Smartphone
	//1 columns
	else if(containerWidth <= 500)
	{
		colsNumber = 1;
		
	}
	
	var smallBrickTotal = 0;
        var changeBrickTotal = 0;
	$('.masonry-block').each(function(i, brick){
            
//            //règle aléatoire pour affciher des tiles de taille diférente
//            if(smallBrickTotal>3 && $(brick).attr('data-brick-type')=='small-brick'){
//                smallBrickTotal = 0;
//                changeBrickTotal++;
//                log('Change Brick total');
//                log(changeBrickTotal%3);
//                switch(changeBrickTotal%3){
//                    case 0 : 
//                        $(brick).attr('data-brick-type','normal-brick');
//                        break;
//                    
//                    case 1 : 
//                        $(brick).attr('data-brick-type','large-brick');
//                        break;
//                    
//                    case 2 : 
//                        $(brick).attr('data-brick-type','extra-large-brick');
//                        break;
//                }
//                
//            }
            
	    //défini la largeur de la brick
	    switch($(brick).attr('data-brick-type')){
		case 'small-brick' :
                    smallBrickTotal++;
		    colSize = (60/colsNumber)*1;
		    defaultSizeClass = '.col-xs-'+colSize;
		    
		    break;
	    
		case 'normal-brick' :
		    colSize = (60/colsNumber)*2;
		    if(colSize>60)colSize=60;

		    break;
	
		case 'large-brick' :
		    colSize = (60/colsNumber)*3;
		    if(colSize>60)colSize=60;
		    
		    break;
		
		case 'extra-large-brick' :
		    colSize = (60/colsNumber)*3;
		    if(colSize>60)colSize=60;
		    colSize = colSize+' row-2';
		    break;
		default :
		    colSize = (60/colsNumber)*1;
		    if(colSize>60)colSize=60;
		    colSize = colSize;
		    break;
		    
	    }
	    
	    //récupère les class présentes sur la tiles
	    brickClass = $(brick).attr('class').substr($(brick).attr('class').indexOf('masonry-block'));
	    
	    //applique la classe bootstrap correspondante
	    $(brick).attr('class','col-xs-'+colSize+' '+brickClass);
	});
	
	return colsNumber;
}

//Au chargement de nouvelles tiles, on supprime les ecoutes sur les evenements et on reinitialise le tout
//TILES
function setTiles(){
    var $ = jQuery;
    
    $('.read-later').unbind('click');
    $('.masonry-block').unbind('mouseenter');
    $('.masonry-block').unbind('mouseleave');
    
    resizeTilesImages();
//    setPopover();
    //Read later
    $('.read-later').bind('click', function(){
        if($(this).hasClass('selected')){
            $(this).removeClass('selected');
        }else{
            $(this).addClass('selected');
        }
    });
    
    //Activation des tootip
    $('.post-category-link, .read-later').tooltip();
    
    
    //hover apparition du contenu
    $('.tile').bind({
        mouseenter: function(e) {
            // Hover event handler
//            $(this).find('.tile-header').stop().fadeOut();
            $(this).find('.tile-hover').stop().fadeIn();
            
            
        },
        mouseleave: function(e) {
//            $(this).find('.tile-header').stop().fadeIn();
            $(this).find('.tile-hover').stop().fadeOut();
        }
    });
    
    if($('.tile-follow-btn').length){
        $('.tile-follow-btn').bind({
            mouseenter: function(e) {
                // Hover event handler
                $(this).find('.tile-header').stop().fadeOut();
                $(this).find('.tile-hover').stop().fadeIn();


            },
            mouseleave: function(e) {
                $(this).find('.tile-header').stop().fadeIn();
                $(this).find('.tile-hover').stop().fadeOut();
            }
        });
    }	
    
    //Limite du nombre de caractère pour les tiles ayant un contenu trop imporant
    $('.tile').each(function(i,tile){
        limitChar($(tile).find('.tile-header-title .main'), 65);
        limitChar($(tile).find('.tile-hover-description a'), 260);
    });

};

var myArray = ['#B186DE','#B3DE86','#DEB386','#DE86B1'];

function resizeTilesImages(forceAll){
    var $ = jQuery;
	
    $thumbWrap = $('.masonry-block .tile-header-thumb[data-resize!="resized"]');
    if(forceAll)$thumbWrap = $('.masonry-block .tile-header-thumb');
    
    $thumbWrap.each(function(i,wrap){
        alignThumb($(this).find('img'));
    });
};


function followWallFromTile(wallId)
{
    $('#wall_'+wallId).find('.tile-follow-btn').toggleClass('hidden');
}
function followUserFromTile(userId)
{
    log('callback');
    $('#user_'+userId).find('.tile-follow-btn').toggleClass('hidden');
}