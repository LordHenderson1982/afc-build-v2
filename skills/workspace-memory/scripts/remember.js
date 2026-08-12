#!/usr/bin/env node
'use strict';
const fs = require('fs');
const path = require('path');
const text = process.argv.slice(2).join(' ').trim();
if (!text) { console.error('Usage: remember.js <fact>'); process.exit(2); }
const today = new Date().toISOString().slice(0, 10);
const file = path.join(process.cwd(), 'memory', today + '.md');
fs.mkdirSync(path.dirname(file), { recursive: true });
if (!fs.existsSync(file)) fs.writeFileSync(file, '# ' + today + '\n\n');
fs.appendFileSync(file, '- ' + text.replace(/\s+/g, ' ') + '\n');
console.log(JSON.stringify({ ok: true, wrote: 'memory/' + today + '.md' }, null, 2));
