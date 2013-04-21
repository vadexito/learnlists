window.FollowerView = Backbone.Marionette.ItemView.extend({
    template: "#title_pie-template",
    tagName: 'div',    
    className: 'well span12 pie',
    ui:{
        pie:'#current_pie'
    },
    initialize: function(){
        
    },
    
    updateKnob: function(val){
        var optionsKnob = {
            'min':0,
            'max':this.model.get('nb_questions'),
            'step':1,
            'readOnly':true,
            'width':80,
            'height':80
        };
        
        this.ui.pie.knob(optionsKnob)
        this.ui.pie.val(val).trigger('change'); 
    },
    
    onRender: function(){        
        if (this.model.get('loggedIn') === 'false'){ 
            $('#round_show').hide();
        }
        
        this.updateKnob(this.model.get('nb_question'));
    },
    
    
    modelEvents:{
        'change:nb_question change:comments change:round_nb change:round_total': 'render'
    }
    
    
});


