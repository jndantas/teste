/*!
 * Signer
 * Version 1.0 - built Sat, Oct 6th 2018, 01:12 pm
 * https://simcycreative.com
 * Simcy Creative - <hello@simcycreative.com>
 * Private License
 */

 /*
  * close editor overlay
  */
  $(".close-editor-overlay").click(function(){
    if ($('.signer-assembler action').length > 0) {
      notify("Discard Changes?", "Your changes will be lost.", "warning", "Discard Changes", { showCancelButton: true, closeOnConfirm: true, callback: "closeEditor()" });
    }else{
      closeEditor();
    }
  });

 /*
  * function to close editor overlay
  */
  function closeEditor(){
    $('.signer-assembler').empty();
    renderPage(pageNum);
  	$(".signer-document").appendTo(".document");
  	$("body").removeClass("editor");
    emptyBuilder();
  };

 /*
  * launch editor overlay
  */
  $(".launch-editor").click(function(){
    inviting = false;
    enableTools();
  	launchEditor();
  });

 /*
  * function to launch editor overlay
  */
  function launchEditor(){
    if (inviting) { $(".signer-save span").text("Send"); }else{ $(".signer-save span").text("Save"); }
  	$(".signer-document").appendTo(".signer-overlay-previewer");
  	$("body").addClass("editor");
    renderPage(pageNum);
  };

 /*
  * Copy to clipboard
  */
  var clipboard = new Clipboard('.copy-link');
  clipboard.on('success', function(e) {
      $('#sharefile').modal('hide');
          toastr.success("Link copied to clipboard.", "Copied!");
  });
  clipboard.on('error', function(e) {
          toastr.error("Failed to copy, please try again.", "Oops!");
  });

 /*
  * validate email
  */
function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

 /*
  * Select users to send file
  */
  $("input[name=send-select]").change(function(){
    var email = $(this).val();
    if ($(this).prop("checked")) {
      $('input[name=receivers]').tagsinput('add', email);
    }else{
      $('input[name=receivers]').tagsinput('remove', email);
    }
  });

 /*
  * Select users to send request
  */
  $("input[name=request-select]").change(function(){
    var email = $(this).val();
    if ($(this).prop("checked")) {
      $('input[name=recipients]').tagsinput('add', email);
    }else{
      $('input[name=recipients]').tagsinput('remove', email);
    }
  });


 /*
  * Before an email is added to form
  */
  $('input[name=receivers], input[name=recipients]').on('beforeItemAdd', function(event) {
    if (!isEmail(event.item)) {
      event.cancel = true;
      toastr.error("Enter a valid email address.","Oops!");
    }
});


 /*
  * After an email is added
  */
$('input[name=recipients]').on('itemAdded', function(event) {
  requestOptions();
});

 /*
  * After an email is added
  */
$('input[name=recipients]').on('itemRemoved', function(event) {
  requestOptions();
});

 /*
  * When request oprions are updated
  */
  $("input[name=restricted], input[name=duplicate]").change(function(){
    requestOptions();
  });


 /*
  * Signing request options
  */
  function requestOptions(){
    recipients = $("input[name=recipients]").tagsinput('items');
    if (recipients.length > 1) {
      $(".duplicate-request").show();
      $(".restricted-request").hide();
      if ($("input[name=duplicate]").prop("checked")) {
        $(".restricted-request").show();
      }else{
        if ($("input[name=restricted]").prop("checked")) {
          $("input[name=restricted]").click();
        }
      }
    }else{
      $(".duplicate-request").hide();
      $(".restricted-request").show();
      if ($("input[name=duplicate]").prop("checked")) {
        $("input[name=duplicate]").click();
      }
    }
  }

 /*
  * validate request
  */
  function validateRequest(){
    if ($("input[name=restricted]").prop("checked")) {
      $("#sendRequest").modal("hide");
      inviting = true;
      launchEditor();
      enableTools("request");
    }else{
      sendRequest();
    }
  }

 /*
  * send request
  */
  function sendRequest(){
    var emails = JSON.stringify($("input[name=recipients]").tagsinput('items')),
          message = $("textarea[name=requestmessage]").val(),
          duplicate = "No", positions = docWidth = '';
    if ($("input[name=duplicate]").prop("checked")) { duplicate = "Yes"; }
    if (isTemplate === "Yes" && templateFields !== '') { 
      positions = JSON.stringify(templateFields); 
      docWidth = "set";
    }else if($("input[name=restricted]").prop("checked")){
      orgnizeData(false);
      positions = prepareData(false);
      docWidth = $("#document-viewer").width();
    }
    server({
        url: sendRequestUrl,
        data: {
            "emails": emails,
            "message": message,
            "positions": positions,
            "duplicate": duplicate,
            "docWidth": docWidth,
            "document_key": document_key,
            "csrf-token": Cookies.get("CSRF-TOKEN")
        },
        loader: true
    });
  }

 /*
  * Set document password toggle
  */
  $(".password-protect-toggle").change(function(){
    if ($(this).prop("checked")) {
      $('.protection-password').show();
      $('.protection-password').find("input").attr("required", true);
    }else{
      $('.protection-password').hide();
      $('.protection-password').find("input").attr("required", false);
    }
  });

/*
 * Tools responsiveness
 */
$('#send-team .col-md-12, #send-customers .col-md-12').slimscroll({
    height: '200px',
    width: '100%',
    size: "3px",
    color: 'rgba(0, 0, 0, 0.8)'
});

/*
 * Tools responsiveness
 */
$('.right-bar-body').slimscroll({
    height: 'auto',
    position: 'right',
    size: "3px",
    color: '#9ea5ab'
});

/*
 * Toggle right bar
 */
$(".right-bar-toggle").click(function(event){
  event.preventDefault();
  $(".chat-wrapper").scrollTop($(".chat-list")[0].scrollHeight);
  $(".right-bar."+$(this).attr("bar")).toggleClass("open");
  $(".right-bar-toggle").find("span").hide();
});

/*
 * Close
 */
$(".close-right-bar").click(function(event){
  event.preventDefault();
  $(this).closest(".right-bar").removeClass("open");
});

$(function() {
  var timeToAccelerate;
  var clickedElement;
  $(".arrow").on("mousedown", function() {
    clickedElement = $(this);
    updateValue(clickedElement);

    timeToAccelerate = setInterval(function() {
      updateValue(clickedElement);
    }, 150);
  });
  $(document).on("mouseup", function() {
    clearInterval(timeToAccelerate);
  });
  function updateValue(element) {
    var value = parseInt(element.siblings("input").val(), 10);
    if (element.hasClass("up")) {
      value += 1;
    } else {
      value -= 1;
      if (value < 0) value = 0;
    }
    element.siblings("input").val(value);  
    if (isDrawMode()) {
      modules.stroke(value);
    }else{
      updateTextSize(value);
    }
  }
});

