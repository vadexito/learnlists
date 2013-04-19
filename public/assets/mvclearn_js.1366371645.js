window.LearnMain = Backbone.Model.extend({
    
    initialize: function(){
        
        var listId = this.get('listId');
        var loggedIn = this.get('loggedIn');        
        var maxRound = this.get('maxRound');        
        if (!listId || !loggedIn || !maxRound){
            throw new Error("You must provide a listId, maxRound and loggedIn boolean in order to initialize Learnmain");;
        }
        
        new Listquest({id: listId}).fetch({success: $.proxy(function(list){
            
            //init list of questions
            this.questions = {
                listId : listId,
                maxRound:maxRound,
                collection:new Questions()
            };
            
            
            this.questions.collection.initQuestions(list.get('questions'));  
            
            learnMVC.vent.trigger("learn:init");
            
            this.set('title_list',list.get('title'));
            this.set('rules',list.get('rules'));
            this.set('round_total',this.questions.maxRound);
            this.set('nb_questions',this.questions.collection.length);
            this.set('nb_question',this.questions.collection.length);
            
            //init last rounds
            this.lastRounds = new Rounds();
            if (loggedIn === 'true'){                
                this.lastRounds.init(listId,maxRound);
            } else {
                //no last round to init if not logged
                learnMVC.vent.trigger("learn:initNewRound");
            }
        },this)});
    
        learnMVC.vent.on("learn:initNewRound",this.initNewRound,this);
        learnMVC.vent.on("learn:initNewQuestion",this.initNewQuestion,this);
        learnMVC.vent.on("learn:nextQuestion",this.nextQuestion,this);        
        learnMVC.vent.on("learn:answerCheck",this.checkAnswer,this);        
        learnMVC.vent.on("learn:showAnswer",this.showAnswer,this);        
        learnMVC.vent.on("learn:proceedAnsweredQuestion",this.proceedAnsweredQuestion,this);
        learnMVC.vent.on("learn:roundCompleted",this.roundCompleted,this);        
        
        
    },
    
    roundCompleted: function(){
        if (this.get('loggedIn') === 'true' || this.get('saveRoundsWhenNotLogged') === true ){
            this.currentRound.saveDB();
            this.lastRounds.add(this.currentRound);
        }
        
        learnMVC.vent.trigger("learn:showResult");

    },
    
    nextQuestion: function(){

        var roundOrder = this.currentRound.get('roundOrder');

        if (roundOrder.length > 0) {   
            learnMVC.vent.trigger("learn:initNewQuestion",_.first(roundOrder));
        } else {
            learnMVC.vent.trigger("learn:roundCompleted");
        }
    },
    
    checkAnswer: function(answerGiven){
        var question = this.questions.collection.get(this.model.get('questionId'));

        var result = this.model.checkAnswer({
            answerGiven: answerGiven,
            answerInDB: question.get('answer'),
            answerPart: this.model.get('answerPart')
        });

        if (result === true){
            learnMVC.vent.trigger("learn:proceedAnsweredQuestion");
        //only a part of the answer has been given
        } else if (typeof result === 'number'){
            this.model.set('answerPart',result);
        }
    },
    
    showAnswer: function(){
        var question = this.questions.collection.get(this.model.get('questionId'));   
        var answer = question.get('answer');

        if (typeof answer === 'string'){
            this.set('answer',_.escape(answer));
        }        

        if ($.isArray(answer)){
            var text = question.get('text');
            for (var i = 0, iMax = answer.length; i < iMax; i++){
                text = this.replaceImg(text,answer[i]);
            };
            this.set('text',text);
        }

        this.model.set('answer_asked',true);
        learnMVC.vent.trigger("learn:proceedAnsweredQuestion"); 
    },
    
    proceedAnsweredQuestion: function(){
        this.currentRound.get('roundOrder').pop();
        this.model.setAnswerType();
        this.currentRound.get('questionresults').add(this.model);
        
        var answerType = this.model.get('answerType');
        this.set({
            'nb_question': this.currentRound.get('roundOrder').length,
            'tip': this.questions.collection.get(this.model.get('questionId')).get('tip'),
            'maxPoint': this.get('maxPoint')+ _.max(_.values(this.currentRound.answerTypePointTable)),
            'score': this.get('score') + this.currentRound.answerTypePointTable[this.model.get('answerType')]
        });

        var total = this.get('nb_questions');
        var perfect = this.get('nb_perfect_answering') * total/100,
            average = this.get('nb_average_answering') * total/100,
                bad = this.get('nb_bad_answering') * total/100;

        switch(answerType){
            case '1':
                perfect++;
                break;
            case '2':
                average++;
                break;
            case '3':
                average++;
                break;
            case '4':
                average++;
                break;
            case '5':
                bad++;
                break;
            default:                    
        }
        this.set({
            'nb_perfect_answering':perfect/total * 100,
            'nb_average_answering':average/total  * 100,
            'nb_bad_answering':bad/total * 100
        });
        
        if (this.lastRounds.models.length > 0 ){
            var lastAnswerType = _.first(_.last(this.lastRounds.models)
                              .get('questionresults')
                              .where({'questionId':this.model.get('questionId')}))
                              .get('answerType');
            
            var commentsValues = {
                '1':{
                    '1' : 'Always perfect',
                    '2' : 'You did perfect in less than 10 seconds now',
                    '3' : 'Perfect, no more mistake like last time!',
                    '4' : 'Perfect, no more mistakes like last time!',
                    '5' : 'Perfect and you did not ask the answer like last time!'
                },
                '2':{
                    '1' : 'Last time you were quicker',
                    '2' : 'Like last time, you are still too slow',
                    '3' : 'Great, no more mistake like last time but too slow!',
                    '4' : 'Great, no more mistakes like last time but too slow!',
                    '5' : 'Great event if too slow but you did not ask the answer like last time!'
                }
            };

            if (commentsValues[String(answerType)]){
                this.set('comments',commentsValues[String(answerType)][String(lastAnswerType)] || '');
            }
            
        }
        
        
        
    },
    
    initNewRound:function () { 
        
        this.set('round_nb',this.lastRounds.models.length + 1);
        
        //create new Round       
        this.currentRound = new Round({
            listquestId: this.questions.listId,
            startDate: {date:new Date()},
            questionresults : new Questionresults(),
            roundOrder: this.lastRounds.newRoundOrder(this.questions.collection), 
            localDate:true
        });
        
        this.set('nb_question',this.get('nb_questions'));
        
        this.set({
                'nb_perfect_answering':0,
                'nb_average_answering':0,
                'nb_bad_answering':0
            });
        learnMVC.vent.trigger("learn:nextQuestion");
    },
    
    initNewQuestion: function(questionId){
            
        //push the new id to the end of the array (in case the next button is pushed before the question is answered)
        this.currentRound.get('roundOrder').shift();
        this.currentRound.get('roundOrder').push(questionId);
        this.set('comments','');
        
        var question = this.questions.collection.get(questionId);
        this.model = new Questionresult({
            startDate:new Date(),
            answerPart:0,
            questionId:questionId
        });      
        
        this.set({
            'text': question.get('text')
        });
        this.attributes.answer = '';
        this.attributes.tip = '';
        
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
    
    defaults: {
        text:'',
        answer:'',
        tip:'',
        
        round_nb:'',
        round_total:'',
        title_list:'',
        
        nb_question:'',
        nb_perfect_answering:0,
        nb_average_answering:0,
        nb_bad_answering:0,
        score:0,
        maxPoint:0,
        comments:''
    }
}); 
window.Listquest= Backbone.Model.extend({
    
    initialize: function() {
        
    },
    
    defaults: {
        title: 'undefined',
        questions : 'undefined'
    },
    
    urlRoot: "/learnlists-rest/listquest"
    
});




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
            var img = '<img class="img-find" src="/images/icons/find.png" alt="icon-hole" style="max-height:30px"/>';
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
    
    comparator: function(questionresult){
        return -parseInt(questionresult.get('answerType'));
    },
    
    url: "/learnlists-rest/questionresult"
});

