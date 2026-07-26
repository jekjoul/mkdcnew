/*!
 * UNIVERSAL BACKGROUND.JS (Chrome MV3 + Firefox MV2)
 * Optimized Version — No Memory Leak — CPU Stable — Same Logic
 */
const api = "undefined" != typeof chrome ? chrome : browser,
    actionAPI = api.action ? api.action : api.browserAction;

function getRuntimeLastErrorSafe() {
    try {
        return api && api.runtime ? api.runtime.lastError : null
    } catch (e) {
        return null
    }
}

function isTabGoneRuntimeError(e) {
    let t = String(e && (e.message || e) || "").toLowerCase();
    return t.includes("no tab with id") || t.includes("invalid tab id") || t.includes("tab was closed") || t.includes("cannot find tab") || t.includes("tabs cannot be edited right now")
}

function isBenignMessageRuntimeError(e) {
    let t = String(e && (e.message || e) || "").toLowerCase();
    return t.includes("a listener indicated an asynchronous response") || t.includes("message channel closed before a response was received") || t.includes("the message port closed before a response was received") || t.includes("receiving end does not exist") || t.includes("could not establish connection") || t.includes("extension context invalidated") || t.includes("context invalidated")
}

function onceCallback(e) {
    let t = !1;
    return (...n) => {
        if (!t) {
            t = !0;
            try {
                e && e(...n)
            } catch (r) {}
        }
    }
}

