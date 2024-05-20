<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Editor.
 *
 * @package     theme_degrade
 * @copyright   2024 Eduardo kraus (http://eduardokraus.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

$chave = required_param('chave', PARAM_TEXT);
$editlang = required_param('editlang', PARAM_TEXT);
require_login();
require_capability('moodle/site:config', context_system::instance());

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit</title>
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
    <script src="js/plugins/grapesjs-custom-code.js"></script>
    <script src="js/plugins/grapesjs-touch.js"></script>
    <script src="js/plugins/grapesjs-parser-postcss.js"></script>
    <script src="js/plugins/grapesjs-tui-image-editor.js"></script>
    <script src="js/plugins/grapesjs-style-bg.js"></script>
    <script src="js/plugins/grapesjs-style-gradient.js"></script>
    <script src="js/plugins/grapesjs-plugin-ckeditor.js"></script>
    <script src="js/plugins/grapesjs-tabs.js"></script>
    <script src="js/plugins/grapesjs-google-material-icons.js"></script>
    <script src="js/plugins/grapesjs-component-code-editor.js"></script>
    <script src="js/plugins/grapesjs-ui-suggest-classes.js"></script>
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

        $hometype = get_config("theme_degrade", "home_type");

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

    global $plugins;
    require_once("{$CFG->dirroot}/lib/jquery/plugins.php");
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
        'selectorManager' : {'componentFirst' : true},
        'styleManager'    : {
            'sectors' : [
                {
                    'name'       : '<?php echo get_string("grapsjs-general", "theme_degrade") ?>',
                    'properties' : [
                        'display',
                        {'extend' : 'position', 'type' : 'select'},
                        'top',
                        'right',
                        'left',
                        'bottom',
                    ],
                },
                {
                    'name'       : '<?php echo get_string("grapsjs-dimensions", "theme_degrade") ?>',
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
                    'name'       : '<?php echo get_string("grapsjs-tipografia", "theme_degrade") ?>',
                    'open'       : false,
                    'properties' : [
                        {
                            'property' : 'font-family',
                            'type'     : 'select',
                            'name'     : '<?php echo get_string("grapsjs-stylemanager-properties-font-family", "theme_degrade") ?>',
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
                                    'label'     : '<?php echo get_string("grapsjs-stylemanager-properties-left", "theme_degrade") ?>',
                                    'className' : 'fa fa-align-left'
                                },
                                {
                                    'id'        : 'center',
                                    'label'     : '<?php echo get_string("grapsjs-stylemanager-properties-center", "theme_degrade") ?>',
                                    'className' : 'fa fa-align-center'
                                },
                                {
                                    'id'        : 'right',
                                    'label'     : '<?php echo get_string("grapsjs-stylemanager-properties-right", "theme_degrade") ?>',
                                    'className' : 'fa fa-align-right'
                                },
                                {
                                    'id'        : 'justify',
                                    'label'     : '<?php echo get_string("grapsjs-stylemanager-properties-justify", "theme_degrade") ?>',
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
                                    'label'     : '<?php echo get_string("grapsjs-stylemanager-properties-none", "theme_degrade") ?>',
                                    'className' : 'fa fa-times'
                                },
                                {
                                    'id'        : 'underline',
                                    'label'     : '<?php echo get_string("grapsjs-stylemanager-properties-underline", "theme_degrade") ?>',
                                    'className' : 'fa fa-underline'
                                },
                                {
                                    'id'        : 'line-through',
                                    'label'     : '<?php echo get_string("grapsjs-stylemanager-properties-line-through", "theme_degrade") ?>',
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
                    'name'       : '<?php echo get_string("grapsjs-stylemanager-properties-background", "theme_degrade") ?>',
                    'open'       : false,
                    'properties' : [
                        'background',
                    ],
                },
                {
                    'name'       : '<?php echo get_string("grapsjs-decoration", "theme_degrade") ?>',
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
            'grapesjs-style-gradient',
            'grapesjs-tabs',
            'grapesjs-google-material-icons',
            'grapesjs-component-code-editor',
            'grapesjs-ui-suggest-classes',
        ],
        'pluginsOpts'     : {
            'grapesjs-blocks-basic'          : {
                'flexGrid' : false,
            },
            'grapesjs-tui-image-editor'      : {
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
            'grapesjs-typed'                 : {
                'block' : {
                    'category' : 'Extra',
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
            'grapesjs-preset-webpage'        : {
                'modalImportTitle'   : '<?php echo get_string("grapsjs-edit_code", "theme_degrade") ?>',
                'modalImportLabel'   : '<div style="margin-bottom: 10px; font-size: 13px;"><?php echo get_string("grapsjs-edit_code_paste_here_html", "theme_degrade") ?></div>',
                'modalImportContent' : function(editor) {
                    var html = editor.getHtml();
                    html = html.split(/<body.*?>/).join('');
                    html = html.split('</body>').join('');

                    var css = "\n" + editor.getCss();
                    css = css.split(/\*.*?}/s).join('');
                    css = css.split(/\nbody.*?}/s).join('');
                    css = css.split(/:root.*?}/s).join('');
                    css = css.split(/\[data-gjs-type="?wrapper"?]\s?>\s?#/).join('#');
                    css = css.split(/\[data-gjs-type="?wrapper"?]\s?>\s/).join('');

                    return `${html}\n<style>\n${css}</style>`;
                },
            },
            'grapesjs-blocks-table'          : {
                'containerId' : '#gjs'
            },
            'grapesjs-plugin-ckeditor'       : {
                'options' : {
                    'baseHref'            : '<?php echo $CFG->wwwroot ?>/',
                    'startupFocus'        : true,
                    'extraAllowedContent' : '*(*);*{*}',
                    'allowedContent'      : true,
                    'enterMode'           : 2,
                    'extraPlugins'        : 'sharedspace,justify,colorbutton,panelbutton,font',
                    'toolbar'             : "Basic",
                    'toolbarGroups'       : [
                        {'name' : 'clipboard', 'groups' : ['clipboard', 'undo']},
                        {'name' : 'links'},
                        {'name' : 'basicstyles', 'groups' : ['basicstyles', 'cleanup']},
                        {'name' : 'colors'},
                        '/',
                        {'name' : 'styles'},

                    ],
                    'font_names'          : "Arial/Arial,Helvetica,sans-serif;Courier New/Courier New,Courier,monospace;Verdana/Verdana,Geneva,sans-serif;<?php echo \theme_degrade\fonts\font_util::ckeditor(); ?>",
                    'stylesSet'           : [
                        {'name' : 'Paragraph', 'element' : 'p'},
                        {'name' : 'Heading 1', 'element' : 'h1'},
                        {'name' : 'Heading 2', 'element' : 'h2'},
                        {'name' : 'Heading 3', 'element' : 'h3'},
                        {'name' : 'Heading 4', 'element' : 'h4'},
                        {'name' : 'Heading 5', 'element' : 'h5'},
                        {'name' : 'Heading 6', 'element' : 'h6'},
                        {'name' : 'Preformatted Text', 'element' : 'pre'},
                        {'name' : 'Address', 'element' : 'address'},

                        {'name' : 'Big', 'element' : 'big'},
                        {'name' : 'Small', 'element' : 'small'},
                        {'name' : 'Typewriter', 'element' : 'tt'},

                        {'name' : 'Computer Code', 'element' : 'code'},
                        {'name' : 'Keyboard Phrase', 'element' : 'kbd'},
                        {'name' : 'Sample Text', 'element' : 'samp'},

                        {'name' : 'Cited Work', 'element' : 'cite'},
                        {'name' : 'Inline Quotation', 'element' : 'q'},

                        {'name' : 'Styled Image (left)', 'element' : 'img', 'attributes' : {'class' : 'left'}},
                        {'name' : 'Styled Image (right)', 'element' : 'img', 'attributes' : {'class' : 'right'}},

                        {'name' : 'Square Bulleted List', 'element' : 'ul', 'styles' : {'list-style-type' : 'square'}},
                    ],
                },
            },
            'grapesjs-style-gradient'        : {
                'colorPicker' : 'default',
                'grapickOpts' : {
                    'min' : 1,
                    'max' : 99,
                },
            },
            'grapesjs-tabs'                  : {
                'tabsBlock' : {
                    'category' : 'Extra',
                },
            },
            'grapesjs-google-material-icons' : {},
            'grapesjs-component-code-editor' : {},
            'grapesjs-ui-suggest-classes':{},
        },
        'canvas'          : {
            'styles'  : [
                'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css',
                '<?php echo "{$CFG->wwwroot}/local/kopere_dashboard/_editor/styles/bootstrap.css"; ?>',
                '<?php echo \theme_degrade\fonts\font_util::css() ?>',
                '<?php echo "{$CFG->wwwroot}/lib/jquery/{$plugins['ui-css']['files'][0]}"; ?>',
            ],
            'scripts' : [
                '<?php echo "{$CFG->wwwroot}/lib/jquery/{$plugins['jquery']['files'][0]}"; ?>',
                '<?php echo "{$CFG->wwwroot}/lib/jquery/{$plugins['ui']['files'][0]}"; ?>',
                'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js',
            ],
        },
        'i18n'            : {
            'locale'         : 'en',
            'detectLocale'   : false,
            'localeFallback' : 'en',
            'messages'       : {
                'en' : {
                    'assetManager'    : {
                        'addButton'   : "<?php echo get_string("grapsjs-assetmanager-addbutton", "theme_degrade") ?>",
                        'modalTitle'  : "<?php echo get_string("grapsjs-assetmanager-modaltitle", "theme_degrade") ?>",
                        'uploadTitle' : "<?php echo get_string("grapsjs-assetmanager-uploadtitle", "theme_degrade") ?>"
                    },
                    'domComponents'   : {
                        'names' : {
                            ""        : "<?php echo get_string("grapsjs-domcomponents-names-", "theme_degrade") ?>",
                            'wrapper' : "<?php echo get_string("grapsjs-domcomponents-names-wrapper", "theme_degrade") ?>",
                            'text'    : "<?php echo get_string("grapsjs-domcomponents-names-text", "theme_degrade") ?>",
                            'comment' : "<?php echo get_string("grapsjs-domcomponents-names-comment", "theme_degrade") ?>",
                            'image'   : "<?php echo get_string("grapsjs-domcomponents-names-image", "theme_degrade") ?>",
                            'video'   : "<?php echo get_string("grapsjs-domcomponents-names-video", "theme_degrade") ?>",
                            'label'   : "<?php echo get_string("grapsjs-domcomponents-names-label", "theme_degrade") ?>",
                            'link'    : "<?php echo get_string("grapsjs-domcomponents-names-link", "theme_degrade") ?>",
                            'map'     : "<?php echo get_string("grapsjs-domcomponents-names-map", "theme_degrade") ?>",
                            'tfoot'   : "<?php echo get_string("grapsjs-domcomponents-names-tfoot", "theme_degrade") ?>",
                            'tbody'   : "<?php echo get_string("grapsjs-domcomponents-names-tbody", "theme_degrade") ?>",
                            'thead'   : "<?php echo get_string("grapsjs-domcomponents-names-thead", "theme_degrade") ?>",
                            'table'   : "<?php echo get_string("grapsjs-domcomponents-names-table", "theme_degrade") ?>",
                            'row'     : "<?php echo get_string("grapsjs-domcomponents-names-row", "theme_degrade") ?>",
                            'cell'    : "<?php echo get_string("grapsjs-domcomponents-names-cell", "theme_degrade") ?>",
                            'section' : "<?php echo get_string("grapsjs-domcomponents-names-section", "theme_degrade") ?>",
                            'body'    : "<?php echo get_string("grapsjs-domcomponents-names-wrapper", "theme_degrade") ?>"
                        }
                    },
                    'deviceManager'   : {
                        'device'  : "<?php echo get_string("grapsjs-devicemanager-device", "theme_degrade") ?>",
                        'devices' : {
                            'desktop'         : "<?php echo get_string("grapsjs-devicemanager-devices-desktop", "theme_degrade") ?>",
                            'tablet'          : "<?php echo get_string("grapsjs-devicemanager-devices-tablet", "theme_degrade") ?>",
                            'mobileLandscape' : "<?php echo get_string("grapsjs-devicemanager-devices-mobilelandscape", "theme_degrade") ?>",
                            'mobilePortrait'  : "<?php echo get_string("grapsjs-devicemanager-devices-mobileportrait", "theme_degrade") ?>"
                        }
                    },
                    'panels'          : {
                        'buttons' : {
                            'titles' : {
                                'preview'       : "<?php echo get_string("grapsjs-panels-buttons-titles-preview", "theme_degrade") ?>",
                                'fullscreen'    : "<?php echo get_string("grapsjs-panels-buttons-titles-fullscreen", "theme_degrade") ?>",
                                "sw-visibility" : "<?php echo get_string("grapsjs-panels-buttons-titles-sw-visibility", "theme_degrade") ?>",
                                "open-sm"       : "<?php echo get_string("grapsjs-panels-buttons-titles-open-sm", "theme_degrade") ?>",
                                "open-tm"       : "<?php echo get_string("grapsjs-panels-buttons-titles-open-tm", "theme_degrade") ?>",
                                "open-layers"   : "<?php echo get_string("grapsjs-panels-buttons-titles-open-layers", "theme_degrade") ?>",
                                "open-blocks"   : "<?php echo get_string("grapsjs-panels-buttons-titles-open-blocks", "theme_degrade") ?>"
                            }
                        }
                    },
                    'selectorManager' : {
                        'label'      : "<?php echo get_string("grapsjs-selectormanager-label", "theme_degrade") ?>",
                        'selected'   : "<?php echo get_string("grapsjs-selectormanager-selected", "theme_degrade") ?>",
                        'emptyState' : "<?php echo get_string("grapsjs-selectormanager-emptystate", "theme_degrade") ?>",
                        'states'     : {
                            'hover'           : "<?php echo get_string("grapsjs-selectormanager-states-hover", "theme_degrade") ?>",
                            'active'          : "<?php echo get_string("grapsjs-selectormanager-states-active", "theme_degrade") ?>",
                            "nth-of-type(2n)" : "<?php echo get_string("grapsjs-selectormanager-states-nth-of-type-2n", "theme_degrade") ?>"
                        }
                    },
                    'styleManager'    : {
                        'empty'      : "<?php echo get_string("grapsjs-stylemanager-empty", "theme_degrade") ?>",
                        'layer'      : "<?php echo get_string("grapsjs-stylemanager-layer", "theme_degrade") ?>",
                        'fileButton' : "<?php echo get_string("grapsjs-stylemanager-filebutton", "theme_degrade") ?>",
                        'sectors'    : {
                            'general'     : "<?php echo get_string("grapsjs-stylemanager-sectors-general", "theme_degrade") ?>",
                            'layout'      : "<?php echo get_string("grapsjs-stylemanager-sectors-layout", "theme_degrade") ?>",
                            'typography'  : "<?php echo get_string("grapsjs-stylemanager-sectors-typography", "theme_degrade") ?>",
                            'decorations' : "<?php echo get_string("grapsjs-stylemanager-sectors-decorations", "theme_degrade") ?>",
                            'extra'       : "Extra",
                            'flex'        : "<?php echo get_string("grapsjs-stylemanager-sectors-flex", "theme_degrade") ?>",
                            'dimension'   : "<?php echo get_string("grapsjs-stylemanager-sectors-dimension", "theme_degrade") ?>"
                        },
                        'properties' : {
                            'float'                      : "<?php echo get_string("grapsjs-stylemanager-properties-float", "theme_degrade") ?>",
                            'display'                    : "<?php echo get_string("grapsjs-stylemanager-properties-display", "theme_degrade") ?>",
                            'position'                   : "<?php echo get_string("grapsjs-stylemanager-properties-position", "theme_degrade") ?>",
                            'top'                        : "<?php echo get_string("grapsjs-stylemanager-properties-top", "theme_degrade") ?>",
                            'right'                      : "<?php echo get_string("grapsjs-stylemanager-properties-right", "theme_degrade") ?>",
                            'left'                       : "<?php echo get_string("grapsjs-stylemanager-properties-left", "theme_degrade") ?>",
                            'bottom'                     : "<?php echo get_string("grapsjs-stylemanager-properties-bottom", "theme_degrade") ?>",
                            'width'                      : "<?php echo get_string("grapsjs-stylemanager-properties-width", "theme_degrade") ?>",
                            'height'                     : "<?php echo get_string("grapsjs-stylemanager-properties-height", "theme_degrade") ?>",
                            "max-width"                  : "<?php echo get_string("grapsjs-stylemanager-properties-max-width", "theme_degrade") ?>",
                            "max-height"                 : "<?php echo get_string("grapsjs-stylemanager-properties-max-height", "theme_degrade") ?>",
                            'margin'                     : "<?php echo get_string("grapsjs-stylemanager-properties-margin", "theme_degrade") ?>",
                            "margin-top"                 : "<?php echo get_string("grapsjs-stylemanager-properties-margin-top", "theme_degrade") ?>",
                            "margin-right"               : "<?php echo get_string("grapsjs-stylemanager-properties-margin-right", "theme_degrade") ?>",
                            "margin-left"                : "<?php echo get_string("grapsjs-stylemanager-properties-margin-left", "theme_degrade") ?>",
                            "margin-bottom"              : "<?php echo get_string("grapsjs-stylemanager-properties-margin-bottom", "theme_degrade") ?>",
                            'padding'                    : "<?php echo get_string("grapsjs-stylemanager-properties-padding", "theme_degrade") ?>",
                            "padding-top"                : "<?php echo get_string("grapsjs-stylemanager-properties-padding-top", "theme_degrade") ?>",
                            "padding-left"               : "<?php echo get_string("grapsjs-stylemanager-properties-padding-left", "theme_degrade") ?>",
                            "padding-right"              : "<?php echo get_string("grapsjs-stylemanager-properties-padding-right", "theme_degrade") ?>",
                            "padding-bottom"             : "<?php echo get_string("grapsjs-stylemanager-properties-padding-bottom", "theme_degrade") ?>",
                            "font-family"                : "<?php echo get_string("grapsjs-stylemanager-properties-font-family", "theme_degrade") ?>",
                            "font-size"                  : "<?php echo get_string("grapsjs-stylemanager-properties-font-size", "theme_degrade") ?>",
                            "font-weight"                : "<?php echo get_string("grapsjs-stylemanager-properties-font-weight", "theme_degrade") ?>",
                            "letter-spacing"             : "<?php echo get_string("grapsjs-stylemanager-properties-letter-spacing", "theme_degrade") ?>",
                            'color'                      : "<?php echo get_string("grapsjs-stylemanager-properties-color", "theme_degrade") ?>",
                            "line-height"                : "<?php echo get_string("grapsjs-stylemanager-properties-line-height", "theme_degrade") ?>",
                            "text-align"                 : "<?php echo get_string("grapsjs-stylemanager-properties-text-align", "theme_degrade") ?>",
                            "text-shadow"                : "<?php echo get_string("grapsjs-stylemanager-properties-text-shadow", "theme_degrade") ?>",
                            "text-shadow-h"              : "<?php echo get_string("grapsjs-stylemanager-properties-text-shadow-h", "theme_degrade") ?>",
                            "text-shadow-v"              : "<?php echo get_string("grapsjs-stylemanager-properties-text-shadow-v", "theme_degrade") ?>",
                            "text-shadow-blur"           : "<?php echo get_string("grapsjs-stylemanager-properties-text-shadow-blur", "theme_degrade") ?>",
                            "text-shadow-color"          : "<?php echo get_string("grapsjs-stylemanager-properties-text-shadow-color", "theme_degrade") ?>",
                            "border-top-left"            : "<?php echo get_string("grapsjs-stylemanager-properties-border-top-left", "theme_degrade") ?>",
                            "border-top-right"           : "<?php echo get_string("grapsjs-stylemanager-properties-border-top-right", "theme_degrade") ?>",
                            "border-bottom-left"         : "<?php echo get_string("grapsjs-stylemanager-properties-border-bottom-left", "theme_degrade") ?>",
                            "border-bottom-right"        : "<?php echo get_string("grapsjs-stylemanager-properties-border-bottom-right", "theme_degrade") ?>",
                            "border-radius-top-left"     : "<?php echo get_string("grapsjs-stylemanager-properties-border-radius-top-left", "theme_degrade") ?>",
                            "border-radius-top-right"    : "<?php echo get_string("grapsjs-stylemanager-properties-border-radius-top-right", "theme_degrade") ?>",
                            "border-radius-bottom-left"  : "<?php echo get_string("grapsjs-stylemanager-properties-border-radius-bottom-left", "theme_degrade") ?>",
                            "border-radius-bottom-right" : "<?php echo get_string("grapsjs-stylemanager-properties-border-radius-bottom-right", "theme_degrade") ?>",
                            "border-radius"              : "<?php echo get_string("grapsjs-stylemanager-properties-border-radius", "theme_degrade") ?>",
                            'border'                     : "<?php echo get_string("grapsjs-stylemanager-properties-border", "theme_degrade") ?>",
                            "border-width"               : "<?php echo get_string("grapsjs-stylemanager-properties-border-width", "theme_degrade") ?>",
                            "border-style"               : "<?php echo get_string("grapsjs-stylemanager-properties-border-style", "theme_degrade") ?>",
                            "border-color"               : "<?php echo get_string("grapsjs-stylemanager-properties-border-color", "theme_degrade") ?>",
                            "box-shadow"                 : "<?php echo get_string("grapsjs-stylemanager-properties-box-shadow", "theme_degrade") ?>",
                            "box-shadow-h"               : "<?php echo get_string("grapsjs-stylemanager-properties-box-shadow-h", "theme_degrade") ?>",
                            "box-shadow-v"               : "<?php echo get_string("grapsjs-stylemanager-properties-box-shadow-v", "theme_degrade") ?>",
                            "box-shadow-blur"            : "<?php echo get_string("grapsjs-stylemanager-properties-box-shadow-blur", "theme_degrade") ?>",
                            "box-shadow-spread"          : "<?php echo get_string("grapsjs-stylemanager-properties-box-shadow-spread", "theme_degrade") ?>",
                            "box-shadow-color"           : "<?php echo get_string("grapsjs-stylemanager-properties-box-shadow-color", "theme_degrade") ?>",
                            "box-shadow-type"            : "<?php echo get_string("grapsjs-stylemanager-properties-box-shadow-type", "theme_degrade") ?>",
                            'background'                 : "<?php echo get_string("grapsjs-stylemanager-properties-background", "theme_degrade") ?>",
                            "background-color"           : "<?php echo get_string("grapsjs-stylemanager-properties-background-color", "theme_degrade") ?>",
                            "background-image"           : "<?php echo get_string("grapsjs-stylemanager-properties-background-image", "theme_degrade") ?>",
                            "background-repeat"          : "<?php echo get_string("grapsjs-stylemanager-properties-background-repeat", "theme_degrade") ?>",
                            "background-position"        : "<?php echo get_string("grapsjs-stylemanager-properties-background-position", "theme_degrade") ?>",
                            "background-attachment"      : "<?php echo get_string("grapsjs-stylemanager-properties-background-attachment", "theme_degrade") ?>",
                            "background-size"            : "<?php echo get_string("grapsjs-stylemanager-properties-background-size", "theme_degrade") ?>",
                            'transition'                 : "<?php echo get_string("grapsjs-stylemanager-properties-transition", "theme_degrade") ?>",
                            "transition-property"        : "<?php echo get_string("grapsjs-stylemanager-properties-transition-property", "theme_degrade") ?>",
                            "transition-duration"        : "<?php echo get_string("grapsjs-stylemanager-properties-transition-duration", "theme_degrade") ?>",
                            "transition-timing-function" : "<?php echo get_string("grapsjs-stylemanager-properties-transition-timing-function", "theme_degrade") ?>",
                            'perspective'                : "<?php echo get_string("grapsjs-stylemanager-properties-perspective", "theme_degrade") ?>",
                            'transform'                  : "<?php echo get_string("grapsjs-stylemanager-properties-transform", "theme_degrade") ?>",
                            "transform-rotate-x"         : "<?php echo get_string("grapsjs-stylemanager-properties-transform-rotate-x", "theme_degrade") ?>",
                            "transform-rotate-y"         : "<?php echo get_string("grapsjs-stylemanager-properties-transform-rotate-y", "theme_degrade") ?>",
                            "transform-rotate-z"         : "<?php echo get_string("grapsjs-stylemanager-properties-transform-rotate-z", "theme_degrade") ?>",
                            "transform-scale-x"          : "<?php echo get_string("grapsjs-stylemanager-properties-transform-scale-x", "theme_degrade") ?>",
                            "transform-scale-y"          : "<?php echo get_string("grapsjs-stylemanager-properties-transform-scale-y", "theme_degrade") ?>",
                            "transform-scale-z"          : "<?php echo get_string("grapsjs-stylemanager-properties-transform-scale-z", "theme_degrade") ?>",
                            "flex-direction"             : "<?php echo get_string("grapsjs-stylemanager-properties-flex-direction", "theme_degrade") ?>",
                            "flex-wrap"                  : "<?php echo get_string("grapsjs-stylemanager-properties-flex-wrap", "theme_degrade") ?>",
                            "justify-content"            : "<?php echo get_string("grapsjs-stylemanager-properties-justify-content", "theme_degrade") ?>",
                            "align-items"                : "<?php echo get_string("grapsjs-stylemanager-properties-align-items", "theme_degrade") ?>",
                            "align-content"              : "<?php echo get_string("grapsjs-stylemanager-properties-align-content", "theme_degrade") ?>",
                            'order'                      : "<?php echo get_string("grapsjs-stylemanager-properties-order", "theme_degrade") ?>",
                            "flex-basis"                 : "<?php echo get_string("grapsjs-stylemanager-properties-flex-basis", "theme_degrade") ?>",
                            "flex-grow"                  : "<?php echo get_string("grapsjs-stylemanager-properties-flex-grow", "theme_degrade") ?>",
                            "flex-shrink"                : "<?php echo get_string("grapsjs-stylemanager-properties-flex-shrink", "theme_degrade") ?>",
                            "align-self"                 : "<?php echo get_string("grapsjs-stylemanager-properties-align-self", "theme_degrade") ?>",
                        },
                    },
                    'traitManager'    : {
                        'empty'  : "<?php echo get_string("grapsjs-traitmanager-empty", "theme_degrade") ?>",
                        'label'  : "<?php echo get_string("grapsjs-traitmanager-label", "theme_degrade") ?>",
                        'traits' : {
                            'options' : {
                                'target' : {
                                    'false'  : "<?php echo get_string("grapsjs-traitmanager-traits-options-target-false", "theme_degrade") ?>",
                                    '_blank' : "<?php echo get_string("grapsjs-traitmanager-traits-options-target-_blank", "theme_degrade") ?>"
                                },
                            },
                        },
                    },
                },
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
                               <?php echo get_string("grapsjs-page_save", "theme_degrade") ?>
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
                               <?php echo get_string("grapsjs-page_preview", "theme_degrade") ?>
                          </button>
                       </form>`,
        }
    ]);

    // jQuery UI custom component
    editor.BlockManager.add('jquery-ui-accordion', {
        label    : 'Accordion',
        category : "jQuery UI",
        content  : `
                <div class="jquery-ui-accordion">
                    <h3>Section 1</h3>
                    <div>
                        <p>
                            Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer
                            ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit
                            amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut
                            odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.
                        </p>
                    </div>
                    <h3>Section 2</h3>
                    <div>
                        <p>
                            Sed non urna. Donec et ante. Phasellus eu ligula. Vestibulum sit amet
                            purus. Vivamus hendrerit, dolor at aliquet laoreet, mauris turpis porttitor
                            velit, faucibus interdum tellus libero ac justo. Vivamus non quam. In
                            suscipit faucibus urna.
                        </p>
                    </div>
                    <h3>Section 3</h3>
                    <div>
                        <p>
                            Nam enim risus, molestie et, porta ac, aliquam ac, risus. Quisque lobortis.
                            Phasellus pellentesque purus in massa. Aenean in pede. Phasellus ac libero
                            ac tellus pellentesque semper. Sed ac felis. Sed commodo, magna quis
                            lacinia ornare, quam ante aliquam nisi, eu iaculis leo purus venenatis dui.
                        </p>
                    </div>
                </div>`,
        media    : `<svg class="svg-item-replace" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 54 54">
                            <path  d="M5.423,32.092h25.858v1.969H5.423V32.092z M5.423,28.967h34.306v1.971H5.423V28.967z M0.799,25.437v11.749
                                h52.059V25.437H0.799z M0,15.658h54v22.686H0V15.658z M48.234,21.908L48.234,21.908l0.799-0.811l0,0l1.541-1.562l-0.799-0.405
                                l-1.541,1.562l-1.541-1.562l-0.8,0.811l1.541,1.562L48.234,21.908z"/>
                            <path  d="M0,2v9.78h54V2H0z M49.376,7.845l-0.798,0.811l0,0l-0.801-0.811l-1.541-1.562l0.799-0.81l1.543,1.562
                                l1.54-1.562l0.8,0.81L49.376,7.845L49.376,7.845z"/>
                            <path  d="M0,42.221V52h54v-9.779H0z M49.376,48.527l-0.798,0.811l0,0l-0.801-0.811l-1.541-1.562l0.799-0.811
                                l1.543,1.562l1.54-1.562l0.8,0.811L49.376,48.527L49.376,48.527z"/>
                        </svg>`,
    });
    editor.BlockManager.add('jquery-ui-tabs', {
        label    : 'Tabs',
        category : "jQuery UI",
        content  : `
                <div class="jquery-ui-tabs">
                    <ul>
                        <li><a href="#tabs-1">Nunc tincidunt</a></li>
                        <li><a href="#tabs-2">Proin dolor</a></li>
                        <li><a href="#tabs-3">Aenean lacinia</a></li>
                    </ul>
                    <div id="tabs-1">
                        <p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus.</p>
                    </div>
                    <div id="tabs-2">
                        <p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante</p>
                    </div>
                    <div id="tabs-3">
                        <p>Mauris eleifend est et turpis. Duis id erat.</p>
                    </div>
                </div>`,
        media    : `<svg class="svg-item-replace" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 54 54">
                            <path   d="M39.791,2h12.918C53.375,2,54,2.618,54,3.196v12.071c0,0.617-0.668,1.195-1.291,1.195
                                H39.791c-0.666,0-1.291-0.617-1.291-1.195V3.196C38.5,2.618,38.793,2,39.791,2z"/>
                            <path   d="M20.375,2h12.916c0.666,0,1.293,0.617,1.293,1.195v12.071
                                c0,0.617-0.668,1.195-1.293,1.195H20.375c-0.667,0-1.292-0.617-1.292-1.195V3.196C19.083,2.618,19.708,2,20.375,2z"/>
                            <path   d="M15.424,20.087l37.285-0.108c0.668,0,1.291,0.617,1.291,1.195v29.523
                                c0,0.617-0.666,1.193-1.291,1.193L1.292,52C0.625,52,0,51.383,0,50.805V21.283v-5.978V3.196C0,2.579,0.667,2,1.292,2h12.917
                                C14.875,2,15.5,2.618,15.5,3.196v12.071l-0.076,0.039"/>
                        </svg>`,
    });

    editor.StyleManager.addProperty('decorations', {
        name     : 'Gradient',
        property : 'background-image',
        type     : 'gradient',
        defaults : 'none'
    });

    // Bootstrap’s custom component
    var bootstrap_colors = [
        {'id' : 'primary', 'name' : 'Primary', 'fill' : '#007bff', 'color' : '#FFFFFF'},
        {'id' : 'secondary', 'name' : 'Secondary', 'fill' : '#6c757d', 'color' : '#FFFFFF'},
        {'id' : 'success', 'name' : 'Success', 'fill' : '#28a745', 'color' : '#FFFFFF'},
        {'id' : 'danger', 'name' : 'Danger', 'fill' : '#dc3545', 'color' : '#FFFFFF'},
        {'id' : 'warning', 'name' : 'Warning', 'fill' : '#ffc107', 'color' : '#FFFFFF'},
        {'id' : 'info', 'name' : 'Info', 'fill' : '#17a2b8', 'color' : '#FFFFFF'},
        {'id' : 'light', 'name' : 'Light', 'fill' : '#f8f9fa', 'color' : '#212529'},
        {'id' : 'dark', 'name' : 'Dark', 'fill' : '#343a40', 'color' : '#FFFFFF'},
        {'id' : 'link', 'name' : 'Link', 'fill' : '', 'color' : '#007bff'},
    ];
    bootstrap_colors.forEach(function(color) {
        editor.BlockManager.add(`Bootstrap-a-${color.id}`, {
            label    : `${color.name}`,
            category : "Bootstrap",
            content  : `<a href="#" class="btn btn-${color.id}" title="button ${color.name}">${color.name}</a>`,
            media    : `<a href="#" class="btn btn-${color.id}">${color.name}</a>`,
        });
        editor.BlockManager.add(`Bootstrap-a-outline-${color.id}`, {
            label    : `out ${color.name}`,
            category : "Bootstrap",
            content  : `<a href="#" class="btn btn-outline-${color.id}" title="button outline ${color.name}">${color.name}</a>`,
            media    : `<a href="#" class="btn btn-outline-${color.id}">${color.name}</a>`,
        });
    });

    bootstrap_colors.forEach(function(color) {
        if (color.id == 'link') return;
        editor.BlockManager.add(`Bootstrap-alert-${color.id}`, {
            label    : `Alert ${color.name}`,
            category : "Bootstrap",
            content  : `<div class="alert alert-${color.id}" role="alert" title="alert ${color.name}">This is a ${color.id} alert—check it out!</div>`,
            media    : `<div class="alert alert-${color.id}" role="alert">${color.name}</div>`,
        });
    });

    // Update canvas-clear command
    editor.Commands.add('canvas-clear', function() {
        if (confirm("<?php echo get_string("grapsjs-confirm_clear", "theme_degrade") ?>")) {
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
        ['sw-visibility', '<?php echo get_string("grapsjs-show_border", "theme_degrade") ?>'],
        ['preview', '<?php echo get_string("grapsjs-preview", "theme_degrade") ?>'],
        ['fullscreen', '<?php echo get_string("grapsjs-fullscreen", "theme_degrade") ?>'],
        ['undo', '<?php echo get_string("grapsjs-undo", "theme_degrade") ?>'],
        ['redo', '<?php echo get_string("grapsjs-redo", "theme_degrade") ?>'],
        ['canvas-clear', '<?php echo get_string("grapsjs-clear", "theme_degrade") ?>'],
        ['gjs-open-import-webpage', '<?php echo get_string("grapsjs-edit_code", "theme_degrade") ?>'],
    ];
    options.forEach(function(item) {
        editor.Panels.getButton('options', item[0]).set('attributes', {
            'title'            : item[1],
            'data-tooltip-pos' : 'bottom',
        });
    });

    var views = [
        ['open-sm', '<?php echo get_string("grapsjs-open_sm", "theme_degrade") ?>'],
        ['open-layers', '<?php echo get_string("grapsjs-open_layers", "theme_degrade") ?>'],
        ['open-blocks', '<?php echo get_string("grapsjs-open_block", "theme_degrade") ?>'],
    ];
    views.forEach(function(item) {
        editor.Panels.getButton('views', item[0]).set('attributes', {
            'title'            : item[1],
            'data-tooltip-pos' : 'bottom',
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
