(function(a) {
    if (typeof define === "function" && define.amd) {
        define([ "jquery" ], a);
    } else {
        a(jQuery);
    }
})(function(a) {
    a.ui = a.ui || {};
    var b = a.ui.version = "1.12.1";
    (function() {
        var b, c = Math.max, d = Math.abs, e = /left|center|right/, f = /top|center|bottom/, g = /[\+\-]\d+(\.[\d]+)?%?/, h = /^\w+/, i = /%$/, j = a.fn.pos;
        function k(a, b, c) {
            return [ parseFloat(a[0]) * (i.test(a[0]) ? b / 100 : 1), parseFloat(a[1]) * (i.test(a[1]) ? c / 100 : 1) ];
        }
        function l(b, c) {
            return parseInt(a.css(b, c), 10) || 0;
        }
        function m(b) {
            var c = b[0];
            if (c.nodeType === 9) {
                return {
                    width: b.width(),
                    height: b.height(),
                    offset: {
                        top: 0,
                        left: 0
                    }
                };
            }
            if (a.isWindow(c)) {
                return {
                    width: b.width(),
                    height: b.height(),
                    offset: {
                        top: b.scrollTop(),
                        left: b.scrollLeft()
                    }
                };
            }
            if (c.preventDefault) {
                return {
                    width: 0,
                    height: 0,
                    offset: {
                        top: c.pageY,
                        left: c.pageX
                    }
                };
            }
            return {
                width: b.outerWidth(),
                height: b.outerHeight(),
                offset: b.offset()
            };
        }
        a.pos = {
            scrollbarWidth: function() {
                if (b !== undefined) {
                    return b;
                }
                var c, d, e = a("<div " + "style='display:block;position:absolute;width:50px;height:50px;overflow:hidden;'>" + "<div style='height:100px;width:auto;'></div></div>"), f = e.children()[0];
                a("body").append(e);
                c = f.offsetWidth;
                e.css("overflow", "scroll");
                d = f.offsetWidth;
                if (c === d) {
                    d = e[0].clientWidth;
                }
                e.remove();
                return b = c - d;
            },
            getScrollInfo: function(b) {
                var c = b.isWindow || b.isDocument ? "" : b.element.css("overflow-x"), d = b.isWindow || b.isDocument ? "" : b.element.css("overflow-y"), e = c === "scroll" || c === "auto" && b.width < b.element[0].scrollWidth, f = d === "scroll" || d === "auto" && b.height < b.element[0].scrollHeight;
                return {
                    width: f ? a.pos.scrollbarWidth() : 0,
                    height: e ? a.pos.scrollbarWidth() : 0
                };
            },
            getWithinInfo: function(b) {
                var c = a(b || window), d = a.isWindow(c[0]), e = !!c[0] && c[0].nodeType === 9, f = !d && !e;
                return {
                    element: c,
                    isWindow: d,
                    isDocument: e,
                    offset: f ? a(b).offset() : {
                        left: 0,
                        top: 0
                    },
                    scrollLeft: c.scrollLeft(),
                    scrollTop: c.scrollTop(),
                    width: c.outerWidth(),
                    height: c.outerHeight()
                };
            }
        };
        a.fn.pos = function(b) {
            if (!b || !b.of) {
                return j.apply(this, arguments);
            }
            b = a.extend({}, b);
            var i, n, o, p, q, r, s = a(b.of), t = a.pos.getWithinInfo(b.within), u = a.pos.getScrollInfo(t), v = (b.collision || "flip").split(" "), w = {};
            r = m(s);
            if (s[0].preventDefault) {
                b.at = "left top";
            }
            n = r.width;
            o = r.height;
            p = r.offset;
            q = a.extend({}, p);
            a.each([ "my", "at" ], function() {
                var a = (b[this] || "").split(" "), c, d;
                if (a.length === 1) {
                    a = e.test(a[0]) ? a.concat([ "center" ]) : f.test(a[0]) ? [ "center" ].concat(a) : [ "center", "center" ];
                }
                a[0] = e.test(a[0]) ? a[0] : "center";
                a[1] = f.test(a[1]) ? a[1] : "center";
                c = g.exec(a[0]);
                d = g.exec(a[1]);
                w[this] = [ c ? c[0] : 0, d ? d[0] : 0 ];
                b[this] = [ h.exec(a[0])[0], h.exec(a[1])[0] ];
            });
            if (v.length === 1) {
                v[1] = v[0];
            }
            if (b.at[0] === "right") {
                q.left += n;
            } else if (b.at[0] === "center") {
                q.left += n / 2;
            }
            if (b.at[1] === "bottom") {
                q.top += o;
            } else if (b.at[1] === "center") {
                q.top += o / 2;
            }
            i = k(w.at, n, o);
            q.left += i[0];
            q.top += i[1];
            return this.each(function() {
                var e, f, g = a(this), h = g.outerWidth(), j = g.outerHeight(), m = l(this, "marginLeft"), r = l(this, "marginTop"), x = h + m + l(this, "marginRight") + u.width, y = j + r + l(this, "marginBottom") + u.height, z = a.extend({}, q), A = k(w.my, g.outerWidth(), g.outerHeight());
                if (b.my[0] === "right") {
                    z.left -= h;
                } else if (b.my[0] === "center") {
                    z.left -= h / 2;
                }
                if (b.my[1] === "bottom") {
                    z.top -= j;
                } else if (b.my[1] === "center") {
                    z.top -= j / 2;
                }
                z.left += A[0];
                z.top += A[1];
                e = {
                    marginLeft: m,
                    marginTop: r
                };
                a.each([ "left", "top" ], function(c, d) {
                    if (a.ui.pos[v[c]]) {
                        a.ui.pos[v[c]][d](z, {
                            targetWidth: n,
                            targetHeight: o,
                            elemWidth: h,
                            elemHeight: j,
                            collisionPosition: e,
                            collisionWidth: x,
                            collisionHeight: y,
                            offset: [ i[0] + A[0], i[1] + A[1] ],
                            my: b.my,
                            at: b.at,
                            within: t,
                            elem: g
                        });
                    }
                });
                if (b.using) {
                    f = function(a) {
                        var e = p.left - z.left, f = e + n - h, i = p.top - z.top, k = i + o - j, l = {
                            target: {
                                element: s,
                                left: p.left,
                                top: p.top,
                                width: n,
                                height: o
                            },
                            element: {
                                element: g,
                                left: z.left,
                                top: z.top,
                                width: h,
                                height: j
                            },
                            horizontal: f < 0 ? "left" : e > 0 ? "right" : "center",
                            vertical: k < 0 ? "top" : i > 0 ? "bottom" : "middle"
                        };
                        if (n < h && d(e + f) < n) {
                            l.horizontal = "center";
                        }
                        if (o < j && d(i + k) < o) {
                            l.vertical = "middle";
                        }
                        if (c(d(e), d(f)) > c(d(i), d(k))) {
                            l.important = "horizontal";
                        } else {
                            l.important = "vertical";
                        }
                        b.using.call(this, a, l);
                    };
                }
                g.offset(a.extend(z, {
                    using: f
                }));
            });
        };
        a.ui.pos = {
            _trigger: function(a, b, c, d) {
                if (b.elem) {
                    b.elem.trigger({
                        type: c,
                        position: a,
                        positionData: b,
                        triggered: d
                    });
                }
            },
            fit: {
                left: function(b, d) {
                    a.ui.pos._trigger(b, d, "posCollide", "fitLeft");
                    var e = d.within, f = e.isWindow ? e.scrollLeft : e.offset.left, g = e.width, h = b.left - d.collisionPosition.marginLeft, i = f - h, j = h + d.collisionWidth - g - f, k;
                    if (d.collisionWidth > g) {
                        if (i > 0 && j <= 0) {
                            k = b.left + i + d.collisionWidth - g - f;
                            b.left += i - k;
                        } else if (j > 0 && i <= 0) {
                            b.left = f;
                        } else {
                            if (i > j) {
                                b.left = f + g - d.collisionWidth;
                            } else {
                                b.left = f;
                            }
                        }
                    } else if (i > 0) {
                        b.left += i;
                    } else if (j > 0) {
                        b.left -= j;
                    } else {
                        b.left = c(b.left - h, b.left);
                    }
                    a.ui.pos._trigger(b, d, "posCollided", "fitLeft");
                },
                top: function(b, d) {
                    a.ui.pos._trigger(b, d, "posCollide", "fitTop");
                    var e = d.within, f = e.isWindow ? e.scrollTop : e.offset.top, g = d.within.height, h = b.top - d.collisionPosition.marginTop, i = f - h, j = h + d.collisionHeight - g - f, k;
                    if (d.collisionHeight > g) {
                        if (i > 0 && j <= 0) {
                            k = b.top + i + d.collisionHeight - g - f;
                            b.top += i - k;
                        } else if (j > 0 && i <= 0) {
                            b.top = f;
                        } else {
                            if (i > j) {
                                b.top = f + g - d.collisionHeight;
                            } else {
                                b.top = f;
                            }
                        }
                    } else if (i > 0) {
                        b.top += i;
                    } else if (j > 0) {
                        b.top -= j;
                    } else {
                        b.top = c(b.top - h, b.top);
                    }
                    a.ui.pos._trigger(b, d, "posCollided", "fitTop");
                }
            },
            flip: {
                left: function(b, c) {
                    a.ui.pos._trigger(b, c, "posCollide", "flipLeft");
                    var e = c.within, f = e.offset.left + e.scrollLeft, g = e.width, h = e.isWindow ? e.scrollLeft : e.offset.left, i = b.left - c.collisionPosition.marginLeft, j = i - h, k = i + c.collisionWidth - g - h, l = c.my[0] === "left" ? -c.elemWidth : c.my[0] === "right" ? c.elemWidth : 0, m = c.at[0] === "left" ? c.targetWidth : c.at[0] === "right" ? -c.targetWidth : 0, n = -2 * c.offset[0], o, p;
                    if (j < 0) {
                        o = b.left + l + m + n + c.collisionWidth - g - f;
                        if (o < 0 || o < d(j)) {
                            b.left += l + m + n;
                        }
                    } else if (k > 0) {
                        p = b.left - c.collisionPosition.marginLeft + l + m + n - h;
                        if (p > 0 || d(p) < k) {
                            b.left += l + m + n;
                        }
                    }
                    a.ui.pos._trigger(b, c, "posCollided", "flipLeft");
                },
                top: function(b, c) {
                    a.ui.pos._trigger(b, c, "posCollide", "flipTop");
                    var e = c.within, f = e.offset.top + e.scrollTop, g = e.height, h = e.isWindow ? e.scrollTop : e.offset.top, i = b.top - c.collisionPosition.marginTop, j = i - h, k = i + c.collisionHeight - g - h, l = c.my[1] === "top", m = l ? -c.elemHeight : c.my[1] === "bottom" ? c.elemHeight : 0, n = c.at[1] === "top" ? c.targetHeight : c.at[1] === "bottom" ? -c.targetHeight : 0, o = -2 * c.offset[1], p, q;
                    if (j < 0) {
                        q = b.top + m + n + o + c.collisionHeight - g - f;
                        if (q < 0 || q < d(j)) {
                            b.top += m + n + o;
                        }
                    } else if (k > 0) {
                        p = b.top - c.collisionPosition.marginTop + m + n + o - h;
                        if (p > 0 || d(p) < k) {
                            b.top += m + n + o;
                        }
                    }
                    a.ui.pos._trigger(b, c, "posCollided", "flipTop");
                }
            },
            flipfit: {
                left: function() {
                    a.ui.pos.flip.left.apply(this, arguments);
                    a.ui.pos.fit.left.apply(this, arguments);
                },
                top: function() {
                    a.ui.pos.flip.top.apply(this, arguments);
                    a.ui.pos.fit.top.apply(this, arguments);
                }
            }
        };
        (function() {
            var b, c, d, e, f, g = document.getElementsByTagName("body")[0], h = document.createElement("div");
            b = document.createElement(g ? "div" : "body");
            d = {
                visibility: "hidden",
                width: 0,
                height: 0,
                border: 0,
                margin: 0,
                background: "none"
            };
            if (g) {
                a.extend(d, {
                    position: "absolute",
                    left: "-1000px",
                    top: "-1000px"
                });
            }
            for (f in d) {
                b.style[f] = d[f];
            }
            b.appendChild(h);
            c = g || document.documentElement;
            c.insertBefore(b, c.firstChild);
            h.style.cssText = "position: absolute; left: 10.7432222px;";
            e = a(h).offset().left;
            a.support.offsetFractions = e > 10 && e < 11;
            b.innerHTML = "";
            c.removeChild(b);
        })();
    })();
    var c = a.ui.position;
});

