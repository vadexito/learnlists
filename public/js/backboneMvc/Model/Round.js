window.Round = Backbone.Model.extend({
    
    initialize: function() {
        learnMVC.vent.on('learn:removeRounds',function(listId){
            var options = {data:{listquestId:listId}};
            this.destroy(options);
        },this);
    },
    
    urlRoot: "/round-rest",
    
    saveDB: function(){
        
        this.set('endDate', {date:new Date()});
        this.save(['listquestId','startDate','endDate'],{success: function(round){             
            var roundId = round.get('id');            
            round.get('questionresults').each(function(result){
                result.set('roundId',roundId);
                result.save();                
            });
        }});
    }
});

window.Rounds = Backbone.Collection.extend({
    
    initialize: function() {
        
    },
    
    model: Round,
    
    init: function(listId,maxRound){
        
        this.fetch({
            data:{
                listquestId:listId
            }, 
            success: function(rounds){                
                //transform questionresults into a collection
                rounds.each(function(round){
                    round.set('questionresults',new Questionresults(round.get('questionresults','')));
                });
                
                learnMVC.vent.trigger("learn:initNewRound");
                
            }
        });
        
        
        
    },
    
    url: "/round-rest"
    
});



