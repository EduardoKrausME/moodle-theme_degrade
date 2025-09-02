$(document).ready(function () {
    let $owls = $('.owl-courses-content');
    $owls.each(function () {
        let owl = $(this).owlCarousel({
            autoplay: false,
            nav: true,
            loop: true,
            dots: false,
            autoplayTimeout: 5000,
            autoplayHoverPause: false,
            center: false,
            autoWidth: true,
            smartSpeed: 500,
            margin: 10,
            responsive: {
                1500: {items: 1, slideBy: 3},
                1024: {items: 1, slideBy: 3},
                768: {items: 1, slideBy: 3},
                0: {items: 1, slideBy: 3}
            }
        });
        owl_mousewheel(owl);
    });

    let $owlbanners = $('.owl-course-banner');
    $owlbanners.each(function () {
        let owl = $(this).owlCarousel({
            "autoplay": false,
            "nav": true,
            "dots": false,
            "autoplayTimeout": 5000,
            "autoplayHoverPause": true,
            "center": false,
            "animateOut": "fadeOut",
            "loop": true,
            "autoWidth": false,
            "smartSpeed": 500,
            "responsive": {
                "1024": {"items": 1, "slideBy": 1},
                "768": {"items": 1, "slideBy": 1},
                "0": {"items": 1, "slideBy": 1}
            }
        });
        owl_mousewheel(owl);

        owl.on('translated.owl.carousel', function (event) {
            let $containerplayer = $(".owl-item.active .video-bg-player");
            show_youtube_video($containerplayer);
        });
        owl.on("initialized.owl.carousel", function (event) {
            let $containerplayer = $(".owl-item.active .video-bg-player");
            show_youtube_video($containerplayer);
        });
    });

    /**
     * owl_mousewheel
     *
     * @param owl
     */
    function owl_mousewheel(owl) {
        let lastScrollTime = 0;
        owl.on('mousewheel', '.owl-stage', function (event) {
            const now = Date.now();
            if (now - lastScrollTime < 1500) {
                return;
            }
            lastScrollTime = now;

            if (event.originalEvent.deltaY > 0 || event.originalEvent.deltaX > 0) {
                owl.trigger('next.owl');
            } else {
                owl.trigger('prev.owl');
            }
        });
    }

    /**
     * show_youtube_video
     *
     * @param $containerplayer
     */
    function show_youtube_video($containerplayer) {
        if (!$containerplayer) {
            return;
        }

        let video_url = $containerplayer.attr("data-trailer")
        if(video_url) {
            if (video_url.includes("youtube.com") || video_url.includes("youtu.be")) { // video_url do YouTube

                let regExp = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
                let match = video_url.match(regExp);
                let youtube_id = match ? match[1] : null;
                if (!youtube_id || document.getElementById(youtube_id)) {
                    return;
                }

                $containerplayer.append(`<div id="${youtube_id}"></div>`);

                if (typeof YT !== "undefined" && typeof YT.Player !== "undefined") {
                    new YT.Player(youtube_id, {
                        videoId: youtube_id,
                        width: '100%',
                        height: '100%',
                        playerVars: {
                            autoplay: 1,
                            loop: 1,
                            playlist: youtube_id, // Necessário para loop funcionar com autoplay.
                            controls: 0,          // Opcional: esconde os controles.
                            showinfo: 0,
                            modestbranding: 1,
                            rel: 0
                        },
                        events: {
                            'onReady': function (event) {
                                event.target.mute(); // necessário para autoplay em navegadores modernos.
                                event.target.playVideo();
                            }
                        }
                    });
                }
            }
        }
    }
});
