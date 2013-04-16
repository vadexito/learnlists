$(function() {
    
    $('.checkbox-filter').click(function(e){
        window.location.href = $(e.currentTarget).attr('data-url');
    });
    
    $('#searchForm').submit(function(e){
        var url = $('#searchForm').attr('action')
            +'?search='
            +$('#search').val();
        $('#searchForm').attr('action',url);
    });
    
    //focus on home page and search url
    $('#search.home').focus();
    
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
    
    $('.filter-search-range').each(function(){
        
        var rangeMin = $(this).find('.min-slider').first();
        var rangeMax = $(this).find('.max-slider').first();
        
        var updateSlider = function(range){ 
            var patt1 = /^\[/;
            var patt2 = /\]$/;
            var patt3 = /(\[)?(\d)?\d,/;
            var patt4 = /,(\d)?\d(\])?/; 
            
            rangeMin.html(((range.replace(patt1,'')).replace(patt2,'')).replace(patt4,''));
            rangeMax.html(((range.replace(patt1,'')).replace(patt2,'')).replace(patt3,''));
        };
        
        $(this).find('input').hide().slider().on('slide', function(e){
            updateSlider($(e.currentTarget).val());
        });
        var self = this;
        $(this).find('input').hide().slider().on('slideStop', function(e){
            var url = $(e.currentTarget).attr('data-url');
            var nameMin = $(e.currentTarget).attr('data-filterNameMin');
            var nameMax = $(e.currentTarget).attr('data-filterNameMax');
            var rangeMin = $(self).find('.min-slider').first().html();
            var rangeMax = $(self).find('.max-slider').first().html();
            
            var patt1 = new RegExp('(.*'+nameMin+'=)([0-9]*)(&'+nameMax+'=)([0-9]*)(.*)');
            if (patt1.test(url)){
                var matches = url.match(patt1);
                url = matches[1]+rangeMin+matches[3]+rangeMax+matches[5];
                
                window.location.href = url;
            }
            
        });
        
        //an initialization
        updateSlider($(this).find('input').attr('data-slider-value'));
        
    });
   
        
    
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
