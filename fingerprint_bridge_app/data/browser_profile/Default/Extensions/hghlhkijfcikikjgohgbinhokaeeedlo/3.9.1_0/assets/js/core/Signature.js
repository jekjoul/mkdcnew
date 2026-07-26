let tx_cached_fingerprint = null;
async function tx_sha256(t) {
    let e = new TextEncoder().encode(t),
        n = await crypto.subtle.digest("SHA-256", e),
        r = Array.from(new Uint8Array(n));
    return r.map(t => t.toString(16).padStart(2, "0")).join("")
}

function tx_collectNavigator() {
    let t = "unknown";
    return navigator.userAgentData && navigator.userAgentData.platform ? t = navigator.userAgentData.platform : navigator.platform && (t = navigator.platform), [navigator.userAgent || "", navigator.language || "", t, navigator.hardwareConcurrency || 0, navigator.deviceMemory || 0, navigator.maxTouchPoints || 0].join("|")
}

function tx_collectScreen() {
    return [screen.width, screen.height, screen.colorDepth, screen.pixelDepth].join("|")
}

function tx_canvasFingerprint() {
    try {
        let t = document.createElement("canvas"),
            e = t.getContext("2d");
        return e.textBaseline = "top", e.font = "14px Arial", e.fillText("traffic-exchange", 2, 2), t.toDataURL()
    } catch (n) {
        return "canvas_error"
    }
}

function tx_webglFingerprint() {
    try {
        let t = document.createElement("canvas"),
            e = t.getContext("webgl");
        if (!e) return "webgl_disabled";
        let n = e.getExtension("WEBGL_debug_renderer_info");
        if (!n) return "webgl_limited";
        return e.getParameter(n.UNMASKED_RENDERER_WEBGL)
    } catch (r) {
        return "webgl_error"
    }
}

function tx_detectAutomation() {
    let t = [];
    return t.push(navigator.webdriver ? "1" : "0"), t.push(window.callPhantom ? "1" : "0"), t.push(window._phantom ? "1" : "0"), t.push(window.__nightmare ? "1" : "0"), t.join("|")
}
async function tx_generateFingerprint() {
    if (tx_cached_fingerprint) return tx_cached_fingerprint;
    let t = [tx_collectNavigator(), tx_collectScreen(), tx_canvasFingerprint(), tx_webglFingerprint(), tx_detectAutomation(), Intl.DateTimeFormat().resolvedOptions().timeZone],
        e = t.join("||");
    return tx_cached_fingerprint = await tx_sha256(e)
}
async function tx_generateSignature(t, e, n) {
    let r = await tx_generateFingerprint(),
        a = await tx_sha256(t + e + r + n);
    return {
        fingerprint: r,
        pow: a
    }
}