/*!
 * ViewGrip JavaScript Library
 * https://www.viewgrip.net/
 * Date: 2024-01-21T17:08Z
 */
let TimeoutPlayerStatus = 0,
    Repeattab = 0,
    ViolationInterval = 0,
    checkDeleted = 0,
    CheckPlayerInterval = 0,
    contentInteractionTimeout = 0,
    StartWorker = !0,
    forceStop = !0,
    pausedThreshold = 0,
    bufferingThreshold = 0,
    mutedThreshold = 0,
    popupInterval = 0,
    allowedDomains = ["accounts.google.com", "gds.google.com", "support.google.com", "www.youtube.com"];

function vgIsYouTubeHost() {
    return "www.youtube.com" === window.location.hostname || "youtube.com" === window.location.hostname
}

function vgIsYouTubeShortsPage() {
    return vgIsYouTubeHost() && window.location.pathname.startsWith("/shorts/")
}

function vgIsShortsInteractionContext() {
    try {
        if (vgIsYouTubeShortsPage()) return !0;
        if ("function" == typeof vgQueryOptionalSelector) return Boolean(vgQueryOptionalSelector("#shorts-player, ytd-reel-video-renderer[is-active], yt-reel-channel-bar-view-model, reel-action-bar-view-model", {
            workflow: "interaction",
            intent: "probe",
            phase: "vgIsShortsInteractionContext"
        }))
    } catch (e) {}
    return !1
}

function vgGetPrimaryYouTubePlayer() {
    try {
        if (vgIsYouTubeShortsPage()) return vgQuerySelector("#shorts-player") || vgQuerySelector("#movie_player");
        return vgQuerySelector("#movie_player") || vgQuerySelector("#shorts-player")
    } catch (e) {
        return null
    }
}

function vgIsYouTubeAdShowing(e = null) {
    try {
        let t = e || vgGetPrimaryYouTubePlayer();
        if (t && t.classList && t.classList.contains("ad-showing")) return !0;
        let r = e => {
                if (!e || !e.isConnected) return !1;
                let t = e.getBoundingClientRect();
                if (!t || t.width <= 0 || t.height <= 0) return !1;
                let r = window.getComputedStyle ? window.getComputedStyle(e) : null;
                return !r || "none" !== r.display && "hidden" !== r.visibility && "0" !== r.opacity
            },
            n = vgQuerySelectorAll(".video-ads .ytp-ad-player-overlay, .ytp-ad-text, .ytp-ad-preview-container, .ytp-ad-skip-button-container, .ytp-ad-overlay-container");
        for (let o of n)
            if (r(o)) return !0
    } catch (a) {}
    return !1
}

function vgResetPlaybackViolationThresholds() {
    try {
        bufferingThreshold = 0
    } catch (e) {}
    try {
        pausedThreshold = 0
    } catch (t) {}
}

function vgWaitUntilNoYouTubeAds(e = 9e4, t = 1200) {
    return new Promise(r => {
        if (!vgIsYouTubeHost() || vgIsYouTubeShortsPage()) return r(!0);
        let n = Date.now(),
            o = 0,
            a = setInterval(() => {
                if (!forceStop) {
                    clearInterval(a), r(!1);
                    return
                }
                let i = vgGetPrimaryYouTubePlayer(),
                    c = vgIsYouTubeAdShowing(i);
                if (c) vgResetPlaybackViolationThresholds(), o = 0;
                else if (i && (o || (o = Date.now()), Date.now() - o >= t)) {
                    clearInterval(a), r(!0);
                    return
                }
                Date.now() - n >= e && (clearInterval(a), r(!vgIsYouTubeAdShowing(i)))
            }, 350)
    })
}
let interactionSchedulerActive = !1,
    interactionRunning = !1,
    vgScrollGeneration = 0,
    vgInteractionScopeId = 0,
    vgMandatoryInteractionActive = !1,
    vgMandatoryInteractionPending = !1,
    vgInteractionTimers = new Set,
    vgInteractionStartTimer = 0;

function vgClearInteractionTimers() {
    vgInteractionTimers.forEach(e => {
        try {
            clearTimeout(e)
        } catch (t) {}
    }), vgInteractionTimers.clear()
}

function vgCancelInteractionSideEffects() {
    try {
        vgScrollGeneration++
    } catch (e) {}
    try {
        clearTimeout(contentInteractionTimeout)
    } catch (t) {}
    try {
        clearTimeout(TimeOutTyping)
    } catch (r) {}
    try {
        window.onscroll = null
    } catch (n) {}
    try {
        window.dispatchEvent(new CustomEvent("vg-interaction-cleanup", {
            detail: {
                generation: vgScrollGeneration
            }
        }))
    } catch (o) {}
    try {
        $("html, body").stop(!0, !0)
    } catch (a) {}
    try {
        $(window).stop(!0, !0)
    } catch (i) {}
    try {
        document.scrollingElement && $(document.scrollingElement).stop(!0, !0)
    } catch (c) {}
    try {
        document.documentElement && $(document.documentElement).stop(!0, !0)
    } catch (l) {}
    try {
        document.body && $(document.body).stop(!0, !0)
    } catch (u) {}
}

function vgBeginInteractionScope(e = "interaction") {
    return vgInteractionScopeId++, vgMandatoryInteractionPending = !1, vgMandatoryInteractionActive = !0, vgClearInteractionTimers(), vgCancelInteractionSideEffects(), {
        id: vgInteractionScopeId,
        label: e
    }
}

function vgEndInteractionScope(e) {
    if (e && e.id === vgInteractionScopeId) {
        vgClearInteractionTimers(), vgMandatoryInteractionActive = !1;
        try {
            $("html, body").stop(!0, !1)
        } catch (t) {}
    }
}

function vgIsInteractionScopeActive(e) {
    return Boolean(forceStop && vgMandatoryInteractionActive && e && e.id === vgInteractionScopeId)
}

function vgWatchDurationMs(e = 0) {
    let t = Number(window.timerDuration) || 0,
        r = Number(remainingTime) || 0,
        n = 1e3 * Math.max(0, Number(e) || 0);
    return Math.max(t, r, n, 0)
}

function vgInteractionEnabled(e) {
    let t = String(e ?? "").trim().toLowerCase();
    return "" !== t && "no" !== t && "0" !== t && "false" !== t && "null" !== t && "undefined" !== t
}

function vgNaturalInteractionStartDelay(e, t = 0, r = 1) {
    let n = Math.max(0, Number(e) || 0),
        o = Math.max(1, Number(r) || 1),
        a = Math.max(0, Number(t) || 0);
    if (n <= 0) return vgActionDelay(4200, 9200);
    let i = n >= 42e4 ? 26e3 : n >= 24e4 ? 21e3 : n >= 12e4 ? 15500 : n >= 6e4 ? 10500 : n >= 3e4 ? 6200 : n >= 15e3 ? 3600 : 1600,
        c, l, u;
    n < 15e3 ? (c = .16, l = .34, u = 1100) : n < 3e4 ? (c = .24, l = .44, u = 2600) : n < 45e3 ? (c = .32, l = .52, u = 6200) : (c = .4, l = n < 9e4 ? .58 : n < 24e4 ? .64 : .68, u = n < 9e4 ? 11e3 : n < 18e4 ? 18500 : n < 42e4 ? 32e3 : 46e3);
    let s = Math.max(u, Math.round(n * c)),
        d = Math.max(s + 900, Math.round(n * l)),
        f = n - a - i,
        m, p;
    if (f >= s + 900) m = s, p = Math.min(d, Math.round(f));
    else {
        let y = n > 0 ? Math.min(1, (a + i) / n) : 1,
            h, g;
        y >= .86 ? (h = n < 3e4 ? .1 : .14, g = n < 3e4 ? .24 : .3) : y >= .7 ? (h = n < 45e3 ? .18 : .24, g = n < 45e3 ? .34 : .4) : (h = n < 45e3 ? .26 : .32, g = n < 45e3 ? .44 : .48), p = Math.max((m = Math.max(Math.min(u, Math.round(.22 * n)), Math.round(n * h))) + 700, Math.round(n * g)), f > m + 700 && (p = Math.min(p, Math.round(f))), p = Math.min(p, d)
    }
    return o >= 3 && f > Math.round(.32 * n) && (m = Math.min(m, Math.max(Math.round(.34 * n), f - Math.round(.08 * a)))), p = Math.max((m = Math.max(700, Math.min(Math.round(m), Math.round(p) - 500))) + 500, Math.round(p)), vgActionDelay(m, p)
}

function vgMandatoryActionGap(e, t = 0, r = 1, n = !1) {
    let o = Math.max(0, Number(e) || 0),
        a = Math.max(0, Number(remainingTime) || 0),
        i = Math.max(1, Number(r) || 1),
        c, l;
    o < 15e3 ? (c = 650, l = 1600) : o < 3e4 ? (c = 1100, l = 2700) : o < 6e4 ? (c = 1900, l = 4500) : o < 12e4 ? (c = 3200, l = 7600) : o < 24e4 ? (c = 5200, l = 11800) : o < 42e4 ? (c = 7600, l = 16800) : (c = 9800, l = 23500), n && (c = Math.round(1.02 * c), l = Math.round(1.06 * l));
    let u = Math.min(1800, Math.max(0, Number(t) || 0) * vgActionDelay(180, 520));
    if (c += Math.round(.35 * u), l += u, a > 0) {
        let s = Math.max(650, Math.floor(Math.max(650, a - (a > 9e4 ? 28e3 : a > 45e3 ? 17e3 : a > 18e3 ? 7800 : 2800)) / (i + 1) * .72));
        l = s < c ? Math.max((c = Math.max(700, Math.floor(.64 * s))) + 450, s) : Math.min(l, Math.max(c + 450, s)), a < 6e3 ? (c = Math.min(c, 700), l = Math.min(l, 1500)) : a < 12e3 && (c = Math.min(c, 1e3), l = Math.min(l, 2600))
    }
    return vgActionDelay(Math.max(450, Math.round(c)), Math.max(Math.round(c) + 350, Math.round(l)))
}
window.SPA_CYCLE_STARTED = !0, window.VG_AUTOPLAY_VIDEO_ID = window.VG_AUTOPLAY_VIDEO_ID || null, window.VG_AUTOPLAY_ACTIVE = window.VG_AUTOPLAY_ACTIVE || !1;
let VG_ALLOW_USER_CLICK = !1;
const safeEventListeners = new WeakMap;

function getRandomInt(e, t) {
    return e = Math.ceil(e), Math.floor(Math.random() * ((t = Math.floor(t)) - e + 1)) + e
}

function vgActionDelay(e, t) {
    return "function" == typeof vgNaturalDelay ? vgNaturalDelay(e, t) : getRandomInt(e, t)
}

function vgLater(e, t = 700, r = 1600) {
    return setTimeout(() => {
        try {
            e()
        } catch (t) {
            vgRecoverFromClientError(t && t.message ? t.message : "delayed_action_error")
        }
    }, vgActionDelay(t, r))
}

function vgEnoughActionTime(e) {
    return "function" == typeof vgCanUseTime ? vgCanUseTime(e, 2500) : !remainingTime || remainingTime > e + 2500
}

function vgScrollToY(e, t = 900, r = 1800) {
    return new Promise(n => {
        try {
            let o = Math.max(0, (document.documentElement && document.documentElement.scrollHeight || 0) - (window.innerHeight || document.documentElement.clientHeight || 700)),
                a = Math.max(0, Math.min(o, Number(e) || 0)),
                i = window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0,
                c = a - i,
                l = Math.max(180, vgActionDelay(t, r)),
                u = performance.now(),
                s = vgScrollGeneration;
            try {
                $("html, body").stop(!0, !1)
            } catch (d) {}
            try {
                window.onscroll = null
            } catch (f) {}
            if (2 > Math.abs(c)) {
                window.scrollTo(0, a), document.documentElement && (document.documentElement.scrollTop = a), document.body && (document.body.scrollTop = a), setTimeout(() => n(!0), 80);
                return
            }
            let m = e => e < .5 ? 4 * e * e * e : 1 - Math.pow(-2 * e + 2, 3) / 2,
                p = e => {
                    if (!forceStop || s !== vgScrollGeneration) {
                        n(!1);
                        return
                    }
                    let t = Math.min(1, (e - u) / l),
                        r = i + c * m(t);
                    try {
                        window.scrollTo(0, r)
                    } catch (o) {}
                    try {
                        document.documentElement && (document.documentElement.scrollTop = r)
                    } catch (d) {}
                    try {
                        document.body && (document.body.scrollTop = r)
                    } catch (f) {}
                    if (t < 1) {
                        requestAnimationFrame(p);
                        return
                    }
                    try {
                        window.scrollTo(0, a)
                    } catch (y) {}
                    try {
                        document.documentElement && (document.documentElement.scrollTop = a)
                    } catch (h) {}
                    try {
                        document.body && (document.body.scrollTop = a)
                    } catch (g) {}
                    setTimeout(() => n(!0), 120)
                };
            requestAnimationFrame(p)
        } catch (y) {
            try {
                window.scrollTo(0, Math.max(0, Number(e) || 0))
            } catch (h) {}
            n(!1)
        }
    })
}

function vgGetPageScrollState() {
    let e = window.innerHeight || document.documentElement.clientHeight || 700,
        t = window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0,
        r = Math.max(document.documentElement && document.documentElement.scrollHeight || 0, document.body && document.body.scrollHeight || 0, 0),
        n = Math.max(0, r - e),
        o = Math.max(0, n - t);
    return {
        viewport: e,
        scrollTop: t,
        scrollHeight: r,
        maxScroll: n,
        bottomGap: o,
        nearBottom: o <= Math.max(140, .18 * e)
    }
}
window._YTLoadState ||= {
    listenerInitialized: !1,
    waiters: []
};
const VG_FEED_SCAN_SCROLL_MIN_MS = 350,
    VG_FEED_SCAN_SCROLL_MAX_MS = 1800,
    VG_FEED_SCAN_SCROLL_DIVISOR = 7.2,
    VG_FEED_SCAN_SCROLL_RANDOM_MS = 80,
    VG_FEED_SCAN_LAZYLOAD_MIN_MS = 650,
    VG_FEED_SCAN_LAZYLOAD_DEFAULT_MS = 1800,
    VG_FEED_SCAN_BATCH_STABLE_MS = 450,
    VG_FEED_SCAN_AFTER_GROWTH_MIN_MS = 220,
    VG_FEED_SCAN_AFTER_GROWTH_MAX_MS = 520,
    VG_FEED_SCAN_NEXT_CYCLE_MIN_MS = 250,
    VG_FEED_SCAN_NEXT_CYCLE_MAX_MS = 650,
    VG_CHANNEL_TAB_AFTER_CLICK_SETTLE_MIN_MS = 900,
    VG_CHANNEL_TAB_AFTER_CLICK_SETTLE_MAX_MS = 1150,
    VG_CHANNEL_TAB_RETURN_TOP_SETTLE_MIN_MS = 160,
    VG_CHANNEL_TAB_RETURN_TOP_SETTLE_MAX_MS = 320,
    VG_CHANNEL_TAB_SKIP_SETTLE_MIN_MS = 450,
    VG_CHANNEL_TAB_SKIP_SETTLE_MAX_MS = 760,
    VG_CHANNEL_TAB_VERIFY_MIN_MS = 700,
    VG_CHANNEL_TAB_VERIFY_MAX_MS = 4800,
    VG_CHANNEL_TAB_VERIFY_POLL_MS = 150,
    VG_CHANNEL_TAB_CONTENT_READY_MAX_MS = 2600,
    VG_CHANNEL_TAB_CONTENT_READY_STABLE_MS = 320;

function vgGetDynamicFeedScanScrollDuration(e, t = {}) {
    let r = Math.max(0, Number(e) || 0),
        n = Math.max(600, window.innerHeight || document.documentElement.clientHeight || 800),
        o = r / n,
        a = Math.max(820, Number(t.minShortMs) || 900),
        i = Math.max(a, Number(t.maxShortMs) || 1450),
        c = Math.max(760, Number(t.minMediumMs) || 850),
        l = Math.max(c, Number(t.maxMediumMs) || 1550),
        u = Math.max(950, Number(t.minLongMs) || 1050),
        s = Math.max(u, Number(t.maxLongMs) || 1800),
        d = Math.max(0, Number(t.randomMs) || 80),
        f;
    return Math.round((f = r <= 35 ? 350 : o <= 2.35 ? Math.min(i, Math.max(a, r / 1.65)) : o <= 4.5 ? Math.min(l, Math.max(c, r / 3.8)) : Math.min(s, Math.max(u, r / 7.2))) + Math.random() * d)
}
async function vgScrollDynamicFeedUntilSettled(e = {}) {
    let t = String(e.workflow || "feed"),
        r = Math.max(650, Number(e.waitForNewContentMs) || 1800),
        n = "function" == typeof e.onStep ? e.onStep : null,
        o = () => {
            try {
                if ("function" == typeof e.contentCountGetter) {
                    let r = Number(e.contentCountGetter());
                    if (Number.isFinite(r)) return Math.max(0, r)
                }
            } catch (n) {}
            try {
                let o = ("function" == typeof vgQueryOptionalSelectorAll ? vgQueryOptionalSelectorAll : vgQuerySelectorAll)('a[href*="/watch?v="], a[href*="/shorts/"], ytd-video-renderer, ytd-rich-item-renderer, ytd-grid-video-renderer, ytd-reel-item-renderer', {
                    workflow: t,
                    intent: "probe",
                    phase: t + ".feedCandidateCount"
                });
                return o && "number" == typeof o.length ? o.length : 0
            } catch (a) {
                return 0
            }
        },
        a = () => {
            let e = vgGetPageScrollState(),
                t = Math.max(400, window.innerHeight || document.documentElement.clientHeight || 800);
            return Math.max(0, e.scrollHeight - t + 4, e.maxScroll || 0)
        },
        i = async () => {
            try {
                $("html, body").stop(!0, !0)
            } catch (e) {}
            try {
                window.onscroll = null
            } catch (t) {}
            let r = vgGetPageScrollState(),
                o = a(),
                i = Math.max(0, o - r.scrollTop);
            if (i < 35 || r.nearBottom) return {
                moved: !1,
                found: !1
            };
            let c = vgGetDynamicFeedScanScrollDuration(i);
            return await new Promise(e => {
                let t = !1,
                    r = !1,
                    a = null,
                    i = null,
                    l = () => {
                        try {
                            a && clearInterval(a)
                        } catch (e) {}
                        try {
                            i && clearTimeout(i)
                        } catch (t) {}
                        a = null, i = null
                    },
                    u = r => {
                        t || (t = !0, l(), e(r || {
                            moved: !0,
                            found: !1
                        }))
                    },
                    s = async () => {
                        if (forceStop && !r && n) {
                            r = !0;
                            try {
                                let e = await n({
                                    stage: "during_smooth_scroll",
                                    step: 1,
                                    state: vgGetPageScrollState()
                                });
                                if (!0 === e) {
                                    try {
                                        $("html, body").stop(!0, !0)
                                    } catch (t) {}
                                    u({
                                        moved: !0,
                                        found: !0
                                    })
                                }
                            } catch (o) {} finally {
                                r = !1
                            }
                        }
                    };
                n && (a = setInterval(s, 650));
                try {
                    $("html, body").stop(!0, !0).animate({
                        scrollTop: o
                    }, c, "swing").promise().done(() => u({
                        moved: !0,
                        found: !1
                    })).fail(() => u({
                        moved: !0,
                        found: !1
                    }))
                } catch (d) {
                    try {
                        window.scrollTo({
                            top: o,
                            behavior: "smooth"
                        }), i = setTimeout(() => u({
                            moved: !0,
                            found: !1
                        }), c + 350)
                    } catch (f) {
                        u({
                            moved: !1,
                            found: !1
                        })
                    }
                }
                i = setTimeout(() => u({
                    moved: !0,
                    found: !1
                }), c + 1200)
            })
        }, c = async (e, t) => {
            let a = Date.now(),
                i = Math.max(0, Number(e) || 0),
                c = Math.max(0, Number(t) || 0),
                l = a,
                u = !1;
            for (; forceStop && Date.now() - a < r;) {
                if (await vgSleep(300), n) try {
                    let s = await n({
                        stage: "waiting_growth",
                        step: 1,
                        state: vgGetPageScrollState()
                    });
                    if (!0 === s) return {
                        found: !0,
                        loadedMore: !0,
                        changed: !0
                    }
                } catch (d) {}
                let f = vgGetPageScrollState(),
                    m = o(),
                    p = f.scrollHeight > i + 80,
                    y = m > c;
                if ((p || y) && (i = Math.max(i, f.scrollHeight), c = Math.max(c, m), l = Date.now(), u = !0), u && Date.now() - l >= 850) break
            }
            return {
                found: !1,
                loadedMore: u,
                changed: u
            }
        }, l = !1, u = !1, s = vgGetPageScrollState(), d = o();
    if ("function" == typeof vgRuntimeHeartbeat) try {
        vgRuntimeHeartbeat(t, "feed_smooth_scroll_to_bottom", {
            workflow: t,
            scroll_top: Math.round(s.scrollTop),
            scroll_height: Math.round(s.scrollHeight),
            bottom_gap: Math.round(s.bottomGap),
            candidates: d
        })
    } catch (f) {}
    if (n) try {
        let m = await n({
            stage: "before_smooth_scroll",
            step: 0,
            state: s
        });
        if (!0 === m) return {
            found: !0,
            reachedBottom: l,
            loadedMore: u,
            steps: 1
        }
    } catch (p) {}
    let y = await i();
    if (l = !0, y && y.found) return {
        found: !0,
        reachedBottom: l,
        loadedMore: u,
        steps: 1
    };
    if (n) try {
        let h = await n({
            stage: "after_smooth_scroll",
            step: 1,
            state: vgGetPageScrollState()
        });
        if (!0 === h) return {
            found: !0,
            reachedBottom: l,
            loadedMore: u,
            steps: 1
        }
    } catch (g) {}
    let v = await c(s.scrollHeight, d);
    if (v && v.found) return {
        found: !0,
        reachedBottom: l,
        loadedMore: !0,
        steps: 1
    };
    if ((u = !!(v && v.loadedMore)) && (await vgSleep(vgActionDelay(220, 520)), n)) try {
        let b = await n({
            stage: "after_growth",
            step: 1,
            state: vgGetPageScrollState()
        });
        if (!0 === b) return {
            found: !0,
            reachedBottom: l,
            loadedMore: u,
            steps: 1
        }
    } catch (w) {}
    return {
        found: !1,
        reachedBottom: l,
        loadedMore: u,
        steps: 1
    }
}
async function vgScrollElementToTop(e, t = 90, r = null) {
    let n = e => {
        if ("function" == typeof r) try {
            r(Boolean(e))
        } catch (t) {}
        return Boolean(e)
    };
    if (!e || !e.isConnected) return n(!1);
    try {
        let o = e.getBoundingClientRect(),
            a = o.top + window.scrollY - t;
        return await vgScrollToY(a, 1e3, 1900), n(!0)
    } catch (i) {
        return n(!1)
    }
}

function vgScrollViewportToTopDuringInteraction(e = null) {
    let t = () => {
        "function" == typeof e && e()
    };
    try {
        if (0 >= (window.scrollY || document.documentElement.scrollTop || 0)) {
            t();
            return
        }
        $("html, body").stop(!0, !0).animate({
            scrollTop: 0
        }, vgActionDelay(800, 1500)).promise().always(t)
    } catch (r) {
        try {
            window.scrollTo(0, 0)
        } catch (n) {}
        t()
    }
}

function vgSleep(e) {
    return new Promise(t => setTimeout(t, Math.max(0, Number(e) || 0)))
}

function vgMandatoryUiTryingDelay() {
    return vgActionDelay(1850, 2250)
}

function vgMandatoryUiProcessingDelay() {
    return vgActionDelay(1850, 2250)
}

function vgMandatoryUiSmallPause() {
    return vgActionDelay(180, 360)
}

function vgSafeWorkerNotification(e, t, r, n, o, a = null, i = !1, c = 7e3, l = !0) {
    return new Promise(u => {
        let s = !1,
            d = e => {
                s || (s = !0, u(!1 !== e))
            },
            f = setTimeout(() => d(l), Math.max(1200, Number(c) || 7e3));
        try {
            if ("function" != typeof showWorkerNotification) {
                clearTimeout(f), d(l);
                return
            }
            Promise.resolve(showWorkerNotification(e, t, r, n, o, a, i)).then(e => {
                clearTimeout(f), d(e)
            }).catch(() => {
                clearTimeout(f), d(l)
            })
        } catch (m) {
            clearTimeout(f), d(l)
        }
    })
}
async function vgFetchJsonWithTimeout(e, t = {}, r = 12e3) {
    let n = null,
        o = null;
    try {
        "undefined" != typeof AbortController && (n = new AbortController, o = setTimeout(() => {
            try {
                n.abort()
            } catch (e) {}
        }, Math.max(3e3, Number(r) || 12e3)));
        let a = Object.assign({}, t);
        n && (a.signal = n.signal);
        let i = await fetch(e, a);
        if (!i || !i.ok) throw Error("http_error_" + (i ? i.status : "unknown"));
        return await i.json()
    } finally {
        o && clearTimeout(o)
    }
}

