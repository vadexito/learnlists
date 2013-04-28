
LoadingView = Backbone.Marionette.ItemView.extend({
    template: "#loading-template",
    initialize: function(){
    }
});

TitleListView = Backbone.Marionette.ItemView.extend({
    template: "#title_list-template",
    initialize: function(){
    },
    modelEvents:{
        'change:title_list': "render"
    }
});

RoundNumberView = Backbone.Marionette.ItemView.extend({
    getTemplate: function(){
        if (this.model.get("round_nb")){
            return "#round_number-template";
        } else {
            return "#empty-template";
        }
    },
    modelEvents:{
        'change:round_nb': 'render'
    }
});

TimerView = Backbone.Marionette.ItemView.extend({
    template: "#timer-template",
    ui:{
        countdown:'#countdown'
    },
    initialize: function(){
        var timeMax = this.model.get('timePerQuestion');
        var self = this;
        this.listenTo(learnMVC.vent,'learn:initNewQuestion',function(){
            self.timeOut = false;
            var countdown = $('#countdown');
            countdown.val(timeMax);
            countdown.knob({
                'min':0,
                'max':timeMax,
                'step':1,
                'readOnly':true,
                'width':100,
                'height':100
            });
            countdown.trigger('change');
            var now = new Date();
            var date = new Date(now.getTime()+10000000) ;
            //count down
            countdown.countdown({
                date: date,
                render: function(){
                    if (self.timeOut === false){
                        var val = countdown.val();
                        countdown.val(val-1);
                        countdown.trigger('change');
                    }
                }
            });
        });
        
        this.listenTo(learnMVC.vent,'learn:proceedAnsweredQuestion',function(){            
            self.timeOut = true;            
        });
    }
});

//views for main region
QuestionView = Backbone.Marionette.ItemView.extend({
    template: "#question-template",
    modelEvents:{
        'change:text' : 'render'
    }
});
AnswerView = Backbone.Marionette.ItemView.extend({
    getTemplate: function(){
        if (this.model.get('answer')){
            return "#answer-template";
        } else {
            return "#empty-template";
        }
    },
    modelEvents:{
        'change:answer' : 'render'
    }
});
CommentView = Backbone.Marionette.ItemView.extend({
    getTemplate: function(){
        if (this.model.get("comment")){
            return "#comment-template";
        } else {
            return "#empty-template";
        }
    },
    modelEvents:{
        'change:comment': 'render'
    }
});
InputView = Backbone.Marionette.ItemView.extend({
    template: "#input-template",
    ui:{
        answerInput     : '#question_asked_answer'
    },
    initialize: function(){
        
        this.listenTo(learnMVC.vent,'learn:proceedAnsweredQuestion',function(){             
            $('#question_asked_answer').attr('readonly','readonly');
        });
        
        this.listenTo(learnMVC.vent,'learn:initNewQuestion',this.initNewQuestion);
        this.listenTo(learnMVC.vent,'learn:answerSuccess',this.showSuccess);
        this.listenTo(learnMVC.vent,'learn:answerError',this.showError);
    },
    
    initNewQuestion: function(){        
        $('#question_asked_answer').val('').focus().removeAttr('readonly');
    },
    
    showSuccess: function(){
        $('#answer-group').addClass('success');        
    },
    
    showError: function(){
        $('#answer-group').addClass('error');
    },
    
    events:{
        'click #question_asked_answer'         : function(e){e.preventDefault();learnMVC.vent.trigger("learn:answerAsked");},
        'keydown #question_asked_answer'      :'enterKeyNoSubmit'
    },
    
    enterKeyNoSubmit : function(e){
        //deactivate function when no answer has to be given
        
        if (e.keyCode == 13){
            if ($('#question_asked_submitbutton').attr('disabled') === 'disabled') {
                e.preventDefault();
                $('#question_asked_nextbutton').click();
            } else {
                $('#question_asked_submitbutton').click();
            }
        }
    }
});
MainButtonsView = Backbone.Marionette.ItemView.extend({
    template: "#main_buttons-template",
    ui:{
        answerButton    : '#question_asked_showanswerbutton',
        nextButton      : '#question_asked_nextbutton',
        checkButton     : '#question_asked_submitbutton' ,
        answerButtons   : '.button-answer'
    },
    initialize: function(){
        this.listenTo(learnMVC.vent,'learn:proceedAnsweredQuestion',function(){  
            
            $('#question_asked_showanswerbutton').attr('disabled','disabled');
            $('#question_asked_submitbutton').attr('disabled','disabled');
            $('#question_asked_nextbutton').attr('title',$('#question_asked_nextbutton').attr('data-text-toggle'));
        });
        
        this.listenTo(learnMVC.vent,'learn:initNewQuestion',this.initNewQuestion);
    },
    
    initNewQuestion: function(){
        $('.button-answer').removeAttr('disabled');
        $('#answer-group').removeClass('error').removeClass('success');
    },
    
    events:{
        'click #question_asked_submitbutton'   : 'checkAnswer',
        'click #question_asked_nextbutton'     : function(e){e.preventDefault();learnMVC.vent.trigger("learn:nextQuestion");},
        'click #question_asked_showanswerbutton':function(e){e.preventDefault();learnMVC.vent.trigger("learn:showAnswer");}
    },
    
    

    checkAnswer: function(e){
        e.preventDefault(); 
        learnMVC.vent.trigger("learn:answerCheck",$('#question_asked_answer').val());
    },

    modelEvents:{
        'change:text': 'render'
    }    
});

