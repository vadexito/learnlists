window.CurrentStats = Backbone.Model.extend({
    
    initialize: function(){
        
        learnMVC.vent.on("learn:addAnsweredQuestion",function(answerType){
            
            
        },this);    
        
        learnMVC.vent.on("learn:initNewRound",function(){ 
            
        },this);
    },
    
    defaults: {
        
    }
}); 