function vgStorageLocalGetSafe(e, t = 5e3, r = {}) {
    return new Promise(n => {
        let o = !1,
            a = e => {
                o || (o = !0, n(void 0 === e ? r : e))
            },
            i = setTimeout(() => a(r), Math.max(1200, Number(t) || 5e3));
        try {
            if ("undefined" == typeof chrome || !chrome.storage || !chrome.storage.local) {
                clearTimeout(i), a(r);
                return
            }
            chrome.storage.local.get(e, e => {
                clearTimeout(i);
                try {
                    chrome.runtime.lastError
                } catch (t) {}
                a(e || r)
            })
        } catch (c) {
            clearTimeout(i), a(r)
        }
    })
}

function vgStorageLocalSetSafe(e, t = 5e3) {
    return new Promise(r => {
        let n = !1,
            o = e => {
                n || (n = !0, r(Boolean(e)))
            },
            a = setTimeout(() => o(!1), Math.max(1200, Number(t) || 5e3));
        try {
            if ("undefined" == typeof chrome || !chrome.storage || !chrome.storage.local) {
                clearTimeout(a), o(!1);
                return
            }
            chrome.storage.local.set(e || {}, () => {
                clearTimeout(a);
                let e = (() => {
                    try {
                        return chrome.runtime.lastError
                    } catch (e) {
                        return null
                    }
                })();
                o(!e)
            })
        } catch (i) {
            clearTimeout(a), o(!1)
        }
    })
}

function vgStorageSyncGetSafe(e, t = 5e3, r = {}) {
    return new Promise(n => {
        let o = !1,
            a = e => {
                o || (o = !0, n(void 0 === e ? r : e))
            },
            i = setTimeout(() => a(r), Math.max(1200, Number(t) || 5e3));
        try {
            if ("undefined" == typeof chrome || !chrome.storage || !chrome.storage.sync) {
                clearTimeout(i), a(r);
                return
            }
            chrome.storage.sync.get(e, e => {
                clearTimeout(i);
                try {
                    chrome.runtime.lastError
                } catch (t) {}
                a(e || r)
            })
        } catch (c) {
            clearTimeout(i), a(r)
        }
    })
}

function vgRuntimeSendMessageSafe(e, t = 5e3, r = null) {
    return new Promise(n => {
        let o = !1,
            a = e => {
                o || (o = !0, n(void 0 === e ? r : e))
            },
            i = setTimeout(() => a(r), Math.max(1200, Number(t) || 5e3));
        try {
            if ("undefined" == typeof chrome || !chrome.runtime || "function" != typeof chrome.runtime.sendMessage) {
                clearTimeout(i), a(r);
                return
            }
            chrome.runtime.sendMessage(e, e => {
                clearTimeout(i);
                try {
                    chrome.runtime.lastError
                } catch (t) {}
                a(void 0 === e ? r : e)
            })
        } catch (c) {
            clearTimeout(i), a(r)
        }
    })
}

function vgTryNativePlaybackRecovery(e = null, t = null) {
    try {
        let r = e || vgGetPrimaryYouTubePlayer(),
            n = t || vgQuerySelector("video.html5-main-video, video");
        try {
            let o = vgQuerySelector(".ytp-large-play-button.ytp-button, .ytp-play-button");
            o && r && !r.classList.contains("playing-mode") && SimulateClick(o)
        } catch (a) {}
        try {
            r && "function" == typeof r.playVideo && r.playVideo()
        } catch (i) {}
        try {
            if (n && "function" == typeof n.play) {
                let c = n.play();
                c && "function" == typeof c.catch && c.catch(() => {})
            }
        } catch (l) {}
        try {
            r && "function" == typeof r.isMuted && r.isMuted() && "function" == typeof r.unMute && r.unMute()
        } catch (u) {}
        try {
            n && (n.muted = !1)
        } catch (s) {}
        return !0
    } catch (d) {
        return !1
    }
}

function vgShuffle(e) {
    let t = Array.isArray(e) ? [...e] : [];
    for (let r = t.length - 1; r > 0; r--) {
        let n = Math.floor(Math.random() * (r + 1));
        [t[r], t[n]] = [t[n], t[r]]
    }
    return t
}

function vgRunWithTimeout(e, t, r = 3e4) {
    return new Promise(n => {
        let o = !1,
            a = null,
            i = (e = "fail") => {
                o || (o = !0, a && clearTimeout(a), n(e))
            };
        a = setTimeout(() => {
            try {
                console.warn("ViewGrip interaction timeout:", e)
            } catch (t) {}
            i("fail")
        }, Math.max(5e3, Number(r) || 3e4));
        try {
            t(i)
        } catch (c) {
            try {
                console.error("ViewGrip interaction error:", e, c)
            } catch (l) {}
            i("fail")
        }
    })
}

function vgGetCommentThreads() {
    try {
        let e = Array.from(vgQuerySelectorAll("#comments #contents > ytd-comment-thread-renderer, ytd-comments #contents > ytd-comment-thread-renderer")),
            t = Array.from(vgQuerySelectorAll("#comments ytd-comment-thread-renderer, #comments ytd-comment-view-model, #comments ytd-comment-renderer")),
            r = e.length ? e : t,
            n = [],
            o = new Set;
        return r.forEach(e => {
            !e || !e.isConnected || o.has(e) || (o.add(e), n.push(e))
        }), n
    } catch (a) {
        return []
    }
}

function vgGetTopVisibleCommentThread() {
    try {
        let e = vgGetCommentThreads().filter(e => {
            if (!e || !e.isConnected) return !1;
            let t = e.getBoundingClientRect();
            return t.width > 0 && t.height > 0 && t.bottom > 0
        });
        if (!e.length) return null;
        return e.sort((e, t) => {
            let r = e.getBoundingClientRect(),
                n = t.getBoundingClientRect();
            return r.top - n.top
        }), e[0] || null
    } catch (t) {
        return null
    }
}

function vgGetSearchInput() {
    for (let e of ["yt-searchbox input.ytSearchboxComponentInput", "input#search", 'input[name="search_query"]', 'input[name*="search"]', 'input[role="combobox"]']) try {
        let t = vgQuerySelector(e, {
            workflow: "search",
            intent: "required",
            phase: "vgGetSearchInput"
        });
        if (t) return t
    } catch (r) {}
    return null
}

function vgGetSearchButton() {
    for (let e of ["yt-searchbox .ytSearchboxComponentSearchButton", "button#search-icon-legacy", "ytd-searchbox button#search-icon-legacy", 'button[aria-label="Search"]']) try {
        let t = vgQuerySelector(e, {
            workflow: "search",
            intent: "required",
            phase: "vgGetSearchButton"
        });
        if (t) return t
    } catch (r) {}
    return null
}

function disableContextMenu(e) {
    e.preventDefault()
}
const VG_RUNTIME_STAGE_TIMEOUTS = {
    bootstrap: 45e3,
    route_decision: 45e3,
    search: 24e4,
    channel: 3e5,
    video_finding: 18e4,
    video_navigation: 15e4,
    prewatch: 18e4,
    playback_waiting: 18e4,
    start_time: 9e4,
    watching: 0,
    interaction: 0,
    verification: 9e4,
    redirect: 75e3,
    restart: 75e3,
    runtime: 18e4
};

function vgRuntimeState() {
    try {
        return window.VG_RUNTIME_STATE && "object" == typeof window.VG_RUNTIME_STATE || (window.VG_RUNTIME_STATE = {
            active: !1,
            recovering: !1,
            startedAt: 0,
            lastProgressAt: 0,
            lastRecoveryAt: 0,
            lastRecoveryKey: "",
            stage: "idle",
            workflow: "runtime",
            status: "idle",
            expectedVideoId: "",
            backupUrl: "",
            token: "",
            viewingMethod: "",
            watchdogInterval: 0
        }), window.VG_RUNTIME_STATE
    } catch (e) {
        return {}
    }
}

function vgRuntimeHeartbeat(e = "runtime", t = "", r = {}) {
    try {
        let n = vgRuntimeState(),
            o = Date.now();
        n.active = !0, n.stage = String(e || "runtime"), n.workflow = r.workflow || n.workflow || n.stage, n.status = String(t || n.status || "progress"), n.lastProgressAt = o, n.startedAt || (n.startedAt = o), (r.video_id || r.expectedVideoId) && (n.expectedVideoId = String(r.video_id || r.expectedVideoId || "")), (r.backup_url || r.backupUrl) && (n.backupUrl = String(r.backup_url || r.backupUrl || "")), r.token && (n.token = String(r.token || "")), (r.viewing_method || r.viewingMethod) && (n.viewingMethod = String(r.viewing_method || r.viewingMethod || ""));
        try {
            window.VG_WORKER_STAGE = n.stage
        } catch (a) {}
        return n
    } catch (i) {
        return null
    }
}

function vgWorkerIsWatchingNow() {
    try {
        if (!0 === window.VG_WORKER_WATCH_STARTED || "undefined" != typeof timerRunning && timerRunning || "undefined" != typeof remainingTime && Number(remainingTime) > 5e3 || void 0 !== window.timerRunning && window.timerRunning || void 0 !== window.remainingTime && Number(window.remainingTime) > 5e3) return !0;
        let e = vgRuntimeState();
        return ["watching", "interaction", "verification"].includes(String(e.stage || ""))
    } catch (t) {
        return !1
    }
}

function vgReportRuntimeWorkflowIssue(e = "runtime", t = "fail", r = "", n = null, o = {}) {
    try {
        if ("function" == typeof vgWorkflowFail) return vgWorkflowFail(e, t, r, n, o || {});
        if (n && "function" == typeof vgReportCaughtError && vgReportCaughtError(n, String(e || "runtime") + "." + String(t || "fail")), "function" == typeof vgReportWorkflowDiagnostic) return vgReportWorkflowDiagnostic(e, t, r, o || {})
    } catch (a) {}
    return !1
}

function vgRecoveryActionForWorkflow(e = "runtime", t = "fail", r = {}) {
    if (r && r.action) return r.action;
    let n = String(e || "").toLowerCase(),
        o = String(t || "").toLowerCase();
    if (vgWorkerIsWatchingNow() || /bootstrap|startup|fatal|context|extension/.test(n + " " + o) || /playback|duration|timer|violation|watching|interaction|verification/.test(n + " " + o)) return "restart";
    if (/search|channel|video_finding|video_navigation|keyword|tab_scan|target_video/.test(n + " " + o)) return "redirect";
    let a = vgRuntimeState();
    return a && a.backupUrl ? "redirect" : "restart"
}

function vgRecoverWorkflow(e = "runtime", t = "fail", r = "Workflow failure detected.", n = null, o = {}) {
    try {
        if (!forceStop) return !1;
        let a = vgRuntimeState(),
            i = Date.now(),
            c = [e, t].join(":");
        if (a.recovering && a.lastRecoveryKey === c && i - a.lastRecoveryAt < 12e3) return !1;
        a.recovering = !0, a.lastRecoveryAt = i, a.lastRecoveryKey = c, vgRuntimeHeartbeat("recovery", t, {
            workflow: e,
            backupUrl: o.backupUrl || o.backup_url || a.backupUrl
        }), vgReportRuntimeWorkflowIssue(e, t, r, n, {
            policy: vgRecoveryActionForWorkflow(e, t, o),
            stage: a.stage || "",
            expected_video_id: a.expectedVideoId || "",
            backup_url_present: Boolean(o.backupUrl || o.backup_url || a.backupUrl)
        });
        let l = o.retryKey ? String(o.retryKey) : "";
        if (l && "function" == typeof o.retryFn) {
            a.retry = a.retry && "object" == typeof a.retry ? a.retry : {};
            let u = Number(a.retry[l] || 0),
                s = Math.max(0, Number(o.maxRetry) || 0);
            if (u < s) {
                a.retry[l] = u + 1;
                let d = Math.max(500, Number(o.retryDelayMs) || 2500);
                return setTimeout(() => {
                    try {
                        a.recovering = !1, forceStop && o.retryFn()
                    } catch (n) {
                        vgRecoverWorkflow(e, t + "_retry_failed", r, n, Object.assign({}, o, {
                            retryFn: null
                        }))
                    }
                }, d), !0
            }
        }
        let f = vgRecoveryActionForWorkflow(e, t, o),
            m = String(o.backupUrl || o.backup_url || a.backupUrl || "").trim();
        if ("redirect" === f && m) return redirectToVideoSafely(m, {
            workflow: e,
            status: t,
            message: r,
            reason: "workflow_recovery"
        }), !0;
        return setTimeout(() => {
            try {
                restartWorkerSession(e, t)
            } catch (r) {
                try {
                    navigateWorkerTab(`${MainUrl}/worker/start`)
                } catch (n) {}
            }
        }, Math.max(500, Number(o.delayMs) || 1200)), !0
    } catch (p) {
        try {
            restartWorkerSession(e, t)
        } catch (y) {}
        return !1
    }
}

function vgStartRuntimeWatchdog(e = {}) {
    try {
        let t = vgRuntimeState(),
            r = Date.now();
        if (t.active = !0, t.recovering = !1, t.startedAt = r, t.lastProgressAt = r, t.stage = "bootstrap", t.workflow = "worker_runtime", t.status = "started", t.expectedVideoId = String(e.video_id || e.expectedVideoId || t.expectedVideoId || ""), t.backupUrl = String(e.backup_url || e.backupUrl || t.backupUrl || ""), t.token = String(e.token || t.token || ""), t.viewingMethod = String(e.viewing_method || e.viewingMethod || t.viewingMethod || ""), t.watchdogInterval) try {
            clearInterval(t.watchdogInterval)
        } catch (n) {}
        return t.watchdogInterval = setInterval(() => {
            try {
                if (!forceStop || !t.active) {
                    clearInterval(t.watchdogInterval), t.watchdogInterval = 0;
                    return
                }
                if (t.recovering) return;
                let e = String(t.stage || "runtime"),
                    r = Number(VG_RUNTIME_STAGE_TIMEOUTS[e] ?? VG_RUNTIME_STAGE_TIMEOUTS.runtime),
                    n = Date.now() - Number(t.lastProgressAt || t.startedAt || Date.now());
                if (r > 0 && n > r) {
                    let o = vgWorkerIsWatchingNow() ? "restart" : t.backupUrl ? "redirect" : "restart";
                    vgRecoverWorkflow("watching" === e ? "playback" : e, "runtime_watchdog_timeout", "Worker runtime watchdog detected no forward progress. stage=" + e + " | elapsed_ms=" + n, null, {
                        backupUrl: t.backupUrl,
                        action: o
                    });
                    return
                }
                let a = String(t.expectedVideoId || ""),
                    i = "function" == typeof extractYouTubeVideoId ? String(extractYouTubeVideoId() || "") : "",
                    c = e.toLowerCase(),
                    l = /search|channel|video_navigation|video_finding|redirect|bootstrap|route_decision/.test(c);
                a && i && i !== a && !l && vgRecoverWorkflow("video_navigation", "unexpected_video_id", "Worker tab is on a non-target YouTube video. expected_video_id=" + a + " | current_video_id=" + i + " | stage=" + e, null, {
                    backupUrl: t.backupUrl,
                    action: vgWorkerIsWatchingNow() ? "restart" : "redirect"
                })
            } catch (u) {}
        }, 15e3), t
    } catch (o) {
        return null
    }
}

function vgStopRuntimeWatchdog() {
    try {
        let e = vgRuntimeState();
        e.active = !1, e.recovering = !1, e.watchdogInterval && (clearInterval(e.watchdogInterval), e.watchdogInterval = 0)
    } catch (t) {}
}

function redirectToVideoSafely(e, t = {}) {
    if (!forceStop) return !1;
    let r = String(e || "").trim();
    return /^https:\/\/(www\.)?youtube\.com\//i.test(r) ? (vgRuntimeHeartbeat("redirect", t.status || "redirect_to_backup", {
        workflow: t.workflow || "video_navigation",
        backupUrl: r
    }), setTimeout(async function() {
        if (forceStop) {
            try {
                await vgSafeWorkerNotification(1, "info", "redirecting_to_video", 0, "", null, !1, 7e3, !0)
            } catch (e) {}
            forceStop && setTimeout(function() {
                if (forceStop) try {
                    let e = navigateWorkerTab(r);
                    e || setTimeout(() => {
                        try {
                            let e = navigateWorkerTab(r);
                            e || vgRecoverWorkflow(t.workflow || "video_navigation", t.status || "redirect_navigation_rejected", "navigateWorkerTab rejected redirect twice, likely due to tab update lock or invalid state.", null, {
                                action: "restart"
                            })
                        } catch (n) {
                            vgRecoverWorkflow(t.workflow || "video_navigation", t.status || "redirect_navigation_retry_failed", "navigateWorkerTab retry failed while redirecting to backup video.", n, {
                                action: "restart"
                            })
                        }
                    }, 3600)
                } catch (n) {
                    vgRecoverWorkflow(t.workflow || "video_navigation", t.status || "redirect_navigation_failed", "navigateWorkerTab failed while redirecting to backup video.", n, {
                        action: "restart"
                    })
                }
            }, 900)
        }
    }, Math.max(500, Number(t.delayMs) || 1200)), !0) : (vgRecoverWorkflow(t.workflow || "video_navigation", t.status || "invalid_redirect_url", "redirectToVideoSafely received an invalid or empty YouTube URL.", null, {
        action: "restart"
    }), !1)
}
async function waitForDomReady() {
    if ("interactive" !== document.readyState && "complete" !== document.readyState) return new Promise(e => {
        let t = !1,
            r = () => {
                t || (t = !0, clearTimeout(o), removeSafeEventListener(document, "DOMContentLoaded", n), e())
            },
            n = () => r(),
            o = setTimeout(r, 5e3);
        addSafeEventListener(document, "DOMContentLoaded", n)
    })
}

function vgCurrentPageSpecificStartupMessage() {
    try {
        let e = window.location.hostname,
            t = window.location.href;
        if (t.includes("signin_prompt")) return {
            dots: 0,
            type: "info",
            message: "please_select_channel"
        };
        if ("accounts.google.com" === e || "gds.google.com" === e) return {
            dots: 0,
            type: "info",
            message: "setting_up_your_youtube_account"
        };
        if ("support.google.com" === e) return {
            dots: 0,
            type: "info",
            message: "you_not_have_youtube account"
        };
        return null
    } catch (r) {
        return null
    }
}

function vgPageAlreadyReadyForStartupNotification() {
    try {
        if ("loading" === document.readyState || !document.body && !document.documentElement) return !1;
        let e = vgCurrentPageSpecificStartupMessage();
        if (e) return !0;
        if (vgIsYouTubeHost()) return Boolean(vgGetPrimaryYouTubePlayer() || vgQuerySelector("ytd-app", document, "startup_ready_check") || vgQuerySelector("video", document, "startup_ready_check"));
        return "interactive" === document.readyState || "complete" === document.readyState
    } catch (t) {
        return !1
    }
}

function handleRenderingMessage() {
    if (window.location.hostname === parsedUrl.hostname || !vgQuerySelector("html") || !forceStop) return;
    let e = vgCurrentPageSpecificStartupMessage();
    if (e) {
        vgSafeWorkerNotification(e.dots, e.type, e.message, 0, "", null, !1, 7e3, !0).then(e => {
            e && setTimeout(() => {
                allowedDomains.includes(window.location.hostname) || notifyWorkerRestart()
            }, 3e3)
        });
        return
    }
    vgPageAlreadyReadyForStartupNotification() || showWorkerNotification(1, "info", "please_wait_page_is_loading", 0, "", null, !1).then(e => {
        e && (setTimeout(() => {
            allowedDomains.includes(window.location.hostname) || notifyWorkerRestart()
        }, 3e3), setTimeout(() => {
            if (vgIsYouTubeHost()) {
                let e = Boolean(vgGetPrimaryYouTubePlayer() || vgQuerySelector("video"));
                e || vgIsYouTubeAdShowing();
                return
            }
            if ("complete" !== document.readyState) {
                if (!forceStop) return;
                showWorkerNotification(0, "warning", "your_connection_too_slow", 0, "", null, !0)
            }
        }, 2e4))
    })
}
async function handleFetchDataMessage() {
    let e = () => setTimeout(() => navigateWorkerTab(`${MainUrl}/worker/start`), 1e3);
    try {
        let t = window.location.hostname === parsedUrl.hostname && window.location.href.includes("worker/start");
        if (!t) return;
        let r = readCookie("worker_token");
        if (!r) return e();
        let n = await vgStorageLocalSetSafe({
            token: r
        }, 5e3);
        if (!n) return e();
        let o = readCookie("lang") || "US";
        await vgStorageLocalSetSafe({
            langCode: o
        }, 5e3), requestNextVideo()
    } catch (a) {
        e()
    }
}

function handleStartWorkerMessage() {
    if (StartWorker = !1, !forceStop) return;
    let e = window.location.hostname,
        t = window.location.href,
        r = vgCurrentPageSpecificStartupMessage();
    if (r) {
        if (!forceStop) return;
        return vgSafeWorkerNotification(r.dots, r.type, r.message, 0, "", null, !1, 7e3, !0)
    }
    if (document.body && (t.includes("facebook.com") || t.includes("instagram.com") || t.includes("sorry/index?continue"))) {
        if (!forceStop) return;
        notifyWorkerRestart("worker_bootstrap", "unsupported_redirect_domain", "Worker tab reached an unsupported domain during startup.");
        return
    }
    if ("www.youtube.com" === e) {
        let n = "https://www.youtube.com/" === t || t.includes("youtube.com/watch?v=") || t.includes("youtube.com/shorts/") || t.includes("/channel") || t.includes(".com/@");
        if (n) addSafeEventListener(document, "contextmenu", disableContextMenu), startWorkerPlaybackFlow();
        else {
            if (!forceStop) return;
            vgSafeWorkerNotification(1, "success", "worker_is_ready", 0, "", null, !1, 7e3, !0).then(() => {
                setTimeout(() => requestNextVideo(), 2e3)
            })
        }
        return
    }
    vgSafeWorkerNotification(1, "default", "processing", 0, "", null, !1, 7e3, !0).then(() => !0)
}

function handleSlowConnectionMessage() {
    notifyWorkerRestart("connection", "slow_connection", "Background reported slow connection or worker tab inactivity.")
}
async function handleConnectionRetryMessage() {
    forceStop && (await vgSafeWorkerNotification(0, "warning", "Connection issue detected", 0, "", null, !1, 7e3, !0).catch(() => !1), forceStop && restartWorkerSession("connection", "content_retry_requested"))
}
async function startWorkerPlaybackFlow() {
    try {
        vgRuntimeHeartbeat("bootstrap", "startWorkerPlaybackFlow", {
            workflow: "worker_bootstrap"
        });
        let e = await vgStorageLocalGetSafe(["AjaxData", "token"], 5e3, null),
            t = e && e.AjaxData ? e.AjaxData : null,
            r = e && e.token ? e.token : null;
        if (!t || !r || !t.video_id) {
            "function" == typeof vgWorkflowFail && vgWorkflowFail("worker_bootstrap", "ajax_data_missing", "Worker playback flow could not start because AjaxData, token, or video_id was missing."), notifyWorkerRestart("worker_bootstrap", "ajax_data_missing");
            return
        }
        vgRuntimeHeartbeat("bootstrap", "campaign_data_loaded", {
            workflow: "worker_bootstrap",
            token: r,
            video_id: t.video_id,
            backup_url: t.backup_url,
            viewing_method: t.viewing_method
        }), runCampaignWorker(r, t.video_id, t.backup_url, t.viewing_method, t.keyword, t.like, t.subscribe, t.comment, t.comment_liking, t.duration)
    } catch (n) {
        "function" == typeof vgWorkflowFail && vgWorkflowFail("worker_bootstrap", "runtime_error", "Worker playback flow threw before campaign execution started.", n), notifyWorkerRestart("worker_bootstrap", "runtime_error")
    }
}

function notifyWorkerRestart(e = "worker_runtime", t = "restart_requested", r = "Worker restart was requested after a runtime or state failure.", n = null) {
    return !!forceStop && vgRecoverWorkflow(e, t, r, n, {
        action: "restart"
    })
}

