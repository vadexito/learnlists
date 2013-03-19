window.FollowerView = Backbone.Marionette.ItemView.extend({
    template: "#title_pie-template",
    tagName: 'div',    
    className: 'well span12 pie',
    ui:{
        pie_input:'#current_pie'
    },
    initialize: function(){
        this.listenTo(this.model,'change:nb_question',function(){
            var total = this.model.get('nb_questions');
            this.render();
            $("#current_pie").knob({
                'min':0,
                'max':total,
                'step':1,
                'readOnly':true,
                'width':80,
                'height':80
            })
             $("#current_pie").val(total - this.model.get('nb_question'))
                              .trigger('change'); 
        });
    }
});


