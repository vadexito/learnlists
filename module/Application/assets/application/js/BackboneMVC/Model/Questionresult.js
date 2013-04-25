window.Questionresult = Backbone.Model.extend({
    
    initialize: function() {        
    },
    
    resetStat: function (){
        this.set('answerType',false);
        this.set('answer_asked',false);
        this.set('multiple',false);
        this.set('answerPart',0);
    },
    
    defaults: {
        questionId: undefined,
        roundId: undefined,
        answerType: 'false',
        answer_asked: false,
        multiple: 0,
        answerPart:0
    },
    
    urlRoot: "/learnlists-rest/questionresult",
    
    setAnswerType: function(timePerQuestion){ //time for one question in seconds
        if (!timePerQuestion){
            timePerQuestion = 10;
        }
        
        var answerType = true;
        var timeAnswer = new Date();
        var timeToAnswer = (timeAnswer - this.get('startDate'))/1000;
        
        // 1: right answer provided in less than the time per question
        // 2: right answer provided in more than the time per question
        // 3: right answer provided after at least one mistake
        // 4: right answer provided after more than one mistake
        // 5: no right answer provided and asked for answer 
        
        if ((timeToAnswer < timePerQuestion) && !this.get('multiple') 
            && !this.get('answer_asked')){
            answerType = '1';
        } else if (!this.get('multiple') && !this.get('answer_asked')){
            answerType = '2';
        } else if ((this.get('multiple') === 1) && !this.get('answer_asked')) {
            answerType = '3';
        } else if (!this.get('answer_asked')) {
            answerType = '4';
        } else {
            answerType = '5';
        }
        
        this.set('answerType',answerType);        
        return this;
    },
    
    checkAnswer: function(options){
        
        var answerGiven = options.answerGiven,
            answerInDB = options.answerInDB,
            answerPart = options.answerPart,
            text = $('#question_asked_text'),
            answer = $('#question_asked_answer'),
            answersToCheck; 
        
        if (typeof answerInDB === 'string'){        
            answersToCheck = answerInDB;    
        } else if ($.isArray(answerInDB)){            
            answersToCheck = answerInDB[answerPart];
        }
        
        //the answer is true   
        if (this.isAnswerRight(answerGiven,answersToCheck)){
            
            learnMVC.vent.trigger("learn:answerSuccess");
            
            // if the answer is not in the sentence
            if (typeof answerInDB === 'string'){   
                return true;
            
            } 
            // if answer is in the sentence
            if ($.isArray(answerInDB)){ 
                
                var firstAnswer = $('.answer-location.hiddenanswer').first();
                var answerValue = firstAnswer.attr('data-answer');
                
                firstAnswer.removeClass('hiddenanswer').addClass('animated bounceInLeft');     
                firstAnswer.html('<span class="right-answer">'+answerValue+'</span>');
                answerPart++;
                
                if (answerInDB.length === answerPart){                    
                    return true;
                } else {
                    answer.val('');
                    return answerPart;
                }
            }
        //the answer is false
        } else {
            learnMVC.vent.trigger("learn:answerError");
            this.set('multiple',this.get('multiple') + 1);
            return false;
        }
    },
    
    isAnswerRight: function(value,answer){
        
        if (!answer){
            return false;
        }
        
        if (typeof answer === 'string'){
            return value === answer;
        }
        
        if ($.isArray(answer)){
            return ($.inArray(value,answer) > -1);
        }
    }
});



window.Questionresults = Backbone.Collection.extend({
    
    initialize: function() {
        
    },
    
    comparator: function(questionresult){
        return -parseInt(questionresult.get('answerType'));
    },
    
    url: "/learnlists-rest/questionresult"
});