function runCampaignWorker(e, t, r, n, o, a, i, c, l, u) {
    if (!forceStop) return;
    vgStartRuntimeWatchdog({
        token: e,
        video_id: t,
        backup_url: r,
        viewing_method: n
    }), vgRuntimeHeartbeat("route_decision", "runCampaignWorker", {
        workflow: "worker_runtime",
        token: e,
        video_id: t,
        backup_url: r,
        viewing_method: n
    });
    try {
        enableWorkerInputGuard()
    } catch (s) {}
    try {
        VGSettings()
    } catch (d) {}
    let f = String(c ?? "").trim(),
        m = (e, t, r, n = null, o = {}) => {
            try {
                if ("function" == typeof vgWorkflowFail) return vgWorkflowFail(e, t, r, n, o);
                if (n && "function" == typeof vgReportCaughtError && vgReportCaughtError(n, e + "." + t), "function" == typeof vgReportWorkflowDiagnostic) return vgReportWorkflowDiagnostic(e, t, r, o)
            } catch (a) {}
            return !1
        },
        p = (e, t, r = "required", n = null) => ({
            workflow: e,
            phase: t,
            intent: r,
            root: n
        });
    if (null !== t && "" !== t) {
        function y() {
            try {
                if (!t || "string" != typeof t) return null;
                let e = t.toLowerCase(),
                    r = vgQuerySelectorAll('a[href*="watch?v="], a[href*="shorts/"]', p("video_finding", "findVideoElement.anchors", "required")),
                    n = [];
                for (let o of r) {
                    let a = o.getAttribute("href");
                    if (!a) continue;
                    let i = a.toLowerCase(),
                        c = !1,
                        l = i.indexOf("watch?v=");
                    if (-1 !== l) {
                        let u = l + 8,
                            s = i.substring(u, u + 11);
                        s === e && (c = !0)
                    }
                    let d = i.indexOf("shorts/");
                    if (!c && -1 !== d) {
                        let f = d + 7,
                            y = i.substring(f, f + 11);
                        y === e && (c = !0)
                    }
                    if (!c || "_blank" === o.getAttribute("target") || o.closest("#play-button") || o.closest("#secondary") || o.closest("#related") || o.closest("#comments") || o.closest("ytd-watch-next-secondary-results-renderer") || o.closest('ytd-item-section-renderer[section-identifier="related"]')) continue;
                    let h;
                    try {
                        h = o.getBoundingClientRect()
                    } catch (g) {
                        continue
                    }
                    let v = h.width > 1 ? h.width : 1,
                        b = h.height > 1 ? h.height : 1;
                    n.push({
                        element: o,
                        size: v * b,
                        top: h.top
                    })
                }
                if (0 === n.length) return null;
                n.sort(function(e, t) {
                    return t.size - e.size
                });
                let w = n[0].size,
                    S = [];
                for (let _ of n) _.size === w && S.push(_);
                S.sort(function(e, t) {
                    return e.top - t.top
                });
                let k = S[0].element,
                    T = function e(t) {
                        try {
                            if (!t || "function" != typeof t.getBoundingClientRect) return t || document.body;
                            let r = t.getBoundingClientRect(),
                                n = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0, 800),
                                o = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0, 600),
                                a = Math.max(1, r.width || 1),
                                i = Math.max(1, r.height || 1),
                                c = Math.max(1, a * i),
                                l = e => {
                                    try {
                                        if (!e || e === document.body || e === document.documentElement) return !1;
                                        let t = e.getBoundingClientRect();
                                        if (!t || t.width < 24 || t.height < 24) return !1;
                                        let r = Math.max(1, t.width * t.height);
                                        if (t.width > .78 * n && a < .55 * n || t.height > .78 * o && i < .55 * o || t.width > Math.max(4.2 * a, 680) && t.height > Math.max(1.55 * i, 260) || r > Math.max(7.5 * c, 26e4) && c < 16e4) return !1;
                                        return !0
                                    } catch (l) {
                                        return !1
                                    }
                                },
                                u = t.closest("ytd-rich-item-renderer,ytd-rich-grid-media,ytd-video-renderer,ytd-grid-video-renderer,ytd-reel-item-renderer,ytd-compact-video-renderer,ytd-playlist-video-renderer,yt-lockup-view-model,ytm-shorts-lockup-view-model,ytm-video-with-context-renderer");
                            if (l(u)) return u;
                            let s = t,
                                d = Number.POSITIVE_INFINITY,
                                f = t;
                            for (; f && f !== document.body && f !== document.documentElement;) {
                                if (l(f)) {
                                    let m = f.getBoundingClientRect(),
                                        p = Math.max(1, m.width * m.height),
                                        y = String(f.tagName || "").toLowerCase(),
                                        h = /(video|reel|rich|lockup|grid|compact|playlist)/.test(y + " " + String(f.className || "")) ? -5e4 : 0,
                                        g = p + h;
                                    g < d && (d = g, s = f)
                                }
                                f = f.parentElement
                            }
                            return s || t
                        } catch (v) {
                            return t || document.body
                        }
                    }(k);
                try {
                    let x = vgQueryOptionalSelectorAll('[data-vg-highlight="1"]', p("video_finding", "findVideoElement.clearOldHighlight", "probe"));
                    for (let I of Array.from(x || [])) try {
                        I.style.border = "", I.style.boxShadow = "", I.style.padding = "", I.style.transform = "", I.removeAttribute("data-vg-highlight")
                    } catch (E) {}
                } catch (C) {}
                try {
                    T.style.transition = "border 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease", T.style.borderRadius = "12px", T.style.border = "4px solid #2f9e44", T.style.boxShadow = "0 6px 14px rgba(0,0,0,0.15)", T.style.padding = "2px", T.style.transform = "scale(1.012)", T.setAttribute("data-vg-highlight", "1")
                } catch {}
                return k || null
            } catch (A) {
                return m("video_finding", "find_video_runtime_error", "Unexpected DOM error while locating the target video element.", A), null
            }
        }

        function h(e = "video_navigation", t = "unexpected_error", n = "Worker video navigation failed and fallback URL will be used.", o = null) {
            forceStop && (t && "object" == typeof t && (o = t, t = String(e || "unexpected_error"), e = "video_navigation"), vgRecoverWorkflow(e, t, n, o, {
                backupUrl: r,
                action: "redirect",
                retryKey: e + ":" + t,
                maxRetry: 1,
                retryDelayMs: 1800,
                retryFn() {
                    if (forceStop) {
                        if ("search" === e) return v();
                        if ("channel" === e) return w();
                        redirectToVideoSafely(r, {
                            workflow: e,
                            status: t
                        })
                    }
                }
            }))
        }

        function g() {
            if (!forceStop) return;
            let n = null,
                a = e => {
                    if (!e || !e.isConnected) {
                        c();
                        return
                    }
                    scrollElementToCenter(e, t => {
                        if (!t) {
                            c();
                            return
                        }
                        i(e)
                    })
                },
                i = e => {
                    if (!forceStop) return;
                    let r = e;
                    if (!r || !r.isConnected) {
                        c();
                        return
                    }
                    showWorkerNotification(1, "default", "processing", 0, "", null, !1).then(e => {
                        e && setTimeout(() => {
                            if (forceStop) {
                                if (!r || !r.isConnected) {
                                    c();
                                    return
                                }
                                SimulateClick(r).then(e => {
                                    if (!e) {
                                        c();
                                        return
                                    }
                                    waitForYouTubePageUpdate().then(e => {
                                        if (!e && !window.location.href.includes(t)) {
                                            c();
                                            return
                                        }
                                        $("html, body").stop(!0, !1), _()
                                    })
                                }).catch(() => {
                                    c()
                                })
                            }
                        }, 1e3)
                    })
                },
                c = (e = "video_navigation_failed", t = null, n = "Target video navigation failed and backup URL will be used.") => {
                    forceStop && vgRecoverWorkflow("video_navigation", e, n, t, {
                        backupUrl: r,
                        action: "redirect",
                        retryKey: "video_navigation:" + e,
                        maxRetry: 1,
                        retryDelayMs: 1800,
                        retryFn() {
                            forceStop && g()
                        }
                    })
                };
            try {
                let l = y();
                if (!l || !l.isConnected) {
                    let u, s;
                    setWorkerOverlay(2), u = "notInChannel", s = null, window.location.href.includes("search_query") && (u = "notWorkKeyword", s = o), forceStop && (m("notWorkKeyword" === u ? "search" : "channel", "target_video_not_found", "Target campaign video was not found after scanning available results. video_id=" + t + " | report=" + u), showWorkerNotification(0, "error", "video_not_found", 0, "", null, !1).then(t => {
                        t && setTimeout(() => {
                            submitCampaignReport(e, u, s, r)
                        }, 2e3)
                    }));
                    return
                }
                n = l, setTimeout(() => {
                    forceStop && (setWorkerOverlay(2), showWorkerNotification(1, "success", "video_found", 0, "", null, !1).then(e => {
                        e && a(n)
                    }))
                }, 1e3), window.location.href.includes("youtube.com/watch?v=") && window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                })
            } catch (d) {
                c("get_video_element_runtime_error", d, "Unexpected error inside GetVideoElement().")
            }
        }

        function v() {
            if (!forceStop) return;
            let n = () => {
                forceStop && showWorkerNotification(0, "error", "keyword_not_available", 0, "", null, !1).then(t => {
                    t && setTimeout(() => submitCampaignReport(e, "emptyKeyword", null, r), 3e3)
                })
            };
            try {
                if (window.location.href.includes(t)) {
                    h();
                    return
                }
                if (!forceStop) return;
                showWorkerNotification(1, "default", "setting_up_keywords", 0, "", null, !1).then(e => {
                    e && waitForElement('yt-searchbox input.ytSearchboxComponentInput, input#search, input[name="search_query"], input[name*="search"], input[role="combobox"]', 12e3, p("search", "inputKeyword.searchInput", "required")).then(e => {
                        if (!e) return h("search", "search_input_missing", "Search input was not available after timeout.");
                        o?.trim() ? vgLater(b, 900, 1800) : n()
                    })
                })
            } catch (a) {
                h()
            }
        }

        function b() {
            if (forceStop) {
                if (!o.trim()) {
                    forceStop && showWorkerNotification(0, "error", "keyword_not_available", 0, "", null, !1).then(t => {
                        t && setTimeout(() => submitCampaignReport(e, "emptyKeyword", null, r), 3e3)
                    });
                    return
                }
                forceStop && showWorkerNotification(1, "default", "start_typing_keywords", 0, "", null, !1).then(e => {
                    if (e) try {
                        let n = vgGetSearchInput();
                        if (!n) {
                            h("search", "search_input_missing", "Search input was not found before typing keyword.");
                            return
                        }
                        let a = () => {
                            forceStop && showWorkerNotification(1, "default", "processing", 0, "", null, !1).then(e => {
                                e && setTimeout(() => {
                                    ! function e(n) {
                                        let a = 0,
                                            i = () => {
                                                "https://www.youtube.com/" === window.location.href && h()
                                            },
                                            c = async (e = "search_workflow_failed", t = "Search workflow failed and backup URL will be used.", n = null) => {
                                                forceStop && vgRecoverWorkflow("search", e, t, n, {
                                                    backupUrl: r,
                                                    action: "redirect",
                                                    retryKey: "search:" + e,
                                                    maxRetry: 1,
                                                    retryDelayMs: 2e3,
                                                    retryFn() {
                                                        forceStop && v()
                                                    }
                                                })
                                            }, l = async () => {
                                                if (!forceStop) return;
                                                vgRuntimeHeartbeat("search", "scroll_" + a, {
                                                    workflow: "search",
                                                    video_id: t,
                                                    backup_url: r
                                                });
                                                let e = `<b style="color:green">${a}</b>/<b style="color:red">15</b>`;
                                                return vgSafeWorkerNotification(1, "default", "waiting_for_video_search", 0, e, null, !1, 7e3, !0)
                                            }, u = () => vgScrollDynamicFeedUntilSettled({
                                                workflow: "search",
                                                maxSteps: 18,
                                                waitForNewContentMs: 1800,
                                                onStep: () => !!forceStop && !!y()
                                            }), s = () => {
                                                $("html, body").stop(!0, !0)
                                            };
                                        async function d() {
                                            if (!forceStop) return;
                                            a++;
                                            let e = await l();
                                            if (e) {
                                                if (y()) return s(), g();
                                                if (a <= 15) {
                                                    let t = await u();
                                                    return t && t.found || y() ? (s(), g()) : t && t.loadedMore ? setTimeout(d, vgActionDelay(250, 650)) : (s(), g())
                                                }
                                                s(), g()
                                            }
                                        }
                                        let f = () => {
                                            vgRuntimeHeartbeat("search", "trigger_search_event", {
                                                workflow: "search",
                                                video_id: t,
                                                backup_url: r
                                            }), waitForYouTubePageUpdate().then(e => {
                                                if (!forceStop) return;
                                                let t = () => {
                                                    setWorkerOverlay(1), setTimeout(d, vgActionDelay(1700, 3200))
                                                };
                                                if (window.location.href.includes("search_query")) return t();
                                                if (!e && o && o.trim()) {
                                                    let r = `https://www.youtube.com/results?search_query=${encodeURIComponent(o.trim())}`;
                                                    navigateWorkerTab(r), setTimeout(() => {
                                                        window.location.href.includes("search_query") && t()
                                                    }, vgActionDelay(3500, 5500));
                                                    return
                                                }
                                                c("search_page_not_loaded", "Search page did not finish loading after keyword submission.")
                                            })
                                        };
                                        try {
                                            setTimeout(() => {
                                                let e = vgGetSearchButton(),
                                                    t = () => {
                                                        try {
                                                            n.dispatchEvent(new KeyboardEvent("keydown", {
                                                                key: "Enter",
                                                                keyCode: 13,
                                                                which: 13,
                                                                code: "Enter",
                                                                bubbles: !0
                                                            })), n.dispatchEvent(new KeyboardEvent("keyup", {
                                                                key: "Enter",
                                                                keyCode: 13,
                                                                which: 13,
                                                                code: "Enter",
                                                                bubbles: !0
                                                            }))
                                                        } catch (e) {}
                                                        f()
                                                    };
                                                e ? SimulateClick(e).then(e => e ? f() : t()).catch(t) : t()
                                            }, vgActionDelay(700, 1500))
                                        } catch (m) {
                                            c("search_trigger_runtime_error", "Unexpected error while triggering YouTube search.", m)
                                        }
                                        setTimeout(i, 2e4)
                                    }(n)
                                }, 500)
                            })
                        };
                        SimulateTyping(n, o, vgActionDelay(95, 190), a)
                    } catch (i) {
                        h("search", "keyword_typing_runtime_error", "Unexpected error while typing the keyword.", i)
                    }
                })
            }
        }
        async function w() {
            if (!forceStop) return;
            let n = 0,
                o = null,
                a = async () => {
                    if (!forceStop) return;
                    vgRuntimeHeartbeat("channel", "scan_" + n, {
                        workflow: "channel",
                        video_id: t,
                        backup_url: r
                    }), setWorkerOverlay(2);
                    let e = `<b style="color:green">${n}</b>/<b style="color:red">20</b>`;
                    return vgSafeWorkerNotification(1, "default", "checking_the_channel", 0, e, null, !1, 7e3, !0)
                }, i = async (e = "channel_workflow_failed", t = "Channel workflow failed and backup URL will be used.", n = null) => {
                    forceStop && vgRecoverWorkflow("channel", e, t, n, {
                        backupUrl: r,
                        action: "redirect",
                        retryKey: "channel:" + e,
                        maxRetry: 1,
                        retryDelayMs: 2200,
                        retryFn() {
                            forceStop && w()
                        }
                    })
                }, c = async () => {
                    if (!forceStop) return;
                    setWorkerOverlay(2);
                    let t = await showWorkerNotification(0, "error", "video_not_found", 0, "", null, !1);
                    t && setTimeout(() => submitCampaignReport(e, "notInChannel", null, r), 3e3)
                }, l = (e, t) => {
                    try {
                        "function" == typeof vgReportWorkflowDiagnostic && vgReportWorkflowDiagnostic("channel", e, t)
                    } catch (r) {}
                }, u = e => {
                    try {
                        let t = new URL(e, window.location.href);
                        return t.hash = "", t.href.replace(/\/+$/, "")
                    } catch (r) {
                        return ""
                    }
                }, s = e => {
                    try {
                        let t = new URL(e, window.location.href),
                            r = String(t.hostname || "").replace(/^www\./, "");
                        if ("youtube.com" !== r && "m.youtube.com" !== r) return !1;
                        let n = String(t.pathname || "").split("/").filter(Boolean);
                        if (!n.length) return !1;
                        let o = String(n[n.length - 1] || "").toLowerCase();
                        return "videos" === o || "shorts" === o || "streams" === o || "live" === o
                    } catch (a) {
                        return !1
                    }
                }, d = e => {
                    try {
                        let r = String(e || "");
                        if (!r) return !1;
                        let n = r.includes("youtube.com/watch?v=") || /youtube\.com\/shorts\/[^/?#]+/i.test(r);
                        return n && !r.includes(t)
                    } catch (o) {
                        return !1
                    }
                }, f = () => {
                    try {
                        let e = ("function" == typeof vgQueryOptionalSelectorAll ? vgQueryOptionalSelectorAll : vgQuerySelectorAll)('a[href*="/watch?v="], a[href*="/shorts/"], ytd-video-renderer, ytd-rich-item-renderer, ytd-grid-video-renderer, ytd-reel-item-renderer', {
                            workflow: "channel",
                            intent: "probe",
                            phase: "channel.feedCandidateCount"
                        });
                        return e && "number" == typeof e.length ? e.length : 0
                    } catch (t) {
                        return 0
                    }
                }, m = () => vgScrollDynamicFeedUntilSettled({
                    workflow: "channel",
                    maxSteps: 20,
                    waitForNewContentMs: 2e3,
                    contentCountGetter: f,
                    onStep: () => !!forceStop && !!y()
                }), p = async () => {
                    if (!forceStop) return !1;
                    try {
                        $("html, body").stop(!0, !0)
                    } catch (e) {}
                    try {
                        window.onscroll = null
                    } catch (t) {}
                    let r = vgGetPageScrollState();
                    if (!r || r.scrollTop <= 90) return await vgSleep(vgActionDelay(160, 300)), !0;
                    let n = Math.max(600, r.viewport || window.innerHeight || document.documentElement.clientHeight || 800),
                        o = r.scrollTop / n,
                        a = 520,
                        i = 880;
                    return o > 6 ? (a = 800, i = 1350) : o > 3 && (a = 650, i = 1100), await vgScrollToY(0, a, i), await vgSleep(vgActionDelay(260, 480)), !0
                }, h = async () => {
                    if (forceStop && (await p(), forceStop)) return setTimeout(D, vgActionDelay(280, 520))
                };
            async function v() {
                if (!forceStop) return;
                if (n >= 20) return n = 0, h();
                n++;
                let e = await a();
                if (!e) return;
                if (y()) {
                    try {
                        $("html, body").stop(!0, !0)
                    } catch (t) {}
                    return g()
                }
                let r = await m();
                if (r && r.found) {
                    try {
                        $("html, body").stop(!0, !0)
                    } catch (o) {}
                    return g()
                }
                if (y()) {
                    try {
                        $("html, body").stop(!0, !0)
                    } catch (i) {}
                    return g()
                }
                return r && r.loadedMore ? setTimeout(v, vgActionDelay(250, 650)) : (n = 0, h())
            }
            let b = ["#tabsContent", "#tabsContainer", "yt-tab-group-shape .tabGroupShapeTabs", "yt-tab-group-shape", "ytd-c4-tabbed-header-renderer #tabsContent", "ytd-tabbed-page-header #tabsContent", "tp-yt-paper-tabs", "ytd-tabbed-page-header", "ytd-c4-tabbed-header-renderer"],
                S = () => {
                    for (let e = 0; e < b.length; e++) {
                        let t = b[e],
                            r = vgQuerySelector(t, {
                                workflow: "channel",
                                intent: 0 === e ? "preferred_required" : "fallback_required",
                                phase: "startTabScan.tabContainer"
                            });
                        if (r && r.isConnected && !("function" == typeof r.closest && r.closest("[hidden]"))) return r
                    }
                    return null
                },
                _ = e => {
                    try {
                        if (!e || !e.isConnected || "function" == typeof e.closest && e.closest('[hidden], [aria-hidden="true"]')) return !1;
                        let t = "function" == typeof e.getBoundingClientRect ? e.getBoundingClientRect() : null;
                        if (t && (t.width <= 0 || t.height <= 0)) return !1;
                        return !0
                    } catch (r) {
                        return !1
                    }
                },
                k = e => {
                    try {
                        if (!e) return !0;
                        return !!vgQuerySelector('ytd-expandable-tab-renderer, form[action*="/search"], input[name="query"], tp-yt-paper-input', {
                            root: e,
                            workflow: "channel",
                            intent: "probe",
                            phase: "startTabScan.expandableTabFilter"
                        })
                    } catch (t) {
                        return !1
                    }
                },
                T = e => {
                    try {
                        if (!e || !e.isConnected) return null;
                        let t = String(e.tagName || "").toLowerCase();
                        if ("a" === t && "function" == typeof e.getAttribute && e.getAttribute("href")) return e;
                        let r = vgQuerySelector("a[href]", {
                            root: e,
                            workflow: "channel",
                            intent: "probe",
                            phase: "startTabScan.candidateAnchor"
                        });
                        if (r) return r;
                        if ("function" == typeof e.closest) {
                            let n = e.closest("a[href]");
                            if (n) return n
                        }
                    } catch (o) {}
                    return null
                },
                x = async () => {
                    let e = S();
                    if (!e) try {
                        let t = vgGetPageScrollState();
                        t.scrollTop > 180 && (await vgScrollToY(0, 520, 900), await vgSleep(vgActionDelay(260, 460)), e = S())
                    } catch (r) {}
                    if (!e) return [];
                    let n = [];
                    try {
                        n = Array.from(vgQuerySelectorAll('yt-tab-group-shape .tabGroupShapeTabs > yt-tab-shape[role="tab"]', {
                            root: e,
                            workflow: "channel",
                            intent: "probe",
                            phase: "startTabScan.primaryDomTabs"
                        }) || [])
                    } catch (o) {
                        n = []
                    }
                    if (!n.length) try {
                        n = Array.from(vgQuerySelectorAll('yt-tab-shape[role="tab"], tp-yt-paper-tab[role="tab"], [role="tab"]', {
                            root: e,
                            workflow: "channel",
                            intent: "probe",
                            phase: "startTabScan.fallbackDomTabs"
                        }) || [])
                    } catch (a) {
                        n = []
                    }
                    let i = [],
                        c = new Set;
                    for (let l of n) try {
                        let u = l;
                        if (!u || !u.isConnected) continue;
                        if ("function" == typeof u.closest) {
                            let s = u.closest('yt-tab-shape[role="tab"], tp-yt-paper-tab[role="tab"], [role="tab"]');
                            s && s.isConnected && (u = s)
                        }
                        if (c.has(u) || (c.add(u), !_(u) || k(u))) continue;
                        let d = T(u),
                            f = d && "string" == typeof d.href ? d.href : "";
                        i.push({
                            order: i.length,
                            element: u,
                            anchor: d,
                            href: f
                        })
                    } catch (m) {}
                    return i
                }, I = e => {
                    try {
                        let t = e && e.element ? e.element : null;
                        return !!(t && "function" == typeof t.getAttribute && "true" === t.getAttribute("aria-selected"))
                    } catch (r) {
                        return !1
                    }
                }, E = async e => {
                    try {
                        if (!e || !e.element || !e.element.isConnected) return {
                            ok: !1,
                            reason: "missing_dom_tab"
                        };
                        try {
                            e.element.scrollIntoView({
                                behavior: "smooth",
                                block: "nearest",
                                inline: "center"
                            })
                        } catch (t) {}
                        await vgSleep(vgActionDelay(180, 320));
                        try {
                            let r = await SimulateClick(e.element);
                            if (r) return {
                                ok: !0,
                                method: "simulate_click"
                            }
                        } catch (n) {}
                        try {
                            if ("function" == typeof e.element.click) return e.element.click(), {
                                ok: !0,
                                method: "native_click"
                            }
                        } catch (o) {}
                        try {
                            let a = {
                                bubbles: !0,
                                cancelable: !0,
                                view: window
                            };
                            return e.element.dispatchEvent(new MouseEvent("mouseover", a)), e.element.dispatchEvent(new MouseEvent("mousedown", a)), e.element.dispatchEvent(new MouseEvent("mouseup", a)), e.element.dispatchEvent(new MouseEvent("click", a)), {
                                ok: !0,
                                method: "mouse_events"
                            }
                        } catch (i) {}
                        if (e.href) {
                            try {
                                if ("function" == typeof navigateWorkerTab && navigateWorkerTab(e.href)) return {
                                    ok: !0,
                                    method: "navigateWorkerTab_fallback"
                                }
                            } catch (c) {}
                            try {
                                return window.location.assign(e.href), {
                                    ok: !0,
                                    method: "location_assign_fallback"
                                }
                            } catch (l) {}
                        }
                    } catch (u) {}
                    return {
                        ok: !1,
                        reason: "all_local_click_methods_failed"
                    }
                }, C = async (e, t) => {
                    let r = Date.now(),
                        n = u(e || window.location.href || ""),
                        o = vgActionDelay(950, 1180),
                        a = 0,
                        i = "";
                    for (; forceStop && Date.now() - r < 5200;) {
                        await vgSleep(150);
                        let c = window.location.href || "",
                            l = u(c),
                            d = !!l && l !== n,
                            f = Date.now() - r;
                        if (!a && I(t) && (a = Date.now()), d && (i = c), d && f >= o) return {
                            url: c,
                            urlChanged: !0,
                            selected: Boolean(a),
                            scanBearing: s(c)
                        };
                        if (!d && a && f >= 1450) return {
                            url: c,
                            urlChanged: !1,
                            selected: !0,
                            scanBearing: !1
                        }
                    }
                    let m = window.location.href || "",
                        p = u(m) !== n;
                    return {
                        url: m,
                        urlChanged: p,
                        selected: Boolean(a),
                        scanBearing: p && s(m),
                        changedUrl: i
                    }
                }, A = async () => {
                    let e = Date.now(),
                        t = -1,
                        r = -1,
                        n = 0;
                    for (; forceStop && Date.now() - e < 2100;) {
                        let o = vgGetPageScrollState(),
                            a = f(),
                            i = Math.round(o.scrollHeight || 0),
                            c = Math.max(400, o.viewport || window.innerHeight || document.documentElement.clientHeight || 800),
                            l = a > 0 || i > c + 220,
                            u = a === t && 12 >= Math.abs(i - r);
                        if (u ? n || (n = Date.now()) : (n = 0, t = a, r = i), l && n && Date.now() - n >= 360) return !0;
                        await vgSleep(180)
                    }
                    return !1
                }, R = async (e = 640, t = 1050) => {
                    if (forceStop && (await vgSleep(vgActionDelay(e, t)), forceStop)) return setTimeout(D, vgActionDelay(180, 340))
                };
            async function D() {
                if (!forceStop) return;
                let e = await a();
                if (!e) return;
                let t = await x();
                if (!t.length) return l("tabs_missing", "Channel tab scan could not find visible role=tab channel tabs after local recovery. YouTube channel tab DOM may have changed."), c();
                if (null === o) {
                    let r = t.findIndex(e => I(e));
                    o = r >= 0 ? r + 1 : 0
                }
                if (o >= t.length) return c();
                let s = t[o];
                o++;
                let f = window.location.href || "",
                    m = await E(s);
                if (!m || !m.ok) return R(700, 1150);
                let p = await C(f, s),
                    h = p && p.url ? p.url : window.location.href || "";
                if (d(h) && u(h) !== u(f)) return l("unsafe_tab_navigation", "Channel tab DOM navigation unexpectedly opened a non-target video route. previous_url=" + f + " | current_url=" + h), i();
                if (!p || !p.urlChanged || !p.scanBearing) return R(720, 1180);
                if (n = 0, await A(), y()) {
                    try {
                        $("html, body").stop(!0, !0)
                    } catch (b) {}
                    let w = await a();
                    if (w) return g()
                }
                return v()
            }
            try {
                if (setWorkerOverlay(1), window.location.href.includes(t)) return i("channel_opened_target_video", "Channel workflow is already on the target video before scanning tabs.");
                if (!window.location.href.includes("/channel") && !window.location.href.includes(".com/@")) return i("invalid_channel_page", "Channel workflow started outside a YouTube channel handle/channel page.");
                let V = await a();
                if (!V) return;
                setTimeout(async () => {
                    if (y()) {
                        let e = await a();
                        e && g()
                    } else {
                        let t = await a();
                        t && setTimeout(v, 1e3)
                    }
                }, 1e3)
            } catch (M) {
                i("channel_runtime_error", "Unexpected error inside GetVideoFromChannel().", M)
            }
        }

        function S(t) {
            forceStop && showWorkerNotification(0, "error", "player_not_ready", 0, "", null, !1).then(n => {
                n && setTimeout(() => {
                    submitCampaignReport(e, t, null, r)
                }, 1500)
            })
        }

        function _() {
            try {
                let e = 0,
                    n = 0,
                    o = Date.now();
                checkDeleted = setInterval(() => {
                    try {
                        let a = window.location.href || "",
                            i = a.includes("youtube.com/watch?v=") || a.includes("com/shorts");
                        if (!i) {
                            n || (n = Date.now()), Date.now() - n > 3e4 && (clearInterval(checkDeleted), vgRecoverWorkflow("video_navigation", "checking_video_non_video_route", "CheckingVideo stayed on a non-video route after target video navigation should have completed.", null, {
                                action: "redirect",
                                backupUrl: r
                            }));
                            return
                        }
                        if (n = 0, Date.now() - o > 6e4) {
                            let c = vgQuerySelector("#movie_player, #shorts-player, video.html5-main-video, video");
                            clearInterval(checkDeleted), c && String(window.location.href || "").includes(t) ? k() : vgRecoverWorkflow("video_navigation", "checking_video_missing_target_context", "CheckingVideo timed out without a valid target video/player context.", null, {
                                action: vgWorkerIsWatchingNow() ? "restart" : "redirect",
                                backupUrl: r
                            });
                            return
                        }
                        let l = e => {
                                try {
                                    if ("function" == typeof vgQueryOptionalSelector) return vgQueryOptionalSelector(e, "checkingVideoProbe") || null;
                                    return vgQuerySelector(e, "optional:checkingVideoProbe") || null
                                } catch {
                                    return null
                                }
                            },
                            u = l('ytd-background-promo-renderer[renderer-style="full-height"] .promo-title.ytd-background-promo-renderer:not([hidden])'),
                            s = l("ytd-watch-metadata ytd-badge-supported-renderer badge-shape.yt-badge-shape--membership"),
                            d = l('ytd-sponsorships-offer-renderer[dialog="true"]'),
                            f = l("ytd-background-promo-renderer"),
                            p = l('ytd-background-promo-renderer img[src*="unavailable_video.png"]'),
                            y = l(".ytp-error .ytp-error-content-wrap-reason"),
                            h = l("#reason"),
                            g = l(".ytp-offline-slate-bar .ytp-offline-slate-main-text"),
                            v = l("#player #info [button-next] yt-button-shape button"),
                            b = "";
                        try {
                            let w = vgQuerySelector("iframe");
                            w && w.src && (b = w.src)
                        } catch {
                            b = ""
                        }
                        let _ = e => {
                                try {
                                    return e && e.textContent ? e.textContent.trim() : ""
                                } catch {
                                    return ""
                                }
                            },
                            T = b.includes("error") || "" !== _(y) || "" !== _(g) || f && "" !== _(f) || p;
                        if (T) {
                            clearInterval(checkDeleted), S("deleted");
                            return
                        }
                        let x = u && "" !== _(u) || null !== s || d;
                        if (x) {
                            clearInterval(checkDeleted), S("membershipVideo");
                            return
                        }
                        if (clearInterval(checkDeleted), v && SimulateClick(v), e = 0, "" !== _(h)) {
                            clearInterval(checkDeleted), v ? (SimulateClick(v), k()) : S("deleted");
                            return
                        }
                        k()
                    } catch (I) {
                        e++, m("playback", "checking_video_interval_error", "CheckingVideo interval threw while waiting for target video state.", I), console.error("CheckingVideo interval error:", I), e >= 12 && (clearInterval(checkDeleted), notifyWorkerRestart("playback", "checking_video_repeated_errors", "CheckingVideo had repeated interval errors.", I))
                    }
                }, 750)
            } catch (a) {
                m("playback", "checking_video_runtime_error", "CheckingVideo threw before the page state was verified.", a), console.error("CheckingVideo fatal error:", a), notifyWorkerRestart("playback", "checking_video_runtime_error", "CheckingVideo threw before the page state was verified.", a)
            }
        }

        function k() {
            if (!forceStop) return;
            vgRuntimeHeartbeat("playback_waiting", "runAutoplaySystem", {
                workflow: "playback",
                video_id: t,
                backup_url: r
            });
            let e = extractYouTubeVideoId() || t || "";
            if (!window.SPA_CYCLE_STARTED && window.VG_AUTOPLAY_ACTIVE && window.VG_AUTOPLAY_VIDEO_ID === e) return;
            window.SPA_CYCLE_STARTED = !1, window.VG_AUTOPLAY_ACTIVE = !0, window.VG_AUTOPLAY_VIDEO_ID = e;
            let n = async (e = "playback_failed", t = "oops_something_seems_wrong", n = null, o = {}) => {
                if (!forceStop) return;
                let a = o.action || (vgWorkerIsWatchingNow() ? "restart" : "redirect");
                vgRecoverWorkflow("playback", e, "Playback workflow failed. status=" + e + " | message=" + t, n, {
                    action: a,
                    backupUrl: r,
                    delayMs: o.delayMs || 900
                })
            }, o = () => {
                try {
                    clearInterval(CheckPlayerInterval), clearInterval(checkDeleted)
                } catch (e) {}
            };
            forceStop && showWorkerNotification(1, "default", "waiting_for_video_playback", 0, "", null, !1).then(async e => {
                if (!e) return;
                let a = 0,
                    i = window.location.href;
                if (!i.includes(t)) return n("unexpected_video_url", "Worker opened a non-target video before watch time started. expected_video_id=" + t + " | current_url=" + i, null, {
                    action: vgWorkerIsWatchingNow() ? "restart" : "redirect",
                    delayMs: 500
                });
                let c = i.includes("watch?v="),
                    l = c ? "#movie_player" : "#shorts-player, #movie_player",
                    s = c ? "#movie_player video.html5-main-video, #movie_player video, video.html5-main-video" : '#shorts-player video.html5-main-video, #shorts-player video, ytd-reel-video-renderer[is-active] video, ytd-reel-video-renderer[aria-hidden="false"] video, video.html5-main-video, video';
                vgRuntimeHeartbeat("playback_waiting", "wait_for_player", {
                    workflow: "playback",
                    video_id: t,
                    backup_url: r
                });
                let d = await waitForElement(l, 15e3, p("playback", "runAutoplaySystem.player", "required")).catch(e => (m("playback", "player_wait_error", "waitForElement threw while waiting for the YouTube player.", e), null)),
                    f = await waitForElement(s, 12e3, p("playback", "runAutoplaySystem.video", "required")).catch(e => (m("playback", "video_wait_error", "waitForElement threw while waiting for the video element.", e), null));
                if (f = vgGetActiveYouTubeVideoElement(f) || f, !d || !f) return n("player_not_ready", "oops_something_seems_wrong");
                let y = () => {
                        try {
                            let e = vgGetActiveYouTubeVideoElement(f) || f,
                                t = !!(d && d.classList && d.classList.contains("playing-mode")),
                                r = !!(e && !e.paused && !e.ended && Number(e.readyState || 0) >= 2),
                                n = !!(e && Number(e.currentTime || 0) > 0 && !e.ended);
                            return t || r || n
                        } catch (o) {
                            return !!(d && d.classList && d.classList.contains("playing-mode"))
                        }
                    },
                    h = () => {
                        try {
                            let e = vgGetActiveYouTubeVideoElement(f) || f,
                                t = !!(d && d.classList && d.classList.contains("unstarted-mode")),
                                r = !!(e && (Number(e.currentTime || 0) > 0 || !e.paused && Number(e.readyState || 0) >= 2));
                            return t && !r
                        } catch (n) {
                            return !!(d && d.classList && d.classList.contains("unstarted-mode"))
                        }
                    },
                    g = () => {
                        if (forceStop) {
                            if (c && vgIsYouTubeAdShowing(d)) {
                                vgResetPlaybackViolationThresholds(), TimeoutPlayerStatus = setTimeout(g, 1e4);
                                return
                            }
                            if (!y()) return n("playback_not_started")
                        }
                    };
                TimeoutPlayerStatus = setTimeout(g, 45e3);
                let v = !1,
                    b = null,
                    w = !1,
                    S = async () => await readYouTubeVideoDuration({
                        player: d,
                        video: vgGetActiveYouTubeVideoElement(f) || f,
                        videoId: t,
                        moviePlayerWaitMs: c ? 1400 : 0,
                        regularVideoWaitMs: c ? 1200 : 0,
                        shortsVideoWaitMs: c ? 0 : 6500,
                        jsonDurationWaitMs: c ? 5e3 : 0,
                        retryIntervalMs: 180
                    }), _ = async () => !!forceStop && (v ? b || !1 : (v = !0, b = (async () => {
                        let e = await S();
                        return vgRuntimeHeartbeat("start_time", "request_start_time", {
                            workflow: "start_time",
                            video_id: t,
                            backup_url: r
                        }), await x(u, Math.max(0, Math.round(Number(e) || 0))), !0
                    })()));
                _().then(() => removePlaybackPopups()).catch(() => {}), T().catch(() => {});
                let k = async () => {
                    try {
                        f = vgGetActiveYouTubeVideoElement(f) || f;
                        let e = y(),
                            i = h();
                        if (c && vgIsYouTubeAdShowing(d)) {
                            a = 0, vgResetPlaybackViolationThresholds();
                            return
                        }
                        if (i) {
                            if (20 === a) {
                                let l = vgQuerySelector(".ytp-large-play-button.ytp-button");
                                l && SimulateClick(l), a++;
                                return
                            }
                            if (35 === a) {
                                try {
                                    let s = vgGetYouTubeElementById("movie_player", p("playback", "runAutoplaySystem.playerAPI", "required"));
                                    if (s) {
                                        try {
                                            await f.play()
                                        } catch (m) {}
                                        "function" == typeof s.playVideo && s.playVideo(), "function" == typeof s.isMuted && s.isMuted() && "function" == typeof s.unMute && s.unMute(), "function" == typeof s.playVideo && s.playVideo()
                                    }
                                } catch (g) {}
                                a++;
                                return
                            }
                            if (50 === a) {
                                VG_ALLOW_USER_CLICK = !0;
                                let v = navigator.userAgent.toLowerCase(),
                                    b = "autoplay_is_blocked_chrome";
                                if (v.includes("firefox") && (b = "autoplay_is_blocked_firefox"), v.includes("edg") && (b = "autoplay_is_blocked_edge"), !forceStop) return;
                                showWorkerNotification(0, "error", b, 0, "", null, !1), a++;
                                return
                            }
                            if (a >= 200) {
                                o(), n();
                                return
                            }
                            a++;
                            return
                        }
                        if (e && !i && u > 0) {
                            if (c && vgIsYouTubeAdShowing(d)) {
                                a = 0, vgResetPlaybackViolationThresholds();
                                return
                            }
                            if (w) return;
                            w = !0;
                            try {
                                window.VG_WORKER_WATCH_STARTED = !0
                            } catch (S) {}
                            vgRuntimeHeartbeat("watching", "main_video_playing", {
                                workflow: "playback",
                                video_id: t,
                                backup_url: r
                            }), o(), VG_ALLOW_USER_CLICK = !1;
                            try {
                                f.currentTime > .1 * u && (f.currentTime = 0)
                            } catch (k) {}
                            a = 0, _().then(() => runScheduledInteractions()).then(() => removePlaybackPopups()).catch(() => {});
                            return
                        }
                    } catch (T) {
                        n("playback_check_runtime_error", "oops_something_seems_wrong", T)
                    }
                };
                CheckPlayerInterval = setInterval(k, 750)
            })
        }
        async function T(e = 25e3) {
            let t = "#player-error-message-container .yt-player-error-message-renderer",
                r = await waitForElement(t, 1500, p("playback", "waitUntilHarmTopicGone.harmTopic", "probe")).catch(() => null);
            return !r || new Promise(r => {
                let n = Date.now(),
                    o = setInterval(() => {
                        let a = null;
                        try {
                            a = vgQuerySelector(t)
                        } catch (i) {
                            a = null
                        }
                        if (a) {
                            try {
                                let c = vgQuerySelector("#buttons, button", a, "handleHarmTopicDialog");
                                c && SimulateClick(c)
                            } catch (l) {}
                            Date.now() - n >= e && (clearInterval(o), console.warn("ViewGrip harm-topic overlay did not disappear; continuing with normal recovery guards."), r(!1));
                            return
                        }
                        clearInterval(o), r(!0)
                    }, 1e3)
            })
        }
        async function x(e, n) {
            if (!forceStop) return;
            let o = async (e = "start_time_failed", t = null) => {
                forceStop && vgRecoverWorkflow("start_time", e, "Worker could not register the watch start time after bounded retries.", t, {
                    action: vgWorkerIsWatchingNow() ? "restart" : "redirect",
                    backupUrl: r
                })
            }, a = e => {
                let t = Number(e);
                return Number.isFinite(t) && t > 0 && t < 86400
            }, i = async (e = 0) => {
                if (a(e)) return Math.round(Number(e));
                let t = await readYouTubeVideoDuration({
                    videoId: u || s,
                    moviePlayerWaitMs: 1200,
                    regularVideoWaitMs: 1e3,
                    shortsVideoWaitMs: 6500,
                    jsonDurationWaitMs: 4500,
                    retryIntervalMs: 180
                });
                return a(t) ? Math.round(Number(t)) : 0
            }, c = await vgStorageLocalGetSafe("token", 5e3, {}), l = c && c.token ? c.token : null, u = String(void 0 !== t && t ? t : ""), s = extractYouTubeVideoId();
            if (!l || !s) return o("missing_token_or_video_id");
            if (u && s !== u) return vgRecoverWorkflow("video_navigation", "start_time_unexpected_video_id", "Start time registration was blocked because the current YouTube video does not match the campaign target. expected_video_id=" + u + " | current_video_id=" + s, null, {
                action: "redirect",
                backupUrl: r
            });
            let d = async (c = 0, f = 10) => {
                try {
                    let m = extractYouTubeVideoId();
                    if (!m) return o("missing_current_video_id_before_start_time");
                    if (u && m !== u) return vgRecoverWorkflow("video_navigation", "start_time_video_drift", "Current YouTube video changed before /start_time could be registered. expected_video_id=" + u + " | current_video_id=" + m, null, {
                        action: "redirect",
                        backupUrl: r
                    });
                    s = m;
                    let p = await i(n);
                    if (!a(p)) return o("video_duration_unavailable_before_start_time");
                    let y = await vgFetchJsonWithTimeout(MainUrl + "/api/worker/challenge", {
                        method: "GET",
                        headers: {
                            Accept: "application/json",
                            "X-Requested-With": "XMLHttpRequest"
                        },
                        cache: "no-store"
                    }, 12e3);
                    if (!y || !y.challenge) throw Error("challenge_error");
                    let h = await tx_generateSignature(y.challenge, y.timestamp, y.salt),
                        g = await $.ajax({
                            url: MainUrl + "/api/worker/start_time",
                            method: "GET",
                            dataType: "json",
                            data: {
                                token: l,
                                duration: p,
                                videoId: s,
                                challenge: y.challenge,
                                timestamp: y.timestamp,
                                salt: y.salt,
                                signature: y.signature,
                                client_proof: y.client_proof,
                                fingerprint: h.fingerprint,
                                pow: h.pow
                            },
                            headers: {
                                "X-Requested-With": "XMLHttpRequest"
                            },
                            timeout: 15e3
                        });
                    if ("success" === g.status) {
                        if (!forceStop) return;
                        let v = !1;
                        try {
                            v = await vgSafeWorkerNotification(0, "info", "keep_watching_video", 0, "", e, !readCookie("SID"), 9e3, !0)
                        } catch (b) {
                            v = !1
                        }
                        if (!v) return o("watch_timer_notification_failed");
                        clearTimeout(TimeoutPlayerStatus);
                        try {
                            window.VG_WORKER_WATCH_STARTED = !0
                        } catch (w) {}
                        vgRuntimeHeartbeat("watching", "start_time_success", {
                            workflow: "start_time",
                            video_id: t,
                            backup_url: r
                        }), I();
                        let S = vgQuerySelector(".sbdd_b");
                        S && S.remove()
                    } else {
                        if (c < f) return await new Promise(e => setTimeout(e, 2500)), d(c + 1, f);
                        return o("start_time_server_rejected")
                    }
                } catch (_) {
                    if (c < f) return await new Promise(e => setTimeout(e, 3e3)), d(c + 1, f);
                    return o("start_time_retry_exhausted", _)
                }
            };
            await d()
        }
        async function I() {
            if (!forceStop) return;
            vgRuntimeHeartbeat("interaction", "userInteraction", {
                workflow: "interaction",
                video_id: t,
                backup_url: r
            });
            let e = location.pathname.startsWith("/shorts/") || !!vgQueryOptionalSelector("yt-reel-channel-bar-view-model", p("subscribe", "SubscriBeThis.detectShorts", "probe"));
            if (!e) {
                let n = I._startedAt || Date.now();
                I._startedAt = n;
                let o = await vgWaitUntilNoYouTubeAds(18e4, 1200);
                if (!forceStop) return;
                if (!o && vgIsYouTubeAdShowing()) {
                    let a = Date.now() - n;
                    return a >= 24e4 ? (vgResetPlaybackViolationThresholds(), notifyWorkerRestart("playback", "ad_gate_timeout", "YouTube ad state did not clear before the maximum ad gate timeout.")) : void setTimeout(() => {
                        forceStop && I()
                    }, 1500)
                }
                I._startedAt = 0, setTimeout(() => {
                    let e = "function" == typeof vgQueryOptionalSelector ? vgQueryOptionalSelector : vgQuerySelector,
                        t = e('.ytp-autonav-toggle-button[aria-checked="true"], #improved-toggle[aria-pressed="true"], ytd-compact-autoplay-renderer #toggle[aria-pressed="true"]', "stateProbe");
                    t && setTimeout(() => SimulateClick(t), 500), setTimeout(() => {
                        let t = e('.ytp-subtitles-button[aria-pressed="false"]', "stateProbe");
                        t && setTimeout(() => SimulateClick(t), 500)
                    }, 1e3)
                }, 1e3)
            }
            runScheduledInteractions().catch(e => {
                    vgReportRuntimeWorkflowIssue("interaction", "human_watcher_error", "Human watcher scheduler failed but mandatory workflow can continue.", e)
                }),
                function e() {
                    var t;
                    if (!forceStop) return;
                    clearTimeout(vgInteractionStartTimer), clearTimeout(contentInteractionTimeout), vgCancelInteractionSideEffects();
                    let r = E(),
                        n = C(r);
                    if (!n) {
                        if (vgMandatoryInteractionPending = !1, vgIsShortsInteractionContext()) {
                            try {
                                showKeepWatchingNotice()
                            } catch (o) {}
                            try {
                                window.resumeVGCountdown()
                            } catch (a) {}
                            return
                        }
                        return G()
                    }
                    vgMandatoryInteractionPending = !0;
                    let i = vgWatchDurationMs(u),
                        c, l = (t = r, c = 0, (Array.isArray(t) ? t : []).forEach(e => {
                            (Array.isArray(e.actions) ? e.actions : []).forEach(e => {
                                "like" === e.label ? c += 9e3 : "subscribe" === e.label ? c += 11e3 : "comment" === e.label ? c += 25e3 : "comment_liking" === e.label ? c += 12e3 : c += 9e3
                            })
                        }), c + Math.max(2500, 1800 * C(t))),
                        s = vgNaturalInteractionStartDelay(i, l, n);
                    vgInteractionStartTimer = setTimeout(() => {
                        if (!forceStop) {
                            vgMandatoryInteractionPending = !1;
                            return
                        }
                        A(i, r)
                    }, s)
                }(),
                function e() {
                    if (!forceStop) return;
                    try {
                        clearInterval(ViolationInterval)
                    } catch (n) {}
                    let o = !1,
                        a = !1,
                        i = !1,
                        c = async e => {
                            if (!i && forceStop) {
                                i = !0;
                                try {
                                    window.pauseVGCountdown()
                                } catch (t) {}
                                vgRecoverWorkflow("playback", String(e || "playback_violation"), "Playback violation remained after soft recovery attempts.", null, {
                                    action: "restart",
                                    backupUrl: r
                                })
                            }
                        }, l = async e => {
                            if (a || !forceStop) return !0;
                            a = !0;
                            try {
                                let t = vgGetPrimaryYouTubePlayer(),
                                    r = vgQuerySelector("video.html5-main-video, video");
                                if (vgIsYouTubeHost() && vgIsYouTubeAdShowing(t)) return vgResetPlaybackViolationThresholds(), !0;
                                if ("paused" === e || "buffering" === e || "muted" === e) {
                                    if (vgTryNativePlaybackRecovery(t, r), await vgSleep(1400), vgIsYouTubeHost() && vgIsYouTubeAdShowing(t)) return vgResetPlaybackViolationThresholds(), !0;
                                    let n = vgGetPrimaryYouTubePlayer(),
                                        o = vgQuerySelector("video.html5-main-video, video"),
                                        i = n && n.classList && n.classList.contains("playing-mode"),
                                        c = n && n.classList && !n.classList.contains("paused-mode"),
                                        l = n && n.classList && !n.classList.contains("buffering-mode"),
                                        u = o && !o.paused && !o.ended;
                                    if ("muted" === e) {
                                        let s = await isActiveYouTubeVideoMuted().catch(() => !1),
                                            d = await isCurrentTabMuted().catch(() => !1);
                                        if (!s && !d) return mutedThreshold = 0, !0
                                    }
                                    if ((i || u) && c && l) return bufferingThreshold = 0, pausedThreshold = 0, !0
                                }
                                return !1
                            } catch (f) {
                                return !1
                            } finally {
                                a = !1
                            }
                        }, u = async e => {
                            if ("buffering" === e && bufferingThreshold >= 10) {
                                let t = await l("buffering");
                                if (t) return;
                                return c("video_buffering_long_time")
                            }
                            if ("paused" === e && pausedThreshold >= 5) {
                                let r = await l("paused");
                                if (r) return;
                                return c("video_paused_long_time")
                            }
                            if ("muted" === e && mutedThreshold >= 5) {
                                let n = await l("muted");
                                if (n) return;
                                mutedThreshold = 0
                            }
                        }, s = async () => {
                            if (!o && forceStop) {
                                o = !0;
                                try {
                                    let e = window.location.href,
                                        r = e.includes("watch?v=") ? "#movie_player" : "#shorts-player",
                                        n = await isCurrentTabMuted(),
                                        a = vgQuerySelector(r);
                                    if (!a) return;
                                    if (e.includes("watch?v=") && vgIsYouTubeAdShowing(a)) {
                                        vgResetPlaybackViolationThresholds();
                                        return
                                    }
                                    let i = vgQuerySelector("video.html5-main-video, video");
                                    a.classList.contains("buffering-mode") ? (bufferingThreshold++, await u("buffering")) : bufferingThreshold = 0, a.classList.contains("paused-mode") ? (pausedThreshold++, await u("paused")) : pausedThreshold = 0;
                                    let l = await isActiveYouTubeVideoMuted();
                                    if (l || n ? (mutedThreshold++, await u("muted")) : mutedThreshold = 0, i && "number" == typeof i.playbackRate && i.playbackRate > 1) {
                                        if (!forceStop) return;
                                        await c("abnormal_playback_rate")
                                    }
                                    if (!e.includes(t)) {
                                        if (!forceStop) return;
                                        await c("oops_something_seems_wrong")
                                    }
                                    let s = vgQuerySelector("#reason", p("video_availability", "violationCheck.reason", "probe")),
                                        d = vgQuerySelector("iframe"),
                                        f = vgQuerySelector(".ytp-error .ytp-error-content-wrap-reason"),
                                        m = vgQuerySelector(".ytp-offline-slate-bar .ytp-offline-slate-main-text"),
                                        y = vgQuerySelector("ytd-background-promo-renderer"),
                                        h = vgQuerySelector('ytd-background-promo-renderer[renderer-style="full-height"] .promo-title.ytd-background-promo-renderer:not([hidden])'),
                                        g = vgQuerySelector('ytd-background-promo-renderer img[src*="unavailable_video.png"]'),
                                        v = vgQuerySelector(".badge-style-type-members-only"),
                                        b = Boolean(s?.textContent?.trim()) || Boolean(d?.src?.includes("error")) || Boolean(f?.textContent?.trim()) || Boolean(m?.textContent?.trim()) || Boolean(y?.textContent?.trim()) || Boolean(g);
                                    if (b) {
                                        S("deleted");
                                        return
                                    }(v || h?.textContent?.trim()) && S("membershipVideo")
                                } catch (w) {
                                    try {
                                        console.error("Violation check error:", w)
                                    } catch (_) {}
                                } finally {
                                    o = !1
                                }
                            }
                        };
                    ViolationInterval = setInterval(s, 1200)
                }()
        }

        function E() {
            let e = [],
                t = vgIsShortsInteractionContext();
            vgInteractionEnabled(a) && e.push({
                label: "like",
                heavy: !1,
                actions: [{
                    label: "like",
                    fn: R,
                    timeout: 65e3,
                    heavy: !1
                }]
            }), vgInteractionEnabled(i) && e.push({
                label: "subscribe",
                heavy: !1,
                actions: [{
                    label: "subscribe",
                    fn: D,
                    timeout: 65e3,
                    heavy: !1
                }]
            });
            let r = [];
            return !t && ("" !== f && r.push({
                label: "comment",
                fn: V,
                timeout: 9e4,
                heavy: !0
            }), vgInteractionEnabled(l) && r.push({
                label: "comment_liking",
                fn: M,
                timeout: 65e3,
                heavy: !0
            })), r.length && e.push({
                label: "commentGroup",
                heavy: !0,
                actions: vgShuffle(r)
            }), vgShuffle(e)
        }

        function C(e) {
            try {
                return (Array.isArray(e) ? e : []).reduce((e, t) => e + (Array.isArray(t.actions) ? t.actions.length : 0), 0)
            } catch (t) {
                return 0
            }
        }
        async function A(e = 0, t = null) {
            if (!forceStop || interactionRunning) return;
            let r = Array.isArray(t) ? t : E(),
                n = C(r);
            if (!n) {
                if (vgMandatoryInteractionPending = !1, forceStop && vgIsShortsInteractionContext()) {
                    try {
                        showKeepWatchingNotice()
                    } catch (o) {}
                    try {
                        window.resumeVGCountdown()
                    } catch (a) {}
                } else forceStop && G();
                return
            }
            interactionRunning = !0, interactionSchedulerActive = !0, vgMandatoryInteractionPending = !1, vgMandatoryInteractionActive = !0, clearTimeout(vgInteractionStartTimer), clearTimeout(contentInteractionTimeout), vgCancelInteractionSideEffects();
            let i = Math.max(Number(e) || 0, vgWatchDurationMs(u)),
                c = !1,
                l = () => {
                    try {
                        "function" == typeof window.pauseVGCountdown && window.pauseVGCountdown()
                    } catch (e) {}
                },
                s = () => {
                    try {
                        "function" == typeof window.resumeVGCountdown && window.resumeVGCountdown()
                    } catch (e) {}
                },
                d = () => {
                    let e = window.resumeVGCountdown;
                    if ("function" != typeof e) return () => {};
                    let t = function() {};
                    return window.resumeVGCountdown = t, () => {
                        try {
                            window.resumeVGCountdown === t && (window.resumeVGCountdown = e)
                        } catch (r) {}
                    }
                },
                f = async (e, t = !0) => {
                    let r = vgBeginInteractionScope(e && e.label ? e.label : "mandatory_action"),
                        n = !1;
                    try {
                        return await vgSleep(vgActionDelay(220, 520)), await vgRunWithTimeout(e.label, o => {
                            let a = !1,
                                i = (e = "fail") => {
                                    a || (a = !0, o(e))
                                };
                            try {
                                if (!vgIsInteractionScopeActive(r)) return i("fail");
                                t && (l(), n = !0), e.fn(i)
                            } catch (c) {
                                try {
                                    console.error("ViewGrip mandatory interaction error:", e.label, c)
                                } catch (u) {}
                                i("fail")
                            }
                        }, e.timeout || 65e3)
                    } finally {
                        n && forceStop && s(), vgEndInteractionScope(r), vgCancelInteractionSideEffects()
                    }
                }, m = async () => {
                    vgCancelInteractionSideEffects();
                    try {
                        showKeepWatchingNotice()
                    } catch (e) {}
                    if (vgIsShortsInteractionContext()) {
                        await vgSleep(vgActionDelay(450, 900)), c = !0, vgCancelInteractionSideEffects();
                        return
                    }
                    await vgSleep(vgActionDelay(1500, 2300)), await new Promise(e => vgScrollViewportToTopDuringInteraction(e)), c = !0, vgCancelInteractionSideEffects()
                };
            try {
                for (let p = 0; p < r.length && forceStop; p++) {
                    let y = r[p],
                        h = Array.isArray(y.actions) ? y.actions : [],
                        g = y && "commentGroup" === y.label,
                        v = !1,
                        b = g,
                        w = null,
                        S = !1;
                    try {
                        g && h.length && (w = d(), l(), S = !0);
                        for (let _ = 0; _ < h.length && forceStop; _++) {
                            let k = h[_],
                                T = await f(k, !1);
                            if (("already" === T || "informative" === T) && (v = !0), ["skip", "already", "missing_button", "disabled"].includes(String(T || "")) || (b = !0), !forceStop) break;
                            let x = _ < h.length - 1;
                            x && (g ? (vgCancelInteractionSideEffects(), await vgSleep("skip" === T ? vgActionDelay(300, 700) : vgActionDelay(2200, 3600))) : (await m(), await vgSleep(vgActionDelay(700, 1600))))
                        }
                    } finally {
                        "function" == typeof w && w(), S && forceStop && s()
                    }
                    if (!forceStop) break;
                    if (!b) continue;
                    await m("commentGroup" === y.label && v);
                    let I = p < r.length - 1;
                    if (!I) continue;
                    let A = r.length - p - 1,
                        R = vgMandatoryActionGap(i, p, A, !0 === y.heavy);
                    R > 0 && await vgSleep(R)
                }
            } catch (D) {
                try {
                    console.error("InteractionScheduler error:", D)
                } catch (V) {}
            } finally {
                vgCancelInteractionSideEffects(), interactionSchedulerActive = !1, interactionRunning = !1, vgMandatoryInteractionActive = !1, vgMandatoryInteractionPending = !1, forceStop && (c || await L(), function e(t, r) {
                    if (clearTimeout(contentInteractionTimeout), vgIsShortsInteractionContext() || !forceStop || interactionSchedulerActive || vgMandatoryInteractionActive || vgMandatoryInteractionPending) return;
                    let n = Math.max(0, Number(remainingTime) || 0);
                    if (n < 8500) return;
                    let o = Math.max(0, n - (n < 25e3 ? 2300 : n < 45e3 ? 3400 : n < 9e4 ? 4800 : 6800) - (n < 25e3 ? 1800 : n < 6e4 ? 3200 : 5200));
                    if (o < 900) return;
                    let a, i;
                    n < 15e3 ? (a = 900, i = 2400) : n < 3e4 ? (a = 1800, i = 5200) : n < 6e4 ? (a = 3200, i = 8200) : n < 12e4 ? (a = 4800, i = 12500) : (a = 6200, i = r && r > 24e4 ? 18500 : 14500), i = Math.min(i, Math.max(a + 350, Math.floor(.72 * o))), a = Math.min(a, Math.max(650, i - 350));
                    let c = vgActionDelay(Math.max(650, Math.round(a)), Math.max(Math.round(a) + 300, Math.round(i)));
                    contentInteractionTimeout = setTimeout(() => {
                        !forceStop || interactionSchedulerActive || vgMandatoryInteractionActive || vgMandatoryInteractionPending || G(!0)
                    }, c)
                }(n, i))
            }
        }

        function R(t) {
            let r = !1,
                n = !1,
                o = () => {
                    if (!n) {
                        n = !0;
                        try {
                            "function" == typeof window.pauseVGCountdown && window.pauseVGCountdown()
                        } catch (e) {}
                    }
                },
                i = () => {
                    if (n) {
                        n = !1;
                        try {
                            "function" == typeof window.resumeVGCountdown && window.resumeVGCountdown()
                        } catch (e) {}
                    }
                },
                c = (e = "fail") => {
                    i(), r && vgFlushActionSelectorDiagnostics("like", e, 1), "function" == typeof t && t(e)
                };
            loadFeatureToggles().then(t => {
                if (!forceStop) return c();
                if (!(r = Boolean(vgInteractionEnabled(a) && t["tx-like"] && readCookie("SID")))) return c("skip");
                let n = location.pathname.startsWith("/shorts/") || !!vgQueryOptionalSelector("reel-action-bar-view-model", p("like", "LikeThisVideo.detectShorts", "probe")),
                    i = () => vgGetVideoLikeButton(),
                    l = i();
                if (!l) return c("missing_button");
                if ("false" !== l.getAttribute("aria-pressed")) return c("already");
                o();
                let u = e => {
                        let t = i();
                        if (!t || !t.isConnected) return c("skip");
                        if ("false" !== t.getAttribute("aria-pressed")) return c("already");
                        if (n) {
                            e(t);
                            return
                        }
                        let r = t.closest("like-button-view-model") || t.closest("#segmented-like-button") || t.closest("#top-level-buttons-computed") || t.closest("#actions") || t.closest("ytd-watch-metadata") || vgQuerySelector("#above-the-fold") || t;
                        vgScrollElementForMandatoryAction(r, () => {
                            setTimeout(() => {
                                let t = i();
                                return t && t.isConnected ? "false" !== t.getAttribute("aria-pressed") ? c("already") : void e(t) : c("skip")
                            }, vgActionDelay(250, 500))
                        }, .66)
                    },
                    s = e => {
                        if (!forceStop) return c();
                        showWorkerNotification(1, "default", "processing", 0, "", null, !1).then(() => {
                            if (!forceStop) return c();
                            setTimeout(() => d(e), vgMandatoryUiProcessingDelay())
                        }).catch(() => c())
                    },
                    d = async e => {
                        if (!forceStop) return c();
                        let t = e || i();
                        if (!t || "false" !== t.getAttribute("aria-pressed")) return c(t ? "already" : "skip");
                        let r = await SimulateClick(t);
                        if (!r) return forceStop ? void showWorkerNotification(0, "warning", "canceling_like_action", 0, "", null, !1).then(() => c()) : c();
                        setTimeout(() => f(), 1e3)
                    }, f = (t = 1) => {
                        if (!forceStop) return c();
                        let r = i();
                        if (!r) return c();
                        let n = "true" === r.getAttribute("aria-pressed");
                        if (n) {
                            submitInteractionReport(e, "like", null, () => {
                                c("success")
                            });
                            return
                        }
                        if (t >= 5) return forceStop ? void showWorkerNotification(0, "error", "oops_something_seems_wrong", 0, "", null, !1).then(() => c()) : c();
                        setTimeout(() => f(t + 1), 700)
                    };
                (() => {
                    if (!forceStop) return c();
                    showWorkerNotification(1, "info", "trying_to_like_video", 0, "", null, !1).then(e => {
                        if (!e || !forceStop) return c();
                        setTimeout(() => {
                            if (!forceStop) return c();
                            u(s)
                        }, vgMandatoryUiTryingDelay())
                    }).catch(() => c())
                })()
            }).catch(() => {
                c()
            })
        }

        function D(t) {
            let r = !1,
                n = !1,
                o = () => {
                    if (!n) {
                        n = !0;
                        try {
                            "function" == typeof window.pauseVGCountdown && window.pauseVGCountdown()
                        } catch (e) {}
                    }
                },
                a = () => {
                    if (n) {
                        n = !1;
                        try {
                            "function" == typeof window.resumeVGCountdown && window.resumeVGCountdown()
                        } catch (e) {}
                    }
                },
                c = (e = "fail") => {
                    a(), r && vgFlushActionSelectorDiagnostics("subscribe", e, 1), "function" == typeof t && t(e)
                };
            loadFeatureToggles().then(t => {
                if (!forceStop) return c();
                if (!(r = Boolean(vgInteractionEnabled(i) && t["tx-subscribe"] && readCookie("SID")))) return c("skip");
                let n = location.pathname.startsWith("/shorts/") || !!vgQueryOptionalSelector("yt-reel-channel-bar-view-model", p("subscribe", "SubscriBeThis.detectShorts", "probe")),
                    a = () => n ? vgQuerySelector('.ytReelChannelBarViewModelReelSubscribeButton yt-subscribe-button-view-model button[class*="ytSpecButtonShapeNext"], .ytReelChannelBarViewModelReelSubscribeButton yt-subscribe-button-view-model button, .ytReelChannelBarViewModelReelSubscribeButton button[class*="ytSpecButtonShapeNext"], yt-reel-channel-bar-view-model yt-subscribe-button-view-model button[class*="ytSpecButtonShapeNext"], yt-reel-channel-bar-view-model yt-subscribe-button-view-model button, .ytReelChannelBarViewModelReelSubscribeButton button.yt-spec-button-shape-next', p("subscribe", "getSubscribeButton.shorts", "required")) : vgQuerySelector('#subscribe-button .yt-spec-button-shape-next--filled, #subscribe-button tp-yt-paper-button.ytd-subscribe-button-renderer:not([subscribed]), ytd-subscribe-button-renderer button, button[aria-label^="Subscribe"]', p("subscribe", "getSubscribeButton.watch", "required")),
                    l = e => {
                        try {
                            if (!e || !e.classList) return !1;
                            if (e.classList.contains("ytSpecButtonShapeNextTonal") || e.classList.contains("yt-spec-button-shape-next--tonal")) return !0;
                            if (e.classList.contains("ytSpecButtonShapeNextFilled")) return !1;
                            e.classList.contains("yt-spec-button-shape-next--filled")
                        } catch (t) {}
                        return !1
                    },
                    u = () => {
                        try {
                            let e = a();
                            if (!e) return !1;
                            if (n) return l(e);
                            if (vgQuerySelector("yt-button-shape#subscribe-button-shape[invisible]")) return !0;
                            let t = (e.getAttribute("aria-label") || e.innerText || e.textContent || "").toLowerCase();
                            return t.includes("subscribed") || t.includes("unsubscribe")
                        } catch (r) {
                            return !1
                        }
                    },
                    s = a();
                if (!s) return c("missing_button");
                if (u()) return c("already");
                if (!forceStop) return c();
                o();
                let d = e => {
                        let t = a();
                        if (!t || !t.isConnected) return c("skip");
                        if (u()) return c("already");
                        if (n) {
                            e(t);
                            return
                        }
                        let r = t.closest("#subscribe-button") || t.closest("ytd-subscribe-button-renderer") || t;
                        vgScrollElementForMandatoryAction(r, t => {
                            if (!forceStop) return c();
                            if (!t) return c("skip");
                            let r = a();
                            return r && r.isConnected ? u() ? c("already") : void e(r) : c("skip")
                        }, .64)
                    },
                    f = e => {
                        if (!forceStop) return c();
                        showWorkerNotification(1, "default", "processing", 0, "", null, !1).then(() => {
                            if (!forceStop) return c();
                            setTimeout(() => {
                                if (!forceStop) return c();
                                m(e)
                            }, vgMandatoryUiProcessingDelay())
                        }).catch(() => c())
                    },
                    m = async e => {
                        if (!forceStop) return c();
                        let t = e || a();
                        if (!t || u()) return c(t ? "already" : "skip");
                        let r = await SimulateClick(t);
                        if (!r) return forceStop ? void showWorkerNotification(0, "warning", "subscription_action_canceled", 0, "", null, !1).then(() => c()) : c();
                        setTimeout(async () => {
                            if (!forceStop) return c();
                            let e = await dismissYouTubeBackdrop();
                            if (e) return forceStop ? void showWorkerNotification(0, "warning", "subscription_action_canceled", 0, "", null, !1).then(() => c()) : c();
                            y()
                        }, 1e3)
                    }, y = (t = 1) => {
                        if (!forceStop) return c();
                        if (t > 5) {
                            showWorkerNotification(0, "error", "oops_something_seems_wrong", 0, "", null, !1).then(() => c());
                            return
                        }
                        if (u()) {
                            submitInteractionReport(e, "subscribe", null, () => {
                                c("success")
                            }), setTimeout(() => {
                                let e = vgQuerySelector(".yt-confirm-dialog-renderer #cancel-button");
                                e && SimulateClick(e)
                            }, 2e3);
                            return
                        }
                        if (n) {
                            let r = a();
                            if (r && l(r)) {
                                submitInteractionReport(e, "subscribe", null, () => {
                                    c("success")
                                });
                                return
                            }
                        }
                        setTimeout(() => y(t + 1), 700)
                    };
                (() => {
                    if (!forceStop) return c();
                    showWorkerNotification(1, "info", "trying_to_subscribe_channel", 0, "", null, !1).then(e => {
                        if (!e || !forceStop) return c();
                        setTimeout(() => {
                            if (!forceStop) return c();
                            d(f)
                        }, vgMandatoryUiTryingDelay())
                    }).catch(() => c())
                })()
            }).catch(() => {
                c()
            })
        }
        async function V(t) {
            if (vgIsShortsInteractionContext()) {
                "function" == typeof t && t("skip");
                return
            }
            let n = !1,
                o = !1,
                a = (e = "fail") => {
                    if (!n) {
                        n = !0, o && vgFlushActionSelectorDiagnostics("comment", e, 1);
                        try {
                            window.resumeVGCountdown()
                        } catch (r) {}
                        "function" == typeof t && t(e)
                    }
                },
                i = String(f ?? "").trim(),
                c = e => new Promise(t => setTimeout(t, Math.max(0, Number(e) || 0))),
                l = (e, t, r, n = "") => {
                    try {
                        return showWorkerNotification(e, t, r, 0, n, null, !1)
                    } catch (o) {
                        return Promise.resolve(!1)
                    }
                },
                u = (e = "fail", t = "canceling_comment_action") => {
                    n || l(0, "warning", t, "").then(() => a(e)).catch(() => a(e))
                },
                s = e => {
                    try {
                        console.error("ViewGrip comment action fatal error:", e)
                    } catch (t) {}
                    try {
                        vgRecoverWorkflow("comment", "comment_runtime_error", "Mandatory comment workflow threw a runtime error.", e, {
                            action: "restart",
                            backupUrl: r
                        })
                    } catch (n) {}
                    a("fail")
                };
            try {
                loadFeatureToggles().then(e => {
                    try {
                        if (!(o = Boolean("" !== i && e["tx-comment"] && readCookie("SID") && forceStop))) return a("skip");
                        try {
                            window.pauseVGCountdown()
                        } catch (t) {}
                        l(1, "info", "trying_to_comment_on_video").then(e => {
                            if (!e || !forceStop) return a();
                            setTimeout(() => {
                                try {
                                    if (!forceStop) return a();
                                    m().catch(s)
                                } catch (e) {
                                    s(e)
                                }
                            }, vgMandatoryUiTryingDelay())
                        }).catch(s)
                    } catch (r) {
                        s(r)
                    }
                }).catch(s)
            } catch (d) {
                s(d)
            }
            async function m() {
                if (n || !forceStop || (await y(), n || !forceStop)) return a();
                let t = findCommentElementByText(i);
                return t ? function t(r) {
                    if (n || !forceStop) return a();
                    if (!r || !r.isConnected) return V();
                    try {
                        vgScrollElementForMandatoryAction(r, () => {
                            try {
                                ! function t(r) {
                                    if (n || !forceStop) return a();
                                    l(0, "warning", "comments_already_exist").then(t => {
                                        if (!t || n) return a("already");
                                        try {
                                            r.style.background = "red", r.style.padding = "10px"
                                        } catch (o) {}
                                        try {
                                            $.ajax({
                                                url: MainUrl + "/api/worker/comment_exist",
                                                type: "POST",
                                                data: {
                                                    token: e,
                                                    value: i
                                                },
                                                headers: {
                                                    "X-Requested-With": "XMLHttpRequest"
                                                },
                                                timeout: 5e3,
                                                complete() {
                                                    try {
                                                        r.style.background = "", r.style.padding = ""
                                                    } catch (e) {}
                                                    a("already")
                                                }
                                            })
                                        } catch (c) {
                                            try {
                                                r.style.background = "", r.style.padding = ""
                                            } catch (l) {}
                                            a("already")
                                        }
                                    }).catch(() => a("already"))
                                }(r)
                            } catch (t) {
                                s(t)
                            }
                        }, .58)
                    } catch (o) {
                        s(o)
                    }
                }(t) : V()
            }
            async function y() {
                await S();
                let e = C();
                if (!(e.count > 0) || A(e))
                    for (let t = 0; t < 5; t++) {
                        if (n || !forceStop || findCommentElementByText(i)) return;
                        let r = C();
                        if (r.count > 0 && t > 0 && !A(r)) break;
                        let o = await T(t),
                            a = await E(r, t);
                        if (e = C(), await c(vgActionDelay(180, 360)), findCommentElementByText(i)) return;
                        if (!a || !a.grew || !A(e) || !o && !a.grew) break
                    }
            }

            function h() {
                try {
                    return vgQuerySelector("ytd-comments#comments") || vgQuerySelector('ytd-comments[id="comments"]') || vgQuerySelector("#comments")
                } catch (e) {
                    return null
                }
            }

            function g() {
                return function e() {
                    try {
                        let t = h();
                        if (t) return vgQuerySelector('ytd-item-section-renderer#sections[section-identifier="comment-item-section"]', t, "getCommentSections") || vgQuerySelector("ytd-item-section-renderer#sections", t, "getCommentSections") || vgQuerySelector("#sections", t, "getCommentSections") || null;
                        return vgQuerySelector("ytd-comments#comments ytd-item-section-renderer#sections") || vgQuerySelector('ytd-item-section-renderer#sections[section-identifier="comment-item-section"]') || null
                    } catch (r) {
                        return null
                    }
                }() || h()
            }

            function v() {
                return window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0
            }

            function b() {
                let e = window.innerHeight || document.documentElement.clientHeight || 700,
                    t = Math.max(document.documentElement?.scrollHeight || 0, document.body?.scrollHeight || 0, $(document).height ? $(document).height() : 0);
                return Math.max(0, t - e)
            }

            function w(e) {
                try {
                    if (!e || !e.isConnected) return null;
                    let t = e.getBoundingClientRect();
                    if (!t || t.width <= 0 || t.height <= 0) return null;
                    let r = t.top + v(),
                        n = t.bottom + v();
                    return {
                        top: r,
                        bottom: n,
                        height: t.height,
                        width: t.width
                    }
                } catch (o) {
                    return null
                }
            }
            async function S() {
                try {
                    let e = g() || h();
                    if (e || (e = await waitForElement("ytd-comments#comments, #comments, #comment-teaser", 4500, p("comment", "ensureCommentSurfaceVisible.surface", "required"))), !e || !e.isConnected) return !1;
                    let t = e.getBoundingClientRect(),
                        r = window.innerHeight || document.documentElement.clientHeight || 700,
                        n = t.top < .62 * r && t.bottom > .22 * r;
                    if (n) return !0;
                    let o = Math.max(0, t.top + v() - .28 * r);
                    return await I(o, vgActionDelay(850, 1350)), await c(vgActionDelay(450, 850)), !0
                } catch (a) {
                    return !1
                }
            }

            function _() {
                let e = g(),
                    t = w(e);
                if (!t) return null;
                let r = window.innerHeight || document.documentElement.clientHeight || 700;
                return Math.max(0, Math.round(t.bottom - r + Math.max(80, Math.min(160, .16 * r))))
            }

            function k(e) {
                return vgGetDynamicFeedScanScrollDuration(e)
            }
            async function T(e = 0) {
                try {
                    var t;
                    await x();
                    let r = _();
                    if (null === r && (await S(), r = _()), null === r || (await c(vgActionDelay(60, 140)), r = _(), null === r)) return !1;
                    let n = v(),
                        o = b();
                    if ((r = Math.max(0, Math.min(o, r))) <= n + 18) return await c(vgActionDelay(220, 420)), !1;
                    let a = Math.abs(r - n);
                    return await I(r, (t = a, vgGetDynamicFeedScanScrollDuration(t)))
                } catch (i) {
                    return await c(vgActionDelay(250, 480)), !1
                }
            }
            async function x() {
                try {
                    window.onscroll = null
                } catch (e) {}
                try {
                    $(window).stop(!0, !0)
                } catch (t) {}
                try {
                    $("html, body").stop(!0, !0)
                } catch (r) {}
                try {
                    document.documentElement && $(document.documentElement).stop(!0, !0)
                } catch (n) {}
                try {
                    document.body && $(document.body).stop(!0, !0)
                } catch (o) {}
                await c(vgActionDelay(80, 170))
            }

            function I(e, t = 1300) {
                return new Promise(r => {
                    let n = !1,
                        o = vgScrollGeneration,
                        a = Math.max(0, Math.min(b(), Math.round(Number(e) || 0))),
                        i = () => {
                            n || (n = !0, r(!0))
                        };
                    try {
                        Promise.resolve(vgScrollToY(a, Math.max(850, t - 250), t + 300)).then(i).catch(() => {
                            if (o !== vgScrollGeneration) return i();
                            try {
                                window.scrollTo({
                                    top: a,
                                    behavior: "smooth"
                                })
                            } catch (e) {
                                window.scrollTo(0, a)
                            }
                            setTimeout(i, t)
                        });
                        return
                    } catch (c) {}
                    try {
                        if (o !== vgScrollGeneration) return i();
                        $("html, body").stop(!0, !0).animate({
                            scrollTop: a
                        }, t, i)
                    } catch (l) {
                        try {
                            window.scrollTo({
                                top: a,
                                behavior: "smooth"
                            })
                        } catch (u) {
                            window.scrollTo(0, a)
                        }
                        setTimeout(i, t)
                    }
                })
            }
            async function E(e, t) {
                let r = Date.now(),
                    o = 0 === t ? 520 : 420,
                    a = 0 === t ? 2200 : 1800,
                    i = C(),
                    l = 0,
                    u = !1,
                    s = Boolean(i.hasSurface || e.hasSurface),
                    d = A(i);
                for (; Date.now() - r < a;) {
                    if (n || !forceStop) return {
                        grew: !1,
                        stopped: !0
                    };
                    let f = C(),
                        m = f.count > (e.count || 0),
                        p = f.height > (e.height || 0) + 60,
                        y = f.bottom > (e.bottom || 0) + 60,
                        h = A(f);
                    f.hasSurface && (s = !0), h && (d = !0), (m || p || y) && (u = !0);
                    let g = f.count === i.count,
                        v = 8 >= Math.abs(f.height - i.height),
                        b = 10 >= Math.abs(f.bottom - i.bottom);
                    g && v && b ? l++ : l = 0, i = f;
                    let w = Date.now() - r >= o;
                    if (w && u && l >= 1) return {
                        grew: !0,
                        stopped: !1
                    };
                    if (w && s && !u && !h && l >= 1 || w && s && d && !h && !u && l >= 1) return {
                        grew: !1,
                        stopped: !0
                    };
                    await c(vgActionDelay(160, 260))
                }
                return {
                    grew: u,
                    stopped: !u
                }
            }

            function C() {
                try {
                    let e = h(),
                        t = g() || e,
                        r = e || document,
                        n = Array.from(vgQuerySelectorAll("ytd-comment-thread-renderer, ytd-comment-view-model, ytd-comment-renderer", r, "getCommentSurfaceSnapshot")).filter(t => {
                            try {
                                return !e || e.contains(t)
                            } catch (r) {
                                return !0
                            }
                        }),
                        o = n.length,
                        a = R(),
                        i = D(),
                        c = w(t || e),
                        l = c ? c.height : 0,
                        u = c ? c.bottom : 0;
                    return {
                        count: o,
                        height: l,
                        bottom: u,
                        maxScroll: b(),
                        scrollTop: v(),
                        hasSurface: Boolean(e || t || o > 0 || a || i)
                    }
                } catch (s) {
                    return {
                        count: 0,
                        height: 0,
                        bottom: 0,
                        maxScroll: 0,
                        scrollTop: 0,
                        hasSurface: !1
                    }
                }
            }

            function A(e = null) {
                try {
                    let t = h();
                    if (!t) return !(e && e.count > 0);
                    let r = g() || t,
                        n = r?.getBoundingClientRect?.(),
                        o = Array.from(vgQuerySelectorAll('ytd-continuation-item-renderer, yt-next-continuation, #continuations, tp-yt-paper-spinner-lite, paper-spinner-lite, yt-spinner, [role="progressbar"], [aria-busy="true"]', r, "hasCommentContinuationOrLoader"));
                    for (let a of o) {
                        if (!a || !a.isConnected) continue;
                        let i = a.hidden || null !== a.getAttribute("hidden") || "true" === a.getAttribute("aria-hidden");
                        if (i) continue;
                        try {
                            let c = window.getComputedStyle(a);
                            if ("none" === c.display || "hidden" === c.visibility || 0 === Number(c.opacity)) continue
                        } catch (l) {}
                        let u = null;
                        try {
                            if ((u = a.getBoundingClientRect()).width <= 0 && u.height <= 0) continue
                        } catch (s) {}
                        let d = a.matches?.('tp-yt-paper-spinner-lite, paper-spinner-lite, yt-spinner, [role="progressbar"], [aria-busy="true"]') || "true" === String(a.getAttribute("aria-busy") || "").toLowerCase(),
                            f = (() => {
                                try {
                                    if (!u || !n) return !0;
                                    return u.top >= n.bottom - 520 || 360 >= Math.abs(u.bottom - n.bottom)
                                } catch (e) {
                                    return !0
                                }
                            })();
                        if (d || f) return !0
                    }
                    return !1
                } catch (m) {
                    return !1
                }
            }

            function R() {
                try {
                    return vgQuerySelector("#comments ytd-comment-simplebox-renderer #placeholder-area, #comments #placeholder-area, #comments ytd-comment-simplebox-renderer, ytd-comment-simplebox-renderer #placeholder-area, ytd-comment-simplebox-renderer, #simple-box, #simplebox-placeholder")
                } catch (e) {
                    return null
                }
            }

            function D() {
                try {
                    return vgQuerySelector('#comments ytd-commentbox #contenteditable-root[contenteditable="true"], ytd-commentbox #contenteditable-root[contenteditable="true"], #contenteditable-root[contenteditable="true"], #contenteditable-root.yt-formatted-string')
                } catch (e) {
                    return null
                }
            }
            async function V() {
                if (n || !forceStop) return a();
                try {
                    let e = R();
                    if (e || (e = await waitForElement("#comments ytd-comment-simplebox-renderer, #comments #placeholder-area, ytd-comment-simplebox-renderer, #simple-box, #simplebox-placeholder", 6e3, p("comment", "scrollToCommentBox.commentBox", "required"))), !e || !e.isConnected) return u("skip");
                    vgScrollElementForMandatoryAction(e, async () => {
                        try {
                            if (!forceStop || n) return a();
                            await c(vgMandatoryUiSmallPause()), l(1, "default", "processing").then(() => {
                                if (!forceStop || n) return a();
                                setTimeout(() => {
                                    if (!forceStop || n) return a();
                                    M(e).catch(s)
                                }, vgMandatoryUiProcessingDelay())
                            }).catch(s)
                        } catch (t) {
                            s(t)
                        }
                    }, .54)
                } catch (t) {
                    s(t)
                }
            }
            async function M(e) {
                if (n || !forceStop) return a();
                try {
                    let t = await G(e);
                    if (!t || n || !forceStop) return u();
                    let r = await P(8e3);
                    if (!r || !r.isConnected) return u("skip");
                    await c(vgActionDelay(650, 1200)), SimulateTyping(r, i, vgActionDelay(55, 105), async () => {
                        try {
                            if (n || !forceStop) return a();
                            (function e(t, r) {
                                try {
                                    let n = String(t.value ?? t.textContent ?? "").trim();
                                    if (n === r) return;
                                    if (void 0 !== t.value) t.value = r;
                                    else {
                                        t.textContent = r;
                                        try {
                                            t.innerText = r
                                        } catch (o) {}
                                    }
                                    t.dispatchEvent(new InputEvent("input", {
                                        bubbles: !0,
                                        cancelable: !0,
                                        inputType: "insertText",
                                        data: r
                                    })), t.dispatchEvent(new Event("change", {
                                        bubbles: !0,
                                        cancelable: !0
                                    }))
                                } catch (a) {}
                            })(r, i);
                            let e = await
                            function e(t = 8e3) {
                                return new Promise(e => {
                                    let r = Date.now(),
                                        o = () => {
                                            try {
                                                let e = Array.from(vgQuerySelectorAll("ytd-commentbox #submit-button button, ytd-commentbox #submit-button, #submit-button.ytd-commentbox, #submit-button button, #submit-button"));
                                                for (let t of e) {
                                                    if (!t || !t.isConnected) continue;
                                                    let r = String(t.getAttribute("aria-disabled") || "").toLowerCase(),
                                                        n = Boolean(t.disabled) || "true" === r || t.hasAttribute("disabled"),
                                                        o = t.getBoundingClientRect(),
                                                        a = o.width > 2 && o.height > 2;
                                                    if (!n && a) return t
                                                }
                                            } catch (i) {}
                                            return null
                                        },
                                        a = () => {
                                            if (n || !forceStop) return e(null);
                                            let i = o();
                                            return i ? e(i) : Date.now() - r >= t ? e(null) : void setTimeout(a, 300)
                                        };
                                    a()
                                })
                            }(8e3);
                            if (!e) return u();
                            setTimeout(() => W(e), vgActionDelay(1200, 2100))
                        } catch (t) {
                            s(t)
                        }
                    })
                } catch (o) {
                    s(o)
                }
            }
            async function G(e) {
                let t = function e(t = null) {
                    try {
                        let r = D();
                        if (L(r)) return r;
                        let n = h() || document,
                            o = [t ? vgQuerySelector('#contenteditable-root[contenteditable="true"]', t, "findDirectCommentInputTarget") : null, vgQuerySelector('ytd-commentbox #contenteditable-root[contenteditable="true"]', n, "findDirectCommentInputTarget"), t ? vgQuerySelector('[contenteditable="true"]', t, "findDirectCommentInputTarget") : null, vgQuerySelector("ytd-comment-simplebox-renderer #placeholder-area", n, "findDirectCommentInputTarget"), vgQuerySelector("ytd-comment-simplebox-renderer #simplebox-placeholder", n, "findDirectCommentInputTarget"), vgQuerySelector("#placeholder-area", n, "findDirectCommentInputTarget"), vgQuerySelector("#simplebox-placeholder", n, "findDirectCommentInputTarget"), t?.matches?.('#placeholder-area, #simplebox-placeholder, [role="textbox"], [contenteditable="true"]') ? t : null, t ? vgQuerySelector('[role="textbox"]', t, "findDirectCommentInputTarget") : null];
                        for (let a of o)
                            if (L(a)) return a
                    } catch (i) {}
                    return null
                }(e);
                if (!t || !t.isConnected) return Boolean(D());
                try {
                    let r = await SimulateClick(t);
                    r || (t.dispatchEvent(new MouseEvent("mouseover", {
                        bubbles: !0,
                        cancelable: !0
                    })), t.dispatchEvent(new MouseEvent("mousedown", {
                        bubbles: !0,
                        cancelable: !0
                    })), t.dispatchEvent(new MouseEvent("mouseup", {
                        bubbles: !0,
                        cancelable: !0
                    })), t.dispatchEvent(new MouseEvent("click", {
                        bubbles: !0,
                        cancelable: !0
                    })), "function" == typeof t.click && t.click())
                } catch (n) {
                    try {
                        t.dispatchEvent(new MouseEvent("click", {
                            bubbles: !0,
                            cancelable: !0
                        })), "function" == typeof t.click && t.click()
                    } catch (o) {}
                }
                return await c(vgActionDelay(700, 1150)), Boolean(D() || t.matches?.('#contenteditable-root[contenteditable="true"], [contenteditable="true"], [role="textbox"]'))
            }

            function L(e) {
                try {
                    if (!e || !e.isConnected) return !1;
                    let t = e.getBoundingClientRect();
                    if (t.width < 2 || t.height < 2) return !1;
                    let r = window.getComputedStyle(e);
                    if ("none" === r.display || "hidden" === r.visibility || 0 === Number(r.opacity)) return !1;
                    return !0
                } catch (n) {
                    return !1
                }
            }
            async function P(e = 8e3) {
                let t = Date.now();
                for (; Date.now() - t < e && !n && forceStop;) {
                    let r = D();
                    if (r && r.isConnected) try {
                        let o = r.getBoundingClientRect();
                        if (o.width > 2 && o.height > 2) return r
                    } catch (a) {
                        return r
                    }
                    await c(250)
                }
                return null
            }
            async function W(e) {
                if (n || !forceStop) return a();
                try {
                    await dismissYouTubeBackdrop(1200).catch(() => !1), await c(vgActionDelay(350, 700));
                    let t = await SimulateClick(e);
                    if (!t || n || !forceStop) return u();
                    try {
                        window.onscroll = null
                    } catch (r) {}
                    setTimeout(() => {
                        try {
                            let t = vgQuerySelector('tp-yt-paper-dialog yt-button-shape button, tp-yt-paper-dialog yt-button-shape .yt-spec-touch-feedback-shape[aria-hidden="true"], ytd-confirm-dialog-renderer #confirm-button button');
                            t && t.isConnected ? SimulateClick(t).then(t => {
                                if (!t || n || !forceStop) return u();
                                setTimeout(() => {
                                    SimulateClick(e).then(e => {
                                        e ? Y() : u()
                                    }).catch(s)
                                }, vgActionDelay(1100, 1700))
                            }).catch(s) : Y()
                        } catch (r) {
                            s(r)
                        }
                    }, vgActionDelay(1300, 2100))
                } catch (o) {
                    s(o)
                }
            }

            function Y(t = 1) {
                if (n || !forceStop) return a();
                try {
                    let r = findCommentElementByText(i);
                    if (r) {
                        submitInteractionReport(e, "comment", i, () => a("success"));
                        return
                    }
                    if (t >= 8) {
                        l(0, "error", "oops_something_seems_wrong").then(() => a()).catch(() => a());
                        return
                    }
                    setTimeout(() => Y(t + 1), vgActionDelay(850, 1350))
                } catch (o) {
                    s(o)
                }
            }
        }
        async function M(t) {
            if (vgIsShortsInteractionContext()) {
                "function" == typeof t && t("skip");
                return
            }
            let r = !1,
                n = (e = "fail") => {
                    r && vgFlushActionSelectorDiagnostics("comment_liking", e, 1), "function" == typeof t && t(e)
                },
                o = (e, t, r) => showWorkerNotification(e, t, r, 0, "", null, !1);
            async function a() {
                try {
                    let e = d();
                    if (e) {
                        setTimeout(f, vgMandatoryUiSmallPause());
                        return
                    }
                    let t = vgGetCommentsAnchor();
                    if (t && t.isConnected) {
                        vgScrollElementForMandatoryAction(t, () => {
                            setTimeout(() => {
                                s(0).catch(() => f())
                            }, vgMandatoryUiSmallPause())
                        }, .24);
                        return
                    }
                    await s(0)
                } catch (r) {
                    n()
                }
            }

            function i() {
                try {
                    let e = Array.from(vgQuerySelectorAll("#comments ytd-comment-engagement-bar #like-button button[aria-pressed], #comments-view ytd-comment-engagement-bar #like-button button[aria-pressed], #contents ytd-comment-engagement-bar #like-button button[aria-pressed], ytd-comment-thread-renderer, ytd-comment-view-model, ytd-comment-renderer"));
                    return e.length
                } catch (t) {
                    return 0
                }
            }

            function c(e) {
                return vgGetDynamicFeedScanScrollDuration(e)
            }
            async function u(e, t) {
                let r = Date.now(),
                    n = !1,
                    o = r,
                    a = Math.max(0, Number(e) || 0),
                    c = Math.max(0, Number(t) || 0);
                for (; forceStop && Date.now() - r < 1800;) {
                    if (await vgSleep(vgActionDelay(160, 260)), d()) return {
                        found: !0,
                        loadedMore: !0
                    };
                    let l = i(),
                        u = Math.max(document.documentElement?.scrollHeight || 0, document.body?.scrollHeight || 0, $(document).height ? $(document).height() : 0);
                    if ((l > a || u > c + 80) && (a = Math.max(a, l), c = Math.max(c, u), o = Date.now(), n = !0), n && Date.now() - o >= 420) return {
                        found: !1,
                        loadedMore: !0
                    }
                }
                return {
                    found: !1,
                    loadedMore: n
                }
            }
            async function s(e = 0) {
                if (!forceStop) return n();
                let t = d();
                if (t) {
                    setTimeout(f, vgMandatoryUiSmallPause());
                    return
                }
                if (e >= 4) {
                    f();
                    return
                }
                try {
                    let r = Math.max(400, window.innerHeight || document.documentElement.clientHeight || 700),
                        o = window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0,
                        a = Math.max(document.documentElement?.scrollHeight || 0, document.body?.scrollHeight || 0, $(document).height ? $(document).height() : 0),
                        c = Math.max(0, a - r + 4),
                        l = Math.max(0, c - o),
                        m = i();
                    l > 35 && await new Promise(e => {
                        try {
                            var t;
                            $("html, body").stop(!0, !0).animate({
                                scrollTop: c
                            }, (t = l, vgGetDynamicFeedScanScrollDuration(t)), "swing").promise().always(e)
                        } catch (r) {
                            try {
                                window.scrollTo({
                                    top: c,
                                    behavior: "smooth"
                                })
                            } catch (n) {}
                            setTimeout(e, 900)
                        }
                    });
                    let p = await u(m, a);
                    if (p && p.found) return;
                    setTimeout(() => {
                        s(e + 1).catch(() => f())
                    }, vgActionDelay(220, 480))
                } catch (y) {
                    f()
                }
            }

            function d() {
                try {
                    let e = Array.from(vgQuerySelectorAll("#comments ytd-comment-engagement-bar #like-button button[aria-pressed], #comments-view ytd-comment-engagement-bar #like-button button[aria-pressed], #contents ytd-comment-engagement-bar #like-button button[aria-pressed]"));
                    for (let t of e) {
                        if (!t || !t.isConnected || t.disabled) continue;
                        let r = t.getBoundingClientRect();
                        if (r.width > 2 && r.height > 2 && r.bottom > 0 && r.top < window.innerHeight) return t
                    }
                } catch (n) {}
                return null
            }

            function f() {
                setTimeout(() => {
                    try {
                        var r, a;
                        let i = vgQuerySelector('#contents ytd-comment-engagement-bar #like-button button:not([aria-pressed="true"])'),
                            c = vgQuerySelector('#contents ytd-comment-engagement-bar #like-button button[aria-pressed="true"]');
                        if (c) {
                            return r = c, void vgScrollElementForMandatoryAction(r, () => {
                                setTimeout(() => {
                                    if (!forceStop) return n();
                                    o(0, "warning", "already_liked_comment").then(e => {
                                        if (!e) return n("already");
                                        r.style.border = "solid 4px red", setTimeout(() => {
                                            r.style.border = "", n("already")
                                        }, 1500)
                                    })
                                }, vgActionDelay(600, 1100))
                            }, .58)
                        }
                        if (!i) return m();
                        a = i, vgScrollElementForMandatoryAction(a, () => {
                            if (!forceStop) return n();
                            setTimeout(() => {
                                if (!forceStop) return n();
                                o(1, "default", "processing").then(() => {
                                    setTimeout(() => {
                                        SimulateClick(a).then(r => {
                                            r ? setTimeout(function r(a = 1) {
                                                if (!forceStop) return n();
                                                let i = vgQuerySelector('#contents ytd-comment-engagement-bar #like-button button[aria-pressed="true"]');
                                                if (i) {
                                                    submitInteractionReport(e, "comment_liking", null, () => {
                                                        "function" == typeof t && t("success")
                                                    });
                                                    return
                                                }
                                                if (a >= 5) {
                                                    o(0, "error", "oops_something_seems_wrong").then(() => n());
                                                    return
                                                }
                                                setTimeout(() => r(a + 1), 700)
                                            }, 1500) : m()
                                        })
                                    }, vgMandatoryUiProcessingDelay())
                                })
                            }, vgMandatoryUiSmallPause())
                        }, .58)
                    } catch (l) {
                        m()
                    }
                }, vgMandatoryUiSmallPause())
            }

            function m() {
                o(0, "warning", "canceling_liked_comment").then(() => n())
            }
            loadFeatureToggles().then(e => {
                if (!(r = Boolean(vgInteractionEnabled(l) && e["tx-likecomment"] && readCookie("SID")))) return n("skip");
                window.pauseVGCountdown(), o(1, "info", "trying_to_like_comment").then(e => {
                    if (!e || !forceStop) return n();
                    setTimeout(() => {
                        if (!forceStop) return n();
                        a()
                    }, vgMandatoryUiTryingDelay())
                }).catch(() => n())
            }).catch(() => n())
        }

        function G(e = !1) {
            if (!forceStop || interactionSchedulerActive || vgMandatoryInteractionActive || vgMandatoryInteractionPending) return;
            if (clearTimeout(contentInteractionTimeout), vgIsShortsInteractionContext()) {
                try {
                    showKeepWatchingNotice()
                } catch (t) {}
                try {
                    window.resumeVGCountdown()
                } catch (r) {}
                return
            }
            if (L(), remainingTime < (e ? 7e3 : 25e3)) return;
            let n = remainingTime < 6e4 ? getRandomInt(10, 20) / 100 : remainingTime < 12e4 ? getRandomInt(16, 30) / 100 : remainingTime < 24e4 ? getRandomInt(22, 38) / 100 : getRandomInt(28, 46) / 100,
                o = (() => {
                    let e = Number(remainingTime) || 0;
                    return e < 9e3 ? getRandomInt(850, 1500) : e < 25e3 ? getRandomInt(1100, 2100) : e < 45e3 ? getRandomInt(1800, 3e3) : e < 9e4 ? getRandomInt(2600, 4200) : e < 18e4 ? getRandomInt(3400, 5200) : getRandomInt(4200, 6500)
                })(),
                a = e ? Math.min(Math.max(650, Math.round(remainingTime * (getRandomInt(2, 5) / 100))), remainingTime < 14e3 ? 1600 : 3200) : Math.max(2500, Math.min(remainingTime * n, remainingTime - o - 6500));

            function i(e) {
                try {
                    let t = vgQuerySelector(e);
                    if (!t || !t.isConnected) return !1;
                    let r = t.getBoundingClientRect();
                    return r.width > 0 && r.height > 0
                } catch (n) {
                    return !1
                }
            }

            function c(e = 350, t = 1100) {
                let r = Math.max(0, Math.round(Number(e) || 0)),
                    n = Math.max(0, Math.round(Number(t) || 0));
                setTimeout(() => {
                    !forceStop || interactionSchedulerActive || vgMandatoryInteractionActive || vgMandatoryInteractionPending || L()
                }, vgActionDelay(Math.min(r, n), Math.max(r, n)))
            }

            function l(e, t = .18, r = .42) {
                let n = Math.max(350, Math.round(e * t));
                return vgActionDelay(n, Math.max(n + 200, Math.round(e * r)))
            }

            function u(e) {
                try {
                    let t = vgQuerySelector("#above-the-fold") || vgQuerySelector("#info-contents") || vgQuerySelector("#below");
                    if (!t) return c(250, 600);
                    scrollElementToCenter(t, () => {
                        c(l(e, .12, .28), l(e, .22, .38))
                    })
                } catch (r) {
                    c(250, 600)
                }
            }

            function s(e, t = null) {
                try {
                    let r = vgQuerySelector("#above-the-fold") || vgQuerySelector("#info-contents");
                    if (!r) return "function" == typeof t ? t() : c(250, 600);
                    scrollElementToCenter(r, () => {
                        let n = vgQuerySelector("#below #expand"),
                            o = () => {
                                "function" == typeof t ? setTimeout(() => {
                                    !forceStop || interactionSchedulerActive || vgMandatoryInteractionActive || vgMandatoryInteractionPending || t()
                                }, vgActionDelay(300, 800)) : c(l(e, .1, .26), l(e, .2, .36))
                            };
                        if (!n || !forceStop) return o();
                        SimulateClick(n).then(() => {
                            setTimeout(() => {
                                if (!forceStop || interactionSchedulerActive || vgMandatoryInteractionActive || vgMandatoryInteractionPending) return;
                                let e = vgQuerySelector("#bottom-row #description") || r,
                                    t = e.getBoundingClientRect(),
                                    n = Math.max(0, t.top + window.scrollY - getRandomInt(100, 180));
                                vgScrollToY(n, 450, 950).then(o)
                            }, vgActionDelay(350, 800))
                        }).catch(o)
                    })
                } catch (n) {
                    c(250, 600)
                }
            }

            function d(e, t = null) {
                try {
                    let r = vgQuerySelector("ytd-comments#comments") || vgQuerySelector("#comments");
                    if (!r) return "function" == typeof t ? t() : c(250, 600);

                    function n() {
                        "function" == typeof t ? setTimeout(() => {
                            !forceStop || interactionSchedulerActive || vgMandatoryInteractionActive || vgMandatoryInteractionPending || t()
                        }, vgActionDelay(250, 700)) : c(l(e, .08, .2), l(e, .16, .3))
                    }
                    vgScrollElementToTop(r, getRandomInt(80, 130), () => {
                        if (!forceStop) return;
                        let e = vgGetTopVisibleCommentThread();
                        if (e) {
                            let t = e.getBoundingClientRect(),
                                r = Math.max(0, t.top + window.scrollY - getRandomInt(95, 150));
                            vgScrollToY(r, 450, 900).then(n)
                        } else n()
                    })
                } catch (o) {
                    c(250, 600)
                }
            }
            Number.isFinite(a) && !(a < (e ? 250 : 700)) && (contentInteractionTimeout = setTimeout(() => {
                forceStop && !interactionSchedulerActive && !vgMandatoryInteractionActive && !vgMandatoryInteractionPending && (remainingTime && remainingTime < o + (e ? 1400 : 2500) || function e(t) {
                    if (forceStop) try {
                        if (interactionSchedulerActive || vgMandatoryInteractionActive || vgMandatoryInteractionPending) return;
                        showWorkerNotification(1, "info", "trying_engage_other_interactions", 0, "", null, !1).then(e => {
                            e && forceStop && setTimeout(() => {
                                if (!forceStop || interactionSchedulerActive || vgMandatoryInteractionActive || vgMandatoryInteractionPending) return;
                                let e = function e() {
                                    let t = [];
                                    return ((i("#above-the-fold") || i("#info-contents")) && t.push("metadata_glance", "micro_scroll"), (i("#owner") || i("ytd-video-owner-renderer")) && t.push("channel_glance"), (i("#below #expand") || i("#bottom-row #description")) && t.push("description_peek"), (i("ytd-comments#comments") || i("#comments")) && t.push("comments_peek"), remainingTime > 9e4 && vgEnoughActionTime(22e3) && t.includes("description_peek") && t.includes("comments_peek") && t.push("quick_mixed"), t.length) ? t[Math.floor(Math.random() * t.length)] : "metadata_glance"
                                }();
                                return "comments_peek" === e ? d(t) : "description_peek" === e ? s(t) : "channel_glance" === e ? function e(t) {
                                    try {
                                        let r = vgQuerySelector("#owner") || vgQuerySelector("ytd-video-owner-renderer") || vgQuerySelector("#above-the-fold");
                                        if (!r) return u(t);
                                        vgScrollElementToTop(r, getRandomInt(90, 150), () => {
                                            c(l(t, .14, .3), l(t, .24, .42))
                                        })
                                    } catch (n) {
                                        c(250, 600)
                                    }
                                }(t) : "micro_scroll" === e ? function e(t) {
                                    try {
                                        let r = window.innerHeight || document.documentElement.clientHeight || 700,
                                            n = window.scrollY || document.documentElement.scrollTop || 0,
                                            o = Math.max(0, n + r * (getRandomInt(16, 34) / 100));
                                        vgScrollToY(o, 550, 1050).then(() => {
                                            c(l(t, .1, .24), l(t, .2, .34))
                                        })
                                    } catch (a) {
                                        c(250, 600)
                                    }
                                }(t) : "quick_mixed" === e ? function e(t) {
                                    if (!vgEnoughActionTime(19e3)) return u(t);
                                    let r = Math.max(1800, Math.round(.45 * t));
                                    .5 > Math.random() ? s(r, () => d(r)) : d(r, () => s(r))
                                }(t) : void u(t)
                            }, vgMandatoryUiTryingDelay())
                        })
                    } catch (r) {
                        c()
                    }
                }(o))
            }, a))
        }

        function L() {
            return new Promise(e => {
                if (!forceStop) {
                    e();
                    return
                }
                let t = () => {
                    showKeepWatchingNotice(), window.resumeVGCountdown(), e()
                };
                if (vgIsShortsInteractionContext()) {
                    t();
                    return
                }
                window.scrollY > 0 ? $("html").stop(!0, !0).animate({
                    scrollTop: 0
                }, 2e3, t) : t()
            })
        }
        "direct" === n || window.location.href.includes("youtube.com/watch?v=") || window.location.href.includes("youtube.com/shorts/") ? (vgRuntimeHeartbeat("prewatch", "checking_video", {
            workflow: "playback",
            video_id: t,
            backup_url: r
        }), _()) : "keyword" === n ? (vgRuntimeHeartbeat("search", "keyword_mode_start", {
            workflow: "search",
            video_id: t,
            backup_url: r
        }), "www.youtube.com" === window.location.hostname && "/" === window.location.pathname ? vgLater(v, 900, 1800) : navigateWorkerTab("https://www.youtube.com/")) : "channel" === n ? (vgRuntimeHeartbeat("channel", "channel_mode_start", {
            workflow: "channel",
            video_id: t,
            backup_url: r
        }), window.location.href.includes("/channel") || window.location.href.includes(".com/@") ? vgLater(w, 900, 1900) : redirectToVideoSafely(r, {
            workflow: "channel",
            status: "open_backup_channel_or_video"
        })) : (vgRuntimeHeartbeat("video_navigation", "unknown_viewing_method", {
            workflow: "video_navigation",
            video_id: t,
            backup_url: r
        }), redirectToVideoSafely(r, {
            workflow: "video_navigation",
            status: "unknown_viewing_method"
        }))
    } else {
        if (!forceStop) return;
        showWorkerNotification(1, "error", "oops_something_seems_wrong", 0, "", null, !1).then(e => {
            e && restartWorkerSession()
        })
    }
}

