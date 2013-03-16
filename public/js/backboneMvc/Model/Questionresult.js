window.Questionresult = Backbone.Model.extend({
    
    initialize: function() {        
    },
    
    resetStat: function (){
        this.set('answered',false);
        this.set('answer_asked',false);
        this.set('multiple',false);
    },
    defaults: {
        questionId: undefined,
        roundId: undefined,
        answerType: false
    },
    
    urlRoot: "/questionresult-rest"
    
});