function handlePromiseResult(e, t, n) {
    try {
        e && "function" == typeof e.then && e.then(t).catch(n || (() => {}))
    } catch (r) {}
}
const safeTabs = {
        get(e, t) {
            let n = onceCallback(t || (() => {}));
            if ("number" != typeof e || e < 0) return n(null);
            try {
                let r = api.tabs.get(e, t => {
                    let r = getRuntimeLastErrorSafe();
                    if (r || !t) return isTabGoneRuntimeError(r) && clearTabScopedState(e), n(null);
                    n(t)
                });
                handlePromiseResult(r, e => n(e || null), t => {
                    isTabGoneRuntimeError(t) && clearTabScopedState(e), n(null)
                })
            } catch (a) {
                isTabGoneRuntimeError(a) && clearTabScopedState(e), n(null)
            }
        },
        query(e, t = () => {}) {
            let n = onceCallback(t);
            try {
                let r = api.tabs.query(e || {}, e => {
                    let t = getRuntimeLastErrorSafe();
                    if (t || !Array.isArray(e)) return n([]);
                    n(e)
                });
                handlePromiseResult(r, e => n(Array.isArray(e) ? e : []), () => n([]))
            } catch (a) {
                n([])
            }
        },
        create(e, t = () => {}) {
            let n = onceCallback(t);
            try {
                let r = api.tabs.create(e || {}, e => {
                    let t = getRuntimeLastErrorSafe();
                    if (t || !e) return t && !isTabGoneRuntimeError(t) && bgReportDiagnostic("background_tab_create_failed", "safeTabs.create", t.message || String(t)), n(null);
                    n(e)
                });
                handlePromiseResult(r, e => n(e || null), () => n(null))
            } catch (a) {
                n(null)
            }
        },
        update(e, t, n = () => {}) {
            let r = onceCallback(n);
            if ("number" != typeof e || e < 0) return r(null);
            try {
                let a = api.tabs.update(e, t || {}, t => {
                    let n = getRuntimeLastErrorSafe();
                    if (n || !t) return isTabGoneRuntimeError(n) ? clearTabScopedState(e) : n && bgReportDiagnostic("background_tab_update_failed", "safeTabs.update", n.message || String(n), {
                        message_tab_id: e
                    }), r(null);
                    r(t)
                });
                handlePromiseResult(a, e => r(e || null), t => {
                    isTabGoneRuntimeError(t) && clearTabScopedState(e), r(null)
                })
            } catch (o) {
                isTabGoneRuntimeError(o) && clearTabScopedState(e), r(null)
            }
        },
        reload(e, t = {}, n = () => {}) {
            "function" == typeof t && (n = t, t = {});
            let r = onceCallback(n);
            if ("number" != typeof e || e < 0) return r(!1);
            try {
                let a = api.tabs.reload(e, t || {}, () => {
                    let t = getRuntimeLastErrorSafe();
                    if (t) return isTabGoneRuntimeError(t) && clearTabScopedState(e), r(!1);
                    r(!0)
                });
                handlePromiseResult(a, () => r(!0), t => {
                    isTabGoneRuntimeError(t) && clearTabScopedState(e), r(!1)
                })
            } catch (o) {
                isTabGoneRuntimeError(o) && clearTabScopedState(e), r(!1)
            }
        },
        remove(e, t = () => {}) {
            let n = onceCallback(t);
            if ("number" != typeof e || e < 0) return n(null);
            try {
                let r = api.tabs.remove(e, () => {
                    let t = getRuntimeLastErrorSafe();
                    clearTabScopedState(e), n(t || null)
                });
                handlePromiseResult(r, () => {
                    clearTabScopedState(e), n(null)
                }, t => {
                    clearTabScopedState(e), n(t || null)
                })
            } catch (a) {
                clearTabScopedState(e), n(a)
            }
        },
        sendMessage(e, t, n = null) {
            let r = "function" == typeof n,
                a = onceCallback(r ? n : () => {});
            if ("number" != typeof e || e < 0) return a(null);
            let o = n => {
                try {
                    if (!n) return;
                    if (isTabGoneRuntimeError(n)) {
                        clearTabScopedState(e);
                        return
                    }
                    if (isBenignMessageRuntimeError(n)) return;
                    e === activeTabId && bgReportDiagnostic("background_message_failed", "safeTabs.sendMessage", n.message || String(n), {
                        message_tab_id: e,
                        message_name: t && t.msg ? t.msg : ""
                    })
                } catch (r) {}
            };
            try {
                let i;
                if (r) try {
                    i = api.tabs.sendMessage(e, t, e => {
                        let t = getRuntimeLastErrorSafe();
                        o(t), a(e || null)
                    })
                } catch (c) {
                    o(c), a(null);
                    return
                } else i = api.tabs.sendMessage(e, t);
                handlePromiseResult(i, e => {
                    r && a(e || null)
                }, e => {
                    o(e), r && a(null)
                })
            } catch (s) {
                o(s), a(null)
            }
        }
    },
    safeWindows = {
        create(e, t = () => {}) {
            let n = onceCallback(t);
            try {
                let r = api.windows.create(e || {}, e => {
                    let t = getRuntimeLastErrorSafe();
                    if (t || !e) return n(null);
                    n(e)
                });
                handlePromiseResult(r, e => n(e || null), () => n(null))
            } catch (a) {
                n(null)
            }
        },
        update(e, t, n = () => {}) {
            let r = onceCallback(n);
            if ("number" != typeof e || e < 0) return r(null);
            try {
                let a = api.windows.update(e, t || {}, e => {
                    let t = getRuntimeLastErrorSafe();
                    if (t || !e) return r(null);
                    r(e)
                });
                handlePromiseResult(a, e => r(e || null), () => r(null))
            } catch (o) {
                r(null)
            }
        },
        get(e, t = () => {}) {
            let n = onceCallback(t);
            if ("number" != typeof e || e < 0) return n(null);
            try {
                let r = api.windows.get(e, e => {
                    let t = getRuntimeLastErrorSafe();
                    if (t || !e) return n(null);
                    n(e)
                });
                handlePromiseResult(r, e => n(e || null), () => n(null))
            } catch (a) {
                n(null)
            }
        }
    },
    safeStorageSync = {
        get(e, t = () => {}) {
            let n = onceCallback(t);
            try {
                let r = api.storage.sync.get(e, e => {
                    let t = getRuntimeLastErrorSafe();
                    n(t || !e ? {} : e)
                });
                handlePromiseResult(r, e => n(e || {}), () => n({}))
            } catch (a) {
                n({})
            }
        },
        set(e, t = () => {}) {
            let n = onceCallback(t);
            try {
                let r = api.storage.sync.set(e || {}, () => {
                    let e = getRuntimeLastErrorSafe();
                    n(!e)
                });
                handlePromiseResult(r, () => n(!0), () => n(!1))
            } catch (a) {
                n(!1)
            }
        }
    };

function safeActionCall(e, t) {
    try {
        if (!actionAPI || "function" != typeof actionAPI[e]) return;
        let n;
        try {
            n = actionAPI[e](t, () => {
                getRuntimeLastErrorSafe()
            })
        } catch (r) {
            n = actionAPI[e](t)
        }
        handlePromiseResult(n, () => {}, () => {})
    } catch (a) {}
}
let MainUrl = "https://www.viewgrip.net",
    parsedUrl = new URL(MainUrl),
    bgDiagnosticSent = Object.create(null);

