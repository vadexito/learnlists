$(function() {
    
    //focus on home page and search url
    if ($('#search').length > 0){
        if ($('.home').length > 0){
            $('#search').focus();
        }
        
        $('#searchForm').submit(function(e){
            var url = $('#searchForm').attr('action')
                +'?search='
                +$('#search').val();
            $('#searchForm').attr('action',url);
        });
        
        $('#authorName_0').click(function(e){
            var href = $(e.currentTarget).attr('data-href');
            window.location.href = href;
        });
        
        if ($('.checkbox-filter').length >0) {
            
            $('.checkbox-filter').click(function(e){
                var href = $(e.currentTarget).attr('data-url');
                window.location.href = href;
            });
        }
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
    
    if ($('#questionNb').length >0){
        //slide in show list of lists page
        var rangeMin = $('#questionNb').parents('.filter-choice').first().children('.min-slider').first();
        var rangeMax = $('#questionNb').parents('.filter-choice').first().children('.max-slider').first();
        var updateSlider = function(range){        
            var patt1 = /^(\d)?\d,/;
            var patt2 = /,(\d)?\d$/;        
            rangeMin.html(range.replace(patt2,''));
            rangeMax.html(range.replace(patt1,''));
        };
        $('#questionNb').hide().slider().on('slide', function(e){
            updateSlider($(e.currentTarget).val());
        });
        rangeMin.html($('#questionNb').attr('data-slider-min'));
        rangeMax.html($('#questionNb').attr('data-slider-max'));
    }
        
    
    //table for the lists
    if ($("table.listquest_table").length > 0){
        var idTable = $('table').attr('id');
        $("table").dataTable({
            "iDisplayLength": 10,
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
                  $('#'+idTable+'_filter input').attr('placeholder',$('#keywords-bar').attr('data-placeholder'));
                  $('#keywords-bar').append($('#'+idTable+'_filter input'));
                  
                  
                  
//                $('#search_hidden').hide();
//                $('#'+idTable+'_filter input').attr('placeholder',$('#search_hidden input').attr('placeholder'))
//                    .unwrap().wrap('<div class="input-append"/>')
//                    .after('<button type="submit" class="btn"><i class="icon-search"></i></button>');
                },
            "fnDrawCallback": function( oSettings ) {
                $('#'+idTable+'_paginate').addClass('pagination pull-right');
           
                if ($('#'+idTable+'_paginate ul').length == 0){
                    $('#'+idTable+'_paginate').children().wrapAll('<ul/>');
                    $('#'+idTable+'_paginate ul>a').wrap('<li/>');
                }
                
                $('#'+idTable+'_paginate li.page_number').remove(); 
                $('#'+idTable+'_paginate span>a').wrap('<li class="page_number"/>'); 
                $('#'+idTable+'_paginate ul>li').eq(1).after($('#'+idTable+'_paginate span li'));
                $('#'+idTable+'_paginate span').hide(); 
                $('#'+idTable+'_paginate a.paginate_active').parent().addClass('active');
                
                $('#search-pagination').append($('#'+idTable+'_paginate'));
                $('#search-results').append($('#'+idTable+'_info'));
             }
        });
    } 
    
    //multi line elements in forms
    var collection = $('#questions_element');
    collection.find('tr.populate input').attr('readonly','true');
    //multi line element for edit list (several questions)
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
    
    //multi line elemnet for create list
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
    
    //edit list page
    $('a.edit-question').click(function(e){
        e.preventDefault();
        $(e.currentTarget).parents('tr').find('input').removeAttr('readonly');
        $(e.currentTarget).hide();        
    });
    
    
});
