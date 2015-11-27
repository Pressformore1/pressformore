jQuery(document).ready
(
    function()
    {
        ckBrowser();
        checkAll();
    }
);


function checkAll()
{
    console.log('check');
    $('#checkAllInvitations')
            .click(function(){
                $('.checkBoxInvitationChoose').prop('checked',true);
            });
    $('#unCheckAllInvitations')
            .click(function(){
                $('.checkBoxInvitationChoose').prop('checked',false);
            });
}





function ckBrowser()
{
    var $ = jQuery;
    $('.ckBrowser').click(function(e) {
            
            var config = {};
            config.basePath = $(this).data('basepath');            
            config.connectorPath = $(this).data('connectorpath'); 
            config.selectActionData =$(this).prev('input').attr('id');
            var finder = new CKFinder(config);
            finder.selectActionFunction = SetFileField;
            finder.popup();
            return false;
    });
}

function SetFileField( fileUrl, data )
{
    document.getElementById( data["selectActionData"] ).value = fileUrl;
}
