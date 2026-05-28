/**
 * https://github.com/mickidum/acc_toolbar
 */
define(["core/templates"], function (Templates) {
    return {
        init: function () {
            degrade_track_vlibras_access_button();
            Templates.render('theme_degrade/settings/acctoolbar', {})
                .then(function (html, js) {
                    window.micAccessTool = new degrade_AccessTool(html);
                    degrade_track_toolbar_accessibility();
                })
                .fail(function (ex) {
                    console.error(ex);
                });
        },
        track_vlibras: function () {
            degrade_track_vlibras_access_button();
        },
    };
});

function degrade_get_accessibility_courseid() {
    if (window.M && M.cfg && M.cfg.courseId) {
        return M.cfg.courseId;
    }

    var body = document.body;
    if (!body || !body.className) {
        return 0;
    }

    var match = body.className.match(/\bcourse-(\d+)\b/);
    return match ? match[1] : 0;
}

function degrade_get_accessibility_cmid() {
    var body = document.body;
    if (!body || !body.className) {
        return 0;
    }

    var match = body.className.match(/\bcmid-(\d+)\b/);
    return match ? match[1] : 0;
}

function degrade_get_accessibility_active_items_list() {
    var state = window.degrade_toolboxAppstate || {};
    var activeItems = [];

    if (state.bodyClassList) {
        for (var bodyClass in state.bodyClassList) {
            if (Object.prototype.hasOwnProperty.call(state.bodyClassList, bodyClass) && state.bodyClassList[bodyClass]) {
                activeItems.push(state.bodyClassList[bodyClass]);
            }
        }
    }

    if (Number(state.fontSize || 1) > 1) {
        activeItems.push('font-size-' + state.fontSize);
    }

    if (state.imagesTitle && activeItems.indexOf('mic-toolbox-content-images') === -1) {
        activeItems.push('mic-toolbox-content-images');
    }

    if (state.keyboardRoot && activeItems.indexOf('mic-toolbox-disable-buttons-keyboard') === -1) {
        activeItems.push('mic-toolbox-disable-buttons-keyboard');
    }

    return activeItems;
}

function degrade_get_accessibility_active_items() {
    return degrade_get_accessibility_active_items_list().join(', ');
}

function degrade_log_accessibility(action, item, status, activeItemsOverride, stateOverride) {
    if (!window.M || !M.cfg || !M.cfg.wwwroot || !M.cfg.sesskey || typeof fetch === 'undefined') {
        return;
    }

    var state = stateOverride || window.degrade_toolboxAppstate || {};
    var params = new URLSearchParams();
    params.append('sesskey', M.cfg.sesskey);
    params.append('action', action || '');
    params.append('item', item || '');
    params.append('status', status || '');
    params.append('courseid', degrade_get_accessibility_courseid());
    params.append('cmid', degrade_get_accessibility_cmid());
    params.append('pageurl', window.location.href);
    params.append('activeitems', typeof activeItemsOverride === 'string' ? activeItemsOverride : degrade_get_accessibility_active_items());
    params.append('statejson', JSON.stringify(state));

    fetch(M.cfg.wwwroot + '/theme/degrade/accessibility-ajax.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: params.toString()
    }).catch(function () {
        // Logging must never block accessibility features.
    });
}

function degrade_track_vlibras_access_button() {
    if (window.degradeVlibraAccessButtonTracked) {
        return;
    }

    window.degradeVlibraAccessButtonTracked = true;
    document.addEventListener('click', function (event) {
        var target = event.target;
        if (!target || !target.closest) {
            return;
        }

        if (target.closest('div[vw-access-button]')) {
            degrade_log_accessibility('vlibras_tab_click', 'vw-access-button', 'clicked');
        }
    }, true);
}

