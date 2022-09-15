/*
 * For Nepali Amount to Word Conversion
 * Dinesh Shrestaha
 * shresthadinesh.com.np

 *
 * function -> translate_nepali_num_into_words(number)
 * input -> number
 * return -> String
 */
// /*
function getBelowHundred(t) {
    return teens[t]
}
function translate_nepali_num_into_words(t) {
    if (isNaN(t) || '' == t)return "s[kof C)f /sd /fVg'xf]nf .";
    var n = '', e = 0;
    if (-1 != t.indexOf('.') && (number_parts = t.split('.'),
            t = number_parts[0], e = number_parts[1], decimal_tens = e.substring(0, 2).toString(),
        1 == decimal_tens.length && (decimal_tens = decimal_tens.toString() + '0')
            , decimal_words = teens[parseInt(decimal_tens)].toString() + ' k};f'),
        t.length > 13)return void alert('माफ गर्नुहाेला!! खरब भन्दा मथिका अङ्ककाे अक्षरमा परिबर्तन गर्न मिल्दैन ।');
    var r = Math.floor(t % 100);
    if (t.toString().length > 2)var s = t.toString().substring(t.toString().length - 3, t.toString().length - 2);
    var i = Math.floor(t % 1e5);
    i = i.toString(), i = i.substring(0, i.length - 3);
    var a = Math.floor(t % 1e7);
    a = a.toString(), a = a.substring(0, a.length - 5);
    var o = Math.floor(t % 1e9);
    o = o.toString(), o = o.substring(0, o.length - 7);
    var g = Math.floor(t % 1e11);
    g = g.toString(), g = g.substring(0, g.length - 9);
    var l = Math.floor(t % 1e13);
    return l = l.toString(), l = l.substring(0, l.length - 11)
        , l > 0 && (n += teens[l] + ' v/a'), g > 0 && (n += ' ' + teens[g] + ' c/a'), o > 0 && (n += ' ' + teens[o] + ' s/f]*')
        , a > 0 && (n += ' ' + teens[a] + ' nfv'), i > 0 && (n += ' ' + teens[i] + ' xhf/'), s > 0 && (n += ' ' + units[s] + ' ;o')
        , r > 0 && (n += ' ' + teens[r]), n += ' ?k}of¤', e > 0 && (n += ', ' + decimal_words), n
}


var units = ['z"Go', 'Ps', 'b"O{', 'tLg', 'rf/', 'kf¤r', '%', ';ft', 'cf&', 'gf}', 'bz'],
    teens = ['z"Go', 'Ps', 'b"O', 'tLg', 'rf/', 'kf¤r', '%', ';ft', 'cf&', 'gf}', 'bz', 'P#f/', 'afx|',
        't]x|', 'rf}w', 'kGw|', ';f]x|', ';q', 'c&f/', 'pGgfO;', 'aL;', 'PsfO;', 'afO;', 't]O;',
        'rf}aL;', 'kRrL;', '%AaL;', ';QfO;', 'c¶fO;', 'pgfGtL;', 'tL;', 'PstL;', 'aQL;', 't]QL;',
        'rf}tL;', 'k}+tL;', '%QL;', ';*tL;', 'c*tL;', 'pgfGrfnL;', 'rfnL;', 'PsrfnL;', 'aofnL;', 'qLrfnL;',
        'rf}jfln;', 'k}+tfln;', '%ofln;', ';TtrfnL;', 'c*rfnL;', 'pgfGrf;', 'krf;', 'PsfpGg', 'afpGg', 'qLkGg',
        'rf}jGg', 'kRrkGg', '%kGg', ';GtfpGg', 'cG&fpGg', 'pgfG;f¶L', ';f¶L', 'Ps;¶L', 'a};¶L', 'lq;¶L',
        'rf};¶L', 'k};¶L', '%};¶L', ';T;¶L', 'c&;¶L', 'pgfG;Q/L', ';Q/L', 'PsxQ/', 'axQ/', 'qLxQ/', 'rf}xQ/',
        'krxQ/', '%xQ/', ';txQ/', 'c&xQ/', 'pgfGc;L', 'c:;L', 'Psf;L', 'aof;L', 'qLof;L', 'rf}/f;L', 'krf;L',
        '%of;L', ';Qf;L', 'c&f;L', 'pgfGgAa]', 'gAa]', 'PsfGgAa]', 'aofGgAa]', 'qLofGgAa]', 'rf}/fGgAa]',
        'kGrfGgAa]', '%ofGgAa]', ';GtfGgAa]', 'cG&fGgAa]', 'pgfG;o'],

    tens = ['', ';o', 'aL;', 'tL;', 'rfnL;',
        'krf;', ';f&L', ';Q/L', 'c:;L', 'gAa]'];


// *//* * End
//  * For Nepali Amount to Word Conversion
//  *Dinesh k Shrestha
//
/*
 * For Nepali Amount to Word Conversion
 * Dinesh k Shrestha

 *
 * function -> translate_nepali_num_into_words(number)
 * input -> number
 * return -> String
 */