window.Round = Backbone.Model.extend({
    
    initialize: function() {
        this.answerTypePointTable = {
            'false' : 0,
            '1' : 4,
            '2' : 3,
            '3' : 2,
            '4' : 1,
            '5' : 0
        };
        
        learnMVC.vent.on('learn:showResult',this.initStat,this);
        learnMVC.vent.on('learn:removeRounds',function(listId){
            var options = {data:{listquestId:listId}};
            this.destroy(options);
        },this);
    },
    
    initStat: function(){
        
        var res = this.get('questionresults');
        
        if(res){
            
            var total = res.models.length,
                answerTypeNb = {},
                totalPotentialPoint = 0,
                totalPoint = 0;
                
            _.each(['false','1','2','3','4','5'],function(answerType){
                answerTypeNb[answerType] = res.where({answerType:answerType}).length;
                totalPoint+=this.answerTypePointTable[answerType]*answerTypeNb[answerType];
            },this);
            
            totalPotentialPoint+= this.answerTypePointTable['1'] * (total - answerTypeNb['false']);
            
            
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
                finalnote:totalPoint + '/' + totalPotentialPoint,
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
    urlRoot: "/learnlists-rest/round",
    
    defaults:{
      finalnote:0,
      perfectanswer:0,  
      averageanswer:0,  
      badanswer:0,  
      notdone:0,
      duration:{days:0,hours:0,minutes:0,seconds:0},
      score:0
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
    
    newRoundOrder: function(questions){
        //if there are historical data
        if (this.models.length > 0){
            var lastResults = _.last(this.models).get('questionresults');
            return lastResults.pluck('questionId');            
        } else {
            return _.shuffle(questions.pluck('id'));         
        }
        
        
    },
    url: "/learnlists-rest/round"
    
});




window.CurrentStatsView = Backbone.Marionette.ItemView.extend({
    template: "#current_stats-template",
    tagName: 'span',
    ui:{
        perfectbar : '#perfect_answering_bar',
        average_bar : '#average_answering_bar',
        bad_bar     : '#bad_answering_bar'
    },
    initialize: function(){
        
        var optionsNotRefreshed = {transition_delay: 0};
        var optionsRefreshed = {transition_delay: 50};
        
        this.listenTo(this.model,'change:nb_perfect_answering',function(){
            this.render();
            this.ui.perfectbar.progressbar(optionsRefreshed);
            this.ui.average_bar.progressbar(optionsNotRefreshed);
            this.ui.bad_bar.progressbar(optionsNotRefreshed);
        });
        this.listenTo(this.model,'change:nb_average_answering',function(){
            this.render();
            this.ui.perfectbar.progressbar(optionsNotRefreshed);
            this.ui.average_bar.progressbar(optionsRefreshed);
            this.ui.bad_bar.progressbar(optionsNotRefreshed);
        });
        this.listenTo(this.model,'change:nb_bad_answering',function(){
            
            this.render();
            this.ui.perfectbar.progressbar(optionsNotRefreshed);
            this.ui.average_bar.progressbar(optionsNotRefreshed);
            this.ui.bad_bar.progressbar(optionsRefreshed);
        });
    }
});


window.FollowerView = Backbone.Marionette.ItemView.extend({
    template: "#title_pie-template",
    tagName: 'div',    
    className: 'well span12 pie',
    ui:{
        pie:'#current_pie'
    },
    initialize: function(){
        
    },
    
    updateKnob: function(val){
        var optionsKnob = {
            'min':0,
            'max':this.model.get('nb_questions'),
            'step':1,
            'readOnly':true,
            'width':80,
            'height':80
        };
        
        this.ui.pie.knob(optionsKnob)
        this.ui.pie.val(val).trigger('change'); 
    },
    
    onRender: function(){        
        if (this.model.get('loggedIn') === 'false'){ 
            $('#round_show').hide();
        }
        
        this.updateKnob(this.model.get('nb_question'));
    },
    
    
    modelEvents:{
        'change:nb_question change:comments change:round_nb change:round_total': 'render'
    }
    
    
});



var learnMVC = new Marionette.Application();

learnMVC.addRegions({
    main:'#questionMain'
});


LearnListsLayout = Backbone.Marionette.Layout.extend({
    template: "#layout-template",
    className:'row-fluid',
    regions: {
      answer      : '#answer_show',
      text        : '#question_asked_text',
      central_area: '#central_area',
      follower    : '#question_follower',
      currentstats: '#current_stats',
      presCorner  : '#first_presentation_corner',
      tip         : '#question_tip'
    },
    
    modelEvents:{
        'change:title_list': 'renderTitle'
    },
    
    renderTitle: function(model,data){
        $('#title_list').html(data);
    }
});

TipView = Backbone.Marionette.ItemView.extend({
    template: "#tip-template",
    className: 'well span12',
    initialize: function(){
    },
    modelEvents:{
        'change:tip': function(){
            this.render();
            $('#question_tip').show();
        }
    }
});


AnswerView = Backbone.Marionette.ItemView.extend({
    template: "#answer_show-template",
    tagName: 'div',    
    className: 'well span12',
    initialize: function(){
        
        learnMVC.vent.on('learn:initNewQuestion learn:roundCompleted',function(){
            $('#answer_show').hide();
        },this);
    },
    modelEvents:{
        'change:answer': function(){
            this.render();
            $('#answer_show').show();
        }
    }
});

InsideNavBarView = Backbone.Marionette.ItemView.extend({
    template: "#nav_bar_question-template",
    tagName: 'div',    
    className: 'well sidebar-nav',
    ui: {
        cancelButton        : '#question_asked_cancelRound',
        removeRoundsButton  : '#remove_rounds_button',
        newRoundButton      : '#question_asked_resetbutton'
    },
    events:{
        'click #remove_rounds_button'          : 'removeRounds',
        'click #question_asked_cancelRound'    : 'cancelRound',
        'click #question_asked_resetbutton'    : function(e){e.preventDefault();learnMVC.vent.trigger("learn:initNewRound");}
    },

    removeRounds: function(e){
        e.preventDefault(); 
        learnMVC.vent.trigger("learn:removeRounds",this.model.questions.listId);
        learnMVC.vent.trigger("learn:initNewRound");        
    },

    cancelRound: function(e){
        e.preventDefault(); 
        $('#question-asking').hide();
        $(e.currentTarget).hide();
        $('.reset_button').show().focus();
        learnMVC.vent.trigger("learn:showResult");
    },
    
    initialize: function(){        
        learnMVC.vent.on('learn:initNewRound',function(){
            this.ui.removeRoundsButton.hide();
            this.ui.newRoundButton.hide();
            this.ui.cancelButton.show()
        },this);
        
        learnMVC.vent.on('learn:initNewQuestion',function(){             
            $('#question_tip').hide();
        },this);
        
        learnMVC.vent.on('learn:roundCompleted',function(){
            
            if (this.model.get('loggedIn') === 'true'){
                this.ui.removeRoundsButton.show();
            }
            
            if (this.model.lastRounds.models.length < this.model.get('maxRound')){
                this.ui.newRoundButton.show().focus();
            }
            
            this.ui.cancelButton.hide();
        },this); 
    }
});

AskingQuestionView = Backbone.Marionette.ItemView.extend({
    template: "#asking_question-template",
    tagName: 'div',    
    className: 'well card span12',
    ui:{
        answerButton    : '#question_asked_showanswerbutton',
        nextButton      : '#question_asked_nextbutton',
        checkButton     : '#question_asked_submitbutton' ,
        answerInput     : '#question_asked_answer',
        answerButtons   : '.button-answer'
    },
    initialize: function(){
        
        learnMVC.vent.on('learn:proceedAnsweredQuestion',function(){             
            this.ui.answerButton.attr('disabled','disabled');
            this.ui.checkButton.attr('disabled','disabled');
            this.ui.answerInput.attr('readonly','readonly');
            this.ui.nextButton.html(this.ui.nextButton.attr('data-text-toggle'));
        },this);
        
        learnMVC.vent.on('learn:initNewQuestion',this.initNewQuestion,this);
        learnMVC.vent.on('learn:answerSuccess',this.showSuccess,this);
        learnMVC.vent.on('learn:answerError',this.showError,this);
    },
    
    initNewQuestion: function(){
        this.ui.answerButtons.removeAttr('disabled');
        this.ui.answerInput.val('').focus().removeAttr('readonly');
        $('#answer-group').removeClass('error').removeClass('success');
    },
    
    showSuccess: function(){
        $('#answer-group').addClass('success');        
    },
    
    showError: function(){
        $('#answer-group').addClass('error');
    },
    
    events:{
        'click #question_asked_submitbutton'   : 'checkAnswer',
        'click #question_asked_answer'         : function(e){e.preventDefault();learnMVC.vent.trigger("learn:answerAsked");},
        'click #question_asked_nextbutton'     : function(e){e.preventDefault();learnMVC.vent.trigger("learn:nextQuestion");},
        'click #question_asked_showanswerbutton':function(e){e.preventDefault();learnMVC.vent.trigger("learn:showAnswer");},
        'keydown #question_asked_answer'      :'enterKeyNoSubmit'
    },
    
    enterKeyNoSubmit : function(e){
        //deactivate function when no answer has to be given
        
        if (e.keyCode == 13 &&
            this.ui.checkButton.attr('disabled') === 'disabled') {
            e.preventDefault();
            this.ui.nextButton.click();
        }
    },

    checkAnswer: function(e){
        e.preventDefault(); 
        learnMVC.vent.trigger("learn:answerCheck",this.ui.answerInput.val());
    },

    modelEvents:{
        'change:text': 'render'
    }
});

RoundResultView = Backbone.Marionette.ItemView.extend({
    tagName: "tr",
    template: "#row_resultTable-template",
    templateHelpers: {
        roundinfo: function(){
            
            var duration = this.duration; 
            var time;
            
            if (duration.now){
                return 'current';
            }            
            if (duration.days > 0){
                time = duration.days + 'd';
            } else if (duration.hours > 0){
                time = duration.hours + 'h';
            } else if (duration.minutes > 0){
                time = duration.minutes + 'm';
            } else if (duration.seconds > 0){
                time = duration.seconds + 's';
            }
            
            return time+' ago';
        }
    },
    initialize: function(){
        
    }, 
    
    modelEvents: {
        "change": "render"
    }
});

NoResultsView = Backbone.Marionette.ItemView.extend({
    tagName: "tr",
    template: "#noresult-template"
});

ResultsView = Backbone.Marionette.CompositeView.extend({
    template: "#results-template",
    tagName: 'div',    
    className: 'well span12',
    itemView: RoundResultView,
    emptyView: NoResultsView,
    initialize: function(){
        this.collection = this.model.lastRounds;
    },
    appendHtml: function(collectionView, itemView, index){
        collectionView.$('tbody').prepend(itemView.el);
      }
});


learnMVC.addInitializer(function(options){
    
    var learnMain = new LearnMain({
        listId:options.listId,
        loggedIn: options.loggedIn,
        maxRound: options.maxRound,
        saveRoundsWhenNotLogged : this.saveRoundsWhenNotLogged
    });   
    
    var layout = new LearnListsLayout({model:learnMain});    
    learnMVC.main.show(layout);  
    
    layout.follower.show(new FollowerView({model:layout.model}));
    layout.presCorner.show(new InsideNavBarView({model:layout.model}));
    
    learnMVC.vent.on('learn:initNewRound',function(){
        var askingQuestionView = new AskingQuestionView({model:layout.model});
        layout.central_area.show(askingQuestionView);
        //has to be initiated because initNewQuestion is not triggered 
        //on the first question before the view has been created
        askingQuestionView.initNewQuestion();
    });
    
    learnMVC.vent.on('learn:initNewQuestion',function(){
        layout.answer.close();
        layout.answer.show(new AnswerView({model:layout.model}));
        layout.tip.close();
        layout.tip.show(new TipView({model:layout.model})); 
    });
    
    learnMVC.vent.on('learn:showResult',function(){
        layout.tip.close();
        layout.answer.close();
        layout.central_area.show(new ResultsView({model: layout.model}));
    });
    
});