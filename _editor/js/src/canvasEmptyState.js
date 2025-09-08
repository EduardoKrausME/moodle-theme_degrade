(function (y, v) {
    typeof exports == "object" && typeof module < "u" ? module.exports = v() : typeof define == "function" && define.amd ? define(v) : (y = typeof globalThis < "u" ? globalThis : y || self, y.GrapesJsPlugins_canvasEmptyState = v())
})(this, function () {
    "use strict";
    const y = "app.grapesjs.com", v = "app-stage.grapesjs.com",
        W = [y, v, "localhost", "127.0.0.1", ".local-credentialless.webcontainer.io", ".local.webcontainer.io", "-sandpack.codesandbox.io"],
        P = "license:check:start", M = "license:check:end", $ = () => typeof window < "u",
        _ = ({isDev: e, isStage: t}) => `${e ? "" : `https://${t ? v : y}`}/api`, D = () => {
            const e = $() && window.location.hostname;
            return !!e && (W.includes(e) || W.some(t => e.endsWith(t)))
        };

    function R(e) {
        return typeof e == "function"
    }

    async function K({path: e, baseApiUrl: t, method: o = "GET", headers: a = {}, params: i, body: u}) {
        const w = `${t || _({isDev: !1, isStage: !1})}${e}`,
            p = {method: o, headers: {"Content-Type": "application/json", ...a}};
        u && (p.body = JSON.stringify(u));
        const d = i ? new URLSearchParams(i).toString() : "", s = d ? `?${d}` : "", c = await fetch(`${w}${s}`, p);
        if (!c.ok) throw new Error(`HTTP error! status: ${c.status}`);
        return c.json()
    }

    var g = (e => (e.free = "free", e.startup = "startup", e.business = "business", e.enterprise = "enterprise", e))(g || {});
    const I = {[g.free]: 0, [g.startup]: 10, [g.business]: 20, [g.enterprise]: 30};

    function j(e) {
        const t = e;
        return t.init = o => a => e(a, o), t
    }

    const x = e => j(e);

    async function O({editor: e, plan: t, pluginName: o, licenseKey: a, cleanup: i}) {
        let u = "", h = !1;
        const w = D(), p = s => {
            console.warn("Cleanup plugin:", o, "Reason:", s), i()
        }, d = (s = {}) => {
            var S;
            const {error: c, sdkLicense: T} = s, C = (S = s.plan) == null ? void 0 : S.category;
            if (!(T || s.license) || c) p(c || "Invalid license"); else if (C) {
                const L = I[t], b = I[C];
                L > b && p({pluginRequiredPlan: t, licensePlan: C})
            }
        };
        e.on(P, s => {
            u = s == null ? void 0 : s.baseApiUrl, h = !0
        }), e.on(M, s => {
            d(s)
        })
    }

    async function G(e) {
        const {licenseKey: t, pluginName: o, baseApiUrl: a} = e;
        try {
            return (await K({
                baseApiUrl: a,
                path: `/sdk/${t || "na"}`,
                method: "POST",
                params: {d: window.location.hostname, pn: o}
            })).result || {}
        } catch (i) {
            return console.error("Error during SDK license check:", i), !1
        }
    }

    const F = "canvasEmptyState", H = g.startup;
    return x(function (e, t = {}) {
        const o = new WeakMap, a = new WeakMap, i = new WeakMap, u = new Set, h = new WeakMap,
            w = {emptyStates: [], ...t}, p = (n, r) => {
                let m = !1;
                const {isValid: f} = r;
                return Array.isArray(f) ? m = f.some(l => n.is(l)) : R(f) ? m = f({
                    component: n,
                    editor: e
                }) : m = n.is(f), m
            }, d = n => {
                const r = o.get(n);
                o.delete(n), r == null || r()
            }, s = n => {
                n.views.forEach(r => d(r)), i.delete(n)
            }, c = n => {
                if (!(!n || u.has(n))) try {
                    u.add(n);
                    const r = n.components().length > 0, m = i.get(n);
                    if (r && m) s(n); else if (!r && !m) {
                        const f = h.has(n) ? h.get(n) : w.emptyStates.find(l => p(n, l));
                        if (h.set(n, f), !f) return;
                        n.views.forEach(l => {
                            const U = f.render({
                                editor: e, component: n, componentView: l, mount: E => {
                                    a.set(l, E), i.set(n, !0);
                                    const A = l.getChildrenContainer();
                                    A == null || A.appendChild(E)
                                }, unmount: () => d(l)
                            });
                            o.set(l, () => {
                                U == null || U();
                                const E = a.get(l);
                                E == null || E.remove()
                            })
                        })
                    }
                } finally {
                    u.delete(n)
                }
            }, T = n => {
                i.has(n) && s(n)
            }, C = ({model: n}) => {
                c(n.getComponent())
            }, k = e.Components.events, S = `${k.update}:components`, L = "component:mount",
            b = e.Canvas.events.frameLoadBody, N = k.removed;
        e.on(S, c), e.on(L, c), e.on(N, T), e.on(b, C), O({
            editor: e,
            licenseKey: w.licenseKey,
            plan: H,
            pluginName: F,
            cleanup: () => {
                e.off(S, c), e.off(L, c), e.off(N, T), e.off(b, C)
            }
        })
    })
});
