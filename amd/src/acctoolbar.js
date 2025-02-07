/**
 * https://github.com/mickidum/acc_toolbar
 */
define([], function() {
    return {
        init: function(iconurl) {
            window.micAccessTool = new MicAccessTool(iconurl, {
                toolbar:M.util.get_string("acctoolbar_toolbar", "theme_degrade"),
                btn_open: M.util.get_string("acctoolbar_btn_open", "theme_degrade"),
                btn_close: M.util.get_string("acctoolbar_btn_close", "theme_degrade"),
                keyboard_root: M.util.get_string("acctoolbar_keyboard_root", "theme_degrade"),
                disable_animattions: M.util.get_string("acctoolbar_disable_animattions", "theme_degrade"),
                dyslexic: M.util.get_string("acctoolbar_dyslexic", "theme_degrade"),
                access_declaration: M.util.get_string("acctoolbar_access_declaration", "theme_degrade"),
                debug_contacts: M.util.get_string("acctoolbar_debug_contacts", "theme_degrade"),
                reset_all_settings: M.util.get_string("acctoolbar_reset_all_settings", "theme_degrade"),
                image_without_alt: M.util.get_string("acctoolbar_image_without_alt", "theme_degrade"),
                contrast_block: {
                    header: M.util.get_string("acctoolbar_contrast_block_header", "theme_degrade"),
                    btn_monochrome: M.util.get_string("acctoolbar_btn_monochrome", "theme_degrade"),
                    btn_bright: M.util.get_string("acctoolbar_btn_bright", "theme_degrade"),
                    btn_invert: M.util.get_string("acctoolbar_btn_invert", "theme_degrade")
                },
                text_block: {
                    header: M.util.get_string("acctoolbar_text_block_header", "theme_degrade"),
                    btn_font_up: M.util.get_string("acctoolbar_btn_font_up", "theme_degrade"),
                    btn_font_down: M.util.get_string("acctoolbar_btn_font_down", "theme_degrade"),
                    btn_font_readable: M.util.get_string("acctoolbar_btn_font_readable", "theme_degrade")
                },
                content_block: {
                    header: M.util.get_string("acctoolbar_content_block_header", "theme_degrade"),
                    btn_underline_links: M.util.get_string("acctoolbar_btn_underline_links", "theme_degrade"),
                    btn_underline_headers: M.util.get_string("acctoolbar_btn_underline_headers", "theme_degrade"),
                    btn_images_titles: M.util.get_string("acctoolbar_btn_images_titles", "theme_degrade")
                },
                zoom_block: {
                    header: M.util.get_string("acctoolbar_zoom_block_header", "theme_degrade"),
                    btn_cursor_white: M.util.get_string("acctoolbar_btn_cursor_white", "theme_degrade"),
                    btn_cursor_black: M.util.get_string("acctoolbar_btn_cursor_black", "theme_degrade"),
                    btn_zoom_in: M.util.get_string("acctoolbar_btn_zoom_in", "theme_degrade")
                }
            });
        },
    };
});

function MicAccessTool(iconurl, locale) {
    this.init = {
        iconurl: iconurl,
        link: "",
        contact: "",
        buttonPosition: "right",
    };
    this.locale = locale;
    this.buildToolBox();
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
    this.initialApp();
}

