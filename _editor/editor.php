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
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../../config.php");
require_once("editor-lib.php");

global $CFG, $DB, $PAGE, $OUTPUT;

require_admin();

$PAGE->set_context(\context_system::instance());
$PAGE->set_url(new moodle_url("/theme/degrade/_editor/editor.php", $_GET));

if (optional_param("delete", false, PARAM_INT)) {
    $dataid = required_param("dataid", PARAM_INT);

    $PAGE->set_pagelayout("standard");
    $PAGE->set_title(get_string("delete_block_title", "theme_degrade"));
    $PAGE->set_heading(get_string("delete_block_title", "theme_degrade"));

    echo $OUTPUT->header();
    $confirm = md5($dataid . $CFG->wwwroot);
    if (optional_param("confirm", false, PARAM_TEXT) == $confirm) {
        $DB->delete_records("theme_degrade_pages", ["id" => $dataid]);

        \cache::make("theme_degrade", "frontpage_cache")->purge();
        redirect($CFG->wwwroot, get_string("delete_block_success", "theme_degrade"));
    } else {
        echo "
            <p>" . get_string("delete_block_confirm", "theme_degrade") . "</p>
            <div class=\"d-flex\">
                <a class=\"btn btn-danger me-3\"
                   href=\"{$CFG->wwwroot}/theme/degrade/_editor/editor.php?dataid={$dataid}&delete=1&confirm={$confirm}\">
                   " . get_string("yes") . "</a>
                <a class=\"btn btn-info me-3\"
                   href=\"{$CFG->wwwroot}/\">
                   " . get_string("no") . "</a>
            </div>";
    }
    echo $OUTPUT->footer();
    die;
}

if (required_param("dataid", PARAM_TEXT) == "create") {
    $template = required_param("template", PARAM_TEXT);
    $lang = required_param("lang", PARAM_TEXT);
    $local = required_param("local", PARAM_TEXT);
    $page = theme_degrade_editor_create_page($template, $lang, $local);
    redirect("{$CFG->wwwroot}/theme/degrade/_editor/editor.php?dataid={$page->id}");
    die;
}

$dataid = required_param("dataid", PARAM_INT);
$page = $DB->get_record("theme_degrade_pages", ["id" => $dataid], "*", MUST_EXIST);

$formitens = false;
switch ($page->type) {
    case "html":
        $formitens = false;
        break;
    case "html-form":
    case "form":
        $formitens = true;
        break;
    default:
        throw new Exception("Type not found");
}

$lang = $page->lang;
$local = $page->local;

$pageinfo = json_decode($page->info);

$cssfiles = "@import url('model/{$page->template}/style.css');\n";
$cssfiles .= "@import url('css/bootstrap.css');\n";
if (file_exists(__DIR__ . "/model/{$page->template}/editor-plugin.js")) {
    $csscontent = file_get_contents(__DIR__ . "/model/_assets/editor.css");
    $cssfiles .= theme_degrade_replace_lang_by_string($csscontent);
}
if(isset($pageinfo->form->styles)) {
    foreach ($pageinfo->form->styles as $styles) {
        if ($styles == "bootstrap") {
            continue;
        }
        $cssfiles .= "@import url('model/{$page->template}/{$styles}'); \n";
    }
}

if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $page->html, $matches)) {
    $page->html = trim($matches[1]);
}

$languages = get_string_manager()->get_list_of_translations();
?>

<!DOCTYPE html>
<html lang="<?php echo $page->lang ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/build/index.min.js"></script>
    <script src="js/build/tableComponent.min.js"></script>
    <script src="js/build/iconifyComponent.min.js"></script>
    <script src="js/build/flexComponent.min.js"></script>
    <script src="js/build/canvasEmptyState.min.js"></script>
    <script src="js/build/layoutSidebarButtons.min.js"></script>
    <script src="js/build/prosemirror.min.js"></script>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/grapes.css"/>
    <title>GrapesJs</title>