//view for side region
QuestionFollowerView = Backbone.Marionette.ItemView.extend({
    template: "#question_follower-template",
    modelEvents:{
            'change:nb_question ' : 'render'
    }
});
ScoreView = Backbone.Marionette.ItemView.extend({
    template: "#score-template",
    modelEvents:{
            'change:score change:maxPoint' : 'render'
    }
});
CheckMessageView = Backbone.Marionette.ItemView.extend({
    template: "#check_message-template",
    modelEvents:{
            'change:newPoints change:checkMessageTitle change:comments' : 'render'
    },
    initialize: function(){        
        this.listenTo(learnMVC.vent,'learn:initNewQuestion learn:roundCompleted learn:showResult',function(){
            this.model.set({
                newPoints:'',
                checkMessageTitle:'',
                comments:''
            });
        });
        
    }
});
SideButtonsView = Backbone.Marionette.ItemView.extend({
    template: "#side_buttons-template",
    ui: {
        cancelButton        : '#question_asked_cancelRound',
        removeRoundsButton  : '#remove_rounds_button',
        newRoundButton      : '#question_asked_resetbutton'
    },
    events:{
        'click #introjs_start_btn'             : 'startIntrojs',
        'click #remove_rounds_button'          : 'removeRounds',
        'click #question_asked_cancelRound'    : 'cancelRound',
        'click #question_asked_resetbutton'    : function(e){
            e.preventDefault();
            console.log('trigger learn:pre-initNewRound');
            learnMVC.vent.trigger('learn:pre-initNewRound');
            console.log('trigger learn:initNewRound');
            learnMVC.vent.trigger("learn:initNewRound");
        }
    },
    startIntrojs: function(){
        introJs().start();
    },
    
    
    removeRounds: function(e){
        e.preventDefault(); 
        console.log('trigger learn:removeRounds');
        learnMVC.vent.trigger("learn:removeRounds",this.model.questions.listId);
        console.log('trigger learn:pre-initNewRound');
        learnMVC.vent.trigger('learn:pre-initNewRound');
        console.log('trigger learn:initNewRound');
        learnMVC.vent.trigger("learn:initNewRound");        
    },

    cancelRound: function(e){
        e.preventDefault(); 
        $('#question-asking').hide();
        $(e.currentTarget).hide();
        $('.reset_button').show().focus();
        console.log('event learn:pre-showResult');
        learnMVC.vent.trigger("learn:pre-showResult");
        console.log('trigger learn:showResult');
        learnMVC.vent.trigger("learn:showResult");
    },
    
    initialize: function(){
        var self = this;
        this.listenTo(learnMVC.vent,'learn:initNewRound',function(){
            $('#remove_rounds_button').hide();
            $('#question_asked_resetbutton').hide();
            $('#question_asked_cancelRound').show()
        });
        
        this.listenTo(learnMVC.vent,'learn:showResult',function(){
            
            if (self.model.get('loggedIn') === 'true'){
                $('#remove_rounds_button').show();
            }
            $('#question_asked_resetbutton').addClass('btn-primary').show();
            $('#question_asked_cancelRound').hide();
        }); 
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
