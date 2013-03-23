window.Round = Backbone.Model.extend({
    
    initialize: function() {
        learnMVC.vent.on('learn:removeRounds',function(listId){
            var options = {data:{listquestId:listId}};
            this.destroy(options);
        },this);
        learnMVC.vent.on('learn:showResult',function(){
            this.initStat();
        },this);
    },
    
    initStat: function(){
        
        var res = this.get('questionresults');
        
        if(res){
            
            var total = res.models.length,
                answerTypeNb = {},
                givenPointTable = {
                    'false' : 0,
                    '1' : 4,
                    '2' : 3,
                    '3' : 2,
                    '4' : 1,
                    '5' : 0
                },
                totalPotentialPoint = 0,
                totalPoint = 0;
                
            _.each(['false','1','2','3','4','5'],function(answerType){
                answerTypeNb[answerType] = res.where({answerType:answerType}).length;
                totalPoint+=givenPointTable[answerType]*answerTypeNb[answerType];
            });
            
            totalPotentialPoint+= givenPointTable['1'] * (total - answerTypeNb['false']);
            //console.log(totalPotentialPoint);
            
            
            var sum = 0;
            var perc = function(num,den,perc){
                if ($.isArray(num)){
                    _.each(num,function(el){
                       sum+=el; 
                    })
                    num = sum;
                }
                
                if (perc === false){
                    return parseInt(num / den * 100);
                }
                return parseInt(num / den * 100)+'%'
            };
            
            this.set({
                finalnote:perc(totalPoint,totalPotentialPoint,false),
                perfectanswer:perc(answerTypeNb[1],total),  
                averageanswer:perc([answerTypeNb[2],answerTypeNb[3],answerTypeNb[4]],total),                      
                badanswer:perc(answerTypeNb[5],total),  
                notdone:perc(answerTypeNb['false'],total)  
            });
            
            if (this.get('endDate')){
                this.set('duration',this.countDuration(new Date() - new Date(this.get('endDate').date)));
            }
        };
    },
    urlRoot: "/round-rest",
    
    defaults:{
      finalnote:0,
      perfectanswer:0,  
      averageanswer:0,  
      badanswer:0,  
      notdone:0,
      duration:{days:0,hours:0,minutes:0,seconds:0}
    },
    
    saveDB: function(){
        
        this.set('endDate', {date:new Date()});
        this.save(['listquestId','startDate','endDate'],{success: function(round){             
            var roundId = round.get('id');            
            round.get('questionresults').each(function(result){
                result.set('roundId',roundId);
                result.save();                
            });
        }});
    },
    
    countDuration: function(duration){
        var seconds,minutes,hours,days;
        //duration in ms
        seconds = Math.floor(duration/1000);
        
        days = Math.floor(seconds/(24 * 60 * 60));
        hours = Math.floor((seconds - days * 24 * 60 * 60) /(60 * 60));
        minutes = Math.floor((seconds - days * 24 * 60 * 60 - hours * 60 * 60) /60);
        seconds = Math.floor(seconds - days * 24 * 60 * 60 - hours * 60 * 60 - minutes * 60);
        
        return {
            days : days,
            hours : hours,
            minutes : minutes,
            seconds : seconds,
            now: (days == 0 && hours == 0 && minutes == 0 && seconds == 0)
        };
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