function degrade_track_toolbar_accessibility() {
    if (window.degradeToolbarAccessibilityTracked) {
        return;
    }

    window.degradeToolbarAccessibilityTracked = true;

    document.addEventListener('click', function (event) {
        var target = event.target;
        if (!target || !target.closest) {
            return;
        }

        var toolbarButton = target.closest('#mic-access-tool-box button');
        if (toolbarButton) {
            window.degradeToolbarBeforeItems = degrade_get_accessibility_active_items_list();
        }

        var resetButton = target.closest('#mic-toolbox-disable-buttons-reset-all');
        if (resetButton) {
            degrade_log_accessibility('toolbar_reset', resetButton.id, 'disabled', '', {
                bodyClassList: {},
                fontSize: 1,
                imagesTitle: false,
                keyboardRoot: false,
                initFontSize: false
            });
        }
    }, true);

    document.addEventListener('click', function (event) {
        var target = event.target;
        if (!target || !target.closest) {
            return;
        }

        var openButton = target.closest('#mic-access-tool-general-button');
        if (openButton) {
            degrade_log_accessibility('toolbar_open', openButton.id, 'opened');
            return;
        }

        var toolbarButton = target.closest('#mic-access-tool-box button');
        if (!toolbarButton || toolbarButton.id === 'mic-access-tool-box-close-button' ||
            toolbarButton.id === 'mic-toolbox-disable-buttons-reset-all') {
            return;
        }

        var itemid = toolbarButton.id;
        var beforeItems = window.degradeToolbarBeforeItems || degrade_get_accessibility_active_items_list();

        setTimeout(function () {
            var afterItems = degrade_get_accessibility_active_items_list();
            var status = afterItems.indexOf(itemid) === -1 ? 'disabled' : 'enabled';

            if (itemid === 'mic-toolbox-fonts-up' || itemid === 'mic-toolbox-fonts-down') {
                status = Number((window.degrade_toolboxAppstate || {}).fontSize || 1) > 1 ? 'enabled' : 'disabled';
            }

            degrade_log_accessibility('toolbar_item', itemid, status);

            beforeItems.forEach(function (previousItem) {
                if (previousItem !== itemid && afterItems.indexOf(previousItem) === -1) {
                    degrade_log_accessibility('toolbar_item', previousItem, 'disabled');
                }
            });
        }, 0);
    }, false);
}

function degrade_AccessTool(html) {
    this.init = {
        link: "",
        contact: "",
        buttonPosition: "right",
    };
    if (this.buildToolBox(html)) {
        this.toolBox = document.getElementById("mic-access-tool-box");
        this.toolBoxOpenButton = document.getElementById("mic-access-tool-general-button");
        this.toolBoxCloseButton = document.getElementById("mic-access-tool-box-close-button");
        this.toolBoxOpenButton.addEventListener("click", this.openBox.bind(this));
        this.toolBoxCloseButton.addEventListener("click", this.closeBox.bind(this));
        document.addEventListener("keyup", this.openCloseBoxKeyboard.bind(this));
        this.micContrastMonochrome = document.getElementById("mic-toolbox-contrast-monochrome");
        this.micContrastSoft = document.getElementById("mic-toolbox-contrast-soft");
        this.micContrastHard = document.getElementById("mic-toolbox-contrast-hard");
        this.micContrastMonochrome.addEventListener("click", this.contrastChange);
        this.micContrastSoft.addEventListener("click", this.contrastChange);
        this.micContrastHard.addEventListener("click", this.contrastChange);
        this.micDisableButtonsAnimations = document.getElementById("mic-toolbox-disable-buttons-animations");
        this.micDisableButtonsAnimations.addEventListener("click", this.onceButtonChange);
        this.micdyslexic = document.getElementById("mic-dyslexic-buttons");
        this.micdyslexic.addEventListener("click", this.onceButtonChange);
        this.micDisableButtonsKeyboard = document.getElementById("mic-toolbox-disable-buttons-keyboard");
        this.micDisableButtonsKeyboard.addEventListener("click", this.onceButtonChange);
        this.micToolboxFontsUp = document.getElementById("mic-toolbox-fonts-up");
        this.micToolboxFontsDown = document.getElementById("mic-toolbox-fonts-down");
        this.micToolboxFontsSimple = document.getElementById("mic-toolbox-fonts-simple");
        this.micToolboxFontsUp.addEventListener("click", this.fontsChange);
        this.micToolboxFontsDown.addEventListener("click", this.fontsChange);
        this.micToolboxFontsSimple.addEventListener("click", this.onceButtonChange);
        this.micToolboxContentLinks = document.getElementById("mic-toolbox-content-links");
        this.micToolboxContentHeaders = document.getElementById("mic-toolbox-content-headers");
        this.micToolboxContentImages = document.getElementById("mic-toolbox-content-images");
        this.micToolboxContentLinks.addEventListener("click", this.onceButtonChange);
        this.micToolboxContentHeaders.addEventListener("click", this.onceButtonChange);
        this.micToolboxContentImages.addEventListener("click", this.onceButtonChange);
        this.micToolboxCursorWhite = document.getElementById("mic-toolbox-cursor-big-white");
        this.micToolboxCursorBlack = document.getElementById("mic-toolbox-cursor-big-black");
        this.micToolboxZoomUp = document.getElementById("mic-toolbox-zoom-up");
        this.micToolboxCursorWhite.addEventListener("click", this.cursorChange);
        this.micToolboxCursorBlack.addEventListener("click", this.cursorChange);
        this.micToolboxZoomUp.addEventListener("click", this.onceButtonChange);
        this.micToolboxDisableButtonsAll = document.getElementById("mic-toolbox-disable-buttons-reset-all");
        this.micToolboxDisableButtonsAll.addEventListener("click", this.resetApp.bind(this));
    }
    this.initialApp();
}

