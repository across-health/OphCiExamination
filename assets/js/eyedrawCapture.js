$(document).ready(function() {

    function captureImage(element, callback) {
        var image = new Image();
        image.src = element[0].toDataURL("image/png");

        var body = $(element).parents('.ed-body').find('.ed-editor-container');
        var toolbar = body.find('.ed-toolbar');
        var height = toolbar.css('height');
        var width = toolbar.css('width');

        var qr = body.append('<div class="qr-header" style="height:'+height+'; width: '+width+';">Please wait ...</div>').find('.qr-header');
        toolbar.hide();
        body.find('.ed-editor').hide();

        $.ajax({
            cache: false,
            type: 'POST',
            data: {
                image: image.src,
                YII_CSRF_TOKEN: YII_CSRF_TOKEN
            },
            dataType: 'json',
            url: baseUrl + '/OphCiExamination/default/storeCanvasForEditing',
            success: function (data) {

                qrData = {
                    download: baseUrl + '/OphCiExamination/default/downloadCanvasForEditing/' + data['uuid'],
                    upload: baseUrl + '/OphCiExamination/default/uploadEditedCanvas' + data['uuid']
                };

                qr.html("Please scan the QR Code below with your app.");
                qr.qrcode({
                    size: 300,
                    color: "#3a3",
                    text: JSON.stringify(qrData)
                });

            },
            error: function (req, status, err) {
                var alert = new OpenEyes.UI.Dialog.Alert({
                    title: 'Service Unavailable',
                    content: "eyePad Draw is not available at the moment."});
                alert.on('close', function() {
                    toolbar.show();
                    body.find('.ed-editor').show();
                    qr.remove();
                    callback();
                });
                alert.open();

            }
        });
    }

    $('.event').on('click', '.eyedraw-capture', function(e) {
        e.preventDefault();
        var source = $(this);
        var target_id = $(this).data('target-canvas');
        source.prop('disabled', true);
        captureImage($('#' + target_id), function() {
            source.prop('disabled', false);
        });
    });
});