MicAccessTool.prototype.buildToolBox = function() {
    var o = `
        <button title="${this.locale.btn_open}" tabindex="1" id="mic-access-tool-general-button"
                class="mic-access-tool-general-button">
            <div>
                <span>CTRL+F2</span>
                <img src="${this.init.iconurl}" 
                     alt="${this.locale.btn_open}">
            </div>
        </button>
        <div id="mic-access-tool-box" class="mic-access-tool-box">
            <div class="mic-access-tool-box-header">
                ${this.locale.toolbar}
                <button title="${this.locale.btn_close}" id="mic-access-tool-box-close-button">
                    ${this.locale.btn_close}
                </button>
            </div>
            <div class="mic-disable-buttons">
                <button title="${this.locale.keyboard_root}" id="mic-toolbox-disable-buttons-keyboard">
                    <span>${this.locale.keyboard_root}</span>
                    <img src="${this.init.iconurl.replace('icon','keyboard_root')}"
                         alt="${this.locale.keyboard_root}">
                </button>
                <button title="${this.locale.disable_animattions}" id="mic-toolbox-disable-buttons-animations">
                    <span>${this.locale.disable_animattions}</span>
                    <img src="${this.init.iconurl.replace('icon','disable_animattions')}"
                         alt="${this.locale.disable_animattions}">
                </button>
                <button title="${this.locale.dyslexic}" id="mic-dyslexic-buttons">
                    <span>${this.locale.dyslexic}</span>
                    <img src="${this.init.iconurl.replace('icon','dyslexic')}"
                         alt="${this.locale.dyslexic}" width="32" height="32">
                </button>
            </div>
            <div id="mic-toolbox-contrast-block" class="mic-contrast-block mic-buttons-block">
                <span class="mic-subtitle-span">${this.locale.contrast_block.header}</span>
                <button title="${this.locale.contrast_block.btn_monochrome}" id="mic-toolbox-contrast-monochrome">
                    <span>
                        <img alt="${this.locale.contrast_block.btn_monochrome}"
                             src="${this.init.iconurl.replace('icon','btn_monochrome')}">
                    </span>
                    <span>${this.locale.contrast_block.btn_monochrome}</span>
                </button>
                <button title="${this.locale.contrast_block.btn_bright}" id="mic-toolbox-contrast-soft">
                    <span>
                        <img alt="${this.locale.contrast_block.btn_bright}"
                             src="${this.init.iconurl.replace('icon','btn_bright')}">
                    </span>
                    <span>${this.locale.contrast_block.btn_bright}</span>
                </button>
                <button title="${this.locale.contrast_block.btn_invert}" id="mic-toolbox-contrast-hard">
                    <span>
                        <img alt="${this.locale.contrast_block.btn_invert}"
                             src="${this.init.iconurl.replace('icon','btn_invert')}">
                    </span>
                    <span>${this.locale.contrast_block.btn_invert}</span>
                </button>
            </div>
            <div class="mic-fonts-block mic-buttons-block">
                <span class="mic-subtitle-span">${this.locale.text_block.header}</span>
                <button title="${this.locale.text_block.btn_font_up}" id="mic-toolbox-fonts-up">
                    <span>
                        <img src="${this.init.iconurl.replace('icon','btn_font_up')}"
                             alt="${this.locale.text_block.btn_font_up}">
                    </span>
                    <span>${this.locale.text_block.btn_font_up}</span>
                    <span id="mic-toolbox-fonts-up-enabled"></span>
                </button>
                <button title="${this.locale.text_block.btn_font_down}" id="mic-toolbox-fonts-down">
                    <span>
                        <img src="${this.init.iconurl.replace('icon','btn_font_down')}"
                             alt="${this.locale.text_block.btn_font_down}">
                    </span>
                    <span>${this.locale.text_block.btn_font_down}</span>
                </button>
                <button title="${this.locale.text_block.btn_font_readable}" id="mic-toolbox-fonts-simple">
                    <span>
                        <img src="${this.init.iconurl.replace('icon','btn_font_readable')}"
                             alt="${this.locale.text_block.btn_font_readable}">
                    </span>
                    <span>${this.locale.text_block.btn_font_readable}</span>
                </button>
            </div>
            <div class="mic-content-block mic-buttons-block">
                <span class="mic-subtitle-span">${this.locale.content_block.header}</span>
                <button title="${this.locale.content_block.btn_underline_links}" id="mic-toolbox-content-links">
                    <span>
                        <img src="${this.init.iconurl.replace('icon','btn_underline_links')}"
                             alt="${this.locale.content_block.btn_underline_links}">
                    </span>
                    <span>${this.locale.content_block.btn_underline_links}</span>
                </button>
                <button title="${this.locale.content_block.btn_underline_headers}" id="mic-toolbox-content-headers">
                    <span>
                        <img src="${this.init.iconurl.replace('icon','btn_underline_headers')}"
                             alt="${this.locale.content_block.btn_underline_headers}">
                    </span>
                    <span>${this.locale.content_block.btn_underline_headers}</span>
                </button>
                <button title="${this.locale.content_block.btn_images_titles}" id="mic-toolbox-content-images">
                    <span>
                        <img src="${this.init.iconurl.replace('icon','btn_images_titles')}"
                             alt="${this.locale.content_block.btn_images_titles}">
                    </span>
                    <span>${this.locale.content_block.btn_images_titles}</span>
                </button>
            </div>
            <div class="mic-cursors-block mic-buttons-block">
                <span class="mic-subtitle-span">${this.locale.zoom_block.header}</span>
                <button title="${this.locale.zoom_block.btn_cursor_white}" id="mic-toolbox-cursor-big-white">
                    <span>
                        <img alt="${this.locale.zoom_block.btn_cursor_white}"
                             src="${this.init.iconurl.replace('icon','btn_cursor_white')}">
                    </span>
                    <span>${this.locale.zoom_block.btn_cursor_white}</span>
                </button>
                <button title="${this.locale.zoom_block.btn_cursor_black}" id="mic-toolbox-cursor-big-black">
                    <span>
                        <img alt="${this.locale.zoom_block.btn_cursor_black}"
                             src="${this.init.iconurl.replace('icon','btn_cursor_black')}">
                    </span>
                    <span>${this.locale.zoom_block.btn_cursor_black}</span>
                </button>
                <button title="${this.locale.zoom_block.btn_zoom_in}" id="mic-toolbox-zoom-up">
                    <span>
                        <img alt="${this.locale.zoom_block.btn_zoom_in}"
                             src="${this.init.iconurl.replace('icon','btn_zoom_in')}">
                    </span>
                    <span>${this.locale.zoom_block.btn_zoom_in}</span>
                </button>
            </div>
            <div class="link-access-page">
                <a class="atb-hide-if-empty" 
                   title="${this.locale.access_declaration}" 
                   id="mic-toolbox-link-nagishut" 
                   href="#"
                   target="_blank">${this.locale.access_declaration}</a>
                <a class="atb-hide-if-empty" 
                   title="${this.locale.debug_contacts}" 
                   id="mic-toolbox-link-contact" 
                   href="#">${this.locale.debug_contacts}</a>
                <button title="${this.locale.reset_all_settings}" 
                        id="mic-toolbox-disable-buttons-reset-all">
                    <span>${this.locale.reset_all_settings}</span>
                    <img src="${this.init.iconurl.replace('icon','reset_all_settings')}"
                         alt="${this.locale.reset_all_settings}">
                </button>
            </div>
        </div>`;

    var i = document.createElement("div");
    i.id = "mic-init-access-tool";
    i.innerHTML = o;
    document.body.insertBefore(i, document.body.firstChild)
};

