/*
Project: Signer | Create Digital signatures and Sign PDF documents online
Author: Simcy Creative
File: Files js
*/

    $(document).ready(function() {
        /*
         * Load documents when page is ready
         */
        loadDocuments();
    });
        
    // when folder is right clicked
    var $folderMenu = $("#folder-menu"),
          $fileMenu = $("#file-menu");
    $("body").on("contextmenu", ".data-folder", function(e) {
        $("body").find(".signer-active-select").removeClass("signer-active-select");
        $(this).addClass("signer-active-select");
        $fileMenu.hide();
        $folderMenu.css({
            display: "block",
            left: e.pageX,
            top: e.pageY
        });
        return false;
    });

    // when file is right clicked
    $("body").on("contextmenu", ".data-file", function(e) {
        $("body").find(".signer-active-select").removeClass("signer-active-select");
        $(this).addClass("signer-active-select");
        $folderMenu.hide();
        $fileMenu.css({
            display: "block",
            left: e.pageX,
            top: e.pageY
        });
        return false;
    });

    // close right click menu
    $('body').click(function() {
        $folderMenu.hide();
        $fileMenu.hide();
    });

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
      * Check if device is a mobile device
      */
    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };


// drag and drop feature
function dragAction() {
    if(!isMobile.any()) {
        $(".content-list").selectable({
            start: function(event, ui) {
                $folderMenu.hide();
                $fileMenu.hide();
                $(".dropdown").removeClass("open");
            },        
            selected: function(event, ui) {
                docListTools();
            },
            unselecting: function(event, ui) {
                docListTools();
            }
        });
    }

    $(".content-list").find(".folder").draggable({
        revert: 'false',
        helper: function(event, ui) {
            var $group = $("<div>", {
                class: "content-list"
            });
            $(".folder[data-type=folder]").droppable("enable");
            if ($(".folder.ui-selected").length) {
                var theClone = $(".folder.ui-selected").clone();
                theClone = $group.html(theClone);
                $(".folder.ui-selected[data-type=folder]").droppable("disable");
            } else {
                theClone = $group.html($(this).clone());
            }
            // 
            return theClone;
        }
    });
    $(".content-list").find(".data-folder").droppable({
        tolerance: 'pointer',
        drop: function(event, ui) {
            var data = [];
            for (let item of ui.helper[0].children) {
                data.push({
                        type: item.dataset.type,
                        sourceid: item.dataset.id,
                        destination: $(this).attr("data-id")
                    });
            }
            relocate(data);
            if ($(".folder.ui-selected").length) {
                $(".folder.ui-selected").addClass("relocating");
                $(".folder.ui-selected").hide();
            } else {
                ui.draggable.addClass("relocating");
                ui.draggable.hide();
            }

            $(this).removeClass("over-now");
        },
        over: function(event, ui) {
            $(this).addClass("over-now");
        },
        out: function(event, ui) {
            $(this).removeClass("over-now");
        }
    });

    $(".content-list div").click(function(event) {
        if (!$(this).hasClass("ui-selected")) {
            if (event.metaKey) {
                $(this).addClass("ui-selected");
            }else{
                $(this).addClass("ui-selected").siblings().removeClass("ui-selected");
            }
        }else{
            $(this).removeClass("ui-selected");
        }
        docListTools();
    });
}

// show or hide doc tools on listing page
function docListTools() {
    // tools to show
    if ($(".folder.ui-selected").length > 1) {
        $(".select-option [action=open], .select-option [action=share], .select-option [action=rename]").attr("disabled", true);
    }else{
        $(".select-option [action=open], .select-option [action=share], .select-option [action=rename]").attr("disabled", false);
    }

    // show hide toogle
    if ($(".folder.ui-selected").length > 0) {
        $(".select-option").addClass("show");
    } else {
        $(".select-option").removeClass("show");
    }
}

// initialize drag and drop
dragAction();


