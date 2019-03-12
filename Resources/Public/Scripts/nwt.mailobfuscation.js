/*
 * Copyright (C) 2014 networkteam GmbH
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the
 * Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

/**
 * Uncrypt an obfuscated email address
 *
 * Uses a slightly improved implementation from TYPO3 CMS.
 */
function linkTo_UnCryptMailto(s, offset) {

    function decryptCharcode(n, start, end, offset) {
        n += offset % (end - start + 1);
        if (offset > 0 && n > end) {
            n = start + (n - end - 1);
        } else if (offset < 0 && n < start) {
            n = end - (start - n - 1);
        }
        return String.fromCharCode(n);
    }

    function decryptString(enc, offset) {
        var dec = "";
        var len = enc.length;
        for (var i = 0; i < len; i++) {
            var n = enc.charCodeAt(i);
            if (n >= 0x2B && n <= 0x3A) {
                dec += decryptCharcode(n, 0x2B, 0x3A, offset);	// 0-9 . , - + / :
            } else if (n >= 0x40 && n <= 0x5A) {
                dec += decryptCharcode(n, 0x40, 0x5A, offset);	// A-Z @
            } else if (n >= 0x61 && n <= 0x7A) {
                dec += decryptCharcode(n, 0x61, 0x7A, offset);	// a-z
            } else {
                dec += enc.charAt(i);
            }
        }
        return dec;
    }

    location.href = 'mailto:' + decryptString(s, offset);
}

global.linkTo_UnCryptMailto = linkTo_UnCryptMailto;