/*
 * Post chat
 */
$(".new-message").keypress(function (e) {
    if(e.which == 13) {
      $(".empty-chat").remove();
      var message = $(this).val(), avatar = $(".user-avatar").attr("src"), chatId = random();
      $(".chat-list").append("<div class='chat-message chat-message-sender'><img class='chat-image chat-image-default' src='"+avatar+"' />"+
      "<div class='chat-message-wrapper'><div class='chat-message-content'><p>"+message+"</p></div><div class='chat-details'>"+
      "<span class='chat-message-localization font-size-small chat-"+chatId+"'>Sending....</span></div></div></div>");
      $(".chat-wrapper").scrollTop($(".chat-list")[0].scrollHeight);
      $(this).val("");
      e.preventDefault();
      server({
          url: postChatUrl,
          data: {
              "message": message,
              "chatId": chatId,
              "document_key": document_key,
              "csrf-token": Cookies.get("CSRF-TOKEN")
          },
          loader: false
      });
    }
});

/*
 *  chat response
 */
 function chatResponse(sendTime, chatKey, chatId){
    $('.chat-list').find(".chat-"+chatKey).text(sendTime);
    $('.chat-list').find(".chat-"+chatKey).closest(".chat-message").attr("id", chatId);
 }

/*
 *  fetch chats 
 */
function getChats() {
    var data =  {
              "lastChat": $('.chat-list').children().last().attr("id"),
              "document_key": document_key,
              "csrf-token": Cookies.get("CSRF-TOKEN")
          }
    var posting = $.post(getChatUrl, data);
    posting.done(function(data) {
      if (data != 'empty') {
        $(".chat-list").append(data);
        $(".chat-wrapper").scrollTop($(".chat-list")[0].scrollHeight);
        $(".empty-chat").remove();
        $('[data-toggle="tooltip"]').tooltip();
        if(!$(".right-bar").hasClass("open")){
          $(".right-bar").addClass("open");
        }
      }
    });
}


/*
 *  check for new chats after 5seconds 
 */
if (getChatUrl !== '') {
  setInterval(function() {
    if($(".chat-list").length){
      getChats();
    }
  }, 5000);
}


/*
 *  Signer tools select
 */
$(".signer-tool").click(function(event){
  event.preventDefault();
  if ($(this).hasClass("disabled")) {
    return false;
  }
  if ($(this).attr("action") === "true") {
    deselectElements();
    deactivateTools();
  }

  var tool = $(this).attr("tool");
  if (tool !== "rotate" && $('action[type=rotate]').length) {
    toastr.warning("Save rotation changes before editing document.","Hmm!", {timeOut: 2000, closeButton: true, progressBar: false});
    return false;
  }
  if (tool === "rotate") {
    if ($('.signer-builder .signer-element').length || modules.original !== $('#document-viewer').getCanvasImage("image/png")) {
      toastr.warning("Save changes before rotating.","Hmm!", {timeOut: 2000, closeButton: true, progressBar: false})
    }else{
      rotatePage(pageNum);
    }
  }else if(tool === "image"){
    $("#selectImage").modal({show: true, backdrop: 'static', keyboard: false});
  }else if(tool === "delete"){
    deleteElement();
  }else if(tool === "text"){
    enableTextMode();
  }else if(tool === "font"){
    $(".right-bar.font-list").toggleClass("open");
  }else if(tool === "symbol"){
    $(".right-bar.symbol-list").toggleClass("open");
  }else if(tool === "shape"){
    $(".right-bar.shape-list").toggleClass("open");
  }else if(tool === "fields"){
    if (auth) {
      $(".right-bar.fields-list").toggleClass("open");
    }else{
      loginRequired();
    }
  }else if(tool === "input"){
    if (auth) {
      if (isTemplate === "Yes" || inviting) {
        $(".right-bar.input-fields-list").toggleClass("open");
      }else{
        notify("Template Only", "Inputs are added to templates only. Do you want to create a template copy of this file?", "warning", "Yes, Create", { showCancelButton: true, closeOnConfirm: true, callback: "createTemplate()" });
      }
    }else{
      loginRequired();
    }
  }else if(tool === "color"){
    document.getElementById('color-picker').jscolor.show();
  }else if(tool === "duplicate"){
    duplicateSelected();
  }else if(tool === "signature"){
    enableSignatureMode();
  }else if(tool === "draw"){
    enableDrawMode();
  }else if(tool === "bold" || tool === "italic" || tool === "underline" || tool === "strikethrough" || tool === "alignright" || tool === "aligncenter" || tool === "alignleft"){
    styleText(tool);
  }
});

/*
 *  Rotate Page
 */
function rotatePage(pageNum){
    var degree = parseInt(getActualRotation(pageNum) + 90);
    if (degree == 360 ) { degree = 0; }
    assemble({"type": "rotate", "page": pageNum, "degree": degree});
    renderPage(pageNum);
    $("#document-viewer").css("max-width", "100%");
}

/*
 *  Get actual page rotation
 */
function getActualRotation(pageNumber){
    if($("action[type=rotate][page="+pageNumber+"]").length > 0){
      rotationDegree = parseInt($("action[type=rotate][page="+pageNumber+"]").attr("degree"));
    }else{
      rotationDegree = 0;
    }
  return rotationDegree;
}

/*
 *  Group completed actions
 */
function assemble(data, prepare){
  if (prepare === undefined) {
    send = true;
  }
  if (data.group === undefined) {
    data.group = "field";
  }
  if (data.type === "rotate") {
    if($("action[type=rotate][page="+data.page+"]").length > 0){
      if (data.degree == 0) {
        $("action[type=rotate][page="+data.page+"]").remove();
      }else{
        $("action[type=rotate][page="+data.page+"]").attr("degree", data.degree);
      }
    }else{
      $(".signer-assembler").append('<action type="rotate" group="'+data.group+'" page="'+data.page+'" degree="'+data.degree+'">');
    }
  }else if(data.type === "image" || data.type === "signature" || data.type === "symbol" || data.type === "shape"){
    if (data.group === "field") {
      $(".signer-assembler").append('<action type="'+data.type+'" group="'+data.group+'" page="'+data.page+'" xPos="'+data.xPos+'" yPos="'+data.yPos+'" width="'+data.width+'" height="'+data.height+'" image="'+data.image+'">');
    }else if (data.group === "input"){
      $(".signer-assembler").append('<action type="'+data.type+'" group="'+data.group+'" page="'+data.page+'" xPos="'+data.xPos+'" yPos="'+data.yPos+'" width="'+data.width+'" height="'+data.height+'">');
    }
  }else if(data.type === "drawing"){
    $(".signer-assembler").append('<action type="'+data.type+'" group="'+data.group+'" page="'+data.page+'" drawing="'+data.drawing+'" >');
  }else if(data.type === "text"){
    if (data.group === "field") {
      $(".signer-assembler").append('<action type="text" group="'+data.group+'" page="'+data.page+'" xPos="'+data.xPos+'" yPos="'+data.yPos+'" width="'+data.width+'" height="'+data.height+'" text="'+data.text+'" bold="'+data.bold+'" italic="'+data.italic+'" font="'+data.font+'" fontsize="'+data.fontsize+'">');
    }else if (data.group === "input"){
      $(".signer-assembler").append('<action type="text" group="'+data.group+'" page="'+data.page+'" xPos="'+data.xPos+'" yPos="'+data.yPos+'" width="'+data.width+'" height="'+data.height+'" text="'+data.text+'" bold="'+data.bold+'" italic="'+data.italic+'" font="'+data.font+'" fontsize="'+data.fontsize+'" fontfamily="'+data.fontfamily+'" underline="'+data.underline+'" strikethrough="'+data.strikethrough+'" color="'+data.color+'" align="'+data.align+'">');
    }
  }
  if (prepare) {
    prepareData();
  }
}