function skipCurrentCampaign() {
    try {
        let e = vgQuerySelector("#tx-skip-btn");
        if (window.pauseVGCountdown(), !forceStop) return;
        showWorkerNotification(1, "warning", "processing_campaign_skip_request", 0, "", null, !1).then(async t => {
            if (!t) return;
            let r = await vgStorageLocalGetSafe("token", 5e3, {}),
                n = r && r.token ? r.token : null;
            $.ajax({
                url: MainUrl + "/api/worker/skip",
                type: "GET",
                data: {
                    token: n
                },
                cache: !1,
                timeout: 5e3,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }).done(function(t) {
                forceStop && (showWorkerNotification(0, t.status, t.message, 0, "", null, !1).then(() => {
                    "success" === t.status ? restartWorkerSession() : setTimeout(() => {
                        showKeepWatchingNotice(), window.resumeVGCountdown(), e && (e.style.pointerEvents = "auto", e.style.opacity = "1")
                    }, 1500)
                }), console.clear())
            }).fail(function(t) {
                forceStop && (showWorkerNotification(0, "error", "server_error_occurred", 0, "", null, !1).then(async () => {
                    setTimeout(function() {
                        showKeepWatchingNotice(), window.resumeVGCountdown()
                    }, 1500)
                }), e && (e.style.pointerEvents = "auto", e.style.opacity = "1"), console.clear())
            })
        })
    } catch (t) {
        if (!forceStop) return;
        showWorkerNotification(0, "error", "server_error_occurred", 0, "", null, !1).then(async () => {
            setTimeout(function() {
                showKeepWatchingNotice(), window.resumeVGCountdown()
            }, 1500);
            let e = vgQuerySelector("#tx-skip-btn");
            e && (e.style.pointerEvents = "auto", e.style.opacity = "1")
        })
    }
}

