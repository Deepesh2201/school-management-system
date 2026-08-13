 $(document).on('focus', ':input', function() {
     $(this).attr('autocomplete', 'off');
 });


modal_click_disabled=(...params)=>{

  for (i=0; i<params.length; i++) {
  
         $('#'+params[i]).modal({
         backdrop: 'static',
         keyboard: false,
         show: false
     });
  }


}

function modal_loader_div(){
    var div_top = $('<div/>');
    var div_model = $('<div/>').addClass('modal_body_loader');
    var sub_img = $('<img/>').attr('src', 'https://dev.webfeb.com/ss640dev/backend/images/chatloading.gif').appendTo(div_model);
    var div_loader = $('<div/>').addClass('modal_body_loader_fade');
    div_top.append(div_loader).append(div_model);
    return div_top;
}

    function reset_form(selector){
        $(selector).find('input:text, input:password, input:file, select, textarea').val('');
        $(selector).find('input:radio, input:checkbox').prop('checked',false);
        $(selector +" select option").prop("selected", false);
    }


 $(document).ready(function() {

       $('body').popover({
    selector: '[data-toggle="popover"]',
    trigger: 'hover',
    container: 'body',
    html: true,
    content: function () {
         return $(this).closest('td').find('.fee_detail_popover').html();
    }
});
     $('#sessionModal').modal({
         backdrop: 'static',
         keyboard: false,
         show: false
     })
     $('#sessionModal').on('show.bs.modal', function(event) {
         var $modalDiv = $(event.delegateTarget);
         $('.sessionmodal_body').html("");
         $.ajax({
             type: "POST",
             url: baseurl + "admin/admin/getSession",
             dataType: 'text',
             data: {},
             beforeSend: function() {
                 $modalDiv.addClass('modal_loading');
             },
             success: function(data) {
                 $('.sessionmodal_body').html(data);
             },
             error: function(xhr) { // if error occured
                 $modalDiv.removeClass('modal_loading');
             },
             complete: function() {
                 $modalDiv.removeClass('modal_loading');
             },
         });
     })
     $(document).on('click', '.submit_session', function() {
         var $this = $(this);
         var datastring = $("form#form_modal_session").serialize();
         $.ajax({
             type: "POST",
             url: baseurl + "admin/admin/updateSession",
             dataType: "json",
             data: datastring,
             beforeSend: function() {
                 $this.button('loading');
             },
             success: function(data) {
                 if (data.status == 1) {
                     $('#sessionModal').modal('hide');
                     window.location.href = baseurl + "admin/admin/dashboard";
                       successMsg(data.message);
                 }
             },
             error: function(xhr) {
                 $this.button('reset');
             },
             complete: function() {
                 $this.button('reset');
             },
         });
     });
    
     //=============Sticky header==============
     $('#alert').affix({
         offset: {
             top: 10,
             bottom: function() {}
         }
     })
     $('#alert2').affix({
         offset: {
             top: 20,
             bottom: function() {}
         }
     })
     //========================================
     //==============User Quick session============
     $('#user_sessionModal').modal({
         backdrop: 'static',
         keyboard: false,
         show: false
     })
     $('#user_sessionModal').on('show.bs.modal', function(event) {
         var $modalDiv = $(event.delegateTarget);
         $('.user_sessionmodal_body').html("");
         $.ajax({
             type: "POST",
             url: baseurl + "common/getSudentSessions",
             dataType: 'text',
             data: {},
             beforeSend: function() {
                 $modalDiv.addClass('modal_loading');
             },
             success: function(data) {
                 $('.user_sessionmodal_body').html(data);
             },
             error: function(xhr) { // if error occured
                 $modalDiv.removeClass('modal_loading');
             },
             complete: function() {
                 $modalDiv.removeClass('modal_loading');
             },
         });
     });
     $(document).on('click', '.submit_usersession', function() {
         var $this = $(this);
         var datastring = $("form#form_modal_usersession").serialize();
         $.ajax({
             type: "POST",
             url: baseurl + "common/updateSession",
             dataType: "json",
             data: datastring,
             beforeSend: function() {
                 $this.button('loading');
             },
             success: function(data) {
                 if (data.status == 1) {
                     $('#sessionModal').modal('hide');
                     window.location.href = baseurl + "user/user/dashboard";
                      successMsg(data.message);
                 }
             },
             error: function(xhr) {
                 $this.button('reset');
             },
             complete: function() {
                 $this.button('reset');
             },
         });
     });
     //====================
     $('#commanSessionModal').modal({
         backdrop: 'static',
         keyboard: false,
         show: false
     });
     $('#commanSessionModal').on('show.bs.modal', function(event) {
         var $modalDiv = $(event.delegateTarget);
         $('.commonsessionmodal_body').html("");
         $.ajax({
             type: "POST",
             url: baseurl + "common/getAllSession",
             dataType: 'text',
             data: {},
             beforeSend: function() {
                 $modalDiv.addClass('modal_loading');
             },
             success: function(data) {
                 $('.commonsessionmodal_body').html(data);
             },
             error: function(xhr) { // if error occured
                 $modalDiv.removeClass('modal_loading');
             },
             complete: function() {
                 $modalDiv.removeClass('modal_loading');
             },
         });
     });
     $(document).on('click', '.submit_common_session', function() {
         var $this = $(this);
         var datastring = $("form#form_modal_commonsession").serialize();
         $.ajax({
             type: "POST",
             url: baseurl + "common/updateSession",
             dataType: "json",
             data: datastring,
             beforeSend: function() {
                 $this.button('loading');
             },
             success: function(data) {
                 if (data.status == 1) {
                     $('#sessionModal').modal('hide');
                     window.location.href = data.redirect_url;
                   successMsg(data.message);
                 }
             },
             error: function(xhr) {
                 $this.button('reset');
             },
             complete: function() {
                 $this.button('reset');
             },
         });
     });
 });