/*
 * close document modals, reset form inputs
 */
 function documentsReset(){
    // close modals
    $(".modal").modal("hide");
    $("input[type=text]").val("");
    $(".dropify-clear").click();
 }


/*
 * Move files and folders to another directory
 */
 function relocate(data){
    $(".relocating").remove();
    server({
        url: relocateDocumentsUrl,
        data: {
            "data": data,
            "csrf-token": Cookies.get("CSRF-TOKEN")
        },
        loader: false
    });
 }


/*
 * Documents callback
 */
 function documentsCallback(){
    // reset
    documentsReset();
    // load documents 
    loadDocuments();
 }

/*
 * load documents 
 */
 function loadDocuments(password){
    $(".content-list").html('<div class="loader-box"><div class="circle-loader"></div></div>');
    if (docType === "documents") {
        filter = {};
        if (password === undefined) { password = ""; }
        var posting = $.post(documentsUrl, { "folder": $("input[name=folder]").val(),  "search": $("input[name=search]").val(),  "status": $("input[name=status]:checked").val(), "type": $("input[name=type]:checked").val(), "extension": $("input[name=extension]:checked").val(), "password": password, "csrf-token": Cookies.get("CSRF-TOKEN") });
    }else{
        var posting = $.post(templatesUrl, { "csrf-token": Cookies.get("CSRF-TOKEN") });
    }
    posting.done(function (documents) {
        $(".content-list").html(documents);
        $('[data-toggle="tooltip"]').tooltip();
        dragAction();
        $(".select-option").removeClass("show");
    });
 }

/*
 *  open File 
 */
 function openFile(document_key){
    showLoader();
    redirect(appUrl+"/document/"+document_key);
 }

/*
 *  open folder 
 */
 function openFolder(folderId, folderName){
    var currentFolder = $("input[name=folder]").val();
    if (currentFolder === folderId) {
        return false;
    }
    if (!folderName && folderId > 1) {
        $(".breadcrumbs").find(".breadcrumbs-item span[data-id="+folderId+"]").parent().nextAll().remove();
    }else if (folderId > 1){
        $(".breadcrumbs").append('<span class="breadcrumbs-item"> > <span data-id="'+folderId+'">'+folderName+'<span></span>');
    }
    $("input[name=folder]").val(folderId);
    if (folderId > 1) {
        $(".go-back").show();
    }else{
        $(".go-back").hide();
    }
    loadDocuments();
 }

/*
 *  rename folder 
 */
 function renameFolder(folderId, folderName){
    $("#renamefolder input[name=folderid]").val(folderId);
    $("#renamefolder input[name=foldername]").val(folderName);
    $("#renamefolder").modal({show: true, backdrop: 'static', keyboard: false});
 }

/*
 *  rename File 
 */
 function renameFile(fileId, fileName){
    $("#renamefile input[name=fileid]").val(fileId);
    $("#renamefile input[name=filename]").val(fileName);
    $("#renamefile").modal({show: true, backdrop: 'static', keyboard: false});
 }

/*
 * double click on folder
 */
$(".documents-grid").on('dblclick', '.data-folder', function() {
    openFolder($(this).attr("data-id"), $(this).attr("data-original-title"));
});


/*
 * folder protection form
 */
$("body").on("submit", ".folder-protection-form", function (event) {
    event.preventDefault();
    $(this).parsley().validate();
    if (($(this).parsley().isValid())) {
        loadDocuments($("input[name=folderpassword]").val());
    }
});

/*
 * double click on file
 */
$(".documents-grid").on('dblclick', '.data-file', function() {
    openFile($(this).attr("document_key"));
});


/*
 * Save file imported from dropbox
 */
 function saveDropboxFile(file){
    server({
        url: dropboxUrl,
        data: {
            "name": file.name,
            "url": file.link,
            "folder": $("input[name=folder]").val(),
            "csrf-token": Cookies.get("CSRF-TOKEN")
        },
        loader: true
    });
 }


