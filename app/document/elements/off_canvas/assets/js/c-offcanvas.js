var oldBodyMarginRight = jQuery("body").css("margin-right");
function onShow() {
    var $ = jQuery;
    var body = $("body");
    var html = $("html");
    var oldBodyOuterWidth = body.outerWidth(true);
    var oldScrollTop = html.scrollTop();
    var newBodyOuterWidth;
    // $(body).css("overflow-y", "hidden");
    newBodyOuterWidth = $("body").outerWidth(true);
    body.css("margin-right", (newBodyOuterWidth - oldBodyOuterWidth + parseInt(oldBodyMarginRight)) + "px");
    html.scrollTop(oldScrollTop);
    var SMC_html;
    if (!$('#system-message-container')[0]) {
        SMC_html = $('<div />', {
            id: 'system-message-container'
        })
    } else {
        var blk = $('<div />', {
            id: '_BLK_system-message-container'
        })
        $('#system-message-container').before(blk);
        SMC_html = $('#system-message-container').detach();
    }
    $('#wrMes').append(SMC_html);
}
function onClose() {
    var $ = jQuery;
    var SMC_html;
    var html = $("html");
    var oldScrollTop = html.scrollTop();
    setTimeout(function() {
       //  $("body").css("overflow-y", "auto")/*.scrollTop(oldScrollTop)*/;
        $("body").css("margin-right", oldBodyMarginRight);
        var blk;
        $('#system-message-container').empty();
        var SMC = $('#system-message-container').detach()
        blk = $('#_BLK_system-message-container')
        if (blk[0]) {
            $(blk).replaceWith(SMC);
        } else {
            $(SMC).remove();
        }
    }, 500);
}
var offCanvas = offCanvas || {}
offCanvas.getCouponCallback = function(r) {
    var $ = jQuery;
    var data = JSON.parse(r.response);
    var type
    if (!data.success && data.messages) {
        Joomla.renderMessages(data.messages);
        $('#getCouponCodeForm').remove();
        return false;
    }
    if (!data.success && data.message) {
        var messages = {
            "error": [data.message]
        }
        Joomla.renderMessages(messages);
        return false;
    }
    if (data.messages) {
        Joomla.renderMessages(data.messages);
    }
    return false;
    console.log(JSON.parse(r.response));
}
function openNav() {
    onShow();
    document.getElementById("mySidenavRight").style.width = "350px";
    document.getElementById("main").style.marginRight = "350px";
   /* document.getElementById("webim-buttonContainer").style.display = "none";*/
    document.body.style.overflow = 'hidden';
    document.body.className += ' ' + 'c-offcanvas-open'
}
function closeNav() {
    var $ = jQuery;
    document.body.style.overflow = 'auto';
    document.getElementById("mySidenavRight").style.width = "0";
    document.getElementById("main").style.marginRight = "0";
     
    onClose();
    document.querySelector('body').classList.remove('c-offcanvas-open');
}
document.body.addEventListener("click", function(e) {
    var $ = jQuery;
    if ($(e.target).closest('.elSrv-offCan')[0] || $(e.target).closest('#fancybox-wrap')[0] || $(e.target).closest('#fancybox-overlay')[0]  ) {
        return false;
    }
    closeNav();
});