function submitUserCampaignReport(e) {
    try {
        let t = vgQuerySelector("#tx-report-btn");
        if (window.pauseVGCountdown(), !forceStop) return;
        showWorkerNotification(1, "warning", "reporting_campaign_process", 0, "", null, !1).then(async r => {
            if (!r) return;
            let n = await vgStorageLocalGetSafe("token", 5e3, {}),
                o = n && n.token ? n.token : null;
            $.ajax({
                url: MainUrl + "/api/worker/user_report",
                type: "GET",
                data: {
                    token: o,
                    reason: e
                },
                cache: !1,
                timeout: 5e3,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }).done(function(e) {
                forceStop && (showWorkerNotification(0, e.status, e.message, 0, "", null, !1).then(() => {
                    "success" === e.status ? restartWorkerSession() : setTimeout(() => {
                        showKeepWatchingNotice(), window.resumeVGCountdown(), t && (t.style.pointerEvents = "auto", t.style.opacity = "1")
                    }, 1500)
                }), console.clear())
            }).fail(function(e) {
                forceStop && (showWorkerNotification(0, "error", "server_error_occurred", 0, "", null, !1).then(async () => {
                    setTimeout(function() {
                        showKeepWatchingNotice(), window.resumeVGCountdown()
                    }, 1500)
                }), t && (t.style.pointerEvents = "auto", t.style.opacity = "1"), console.clear())
            })
        })
    } catch (r) {
        if (!forceStop) return;
        showWorkerNotification(0, "error", "server_error_occurred", 0, "", null, !1).then(async () => {
            setTimeout(function() {
                showKeepWatchingNotice(), window.resumeVGCountdown()
            }, 1500);
            let e = vgQuerySelector("#tx-report-btn");
            e && (e.style.pointerEvents = "auto", e.style.opacity = "1")
        })
    }
}