/*
 *  Signer Save click
 */
 $(".signer-save").click(function(event){
  event.preventDefault();
  if (inviting) {
    sendRequest();
  }else if ($('.signer-builder .signer-element').length || modules.original !== $('#document-viewer').getCanvasImage("image/png")) {
    orgnizeData();
  }else if ($('.signer-assembler action').length) {
    prepareData();
  }else{
    toastr.warning("No changes to save.","Hmm!");
    return false;
  }
 });

/*
 *  On font/stroke size change
 */
 $(".font-size").change(function(){
  size = parseInt($(this).val());
  if (isDrawMode()) {
    modules.stroke(size);
  }else{
    updateTextSize(size);
  }
 });

/*
 *  Font preview on mouseover
 */
 $(".font-item").mouseover(function(){
  if ($(".signer-element.selected-element[type=text]").length) {
    elem = $(".signer-element.selected-element[type=text]");
  }else{
    elem = $(".signer-element[type=text]");
  }
  elem.find(".writing-pad").css("font-family", $(this).attr("family"));
 });

/*
 *  Exit font preview
 */
 $(".font-item").mouseleave(function(){
  if ($(".signer-element.selected-element[type=text]").length) {
    elem = $(".signer-element.selected-element[type=text]");
  }else{
    elem = $(".signer-element[type=text]");
  }
  elem.each(function(){
    if(elem.attr("font") === undefined){
      elem.find(".writing-pad").css("font-family", "'Lato', sans-serif");
    }else{
      elem.find(".writing-pad").css("font-family", $(".font-item[font="+elem.attr("font")+"]").attr("family"));
    }
  })
 });

/*
 *  Update font of text
 */
 $(".font-item").click(function(){
  if ($(".signer-element.selected-element[type=text]").length) {
    elem = $(".signer-element.selected-element[type=text]");
  }else{
    elem = $(".signer-element[type=text]");
  }
  elem.attr("font", $(this).attr("font"));
  elem.find(".writing-pad").css("font-family", $(this).attr("family"));
  highlightSelectedFont($(this).attr("font"));
 });

/*
 *  select an element
 */
 $(".signer-builder").on("click", ".signer-element", function(){
  deselectElements();
  $(this).addClass("selected-element");
  if ($(this).attr("type") === "text") {
    $(this).find(".writing-pad").focus();
    if ($(this).attr("group") !== "field") {
      showActiveTextTools();
      updateColorPicker($(this).attr("color"));
      updateSelectedFontSize($(this).attr("font-size"));
      highlightSelectedFont($(this).attr("font"));
    }
  }else if ($(this).attr("type") === "signature") {
    if ($(this).attr("group") === "field") {
      if (!auth) {
        if(sessionStorage.getItem('signature') === null) {
          $("#updateSignature").modal({show: true, backdrop: 'static', keyboard: false});
        }else{
          $(this).attr("signed", "true")
          $(this).find("img").attr("src", sessionStorage.getItem('signature'));
        }
      }else if (signature !== '') {
        $(this).attr("signed", "true")
        $(this).find("img").attr("src", signature);
      }else{
          notify("Create signature?", "You don't have a signature yet, create one now on settings page under signature tab.", "info", "Create Signature", { showCancelButton: true, closeOnConfirm: true, callback: "redirect('"+settingsPage+"')" });
      }
    }
  }
  if ($(this).attr("group") === "field") {
    disableTools();
  }
 });

/*
 *  Organize data in action format before it's prepared
 */
