(function($){
    function updatePreview(opts) {
        var gradient = 'linear-gradient(-45deg, ' + (opts.gradient1 || '#0b1120') + ', ' + (opts.gradient2 || '#1e3a8a') + ', ' + (opts.gradient3 || '#1e40af') + ', ' + (opts.gradient4 || '#1e3a8a') + ')';
        $('#ecwpr-preview').css({'background-image': gradient, 'background-size': '400% 400%'});

        // animation
        if (opts.enable_animation && parseInt(opts.enable_animation, 10) === 1) {
            var speed = (opts.animation_speed && parseInt(opts.animation_speed,10) > 0) ? parseInt(opts.animation_speed,10) : 20;
            $('#ecwpr-preview').css('animation', 'ecwprPreviewShift ' + speed + 's ease infinite');
            // append keyframes once
            if ($('#ecwpr-preview-keyframes').length === 0) {
                $('head').append('<style id="ecwpr-preview-keyframes">@keyframes ecwprPreviewShift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}</style>');
            }
        } else {
            $('#ecwpr-preview').css('animation', 'none');
        }

        // button color
        var btnColor = opts.button_color || '#3b82f6';
        $('#ecwpr-preview .ecwpr-preview-form button').css('background-color', btnColor);

        // input border
        var inputBorder = opts.input_border || 'rgba(255,255,255,0.08)';
        $('#ecwpr-preview .ecwpr-preview-form input').css({'border': '1px solid ' + inputBorder, 'background-color': 'transparent', 'color': (opts.initial_mode === 'light' ? '#1e293b' : '#fff')});

        // font
        if (opts.font_family) {
            $('#ecwpr-preview').css('font-family', opts.font_family);
        }

        // texts
        $('#ecwpr-preview .ecwpr-preview-footer').text(opts.branding_text || '');
        $('#ecwpr-preview .ecwpr-preview-title').text(opts.title_text || '');
    }

    $(document).ready(function(){
        if ($.fn.wpColorPicker) {
            $('.ecwpr-color').wpColorPicker({
                change: function(event, ui) {
                    var el = $(this).attr('id');
                    var val = $(this).val();
                    ecwprOptions[el] = val;
                    updatePreview(ecwprOptions);
                },
                clear: function() {
                    // On clear, fall back to empty
                }
            });
        }

        // initialize preview
        if (typeof ecwprOptions !== 'undefined') {
            updatePreview(ecwprOptions);
        }

        // Preview mode toggle (light/dark) inside admin preview
        var $modeToggle = $('#ecwpr-preview-mode-toggle');
        function setPreviewMode(mode) {
            var $preview = $('#ecwpr-preview');
            if (mode === 'light') {
                $preview.addClass('ecwpr-preview-light');
                $modeToggle.text('Dark Mode');
                $modeToggle.attr('aria-pressed', 'true');
            } else {
                $preview.removeClass('ecwpr-preview-light');
                $modeToggle.text('Toggle Light');
                $modeToggle.attr('aria-pressed', 'false');
            }
        }

        // initialize mode from settings or default
        var initMode = (ecwprOptions && ecwprOptions.initial_mode) ? ecwprOptions.initial_mode : 'dark';
        setPreviewMode(initMode);

        $modeToggle.on('click', function(){
            var current = $('#ecwpr-preview').hasClass('ecwpr-preview-light') ? 'light' : 'dark';
            var next = (current === 'light') ? 'dark' : 'light';
            setPreviewMode(next);
        });

        // Preview visual toggle (the pill inside the preview)
        $(document).on('click', '.ecwpr-preview-toggle-visual', function(){
            var $p = $('#ecwpr-preview');
            var isLight = $p.hasClass('ecwpr-preview-light');
            setPreviewMode(isLight ? 'dark' : 'light');
            // reflect on the pill
            $('.ecwpr-preview-toggle-visual').attr('aria-pressed', String(!isLight));
            $('.ecwpr-preview-toggle-visual').text(isLight ? 'Light' : 'Dark');
        });

        // Collapsible sections handling
        $('.ecwpr-section-toggle').on('click', function(){
            var $btn = $(this);
            var expanded = $btn.attr('aria-expanded') === 'true';
            var $section = $btn.closest('.ecwpr-section');
            var $body = $section.find('.ecwpr-section-body');
            if (expanded) {
                $btn.attr('aria-expanded','false');
                $body.slideUp(180, function(){ $body.attr('hidden', true); });
            } else {
                $btn.attr('aria-expanded','true');
                $body.attr('hidden', false).slideDown(180);
            }
        });

        // wire simple inputs for preview
        $('#enable_animation, #animation_speed, #branding_text, #title_text').on('change keyup', function(){
            var id = $(this).attr('id');
            var val = $(this).is(':checkbox') ? ($(this).is(':checked') ? 1 : 0) : $(this).val();
            ecwprOptions[id] = val;
            updatePreview(ecwprOptions);
        });

        // Intercept the settings form submit to save via AJAX
        $('form').on('submit', function(e){
            var $form = $(this);
            // Only intercept our settings form (has settings_fields input)
            if ($form.find('input[name="option_page"]').val() !== 'ecwpr_settings_group') return true;
            e.preventDefault();

            var settings = {};
            // collect our fields
            settings.gradient1 = $('#gradient1').val();
            settings.gradient2 = $('#gradient2').val();
            settings.gradient3 = $('#gradient3').val();
            settings.gradient4 = $('#gradient4').val();
            settings.light_bg = $('#light_bg').val();
            settings.enable_animation = $('#enable_animation').is(':checked') ? 1 : 0;
            settings.animation_speed = $('#animation_speed').val();
            settings.branding_text = $('#branding_text').val();
            settings.title_text = $('#title_text').val();

            if (typeof ecwprAdmin === 'undefined') {
                $form.off('submit');
                $form.submit();
                return;
            }

            $.post(ecwprAdmin.ajax_url, { action: 'ecwpr_save_settings', nonce: ecwprAdmin.nonce, settings: settings }, function(resp){
                if (resp && resp.success) {
                    // show a notice
                    var $n = $('<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>');
                    $('.wrap h1').after($n);
                    setTimeout(function(){ $n.fadeOut(300, function(){ $n.remove(); }); }, 2500);
                } else {
                    var $n = $('<div class="notice notice-error is-dismissible"><p>Could not save settings.</p></div>');
                    $('.wrap h1').after($n);
                    setTimeout(function(){ $n.fadeOut(300, function(){ $n.remove(); }); }, 2500);
                }
            }, 'json').fail(function(){
                var $n = $('<div class="notice notice-error is-dismissible"><p>Could not save settings (request failed).</p></div>');
                $('.wrap h1').after($n);
                setTimeout(function(){ $n.fadeOut(300, function(){ $n.remove(); }); }, 2500);
            });
        });
    });
})(jQuery);