// CONTRAST FUNCTION
MicAccessTool.prototype.contrastChange = function(event) {
    event.preventDefault();

    if (document.body.classList.contains(this.id)) {
        this.classList.remove('vi-enabled');
        document.body.classList.remove(this.id);

        delete window.MICTOOLBOXAPPSTATE.bodyClassList[this.id];
    }
    else {
        var buttons = document.querySelectorAll('.mic-contrast-block button');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove('vi-enabled');
            document.body.classList.remove(buttons[i].id);

            delete window.MICTOOLBOXAPPSTATE.bodyClassList[buttons[i].id];
        }
        this.classList.add('vi-enabled');
        document.body.classList.add(this.id);

        window.MICTOOLBOXAPPSTATE.bodyClassList[this.id] = this.id;
    }
    MicAccessTool.prototype.updateState();
};

// CURSOR CHANGE
MicAccessTool.prototype.cursorChange = function(event) {
    event.preventDefault();

    if (document.body.classList.contains(this.id)) {
        this.classList.remove('vi-enabled');
        document.body.classList.remove(this.id);
        delete window.MICTOOLBOXAPPSTATE.bodyClassList[this.id];
    }
    else {
        var buttons = document.querySelectorAll('#mic-toolbox-cursor-big-black,#mic-toolbox-cursor-big-white');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove('vi-enabled');
            document.body.classList.remove(buttons[i].id);

            delete window.MICTOOLBOXAPPSTATE.bodyClassList[buttons[i].id];
        }
        this.classList.add('vi-enabled');
        document.body.classList.add(this.id);

        window.MICTOOLBOXAPPSTATE.bodyClassList[this.id] = this.id;
    }
    MicAccessTool.prototype.updateState();
};

