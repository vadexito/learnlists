window.QuestionView = Backbone.View.extend({
    
    el:'#question',
    
    
    
    showResult: function(){
        $('#question-asking').hide();
        
//        var results = $('#round-results');         
//        var tbody = results.children().find('tbody');
//        var thead = results.children().find('thead');
//        var now = new Date();
//        var resultArray = {};
//        var localId = {};
//        
//        thead.html('');
//        tbody.html('');
//        
//        this.collection.each(function(question){
//            var questionId = question.get('id');
//            
//            localId[questionId] = question.get('localId');
//            resultArray[questionId] = new Array();
//        });
//        
//        
//        var lastRounds = this.lastRounds;
//        if (lastRounds){            
//            lastRounds.each(function(round){
//                
//                round.get('questionresults').each(function(questionresult){
//                    var answer = {
//                        answer : parseInt(questionresult.get('answerType')),
//                        date : new Date(round.get('endDate').date)
//                    }
//                    
//                    if (round.get('localDate')){
//                        answer['localDate'] = true;
//                    }
//                    
//                    resultArray[questionresult.get('questionId')].push(answer);
//                });
//            }); 
//        }
//        
//        var newHeadContent= '<th>#</th>';
//        var newHead;
//        var newRow;
//        
//        _.each(resultArray,function(row,index){
//            
//            // id local of each question
//            var newRowContent = '<td>'+localId[index]+'</td>';
//            delete row.localId;
//            
//            row = _.sortBy(row,function(element){ 
//                return -(element.date.getTime() -  (element.localDate ? 0 : 1) * now.getTimezoneOffset()*60000);
//            })
//            
//           //create a line for each question
//           var i=row.length;
//            _.each(row,function(element){
//                
//                var date_futureUGC,date_pastUGC,duration;   
//                
//                date_futureUGC = now.getTime() + (element.localDate ? 0 : 1) * now.getTimezoneOffset()*60000;
//                date_pastUGC = element.date.getTime();                    
//                duration = this.countDuration(date_futureUGC - date_pastUGC);
//                
//                newRowContent+= '<td>' + element.answer +'</td>';
//                
//                if (!newHead){
//                    
//                    newHeadContent+= '<th> round#' + i + ' - '
//                        +(duration.now ? 'now' : 
//                        (duration.days ? (duration.days + 'd') : '')
//                        + (duration.hours ? (duration.hours + 'h') : '')
//                        + (duration.minutes ? (duration.minutes + 'm') : '')
//                        + (duration.seconds ? (duration.seconds + 's') : '')+' ago')
//                        +'</th>';
//                }
//                i+=-1;
//                
//            },this);
//            newRow = '<tr>' + newRowContent + '</tr>';  
//            tbody.append(newRow);
//            
//            if (!newHead){
//                newHead = '<tr>' + newHeadContent + '</tr>';
//                thead.append(newHead);
//            }
//        },this);
        
        
        $('#question_asked_cancelRound').hide();
        
        if(this.lastRounds.models.length < this.maxRound){
            $('#question_asked_resetbutton').show().focus();
        }
        
        $('#remove_rounds_button').show();
        
    }
    
    
    
});