(function(a) {
    "use strict";
    if (typeof define === "function" && define.amd) {
        define([ "jquery" ], a);
    } else if (window.jQuery && !window.jQuery.fn.iconpicker) {
        a(window.jQuery);
    }
})(function(a) {
    "use strict";
    var b = {
        isEmpty: function(a) {
            return a === false || a === "" || a === null || a === undefined;
        },
        isEmptyObject: function(a) {
            return this.isEmpty(a) === true || a.length === 0;
        },
        isElement: function(b) {
            return a(b).length > 0;
        },
        isString: function(a) {
            return typeof a === "string" || a instanceof String;
        },
        isArray: function(b) {
            return a.isArray(b);
        },
        inArray: function(b, c) {
            return a.inArray(b, c) !== -1;
        },
        throwError: function(a) {
            throw "Font Awesome Icon Picker Exception: " + a;
        }
    };
    var c = function(d, e) {
        this._id = c._idCounter++;
        this.element = a(d).addClass("iconpicker-element");
        this._trigger("iconpickerCreate");
        this.options = a.extend({}, c.defaultOptions, this.element.data(), e);
        this.options.templates = a.extend({}, c.defaultOptions.templates, this.options.templates);
        this.options.originalPlacement = this.options.placement;
        this.container = b.isElement(this.options.container) ? a(this.options.container) : false;
        if (this.container === false) {
            if (this.element.is(".dropdown-toggle")) {
                this.container = a("~ .dropdown-menu:first", this.element);
            } else {
                this.container = this.element.is("input,textarea,button,.btn") ? this.element.parent() : this.element;
            }
        }
        this.container.addClass("iconpicker-container");
        if (this.isDropdownMenu()) {
            this.options.templates.search = false;
            this.options.templates.buttons = false;
            this.options.placement = "inline";
        }
        this.input = this.element.is("input,textarea") ? this.element.addClass("iconpicker-input") : false;
        if (this.input === false) {
            this.input = this.container.find(this.options.input);
            if (!this.input.is("input,textarea")) {
                this.input = false;
            }
        }
        this.component = this.isDropdownMenu() ? this.container.parent().find(this.options.component) : this.container.find(this.options.component);
        if (this.component.length === 0) {
            this.component = false;
        } else {
            this.component.find("i").addClass("iconpicker-component");
        }
        this._createPopover();
        this._createIconpicker();
        if (this.getAcceptButton().length === 0) {
            this.options.mustAccept = false;
        }
        if (this.isInputGroup()) {
            this.container.parent().append(this.popover);
        } else {
            this.container.append(this.popover);
        }
        this._bindElementEvents();
        this._bindWindowEvents();
        this.update(this.options.selected);
        if (this.isInline()) {
            this.show();
        }
        this._trigger("iconpickerCreated");
    };
    c._idCounter = 0;
    c.defaultOptions = {
        title: false,
        selected: false,
        defaultValue: false,
        placement: "bottom",
        collision: "none",
        animation: true,
        hideOnSelect: false,
        showFooter: false,
        searchInFooter: false,
        mustAccept: false,
        selectedCustomClass: "bg-primary",
        icons: [],
        fullClassFormatter: function(a) {
            return a;
        },
        input: "input,.iconpicker-input",
        inputSearch: false,
        container: false,
        component: ".input-group-addon,.iconpicker-component",
        templates: {
            popover: '<div class="iconpicker-popover popover"><div class="arrow"></div>' + '<div class="popover-title"></div><div class="popover-content"></div></div>',
            footer: '<div class="popover-footer"></div>',
            buttons: '<button class="iconpicker-btn iconpicker-btn-cancel btn btn-default btn-sm">Cancel</button>' + ' <button class="iconpicker-btn iconpicker-btn-accept btn btn-primary btn-sm">Accept</button>',
            search: '<input type="search" class="form-control iconpicker-search" placeholder="Type to filter" />',
            iconpicker: '<div class="iconpicker"><div class="iconpicker-items"></div></div>',
            iconpickerItem: '<a role="button" href="#" class="iconpicker-item"><i></i></a>'
        }
    };
    c.batch = function(b, c) {
        var d = Array.prototype.slice.call(arguments, 2);
        return a(b).each(function() {
            var b = a(this).data("iconpicker");
            if (!!b) {
                b[c].apply(b, d);
            }
        });
    };
    c.prototype = {
        constructor: c,
        options: {},
        _id: 0,
        _trigger: function(b, c) {
            c = c || {};
            this.element.trigger(a.extend({
                type: b,
                iconpickerInstance: this
            }, c));
        },
        _createPopover: function() {
            this.popover = a(this.options.templates.popover);
            var c = this.popover.find(".popover-title");
            if (!!this.options.title) {
                c.append(a('<div class="popover-title-text">' + this.options.title + "</div>"));
            }
            if (this.hasSeparatedSearchInput() && !this.options.searchInFooter) {
                c.append(this.options.templates.search);
            } else if (!this.options.title) {
                c.remove();
            }
            if (this.options.showFooter && !b.isEmpty(this.options.templates.footer)) {
                var d = a(this.options.templates.footer);
                if (this.hasSeparatedSearchInput() && this.options.searchInFooter) {
                    d.append(a(this.options.templates.search));
                }
                if (!b.isEmpty(this.options.templates.buttons)) {
                    d.append(a(this.options.templates.buttons));
                }
                this.popover.append(d);
            }
            if (this.options.animation === true) {
                this.popover.addClass("fade");
            }
            return this.popover;
        },
        _createIconpicker: function() {
            var b = this;
            this.iconpicker = a(this.options.templates.iconpicker);
            var c = function(c) {
                var d = a(this);
                if (d.is("i")) {
                    d = d.parent();
                }
                b._trigger("iconpickerSelect", {
                    iconpickerItem: d,
                    iconpickerValue: b.iconpickerValue
                });
                if (b.options.mustAccept === false) {
                    b.update(d.data("iconpickerValue"));
                    b._trigger("iconpickerSelected", {
                        iconpickerItem: this,
                        iconpickerValue: b.iconpickerValue
                    });
                } else {
                    b.update(d.data("iconpickerValue"), true);
                }
                if (b.options.hideOnSelect && b.options.mustAccept === false) {
                    b.hide();
                }
                c.preventDefault();
                return false;
            };
            for (var d in this.options.icons) {
                if (typeof this.options.icons[d] === "string") {
                    var e = a(this.options.templates.iconpickerItem);
                    e.find("i").addClass(this.options.fullClassFormatter(this.options.icons[d]));
                    e.data("iconpickerValue", this.options.icons[d]).on("click.iconpicker", c);
                    this.iconpicker.find(".iconpicker-items").append(e.attr("title", "." + this.options.icons[d]));
                }
            }
            this.popover.find(".popover-content").append(this.iconpicker);
            return this.iconpicker;
        },
        _isEventInsideIconpicker: function(b) {
            var c = a(b.target);
            if ((!c.hasClass("iconpicker-element") || c.hasClass("iconpicker-element") && !c.is(this.element)) && c.parents(".iconpicker-popover").length === 0) {
                return false;
            }
            return true;
        },
        _bindElementEvents: function() {
            var c = this;
            this.getSearchInput().on("keyup.iconpicker", function() {
                c.filter(a(this).val().toLowerCase());
            });
            this.getAcceptButton().on("click.iconpicker", function() {
                var a = c.iconpicker.find(".iconpicker-selected").get(0);
                c.update(c.iconpickerValue);
                c._trigger("iconpickerSelected", {
                    iconpickerItem: a,
                    iconpickerValue: c.iconpickerValue
                });
                if (!c.isInline()) {
                    c.hide();
                }
            });
            this.getCancelButton().on("click.iconpicker", function() {
                if (!c.isInline()) {
                    c.hide();
                }
            });
            this.element.on("focus.iconpicker", function(a) {
                c.show();
                a.stopPropagation();
            });
            if (this.hasComponent()) {
                this.component.on("click.iconpicker", function() {
                    c.toggle();
                });
            }
            if (this.hasInput()) {
                this.input.on("keyup.iconpicker", function(d) {
                    if (!b.inArray(d.keyCode, [ 38, 40, 37, 39, 16, 17, 18, 9, 8, 91, 93, 20, 46, 186, 190, 46, 78, 188, 44, 86 ])) {
                        c.update();
                    } else {
                        c._updateFormGroupStatus(c.getValid(this.value) !== false);
                    }
                    if (c.options.inputSearch === true) {
                        c.filter(a(this).val().toLowerCase());
                    }
                });
            }
        },
        _bindWindowEvents: function() {
            var b = a(window.document);
            var c = this;
            var d = ".iconpicker.inst" + this._id;
            a(window).on("resize.iconpicker" + d + " orientationchange.iconpicker" + d, function(a) {
                if (c.popover.hasClass("in")) {
                    c.updatePlacement();
                }
            });
            if (!c.isInline()) {
                b.on("mouseup" + d, function(a) {
                    if (!c._isEventInsideIconpicker(a) && !c.isInline()) {
                        c.hide();
                    }
                    a.stopPropagation();
                    a.preventDefault();
                    return false;
                });
            }
            return false;
        },
        _unbindElementEvents: function() {
            this.popover.off(".iconpicker");
            this.element.off(".iconpicker");
            if (this.hasInput()) {
                this.input.off(".iconpicker");
            }
            if (this.hasComponent()) {
                this.component.off(".iconpicker");
            }
            if (this.hasContainer()) {
                this.container.off(".iconpicker");
            }
        },
        _unbindWindowEvents: function() {
            a(window).off(".iconpicker.inst" + this._id);
            a(window.document).off(".iconpicker.inst" + this._id);
        },
        updatePlacement: function(b, c) {
            b = b || this.options.placement;
            this.options.placement = b;
            c = c || this.options.collision;
            c = c === true ? "flip" : c;
            var d = {
                at: "right bottom",
                my: "right top",
                of: this.hasInput() && !this.isInputGroup() ? this.input : this.container,
                collision: c === true ? "flip" : c,
                within: window
            };
            this.popover.removeClass("inline topLeftCorner topLeft top topRight topRightCorner " + "rightTop right rightBottom bottomRight bottomRightCorner " + "bottom bottomLeft bottomLeftCorner leftBottom left leftTop");
            if (typeof b === "object") {
                return this.popover.pos(a.extend({}, d, b));
            }
            switch (b) {
              case "inline":
                {
                    d = false;
                }
                break;

              case "topLeftCorner":
                {
                    d.my = "right bottom";
                    d.at = "left top";
                }
                break;

              case "topLeft":
                {
                    d.my = "left bottom";
                    d.at = "left top";
                }
                break;

              case "top":
                {
                    d.my = "center bottom";
                    d.at = "center top";
                }
                break;

              case "topRight":
                {
                    d.my = "right bottom";
                    d.at = "right top";
                }
                break;

              case "topRightCorner":
                {
                    d.my = "left bottom";
                    d.at = "right top";
                }
                break;

              case "rightTop":
                {
                    d.my = "left bottom";
                    d.at = "right center";
                }
                break;

              case "right":
                {
                    d.my = "left center";
                    d.at = "right center";
                }
                break;

              case "rightBottom":
                {
                    d.my = "left top";
                    d.at = "right center";
                }
                break;

              case "bottomRightCorner":
                {
                    d.my = "left top";
                    d.at = "right bottom";
                }
                break;

              case "bottomRight":
                {
                    d.my = "right top";
                    d.at = "right bottom";
                }
                break;

              case "bottom":
                {
                    d.my = "center top";
                    d.at = "center bottom";
                }
                break;

              case "bottomLeft":
                {
                    d.my = "left top";
                    d.at = "left bottom";
                }
                break;

              case "bottomLeftCorner":
                {
                    d.my = "right top";
                    d.at = "left bottom";
                }
                break;

              case "leftBottom":
                {
                    d.my = "right top";
                    d.at = "left center";
                }
                break;

              case "left":
                {
                    d.my = "right center";
                    d.at = "left center";
                }
                break;

              case "leftTop":
                {
                    d.my = "right bottom";
                    d.at = "left center";
                }
                break;

              default:
                {
                    return false;
                }
                break;
            }
            this.popover.css({
                display: this.options.placement === "inline" ? "" : "block"
            });
            if (d !== false) {
                this.popover.pos(d).css("maxWidth", a(window).width() - this.container.offset().left - 5);
            } else {
                this.popover.css({
                    top: "auto",
                    right: "auto",
                    bottom: "auto",
                    left: "auto",
                    maxWidth: "none"
                });
            }
            this.popover.addClass(this.options.placement);
            return true;
        },
        _updateComponents: function() {
            this.iconpicker.find(".iconpicker-item.iconpicker-selected").removeClass("iconpicker-selected " + this.options.selectedCustomClass);
            if (this.iconpickerValue) {
                this.iconpicker.find("." + this.options.fullClassFormatter(this.iconpickerValue).replace(/ /g, ".")).parent().addClass("iconpicker-selected " + this.options.selectedCustomClass);
            }
            if (this.hasComponent()) {
                var a = this.component.find("i");
                if (a.length > 0) {
                    a.attr("class", this.options.fullClassFormatter(this.iconpickerValue));
                } else {
                    this.component.html(this.getHtml());
                }
            }
        },
        _updateFormGroupStatus: function(a) {
            if (this.hasInput()) {
                if (a !== false) {
                    this.input.parents(".form-group:first").removeClass("has-error");
                } else {
                    this.input.parents(".form-group:first").addClass("has-error");
                }
                return true;
            }
            return false;
        },
        getValid: function(c) {
            if (!b.isString(c)) {
                c = "";
            }
            var d = c === "";
            c = a.trim(c);
            if (b.inArray(c, this.options.icons) || d) {
                return c;
            }
            return false;
        },
        setValue: function(a) {
            var b = this.getValid(a);
            if (b !== false) {
                this.iconpickerValue = b;
                this._trigger("iconpickerSetValue", {
                    iconpickerValue: b
                });
                return this.iconpickerValue;
            } else {
                this._trigger("iconpickerInvalid", {
                    iconpickerValue: a
                });
                return false;
            }
        },
        getHtml: function() {
            return '<i class="' + this.options.fullClassFormatter(this.iconpickerValue) + '"></i>';
        },
        setSourceValue: function(a) {
            a = this.setValue(a);
            if (a !== false && a !== "") {
                if (this.hasInput()) {
                    this.input.val(this.iconpickerValue);
                } else {
                    this.element.data("iconpickerValue", this.iconpickerValue);
                }
                this._trigger("iconpickerSetSourceValue", {
                    iconpickerValue: a
                });
            }
            return a;
        },
        getSourceValue: function(a) {
            a = a || this.options.defaultValue;
            var b = a;
            if (this.hasInput()) {
                b = this.input.val();
            } else {
                b = this.element.data("iconpickerValue");
            }
            if (b === undefined || b === "" || b === null || b === false) {
                b = a;
            }
            return b;
        },
        hasInput: function() {
            return this.input !== false;
        },
        isInputSearch: function() {
            return this.hasInput() && this.options.inputSearch === true;
        },
        isInputGroup: function() {
            return this.container.is(".input-group");
        },
        isDropdownMenu: function() {
            return this.container.is(".dropdown-menu");
        },
        hasSeparatedSearchInput: function() {
            return this.options.templates.search !== false && !this.isInputSearch();
        },
        hasComponent: function() {
            return this.component !== false;
        },
        hasContainer: function() {
            return this.container !== false;
        },
        getAcceptButton: function() {
            return this.popover.find(".iconpicker-btn-accept");
        },
        getCancelButton: function() {
            return this.popover.find(".iconpicker-btn-cancel");
        },
        getSearchInput: function() {
            return this.popover.find(".iconpicker-search");
        },
        filter: function(c) {
            if (b.isEmpty(c)) {
                this.iconpicker.find(".iconpicker-item").show();
                return a(false);
            } else {
                var d = [];
                this.iconpicker.find(".iconpicker-item").each(function() {
                    var b = a(this);
                    var e = b.attr("title").toLowerCase();
                    var f = false;
                    try {
                        f = new RegExp(c, "g");
                    } catch (a) {
                        f = false;
                    }
                    if (f !== false && e.match(f)) {
                        d.push(b);
                        b.show();
                    } else {
                        b.hide();
                    }
                });
                return d;
            }
        },
        show: function() {
            if (this.popover.hasClass("in")) {
                return false;
            }
            a.iconpicker.batch(a(".iconpicker-popover.in:not(.inline)").not(this.popover), "hide");
            this._trigger("iconpickerShow");
            this.updatePlacement();
            this.popover.addClass("in");
            setTimeout(a.proxy(function() {
                this.popover.css("display", this.isInline() ? "" : "block");
                this._trigger("iconpickerShown");
            }, this), this.options.animation ? 300 : 1);
        },
        hide: function() {
            if (!this.popover.hasClass("in")) {
                return false;
            }
            this._trigger("iconpickerHide");
            this.popover.removeClass("in");
            setTimeout(a.proxy(function() {
                this.popover.css("display", "none");
                this.getSearchInput().val("");
                this.filter("");
                this._trigger("iconpickerHidden");
            }, this), this.options.animation ? 300 : 1);
        },
        toggle: function() {
            if (this.popover.is(":visible")) {
                this.hide();
            } else {
                this.show(true);
            }
        },
        update: function(a, b) {
            a = a ? a : this.getSourceValue(this.iconpickerValue);
            this._trigger("iconpickerUpdate");
            if (b === true) {
                a = this.setValue(a);
            } else {
                a = this.setSourceValue(a);
                this._updateFormGroupStatus(a !== false);
            }
            if (a !== false) {
                this._updateComponents();
            }
            this._trigger("iconpickerUpdated");
            return a;
        },
        destroy: function() {
            this._trigger("iconpickerDestroy");
            this.element.removeData("iconpicker").removeData("iconpickerValue").removeClass("iconpicker-element");
            this._unbindElementEvents();
            this._unbindWindowEvents();
            a(this.popover).remove();
            this._trigger("iconpickerDestroyed");
        },
        disable: function() {
            if (this.hasInput()) {
                this.input.prop("disabled", true);
                return true;
            }
            return false;
        },
        enable: function() {
            if (this.hasInput()) {
                this.input.prop("disabled", false);
                return true;
            }
            return false;
        },
        isDisabled: function() {
            if (this.hasInput()) {
                return this.input.prop("disabled") === true;
            }
            return false;
        },
        isInline: function() {
            return this.options.placement === "inline" || this.popover.hasClass("inline");
        }
    };
    a.iconpicker = c;
    a.fn.iconpicker = function(b) {
        return this.each(function() {
            var d = a(this);
            if (!d.data("iconpicker")) {
                d.data("iconpicker", new c(this, typeof b === "object" ? b : {}));
            }
        });
    };
    c.defaultOptions.icons = [ "dir-icon-500px", "dir-icon-address-book", "dir-icon-address-book-o", "dir-icon-address-card", "dir-icon-address-card-o", "dir-icon-adjust", "dir-icon-adn", "dir-icon-align-center", "dir-icon-align-justify", "dir-icon-align-left", "dir-icon-align-right", "dir-icon-amazon", "dir-icon-ambulance", "dir-icon-american-sign-language-interpreting", "dir-icon-anchor", "dir-icon-android", "dir-icon-angellist", "dir-icon-angle-double-down", "dir-icon-angle-double-left", "dir-icon-angle-double-right", "dir-icon-angle-double-up", "dir-icon-angle-down", "dir-icon-angle-left", "dir-icon-angle-right", "dir-icon-angle-up", "dir-icon-apple", "dir-icon-archive", "dir-icon-area-chart", "dir-icon-arrow-circle-down", "dir-icon-arrow-circle-left", "dir-icon-arrow-circle-o-down", "dir-icon-arrow-circle-o-left", "dir-icon-arrow-circle-o-right", "dir-icon-arrow-circle-o-up", "dir-icon-arrow-circle-right", "dir-icon-arrow-circle-up", "dir-icon-arrow-down", "dir-icon-arrow-left", "dir-icon-arrow-right", "dir-icon-arrow-up", "dir-icon-arrows", "dir-icon-arrows-alt", "dir-icon-arrows-h", "dir-icon-arrows-v", "dir-icon-asl-interpreting", "dir-icon-assistive-listening-systems", "dir-icon-asterisk", "dir-icon-at", "dir-icon-audio-description", "dir-icon-automobile", "dir-icon-backward", "dir-icon-balance-scale", "dir-icon-ban", "dir-icon-bandcamp", "dir-icon-bank", "dir-icon-bar-chart", "dir-icon-bar-chart-o", "dir-icon-barcode", "dir-icon-bars", "dir-icon-bath", "dir-icon-bathtub", "dir-icon-battery-0", "dir-icon-battery-1", "dir-icon-battery-2", "dir-icon-battery-3", "dir-icon-battery-4", "dir-icon-battery-empty", "dir-icon-battery-full", "dir-icon-battery-half", "dir-icon-battery-quarter", "dir-icon-battery-three-quarters", "dir-icon-bed", "dir-icon-beer", "dir-icon-behance", "dir-icon-behance-square", "dir-icon-bell", "dir-icon-bell-o", "dir-icon-bell-slash", "dir-icon-bell-slash-o", "dir-icon-bicycle", "dir-icon-binoculars", "dir-icon-birthday-cake", "dir-icon-bitbucket", "dir-icon-bitbucket-square", "dir-icon-bitcoin", "dir-icon-black-tie", "dir-icon-blind", "dir-icon-bluetooth", "dir-icon-bluetooth-b", "dir-icon-bold", "dir-icon-bolt", "dir-icon-bomb", "dir-icon-book", "dir-icon-bookmark", "dir-icon-bookmark-o", "dir-icon-braille", "dir-icon-briefcase", "dir-icon-btc", "dir-icon-bug", "dir-icon-building", "dir-icon-building-o", "dir-icon-bullhorn", "dir-icon-bullseye", "dir-icon-bus", "dir-icon-buysellads", "dir-icon-cab", "dir-icon-calculator", "dir-icon-calendar", "dir-icon-calendar-check-o", "dir-icon-calendar-minus-o", "dir-icon-calendar-o", "dir-icon-calendar-plus-o", "dir-icon-calendar-times-o", "dir-icon-camera", "dir-icon-camera-retro", "dir-icon-car", "dir-icon-caret-down", "dir-icon-caret-left", "dir-icon-caret-right", "dir-icon-caret-square-o-down", "dir-icon-caret-square-o-left", "dir-icon-caret-square-o-right", "dir-icon-caret-square-o-up", "dir-icon-caret-up", "dir-icon-cart-arrow-down", "dir-icon-cart-plus", "dir-icon-cc", "dir-icon-cc-amex", "dir-icon-cc-diners-club", "dir-icon-cc-discover", "dir-icon-cc-jcb", "dir-icon-cc-mastercard", "dir-icon-cc-paypal", "dir-icon-cc-stripe", "dir-icon-cc-visa", "dir-icon-certificate", "dir-icon-chain", "dir-icon-chain-broken", "dir-icon-check", "dir-icon-check-circle", "dir-icon-check-circle-o", "dir-icon-check-square", "dir-icon-check-square-o", "dir-icon-chevron-circle-down", "dir-icon-chevron-circle-left", "dir-icon-chevron-circle-right", "dir-icon-chevron-circle-up", "dir-icon-chevron-down", "dir-icon-chevron-left", "dir-icon-chevron-right", "dir-icon-chevron-up", "dir-icon-child", "dir-icon-chrome", "dir-icon-circle", "dir-icon-circle-o", "dir-icon-circle-o-notch", "dir-icon-circle-thin", "dir-icon-clipboard", "dir-icon-clock-o", "dir-icon-clone", "dir-icon-close", "dir-icon-cloud", "dir-icon-cloud-download", "dir-icon-cloud-upload", "dir-icon-cny", "dir-icon-code", "dir-icon-code-fork", "dir-icon-codepen", "dir-icon-codiepie", "dir-icon-coffee", "dir-icon-cog", "dir-icon-cogs", "dir-icon-columns", "dir-icon-comment", "dir-icon-comment-o", "dir-icon-commenting", "dir-icon-commenting-o", "dir-icon-comments", "dir-icon-comments-o", "dir-icon-compass", "dir-icon-compress", "dir-icon-connectdevelop", "dir-icon-contao", "dir-icon-copy", "dir-icon-copyright", "dir-icon-creative-commons", "dir-icon-credit-card", "dir-icon-credit-card-alt", "dir-icon-crop", "dir-icon-crosshairs", "dir-icon-css3", "dir-icon-cube", "dir-icon-cubes", "dir-icon-cut", "dir-icon-cutlery", "dir-icon-dashboard", "dir-icon-dashcube", "dir-icon-database", "dir-icon-deaf", "dir-icon-deafness", "dir-icon-dedent", "dir-icon-delicious", "dir-icon-desktop", "dir-icon-deviantart", "dir-icon-diamond", "dir-icon-digg", "dir-icon-dollar", "dir-icon-dot-circle-o", "dir-icon-download", "dir-icon-dribbble", "dir-icon-drivers-license", "dir-icon-drivers-license-o", "dir-icon-dropbox", "dir-icon-drupal", "dir-icon-edge", "dir-icon-edit", "dir-icon-eercast", "dir-icon-eject", "dir-icon-ellipsis-h", "dir-icon-ellipsis-v", "dir-icon-empire", "dir-icon-envelope", "dir-icon-envelope-o", "dir-icon-envelope-open", "dir-icon-envelope-open-o", "dir-icon-envelope-square", "dir-icon-envira", "dir-icon-eraser", "dir-icon-etsy", "dir-icon-eur", "dir-icon-euro", "dir-icon-exchange", "dir-icon-exclamation", "dir-icon-exclamation-circle", "dir-icon-exclamation-triangle", "dir-icon-expand", "dir-icon-expeditedssl", "dir-icon-external-link", "dir-icon-external-link-square", "dir-icon-eye", "dir-icon-eye-slash", "dir-icon-eyedropper", "dir-icon-facebook", "dir-icon-facebook-f", "dir-icon-facebook-official", "dir-icon-facebook-square", "dir-icon-fast-backward", "dir-icon-fast-forward", "dir-icon-fax", "dir-icon-feed", "dir-icon-female", "dir-icon-fighter-jet", "dir-icon-file", "dir-icon-file-archive-o", "dir-icon-file-audio-o", "dir-icon-file-code-o", "dir-icon-file-excel-o", "dir-icon-file-image-o", "dir-icon-file-movie-o","dir-icon-file-o", "dir-icon-file-pdf-o", "dir-icon-file-photo-o", "dir-icon-file-picture-o", "dir-icon-file-powerpoint-o", "dir-icon-file-sound-o", "dir-icon-file-text", "dir-icon-file-text-o", "dir-icon-file-video-o", "dir-icon-file-word-o", "dir-icon-file-zip-o", "dir-icon-files-o", "dir-icon-film", "dir-icon-filter", "dir-icon-fire", "dir-icon-fire-extinguisher", "dir-icon-firefox", "dir-icon-first-order", "dir-icon-flag", "dir-icon-flag-checkered", "dir-icon-flag-o", "dir-icon-flash", "dir-icon-flask", "dir-icon-flickr", "dir-icon-floppy-o", "dir-icon-folder", "dir-icon-folder-o", "dir-icon-folder-open", "dir-icon-folder-open-o", "dir-icon-font", "dir-icon-font-awesome", "dir-icon-fonticons", "dir-icon-fort-awesome", "dir-icon-forumbee", "dir-icon-forward", "dir-icon-foursquare", "dir-icon-free-code-camp", "dir-icon-frown-o", "dir-icon-futbol-o", "dir-icon-gamepad", "dir-icon-gavel", "dir-icon-gbp", "dir-icon-ge", "dir-icon-gear", "dir-icon-gears", "dir-icon-genderless", "dir-icon-get-pocket", "dir-icon-gg", "dir-icon-gg-circle", "dir-icon-gift", "dir-icon-git", "dir-icon-git-square", "dir-icon-github", "dir-icon-github-alt", "dir-icon-github-square", "dir-icon-gitlab", "dir-icon-gittip", "dir-icon-glass", "dir-icon-glide", "dir-icon-glide-g", "dir-icon-globe", "dir-icon-google", "dir-icon-google-plus", "dir-icon-google-plus-circle", "dir-icon-google-plus-official", "dir-icon-google-plus-square", "dir-icon-google-wallet", "dir-icon-graduation-cap", "dir-icon-gratipay", "dir-icon-grav", "dir-icon-group", "dir-icon-h-square", "dir-icon-hacker-news", "dir-icon-hand-grab-o", "dir-icon-hand-lizard-o", "dir-icon-hand-o-down", "dir-icon-hand-o-left", "dir-icon-hand-o-right", "dir-icon-hand-o-up", "dir-icon-hand-paper-o", "dir-icon-hand-peace-o", "dir-icon-hand-pointer-o", "dir-icon-hand-rock-o", "dir-icon-hand-scissors-o", "dir-icon-hand-spock-o", "dir-icon-hand-stop-o", "dir-icon-handshake-o", "dir-icon-hard-of-hearing", "dir-icon-hashtag", "dir-icon-hdd-o", "dir-icon-header", "dir-icon-headphones", "dir-icon-heart", "dir-icon-heart-o", "dir-icon-heartbeat", "dir-icon-history", "dir-icon-home", "dir-icon-hospital-o", "dir-icon-hotel", "dir-icon-hourglass", "dir-icon-hourglass-1", "dir-icon-hourglass-2", "dir-icon-hourglass-3", "dir-icon-hourglass-end", "dir-icon-hourglass-half", "dir-icon-hourglass-o", "dir-icon-hourglass-start", "dir-icon-houzz", "dir-icon-html5", "dir-icon-i-cursor", "dir-icon-id-badge", "dir-icon-id-card", "dir-icon-id-card-o", "dir-icon-ils", "dir-icon-image", "dir-icon-imdb", "dir-icon-inbox", "dir-icon-indent", "dir-icon-industry", "dir-icon-info", "dir-icon-info-circle", "dir-icon-inr", "dir-icon-instagram", "dir-icon-institution", "dir-icon-internet-explorer", "dir-icon-intersex", "dir-icon-ioxhost", "dir-icon-italic", "dir-icon-joomla", "dir-icon-jpy", "dir-icon-jsfiddle", "dir-icon-key", "dir-icon-keyboard-o", "dir-icon-krw", "dir-icon-language", "dir-icon-laptop", "dir-icon-lastfm", "dir-icon-lastfm-square", "dir-icon-leaf", "dir-icon-leanpub", "dir-icon-legal", "dir-icon-lemon-o", "dir-icon-level-down", "dir-icon-level-up", "dir-icon-life-bouy", "dir-icon-life-buoy", "dir-icon-life-ring", "dir-icon-life-saver", "dir-icon-lightbulb-o", "dir-icon-line-chart", "dir-icon-link", "dir-icon-linkedin", "dir-icon-linkedin-square", "dir-icon-linode", "dir-icon-linux", "dir-icon-list", "dir-icon-list-alt", "dir-icon-list-ol", "dir-icon-list-ul", "dir-icon-location-arrow", "dir-icon-lock", "dir-icon-long-arrow-down", "dir-icon-long-arrow-left", "dir-icon-long-arrow-right", "dir-icon-long-arrow-up", "dir-icon-low-vision", "dir-icon-magic", "dir-icon-magnet", "dir-icon-mail-forward", "dir-icon-mail-reply", "dir-icon-mail-reply-all", "dir-icon-male", "dir-icon-map", "dir-icon-map-marker", "dir-icon-map-o", "dir-icon-map-pin", "dir-icon-map-signs", "dir-icon-mars", "dir-icon-mars-double", "dir-icon-mars-stroke", "dir-icon-mars-stroke-h", "dir-icon-mars-stroke-v", "dir-icon-maxcdn", "dir-icon-meanpath", "dir-icon-medium", "dir-icon-medkit", "dir-icon-meetup", "dir-icon-meh-o", "dir-icon-mercury", "dir-icon-microchip", "dir-icon-microphone", "dir-icon-microphone-slash", "dir-icon-minus", "dir-icon-minus-circle", "dir-icon-minus-square", "dir-icon-minus-square-o", "dir-icon-mixcloud", "dir-icon-mobile", "dir-icon-mobile-phone", "dir-icon-modx", "dir-icon-money", "dir-icon-moon-o", "dir-icon-mortar-board", "dir-icon-motorcycle", "dir-icon-mouse-pointer", "dir-icon-music", "dir-icon-navicon", "dir-icon-neuter", "dir-icon-newspaper-o", "dir-icon-object-group", "dir-icon-object-ungroup", "dir-icon-odnoklassniki", "dir-icon-odnoklassniki-square", "dir-icon-opencart", "dir-icon-openid", "dir-icon-opera", "dir-icon-optin-monster", "dir-icon-outdent", "dir-icon-pagelines", "dir-icon-paint-brush", "dir-icon-paper-plane", "dir-icon-paper-plane-o", "dir-icon-paperclip", "dir-icon-paragraph", "dir-icon-paste", "dir-icon-pause", "dir-icon-pause-circle", "dir-icon-pause-circle-o", "dir-icon-paw", "dir-icon-paypal", "dir-icon-pencil", "dir-icon-pencil-square", "dir-icon-pencil-square-o", "dir-icon-percent", "dir-icon-phone", "dir-icon-phone-square", "dir-icon-photo", "dir-icon-picture-o", "dir-icon-pie-chart", "dir-icon-pied-piper", "dir-icon-pied-piper-alt", "dir-icon-pied-piper-pp", "dir-icon-pinterest", "dir-icon-pinterest-p", "dir-icon-pinterest-square", "dir-icon-plane", "dir-icon-play", "dir-icon-play-circle", "dir-icon-play-circle-o", "dir-icon-plug", "dir-icon-plus", "dir-icon-plus-circle", "dir-icon-plus-square", "dir-icon-plus-square-o", "dir-icon-podcast", "dir-icon-power-off", "dir-icon-print", "dir-icon-product-hunt", "dir-icon-puzzle-piece", "dir-icon-qq", "dir-icon-qrcode", "dir-icon-question", "dir-icon-question-circle", "dir-icon-question-circle-o", "dir-icon-quora", "dir-icon-quote-left", "dir-icon-quote-right", "dir-icon-ra", "dir-icon-random", "dir-icon-ravelry", "dir-icon-rebel", "dir-icon-recycle", "dir-icon-reddit", "dir-icon-reddit-alien", "dir-icon-reddit-square", "dir-icon-refresh", "dir-icon-registered", "dir-icon-remove", "dir-icon-renren", "dir-icon-reorder", "dir-icon-repeat", "dir-icon-reply", "dir-icon-reply-all", "dir-icon-resistance", "dir-icon-retweet", "dir-icon-rmb", "dir-icon-road", "dir-icon-rocket", "dir-icon-rotate-left", "dir-icon-rotate-right", "dir-icon-rouble", "dir-icon-rss", "dir-icon-rss-square", "dir-icon-rub", "dir-icon-ruble", "dir-icon-rupee", "dir-icon-s15", "dir-icon-safari", "dir-icon-save", "dir-icon-scissors", "dir-icon-scribd", "dir-icon-search", "dir-icon-search-minus", "dir-icon-search-plus", "dir-icon-sellsy", "dir-icon-send", "dir-icon-send-o", "dir-icon-server", "dir-icon-share", "dir-icon-share-alt", "dir-icon-share-alt-square", "dir-icon-share-square", "dir-icon-share-square-o", "dir-icon-shekel", "dir-icon-sheqel", "dir-icon-shield", "dir-icon-ship", "dir-icon-shirtsinbulk", "dir-icon-shopping-bag", "dir-icon-shopping-basket", "dir-icon-shopping-cart", "dir-icon-shower", "dir-icon-sign-in", "dir-icon-sign-language", "dir-icon-sign-out", "dir-icon-signal", "dir-icon-signing", "dir-icon-simplybuilt", "dir-icon-sitemap", "dir-icon-skyatlas", "dir-icon-skype", "dir-icon-slack", "dir-icon-sliders", "dir-icon-slideshare", "dir-icon-smile-o", "dir-icon-snapchat", "dir-icon-snapchat-ghost", "dir-icon-snapchat-square", "dir-icon-snowflake-o", "dir-icon-soccer-ball-o", "dir-icon-sort", "dir-icon-sort-alpha-asc", "dir-icon-sort-alpha-desc", "dir-icon-sort-amount-asc", "dir-icon-sort-amount-desc", "dir-icon-sort-asc", "dir-icon-sort-desc", "dir-icon-sort-down", "dir-icon-sort-numeric-asc", "dir-icon-sort-numeric-desc", "dir-icon-sort-up", "dir-icon-soundcloud", "dir-icon-space-shuttle", "dir-icon-spinner", "dir-icon-spoon", "dir-icon-spotify", "dir-icon-square", "dir-icon-square-o", "dir-icon-stack-exchange", "dir-icon-stack-overflow", "dir-icon-star", "dir-icon-star-half", "dir-icon-star-half-empty", "dir-icon-star-half-full", "dir-icon-star-half-o", "dir-icon-star-o", "dir-icon-steam", "dir-icon-steam-square", "dir-icon-step-backward", "dir-icon-step-forward", "dir-icon-stethoscope", "dir-icon-sticky-note", "dir-icon-sticky-note-o", "dir-icon-stop", "dir-icon-stop-circle", "dir-icon-stop-circle-o", "dir-icon-street-view", "dir-icon-strikethrough", "dir-icon-stumbleupon", "dir-icon-stumbleupon-circle", "dir-icon-subscript", "dir-icon-subway", "dir-icon-suitcase", "dir-icon-sun-o", "dir-icon-superpowers", "dir-icon-superscript", "dir-icon-support", "dir-icon-table", "dir-icon-tablet", "dir-icon-tachometer", "dir-icon-tag", "dir-icon-tags", "dir-icon-tasks", "dir-icon-taxi", "dir-icon-telegram", "dir-icon-television", "dir-icon-tencent-weibo", "dir-icon-terminal", "dir-icon-text-height", "dir-icon-text-width", "dir-icon-th", "dir-icon-th-large", "dir-icon-th-list", "dir-icon-themeisle", "dir-icon-thermometer", "dir-icon-thermometer-0", "dir-icon-thermometer-1", "dir-icon-thermometer-2", "dir-icon-thermometer-3", "dir-icon-thermometer-4", "dir-icon-thermometer-empty", "dir-icon-thermometer-full", "dir-icon-thermometer-half", "dir-icon-thermometer-quarter", "dir-icon-thermometer-three-quarters", "dir-icon-thumb-tack", "dir-icon-thumbs-down", "dir-icon-thumbs-o-down", "dir-icon-thumbs-o-up", "dir-icon-thumbs-up", "dir-icon-ticket", "dir-icon-times", "dir-icon-times-circle", "dir-icon-times-circle-o", "dir-icon-times-rectangle", "dir-icon-times-rectangle-o", "dir-icon-tint", "dir-icon-toggle-down", "dir-icon-toggle-left", "dir-icon-toggle-off", "dir-icon-toggle-on", "dir-icon-toggle-right", "dir-icon-toggle-up", "dir-icon-trademark", "dir-icon-train", "dir-icon-transgender", "dir-icon-transgender-alt", "dir-icon-trash", "dir-icon-trash-o", "dir-icon-tree", "dir-icon-trello", "dir-icon-tripadvisor", "dir-icon-trophy", "dir-icon-truck", "dir-icon-try", "dir-icon-tty", "dir-icon-tumblr", "dir-icon-tumblr-square", "dir-icon-turkish-lira", "dir-icon-tv", "dir-icon-twitch", "dir-icon-twitter", "dir-icon-twitter-square", "dir-icon-umbrella", "dir-icon-underline", "dir-icon-undo", "dir-icon-universal-access", "dir-icon-university", "dir-icon-unlink", "dir-icon-unlock", "dir-icon-unlock-alt", "dir-icon-unsorted", "dir-icon-upload", "dir-icon-usb", "dir-icon-usd", "dir-icon-user", "dir-icon-user-circle", "dir-icon-user-circle-o", "dir-icon-user-md", "dir-icon-user-o", "dir-icon-user-plus", "dir-icon-user-secret", "dir-icon-user-times", "dir-icon-users", "dir-icon-vcard", "dir-icon-vcard-o", "dir-icon-venus", "dir-icon-venus-double", "dir-icon-venus-mars", "dir-icon-viacoin", "dir-icon-viadeo", "dir-icon-viadeo-square", "dir-icon-video-camera", "dir-icon-vimeo", "dir-icon-vimeo-square", "dir-icon-vine", "dir-icon-vk", "dir-icon-volume-control-phone", "dir-icon-volume-down", "dir-icon-volume-off", "dir-icon-volume-up", "dir-icon-warning", "dir-icon-wechat", "dir-icon-weibo", "dir-icon-weixin", "dir-icon-whatsapp", "dir-icon-wheelchair", "dir-icon-wheelchair-alt", "dir-icon-wifi", "dir-icon-wikipedia-w", "dir-icon-window-close", "dir-icon-window-close-o", "dir-icon-window-maximize", "dir-icon-window-minimize", "dir-icon-window-restore", "dir-icon-windows", "dir-icon-won", "dir-icon-wordpress", "dir-icon-wpbeginner", "dir-icon-wpexplorer", "dir-icon-wpforms", "dir-icon-wrench", "dir-icon-xing", "dir-icon-xing-square", "dir-icon-y-combinator", "dir-icon-y-combinator-square", "dir-icon-yahoo", "dir-icon-yc", "dir-icon-yc-square", "dir-icon-yelp", "dir-icon-yen", "dir-icon-yoast", "dir-icon-youtube", "dir-icon-youtube-play", "dir-icon-youtube-square" ];
});