function removePlaybackPopups() {
    clearInterval(popupInterval), popupInterval = setInterval(() => {
        try {
            let e = vgCleanupSelector("tp-yt-iron-overlay-backdrop", "cleanup", "removePlaybackPopups.modalOverlay"),
                t = vgCleanupSelector("ytd-enforcement-message-view-model", "cleanup", "removePlaybackPopups.enforcementPopup"),
                r = "function" == typeof vgCleanupSelector ? vgCleanupSelector("#dismiss-button", "cleanup", "removePlaybackPopups.dismissButton") : vgQueryOptionalSelector("#dismiss-button", {
                    workflow: "cleanup",
                    intent: "cleanup",
                    phase: "removePlaybackPopups.dismissButton"
                }),
                n = [".ytp-ad-overlay-close-button", ".ytp-ad-button.ytp-ad-info-dialog-mute-button.ytp-ad-button-link", ".ytp-ad-feedback-dialog-reason-input", ".ytp-ad-feedback-dialog-confirm-button", "yt-confirm-dialog-renderer a.yt-simple-endpoint.style-scope.yt-button-renderer", ".ytd-mealbar-promo-renderer #dismiss-button a.yt-simple-endpoint.style-scope.ytd-button-renderer", "tp-yt-paper-dialog #dismiss-button", ".yt-mealbar-promo-renderer #dismiss-button", "yt-button-renderer button .yt-spec-button-shape-next", ],
                o = document.body.style;
            o.setProperty("overflow-y", "auto", "important"), e && (e.removeAttribute("opened"), e.remove()), t && (r && SimulateClick(r), t.remove());
            for (let a = 0; a < n.length; a++) {
                let i = vgCleanupSelector(n[a], "cleanup", "removePlaybackPopups.closeButton");
                if (i) {
                    SimulateClick(i).then(e => {
                        e && n[a].includes(".ytd-mealbar-promo-renderer") && i.parentElement.parentElement.remove()
                    });
                    break
                }
            }
            removePageAdvertisements()
        } catch (c) {
            try {
                console.warn("ViewGrip popup cleanup skipped:", c)
            } catch (l) {}
        }
    }, 3e3)
}

