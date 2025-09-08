(function (T, L) {
    typeof exports == "object" && typeof module < "u" ? module.exports = L() : typeof define == "function" && define.amd ? define(L) : (T = typeof globalThis < "u" ? globalThis : T || self, T.GrapesJsPlugins_flexComponent = L())
})(this, function () {
    "use strict";
    const T = "app.grapesjs.com", L = "app-stage.grapesjs.com",
        oe = [T, L, "localhost", "127.0.0.1", ".local-credentialless.webcontainer.io", ".local.webcontainer.io", "-sandpack.codesandbox.io"],
        ye = "license:check:start", be = "license:check:end", Ce = () => typeof window < "u",
        Pe = ({isDev: n, isStage: e}) => `${n ? "" : `https://${e ? L : T}`}/api`, ze = () => {
            const n = Ce() && window.location.hostname;
            return !!n && (oe.includes(n) || oe.some(e => n.endsWith(e)))
        };

    function xe(n) {
        return typeof n == "function"
    }

    async function we({path: n, baseApiUrl: e, method: t = "GET", headers: s = {}, params: i, body: a}) {
        const l = `${e || Pe({isDev: !1, isStage: !1})}${n}`,
            c = {method: t, headers: {"Content-Type": "application/json", ...s}};
        a && (c.body = JSON.stringify(a));
        const f = i ? new URLSearchParams(i).toString() : "", p = f ? `?${f}` : "", u = await fetch(`${l}${p}`, c);
        if (!u.ok) throw new Error(`HTTP error! status: ${u.status}`);
        return u.json()
    }

    var $ = (n => (n.free = "free", n.startup = "startup", n.business = "business", n.enterprise = "enterprise", n))($ || {}),
        re = (n => (n.web = "web", n.email = "email", n.document = "document", n))(re || {});
    const ce = {[$.free]: 0, [$.startup]: 10, [$.business]: 20, [$.enterprise]: 30};

    function ve(n) {
        const e = n;
        return e.init = t => s => n(s, t), e
    }

    const Ie = n => ve(n);

    async function Re({editor: n, plan: e, pluginName: t, licenseKey: s, cleanup: i}) {
        let a = "", o = !1;
        const l = ze(), c = p => {
            console.warn("Cleanup plugin:", t, "Reason:", p), i()
        }, f = (p = {}) => {
            var y;
            const {error: u, sdkLicense: g} = p, v = (y = p.plan) == null ? void 0 : y.category;
            if (!(g || p.license) || u) c(u || "Invalid license"); else if (v) {
                const I = ce[e], R = ce[v];
                I > R && c({pluginRequiredPlan: e, licensePlan: v})
            }
        };
        n.on(ye, p => {
            a = p == null ? void 0 : p.baseApiUrl, o = !0
        }), n.on(be, p => {
            f(p)
        })
    }

    async function Ee(n) {
        const {licenseKey: e, pluginName: t, baseApiUrl: s} = n;
        try {
            return (await we({
                baseApiUrl: s,
                path: `/sdk/${e || "na"}`,
                method: "POST",
                params: {d: window.location.hostname, pn: t}
            })).result || {}
        } catch (i) {
            return console.error("Error during SDK license check:", i), !1
        }
    }

    const le = n => e => {
            var t;
            return ((t = e.getAttribute) == null ? void 0 : t.call(e, J)) === n
        }, Ge = (...n) => e => n.some(t => e.is(t)), Te = (...n) => (e, t) => n.some(s => t.is(s)), pe = "gjs-plg-",
        J = "data-type-role", B = class B {
            constructor(e) {
                this.config = e
            }

            getSize(e) {
                var t;
                return (t = this.config) != null && t.getSize ? this.config.getSize(e) : ne(e.componentColumn, B.CSS_FLEX_BASIS)
            }

            setSize(e) {
                var t;
                if ((t = this.config) != null && t.setSize) this.config.setSize(e); else {
                    const {componentColumn: s, sizeValue: i, partial: a} = e;
                    s.addStyle({[B.CSS_FLEX_BASIS]: i}, {partial: a})
                }
            }
        };
    B.CSS_FLEX_BASIS = "flex-basis";
    let q = B;
    const D = class D {
        constructor(e) {
            this.config = e
        }

        isGapSupported() {
            return !0
        }

        getGap(e) {
            var t;
            return (t = this.config) != null && t.getGap ? this.config.getGap(e) : ne(e.componentRow, D.CSS_GAP)
        }

        setGap(e) {
            var t;
            if ((t = this.config) != null && t.setGap) this.config.setGap(e); else {
                const {componentRow: s, gapValue: i, partial: a} = e;
                s.addStyle({[D.CSS_GAP]: i}, {partial: a})
            }
        }
    };
    D.CSS_GAP = "gap";
    let Z = D;
    const U = class U {
        constructor(e) {
            this.config = e
        }

        getParentSize(e) {
            var a;
            if ((a = this.config) != null && a.getParentSize) return this.config.getParentSize(e);
            const {componentRow: t, isVertical: s} = e, i = t.getEl();
            return i ? s ? i.clientHeight : i.clientWidth : 0
        }

        isLayoutVertical(e) {
            var s;
            if ((s = this.config) != null && s.isParentVertical) return this.config.isParentVertical(e);
            const t = ne(e.componentRow, U.CSS_FLEX_DIRECTION, !0);
            return t === "column" || t === "column-reverse"
        }
    };
    U.CSS_FLEX_DIRECTION = "flex-direction";
    let Q = U;
    const k = class k {
        constructor(e) {
            this.config = e
        }

        getSize(e) {
            var s;
            if ((s = this.config) != null && s.getSize) return this.config.getSize(e);
            const t = e.componentColumn;
            if (t.is(k.MJML_COLUMN_TYPE)) {
                const a = t.getAttributes().width;
                if (a && (a.includes("%") || !isNaN(parseFloat(a)))) return parseFloat(a);
                const o = t.parent();
                return 100 / (o ? o.components().length : 1)
            }
            return 0
        }

        setSize(e) {
            var t;
            if ((t = this.config) != null && t.setSize) this.config.setSize(e); else {
                const {componentColumn: s, sizeValue: i, partial: a} = e;
                s.is(k.MJML_COLUMN_TYPE) && s.addAttributes({width: `${parseFloat(i)}%`}, {partial: a})
            }
        }
    };
    k.MJML_COLUMN_TYPE = "mj-column";
    let ee = k;
    const W = class W {
        constructor(e) {
            this.config = e
        }

        isGapSupported() {
            return !1
        }

        getGap(e) {
            var s;
            if ((s = this.config) != null && s.getGap) return this.config.getGap(e);
            const {componentRow: t} = e;
            if (t.is(W.MJML_SECTION_TYPE)) {
                const i = t.getAttributes();
                if (i && i.padding) return parseInt(i.padding, 10) || 0
            }
            return 0
        }

        setGap(e) {
            var t, s;
            (s = (t = this.config) == null ? void 0 : t.setGap) == null || s.call(t, e)
        }
    };
    W.MJML_SECTION_TYPE = "mj-section";
    let te = W;

    class Le {
        constructor(e) {
            this.config = e
        }

        getParentSize(e) {
            var o;
            if ((o = this.config) != null && o.getParentSize) return this.config.getParentSize(e);
            const {componentRow: t, isVertical: s} = e, i = t.getEl();
            if (!i) return 0;
            let a = i;
            if (i.tagName !== "TABLE") {
                const l = i.querySelectorAll("table");
                l.length && (a = l[0])
            }
            return s ? a.clientHeight : a.clientWidth
        }

        isLayoutVertical(e) {
            var t;
            return (t = this.config) != null && t.isParentVertical ? this.config.isParentVertical(e) : !1
        }
    }

    class A {
        constructor() {
            this.handlerCache = new Map
        }

        static getInstance() {
            return A.instance || (A.instance = new A), A.instance
        }

        getHandlers(e) {
            const t = `${e.projectType}-${e.disableGapHandler ? "nogap" : "gap"}`;
            return this.handlerCache.has(t) || (H(e) ? this.handlerCache.set(t, {
                sizeHandler: new ee(e),
                gapHandler: new te(e),
                parentSizeHandler: new Le(e)
            }) : this.handlerCache.set(t, {
                sizeHandler: new q(e),
                gapHandler: new Z(e),
                parentSizeHandler: new Q(e)
            })), this.handlerCache.get(t)
        }
    }

    function G(n) {
        return A.getInstance().getHandlers(n)
    }

    const de = n => {
            n.style.display = "none"
        }, ue = n => {
            n.style.display = "block"
        }, N = n => +parseFloat(`${n}`).toFixed(2), H = n => n.projectType === re.email, O = (n, e) => {
            const t = n.Canvas.getFramesEl();
            t && (t.style.pointerEvents = e ? "none" : "")
        }, $e = n => n ? ["top", "bottom"] : ["left", "right"], Ae = n => {
            const e = n.parent();
            return e ? e.components().models.indexOf(n) === 0 : !1
        }, He = n => {
            const e = n.parent();
            if (!e) return !1;
            const t = e.components().models;
            return t.indexOf(n) === t.length - 1
        }, Ve = n => {
            const e = n.parent();
            if (!e) return !1;
            const t = e.components().models;
            return t.length > 1 && n.index() === t.length - 1
        }, Me = (n, e, t, s) => {
            const a = n + (t ? e === "top" ? -1 : e === "bottom" ? 1 : 0 : e === "left" ? -1 : e === "right" ? 1 : 0);
            return a >= 0 && a < s ? a : -1
        }, X = n => n === "top" || n === "bottom",
        Ne = (n, e) => !!(Ae(n) && (e === "left" || e === "top") || He(n) && (e === "right" || e === "bottom")),
        je = n => {
            const e = n.parent();
            return e ? e.components().models.length > 1 : !1
        }, he = (n, e) => n === "right" || n === "bottom" ? e > 0 : e < 0, ne = (n, e, t = !1) => {
            const s = !t, i = n.getEl();
            if (!i) return s ? 0 : "";
            const o = window.getComputedStyle(i)[e] || "";
            if (s) {
                let l;
                return typeof o == "string" ? (l = parseFloat(o.replace(/[^-\d.]/g, "")), isNaN(l) && (l = 0)) : l = Number(o) || 0, l
            }
            return o
        }, fe = (n, e, t) => {
            const s = se({componentRow: n, isVertical: e}, t), i = n.components().models;
            return s * (i.length - 1)
        }, j = (n, e) => G(e).parentSizeHandler.isLayoutVertical({componentRow: n}),
        _ = (n, e) => G(e).sizeHandler.getSize(n), se = (n, e) => {
            if (e != null && e.disableGapHandler) return 0;
            const {gapHandler: t, parentSizeHandler: s} = G(e);
            if (!t.isGapSupported()) return 0;
            const i = t.getGap(n), a = s.getParentSize(n);
            return i / 100 * a
        }, ie = (n, e) => G(e).parentSizeHandler.getParentSize(n), F = (n, e) => {
            G(e).sizeHandler.setSize(n)
        }, ge = (n, e) => {
            const t = _(n, e);
            F({...n, sizeValue: `${t}%`, partial: !1}, e)
        }, me = (n, e) => {
            if (e != null && e.disableGapHandler) return;
            const {gapHandler: t} = G(e);
            t.isGapSupported() && t.setGap(n)
        }, _e = (n, e) => {
            const t = n.getStyle().gap;
            if (!t) return;
            const s = n.components().models, i = {componentRow: n, isVertical: j(n, e)};
            s.map(a => ge({...i, componentColumn: a}, e)), me({...i, gapValue: String(t), partial: !1}, e)
        };

    function Fe(n, e) {
        const {Blocks: t} = n, {typeColumn: s, typeRow: i, blocks: a} = e;
        if (a === !1 || H(e)) return;
        const o = u => ({type: i, components: u.map(g => ({type: s, style: {"flex-basis": `${g}%`}}))}), l = u => `<div class="gs-block-item__flex-row" style="display: flex; height: 1.75rem; width: 100%; flex-wrap: nowrap; gap: 0.5rem;">
      ${u.map(g => `<div style="flex-basis: ${g}%; border-color: currentColor; border-width: 2px; border-radius: 0.12rem;"></div>`).join("")}
    </div>`, c = (u, g) => ({
                id: `flex-row-${g.join("-")}`,
                label: u,
                category: "Layout",
                select: !0,
                full: !0,
                attributes: {class: "gs-block-item__flex gs-utl-w-full"},
                media: l(g),
                content: o(g)
            }),
            f = [c("1 Column", [100]), c("2 Columns 50/50", [50, 50]), c("2 Columns 25/75", [25, 75]), c("2 Columns 75/25", [75, 25]), c("3 Columns", [33.33, 33.33, 33.33]), c("3 Columns 50/25/25", [50, 25, 25]), c("3 Columns 25/50/25", [25, 50, 25]), c("3 Columns 25/25/50", [25, 25, 50]), c("4 Columns", [25, 25, 25, 25]), c("5 Columns", [20, 20, 20, 20, 20])],
            p = xe(a) ? a({blocks: f}) : f;
        return p.forEach(u => t.add(u.id, u)), () => {
            p.forEach(u => t.remove(u.id))
        }
    }

    const Be = (n, e) => {
        const {Components: t} = n, {typeRow: s, typeColumn: i, extendTypeColumn: a} = e, o = `${pe}${i}`,
            c = !!i && !!t.getType(i) && !a;
        if (!(!i || c || H(e))) return t.addType(i, {
            isComponent: le(i), model: {
                defaults: {
                    name: "Column",
                    resizable: !1,
                    emptyState: !0,
                    classes: o,
                    icon: '<svg viewBox="0 0 24 24"><path d="M14.5 2.3A1.8 1.8 0 0 0 12.7 4v16c0 1 .8 1.8 1.8 1.8h3a1.8 1.8 0 0 0 1.8-1.8V4a1.8 1.8 0 0 0-1.8-1.8zm-8 0A1.8 1.8 0 0 0 4.7 4v16c0 1 .8 1.8 1.8 1.8h3a1.8 1.8 0 0 0 1.8-1.8V4a1.8 1.8 0 0 0-1.8-1.8z"/></svg>',
                    draggable: Te(s),
                    attributes: {[J]: i},
                    styles: `
          .${o} {
            flex-grow: 1;
          }
        `
                }
            }
        }), () => {
            t.removeType(i)
        }
    }, De = (n, e) => {
        const {Components: t} = n, {typeRow: s, typeColumn: i, extendTypeRow: a} = e, o = `${pe}${s}`,
            c = !!s && !!t.getType(s) && !a;
        if (!(!s || c || H(e))) return t.addType(s, {
            isComponent: le(s), model: {
                defaults: {
                    name: "Row",
                    classes: o,
                    icon: '<svg viewBox="0 0 24 24"><path d="M4 4.8a1.8 1.8 0 0 0-1.8 1.7v3c0 1 .8 1.8 1.8 1.8h16a1.8 1.8 0 0 0 1.8-1.8v-3A1.8 1.8 0 0 0 20 4.7zm0 8a1.8 1.8 0 0 0-1.8 1.7v3c0 1 .8 1.8 1.8 1.8h16a1.8 1.8 0 0 0 1.8-1.8v-3a1.8 1.8 0 0 0-1.8-1.8z"/></svg>',
                    emptyState: {styleIn: "width: 100%"},
                    attributes: {[J]: s},
                    droppable: Ge(i),
                    traits: [{
                        type: "checkbox",
                        name: "snap",
                        label: "Enable Snap",
                        default: e.snapEnabled,
                        changeProp: !0
                    }, {
                        type: "number",
                        name: "snap-divisions",
                        label: "Snap Divisions",
                        min: 1,
                        max: 12,
                        step: 1,
                        default: e.snapDivisions,
                        changeProp: !0
                    }],
                    resizable: {tl: 0, tc: 0, tr: 0, cl: 0, bl: 0, br: 0},
                    styles: `
          .${o} {
            display: flex;
            align-items: stretch;
            flex-wrap: nowrap;
          }
        `
                }
            }
        }), () => {
            t.removeType(s)
        }
    }, ke = "flexComponent", Oe = $.startup;

    class Xe {
        constructor() {
            this.resizableChildTypes = new Set, this.gapAdjustableParentTypes = new Set, this.typeRelationships = new Map
        }

        registerResizableChild(e) {
            this.resizableChildTypes.add(e)
        }

        registerGapAdjustableParent(e) {
            this.gapAdjustableParentTypes.add(e)
        }

        registerTypeRelationship(e, t) {
            this.typeRelationships.has(e) || this.typeRelationships.set(e, new Set), this.typeRelationships.get(e).add(t), this.registerResizableChild(t), this.registerGapAdjustableParent(e)
        }

        isResizableChild(e) {
            return this.resizableChildTypes.has(e.get("type"))
        }

        isGapAdjustableParent(e) {
            return this.gapAdjustableParentTypes.has(e.get("type"))
        }

        isValidRelationship(e, t) {
            const s = e.get("type"), i = t.get("type");
            return this.typeRelationships.has(s) ? this.typeRelationships.get(s).has(i) : !1
        }
    }

    class Ye {
        constructor(e, t, s) {
            this.editor = e, this.registry = t, this.opts = s, this.resizeState = new WeakMap
        }

        getState(e) {
            return this.resizeState.has(e) || this.resizeState.set(e, {}), this.resizeState.get(e)
        }

        clearState(e) {
            return this.resizeState.delete(e)
        }

        startResize(e, t, s) {
            const {opts: i} = this, a = this.getState(e);
            a.direction = t, a.startX = s.clientX, a.startY = s.clientY, a.resizing = !0, a.lastSnappedPercent = void 0;
            const o = e.parent();
            if (!o || !this.registry.isValidRelationship(o, e)) return;
            a.snapEnabled = o.get("snap") ?? i.snapEnabled, a.snapDivisions = o.get("snap-divisions") ?? i.snapDivisions ?? 12;
            const l = o.components().models, c = l.findIndex(g => g.cid === e.cid), f = j(o, i),
                p = Me(c, t, f, l.length);
            p !== -1 && (a.adjacentIdx = p);
            const u = {componentColumn: e, componentRow: o, isVertical: f};
            if (a.startPercent = _(u, i), typeof a.adjacentIdx < "u") {
                const g = l[a.adjacentIdx];
                a.neighborStartPercent = _({...u, componentColumn: g}, i)
            }
        }

        updateResizeByDelta(e, t, s) {
            const i = this.getState(e), a = e.parent();
            !i.resizing || !a || (i.snapEnabled ? this.updateSnapResize(e, t, s) : this.updateContinuesResize(e, t, s))
        }

        finishResize(e) {
            const t = e.parent(), s = t == null ? void 0 : t.components().models,
                i = {componentRow: t, isVertical: !!t && j(t, this.opts)};
            s == null || s.forEach(a => ge({...i, componentColumn: a}, this.opts)), this.clearState(e)
        }

        updateContinuesResize(e, t, s) {
            const i = this.getState(e), a = e.parent(), {opts: o} = this;
            if (!a) return;
            const l = o.minItemPercent ?? 0, c = X(t), f = ie({componentRow: a, isVertical: c}, o),
                p = fe(a, c, this.opts), g = 100 - p / f * 100, v = s / (f - p) * g, {
                    startPercent: P = 0,
                    neighborStartPercent: y = 0
                } = i, I = {componentRow: a, isVertical: c, componentColumn: e, partial: !0},
                R = he(t, s) ? P + Math.abs(v) : P - Math.abs(v);
            if (typeof i.adjacentIdx < "u") {
                const z = a.components().models[i.adjacentIdx], x = P + y, b = x - l, r = Math.min(b, Math.max(l, R)),
                    d = x - r;
                F({...I, sizeValue: `${N(r)}%`}, o), F({...I, componentColumn: z, sizeValue: `${N(d)}%`}, o)
            } else {
                const z = Math.max(l, Math.min(g, R));
                F({...I, sizeValue: `${N(z)}%`}, o)
            }
        }

        updateSnapResize(e, t, s) {
            const i = this.getState(e), a = e.parent(), o = X(t), l = ie({componentRow: a, isVertical: o}, this.opts),
                c = fe(a, o, this.opts), f = l - c, p = s / f * 100, u = he(t, s), {
                    startPercent: g = 0,
                    snapDivisions: v = 12
                } = i, P = u ? g + Math.abs(p) : g - Math.abs(p), y = 100 / v, I = Math.floor(P / y) * y,
                R = Math.ceil(P / y) * y, z = Math.abs(R - P), x = Math.abs(P - I), b = y * .2, C = z < x ? R : I;
            if (C > 0 && C < 100 && (z < b || x < b) && i.lastSnappedPercent !== C) {
                i.lastSnappedPercent = C;
                let m = Math.abs(C - g) / 100 * f;
                (u && (t === "left" || t === "top") || !u && (t === "right" || t === "bottom")) && (m = -m), this.updateContinuesResize(e, t, m)
            } else z >= b && x >= b && (i.lastSnappedPercent = void 0)
        }
    }

    class Ue {
        constructor(e, t, s) {
            this.editor = e, this.registry = t, this.opts = s
        }

        updateGapByDelta(e, t, s, i) {
            if (!this.registry.isGapAdjustableParent(e)) return;
            const {opts: a} = this, o = Math.max(0, s + t), l = ie({componentRow: e, isVertical: i}, a),
                c = o / l * 100, f = e.components().models, p = f.length - 1, u = {componentRow: e, isVertical: i},
                g = f.reduce((b, C) => b + _({...u, componentColumn: C}, a), 0), P = 100 - c * p,
                y = a.minItemPercent || 5;
            if (P < y * f.length) return;
            const R = 100 - p * c, z = g - R, x = {componentRow: e, isVertical: i, partial: !0};
            f.map(b => {
                const C = _({...u, componentColumn: b}, a), r = C - C / g * z;
                F({...x, componentColumn: b, sizeValue: `${N(r)}%`}, a)
            }), me({...x, gapValue: `${N(c)}%`}, a)
        }

        finishGapAdjust(e) {
            _e(e, this.opts)
        }
    }

    class We {
        constructor(e, t, s) {
            this.editor = e, this.registry = t, this.opts = s, this.resizeHandler = new Ye(e, t, s), this.gapHandler = new Ue(e, t, s)
        }

        startResize(e, t, s) {
            this.resizeHandler.startResize(e, t, s)
        }

        updateResizeByDelta(e, t, s) {
            this.resizeHandler.updateResizeByDelta(e, t, s)
        }

        finishResize(e) {
            this.resizeHandler.finishResize(e)
        }

        updateGapByDelta(e, t, s, i) {
            this.gapHandler.updateGapByDelta(e, t, s, i)
        }

        finishGapAdjust(e) {
            this.gapHandler.finishGapAdjust(e)
        }

        canResize(e) {
            const t = e.parent();
            return t ? this.registry.isValidRelationship(t, e) : !1
        }

        canAdjustGap(e) {
            return this.registry.isGapAdjustableParent(e)
        }
    }

    const Y = "gs-flex-spots", Se = `${Y}__handle-size`, Ke = `${Y}__handle-gap`;

    function Je(n, e) {
        const {Canvas: t} = n, s = "flex-resize-spot";
        let i, a, o, l, c = null, f = null;
        const p = new Xe, u = new We(n, p, e), g = () => {
            var r;
            i = document.createElement("div"), i.className = Y, i.style.display = "none", a = document.createElement("div"), a.className = `${Y}__handles`, a.style.position = "absolute", a.style.pointerEvents = "none", a.style.zIndex = "21", o = {
                left: document.createElement("div"),
                right: document.createElement("div"),
                top: document.createElement("div"),
                bottom: document.createElement("div")
            }, Object.entries(o).forEach(([d, m]) => {
                const S = d, h = X(S);
                m.className = `${Se} ${Se}-${S} gjs-resizer-h gjs-cv-unscale`, m.style.cssText = `
        pointer-events: all;
        position: absolute;
        z-index: 1;
        cursor: ${h ? "ns-resize" : "ew-resize"};
        ${h ? "left: 50%;" : "top: 50%;"}
        ${S === "left" ? "left: 0px;" : ""}
        ${S === "right" ? "right: 0px;" : ""}
        ${S === "top" ? "top: 0px;" : ""}
        ${S === "bottom" ? "bottom: 0px;" : ""}
      `, m.addEventListener("pointerdown", v(S)), a.appendChild(m)
            }), !e.disableGapHandler && !H(e) && (l = document.createElement("div"), l.className = Ke, l.style.cssText = `
        position: absolute;
        background-color: var(--gs-theme-cl-cmp-bg1, #3b97e3);
        border-width: 2px;
        border-radius: 9999px;
        border-color: #fff;
        box-sizing: content-box;
        pointer-events: all;
        max-width: 3rem;
        max-height: 3rem;
      `, l.addEventListener("pointerdown", P()), a.appendChild(l)), i.append(a), (r = t.getSpotsEl()) == null || r.appendChild(i)
        }, v = r => d => {
            if (!c) return;
            O(n, !0), d.stopPropagation(), d.preventDefault(), u.startResize(c, r, d);
            const m = d.clientX, S = d.clientY;
            d.target.setPointerCapture(d.pointerId);
            const h = V => {
                const E = n.Canvas.getZoomMultiplier(), M = (V.clientX - m) * E, K = (V.clientY - S) * E,
                    ae = X(r) ? K : M;
                u.updateResizeByDelta(c, r, ae)
            }, w = () => {
                O(n, !1), u.finishResize(c), d.target.releasePointerCapture(d.pointerId), document.removeEventListener("pointermove", h), document.removeEventListener("pointerup", w)
            };
            document.addEventListener("pointermove", h), document.addEventListener("pointerup", w)
        }, P = () => r => {
            if (!f) return;
            const d = f;
            O(n, !0), r.stopPropagation(), r.preventDefault();
            const m = j(d, e), S = se({componentRow: d, isVertical: m}, e), h = r.clientX, w = r.clientY;
            r.target.setPointerCapture(r.pointerId);
            const V = M => {
                const K = n.Canvas.getZoomMultiplier(), ae = m ? (M.clientY - w) * K : (M.clientX - h) * K;
                u.updateGapByDelta(d, ae, S, m)
            }, E = () => {
                O(n, !1), u.finishGapAdjust(d), r.target.releasePointerCapture(r.pointerId), document.removeEventListener("pointermove", V), document.removeEventListener("pointerup", E)
            };
            document.addEventListener("pointermove", V), document.addEventListener("pointerup", E)
        }, y = r => {
            const d = r.component;
            if (!d || !i || (c = d, f = d.parent(), !f)) return;
            const m = f;
            ue(i);
            const S = r.getStyle();
            a && Object.assign(a.style, S);
            const h = j(m, e), w = $e(h);
            if (Object.values(o).forEach(de), w.forEach(E => {
                if (Ne(d, E)) return;
                const M = o[E];
                ue(M)
            }), G(e).gapHandler.isGapSupported() && l && je(d)) {
                const E = se({componentRow: m, isVertical: h}, e);
                I(E, h, Ve(d))
            }
        }, I = (r, d, m = !1) => {
            const S = e.gapHandleSize, h = l.style;
            if (d) {
                h.height = `${S}px`;
                const w = l.offsetHeight;
                m ? (h.top = `-${(r + w) / 2}px`, h.bottom = "") : (h.bottom = `-${(r + w) / 2}px`, h.top = ""), h.width = "50%", h.left = "50%", h.transform = "translateX(-50%)", h.cursor = "row-resize"
            } else {
                h.width = `${S}px`;
                const w = l.offsetWidth;
                m ? (h.left = `-${(r + w) / 2}px`, h.right = "") : (h.right = `-${(r + w) / 2}px`, h.left = ""), h.height = "50%", h.top = "50%", h.transform = "translateY(-50%)", h.cursor = "col-resize"
            }
        }, R = () => {
            i && (de(i), c = null, f = null)
        }, z = () => {
            t.removeSpots({type: s});
            const r = n.getSelected(), d = r == null ? void 0 : r.parent();
            r && d && p.isValidRelationship(d, r) ? t.addSpot({type: s, component: r}) : R()
        }, x = () => {
            const r = t.getSpots().filter(d => d.type === s)[0];
            r && y(r)
        }, b = (r, d) => {
            p.registerTypeRelationship(r, d)
        };
        n.onReady(() => {
            g(), H(e) ? b("mj-section", "mj-column") : b(e.typeRow, e.typeColumn)
        });
        const C = "component:toggled";
        return n.on(t.events.spot, x), n.on(C, z), () => {
            n.off(t.events.spot, x), n.off(C, z)
        }
    }

    return Ie(function (n, e = {}) {
        const t = {
            typeRow: "flex-row",
            typeColumn: "flex-column",
            gapHandleSize: 3,
            snapDivisions: 12,
            minItemPercent: 5, ...e
        }, s = [De(n, t), Be(n, t)], i = Je(n, t), a = Fe(n, t);
        Re({
            editor: n, licenseKey: t.licenseKey, plan: Oe, pluginName: ke, cleanup: () => {
                s.forEach(o => o == null ? void 0 : o()), i(), a == null || a()
            }
        })
    })
});
