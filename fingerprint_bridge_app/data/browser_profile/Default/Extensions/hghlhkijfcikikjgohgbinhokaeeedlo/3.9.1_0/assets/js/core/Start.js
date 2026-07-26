/*!
 * ViewGrip JavaScript Library
 * https://www.viewgrip.net/
 * Date: 2024-01-21T17:08Z
 */
var MainUrl = void 0 !== MainUrl ? MainUrl : "https://www.viewgrip.net",
    parsedUrl = void 0 !== parsedUrl ? parsedUrl : new URL(MainUrl),
    Manifest = void 0 !== Manifest ? Manifest : "undefined" != typeof chrome && chrome.runtime && "function" == typeof chrome.runtime.getManifest ? chrome.runtime.getManifest() : {
        version: "0.0.0"
    };
let TimeOutTyping, remainingTime = 0,
    startVerification = !0,
    startGetVideo = !0,
    lastUpdateTs = 0;
window.timerRunning = !1, window.notificationElement = null, window.timerStartTime = 0, window.timerDuration = 0, window.styleElement = null, window.paused = !1, window.elapsedWhenPaused = 0, window.countdownTimer = null, window.updateTimer = null, window.VG_SAFE_RUNTIME || (window.VG_SAFE_RUNTIME = {
    errorAttached: !1,
    lastRecoveryNoticeAt: 0,
    lastAutoRecoverAt: 0,
    errorBurst: []
});
var VG_NATIVE_DOM = void 0 !== VG_NATIVE_DOM ? VG_NATIVE_DOM : {
    documentQuerySelector: "undefined" != typeof Document && Document.prototype ? Document.prototype.querySelector : null,
    documentQuerySelectorAll: "undefined" != typeof Document && Document.prototype ? Document.prototype.querySelectorAll : null,
    elementQuerySelector: "undefined" != typeof Element && Element.prototype ? Element.prototype.querySelector : null,
    elementQuerySelectorAll: "undefined" != typeof Element && Element.prototype ? Element.prototype.querySelectorAll : null
};

function vgMarkWorkerControlled(e = "background_message") {
    try {
        window.VG_WORKER_CONTROLLED_TAB = !0, window.VG_WORKER_CONTROLLED_REASON = vgDiagnosticText(e, 160), window.VG_WORKER_CONTROLLED_AT = Date.now()
    } catch (t) {}
}

function vgIsWorkerControlledTab() {
    try {
        return !0 === window.VG_WORKER_CONTROLLED_TAB
    } catch (e) {
        return !1
    }
}

function vgDiagnosticText(e, t = 2e3) {
    try {
        if ((e = (e = null == e ? "" : String(e)).replace(/[\u0000\r\n]+/g, " ").trim()).length > t) return e.slice(0, t);
        return e
    } catch (r) {
        return ""
    }
}