MicAccessTool.prototype.onceButtonChange = function(event) {
    event.preventDefault();

    if (this.id === 'mic-toolbox-disable-buttons-keyboard') {
        window.MICTOOLBOXAPPSTATE.keyboardRoot = !window.MICTOOLBOXAPPSTATE.keyboardRoot;
        MicAccessTool.prototype.keyboardRootEnable();
    }

    if (this.id === 'mic-toolbox-content-images') {
        MicAccessTool.prototype.imagesChange();
    }

    if (document.body.classList.contains(this.id)) {
        this.classList.remove('vi-enabled');
        document.body.classList.remove(this.id);

        delete window.MICTOOLBOXAPPSTATE.bodyClassList[this.id];
    }
    else {
        this.classList.add('vi-enabled');
        document.body.classList.add(this.id);

        window.MICTOOLBOXAPPSTATE.bodyClassList[this.id] = this.id;
    }
    MicAccessTool.prototype.updateState();
};

MicAccessTool.prototype.keyboardRootEnable = function() {
    if (window.MICTOOLBOXAPPSTATE.keyboardRoot) {
        var headers = document.querySelectorAll('h1,h2,h3,h4,h5,h6,p,a,button,input,select,textarea');
        for (var i = 0; i < headers.length; i++) {
            var item = headers[i];
            item.tabIndex = i + 1
        }
    }
    else {
        window.location.reload();
    }
};

