window.QuestionView = Backbone.View.extend({
    
    el:'#question',
    
    model: Question,
    
    collection : new Questions(),
    
    events:{
        'click #question_asked_submitbutton'       : 'checkAnswer',
        'click #question_asked_nextbutton'         : 'newQuestion',
        'click #question_asked_resetbutton'        : 'resetList',
        'click #question_asked_showResults'        : 'showResults',
        'click #question_asked_showanswerbutton'   : 'showAnswer'
    },
    
    initialize: function() {
        
        this.listId = $('#listId').val();        
        this.answer = $('#question_asked_answer');
        this.text = $('#question_asked_text');
        
        //add eventlistener if the model changes
        this.listenTo(this.collection,'change:answered',this.updateStatRightAnswer);        
        this.listenTo(this.collection,'change:answer_asked',this.updateStatAnswerAsked);        
        this.listenTo(this.collection,'change:multiple',this.updateStatMultiple);   //multiple corresponds to question for which at least one wrong answer was given     
        
        //init list and questions
        var list = new Listquest({id:this.listId});
        list.fetch({success: $.proxy(function(){
            this.initTextAnswersandLocalId(list);
            this.collection.add(list.get('questions'));
            
            $('#title-list').html(_.escape(list.get('title')));
            $('#rules').html(_.escape(list.get('rules')));
            this.resetRound();
        },this)});
    },
    
    initTextAnswersandLocalId: function (list){
        //localId is the id within the list (show to the student)
        var localId = 1;
        
        _.each(list.get('questions'),function(question){
            
            var img = '<img class="img-find" src="/images/icons/find.png" alt="icon-hole" style="max-height:30px"/>';
            var patternInlineSolution = /%[^%]*%/;
            var text = _.escape(question.text);
            var answers = [];            
            
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
            question.text = localId+'. '+ text;      
            question.localId = localId;
            
            localId++;
        }); 
        
    },
    
    
    resetRound:function () {
        
        $('#round-results').hide();
        $('#question-asking').show();
        $('#question_asked_resetbutton').hide();
        $('#question_asked_showResults').show();
        
        var collection = this.collection; 
        collection.init();
        _.each(collection.models,function(model){
            model.resetStat();
        });
        collection.randomOrder = _.shuffle(collection.pluck('id')); 
        
        $('#percent-firsttime,#percent-noanswerused').html(0);
        $('#nb-questions').html(collection.length);
        $('#bar-progress').html('').css('width','0');
        
        //init new round in the database        
        var rounds = new Rounds({});
        this.currentRound = {startDate:new Date()};
        rounds.fetch({
            data:{
                listquestId:this.listId
            }, 
            success: $.proxy(function(){
                
                this.lastRounds = rounds;                
                $('#round-number').html(rounds.models.length+1);
            },this)
        });
        
        this.initNewQuestion();
    },
    
    updateStatRightAnswer: function(model,value){
        
        if (!value){
           return; 
        }
        
        var l = this.collection.length;
        var a = ++this.collection.answered;
        var withoutAnswer = a - this.collection.answer_asked;
        var firstTime = a - this.collection.multiple;
        
        var percent = a / l;
        
        if (percent > 0.2){
            $('#bar-progress').html(Math.round(percent*100)+'%')
                .css('width',percent*100+'%');
        }
        
        $('#percent-firsttime').html(Math.round( firstTime / l *100));
        $('#percent-noanswerused').html(Math.round( withoutAnswer / l *100));
        
        if (percent === 1){
            this.endList();
        }

    },
    
    updateStatAnswerAsked: function(model,value){
        
        if (value){
            this.collection.answer_asked++;
        }        
    },
    
    updateStatMultiple: function(model,value){
        
        if (value){
            this.collection.multiple++;
        }        
    },   
    
    checkAnswer: function(e){
        e.preventDefault();
        
        var value = this.answer.val();
        var question = this.collection.get(this.model.get('id'));
        var answers = question.get('answer');
        
        var answersToCheck;
        
        if (typeof answers === 'string'){        
            answersToCheck = answers;    
        } else if ($.isArray(answers)){            
            answersToCheck = answers[this.answerPart];
        }
        
        //the answer is true   
        if (this.isAnswerRight(value,answersToCheck)){
            
            $('#error-sign').hide();
            $('#check-sign').show();
            $('#answer-group').addClass('success');
            
            // if the answer has only one part
            if (typeof answers === 'string'){        
                this.questionAnswered(question); 
            // question with multi part answers
            } else if ($.isArray(answers)){ 
                
                this.text.html(this.replaceImg(this.text.html(),answersToCheck));
                this.answerPart++;
                
                if (answers.length === this.answerPart){
                    this.questionAnswered(question);
                } else {
                    this.answer.val('');
                }
            }

        //the answer is false
        } else {
            $('#error-sign').show();
            $('#answer-group').addClass('error');
            question.set('multiple',question.get('multiple') + 1);
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
    
    questionAnswered: function(question){
        
        var answerType = true;
        var timeAnswer = new Date();
        var timeToAnswer = (timeAnswer - this.timeNewQuestion)/1000;
        
        // 1: right answer provided in less than 30s
        // 2: right answer provided in more than 30s
        // 3: right answer provided after at least one mistake
        // 4: right answer provided after more than one mistake
        // 5: no right answer provided and asked for answer 
        
        if ((timeToAnswer < 30) && !question.get('multiple') 
            && !question.get('answer_asked')){
            answerType = 1;
        } else if (!question.get('multiple') && !question.get('answer_asked')){
            answerType = 2;
        } else if ((question.get('multiple') === 1) && !question.get('answer_asked')) {
            answerType = 3;
        } else if (!question.get('answer_asked')) {
            answerType = 4;
        } else {
            answerType = 5;
        }
        
        question.set('answered',answerType);
        
        //deleting the id of the found element from the randomOrder array
        this.collection.randomOrder = _.without(
            this.collection.randomOrder,question.get('id')
        );

        this.questionFinished();
    },
    
    questionFinished: function(){
        
        $('#question_asked_showanswerbutton,#question_asked_submitbutton').attr('disabled','disabled');
        if (this.model.get('tip')){
            $('#tip_text').html(this.model.get('tip'));
            $('.tip').show(); 
        }
        
        this.answer.attr('readonly','readonly');
        $('#nextbutton').focus();
            
    },
    
    showAnswer: function(){
        var question = this.collection.get(this.model.get('id'));   
        var answer = question.get('answer');
        
        
        if (typeof answer === 'string'){
            this.answer.val(_.escape(question.get('answer')));   
        }        
        
        if ($.isArray(answer)){
            var text = question.get('text');
            for (var i = 0, iMax = answer.length; i < iMax; i++){
                text = this.replaceImg(text,answer[i]);
            };
            
            this.text.html(text);
        }    
        
        //remember that for this question an answer was asked
        question.set('answer_asked',true);     
        
        this.questionAnswered(question);
    },
    
    newQuestion: function(e){
        e.preventDefault(); 
        this.initNewQuestion();
        
    },
    
    initNewQuestion: function(){
        $('#answer-group').removeClass('error').removeClass('success');
        $('.answer-sign,.tip').hide();
        
        var newQuestion = this.collection.getNewRandomModel(this.model.get('id'));
        
        //reset index for part of answers (for multipart answers)
        this.answerPart = 0;
        
        if (newQuestion) {
            
            this.timeNewQuestion = new Date();
            this.model.set({'id': newQuestion.get('id')});
            this.model.set({'tip': newQuestion.get('tip')});
            
            //insert the text of question
            this.text.html(newQuestion.get('text'));
            
            $('.button-answer').removeAttr('disabled');
            this.answer.val('').focus().removeAttr('readonly'); 
        } else {
            this.endList();
        }
    },
    
    
    endList: function(){
        
        this.answer.attr('readonly','readonly');
        $('.button-answer').attr('disabled','disabled');
        $('#nextbutton').attr('disabled','disabled');
        $('#question_asked_showResults').focus();
        
        //save round
        var round = new Round({
            listquestId: this.listId,
            startDate: this.currentRound.startDate,
            endDate: new Date()
         });    
        round.save({},{success: $.proxy(function(){              
            _.each(this.collection.models,function(question){
                
                (new Questionresult({
                    questionId:question.get('id'),
                    roundId:round.get('id'),
                    answerType:question.get('answered')    
                })).save();
            });
        },this)});
    },
    
    resetList: function(e){
        e.preventDefault();         
        this.resetRound();
    },
    
    showResults: function(e){
        e.preventDefault(); 
        $('#question-asking').hide();
        var results = $('#round-results'); 
        var tbody = results.children().find('tbody');
        var thead = results.children().find('thead');
        thead.html('');
        tbody.html('');
        
        var now = new Date();
        var resultArray = {};
        var localId = {};
        
        _.each(this.collection.models, function(question){
            localId[question.get('id')] = question.get('localId'); 
            resultArray[question.get('id')] = [
                {
                    answer : question.get('answered'),
                    date : now,
                    localDate:true
                }
            ]
        });
        
        _.each(this.lastRounds.models,function(round){
            _.each(round.get('questionresults'),function(questionresult){
                resultArray[questionresult.questionId].push({
                    answer : parseInt(questionresult.answerType),
                    date : new Date(round.get('startDate').date)
                });
            });
        });
        
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
            _.each(row,function(element){
                
                var date_futureUGC,date_pastUGC,duration;   
                
                date_futureUGC = now.getTime() + (element.localDate ? 0 : 1) * now.getTimezoneOffset()*60000;
                date_pastUGC = element.date.getTime();                    
                duration = this.countDuration(date_futureUGC - date_pastUGC);
                
                newRowContent+= '<td>' + element.answer +'</td>';
                
                if (!newHead){
                    
                    newHeadContent+= '<th>'+(duration.now ? 'now' : 
                        (duration.days ? (duration.days + 'd') : '')
                        + (duration.hours ? (duration.hours + 'h') : '')
                        + (duration.minutes ? (duration.minutes + 'm') : '')
                        + (duration.seconds ? (duration.seconds + 's') : '')+' ago')
                        +'</th>';
                }
                
            },this);
            newRow = '<tr>' + newRowContent + '</tr>';  
            //add the line to the table
            tbody.append(newRow);
            
            if (!newHead){
                newHead = '<tr>' + newHeadContent + '</tr>';
                thead.append(newHead);
            }
        },this);
        
        
        results.show();
        $(e.currentTarget).hide();
        $('#question_asked_resetbutton').show().focus();
        
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


