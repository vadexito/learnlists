var learnMVC = new Marionette.Application();

learnMVC.addRegions({
    main:'#questionMain'
});


LearnListsLayout = Backbone.Marionette.Layout.extend({
    template: "#layout-template",
    className:'row-fluid',
    regions: {
      top           : '#top-region',
      main          : '#main-region',
      side          : '#side-region'
    }
});

//nested layouts
TopRegionLayout = Backbone.Marionette.Layout.extend({
    template: "#top_region-template",
    regions: {
      left            : '#left-top-region',
      center          : '#center-top-region',
      right           : '#right-top-region'
    }
});

MainRegionLayout = Backbone.Marionette.Layout.extend({
    template: "#main_region-template",
    regions: {
      question      : '#question-region',
      answer        : '#answer-region',
      comment       : '#comment-region',
      input         : '#input-region',
      main_buttons  : '#main_buttons-region'
    }
    
    
});

SideRegionLayout = Backbone.Marionette.Layout.extend({
    template: "#side_region-template",
    regions: {
      top           : '#top-side-region',
      middle        : '#middle-side-region',
      bottom        : '#bottom-side-region'
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
    template: "#round_number-template",
    initialize: function(){
        if (this.model.get('loggedIn') === 'false'){ 
            $(this.el).hide();
        }
    },
    modelEvents:{
        'change:round_nb change:round_total' : 'render'
    }
});

TimerView = Backbone.Marionette.ItemView.extend({
    template: "#timer-template",
    ui:{
        knob:'#timer_knob'
    },
    initialize: function(){
        
//        var optionsKnob = {
//            'min':0,
//            'max':20,
//            'step':1,
//            'readOnly':true,
//            'width':80,
//            'height':80
//        };
//        
//        this.ui.knob();
        
        
    }
//    updateKnob: function(val){
//        
//        
//        this.ui.pie.knob(optionsKnob)
//        this.ui.pie.val(val).trigger('change'); 
//    },
//    
//    onRender: function(){
//        this.updateKnob(this.model.get('nb_question'));
//    }   
    
});

//views for main region
QuestionView = Backbone.Marionette.ItemView.extend({
    template: "#question-template",
    modelEvents:{
        'change:text' : 'render'
    }
});
AnswerView = Backbone.Marionette.ItemView.extend({
    template: "#answer-template",
    modelEvents:{
        'change:answer' : function(){
            this.render();
            $('.answer-region').show();
        }
    },
    initialize: function(){        
        learnMVC.vent.on('learn:initNewQuestion learn:roundCompleted learn:showResult',function(){
            $('.answer-region').hide();
        },this);
    }
});
CommentView = Backbone.Marionette.ItemView.extend({
    template: "#comment-template",
    modelEvents:{
        'change:comment': function(){
            this.render();
            $('.comment-region').show();
        }
    },
    initialize: function(){        
        learnMVC.vent.on('learn:initNewQuestion learn:roundCompleted learn:showResult',function(){
            $('.comment-region').hide();
        },this);
    }
});
InputView = Backbone.Marionette.ItemView.extend({
    template: "#input-template",
    ui:{
        answerInput     : '#question_asked_answer'
    },
    initialize: function(){
        
        learnMVC.vent.on('learn:proceedAnsweredQuestion',function(){             
            $('#question_asked_answer').attr('readonly','readonly');
        },this);
        
        learnMVC.vent.on('learn:initNewQuestion',this.initNewQuestion,this);
        learnMVC.vent.on('learn:answerSuccess',this.showSuccess,this);
        learnMVC.vent.on('learn:answerError',this.showError,this);
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
        
        learnMVC.vent.on('learn:proceedAnsweredQuestion',function(){             
            $('#question_asked_showanswerbutton').attr('disabled','disabled');
            $('#question_asked_submitbutton').attr('disabled','disabled');
            $('#question_asked_nextbutton').attr('title',this.ui.nextButton.attr('data-text-toggle'));
        },this);
        
        learnMVC.vent.on('learn:initNewQuestion',this.initNewQuestion,this);
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
        console.log('i');
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
            'change:score change:maxPoint change:comments' : 'render'
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
            $('#remove_rounds_button').hide();
            $('#question_asked_resetbutton').hide();
            $('question_asked_cancelRound').show()
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
    
    var initLayouts = function(){
        var layout = new LearnListsLayout({model:learnMain}); 
        learnMVC.layout = layout;
        learnMVC.main.show(layout);  

        var topRegionLayout = new TopRegionLayout({model:learnMain});    
        var mainRegionLayout = new MainRegionLayout({model:learnMain});    
        var sideRegionLayout = new SideRegionLayout({model:learnMain}); 

        layout.top.show(topRegionLayout);
        layout.main.show(mainRegionLayout);
        layout.side.show(sideRegionLayout);

        var titleListView = new TitleListView({model:learnMain});
        var roundNumberView = new RoundNumberView({model:learnMain});
        var timerView = new TimerView({model:learnMain}); 
        var questionView = new QuestionView({model:learnMain});
        var answerView = new AnswerView({model:learnMain});
        var commentView = new CommentView({model:learnMain});
        var inputView = new InputView({model:learnMain});
        var mainButtonsView = new MainButtonsView({model:learnMain});
        var questionFollowerView = new QuestionFollowerView({model:learnMain});
        var scoreView = new ScoreView({model:learnMain});
        var sideButtonsView = new SideButtonsView({model:learnMain});


        topRegionLayout.left.show(titleListView);
        topRegionLayout.center.show(roundNumberView);
        topRegionLayout.right.show(questionFollowerView);

        mainRegionLayout.question.show(questionView);
        mainRegionLayout.answer.show(answerView);
        mainRegionLayout.comment.show(commentView);
        mainRegionLayout.input.show(inputView);
        mainRegionLayout.main_buttons.show(mainButtonsView);

        sideRegionLayout.top.show(timerView);
        sideRegionLayout.middle.show(scoreView);
        sideRegionLayout.bottom.show(sideButtonsView);
    }
    
    learnMVC.vent.on('learn:initNewRound',initLayouts);
    learnMVC.vent.on('learn:showResult',function(){
        learnMVC.layout.main.show(new ResultsView({model: learnMain}));
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