degrade_AccessTool.prototype.initialApp = function () {

    window.degrade_toolboxAppstate = JSON.parse(localStorage.getItem('degrade_ACCESSTOOL')) || {
        bodyClassList: {},
        fontSize: 1,
        imagesTitle: false,
        keyboardRoot: false,
        initFontSize: false
    };

    // INIT ADDING CLASSES TO BODY
    if (window.degrade_toolboxAppstate.bodyClassList) {

        for (var bodyClass in window.degrade_toolboxAppstate.bodyClassList) {
            var initBodyClassList = window.degrade_toolboxAppstate.bodyClassList[bodyClass];
            var enabledButton = document.getElementById(initBodyClassList);
            if (enabledButton) {
                enabledButton.classList.add('vi-enabled');
            }
            document.body.classList.add(initBodyClassList);
        }
    }

    // FONT SIZE INIT
    if (window.degrade_toolboxAppstate.fontSize > 1) {
        this.initFontsChange();
    }

    // SET IMAGES TITLES
    if (window.degrade_toolboxAppstate.imagesTitle) {
        this.imagesAddTitles();
    }

    // SET KEBOARD ROOTING
    if (window.degrade_toolboxAppstate.keyboardRoot) {
        this.keyboardRootEnable();
    }
    if (this.init.buttonPosition === 'right') {
        var button = document.getElementById('mic-access-tool-general-button');
        if (button) {
            document.getElementById('mic-access-tool-general-button').classList.add('mic-access-tool-general-button-right');
            document.getElementById('mic-access-tool-box').classList.add('mic-access-tool-box-right');
        }
    }
};

degrade_AccessTool.prototype.initialApp_iframe = function (iframe) {
    setInterval(function () {
        var appstate = JSON.parse(localStorage.getItem('degrade_ACCESSTOOL')) || {
            bodyClassList: false
        };

        iframe.contentDocument.body.classList = [];
        if (appstate.bodyClassList) {
            for (var bodyClass in appstate.bodyClassList) {
                var initBodyClassList = appstate.bodyClassList[bodyClass];
                iframe.contentDocument.body.classList.add(initBodyClassList);
            }
        }
    }, 2000);
};

degrade_AccessTool.prototype.buildToolBox = function (html) {
    if (document.querySelector("body.pagelayout-embedded")) {
        return false;
    }

    var i = document.createElement("div");
    i.id = "mic-init-access-tool";
    i.innerHTML = html;
    i.style.display = "none";
    document.body.insertBefore(i, document.body.firstChild);

    return true;
};

// CONTRAST FUNCTION
degrade_AccessTool.prototype.contrastChange = function (event) {
    event.preventDefault();

    if (document.body.classList.contains(this.id)) {
        this.classList.remove('vi-enabled');
        document.body.classList.remove(this.id);

        delete window.degrade_toolboxAppstate.bodyClassList[this.id];
    } else {
        var buttons = document.querySelectorAll('.mic-contrast-block button');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove('vi-enabled');
            document.body.classList.remove(buttons[i].id);

            delete window.degrade_toolboxAppstate.bodyClassList[buttons[i].id];
        }
        this.classList.add('vi-enabled');
        document.body.classList.add(this.id);

        window.degrade_toolboxAppstate.bodyClassList[this.id] = this.id;
    }
    degrade_AccessTool.prototype.updateState();
};

