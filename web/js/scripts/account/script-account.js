jQuery(document).ready(
        function()
        {
            manageWallSettings();
            toggler();
            paiementFormManage();
            formForce();
            paymentMethod();
            requiredFields();
            cardExpiration();
            
        }        
);

function cardExpiration()
{
    
    
    if ($('#cardExpirationDateMonth').length)
    {
        var month = $('#cardExpirationDateMonth').val();
        var year = $('#cardExpirationDateYear').val().substring(2);
        $('#cardExpirationDate').val(month+year);
        $('#cardExpirationDateMonth,#cardExpirationDateYear')
            .change
            (
                function()
                {
                    var month = $('#cardExpirationDateMonth').val();
                    var year = $('#cardExpirationDateYear').val().substring(2);
                    $('#cardExpirationDate').val(month+year);
                }
            );
    }
}


function requiredFields()
{
    if (!$('.form-horizontal').hasClass('new'))
    {
        $('.form-group input[required=required],.form-group select[required=required],.form-group textarea[required=required]')
        .each
        (
                function()
                {
                    requiredFieldsColor($(this));
                }
        );
    }
    
    $('.form-group input[required=required],.form-group select[required=required],.form-group textarea[required=required]').change(function(e){log("change");requiredFieldsColor($(this));});
}
function requiredFieldsColor(target)
{
    var group = target.closest('.form-group');
    var label = group.find('label');
    
    var add = false;
    group.find('input[required=required],select[required=required],textarea[required=required]').each
    (
        function()
        {
            if (!$(this).val().length)
            {
                add=true;
            }
        }
    )
    if (add)
    {
        log('add');
        label.addClass('pfmred');
    }
    else
    {
        log('remove');
        label.removeClass('pfmred');
    }
}