function bgDiagnosticText(e, t = 1500) {
    try {
        return (e = (e = null == e ? "" : String(e)).replace(/[\u0000\r\n]+/g, " ").trim()).length > t ? e.slice(0, t) : e
    } catch (n) {
        return ""
    }
}

function bgDiagnosticShouldSend(e, t, n) {
    try {
        let r = Date.now(),
            a = [e, t, n].map(e => bgDiagnosticText(e, 240)).join("|"),
            o = bgDiagnosticSent[a] || 0;
        if (r - o < 6e5) return !1;
        return bgDiagnosticSent[a] = r, !0
    } catch (i) {
        return !1
    }
}

function bgReportDiagnostic(e = "background_issue", t = "background", n = "", r = {}) {
    try {
        if (!r || !0 !== r.developer_action_required || !isRunning && !activeTabId || !bgDiagnosticShouldSend(e, t, n)) return !1;
        let a = Object.assign({
                type: e,
                phase: t,
                name: "Background worker control issue",
                message: bgDiagnosticText(n, 1800),
                file: "background.js",
                line: "",
                column: "",
                selector: "",
                stack: "",
                url: "background",
                host: "background"
            }, r || {}),
            o = new URLSearchParams;
        return Object.keys(a).forEach(e => o.append(e, bgDiagnosticText(a[e], "stack" === e ? 3e3 : 1800))), fetch(MainUrl.replace(/\/$/, "") + "/api/worker/diagnostic", {
            method: "POST",
            mode: "no-cors",
            credentials: "omit",
            keepalive: !0,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8"
            },
            body: o.toString()
        }).catch(() => {}), !0
    } catch (i) {
        return !1
    }
}
let loadingTimers = {},
    fallbackTimers = {},
    renderingSent = {},
    isRunning = !1,
    activeTabId = null,
    activeWindowId = null,
    clickLock = !1,
    lastKnownUrl = null,
    illegalWorkerTabId = null,
    workerShuttingDown = !1,
    networkErrorListener = null,
    connectionRetrySession = {},
    connectionRetryLock = {},
    connectionReloadLoops = {},
    connectionRetryMessageSent = {},
    ajaxErrorCounter = {};

function clearTabScopedState(e) {
    if ("number" == typeof e) try {
        loadingTimers[e] && (clearTimeout(loadingTimers[e]), delete loadingTimers[e]), fallbackTimers[e] && (clearTimeout(fallbackTimers[e]), delete fallbackTimers[e]), connectionReloadLoops[e] && (clearTimeout(connectionReloadLoops[e]), delete connectionReloadLoops[e]), delete renderingSent[e], delete connectionRetrySession[e], delete connectionRetryLock[e], delete connectionRetryMessageSent[e], delete ajaxErrorCounter[e]
    } catch (t) {}
}

function normalizeHostname(e) {
    return String(e || "").replace(/^www\./, "").toLowerCase()
}

function getParsedUrlSafe(e) {
    try {
        if (!e || "string" != typeof e) return null;
        return new URL(e)
    } catch (t) {
        return null
    }
}

function isViewGripUrl(e) {
    let t = getParsedUrlSafe(e);
    return !!t && normalizeHostname(t.hostname) === normalizeHostname(parsedUrl.hostname)
}

function isYouTubePlaybackUrl(e) {
    let t = getParsedUrlSafe(e);
    if (!t) return !1;
    let n = normalizeHostname(t.hostname);
    return "youtube.com" === n && ("/watch" === t.pathname || t.pathname.startsWith("/shorts/"))
}

function isMainFrameNavigationError(e) {
    return !!e && (!e.type || "main_frame" === e.type) && 0 === e.frameId && (-1 === e.parentFrameId || void 0 === e.parentFrameId)
}

function clearTimerBucket(e) {
    e && "object" == typeof e && Object.keys(e).forEach(t => {
        try {
            clearTimeout(e[t]), clearInterval(e[t])
        } catch (n) {}
        delete e[t]
    })
}