// FONTS CHANGE
MicAccessTool.prototype.fontsChange = function(event) {
    event.preventDefault();

    // var mainBody = Number(document.body.style.fontSize.split('px')[0]);

    var counter = window.MICTOOLBOXAPPSTATE.fontSize;

    if (this.id === 'mic-toolbox-fonts-up') {
        if (counter >= 1.6) {return}
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
            window.MICTOOLBOXAPPSTATE.fontSize = 1;
            MicAccessTool.prototype.updateState();
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

    window.MICTOOLBOXAPPSTATE.fontSize = counter;
    MicAccessTool.prototype.getFontsChanges(counter);
    MicAccessTool.prototype.updateState();

};

// INITIAL FONT SIZE
MicAccessTool.prototype.initFontsChange = function() {
    var items = document.querySelectorAll('body,h1,h2,h3,h4,h5,h6,p,a,button,input,textarea,li,td,th,strong,span,blockquote,div');
    var initFontSize = window.MICTOOLBOXAPPSTATE.fontSize;
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

MicAccessTool.prototype.initFontsChangeFirst = function() {
    var items = document.querySelectorAll('body,h1,h2,h3,h4,h5,h6,p,a,button,input,textarea,li,td,th,strong,span,blockquote,div');
    for (var i = 0; i < items.length; i++) {
        var item = items[i];
        var font = window.getComputedStyle(item).getPropertyValue('font-size');
        item.style.fontSize = font;
        var fs = item.style.fontSize.split('px');
    }
};

MicAccessTool.prototype.getFontsChanges = function(initFontSize) {
    if (initFontSize > 1) {
        document.getElementById('mic-toolbox-fonts-up').classList.add('vi-font-enabled');
        var initPerc = (Number(initFontSize) * 100 - 100).toFixed();
        var perc = '+' + initPerc + '%';
        document.getElementById('mic-toolbox-fonts-up-enabled').textContent = perc;
    }
    else {
        document.getElementById('mic-toolbox-fonts-up').classList.remove('vi-font-enabled');
        document.getElementById('mic-toolbox-fonts-up-enabled').textContent = '';
    }
};

// IMAGES CHANGE
MicAccessTool.prototype.imagesChange = function() {

    if (document.body.classList.contains('mic-toolbox-content-images')) {

        var titles = document.querySelectorAll('.mic-toolbox-images-titles');
        for (var i = 0; i < titles.length; i++) {
            var parent = titles[i].parentElement;
            parent.removeChild(titles[i]);
        }
        window.MICTOOLBOXAPPSTATE.imagesTitle = false;
    }

    else {
        this.imagesAddTitles();
        window.MICTOOLBOXAPPSTATE.imagesTitle = true;
    }
};

MicAccessTool.prototype.imagesAddTitles = function() {

    var images = document.images;
    for (var i = 0; i < images.length; i++) {
        var img = images[i];
        if (img.alt) {
            var title = document.createElement('span');
            title.className = 'mic-toolbox-images-titles';
            title.textContent = img.alt;
            img.parentNode.insertBefore(title, img);
        }
        else {
            var title = document.createElement('span');
            title.className = 'mic-toolbox-images-titles';
            title.textContent = 'image without text';
            img.parentNode.insertBefore(title, img);
        }
    }

};


MicAccessTool.prototype.updateState = function() {
    var jsonSting = JSON.stringify(window.MICTOOLBOXAPPSTATE);
    if (typeof(Storage) !== "undefined") {
        localStorage.setItem('MICTOOLBOXAPPSTATE', jsonSting);
    } else {
        console.log('No Storage Found');
    }
};


MicAccessTool.prototype.openBox = function(event) {
    this.toolBox.classList.add('opened-mic-access-tool');
    if (!window.MICTOOLBOXAPPSTATE.initFontSize || window.MICTOOLBOXAPPSTATE.fontSize <= 1) {
        this.initFontsChangeFirst();
        window.MICTOOLBOXAPPSTATE.initFontSize = true;
    }
    this.toolBoxCloseButton.focus();
};

MicAccessTool.prototype.closeBox = function(event) {
    this.toolBox.classList.remove('opened-mic-access-tool');
};

MicAccessTool.prototype.openCloseBoxKeyboard = function(event) {
    if (event.keyCode == 27) {
        this.closeBox();
    }
    if (event.ctrlKey && event.keyCode == 113) {
        this.openBox();
    }
};

MicAccessTool.prototype.resetApp = function(event) {
    localStorage.removeItem('MICTOOLBOXAPPSTATE');
    window.location.reload();
};

MicAccessTool.prototype.initialApp = function() {
    window.MICTOOLBOXAPPSTATE = JSON.parse(localStorage.getItem('MICTOOLBOXAPPSTATE')) || {
        bodyClassList: {},
        fontSize: 1,
        imagesTitle: false,
        keyboardRoot: false,
        initFontSize: false
    };


    // INIT ADDING CLASSES TO BODY
    if (window.MICTOOLBOXAPPSTATE.bodyClassList) {
        for (var bodyClass in window.MICTOOLBOXAPPSTATE.bodyClassList) {
            var initBodyClassList = window.MICTOOLBOXAPPSTATE.bodyClassList[bodyClass];
            var enabledButton = document.getElementById(initBodyClassList);
            if (enabledButton) {
                enabledButton.classList.add('vi-enabled');
            }
            document.body.classList.add(initBodyClassList);
        }
    }

    // FONT SIZE INIT
    if (window.MICTOOLBOXAPPSTATE.fontSize > 1) {
        this.initFontsChange();
    }

    // SET IMAGES TITLES
    if (window.MICTOOLBOXAPPSTATE.imagesTitle) {
        this.imagesAddTitles();
    }

    // SET KEBOARD ROOTING
    if (window.MICTOOLBOXAPPSTATE.keyboardRoot) {
        this.keyboardRootEnable();
    }

    var isIE11 = !!window.MSInputMethodContext && !!document.documentMode;
    if (isIE11) {
        var contrastBlock = document.getElementById('mic-toolbox-contrast-block');
        contrastBlock.style.display = 'none';
    }
    if (this.init.link) {
        var initLink = document.getElementById('mic-toolbox-link-nagishut') || {};
        initLink.classList.remove('atb-hide-if-empty');
        initLink.href = this.init.link;
    }
    if (this.init.contact) {
        var initContact = document.getElementById('mic-toolbox-link-contact') || {};
        initContact.classList.remove('atb-hide-if-empty');
        initContact.href = this.init.contact;
    }
    if (this.init.buttonPosition === 'right') {
        document.getElementById('mic-access-tool-general-button').classList.add('mic-access-tool-general-button-right');
        document.getElementById('mic-access-tool-box').classList.add('mic-access-tool-box-right');
    }
};
