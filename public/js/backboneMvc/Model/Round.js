window.Round = Backbone.Model.extend({
    
    initialize: function() {
        this.attributes.questionresults = new Questionresults();
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
    
    init: function(listId,maxRound){
        
        this.fetch({
            data:{
                listquestId:listId
            }, 
            success: function(rounds){                
                $('#round-number').html((rounds.models.length+1)+'/'+maxRound);
                //transform questionresults into a collection
                rounds.each(function(round){
                    round.set('questionresults',new Questionresults(round.get('questionresults','')));
                });
                
                
            }
        });
        
        
        
        
    },  
    
    url: "/round-rest"
    
});



