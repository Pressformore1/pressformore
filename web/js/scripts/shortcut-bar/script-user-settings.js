;

jQuery(document).ready(function(){
       	userSettingsManage();
		

});




function userSettingsManage(){
	var $ = jQuery;
	categoriesManage();
	tagsManage();
	
	//Page changement de password
	if($('#password-match').length){
            log("user Settings");
            if ($('#password-confirm').length)
            {
		//Indicateur de vulnérabilité du pass
		$('#password-confirm').passStrengthify({
			minimum: 5,
			labels: {
				tooShort: 'Too short custom text',
				passwordStrength: 'Password strength custom text'
			}}
		);
		//Confirmation du nouveau mot de passe, vérifie que les 2 champs correspodent
		$('#new-password-confirm').keyup(function(){
			if($('#password-confirm').val()===$('#new-password-confirm').val()){
				$('#password-no-match').hide();
				$('#password-match').show();
			}else{
				$('#password-no-match').show();
				$('#password-match').hide();
			}
		});
	}
            else
            {
                //Indicateur de vulnérabilité du pass
		$('#password-confirm').passStrengthify({
			minimum: 6,
			labels: {
				tooShort: 'Too short custom text',
				passwordStrength: 'Password strength custom text'
			}}
		);
		//Confirmation du nouveau mot de passe, vérifie que les 2 champs correspodent
		$('#fos_user_registration_form_plainPassword_first,#fos_user_registration_form_plainPassword_second').keyup(function(){
                    if ($('#fos_user_registration_form_plainPassword_first').val().length && $('#fos_user_registration_form_plainPassword_second').val().length )
                    {
                        if($('#fos_user_registration_form_plainPassword_first').val()===$('#fos_user_registration_form_plainPassword_second').val()){
				$('#password-no-match').hide();
				$('#password-match').show();
			}else{
				$('#password-no-match').show();
				$('#password-match').hide();
			}
                    }
	
		});
            }
		
	}
	
	//Page my-account, édition des skills
	$('#user-skills').tagsinput();
	
};




//gestion des tags
function tagsManage(){
	var $ = jQuery;
	
	//Drag and drop de la page my-pref
	$('#tagsFollowed').sortable({
		connectWith: ".tags-list",
		 placeholder: "ui-state-highlight"
	});
	$('#searchTags-list').sortable({
		connectWith: ".tags-list",
		placeholder: "ui-state-highlight"
	});
	$( "#tagsBanned" ).sortable({
		connectWith: ".tags-list",
		placeholder: "ui-state-highlight"
	});
	$('#mostPopularTags').sortable({
		connectWith: ".tags-list",
		placeholder: "ui-state-highlight"
	});

	
	//Afficahge des résultats de la recherhce dans tag dans mypref
	$('#searchTags').keyup(function(){
		
		//Si la div de résultat n'est pas affiché on l'affiche et on hide les tags les plus populaires 
		if(!$('#searchTags-wrap').is(':visible')){
			$('#searchTags-wrap').show();
		}
		var data = "k="+$('#searchTags').val();
//JONA : //Fonction ajax pour remplir la liste des tags trouvé
		//$('#searchTags-list').html(ResultAJAX)
		
	});
	
	
	
	
	//Follow et unfollow un tag présent dans le site
	$('.tag-active').each(function(i,tag){
		$(tag).find('.tag-icon').click(function(){
			if($(tag).hasClass('tag-followed')){
//JONA 				//TON AJAX pour dire que ce tag est unfollow
				$(tag).removeClass('tag-followed');
			}else{
//JONA 				//TON AJAX pour dire que ce tag est unfollow
				$(tag).addClass('tag-followed');
			}
			return false;
		});
	});
	
	
	
};

function categoriesManage(){
	var $= jQuery;
	//Au hover on change l'icon ou follow si le tag n'est pas suivi, en remove si il est déjà suivi
	$('.category-active').each(function(i,cat){
		$(cat).find('.category-icon').hover(
			function(){
				$(this).find('img').hide()
				
				if($(cat).hasClass('category-followed')){
					$(this).find('i').addClass('icon-remove');
				}else{
					$(this).find('i').addClass('icon-ok');
				}
			},function(){
				$(this).find('img').show();
				$(this).find('i').attr('class','');
			}
		);
	});
	
	
	//Follow et unfollow un tag présent dans le site
	$('.category-link').each(function(i,cat){
		$(cat).find('.category-icon').click(function(){
			if($(cat).hasClass('category-followed')){
//JONA 				//TON AJAX pour dire que ce tag est unfollow
				$(cat).removeClass('category-followed');
			}else{
//JONA 				//TON AJAX pour dire que ce tag est unfollow
				$(cat).addClass('category-followed');
			}
			return false;
		});
	});
};
