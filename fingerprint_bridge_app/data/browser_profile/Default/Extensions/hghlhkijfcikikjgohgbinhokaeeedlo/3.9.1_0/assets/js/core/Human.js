function vgHumanTimeoutPromise(t, e, i = !1) {
    return new Promise(n => {
        let s = !1,
            o = setTimeout(() => {
                s || (s = !0, n(i))
            }, Math.max(100, Number(e) || 1e3));
        Promise.resolve(t).then(t => {
            s || (s = !0, clearTimeout(o), n(t))
        }).catch(() => {
            s || (s = !0, clearTimeout(o), n(i))
        })
    })
}

function vgHumanNotifySafe(...t) {
    let e = !t.length || "boolean" != typeof t[t.length - 1] || t.pop(),
        i = t.length && "number" == typeof t[t.length - 1] ? t.pop() : 3e3;
    try {
        if ("function" != typeof showWorkerNotification) return Promise.resolve(e);
        return vgHumanTimeoutPromise(showWorkerNotification(...t), i, e)
    } catch (n) {
        return Promise.resolve(e)
    }
}

function vgHumanOnce(t) {
    let e = !1;
    return function(...i) {
        if (!e && (e = !0, "function" == typeof t)) try {
            t(...i)
        } catch (n) {}
    }
}

