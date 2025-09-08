$(document).ready(function () {
    $(".faq-editor-item").click( function () {
        let $this = $(this);
        let id = $this.attr("id");
        $(`.faq-editor-item:not(#${id}) .faq-answer`).addClass("faq-hidden");
        $this.find(".faq-answer").toggleClass("faq-hidden");
        $this.find("svg").toggleClass("rotate-180");
    });
});
