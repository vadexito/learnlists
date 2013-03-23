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
            learnMVC.vent.trigger("learn:initNewQuestion",_.first(this.currentRound.get('roundOrder')));
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

        switch(this.model.get('answerType')){
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
        
        
        
    },
    
    initNewRound:function () { 
        
        this.set('round_nb',this.lastRounds.models.length + 1);
        
        //fetch former rounds from the database and create new Round       
        this.currentRound = new Round({
            listquestId: this.questions.listId,
            startDate: {date:new Date()},
            questionresults : new Questionresults(),
            roundOrder: _.shuffle(this.questions.collection.pluck('id')), 
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
        maxPoint:0
    }
}); 