// CURSOR CHANGE
degrade_AccessTool.prototype.cursorChange = function (event) {
    event.preventDefault();

    if (document.body.classList.contains(this.id)) {
        this.classList.remove('vi-enabled');
        document.body.classList.remove(this.id);
        delete window.degrade_toolboxAppstate.bodyClassList[this.id];
    } else {
        var buttons = document.querySelectorAll('#mic-toolbox-cursor-big-black,#mic-toolbox-cursor-big-white');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove('vi-enabled');
            document.body.classList.remove(buttons[i].id);

            delete window.degrade_toolboxAppstate.bodyClassList[buttons[i].id];
        }
        this.classList.add('vi-enabled');
        document.body.classList.add(this.id);

        window.degrade_toolboxAppstate.bodyClassList[this.id] = this.id;
    }
    degrade_AccessTool.prototype.updateState();
};

degrade_AccessTool.prototype.onceButtonChange = function (event) {
    event.preventDefault();

    if (this.id === 'mic-toolbox-disable-buttons-keyboard') {
        window.degrade_toolboxAppstate.keyboardRoot = !window.degrade_toolboxAppstate.keyboardRoot;
        degrade_AccessTool.prototype.keyboardRootEnable();
    }

    if (this.id === 'mic-toolbox-content-images') {
        degrade_AccessTool.prototype.imagesChange();
    }

    if (document.body.classList.contains(this.id)) {
        this.classList.remove('vi-enabled');
        document.body.classList.remove(this.id);

        delete window.degrade_toolboxAppstate.bodyClassList[this.id];
    } else {
        this.classList.add('vi-enabled');
        document.body.classList.add(this.id);

        window.degrade_toolboxAppstate.bodyClassList[this.id] = this.id;
    }
    degrade_AccessTool.prototype.updateState();
};

degrade_AccessTool.prototype.keyboardRootEnable = function () {
    if (window.degrade_toolboxAppstate.keyboardRoot) {
        var headers = document.querySelectorAll('h1,h2,h3,h4,h5,h6,p,a,button,input,select,textarea');
        for (var i = 0; i < headers.length; i++) {
            var item = headers[i];
            item.tabIndex = i + 1
        }
    } else {
        window.location.reload();
    }
};

// FONTS CHANGE
degrade_AccessTool.prototype.fontsChange = function (event) {
    event.preventDefault();

    // var mainBody = Number(document.body.style.fontSize.split('px')[0]);

    var counter = window.degrade_toolboxAppstate.fontSize;

    if (this.id === 'mic-toolbox-fonts-up') {
        if (counter >= 1.6) {
            return
        }
        var items = document.querySelectorAll('body,h1,h2,h3,h4,h5,h6,p,a,button,input,textarea,li,td,th,strong,span,blockquote,div');
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            var font = window.getComputedStyle(item).getPropertyValue('font-size').split('px');
            var fontSize = Number(font[0]);
            item.style.fontSize = (fontSize * 1.1).toFixed() + 'px';
        }
        counter = (counter * 1.1).toFixed(2);
    }
    if (this.id === 'mic-toolbox-fonts-down') {
        if (counter <= 1) {
            window.degrade_toolboxAppstate.fontSize = 1;
            degrade_AccessTool.prototype.updateState();
            return;
        }
        var items = document.querySelectorAll('body,h1,h2,h3,h4,h5,h6,p,a,button,input,textarea,li,td,th,strong,span,blockquote,div');
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            var font = window.getComputedStyle(item).getPropertyValue('font-size').split('px');
            var fontSize = Number(font[0]);
            item.style.fontSize = (fontSize / 1.1).toFixed() + 'px';
        }
        counter = (counter / 1.1).toFixed(2);
    }

    window.degrade_toolboxAppstate.fontSize = counter;
    degrade_AccessTool.prototype.getFontsChanges(counter);
    degrade_AccessTool.prototype.updateState();
};