function vgSanitizeDiagnosticFile(e) {
    try {
        let t = vgDiagnosticText(e, 1200);
        return t = (t = (t = t.replace(/(?:chrome|moz)-extension:\/\/[^/]+\//g, "extension://")).replace(/(?:chrome|moz)-extension:\/\/[^/]+/g, "extension://")).replace(/ViewGrip\s+Extension/gi, "extension"), vgDiagnosticText(t, 900)
    } catch (r) {
        return ""
    }
}

function vgSanitizeDiagnosticStack(e) {
    try {
        let t = vgDiagnosticText(e, 3500);
        return t = (t = (t = t.replace(/(?:chrome|moz)-extension:\/\/[^/]+\//g, "extension://")).replace(/(?:chrome|moz)-extension:\/\/[^/]+/g, "extension://")).replace(/ViewGrip\s+Extension/gi, "extension"), vgDiagnosticText(t, 3e3)
    } catch (r) {
        return ""
    }
}

function vgDiagnosticEndpoint() {
    try {
        let e = void 0 !== MainUrl && MainUrl ? String(MainUrl) : "https://www.viewgrip.net";
        return e.replace(/\/$/, "") + "/api/worker/diagnostic"
    } catch (t) {
        return "https://www.viewgrip.net/api/worker/diagnostic"
    }
}

function vgDiagnosticCurrentPage() {
    try {
        if (!window.location) return "";
        return window.location.hostname + window.location.pathname
    } catch (e) {
        return ""
    }
}

function vgDiagnosticFormBody(e) {
    let t = Object.assign({
        type: "script_error",
        phase: "",
        name: "",
        message: "",
        file: "",
        line: "",
        column: "",
        selector: "",
        stack: ""
    }, e || {});
    try {
        t.file = vgSanitizeDiagnosticFile(t.file), t.stack = vgSanitizeDiagnosticStack(t.stack), t.url = vgDiagnosticCurrentPage(), t.host = window.location ? window.location.hostname : ""
    } catch (r) {}
    try {
        let o = new URLSearchParams;
        return Object.keys(t).forEach(e => o.append(e, vgDiagnosticText(t[e], "stack" === e ? 3e3 : 2e3))), o.toString()
    } catch (n) {
        return Object.keys(t).map(e => encodeURIComponent(e) + "=" + encodeURIComponent(vgDiagnosticText(t[e]))).join("&")
    }
}

function vgDiagnosticShouldSend(e) {
    try {
        let t = window.VG_DIAGNOSTIC_STATE || (window.VG_DIAGNOSTIC_STATE = {
                sent: Object.create(null),
                selectorMisses: Object.create(null),
                minuteWindowStartedAt: 0,
                minuteCount: 0
            }),
            r = Date.now(),
            o = String(e.type || "");
        if ((!t.minuteWindowStartedAt || r - t.minuteWindowStartedAt > 6e4) && (t.minuteWindowStartedAt = r, t.minuteCount = 0), t.minuteCount >= 10) return !1;
        let n = [o, e.phase, e.name, e.selector, e.message].map(e => vgDiagnosticText(e, 300)).join("|"),
            i = t.sent[n] || 0,
            a = 0 === o.indexOf("missing_") ? 36e5 : 6e5;
        if (r - i < a) return !1;
        return t.sent[n] = r, t.minuteCount += 1, !0
    } catch (l) {
        return !1
    }
}

function vgReportDiagnostic(e) {
    try {
        let t = Object.assign({}, e || {});
        if (!t.name && !t.message && !t.selector || !vgDiagnosticWorkerActive() || 0 === String(t.type || "").indexOf("missing_") && !0 !== t.confirmed && "1" !== t.confirmed || (delete t.confirmed, !vgDiagnosticShouldSend(t))) return !1;
        let r = vgDiagnosticFormBody(t),
            o = vgDiagnosticEndpoint();
        try {
            if (navigator && "function" == typeof navigator.sendBeacon) {
                let n = new Blob([r], {
                    type: "application/x-www-form-urlencoded;charset=UTF-8"
                });
                if (navigator.sendBeacon(o, n)) return !0
            }
        } catch (i) {}
        try {
            return fetch(o, {
                method: "POST",
                mode: "no-cors",
                credentials: "omit",
                keepalive: !0,
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8"
                },
                body: r
            }).catch(() => {}), !0
        } catch (a) {
            return !1
        }
    } catch (l) {
        return !1
    }
}

function vgDiagnosticStack(e = null) {
    try {
        if (e && e.stack) return vgSanitizeDiagnosticStack(e.stack);
        return vgSanitizeDiagnosticStack(Error().stack || "")
    } catch (t) {
        return ""
    }
}

function vgDiagnosticExistingStack(e = null) {
    try {
        return e && e.stack ? vgSanitizeDiagnosticStack(e.stack) : ""
    } catch (t) {
        return ""
    }
}

function vgDiagnosticSourceFromStack(e) {
    let t = {
        file: "",
        line: "",
        column: ""
    };
    try {
        let r = String(e || "").split("\n");
        for (let o of r) {
            if (/vg(QuerySelector|QuerySelectorAll|NativeQuery|RecordMissingSelector|FlushSelectorDiagnostics|Diagnostic)/.test(o)) continue;
            let n = o.match(/(?:chrome|moz)-extension:\/\/[^/]+\/([^\s):]+):(\d+):(\d+)/);
            if (n) {
                t.file = n[1], t.line = n[2], t.column = n[3];
                break
            }
            let i = o.match(/extension:\/\/([^\s):]+):(\d+):(\d+)/);
            if (i) {
                t.file = i[1], t.line = i[2], t.column = i[3];
                break
            }
            let a = o.match(/(https?:\/\/[^\s)]+):(\d+):(\d+)/);
            if (a) {
                t.file = vgSanitizeDiagnosticFile(a[1]), t.line = a[2], t.column = a[3];
                break
            }
        }
    } catch (l) {}
    return t
}

function vgDiagnosticPhaseFromStack(e) {
    try {
        let t = String(e || "").split("\n");
        for (let r of t) {
            if (/vg(QuerySelector|QuerySelectorAll|NativeQuery|RecordMissingSelector|FlushSelectorDiagnostics|Diagnostic)/.test(r)) continue;
            let o = r.match(/at\s+([^\s(]+)/);
            if (o && o[1]) return vgDiagnosticText(o[1], 160)
        }
    } catch (n) {}
    return "unknown_phase"
}

function vgDescribeElementForDiagnostic(e) {
    try {
        if (!e) return "null_root";
        if (e === document) return "document";
        let t = e.tagName ? String(e.tagName).toLowerCase() : "element",
            r = e.id ? "#" + e.id : "",
            o = "";
        return e.classList && e.classList.length && (o = "." + Array.from(e.classList).slice(0, 4).join(".")), vgDiagnosticText(t + r + o, 220)
    } catch (n) {
        return "element"
    }
}

function vgSplitSelectorList(e) {
    let t = String(e || ""),
        r = [],
        o = "",
        n = "",
        i = 0,
        a = 0;
    for (let l = 0; l < t.length; l++) {
        let c = t[l],
            s = t[l - 1];
        if (n) {
            o += c, c === n && "\\" !== s && (n = "");
            continue
        }
        if ("'" === c || '"' === c) {
            n = c, o += c;
            continue
        }
        if ("[" === c && i++, "]" === c && i > 0 && i--, "(" === c && a++, ")" === c && a > 0 && a--, "," === c && 0 === i && 0 === a) {
            let u = o.trim();
            u && r.push(u), o = "";
            continue
        }
        o += c
    }
    let g = o.trim();
    return g && r.push(g), r.length ? r : [t]
}

function vgDiagnosticIsYouTubePage() {
    try {
        return /(^|\.)youtube\.com$/i.test(window.location.hostname || "")
    } catch (e) {
        return !1
    }
}

function vgDiagnosticNavigationKey() {
    try {
        let e = window.location,
            t = e.hostname + e.pathname;
        return "/watch" === e.pathname && (t += "?v=" + (new URLSearchParams(e.search).get("v") || "")), t
    } catch (r) {
        return ""
    }
}

function vgDiagnosticUpdatePageState() {
    try {
        let e = window.VG_DIAGNOSTIC_STATE || (window.VG_DIAGNOSTIC_STATE = {
                sent: Object.create(null),
                selectorMisses: Object.create(null)
            }),
            t = Date.now(),
            r = vgDiagnosticNavigationKey();
        r && r !== e.navigationKey && (e.navigationKey = r, e.navigationStartedAt = t, e.pageReadySeenAt = 0, e.selectorMisses = Object.create(null));
        let o = document.readyState,
            n = ("interactive" === o || "complete" === o) && !!document.documentElement && !!document.body;
        if (!n) return !1;
        if (vgDiagnosticIsYouTubePage()) {
            let i = !!(document.getElementById("content") || document.getElementById("page-manager") || document.getElementById("movie_player") || document.getElementById("shorts-player") || VG_NATIVE_DOM.documentQuerySelector && VG_NATIVE_DOM.documentQuerySelector.call(document, "ytd-app, ytm-app, video"));
            if (!i) return !1
        }
        return e.pageReadySeenAt || (e.pageReadySeenAt = t), !0
    } catch (a) {
        return !1
    }
}

function vgDiagnosticPageReadyLongEnough() {
    try {
        if (!vgDiagnosticUpdatePageState()) return !1;
        let e = window.VG_DIAGNOSTIC_STATE || {},
            t = Date.now();
        return Boolean(e.pageReadySeenAt && t - e.pageReadySeenAt >= 12e3 && t - (e.navigationStartedAt || 0) >= 12e3)
    } catch (r) {
        return !1
    }
}

function vgDiagnosticWorkerActive() {
    try {
        if (!vgIsWorkerControlledTab()) return !1;
        if ("undefined" != typeof forceStop) return Boolean(forceStop);
        return !1
    } catch (e) {
        return !1
    }
}

function vgDiagnosticCleanPhase(e = "") {
    return String(e || "").replace(/^optional:/i, "")
}

function vgDiagnosticContextText(e = "", t = "", r = "") {
    try {
        if (e && "object" == typeof e) return [e.selector || "", e.phase || "", e.stack || ""].join(" ").toLowerCase();
        return [String(e || ""), String(t || ""), String(r || "")].join(" ").toLowerCase()
    } catch (o) {
        return ""
    }
}

function vgPhaseLooksProbe(e = "") {
    let t = String(e || "").toLowerCase();
    return !!t && (!!/^optional:/i.test(t) || /probe|cleanup|clean|dismiss|close|hide|modal|popup|dialog|overlay|scan|detect|checkingvideo|statecheck|toggleprobe|already/.test(t))
}

function vgSelectorLooksOptional(e, t = "") {
    let r = String(e || "").toLowerCase().trim(),
        o = String(t || "").toLowerCase();
    return !!(!r || "html" === r || "body" === r || "document" === r || "iframe" === r || /vgnotif|vgoverlay|textmessage|vgloginwarning|countdown|timer-progress|timer-dot|loader-wrapper|tx-skip-btn|tx-report-btn/.test(r + " " + o))
}

function vgSelectorLooksCritical(e, t = "", r = "") {
    let o = String(e || "").toLowerCase(),
        n = vgDiagnosticCleanPhase(t).toLowerCase(),
        i = vgDiagnosticContextText(e, n, r);
    return !(vgSelectorLooksOptional(o, n) || vgPhaseLooksProbe(t)) && !!(/getsubscribebutton|subscribethis|subscribe/.test(i) || /findlikebutton|likevideobutton|likeaction|likevideo|\blike\b/.test(i) && !/comment/.test(i) || /comment_liking|commentlike|comment-like|findcommentlike|comment.*engagement|likecomment/.test(i) || /commentbox|commentinput|directcomment|submitcomment|commentaction|\bcomment\b/.test(i) || /autonext|autonav|auto[-_ ]?play/.test(i) || /keyword|search|channel|tab|find/.test(i) || /^#movie_player$|^#shorts-player$|video\.html5-main-video|^video$|ytd-watch-flexy|ytd-player/.test(o) || /subscribe-button|aria-label\^=["']?subscribe|subscribe"]|reelsubscribebutton|reel.*subscribe/.test(o) || /segmented-like-dislike|like-button-view-model|#like-button|aria-label\^=["']?like|aria-label\*=["']?like|reel.*like/.test(o) || /commentbox|contenteditable-root|contenteditable|simplebox|placeholder-area|simplebox-placeholder|ytd-comments|#comments|#submit-button/.test(o))
}

function vgDiagnosticNormalizeWorkflow(e = "") {
    return String(e || "").toLowerCase().replace(/[^a-z0-9]+/g, "_").replace(/^_+|_+$/g, "")
}

function vgDiagnosticFunctionNamesFromStack(e = "") {
    try {
        return String(e || "").split("\n").map(e => {
            let t = e.match(/\bat\s+([A-Za-z0-9_$]+)\s*\(/);
            if (t && t[1]) return t[1];
            let r = e.match(/\bat\s+([A-Za-z0-9_$]+)\s+/);
            return r && r[1] ? r[1] : ""
        }).filter(Boolean)
    } catch (t) {
        return []
    }
}

function vgDiagnosticInferWorkflow(e = "", t = "", r = "") {
    try {
        let o = e && "object" == typeof e ? e : null,
            n = o ? o.phase || "" : t || "",
            i = o ? o.stack || "" : r || "",
            a = o ? o.selector || "" : e || "",
            l = vgDiagnosticCleanPhase(n),
            c = vgDiagnosticFunctionNamesFromStack(i),
            s = [l].concat(c).map(vgDiagnosticNormalizeWorkflow).filter(Boolean);
        for (let u of s) {
            if (/subscribe/.test(u)) return "subscribe";
            if (/comment_liking|comment_like|likeoncomments|like_comment/.test(u)) return "comment_liking";
            if (/comment/.test(u)) return "comment";
            if (/like/.test(u) && !/comment/.test(u)) return "like";
            if (/playback|player|video|checkingvideo|movie_player|shorts_player/.test(u)) return "playback";
            if (/search|keyword/.test(u)) return "search";
            if (/channel/.test(u)) return "channel";
            if (/popup|modal|dialog|overlay|dismiss|close|cleanup|clean/.test(u)) return "cleanup"
        }
        let g = vgDiagnosticNormalizeWorkflow(a);
        if (/subscribe/.test(g)) return "subscribe";
        if (/comment/.test(g) && /like/.test(g)) return "comment_liking";
        if (/comment/.test(g)) return "comment";
        if (/like/.test(g)) return "like";
        if (/movie_player|shorts_player|html5_main_video|video|watch_flexy|ytd_player/.test(g)) return "playback";
        return s[0] || "unknown"
    } catch (d) {
        return "unknown"
    }
}

function vgSelectorMatchesAction(e, t = "", r = "workflow") {
    let o = e && "object" == typeof e ? e : null,
        n = o ? o.selector : e,
        i = String(o ? o.phase || "" : t || "").toLowerCase();
    if (vgSelectorLooksOptional(n, i)) return !1;
    let a = vgDiagnosticNormalizeWorkflow(r || "workflow"),
        l = vgDiagnosticInferWorkflow(o || n, t, o && o.stack || "");
    return a && "workflow" !== a ? a === l || ("comment" !== a || "comment_liking" !== l) && ("comment_liking" !== a || "comment" !== l) && "player" === a && "playback" === l : "cleanup" !== l && "unknown" !== l
}

function vgDiagnosticExplicitDomSignal(e = "") {
    try {
        let t = String(e || "").toLowerCase();
        return /\b(dom_changed|class_changed|class_removed|selector_syntax_error|invalid_selector|selector_runtime_error|native_selector_unavailable|native selector unavailable|query_syntax_error)\b/.test(t)
    } catch (r) {
        return !1
    }
}

function vgSelectorDiagnosticStatusNeedsReport(e = "fail") {
    let t = String(e || "fail").toLowerCase();
    return !!(t && "success" !== t && "already" !== t && "disabled" !== t && "not_required" !== t && "off" !== t && "skip" !== t && "skipped" !== t && vgDiagnosticExplicitDomSignal(t))
}

function vgDiagnosticTextLooksExternalOrRecoverable(e = "", t = "", r = "", o = {}) {
    try {
        if (o && (!0 === o.developer_action_required || !0 === o.diagnostic_required || !0 === o.dom_issue || !0 === o.script_fatal)) return !1;
        let n = [e, t, r, o && o.policy, o && o.stage].map(e => String(e || "").toLowerCase()).join(" ");
        if (/server|network|internet|connection|timeout|ajax|xhr|fetch|challenge|signature|token|session|expired|inactive|cron|database|db|worker deleted|worker_deleted|worker_has_been_deleted|start_time|retry|rejected|notification|storage|slow connection|slow_connection|content retry|content_retry|reload loop|request failed|request_failed|response handler|response_handler/.test(n) || /unexpected video url|unexpected_video_url|video drift|video_drift|target_video_not_found|video_not_found|homepage_redirect|non_target|not in channel|notworkkeyword/.test(n)) return !0;
        return !1
    } catch (i) {
        return !0
    }
}

function vgDiagnosticWorkflowNeedsDeveloperReport(e = "workflow", t = "fail", r = "", o = {}) {
    try {
        if (o && (!0 === o.developer_action_required || !0 === o.diagnostic_required || !0 === o.dom_issue || !0 === o.script_fatal)) return !0;
        let n = [e, t, r].map(e => String(e || "").toLowerCase()).join(" ");
        if (vgDiagnosticTextLooksExternalOrRecoverable(e, t, r, o)) return !1;
        return vgDiagnosticExplicitDomSignal(n)
    } catch (i) {
        return !1
    }
}

function vgFlushActionSelectorDiagnostics(e = "workflow", t = "fail", r = 1) {
    try {
        if (!vgSelectorDiagnosticStatusNeedsReport(t) || !vgDiagnosticIsYouTubePage() || !vgDiagnosticWorkerActive()) return !1;
        return vgFlushSelectorDiagnostics(String(e || "workflow") + "_" + String(t || "fail"), !0, Math.max(1, Number(r) || 1), String(e || "workflow")), !0
    } catch (o) {
        return !1
    }
}

function vgRecordMissingSelector(e, t, r, o, n) {
    try {
        if (!vgDiagnosticIsYouTubePage() || !vgDiagnosticWorkerActive()) return;
        let i = vgDiagnosticSourceFromStack(n),
            a = vgSplitSelectorList(e),
            l = vgDiagnosticText(e, 3e3),
            c = r || vgDiagnosticPhaseFromStack(n),
            s = /^optional:/i.test(String(c || "")) || vgPhaseLooksProbe(c),
            u = vgDiagnosticCleanPhase(c || "unknown_phase") || "unknown_phase",
            g = vgDescribeElementForDiagnostic(o || document),
            d = window.VG_DIAGNOSTIC_STATE || (window.VG_DIAGNOSTIC_STATE = {
                sent: Object.create(null),
                selectorMisses: Object.create(null)
            }),
            f = Date.now();
        a.forEach(e => {
            if (vgSelectorLooksOptional(e, u)) return;
            let r = [d.navigationKey || vgDiagnosticNavigationKey(), u, g, e].join("|"),
                o = d.selectorMisses[r];
            o || (o = d.selectorMisses[r] = {
                selector: e,
                groupSelector: l,
                all: Boolean(t),
                phase: u,
                rootName: g,
                stack: n,
                source: i,
                firstAt: f,
                lastAt: f,
                count: 0,
                optional: Boolean(s),
                sent: !1
            }), o.count += 1, o.lastAt = f, o.optional = Boolean(o.optional || s), o.stack = n || o.stack, o.source = i || o.source
        })
    } catch (p) {}
}

function vgRecordSelectorFallbackEvidence(e, t, r, o, n) {
    try {
        if (!vgDiagnosticIsYouTubePage() || !vgDiagnosticWorkerActive()) return;
        let i = r || vgDiagnosticPhaseFromStack(n);
        if (/^optional:/i.test(String(i || "")) || vgPhaseLooksProbe(i)) return;
        let a = vgSplitSelectorList(e);
        if (!a || a.length <= 1) return;
        let l = o && ("function" == typeof o.querySelector || "function" == typeof o.querySelectorAll) ? o : document,
            c = l === document || 9 === l.nodeType,
            s = t ? c ? VG_NATIVE_DOM.documentQuerySelectorAll : VG_NATIVE_DOM.elementQuerySelectorAll : c ? VG_NATIVE_DOM.documentQuerySelector : VG_NATIVE_DOM.elementQuerySelector;
        if ("function" != typeof s) return;
        for (let u of a) try {
            if (!u || vgSelectorLooksOptional(u, i)) continue;
            let g = s.call(l, u),
                d = t ? !g || 0 === g.length : !g;
            d && vgRecordMissingSelector(u, t, String(i || "selector") + "|group_fallback_evidence", l, n)
        } catch (f) {}
    } catch (p) {}
}

function vgMaybeReportMissingSelector(e, t = "selector_still_missing", r = !1) {
    try {
        if (!e || e.sent) return !1;
        let o = [e.phase, t, e.selector, e.stack].join(" ");
        if (!vgDiagnosticExplicitDomSignal(o) || e.optional || !r && !vgDiagnosticPageReadyLongEnough() || !r && (e.count < 8 || Date.now() - e.firstAt < 45e3)) return !1;
        e.sent = !0;
        let n = Math.round((Date.now() - e.firstAt) / 1e3),
            i = e.source || vgDiagnosticSourceFromStack(e.stack);
        return vgReportDiagnostic(Object.assign({
            confirmed: !0,
            type: e.all ? "missing_elements_confirmed" : "missing_element_confirmed",
            phase: e.phase,
            name: "Explicit YouTube DOM contract signal",
            message: "Explicit DOM diagnostic signal. selector=" + e.selector + " | workflow=" + vgDiagnosticInferWorkflow(e) + " | root=" + e.rootName + " | count=" + e.count + " | age=" + n + "s | reason=" + t,
            selector: e.selector,
            stack: e.stack
        }, i))
    } catch (a) {
        return !1
    }
}

function vgFlushSelectorDiagnostics(e = "workflow_stuck", t = !1, r = 2, o = "") {
    try {
        if (!vgDiagnosticExplicitDomSignal(e)) return;
        let n = window.VG_DIAGNOSTIC_STATE || {},
            i = Date.now();
        if (!t && i - (n.lastSelectorFlushAt || 0) < 2e4 || !vgDiagnosticIsYouTubePage() || !vgDiagnosticWorkerActive() || !t && !vgDiagnosticPageReadyLongEnough()) return;
        n.lastSelectorFlushAt = i;
        let a = String(o || ""),
            l = Object.keys(n.selectorMisses || {}).map(e => n.selectorMisses[e]).filter(e => !(!e || e.sent || e.optional || e.count < Math.max(8, Number(r) || 8)) && (!a || !!vgSelectorMatchesAction(e, e.phase, a)) && vgDiagnosticExplicitDomSignal([e.phase, e.selector, e.stack].join(" "))).sort((e, t) => t.count - e.count).slice(0, 4);
        l.forEach(t => vgMaybeReportMissingSelector(t, e, !0))
    } catch (c) {}
}

function vgReportWorkflowDiagnostic(e = "workflow", t = "fail", r = "", o = {}) {
    try {
        let n = String(e || "workflow"),
            i = String(t || "fail"),
            a = o || {},
            l = !0 === a.developer_action_required || !0 === a.diagnostic_required || !0 === a.dom_issue || !0 === a.script_fatal || vgDiagnosticExplicitDomSignal([n, i, r, a.stage, a.policy].join(" "));
        if (!l || !vgSelectorDiagnosticStatusNeedsReport(i) && !vgDiagnosticExplicitDomSignal([n, i, r].join(" ")) && !0 !== a.developer_action_required && !0 !== a.diagnostic_required && !0 !== a.dom_issue && !0 !== a.script_fatal || !vgDiagnosticWorkflowNeedsDeveloperReport(n, i, r, a) || !vgDiagnosticIsYouTubePage() || !vgDiagnosticWorkerActive()) return !1;
        return vgReportDiagnostic(Object.assign({
            confirmed: !0,
            type: "workflow_state_issue",
            phase: n,
            name: "Developer-actionable YouTube workflow issue",
            message: vgDiagnosticText(r || "A developer-maintained selector/DOM contract failed. action=" + n + " | status=" + i, 3e3),
            selector: "",
            stack: vgDiagnosticStack()
        }, a))
    } catch (c) {
        return !1
    }
}

function vgDiagnosticSelectorMeta(e = {}, t = "workflow", r = "selector", o = "required") {
    try {
        if (e && "object" == typeof e) return {
            root: e.root || null,
            workflow: e.workflow || e.action || e.feature || t || "workflow",
            intent: e.intent || e.mode || o || "required",
            phase: e.phase || r || e.workflow || t || "selector"
        };
        return {
            root: null,
            workflow: t || "workflow",
            intent: o || "required",
            phase: "string" == typeof e && e ? e : r || t || "selector"
        }
    } catch (n) {
        return {
            root: null,
            workflow: t || "workflow",
            intent: o || "required",
            phase: r || "selector"
        }
    }
}

function vgCleanupSelector(e, t = "cleanup", r = "cleanup_probe", o = null) {
    return vgQueryOptionalSelector(e, vgDiagnosticSelectorMeta({
        root: o,
        workflow: t,
        intent: "cleanup",
        phase: r
    }, t, r, "cleanup"))
}

function vgCssEscapeIdentifier(e = "") {
    try {
        if ("undefined" != typeof CSS && CSS && "function" == typeof CSS.escape) return CSS.escape(String(e));
        return String(e).replace(/[^a-zA-Z0-9_-]/g, "\\$&")
    } catch (t) {
        return String(e || "")
    }
}

function vgGetYouTubeElementById(e, t = {}) {
    let r = String(e || "").trim();
    if (!r) return null;
    let o = vgDiagnosticSelectorMeta(t, t.workflow || "youtube_dom", t.phase || "getElementById:" + r, t.intent || "required");
    return vgQuerySelector("#" + vgCssEscapeIdentifier(r), o)
}

function vgWorkflowFail(e = "workflow", t = "fail", r = "", o = null, n = {}) {
    try {
        return o && vgReportCaughtError(o, String(e || "workflow") + "." + String(t || "fail")), vgReportWorkflowDiagnostic(e, t, r, n || {})
    } catch (i) {
        return !1
    }
}

function vgWorkflowSoftFail(e = "workflow", t = "fail", r = "", o = {}) {
    try {
        return vgReportWorkflowDiagnostic(e, t, r, o || {})
    } catch (n) {
        return !1
    }
}

function vgIsExtensionOwnedSource(e = "", t = "") {
    let r = vgSanitizeDiagnosticFile(e),
        o = vgSanitizeDiagnosticStack(t);
    return !!(/assets\/js\/core\//.test(r) || /extension:\/\/assets\/js\/core\//.test(o) || /Extension context invalidated/i.test(o))
}

function vgShouldReportScriptProblem(e, t, r, o, n) {
    try {
        let i = String(e || ""),
            a = String(t || ""),
            l = String(r || ""),
            c = a + " " + l;
        if (/Extension context invalidated|The message port closed before a response was received|Receiving end does not exist/i.test(c) || !vgIsExtensionOwnedSource(o, n) && !/Maximum call stack|ReferenceError|SyntaxError/i.test(c) || /AbortError|ResizeObserver loop|Script error\.?$/i.test(c) || !/TypeError|ReferenceError|SyntaxError|RangeError|Maximum call stack/i.test(c) && /server|network|internet|connection|timeout|ajax|xhr|fetch|challenge|signature|token|session|expired|inactive|database|db|start_time|retry|rejected|notification|storage|worker_has_been_deleted|worker deleted/i.test(c) || "caught_error" === i) return !1;
        return !0
    } catch (s) {
        return !1
    }
}

function vgNativeQuery(e, t = null, r = !1, o = "") {
    let n = vgDiagnosticStack();
    try {
        let i = t && ("function" == typeof t.querySelector || "function" == typeof t.querySelectorAll) ? t : document,
            a = i === document || 9 === i.nodeType,
            l = r ? a ? VG_NATIVE_DOM.documentQuerySelectorAll : VG_NATIVE_DOM.elementQuerySelectorAll : a ? VG_NATIVE_DOM.documentQuerySelector : VG_NATIVE_DOM.elementQuerySelector;
        if ("function" != typeof l) {
            let c = vgDiagnosticSourceFromStack(n);
            return vgShouldReportScriptProblem("selector_runtime_error", "Native selector function unavailable", "Native selector function is unavailable for selector: " + e, c.file, n) && vgReportDiagnostic(Object.assign({
                type: "selector_runtime_error",
                phase: o || vgDiagnosticPhaseFromStack(n),
                name: "Native selector function unavailable",
                message: "Native selector function is unavailable for selector: " + e,
                selector: e,
                stack: n
            }, c)), r ? [] : null
        }
        let s = l.call(i, e),
            u = r ? !s || 0 === s.length : !s;
        return u ? vgRecordMissingSelector(e, r, o, i, n) : vgRecordSelectorFallbackEvidence(e, r, o, i, n), r ? s || [] : s || null
    } catch (g) {
        let d = vgDiagnosticStack(g) || n,
            f = vgDiagnosticSourceFromStack(d),
            p = g && g.name ? g.name : "SelectorError",
            m = g && g.message ? g.message : "Selector query failed";
        return vgShouldReportScriptProblem("selector_error", p, m, f.file, d) && vgReportDiagnostic(Object.assign({
            type: "selector_error",
            phase: o || vgDiagnosticPhaseFromStack(d),
            name: p,
            message: m,
            selector: e,
            stack: d
        }, f)), r ? [] : null
    }
}

function vgDiagnosticSelectorOptions(e = null, t = "", r = "") {
    try {
        let o = e && ("function" == typeof e.querySelector || "function" == typeof e.querySelectorAll);
        if (o) return {
            root: e,
            phase: t || r || ""
        };
        if (e && "object" == typeof e) {
            let n = e,
                i = n.root && ("function" == typeof n.root.querySelector || "function" == typeof n.root.querySelectorAll) ? n.root : null,
                a = n.workflow || n.action || n.feature || "",
                l = n.intent || n.mode || "",
                c = n.phase || t || r || a || l || "selector",
                s = [c, a ? "workflow:" + a : "", l ? "intent:" + l : ""].filter(Boolean).join("|");
            return {
                root: i,
                phase: s
            }
        }
        return {
            root: null,
            phase: e || t || r || ""
        }
    } catch (u) {
        return {
            root: null,
            phase: t || r || ""
        }
    }
}

function vgQuerySelector(e, t = null, r = "") {
    let o = vgDiagnosticSelectorOptions(t, r, "selector");
    return vgNativeQuery(e, o.root, !1, o.phase)
}

function vgQuerySelectorAll(e, t = null, r = "") {
    let o = vgDiagnosticSelectorOptions(t, r, "selector_all");
    return vgNativeQuery(e, o.root, !0, o.phase)
}

function vgQueryOptionalSelector(e, t = null, r = "optional_probe") {
    let o = vgDiagnosticSelectorOptions(t, r, "optional_probe");
    return vgNativeQuery(e, o.root, !1, "optional:" + o.phase)
}

function vgQueryOptionalSelectorAll(e, t = null, r = "optional_probe") {
    let o = vgDiagnosticSelectorOptions(t, r, "optional_probe_all");
    return vgNativeQuery(e, o.root, !0, "optional:" + o.phase)
}

function vgReportCaughtError(e, t = "caught_error") {
    try {
        if (!e || !0 !== e.vgDeveloperActionRequired) return !1;
        let r = vgDiagnosticStack(e),
            o = vgDiagnosticSourceFromStack(r),
            n = e && e.name ? e.name : "CaughtError",
            i = e && e.message ? e.message : String(e || "Caught script error");
        if (!vgShouldReportScriptProblem("caught_error", n, i, o.file, r)) return !1;
        return vgReportDiagnostic(Object.assign({
            type: "caught_error",
            phase: t,
            name: n,
            message: i,
            stack: r
        }, o))
    } catch (a) {
        return !1
    }
}
window.VG_DIAGNOSTIC_STATE || (window.VG_DIAGNOSTIC_STATE = {
    sent: Object.create(null),
    selectorMisses: Object.create(null),
    minuteWindowStartedAt: 0,
    minuteCount: 0,
    pageReadySeenAt: 0,
    navigationStartedAt: Date.now(),
    navigationKey: "",
    lastSelectorFlushAt: 0
}), void 0 === window.VG_WORKER_CONTROLLED_TAB && (window.VG_WORKER_CONTROLLED_TAB = !1);
try {
    document.addEventListener("DOMContentLoaded", vgDiagnosticUpdatePageState, {
        once: !1,
        passive: !0
    }), window.addEventListener("load", vgDiagnosticUpdatePageState, {
        once: !1,
        passive: !0
    })
} catch (e) {}

function vgRandInt(e, t) {
    return e = Math.ceil(Number(e) || 0), (t = Math.floor(Number(t) || e)) < e && (t = e), Math.floor(Math.random() * (t - e + 1)) + e
}

function vgSleep(e) {
    return new Promise(t => setTimeout(t, Math.max(0, Number(e) || 0)))
}

function vgNaturalDelay(e = 700, t = 1800, r = 1) {
    return vgRandInt(e, t) * Math.max(.2, Number(r) || 1)
}

function vgSafeGetById(e) {
    try {
        return document.getElementById(e) || null
    } catch (t) {
        return null
    }
}

function vgSetDisplayById(e, t) {
    let r = vgSafeGetById(e);
    if (!r || !r.style) return !1;
    try {
        return r.style.display = t, !0
    } catch (o) {
        return !1
    }
}

function vgAddClassById(e, t) {
    let r = vgSafeGetById(e);
    if (!r || !r.classList) return !1;
    try {
        return r.classList.add(t), !0
    } catch (o) {
        return !1
    }
}

function vgRemainingMs() {
    try {
        return Math.max(0, Number(remainingTime) || 0)
    } catch (e) {
        return 0
    }
}

function vgCanUseTime(e, t = 2500) {
    let r = vgRemainingMs();
    return 0 === r || r > (Number(e) || 0) + (Number(t) || 0)
}

function vgSafeRuntimeMessage(e) {
    try {
        if ("undefined" == typeof chrome || !chrome.runtime || "function" != typeof chrome.runtime.sendMessage) return !1;
        let t = chrome.runtime.sendMessage(e, () => {
            try {
                chrome.runtime.lastError
            } catch (e) {}
        });
        return t && "function" == typeof t.then && t.catch(() => {}), !0
    } catch (r) {
        return !1
    }
}

function vgStorageLocalGet(e, t = 5e3) {
    return new Promise(r => {
        let o = !1,
            n = e => {
                o || (o = !0, i && clearTimeout(i), r(e || {}))
            },
            i = setTimeout(() => n({}), Math.max(1500, Number(t) || 5e3));
        try {
            if ("undefined" == typeof chrome || !chrome.storage || !chrome.storage.local) {
                n({});
                return
            }
            chrome.storage.local.get(e, e => {
                try {
                    chrome.runtime.lastError
                } catch (t) {}
                n(e || {})
            })
        } catch (a) {
            n({})
        }
    })
}

function vgStorageLocalSet(e, t = 5e3) {
    return new Promise(r => {
        let o = !1,
            n = e => {
                o || (o = !0, i && clearTimeout(i), r(Boolean(e)))
            },
            i = setTimeout(() => n(!1), Math.max(1500, Number(t) || 5e3));
        try {
            if ("undefined" == typeof chrome || !chrome.storage || !chrome.storage.local) {
                n(!1);
                return
            }
            chrome.storage.local.set(e, () => {
                try {
                    chrome.runtime.lastError
                } catch (e) {}
                n(!0)
            })
        } catch (a) {
            n(!1)
        }
    })
}

function vgSafeRestartWorker(e = 0) {
    let t = () => {
            try {
                if ("function" == typeof restartWorkerSession) return restartWorkerSession(), !0
            } catch (e) {}
            try {
                return navigateWorkerTab(`${MainUrl}/worker/start`), !0
            } catch (t) {}
            return !1
        },
        r = Math.max(0, Number(e) || 0);
    return r > 0 ? (setTimeout(t, r), !0) : t()
}
async function vgStartFetchJsonWithTimeout(e, t = {}, r = 1e4) {
    let o = null,
        n = null;
    try {
        let i = Object.assign({}, t);
        "undefined" != typeof AbortController && (o = new AbortController, i.signal = o.signal, n = setTimeout(() => {
            try {
                o.abort()
            } catch (e) {}
        }, Math.max(3e3, Number(r) || 1e4)));
        let a = await fetch(e, i);
        if (!a || !a.ok) throw Error("http_error_" + (a ? a.status : "unknown"));
        return await a.json()
    } finally {
        n && clearTimeout(n)
    }
}

function vgRecoverFromClientError(e = "client_error") {
    try {
        if (!vgDiagnosticWorkerActive()) return;
        let t = Date.now(),
            r = window.VG_SAFE_RUNTIME || {};
        r.errorBurst = Array.isArray(r.errorBurst) ? r.errorBurst.filter(e => t - e < 15e3) : [], r.errorBurst.push(t), window.VG_SAFE_RUNTIME = r;
        let o = () => {
                try {
                    if ("function" == typeof restartWorkerSession) {
                        restartWorkerSession();
                        return
                    }
                } catch (e) {}
                try {
                    navigateWorkerTab(`${MainUrl}/worker/start`)
                } catch (t) {}
            },
            n = String(e || "").includes("Extension context invalidated") || String(e || "").includes("Maximum call stack") || r.errorBurst.length >= 3,
            i = vgDiagnosticWorkerActive();
        if (n && i) {
            if (vgFlushSelectorDiagnostics("client_recovery", !0), t - r.lastAutoRecoverAt < 8e3) return;
            r.lastAutoRecoverAt = t, setTimeout(o, vgNaturalDelay(1200, 2600));
            return
        }
        if (t - r.lastAutoRecoverAt < 8e3) return;
        if (r.lastAutoRecoverAt = t, "www.youtube.com" === window.location.hostname && window.location.pathname.includes("/oops") && i) {
            setTimeout(o, vgNaturalDelay(2500, 4500));
            return
        }
        "function" == typeof showWorkerNotification && i && t - r.lastRecoveryNoticeAt > 15e3 && (r.lastRecoveryNoticeAt = t, showWorkerNotification(1, "warning", "wait_moment", 0, "", null, !1).catch(() => {}))
    } catch (a) {}
}

function vgIsRecoverableExtensionErrorSource(e = "", t = "", r = "") {
    let o = vgSanitizeDiagnosticFile(e),
        n = String(t || ""),
        i = vgSanitizeDiagnosticStack(r);
    return !!(/assets\/js\/core\//.test(o) || /extension:\/\/assets\/js\/core\//.test(i) || n.includes("Extension context invalidated") || n.includes("Maximum call stack") && (/assets\/js\//.test(o) || /extension:\/\/assets\/js\//.test(i)))
}

function readCookie(e) {
    try {
        let t = e + "=",
            r = document.cookie.split(";");
        for (let o = 0; o < r.length; o++) {
            let n = r[o];
            for (;
                " " == n.charAt(0);) n = n.substring(1, n.length);
            if (0 == n.indexOf(t)) return n.substring(t.length, n.length)
        }
        return null
    } catch (i) {
        return null
    }
}

function navigateWorkerTab(e) {
    let t = Date.now();
    if (t - lastUpdateTs < 3e3 || !e || "string" != typeof e) return !1;
    try {
        vgMarkWorkerControlled("navigateWorkerTab");
        let r = new URL(e, window.location.href);
        if ("http:" !== r.protocol && "https:" !== r.protocol) return !1;
        return lastUpdateTs = t, vgSafeRuntimeMessage({
            cmd: "updateTab",
            url: r.href
        }), forceStop = !0, !0
    } catch (o) {
        return !1
    }
}
async function translateMessage(e, t = null) {
    if ("string" == typeof e && e.includes(" ")) return e;
    let r = "langData",
        o = "langCode",
        n = "langExpireTime",
        i = `${MainUrl}/api/lang/full`,
        a = {
            method: "GET",
            cache: "no-cache",
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        },
        l = e => btoa(unescape(encodeURIComponent(e))),
        c = e => decodeURIComponent(escape(atob(e))),
        s = {
            get: e => vgStorageLocalGet(e, 4500),
            set: e => vgStorageLocalSet(e, 4500)
        },
        u = (e, t) => (e = null == e ? "" : String(e), Array.isArray(t) || (t = [t]), e.replace(/\{(\d+)\}/g, (e, r) => void 0 !== t[r] ? t[r] : e)),
        g = Date.now(),
        {
            [r]: d,
            [o]: f = "en",
            [n]: p
        } = await s.get([r, o, n]),
        m = {};
    try {
        d && (m = JSON.parse(c(d)))
    } catch (v) {
        console.warn("Failed to decode stored langData:", v), m = {}
    }
    let y = async e => {
        let t = {};
        t[f] = e, await s.set({
            [r]: l(JSON.stringify(t)),
            [o]: f,
            [n]: g + 864e5
        })
    }, h = e => null == e ? "" : "string" == typeof e ? e : JSON.stringify(e), w = async () => {
        let r = null,
            o = null;
        try {
            let n = Object.assign({}, a);
            "undefined" != typeof AbortController && (r = new AbortController, n.signal = r.signal, o = setTimeout(() => {
                try {
                    r.abort()
                } catch (e) {}
            }, 6500));
            let l = await fetch(`${i}?lang_code=${f}`, n);
            if (!l.ok) throw Error("Fetch failed");
            let s = await l.json();
            if (!s?.langData) return e;
            {
                let g = JSON.parse(c(s.langData));
                await y(g);
                let d = g[e] || e;
                return d = h(d), null !== t ? u(d, t) : d
            }
        } catch (p) {
            return console.error("Fetch language failed:", p), e
        } finally {
            o && clearTimeout(o)
        }
    };
    try {
        if (!p || g > p) return await w();
        let k = m[f];
        if (k && k[e]) {
            let S = h(k[e]);
            return null !== t ? u(S, t) : S
        }
        return await w()
    } catch (b) {
        return console.error("translateMessage error:", b), e
    }
}

function vgWorkerNotificationSpecificPageMessage(e) {
    try {
        let t = String(e || ""),
            r = new Set(["please_wait_page_is_loading", "processing"]);
        if (!r.has(t)) return t;
        let o = window.location && window.location.hostname ? window.location.hostname : "",
            n = window.location && window.location.href ? window.location.href : "";
        if (n.includes("signin_prompt")) return "please_select_channel";
        if ("accounts.google.com" === o || "gds.google.com" === o) return "setting_up_your_youtube_account";
        if ("support.google.com" === o) return "you_not_have_youtube account";
        return t
    } catch (i) {
        return e
    }
}

function vgWorkerNotificationPageLooksReady() {
    try {
        let e = window.location && window.location.hostname ? window.location.hostname : "",
            t = window.location && window.location.href ? window.location.href : "";
        if ("loading" === document.readyState || !document.body && !document.documentElement) return !1;
        if ("accounts.google.com" === e || "gds.google.com" === e || "support.google.com" === e || t.includes("signin_prompt")) return !0;
        if ("www.youtube.com" === e || "youtube.com" === e || e.endsWith(".youtube.com")) return Boolean(document.body || vgQuerySelector("ytd-app", document, "notification_ready_check") || vgQuerySelector("#movie_player", document, "notification_ready_check") || vgQuerySelector("#shorts-player", document, "notification_ready_check") || vgQuerySelector("video", document, "notification_ready_check"));
        return "interactive" === document.readyState || "complete" === document.readyState
    } catch (r) {
        return !1
    }
}

function vgWorkerNotificationShouldSkip(e, t) {
    try {
        let r = window.VG_NOTIFICATION_STATE || (window.VG_NOTIFICATION_STATE = {}),
            o = Date.now(),
            n = String(e || ""),
            i = String(r.currentMessage || ""),
            a = o - (Number(r.currentAt) || 0);
        if ("please_wait_page_is_loading" === n && vgWorkerNotificationPageLooksReady()) return !0;
        let l = new Set(["setting_up_your_youtube_account", "please_select_channel", "you_not_have_youtube account", "worker_is_ready", "waiting_for_video_playback", "keep_watching_video"]),
            c = new Set(["setting_up_your_youtube_account", "please_select_channel", "you_not_have_youtube account", "worker_is_ready"]);
        if ("please_wait_page_is_loading" === n && l.has(i) && a < 15e3 || "processing" === n && c.has(i) && a < 15e3 || "please_wait_page_is_loading" === n && null === t && window.timerRunning && a < 6e4) return !0;
        return !1
    } catch (s) {
        return !1
    }
}

function vgWorkerNotificationRemember(e, t) {
    try {
        let r = window.VG_NOTIFICATION_STATE || (window.VG_NOTIFICATION_STATE = {});
        r.currentMessage = String(e || ""), r.currentType = String(t || ""), r.currentAt = Date.now(), "please_wait_page_is_loading" === r.currentMessage && (r.lastLoadingAt = r.currentAt)
    } catch (o) {}
}
async function showWorkerNotification(e, t, r, o, n, i = null, a = !1) {
    try {
        if (r = vgWorkerNotificationSpecificPageMessage(r), vgWorkerNotificationShouldSkip(r, i)) return !0;
        let l = await translateMessage(r, o);
        if (!l) throw Error("translateMessage fetch failed");
        if (vgWorkerNotificationShouldSkip(r, i)) return !0;
        if (window.location.hostname !== parsedUrl.hostname) {
            let c = {
                    success: "rgb(65, 185, 96)",
                    danger: "rgba(255, 69, 0, 0.9)",
                    error: "rgba(255, 69, 0, 0.9)",
                    info: "rgb(30, 144, 255)",
                    warning: "rgb(255, 193, 7)",
                    default: "rgb(123, 104, 238)"
                },
                s = c[t] || c.default,
                u = 1 == e ? '<span id="loadingconsole" class="vgloadingDots"></span>' : '<strong id="loadingconsole"></strong>',
                g = document.getElementById("ViewGripConsole"),
                d = vgQuerySelector("style[data-vgnotif]"),
                f = !g;
            g || ((g = document.createElement("div")).id = "ViewGripConsole", d || ((d = document.createElement("style")).setAttribute("data-vgnotif", "true"), d.textContent = "#ViewGripConsole{position:fixed;z-index:9999;bottom:10px;left:50%;transform:translateX(-50%);background:#222;text-align:left;box-shadow:0 4px 10px rgba(0,0,0,.3);font-family:'Roboto Mono','Courier New',Courier,monospace;padding:10px;font-size:14px;color:#fff;border-radius:10px;max-height:200px;opacity:1;transition:opacity .3s ease-out}@media (min-width:768px){#ViewGripConsole{width:50%}}@media (max-width:768px){#ViewGripConsole{width:90%}}#TextMessage,#VGLoginWarning{display:block;border-radius:2px;overflow-x:auto;padding:5px;font-size:14px;margin:3px 15px}#TextMessage{background-color:rgba(57,57,57,.44);border-left:2px solid #5affd6;white-space:pre-wrap}#VGLoginWarning{display:none;background:#d4a017;color:#000;border-left:2px solid #000;text-align:center}.viewgrip{margin-left:15px;color:#fff}.vgloadingDots::after{content:\".\";animation:1s steps(5,end) infinite loadingDots;font-size:14px;color:#cdee69;font-family:Consolas,monospace}@keyframes loadingDots{0%,20%{color:transparent;text-shadow:.25em 0 0 transparent,.5em 0 0 transparent}40%{color:#cdee69;text-shadow:.25em 0 0 transparent,.5em 0 0 transparent}60%{text-shadow:.25em 0 0 #cdee69,.5em 0 0 transparent}100%,80%{text-shadow:.25em 0 0 #cdee69,.5em 0 0 #cdee69}}strong::after{content:\"_\";opacity:0;animation:1s infinite cursor;color:#cdee69}@keyframes cursor{0%,100%,40%{opacity:0}50%,90%{opacity:1}}.countdown-timer{position:relative;height:3px;background-color:rgba(157,160,164,.7);border-radius:3px;margin:10px 15px 0;overflow:visible}.timer-dot,.timer-progress{position:absolute;background-color:red}.timer-progress{top:0;left:0;height:100%;width:0;border-radius:3px;transition:width .1s linear}.timer-dot{right:0;top:50%;width:10px;height:10px;border-radius:50%;transform:translate(50%,-50%);display:none}.vg-fade-in{animation:.45s ease-out forwards vgFadeIn}@keyframes vgFadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}.vg-console-appear{animation:.45s ease-out forwards vgConsoleAppear}@keyframes vgConsoleAppear{from{opacity:0;transform:translate(-50%,25px)}to{opacity:1;transform:translate(-50%,0)}}", document.head.appendChild(d)), document.documentElement.appendChild(g), g.dataset.firstload = "1"), f && (g.classList.add("vg-console-appear"), setTimeout(() => g.classList.remove("vg-console-appear"), 600));
            let p = vgQuerySelector(".countdown-timer", g, "showWorkerNotification") && null === i;
            if (p) {
                let m = vgQuerySelector("#TextMessage", g, "showWorkerNotification");
                m && (m.innerHTML = `${l} ${u} ${n}`, m.style.color = s);
                let v = vgQuerySelector("#VGLoginWarning", g, "showWorkerNotification");
                return v && (v.style.display = a ? "block" : "none"), !0
            }
            let y = "⚠️ " + await translateMessage("login_youtube_warning", "");
            g.innerHTML = `
                <span class="viewgrip">@viewgrip:</span>
                <div id="VGLoginWarning" style="display:${a?"block":"none"}">
                    ${y}
                </div>
                <span id="TextMessage" style="color: ${s}">${l} ${u} ${n}</span>
                ${i&&i>0?'<div class="countdown-timer"><div class="timer-progress"><div class="timer-dot"></div></div></div>':""}
         `;
            let h = vgQuerySelector("#TextMessage", g, "showWorkerNotification"),
                w = vgQuerySelector("#VGLoginWarning", g, "showWorkerNotification"),
                k = vgQuerySelector(".countdown-timer", g, "showWorkerNotification");
            if ("1" === g.dataset.firstload && (h && h.classList && (h.classList.add("vg-fade-in"), setTimeout(() => h.classList.remove("vg-fade-in"), 600)), g.dataset.firstload = "0"), w && (w.classList.add("vg-fade-in"), setTimeout(() => w.classList.remove("vg-fade-in"), 500)), k && (k.classList.add("vg-fade-in"), setTimeout(() => k.classList.remove("vg-fade-in"), 500)), i && i > 0) {
                timerRunning && (clearTimeout(countdownTimer), timerRunning = !1);
                let S = vgQuerySelector(".timer-progress", g, "showWorkerNotification"),
                    b = vgQuerySelector(".timer-dot", g, "showWorkerNotification");
                if (!S || !b) return console.warn("Timer elements not found, skipping timer"), !0;

                function _() {
                    if (paused) return;
                    let e = Date.now(),
                        t = e - timerStartTime,
                        r = Math.min(t / timerDuration, 1);
                    if (remainingTime = timerDuration - t, S.style.width = `${100*r}%`, r < 1) {
                        let o = Math.min(100, timerDuration - t);
                        countdownTimer = setTimeout(_, o)
                    } else timerRunning = !1, openVideoChannel()
                }
                b.style.display = "block", timerDuration = 1e3 * i, timerStartTime = Date.now(), timerRunning = !0, paused = !1, elapsedWhenPaused = 0, window.pauseVGCountdown = function() {
                    timerRunning && !paused && (paused = !0, clearTimeout(countdownTimer), elapsedWhenPaused = Date.now() - timerStartTime)
                }, window.resumeVGCountdown = function() {
                    paused && (paused = !1, timerStartTime = Date.now() - elapsedWhenPaused, _())
                }, _()
            }
            return vgWorkerNotificationRemember(r, t), !0
        }
        return new Promise(o => {
            let n = vgQuerySelector("#loader-wrapper #loader") || vgQuerySelector("#loader-wrapper #infoloader"),
                i = document.getElementById("message");
            if (!i) {
                o(!0);
                return
            }
            i.style.transition = "opacity 0.5s ease, transform 0.5s ease", i.style.opacity = "0", i.style.transform = "translateY(-10px)", setTimeout(() => {
                switch (i.textContent = l, i.style.opacity = "1", i.style.transform = "translateY(0)", t) {
                    case "error":
                        i.style.color = "#ec2121";
                        break;
                    case "warning":
                        i.style.color = "#cc8e17";
                        break;
                    case "success":
                        i.style.color = "#15ac10";
                        break;
                    default:
                        i.style.color = "#686262"
                }
                n && (n.id = 0 === e ? "infoloader" : "loader"), vgWorkerNotificationRemember(r, t), o(!0)
            }, 200)
        })
    } catch (D) {
        return vgReportCaughtError(D, "showWorkerNotification"), vgDiagnosticWorkerActive() && setTimeout(() => {
            try {
                "function" == typeof restartWorkerSession ? restartWorkerSession() : navigateWorkerTab(MainUrl + "/worker/start")
            } catch (e) {}
        }, 1200), !1
    }
}

function vgNotifySafe(e, t, r, o, n, i = null, a = !1, l = 7500, c = !0) {
    return new Promise(s => {
        let u = !1,
            g = e => {
                u || (u = !0, d && clearTimeout(d), s(e))
            },
            d = setTimeout(() => g(c), Math.max(1500, Number(l) || 7500));
        try {
            let f = showWorkerNotification(e, t, r, o, n, i, a);
            f && "function" == typeof f.then ? f.then(g).catch(() => g(c)) : g(!1 !== f && c)
        } catch (p) {
            g(c)
        }
    })
}
async function openVideoChannel() {
    try {
        var e;
        clearInterval(ViolationInterval), clearInterval(popupInterval), ViolationInterval = null, stopAllWorkerProcesses(), await g(1500), forceStop = !0, await vgStorageLocalSet({
            AjaxData: {
                video_id: null,
                backup_url: null,
                viewing_method: null,
                keyword: null,
                like: null,
                subscribe: null,
                comment: null,
                comment_liking: null,
                duration: null
            }
        }, 5e3);
        let t = await dismissYouTubeBackdrop();
        if (t) return await g(1500 + 1500 * Math.random()), handleWatchTimeComplete();
        if (.55 > Math.random()) return await g(600 + 1200 * Math.random()), handleWatchTimeComplete();
        let r = {
                "watch?v=": "#above-the-fold #top-row .ytd-video-owner-renderer",
                "/shorts": "#channel-container #channel-info #avatar"
            },
            o = Object.keys(r).find(e => location.href.includes(e)),
            n = o ? vgQuerySelector(r[o]) : null;
        if (!n) return handleWatchTimeComplete();
        let i = await vgNotifySafe(1, "info", "visit_video_channel", 0, "", null, !1, 6500, !0);
        if (!i) return handleWatchTimeComplete();
        await g(500 + 800 * Math.random()), location.href.includes("/shorts") || await (e = n, new Promise(t => {
            try {
                if (!e || !e.getBoundingClientRect) {
                    t();
                    return
                }
                let r = e.getBoundingClientRect(),
                    o = window.pageYOffset || document.documentElement.scrollTop,
                    n = r.top + o - window.innerHeight / 2 + e.offsetHeight / 2,
                    i = !1,
                    a = () => {
                        i || (i = !0, t())
                    };
                $("html, body").stop(!0, !0).animate({
                    scrollTop: n
                }, 1200, a), setTimeout(a, 1500)
            } catch (l) {
                t()
            }
        }));
        let a = await SimulateClick(n);
        if (!a) return handleWatchTimeComplete();
        let l = waitForYouTubePageUpdate(),
            c = await dismissYouTubeBackdrop();
        if (c) return await g(1500 + 1500 * Math.random()), handleWatchTimeComplete();
        let s = await l;
        if (s) return await g(1500 + 1500 * Math.random()), handleWatchTimeComplete();
        if (!forceStop) return;
        return await vgNotifySafe(0, "error", "oops_something_seems_wrong", 0, "", null, !1, 6500, !0), handleWatchTimeComplete()
    } catch (u) {
        return handleWatchTimeComplete()
    }

    function g(e) {
        return new Promise(t => setTimeout(t, e))
    }
}

function setWorkerOverlay(e) {
    if ("www.youtube.com" === window.location.hostname) {
        if (vgQuerySelector("#vgoverlay")) 1 == e ? $("#vgoverlay").show() : $("#vgoverlay").hide();
        else try {
            vgQuerySelector("html").insertAdjacentHTML("beforeend", '<style>.vgoverlay{-webkit-animation:1s fadein;-moz-animation:1s fadein;-o-animation:1s fadein;animation:1s fadein}.vgspinner-wrapper{min-width:100%;min-height:100%;height:100%;top:0;left:0;background:rgb(54 54 54 / 68%);position:fixed;z-index:9998}.vgspinner-text{position:absolute;top:41.5%;left:47%;margin:16px 0 0 35px;font-size:9px;font-family:Arial;color:#ffff;letter-spacing:1px;font-weight:700}.vgspinner{top:30%;width:48px;height:48px;display:block;margin:20px auto;position:relative;border:3px solid #e10c0c;border-radius:50%;box-sizing:border-box;animation:2s linear infinite animloader}.vgspinner::after{content:"";box-sizing:border-box;width:6px;height:24px;background:#fff;transform:rotate(-45deg);position:absolute;bottom:-20px;left:46px}@keyframes animloader{0%,100%{transform:translate(-10px,-10px)}25%{transform:translate(-10px,10px)}50%{transform:translate(10px,10px)}75%{transform:translate(10px,-10px)}}</style><div id="vgoverlay" class="vgoverlay"><div class="vgspinner-wrapper"><span class="vgspinner-text">WAIT A MOMENT</span><span class="vgspinner"></span></div></div>')
        } catch (t) {}
    }
}

function handleWatchTimeComplete() {
    if (!startVerification) return;
    startVerification = !1;
    let e = "www.youtube.com" === window.location.hostname,
        t = e ? 3e3 : 6e3,
        r = {
            AjaxData: {
                video_id: null,
                backup_url: null,
                video_type: null,
                viewing_method: null,
                keyword: null,
                like: null,
                subscribe: null,
                comment: null,
                comment_liking: null,
                duration: null
            }
        },
        o = () => {
            startGetVideo = !0, setTimeout(() => {
                forceStop && (e ? navigateWorkerTab(`${MainUrl}/worker/start`) : requestNextVideo())
            }, t)
        },
        n = () => {
            forceStop && vgNotifySafe(0, "error", "server_error_occurred", 0, "", null, !1, 6500, !0).finally(() => {
                forceStop && vgSafeRestartWorker(5e3)
            })
        };
    async function i(e, t = 0) {
        try {
            let r = await vgStartFetchJsonWithTimeout(MainUrl + "/api/worker/challenge", {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                cache: "no-store"
            }, 1e4);
            if (!r || !r.challenge) throw Error("challenge_error");
            let o = await tx_generateSignature(r.challenge, r.timestamp, r.salt);
            $.ajax({
                url: MainUrl + "/api/worker/verification",
                type: "GET",
                cache: !1,
                data: {
                    token: e,
                    challenge: r.challenge,
                    timestamp: r.timestamp,
                    salt: r.salt,
                    signature: r.signature,
                    client_proof: r.client_proof,
                    fingerprint: o.fingerprint,
                    pow: o.pow
                },
                timeout: 9e3,
                dataType: "json",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                success: a,
                error: function() {
                    forceStop && (t < 5 ? vgNotifySafe(1, "warning", "trying_to_redial", 0, "", null, !1, 6500, !0).finally(() => {
                        forceStop && setTimeout(() => i(e, t + 1), vgNaturalDelay(2500, 5500))
                    }) : n())
                }
            })
        } catch (l) {
            t < 5 && forceStop ? setTimeout(() => i(e, t + 1), vgNaturalDelay(2500, 5500)) : n()
        }
    }
    async function a(e) {
        if (e && "object" == typeof e || (e = {
                status: "error",
                action: "reload",
                message: "server_error_occurred"
            }), !forceStop) return;
        await vgNotifySafe(0, e.status, e.message, 0, "", null, !1, 7500, !0);
        let t = await vgStorageLocalSet(r, 5e3);
        if (!t) {
            o();
            return
        }
        "new_request" === e.action ? (startGetVideo = !0, setTimeout(() => {
            forceStop && requestNextVideo()
        }, vgNaturalDelay(1200, 2400))) : o()
    }(async () => {
        try {
            if (!forceStop) return;
            await vgNotifySafe(1, "info", "verifying", 0, "", null, !1, 6500, !0);
            let e = await vgStorageLocalGet("token", 5e3);
            if (await vgSleep(vgNaturalDelay(900, 1800)), !e || !e.token) {
                navigateWorkerTab(MainUrl + "/worker/start");
                return
            }
            i(e.token, 0)
        } catch (t) {
            n()
        }
    })()
}

function requestNextVideo() {
    if (!startGetVideo) return;
    startGetVideo = !1;
    let e = {
        AjaxData: {
            video_id: null,
            backup_url: null,
            video_type: null,
            viewing_method: null,
            keyword: null,
            like: null,
            subscribe: null,
            comment: null,
            comment_liking: null,
            duration: null
        }
    };
    async function t(e, n = 0) {
        try {
            let i = await vgStartFetchJsonWithTimeout(MainUrl + "/api/worker/challenge", {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                cache: "no-store"
            }, 1e4);
            if (!i || !i.challenge) throw Error("challenge_error");
            let a = await tx_generateSignature(i.challenge, i.timestamp, i.salt);
            $.ajax({
                url: MainUrl + "/api/worker/fetch_video",
                type: "GET",
                cache: !1,
                data: {
                    token: e,
                    version: Manifest.version,
                    challenge: i.challenge,
                    timestamp: i.timestamp,
                    salt: i.salt,
                    signature: i.signature,
                    client_proof: i.client_proof,
                    fingerprint: a.fingerprint,
                    pow: a.pow
                },
                timeout: 9e3,
                dataType: "json",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                success: r,
                error: function() {
                    forceStop && (n < 4 ? vgNotifySafe(1, "warning", "trying_to_redial", 0, "", null, !1, 6500, !0).finally(() => {
                        forceStop && setTimeout(() => t(e, n + 1), vgNaturalDelay(2500, 5500))
                    }) : o())
                }
            })
        } catch (l) {
            n < 4 && forceStop ? setTimeout(() => t(e, n + 1), vgNaturalDelay(2500, 5500)) : o()
        }
    }
    async function r(e) {
        e && "object" == typeof e || (e = {
            status: "error",
            action: "standby",
            message: "server_error_occurred",
            value: 0
        });
        let t = "www.youtube.com" === window.location.hostname,
            r = t ? 3e3 : 6e3,
            o = "error" === e.status || "warning" === e.status ? 0 : 1,
            n = () => {
                startGetVideo = !0, setTimeout(() => {
                    forceStop && (t ? navigateWorkerTab(`${MainUrl}/worker/start`) : requestNextVideo())
                }, r)
            };
        switch (e.action) {
            case "auth": {
                let i = vgSetDisplayById("loader-wrapper", "none"),
                    a = vgAddClassById("login-wrapper", "active"),
                    l = await translateMessage(e.message || "please_relogin"),
                    c = vgSafeGetById("login-alert");
                c && (c.textContent = l, c.style && (c.style.display = "block")), i || a || vgNotifySafe(0, "error", e.message || "please_relogin", e.value || 0, "", null, !1, 6500, !0).finally(() => {
                    setTimeout(() => navigateWorkerTab(`${MainUrl}/worker/start`), vgNaturalDelay(2500, 4500))
                });
                return
            }
            case "reload":
            case "standby":
            default:
                n();
                break;
            case "update":
                setTimeout(showExtensionUpdateNotice, 2e3);
                break;
            case "re_request":
                startGetVideo = !0, setTimeout(() => {
                    forceStop && requestNextVideo()
                }, 5e3);
                break;
            case "start":
                if (e.command && e.command.url) {
                    let s = null;
                    try {
                        s = new URL(e.command.url, window.location.href).href
                    } catch (u) {
                        s = null
                    }
                    if (!s) {
                        n();
                        break
                    }
                    let g = await vgStorageLocalSet({
                        AjaxData: {
                            video_id: e.command.video_id,
                            backup_url: e.command.backup_url,
                            video_type: e.command.video_type,
                            viewing_method: e.command.viewing_method,
                            keyword: e.command.keyword,
                            like: e.command.like,
                            subscribe: e.command.subscribe,
                            comment: e.command.comment,
                            comment_liking: e.command.comment_liking,
                            duration: e.command.duration
                        }
                    }, 5e3);
                    if (!g) {
                        n();
                        break
                    }
                    setTimeout(() => {
                        let e = navigateWorkerTab(s);
                        e || n()
                    }, vgNaturalDelay(1200, 2400))
                } else n()
        }
        forceStop && (vgNotifySafe(o, e.status, e.message, e.value, "", null, !1, 6500, !0), console.clear())
    }

    function o() {
        startGetVideo = !0, forceStop && vgNotifySafe(1, "error", "server_error_occurred", 0, "", null, !1, 6500, !0).finally(() => {
            forceStop && vgSafeRestartWorker(5e3)
        })
    }
    setTimeout(() => {
        forceStop && vgNotifySafe(1, "info", "fetching_campaign_data", 0, "", null, !1, 6500, !0).finally(() => void setTimeout(async () => {
            try {
                await vgStorageLocalSet(e, 5e3);
                let r = await vgStorageLocalGet("token", 5e3);
                if (!r || !r.token) {
                    navigateWorkerTab(MainUrl + "/worker/start");
                    return
                }
                setTimeout(() => t(r.token, 0), vgNaturalDelay(1200, 2400))
            } catch (o) {
                navigateWorkerTab(MainUrl + "/worker/start")
            }
        }, 1500))
    }, 1500)
}

function showExtensionUpdateNotice() {
    let e = document.getElementById("update"),
        t = document.getElementById("loader-wrapper");
    t && (t.style.display = "none"), e && (e.style.display = "block")
}

function showKeepWatchingNotice() {
    forceStop && remainingTime > 2e3 && setTimeout(function() {
        forceStop && showWorkerNotification(0, "info", "keep_watching_video", 0, "", null, !readCookie("SID"))
    }, 500)
}
window.VG_SAFE_RUNTIME.errorAttached || (window.VG_SAFE_RUNTIME.errorAttached = !0, window.addEventListener("error", function(e) {
    try {
        let t = vgSanitizeDiagnosticFile(e.filename || ""),
            r = String(e.message || ""),
            o = e.error || null;
        if (!t && !o) return;
        let n = vgDiagnosticExistingStack(o),
            i = t ? {
                file: t,
                line: e.lineno || "",
                column: e.colno || ""
            } : vgDiagnosticSourceFromStack(n),
            a = o && o.name ? o.name : "WindowError",
            l = r || o && o.message || "";
        if (!l && !n || !vgIsRecoverableExtensionErrorSource(i.file || t, l, n)) return;
        vgShouldReportScriptProblem("fatal_error", a, l, i.file, n) && vgReportDiagnostic({
            type: "fatal_error",
            phase: "window.error",
            name: a,
            message: l || "Unhandled extension error",
            file: i.file,
            line: i.line || "",
            column: i.column || "",
            stack: n
        }), e.preventDefault(), vgRecoverFromClientError(l || "window_error")
    } catch (c) {}
}, !0), window.addEventListener("unhandledrejection", function(e) {
    try {
        let t = e.reason,
            r = String(t && (t.stack || t.message) || t || ""),
            o = vgDiagnosticExistingStack(t),
            n = vgDiagnosticSourceFromStack(o),
            i = t && t.name ? t.name : "UnhandledRejection",
            a = t && t.message ? t.message : r;
        if (!o && !n.file || !vgIsRecoverableExtensionErrorSource(n.file, a, o)) return;
        vgShouldReportScriptProblem("unhandled_rejection", i, a || "Unhandled promise rejection", n.file, o) && vgReportDiagnostic(Object.assign({
            type: "unhandled_rejection",
            phase: "window.unhandledrejection",
            name: i,
            message: a || "Unhandled promise rejection",
            stack: o
        }, n)), e.preventDefault(), vgRecoverFromClientError(a || "unhandled_rejection")
    } catch (l) {}
}, !0)), window.pauseVGCountdown = function() {
    window.timerRunning && (window.paused = !0, clearTimeout(window.countdownTimer), window.elapsedWhenPaused = Date.now() - window.timerStartTime)
}, window.resumeVGCountdown = function() {
    window.paused && (window.paused = !1, window.timerStartTime = Date.now() - window.elapsedWhenPaused, "function" == typeof window.updateTimer && window.updateTimer())
}, window.VG_NOTIFICATION_STATE || (window.VG_NOTIFICATION_STATE = {
    currentMessage: "",
    currentType: "",
    currentAt: 0,
    lastLoadingAt: 0
}), $(document).ready(function() {
    $('[id^="StartWorker_"]').off("click.vgStartWorker").on("click.vgStartWorker", async function() {
        let e = await vgSafeRuntimeMessage({
            cmd: "openTab",
            url: MainUrl + "/worker/start"
        });
        if (e && !1 === e.ok && "already_running" === e.reason) {
            let t = await translateMessage("worker_running_warning", "");
            alert(t)
        }
    })
});