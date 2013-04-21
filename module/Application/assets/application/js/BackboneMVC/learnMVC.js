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
            $('#question_asked_showanswerbutton').attr('disabled','disabled');
            $('#question_asked_submitbutton').attr('disabled','disabled');
            $('#question_asked_answer').attr('readonly','readonly');
            $('#question_asked_nextbutton').html(this.ui.nextButton.attr('data-text-toggle'));
        },this);
        
        learnMVC.vent.on('learn:initNewQuestion',this.initNewQuestion,this);
        learnMVC.vent.on('learn:answerSuccess',this.showSuccess,this);
        learnMVC.vent.on('learn:answerError',this.showError,this);
    },
    
    initNewQuestion: function(){
        
        $('.button-answer').removeAttr('disabled');
        $('#question_asked_answer').val('').focus().removeAttr('readonly');
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

$(function(){
    learnMVC.start({
            listId:$('#listId').val(),
            loggedIn: $('#listId').attr('data-loggedin'),
            maxRound: $('#listId').attr('data-maxRound'),
            saveRoundsWhenNotLogged: false
    });
});