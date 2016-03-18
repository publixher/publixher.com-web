var Regex101Colorizer = function () {
    "use strict";
    function e(e, r) {
        var t = S[e];
        return void 0 === t ? 1 : (t = t[r], void 0 === t ? 1 : t)
    }

    function r(e) {
        if (e.length > 1 && "\\" === e.charAt(0)) {
            var r = e.slice(1);
            if (/^c[\u0000-\u007F]$/.test(r))return 64 ^ r.charAt(1).toUpperCase().charCodeAt(0);
            if (/^x(?:[0-9A-Fa-f]{2}|{[0-9A-Fa-f]+})|u[0-9A-Fa-f]{4}$/.test(r))return parseInt(r.replace(/[^0-9a-f]/gi, ""), 16);
            if (/^(?:[0-3][0-7]{1,2}|[1-7][0-7]|0)$/.test(r))return parseInt(r, 8);
            if (1 === r.length && "cuxDdSsWwhHvVg".indexOf(r) > -1)return 0 / 0;
            if (1 === r.length)switch (r) {
                case"a":
                    return 7;
                case"b":
                    return 8;
                case"f":
                    return 12;
                case"n":
                    return 10;
                case"r":
                    return 13;
                case"t":
                    return 9;
                case"e":
                    return 27;
                default:
                    return r.charCodeAt(0)
            }
        }
        return "\\" !== e ? e.charCodeAt(0) : 0 / 0
    }

    function t(t, o, p, i, s) {
        var a, l, c, _ = g.exec(t), y = [], h = {rangeable: !1, type: regex_type.NONE}, _ = {
            opening: _[1],
            content: _[2],
            closing: _[3]
        };
        if (1 === e("posix", N) && /^\[:.*?:\]$/.test(t))a = {
            quantifiable: !1,
            contents: t,
            group_contents: t,
            children: y,
            error: !0,
            error_type: error_type.INVALID_POSIX_LOCATION,
            selected: o,
            type: regex_type.CHARCLASS,
            modifiers: p
        }, I = !0; else for (a = {
            quantifiable: !0,
            contents: _.opening,
            group_contents: _.opening,
            children: y,
            error: _.closing ? !1 : !0,
            error_type: error_type.INCOMPLETE_CHARCLASS,
            selected: o,
            type: regex_type.CHARCLASS,
            modifiers: p
        }; l = u.exec(_.content);) {
            if (c = l[0], "\\" === c.charAt(0))if (/^\\[cuxE]$/.test(c) && 1 === e(c.charAt(1), N))y.push({
                contents: c,
                error: !0,
                error_type: error_type.INCOMPLETE_TOKEN,
                type: regex_type.META
            }), h = {
                type: regex_type.META,
                rangeable: !1
            }; else if (/^\\[NlLUu]$/.test(c) && -1 === e(c.charAt(1), N))y.push({
                contents: c,
                error: !0,
                error_type: error_type.UNSUPPORTED_TOKEN,
                type: regex_type.META
            }), h = {type: regex_type.META, rangeable: !1}; else if (/^\\[dswb]$/i.test(c))y.push({
                contents: c,
                type: regex_type.META
            }), h = {rangeable: !1, type: regex_type.META}; else if ("\\" === c)y.push({
                contents: c,
                error: !0,
                error_type: error_type.INCOMPLETE_TOKEN,
                type: regex_type.META
            }); else if ("pP".indexOf(c.charAt(1)) > -1 && 1 === e(c.charAt(1), N)) {
                var d;
                d = 2 === c.length || "{" === c.charAt(2) && "}" !== c.slice(-1) ? error_type.INCOMPLETE_TOKEN : error_type.UNKNOWN_SCRIPT, y.push({
                    contents: c,
                    type: regex_type.META,
                    error: !E.test(c),
                    error_type: d
                }), h = {type: regex_type.META, rangeable: !1}
            } else if (/^\\Q[\s\S]*(?:\\E)?$/.test(c) && 1 === e(c.charAt(1), N)) {
                y.push({contents: c.substring(0, 2), type: regex_type.QUOTE});
                var f = c.substring(c.length - 2, c.length), x = c.length;
                i += "\\Q", "\\E" === f && (x -= 2);
                for (var A = c.substring(2, x), O = A.split(""), R = 0, b = O.length; b > R; R++)y.push({
                    contents: O[R],
                    type: regex_type.QUOTE_TEXT,
                    modifiers: a.modifiers
                });
                i += A, "\\E" === f && y.push({
                    contents: c.substring(c.length - 2, c.length),
                    type: regex_type.QUOTE
                }), h = {type: regex_type.QUOTE, rangeable: !1}
            } else if (/^\\\d/.test(c))y.push({
                contents: c,
                type: regex_type.OCTAL
            }), h = {rangeable: h.type !== regex_type.RANGE_HYPHEN, charCode: r(c)}; else if ("x" === c.charAt(1)) {
                var v = {contents: c, type: regex_type.HEX};
                if ("{" === c.charAt(2)) {
                    var C = c.slice(3, -1);
                    C.length > 4 && -1 === a.modifiers.indexOf("u") ? (v.error = !0, v.error_type = error_type.TOO_LARGE_OFFSET) : parseInt(C, 16) > parseInt("10FFFF", 16) ? (v.error = !0, v.error_type = error_type.UNICODE_OVERFLOW) : s && parseInt(C, 16) >= parseInt("0xd800", 16) && parseInt(C, 16) <= parseInt("0xdfff", 16) && (v.error = !0, v.error_type = error_type.SURROGATE)
                }
                y.push(v), h = {rangeable: h.type !== regex_type.RANGE_HYPHEN, charCode: r(c)}
            } else-1 !== "abfntrehvHV".indexOf(c.charAt(1)) ? (y.push({
                contents: c,
                type: regex_type.META
            }), h = {rangeable: h.type !== regex_type.RANGE_HYPHEN, charCode: r(c)}) : (y.push({
                contents: c,
                type: regex_type.ESCAPED_TEXT
            }), h = {
                rangeable: h.type !== regex_type.RANGE_HYPHEN,
                charCode: r(c)
            }); else if ("-" === c)if (h.rangeable) {
                var P = n(1, u, _.content);
                if (P) {
                    var U = r(P[0]);
                    if ("\\Q" === P[0].slice(0, 2) && P[0].length > 2) {
                        var m = P[0].slice(2);
                        "\\E" === m.slice(-2) && (m = m.slice(0, -2)), U = r(P[0].charAt(2))
                    }
                    y.push(/^(?:\[:[^:]+:\]|\\[GDdHhSsVvWwXAZzBRKCulLUNkN])$/.test(P[0]) ? {
                        contents: c,
                        type: regex_type.RANGE_HYPHEN,
                        error: !0,
                        error_type: error_type.INVALID_TEXT_RANGE
                    } : !isNaN(U) && h.charCode > U ? {
                        contents: c,
                        type: regex_type.RANGE_HYPHEN,
                        error: !0,
                        error_type: error_type.BAD_TEXT_RANGE
                    } : {contents: c, type: regex_type.RANGE_HYPHEN}), h = {
                        rangeable: !1,
                        type: regex_type.RANGE_HYPHEN
                    }
                } else y.push(_.closing ? {contents: c, type: regex_type.TEXT} : {
                    contents: c,
                    type: regex_type.RANGE_HYPHEN
                })
            } else y.push({contents: c, type: regex_type.TEXT, error: !1}), h = {
                type: regex_type.TEXT,
                rangeable: h.type !== regex_type.RANGE_HYPHEN
            }; else if (1 === e("posix", N) && /\[:\^?.*?:\]/.test(c)) {
                var L = {contents: c, type: regex_type.POSIX, error: !1};
                L.error = !/^\[:\^?(?:alnum|ascii|alpha|word|blank|cntrl|digit|graph|lower|print|punct|space|upper|xdigit):\]$/.test(c), L.error_type = error_type.INVALID_POSIX, y.push(L), h = {
                    type: regex_type.POSIX,
                    rangeable: !1
                }
            } else y.push({
                contents: c,
                type: regex_type.TEXT,
                error: c === T && 1 === e("check_delimiter", N),
                error_type: error_type.UNESCAPED_DELIMITER
            }), h = {
                rangeable: c.length > 1 || h.type !== regex_type.RANGE_HYPHEN,
                charCode: c.charCodeAt(c.length - 1)
            };
            a.group_contents += c;
            var S = y[y.length - 1];
            S.error && (I = !0), S.modifiers = a.modifiers
        }
        return a.error || (a.group_contents += "]"), a
    }

    function n(e, r, t) {
        for (var n, o = r.lastIndex, p = 0; e > p; p++)n = r.exec(t);
        return r.lastIndex = o, n
    }

    function o(r, n, o) {
        function s(e, r, t, n, o) {
            for (var p = t, i = 0, l = e.length; l > i; i++) {
                var c = e[i];
                if (!c.error) {
                    if (c.type === regex_type.TEXT) {
                        var _ = c.contents;
                        -1 !== v.modifiers.indexOf("x") && (_ = _.replace(/\s+/g, "")), p += _.length
                    } else if (c.type === regex_type.ESCAPED_TEXT)p += c.contents.length - 1; else if (c.type === regex_type.CHARCLASS)p++; else {
                        if (c.type !== regex_type.META && c.type !== regex_type.HEX && c.type !== regex_type.OCTAL) {
                            if (c.type === regex_type.LOOKAHEAD || c.type === regex_type.LOOKBEHIND)continue;
                            if (isGroupType(c.type)) {
                                p += s(c.children, -1, 0, c, !1);
                                continue
                            }
                            if (c.type === regex_type.ALTERNATOR) {
                                o || a(n, r, p), r = p, p = 0;
                                continue
                            }
                            continue
                        }
                        if (/^(?:[\^$]|\\[GAZzBbK])$/.test(c.contents))continue;
                        G && "\\C" === c.contents && a(n, 99, -1), p++
                    }
                    c.from && (p += parseInt(c.from) - 1)
                }
            }
            return o || a(n, r, p), p
        }

        function a(e, r, t) {
            void 0 !== e && r > -1 && r !== t && (e.error = !0, e.error_type = error_type.NOT_FIXED_WIDTH, I = !0)
        }

        function l(e, r) {
            r.modifiers = e.modifiers
        }

        var c, _, u, g, x, A = [{
            contents: "",
            quantifiable: !1,
            level: 0,
            group_contents: "",
            children: [],
            modifiers: n,
            lookbehind: !1,
            alternations: 0,
            maxalt: -1
        }], v = A[0], S = -1 !== n.indexOf("x"), F = -1 !== n.indexOf("U"), D = -1 !== n.indexOf("J"), G = -1 !== n.indexOf("u"), H = void 0, M = [], B = [], k = [], w = "";
        O = [], R = 0, I = !1, P = -1, m = "", b = n;
        for (var K = !1; c = y.exec(r);)if (_ = c[0], u = _.charAt(0), g = _.charAt(1), K) {
            if ("\n" === _ || "\r" === _)K = !1, v.group_contents += _; else if (/\n/.test(_)) {
                K = !1;
                var $ = _.split("\n");
                v.children[v.children.length - 1].contents += $[0], y.lastIndex = y.lastIndex - (_.length - $[0].length);
                continue
            }
            if (v.children[v.children.length - 1].contents += _, "\n" === _ || "\r" === _ || y.lastIndex === r.length) {
                /[\x01\x02\x03\x04\x06\x05\x07]/.test(w.slice(-1)) || (w += " ");
                var X = v.children[v.children.length - 1].contents;
                "\n" === X.slice(-1) && (X = X.slice(0, -1)), w += X + "", w += repeat("", v.level * U), v.forceSplit = !0
            }
        } else if (S && "#" === u && e("xmode", N)) {
            var Q = {contents: _, type: regex_type.COMMENT, quantifiable: !1};
            l(v, Q), v.children.push(Q), K = !0
        } else {
            if ("[" === u) {
                var q = o === c.index || o + 1 === c.index + _.length && "]" === r.charAt(c.index + _.length - 1) || "^" === _.charAt(1) && o === c.index + 1, V = t(_, q, v.modifiers, w, G);
                v.children.push(V), v.group_contents += V.group_contents, w += V.group_contents
            } else if ("|" === _) {
                var Y = {
                    unmatched: !1,
                    level: v.level,
                    children: [],
                    group_contents: "",
                    contents: _,
                    type: regex_type.ALTERNATOR,
                    quantifiable: !1,
                    error: !1,
                    lookbehind: v.lookbehind,
                    alternations: 0,
                    maxalt: -1
                };
                v.group_contents += _, v.alternations++, N !== FLAVOR.JS && (Y.error = -1 !== v.maxalt && v.alternations > v.maxalt, Y.error_type = error_type.TOO_MANY_ALTERNATIONS), Y.selected = v.selected || isGroupType(v.type) && o == c.index, Y.selected && (v.selected = !0), (void 0 === H && 0 === c.index || void 0 !== H && H.type === regex_type.ALTERNATOR) && (Y.error = !0, Y.error_type = v.level > 0 ? error_type.TRUNCATING_ALTERNATOR_GROUP : error_type.TRUNCATING_ALTERNATOR), v.type === regex_type.SUBPATTERN_GROUP ? (R = v.capturingGroupCount, v.currSubpatterns = []) : v.type === regex_type.CONDITIONAL && (w += "", w += repeat("", v.level * U)), v.children.push(Y)
            } else if ("^" === _ || "$" === _ || "." === _)v.group_contents += _, v.children.push({
                contents: _,
                type: regex_type.META,
                quantifiable: "." === _
            }); else if ("(" === u) {
                if (/^\(\?#[\S\s]*\)?$/.test(_)) {
                    v.group_contents += _;
                    var j = {
                        contents: _,
                        type: regex_type.GROUP_COMMENT,
                        quantifiable: !1,
                        error: ")" !== _.charAt(_.length - 1),
                        error_type: error_type.UNBALANCED_GROUP
                    };
                    l(v, j), v.children.push(j), j.error && (I = !0), w += _;
                    continue
                }
                if (x = /^\(\*[\w:]*\)?/.exec(_)) {
                    v.group_contents += _;
                    var z = 0 === c.index;
                    z || void 0 === H || H.type !== regex_type.VERB || H.error || (z = !0);
                    var J = {contents: _, type: regex_type.VERB, quantifiable: !1, error: !1};
                    ")" !== x[0].charAt(x[0].length - 1) ? (J.error = !0, J.error_type = error_type.INCOMPLETE_TOKEN) : /^\(\*(?:UTF16|UCP|NO_START_OPT|CR|LF|CRLF|ANYCRLF|ANY|BSR_(?:ANYCRLF|UNICODE)|ACCEPT|F(?:AIL)?|COMMIT|PRUNE(?::\w+)?|SKIP(?::\w+)?|MARK(?::\w+)?|THEN(?::\w+)?)\)/.test(_) ? /\(\*(?:NO_START_OPT|CR|LF|CRLF|ANYCRLF|ANY|BSR_ANYCRLF|BSR_UNICODE|UTF(?:8|16|32)|UCP)\)/.test(_) && !z && (J.error = !0, J.error_type = error_type.VERB_INVALID_LOCATION) : (J.error = !0, J.error_type = error_type.UNKNOWN_VERB), "(*UTF16)" === _ && (G = !0), v.children.push(J)
                } else if (/^\(\?(?:[0R]|[+-]?[1-9]+\d*|P[=>]\w+|&\w+)\)?/.test(_)) {
                    v.group_contents += _;
                    var Z = {
                        contents: _,
                        type: /^\(\?P=/.test(_) ? regex_type.NAME_BACKREF : regex_type.REFERNCE_GROUP,
                        quantifiable: !0,
                        error: ")" !== _.charAt(_.length - 1),
                        error_type: error_type.UNBALANCED_GROUP,
                        groupcount: R
                    };
                    v.lookbehind && (Z.error = !0, Z.error_type = error_type.BACKREF_IN_LOOKBEHIND), v.children.push(Z), M.push(Z)
                } else if (/^\(\?[imsxXUJ-]*\)$/.test(_))v.group_contents += _, v.modifiers = p(i(v.modifiers, _.substring(2, _.length - 1))), S = -1 !== v.modifiers.indexOf("x"), F = -1 !== v.modifiers.indexOf("U"), D = -1 !== v.modifiers.indexOf("J"), v.children.push({
                    contents: _,
                    type: regex_type.MODIFIERS,
                    quantifiable: !1,
                    modifiers: v.modifiers
                }); else {
                    var q = o >= c.index && o < c.index + _.length, W = {
                        unmatched: !0,
                        type: regex_type.GROUP,
                        quantifiable: !0,
                        level: v.level + 1,
                        contents: _,
                        group_contents: _,
                        children: [],
                        error: !1,
                        selected: q,
                        lookbehind: v.lookbehind,
                        alternations: 0,
                        maxalt: -1
                    };
                    if (l(v, W), "(" === _)W.type = regex_type.CAPTURING_GROUP, R++, W.ref_num = R, v.type === regex_type.SUBPATTERN_GROUP && (W.subpatterns = v.subpatterns, W.currSubpatterns = v.currSubpatterns, W.currSubpatterns.push(""), W.subpatterns.push("")); else if (_.length > 3 && /^\(\?[imsxXUJ-]*:$/.test(_))W.modifiers = p(i(v.modifiers, _.substring(2, _.length - 1))), S = -1 !== W.modifiers.indexOf("x"), F = -1 !== W.modifiers.indexOf("U"), D = -1 !== W.modifiers.indexOf("J"), W.type = regex_type.GROUP_MODIFIER; else if ("(?|" === _)W.type = regex_type.SUBPATTERN_GROUP, W.capturingGroupCount = R, W.subpatterns = [], W.currSubpatterns = []; else if (/\(\?(?:P?<\w+>|'\w+')$/.test(_)) {
                        var ee = _.substring(3, _.length - 1);
                        if ("<" === ee.charAt(0) ? (ee = ee.substring(1), W.type = regex_type.NAMED_P_GROUP) : W.type = regex_type.NAMED_GROUP, isNaN(ee.charAt(0)) || (W.error = !0, W.error_type = error_type.INVALID_GROUP_NAME), void 0 !== v.subpatterns) {
                            var re = v.currSubpatterns.length;
                            -1 === v.subpatterns.indexOf(ee) ? -1 === O.indexOf(ee) || D ? (O.push(ee), void 0 === v.subpatterns[re] || "" === v.subpatterns[re] ? (v.subpatterns.push(ee), v.currSubpatterns.push(ee)) : (W.error = !0, W.error_type = error_type.BAD_SUBPATTERN_INDEX_NAME)) : (W.error = !0, W.error_type = error_type.DUPLICATE_SUBPATTERN) : v.subpatterns[re] === ee ? v.currSubpatterns.push(ee) : -1 === O.indexOf(ee) || D ? (W.error = !0, W.error_type = error_type.BAD_SUBPATTERN_INDEX_NAME) : (W.error = !0, W.error_type = error_type.DUPLICATE_SUBPATTERN)
                        } else-1 === O.indexOf(ee) || D ? O.push(ee) : (W.error = !0, W.error_type = error_type.DUPLICATE_SUBPATTERN);
                        R++, W.ref_num = R, W.subpatterns = v.subpatterns, W.currSubpatterns = v.currSubpatterns
                    } else/\(\?<?[=!]$/.test(_) ? ("<" === _.charAt(2) ? (W.type = regex_type.LOOKBEHIND, W.lookbehind = !0, B.push(W)) : (W.type = regex_type.LOOKAHEAD, W.lookbehind = !1), W.quantifiable = !1) : "(?" === _ ? (W.type = N === FLAVOR.JS ? regex_type.GROUP : regex_type.CONDITIONAL, W.maxalt = 1, W.error = !0, W.error_type = error_type.INVALID_GROUP_STRUCTURE) : "(?(DEFINE)" === _ ? (W.maxalt = 0, W.quantifiable = !1, W.type = regex_type.DEFINE) : "(?>" !== _ && "(?:" !== _ && (W.error = !0, W.error_type = error_type.INVALID_GROUP_STRUCTURE);
                    S && v.type !== regex_type.CONDITIONAL && (w += "", w += repeat("", v.level * U)), k.push(w), w = "", v.children.push(W), w += W.contents, S && W.type !== regex_type.CONDITIONAL && (w += "", w += repeat("", W.level * U)), v = W, A.push(v)
                }
            } else if (")" === u)if (0 === v.level)v.group_contents += _, v.children.push({
                contents: ")",
                error: !0,
                error_type: error_type.UNBALANCED_PAREN
            }), P = y.lastIndex - 1; else {
                var te = A.pop();
                if (te.unmatched = !1, te.group_contents += ")", te.selected || (te.selected = o == c.index), v = A[A.length - 1], v.group_contents += te.group_contents, v.type === regex_type.CONDITIONAL)if (1 === v.children.length)if (d.test(te.group_contents)) {
                    if (v.condition = te, v.error = !1, te.children.length > 0 && te.children[0].type === regex_type.QUANTIFIER && (te.children[0].type = regex_type.TEXT, te.children[0].error = !1, I = te.children[0].global_error), te.type !== regex_type.LOOKAHEAD && te.type !== regex_type.LOOKBEHIND) {
                        isGroupType(te.type) && R--;
                        for (var ne = 0, oe = te.children.length; oe > ne; ne++)te.contents += te.children[ne].contents;
                        te.children = [], te.groupcount = R, M.push(te), te.unmatched = !0, te.contents += ")"
                    }
                } else W.maxalt = -1; else W.maxalt = -1;
                if (te.selected && te.alternations > 0)for (var ne = 0, oe = te.children.length; oe > ne; ne++) {
                    var pe = te.children[ne];
                    pe.type === regex_type.ALTERNATOR && (pe.selected = !0)
                }
                S && (w += "", w += repeat("", v.level * U)), w += ")", te.forceSplit || te.group_contents.length > L || te.alternations > 2 && te.group_contents.length > L / 3 || (w = w.replace(/[\x03\x01]/g, "")), w = k.pop() + w, S = -1 !== v.modifiers.indexOf("x"), F = -1 !== v.modifiers.indexOf("U"), S && (w += "", w += repeat("", v.level * U)), v.forceSplit = !0
            } else if ("\\" === u) {
                if (/^\d/.test(g)) {
                    var ie = +_.substring(1);
                    if (0 != g && ie > 0)if (10 > ie) {
                        var se = {
                            contents: _,
                            type: regex_type.NUM_BACKREF,
                            quantifiable: !0,
                            error: !1,
                            groupcount: R
                        };
                        v.lookbehind && (se.error = !0, se.error_type = error_type.BACKREF_IN_LOOKBEHIND), v.children.push(se), M.push(se)
                    } else {
                        if (R >= ie) {
                            var se = {contents: _, type: regex_type.NUM_BACKREF, quantifiable: !0, groupcount: R};
                            v.lookbehind && (se.error = !0, se.error_type = error_type.BACKREF_IN_LOOKBEHIND), v.children.push(se), M.push(se)
                        } else {
                            var ae = "", le = !1;
                            do {
                                if (/^(?:[0-3][0-7]{1,2}|[1-7][0-7]?)$/.test(ie)) {
                                    if (v.children.push({
                                            contents: "\\" + ie,
                                            type: regex_type.OCTAL,
                                            quantifiable: !0
                                        }), ae.length > 0) {
                                        var se = {contents: ae, type: regex_type.TEXT, quantifiable: !0};
                                        v.lookbehind && (se.error = !0, se.error_type = error_type.BACKREF_IN_LOOKBEHIND), v.children.push(se)
                                    }
                                    le = !0;
                                    break
                                }
                                ae = /[0-9]$/.exec(ie)[0] + ae, ie = Math.floor(ie / 10)
                            } while (ie > 0);
                            le || v.children.push({
                                contents: _,
                                type: regex_type.NUM_BACKREF,
                                quantifiable: !1,
                                error: !0,
                                error_type: error_type.INVALID_BACKREF
                            })
                        }
                        for (var ae = "", ie = +_.substring(1); ie > R;) {
                            if (0 === Math.floor(ie / 10)) {
                                /^(?:[0-3][0-7]{1,2}|[1-7][0-7])/.test(_) && (ie = 0);
                                break
                            }
                            ae = /[0-9]$/.exec(ie)[0] + ae, ie = Math.floor(ie / 10)
                        }
                    } else v.children.push({contents: _, type: regex_type.OCTAL, quantifiable: !0})
                } else if ("\\" === _)v.children.push({
                    contents: _,
                    type: regex_type.TEXT,
                    quantifiable: !1,
                    error: !0,
                    error_type: error_type.INCOMPLETE_TOKEN
                }); else if (-1 !== "GDdHhSsVvWwaefnrtXAZzBbRKCulLUcxNkg0pPEQN".indexOf(g) && 0 !== e(g, N)) {
                    var ce = {contents: _, type: regex_type.META, quantifiable: !0};
                    if (-1 !== "uLlUE".indexOf(g) && -1 === e(g, N))ce.error = !0, ce.error_type = error_type.UNSUPPORTED_TOKEN; else if (/^\\[cxkguE]$/.test(_))ce.error = !0, ce.error_type = error_type.INCOMPLETE_TOKEN; else if ("GAZzBbK".indexOf(g) > -1)ce.quantifiable = !1; else if ("x" === g) {
                        if (ce.type = regex_type.HEX, "{" === _.charAt(2)) {
                            var _e = _.slice(3, -1);
                            _e.length > 4 && -1 === v.modifiers.indexOf("u") ? (ce.error = !0, ce.error_type = error_type.TOO_LARGE_OFFSET) : parseInt(_e, 16) > parseInt("10FFFF", 16) ? (ce.error = !0, ce.error_type = error_type.UNICODE_OVERFLOW) : G && parseInt(_e, 16) >= parseInt("0xd800", 16) && parseInt(_e, 16) <= parseInt("0xdfff", 16) && (ce.error = !0, ce.error_type = error_type.SURROGATE)
                        }
                    } else if ("pP".indexOf(g) > -1)ce.error = !E.test(_), ce.error_type = 2 === _.length || "{" === _.charAt(2) && "}" !== _.slice(-1) ? error_type.INCOMPLETE_TOKEN : error_type.UNKNOWN_SCRIPT; else if ("Q" === g) {
                        ce.type = regex_type.QUOTE, ce.contents = "\\Q", w += "\\Q", v.children.push(ce);
                        var ye, ue = _.substring(2), ge = _.substring(_.length - 2, _.length);
                        if ("\\E" === ge) {
                            for (var he = ue.slice(0, -2).split(""), de = 0, fe = he.length; fe > de; de++) {
                                var Ee = {contents: he[de], type: regex_type.QUOTE_TEXT, quantifiable: !1};
                                l(v, Ee), v.children.push(Ee)
                            }
                            var xe = {contents: "\\E", type: regex_type.QUOTE, quantifiable: !1};
                            l(v, xe), v.children.push(xe), w += ue.slice(0, -2)
                        } else if (/[^\\](?:\\\\)*\\$/.test(ue)) {
                            ye = {
                                contents: ue.slice(0, -1),
                                type: regex_type.QUOTE_TEXT,
                                quantifiable: !1
                            }, v.children.push(ye);
                            var Ae = {
                                contents: "\\",
                                type: regex_type.TEXT,
                                quantifiable: !1,
                                error: !0,
                                error_type: error_type.INCOMPLETE_TOKEN
                            };
                            l(v, Ae), v.children.push(Ae)
                        } else for (var he = ue.split(""), de = 0, fe = he.length; fe > de; de++) {
                            var Ee = {contents: he[de], type: regex_type.QUOTE_TEXT, quantifiable: !1};
                            l(v, Ee), v.children.push(Ee)
                        }
                        l(v, ce)
                    } else"k" === g || "g" === g ? (ce.error = !f.test(_), ce.error_type = error_type.INCOMPLETE_TOKEN, ce.groupcount = R, M.push(ce), ce.error || (ce.type = "k" === g ? regex_type.NAME_BACKREF : /^\\g(?:{-?\d+}|\d+)$/.test(_) ? regex_type.NUM_BACKREF : /^\\g{\w+}$/.test(_) ? regex_type.NAME_BACKREF : regex_type.REFERNCE_GROUP, v.lookbehind && (ce.error = !0, ce.error_type = error_type.BACKREF_IN_LOOKBEHIND))) : "N" === g && (ce.error = _.length > 2, ce.error_type = error_type.UNSUPPORTED_TOKEN);
                    "Q" !== g && v.children.push(ce)
                } else v.children.push({
                    contents: _,
                    type: regex_type.ESCAPED_TEXT,
                    quantifiable: !0,
                    error: -1 !== v.modifiers.indexOf("X"),
                    error_type: error_type.X_MODE
                });
                v.group_contents += _
            } else if (h.test(_)) {
                v.group_contents += _;
                var Te = {
                    contents: _,
                    type: regex_type.QUANTIFIER,
                    error: !1,
                    selected: !1,
                    quantifiable: !1,
                    prev: H,
                    Umode: F
                };
                if (H)if (H.type !== regex_type.QUANTIFIER && H.type !== regex_type.GROUP_QUANTIFIER || void 0 === H.prev || void 0 === H.prev.quantifier)if (H.error)Te.error = !0, Te.error_type = error_type.NOT_QUANTIFIABLE; else if (H.quantifiable) {
                    var Ne = /^{([0-9]+)(,([0-9]*))?/.exec(_);
                    if (Ne && (H.from = Ne[1], H.to = Ne[2] ? Ne[3] : Ne[1]), Ne && (+Ne[1] > 65535 || Ne[3] && +Ne[3] > 65535))Te.error = !0, Te.error_type = error_type.TOO_LARGE_QUANTIFIER; else if (Ne && Ne[3] && +Ne[1] > +Ne[3])Te.error = !0, Te.error_type = error_type.BAD_QUANTIFIER_RANGE; else if (v.lookbehind && !/^{(\d+)(?:,\1)?}$/.test(_))Te.error = !0, Te.error_type = error_type.LOOKBEHIND_QUANTIFIER; else {
                        if (isGroupType(H.type) && (Te.type = regex_type.GROUP_QUANTIFIER, Te.level = H.level), (isGroupType(H.type) || H.type === regex_type.CHARCLASS) && (Te.selected = H.selected || o >= c.index && o < c.index + _.length, Te.selected && (H.selected = !0, H.alternations > 0)))for (var ne = 0, Oe = H.children.length; Oe > ne; ne++) {
                            var pe = H.children[ne];
                            pe.type === regex_type.ALTERNATOR && (pe.selected = !0)
                        }
                        H.quantifier = _, H.quantifier_token = Te, H !== v && H.group_contents && (H.group_contents += _)
                    }
                } else Te.error = !0, Te.error_type = error_type.NOT_QUANTIFIABLE; else H.type === regex_type.GROUP_QUANTIFIER && (Te.type = regex_type.GROUP_QUANTIFIER, Te.level = H.level, H.prev.group_contents += _), Te.error = H.error || !h.test(H.contents + _), Te.error_type = H.error_type || error_type.NOT_QUANTIFIABLE, Te.selected = H.selected, H.prev.quantifier += _, H.Umode = F; else Te.error = !0, Te.error_type = error_type.NOT_QUANTIFIABLE;
                v.children.push(Te), H && isGroupType(H.type) && (Te.modifiers = H.modifiers)
            } else v.group_contents += _, v.children.push({
                contents: _,
                type: regex_type.TEXT,
                quantifiable: !0,
                error: _ === T && (1 === e("check_delimiter", N) || 2 === e("check_delimiter", N)),
                error_type: error_type.UNESCAPED_DELIMITER
            });
            var Re = v.children[v.children.length - 1];
            void 0 !== Re ? (isGroupType(Re.type) || Re.type === regex_type.CHARCLASS || (S && Re.type !== regex_type.ESCAPED_TEXT && Re.type !== regex_type.QUOTE_TEXT ? (Re.type === regex_type.GROUP_QUANTIFIER ? (H.type === regex_type.GROUP_QUANTIFIER ? w = w.slice(0, -1) : w += "", w += Re.contents + "") : w += Re.contents.replace(/\s/g, ""), Re.type === regex_type.ALTERNATOR && (w += "", w += repeat("", v.level * U))) : w += Re.contents), (S && !/^\s*$/.test(_) || !S) && (H = Re), void 0 === Re.modifiers && l(v, Re), Re.error && (Re.global_error = I, I = !0)) : H = void 0
        }
        for (; A.length > 1;) {
            var Ie = A.pop();
            Ie.error = !0, Ie.error_type = error_type.UNBALANCED_GROUP, I = !0
        }
        for (var ne = 0, be = M.length; be > ne; ne++) {
            var ve = M[ne];
            if (!ve.error && "(R)" !== ve.group_contents && "(?R)" !== ve.contents && "(?0)" !== ve.contents) {
                var Z = ve.contents.replace(/^(?:\\[gk]|\((?:R|\?P[=>]))|[^-+\w]/g, ""), ie = parseInt(Z);
                if (ve.reference = Z, "-" === Z.charAt(0) || "+" === Z.charAt(0)) {
                    var Ce = ie + parseInt(ve.groupcount);
                    ie = Ce, ve.reference = Ce, "-" === Z.charAt(0) && ve.reference++, Z = Ce
                }
                (Z != ie && -1 === O.indexOf(Z) || (ie > R || 0 > ie) && -1 === O.indexOf(Z)) && (ve.error = !0, ve.error_type = error_type.NON_EXISTENT_REFERENCE, I = !0, ve.quantifier && (ve.quantifier_token.error = !0, ve.quantifier_token.error_type = error_type.NOT_QUANTIFIABLE))
            }
        }
        for (; k.length > 0;)w = k.pop() + w;
        m = w;
        for (var ne = 0, be = B.length; be > ne; ne++)s(B[ne].children, -1, 0, B[ne], !0);
        return C = !0, A
    }

    function p(e) {
        return e.split("-")[0]
    }

    function i(e, r) {
        function t(e, r) {
            for (var t = r.split(""), n = e; t.length;)n = n.split(t.pop()).join("");
            return n
        }

        var n = "", o = "", p = "", i = "", s = (e || "").split("-"), a = (r || "").split("-");
        n = s[0], o = a[0], s.length > 1 && (p = s[1]), a.length > 1 && (i = a[1]);
        var l = t(n, i), c = t(p, o);
        return o += l, i += c, i ? o + "-" + i : o
    }

    function s() {
        switch (N) {
            case FLAVOR.JS:
                y = /\[\^?(?:[^\\\]]+|\\[\S\s]?)*]?|\\(?:0(?:[0-3][0-7]{0,2}|[4-7][0-7]?)?|[1-9][0-9]*|x[0-9A-Fa-f]{2}|u[0-9A-Fa-f]{4}|c[A-Za-z]|[\S\s]?)|\((?:\?[:=!]?)?|(?:[?*+]|\{[0-9]+(?:,[0-9]*)?\})\??|[^.?*+^${[()|\\/\r\n]+(?![+*?]|{[0-9]+(?:,[0-9]*)?})|[^.?*+^${[()|\\/\r\n]|[\S\s]/g, u = /[^\\-]+|-|\\(?:[0-3][0-7]{0,2}|[4-7][0-7]?|x[0-9A-Fa-f]{2}|u[0-9A-Fa-f]{4}|c[A-Za-z]|[\S\s]?)/g, g = /^(\[\^?)((?:[^\\\]]+|\\[\S\s]?)*)(]?)$/, h = /^(?:[?*+]|\{[0-9]+(?:,[0-9]*)?\})\??$/, x = /^[img]*$/;
                break;
            case FLAVOR.PYTHON:
                y = /\[\^?]?(?:[^\\\]]+|\\[\S\s]?)*]?|\\(?:(?:0[0-3][0-7]{0,2}|[1-3][0-7][0-7])|[1-9][0-9]*|x[0-9A-Fa-f]{2}|[\S\s]?)|\((?:\?(?:(?:<[!=]|[!:=])!?|P<\w+>|P=\w+\)?|#[^\)]+\)?|[imsux]+\))?)?|(?:[?*+]|\{[0-9]+(?:,[0-9]*)?\})\??|[^#.?*+^${[()|\\/\r\n]+(?![+*?]|{[0-9]+(?:,[0-9]*)?})|[^#.?*+^${[()|\\/\r\n]|[\S\s]/g, u = /[^\\-]+|-|\\(?:0[0-3][0-7]{0,2}|[1-3][0-7][0-7]|x[0-9A-Fa-f]{2}|[\S\s]?)/g, g = /^(\[\^?)(\]?(?:[^\\\]]+|\\[\S\s]?)*)(]?)$/, h = /^(?:[?*+]|\{[0-9]+(?:,[0-9]*)?\})\??$/, d = /^\(\w+\)$/, x = /^[imgsxu]*$/;
                break;
            default:
                y = new RegExp("\\[\\^?(?:\\\\Q\\\\E)?]?(?:\\\\Q(?:(?!\\\\E)[\\s\\S])*(?:\\\\E)?|\\[:\\^?[^:]*:\\]|[^\\\\\\]]|\\\\[\\S\\s]?)*]?|\\\\(?:N(?:{\\w*}?)?|k(?:'(?:\\w+'?)?|<(?:\\w+>?)?|{(?:\\w+}?)?)|g(?:[1-9]\\d*|{(?:-?[0-9]+}?|(?:\\w+}?)?)|<(?:[+-]?\\d+>?|(?:\\w+>?)?)|'(?:[+-]?\\d+'?|(?:\\w+'?)?))|Q(?:(?!\\\\E)[\\s\\S])*(?:\\\\E)?|[0-3][0-7]{1,2}|[1-7][0-7]|0|[1-9][0-9]{0,2}|x(?:[0-9A-Fa-f]{2}|{[0-9A-Fa-f]+})?|c[\\u0000-\\u007F]|[pP](?:C[cfnos]?|L[lmotu&]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?|\\{\\^?(?:\\w+\\}?)?)|[\\S\\s]?)|\\((?:\\?(?:\\(DEFINE\\)|<?[=!]|'(?:\\w+'?)?|P?<(?:\\w+>?)?|(?:[imsxXUJ-]*:|[imsxXUJ-]*\\))|(?:P[=>]\\w*|[0R]|[+-]?[1-9]\\d*)\\)?|\\#[^)]*\\)?|[!|>:]|&\\w*\\)?)?|\\*[:\\w]+\\)?)?|(?:[?*+]|\\{[0-9]+(?:,[0-9]*)?\\})[+?]?|[^#.?*+^{$[()\\\\" + T + "|\\r\\n](?!(?:[?*+]|\\{[0-9]+(?:,[0-9]*)?\\})[+?]?)|[\\S\\s]", "g"), u = /\\(?:Q(?:(?!\\E)[\s\S])*(?:\\E)?|[0-3][0-7]{1,2}|[1-7][0-7]|0|x(?:[0-9A-Fa-f]{2}|\{[0-9A-Fa-f]+\})?|c[\x00-\x7F]|[pP](?:(?:C[cfnos]?|L[lmotu&]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?)|\{\^?(?:\w+\}?)?)|[\S\s]?)|\[:\^?[^:]*:\]|[\S\s]/g, g = /^(\[\^?)((?:\\Q\\E)?\]?(?:\\Q(?:(?!\\E)[\s\S])*(?:\\E)?|\[:\^?[^:]*:\]|[^\\\]]+|\\[\S\s]?)*)(]?)$/, h = /^(?:[?*+]|\{[0-9]+(?:,[0-9]*)?\})[+?]?$/, d = /^\((?:\?<?[=!]|(?:(?:R(?:[1-9]\d*|&\w+)?|[1-9]\d*|[+-]?[1-9]\d*|\w+|'\w+'|<\w+>)\)$))/, f = /\\(?:k(?:'\w+'|<\w+>|{\w+})|g(?:[1-9]\d*|{(?:-?[0-9]+|\w+)}|<(?:[+-]?[0-9]+|\w+)>|'(?:[+-]?[0-9]+|\w+)'))/, x = /^[xXsiuUmgADJ]*$/, E = /^\\[pP](?:C[cfnos]?|L[lmotu&]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?|{\^?(?:C[cfnos]?|L[lmotu&]?|M[cen]?|N[dlo]?|P[cdefios]?|S[ckmo]?|Z[lps]?|Arabic|Armenian|Avestan|Balinese|Bamum|Batak|Bengali|Bopomofo|Brahmi|Braille|Buginese|Buhid|Canadian_Aboriginal|Carian|Chakma|Cham|Cherokee|Common|Coptic|Cuneiform|Cypriot|Cyrillic|Deseret|Devanagari|Egyptian_Hieroglyphs|Ethiopic|Georgian|Glagolitic|Gothic|Greek|Gujarati|Gurmukhi|Han|Hangul|Hanunoo|Hebrew|Hiragana|Imperial_Aramaic|Inherited|Inscriptional_Pahlavi|Inscriptional_Parthian|Javanese|Kaithi|Kannada|Katakana|Kayah_Li|Kharoshthi|Khmer|Lao|Latin|Lepcha|Limbu|Linear_B|Lisu|Lycian|Lydian|Malayalam|Mandaic|Meetei_Mayek|Meroitic_Cursive|Meroitic_Hieroglyphs|Miao|Mongolian|Myanmar|New_Tai_Lue|Nko|Ogham|Old_Italic|Old_Persian|Old_South_Arabian|Old_Turkic|Ol_Chiki|Oriya|Osmanya|Phags_Pa|Phoenician|Rejang|Runic|Samaritan|Saurashtra|Sharada|Shavian|Sinhala|Sora_Sompeng|Sundanese|Syloti_Nagri|Syriac|Tagalog|Tagbanwa|Tai_Le|Tai_Tham|Tai_Viet|Takri|Tamil|Telugu|Thaana|Thai|Tibetan|Tifinagh|Ugaritic|Vai|Yi)})$/
        }
    }

    function a(e, r, t) {
        e[r] || (e[r] = ""), e[r] = t + e[r]
    }

    function l(e, r, t) {
        e[r] || (e[r] = ""), e[r] += t
    }

    function c(e, r, t, n, o, p) {
        function i(e, r, t) {
            return colorizeRegex() ? r + e + t : e
        }

        function s(e, r) {
            return i(e, '<b class="err" ' + r + ">", "</b>")
        }

        function a(e, r, t) {
            var n = "g" + ((r.level - 1) % PAREN_COUNT + 1);
            r.selected && (n += " selected_paren");
            var o;
            return o = r.ref_num ? '<b class="' + n + '" ' + t + ' data-groupNum="' + r.ref_num + '">' : '<b class="' + n + '" ' + t + ">", i(e, o, "</b>")
        }

        for (var _ = 0, y = e.length; y > _; _++) {
            var u = e[_], g = u.contents;
            if (n || t.injected)g = white_space(escapeHtml(g)); else if (t.currentPos += u.contents.length, t.currentPos > t.pos) {
                var h = g.length - (t.currentPos - t.pos);
                g = white_space(escapeHtml(g.slice(0, h))) + '<div class="caret">&nbsp;</div>' + white_space(escapeHtml(g.slice(h))), t.injected = n = !0
            } else g = white_space(escapeHtml(g));
            var d = 'data-tooltip="' + u.type + ";" + u.error + ";" + u.error_type + ";" + u.modifiers + '"';
            if (r && (u.selected = !1), u.error && (g = s(g, d)), isGroupType(u.type)) {
                if (l(p, o.pos, a(g, u, d)), o.pos += u.contents.length, c(u.children, r, t, n, o, p), !u.unmatched) {
                    var f = ")";
                    n || t.injected || t.currentPos != t.pos || (f = '<div class="caret">&nbsp;</div>)', t.injected = !0), u.error ? l(p, o.pos, s(f, d)) : l(p, o.pos, a(f, u, d)), t.currentPos++, o.pos++
                }
                u.selected && u.ref_num && highlightInteraction() && $("#richtext_test_colors").find("[data-groupNum=" + u.ref_num + "]").addClass("selected_paren_test")
            } else switch (u.type) {
                case regex_type.CHARCLASS:
                    if (u.selected ? l(p, o.pos, i(g, '<i class="selected_paren" ' + d + ">", "</i><i>")) : l(p, o.pos, i(g, "<i " + d + ">", "</i><i>")), o.pos += u.contents.length, c(u.children, r, t, n, o, p), colorizeRegex() && (u.selected ? l(p, o.pos, '</i><i class="selected_paren" ' + d + ">") : l(p, o.pos, "</i><i " + d + ">")), !u.error) {
                        var E = "]";
                        n || t.injected || t.currentPos != t.pos || (E = '<div class="caret">&nbsp;</div>]', t.injected = !0), l(p, o.pos, E), t.currentPos++, o.pos++
                    }
                    colorizeRegex() && l(p, o.pos, "</i>");
                    break;
                case regex_type.QUOTE_TEXT:
                case regex_type.TEXT:
                    l(p, o.pos, g);
                    break;
                case regex_type.ESCAPED_TEXT:
                    l(p, o.pos, i(g, '<b class="et" ' + d + ">", "</b>"));
                    break;
                case regex_type.GROUP_COMMENT:
                case regex_type.COMMENT:
                    l(p, o.pos, i(g, '<b class="comment" ' + d + ">", "</b>"));
                    break;
                case regex_type.VERB:
                case regex_type.MODIFIERS:
                case regex_type.REFERNCE_GROUP:
                    l(p, o.pos, i(g, "<u " + d + ">", "</u>"));
                    break;
                case regex_type.ALTERNATOR:
                    u.level > 0 ? l(p, o.pos, a(g, u, d, "")) : l(p, o.pos, i(g, "<b " + d + ">", "</b>"));
                    break;
                case regex_type.GROUP_QUANTIFIER:
                    l(p, o.pos, a(g, u, d, ""));
                    break;
                default:
                    l(p, o.pos, i(g, "<b " + d + ">", "</b>"))
            }
            isGroupType(u.type) || u.type === regex_type.CHARCLASS || (o.pos += u.contents.length)
        }
    }

    var y, u, g, h, d, f, E, x, A = {}, T = "/", N = FLAVOR.PCRE, O = [], R = 0, I = !1, b = "", v = {}, C = !1, P = -1, U = 2, m = "", L = 50, S = {
        c: {
            1: 1,
            2: 1,
            3: 0
        },
        A: {1: 1, 2: 0, 3: 1},
        k: {1: 1, 2: 0, 3: 0},
        g: {1: 1, 2: 0, 3: 0},
        G: {1: 1, 2: 0, 3: 0},
        X: {1: 1, 2: 0, 3: 0},
        C: {1: 1, 2: 0, 3: 0},
        K: {1: 1, 2: 0, 3: 0},
        p: {1: 1, 2: 0, 3: 0},
        P: {1: 1, 2: 0, 3: 0},
        u: {1: -1, 2: 1, 3: 0},
        z: {1: 1, 2: 0, 3: 0},
        Z: {1: 1, 2: 0, 3: 1},
        v: {1: 1, 2: 1, 3: 1},
        V: {1: 1, 2: 0, 3: 0},
        h: {1: 1, 2: 0, 3: 0},
        H: {1: 1, 2: 0, 3: 0},
        R: {1: 1, 2: 0, 3: 0},
        L: {1: -1, 2: 0, 3: 0},
        l: {1: -1, 2: 0, 3: 0},
        U: {1: -1, 2: 0, 3: 0},
        N: {1: -1, 2: 0, 3: 0},
        Q: {1: 1, 2: 0, 3: 0},
        E: {1: 1, 2: 0, 3: 0},
        define: {1: 1, 2: -1, 3: -1},
        conditional: {1: 1, 2: -1, 3: 1},
        check_delimiter: {3: -1, 1: 1, 2: 2},
        posix: {1: 1, 2: 0, 3: -1},
        xmode: {1: 1, 2: 0, 3: 1}
    };
    return A.colorize = function (e, r, t, n, o, p, i) {
        var s = A.getTokens(e, r, t, n, o);
        return A.makeHtml(s[0].children, !1, !p, i)
    }, A.getTokens = function (e, r, t, n, p) {
        return void 0 !== n && (T = n), void 0 !== p && (N = p), s(), v = o(e, r, t)
    }, A.hasError = function () {
        return I || _.uniq(b).length < b.split("").length || x && !x.test(b)
    }, A.hasUnmatchedParen = function () {
        return P
    }, A.getCaptureData = function () {
        return {group_count: R, subpatterns: O}
    }, A.justParsed = function () {
        return C ? (C = !1, !0) : !1
    }, A.returnTokens = function () {
        return v
    }, A.makeHtml = function (e, r, t, n) {
        var o = {currentPos: 0, injected: t, pos: getCaretPosition(document.getElementById("regex"))};
        !t && highlightInteraction() && $("#richtext_test_colors span").removeClass("selected_paren_test");
        var p = {pos: 0}, i = {};
        c(e, r, o, t, p, i), n && void 0 !== debugHtml && Object.keys(debugHtml).forEach(function (e) {
            a(i, e, debugHtml[e])
        });
        var s = "";
        if (Object.keys(i).forEach(function (e) {
                s += i[e]
            }), !t) {
            if ("" === s)return '<div class="caret">&nbsp;</div>';
            o.injected || (s += '<div class="caret">&nbsp;</div>')
        }
        return s
    }, A.formatRegex = function () {
        return m = m.replace(/^ +/, ""), m = m.replace(/([\x01\x02]*)([\x03\x04]*)\x05(.+?)\x06/g, "$3$1$2"), m = m.replace(/[\x01\x02]+[\x03\x04]*\|/g, "|"), m = m.replace(/^(?:[\x07\x01]\x03*|\x02\x04*)|(?:[\x01\x07]\x03*|\x02\x04*)$/g, ""), m = m.replace(/([\x01\x02\x07])[\x03\x04]*[\x01\x02]/g, "$1"), m = m.replace(/[\x01\x02\x07]/g, "\n").replace(/[\x03\x04]/g, " ")
    }, A
}();