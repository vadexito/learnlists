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
        newRoundButton      : '#question_asked_resetbutton',
        roundShow           : '#round_show'
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
        learnMVC.vent.on('learn:init',function(){
            if (this.model.get('loggedIn') === 'true'){
                this.ui.roundShow.show();
            }
        },this);
        
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
    },
    
    modelEvents:{
        'change:round_nb change:title_list change:round_total': function(){
            this.render();
            this.ui.roundShow.show();
        }
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
        
        learnMVC.vent.on('learn:initNewRound',function(){
            $('#question-asking').show();
            $('#percent-firsttime,#percent-noanswerused').html(0);
            $('#bar-progress').html('').css('width','0');         
        });
        
        learnMVC.vent.on('learn:initNewQuestion',function(){             
            this.ui.answerButtons.removeAttr('disabled');
            this.ui.answerInput.val('').focus().removeAttr('readonly'); 
            $('#answer-group').removeClass('error').removeClass('success');
            $('.answer-sign').hide();
        },this);
        
        learnMVC.vent.on('learn:proceedAnsweredQuestion',function(){             
            this.ui.answerButton.attr('disabled','disabled');
            this.ui.checkButton.attr('disabled','disabled');
            this.ui.answerInput.attr('readonly','readonly');
            this.ui.nextButton.focus();
        },this);
    },
    events:{
        'click #question_asked_submitbutton'   : 'checkAnswer',
        'click #question_asked_answer'         : function(e){e.preventDefault();learnMVC.vent.trigger("learn:answerAsked");},
        'click #question_asked_nextbutton'     : function(e){e.preventDefault();learnMVC.vent.trigger("learn:nextQuestion");},
        'click #question_asked_showanswerbutton':function(e){e.preventDefault();learnMVC.vent.trigger("learn:showAnswer");}
    },

    checkAnswer: function(e){
        e.preventDefault(); 
        $('.answer-sign').hide();
        learnMVC.vent.trigger("learn:answerCheck",this.ui.answerInput.val());
    },

    modelEvents:{
        'change:text': 'render'
    }
});

Results = Backbone.Model.extend({});

CurrentQuestion = Backbone.Model.extend({
    initialize: function(){
        var self = this;
        learnMVC.vent.on('learn:initNewQuestion',function(text){
            self.set('text',text);
        });
    },
    defaults:{
        text:'',
        answer:''
    }
});

RoundResultView = Backbone.Marionette.ItemView.extend({
  tagName: "tr",
  template: "#row-template"
});




ResultsView = Backbone.Marionette.CompositeView.extend({
    template: "#results-template",
    tagName: 'div',    
    className: 'well span12',
    itemViewContainer: "tbody",
    itemView: RoundResultView,
    ui:{
        thead:'#head_table_results',
        tbody:'#body_table_results'
    },
    initialize: function(){
    }
});


learnMVC.addInitializer(function(options){
    
    var learnMain = new LearnMain({
        listId:options.listId,
        loggedIn: options.loggedIn,
        maxRound: options.maxRound
    });    
    var layout = new LearnListsLayout({model:learnMain});    
    learnMVC.main.show(layout);  
    
    layout.follower.show(new FollowerView({model:layout.model}));
    layout.presCorner.show(new InsideNavBarView({model:layout.model}));
    layout.currentstats.show(new CurrentStatsView({model:layout.model}));
    
    var results = new Results({
                duration0:0,
                duration1:0,
                duration2:0,
                duration3:0,
                duration4:0
            });
            
    learnMVC.vent.on('learn:initNewRound',function(){
        layout.central_area.show(new AskingQuestionView({model:layout.model}));
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
        layout.central_area.show(new ResultsView({
            model:results
        }));
    });
    
});