function clearRuntimeBuckets() {
    clearTimerBucket(loadingTimers), clearTimerBucket(fallbackTimers), clearTimerBucket(connectionReloadLoops), renderingSent = {}, connectionRetrySession = {}, connectionRetryLock = {}, connectionRetryMessageSent = {}, ajaxErrorCounter = {}
}
let backgroundRecoveryLock = !1;

function recoverBackground(e = "background_error", t = null) {
    try {
        try {
            console.error("ViewGrip background recovery:", e, t)
        } catch (n) {}
        if (backgroundRecoveryLock) return;
        if (backgroundRecoveryLock = !0, setTimeout(() => {
                backgroundRecoveryLock = !1
            }, 4e3), clearRuntimeBuckets(), isRunning && activeTabId) {
            safeTabs.get(activeTabId, e => {
                if (!e) {
                    cleanupWorkerState();
                    return
                }
                attachNetworkErrorListener(), safeTabs.update(activeTabId, {
                    url: MainUrl + "/worker/start"
                })
            });
            return
        }
        validateWorkerState(() => {})
    } catch (r) {
        try {
            cleanupWorkerState()
        } catch (a) {}
    }
}

function safeBackgroundHandler(e, t) {
    return (...n) => {
        try {
            return t(...n)
        } catch (r) {
            recoverBackground(e, r);
            try {
                let a = n[2];
                "function" == typeof a && a({
                    ok: !1,
                    reason: "background_recovered"
                })
            } catch (o) {}
            return !1
        }
    }
}
try {
    "undefined" != typeof self && self.addEventListener && (self.addEventListener("error", e => {
        recoverBackground("service_worker_error", e && (e.error || e.message))
    }), self.addEventListener("unhandledrejection", e => {
        recoverBackground("service_worker_unhandledrejection", e && e.reason)
    }))
} catch (e) {}

function isSafeNavigationUrl(e) {
    if (!e || "string" != typeof e) return !1;
    try {
        let t = new URL(e);
        return "http:" === t.protocol || "https:" === t.protocol
    } catch (n) {
        return !1
    }
}
try {
    api.runtime.setUninstallURL(MainUrl)
} catch (t) {}

function updateIcon() {
    actionAPI && (isRunning ? (safeActionCall("setIcon", {
        path: "/assets/img/pause.png"
    }), safeActionCall("setTitle", {
        title: "Stop Worker"
    })) : (safeActionCall("setIcon", {
        path: "/assets/img/start.png"
    }), safeActionCall("setTitle", {
        title: "Start Worker"
    })))
}

function setRunningState(e) {
    isRunning = e, saveState(), updateIcon()
}

function saveState() {
    safeStorageSync.set({
        isRunning,
        tabId: activeTabId,
        windowId: activeWindowId
    })
}

function loadState(e) {
    safeStorageSync.get(["isRunning", "tabId", "windowId"], t => {
        isRunning = t.isRunning || !1, activeTabId = t.tabId || null, activeWindowId = t.windowId || null, validateWorkerState(() => {
            updateIcon(), e && e()
        })
    })
}

function handleConnectionRetryMessage(e) {
    if (!e || connectionRetryLock[e]) return;
    connectionRetryLock[e] = !0;
    let t = null,
        n = !1,
        r = () => {
            n || (n = !0, delete connectionRetryLock[e], bgReportDiagnostic("background_content_unresponsive", "handleConnectionRetryMessage", "Content script did not respond before retry timeout; starting reload loop.", {
                message_tab_id: e
            }), startConnectionReloadLoop(e))
        };
    try {
        if (t = setTimeout(r, 800), connectionRetryMessageSent[e]) return;
        connectionRetryMessageSent[e] = !0, safeTabs.sendMessage(e, {
            msg: "connectionRetry"
        }, r => {
            if (!n && r && !0 === r.ok) {
                n = !0, clearTimeout(t), delete connectionRetryLock[e];
                return
            }
        })
    } catch {
        clearTimeout(t), delete connectionRetryLock[e], startConnectionReloadLoop(e)
    }
}

function startConnectionReloadLoop(e) {
    if (!e || connectionReloadLoops[e]) return;
    let t = () => {
        connectionReloadLoops[e] = setTimeout(() => {
            safeTabs.get(e, n => {
                if (!n || e !== activeTabId) {
                    clearTimeout(connectionReloadLoops[e]), delete connectionReloadLoops[e], n || e !== activeTabId || cleanupWorkerState();
                    return
                }
                safeTabs.update(e, {
                    url: MainUrl + "/worker/start"
                }), t()
            })
        }, 5e3)
    };
    t()
}