function orgnizeData(prepare){
  stopOrganizing = false;
  if (prepare === undefined) { prepare = true; }
    if (modules.original !== $('#document-viewer').getCanvasImage("image/png")) {
      assemble({"type": "drawing", "page": pageNum, "drawing": $('#document-viewer').getCanvasImage("image/png") }, false);
    }
    $('.signer-builder .signer-element').each(function(index, value) {
      var signerElement = $(this), actionType = signerElement.attr('type'), thisImage;
      signerElement.show();
      viewerPosition = $("#document-viewer").offset();
      group = signerElement.attr('group');
      pageNumber = parseInt(signerElement.attr('page'));
      if (actionType === "image" || actionType === "signature" || actionType === "symbol" || actionType === "shape") {
        if (group === "field") {
          if (signerElement.attr("signed") === "false") {
            emptyAssembler();
            renderPage(pageNumber);
            signerElement.addClass("selected-element");
            notify("Hmm!", "A signature is required on page "+pageNumber+". Please sign to continue.", "info", "Sign Now");
            stopOrganizing = true;
            return false
          }
        }
        if (actionType === "symbol" || actionType === "shape") {
          signerElement.find("div").remove();
          thisImage = signerEscape(signerElement.html());
          elementWidth = signerElement.find("svg").width();
          elementHeight = signerElement.find("svg").height();
          elementPosition = signerElement.find("svg").offset();
        }else{
          thisImage = signerElement.find("img").attr('src');
          elementWidth = signerElement.find("img").width();
          elementHeight = signerElement.find("img").height();
          elementPosition = signerElement.find("img").offset();
        }
        elementXpos = elementPosition.left - viewerPosition.left;
        elementYpos = elementPosition.top - viewerPosition.top;
        assemble({"group": group, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "image": thisImage }, false);
      }else if(actionType === "text"){
        underline = italic = bold = strikethrough = align = fontfamily = '';
        fontsize = 14;
        font = "lato";
        textHolder = signerElement.find(".writing-pad");
        elementWidth = textHolder.width();
        elementHeight = textHolder.height();
        elementPosition = textHolder.offset();
        elementXpos = elementPosition.left - viewerPosition.left;
        elementYpos = elementPosition.top - viewerPosition.top;
        if (group === "field") {
          userInput = textHolder.text();
          if (!userInput.replace(/\s/g, '').length) {
            emptyAssembler();
            renderPage(pageNumber);
            signerElement.addClass("selected-element");
            notify("Hmm!", "An input on page "+pageNumber+" is empty. Please fill to continue.", "info", "Fill Now");
            stopOrganizing = true;
            return false
          }
        }
        if (group === "input") {
          if (signerElement.attr("bold") === "true") { bold = "bold"; }
          if (signerElement.attr("italic") === "true") { italic = "italic"; }
          if (signerElement.attr("strikethrough") === "true") { strikethrough = "strikethrough"; }
          if (signerElement.attr("underline") === "true") { underline = "underline"; }
          if (signerElement.attr("color") !== undefined) { color = signerElement.attr("color"); }
          if (signerElement.attr("font-size") !== undefined) { fontsize = signerElement.attr("font-size"); }
          if (signerElement.attr("align") !== undefined) { align = signerElement.attr("align"); }
          if (signerElement.attr("font") !== undefined) { font = signerElement.attr("font"); }
          if (signerElement.attr("font") !== undefined) { fontfamily = signerElement.find(".writing-pad").css("font-family"); }
          textBody = textHolder.html();
        }else{
          if (signerElement.attr("bold") === "true") { bold = "B"; }
          if (signerElement.attr("italic") === "true") { italic = "I"; }
          if (signerElement.attr("strikethrough") === "true") { strikethrough = "text-decoration: line-through;"; }
          if (signerElement.attr("underline") === "true") { underline = "text-decoration: underline;"; }
          if (signerElement.attr("color") !== undefined) { color = "color: "+signerElement.attr("color")+";"; }
          if (signerElement.attr("font-size") !== undefined) { fontsize = signerElement.attr("font-size"); }
          if (signerElement.attr("align") !== undefined) { align = "text-align: "+signerElement.attr("align")+";"; }
          if (signerElement.attr("font") !== undefined) { font = signerElement.attr("font"); }
          textBody = '<div style="'+align+strikethrough+underline+color+'">'+textHolder.html()+'</div>';
        }
        text = signerEscape(textBody);
        assemble({"group": group, "type": actionType, "page": pageNumber, "xPos": elementXpos, "yPos": elementYpos, "width": elementWidth, "height": elementHeight, "text": text, "align": align, "bold": bold, "italic": italic, "font": font, "fontsize": fontsize, "fontfamily": fontfamily, "underline": underline, "color": color, "strikethrough": strikethrough }, false);
      }

      if (pageNumber == pageNum) {
        signerElement.show();
      }else{
        signerElement.hide();
      }
    });
    if (stopOrganizing) {
      emptyAssembler();
      return false;
    }else{
      if (prepare) { prepareData(); }
    }
    
}

/*
 *  Prepare data before sending to database
 */
function prepareData(save){
  if (save === undefined) { save = true; }
    var actions = [];
    $('.signer-assembler action').each(function(index, value) {
      actions.push({
        type: $(this).attr('type'),
        page: $(this).attr('page'),
        degree: $(this).attr('degree'),
        xPos: $(this).attr('xPos'),
        yPos: $(this).attr('yPos'),
        width: $(this).attr('width'),
        height: $(this).attr('height'),
        image: $(this).attr('image'),
        text: $(this).attr('text'),
        align: $(this).attr('align'),
        bold: $(this).attr('bold'),
        italic: $(this).attr('italic'),
        fontsize: parseInt($(this).attr('fontsize')),
        fontfamily: $(this).attr('fontfamily'),
        font: $(this).attr('font'),
        group: $(this).attr('group'),
        underline: $(this).attr('underline'),
        strikethrough: $(this).attr('strikethrough'),
        color: $(this).attr('color'),
        drawing: $(this).attr('drawing')
      });
    });
    actions = JSON.stringify(actions);
    if (save) { saveChanges(actions); }else{ return actions; }
}

/*
 *  send actions to server
 */
function saveChanges(actions){
  server({
      url: signDocumentUrl,
      data: {
          "actions": actions,
          "docWidth": $("#document-viewer").width(),
          "document_key": document_key,
          "signing_key": signingKey,
          "csrf-token": Cookies.get("CSRF-TOKEN")
      },
      loader: true
  });
}

/*
 *  escape string
 */