/*
 * Save file imported from google drive
 */
 function saveGoogleDriveFile(fileId, fileName, service){
    server({
        url: googledriveimportUrl,
        data: {
            "name": fileName,
            "fileId": fileId,
            "service": service,
            "folder": $("input[name=folder]").val(),
            "csrf-token": Cookies.get("CSRF-TOKEN")
        },
        loader: true
    });
 }

/*
 * Folder actions
 */
 $(".folder-actions").on("click",  "a", function(event){
    event.preventDefault();
    var action = $(this).attr("action");
    if (action === "open") {
        openFolder($(".signer-active-select").attr("data-id"), $(".signer-active-select").attr("data-original-title"));
    }else if (action === "rename") {
        renameFolder($(".signer-active-select").attr("data-id"), $(".signer-active-select").attr("data-original-title"));
    }else if (action === "delete") {
        $(".signer-active-select").addClass("deleted");
        $(".signer-active-select").hide();
        server({ 
            url: deleteFolderUrl,
            data: {
                "folder": $(".signer-active-select").attr("data-id"),
                "csrf-token": Cookies.get("CSRF-TOKEN")
            },
            loader: false
        });
    }else if (action === "access") {
        var data =  { "folder": $(".signer-active-select").attr("data-id"), "csrf-token": Cookies.get("CSRF-TOKEN") };
        putOnModal(folderAccessViewUrl, data, folderAccessUrl, "Folder Accessibility");
    }else if (action === "protect") {
        var data =  { "folder": $(".signer-active-select").attr("data-id"), "csrf-token": Cookies.get("CSRF-TOKEN") };
        putOnModal(folderProtectViewUrl, data, folderProtectUrl, "Folder Protection");
    }
 });

/*
 * File actions
 */
 $(".file-actions").on("click",  "a", function(event){
    event.preventDefault();
    var action = $(this).attr("action");
    if (action === "open") {
        openFile($(".signer-active-select").attr("document_key"));
    }else if (action === "sign") {
        openFile($(".signer-active-select").attr("document_key")+"?action=sign");
    }else if (action === "download") {
        redirect("../document/"+$(".signer-active-select").attr("data-id")+"/download/");
    }else if (action === "share") {
        var sharingLink = appUrl+"/view/"+$(".signer-active-select").attr("document_key");
        $(".sharing-link").val(sharingLink);
        $("#sharefile").modal({show: true, backdrop: 'static', keyboard: false});
    }else if (action === "rename") {
        renameFile($(".signer-active-select").attr("data-id"), $(".signer-active-select").attr("data-original-title"));
    }else if (action === "duplicate") {
        server({ 
            url: duplicateFileUrl,
            data: {
                "file": $(".signer-active-select").attr("data-id"),
                "csrf-token": Cookies.get("CSRF-TOKEN")
            },
            loader: true
        });
    }else if (action === "delete") {
        $(".signer-active-select").addClass("deleted");
        $(".signer-active-select").hide();
        server({ 
            url: deleteFileUrl,
            data: {
                "file": $(".signer-active-select").attr("data-id"),
                "csrf-token": Cookies.get("CSRF-TOKEN")
            },
            loader: false
        });
    }else if (action === "access") {
        var data =  { "file": $(".signer-active-select").attr("data-id"), "csrf-token": Cookies.get("CSRF-TOKEN") };
        putOnModal(fileAccessViewUrl, data, fileAccessUrl, "File Accessibility");
    }else if (action === "protect") {
        var data =  { "folder": $(".signer-active-select").attr("data-id"), "csrf-token": Cookies.get("CSRF-TOKEN") };
        putOnModal(folderProtectViewUrl, data, folderProtectUrl, "Folder Protection");
    }
 });

/*
 * what to do with deleted files
 */
 function deleted(status){
    if (status) {
        $(".deleted").remove();
    }else{
        $(".deleted").show();
    }
 }