function attachNetworkErrorListener() {
    !networkErrorListener && (networkErrorListener = e => {
        if (!e || !activeTabId || e.tabId !== activeTabId || "number" != typeof e.tabId || e.tabId < 0 || !e.error || !e.url) return;
        let t = e.tabId,
            n = e.type || "",
            r = isViewGripUrl(e.url),
            a = isMainFrameNavigationError(e);
        if ((a || r) && ["NS_ERROR_CONNECTION_REFUSED", "NS_ERROR_UNKNOWN_HOST", "NS_ERROR_NET_TIMEOUT", "ERR_INTERNET_DISCONNECTED", "ERR_CONNECTION_RESET", "ERR_CONNECTION_TIMED_OUT", "ERR_NAME_NOT_RESOLVED"].some(t => e.error.includes(t))) {
            if (a) {
                connectionRetrySession[t] && delete connectionRetrySession[t], connectionRetrySession[t] = !0, handleConnectionRetryMessage(t);
                return
            }
            if (n && !["image", "stylesheet", "font", "script", "media", "ping"].includes(n) && ("xmlhttprequest" === n || "fetch" === n)) {
                if (!r || (ajaxErrorCounter[t] || (ajaxErrorCounter[t] = 0), ajaxErrorCounter[t]++, ajaxErrorCounter[t] < 3)) return;
                ajaxErrorCounter[t] = 0, connectionRetrySession[t] || (connectionRetrySession[t] = !0, handleConnectionRetryMessage(t))
            }
        }
    }, api.webRequest && api.webRequest.onErrorOccurred && api.webRequest.onErrorOccurred.addListener(networkErrorListener, {
        urls: ["<all_urls>"]
    }), api.webNavigation && api.webNavigation.onErrorOccurred && api.webNavigation.onErrorOccurred.addListener(networkErrorListener, {
        url: [{
            schemes: ["http", "https"]
        }]
    }))
}

function detachNetworkErrorListener() {
    if (networkErrorListener) {
        try {
            api.webRequest && api.webRequest.onErrorOccurred && api.webRequest.onErrorOccurred.removeListener(networkErrorListener), api.webNavigation && api.webNavigation.onErrorOccurred && api.webNavigation.onErrorOccurred.removeListener(networkErrorListener)
        } catch {}
        networkErrorListener = null
    }
}

function refreshPages() {
    isRunning || safeTabs.query({
        url: MainUrl + "/*"
    }, e => {
        if (!e || 0 === e.length) {
            safeTabs.create({
                url: MainUrl + "/workers"
            });
            return
        }
        let t = !1;
        e.forEach(e => {
            if (e && e.id && e.url && !t) try {
                let n = new URL(e.url);
                if ("/workers" === n.pathname) {
                    safeTabs.update(e.id, {
                        url: MainUrl + "/workers"
                    }), t = !0;
                    return
                }
                n.hostname === parsedUrl.hostname && (safeTabs.update(e.id, {
                    url: MainUrl + "/workers"
                }), t = !0)
            } catch {}
        })
    })
}

function CloseYoTubeTab(e = null) {
    try {
        safeTabs.query({}, t => {
            Array.isArray(t) && t.forEach(t => {
                t && t.id && t.url && t.id !== activeTabId && t.id !== e && t.url.includes("youtube.com") && safeTabs.remove(t.id)
            })
        })
    } catch {}
}

function enforceSingleTabById(e, t) {
    isRunning && activeWindowId && t === activeWindowId && e !== activeTabId && safeTabs.remove(e)
}

function startWorker(e) {
    isSafeNavigationUrl(e) || (e = MainUrl + "/worker/start"), isRunning || clickLock || (clickLock = !0, attachNetworkErrorListener(), safeWindows.create({
        url: e,
        focused: !0
    }, e => {
        if (!e || !e.tabs || !e.tabs[0]) {
            bgReportDiagnostic("background_worker_window_failed", "startWorker", "Background failed to create an isolated worker window/tab."), clickLock = !1, cleanupWorkerState();
            return
        }
        activeWindowId = e.id, activeTabId = e.tabs[0].id, setRunningState(!0), safeWindows.update(activeWindowId, {
            state: "maximized"
        }), CloseYoTubeTab(activeTabId), safeTabs.query({
            windowId: activeWindowId
        }, e => {
            Array.isArray(e) && e.forEach(e => {
                e.id !== activeTabId && safeTabs.remove(e.id)
            })
        }), setTimeout(() => clickLock = !1, 500)
    }))
}

