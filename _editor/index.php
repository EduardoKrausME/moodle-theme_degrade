<?php

require_once('../../../config.php');

$chave = required_param('chave', PARAM_TEXT);
$editlang = required_param('editlang', PARAM_TEXT);
require_login();
require_capability('moodle/site:config', context_system::instance());

$currentlang = $CFG->lang;
if (isset($_SESSION['SESSION']->lang)) {
    $currentlang = $_SESSION['SESSION']->lang;
}
$langP = explode("_", $currentlang);
foreach ($langP as $i) {
    $_lang = implode("_", $langP);
    if (file_exists(__DIR__ . "/js/locale/{$_lang}.json")) {
        $currentlang = $_lang;
        break;
    }
    array_pop($langP);
}
if (!file_exists(__DIR__ . "/js/locale/{$currentlang}.json")) {
    $currentlang = "en";
}
$langs = json_decode(file_get_contents(__DIR__ . "/js/locale/{$currentlang}.json"));

?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Edit Page</title>
        <link rel="stylesheet" href="styles/toastr.css">
        <link rel="stylesheet" href="styles/grapes.css">
        <link rel="stylesheet" href="styles/grapesjs-preset-webpage.css">
        <link rel="stylesheet" href="styles/style.css">
        <link rel="stylesheet" href="styles/tooltip.css">
        <link rel="stylesheet" href="styles/grapick.css">

        <script src="js/jquery.js"></script>
        <script src="js/toastr.js"></script>
        <script src="js/grapes.js"></script>

        <script src="js/plugins/grapesjs-preset-webpage.js"></script>
        <script src="js/plugins/grapesjs-blocks-basic.js"></script>
        <script src="js/plugins/grapesjs-plugin-forms.js"></script>
        <script src="js/plugins/grapesjs-component-countdown.js"></script>
        <script src="js/plugins/grapesjs-tabs.js"></script>
        <script src="js/plugins/grapesjs-custom-code.js"></script>
        <script src="js/plugins/grapesjs-touch.js"></script>
        <script src="js/plugins/grapesjs-parser-postcss.js"></script>
        <script src="js/plugins/grapesjs-tooltip.js"></script>
        <script src="js/plugins/grapesjs-tui-image-editor.js"></script>
        <script src="js/plugins/grapesjs-typed.js"></script>
        <script src="js/plugins/grapesjs-style-bg.js"></script>
        <script src="js/plugins/grapesjs-plugin-ckeditor.js"></script>
    </head>
    <body>

    <div id="gjs" style="height:0; overflow:hidden">
        <?php
        $action = optional_param('action', false, PARAM_TEXT);
        if ($action == 'save' && confirm_sesskey()) {
            $htmldata = optional_param('htmldata', false, PARAM_RAW);
            $cssdata = optional_param('cssdata', false, PARAM_RAW);
            if ($cssdata) {
                $html = "{$htmldata}\n<style>{$cssdata}</style>";
            } else {
                $html = "{$htmldata}";
            }
            set_config("{$chave}_htmleditor_{$editlang}", $html, "theme_degrade");

            $home_type = get_config("theme_degrade", "home_type");

            redirect("{$CFG->wwwroot}/admin/settings.php?section=themesettingdegrade#theme_degrade_{$chave}");
        }

        if (file_exists(__DIR__ . "/default-{$chave}.html")) {
            $htmldata = get_config("theme_degrade", "{$chave}_htmleditor_{$editlang}");
            if (isset($htmldata[40])) {
                echo $htmldata;
            } else {
                $htmldata = file_get_contents(__DIR__ . "/default-{$chave}.html");
                $htmldata = str_replace("{wwwroot}", $CFG->wwwroot, $htmldata);
                $htmldata = str_replace("{shortname}", $SITE->shortname, $htmldata);
                $htmldata = str_replace("{fullname}", $SITE->fullname, $htmldata);

                $htmldata = str_replace("{footer_links_title_default}", get_string('footer_links_title_default', 'theme_degrade'), $htmldata);
                $htmldata = str_replace("{footer_social_title_default}", get_string('footer_social_title_default', 'theme_degrade'), $htmldata);
                $htmldata = str_replace("{footer_contact_title_default}", get_string('footer_contact_title_default', 'theme_degrade'), $htmldata);
                $htmldata = str_replace("{contact_address}", get_string('contact_address', 'theme_degrade'), $htmldata);

                echo $htmldata;
            }

            if (file_exists(__DIR__ . "/default-{$chave}.css")) {
                $css = file_get_contents(__DIR__ . "/default-{$chave}.css");
                echo "<style>{$css}</style>";
            }
        }
        ?>
    </div>

    <script type="text/javascript">
        var editor = grapesjs.init({
            'height'          : '100%',
            'container'       : '#gjs',
            'fromElement'     : true,
            'showOffsets'     : true,
            'storageManager'  : false,
            'assetManager'    : {
                'upload'     : '<?php echo $CFG->wwwroot ?>/theme/degrade/_editor/files.php?chave=<?php echo $chave ?>',
                'uploadName' : 'files',
                'assets'     : [<?php
                    $contextid = context_system::instance()->id;
                    $component = 'theme_degrade';
                    $filearea = "editor_{$chave}";
                    $fs = get_file_storage();
                    $files = $fs->get_area_files($contextid, $component, $filearea, false, $sort = "filename", false);

                    /** @var stored_file $file */
                    foreach ($files as $file) {
                        $url = moodle_url::make_file_url(
                            "$CFG->wwwroot/pluginfile.php",
                            "/{$contextid}/theme_degrade/{$file->get_filearea()}/{$file->get_itemid()}{$file->get_filepath()}{$file->get_filename()}");
                        echo "\n                '{$url->out(false)}',";
                    }
                    if ($chave == 'home') {
                        echo "
                    '<?php echo $CFG->wwwroot ?>/theme/degrade/_editor/img/demo/78c5d6.png',
                    '<?php echo $CFG->wwwroot ?>/theme/degrade/_editor/img/demo/459ba8.png',
                    '<?php echo $CFG->wwwroot ?>/theme/degrade/_editor/img/demo/79c267.png',
                    '<?php echo $CFG->wwwroot ?>/theme/degrade/_editor/img/demo/c5d647.png',
                    '<?php echo $CFG->wwwroot ?>/theme/degrade/_editor/img/demo/f28c33.png',
                    '<?php echo $CFG->wwwroot ?>/theme/degrade/_editor/img/demo/e868a2.png',";
                    }
                    ?>
                ],
            },
            'selectorManager' : {componentFirst : true},
            'styleManager'    : {
                'sectors' : [
                    {
                        'name'       : '<?php echo $langs->styleManager->sectors->general ?>',
                        'properties' : [
                            'display',
                            {extend : 'position', 'type' : 'select'},
                            'top',
                            'right',
                            'left',
                            'bottom',
                        ],
                    },
                    {
                        'name'       : '<?php echo $langs->styleManager->sectors->dimension ?>',
                        'open'       : false,
                        'properties' : [
                            'width',
                            'height',
                            'max-width',
                            'min-width',
                            'max-height',
                            'min-height',
                            'margin',
                            'padding'
                        ],
                    },
                    {
                        'name'       : '<?php echo $langs->styleManager->sectors->typography ?>',
                        'open'       : false,
                        'properties' : [
                            {
                                'property' : 'font-family',
                                'type'     : 'select',
                                'name'     : '<?php $a = 'font-family'; echo $langs->styleManager->properties->$a ?>',
                                'options'  : [
                                    {
                                        'id'    : "Arial,Helvetica,sans-serif",
                                        'label' : 'Arial',
                                    },
                                    {
                                        'id'    : "'Courier New',Courier,monospace",
                                        'label' : 'Courier New',
                                    },
                                    {
                                        'id'    : "Verdana,Geneva,sans-serif",
                                        'label' : 'Verdana',
                                    },
                                    <?php echo \theme_degrade\fonts\font_util::grapsjs() ?>
                                ]
                            },
                            'font-size',
                            'font-weight',
                            'letter-spacing',
                            'color',
                            'line-height',
                            {
                                'extend'  : 'text-align',
                                'options' : [
                                    {
                                        'id'        : 'left',
                                        'label'     : '<?php echo $langs->styleManager->properties->left ?>',
                                        'className' : 'fa fa-align-left'
                                    },
                                    {
                                        'id'        : 'center',
                                        'label'     : '<?php echo $langs->styleManager->properties->center ?>',
                                        'className' : 'fa fa-align-center'
                                    },
                                    {
                                        'id'        : 'right',
                                        'label'     : '<?php echo $langs->styleManager->properties->right ?>',
                                        'className' : 'fa fa-align-right'
                                    },
                                    {
                                        'id'        : 'justify',
                                        'label'     : '<?php echo $langs->styleManager->properties->justify ?>',
                                        'className' : 'fa fa-align-justify'
                                    }
                                ],
                            },
                            {
                                'property' : 'text-decoration',
                                'type'     : 'radio',
                                'default'  : 'none',
                                'options'  : [
                                    {
                                        'id'        : 'none',
                                        'label'     : '<?php echo $langs->styleManager->properties->none ?>',
                                        'className' : 'fa fa-times'
                                    },
                                    {
                                        'id'        : 'underline',
                                        'label'     : '<?php echo $langs->styleManager->properties->underline ?>',
                                        'className' : 'fa fa-underline'
                                    },
                                    {
                                        'id'        : 'line-through',
                                        'label'     : '<?php echo $langs->styleManager->properties->line_through ?>',
                                        'className' : 'fa fa-strikethrough'
                                    }
                                ],
                            },
                            'text-shadow',
                            {
                                'property' : 'text-transform',
                                'type'     : 'radio',
                                'default'  : 'none',
                                'options'  : [
                                    {
                                        'id'    : 'none',
                                        'label' : 'x'
                                    },
                                    {
                                        'id'    : 'capitalize',
                                        'label' : 'Tt'
                                    },
                                    {
                                        'id'    : 'lowercase',
                                        'label' : 'tt'
                                    },
                                    {
                                        'id'    : 'uppercase',
                                        'label' : 'TT'
                                    }
                                ]
                            }
                        ],
                    },
                    {
                        'name'       : '<?php echo $langs->styleManager->properties->background ?>',
                        'open'       : false,
                        'properties' : [
                            'background',
                        ],
                    },
                    {
                        'name'       : '<?php echo $langs->styleManager->sectors->decorations ?>',
                        'open'       : false,
                        'properties' : [
                            'opacity',
                            'border-radius',
                            'border',
                        ],
                    },
                ],
            },
            'plugins'         : [
                'grapesjs-blocks-basic',
                'grapesjs-plugin-forms',
                'grapesjs-component-countdown',
                'grapesjs-tabs',
                'grapesjs-custom-code',
                'grapesjs-touch',
                'grapesjs-parser-postcss',
                'grapesjs-tooltip',
                'grapesjs-tui-image-editor',
                'grapesjs-typed',
                'grapesjs-style-bg',
                'grapesjs-preset-webpage',
                'grapesjs-plugin-ckeditor',
            ],
            'pluginsOpts'     : {
                'grapesjs-blocks-basic'     : {
                    'flexGrid' : false,
                },
                'grapesjs-tui-image-editor' : {
                    'script' : [
                        './js/tui/tui-code-snippet.js',
                        './js/tui/tui-color-picker.js',
                        './js/tui/tui-image-editor.js'
                    ],
                    'style'  : [
                        './styles/tui/tui-color-picker.css',
                        './styles/tui/tui-image-editor.css',
                    ],
                },
                'grapesjs-tabs'             : {
                    'tabsBlock' : {'category' : '<?php echo $langs->styleManager->sectors->extra ?>'}
                },
                'grapesjs-typed'            : {
                    'block' : {
                        'category' : '<?php echo $langs->styleManager->sectors->extra ?>',
                        'content'  : {
                            'type'       : 'typed',
                            'type-speed' : 40,
                            'strings'    : [
                                'Text row one',
                                'Text row two',
                                'Text row three',
                            ],
                        }
                    }
                },
                'grapesjs-preset-webpage'   : {
                    'modalImportTitle'   : '<?php echo $langs->preset->webpage->edit_code ?>',
                    'modalImportLabel'   : '<div style="margin-bottom: 10px; font-size: 13px;"><?php echo $langs->preset->webpage->edit_code_paste_here_html ?></div>',
                    'modalImportContent' : function(editor) {
                        var html = editor.getHtml();
                        html = html.split(/<body.*?>/).join('');
                        html = html.split('</body>').join('');

                        var css = "\n" + editor.getCss();
                        css = css.split(/\*.*?}/s).join('');
                        css = css.split(/\nbody.*?}/s).join('');
                        css = css.split(/:root.*?}/s).join('');
                        css = css.split(/\.row.*?}/s).join('');
                        css = css.split(/\[data-gjs-type="?wrapper"?]\s?>\s?#/).join('#');
                        css = css.split(/\[data-gjs-type="?wrapper"?]\s?>\s/).join('');

                        return `${html}\n<style>\n${css}</style>`;
                    },
                },
                'grapesjs-blocks-table'     : {
                    'containerId' : '#gjs'
                },
                'grapesjs-plugin-ckeditor'  : {
                    options : {
                        baseHref            : '<?php echo $CFG->wwwroot ?>/',
                        startupFocus        : true,
                        extraAllowedContent : '*(*);*{*}',
                        allowedContent      : true,
                        enterMode           : 2,
                        extraPlugins        : 'sharedspace,justify,colorbutton,panelbutton,font',
                        toolbar             : "Basic",
                        toolbarGroups       : [
                            {name : 'clipboard', groups : ['clipboard', 'undo']},
                            {name : 'links'},
                            {name : 'basicstyles', groups : ['basicstyles', 'cleanup']},
                            {name : 'colors'},
                            '/',
                            {name : 'styles'},

                        ],
                        font_names          : "Arial/Arial,Helvetica,sans-serif;Courier New/Courier New,Courier,monospace;Verdana/Verdana,Geneva,sans-serif;<?php echo \theme_degrade\fonts\font_util::ckeditor(); ?>",
                        stylesSet           : [
                            {name : 'Paragraph', element : 'p'},
                            {name : 'Heading 1', element : 'h1'},
                            {name : 'Heading 2', element : 'h2'},
                            {name : 'Heading 3', element : 'h3'},
                            {name : 'Heading 4', element : 'h4'},
                            {name : 'Heading 5', element : 'h5'},
                            {name : 'Heading 6', element : 'h6'},
                            {name : 'Preformatted Text', element : 'pre'},
                            {name : 'Address', element : 'address'},

                            {name : 'Big', element : 'big'},
                            {name : 'Small', element : 'small'},
                            {name : 'Typewriter', element : 'tt'},

                            {name : 'Computer Code', element : 'code'},
                            {name : 'Keyboard Phrase', element : 'kbd'},
                            {name : 'Sample Text', element : 'samp'},

                            {name : 'Cited Work', element : 'cite'},
                            {name : 'Inline Quotation', element : 'q'},

                            {name : 'Styled Image (left)', element : 'img', attributes : {'class' : 'left'}},
                            {name : 'Styled Image (right)', element : 'img', attributes : {'class' : 'right'}},

                            {name : 'Square Bulleted List', element : 'ul', styles : {'list-style-type' : 'square'}},
                        ],
                    },
                },
            },
            'canvas'          : {
                'styles'  : [
                    'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css',
                    '<?php echo \theme_degrade\fonts\font_util::css() ?>',
                ],
                'scripts' : [],
            },
            'i18n'            : {
                'locale'         : 'en',
                'detectLocale'   : false,
                'localeFallback' : 'en',
                'messages'       : {
                    'en' : <?php echo json_encode($langs, JSON_PRETTY_PRINT) ?>,
                }
            }
        });

        //editor.getConfig().showDevices = 0;
        editor.Panels.addPanel({
            'id' : "devices-c"
        }).get("buttons").add([
            {
                'id'        : "block-save",
                'className' : "btn-salvar padding-0",
                'label'     : `<form class="form-preview-preview" method="post" target="_top"
                                 style="display:none;margin:0;"
                                 action="<?php echo $CFG->wwwroot ?>/theme/degrade/_editor/">
                               <input type="hidden" name="chave"    value="<?php echo $chave ?>">
                               <input type="hidden" name="editlang" value="<?php echo $editlang ?>">
                               <input type="hidden" name="sesskey"  value="<?php echo sesskey() ?>">
                               <input type="hidden" name="action"   value="save">
                               <input type="hidden" name="htmldata" class="form-htmldata">
                               <input type="hidden" name="cssdata"  class="form-cssdata">
                               <button type="submit" class="btn-salvar gjs-pn-btn gjs-pn-active gjs-four-color">
                                   <i class='fa fa-save'></i>&nbsp;
                                   <?php echo $langs->page->save ?>
                              </button>
                           </form>
                           <form class="form-preview-preview" method="post" target="home-preview"
                                 style="display:none;margin:0;"
                                 action="<?php echo $CFG->wwwroot ?>/#<?php echo $chave ?>">
                               <input type="hidden" name="chave"    value="<?php echo $chave ?>">
                               <input type="hidden" name="editlang" value="<?php echo $editlang ?>">
                               <input type="hidden" name="sesskey"  value="<?php echo sesskey() ?>">
                               <input type="hidden" name="htmldata" class="form-htmldata">
                               <input type="hidden" name="cssdata"  class="form-cssdata">
                               <button type="submit" class="btn-salvar gjs-pn-btn gjs-pn-active gjs-four-color">
                                   <i class='fa fa-eye'></i>&nbsp;
                                   <?php echo $langs->page->preview ?>
                              </button>
                           </form>`,
            }
        ]);

        // Update canvas-clear command
        editor.Commands.add('canvas-clear', function() {
            if (confirm("<?php echo $langs->canvas->clear ?>")) {
                editor.runCommand('core:canvas-clear');
                setTimeout(function() {
                    localStorage.clear()
                }, 0)
            }
        });

        // Simple warn notifier
        var origWarn = console.warn;
        toastr.options = {
            'closeButton'       : true,
            'preventDuplicates' : true,
            'showDuration'      : 250,
            'hideDuration'      : 150
        };
        console.warn = function(msg) {
            if (msg.indexOf('[undefined]') == -1) {
                toastr.warning(msg);
            }
            origWarn(msg);
        };

        // Add and beautify tooltips
        var options = [
            ['sw-visibility', '<?php $a = 'sw-visibility'; echo $langs->panels->buttons->titles->$a ?>'],
            ['preview', '<?php echo $langs->panels->buttons->titles->preview ?>'],
            ['fullscreen', '<?php echo $langs->panels->buttons->titles->fullscreen ?>'],
            ['undo', '<?php echo $langs->panels->buttons->titles->undo ?>'],
            ['redo', '<?php echo $langs->panels->buttons->titles->redo ?>'],
            ['canvas-clear', '<?php echo $langs->panels->buttons->titles->clear ?>'],
            ['gjs-open-import-webpage', '<?php echo $langs->panels->buttons->titles->edit_code ?>'],
        ];
        options.forEach(function(item) {
            editor.Panels.getButton('options', item[0]).set('attributes', {
                title              : item[1],
                'data-tooltip-pos' : 'bottom'
            });
        });

        var views = [
            ['open-sm', '<?php $a = 'open-sm'; echo $langs->panels->buttons->titles->$a ?>'],
            ['open-layers', '<?php $a = 'open-layers'; echo $langs->panels->buttons->titles->$a ?>'],
            ['open-blocks', '<?php $a = 'open-blocks'; echo $langs->panels->buttons->titles->$a ?>'],
        ];
        views.forEach(function(item) {
            editor.Panels.getButton('views', item[0]).set('attributes', {
                title              : item[1],
                'data-tooltip-pos' : 'bottom'
            });
        });
        var titles = document.querySelectorAll('*[title]');

        for (var i = 0; i < titles.length; i++) {
            var el = titles[i];
            var title = el.getAttribute('title');
            title = title ? title.trim() : '';
            if (!title)
                break;
            el.setAttribute('data-tooltip', title);
            el.setAttribute('title', '');
        }


        function showButtonUpdate() {
            var html = editor.getHtml();
            html = html.split(/<body.*?>/).join('');
            html = html.split('</body>').join('');

            var css = editor.getCss();
            css = css.split(/\*.*?}/s).join('');
            css = css.split(/\nbody.*?}/s).join('');
            css = css.split(/:root.*?}/s).join('');
            css = css.split(/\[data-gjs-type="?wrapper"?]\s?>\s?#/).join('#');
            css = css.split(/\[data-gjs-type="?wrapper"?]\s?>\s/).join('');

            $(".form-htmldata").val(html);
            $(".form-cssdata").val(css);
            $(".form-preview-preview").show(300);
        }

        editor.on('update', showButtonUpdate);

        // Do stuff on load
        editor.on('load', function() {
            var $ = grapesjs.$;

            // Show borders by default
            editor.Panels.getButton('options', 'sw-visibility').set({
                'command' : 'core:component-outline',
                'active'  : true,
            });

            // Load and show settings and style manager
            var openTmBtn = editor.Panels.getButton('views', 'open-tm');
            openTmBtn && openTmBtn.set('active', 1);
            var openSm = editor.Panels.getButton('views', 'open-sm');
            openSm && openSm.set('active', 1);

            // Remove trait view
            editor.Panels.removeButton('views', 'open-tm');

            // Add Settings Sector
            var traitsSector = $('<div class="gjs-sm-sector no-select">' +
                '<div class="gjs-sm-sector-title"><span class="icon-settings fa fa-cog"></span> <span class="gjs-sm-sector-label"><?php echo $langs->page->settings ?></span></div>' +
                '<div class="gjs-sm-properties" style="display: none;"></div></div>');
            var traitsProps = traitsSector.find('.gjs-sm-properties');
            traitsProps.append($('.gjs-traits-cs'));
            $('.gjs-sm-sectors').before(traitsSector);
            traitsSector.find('.gjs-sm-sector-title').on('click', function() {
                var traitStyle = traitsProps.get(0).style;
                var hidden = traitStyle.display == 'none';
                if (hidden) {
                    traitStyle.display = 'block';
                } else {
                    traitStyle.display = 'none';
                }
            });

            // Open block manager
            var openBlocksBtn = editor.Panels.getButton('views', 'open-blocks');
            openBlocksBtn && openBlocksBtn.set('active', 1);

            showButtonUpdate();

            // Show help button
            var logoCont = document.querySelector('.gjs-help-icon');
            var logoPanel = document.querySelector('.gjs-pn-commands');
            logoPanel.appendChild(logoCont);
        });
    </script>
    <div style="display: none">
        <div class="gjs-help-icon">
            <a href="https://grapesjs.com/docs/" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 250" style="height:27px;">
                    <g fill="#b9a5a6">
                        <path d="M469.779,250C493.059,250,512,231.463,512,208.682V41.319C512,18.537,493.059,0,469.779,0H42.221C18.94,0,0,18.537,0,41.319
                            v167.363C0,231.465,18.941,250,42.221,250 M42.221,234.309c-14.438,0-26.188-11.496-26.188-25.629V41.318
                            c0-14.133,11.748-25.629,26.188-25.629h427.556c14.439,0,26.189,11.496,26.189,25.629v167.364l0,0
                            c0,14.131-11.748,25.629-26.188,25.629"/>
                        <path id="H" d="M141.821,62.987c-4.729,0-8.563,3.752-8.563,8.381v45.251H77.311V71.368c0-4.627-3.833-8.381-8.564-8.381
                            c-4.731,0-8.563,3.752-8.563,8.381v107.265c0,4.629,3.833,8.381,8.563,8.381c4.73,0,8.564-3.75,8.564-8.381v-45.252h55.947v45.252
                            c0,4.629,3.834,8.381,8.563,8.381s8.564-3.75,8.564-8.381V71.368C150.384,66.739,146.551,62.987,141.821,62.987z"/>
                        <path id="E" d="M242.298,170.252h-54.805c-0.316,0-0.572-0.25-0.572-0.559v-36.314h37.107c4.729,0,8.564-3.75,8.564-8.379
                            c0-4.629-3.835-8.38-8.564-8.38h-37.107V80.305c0-0.308,0.254-0.558,0.572-0.558h54.805c4.729,0,8.563-3.751,8.563-8.38
                            c0-4.629-3.832-8.38-8.563-8.38h-54.805c-9.76,0-17.698,7.768-17.698,17.318v89.386c0,9.553,7.938,17.32,17.698,17.32h54.805
                            c4.729,0,8.563-3.75,8.563-8.381C250.861,174.004,247.027,170.252,242.298,170.252z"/>
                        <path id="L" d="M342.773,170.252h-54.807c-0.313,0-0.57-0.25-0.57-0.559V71.367c0-4.628-3.832-8.381-8.564-8.381
                            c-4.729,0-8.564,3.752-8.564,8.381v98.327c0,9.549,7.941,17.32,17.697,17.32h54.807c4.729,0,8.564-3.752,8.564-8.381
                            C351.336,174.002,347.504,170.252,342.773,170.252z"/>
                        <path id="P" d="M406.715,62.986h-27.404c-9.76,0-17.697,7.769-17.697,17.319v98.328c0,4.629,3.836,8.381,8.566,8.381
                            c4.727,0,8.562-3.752,8.562-8.381v-27.375h27.976c24.869,0,45.1-19.799,45.1-44.135C451.816,82.786,431.581,62.986,406.715,62.986z
                             M406.715,134.496h-27.976V80.305c0-0.309,0.257-0.558,0.571-0.558h27.402c15.424,0,27.973,12.28,27.973,27.375
                            C434.688,122.216,422.139,134.496,406.715,134.496z"/>
                    </g>
                </svg>
            </a>
        </div>
    </div>
    </body>
    </html>

<?php

file_put_contents(__DIR__ . "/js/locale/{$currentlang}-OK.json", json_encode($langs, JSON_PRETTY_PRINT));