</head>
<body style="margin: 0;">
<form method="post" action="<?php echo theme_degrade_actionurl("page-save") ?>" id="form-save-editor">
    <input type="hidden" name="html" id="html-body" value="<?php echo htmlentities($page->html) ?>">
    <input type="hidden" name="css" id="css-body">
    <input type="hidden" id="editor-sesskey" value="<?php echo sesskey() ?>">
    <input type="hidden" id="editor-lang" value="<?php echo $page->lang ?>">
    <input type="hidden" id="editor-local" value="<?php echo $page->local ?>">
    <input type="hidden" id="editor-dataid" name="dataid" value="<?php echo $page->id ?>">
    <input type="hidden" id="editor-wwwroot" value="<?php echo $CFG->wwwroot ?>">
    <?php
    if ($formitens) {
        require_once("{$CFG->dirroot}/lib/jquery/plugins.php");
        global $plugins;

        $jquery = $plugins["jquery"]["files"][0];
        $jqueryui = $plugins["ui"]["files"][0];
        $jqueryuicss = $plugins["ui-css"]["files"][0]; ?>
        <input type="hidden" id="form-itens-json" value="<?php echo base64_encode($page->info) ?>">
        <div id="form-itens" class="form-itens-<?php echo $page->type; ?>">
            <h3 class="d-flex align-items-center gap-2">
                <label for="change-lang"><?php echo get_string("language") ?>:</label>
                <select class="form-control" id="change-lang" style="width: auto;">
                    <?php
                    echo "<option value=\"all\">" . get_string("language_all", "theme_degrade") . "</option>\n";
                    foreach ($languages as $langcode => $label) {
                        $selected = $page->lang == $langcode ? "selected" : "";
                        echo "<option {$selected} value=\"{$langcode}\">{$label}</option>\n";
                    } ?>
                </select>
            </h3>

            <div class="itens"></div>
            <div id="botoes-editor-action" class="f-flex mt-3 form-itens-<?php echo $page->type; ?>">
                <input type="submit" class="btn btn-primary me-3" value="<?php echo get_string("save") ?>">
                <input type="button" class="btn btn-primary me-3"
                       id="btn-editor-preview"
                       value="<?php echo get_string("preview") ?>">
                <button id="btn-add-block" type="button" class="btn btn-secondary me-3" style="display:none">
                    <?php echo get_string("add_block", "theme_degrade") ?></button>
            </div>
        </div>
        <link rel="stylesheet" href="css/bootstrap.css"/>
        <link rel="stylesheet" href="css/form-itens.css"/>
        <link rel="stylesheet" href="<?php echo "{$CFG->wwwroot}/lib/jquery/{$jqueryuicss}"; ?>"/>
        <script src="<?php echo "{$CFG->wwwroot}/lib/jquery/{$jquery}"; ?>"></script>
        <script src="<?php echo "{$CFG->wwwroot}/lib/jquery/{$jqueryui}"; ?>"></script>
        <script src="js/build/form-itens.min.js"></script><?php
    } else { ?>
        <input type="submit" style="display:none"><?php
    } ?>
