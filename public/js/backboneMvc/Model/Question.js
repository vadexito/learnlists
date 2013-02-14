window.Question = Backbone.Model.extend({
    
    initialize: function() {
        this.resetStat();
    },
    
    resetStat: function (){
        this.set('answered',false);
        this.set('answer_asked',false);
        this.set('multiple',false);
    },
    defaults: {
        text: 'undefined',
        answer: 'undefined',
        tip: 'undefined'
    },
    
    urlRoot: "/question-rest"
    
});

window.Questions = Backbone.Collection.extend({
    
    initialize: function() {
    },
    
    init:function(){
        this.answered = 0;
        this.answer_asked = 0;
        this.multiple = 0; 
    },
    
    
    model: Question,
    
    getNewRandomModel: function(currentId){
        
        if (this.randomOrder){
            if (this.randomOrder[0] === currentId
                && this.randomOrder.length > 1 ){               
                return this.get(this.randomOrder[1]);   
            } else {
                return this.get(this.randomOrder[0]);    
            }
        } else {
            return false;
        }
        
    },

    url: "/question-rest"
});


