(function ($) {
    "use strict";

    $(function () {
        $(".rt-tab-nav li:first-child a").trigger('click');
    });
    if ($(".rt-color").length) {
        $(".rt-color").wpColorPicker();
    }
    if ($(".rt-select2").length) {
        $(".rt-select2").select2({
            theme: "classic",
            minimumResultsForSearch: Infinity
        });
    }

    $("#wls_image_resize").on('change', function () {
        wlsImageResizeOption();
    });
    $("#wls_layout").on('change', function () {
        wlsCarouselOption();
    });

    $("#wls_image_size").on('change', function () {
        wlsImageSize();
    });

    wlsImageResizeOption();
    wlsCarouselOption();

    wlsImageSize();


    $(".rt-tab-nav li").on('click', 'a', function (e) {
        e.preventDefault();
        var container = $(this).parents('.rt-tab-container'),
            nav = container.children('.rt-tab-nav'),
            content = container.children(".rt-tab-content"),
            $this = $(this),
            $id = $this.attr('href');
        content.hide();
        nav.find('li').removeClass('active');
        $this.parent().addClass('active');
        container.find($id).show();
    });

    function wlsImageResizeOption() {
        if ($("#wls_image_resize").is(":checked")) {
            $("#wls_image_width_holder, #wls_image_height_holder").show();
        } else {
            $("#wls_image_width_holder, #wls_image_height_holder").hide();
        }
    }

    function wlsCarouselOption() {
        var id = $("#wls_layout").val();
        if (id == 'carousel-layout') {
            $(".wls_column_holder").hide();
            $(".wls_carousel_options_holder").show();
        } else {
            $(".wls_column_holder").show();
            $(".wls_carousel_options_holder").hide();
        }
    }

    function wlsAjaxCall(element, action, arg, handle) {
        "use strict";
        var data;
        if (action) data = "action=" + action;
        if (arg) data = arg + "&action=" + action;
        if (arg && !action) data = arg;

        var n = data.search(wls.nonceID);
        if (n < 0) {
            data = data + "&" + wls.nonceID + "=" + wls.nonce;
        }
        $.ajax({
            type: "post",
            url: wls.ajaxurl,
            data: data,
            beforeSend: function () {
                $("<span class='rt-loading'></span>").insertAfter(element);
            },
            success: function (data) {
                $(".rt-loading").remove();
                handle(data);
            }
        });
    }

    /* ShortCode preview */
    function wlsImageSize() {
        /* custom image size jquery */
        var fImageSize = $("#wls_image_size").val();
        // console.log( fImageSize );
        if ( fImageSize === "wls_custom_image_size" ) {
            $(".wls_image_size_holder").show();
        } else {
            $(".wls_image_size_holder").hide();
        }
    }

    $("#rt-wls-settings-form").on('submit', function (e) {
        e.preventDefault();
        wlsSyncCss();
        var form = $(this),
            response_wrap = $('.rt-response'),
            arg = form.serialize(),
            bindElement = form.find('#rtSaveButton');
        response_wrap.hide();
        wlsAjaxCall(bindElement, 'rtWLSSettings', arg, function (data) {
            if (data.error) {
                response_wrap.addClass('success').removeClass('error');
                response_wrap.show('slow').text(data.msg);
            } else {
                response_wrap.addClass('error').removeClass('success');
                response_wrap.show('slow').text(data.msg);
            }
        });
    });
})(jQuery);

(function (global, $) {
    "use strict";
    var editor,
        syncCSS = function () {
            wlsSyncCss();
        },
        loadAce = function () {
            $('.rt-custom-css').each(function () {
                var id = $(this).find('.custom-css').attr('id');
                editor = ace.edit(id);
                global.safecss_editor = editor;
                editor.getSession().setUseWrapMode(true);
                editor.setShowPrintMargin(false);
                editor.getSession().setValue($(this).find('.custom_css_textarea').val());
                editor.getSession().setMode("ace/mode/css");
            });

            jQuery.fn.spin && $('.custom_css_container').spin(false);
            $('#post').submit(syncCSS);
        };
    if ($.browser.msie && parseInt($.browser.version, 10) <= 7) {
        $('.custom_css_container').hide();
        $('.custom_css_textarea').show();
        return false;
    } else {
        $(global).load(loadAce);
    }
    global.aceSyncCSS = syncCSS;
})(this, jQuery);

function wlsSyncCss() {
    "use strict";
    jQuery('.rt-custom-css').each(function () {
        var e = ace.edit(jQuery(this).find('.custom-css').attr('id'));
        jQuery(this).find('.custom_css_textarea').val(e.getSession().getValue());
    });
}
