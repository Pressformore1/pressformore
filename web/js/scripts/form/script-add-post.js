;

jQuery(document).ready(function(){
       
	addPostManage();
        
});




/*****************************************************************************************/
//Fonctions li�s � la modal et au champ addPost
function addPostManage(){
	var $ = jQuery;
	
	log('HERE ???')
	
	//Champs tags de l'addpost
	$('#addPost-tags').tagsinput({
		//possibilit� d'ajout� un json :
			//typeahead: {
			//       source: function(query) {
			//	       return $.get('http://someservice.com');
			//	}
			//}
		
		typeahead: {
			source: ['Amsterdam', 'Washington', 'Sydney', 'Beijing', 'Cairo']
		}
	});
	
	//Champ cat�gorie de l'addPost
	//$("#addPost-category, #myPref-category").select2({
	//	formatResult: categoryListFormat,
	//	formatSelection: categoryListFormat,
	//	escapeMarkup: function(m) { return m; }
	//});
		
	
};
//
////Choix de la miniature lors de l'addPost
//function addPostThumb(){
//	var $ = jQuery;
//	imgNumber = 0;
//	
//	if($('#thumbnail-slider-wrap').length){
//		
//		//Affichage du slider
//		$('#thumbnail-slider-wrap').show();
//		
//		//cr�e les images dans le slider
//		$('#post-thumbnail option').each(function(i,img){
//			selectedClass='';
//			if(i==0)selectedClass = "thumbnail-slider-selected"
//			$('#thumbnail-slider').append('<li class="'+selectedClass+'"><img src="'+$(img).val()+'" alt="" /></li>');	
//		});
//		$('#post-thumbnail').hide();
//		
//		
//		//le premier �l�ment est toujours pr�selection�
//		$('#thumbnail-slider-prevImg, #thumbnail-slider-nextImg').click(function(){
//			
//			//Image pr�c�cdente
//			if($(this).attr('id')==="thumbnail-slider-prevImg"){
//				imgNumber--;
//			}
//			if($(this).attr('id')==="thumbnail-slider-nextImg"){
//				imgNumber++;
//			}
//			if(imgNumber>$('#thumbnail-slider li').length-1)imgNumber=0;
//			else if(imgNumber<0)imgNumber=$('#thumbnail-slider li').length-1;
//			console.log(imgNumber);
//			$('#thumbnail-slider li.thumbnail-slider-selected').removeClass('thumbnail-slider-selected');
//			$('#thumbnail-slider li:eq('+imgNumber+')').addClass('thumbnail-slider-selected');
//			$('#post-thumbnail option:selected').attr('selected', false);
//			$('#post-thumbnail option:eq('+imgNumber+')').attr('selected', true);
//		});
//	}
//};
//
//
//
//