function stopWorker() {
    if (!isRunning || clickLock) return;
    clickLock = !0;
    let e = activeTabId;
    detachNetworkErrorListener();
    let t = () => {
        activeTabId = null, activeWindowId = null, setRunningState(!1), setTimeout(() => {
            clickLock = !1
        }, 500)
    };
    e ? safeTabs.remove(e, () => {
        t()
    }) : t()
}

function handleTabUpdated(e, t, n) {
    if (illegalWorkerTabId && e === illegalWorkerTabId) {
        forceRemoveTab(e);
        return
    }
    if (!isRunning || !n || n.windowId !== activeWindowId) return;
    if (e !== activeTabId) {
        if ("complete" !== t.status || isAllowedHost(n.url)) return;
        safeTabs.remove(e);
        return
    }
    let r = n.url || "";
    if ("loading" === t.status && lastKnownUrl && r === lastKnownUrl) return;
    "loading" !== t.status || renderingSent[e] || (renderingSent[e] = !0, safeTabs.sendMessage(e, {
        msg: "Rendering"
    })), ("loading" === t.status || "complete" === t.status) && r && r !== lastKnownUrl && (lastKnownUrl = r), ("loading" === t.status || "complete" === t.status) && (loadingTimers[e] && (clearTimeout(loadingTimers[e]), delete loadingTimers[e]), fallbackTimers[e] && (clearTimeout(fallbackTimers[e]), delete fallbackTimers[e]));
    let a;
    try {
        a = new URL(r)
    } catch {
        r && !String(r).startsWith("about:") && safeTabs.sendMessage(e, {
            msg: "slowConnection"
        });
        return
    }
    "loading" !== t.status || isYouTubePlaybackUrl(r) || (loadingTimers[e] = setTimeout(() => {
        safeTabs.get(e, t => {
            t && "loading" === t.status && t.url === lastKnownUrl && !isYouTubePlaybackUrl(t.url) && safeTabs.reload(e)
        })
    }, 3e4)), "complete" === t.status && (("undefined" == typeof navigator || !1 !== navigator.onLine) && (connectionReloadLoops[e] && (clearTimeout(connectionReloadLoops[e]), delete connectionReloadLoops[e]), ajaxErrorCounter[e] && delete ajaxErrorCounter[e]), renderingSent[e] && delete renderingSent[e], connectionRetryLock[e] && delete connectionRetryLock[e], connectionRetrySession[e] && delete connectionRetrySession[e], connectionRetryMessageSent[e] && delete connectionRetryMessageSent[e], CloseYoTubeTab(e), a.hostname === parsedUrl.hostname ? safeTabs.sendMessage(e, {
        msg: "StartGetData"
    }) : safeTabs.sendMessage(e, {
        msg: "StartWorker"
    }))
}

function handleTabRemoved(e) {
    clearTabScopedState(e), isRunning && e === activeTabId && cleanupWorkerState()
}

function validateWorkerState(e) {
    if (!isRunning || !activeTabId || !activeWindowId) return cleanupWorkerState(), e && e(!1);
    safeTabs.get(activeTabId, t => {
        if (!t || t.windowId !== activeWindowId) return cleanupWorkerState(), e && e(!1);
        safeWindows.get(activeWindowId, t => {
            if (!t) return cleanupWorkerState(), e && e(!1);
            e && e(!0)
        })
    })
}

function changeTabURL(e) {
    isSafeNavigationUrl(e) || (e = MainUrl + "/worker/start"), safeTabs.update(activeTabId, {
        url: e
    }), CloseYoTubeTab(activeTabId)
}

function forceRemoveTab(e) {
    "number" != typeof e || e < 0 || setTimeout(() => {
        safeTabs.get(e, t => {
            if (!t) {
                e === activeTabId && cleanupWorkerState();
                return
            }
            safeTabs.remove(e, () => {
                e === activeTabId && cleanupWorkerState()
            })
        })
    }, 80)
}

