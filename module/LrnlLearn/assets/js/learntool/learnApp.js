var learnMVC = new Backbone.Marionette.Application();

learnMVC.addRegions({
    main:'#questionMain'
});

learnMVC.startLearn = function(){
    var loadingView = new LoadingView();
    learnMVC.main.show(loadingView);
    console.log('trigger learn:start');
    learnMVC.vent.trigger("learn:start");
};

learnMVC.addInitializer(function(options){
    var model = new LearnMain(options.model);
        
    learnMVC.showLearn = function(optionsModel){
        
        if (optionsModel){
            model.set(optionsModel);
        }
        if (learnMVC.learnViews || learnMVC.resultViews){
            _.each(learnMVC.learnViews,function(view){
                view.close();
            });
            _.each(learnMVC.resultViews,function(view){
                view.close();
            });
        }
            
        learnMVC.learnViews = {
            'titleListView' : new TitleListView({model:model}),
            'roundNumberView' : new RoundNumberView({model:model}),
            'timerView' : new TimerView({model:model}), 
            'questionView' : new QuestionView({model:model}),
            'answerView' : new AnswerView({model:model}),
            'commentView' : new CommentView({model:model}),
            'inputView' : new InputView({model:model}),
            'mainButtonsView' : new MainButtonsView({model:model}),
            'questionFollowerView' : new QuestionFollowerView({model:model}),
            'scoreView' : new ScoreView({model:model}),
            'checkMessageView' : new CheckMessageView({model:model}),
            'sideButtonsView' : new SideButtonsView({model:model}),  
            'resultsView' : new ResultsView({model: model}),
            'layout' : new LearnListsLayout({model:model}),     
            'topRegionLayout' : new TopRegionLayout({model:model}),    
            'mainRegionLayout' : new MainRegionLayout({model:model}),    
            'sideRegionLayout' : new SideRegionLayout({model:model}) 
        };
        var views = learnMVC.learnViews;
        learnMVC.main.show(views['layout']);
        
        views['layout'].top.show(views['topRegionLayout']);
        views['layout'].main.show(views['mainRegionLayout']);
        views['layout'].side.show(views['sideRegionLayout']);

        views['topRegionLayout'].left.show(views['titleListView']);
        views['topRegionLayout'].center.show(views['roundNumberView']);
        views['topRegionLayout'].right.show(views['questionFollowerView']);

        views['mainRegionLayout'].question.show(views['questionView']);
        views['mainRegionLayout'].answer.show(views['answerView']);
        views['mainRegionLayout'].comment.show(views['commentView']);
        views['mainRegionLayout'].input.show(views['inputView']);
        views['mainRegionLayout'].main_buttons.show(views['mainButtonsView']);

        views['sideRegionLayout'].top.show(views['timerView']);
        views['sideRegionLayout'].middle_top.show(views['checkMessageView']);
        views['sideRegionLayout'].middle.show(views['scoreView']);
        views['sideRegionLayout'].bottom.show(views['sideButtonsView']);
        
        learnMVC.layout = views['layout'];
        learnMVC.topLayout = views['topRegionLayout'];
        learnMVC.mainLayout = views['mainRegionLayout'];
        learnMVC.sideLayout = views['sideRegionLayout'];
        
        console.log('initiated layouts for learning');
    };
    
    learnMVC.showResults = function(optionsModel){
        
        if (optionsModel){
            model.set(optionsModel);
        }
        if (learnMVC.learnViews || learnMVC.resultViews){
            _.each(learnMVC.learnViews,function(view){
                view.close();
            });
            _.each(learnMVC.resultViews,function(view){
                view.close();
            });
        }
            
        learnMVC.resultViews = {
            'resultsView' : new ResultsView({model: model}),
            'layout' : new LearnListsLayout({model:model}) ,
            'sideRegionLayout' : new SideRegionLayout({model:model}),
            'sideButtonsView' : new SideButtonsView({model:model}),
            'titleListView' : new TitleListView({model:model}),
            'roundNumberView' : new RoundNumberView({model:model}),
            'questionFollowerView' : new QuestionFollowerView({model:model}),
            'topRegionLayout' : new TopRegionLayout({model:model}) 
        };
        var views = learnMVC.resultViews;
        
        learnMVC.main.show(views['layout']);
        
        views['layout'].top.show(views['topRegionLayout']);
        views['layout'].main.show(views['resultsView']);
        views['layout'].side.show(views['sideRegionLayout']);
        views['sideRegionLayout'].bottom.show(views['sideButtonsView']);
        views['topRegionLayout'].left.show(views['titleListView']);
        views['topRegionLayout'].center.show(views['roundNumberView']);
        views['topRegionLayout'].right.show(views['questionFollowerView']);
        console.log('update layouts for results');
    };
});

learnMVC.addInitializer(function(options){
    var currentStatus;
    //modal for choice between demo and direct learning
    $('#start-modal').modal();
    $('#start-modal').on('hide', function () {
        if (currentStatus !== 'demo'){
            learnMVC.startLearn();
        }
    });
    
    
    //options for the demo
    learnMVC.showLearn(options.demo.model);    
    $('#seeHowItWorks-btn').click(function(){
        currentStatus = 'demo';
        $('#start-modal').modal('hide');
        $('#countdown').knob({
            'min':0,
            'max':options.model.timePerQuestion,
            'step':1,
            'readOnly':true,
            'width':100,
            'height':100
        });
        $('#countdown').val(5).trigger('change');
        introJs().setOptions(options.pluginTranslations.introJs).oncomplete(function() {
            learnMVC.startLearn();
        }).onexit(function() {
            learnMVC.startLearn();
        }).start();
        
    });
    
    learnMVC.vent.on('learn:pre-initNewRound',function(){
        learnMVC.showLearn();
        console.log('preinit views');
    });
    learnMVC.vent.on('learn:pre-showResult',function(){
        
         $('#star').raty({
            starHalf      : '/assets/components/raty/lib/img/star-half.png',
            starOff       : '/assets/components/raty/lib/img/star-off.png',
            starOn        : '/assets/components/raty/lib/img/star-on.png',
            number        : 10,
            numberMax     : 10,
            hints         : options.pluginTranslations.raty.hints,
            scoreName     : 'review[rating]'
        });
        $('#review-modal').modal('show');
        $('#add_review-form').ajaxForm(function() {         
        }); 
        $('#submit_review_btn').click(function(){
            $('#review-modal').modal('hide');
        });        
        learnMVC.showResults();
    });
    
});

$(function(){
    var hiddenInput = $('#listId');
    
    learnMVC.start({
        model: {
            listId:hiddenInput.val(),
            loggedIn: hiddenInput.attr('data-loggedin'),
            saveRoundsWhenNotLogged: false,
            timePerQuestion:hiddenInput.attr('data-timePerQuestion'),
            translatedCommentArray: $.parseJSON(hiddenInput.attr('data-comments')) 
       },
       pluginTranslations: $.parseJSON(hiddenInput.attr('data-plugintranslations')),   
       demo : $.parseJSON(hiddenInput.attr('data-demo'))
    });
});