/*
 * tools bar actions
 */
 $(".select-option").on("click",  "a", function(event){
    event.preventDefault();
    if ($(this).attr("disabled") === "disabled") {
        return false;
    }
    var action = $(this).attr("action");
    if (action === "open") {
        if ($(".ui-selected").attr("data-type") === "folder") {
            openFolder($(".ui-selected").attr("data-id"), $(".ui-selected").attr("data-original-title"));
        }else if ($(".ui-selected").attr("data-type") === "file") {
            openFile($(".ui-selected").attr("document_key"));
        }
    }else if (action === "rename") {
        if ($(".ui-selected").attr("data-type") === "folder") {
            renameFolder($(".ui-selected").attr("data-id"), $(".ui-selected").attr("data-original-title"));
        }else if ($(".ui-selected").attr("data-type") === "file") {
            renameFile($(".ui-selected").attr("data-id"), $(".ui-selected").attr("data-original-title"));
        }
    }else if (action === "delete") {
        $(".ui-selected.folder").addClass("deleted");
        $(".ui-selected.folder").hide();
        if ($(".ui-selected.folder").length > 1){
            var data = [];
            $(".ui-selected.folder").each(function(){
                data.push({
                        type: $(this).attr("data-type"),
                        itemid: $(this).attr("data-id")
                    });
            });
            deleteDocuments(deleteUrl, { "data": data, "csrf-token": Cookies.get("CSRF-TOKEN") });
        }else{
            if ($(".ui-selected").attr("data-type") === "folder") {
                deleteDocuments(deleteFolderUrl, { "folder": $(".ui-selected").attr("data-id"), "csrf-token": Cookies.get("CSRF-TOKEN") });
            }else if ($(".ui-selected").attr("data-type") === "file") {
                deleteDocuments(deleteFileUrl, { "file": $(".ui-selected").attr("data-id"), "csrf-token": Cookies.get("CSRF-TOKEN") });
            }
        }
    }
 });
 

/*
 * Delete files & folders
 */
function deleteDocuments(url, data){
    server({ 
        url: url,
        data: data,
        loader: false
    });
 };


/*
 * Folder breadcrumbs click
 */
 $(".breadcrumbs").on("click",  ".breadcrumbs-item span", function(event){
    event.preventDefault();
    openFolder($(this).attr("data-id"), false);
 });

/*
 * Home Folder breadcrumbs click
 */
 $(".breadcrumbs").on("click",  ".home-folder", function(event){
    event.preventDefault();
    $(".breadcrumbs").find(".breadcrumbs-item").remove()
    openFolder(1, "Home Folder");
 });

/*
 * On Go Back click
 */
 $(".go-back").click(function(event){
    event.preventDefault();
    var folderId = $(".breadcrumbs").find(".breadcrumbs-item:nth-last-child(2) span").attr("data-id");
    if (folderId !== undefined) {
        openFolder(folderId, false);
    }else{
        $(".home-folder").click();
    } 
 });

/*
 * Fetch HTML and put on modals
 */
 function putOnModal(url, data, formUrl, title){
    showLoader();
    var posting = $.post(url, data);
    posting.done(function (data) {
        hideLoader();
        $("#shared .modal-title").text(title);
        $("#shared form").attr("action", formUrl);
        $(".shared-holder").html(data);
        $("#shared").modal({show: true, backdrop: 'static', keyboard: false});
    });
 }

/*
 * Hide shared modal
 */
 function hideSharedModal(){
    $("#shared").modal("hide");
 }

/*
 * Folder accessibility options
 */
 $("#shared").on("change", "select[name=accessibility]", function(){
    if ($(this).val() === "Departments") {
        $("#shared").find(".departments-holder").show();
        $("#shared").find("select").last().attr("required", true);
    }else{
        $("#shared").find(".departments-holder").hide();
        $("#shared").find("select").last().attr("required", false);
    }
 });

/*
 * Documents filter
 */
 $(".documents-filter-form input").change(function(){
    loadDocuments();
 });