// INITIAL FONT SIZE
degrade_AccessTool.prototype.initFontsChange = function () {
    var items = document.querySelectorAll('body,h1,h2,h3,h4,h5,h6,p,a,button,input,textarea,li,td,th,strong,span,blockquote,div');
    var initFontSize = window.degrade_toolboxAppstate.fontSize;
    for (var i = 0; i < items.length; i++) {
        var item = items[i];
        var font = window.getComputedStyle(item).getPropertyValue('font-size');
        item.style.fontSize = font;
        var fs = item.style.fontSize.split('px');
    }
    for (var i = 0; i < items.length; i++) {
        var item = items[i];
        var font = window.getComputedStyle(item).getPropertyValue('font-size').split('px');
        var fs = Number(font[0]);
        item.style.fontSize = (fs * initFontSize).toFixed() + 'px';
    }
    if (initFontSize) {
        this.getFontsChanges(initFontSize);
    }
};

degrade_AccessTool.prototype.initFontsChangeFirst = function () {
    var items = document.querySelectorAll('body,h1,h2,h3,h4,h5,h6,p,a,button,input,textarea,li,td,th,strong,span,blockquote,div');
    for (var i = 0; i < items.length; i++) {
        var item = items[i];
        var font = window.getComputedStyle(item).getPropertyValue('font-size');
        item.style.fontSize = font;
        var fs = item.style.fontSize.split('px');
    }
};

degrade_AccessTool.prototype.getFontsChanges = function (initFontSize) {
    if (initFontSize > 1) {
        document.getElementById('mic-toolbox-fonts-up').classList.add('vi-font-enabled');
        var initPerc = (Number(initFontSize) * 100 - 100).toFixed();
        var perc = '+' + initPerc + '%';
        document.getElementById('mic-toolbox-fonts-up-enabled').textContent = perc;
    } else {
        document.getElementById('mic-toolbox-fonts-up').classList.remove('vi-font-enabled');
        document.getElementById('mic-toolbox-fonts-up-enabled').textContent = "";
    }
};

// IMAGES CHANGE
degrade_AccessTool.prototype.imagesChange = function () {

    if (document.body.classList.contains('mic-toolbox-content-images')) {

        var titles = document.querySelectorAll('.mic-toolbox-images-titles');
        for (var i = 0; i < titles.length; i++) {
            var parent = titles[i].parentElement;
            parent.removeChild(titles[i]);
        }
        window.degrade_toolboxAppstate.imagesTitle = false;
    } else {
        this.imagesAddTitles();
        window.degrade_toolboxAppstate.imagesTitle = true;
    }
};

degrade_AccessTool.prototype.imagesAddTitles = function () {

    var images = document.images;
    for (var i = 0; i < images.length; i++) {
        var img = images[i];
        if (img.alt) {
            var title = document.createElement('span');
            title.className = 'mic-toolbox-images-titles';
            title.textContent = img.alt;
            img.parentNode.insertBefore(title, img);
        } else {
            var title = document.createElement('span');
            title.className = 'mic-toolbox-images-titles';
            title.textContent = M.util.get_string("acctoolbar_image_without_alt", "theme_degrade");
            img.parentNode.insertBefore(title, img);
        }
    }
};

degrade_AccessTool.prototype.updateState = function () {
    var jsonSting = JSON.stringify(window.degrade_toolboxAppstate);
    if (typeof (Storage) !== "undefined") {
        localStorage.setItem('degrade_ACCESSTOOL', jsonSting);
    } else {
        console.log('No Storage Found');
    }
};

degrade_AccessTool.prototype.openBox = function (event) {
    this.toolBox.classList.add('opened-mic-access-tool');
    if (!window.degrade_toolboxAppstate.initFontSize || window.degrade_toolboxAppstate.fontSize <= 1) {
        this.initFontsChangeFirst();
        window.degrade_toolboxAppstate.initFontSize = true;
    }
    this.toolBoxCloseButton.focus();
};

degrade_AccessTool.prototype.closeBox = function (event) {
    this.toolBox.classList.remove('opened-mic-access-tool');
};

degrade_AccessTool.prototype.openCloseBoxKeyboard = function (event) {
    if (event.keyCode == 27) {
        this.closeBox();
    }
    if (event.ctrlKey && event.keyCode == 113) {
        this.openBox();
    }
};

degrade_AccessTool.prototype.resetApp = function (event) {
    localStorage.removeItem('degrade_ACCESSTOOL');
    window.location.reload();
};
