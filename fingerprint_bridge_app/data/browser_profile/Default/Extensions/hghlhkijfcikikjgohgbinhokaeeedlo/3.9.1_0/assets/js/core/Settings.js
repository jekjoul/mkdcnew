async function VGSettings() {
    if (!window.VG_SETTINGS_INIT_RUNNING && !(window.VG_SETTINGS_INITIALIZED && document.getElementById("tx-settings-btn") && document.getElementById("tx-skip-btn") && document.getElementById("tx-stop-btn"))) {
        window.VG_SETTINGS_INIT_RUNNING = !0;
        try {
            await VGSettingsCore(), window.VG_SETTINGS_INITIALIZED = !0
        } catch (t) {
            window.VG_SETTINGS_INITIALIZED = !1;
            try {
                console.warn("[ViewGrip] Settings UI initialization skipped:", t && (t.message || t))
            } catch (e) {}
            return !1
        } finally {
            window.VG_SETTINGS_INIT_RUNNING = !1
        }
    }
}
async function VGSettingsCore() {
    var t;
    let e = document.getElementById("tx-settings-style");
    e && e.remove();
    let a = document.createElement("style");
    a.id = "tx-settings-style", a.textContent = '#tx-skip-btn:hover,#tx-stop-btn:hover{transform:translateY(-4px) scale(1.02)}#tx-settings-btn,#tx-settings-popup{background:linear-gradient(180deg,rgba(34,34,34,.98),rgba(20,20,20,.98));font-family:"Segoe UI",Roboto,Arial,sans-serif}#tx-report-modal,#tx-report-text{box-sizing:border-box;color:#e6eef8}#tx-settings-btn{position:fixed;bottom:22px;right:22px;width:62px;height:62px;border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;cursor:pointer;z-index:2147483646;box-shadow:0 10px 30px rgba(0,0,0,.6),0 2px 6px rgba(0,0,0,.4);transform:perspective(600px) translateZ(0);transition:transform .15s,box-shadow .15s;user-select:none;-webkit-user-select:none;outline:0}#tx-skip-btn,#tx-stop-btn{border-radius:14px;display:flex;align-items:center;box-shadow:0 10px 30px rgba(0,0,0,.6),0 2px 6px rgba(0,0,0,.4);transition:transform .15s,box-shadow .15s;color:#fff;right:22px;width:62px;height:62px;font-weight:700;cursor:pointer;z-index:2147483646;position:fixed}#tx-settings-btn:hover{transform:perspective(600px) translateY(-4px) scale(1.02);box-shadow:0 20px 40px rgba(0,0,0,.7)}#tx-settings-btn:active{transform:translateY(0) scale(.99)}#tx-skip-btn{bottom:100px;background:linear-gradient(180deg,rgba(37,36,36,.98),rgba(44,43,43,.98));justify-content:center}#tx-skip-btn:active{transform:scale(.97)}#tx-stop-btn{bottom:178px;background:linear-gradient(180deg,rgba(180,0,0,.98),rgba(120,0,0,.98));justify-content:center}#tx-stop-btn:hover{box-shadow:0 20px 40px rgba(0,0,0,.7)}#tx-stop-btn:active{transform:scale(.95)}#tx-settings-popup{position:fixed;bottom:96px;right:22px;width:360px;max-width:calc(100vw - 44px);border-radius:18px;padding:18px;display:none;z-index:2147483647;color:#e6eef8;box-shadow:0 30px 60px rgba(2,6,23,.75),inset 0 1px 0 rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.03);transform-style:preserve-3d}#tx-header{display:flex;align-items:center;gap:10px;margin-bottom:12px}#tx-header h2{margin:0;color:#fff;font-size:18px;letter-spacing:.6px;text-shadow:0 2px 12px rgba(0,0,0,.7)}#tx-header .tx-badge{font-size:11px;padding:4px 8px;border-radius:999px;background:linear-gradient(90deg,#0ea5e9,#7c3aed);color:#fff;box-shadow:0 6px 18px rgba(124,58,237,.12),0 1px 0 rgba(255,255,255,.02)}#tx-user-card{background:linear-gradient(180deg,rgba(255,255,255,.02),rgba(255,255,255,.01));border-radius:12px;padding:12px;display:flex;gap:12px;align-items:center;margin-bottom:14px;border:1px solid rgba(255,255,255,.02)}#tx-quality,.tx-toggle-row{border-radius:10px;padding:10px}#tx-avatar{width:58px;height:58px;border-radius:12px;background:linear-gradient(135deg,#111,#222);box-shadow:0 8px 20px rgba(0,0,0,.7),inset 0 1px 0 rgba(255,255,255,.02);display:flex;align-items:center;justify-content:center;color:#9fb0d3;font-weight:700;font-size:20px}#tx-user-meta{flex:1;display:flex;flex-direction:column;gap:6px}.tx-meta-row{display:flex;justify-content:space-between;align-items:center;gap:8px}#tx-report-modal .hint,.tx-meta-title{font-size:12px;color:#a9b7c7}.tx-meta-value{font-size:14px;font-weight:700;color:#e6eef8;text-align:right;transition:opacity .28s;opacity:1}.tx-section-title{font-size:13px;color:#b9c7d6;margin:10px 2px}.tx-checkbox-group{display:flex;flex-direction:column;gap:14px;margin-bottom:12px;padding-right:4px}#tx-report-btn,.tx-toggle-row{display:flex;align-items:center}.tx-toggle-row{justify-content:space-between;gap:12px;background:linear-gradient(180deg,rgba(255,255,255,.01),transparent);border:1px solid rgba(255,255,255,.02);box-shadow:0 4px 18px rgba(0,0,0,.6);transform:translateZ(0)}.tx-toggle-left{display:flex;flex-direction:column;gap:4px}.tx-toggle-label{font-size:14px;color:#e6eef8;font-weight:700}.tx-toggle-desc{font-size:12px;color:#95a6b7}.tx-bonus{font-size:12px;padding:4px 8px;border-radius:999px;background:rgba(16,185,129,.12);color:#10b981;border:1px solid rgba(16,185,129,.12)}.tx-switch{--w:48px;--h:26px;width:var(--w);height:var(--h);border-radius:999px;background:linear-gradient(90deg,#ff6b6b,#ef4444);position:relative;transition:background .18s,box-shadow .18s;box-shadow:inset 0 -4px 12px rgba(0,0,0,.45),0 6px 16px rgba(0,0,0,.5);flex-shrink:0}.tx-switch .tx-knob{position:absolute;top:3px;left:3px;width:20px;height:20px;border-radius:50%;background:linear-gradient(180deg,#fff,#e6eef8);box-shadow:0 6px 12px rgba(0,0,0,.5),inset 0 1px 0 rgba(255,255,255,.6);transition:left .18s cubic-bezier(.2,.9,.2,1),transform .18s}.tx-switch.on{background:linear-gradient(90deg,#2563eb,#60a5fa)}.tx-switch.on .tx-knob{left:calc(100% - 23px);transform:translateX(0)}#tx-quality{width:100%;background:linear-gradient(180deg,#1955a3,#0f35eb);color:#00f5f5;border:1px solid rgba(255,255,255,.03);font-size:14px;appearance:none;-webkit-appearance:none;outline:0;box-shadow:0 8px 20px rgba(0,0,0,.6),inset 0 1px 0 rgba(255,255,255,.02)}#tx-login-btn{width:100%;padding:10px;border-radius:10px;background:linear-gradient(90deg,#1e3a8a,#7c3aed);color:#fff;border:none;font-weight:700;cursor:pointer;box-shadow:0 8px 24px rgba(0,0,0,.6)}#tx-global-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);display:none;z-index:2147483645}#tx-refresh-modal{position:fixed;left:50%;top:50%;transform:translate(-50%,-50%) scale(1);background:linear-gradient(180deg,#0b0c0e,#101214);border-radius:12px;padding:16px;width:360px;max-width:calc(100vw - 40px);box-shadow:0 30px 80px rgba(0,0,0,.8);display:none;z-index:2147483648;border:1px solid rgba(255,255,255,.03);color:#dbeafe}#tx-refresh-modal p{margin:8px 0 14px;color:#bcd1e9;font-size:14px}#tx-refresh-modal .tx-actions{display:flex;gap:10px;justify-content:flex-end}.btn-report,.tx-btn{padding:8px 12px;border-radius:8px;font-weight:700;cursor:pointer;border:none}.tx-btn.cancel{background:0 0;color:#b6c6db;border:1px solid rgba(255,255,255,.03)}.btn-report.send,.tx-btn.confirm{background:linear-gradient(90deg,#2563eb,#60a5fa);color:#fff}#tx-report-btn{position:fixed;bottom:256px;right:22px;width:62px;height:62px;border-radius:14px;background:linear-gradient(180deg,#ffd166,#f4c430);justify-content:center;color:#1a1a1a;font-weight:700;cursor:pointer;z-index:2147483646;box-shadow:0 10px 30px rgba(0,0,0,.25);transition:transform .12s,box-shadow .12s,opacity .12s}#tx-report-btn:hover{transform:translateY(-4px) scale(1.03);box-shadow:0 20px 40px rgba(0,0,0,.28)}#tx-report-btn:active{transform:translateY(0) scale(.98)}#tx-report-btn[aria-busy=true]{opacity:.6;pointer-events:none}#tx-report-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);display:none;z-index:2147483650;align-items:center;justify-content:center;padding:18px}#tx-report-overlay.show{display:flex}#tx-report-modal{width:520px;max-width:calc(100vw - 36px);background:linear-gradient(180deg,#0f1724,#071027);border-radius:12px;padding:16px;box-shadow:0 30px 80px rgba(2,6,23,.8);border:1px solid rgba(255,255,255,.04);display:flex;flex-direction:column;gap:12px;max-height:calc(100vh - 80px);overflow:hidden}#tx-report-modal h3{margin:0;font-size:16px;color:#fff}#tx-report-text{width:100%;min-width:0;padding:10px 12px;border-radius:8px;border:1px solid rgba(255,255,255,.06);background:linear-gradient(180deg,#061226,#07162a);font-size:14px;font-family:inherit;outline:0;resize:vertical;overflow:auto;flex:1 1 auto;min-height:110px;max-height:40vh;margin:0}#tx-report-modal .footer{display:flex;align-items:center;justify-content:space-between;gap:12px;flex:0 0 auto}#tx-report-modal .count{font-size:12px;color:#9fb0d3}.btn-report.cancel{background:0 0;color:#b6c6db;border:1px solid rgba(255,255,255,.04)}@media (max-width:480px){#tx-report-modal{width:100%;padding:12px}#tx-report-text{min-height:90px;max-height:35vh}}', document.head.appendChild(a), ["tx-settings-btn", "tx-skip-btn", "tx-stop-btn", "tx-settings-popup", "tx-global-overlay", "tx-refresh-modal"].forEach(t => {
        let e = document.getElementById(t);
        e && e.remove()
    });
    let n = document.createElement("div");
    n.id = "tx-settings-btn", n.setAttribute("title", await translateMessage("settings", "")), n.style.pointerEvents = "auto";
    let i = "http://www.w3.org/2000/svg",
        r = document.createElementNS(i, "svg");
    r.setAttribute("width", "26"), r.setAttribute("height", "26"), r.setAttribute("viewBox", "0 0 24 24"), r.setAttribute("fill", "none");
    let o = document.createElementNS(i, "path");
    o.setAttribute("d", "M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"), o.setAttribute("stroke", "white"), o.setAttribute("stroke-width", "1.25"), o.setAttribute("stroke-linecap", "round"), o.setAttribute("stroke-linejoin", "round"), r.appendChild(o);
    let l = document.createElementNS(i, "path");
    l.setAttribute("d", "M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06A2 2 0 1 1 2.3 16.9l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09c.7 0 1.27-.38 1.51-1A1.65 1.65 0 0 0 3.3 6.1L3.24 6A2 2 0 1 1 6.07 3.18l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09c0 .59.38 1.12 1 1.51h.06a1.65 1.65 0 0 0 1.82-.33l.06-.06A2 2 0 1 1 21.7 7.1l-.06.06a1.65 1.65 0 0 0-.33 1.82V10c.59 0 1.12.38 1.51 1h.09a2 2 0 1 1-2 4h-.09c-.7 0-1.27.38-1.51 1z"), l.setAttribute("stroke", "white"), l.setAttribute("stroke-width", "1"), l.setAttribute("stroke-linecap", "round"), l.setAttribute("stroke-linejoin", "round"), r.appendChild(l), n.appendChild(r), document.body.appendChild(n);
    let s = document.createElement("div");
    s.id = "tx-skip-btn", s.setAttribute("title", await translateMessage("skip_campaign", "")), s.style.pointerEvents = "auto";
    let d = document.createElementNS(i, "svg");
    d.setAttribute("width", "28"), d.setAttribute("height", "28"), d.setAttribute("viewBox", "0 0 24 24"), d.setAttribute("fill", "none");
    let p = document.createElementNS(i, "path");
    p.setAttribute("d", "M5 4l10 8-10 8V4zm11 0h2v16h-2V4z"), p.setAttribute("stroke", "white"), p.setAttribute("stroke-width", "1.6"), p.setAttribute("stroke-linecap", "round"), p.setAttribute("stroke-linejoin", "round"), d.appendChild(p), s.appendChild(d), s.onclick = () => {
        try {
            skipCurrentCampaign()
        } catch (t) {}
        s.style.pointerEvents = "none", s.style.opacity = "0.5"
    }, document.body.appendChild(s);
    let c = document.createElement("div");
    c.id = "tx-stop-btn", c.setAttribute("title", await translateMessage("stop", "")), c.style.pointerEvents = "auto";
    let x = document.createElementNS(i, "svg");
    x.setAttribute("width", "26"), x.setAttribute("height", "26"), x.setAttribute("viewBox", "0 0 24 24"), x.setAttribute("fill", "none");
    let $ = document.createElementNS(i, "path");
    if ($.setAttribute("d", "M6 6l12 12M18 6L6 18"), $.setAttribute("stroke", "white"), $.setAttribute("stroke-width", "2"), $.setAttribute("stroke-linecap", "round"), x.appendChild($), c.appendChild(x), c.onclick = () => {
            try {
                "undefined" != typeof chrome && chrome.runtime && "function" == typeof chrome.runtime.sendMessage && chrome.runtime.sendMessage({
                    cmd: "closeTab",
                    url: ""
                }, () => {
                    try {
                        chrome.runtime.lastError
                    } catch (t) {}
                })
            } catch (t) {}
        }, document.body.appendChild(c), !document.getElementById("tx-report-btn")) {
        let b = document.createElement("div");
        b.id = "tx-report-btn", b.setAttribute("title", await translateMessage("reporting_campaign", "")), b.style.pointerEvents = "auto", b.setAttribute("role", "button"), b.setAttribute("tabindex", "0");
        let u = document.createElementNS(i, "svg");
        u.setAttribute("width", "26"), u.setAttribute("height", "26"), u.setAttribute("viewBox", "0 0 24 24"), u.setAttribute("fill", "none");
        let g = document.createElementNS(i, "path");
        g.setAttribute("d", "M6 2v20"), g.setAttribute("stroke", "#1a1a1a"), g.setAttribute("stroke-width", "1.4"), g.setAttribute("stroke-linecap", "round"), u.appendChild(g);
        let h = document.createElementNS(i, "path");
        h.setAttribute("d", "M6 4c3-1 5 0 8 0s5-1 8 0v10c-3-1-5 0-8 0s-5-1-8 0V4z"), h.setAttribute("stroke", "#1a1a1a"), h.setAttribute("stroke-width", "1.2"), h.setAttribute("stroke-linecap", "round"), u.appendChild(h), b.appendChild(u), document.body.appendChild(b)
    }
    if (!document.getElementById("tx-report-overlay")) {
        let _ = document.createElement("div");
        _.id = "tx-report-overlay", _.setAttribute("role", "dialog"), _.setAttribute("aria-modal", "true"), _.style.pointerEvents = "auto";
        let m = document.createElement("div");
        m.id = "tx-report-modal";
        let f = document.createElement("h3");
        f.textContent = await translateMessage("reporting_campaign", "");
        let y = document.createElement("textarea");
        y.id = "tx-report-text", y.placeholder = await translateMessage("report_reason_prompt", ""), y.setAttribute("maxlength", "1000"), y.setAttribute("aria-label", "report_reason_brief");
        let w = document.createElement("div");
        w.className = "footer";
        let v = document.createElement("div");
        v.style.display = "flex", v.style.flexDirection = "column", v.style.gap = "6px";
        let E = document.createElement("div");
        E.className = "hint", E.textContent = await translateMessage("report_reason_brief", "");
        let k = document.createElement("div");
        k.className = "count", k.textContent = "0 / 1000", v.appendChild(E), v.appendChild(k);
        let C = document.createElement("div"),
            A = document.createElement("button");
        A.type = "button", A.className = "btn-report cancel", A.textContent = await translateMessage("cancel", "");
        let N = document.createElement("button");

        function I() {
            _.classList.remove("show"), document.documentElement.style.overflow = "", window.resumeVGCountdown();
            let t = vgQuerySelector("#tx-report-btn");
            t && (t.style.pointerEvents = "auto", t.style.opacity = "1")
        }
        N.type = "button", N.className = "btn-report send", N.textContent = await translateMessage("send", ""), C.appendChild(A), C.appendChild(N), w.appendChild(v), w.appendChild(C), m.appendChild(f), m.appendChild(y), m.appendChild(w), _.appendChild(m), document.body.appendChild(_), y.addEventListener("input", () => {
            k.textContent = `${y.value.length} / ${y.getAttribute("maxlength")}`
        }), A.addEventListener("click", t => {
            t.preventDefault(), I()
        }), _.addEventListener("click", t => {
            t.target === _ && I()
        }), y.addEventListener("keydown", t => {
            if ("Escape" === t.key) {
                t.preventDefault(), I();
                return
            }(t.ctrlKey || t.metaKey) && "Enter" === t.key && (t.preventDefault(), N.click())
        }), N.addEventListener("click", async t => {
            t.preventDefault();
            let e = (y.value || "").trim(),
                a = await translateMessage("min_char_required", "");
            if (e.length < 5) {
                try {
                    alert(a)
                } catch (n) {}
                y.focus();
                return
            }
            let i = await translateMessage("max_char_allowed", "");
            if (e.length > 1e3) {
                try {
                    alert(i)
                } catch (r) {}
                y.focus();
                return
            }
            N.disabled = !0, N.textContent = await translateMessage("processing", ""), A.disabled = !0;
            try {
                let o = "function" == typeof submitUserCampaignReport ? submitUserCampaignReport : null,
                    l = o(e);
                l && "function" == typeof l.then && await l, I()
            } catch (s) {
                try {
                    I()
                } catch (d) {}
            } finally {
                N.disabled = !1, N.textContent = await translateMessage("send", ""), A.disabled = !1
            }
        });
        let S = document.getElementById("tx-report-btn");
        S && (S.addEventListener("click", t => {
            t.stopPropagation();
            let e = "true" === S.getAttribute("aria-busy");
            e || (function t(e = "") {
                y.value = e, k.textContent = `${y.value.length} / ${y.getAttribute("maxlength")}`, _.classList.add("show"), setTimeout(() => y.focus(), 50), document.documentElement.style.overflow = "hidden", window.pauseVGCountdown()
            }(), S.style.pointerEvents = "none", S.style.opacity = "0.5")
        }), S.addEventListener("keydown", t => {
            ("Enter" === t.key || " " === t.key) && (t.preventDefault(), S.click())
        }))
    }
    let z = document.createElement("div");
    z.id = "tx-settings-popup", z.style.pointerEvents = "auto";
    let L = document.createElement("div");
    L.id = "tx-header";
    let G = document.createElement("h2");
    G.textContent = "ViewGrip";
    let T = document.createElement("div");
    T.className = "tx-badge", T.textContent = await translateMessage("settings", ""), L.appendChild(G), L.appendChild(T), z.appendChild(L);
    let V = document.createElement("div");
    V.id = "tx-user-card";
    let B = document.createElement("div");
    B.id = "tx-avatar", B.textContent = "VG", V.appendChild(B);
    let j = document.createElement("div");

    function q(t, e, a) {
        let n = document.createElement("div");
        n.className = "tx-meta-row";
        let i = document.createElement("div");
        i.className = "tx-meta-title", i.textContent = t;
        let r = document.createElement("div");
        r.className = "tx-meta-value";
        let o = document.createElement("span");
        return o.id = e, o.textContent = void 0 === a ? "-" : String(a), r.appendChild(o), n.appendChild(i), n.appendChild(r), n
    }
    j.id = "tx-user-meta";
    let D = q(await translateMessage("username", ""), "tx-username", "-"),
        M = q(await translateMessage("coins", ""), "tx-coins", "-"),
        F = q(await translateMessage("membership", ""), "tx-membership", "-"),
        R = q(await translateMessage("today_watches", ""), "tx-todaywatch", "-");
    j.appendChild(D), j.appendChild(M), j.appendChild(F), j.appendChild(R), V.appendChild(j), z.appendChild(V);
    let Y = document.createElement("div");
    Y.className = "tx-section-title", Y.textContent = await translateMessage("auto_features", ""), z.appendChild(Y);
    let U = document.createElement("div");
    U.className = "tx-checkbox-group";
    let Z = [{
        id: "tx-like",
        label: await translateMessage("like", ""),
        bonus: "+1 " + await translateMessage("coin", "")
    }, {
        id: "tx-subscribe",
        label: await translateMessage("subscribe", ""),
        bonus: "+2 " + await translateMessage("coins", "")
    }, {
        id: "tx-comment",
        label: await translateMessage("comment", ""),
        bonus: "+1 " + await translateMessage("coin", "")
    }, {
        id: "tx-likecomment",
        label: await translateMessage("like_comment", ""),
        bonus: "+0.5 " + await translateMessage("coin", "")
    }, ];
    Z.forEach(t => {
        let e = document.createElement("div");
        e.className = "tx-toggle-row", e.id = `row-${t.id}`;
        let a = document.createElement("div");
        a.className = "tx-toggle-left";
        let n = document.createElement("div");
        n.className = "tx-toggle-label", n.textContent = t.label;
        let i = document.createElement("div");
        i.className = "tx-toggle-desc", i.textContent = t.bonus, a.appendChild(n), a.appendChild(i);
        let r = document.createElement("div");
        r.style.display = "flex", r.style.alignItems = "center", r.style.gap = "10px";
        let o = document.createElement("div");
        o.className = "tx-bonus", o.textContent = t.bonus, o.style.display = "none";
        let l = document.createElement("div");
        l.className = "tx-switch", l.id = `${t.id}-switch`, l.setAttribute("role", "switch"), l.setAttribute("aria-checked", "false");
        let s = document.createElement("div");
        s.className = "tx-knob", l.appendChild(s), r.appendChild(o), r.appendChild(l), e.appendChild(a), e.appendChild(r), U.appendChild(e), l.style.pointerEvents = "auto"
    }), z.appendChild(U);
    let P = document.createElement("div");
    P.className = "tx-section-title", P.textContent = await translateMessage("video_quality", ""), z.appendChild(P);
    let H = document.createElement("select");
    H.id = "tx-quality";
    let W = document.createElement("option");
    W.value = 0, W.textContent = await translateMessage("default", ""), H.appendChild(W), [144, 240, 360, 480, 720].forEach(t => {
        let e = document.createElement("option");
        e.value = t, e.textContent = `${t}p`, H.appendChild(e)
    }), z.appendChild(H);
    let X = document.createElement("button");
    X.id = "tx-login-btn", X.textContent = "Login to YouTube", X.style.marginTop = "12px", X.style.display = "none", z.appendChild(X), document.body.appendChild(z);
    let K = document.createElement("div");
    K.id = "tx-global-overlay", K.style.pointerEvents = "auto", document.body.appendChild(K);
    let O = document.createElement("div");
    O.id = "tx-refresh-modal";
    let Q = document.createElement("div");
    Q.style.fontWeight = "800", Q.style.fontSize = "16px", Q.style.color = "#fff", Q.textContent = await translateMessage("refresh_required", ""), O.appendChild(Q);
    let J = document.createElement("p");
    J.textContent = await translateMessage("refresh_info", ""), O.appendChild(J);
    let tt = document.createElement("div");
    tt.className = "tx-actions";
    let te = document.createElement("button");
    te.className = "tx-btn cancel", te.textContent = "Cancel";
    let ta = document.createElement("button");

    function tn(t) {
        try {
            chrome && chrome.storage && chrome.storage.sync && chrome.storage.sync.set(t)
        } catch (e) {}
    }
    ta.className = "tx-btn confirm", ta.textContent = await translateMessage("refresh", ""), tt.appendChild(te), tt.appendChild(ta), O.appendChild(tt), document.body.appendChild(O);
    let ti = Z.map(t => t.id),
        tr = ti.reduce((t, e) => (t[e] = !0, t), {});

    function to(t) {
        ti.forEach(e => {
            let a = document.getElementById(`${e}-switch`);
            if (!a) return;
            let n = !1 !== t[e];
            a.classList.toggle("on", n), a.setAttribute("aria-checked", n ? "true" : "false")
        })
    }
    to(tr),
        function t(e, a) {
            let n = !1,
                i = t => {
                    if (!n) {
                        n = !0;
                        try {
                            a(t || {})
                        } catch (e) {}
                    }
                },
                r = setTimeout(() => i({}), 5e3);
            try {
                "undefined" != typeof chrome && chrome.storage && chrome.storage.sync ? chrome.storage.sync.get(e, t => {
                    clearTimeout(r);
                    try {
                        chrome.runtime.lastError
                    } catch (e) {}
                    i(t || {})
                }) : (clearTimeout(r), i({}))
            } catch (o) {
                clearTimeout(r), i({})
            }
        }(["txFeatures"], t => {
            let e = !!(t && t.txFeatures && "object" == typeof t.txFeatures),
                a = function t(e) {
                    let a = {
                        ...tr
                    };
                    return e && "object" == typeof e && ti.forEach(t => {
                        Object.prototype.hasOwnProperty.call(e, t) && (a[t] = !1 !== e[t])
                    }), a
                }(e ? t.txFeatures : null);
            to(a), e || tn({
                txFeatures: a
            })
        }), ti.forEach(t => {
            let e = document.getElementById(`${t}-switch`);
            e && e.addEventListener("click", t => {
                t.stopPropagation();
                let a = e.classList.toggle("on");
                e.setAttribute("aria-checked", a ? "true" : "false");
                let n = {};
                ti.forEach(t => {
                    let e = document.getElementById(`${t}-switch`);
                    n[t] = e && e.classList.contains("on")
                }), tn({
                    txFeatures: n
                })
            })
        });
    try {
        let tl = localStorage.getItem("yt-player-quality");
        if (tl) {
            let ts = JSON.parse(tl);
            if (ts && ts.data) {
                let td = JSON.parse(ts.data);
                td && td.quality && (H.value = td.quality)
            }
        }
    } catch (tp) {}
    H.addEventListener("change", t => {
        let e = t.target.value;
        ! function t(e) {
            K.style.display = "block", O.style.display = "block", K.onclick = () => {};
            let a = () => {
                    K.style.display = "none", O.style.display = "none", te.removeEventListener("click", n), ta.removeEventListener("click", i)
                },
                n = () => a(),
                i = () => {
                    a(), e()
                };
            te.addEventListener("click", n), ta.addEventListener("click", i)
        }(() => {
            try {
                0 == e ? localStorage.removeItem("yt-player-quality") : function t(e) {
                    let a = Date.now(),
                        n = {
                            quality: Number(e),
                            previousQuality: Number(e)
                        },
                        i = {
                            data: JSON.stringify(n),
                            expiration: a + 94608e6,
                            creation: a
                        };
                    try {
                        localStorage.setItem("yt-player-quality", JSON.stringify(i))
                    } catch (r) {}
                }(e), window.location.href.includes("youtube.com/watch?v=") && location.reload()
            } catch (t) {}
        })
    }), X.addEventListener("click", () => {
        let t = window.location.href,
            e = encodeURIComponent("https://www.youtube.com/signin?action_handle_signin=true&next=" + encodeURIComponent(t));
        window.open("https://accounts.google.com/v3/signin/identifier?service=youtube&hl=en&continue=" + e + "&flowName=GlifWebSignIn&flowEntry=ServiceLogin", "_self")
    });
    let tc = !1;

    function tx() {
        z.style.display = "none", tc = !1
    }
    n.addEventListener("click", t => {
        t.stopPropagation(), tc ? tx() : (z.style.display = "block", tc = !0, L.style.display = "flex", tf())
    }), document.addEventListener("click", function(t) {
        tc && (z.contains(t.target) || n.contains(t.target) || tx())
    }, !0);
    try {
        let t$ = document.getElementsByTagName("html")[0];
        t$ && "none" === getComputedStyle(t$).pointerEvents && (n.style.pointerEvents = "auto", z.style.pointerEvents = "auto", K.style.pointerEvents = "auto", O.style.pointerEvents = "auto")
    } catch (tb) {}
    let tu = !1;
    try {
        let tg = "function" == typeof readCookie ? readCookie("SID") : null;
        tu = !!tg
    } catch (th) {
        tu = !1
    }
    async function t_(t, e = {}, a = 7e3) {
        let n = null,
            i = null;
        try {
            let r = Object.assign({}, e);
            "undefined" != typeof AbortController && (n = new AbortController, r.signal = n.signal, i = setTimeout(() => {
                try {
                    n.abort()
                } catch (t) {}
            }, Math.max(1500, Number(a) || 7e3)));
            let o = await fetch(t, r);
            if (!o || !o.ok) return null;
            return await o.json()
        } catch (l) {
            return null
        } finally {
            i && clearTimeout(i)
        }
    }

    function tm(t, e) {
        let a = document.getElementById(t);
        if (!a) return;
        let n = a.parentElement;
        if (!n) {
            a.textContent = null != e ? String(e) : "-";
            return
        }
        n.style.transition = "opacity .22s ease", n.style.opacity = "0", setTimeout(() => {
            a.textContent = null != e && "" !== e ? String(e) : "-", n.style.opacity = "1"
        }, 220)
    }
    async function tf() {
        ["tx-username", "tx-coins", "tx-membership", "tx-todaywatch"].forEach(t => {
            tm(t, "...")
        });
        let t = await new Promise(t => {
                let e = !1,
                    a = a => {
                        e || (e = !0, t(a || null))
                    },
                    n = setTimeout(() => a(null), 5e3);
                try {
                    "undefined" != typeof chrome && chrome.storage && chrome.storage.local ? chrome.storage.local.get("token", t => {
                        clearTimeout(n);
                        try {
                            chrome.runtime.lastError
                        } catch (e) {}
                        a(t && t.token ? t.token : null)
                    }) : (clearTimeout(n), a(null))
                } catch (i) {
                    clearTimeout(n), a(null)
                }
            }),
            e = new URLSearchParams;
        t ? e.append("token", t) : e.append("token", "");
        let a = "https://www.viewgrip.net/api/user_info?" + e.toString();
        try {
            let n = await t_(a, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }, 7e3);
            if (!n || !n || "success" !== n.status) {
                tm("tx-username", "-"), tm("tx-coins", "-"), tm("tx-membership", "-"), tm("tx-todaywatch", "-");
                return
            }
            tm("tx-username", n.username ?? "-"), tm("tx-coins", void 0 !== n.coins && null !== n.coins ? n.coins : "-"), tm("tx-membership", n.membership ?? "-"), tm("tx-todaywatch", void 0 !== n.today_watches && null !== n.today_watches ? n.today_watches : "-")
        } catch (i) {
            tm("tx-username", "-"), tm("tx-coins", "-"), tm("tx-membership", "-"), tm("tx-todaywatch", "-");
            return
        }
    }(t = tu) ? (U.style.display = "flex", H.style.display = "block", Y.style.display = "block", X.style.display = "none") : (U.style.display = "none", H.style.display = "none", Y.style.display = "none", X.style.display = "block"), document.addEventListener("keydown", t => {
        "Escape" === t.key && tc && tx()
    }), [n, z, K, O].forEach(t => {
        t && (("tx-settings-popup" === t.id || "tx-refresh-modal" === t.id) && (t.style.zIndex = "2147483647"), t.style.pointerEvents = "auto")
    });
    try {
        window.VGSettingsFetch = tf
    } catch (ty) {}
}