/*
 * Main admin script for Easy Custom WP Login (versioned)
 */
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
    $('#ecwpr-preview .ecwpr-preview-form input').css({'border': '1px solid ' + inputBorder, 'background-color': (opts.initial_mode === 'light' ? '#fff' : 'transparent'), 'color': (opts.initial_mode === 'light' ? '#1e293b' : '#fff')});

        // font
        if (opts.font_family) {
            $('#ecwpr-preview').css('font-family', opts.font_family);
        }

        // texts
        $('#ecwpr-preview .ecwpr-preview-title').text(opts.title_text || '');
        // logo preview in admin (respect logo_mode and square size)
        if (opts.logo) {
            var $logo = $('#ecwpr-logo-in-preview');
            if ($logo.length === 0) {
                $('#ecwpr-preview .ecwpr-preview-logo').prepend('<div id="ecwpr-logo-in-preview" style="margin-bottom:10px;text-align:center;"></div>');
                $logo = $('#ecwpr-logo-in-preview');
            }
            var mode = opts.logo_mode || ($('#logo_mode').val() || 'contain');
            var imgStyle = '';
            if (mode === 'square') {
                var sq = parseInt(opts.logo_square_size || $('#logo_square_size').val() || 200, 10) || 200;
                imgStyle = 'width: ' + sq + 'px; height: ' + sq + 'px; object-fit: cover; border-radius:6px; display:inline-block;';
            } else if (mode === 'cover') {
                imgStyle = 'max-width:220px; width:100%; height:auto; object-fit:cover; border-radius:6px; display:inline-block;';
            } else {
                imgStyle = 'max-width:220px; width:auto; height:auto; object-fit:contain; border-radius:6px; display:inline-block;';
            }
            $logo.html('<img src="' + opts.logo + '" alt="logo" style="' + imgStyle + '"/>');
        } else {
            $('#ecwpr-logo-in-preview').remove();
        }

        // light mode background color (when preview is set to light)
        if (opts.initial_mode === 'light' || $('#ecwpr-preview').hasClass('ecwpr-preview-light')) {
            var lb = opts.light_bg || $('#light_bg').val() || '#f3f4f6';
            $('#ecwpr-preview').css('background', lb);
        }
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

        // Preview mode toggle function (no external button, only the pill inside preview)
        function setPreviewMode(mode) {
            var $preview = $('#ecwpr-preview');
            if (mode === 'light') {
                $preview.addClass('ecwpr-preview-light');
                // animate pill
                $('.ecwpr-preview-toggle-visual').addClass('ecwpr-toggle-on');
                $('.ecwpr-preview-toggle-visual').attr('aria-pressed', 'true').text('Dark');
            } else {
                $preview.removeClass('ecwpr-preview-light');
                $('.ecwpr-preview-toggle-visual').removeClass('ecwpr-toggle-on');
                $('.ecwpr-preview-toggle-visual').attr('aria-pressed', 'false').text('Light');
            }
        }

        // initialize mode from settings or default (and ensure preview matches dropdown changes)
        var initMode = (ecwprOptions && ecwprOptions.initial_mode) ? ecwprOptions.initial_mode : 'dark';
        setPreviewMode(initMode);

        // when admin changes the initial_mode dropdown, immediately update the preview mode
        $('#initial_mode').on('change', function(){
            var m = $(this).val() || 'dark';
            ecwprOptions.initial_mode = m;
            setPreviewMode(m);
            updatePreview(ecwprOptions);
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

        // Media uploader for logo
        var mediaUploader;
        $('#ecwpr-upload-logo').on('click', function(e){
            e.preventDefault();
            if (mediaUploader) { mediaUploader.open(); return; }
            mediaUploader = wp.media({ title: 'Select Logo', button: { text: 'Use this logo' }, multiple: false });
            mediaUploader.on('select', function(){
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#logo').val(attachment.url).change();
                $('#ecwpr-logo-preview').html('<img src="' + attachment.url + '" alt="logo" style="max-width:200px;height:auto;border-radius:6px;" />');
                // validate dimensions for square mode
                var squareSize = parseInt($('#logo_square_size').val(),10) || 200;
                var mode = $('#logo_mode').val() || 'contain';
                if (mode === 'square' && attachment.width && attachment.height) {
                    if (attachment.width < squareSize || attachment.height < squareSize) {
                        $('#ecwpr-logo-notice').text('Selected image is smaller than the requested square size (' + squareSize + 'px). Consider uploading a larger image or reducing the square size.').show();
                    } else {
                        $('#ecwpr-logo-notice').hide();
                    }
                }
                // store attachment id for later actions
                $('#logo').data('attachment-id', attachment.id);
                // update preview options
                ecwprOptions.logo = attachment.url;
                ecwprOptions.logo_mode = mode;
                ecwprOptions.logo_square_size = squareSize;
                updatePreview(ecwprOptions);
            });
            mediaUploader.open();
        });

        $('#ecwpr-remove-logo').on('click', function(){
            $('#logo').val('').change();
            $('#ecwpr-logo-preview').empty();
            ecwprOptions.logo = '';
            updatePreview(ecwprOptions);
        });

        // Generate square thumbnail immediately (AJAX)
        $('#ecwpr-generate-square').on('click', function(){
            var attachmentId = $('#logo').data('attachment-id') || 0;
            var size = parseInt($('#logo_square_size').val(),10) || 200;
            if (!attachmentId) {
                $('#ecwpr-logo-notice').text('No media attachment available. Please upload/select an image using the Upload button.').show();
                return;
            }
            $('#ecwpr-logo-notice').text('Generating thumbnail...').show();
            $.post(ecwprAdmin.ajax_url, { action: 'ecwpr_generate_square_logo', nonce: ecwprAdmin.nonce, attachment_id: attachmentId, size: size }, function(resp){
                if (resp && resp.success && resp.data && resp.data.url) {
                    $('#logo').val(resp.data.url).change();
                    $('#ecwpr-logo-preview').html('<img src="' + resp.data.url + '" alt="logo" style="max-width:200px;height:auto;border-radius:6px;" />');
                    ecwprOptions.logo = resp.data.url;
                    updatePreview(ecwprOptions);
                    $('#ecwpr-logo-notice').text('Square thumbnail generated and set.').show().fadeOut(4000);
                } else {
                    $('#ecwpr-logo-notice').text('Could not generate square thumbnail.').show();
                }
            }, 'json').fail(function(){
                $('#ecwpr-logo-notice').text('Request failed while generating thumbnail.').show();
            });
        });

        // Persist collapsible sections state
        $('.ecwpr-section-toggle').each(function(){
            var id = $(this).closest('.ecwpr-section').data('section');
            var key = 'ecwpr_section_' + id;
            var stored = localStorage.getItem(key);
            if (stored === 'open') {
                $(this).attr('aria-expanded','true');
                $(this).closest('.ecwpr-section').find('.ecwpr-section-body').attr('hidden', false).show();
            }
        });
        $('.ecwpr-section-toggle').on('click', function(){
            var $btn = $(this);
            var id = $btn.closest('.ecwpr-section').data('section');
            var key = 'ecwpr_section_' + id;
            var expanded = $btn.attr('aria-expanded') === 'true';
            if (expanded) {
                localStorage.setItem(key, 'closed');
            } else {
                localStorage.setItem(key, 'open');
            }
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
        $('#enable_animation, #animation_speed, #title_text, #logo_mode, #logo_square_size, #light_bg, #button_color, #input_border, #font_family, #message_bg_color, #error_bg_color').on('change keyup', function(){
            var id = $(this).attr('id');
            var val = $(this).is(':checkbox') ? ($(this).is(':checked') ? 1 : 0) : $(this).val();
            // map control ids to option keys used by updatePreview
            if (id === 'logo_square_size') id = 'logo_square_size';
            if (id === 'light_bg') id = 'light_bg';
            if (id === 'button_color') id = 'button_color';
            if (id === 'input_border') id = 'input_border';
            if (id === 'font_family') id = 'font_family';
            ecwprOptions[id] = val;
            // Special case: when logo_mode changes, ensure preview uses the selected mode
            if (id === 'logo_mode') {
                ecwprOptions.logo_mode = val;
            }
            updatePreview(ecwprOptions);
        });

        // disable styling checkbox
        $('#disable_styling').on('change', function(){
            var v = $(this).is(':checked') ? 1 : 0;
            ecwprOptions.disable_styling = v;
            updatePreview(ecwprOptions);
        });

        // export to theme and aggressive override options: include in ecwprOptions and show/hide note
        $('#export_css_to_theme, #override_all_styles').on('change', function(){
            ecwprOptions.export_css_to_theme = $('#export_css_to_theme').is(':checked') ? 1 : 0;
            ecwprOptions.override_all_styles = $('#override_all_styles').is(':checked') ? 1 : 0;
        });

        // show the disable styling note when checkbox is checked
        function syncDisableNote(){
            if ($('#disable_styling').is(':checked')) {
                $('#ecwpr-disable-note').show();
            } else {
                $('#ecwpr-disable-note').hide();
            }
        }
        syncDisableNote();
        $('#disable_styling').on('change', syncDisableNote);

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
            settings.title_text = $('#title_text').val();
            settings.button_color = $('#button_color').val();
            settings.input_border = $('#input_border').val();
            settings.font_family = $('#font_family').val();
            settings.message_bg_color = $('#message_bg_color').val();
            settings.error_bg_color = $('#error_bg_color').val();
            settings.initial_mode = $('#initial_mode').val();
            settings.logo = $('#logo').val();
            settings.logo_mode = $('#logo_mode').val();
            settings.logo_square_size = $('#logo_square_size').val();
            settings.disable_styling = $('#disable_styling').is(':checked') ? 1 : 0;
            settings.hide_scrollbar = $('#hide_scrollbar').is(':checked') ? 1 : 0;
            settings.export_css_to_theme = $('#export_css_to_theme').is(':checked') ? 1 : 0;
            settings.override_all_styles = $('#override_all_styles').is(':checked') ? 1 : 0;

            if (typeof ecwprAdmin === 'undefined') {
                $form.off('submit');
                $form.submit();
                return;
            }

            var doSave = function() {
                $.post(ecwprAdmin.ajax_url, { action: 'ecwpr_save_settings', nonce: ecwprAdmin.nonce, settings: settings }, function(resp){
                    if (resp && resp.success) {
                        var $n = $('<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>');
                        if (resp.data && resp.data.export) {
                            var ex = resp.data.export;
                            if (ex.exported) {
                                var msgHtml = 'Login CSS exported to theme: <code>' + ex.path + '</code>';
                                if (ex.backup_path) {
                                    msgHtml = 'Existing theme file backed up to: <code>' + ex.backup_path + '</code><br/>' + msgHtml;
                                }
                                var $e = $('<div class="notice notice-success is-dismissible"><p>' + msgHtml + '</p></div>');
                                $('.wrap h1').after($e);
                                setTimeout(function(){ $e.fadeOut(300, function(){ $e.remove(); }); }, 6000);
                                if (ex.override_written) {
                                    var $o = $('<div class="notice notice-success is-dismissible"><p>Override CSS written: <code>' + ex.override_path + '</code></p></div>');
                                    $('.wrap h1').after($o);
                                    setTimeout(function(){ $o.fadeOut(300, function(){ $o.remove(); }); }, 6000);
                                }
                            } else if (settings.export_css_to_theme) {
                                var err = ex.error || 'unknown';
                                var msg = 'Could not export CSS to theme. Error: ' + err + '. Check theme folder permissions and that the theme is writeable.';
                                var $e = $('<div class="notice notice-error is-dismissible"><p>' + msg + '</p></div>');
                                $('.wrap h1').after($e);
                                setTimeout(function(){ $e.fadeOut(6000, function(){ $e.remove(); }); }, 8000);
                            }
                        }
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
            };

            if (settings.export_css_to_theme) {
                // call preflight
                $.post(ecwprAdmin.ajax_url, { action: 'ecwpr_check_export_target', nonce: ecwprAdmin.nonce }, function(pref){
                    if (pref && pref.success && pref.data) {
                        var data = pref.data;
                        if (data.exists) {
                            // show modal confirmation
                            $('#ecwpr-export-confirm-message').text('A file already exists at: ' + data.dst + '. Exporting will back it up to a .bak- timestamp and overwrite. Proceed?');
                            $('#ecwpr-export-confirm-modal').show();
                            $('#ecwpr-export-confirm').off('click').on('click', function(){
                                $('#ecwpr-export-confirm-modal').hide();
                                doSave();
                            });
                            $('#ecwpr-export-cancel').off('click').on('click', function(){ $('#ecwpr-export-confirm-modal').hide(); });
                        } else if (!data.writable) {
                            // not writable: warn and still allow explicit proceed
                            $('#ecwpr-export-confirm-message').text('Theme folder does not appear writable. Export will likely fail. Proceed anyway? Destination: ' + data.dst);
                            $('#ecwpr-export-confirm-modal').show();
                            $('#ecwpr-export-confirm').off('click').on('click', function(){
                                $('#ecwpr-export-confirm-modal').hide();
                                doSave();
                            });
                            $('#ecwpr-export-cancel').off('click').on('click', function(){ $('#ecwpr-export-confirm-modal').hide(); });
                        } else {
                            // ok to save directly
                            doSave();
                        }
                    } else {
                        // if preflight failed, still attempt save but warn
                        doSave();
                    }
                }, 'json').fail(function(){
                    doSave();
                });
            } else {
                doSave();
            }
        });
    });
})(jQuery);