function removePageAdvertisements() {
    let e = vgQueryOptionalSelectorAll("div#player-ads.style-scope.ytd-watch-flexy, div#panels.style-scope.ytd-watch-flexy", {
            workflow: "cleanup",
            intent: "cleanup",
            phase: "removePageAdvertisements.sponsor"
        }),
        t = document.getElementById("vg-hide-page-ads-style");
    !t && ((t = document.createElement("style")).id = "vg-hide-page-ads-style", t.textContent = `
           ytd-action-companion-ad-renderer,
           ytd-display-ad-renderer,
           ytd-video-masthead-ad-advertiser-info-renderer,
           ytd-video-masthead-ad-primary-video-renderer,
           ytd-in-feed-ad-layout-renderer,
           ytd-ad-slot-renderer,
           yt-about-this-ad-renderer,
           yt-mealbar-promo-renderer,
           ytd-statement-banner-renderer,
           ytd-ad-slot-renderer,
           ytd-in-feed-ad-layout-renderer,
           ytd-banner-promo-renderer-background
           statement-banner-style-type-compact,
           .ytd-video-masthead-ad-v3-renderer,
           div#root.style-scope.ytd-display-ad-renderer.yt-simple-endpoint,
           div#sparkles-container.style-scope.ytd-promoted-sparkles-web-renderer,
           div#main-container.style-scope.ytd-promoted-video-renderer,
           div#player-ads.style-scope.ytd-watch-flexy,
           ad-slot-renderer,
           ytm-promoted-sparkles-web-renderer,
           masthead-ad,
           tp-yt-iron-overlay-backdrop,
           ytd-compact-promoted-item-renderer,
           ytd-promoted-sparkles-web-renderer,

           #masthead-ad {
               display: none !important;
           }
       `, document.head && document.head.appendChild(t)), e?.forEach(e => {
        "rendering-content" === e.getAttribute("id") && e.childNodes?.forEach(t => {
            t?.data.targetId && "engagement-panel-macro-markers-description-chapters" !== t?.data.targetId && (e.style.display = "none")
        })
    })
}
async function runScheduledInteractions() {
    try {
        let e = vgQuerySelector(".html5-main-video");
        if (e && "function" == typeof UltimateYouTubeWatcher) {
            if (window.humanWatcher && "function" == typeof window.humanWatcher.stop) try {
                window.humanWatcher.stop()
            } catch (t) {}
            window.humanWatcher = new UltimateYouTubeWatcher;
            let r = Math.max(15, Math.min(120, Math.round((remainingTime || 6e4) / 1e3)));
            window.humanWatcher.startWatching(e, r).catch(() => {})
        }
    } catch (n) {}
    return !0
}

function restartWorkerSession(e = "worker_runtime", t = "restart_session") {
    try {
        0 === arguments.length && "function" == typeof vgWorkflowSoftFail && vgWorkflowSoftFail(e, t, "Worker session is being restarted after workflow/state failure.")
    } catch (r) {}
    let n = window.VG_SAFE_RUNTIME || (window.VG_SAFE_RUNTIME = {}),
        o = Date.now();
    n.restartInProgress && o - n.restartInProgress < 5e3 || (n.restartInProgress = o, startGetVideo = !0, startVerification = !0, vgRuntimeHeartbeat("restart", t, {
        workflow: e
    }), stopAllWorkerProcesses(), setTimeout(function() {
        vgSafeWorkerNotification(1, "warning", "restarting", 0, "", null, !1, 7e3, !0).then(e => {
            if (!e) {
                n.restartInProgress = 0, navigateWorkerTab(MainUrl + "/worker/start");
                return
            }
            setTimeout(function() {
                n.restartInProgress = 0, navigateWorkerTab(MainUrl + "/worker/start")
            }, 1800)
        }).catch(() => {
            n.restartInProgress = 0, navigateWorkerTab(MainUrl + "/worker/start")
        })
    }, 1200))
}

function stopAllWorkerProcesses() {
    try {
        window.humanWatcher && "function" == typeof window.humanWatcher.stop && window.humanWatcher.stop()
    } catch (e) {}
    clearInterval(ViolationInterval), clearInterval(checkDeleted), clearTimeout(TimeOutTyping), clearTimeout(contentInteractionTimeout), clearTimeout(vgInteractionStartTimer), vgClearInteractionTimers(), vgMandatoryInteractionActive = !1, vgMandatoryInteractionPending = !1, interactionSchedulerActive = !1, interactionRunning = !1;
    try {
        window.VG_AUTOPLAY_ACTIVE = !1
    } catch (t) {}
    try {
        window.VG_AUTOPLAY_VIDEO_ID = null
    } catch (r) {}
    try {
        window.SPA_CYCLE_STARTED = !0
    } catch (n) {}
    clearInterval(popupInterval);
    try {
        vgStopRuntimeWatchdog()
    } catch (o) {}
    try {
        window.VG_WORKER_WATCH_STARTED = !1
    } catch (a) {}
    forceStop = !1, $("html, body").stop(!0, !0), window.pauseVGCountdown()
}

function submitInteractionReport(e, t, r, n) {
    if (!forceStop) return;
    let o = (e = 0) => {
            setTimeout(() => {
                "function" == typeof n && n()
            }, Math.max(0, Number(e) || 0))
        },
        a = (e, t) => {
            let r = e || "warning",
                n = "success" === r ? vgActionDelay(1800, 2600) : vgActionDelay(1200, 1900);
            remainingTime > 5e3 && forceStop ? Promise.resolve(showWorkerNotification(0, r, t || "server_error_occurred", 0, "", null, !1)).then(() => o(n)).catch(() => o(n)) : o(vgActionDelay(250, 500))
        };
    try {
        $.ajax({
            url: MainUrl + "/api/worker/interaction",
            type: "GET",
            data: {
                token: e,
                type: t,
                value: r
            },
            cache: !1,
            timeout: 5e3,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        }).done(function(e) {
            e && "object" == typeof e || (e = {
                status: "warning",
                message: "server_error_occurred"
            }), a(e.status, e.message), console.clear()
        }).fail(function() {
            a("error", "server_error_occurred"), console.clear()
        })
    } catch (i) {
        a("error", "server_error_occurred"), console.clear()
    }
}

function submitCampaignReport(e, t, r, n) {
    forceStop && (vgRuntimeHeartbeat("verification", "submit_campaign_report:" + String(t || ""), {
        workflow: "campaign_report",
        backupUrl: n
    }), vgSafeWorkerNotification(1, "warning", "reporting_campaign", 0, "", null, !1, 7e3, !0).then(() => {
        setTimeout(function() {
            if (forceStop) try {
                $.ajax({
                    url: MainUrl + "/api/worker/report",
                    type: "GET",
                    data: {
                        token: e,
                        type: t,
                        value: r
                    },
                    cache: !1,
                    timeout: 5e3,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                }).done(function(e) {
                    try {
                        if (!forceStop) return;
                        vgSafeWorkerNotification(0, e.status, e.message, 0, "", null, !1, 7e3, !0).finally(() => {
                            "notWorkKeyword" === t || "notInChannel" === t || "emptyKeyword" === t ? setTimeout(function() {
                                redirectToVideoSafely(n, {
                                    workflow: "campaign_report",
                                    status: String(t || "reported")
                                })
                            }, 1500) : setTimeout(function() {
                                restartWorkerSession("campaign_report", String(t || "reported"))
                            }, 1500)
                        })
                    } catch (r) {
                        vgRecoverWorkflow("campaign_report", "response_handler_error", "Campaign report response handler threw.", r, {
                            action: "restart",
                            backupUrl: n
                        })
                    }
                    console.clear()
                }).fail(function(e) {
                    forceStop && (vgRecoverWorkflow("campaign_report", "report_request_failed", "Campaign report request failed.", e, {
                        action: "restart",
                        backupUrl: n
                    }), console.clear())
                })
            } catch (o) {
                if (!forceStop) return;
                vgRecoverWorkflow("campaign_report", "report_runtime_error", "Campaign report runtime error.", o, {
                    action: "restart",
                    backupUrl: n
                })
            }
        }, 2e3)
    }))
}

function extractYouTubeVideoId(e = window.location.href) {
    try {
        let t = new URL(e),
            r = t.hostname;
        if (r.includes("youtube.com") || "youtu.be" === r) {
            if ("/watch" === t.pathname) return t.searchParams.get("v");
            if ("youtu.be" === r) return t.pathname.slice(1);
            let n = t.pathname.match(/\/(embed|v)\/([a-zA-Z0-9_-]{11})/);
            if (n && n[2]) return n[2];
            let o = t.pathname.match(/\/shorts\/([a-zA-Z0-9_-]{11})/);
            if (o && o[1]) return o[1]
        }
        return null
    } catch (a) {
        return null
    }
}

function parseYouTubeIsoDurationToSeconds(e) {
    try {
        if ("string" != typeof e) return 0;
        let t = e.trim().match(/^PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?$/);
        if (!t) return 0;
        let r = parseInt(t[1] || 0, 10),
            n = parseInt(t[2] || 0, 10),
            o = parseInt(t[3] || 0, 10),
            a = 3600 * r + 60 * n + o;
        return Number.isFinite(a) && a > 0 ? Math.round(a) : 0
    } catch (i) {
        return 0
    }
}

function vgIsUsableYouTubeDuration(e) {
    let t = Number(e);
    return Number.isFinite(t) && t > 0 && t < 86400
}

function readVideoObjectDurationFromLdJsonOnce() {
    try {
        let e = Array.from(vgQueryOptionalSelectorAll('script[type="application/ld+json"]', {
            workflow: "duration",
            intent: "probe",
            phase: "readYouTubeVideoDuration.videoObject"
        }));
        for (let t of e) {
            let r = null;
            try {
                r = JSON.parse(String(t && t.textContent || "").trim())
            } catch (n) {
                continue
            }
            let o = Array.isArray(r) ? r.slice() : [r];
            for (; o.length;) {
                let a = o.shift();
                if (a && "object" == typeof a) {
                    if ("VideoObject" === a["@type"] && "string" == typeof a.duration) {
                        let i = parseYouTubeIsoDurationToSeconds(a.duration);
                        if (vgIsUsableYouTubeDuration(i)) return i
                    }
                    if (Array.isArray(a)) {
                        o.push(...a);
                        continue
                    }
                    for (let c of Object.keys(a)) {
                        let l = a[c];
                        l && "object" == typeof l && o.push(l)
                    }
                }
            }
        }
    } catch (u) {}
    return 0
}

