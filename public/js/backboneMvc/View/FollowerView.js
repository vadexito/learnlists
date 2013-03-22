window.FollowerView = Backbone.Marionette.ItemView.extend({
    template: "#title_pie-template",
    tagName: 'div',    
    className: 'well span12 pie',
    ui:{
        pie:'#current_pie'
    },
    initialize: function(){
        
    },
    
    modelEvents:{
        'change:nb_question': function(model,newNb){
            var total = model.get('nb_questions');
            this.render();
            this.ui.pie.knob({
                'min':0,
                'max':total,
                'step':1,
                'readOnly':true,
                'width':80,
                'height':80
            })
            this.ui.pie.val(total - newNb).trigger('change'); 
        }
    }
    
    
});


