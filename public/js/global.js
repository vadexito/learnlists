$(function() {
    
    
    var loadingIconAndGoToRef = function(href){
        $('.modal').modal('show');
        window.location.href = href;
    }
    
    $('.checkbox-filter').click(function(e){
        loadingIconAndGoToRef($(e.currentTarget).attr('data-url'));
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
    
    //slider elements
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
            var name = $(e.currentTarget).attr('name');
            var rangeMin = $(self).find('.min-slider').first().html();
            var rangeMax = $(self).find('.max-slider').first().html();
            
            var patt1 = new RegExp('(.*'+name+'%5Bmin%5D'+'=)([0-9]*)(&'+name+'%5Bmax%5D'+'=)([0-9]*)(.*)');
            if (patt1.test(url)){                
                var matches = url.match(patt1);
                url = matches[1]+rangeMin+matches[3]+rangeMax+matches[5];
                
                loadingIconAndGoToRef(url);
                
            }
            
        });        
        //on initialization of the sliders
        updateSlider($(this).find('input').attr('data-slider-value'));
        
    });
   
    //simple search elements
    $('.filter-search-search').each(function(){
        var self=$(this);
        var input = self.find('input'); 
        var searchButton = $(this).find('button');
        searchButton.click(function(e){
            var url = input.attr('data-url');
            var name = input.attr('name');
            var value = input.val();
            
            var patt = new RegExp('(.*'+name+'%5B'+name+'%5D'+'=)(var)');
            var matches = url.match(patt);
            url = matches[1]+value;     
            if (matches[3]){
                url +=matches[3];
            }      
            loadingIconAndGoToRef(url);
        });
        
        input.keydown(function(e){
            if(e.which == 13) {
                searchButton.click();
            }
        });
    });
    
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
    
    
    
    //loading icon (layout)
    var opts = {
        lines: 11, // The number of lines to draw
        length: 8, // The length of each line
        width: 5, // The line thickness
        radius: 15, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 0, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: '#FFCB0F', // #rgb or #rrggbb
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: false, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: -10, // Top position relative to parent in px
        left: 50 // Left position relative to parent in px
      };
      var target = document.getElementById('loading-icon');
      new Spinner(opts).spin(target);
});
