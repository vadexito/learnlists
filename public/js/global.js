$(function() {
    
    if (typeof learnMVC === 'object') {learnMVC.start();}
    
    if ($('a[data-toggle="tooltip"]').length >0){
        $('a[data-toggle="tooltip"]').tooltip();
    }
    if ($('a[data-toggle="popover"]').length >0){
        $('a[data-toggle="popover"]').popover();
    }
    
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
        
});
