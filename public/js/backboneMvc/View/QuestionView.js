window.QuestionView = Backbone.View.extend({
    
    el:'#question',
    
    //contains the current question
    model: Question,
    
    //contains the question of the list
    collection : new Questions(),
    
    events:{
        'click #question_asked_submitbutton'       : 'checkAnswer',
        'click #question_asked_nextbutton'         : 'newQuestion',
        'click #question_asked_resetbutton'        : 'resetList',
        'click #question_asked_cancelRound'        : 'cancelRound',
        'click #question_asked_showanswerbutton'   : 'showAnswer',
        'click #remove_rounds_button'              : 'removeRounds'
    },
    
    initialize: function() {
        this.maxRound = 5;
        var listId = $('#listId').val();        
        this.answer = $('#question_asked_answer');
        this.text = $('#question_asked_text');
        
        //init list and questions
        this.list = new Listquest({id: listId});
        this.list.fetch({success: $.proxy(function(list){
            
            this.collection.initQuestions(list.get('questions'));  
            var nb_questions = this.collection.length;
            $('#nb-questions').html(nb_questions);
            $('#title-list').html(_.escape(list.get('title')));
            $('#rules').html(_.escape(list.get('rules')));
            
            learnMVC.vent.trigger("learn:init",nb_questions);
            
            this.lastRounds = new Rounds();
            
            //TODO : not to do if not connected
            this.lastRounds.init(listId,this.maxRound);
            
            this.initNewRound();
        },this)});
    },
    
    initNewRound:function () {  
        learnMVC.vent.trigger("learn:initNewRound");
        $('#round-results').hide();
        $('#question-asking').show();
        $('.reset_button').hide();
        $('#question_asked_cancelRound').show();
        $('#percent-firsttime,#percent-noanswerused').html(0);
        $('#bar-progress').html('').css('width','0');
        
        //if it not the first time after initialization
        if (this.currentRound){
            $('#round-number').html((this.lastRounds.models.length+1)+'/'+this.maxRound);
        }
        
        this.collection.newRoundInit();
        
        //fetch former rounds from the database and create new Round       
        this.currentRound = new Round({
            listquestId: this.list.get('id'),
            startDate: {date:new Date()},
            questionresults : new Questionresults(),
            localDate:true
        });
        
        this.listenTo(this.currentRound.get('questionresults'),'add',this.questionFinished);        
        this.initNewQuestion();
    },
    
    removeRounds: function(){
        var options = {
            data:{
                listquestId:this.list.get('id')
            }, 
            success: function(){
            }
        };
        
        while(this.lastRounds.models.length > 0){
            this.lastRounds.models[0].destroy(options);
        }
        
        $('#round-number').html('1/'+this.maxRound);
        this.initNewRound();
        
    },
    
    
//    updateStatRightAnswer: function(model,value){
//        
//        if (!value){
//           return; 
//        }
//        
//        var l = this.collection.length;
//        var a = ++this.collection.answerType;
//        var withoutAnswer = a - this.collection.answer_asked;
//        var firstTime = a - this.collection.multiple;
//        
//        var percent = a / l;
//        
//        if (percent > 0.2){
//            $('#bar-progress').html(Math.round(percent*100)+'%')
//                .css('width',percent*100+'%');
//        }
//        
//        $('#percent-firsttime').html(Math.round( firstTime / l *100));
//        $('#percent-noanswerused').html(Math.round( withoutAnswer / l *100));
//        
//        if (percent === 1){
//            this.listCompleted();
//        }
//
//    },
//    
//    updateStatAnswerAsked: function(model,value){
//        
//        if (value){
//            this.collection.answer_asked++;
//        }        
//    },
//    
//    updateStatMultiple: function(model,value){
//        
//        if (value){
//            this.collection.multiple++;
//        }        
//    },   
    
    checkAnswer: function(e){
        e.preventDefault(); 
        $('.answer-sign').hide();
        var newResult = new Questionresult({
            startDate:this.model.get('startDate'),
            questionId:this.model.get('id')
        });  
        
        var result = newResult.checkAnswer({
            answerGiven: this.answer.val(),
            answerInDB: this.model.get('answer'),
            answerPart: this.model.get('answerPart')
        });
        
        if (result === true){
            this.currentRound.get('questionresults').add(newResult);
            learnMVC.vent.trigger("learn:addAnsweredQuestion",newResult.get('answerType'));
           
        //only a part of the answer has been given
        } else if (typeof result === 'number'){
            this.model.set('answerPart',result);
        }
    },
    
    
    questionFinished: function(){
        
        //deleting the id of the found element from the roundOrder array
        this.collection.roundOrder = _.without(
            this.collection.roundOrder,this.model.get('id')
        );
        
        if (this.model.get('tip')){
            $('#question_tip').show();
            $('#tip_text').html(this.model.get('tip'));
            $('.tip').show(); 
        }
        
        $('#question_asked_showanswerbutton,#question_asked_submitbutton').attr('disabled','disabled');
        this.answer.attr('readonly','readonly');
        $('#nextbutton').focus();
            
    },
    
    showAnswer: function(e){
        e.preventDefault(); 
        
        var question = this.model;   
        var answer = question.get('answer');
        
        
        if (typeof answer === 'string'){
            this.answer.val(_.escape(answer));   
        }        
        
        if ($.isArray(answer)){
            var text = question.get('text');
            for (var i = 0, iMax = answer.length; i < iMax; i++){
                text = this.replaceImg(text,answer[i]);
            };
            
            this.text.html(text);
        }    
        
        
        //remember that for this question an answer was asked        
        var newResult = new Questionresult({
            questionId:this.model.get('id'),
            answer_asked:true
        });   
        newResult.saveAnswerType();
        this.currentRound.get('questionresults').add(newResult);
        learnMVC.vent.trigger("learn:addAnsweredQuestion",newResult.get('answerType'));
    },
    
    newQuestion: function(e){
        e.preventDefault(); 
        this.initNewQuestion();
        
    },
    
    initNewQuestion: function(){
        $('#answer-group').removeClass('error').removeClass('success');
        $('.answer-sign,#question_tip').hide();
        
        var newQuestion = this.collection.getNewRandomModel(this.model.get('id'));
        
        if (newQuestion) {
            this.model = newQuestion;
            
            this.model.set('startDate',new Date());
            this.model.set('answerPart',0);
            //insert the text of question
            this.text.html(this.model.get('text'));
            $('.button-answer').removeAttr('disabled');
            this.answer.val('').focus().removeAttr('readonly'); 
        } else {
            this.listCompleted();
        }
    },
    
    
    listCompleted: function(){
        learnMVC.vent.trigger("learn:roundCompleted");
        
        this.answer.attr('readonly','readonly');
        $('.button-answer').attr('disabled','disabled');
        $('#nextbutton').attr('disabled','disabled');
        
        this.currentRound.saveDB();
        this.lastRounds.add(this.currentRound); 
        
        this.showResult();
    
    },
    
    resetList: function(e){
        e.preventDefault();         
        this.initNewRound();
    },
    
    cancelRound: function(e){
        e.preventDefault(); 
        $('#question-asking').hide();
        $(e.currentTarget).hide();
        $('.reset_button').show().focus();
        
    },
    
    showResult: function(){
        $('#question-asking').hide();
        
        var results = $('#round-results');         
        var tbody = results.children().find('tbody');
        var thead = results.children().find('thead');
        var now = new Date();
        var resultArray = {};
        var localId = {};
        
        thead.html('');
        tbody.html('');
        
        this.collection.each(function(question){
            var questionId = question.get('id');
            
            localId[questionId] = question.get('localId');
            resultArray[questionId] = new Array();
        });
        
        
        var lastRounds = this.lastRounds;
        if (lastRounds){            
            lastRounds.each(function(round){
                
                round.get('questionresults').each(function(questionresult){
                    var answer = {
                        answer : parseInt(questionresult.get('answerType')),
                        date : new Date(round.get('endDate').date)
                    }
                    
                    if (round.get('localDate')){
                        answer['localDate'] = true;
                    }
                    
                    resultArray[questionresult.get('questionId')].push(answer);
                });
            }); 
        }
        
        var newHeadContent= '<th>#</th>';
        var newHead;
        var newRow;
        
        _.each(resultArray,function(row,index){
            
            // id local of each question
            var newRowContent = '<td>'+localId[index]+'</td>';
            delete row.localId;
            
            row = _.sortBy(row,function(element){ 
                return -(element.date.getTime() -  (element.localDate ? 0 : 1) * now.getTimezoneOffset()*60000);
            })
            
           //create a line for each question
           var i=row.length;
            _.each(row,function(element){
                
                var date_futureUGC,date_pastUGC,duration;   
                
                date_futureUGC = now.getTime() + (element.localDate ? 0 : 1) * now.getTimezoneOffset()*60000;
                date_pastUGC = element.date.getTime();                    
                duration = this.countDuration(date_futureUGC - date_pastUGC);
                
                newRowContent+= '<td>' + element.answer +'</td>';
                
                if (!newHead){
                    
                    newHeadContent+= '<th> round#' + i + ' - '
                        +(duration.now ? 'now' : 
                        (duration.days ? (duration.days + 'd') : '')
                        + (duration.hours ? (duration.hours + 'h') : '')
                        + (duration.minutes ? (duration.minutes + 'm') : '')
                        + (duration.seconds ? (duration.seconds + 's') : '')+' ago')
                        +'</th>';
                }
                i+=-1;
                
            },this);
            newRow = '<tr>' + newRowContent + '</tr>';  
            tbody.append(newRow);
            
            if (!newHead){
                newHead = '<tr>' + newHeadContent + '</tr>';
                thead.append(newHead);
            }
        },this);
        
        
        results.show();
        $('#question_asked_cancelRound').hide();
        
        if(this.lastRounds.models.length < this.maxRound){
            $('#question_asked_resetbutton').show().focus();
        }
        
        $('#remove_rounds_button').show();
        
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


