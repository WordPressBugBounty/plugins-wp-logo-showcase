(function ($) {
    "use strict";

    var $wlsPreviewFrame = $("#wls-sc-preview-frame"),
        $wlsPreviewViewport = $(".wls-preview-viewport"),
        $wlsScSettings = $("#wlshowcasesc_sc_settings_meta"),
        // Real viewport widths, so the theme and plugin media queries pick the same columns as on that device.
        wlsDeviceWidths = {desktop: 1200, tablet: 768, mobile: 375},
        wlsDevice = "desktop",
        wlsPreviewHeight = 0,
        wlsPreviewTimer = null;

    $(function () {
        $(".rt-tab-nav li:first-child a").trigger('click');
        wlsFitPreview();
        wlsRenderPreview();
    });
    if ($(".rt-color").length) {
        // Iris writes the input value after these callbacks run; the debounce covers that.
        $(".rt-color").wpColorPicker({
            change: wlsSchedulePreview,
            clear: wlsSchedulePreview
        });
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

    $("input[name='_wls_items[]']").on('change', function () {
        wlsDescriptionLimitOption();
    });

    wlsImageResizeOption();
    wlsCarouselOption();
    wlsDescriptionLimitOption();

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

    function wlsDescriptionLimitOption() {
        if ($("input[name='_wls_items[]'][value='description']").is(":checked")) {
            $("#wls_description_limit_holder").show();
        } else {
            $("#wls_description_limit_holder").hide();
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

    /* ShortCode preview — the front end renders it inside the iframe, so it matches the live site. */
    $wlsScSettings.on("change input", "input,select,textarea", wlsSchedulePreview);
    $(window).on("resize", wlsFitPreview);
    $wlsPreviewFrame.on("load", function () {
        $(".wls-preview-toolbar .spinner").removeClass("is-active");
        $wlsPreviewViewport.removeClass("is-loading");
    });

    $(".wls-preview-device").on("click", function () {
        var $button = $(this);
        wlsDevice = $button.data("device");
        $(".wls-preview-device").removeClass("is-active").attr("aria-pressed", "false");
        $button.addClass("is-active").attr("aria-pressed", "true");
        // Same document, new width: the page reflows and reports its new height.
        wlsFitPreview();
    });

    window.addEventListener("message", function (e) {
        var height = e.data && e.data.wlsPreviewHeight;
        // Only the preview page itself may resize the frame.
        if (!$wlsPreviewFrame.length || e.source !== $wlsPreviewFrame[0].contentWindow || e.origin !== wlsPreviewOrigin()) {
            return;
        }
        if (typeof height !== "number" || !isFinite(height) || height < 0) {
            return;
        }
        wlsPreviewHeight = Math.min(Math.ceil(height), 20000);
        wlsFitPreview();
    });

    function wlsPreviewOrigin() {
        var a = document.createElement("a");
        a.href = wls.previewUrl;
        return a.protocol + "//" + a.host;
    }

    // Debounced so typing in number fields reloads the preview once, not per keystroke.
    function wlsSchedulePreview() {
        if (!$wlsPreviewFrame.length) {
            return;
        }
        clearTimeout(wlsPreviewTimer);
        wlsPreviewTimer = setTimeout(wlsRenderPreview, 400);
    }

    function wlsRenderPreview() {
        if (!$wlsPreviewFrame.length) {
            return;
        }

        // The settings box carries the save nonce; the preview sends its own.
        var fields = $wlsScSettings.find("input[name],select[name],textarea[name]").serializeArray().filter(function (field) {
                return field.name !== wls.nonceID && field.name !== "_wp_http_referer";
            }),
            $form = $("<form>", {method: "post", action: wls.previewUrl, target: $wlsPreviewFrame.attr("name")}).hide();

        fields.push(
            {name: "post_ID", value: $("#post_ID").val()},
            {name: wls.nonceID, value: wls.previewNonce}
        );

        $.each(fields, function (i, field) {
            $("<input>", {type: "hidden", name: field.name, value: field.value}).appendTo($form);
        });

        $(".wls-preview-toolbar .spinner").addClass("is-active");
        $wlsPreviewViewport.addClass("is-loading");

        // POST keeps the nonce out of URLs and logs. Native submit() skips the edit screen's submit handlers.
        $form.appendTo("body");
        $form[0].submit();
        $form.remove();
    }

    // Lay the page out at the device width, then scale it down to fit the box.
    function wlsFitPreview() {
        if (!$wlsPreviewFrame.length) {
            return;
        }
        var width = wlsDeviceWidths[wlsDevice],
            available = $wlsPreviewViewport.width(),
            scale = Math.min(1, available / width),
            height = Math.max(wlsPreviewHeight, 150);

        $wlsPreviewFrame.css({
            width: width + "px",
            height: height + "px",
            left: Math.max(0, (available - width * scale) / 2) + "px",
            transform: "scale(" + scale + ")"
        });
        $wlsPreviewViewport.css("height", Math.ceil(height * scale) + "px");
    }

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
