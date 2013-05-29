$(function() {
    //multi line elements in forms
    
    $('form .collection').each(function(){
        
        var collection = $(this);
        collection.find('tr.populate input').attr('readonly','true');

        //multi line element for edit list (several questions)
        collection.find('.add_item_to_collection_button').click(function(e){
            e.preventDefault();
            collection.find('thead').show();
            var currentCount = collection.find('tr > input').length;

            var template = collection.find('span').data('template');
            template = template.replace(/__index__/g, currentCount);

            var addedPlace = collection.find('.added_elements');
            addedPlace.append(template);
            addedPlace.find('label').remove();
            addedPlace.find('input').slice(1).wrap('<td>');
            addedPlace.append('<td class="btn-group"><div class="btn-group"><a class="btn btn-mini collection remove-line"><i class="icon-remove"></i></a></div></td>');
            addedPlace.wrapInner('<tr>');
            collection.find('tbody').append(addedPlace.find('tr'));            
            
            collection.find('.remove-line').last().click(function(e){
                $(e.currentTarget).parents('tr').first().remove();
            });
            
            
        });
    });
});
