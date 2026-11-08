/* The Special text of a system as the server writes it (setSystemDataWindow): one string, lines joined with
 * <br>. The system card (system/SystemCard.js) draws it as a list instead of printing it flat, and shows only
 * the first lines until Shift is held. Pure, so it can be tested without a DOM.
 *
 * What it recognises - all of it conventions the ship files already use; nothing is asked of the PHP authors:
 * - blank lines (a Special that starts with <br>) are dropped;
 * - " - x" and tab-indented lines are sub-items of the line above;
 * - a line ending in ":" with nothing after it heads the sub-items below it;
 * - "Term: text", the term at most five words, is drawn with the term in bold ("Energy Web: -20 to hit...");
 * - a run of two or more short "term: value" sub-items is a table (the Vorlon prong modes, a Slicer's
 *   acceleration steps);
 * - WARNING/NOTE lines, lines that open with two capitalised words (SHOULD NOT ...) and lines that are mostly
 *   capitals are warnings;
 * - <b>..</b> stays bold (some jump drives write it); any other tag stays literal text.
 * Every line keeps its exact wording: nothing is reworded, merged or dropped except blank lines.
 */

const TERM = /^([A-Za-z0-9][A-Za-z0-9 ()'\/+.\-]{0,34}?):\s+(\S.*)$/;
const TERM_WORDS = 5;
const TABLE_CELL = 44;          //longest "value" a table row may hold; longer sub-items stay a list
export const BRIEF_LINES = 3;   //how many lines the card shows before "+N more - hold Shift"

export function parseRulesText(text) {
    const items = [];
    String(text == null ? '' : text).split(/<br\s*\/?>/i).forEach(raw => {
        const bare = raw.replace(/<\/?b>/gi, '');
        if (!bare.trim()) return;

        let kind = 'item';
        let line;
        if (/^\s*\t/.test(raw) || /^\s*[-–]\s+\S/.test(bare)) {
            kind = 'sub';
            line = raw.replace(/^\s*\t+\s*/, '').replace(/^\s*[-–]\s+/, '');
        } else {
            line = raw.trim();
        }

        const plain = line.replace(/<\/?b>/gi, '').trim();
        let term = null;
        let rest = null;
        const match = plain.match(TERM);
        if (match && match[1].trim().split(/\s+/).length <= TERM_WORDS) {
            term = match[1].trim();
            rest = match[2];
        }

        if (kind === 'item' && !term && /:\s*$/.test(plain) && plain.length <= 160) {
            kind = 'head';
        } else if (kind === 'item' && !term) {
            const letters = plain.replace(/[^A-Za-z]/g, '');
            const capitals = plain.replace(/[^A-Z]/g, '');
            if (/^(WARNING|NOTE|IMPORTANT)\b/.test(plain)
                || /^[A-Z]{3,}(?:\s+[A-Z]{2,})+\b/.test(plain)
                || (letters.length >= 12 && capitals.length / letters.length > 0.6)) {
                kind = 'warn';
            }
        }
        items.push({ kind, line, term, rest });
    });
    return items;
}

/* How many lines the brief card shows. A text only one line over is shown whole (a "+1 more" row costs as
   much as the line), and the brief never ends on a heading whose sub-items it hides. */
export function briefCount(items) {
    if (items.length <= BRIEF_LINES + 1) return items.length;
    let shown = BRIEF_LINES;
    while (shown > 1 && items[shown - 1].kind === 'head') shown--;
    return shown;
}

/* Lines in drawing order, with each run of short "term: value" sub-items folded into one table. */
export function rulesBlocks(items) {
    const blocks = [];
    for (let i = 0; i < items.length; i++) {
        if (items[i].kind !== 'sub') {
            blocks.push({ type: 'line', item: items[i] });
            continue;
        }
        const group = [];
        while (i < items.length && items[i].kind === 'sub') group.push(items[i++]);
        i--;
        if (group.length >= 2 && group.every(item => item.term && item.rest.length <= TABLE_CELL)) {
            blocks.push({ type: 'table', rows: group });
        } else {
            group.forEach(item => blocks.push({ type: 'line', item }));
        }
    }
    return blocks;
}

/* A line split into plain and bold runs: <b>..</b> pairs are honoured, any stray half of one is dropped. */
export function boldSegments(line) {
    const text = String(line == null ? '' : line);
    const segments = [];
    const pattern = /<b>([\s\S]*?)<\/b>/gi;
    let last = 0;
    let match;
    while ((match = pattern.exec(text)) !== null) {
        if (match.index > last) segments.push({ text: text.slice(last, match.index), bold: false });
        segments.push({ text: match[1], bold: true });
        last = pattern.lastIndex;
    }
    if (last < text.length) segments.push({ text: text.slice(last), bold: false });
    return segments
        .map(segment => ({ text: segment.text.replace(/<\/?b>/gi, ''), bold: segment.bold }))
        .filter(segment => segment.text !== '');
}
