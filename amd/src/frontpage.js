define(["jquery", "core/modal", "core/notification"], function ($, Modal, Notification) {
    var frontpage = {
        add_block: function (lang) {
            $("#editing-add-new-block").show();
            $("#frontpage_add_block").click(function () {
                Modal.create({
                    title: $("#frontpage_add_block_modal").attr("data-title"),
                    body: '<div id="list-models" class="d-flex flex-column"></div>',
                    large: true,
                    show: true,
                    removeOnClose: true,
                }).then(function (modal) {
                    if (!modal.root) {
                        modal.root = modal._root;
                    }
                    modal.modal.addClass('modal-dialog-centered modal-xl');
                    modal.modal.append(`<style>#row-banner{order:-2;}#row-carousel{order:-1;}</style>`);

                    frontpage.add_block_modal_init(lang);
                }).catch(Notification.exception);
            });
        },

        add_block_modal_init: function (lang) {
            async function loadFiles() {
                const response = await fetch(`${M.cfg.wwwroot}/theme/degrade/_editor/model/?lang=${lang}`);

                if (!response.ok) {
                    throw new Error("Error loading files: " + response.status);
                }

                const $list = $("#list-models");
                $list.empty(); // Clear the previous content.

                const data = await response.json();
                // Group by type (part before the '-').
                const groups = {};
                data.forEach(function (item) {
                    const category = item.category;
                    if (!groups[category]) {
                        groups[category] = [];
                        $list.append(`<div id="row-${category}" class="row"></div>`);
                    }
                    groups[category].push(item);
                });

                console.log(groups);

                // For each group, render its items.
                Object.values(groups).forEach(function (grupo) {
                    let width = "";
                    if (grupo.length === 3 || grupo.length === 6) {
                        width = "col-md-4"; // 3 items.
                    } else if (grupo.length === 2) {
                        width = "col-md-6"; // 2 items.
                    } else if (grupo.length === 1) {
                        width = "col-md-6 mx-auto"; // 1 items.
                    } else {
                        width = "col-md-3"; // 4 items.
                    }

                    grupo.forEach(function (item) {
                        const block = $(`
                            <div class="item-model ${width} text-center" role="button">
                                <div class="item-model-border">
                                    <h4>${item.title}</h4>
                                    <img src="${item.image}"
                                         alt="${item.title}"
                                         class="img-fluid mb-2" style="width:100%;border-radius:8px;max-width:350px;">
                                    <div>
                                        <a class="btn btn-primary mb-2"
                                           href="${M.cfg.wwwroot}/theme/degrade/_editor/editor.php?lang=${lang}&local=home&dataid=create&template=${item.id}"
                                           >Adicionar e editar este bloco</a>
                                        <a class="btn btn-secondary mb-2"
                                           href="${item.preview}"
                                           target="_blank">${M.util.get_string('preview', "theme_degrade")}</a>
                                    </div>
                                </div>
                            </div>`);
                        $(`#row-${item.category}`).append(block);
                        block.find("a").click(function () {
                            event.stopImmediatePropagation();
                        })
                    });
                });
            }

            loadFiles();
        },

        editingswitch: function () {
            $(".editmode-block-form")
                .show(300, function () {
                    $(this).css({"display": "flex"})
                });
            $("#homemode-editingswitch").click(function () {
                $("#homemode-editingswitch-form").submit();
            });
        },

        block_order: function () {
            // Butons move page.
            $(".homemode-pages .btn-move-up").click(function () {
                let $item = $(this).closest('.editmode-page-item');
                let $prev = $item.prev('.editmode-page-item');
                if ($prev.length) {
                    frontpage.block_order_move_item($item, $prev, true);
                }
            });
            $(".homemode-pages .btn-move-down").click(function () {
                let $item = $(this).closest('.editmode-page-item');
                let $next = $item.next('.editmode-page-item');
                if ($next.length) {
                    frontpage.block_order_move_item($item, $next, false);
                }
            });
        },

        // Move pages.
        block_order_move_item: function ($item, $target, isUp) {
            $item.slideUp(400, function () {
                if (isUp) {
                    $target.before($item);
                } else {
                    $target.after($item);
                }
                $item.slideDown(400, function () {
                    frontpage.block_order_save_order(); // <-- save after animation.
                });
            });
        },

        // Save order pages.
        block_order_save_order: function () {
            let order = [];

            $('.editmode-page-item').each(function () {
                order.push($(this).data('pageid'));
            });

            $.ajax({
                url: `${M.cfg.wwwroot}/theme/degrade/_editor/actions.php?action=page-order&local=home`,
                type: 'POST',
                data: {
                    order: order,
                    sesskey: M.cfg.sesskey,
                },
                success: function (response) {
                    console.log('Success', response);
                },
                error: function (error) {
                    console.error('error', error);
                }
            });
        }
    };

    return frontpage;
});