function vgHumanFinite(t, e = 0) {
    let i = Number(t);
    return Number.isFinite(i) ? i : e
}
const ClickController = {
    perElementCooldown: 5e3,
    globalCooldown: 800,
    taskTimeout: 7e3,
    lastElementClick: new Map,
    lastGlobalClick: 0,
    queues: new Map,
    abortControllers: new Map,
    cleanupInterval: null,
    initCleanup() {
        this.cleanupInterval || (this.cleanupInterval = setInterval(() => {
            let t = Date.now();
            for (let [e, i] of this.lastElementClick.entries()) t - i > 15e3 && this.lastElementClick.delete(e);
            for (let [n, s] of this.queues.entries()) s.length || this.queues.delete(n);
            for (let [o, a] of this.abortControllers.entries()) a.signal.aborted && this.abortControllers.delete(o)
        }, 5e3))
    },
    enqueue(t, e) {
        this.queues.has(t) || this.queues.set(t, []);
        let i = this.queues.get(t);
        return new Promise((n, s) => {
            i.push({
                taskFn: e,
                resolve: n,
                reject: s
            }), 1 === i.length && this.runQueue(t)
        })
    },
    async runQueue(t) {
        let e = this.queues.get(t);
        if (!e || 0 === e.length) return;
        let {
            taskFn: i,
            resolve: n,
            reject: s
        } = e[0];
        try {
            let o = await vgHumanTimeoutPromise(Promise.resolve().then(i), this.taskTimeout, !1);
            n(o)
        } catch (a) {
            s(a)
        } finally {
            e.shift(), e.length > 0 && setTimeout(() => this.runQueue(t), 0)
        }
    }
};
async function SimulateClick(t) {
    if (!forceStop || !t || !t.isConnected) return !1;
    let e = t.id ? `id:${t.id}` : `${t.tagName||"EL"}:${String(t.className||"")}:${(t.textContent||"").slice(0,120)}`,
        i = Date.now();
    if (i - ClickController.lastGlobalClick < ClickController.globalCooldown) return !1;
    if (ClickController.lastGlobalClick = i, ClickController.lastElementClick.has(e)) {
        let n = ClickController.lastElementClick.get(e);
        if (i - n < ClickController.perElementCooldown) return !1
    }
    if (ClickController.lastElementClick.set(e, i), ClickController.abortControllers.has(e)) try {
        ClickController.abortControllers.get(e).abort()
    } catch (s) {}
    let o = new AbortController;
    ClickController.abortControllers.set(e, o), VG_ALLOW_USER_CLICK = !0;
    let a = !1;
    try {
        a = await ClickController.enqueue(e, async () => {
            if (o.signal.aborted || !forceStop || !t || !t.isConnected || !document.body) return !1;
            let e = document.body.style.pointerEvents,
                i = {
                    display: t.style.display,
                    visibility: t.style.visibility,
                    pointerEvents: t.style.pointerEvents,
                    opacity: t.style.opacity
                };
            try {
                t.style.display = "block", t.style.visibility = "visible", t.style.pointerEvents = "auto", t.style.opacity = "1";
                let n = t.getBoundingClientRect();
                if (!n || !Number.isFinite(n.left) || !Number.isFinite(n.top)) return !1;
                let s = n.left + n.width / 2,
                    a = n.top + n.height / 2;
                if (!Number.isFinite(s) || !Number.isFinite(a)) return !1;
                let r = async () => {
                    let t = Math.random() * Math.max(1, window.innerWidth || 1),
                        e = Math.random() * Math.max(1, window.innerHeight || 1);
                    for (let i = 0; i <= 25; i++) {
                        if (o.signal.aborted || !forceStop) return !1;
                        let n = t + (s - t) * (i / 25),
                            r = e + (a - e) * (i / 25);
                        try {
                            document.dispatchEvent(new MouseEvent("mousemove", {
                                bubbles: !0,
                                cancelable: !1,
                                clientX: n,
                                clientY: r
                            }))
                        } catch (l) {}
                        await new Promise(t => setTimeout(t, 5 + 8 * Math.random()))
                    }
                    return !0
                }, l = await r();
                if (!l || o.signal.aborted || !forceStop) return !1;
                (() => {
                    try {
                        if (!document.body) return;
                        let t = document.createElement("div");
                        t.style.cssText = `
                    position: fixed;
                    width: 45px;
                    height: 45px;
                    border-radius: 50%;
                    background: rgba(0, 120, 255, 0.45);
                    top: ${a}px;
                    left: ${s}px;
                    transform: translate(-50%, -50%) scale(0);
                    animation: rippleEffect 400ms ease-out forwards;
                    pointer-events: none;
                    z-index: 999999;
                `, document.body.appendChild(t);
                        let e = document.createElement("style");
                        e.textContent = `
                    @keyframes rippleEffect {
                        100% { transform: translate(-50%, -50%) scale(2); opacity: 0; }
                    }
                `, document.head && document.head.appendChild(e), setTimeout(() => {
                            try {
                                t.remove()
                            } catch (i) {}
                            try {
                                e.remove()
                            } catch (n) {}
                        }, 450)
                    } catch (i) {}
                })();
                try {
                    let h = {
                        bubbles: !0,
                        cancelable: !0,
                        clientX: s,
                        clientY: a,
                        buttons: 1
                    };
                    o.signal.aborted || t.dispatchEvent(new MouseEvent("mouseover", h)), o.signal.aborted || t.dispatchEvent(new MouseEvent("mouseenter", h)), o.signal.aborted || t.dispatchEvent(new MouseEvent("mousedown", h)), o.signal.aborted || t.dispatchEvent(new MouseEvent("mouseup", h)), o.signal.aborted || t.dispatchEvent(new MouseEvent("click", h))
                } catch (c) {
                    return !1
                }
                return !o.signal.aborted
            } finally {
                try {
                    t.style.display = i.display
                } catch ($) {}
                try {
                    t.style.visibility = i.visibility
                } catch (u) {}
                try {
                    t.style.pointerEvents = i.pointerEvents
                } catch (d) {}
                try {
                    t.style.opacity = i.opacity
                } catch (m) {}
                try {
                    document.body.style.pointerEvents = e
                } catch (y) {}
                try {
                    removeVideoHighlight()
                } catch (p) {}
            }
        })
    } catch (r) {
        a = !1
    } finally {
        ClickController.abortControllers.get(e) === o && ClickController.abortControllers.delete(e), VG_ALLOW_USER_CLICK = !1
    }
    return !!a
}

