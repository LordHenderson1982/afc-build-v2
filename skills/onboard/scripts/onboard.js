#!/usr/bin/env node
'use strict';
const fs = require('fs');
const path = require('path');
const root = process.cwd();
const today = new Date().toISOString().slice(0, 10);
function writeIfMissing(rel, text) {
  const p = path.join(root, rel);
  fs.mkdirSync(path.dirname(p), { recursive: true });
  if (!fs.existsSync(p)) fs.writeFileSync(p, text);
}
function append(rel, text) {
  const p = path.join(root, rel);
  fs.mkdirSync(path.dirname(p), { recursive: true });
  fs.appendFileSync(p, text);
}
['Human','Machine/Workflows','Machine/Captures','System','memory','skills'].forEach(d => fs.mkdirSync(path.join(root, d), { recursive: true }));
writeIfMissing('MEMORY.md', '# Long-Term Memory\n\n## Profile\n- Not initialized yet.\n');
writeIfMissing('memory/' + today + '.md', '# ' + today + '\n\n');
writeIfMissing('Machine/Inbox.md', '# Inbox\n\n');
writeIfMissing('Machine/Tasks.md', '# Tasks\n\n');
writeIfMissing('Human/Profile.md', '# Profile\n\n');
writeIfMissing('Human/Preferences.md', '# Preferences\n\n');
writeIfMissing('Human/Projects.md', '# Projects\n\n');
writeIfMissing('System/Diagnostics.md', '# Diagnostics\n\nLatest status: not run yet.\n');
append('memory/' + today + '.md', '\n## Onboard\n- Onboarding scaffold verified at ' + new Date().toISOString() + '.\n');
console.log(JSON.stringify({ ok: true, status: 'initialized', date: today }, null, 2));
