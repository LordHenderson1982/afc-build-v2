#!/usr/bin/env node
'use strict';
const fs = require('fs');
const path = require('path');
const text = process.argv.slice(2).join(' ').trim();
if (!text) { console.error('Usage: promote-memory.js <durable fact>'); process.exit(2); }
const file = path.join(process.cwd(), 'MEMORY.md');
if (!fs.existsSync(file)) fs.writeFileSync(file, '# Long-Term Memory\n\n');
fs.appendFileSync(file, '\n- ' + text.replace(/\s+/g, ' ') + '\n');
console.log(JSON.stringify({ ok: true, wrote: 'MEMORY.md' }, null, 2));