function vgReadPlayerApiDuration(e) {
    try {
        if (!e || "function" != typeof e.getDuration) return 0;
        let t = Number(e.getDuration());
        return vgIsUsableYouTubeDuration(t) ? Math.round(t) : 0
    } catch (r) {
        return 0
    }
}

function vgGetVisibleVideoScore(e) {
    try {
        if (!e || "function" != typeof e.getBoundingClientRect) return -1;
        let t = e.getBoundingClientRect(),
            r = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0),
            n = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0),
            o = Math.max(0, Math.min(t.right, r) - Math.max(t.left, 0)),
            a = Math.max(0, Math.min(t.bottom, n) - Math.max(t.top, 0)),
            i = o * a,
            c = Math.max(1, t.width * t.height),
            l = vgIsUsableYouTubeDuration(Number(e.duration)) ? 1e5 : 0,
            u = Number(e.readyState || 0) >= 1 ? 5e4 : 0,
            s = !e.paused && Number(e.currentTime || 0) >= 0 ? 25e3 : 0;
        return i + 1e4 * (i / c) + l + u + s
    } catch (d) {
        return -1
    }
}

function vgGetActiveYouTubeVideoElement(e = null) {
    try {
        let t = [],
            r = e => {
                e && e.tagName && "video" === String(e.tagName).toLowerCase() && !t.includes(e) && t.push(e)
            };
        for (let n of (r(e), ["#shorts-player video.html5-main-video", "#shorts-player video", "ytd-reel-video-renderer[is-active] video", 'ytd-reel-video-renderer[aria-hidden="false"] video', "ytd-reel-video-renderer[is-active] video.html5-main-video", "video.html5-main-video", "video[src]", "video"])) try {
            let o = vgQueryOptionalSelectorAll(n, {
                workflow: "duration",
                intent: "probe",
                phase: "vgGetActiveYouTubeVideoElement." + n
            });
            for (let a of Array.from(o || [])) r(a)
        } catch (i) {}
        if (!t.length) return e || null;
        return t.sort((e, t) => vgGetVisibleVideoScore(t) - vgGetVisibleVideoScore(e)), t[0] || e || null
    } catch (c) {
        return e || null
    }
}

function vgReadYouTubeInitialPlayerResponseDurationOnce(e = "") {
    try {
        let t = String(e || extractYouTubeVideoId() || "").trim();
        if (!t) return 0;
        let r = Array.from(vgQueryOptionalSelectorAll("script", {
                workflow: "duration",
                intent: "probe",
                phase: "readYouTubeVideoDuration.initialPlayerResponse"
            })),
            n = t.replace(/[.*+?^${}()|[\]\\]/g, "\\$&"),
            o = RegExp('"videoId"\\s*:\\s*"' + n + '"'),
            a = /"lengthSeconds"\s*:\s*"?(\d+)"?/g;
        for (let i of r) {
            let c = String(i && i.textContent || "");
            if (!c || -1 === c.indexOf(t) || -1 === c.indexOf("lengthSeconds")) continue;
            let l = o.exec(c);
            if (!l) continue;
            let u = Math.max(0, l.index - 12e3),
                s = Math.min(c.length, l.index + 18e3),
                d = c.slice(u, s);
            a.lastIndex = 0;
            let f;
            for (; f = a.exec(d);) {
                let m = Number(f[1]);
                if (vgIsUsableYouTubeDuration(m)) return Math.round(m)
            }
        }
    } catch (p) {}
    return 0
}

function readMatchedVideoObjectDurationFromLdJsonOnce(e = "") {
    try {
        let t = String(e || extractYouTubeVideoId() || "").trim();
        if (!t) return 0;
        let r = Array.from(vgQueryOptionalSelectorAll('script[type="application/ld+json"]', {
            workflow: "duration",
            intent: "probe",
            phase: "readYouTubeVideoDuration.matchedVideoObject"
        }));
        for (let n of r) {
            let o = String(n && n.textContent || "").trim();
            if (!o || -1 === o.indexOf(t)) continue;
            let a = null;
            try {
                a = JSON.parse(o)
            } catch (i) {
                continue
            }
            let c = Array.isArray(a) ? a.slice() : [a];
            for (; c.length;) {
                let l = c.shift();
                if (l && "object" == typeof l) {
                    if ("VideoObject" === l["@type"] && "string" == typeof l.duration) {
                        let u = JSON.stringify(l);
                        if (-1 === u.indexOf(t)) continue;
                        let s = parseYouTubeIsoDurationToSeconds(l.duration);
                        if (vgIsUsableYouTubeDuration(s)) return s
                    }
                    if (Array.isArray(l)) {
                        c.push(...l);
                        continue
                    }
                    for (let d of Object.keys(l)) {
                        let f = l[d];
                        f && "object" == typeof f && c.push(f)
                    }
                }
            }
        }
    } catch (m) {}
    return 0
}
async function waitForShortsDurationReliable(e = {}) {
    try {
        let t = Math.max(1200, Number(e.maxWaitMs) || Number(e.shortsVideoWaitMs) || 8e3),
            r = Math.max(100, Number(e.retryIntervalMs) || 180),
            n = String(e.videoId || extractYouTubeVideoId() || "").trim(),
            o = Date.now();
        for (; Date.now() - o <= t;) {
            let a = vgGetActiveYouTubeVideoElement(e.video || null),
                i = Number(a && a.duration);
            if (vgIsUsableYouTubeDuration(i)) return Math.round(i);
            let c = [],
                l = e => {
                    e && !c.includes(e) && c.push(e)
                };
            for (let u of (l(e.player || null), l(vgQueryOptionalSelector("#shorts-player", {
                    workflow: "duration",
                    intent: "probe",
                    phase: "waitForShortsDurationReliable.shortsPlayer"
                })), l(vgQueryOptionalSelector("#movie_player", {
                    workflow: "duration",
                    intent: "probe",
                    phase: "waitForShortsDurationReliable.moviePlayer"
                })), c)) {
                let s = vgReadPlayerApiDuration(u);
                if (vgIsUsableYouTubeDuration(s)) return Math.round(s)
            }
            let d = vgReadYouTubeInitialPlayerResponseDurationOnce(n);
            if (vgIsUsableYouTubeDuration(d)) return Math.round(d);
            let f = readMatchedVideoObjectDurationFromLdJsonOnce(n);
            if (vgIsUsableYouTubeDuration(f)) return Math.round(f);
            await new Promise(e => setTimeout(e, r))
        }
    } catch (m) {}
    return 0
}
async function waitForYouTubeVideoElementDuration(e, t = 1200, r = 250) {
    try {
        let n = Date.now();
        for (; e && Date.now() - n <= t;) {
            let o = Number(e.duration);
            if (vgIsUsableYouTubeDuration(o)) return Math.round(o);
            await new Promise(e => setTimeout(e, r))
        }
    } catch (a) {}
    return 0
}
async function waitForVideoObjectDurationFromLdJson(e = 8e3, t = 250) {
    try {
        let r = Date.now();
        for (; Date.now() - r < e;) {
            let n = readVideoObjectDurationFromLdJsonOnce();
            if (vgIsUsableYouTubeDuration(n)) return Math.round(n);
            await new Promise(e => setTimeout(e, t))
        }
    } catch (o) {}
    return 0
}
async function readYouTubeVideoDuration(e = {}) {
    try {
        let t = window.location.pathname.includes("/shorts/"),
            r = Number(e.retryIntervalMs) || 250,
            n = String(e.videoId || extractYouTubeVideoId() || "").trim();
        if (t) return await waitForShortsDurationReliable({
            player: e.player || vgQueryOptionalSelector("#shorts-player, #movie_player", {
                workflow: "duration",
                intent: "probe",
                phase: "readYouTubeVideoDuration.shortsPlayer"
            }),
            video: e.video || null,
            videoId: n,
            shortsVideoWaitMs: Math.max(1200, Number(e.shortsVideoWaitMs) || 8e3),
            retryIntervalMs: Math.max(100, Number(r) || 180)
        });
        let o = vgReadYouTubeInitialPlayerResponseDurationOnce(n);
        if (vgIsUsableYouTubeDuration(o)) return Math.round(o);
        let a = readMatchedVideoObjectDurationFromLdJsonOnce(n);
        if (vgIsUsableYouTubeDuration(a)) return Math.round(a);
        let i = e.player || await waitForElement("#movie_player", Math.max(0, Number(e.moviePlayerWaitMs) || 1800), {
                workflow: "duration",
                intent: "probe",
                phase: "readYouTubeVideoDuration.moviePlayer"
            }),
            c = i && (i.classList && i.classList.contains("ad-showing") || vgIsYouTubeAdShowing(i));
        if (!c) {
            let l = e.video || vgQueryOptionalSelector("video.html5-main-video, video[src], video", {
                    workflow: "duration",
                    intent: "probe",
                    phase: "readYouTubeVideoDuration.videoElement.quick"
                }) || await waitForElement("video.html5-main-video, video[src], video", Math.max(0, Number(e.regularVideoWaitMs) || 1200), {
                    workflow: "duration",
                    intent: "probe",
                    phase: "readYouTubeVideoDuration.videoElement"
                }),
                u = await waitForYouTubeVideoElementDuration(l, Math.max(0, Number(e.regularVideoWaitMs) || 1200), r);
            if (vgIsUsableYouTubeDuration(u)) return Math.round(u)
        }
        let s = Math.max(0, Number(e.jsonDurationWaitMs) || 8e3);
        if (!s) return 0;
        return await waitForVideoObjectDurationFromLdJson(s, r)
    } catch (d) {
        return 0
    }
}

function waitForElement(e, t = 15e3, r = {}) {
    return new Promise(n => {
        if (!e || "string" != typeof e) {
            n(null);
            return
        }
        let o = r && "object" == typeof r ? Object.assign({
                workflow: "wait_for_element",
                intent: "probe",
                phase: "waitForElement"
            }, r) : {
                workflow: "wait_for_element",
                intent: "probe",
                phase: String(r || "waitForElement")
            },
            a = !1,
            i = null,
            c = null,
            l = () => {
                i && clearTimeout(i), c && c.disconnect(), removeSafeEventListener(document, "DOMContentLoaded", d)
            },
            u = (t, r = !1) => {
                if (!a) {
                    if (a = !0, l(), !t && r && "required" === String(o.intent || "").toLowerCase()) try {
                        "function" == typeof vgWorkflowSoftFail && vgWorkflowSoftFail(o.workflow || "wait_for_element", "required_element_timeout", "Required element was not available before timeout. selector=" + e + " | phase=" + (o.phase || "waitForElement"))
                    } catch (i) {}
                    n(t || null)
                }
            },
            s = () => {
                try {
                    return vgQuerySelector(e, o)
                } catch (t) {
                    try {
                        "function" == typeof vgReportCaughtError && vgReportCaughtError(t, (o.workflow || "wait_for_element") + ".selector_query_error")
                    } catch (r) {}
                    return null
                }
            },
            d = () => {
                let e = s();
                e && u(e, !1)
            },
            f = s();
        if (f) {
            u(f, !1);
            return
        }
        c = new MutationObserver(d);
        let m = document.documentElement || document.body;
        m && c.observe(m, {
            childList: !0,
            subtree: !0
        }), "loading" === document.readyState && addSafeEventListener(document, "DOMContentLoaded", d), i = setTimeout(() => u(null, !0), t)
    })
}
async function waitForYouTubePageUpdate(e = 1e4) {
    window._YTLoadState || (window._YTLoadState = {
        listenerInitialized: !1,
        waiters: []
    });
    let t = window._YTLoadState;
    if (!t.listenerInitialized) {
        t.listenerInitialized = !0;
        let r = async () => {
            let e = [...t.waiters];
            t.waiters = [];
            try {
                forceStop && vgSafeWorkerNotification(1, "default", "wait_moment", 0, "", null, !1, 4e3, !0).catch(() => {})
            } catch (r) {}
            for (let n of (await new Promise(e => setTimeout(e, 1e3)), e)) try {
                n(Boolean(forceStop))
            } catch (o) {}
        };
        addSafeEventListener(window, "yt-page-data-updated", r), t._ytPageDataHandler = r, t.removeListener = () => {
            t._ytPageDataHandler && (removeSafeEventListener(window, "yt-page-data-updated", t._ytPageDataHandler), t._ytPageDataHandler = null, t.listenerInitialized = !1)
        }
    }
    return new Promise(r => {
        let n = null,
            o = e => {
                n && clearTimeout(n), r(Boolean(e))
            };
        t.waiters.push(o), n = setTimeout(() => {
            t.waiters = t.waiters.filter(e => e !== o), r(!1)
        }, e)
    })
}

function findCommentElementByText(e) {
    if (!e || "string" != typeof e) return null;
    let t = e => String(e || "").toLowerCase().replace(/[^a-z0-9]/g, ""),
        r = t(e);
    if (!r) return null;
    let n = vgQuerySelector("#comments") || document.body;
    if (!n) return null;
    let o = document.createTreeWalker(n, NodeFilter.SHOW_TEXT, {
            acceptNode: function(e) {
                return e.nodeValue.trim() && e.parentElement && null !== e.parentElement.offsetParent ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT
            }
        }, !1),
        a = o.nextNode();
    for (; a;) {
        let i = t(a.nodeValue.trim());
        if (i.includes(r)) {
            let c = a.parentElement;
            for (; c && !["DIV", "ARTICLE", "SECTION", "LI", "P", "SPAN"].includes(c.tagName);) c = c.parentElement;
            return c || a.parentElement
        }
        a = o.nextNode()
    }
    return null
}
async function loadFeatureToggles() {
    let e = {
        "tx-like": !0,
        "tx-subscribe": !0,
        "tx-comment": !0,
        "tx-likecomment": !0
    };
    try {
        let t = await vgStorageSyncGetSafe(["txFeatures"], 5e3, {}),
            r = t && t.txFeatures && "object" == typeof t.txFeatures ? t.txFeatures : {},
            n = {
                ...e
            };
        return Object.keys(e).forEach(e => {
            Object.prototype.hasOwnProperty.call(r, e) && (n[e] = !1 !== r[e])
        }), n
    } catch (o) {
        return e
    }
}
async function scrollElementToCenter(e, t = null) {
    let r = e => {
        if ("function" == typeof t) try {
            t(Boolean(e))
        } catch (r) {}
        return Boolean(e)
    };
    if (!e || !e.isConnected) return r(!1);
    try {
        let n = e.getBoundingClientRect(),
            o = n.top + window.scrollY - window.innerHeight / 2 + n.height / 2;
        return await vgScrollToY(o, 800, 1400), r(!0)
    } catch (a) {
        return r(!1)
    }
}
async function vgScrollElementForMandatoryAction(e, t = null, r = .62) {
    let n = e => {
        if ("function" == typeof t) try {
            t(Boolean(e))
        } catch (r) {}
        return Boolean(e)
    };
    if (!e || !e.isConnected) return n(!1);
    try {
        let o = e.getBoundingClientRect(),
            a = window.innerHeight || document.documentElement.clientHeight || 700,
            i = Math.max(.22, Math.min(.78, Number(r) || .62)),
            c = window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0,
            l = Math.max(0, document.documentElement.scrollHeight - a),
            u = o.top + c - a * i + o.height / 2;
        if (u = Math.max(0, Math.min(l, u)), 90 > Math.abs(u - c) && l > 180) {
            let s = u + 180 <= l;
            u = s ? u + vgActionDelay(120, 190) : Math.max(0, u - vgActionDelay(120, 190))
        }
        return await vgScrollToY(u, 1100, 1800), n(!0)
    } catch (d) {
        return n(!1)
    }
}

function vgGetVideoLikeButton() {
    try {
        if (vgIsShortsInteractionContext()) return vgQuerySelector('#shorts-player like-button-view-model button[aria-pressed], reel-action-bar-view-model like-button-view-model button[aria-pressed], ytd-reel-video-renderer[is-active] like-button-view-model button[aria-pressed], ytd-reel-video-renderer[aria-hidden="false"] like-button-view-model button[aria-pressed], yt-reel-player-overlay-renderer like-button-view-model button[aria-pressed], like-button-view-model button[aria-pressed]', {
            workflow: "like",
            intent: "required",
            phase: "vgGetVideoLikeButton.shorts"
        });
        return vgQuerySelector("like-button-view-model button[aria-pressed]", {
            workflow: "like",
            intent: "required",
            phase: "vgGetVideoLikeButton"
        }) || vgQuerySelector("#segmented-like-button like-button-view-model button[aria-pressed]", {
            workflow: "like",
            intent: "required",
            phase: "vgGetVideoLikeButton"
        }) || vgQuerySelector("#top-level-buttons-computed like-button-view-model button[aria-pressed]", {
            workflow: "like",
            intent: "required",
            phase: "vgGetVideoLikeButton"
        }) || vgQuerySelector("ytd-segmented-like-dislike-button-renderer #like-button button[aria-pressed]", {
            workflow: "like",
            intent: "required",
            phase: "vgGetVideoLikeButton"
        }) || vgQuerySelector("#top-level-buttons-computed ytd-toggle-button-renderer:first-child button[aria-pressed]", {
            workflow: "like",
            intent: "required",
            phase: "vgGetVideoLikeButton"
        })
    } catch (e) {
        return null
    }
}

function vgGetCommentsAnchor() {
    try {
        return vgQuerySelector("ytd-comments#comments", {
            workflow: "comment",
            intent: "required",
            phase: "vgGetCommentsAnchor"
        }) || vgQuerySelector("#comments", {
            workflow: "comment",
            intent: "required",
            phase: "vgGetCommentsAnchor"
        }) || vgQuerySelector("#comment-teaser", {
            workflow: "comment",
            intent: "required",
            phase: "vgGetCommentsAnchor"
        }) || vgQuerySelector("#below", {
            workflow: "comment",
            intent: "required",
            phase: "vgGetCommentsAnchor"
        }) || vgQuerySelector("#above-the-fold", {
            workflow: "comment",
            intent: "required",
            phase: "vgGetCommentsAnchor"
        })
    } catch (e) {
        return null
    }
}
async function dismissYouTubeBackdrop(e = 2e3) {
    try {
        let t = Date.now(),
            r = () => {
                try {
                    let e = "function" == typeof vgCleanupSelector ? vgCleanupSelector : (e, t, r) => vgQueryOptionalSelector(e, {
                        workflow: t,
                        intent: "cleanup",
                        phase: r
                    });
                    return Boolean(e("tp-yt-iron-overlay-backdrop[opened]", "cleanup", "dismissYouTubeBackdrop.backdrop") || e("tp-yt-paper-dialog", "cleanup", "dismissYouTubeBackdrop.paperDialog") || e("ytd-confirm-dialog-renderer", "cleanup", "dismissYouTubeBackdrop.confirmDialog") || e("ytd-enforcement-message-view-model", "cleanup", "dismissYouTubeBackdrop.enforcement"))
                } catch (t) {
                    return !1
                }
            };
        for (; Date.now() - t < e;) {
            if (r()) return document.dispatchEvent(new KeyboardEvent("keydown", {
                key: "Escape",
                code: "Escape",
                keyCode: 27,
                which: 27,
                bubbles: !0
            })), document.dispatchEvent(new KeyboardEvent("keyup", {
                key: "Escape",
                code: "Escape",
                keyCode: 27,
                which: 27,
                bubbles: !0
            })), !0;
            await vgSleep(50)
        }
        return !1
    } catch (n) {
        return !1
    }
}

function addSafeEventListener(e, t, r, n = !1) {
    if (!e || "function" != typeof e.addEventListener || "function" != typeof r) return;
    safeEventListeners.has(e) || safeEventListeners.set(e, new Map);
    let o = safeEventListeners.get(e);
    o.has(t) || o.set(t, new Set);
    let a = o.get(t);
    a.has(r) || (a.add(r), e.addEventListener(t, r, n))
}

function removeSafeEventListener(e, t, r) {
    if (!e || "function" != typeof e.removeEventListener || !safeEventListeners.has(e)) return;
    let n = safeEventListeners.get(e);
    if (!n.has(t)) return;
    let o = n.get(t);
    o.has(r) && (o.delete(r), e.removeEventListener(t, r))
}
async function isActiveYouTubeVideoMuted() {
    let e = vgQuerySelector(".html5-video-container");
    if (!e) return !1;
    let t = vgQuerySelectorAll("video", e, "isActiveYouTubeVideoMuted");
    if (!t || 0 === t.length) return !1;
    let r = null;
    return t.forEach(e => {
        e.paused || e.ended || !(e.readyState > 0) || (r = e)
    }), !!r && (!0 === r.muted || 0 === r.volume)
}
async function isCurrentTabMuted() {
    let e = await vgRuntimeSendMessageSafe({
        cmd: "isCurrentTabMuted"
    }, 5e3, !1);
    return Boolean(e)
}

function enableWorkerInputGuard() {
    if (window.VG_INPUT_GUARD_ENABLED) return;
    window.VG_INPUT_GUARD_ENABLED = !0;
    let e = ["ArrowUp", "ArrowDown", "PageUp", "PageDown", "Home", "End", " ", "Spacebar"];

    function t(t) {
        let r = t && t.target ? t.target : null,
            n = r && 1 === r.nodeType && "function" == typeof r.closest ? r : r && r.parentElement && "function" == typeof r.parentElement.closest ? r.parentElement : null;
        if (!(n && n.closest("#tx-settings-btn, #tx-settings-popup, #tx-skip-btn, #tx-stop-btn, #tx-report-btn, #tx-report-overlay, #tx-refresh-modal, #tx-global-overlay"))) {
            if ("keydown" === t.type) {
                e.includes(t.key) && (t.preventDefault(), t.stopPropagation());
                return
            }
            if ("wheel" === t.type || "touchmove" === t.type) {
                t.preventDefault(), t.stopPropagation();
                return
            }
            if ("mousedown" === t.type || "mouseup" === t.type || "click" === t.type || "dblclick" === t.type) {
                VG_ALLOW_USER_CLICK || (t.preventDefault(), t.stopPropagation());
                return
            }
            "contextmenu" === t.type && (t.preventDefault(), t.stopPropagation())
        }
    }
    addSafeEventListener(window, "wheel", t, {
        passive: !1,
        capture: !0
    }), addSafeEventListener(window, "touchmove", t, {
        passive: !1,
        capture: !0
    }), addSafeEventListener(window, "keydown", t, {
        passive: !1,
        capture: !0
    }), addSafeEventListener(window, "mousedown", t, !0), addSafeEventListener(window, "mouseup", t, !0), addSafeEventListener(window, "click", t, !0), addSafeEventListener(window, "dblclick", t, !0), addSafeEventListener(window, "contextmenu", t, !0)
}
window.VG_MESSAGE_LISTENER_ADDED || (window.VG_MESSAGE_LISTENER_ADDED = !0, chrome.runtime.onMessage.addListener((e, t, r) => {
    if (!e || !e.msg) return !1;
    let n = ["Rendering", "StartGetData", "StartWorker", "slowConnection", "connectionRetry"];
    if (!n.includes(e.msg)) return !1;
    let o = !1,
        a = e => {
            if (!o) {
                o = !0;
                try {
                    r(e || {
                        ok: !0
                    })
                } catch (t) {}
            }
        };
    if ("function" == typeof vgMarkWorkerControlled) vgMarkWorkerControlled("Background.js:" + e.msg);
    else try {
        window.VG_WORKER_CONTROLLED_TAB = !0
    } catch (i) {}
    return (async () => {
        try {
            switch (await waitForDomReady(), e.msg) {
                case "Rendering":
                    handleRenderingMessage(), a({
                        ok: !0,
                        handled: "Rendering"
                    });
                    break;
                case "StartGetData":
                    await handleFetchDataMessage(), a({
                        ok: !0,
                        handled: "StartGetData"
                    });
                    break;
                case "StartWorker":
                    StartWorker && handleStartWorkerMessage(), a({
                        ok: !0,
                        handled: "StartWorker"
                    });
                    break;
                case "slowConnection":
                    handleSlowConnectionMessage(), a({
                        ok: !0,
                        handled: "slowConnection"
                    });
                    break;
                case "connectionRetry":
                    await handleConnectionRetryMessage(), a({
                        ok: !0,
                        handled: "connectionRetry"
                    });
                    break;
                default:
                    a({
                        ok: !1,
                        reason: "unknown_message"
                    })
            }
        } catch (t) {
            a({
                ok: !1,
                error: t ? t.toString() : "unknown"
            })
        }
    })(), !0
}));