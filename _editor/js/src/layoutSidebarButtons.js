(function (h, v) {
    typeof exports == "object" && typeof module < "u" ? module.exports = v() : typeof define == "function" && define.amd ? define(v) : (h = typeof globalThis < "u" ? globalThis : h || self, h.GrapesJsPlugins_layoutSidebarButtons = v())
})(this, function () {
    "use strict";
    var h = (e => (e.free = "free", e.startup = "startup", e.business = "business", e.enterprise = "enterprise", e))(h || {}),
        v = (e => (e.toastAdd = "studio:toastAdd", e.toastRemove = "studio:toastRemove", e.dialogOpen = "studio:dialogOpen", e.dialogClose = "studio:dialogClose", e.sidebarLeftSet = "studio:sidebarLeft:set", e.sidebarLeftGet = "studio:sidebarLeft:get", e.sidebarLeftToggle = "studio:sidebarLeft:toggle", e.sidebarRightSet = "studio:sidebarRight:set", e.sidebarRightGet = "studio:sidebarRight:get", e.sidebarRightToggle = "studio:sidebarRight:toggle", e.sidebarTopSet = "studio:sidebarTop:set", e.sidebarTopGet = "studio:sidebarTop:get", e.sidebarTopToggle = "studio:sidebarTop:toggle", e.sidebarBottomSet = "studio:sidebarBottom:set", e.sidebarBottomGet = "studio:sidebarBottom:get", e.sidebarBottomToggle = "studio:sidebarBottom:toggle", e.symbolAdd = "studio:symbolAdd", e.symbolDetach = "studio:symbolDetach", e.symbolOverride = "studio:symbolOverride", e.symbolPropagateStyles = "studio:propagateStyles", e.getPagesConfig = "studio:getPagesConfig", e.setPagesConfig = "studio:setPagesConfig", e.getPageSettings = "studio:getPageSettings", e.setPageSettings = "studio:setPageSettings", e.projectFiles = "studio:projectFiles", e.canvasReload = "studio:canvasReload", e.getBlocksPanel = "studio:getBlocksPanel", e.setBlocksPanel = "studio:setBlocksPanel", e.getStateContextMenu = "studio:getStateContextMenu", e.setStateContextMenu = "studio:setStateContextMenu", e.contextMenuComponent = "studio:contextMenuComponent", e.layoutAdd = "studio:layoutAdd", e.layoutRemove = "studio:layoutRemove", e.layoutToggle = "studio:layoutToggle", e.layoutUpdate = "studio:layoutUpdate", e.layoutGet = "studio:layoutGet", e.layoutConfigGet = "studio:layoutConfigGet", e.layoutConfigSet = "studio:layoutConfigSet", e.getStateTheme = "studio:getStateTheme", e.setStateTheme = "studio:setStateTheme", e.assetProviderGet = "studio:assetProviderGet", e.assetProviderAdd = "studio:assetProviderAdd", e.assetProviderRemove = "studio:assetProviderRemove", e.fontGet = "studio:fontGet", e.fontAdd = "studio:fontAdd", e.fontRemove = "studio:fontRemove", e.fontManagerOpen = "studio:fontManagerOpen", e.menuFontLoad = "studio:menuFontLoad", e.toggleStateDataSource = "studio:toggleStateDataSource", e.getStateDataSource = "studio:getStateDataSource", e.dataSourceSetGlobalData = "studio:dataSourceSetGlobalData", e.dataSourceSetImporter = "studio:dataSourceSetImporter", e.dataSourceSetExporter = "studio:dataSourceSetExporter", e.setDragAbsolute = "studio:setDragAbsolute", e))(v || {}),
        A = (e => (e.layoutToggleId = "studio:layoutToggle:", e.toggleBlocksPanel = "studio:toggleBlocksPanel", e.pageSettingsUpdate = "studio:pageSettingsUpdate", e.toggleDataSourcesPreview = "studio:toggleDataSourcesPreview", e))(A || {});
    const k = "app.grapesjs.com", w = "app-stage.grapesjs.com",
        R = [k, w, "localhost", "127.0.0.1", ".local-credentialless.webcontainer.io", ".local.webcontainer.io", "-sandpack.codesandbox.io"],
        E = "license:check:start", O = "license:check:end", $ = () => typeof window < "u",
        _ = ({isDev: e, isStage: t}) => `${e ? "" : `https://${t ? w : k}`}/api`, H = () => {
            const e = $() && window.location.hostname;
            return !!e && (R.includes(e) || R.some(t => e.endsWith(t)))
        };

    async function N({path: e, baseApiUrl: t, method: n = "GET", headers: l = {}, params: c, body: b}) {
        const y = `${t || _({isDev: !1, isStage: !1})}${e}`,
            r = {method: n, headers: {"Content-Type": "application/json", ...l}};
        b && (r.body = JSON.stringify(b));
        const o = c ? new URLSearchParams(c).toString() : "", s = o ? `?${o}` : "", g = await fetch(`${y}${s}`, r);
        if (!g.ok) throw new Error(`HTTP error! status: ${g.status}`);
        return g.json()
    }

    const G = {[h.free]: 0, [h.startup]: 10, [h.business]: 20, [h.enterprise]: 30};

    function W(e) {
        const t = e;
        return t.init = n => l => e(l, n), t
    }

    const j = e => W(e);

    async function K({editor: e, plan: t, pluginName: n, licenseKey: l, cleanup: c}) {
        let b = "", f = !1;
        const y = H(), r = s => {
            console.warn("Cleanup plugin:", n, "Reason:", s), c()
        }, o = (s = {}) => {
            var i;
            const {error: g, sdkLicense: L} = s, a = (i = s.plan) == null ? void 0 : i.category;
            if (!(L || s.license) || g) r(g || "Invalid license"); else if (a) {
                const p = G[t], d = G[a];
                p > d && r({pluginRequiredPlan: t, licensePlan: a})
            }
        };
        e.on(E, s => {
            b = s == null ? void 0 : s.baseApiUrl, f = !0
        }), e.on(O, s => {
            o(s)
        })
    }

    async function V(e) {
        const {licenseKey: t, pluginName: n, baseApiUrl: l} = e;
        try {
            return (await N({
                baseApiUrl: l,
                path: `/sdk/${t || "na"}`,
                method: "POST",
                params: {d: window.location.hostname, pn: n}
            })).result || {}
        } catch (c) {
            return console.error("Error during SDK license check:", c), !1
        }
    }

    var T = (e => (e.panelBlocks = "panelBlocks", e.panelPagesLayers = "panelPagesLayers", e.panelGlobalStyles = "panelGlobalStyles", e.panelSidebarTabs = "panelSidebarTabs", e.panelAssets = "panelAssets", e.aiChatPanel = "aiChatPanel", e))(T || {});
    const M = "sidebarButtonsTarget", B = e => {
        const {
            id: t,
            icon: n,
            label: l,
            tooltip: c,
            className: b,
            skipSelfClose: f,
            removeLayouts: y,
            layoutComponent: r,
            layoutCommand: o
        } = e, s = t;
        return {
            id: D(t),
            type: "button",
            icon: n,
            tooltip: l || c,
            className: b,
            editorEvents: {
                [`${A.layoutToggleId}${s}`]: ({fromEvent: g, setState: L, editor: a}) => {
                    L({active: g.isOpen}), setTimeout(() => a.refresh({tools: !0}), 20)
                }
            },
            onClick: ({editor: g, state: L}) => {
                if (L.active && f) return;
                const a = (o == null ? void 0 : o.placer) || {type: "static", layoutId: M},
                    u = {...a, skipCleanup: a.type === "static"}, i = y || Object.keys(T).filter(d => d !== s);
                I(g, {removeLayouts: i, layout: {placer: u}});
                const p = {
                    id: s,
                    placer: a,
                    header: (o == null ? void 0 : o.header) ?? {label: l, close: !f},
                    layout: r,
                    style: {width: 280, height: "100%", borderRightWidth: 1, ...o == null ? void 0 : o.style}
                };
                g.runCommand(v.layoutToggle, p)
            }
        }
    }, D = e => `button__${e}`, I = (e, t = {}) => {
        (t.removeLayouts || Object.keys(T)).forEach(l => e.runCommand(v.layoutRemove, {id: l, layout: t.layout}))
    }, S = e => {
        const t = (e == null ? void 0 : e.breakpointTablet) ?? 1024,
            n = (e == null ? void 0 : e.breakpointMobile) ?? 768, l = {breakpointTablet: t, breakpointMobile: n},
            c = {}, b = [{
                id: T.panelBlocks,
                icon: "plusBox",
                label: "Blocks",
                layoutComponent: {type: "panelBlocks"}
            }, {
                id: T.panelPagesLayers,
                icon: "layers",
                label: "Pages & Layers",
                layoutComponent: {type: "panelPagesLayers"}
            }, {
                id: T.panelGlobalStyles,
                icon: '<svg viewBox="0 0 24 24"> <path d="M20 14H6c-2.2 0-4 1.8-4 4s1.8 4 4 4h14a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2M6 20c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2m.3-8L13 5.3a2 2 0 0 1 2.8 0l2.8 2.8c.8.8.8 2 0 2.8l-.9 1.1H6.3M2 13.5V4c0-1.1.9-2 2-2h4a2 2 0 0 1 2 2v1.5l-8 8Z"/></svg>',
                label: "Global Styles",
                layoutComponent: {type: "panelGlobalStyles"}
            }, {
                id: T.panelAssets,
                icon: '<svg viewBox="0 0 24 24"><path d="M22,16V4A2,2 0 0,0 20,2H8A2,2 0 0,0 6,4V16A2,2 0 0,0 8,18H20A2,2 0 0,0 22,16M11,12L13.03,14.71L16,11L20,16H8M2,6V20A2,2 0 0,0 4,22H18V20H4V6" /></svg>',
                label: "Assets",
                layoutComponent: {
                    type: "panelAssets",
                    content: {itemsPerRow: 2, header: {addUrl: !1}},
                    style: {padding: 7},
                    onSelect: ({assetProps: a, editor: u}) => {
                        var d;
                        const i = u.getSelected(), p = {type: "image", src: a.src};
                        if (i != null && i.is("image")) return i.set("src", a.src);
                        if (i && u.Components.canMove(i, p)) {
                            const P = i.append(p)[0];
                            P && u.select(P)
                        } else if (!i) {
                            const P = (d = u.getWrapper()) == null ? void 0 : d.append(p)[0];
                            P && u.select(P)
                        }
                    }
                }
            }], f = [...b, {
                id: T.panelSidebarTabs,
                layoutCommand: {header: !1},
                icon: '<svg viewBox="0 0 24 24"><path d="M17.5 12a1.5 1.5 0 0 1-1.5-1.5A1.5 1.5 0 0 1 17.5 9a1.5 1.5 0 0 1 1.5 1.5 1.5 1.5 0 0 1-1.5 1.5m-3-4A1.5 1.5 0 0 1 13 6.5 1.5 1.5 0 0 1 14.5 5 1.5 1.5 0 0 1 16 6.5 1.5 1.5 0 0 1 14.5 8m-5 0A1.5 1.5 0 0 1 8 6.5 1.5 1.5 0 0 1 9.5 5 1.5 1.5 0 0 1 11 6.5 1.5 1.5 0 0 1 9.5 8m-3 4A1.5 1.5 0 0 1 5 10.5 1.5 1.5 0 0 1 6.5 9 1.5 1.5 0 0 1 8 10.5 1.5 1.5 0 0 1 6.5 12M12 3a9 9 0 0 0-9 9 9 9 0 0 0 9 9 1.5 1.5 0 0 0 1.5-1.5c0-.4-.2-.7-.4-1-.2-.3-.4-.6-.4-1a1.5 1.5 0 0 1 1.5-1.5H16a5 5 0 0 0 5-5c0-4.4-4-8-9-8Z"/></svg>',
                label: "Styles & Props",
                layoutComponent: {type: "panelSidebarTabs"}
            }], y = {id: M, type: "column", style: {overflow: "hidden"}}, r = {
                type: "sidebarLeft",
                resizable: !1,
                style: {padding: "10px 5px", alignItems: "center", width: 45, gap: 10},
                children: []
            }, o = {type: "canvasSidebarTop", sidebarTop: {leftContainer: {buttons: []}}}, s = (a, u) => {
                const i = a.map(d => {
                    const P = B(d);
                    return e != null && e.sidebarButton ? e.sidebarButton({
                        id: d.id,
                        buttonIds: T,
                        breakpoint: u,
                        buttonProps: P,
                        sidebarButtonProps: d, ...l,
                        createSidebarButton: q => B({...d, ...q})
                    }) : P
                }).filter(Boolean);
                return (e != null && e.sidebarButtons ? e == null ? void 0 : e.sidebarButtons({
                    buttonIds: T,
                    breakpoint: u,
                    sidebarButtons: i, ...l,
                    createSidebarButton: d => B({...d})
                }) : i).filter(Boolean)
            }, g = (a, u, i) => {
                var p;
                return ((p = e == null ? void 0 : e.rootLayout) == null ? void 0 : p.call(e, {
                    breakpoint: i,
                    sidebarButtons: u,
                    rootLayout: a,
                    layoutSidebarLeft: r,
                    layoutSidebarTarget: y, ...l,
                    createSidebarButton: d => B({...d})
                })) ?? a
            };
        if (t) {
            const a = s(f, t);
            c[t] = g({type: "row", style: {height: "100%"}, children: [{...r, children: a}, y, o]}, a, t)
        }
        if (n) {
            const a = f.map(i => ({
                ...i,
                layoutCommand: {placer: {type: "absolute", position: "left"}, style: {height: "calc(100% - 40px)"}}
            })), u = s(a, n);
            c[n] = g({
                type: "column",
                style: {height: "100%"},
                children: [{type: "sidebarTop", leftContainer: {buttons: []}}, {
                    type: "canvas",
                    grow: !0
                }, {
                    type: "sidebarBottom",
                    style: {padding: "0 5px", alignItems: "center", gap: 10, minHeight: 39},
                    children: u
                }]
            }, u, n)
        }
        const L = s(b, 0);
        return {
            default: g({
                type: "row",
                style: {height: "100%"},
                children: [{...r, children: L}, y, o, {type: "sidebarRight"}]
            }, L, 0), responsive: c
        }
    }, x = "layoutSidebarButtons", F = h.free, U = j(function (e, t = {}) {
        const {Commands: n} = e, l = n.events, c = {...t};
        (() => {
            if (e.runCommand(v.layoutConfigGet) || c.skipLayoutConfig === !0) return;
            const y = S(c);
            e.runCommand(v.layoutConfigSet, {...y})
        })(), n.add(`${x}:toggleButton`, (f, y, r) => {
            const o = document.getElementById(D(r == null ? void 0 : r.id));
            o == null || o.click()
        }), e.on(`${l.runCommand}core:preview`, () => {
            I(e)
        }), K({
            editor: e, licenseKey: c.licenseKey, plan: F, pluginName: x, cleanup: () => {
                e.runCommand(v.layoutConfigSet)
            }
        })
    });
    return U.createLayoutConfig = e => S(e), U
});
