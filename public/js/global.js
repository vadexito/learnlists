$(function() {
    if ($('a[data-toggle="tooltip"]').length >0){
        $('a[data-toggle="tooltip"]').tooltip();
    }
    
    if ($("table").length > 0){
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
                $('#table_lists_filter input').attr('placeholder',$('#search_hidden input').attr('placeholder'))
                    .unwrap().wrap('<div class="input-append"/>')
                    .after('<button type="submit" class="btn"><i class="icon-search"></i></button>');
                },
            "fnDrawCallback": function( oSettings ) {
                $('#table_lists_paginate').addClass('pagination');
           
                if ($('#table_lists_paginate ul').length == 0){
                    $('#table_lists_paginate').children().wrapAll('<ul/>');
                    $('#table_lists_paginate ul>a').wrap('<li/>');
                }
                
                $('#table_lists_paginate li.page_number').remove(); 
                $('#table_lists_paginate span>a').wrap('<li class="page_number"/>'); 
                $('#table_lists_paginate ul>li').eq(1).after($('#table_lists_paginate span li'));
                $('#table_lists_paginate span').hide(); 
                $('#table_lists_paginate a.paginate_active').parent().addClass('active');
             }
        });
    } 
        
});