function signerEscape(string){
  string = string.replace(/"/g, "%22");
  return string;
}

/*
 *  empty assembled data.
 */
function emptyAssembler(){
  $(".signer-assembler").empty();
}

/*
 *  empty builder data.
 */
function emptyBuilder(){
  $(".signer-builder").empty();
}

/*
 *  Duplicate selected element
 */
function duplicateSelected(){
    original = $('.signer-element.selected-element');
    duplicate = original.clone();
    duplicate.appendTo(".signer-builder");
    original.removeClass("selected-element")
    position = duplicate.position();
    if ($(window).width() < 1101) {
      topOffset = 225;
    }else{
      topOffset = 185;
    }
    currentOffset = $(".signer-overlay-previewer").offset();
    yPos = parseInt(position.top - currentOffset.top + topOffset);
    duplicate.css({top: parseInt(yPos + 30)+'px', left: parseInt(position.left + 30)+'px'});
    initElementsDrag();
  focusText();
}

/*
 *  Select image to add on PDF
 */
function selectDocImage(){
  showLoader()
  var reader  = new FileReader();
  reader.readAsDataURL(document.querySelector('input[name=document-selected-image]').files[0]);
  imageWidth = parseInt($("#document-viewer").width() - 30)
  reader.addEventListener("load", function () {
    $("#selectImage").modal("hide");
    hideLoader();
    $('<div class="signer-element selected-element" status="drop" resizeable="true" type="image" page="'+pageNum+'"><img src="'+reader.result+'" style="max-width:'+imageWidth+'px;opacity:0.5;"></div>').appendTo(".signer-builder");
    $( document ).mousemove(function( event ) {
      $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
    });
    disableTools();
    highlightCanvas();
  }, false);
}

/*
 *  Enable signature mode
 */
function enableSignatureMode(){
  if (!auth) {
        if(sessionStorage.getItem('signature') === null) {
          $("#updateSignature").modal({show: true, backdrop: 'static', keyboard: false});
        }else{
            imageWidth = parseInt($("#document-viewer").width() - 30);
            $('<div class="signer-element selected-element" status="drop" resizeable="true" type="signature" page="'+pageNum+'"><img src="'+sessionStorage.getItem('signature')+'" style="max-width:'+imageWidth+'px;width:200px;opacity:0.5;"></div>').appendTo(".signer-builder");
            $( document ).mousemove(function( event ) {
              $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
            });
            disableTools();
            highlightCanvas();
        }
  }else if (signature !== '') {
    imageWidth = parseInt($("#document-viewer").width() - 30);
    $('<div class="signer-element selected-element" status="drop" resizeable="true" type="signature" page="'+pageNum+'"><img src="'+signature+'" style="max-width:'+imageWidth+'px;width:200px;opacity:0.5;"></div>').appendTo(".signer-builder");
    $( document ).mousemove(function( event ) {
      $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
    });
    disableTools();
    highlightCanvas();
  }else{
    notify("Create signature?", "You don't have a signature yet, create one now on settings page under signature tab.", "info", "Create Signature", { showCancelButton: true, closeOnConfirm: true, callback: "redirect('"+settingsPage+"')" });
  }
}

/*
 *  When symbol is selected
 */
$(".symbol-item").click(function(){
  deselectElements();
  $(".right-bar.symbol-list").toggleClass("open");
  $('<div class="signer-element selected-element" status="drop" resizeable="true" color="'+selectedColor()+'" type="symbol" page="'+pageNum+'" style="width:40px;height:40px;">'+$(this).html()+'</div>').appendTo(".signer-builder").find("path").css("fill", selectedColor());
  $( document ).mousemove(function( event ) {
    $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
  });
  disableTools();
  highlightCanvas();
});

/*
 *  When custom field is selected
 */
$(".field-list").on("click", ".field-item div", function(event){
  event.preventDefault();
  deselectElements();
  $(".right-bar.fields-list").toggleClass("open");
  font = selectedFont();
  $('<div class="signer-element selected-element" status="drop" type="text" page="'+pageNum+'" '+currentTextStyle()+' font="'+font.font+'" color="'+selectedColor()+'" font-size="'+selectedFontSize()+'" style="position:absolute;"><div class="writing-pad" contenteditable="true" style="color:'+selectedColor()+';font-size:'+selectedFontSize()+'px;font-family:'+font.family+'"  spellcheck="false">'+$(this).text()+'</div></div>').appendTo(".signer-builder");
  $( document ).mousemove(function( event ) {
    $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
  });
  disableTools();
  highlightCanvas();
});

/*
 *  When custom field is selected
 */
$(".input-field-list").on("click", ".input-field-item div", function(event){
  event.preventDefault();
  deselectElements();
  $(".right-bar.input-fields-list").toggleClass("open");
  font = selectedFont();
  fieldLabel = $(this).text();
  if (fieldLabel == "Signature") {
    $('<div class="signer-element selected-element" status="drop" resizeable="true" type="image" group="input" page="'+pageNum+'"><img src="'+baseUrl+'assets/images/signhere.png" style="width:200px;opacity:0.5;"></div>').appendTo(".signer-builder");
  }else{
    $('<div class="signer-element selected-element" status="drop" type="text" resizeable="free" group="input" page="'+pageNum+'" '+currentTextStyle()+' font="'+font.font+'" color="'+selectedColor()+'" font-size="'+selectedFontSize()+'" style="position:absolute;"><div class="writing-pad" contenteditable="true" style="color:'+selectedColor()+';font-size:'+selectedFontSize()+'px;font-family:'+font.family+'"  spellcheck="false">'+$(this).text()+'</div></div>').appendTo(".signer-builder");
  }
  $( document ).mousemove(function( event ) {
    $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
  });
  disableTools();
  highlightCanvas();
});

/*
 *  When custom field is deleted
 */
$(".field-list").on("click", "#delete-field", function(event){
  event.preventDefault();
  fieldId = $(this).closest(".field-item").attr("id");
  $(this).closest(".field-item").remove();
  deleteField(fieldId);
});

/*
 *  When input field is deleted
 */
$(".input-field-list").on("click", "#delete-input-field", function(event){
  event.preventDefault();
  fieldId = $(this).closest(".input-field-item").attr("id");
  $(this).closest(".input-field-item").remove();
  deleteField(fieldId);
});

/*
 *  Delete custom or input field
 */
 function deleteField(fieldId){
    server({
      url: deleteFieldsUrl,
      data: {
          "fieldId": fieldId,
          "csrf-token": Cookies.get("CSRF-TOKEN")
      },
      loader: false
  });
 }

/*
 *  When shape is selected
 */
$(".shape-item").click(function(){
  deselectElements();
  $(".right-bar.shape-list").toggleClass("open");
  $('<div class="signer-element selected-element" status="drop" resizeable="true" color="'+selectedColor()+'" type="shape" page="'+pageNum+'" style="width:100px;height100px;">'+$(this).html()+'</div>').appendTo(".signer-builder").find("path").css("fill", selectedColor());
  $( document ).mousemove(function( event ) {
    $(".signer-element[status=drop]").css({ left:  event.pageX + 1, top:   event.pageY + 1  });
  });
  disableTools();
  highlightCanvas();
});


/*
 *  Make elements draggable
 */
 function initElementsDrag() {
   $(".signer-element").draggable({
    containment: $("#document-viewer"),
    drag: function() {
      highlightCanvas();
    },
    stop: function() {
      unHighlightCanvas();
    }
   });
 }


/*
 *  Make elements resizeable
 */
 function initElementsResize() {
   $(".signer-element[resizeable=true]").resizable({
      aspectRatio: true,
      autoHide: false,
      handles: "n, e, s, w, se, sw, nw, ne",
      resize: function(event, ui){
        ui.helper.find("img").width(ui.size.width - 10);
        ui.helper.find("img").height(ui.size.height - 12);
      }
    });
   $(".signer-element[resizeable=free]").resizable({
      autoHide: false,
      handles: "n, e, s, w, se, sw, nw, ne",
      resize: function(event, ui){
        ui.helper.find(".writing-pad").width(ui.size.width - 10);
        ui.helper.find(".writing-pad").height(ui.size.height - 12);
      }
    });
 }


/*
 *  Delete selected element
 */
 function deleteElement() {
  if (isDrawMode()) {
    modules.erase();
  }
  if ($(".signer-element.selected-element").length) {
    $(".signer-element.selected-element").remove();
    selectLastElement();
  }
 }


/*
 *  Select the last element
 */
 function selectLastElement() {
  if ($(".signer-element").length) {
    $(".signer-element[page="+pageNum+"]").last().addClass("selected-element");
  }
 }


/*
 *  Deselect all elements
 */
 function deselectElements() {
   $(".signer-element").removeClass("selected-element");
 }


/*
 *  hide all elements
 */
 function hideElements() {
   $(".signer-element").hide();
 }


/*
 *  Deactivate active tools
 */
 function deactivateTools() {
   $(".signer-tool").removeClass("active");
 }


/*
 *  Disable all tools
 */
 function disableTools() {
   $(".signer-tool").addClass("disabled");
 }


/*
 *  Enable all tools
 */
 function enableTools(group) {
  if (inviting) { group = "request"; }
  disableTools();
  $(".signer-tool[tool=delete], .signer-tool[action=true]").removeClass("disabled");
  if (group === "text") {
    $(".signer-tool[group=text], .signer-tool[tool=color], .signer-tool[tool=duplicate], .signer-tool[tool=fontsize]").removeClass("disabled");
  }else if(group === "symbol" || group === "shape"){
    $(".signer-tool[tool=color], .signer-tool[tool=duplicate]").removeClass("disabled");
  }else if(group === "image"){
    $(".signer-tool[tool=duplicate]").removeClass("disabled");
  }else if(group === "draw"){
    $(".signer-tool[tool=color], .signer-tool[tool=fontsize]").removeClass("disabled");
  }else if(group === "request"){
    disableTools();
    $(".signer-tool[tool=input], .signer-tool[group=text], .signer-tool[tool=color], .signer-tool[tool=duplicate], .signer-tool[tool=fontsize], .signer-tool[tool=delete]").removeClass("disabled");
  }else{
    $(".signer-tool").removeClass("disabled");
  }
 }


/*
 *  Show active text tools
 */
 function showActiveTextTools() {
  var elem = $(".signer-element.selected-element[type=text]");
  if (elem.attr("bold") === "true") {
    $(".signer-tool[tool=bold]").addClass("active");
  }else{
    $(".signer-tool[tool=bold]").removeClass("active");
  }
  if (elem.attr("italic") === "true") {
    $(".signer-tool[tool=italic]").addClass("active");
  }else{
    $(".signer-tool[tool=italic]").removeClass("active");
  }
  if (elem.attr("underline") === "true") {
    $(".signer-tool[tool=underline]").addClass("active");
  }else{
    $(".signer-tool[tool=underline]").removeClass("active");
  }
  if (elem.attr("strikethrough") === "true") {
    $(".signer-tool[tool=strikethrough]").addClass("active");
  }else{
    $(".signer-tool[tool=strikethrough]").removeClass("active");
  }
  if (elem.attr("align") === "left") {
    $(".signer-tool[tool=alignleft]").addClass("active");
  }else{
    $(".signer-tool[tool=alignleft]").removeClass("active");
  }
  if (elem.attr("align") === "left") {
    $(".signer-tool[tool=alignleft]").addClass("active");
  }else{
    $(".signer-tool[tool=alignleft]").removeClass("active");
  }
  if (elem.attr("align") === "right") {
    $(".signer-tool[tool=alignright]").addClass("active");
  }else{
    $(".signer-tool[tool=alignright]").removeClass("active");
  }
  if (elem.attr("align") === "center") {
    $(".signer-tool[tool=aligncenter]").addClass("active");
  }else{
    $(".signer-tool[tool=aligncenter]").removeClass("active");
  }
 }


/*
 *  Highlight document canvas
 */
 function highlightCanvas() {
   $("#document-viewer").addClass("active");
 }


/*
 *  Un highlight document canvas
 */
 function unHighlightCanvas() {
   $("#document-viewer").removeClass("active");
 }


/*
 *  Enable text mode
 */
 function enableTextMode() {
  $(".signer-tool[tool=text]").addClass("active");
  $("#document-viewer").css( 'cursor', 'text' );
  updateSelectedFontSize(14, "Font Size");
  highlightCanvas();
  enableTools("text");
 }


/*
 *  Enable drawing mode
 */
 function enableDrawMode() {
  $(".signer-tool[tool=draw]").addClass("active");
  $("#document-viewer").css( 'cursor', 'pointer' );
  updateSelectedFontSize(5, "Stroke Size");
  highlightCanvas();
  initEditor();
  enableTools("draw");
  if (modules.original === undefined) {
    modules.original = $('#document-viewer').getCanvasImage("image/png");
  }
 }


/*
 *  Check if draw mode is active
 */
 function isDrawMode() {
  if ($(".signer-tool[tool=draw]").hasClass("active")) {
    return true;
  }else{
    return false;
  }
 }


/*
 *  Initialize editor on scroll
 */
 $('.signer-overlay').off('scroll').on('scroll', function(){ 
  if (isDrawMode()) {
    initEditor();
  }
});


/*
 *  Get styling used by user
 */
 function currentTextStyle(){
  style = '';
  if ($(".signer-tool[tool=bold]").hasClass("active")) {
    style = style+' bold="true"';
  }
  if ($(".signer-tool[tool=italic]").hasClass("active")) {
    style = style+' italic="true"';
  }
  if ($(".signer-tool[tool=underline]").hasClass("active")) {
    style = style+' underline="true"';
  }
  if ($(".signer-tool[tool=strikethrough]").hasClass("active")) {
    style = style+' strikethrough="true"';
  }
  if ($(".signer-tool[tool=alignleft]").hasClass("active")) {
    style = style+' align="left"';
  }
  if ($(".signer-tool[tool=alignright]").hasClass("active")) {
    style = style+' align="right"';
  }
  if ($(".signer-tool[tool=aligncenter]").hasClass("active")) {
    style = style+' align="center"';
  }
  return style;
 }


/*
 *  Get selected color
 */
function selectedColor(){
  color = $(".signer-tool[tool=color]").attr("color");
  return color;
}


/*
 *  Get selected font
 */
function selectedFont(){
  font = {
    "font": $(".font-item.selected").attr("font"),
    "family": $(".font-item.selected").attr("family")
  };
  return font;
}


/*
 *  Updated selected value of color picker
 */
function updateColorPicker(color){
  colorValue = color.replace("#", "");
  document.getElementById('color-picker').jscolor.fromString(colorValue);
  $(".signer-tool[tool=color]").attr("color", color);
  return true;
}


/*
 *  Get selected font size
 */
function selectedFontSize(){
  fontSize = $(".font-size").val();
  return fontSize;
}


/*
 *  Updated selected font size
 */
function updateSelectedFontSize(fontSize, label){
  $(".font-size").val(fontSize);
  if (label !== undefined) {
    $(".font-size-label").text(label);
  }
  return true;
}


/*
 *  Updated selected font 
 */
function highlightSelectedFont(font){
  $(".font-item").removeClass("selected");
  $(".font-item[font="+font+"]").addClass("selected");
  return true;
}

/*
 *  Add text to canvas
 */
 function addText(xPos, yPos, text, style, color, fontSize, font, page) {
  deselectElements();
  if (text === undefined) { text = ""; }
  if (style === undefined) { style = currentTextStyle(); }
  if (color === undefined) { color = selectedColor(); }
  if (font === undefined) { font = selectedFont(); }
  if (fontSize === undefined) { fontSize = selectedFontSize(); }
  if (page === undefined) { page = pageNum; }
  if ($(window).width() < 1101) {
    topOffset = 225;
  }else{
    topOffset = 185;
  }
  currentOffset = $(".signer-overlay-previewer").offset();
  yPos = parseInt(yPos - currentOffset.top + topOffset);
  $('<div class="signer-element selected-element" type="text" page="'+page+'" '+style+' font="'+font.font+'" color="'+color+'" font-size="'+fontSize+'" style="left:'+parseInt(xPos - 5)+'px;top:'+parseInt(yPos - 15)+'px;position:absolute;"><div class="writing-pad" contenteditable="true" style="color:'+color+';font-size:'+fontSize+'px;font-family:'+font.family+'"  spellcheck="false">'+text+'</div></div>').appendTo(".signer-builder");
  initElementsDrag();
  focusText();
 }


/*
 *  Update selected element color
 */
 function updateColor(color){
  element = $(".signer-element.selected-element");
  $(".signer-tool[tool=color]").attr("color", "#"+color);
  if (element.attr("type") === "text") {
    element.attr("color", "#"+color);
    element.find(".writing-pad").css("color", "#"+color);
  }else if (element.attr("type") === "symbol" || element.attr("type") === "shape") {
    element.find("path").css("fill", "#"+color)
    element.attr("color", "#"+color);
  }else if(isDrawMode()){
    modules.color(color);
  }else if(element.length == 0){
    $(".signer-element[type=text]").attr("color", "#"+color);
    $(".signer-element[type=text]").find(".writing-pad").css("color", "#"+color);
  }
 }


/*
 *  Update selected element font size
 */
 function updateTextSize(fontSize){
  if ($(".signer-element.selected-element[type=text]").length) {
    elem = $(".signer-element.selected-element[type=text]");
  }else{
    elem = $(".signer-element[type=text]");
  }
  elem.attr("font-size", fontSize);
  elem.find(".writing-pad").css("font-size", fontSize+"px");
 }


/*
 *  Focus on selected text
 */
 function focusText(){
  $(".signer-element.selected-element[type=text]").find(".writing-pad").focus();
 }


/*
 *  Style text
 */
function styleText(style, value){
  if ($(".signer-element.selected-element[type=text]").length) {
    elem = $(".signer-element.selected-element[type=text]");
  }else{
    elem = $(".signer-element[type=text]");
  }
  if (style === "bold") {
    if (elem.attr("bold") === "true") {
      elem.removeAttr("bold");
    }else{
      elem.attr("bold", "true");
    }
  }
  if (style === "italic") {
    if (elem.attr("italic") === "true") {
      elem.removeAttr("italic");
    }else{
      elem.attr("italic", "true");
    }
  }
  if (style === "underline") {
    if (elem.attr("underline") === "true") {
      elem.removeAttr("underline");
    }else{
      elem.attr("underline", "true");
    }
  }
  if (style === "strikethrough") {
    if (elem.attr("strikethrough") === "true") {
      elem.removeAttr("strikethrough");
    }else{
      elem.attr("strikethrough", "true");
    }
  }
  if (style === "alignleft") {
      elem.attr("align", "left");
  }
  if (style === "aligncenter") {
      elem.attr("align", "center");
  }
  if (style === "alignright") {
      elem.attr("align", "right");
  }
  showActiveTextTools();
}


/*
 *  When any area on the overlay is clicked
 */
$(".signer-overlay").click(function(event){
  event.preventDefault();
  if ($(".signer-element[status=drop]").length > 0) {
    if (event.target.id === "document-viewer") {
      $(".signer-element[status=drop]").css("top", parseInt(event.pageY + $( ".signer-overlay" ).scrollTop()));
      $(".signer-element").removeAttr("status");
      $(".signer-element").css('position', 'absolute');
      $(".signer-element img").css('opacity', '1');
      enableTools($(".signer-element.selected-element").attr("type"));
      unHighlightCanvas();
      initElementsDrag();
      initElementsResize();
    }
  }else if($(".signer-tool.active[tool=text]").length && event.target.id === "document-viewer"){
    addText(event.pageX, event.pageY);
  }
});

/*
 *  Add custom fields
 */
function addField(){
  $("#addField").modal("hide");
  fieldValue = $("input[name=fieldvalue]").val();
  fieldLabel = $("input[name=fieldlabel]").val();
  fieldId = random();
  $(".field-list").append('<div class="field-item field-'+fieldId+'"><a class="delete-field" id="delete-field" href=""><i class="ion-ios-trash-outline" id="delete-field"></i></a><div>'+fieldValue+'</div> <span class="text-muted text-xs">'+fieldLabel+'</span> </div>');
  server({
      url: saveFieldsUrl,
      data: {
          "fieldId": fieldId,
          "fieldvalue": fieldValue,
          "fieldlabel": fieldLabel,
          "csrf-token": Cookies.get("CSRF-TOKEN")
      },
      loader: false
  });
}

/*
 *  Add input fields
 */
function addInputField(){
  $("#addInputField").modal("hide");
  inputfieldlabel = $("input[name=inputfieldlabel]").val();
  fieldId = random();
  $(".input-field-list").append('<div class="input-field-item input-field-'+fieldId+'"><a class="delete-input-field" id="delete-input-field" href=""><i class="ion-ios-trash-outline" id="delete-input-field"></i></a><div>'+inputfieldlabel+'</div></div>');
  if ($("input[name=savefield]").prop("checked")) {
    server({
        url: saveFieldsUrl,
        data: {
            "fieldId": fieldId,
            "type": "input",
            "fieldlabel": inputfieldlabel,
            "fieldvalue": '',
            "csrf-token": Cookies.get("CSRF-TOKEN")
        },
        loader: false
    });
  }
}

/*
 *  Field response
 */
 function fieldResponse(chatKey, chatId){
    $('.fields-list').find(".field-"+chatKey).closest(".field-item").attr("id", chatId);
    $("input[name=fieldvalue], input[name=fieldlabel]").val('');
 }

/*
 *  Field response
 */
 function inputFieldResponse(chatKey, chatId){
    $('.input-field-list').find(".input-field-"+chatKey).closest(".input-field-item").attr("id", chatId);
    $("input[name=inputfieldlabel]").val('');
 }

/*
 *  Create Template copy
 */
 function createTemplate(){
  server({
      url: createTemplateUrl,
      data: {
          "document_key": document_key,
          "csrf-token": Cookies.get("CSRF-TOKEN")
      },
      loader: true
  });
 }


/*
 *  Scale dimesions compared to the previous render
 */
 function signerScale(dimesion){
  templateWidth = $("#document-viewer").width();
  templateScale = parseFloat(templateWidth / savedWidth).toFixed(3);
  scaled = parseFloat(templateScale * dimesion).toFixed(3);
  return parseFloat(scaled).toFixed(3);
 }


/*
 *  Scale dimesions compared to the previous render (Accept request)
 */
 function signerScaler(dimesion){
  templateWidth = $("#document-viewer").width();
  templateScale = parseFloat(templateWidth / requestWidth).toFixed(3);
  scaled = parseFloat(templateScale * dimesion).toFixed(3);
  return parseFloat(scaled).toFixed(3);
 }


/*
 *  Show Template Fields
 */
 function showTemplateFields(){
  if (isTemplate === "Yes" && templateFields !== '' && $("body").hasClass("editor") && $(".signer-builder").is(':empty')) {
  if ($(window).width() < 1101) {
    topOffset = 225;
  }else{
    topOffset = 185;
  }
  currentOffset = $(".signer-overlay-previewer").offset();
  currentDocOffset = $("#document-viewer").offset();
  currentPosition = $("#document-viewer").position();
  
    $.each( templateFields, function( i, field ) {
      xPos = parseFloat(parseFloat(signerScale(field.xPos)) + currentDocOffset.left - 5).toFixed(3);
      yPos = parseFloat((parseFloat(signerScale(field.yPos)) + currentDocOffset.top) - currentOffset.top + topOffset - 5).toFixed(3);
      if (field.type == "image") {
        $('<div class="signer-element" resizeable="true" type="image" group="input" page="'+field.page+'" style="display:none;left:'+xPos+'px;top:'+yPos+'px;position:absolute;"><img src="'+baseUrl+'assets/images/signhere.png" style="width:'+signerScale(field.width)+'px;"></div>').appendTo(".signer-builder");
        initElementsDrag();
      }else if(field.type == "text"){
        if (field.align !== '') { field.align = ' align="'+field.align+'"'; }
        if (field.strikethrough !== '') { field.strikethrough = ' strikethrough="true"'; }
        if (field.underline !== '') { field.underline = ' underline="true"'; }
        if (field.bold !== '') { field.bold = ' bold="true"'; }
        if (field.italic !== '') { field.italic = ' italic="true"'; }
        $('<div class="signer-element" type="text" resizeable="free" group="input" '+field.align+field.italic+field.bold+field.underline+field.strikethrough+'  page="'+field.page+'" font="'+field.font+'" color="'+field.color+'" font-size="'+field.fontsize+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;"><div class="writing-pad" contenteditable="true" style="width:'+signerScale(field.width)+'px;height:'+signerScale(field.height)+'px;color:'+field.color+';font-size:'+field.fontsize+'px;font-family:'+field.fontfamily+';color:'+field.color+';"  spellcheck="false">'+field.text+'</div></div>').appendTo(".signer-builder");
      }
      hideElements();
    });
    $("[page="+pageNum+"]").show();
    initElementsDrag();
    initElementsResize();
  }
}

/*
 *  When accept request is clicked
 */
$(".accept-request").click(function(event){
  event.preventDefault();
  $("body").addClass("accept");
  inviting = false;
  launchEditor();
})

/*
 *  Accept request
 */
function acceptRequest(){
  if ($("body").hasClass("accept") && requestPositions.length) {
    showLoader();
    if ($(window).width() < 1101) {
      topOffset = 225;
    }else{
      topOffset = 185;
    }
    currentOffset = $(".signer-overlay-previewer").offset();
    currentDocOffset = $("#document-viewer").offset();
    currentPosition = $("#document-viewer").position();
    textInputs = [];
    $.each( requestPositions, function( i, field ) {
      xPos = parseFloat(parseFloat(signerScaler(field.xPos)) + currentDocOffset.left - 5).toFixed(3);
      yPos = parseFloat((parseFloat(signerScaler(field.yPos)) + currentDocOffset.top) - currentOffset.top + topOffset - 5).toFixed(3);
      if (field.type == "image") {
        $('<div class="signer-element" type="signature" signed="false" group="field" page="'+field.page+'" style="display:none;left:'+xPos+'px;top:'+yPos+'px;position:absolute;"><img src="'+baseUrl+'assets/images/signhere.png" style="width:'+signerScaler(field.width)+'px;"></div>').appendTo(".signer-builder");
      }else if(field.type == "text"){
        elementId = random({ case: "lower" });
        textInputs.push({ label: field.text, element: elementId });
        if (field.align !== '') { field.align = ' align="'+field.align+'"'; }
        if (field.strikethrough !== '') { field.strikethrough = ' strikethrough="true"'; }
        if (field.underline !== '') { field.underline = ' underline="true"'; }
        if (field.bold !== '') { field.bold = ' bold="true"'; }
        if (field.italic !== '') { field.italic = ' italic="true"'; }
        $('<div class="signer-element element-'+elementId+'" type="text" group="field" '+field.align+field.italic+field.bold+field.underline+field.strikethrough+'  page="'+field.page+'" font="'+field.font+'" color="'+field.color+'" font-size="'+field.fontsize+'" style="position:absolute;display:none;left:'+xPos+'px;top:'+yPos+'px;"><div class="writing-pad" contenteditable="true" style="width:'+signerScaler(field.width)+'px;height:'+signerScaler(field.height)+'px;color:'+field.color+';font-size:'+field.fontsize+'px;font-family:'+field.fontfamily+';color:'+field.color+';"  spellcheck="false">'+field.text+'</div></div>').appendTo(".signer-builder");
      }
      hideElements();
    });
    if (textInputs.length) {
      $.each( textInputs, function( i, input ) {
        $(".requested-fields").append('<div class="col-md-6"><div class="form-group"><label>'+input.label+'</label><input type="text" data-id="'+input.element+'" class="form-control" placeholder="'+input.label+'" required></div></div>')
      });
      $("#requestFields").modal({show: true, backdrop: 'static', keyboard: false});
    }
    $("[page="+pageNum+"]").show();
    hideLoader();
  }
}

/*
 *  Put data from requsted fields form to the PDF
 */
function updateRequestFields(){
  $(".requested-fields input").each( function( i, input ) {
    elementId = $(this).attr("data-id");
    $(".signer-element.element-"+elementId).find(".writing-pad").text($(this).val());
  });
  $("#requestFields").modal("hide");
  $("body").removeClass("accept");
  disableTools();
}

/*
 *  Login restricted
 */
function loginRequired(){
  notify("Login Required", "You need to login to access this feature.", "warning", "Login Now", { showCancelButton: true, closeOnConfirm: true, callback: "redirect('"+loginPage+"')" });
  return false;
}






