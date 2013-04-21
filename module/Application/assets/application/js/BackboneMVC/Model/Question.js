window.Question = Backbone.Model.extend({
    
    initialize: function() {
    },
    
    defaults: {
        id:  undefined ,
        text: undefined,
        answer: undefined,
        tip: undefined
    }
    
});

window.Questions = Backbone.Collection.extend({
    
    initialize: function() {
        
    },
    
    
    
    model: Question,
    
    

    initQuestions: function (list){
        //localId is the id within the list (show to the student)
        var localId = 1;
        
        _.each(list,function(question){
            
            //replace the question marks in the text
            var img = '<div class="answer-location"><img class="img-find" src="/images/icons/find.png" alt="icon-hole" style="max-height:30px"/></div>';
            var patternInlineSolution = /%[^%]*%/;
            var text = _.escape(question.text);
            var answers = [];            
            
            //put answers in an array
            if (patternInlineSolution.test(text)){
                while (patternInlineSolution.test(text)){
                    var solutionPart = (patternInlineSolution.exec(text)[0]).replace(/%/,'').replace(/%/,'');                

                    if (!(/--/).test(solutionPart)){
                        answers.push(solutionPart);
                    } else {
                         answers.push(solutionPart.match(/[^(--)]+/g));
                    }

                    //replace the %text by a question mark icon
                    text = text.replace(patternInlineSolution,img);
                }  
                
                question.answer = answers;
            }
            question.text = text;      
            question.localId = localId;
            
            localId++;
        }); 
        
        this.add(list);
    }
});


