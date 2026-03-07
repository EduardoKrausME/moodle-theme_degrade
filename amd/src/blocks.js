define(["jquery"], function ($) {
    let blocks = {
        create: function (cmid, thumb, sizecover) {
            if ($("body").hasClass("editing")) return;

            let $module = $(`body.format-weeks #module-${cmid}, body.format-topics #module-${cmid}`);
            $module.addClass("theme-block");
            $module.find(".activity-item")
                .css({"background-image": `url('${thumb}')`})
                .click(function (event) {
                    if (event.target === this) {
                        location.href = $module.find("a.aalink").attr("href");
                    }
                });
            if (sizecover != undefined) {
                $module.find(".activity-item").css({"background-size": sizecover})
            }
            $module.append($module.find(".activity-completion"));
        },

        create_not_themeblock: function () {
            if ($("body").hasClass("editing")) return;

            let find1 = "body.format-weeks  .activity.activity-wrapper:not(.theme-block)";
            let find2 = "body.format-topics .activity.activity-wrapper:not(.theme-block)";
            let $modules = $(`${find1}, ${find2}`);
            $modules.each(function (id, module) {
                let $module = $(module);
                $module.addClass("theme-block");

                let thumb = $module.find(".activity-icon .activityicon").attr("src");
                let size = "contain";

                if ($module.hasClass("modtype_childcourse")) {
                    let cmid = $module.attr("data-id");
                    thumb = `${M.cfg.wwwroot}/mod/childcourse/thumb.php?cmid=${cmid}`
                    size = "cover";
                }

                $module.find(".activity-item")
                    .css({
                        "background-image": `url('${thumb}')`,
                        "background-size": size,
                    })
                    .click(function (event) {
                        if (event.target === this) {
                            location.href = $module.find("a.aalink").attr("href");
                        }
                    });
                $module.append($module.find(".activity-completion"));
            });
        },

        icons: function (cmid, thumb) {
            $(`#course-index-cm-${cmid}`).addClass("personal-icon");
            let rule = `
                #module-${cmid} .courseicon {
                    background       : #fff;
                    background-color : #fff;
                }
                #course-index-cm-${cmid} .activity-icon img,
                #module-${cmid} .courseicon img,
                .cmid-${cmid} #page-header .activityiconcontainer img {
                    content : url('${thumb}');
                    filter  : none;
                }
                .cmid-${cmid} #page-header .activityiconcontainer img {
                        width     : 45px !important;
                        height    : 45px !important;
                        max-width : 45px !important;
                        max-height: 45px !important;
                }`;
            blocks.add_style_tag(rule);
        },

        color: function (cmid, color) {
            if (!color || color.length < 4) {
                return;
            }
            let rule = `
                #module-${cmid} .courseicon {
                    background       : ${color} !important;
                    background-color : ${color} !important;
                }`;
            blocks.add_style_tag(rule);
        },

        add_style_tag: function (rule) {
            let styleTag = $("#degrade-custom-style");
            if (styleTag.length) {
                styleTag.append(rule);
                return;
            }
            styleTag = $("<style>", {id: "degrade-custom-style", type: "text/css"})
                .appendTo("head");
            styleTag.append(rule);
        }
    };

    return blocks;
});
