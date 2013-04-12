$(function() {
    
    //focus on home page
    if ($('#tags').length > 0){
        $('#tags').focus();
    }
    
    
    
    
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
    
    
    var collection = $('#questions_element');
    collection.find('tr.populate input').attr('readonly','true');
    
    $('#add_questions_button.add_item_to_collection_button').click(function(e){
        e.preventDefault();
        
        var collectionId = $(e.currentTarget).data('collection');        
        var currentCount = $('#'+ collectionId+ ' tbody tr').length;
        
        var template = $('#'+ collectionId+ '_adding > span').data('template');
        template = template.replace(/__index__/g, currentCount);
        
        var addedPlace = $('#'+ collectionId+' .added_elements');
        addedPlace.append(template);
        addedPlace.find('label').remove();
        addedPlace.find('input').slice(1).wrap('<td>');
        addedPlace.wrapInner('<tr>');
        
        $('#'+ collectionId).find('tbody').append(addedPlace.find('tr'));
        
    });
    $('#add_tags_button.add_item_to_collection_button').click(function(e){
        e.preventDefault();
        var collectionId = $(e.currentTarget).data('collection');    
        var collection = $('#'+collectionId);
        
        var currentCount = collection.find('input').length;
        
        var template = $('#'+ collectionId+ ' > span').data('template');
        template = template.replace(/__index__/g, currentCount);
        
        collection.append(template);        
        collection.find('label').slice(1).hide();
    });
});
