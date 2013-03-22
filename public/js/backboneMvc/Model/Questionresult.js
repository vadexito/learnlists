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
        answerType: false,
        answer_asked: false,
        multiple: 0,
        answerPart:0
    },
    
    urlRoot: "/questionresult-rest",
    
    setAnswerType: function(){
        var answerType = true;
        var timeAnswer = new Date();
        var timeToAnswer = (timeAnswer - this.get('startDate'))/1000;
        
        // 1: right answer provided in less than 30s
        // 2: right answer provided in more than 30s
        // 3: right answer provided after at least one mistake
        // 4: right answer provided after more than one mistake
        // 5: no right answer provided and asked for answer 
        
        if ((timeToAnswer < 30) && !this.get('multiple') 
            && !this.get('answer_asked')){
            answerType = 1;
        } else if (!this.get('multiple') && !this.get('answer_asked')){
            answerType = 2;
        } else if ((this.get('multiple') === 1) && !this.get('answer_asked')) {
            answerType = 3;
        } else if (!this.get('answer_asked')) {
            answerType = 4;
        } else {
            answerType = 5;
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
            
            
            $('#error-sign').hide();
            $('#check-sign').show();
            $('#answer-group').addClass('success');
            
            // if the answer has only one part
            if (typeof answerInDB === 'string'){   
                return true;
            // question with multi part answers
            } 
            
            if ($.isArray(answerInDB)){ 
                
                text.html(this.replaceImg(text.html(),answersToCheck));
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
            $('#error-sign').show();
            $('#answer-group').addClass('error');
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
    },
    
    replaceImg: function(initialText,replacingText,separator){
        
        if (!separator) {
            separator = '\\';
        }
        
        //choose separator for showing arrays
        if ($.isArray(replacingText)){
            replacingText = replacingText.join(separator);
        }
        
        return initialText.replace(/<img[^<>]*>/,
            '<span>'+replacingText+'</span>'
        );   
    }
    
});



window.Questionresults = Backbone.Collection.extend({
    
    initialize: function() {
        
    },
    
    url: "/questionresult-rest"
});
