$(function() {
    
    if (typeof learnMVC === 'object') {
        learnMVC.start({
            listId:$('#listId').val(),
            loggedIn: $('#listId').attr('data-loggedin'),
            maxRound: $('#listId').attr('data-maxRound'),
            saveRoundsWhenNotLogged: false
        });
    }
    
    $('a[data-toggle="tooltip"]').tooltip();
    $('a[data-toggle="popover"]').popover();
    
    
    //table for the lists
    if ($("table.listquest_table").length > 0){
        var idTable = $('table').attr('id');
        $("table").dataTable({
            "iDisplayLength": 5,
            "sPaginationType": "full_numbers",
            "bLengthChange": false,
            "oLanguage": {
                "sSearch": "",
                 "oPaginate": {
                    "sNext": ">",
                    "sPrevious": "<",
                    "sFirst": "<<",
                    "sLast": ">>"
                }
            },
            "fnInitComplete": function(oSettings, json) {                
                $('#search_hidden').hide();
                $('#'+idTable+'_filter input').attr('placeholder',$('#search_hidden input').attr('placeholder'))
                    .unwrap().wrap('<div class="input-append"/>')
                    .after('<button type="submit" class="btn"><i class="icon-search"></i></button>');
                },
            "fnDrawCallback": function( oSettings ) {
                $('#'+idTable+'_paginate').addClass('pagination');
           
                if ($('#'+idTable+'_paginate ul').length == 0){
                    $('#'+idTable+'_paginate').children().wrapAll('<ul/>');
                    $('#'+idTable+'_paginate ul>a').wrap('<li/>');
                }
                
                $('#'+idTable+'_paginate li.page_number').remove(); 
                $('#'+idTable+'_paginate span>a').wrap('<li class="page_number"/>'); 
                $('#'+idTable+'_paginate ul>li').eq(1).after($('#'+idTable+'_paginate span li'));
                $('#'+idTable+'_paginate span').hide(); 
                $('#'+idTable+'_paginate a.paginate_active').parent().addClass('active');
             }
        });
    } 
    
    $('.add_item_to_collection_button').click(function(){
        var currentCount = $('#tags_element input').length;
        
        var template = $('#tags_element > span').data('template');
        template = template.replace(/__index__/g, currentCount);

        $('#tags_element').append(template);
        $('#tags_element label').slice(1).hide();
    });
});
