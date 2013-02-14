window.QuestionView = Backbone.View.extend({
    
    el:'#question',
    
    model: Question,
    
    collection : new Questions(),
    
    initialize: function() {
        
        this.answer = $('#answer');
        this.listId = $('#listId').val();
        
        //add eventlistener if the model changes
        this.listenTo(this.collection,'change:answered',this.updateStatRightAnswer);        
        this.listenTo(this.collection,'change:answer_asked',this.updateStatAnswerAsked);        
        this.listenTo(this.collection,'change:multiple',this.updateStatMultiple);        
        
        var list = new Listquest({id:this.listId});
        list.fetch({success: $.proxy(function(){
            this.collection.add(list.get('questions'));
            $('#title-list').html(list.get('title'));
            this.resetRound();
        },this)});
        
    },
    
    events:{
        'click #submitbutton'       : 'checkAnswer',
        'click #nextbutton'         : 'newQuestion',
        'click #resetbutton'        : 'resetList',
        'click #showanswerbutton'   : 'showAnswer'
    },
    
    resetRound:function () {
        
        var collection = this.collection; 
        collection.init();
        _.each(collection.models,function(model){
            model.resetStat();
        });
        collection.randomOrder = _.shuffle(collection.pluck('id'));
        
        $('#percent-firsttime,#percent-noanswerused').html(0);
        $('#nb-questions').html(collection.length);
        $('#bar-progress').html('').css('width','0');
        
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
        var questionAnswered = question.get('answered');
        
        //the question is found        
        if (value == question.get('answer') && !questionAnswered){
            
            $('#error-sign').hide();
            $('#check-sign').show();
            $('#answer-group').addClass('success');
            
            question.set('answered',true);            
            
            //deleting the id of the found element from the randomOrder array
            this.collection.randomOrder = _.without(
                this.collection.randomOrder,question.get('id')
            );
            
            this.questionFinished();
        } else {
            $('#error-sign').show();
            $('#answer-group').addClass('error');
            question.set('multiple',true);
        }
    },
    
    questionFinished: function(){
        
        $('#showanswerbutton,#submitbutton').attr('disabled','disabled');
        this.answer.attr('readonly','readonly');
        $('#nextbutton').focus();
            
    },
    
    showAnswer: function(){
        var question = this.collection.get(this.model.get('id'));
        $('#answer').val(question.get('answer'));
        this.questionFinished();
        
        question.set('multiple',true);
        question.set('answer_asked',true);     
    },
    
    newQuestion: function(e){
        e.preventDefault(); 
        this.initNewQuestion();
        
    },
    
    initNewQuestion: function(){
        $('#answer-group').removeClass('error').removeClass('success');
        $('.answer-sign').hide();
        
        var newQuestion = this.collection.getNewRandomModel(this.model.get('id'));
       
        if (newQuestion) {
            
            
            var text = newQuestion.get('text').replace(/%s/,'<img class="img-find" src="/images/icons/find.png" alt="icon-hole" style="max-height:30px"/>');
            
            this.model.set({'id': newQuestion.get('id')});
            this.$el.find('#text').html(text);
            $('.button-answer').removeAttr('disabled');
            this.answer.val('').focus().removeAttr('readonly'); 
        } else {
            $('#nextbutton').attr('disabled','disabled');
            this.endList();
        }
    },
    
    endList: function(){
        
        this.answer.attr('readonly','readonly');
        $('.button-answer').attr('disabled','disabled');
        $('#resetbutton').focus();
    },
    
    resetList: function(e){
        e.preventDefault();       
        this.resetRound();
    }
});


