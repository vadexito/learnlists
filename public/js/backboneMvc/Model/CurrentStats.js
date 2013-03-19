window.CurrentStats = Backbone.Model.extend({
    
    initialize: function(){
        var self = this;
        
        learnMVC.vent.on("learn:init",function(nb_questions){            
            self.set('nb_questions',nb_questions);
        });
        learnMVC.vent.on("learn:addAnsweredQuestion",function(answerType){
            self.set('nb_question',parseInt(self.get('nb_question')) - 1);
            var total = self.get('nb_questions');
            var perfect = self.get('nb_perfect_answering') * total/100,
                average = self.get('nb_average_answering') * total/100,
                    bad = self.get('nb_bad_answering') * total/100;
                    
            switch(answerType){
                case 1:
                    perfect++;
                    break;
                case 2:
                    average++;
                    break;
                case 3:
                    average++;
                    break;
                case 4:
                    average++;
                    break;
                case 5:
                    bad++;
                    break;
                default:                    
            }
            
            self.set({
                'nb_perfect_answering':perfect/total * 100,
                'nb_average_answering':average/total  * 100,
                'nb_bad_answering':bad/total * 100
            });
        });        
        learnMVC.vent.on("learn:initNewRound",function(){ 
            self.set('nb_question',self.get('nb_questions'));
            self.set({
                'nb_perfect_answering':0,
                'nb_average_answering':0,
                'nb_bad_answering':0
            });
        });
    },
    defaults: {
        nb_perfect_answering:0,
        nb_average_answering:0,
        nb_bad_answering:0,
        nb_questions:0,
        nb_question:""
    }
}); 