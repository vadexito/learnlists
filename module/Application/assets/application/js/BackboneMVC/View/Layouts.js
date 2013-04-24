LearnListsLayout = Backbone.Marionette.Layout.extend({
    template: "#layout-template",
    className:'row-fluid well',
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
      middle_top    : '#middle_top-side-region',
      middle        : '#middle-side-region',
      bottom        : '#bottom-side-region'
    }
});