function SimulateTyping(t, e, i, n) {
    let s = vgHumanOnce(n);
    if (!t || "string" != typeof e || !forceStop) {
        s(!1);
        return
    }
    let o = t,
        a = e.length,
        r = 0,
        l = Math.max(20, vgHumanFinite(i, 80)),
        h = Math.max(5e3, a * l * 3 + 3e3),
        c = Date.now(),
        $ = (t = !0) => {
            try {
                clearTimeout(TimeOutTyping)
            } catch (e) {}
            s(t)
        },
        u = t => {
            let e = document.getElementById("typingPercentage");
            e && (e.textContent = t + "%")
        },
        d = () => {
            try {
                o.focus()
            } catch (t) {}
            try {
                if ("number" == typeof o.selectionStart) {
                    let e = (o.value || "").length;
                    o.selectionStart = e, o.selectionEnd = e;
                    return
                }
            } catch (i) {}
            try {
                let n = window.getSelection && window.getSelection();
                if (!n) return;
                let s = document.createRange();
                s.selectNodeContents(o), s.collapse(!1), n.removeAllRanges(), n.addRange(s)
            } catch (a) {}
        },
        m = new MouseEvent("mouseover", {
            bubbles: !0,
            cancelable: !0
        }),
        y = () => {
            try {
                if (!forceStop || !o || !o.isConnected || Date.now() - c > h) return $(!1);
                if (r < a) {
                    r++;
                    let t = e.substring(0, r);
                    void 0 !== o.value ? o.value = t : void 0 !== o.textContent && (o.textContent = t), d();
                    let i = new Event("input", {
                        bubbles: !0,
                        cancelable: !0
                    });
                    try {
                        o.dispatchEvent(i)
                    } catch (n) {}
                    try {
                        o.scrollLeft = o.scrollWidth
                    } catch (s) {}
                    let p = a > 0 ? Math.round(r / a * 100) : 100;
                    u(p), TimeOutTyping = setTimeout(y, Math.max(20, l + (Math.random() * l * .6 - .3 * l)));
                    return
                }
                try {
                    o.dispatchEvent(m)
                } catch (f) {}
                vgHumanNotifySafe(1, "default", "processing", 0, "", 2500, !0).then(() => {
                    $(!0)
                })
            } catch (v) {
                $(!1)
            }
        };
    vgHumanNotifySafe(0, "default", "processing_typing", 0, '<span id="typingPercentage" style="color:#db9104">0%</span>', 2500, !0).then(() => {
        if (!forceStop) return $(!1);
        try {
            o.dispatchEvent(m)
        } catch (t) {}
        try {
            o.focus()
        } catch (e) {}
        TimeOutTyping = setTimeout(y, 400 + 200 * Math.random())
    }).catch(() => {
        $(!1)
    })
}

