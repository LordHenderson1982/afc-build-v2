#!/usr/bin/env node
'use strict';
const fs = require('fs');
const path = require('path');
const root = process.cwd();
const today = new Date().toISOString().slice(0, 10);
function writeIfMissing(rel, text) { const p = path.join(root, rel); fs.mkdirSync(path.dirname(p), { recursive: true }); if (!fs.existsSync(p)) fs.writeFileSync(p, text); }
writeIfMissing('MEMORY.md', '# Long-Term Memory\n\nCurated durable facts go here.\n');
writeIfMissing('memory/' + today + '.md', '# ' + today + '\n\n');
console.log(JSON.stringify({ ok: true, memory: 'ready', daily: 'memory/' + today + '.md' }, null, 2));
