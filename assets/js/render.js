/*!
 * Signer
 * Version 1.0 - built Sat, Oct 6th 2018, 01:12 pm
 * https://simcycreative.com
 * Simcy Creative - <hello@simcycreative.com>
 * Private License
 */

var pdfDoc = null,
    pageNum = 1,
    pageRendering = false,
    pageNumPending = null,
    scale = 1.1,
    password = null;
    canvas = document.getElementById('document-viewer'),
    ctx = canvas.getContext('2d');

/**
 * Get page info from document, resize canvas accordingly, and render page.
 * @param num Page number.
 */
function renderPage(num) {
    $(".document-load").show();
    $(".signer-element").hide();
    rotationDegree = getActualRotation(num);
    pageRendering = true;
    // Using promise to fetch the page
    pdfDoc.getPage(num).then(function(page) {
        if (rotationDegree > 0) {
            var viewport = page.getViewport($(".document-map").width() / page.getViewport(scale).width, parseInt(rotationDegree + page.rotate) );
        }else{
            var viewport = page.getViewport($(".document-map").width() / page.getViewport(scale).width);
        }
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        var renderContext = {
            canvasContext: ctx,
            viewport: viewport
        };
        var renderTask = page.render(renderContext);

        // Wait for rendering to finish
        renderTask.promise.then(function() {
            $(".document-load").hide();
            showTemplateFields();
            acceptRequest()
            $("[page="+pageNum+"]").show();
            modules.original = $('canvas').getCanvasImage("image/png");
            if (pageNum == pdfDoc.numPages) {
                $("#next").addClass("disabled");
            } else {
                $("#next").removeClass("disabled");
            }

            if (pageNum == 1) {
                $("#prev").addClass("disabled");
            } else {
                $("#prev").removeClass("disabled");
            }

            pageRendering = false;
            if (pageNumPending !== null) {
                // New page rendering is pending
                renderPage(pageNumPending);
                pageNumPending = null;
            }
        });
    });

    // Update page counters
    $("#page_num").text(num);
    pageNum = num;

}



/**
 * If another page rendering in progress, waits until the rendering is
 * finised. Otherwise, executes rendering immediately.
 */
function queueRenderPage(num) {
  if (pageRendering) {
    pageNumPending = num;
  } else {
    renderPage(num);
  }
}

/**
 * Displays previous page.
 */
function onPrevPage() {
  if (pageNum <= 1) {
    return;
  }
  pageNum--;
  queueRenderPage(pageNum);
}
document.getElementById('prev').addEventListener('click', onPrevPage);

/**
 * Displays next page.
 */
function onNextPage() {
  if (pageNum >= pdfDoc.numPages) {
    return;
  }
  pageNum++;
  queueRenderPage(pageNum);
}
document.getElementById('next').addEventListener('click', onNextPage);


/**
 * Asynchronously downloads PDF.
 */
openDocument(url, password);

function openDocument(url, password) {

    PDFJS.getDocument({
        url: url,
        password: password
    }).then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;
        document.getElementById('page_count').textContent = pdfDoc.numPages;

        // Initial/first page rendering
        renderPage(pageNum);

        if (pdfDoc.numPages == 1) {
            $("#next, #prev").addClass("disabled");
        }

        $("#unlockFile").modal("hide");
        $(".document-password-error").hide();
    }).catch(function(error) {
        if (error.name == 'PasswordException') {
            $("input[name=docpassword]").val('');
            $("#unlockFile").modal({  show: true, backdrop: 'static', keyboard: false  });
            if (error.code == 2) {
                $(".document-password-error").show();
                $(".password-error").text(error.message);
            }
        } else {
            $(".document-error").find(".error-message").text(error.message);
            $(".document-load").hide();
            $(".document-error").show();
        }
    });

}

/*
 * Render the page again when browser is resized
 */
// $(document).resize(function(event, ui) {
//   // renderPage(pageNum);
// });

/*
 * Zoom in and Zoom Out
 */
$(".btn-zoom").click(function(){
    if($(this).attr("zoom") === "plus"){
        scale = scale - 0.1;
    }else{
        if (scale > 0) {
            scale = scale + 0.1;
        }
    }

    if (scale == 1) {
        $("#document-viewer").css("max-width", "100%");
    }else{
        $("#document-viewer").width("auto");
    }

    renderPage(pageNum);
});


/*
 * Tools responsiveness
 */
$('.signer-more-tools, .signer-header-tool-holder').slimscroll({
    height: '70px',
    width: '100%',
    railOpacity: '0',
    size: "1px",
    color: 'rgba(0, 0, 0, 0)',
    axis: 'x'
});


/*
 * unlock a protected file
 */
 $(".unlock-file").submit(function(event){
    event.preventDefault();
    openDocument(url, $("input[name=docpassword]").val());
 });


/*
 * re render page on resize
 */
 // $( window ).resize(function(){
 //    width = $( window ).width();
 //    if (width % 5 === 0) {
 //        renderPage(pageNum);
 //    }
 // });