function manageWallSettings(){
        var $ = jQuery;
        
        $('.select-mutli-choices').select2({
                formatResult: categoryListFormat,
                formatSelection: categoryListFormat,
                escapeMarkup: function(m) { return m; }
        });
        
        
        if ($('#p4m_userbundle_user_skills').length)
        {
            
            $('#p4m_userbundle_user_skills').tagsinput({
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
        
        }
}

function toggler()
{
    $('.toggler').click
    (function()
    {
        $('#'+$(this).attr('data-toggle')).toggleClass('hidden');
    })
}

function walletManage(){
    var amount = parseInt($('#wallet-load-amount-field').val());
    log('amount');
    $(".knob").knob({
        'draw' : function () { 
            $(this.i).val(this.cv + '€')
            $('#loading-amount').html($(this.i).val()+' '+$('#wallet-load-amount').find('.text-center').html());
            
        },
        'change':function(v)
        {
            log('knob changed'+v);

            if ($('#change-amount').length)
            {
                if (v != amount)
                {
                    $('#change-amount')
                        .removeAttr('disabled')
                        .removeClass('pfm-grey-button');
                }
                else
                {
                    $('#change-amount')
                        .attr('disabled','disabled')
                        .addClass('pfm-grey-button');
                }
            }
            
        }
    });
    
//    if ($('#change-amount').length)
//    {
//        $('#wallet-load-amount-field').change
//        (
//            function()
//            {
//                $('#change-amount')
//                        .removeAttr('disabled')
//                        .removeClass('pfm-grey-button');
//            }
//        );
//    }
}


function paiementFormManage()
{
    $('#cardForm')
        .submit
        (
            function(e)
            {
                var currentElement = $(this);
                log(currentElement.data('confirmed'));
                if (currentElement.attr('data-confirmed') == "0")
                {
                    e.preventDefault();
                    $('#modal-confirm .modal-body').html($(this).attr('data-confirm-text'));
                    $('#modal-confirm .confirm-btn').on('click',function(){currentElement.attr('data-confirmed',1);currentElement.submit();});

                    if ($('#modal-confirm .modal-body').find('.confirm-amount'))
                    {
                        $('#modal-confirm .modal-body').find('#confirm-amount').html($('#wallet-load-amount-field').val());
                        $('#modal-confirm .modal-body').find('.confirm-perdiod').html($('#wallet-load-amount .lead').text());
                    }

            //        $('#modal-confirm .confirm-btn').attr('data-url', $(btn).attr('data-url'));
            //        $('#modal-confirm .confirm-btn').attr('data-loader', 'full');
            //        $('#modal-confirm .confirm-btn').attr('data-params', $(btn).attr('data-params'));
            //        $('#modal-confirm .confirm-btn').attr('data-key', $(btn).attr('data-key'));
            //        $('#modal-confirm .confirm-btn').attr('data-action', $(btn).attr('data-action'));
                    $('#modal-confirm').modal('show');
                    $('#modal-confirm').on('hidden.bs.modal',function()
                    {
                        $('#modal-confirm .confirm-btn').off('click',function(){currentElement.attr('data-confirmed',1);currentElement.submit();});

                    });
                }
                else if ($(this).attr('data-submit') == "0")
                {
                    e.preventDefault();
                    log('data-submit');
                    var form = $(this);
                    $.ajax
                    (
                        {
                            url:$(this).attr('data-registerUrl'),
                            data:{ammount : parseInt($('#wallet-load-amount-field').val()),preAuthorisation: $('#wallet-load-amount-monthly').is(':checked')?1:0},
                            type: 'post',
                            success: function(jsonData)
                            {
                                var datas = JSON.parse(jsonData);
                                form.find('#returnURL').val(datas.returnURL);
                                form.find('#data').val(datas.data);
                                form.find('#accessKeyRef').val(datas.accessKeyRef);
                                form.attr('action',datas.action);
                                form.attr('data-submit',1);
                                log($(this).tagName);
                                form.submit();
                                
                            }
                        }
                    )
                    
                }
            }
        );
}

function formForce()
{
    $('.form-force')
        .click
        (
            function(e)
            {
                e.preventDefault();
                var form = $($(this).attr('data-target'));
                if ($(this).attr('data-params').length)
                {
                    var params = JSON.parse($(this).attr('data-params'));
                    $.each(params,function(key,value)
                    {
                        form.append('<input type="hidden" name="'+key+'" value="'+value+'" />');
                        
                    });
                    
                }
                form.submit();
                
            }
        );

    $('.distant-form-force')
        .submit
        (
            function(e)
            {
                e.preventDefault();
                var form = $($(this).attr('data-target'));
                var currentElement = $(this);
                if (form.attr('id')=='wallet-load-amount' && currentElement.attr('data-confirmed') == "0")
                {
                    log(currentElement.data('confirmed'));
                    e.preventDefault();
                    $('#modal-confirm .modal-body').html($(this).attr('data-confirm-text'));
                    $('#modal-confirm .confirm-btn').on('click',function(){currentElement.attr('data-confirmed',1);currentElement.submit();});

                    if ($('#modal-confirm .modal-body').find('.confirm-amount'))
                    {
                        $('#modal-confirm .modal-body').find('#confirm-amount').html($('#wallet-load-amount-field').val());
                        $('#modal-confirm .modal-body').find('.confirm-perdiod').html($('#wallet-load-amount .lead').text());
                    }

            //        $('#modal-confirm .confirm-btn').attr('data-url', $(btn).attr('data-url'));
            //        $('#modal-confirm .confirm-btn').attr('data-loader', 'full');
            //        $('#modal-confirm .confirm-btn').attr('data-params', $(btn).attr('data-params'));
            //        $('#modal-confirm .confirm-btn').attr('data-key', $(btn).attr('data-key'));
            //        $('#modal-confirm .confirm-btn').attr('data-action', $(btn).attr('data-action'));
                    $('#modal-confirm').modal('show');
                    $('#modal-confirm').on('hidden.bs.modal',function()
                    {
                        $('#modal-confirm .confirm-btn').off('click',function(){currentElement.attr('data-confirmed',1);currentElement.submit();});

                    });
                    
                
                }
                else if(form.attr('id')!='wallet-load-amount' || currentElement.attr('data-confirmed') == "1")
                {
                var formElements = $(this).serializeArray();
                log(formElements);
                for (i = 0; i < formElements.length; i++)
                {
                   var element = formElements[i]; 
                   log(element);
                    form.append('<input type="hidden" name="'+element.name+'" value="'+element.value+'" />');
                }
                
                form.submit();
                }
                
                
                
            }
        );
}
//gestion de la page wallet dans admin
function paymentMethod(){

    $('.payment-method-link').click(function(e){
        e.preventDefault();
        $('.payment-method-form').hide();
        $($(this).attr('data-form')).show();
    });
}
function  loadWalletForm(content)
{
    $('#content-wall').html(content);
    walletManage();

}



function  loadWalletView(content)
{
    $('#content-wall').html(content);
    walletChart();
    alignAllThumb();
    
}

function walletChart()
{
    $('.walletGraph').each
    (
        function()
        {
            var userDatas = JSON.parse($(this).attr('data-graphData'));
    
            var ctx = document.getElementById($(this).attr('id')).getContext("2d");



            var data = {
                labels: [userDatas.label],
                datasets: [
                    {
                        label: "You",
                        fillColor: "rgba(270,50,55,0.5)",
                        strokeColor: "rgba(270,50,55,.8)",
                        highlightFill: "rgba(270,50,55,0.75)",
                        highlightStroke: "rgba(270,50,55,1)",
        //                data: [65,72,90]
                        data: [userDatas.user]
                    },
                    {
                        label: "Average",
                        fillColor: "rgba(151,151,151,0.5)",
                        strokeColor: "rgba(151,151,151,0.8)",
                        highlightFill: "rgba(151,151,151,0.75)",
                        highlightStroke: "rgba(151,151,151,1)",
        //                data: [52,84,23]
                        data: [userDatas.average]
                    }
                ]
            };

            var options=
            {
                responsive: true,
                //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                scaleBeginAtZero : true,

                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines : false,
                scaleOverride: false,
                showScale:false,

                //String - Colour of the grid lines
                scaleGridLineColor : "rgba(0,0,0,.05)",

                //Number - Width of the grid lines
                scaleGridLineWidth : 1,

                //Boolean - If there is a stroke on each bar
                barShowStroke : true,

                //Number - Pixel width of the bar stroke
                barStrokeWidth : 2,

                //Number - Spacing between each of the X value sets
                barValueSpacing : 5,

                //Number - Spacing between data sets within X values
                barDatasetSpacing : 1,
                

                //String - A legend template
                legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"

            };
            
            if (userDatas.type=='percent')
            {
                options.scaleStepWidth = 10;
                options.scaleSteps= 10;
                options.scaleOverride= true;
            }


            var myBarChart = new Chart(ctx).Bar(data, options);
            }
        
    )
   
}