function isAllowedHost(e) {
    if (!e) return !1;
    try {
        let t = new URL(e);
        if (t.hostname === parsedUrl.hostname) return !0
    } catch (n) {}
    return !1
}

function cleanupWorkerState() {
    illegalWorkerTabId = null, workerShuttingDown = !1, detachNetworkErrorListener(), clearRuntimeBuckets(), isRunning = !1, activeTabId = null, activeWindowId = null, lastKnownUrl = null, clickLock = !1, saveState(), updateIcon()
}
api.runtime.onInstalled.addListener(safeBackgroundHandler("onInstalled", () => {
    loadState(() => {
        refreshPages()
    })
})), loadState(), api.runtime.onStartup.addListener(safeBackgroundHandler("onStartup", () => {
    loadState(() => {
        updateIcon()
    })
})), api.runtime.onMessage.addListener(safeBackgroundHandler("runtime.onMessage", (e, t, n) => {
    try {
        if (!e || !e.cmd) return n({
            ok: !1,
            reason: "invalid_request"
        }), !1;
        switch (e.cmd) {
            case "openTab":
                if (isRunning) return n({
                    ok: !1,
                    reason: "already_running"
                }), !1;
                return startWorker(e.url), n({
                    ok: !0
                }), !1;
            case "updateTab":
                return changeTabURL(e.url), n({
                    ok: !0
                }), !1;
            case "closeTab":
                return stopWorker(), n({
                    ok: !0
                }), !1;
            case "isCurrentTabMuted":
                try {
                    if (!t || !t.tab || void 0 === t.tab.id) return n(!1), !1;
                    safeTabs.get(t.tab.id, e => {
                        try {
                            if (!e) {
                                n(!1);
                                return
                            }
                            n(Boolean(e.mutedInfo && e.mutedInfo.muted))
                        } catch (t) {
                            n(!1)
                        }
                    })
                } catch (r) {
                    return n(!1), !1
                }
                return !0;
            default:
                return n({
                    ok: !1,
                    reason: "unknown_command"
                }), !1
        }
    } catch (a) {
        return n({
            ok: !1,
            error: a && a.toString ? a.toString() : "unknown"
        }), recoverBackground("runtime.onMessage.catch", a), !1
    }
})), actionAPI && actionAPI.onClicked && actionAPI.onClicked.addListener(safeBackgroundHandler("action.onClicked", () => {
    if (!clickLock) {
        if (isRunning) {
            stopWorker();
            return
        }
        validateWorkerState(e => {
            e || startWorker(MainUrl + "/worker/start")
        })
    }
})), api.tabs.onUpdated.addListener(safeBackgroundHandler("tabs.onUpdated", handleTabUpdated)), api.tabs.onRemoved.addListener(safeBackgroundHandler("tabs.onRemoved", handleTabRemoved)), api.tabs.onCreated.addListener(safeBackgroundHandler("tabs.onCreated", e => {
    if (illegalWorkerTabId && e.id === illegalWorkerTabId) {
        forceRemoveTab(e.id);
        return
    }
    if (isRunning && activeWindowId) {
        if (isAllowedHost(e.url) || !e.url || "about:blank" === e.url) return;
        enforceSingleTabById(e.id, e.windowId)
    }
})), api.tabs.onAttached && api.tabs.onAttached.addListener(safeBackgroundHandler("tabs.onAttached", e => {
    if (illegalWorkerTabId && e.tabId === illegalWorkerTabId) {
        forceRemoveTab(e.tabId);
        return
    }
    enforceSingleTabById(e.tabId, e.newWindowId)
})), api.windows.onFocusChanged && api.windows.onFocusChanged.addListener(safeBackgroundHandler("windows.onFocusChanged", e => {
    isRunning && e === activeWindowId && safeTabs.query({
        windowId: activeWindowId
    }, e => {
        try {
            if (!Array.isArray(e)) return;
            e.forEach(e => {
                e.id !== activeTabId && safeTabs.remove(e.id)
            })
        } catch (t) {
            recoverBackground("windows.onFocusChanged.query", t)
        }
    })
})), api.tabs.onDetached && api.tabs.onDetached.addListener(safeBackgroundHandler("tabs.onDetached", (e, t) => {
    e === activeTabId && t.oldWindowId === activeWindowId && (illegalWorkerTabId = e, workerShuttingDown = !0)
}));