</form>
<?php
if ($page->type == "form") { // Only form.
    echo "</body><html>";
    die;
}
?>
<div id="studio-editor" style="height:100dvh"></div>
<script>
    window.GrapesJsCSS = `<?php echo $cssfiles; ?>`

    GrapesJs.createEditor({
        root: "#studio-editor",
        theme: "dark",
        fonts: {
            enableFontManager: true,
        },
        project: {
            type: "web",
            default: {
                pages: [
                    {
                        name: "<?php echo $page->title ?>",
                        component: `<?php echo $page->html; ?>`,
                    },
                ],
                custom: {
                    globalPageSettings: {
                        fonts: {
                            Roboto: {
                                variants: {
                                    regular: {
                                         source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Roboto/Roboto-normal-400.woff'
                                    },
                                    bold: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Roboto/Roboto-normal-700.woff'
                                    }
                                }
                            },
                            'Open Sans': {
                                variants: {
                                    regular: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Open-Sans/Open-Sans-italic-400.woff'
                                    },
                                    bold: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Open-Sans/Open-Sans-italic-700.woff'
                                    }
                                }
                            },
                            Montserrat: {
                                variants: {
                                    regular: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Montserrat/Montserrat-normal-400.woff'
                                    },
                                    bold: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Montserrat/Montserrat-normal-700.woff'
                                    }
                                }
                            },
                            Poppins: {
                                variants: {
                                    regular: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Poppins/Poppins-normal-400.woff'
                                    },
                                    bold: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Poppins/Poppins-normal-700.woff'
                                    }
                                }
                            },
                            Lato: {
                                variants: {
                                    regular: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Lato/Lato-normal-400.woff'
                                    },
                                    bold: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Lato/Lato-normal-700.woff'
                                    }
                                }
                            },
                            'Playfair Display': {
                                variants: {
                                    regular: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Playfair-Display/Playfair-Display-italic-400.woff'
                                    },
                                    bold: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Playfair-Display/Playfair-Display-italic-700.woff'
                                    }
                                }
                            },
                            Merriweather: {
                                variants: {
                                    regular: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Merriweather/Merriweather-normal-400.woff'
                                    },
                                    bold: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Merriweather/Merriweather-normal-700.woff'
                                    }
                                }
                            },
                            Raleway: {
                                variants: {
                                    regular: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Raleway/Raleway-normal-400.woff'
                                    },
                                    bold: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Raleway/Raleway-normal-700.woff'
                                    }
                                }
                            },
                            Oswald: {
                                variants: {
                                    regular: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Oswald/Oswald-normal-400.woff'
                                    },
                                    bold: {
                                        source: '<?php echo $CFG->wwwroot ?>/theme/font.php/degrade/theme/<?php echo theme_get_revision() ?>/Oswald/Oswald-normal-700.woff'
                                    }
                                }
                            }
                        }
                    }
                },
            },
        },
        assets: {
            storageType: "self",
            onLoad: async function () {
                const response = await fetch("<?php echo theme_degrade_actionurl("file-list") ?>", {method: "GET"});

                if (!response.ok) {
                    throw new Error(`Erro na requisição: ${response.status}`);
                }

                return await response.json();
            },
            onUpload: async function ({files}) {
                const body = new FormData();
                for (const file of files) {
                    body.append("files", file);
                }
                const response = await fetch("<?php echo theme_degrade_actionurl("file-upload") ?>", {
                    method: "POST",
                    body
                });
                const result = await response.json();
                return result;
            },
            onDelete: async function ({assets}) {
                if (!assets[0].attributes.delete) {
                    throw new Error("Not delete course file!");
                }

                const body = JSON.stringify(assets[0].attributes);
                await fetch("<?php echo theme_degrade_actionurl("file-delete") ?>", {
                    method: "POST",
                    body
                });
            }
        },
        storage: {
            type: "self",
            onSave: async function ({project, editor}) {
                const files = await editor.runCommand("studio:projectFiles");
                const html = files.find(file => file.mimeType === "text/html").content;
                const css = files.find(file => file.mimeType === "text/css").content;

                document.getElementById("html-body").value = html;
                document.getElementById("css-body").value = css;

                if (event) {
                    document.querySelector("#form-save-editor").submit();
                }
            },
            onLoad: async function ({project, editor}) {
                return [];
            },
            autosaveChanges: 1000000,
            autosaveIntervalMs: 500,
        },
        plugins: [
            GrapesJsPlugins_prosemirror.init({ /* Plugin options: https://app.grapesjs.com/docs-sdk/plugins/rte/prosemirror */ }),
            GrapesJsPlugins_layoutSidebarButtons.init({ /* Plugin options: https://app.grapesjs.com/docs-sdk/plugins/layout/sidebar-buttons */ }),
            GrapesJsPlugins_tableComponent.init({ /* Plugin options: https://app.grapesjs.com/docs-sdk/plugins/components/table */}),
            GrapesJsPlugins_iconifyComponent.init({ /* Plugin options: https://app.grapesjs.com/docs-sdk/plugins/components/iconify */}),
            GrapesJsPlugins_flexComponent.init({ /* Plugin options: https://app.grapesjs.com/docs-sdk/plugins/components/flex */}),
            GrapesJsPlugins_canvasEmptyState.init({ /* Plugin options: https://app.grapesjs.com/docs-sdk/plugins/canvas/emptyState */}),

            <?php
            if (file_exists(__DIR__ . "/model/{$page->template}/editor-plugin.js")) {
                $pluginjs = file_get_contents(__DIR__ . "/model/{$page->template}/editor-plugin.js");
                echo theme_degrade_replace_lang_by_string($pluginjs);
            }
            ?>
        ],
    });
</script>
</body>
<html>