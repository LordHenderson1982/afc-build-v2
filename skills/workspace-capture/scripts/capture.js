#!/usr/bin/env node
'use strict';
const fs = require('fs');
const path = require('path');
const kind = (process.argv[2] || 'note').toLowerCase();
const text = process.argv.slice(3).join(' ').trim();
if (!text) { console.error('Usage: capture.js <note|task|decision|resource> <text>'); process.exit(2); }
const root = process.cwd();
const stamp = new Date().toISOString();
function append(rel, line) { const p = path.join(root, rel); fs.mkdirSync(path.dirname(p), { recursive: true }); if (!fs.existsSync(p)) fs.writeFileSync(p, '# ' + path.basename(rel, '.md') + '\n\n'); fs.appendFileSync(p, line); }
if (kind === 'task') append('Machine/Tasks.md', '- [ ] ' + text + ' _(captured ' + stamp + ')_\n');
else if (kind === 'decision') append('Human/Decisions.md', '- ' + text + ' _(captured ' + stamp + ')_\n');
else append('Machine/Inbox.md', '- **' + kind + '**: ' + text + ' _(captured ' + stamp + ')_\n');
console.log(JSON.stringify({ ok: true, captured: kind }, null, 2));
