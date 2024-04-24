<?php

require_once('../../../config.php');
require_once('../lib.php');

$chave = required_param('chave', PARAM_TEXT);
$editlang = required_param('editlang', PARAM_TEXT);
require_login();
require_capability('moodle/site:config', context_system::instance());

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
    <link rel="stylesheet" href="styles/demos.css">
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
    <?php
    require_once("{$CFG->dirroot}/theme/degrade/classes/fonts/font_util.php");
    $fontList = \theme_degrade\fonts\font_util::list_fonts();
    echo "<style>{$fontList['css']}</style>";
    ?>
</head>
<body>

<div id="gjs" style="height:0; overflow:hidden">
    <?php
    $htmldata = optional_param('htmldata', false, PARAM_RAW);
    if ($htmldata && confirm_sesskey()) {
        $cssdata = optional_param('cssdata', false, PARAM_RAW);
        $html = "{$htmldata}\n<style>{$cssdata}</style>";
        set_config("{$chave}_htmleditor_{$editlang}", $html, "theme_degrade");

        $home_type = get_config("theme_degrade", "home_type");

        redirect("{$CFG->wwwroot}/admin/settings.php?section=themesettingdegrade#theme_degrade_{$chave}");
    }

    if (file_exists(__DIR__ . "/default-{$chave}.html")) {
        $htmldata = get_config("theme_degrade", "{$chave}_htmleditor_{$editlang}");
        if ($htmldata) {
            echo $htmldata;
        } else {
            $htmldata = file_get_contents(__DIR__ . "/default-{$chave}.html");
            $htmldata = str_replace("{wwwroot}", $CFG->wwwroot, $htmldata);
            $htmldata = str_replace("{shortname}", $SITE->shortname, $htmldata);
            $htmldata = str_replace("{fullname}", $SITE->fullname, $htmldata);

            $htmldata = str_replace("{footer_links_title_default}", theme_degrade_get_string('footer_links_title_default'), $htmldata);
            $htmldata = str_replace("{footer_social_title_default}", theme_degrade_get_string('footer_social_title_default'), $htmldata);
            $htmldata = str_replace("{footer_contact_title_default}", theme_degrade_get_string('footer_contact_title_default'), $htmldata);
            $htmldata = str_replace("{contact_address}", theme_degrade_get_string('contact_address'), $htmldata);

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
            'embedAsBase64' : true,
            'assets'        : [],
        },
        'selectorManager' : {componentFirst : true},
        'styleManager'    : {
            'sectors' : [
                {
                    'name'       : '<?php theme_degrade_get_string("grapsjs-general") ?>',
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
                    'name'       : '<?php theme_degrade_get_string("grapsjs-dimensions") ?>',
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
                    'name'       : '<?php theme_degrade_get_string("grapsjs-tipografia") ?>',
                    'open'       : false,
                    'properties' : [
                        {
                            'property' : 'font-family',
                            'type'     : 'select',
                            'name'     : '<?php theme_degrade_get_string("grapsjs-stylemanager-properties-font-family") ?>',
                            'options'  : [
                                {
                                    'id' : "Arial", 'label' : 'Arial', 'value' : 'Arial, Helvetica, sans-serif'
                                },
                                <?php echo $fontList['editor-list'] ?>
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
                                    'label'     : '<?php theme_degrade_get_string("grapsjs-stylemanager-properties-left") ?>',
                                    'className' : 'fa fa-align-left'
                                },
                                {
                                    'id'        : 'center',
                                    'label'     : '<?php theme_degrade_get_string("grapsjs-stylemanager-properties-center") ?>',
                                    'className' : 'fa fa-align-center'
                                },
                                {
                                    'id'        : 'right',
                                    'label'     : '<?php theme_degrade_get_string("grapsjs-stylemanager-properties-right") ?>',
                                    'className' : 'fa fa-align-right'
                                },
                                {
                                    'id'        : 'justify',
                                    'label'     : '<?php theme_degrade_get_string("grapsjs-stylemanager-properties-justify") ?>',
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
                                    'label'     : '<?php theme_degrade_get_string("grapsjs-stylemanager-properties-none") ?>',
                                    'className' : 'fa fa-times'
                                },
                                {
                                    'id'        : 'underline',
                                    'label'     : '<?php theme_degrade_get_string("grapsjs-stylemanager-properties-underline") ?>',
                                    'className' : 'fa fa-underline'
                                },
                                {
                                    'id'        : 'line-through',
                                    'label'     : '<?php theme_degrade_get_string("grapsjs-stylemanager-properties-line-through") ?>',
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
                    'name'       : '<?php theme_degrade_get_string("grapsjs-stylemanager-properties-background") ?>',
                    'open'       : false,
                    'properties' : [
                        'background',
                    ],
                },
                {
                    'name'       : '<?php theme_degrade_get_string("grapsjs-decoration") ?>',
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
                'tabsBlock' : {'category' : '<?php theme_degrade_get_string("grapsjs-stylemanager-sectors-extra") ?>'}
            },
            'grapesjs-typed'            : {
                'block' : {
                    'category' : '<?php theme_degrade_get_string("grapsjs-stylemanager-sectors-extra") ?>',
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
                'modalImportTitle'   : '<?php theme_degrade_get_string("grapsjs-edit_code") ?>',
                'modalImportLabel'   : '<div style="margin-bottom: 10px; font-size: 13px;"><?php theme_degrade_get_string("grapsjs-edit_code_paste_here_html") ?></div>',
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
        },
        'i18n'            : {
            'locale'         : 'en',
            'detectLocale'   : false,
            'localeFallback' : 'en',
            'messages'       : {
                'en' : {
                    'assetManager'    : {
                        'addButton'   : "<?php theme_degrade_get_string("grapsjs-assetmanager-addbutton") ?>",
                        'modalTitle'  : "<?php theme_degrade_get_string("grapsjs-assetmanager-modaltitle") ?>",
                        'uploadTitle' : "<?php theme_degrade_get_string("grapsjs-assetmanager-uploadtitle") ?>"
                    },
                    'domComponents'   : {
                        'names' : {
                            ""        : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-") ?>",
                            'wrapper' : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-wrapper") ?>",
                            'text'    : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-text") ?>",
                            'comment' : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-comment") ?>",
                            'image'   : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-image") ?>",
                            'video'   : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-video") ?>",
                            'label'   : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-label") ?>",
                            'link'    : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-link") ?>",
                            'map'     : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-map") ?>",
                            'tfoot'   : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-tfoot") ?>",
                            'tbody'   : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-tbody") ?>",
                            'thead'   : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-thead") ?>",
                            'table'   : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-table") ?>",
                            'row'     : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-row") ?>",
                            'cell'    : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-cell") ?>",
                            'section' : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-section") ?>",
                            'body'    : "<?php theme_degrade_get_string("grapsjs-domcomponents-names-wrapper") ?>"
                        }
                    },
                    'deviceManager'   : {
                        'device'  : "<?php theme_degrade_get_string("grapsjs-devicemanager-device") ?>",
                        'devices' : {
                            'desktop'         : "<?php theme_degrade_get_string("grapsjs-devicemanager-devices-desktop") ?>",
                            'tablet'          : "<?php theme_degrade_get_string("grapsjs-devicemanager-devices-tablet") ?>",
                            'mobileLandscape' : "<?php theme_degrade_get_string("grapsjs-devicemanager-devices-mobilelandscape") ?>",
                            'mobilePortrait'  : "<?php theme_degrade_get_string("grapsjs-devicemanager-devices-mobileportrait") ?>"
                        }
                    },
                    'panels'          : {
                        'buttons' : {
                            'titles' : {
                                'preview'       : "<?php theme_degrade_get_string("grapsjs-panels-buttons-titles-preview") ?>",
                                'fullscreen'    : "<?php theme_degrade_get_string("grapsjs-panels-buttons-titles-fullscreen") ?>",
                                "sw-visibility" : "<?php theme_degrade_get_string("grapsjs-panels-buttons-titles-sw-visibility") ?>",
                                "open-sm"       : "<?php theme_degrade_get_string("grapsjs-panels-buttons-titles-open-sm") ?>",
                                "open-tm"       : "<?php theme_degrade_get_string("grapsjs-panels-buttons-titles-open-tm") ?>",
                                "open-layers"   : "<?php theme_degrade_get_string("grapsjs-panels-buttons-titles-open-layers") ?>",
                                "open-blocks"   : "<?php theme_degrade_get_string("grapsjs-panels-buttons-titles-open-blocks") ?>"
                            }
                        }
                    },
                    'selectorManager' : {
                        'label'      : "<?php theme_degrade_get_string("grapsjs-selectormanager-label") ?>",
                        'selected'   : "<?php theme_degrade_get_string("grapsjs-selectormanager-selected") ?>",
                        'emptyState' : "<?php theme_degrade_get_string("grapsjs-selectormanager-emptystate") ?>",
                        'states'     : {
                            'hover'           : "<?php theme_degrade_get_string("grapsjs-selectormanager-states-hover") ?>",
                            'active'          : "<?php theme_degrade_get_string("grapsjs-selectormanager-states-active") ?>",
                            "nth-of-type(2n)" : "<?php theme_degrade_get_string("grapsjs-selectormanager-states-nth-of-type-2n") ?>"
                        }
                    },
                    'styleManager'    : {
                        'empty'      : "<?php theme_degrade_get_string("grapsjs-stylemanager-empty") ?>",
                        'layer'      : "<?php theme_degrade_get_string("grapsjs-stylemanager-layer") ?>",
                        'fileButton' : "<?php theme_degrade_get_string("grapsjs-stylemanager-filebutton") ?>",
                        'sectors'    : {
                            'general'     : "<?php theme_degrade_get_string("grapsjs-stylemanager-sectors-general") ?>",
                            'layout'      : "<?php theme_degrade_get_string("grapsjs-stylemanager-sectors-layout") ?>",
                            'typography'  : "<?php theme_degrade_get_string("grapsjs-stylemanager-sectors-typography") ?>",
                            'decorations' : "<?php theme_degrade_get_string("grapsjs-stylemanager-sectors-decorations") ?>",
                            'extra'       : "<?php theme_degrade_get_string("grapsjs-stylemanager-sectors-extra") ?>",
                            'flex'        : "<?php theme_degrade_get_string("grapsjs-stylemanager-sectors-flex") ?>",
                            'dimension'   : "<?php theme_degrade_get_string("grapsjs-stylemanager-sectors-dimension") ?>"
                        },
                        'properties' : {
                            'float'                      : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-float") ?>",
                            'display'                    : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-display") ?>",
                            'position'                   : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-position") ?>",
                            'top'                        : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-top") ?>",
                            'right'                      : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-right") ?>",
                            'left'                       : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-left") ?>",
                            'bottom'                     : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-bottom") ?>",
                            'width'                      : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-width") ?>",
                            'height'                     : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-height") ?>",
                            "max-width"                  : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-max-width") ?>",
                            "max-height"                 : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-max-height") ?>",
                            'margin'                     : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-margin") ?>",
                            "margin-top"                 : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-margin-top") ?>",
                            "margin-right"               : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-margin-right") ?>",
                            "margin-left"                : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-margin-left") ?>",
                            "margin-bottom"              : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-margin-bottom") ?>",
                            'padding'                    : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-padding") ?>",
                            "padding-top"                : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-padding-top") ?>",
                            "padding-left"               : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-padding-left") ?>",
                            "padding-right"              : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-padding-right") ?>",
                            "padding-bottom"             : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-padding-bottom") ?>",
                            "font-family"                : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-font-family") ?>",
                            "font-size"                  : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-font-size") ?>",
                            "font-weight"                : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-font-weight") ?>",
                            "letter-spacing"             : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-letter-spacing") ?>",
                            'color'                      : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-color") ?>",
                            "line-height"                : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-line-height") ?>",
                            "text-align"                 : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-text-align") ?>",
                            "text-shadow"                : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-text-shadow") ?>",
                            "text-shadow-h"              : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-text-shadow-h") ?>",
                            "text-shadow-v"              : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-text-shadow-v") ?>",
                            "text-shadow-blur"           : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-text-shadow-blur") ?>",
                            "text-shadow-color"          : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-text-shadow-color") ?>",
                            "border-top-left"            : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border-top-left") ?>",
                            "border-top-right"           : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border-top-right") ?>",
                            "border-bottom-left"         : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border-bottom-left") ?>",
                            "border-bottom-right"        : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border-bottom-right") ?>",
                            "border-radius-top-left"     : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border-radius-top-left") ?>",
                            "border-radius-top-right"    : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border-radius-top-right") ?>",
                            "border-radius-bottom-left"  : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border-radius-bottom-left") ?>",
                            "border-radius-bottom-right" : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border-radius-bottom-right") ?>",
                            "border-radius"              : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border-radius") ?>",
                            'border'                     : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border") ?>",
                            "border-width"               : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border-width") ?>",
                            "border-style"               : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border-style") ?>",
                            "border-color"               : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-border-color") ?>",
                            "box-shadow"                 : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-box-shadow") ?>",
                            "box-shadow-h"               : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-box-shadow-h") ?>",
                            "box-shadow-v"               : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-box-shadow-v") ?>",
                            "box-shadow-blur"            : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-box-shadow-blur") ?>",
                            "box-shadow-spread"          : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-box-shadow-spread") ?>",
                            "box-shadow-color"           : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-box-shadow-color") ?>",
                            "box-shadow-type"            : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-box-shadow-type") ?>",
                            'background'                 : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-background") ?>",
                            "background-color"           : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-background-color") ?>",
                            "background-image"           : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-background-image") ?>",
                            "background-repeat"          : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-background-repeat") ?>",
                            "background-position"        : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-background-position") ?>",
                            "background-attachment"      : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-background-attachment") ?>",
                            "background-size"            : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-background-size") ?>",
                            'transition'                 : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-transition") ?>",
                            "transition-property"        : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-transition-property") ?>",
                            "transition-duration"        : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-transition-duration") ?>",
                            "transition-timing-function" : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-transition-timing-function") ?>",
                            'perspective'                : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-perspective") ?>",
                            'transform'                  : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-transform") ?>",
                            "transform-rotate-x"         : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-transform-rotate-x") ?>",
                            "transform-rotate-y"         : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-transform-rotate-y") ?>",
                            "transform-rotate-z"         : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-transform-rotate-z") ?>",
                            "transform-scale-x"          : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-transform-scale-x") ?>",
                            "transform-scale-y"          : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-transform-scale-y") ?>",
                            "transform-scale-z"          : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-transform-scale-z") ?>",
                            "flex-direction"             : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-flex-direction") ?>",
                            "flex-wrap"                  : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-flex-wrap") ?>",
                            "justify-content"            : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-justify-content") ?>",
                            "align-items"                : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-align-items") ?>",
                            "align-content"              : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-align-content") ?>",
                            'order'                      : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-order") ?>",
                            "flex-basis"                 : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-flex-basis") ?>",
                            "flex-grow"                  : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-flex-grow") ?>",
                            "flex-shrink"                : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-flex-shrink") ?>",
                            "align-self"                 : "<?php theme_degrade_get_string("grapsjs-stylemanager-properties-align-self") ?>"
                        }
                    },
                    'traitManager'    : {
                        'empty'  : "<?php theme_degrade_get_string("grapsjs-traitmanager-empty") ?>",
                        'label'  : "<?php theme_degrade_get_string("grapsjs-traitmanager-label") ?>",
                        'traits' : {
                            'options' : {
                                'target' : {
                                    'false'  : "<?php theme_degrade_get_string("grapsjs-traitmanager-traits-options-target-false") ?>",
                                    '_blank' : "<?php theme_degrade_get_string("grapsjs-traitmanager-traits-options-target-_blank") ?>"
                                }
                            }
                        }
                    }
                }
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
                               <input type="hidden" name="htmldata" class="form-htmldata">
                               <input type="hidden" name="cssdata"  class="form-cssdata">
                               <button type="submit" class="btn-salvar gjs-pn-btn gjs-pn-active gjs-four-color">
                                   <i class='fa fa-save'></i>&nbsp;
                                   <?php theme_degrade_get_string("grapsjs-page_save") ?>
                              </button>
                           </form>
                           <form class="form-preview-preview" method="post" target="home-preview"
                                 style="display:none;margin:0;"
                                 action="<?php echo $CFG->wwwroot ?>/">
                               <input type="hidden" name="chave"    value="<?php echo $chave ?>">
                               <input type="hidden" name="editlang" value="<?php echo $editlang ?>">
                               <input type="hidden" name="sesskey"  value="<?php echo sesskey() ?>">
                               <input type="hidden" name="htmldata" class="form-htmldata">
                               <input type="hidden" name="cssdata"  class="form-cssdata">
                               <button type="submit" class="btn-salvar gjs-pn-btn gjs-pn-active gjs-four-color">
                                   <i class='fa fa-eye'></i>&nbsp;
                                   <?php theme_degrade_get_string("grapsjs-page_preview") ?>
                              </button>
                           </form>`,
        }
    ]);

    function showButtonUpdate() {
        var html = editor.getHtml();
        html = html.split(/<body.*?>/).join('');
        html = html.split('</body>').join('');

        var css = editor.getCss();
        css = css.split(/\*.*?}/s).join('');
        css = css.split(/body.*?}/s).join('');
        css = css.split(/\[data-gjs-type="?wrapper"?]\s?>\s?#/).join('#');
        css = css.split(/\[data-gjs-type="?wrapper"?]\s?>\s/).join('');

        $(".form-htmldata").val(html);
        $(".form-cssdata").val(css);
        $(".form-preview-preview").show(300);
    }

    editor.on('load', showButtonUpdate);
    editor.on('update', showButtonUpdate);

    // Update canvas-clear command
    editor.Commands.add('canvas-clear', function() {
        if (confirm("<?php theme_degrade_get_string("grapsjs-confirm_clear") ?>")) {
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
        ['sw-visibility', '<?php theme_degrade_get_string("grapsjs-show_border") ?>'],
        ['preview', '<?php theme_degrade_get_string("grapsjs-preview") ?>'],
        ['fullscreen', '<?php theme_degrade_get_string("grapsjs-fullscreen") ?>'],
        ['undo', '<?php theme_degrade_get_string("grapsjs-undo") ?>'],
        ['redo', '<?php theme_degrade_get_string("grapsjs-redo") ?>'],
        ['canvas-clear', '<?php theme_degrade_get_string("grapsjs-clear") ?>'],
        ['gjs-open-import-webpage', '<?php theme_degrade_get_string("grapsjs-edit_code") ?>'],
    ];
    options.forEach(function(item) {
        editor.Panels.getButton('options', item[0]).set('attributes', {title : item[1], 'data-tooltip-pos' : 'bottom'});
    });

    var views = [
        ['open-sm', '<?php theme_degrade_get_string("grapsjs-open_sm") ?>'],
        ['open-layers', '<?php theme_degrade_get_string("grapsjs-open_layers") ?>'],
        ['open-blocks', '<?php theme_degrade_get_string("grapsjs-open_block") ?>'],
    ];
    views.forEach(function(item) {
        editor.Panels.getButton('views', item[0]).set('attributes', {title : item[1], 'data-tooltip-pos' : 'bottom'});
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
            '<div class="gjs-sm-sector-title"><span class="icon-settings fa fa-cog"></span> <span class="gjs-sm-sector-label"><?php echo get_string('grapsjs-settings', 'theme_degrade') ?></span></div>' +
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
    });
</script>
</body>
</html>