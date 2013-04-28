window.LearnMain = Backbone.Model.extend({
    
    initialize: function(){
        
        learnMVC.vent.on("learn:start",this.start,this);
        learnMVC.vent.on("learn:initNewRound",this.initNewRound,this);
        learnMVC.vent.on("learn:initNewQuestion",this.initNewQuestion,this);
        learnMVC.vent.on("learn:nextQuestion",this.nextQuestion,this);        
        learnMVC.vent.on("learn:answerCheck",this.checkAnswer,this);        
        learnMVC.vent.on("learn:showAnswer",this.provideAnswer,this);        
        learnMVC.vent.on("learn:proceedAnsweredQuestion",this.proceedAnsweredQuestion,this);
        learnMVC.vent.on("learn:roundCompleted",this.roundCompleted,this);        
        
        console.log('initialize Learnmain model');
        
    },
    
    reset: function(){
       this.set({
           'score' : 0,
           'maxPoint' : 0,
           'round_nb':'',
           'nb_questions' : this.questions.collection.length,
           'nb_question' : this.questions.collection.length
       });  
    },
    
    start: function(){
        var listId = this.get('listId');
        var loggedIn = this.get('loggedIn');        
        var maxRound = this.get('maxRound'); 
        
        if (!listId || !loggedIn || !maxRound){
            throw new Error("You must provide a listId, maxRound and loggedIn boolean in order to initialize Learnmain");;
        }
        
        //fetch the list of questions
        new Listquest({id: listId}).fetch({reset:true,success: $.proxy(function(list){
            
            //init list of questions
            this.questions = {
                listId : listId,
                maxRound:maxRound,
                collection:new Questions()
            };
            
            
            this.questions.collection.initQuestions(list.get('questions'));  
            
            console.log('trigger learn:init');
            learnMVC.vent.trigger("learn:init");
            
            this.set('title_list',list.get('title'));
            this.set('rules',list.get('rules'));
            this.set('round_total',this.questions.maxRound);
            
            console.log(this.questions.collection);
            //init last rounds
            this.lastRounds = new Rounds();
            if (loggedIn === 'true'){                
                this.lastRounds.init(listId,maxRound);
            } else {
                //no last round to init if not logged
                learnMVC.vent.trigger("learn:pre-initNewRound"); 
                console.log('trigger initNewRound');
                learnMVC.vent.trigger("learn:initNewRound"); 
                
            }
        },this)});
    },
    
    initNewRound:function () {
        this.reset();
        
        if (this.get('loggedIn') === 'true'){
            this.set('round_nb',this.lastRounds.models.length + 1);
        }
        
        //create new Round       
        this.currentRound = new Round({
            listquestId: this.questions.listId,
            startDate: {date:new Date()},
            questionresults : new Questionresults(),
            roundOrder: this.lastRounds.newRoundOrder(this.questions.collection), 
            localDate:true
        });
        
        console.log('trigger learn:nextQuestion');
        learnMVC.vent.trigger("learn:nextQuestion");
        
    },
    
    nextQuestion: function(){

        var roundOrder = this.currentRound.get('roundOrder');
        console.log(roundOrder);
        if (roundOrder.length > 0) {   
            console.log('trigger learn:initNewQuestion');
            learnMVC.vent.trigger("learn:initNewQuestion",_.first(roundOrder));
            
        } else {
            console.log('trigger learn:roundCompleted');
            learnMVC.vent.trigger("learn:roundCompleted");
        }
    },
    
    initNewQuestion: function(questionId){
            
        //push the new id to the end of the array (in case the next button is pushed before the question is answered)
        //console.log(this.currentRound);
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
        this.attributes.comment = '';
        
    },
    
    roundCompleted: function(){
        if (this.get('loggedIn') === 'true' || this.get('saveRoundsWhenNotLogged') === true ){
            this.currentRound.saveDB();
            this.lastRounds.add(this.currentRound);
        }
        
        console.log('event learn:pre-showResult');
        learnMVC.vent.trigger("learn:pre-showResult");
        console.log('event learn:showResult');
        learnMVC.vent.trigger("learn:showResult");

    },
    
    
    
    checkAnswer: function(answerGiven){
        var question = this.questions.collection.get(this.model.get('questionId'));
        var translations = this.get('translatedCommentArray')['results'];
        var result = this.model.checkAnswer({
            answerGiven: answerGiven,
            answerInDB: question.get('answer'),
            answerPart: this.model.get('answerPart')
        });

        if (result === true){            
            this.set({
                'checkMessageTitle': translations['right']
            });
            console.log('event learn:proceedAnsweredQuestion');
            learnMVC.vent.trigger("learn:proceedAnsweredQuestion");
        //if only a part of the answer has been given
        } else if (typeof result === 'number'){
             this.set({
                'checkMessageTitle': translations['right']
            });
            this.model.set('answerPart',result);
        } else {// wrong answer
            this.set({
                'checkMessageTitle': translations['wrong']
            });
            var self = this;
            setTimeout(function(){
                self.set({
                    'checkMessageTitle': '',
                    'checkMessage': ''
                });
            },3000);
        }
    },
    
    provideAnswer: function(){
        var question = this.questions.collection.get(this.model.get('questionId'));   
        var answer = question.get('answer');

        //if it is a normal question
        if (typeof answer === 'string'){
            this.set('answer',_.escape(answer));
        } else {
            //if it is a sentence with holes
            $(".answer-location").each(function(){ 
                answer = $(this).attr('data-answer');
                $(this).removeClass('hiddenanswer').addClass('animated fadeIn'); 
                $(this).html('<span class="right-answer">'+answer+'</span>');
                
            });
        } 
        
        this.model.set('answer_asked',true);
        console.log('event learn:proceedAnsweredQuestion');
        learnMVC.vent.trigger("learn:proceedAnsweredQuestion"); 
    },
    
    
    
    proceedAnsweredQuestion: function(){
        var translations = this.get('translatedCommentArray');
        this.currentRound.get('roundOrder').pop();
        this.model.setAnswerType(this.get('timePerQuestion'));
        this.currentRound.get('questionresults').add(this.model);
        
        var answerType = this.model.get('answerType');
        this.set({
            'nb_question': this.currentRound.get('roundOrder').length,
            'comment': this.questions.collection.get(this.model.get('questionId')).get('tip'),
            'maxPoint': this.get('maxPoint')+ _.max(_.values(this.currentRound.answerTypePointTable)),
            'score': this.get('score') + this.currentRound.answerTypePointTable[this.model.get('answerType')]
        });
        
        this.set({
            'newPoints': translations['newPoints'][answerType]
        });
        
        //analysis using the past results
        if (this.lastRounds.models.length > 0 ){
            var lastAnswerType = _.first(_.last(this.lastRounds.models)
                              .get('questionresults')
                              .where({'questionId':this.model.get('questionId')}))
                              .get('answerType');

            var commentsValues = translations['comments'];
            if (commentsValues[String(answerType)]){
                this.set('comments',commentsValues[String(answerType)][String(lastAnswerType)] || '');
            }
        }
    },
    
    
    defaults: {
        text:'',
        answer:'',
        comment:'',
        
        round_nb:'',
        round_total:'',
        title_list:'',
        
        nb_question:'',
        nb_questions:'',
        score:0,
        newPoints:'',
        checkMessageTitle:'',
        maxPoint:0,
        comments:'',
        translatedCommentArray:{}
    }
}); 