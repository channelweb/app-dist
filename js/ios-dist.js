if (typeof Dropzone != "undefined") {
    Dropzone.autoDiscover = false;
}

$(function() {

    $('.delete-btn').click(function(){
        if(!confirm('Please confirm deletion of')) return false;
    });

    if($('img.qr').attr('src')) {
        $('img.qr').css('display', 'block');
    }

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

    if(!isMobile.iOS()) {
        $('#ipa-url').hide();
    }


    if($('body').hasClass('upload')) {

        var myDropzone = new Dropzone("#dropzone", {
            url: 'upload.php',
            acceptedFiles: '.ipa',
            parallelUploads: 1,
            uploadMultiple: false,
            addRemoveLinks: true,
            autoDiscover: false,

            dictDefaultMessage: 'Drag IPA file here',
            complete: function() {

            },

            drop: function() {
                myDropzone.removeAllFiles()
                resetContainers();
            },

            reset: function() {
                resetContainers();
            },

            canceled: function() {
                resetContainers();
            },

            success: function(xxx, response) {
                // disable dropzone
                myDropzone.disable();

                if (typeof response == 'string') {
                    $('.dz-preview').hide();
                    //$('#dropzone label').text(response).show();
                    alert(response);
                    reset();
                    return;
                }
                // inject udids
                response.devices.forEach(function(item) {
                    $('#ipa-devices ul').append($('<li>').text(item));
                });
                $('body').addClass('view');

                // push state
                var state = { 'view': response.id };
                history.pushState(state, 'View ' + response.filename, './view.php?p=' + response.id);

                // update fields
                $('#ipa-url a').attr('href', response.url);
                $('#dropzone label').text(response.filename).show();
                $('#dropzone a.delete-btn').attr('href', 'delete.php?id=' + response.id);
                $('#dropzone input').attr('value', window.location.href);

                // QR code
                var url = encodeURIComponent(window.location.href);
                var QRsrc = 'https://chart.googleapis.com/chart?chs=140x140&cht=qr&chld=H|1&chl=' + url;
                $('.dz-preview').hide();
                $('img.qr').attr('src', QRsrc).css('display', 'block');
            }
        });
    }


    $('header').click(function() {
        if ($(this).hasClass('view')) {
            resetContainers();
            $('body').removeClass('view');
            $('img.qr').attr('src', '').css('display', 'none');
            myDropzone.enable();
        } else {
            window.location.href = './';
        }
    });


    var resetContainers = function() {
        $('#ipa-url a').attr('href', '');
        $('#ipa-devices ul').empty();
    }

});