function removeVideoHighlight() {
    let t = vgQuerySelector('[data-vg-highlight="1"]');
    if (t) try {
        t.style.transition = "all 0.2s ease", t.style.opacity = "0.85", t.style.transform = "scale(1)", t.style.border = "0px solid transparent", t.style.boxShadow = "none", setTimeout(() => {
            try {
                t.removeAttribute("data-vg-highlight"), t.style.opacity = ""
            } catch {}
        }, 150)
    } catch {}
}
ClickController.initCleanup();
class UltimateYouTubeWatcher {
    constructor() {
        this.isActive = !1, this.video = null, this.mouseHistory = [], this.cleanupHandlers = [], this.behavior = null, this.personality = null, this.defineBehaviors(), this.definePersonalities(), this.startAutoVideoMonitor()
    }
    defineBehaviors() {
        this.behaviors = {
            focused: {
                attentionSpan: [1, 1.3],
                moveFrequency: [18, 30],
                moveSpeed: [850, 1400],
                microMovements: [2, 4],
                distractionChance: .05
            },
            casual: {
                attentionSpan: [.8, 1.1],
                moveFrequency: [14, 26],
                moveSpeed: [700, 1200],
                microMovements: [3, 7],
                distractionChance: .2
            },
            distracted: {
                attentionSpan: [.6, .9],
                moveFrequency: [10, 20],
                moveSpeed: [500, 900],
                microMovements: [4, 8],
                distractionChance: .45
            }
        }
    }
    definePersonalities() {
        this.personalities = {
            engaged: {
                idleDeepChance: .35,
                curiosityChance: .15,
                tremorChance: .05,
                attentionShiftChance: .1
            },
            casual: {
                idleDeepChance: .2,
                curiosityChance: .25,
                tremorChance: .15,
                attentionShiftChance: .2
            },
            restless: {
                idleDeepChance: .1,
                curiosityChance: .35,
                tremorChance: .25,
                attentionShiftChance: .4
            }
        }
    }
    async startWatching(t, e) {
        if (!this.isActive && t) {
            this.isActive = !0, this.video = t;
            try {
                let i = Object.keys(this.behaviors),
                    n = Object.keys(this.personalities);
                this.behavior = this.behaviors[i[Math.floor(Math.random() * i.length)]], this.personality = this.personalities[n[Math.floor(Math.random() * n.length)]], e = Math.max(5, Math.round(vgHumanFinite(e, 15) * this.random(...this.behavior.attentionSpan))), this.trackMouse(), await this.naturalEntry();
                let s = Date.now() + 1e3 * e;
                for (; this.isActive && forceStop && Date.now() < s && this.safeVideo();) await this.performHumanCycle(), await this.sleep(1e3 * this.random(...this.behavior.moveFrequency));
                await this.naturalExit()
            } catch (o) {} finally {
                this.stop()
            }
        }
    }
    safeVideo() {
        return this.video && document.body.contains(this.video)
    }
    startAutoVideoMonitor() {
        let t = setInterval(() => {
            if (this.isActive && !this.safeVideo()) {
                let t = vgQuerySelector("video");
                t && (this.video = t)
            }
        }, 500);
        this.cleanupHandlers.push(() => clearInterval(t))
    }
    trackMouse() {
        let t = t => {
            this.mouseHistory.push({
                x: t.clientX,
                y: t.clientY
            }), this.mouseHistory.length > 20 && this.mouseHistory.shift()
        };
        addSafeEventListener(window, "mousemove", t), this.cleanupHandlers.push(() => removeSafeEventListener(window, "mousemove", t))
    }
    lastMouse() {
        return this.mouseHistory.length ? this.mouseHistory[this.mouseHistory.length - 1] : {
            x: window.innerWidth / 2,
            y: window.innerHeight / 2
        }
    }
    async performHumanCycle() {
        if (this.safeVideo()) try {
            await this.possibleIdleState(), await this.possibleCuriosityCheck(), await this.possibleAttentionShift();
            let t = Math.random();
            t < .55 ? await this.focusContent() : t < .8 ? await this.focusProgress() : await this.lookAround(), await this.doMicroMoves(), await this.possibleTinyTremor(), this.behavior && Math.random() < this.behavior.distractionChance && await this.randomDistraction()
        } catch (e) {}
    }
    async possibleIdleState() {
        let t = this.personality;
        if (Math.random() < t.idleDeepChance) {
            await this.sleep(this.random(3e3, 7e3));
            return
        }
        if (.25 > Math.random()) {
            await this.sleep(this.random(1500, 3500));
            return
        }
        .4 > Math.random() && await this.sleep(this.random(600, 1200))
    }
    async possibleCuriosityCheck() {
        if (Math.random() > this.personality.curiosityChance || !this.safeVideo()) return;
        let t = this.video.getBoundingClientRect(),
            e = {
                x: t.right - this.random(40, 80),
                y: t.top + this.random(20, 50)
            };
        await this.moveTo(e.x, e.y), await this.sleep(this.random(500, 1500));
        let i = this.getVideoCenter();
        i && await this.moveTo(i.x, i.y)
    }
    async possibleAttentionShift() {
        if (Math.random() > this.personality.attentionShiftChance || !this.safeVideo()) return;
        let t = {
            x: this.random(50, window.innerWidth - 50),
            y: this.random(20, 120)
        };
        await this.moveTo(t.x, t.y), await this.sleep(this.random(1500, 3e3));
        let e = this.getVideoCenter();
        e && await this.moveTo(e.x, e.y)
    }
    async possibleTinyTremor() {
        if (Math.random() > this.personality.tremorChance) return;
        let t = this.lastMouse();
        for (let e = 0; e < 4; e++) {
            let i = this.random(-2, 2),
                n = this.random(-2, 2);
            this.fireMove(t.x + i, t.y + n), await this.sleep(this.random(40, 80))
        }
    }
    getVideoCenter() {
        if (!this.safeVideo()) return null;
        let t = this.video.getBoundingClientRect();
        return {
            x: t.left + t.width / 2,
            y: t.top + t.height / 2
        }
    }
    getRect() {
        return this.safeVideo() ? this.video.getBoundingClientRect() : null
    }
    async focusContent() {
        let t = this.getRect();
        if (!t) return;
        let e = t.left + t.width * this.random(.35, .65),
            i = t.top + t.height * this.random(.3, .7);
        await this.moveTo(e, i)
    }
    async focusProgress() {
        let t = this.getRect();
        if (!t) return;
        let e = t.left + t.width * this.random(.2, .8),
            i = t.bottom - this.random(8, 16);
        await this.moveTo(e, i)
    }
    async lookAround() {
        let t = this.getRect();
        if (!t) return;
        let e = t.left + this.random(20, t.width - 20),
            i = t.top + this.random(25, t.height - 25);
        await this.moveTo(e, i)
    }
    async doMicroMoves() {
        let t = this.random(...this.behavior.microMovements),
            e = this.lastMouse();
        for (let i = 0; i < t; i++) await this.sleep(this.random(150, 350)), await this.moveTo(e.x + this.random(-6, 6), e.y + this.random(-6, 6))
    }
    async randomDistraction() {
        let t = this.getRect();
        if (!t) return;
        let e = t.right + this.random(30, 120),
            i = this.random(t.top - 40, t.bottom + 40);
        await this.moveTo(e, i), await this.sleep(this.random(2e3, 5e3));
        let n = this.getVideoCenter();
        n && await this.moveTo(n.x, n.y)
    }
    async naturalEntry() {
        let t = this.getVideoCenter();
        if (!t) return;
        let e = [{
                x: .1 * window.innerWidth,
                y: -80
            }, {
                x: .9 * window.innerWidth,
                y: window.innerHeight + 80
            }, {
                x: -80,
                y: .4 * window.innerHeight
            }],
            i = e[Math.floor(Math.random() * e.length)];
        await this.moveHuman(i.x, i.y, t.x, t.y, this.random(1200, 2e3))
    }
    async naturalExit() {
        let t = [{
                x: -80,
                y: .3 * window.innerHeight
            }, {
                x: window.innerWidth + 80,
                y: .7 * window.innerHeight
            }, {
                x: .4 * window.innerWidth,
                y: -80
            }],
            e = t[Math.floor(Math.random() * t.length)],
            i = this.lastMouse();
        await this.moveHuman(i.x, i.y, e.x, e.y, this.random(900, 1500))
    }
    async moveTo(t, e) {
        let i = this.lastMouse();
        await this.moveHuman(i.x, i.y, t, e, this.random(...this.behavior.moveSpeed))
    }
    async moveHuman(t, e, i, n, s) {
        if (!this.isActive) return;
        let o = {
                x: (t + i) / 2 + this.random(-80, 80),
                y: (e + n) / 2 + this.random(-80, 80)
            },
            a = {
                x: (t + i) / 2 + this.random(-80, 80),
                y: (e + n) / 2 + this.random(-80, 80)
            };
        for (let r = 0; r <= 20 && this.isActive; r++) {
            if (!this.safeVideo()) return;
            let l = Math.pow(r / 20, 1.3),
                h = this.bezier(t, e, o.x, o.y, a.x, a.y, i, n, l);
            this.fireMove(h.x, h.y), await this.sleep(s / 20)
        }
    }
    bezier(t, e, i, n, s, o, a, r, l) {
        let h = 1 - l;
        return {
            x: t * h * h * h + 3 * i * l * h * h + 3 * s * l * l * h + a * l * l * l,
            y: e * h * h * h + 3 * n * l * h * h + 3 * o * l * l * h + r * l * l * l
        }
    }
    fireMove(t, e) {
        if (this.safeVideo() && (t = vgHumanFinite(t, window.innerWidth / 2), e = vgHumanFinite(e, window.innerHeight / 2), Number.isFinite(t) && Number.isFinite(e))) try {
            let i = new MouseEvent("mousemove", {
                clientX: t,
                clientY: e,
                bubbles: !0,
                cancelable: !0
            });
            this.video.dispatchEvent(i), this.mouseHistory.push({
                x: t,
                y: e
            }), this.mouseHistory.length > 20 && this.mouseHistory.shift()
        } catch (n) {}
    }
    random(t, e) {
        return t = vgHumanFinite(t, 0), (e = vgHumanFinite(e, t)) < t && ([t, e] = [e, t]), t + Math.random() * (e - t)
    }
    sleep(t) {
        return t = Math.max(0, vgHumanFinite(t, 0)), new Promise(e => setTimeout(e, t))
    }
    stop() {
        this.isActive = !1, this.video = null, this.mouseHistory = [];
        let t = this.cleanupHandlers.slice();
        for (let e of (this.cleanupHandlers = [], t)) try {
            e()
        } catch (i) {}
    }
}