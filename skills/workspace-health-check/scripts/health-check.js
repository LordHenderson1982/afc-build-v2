#!/usr/bin/env node
'use strict';
const fs = require('fs');
const http = require('http');
const path = require('path');
const { spawnSync } = require('child_process');
const root = process.cwd();
function exists(rel) { return fs.existsSync(path.join(root, rel)); }
function run(cmd, args) { const r = spawnSync(cmd, args, { encoding: 'utf8', timeout: 15000 }); return { status: r.status, stdout: r.stdout || '', stderr: r.stderr || '' }; }
function red(s) { return String(s || '').replace(/[A-Za-z0-9_-]{24,}.[A-Za-z0-9_-]{6,}.[A-Za-z0-9_-]{20,}/g, '[REDACTED_TOKEN]').replace(/sk-[A-Za-z0-9_-]+/g, '[REDACTED_KEY]'); }
function checkHttp(pathname) { return new Promise(resolve => { const req = http.get({ host: '127.0.0.1', port: 18789, path: pathname, timeout: 4000 }, res => { res.resume(); resolve(String(res.statusCode)); }); req.on('timeout', () => { req.destroy(); resolve('timeout'); }); req.on('error', e => resolve(e.code || 'error')); }); }
(async () => {
  const problems = [];
  const notes = [];
  const health = await checkHttp('/health');
  const openclawHealth = await checkHttp('/__openclaw/health');
  if (health !== '200' && openclawHealth !== '200') problems.push('local gateway health did not return HTTP 200');
  const cfg = run('openclaw', ['config', 'validate']);
  if (cfg.status !== 0) problems.push('OpenClaw config validation failed: ' + red(cfg.stderr || cfg.stdout).slice(0, 500));
  const channels = run('openclaw', ['channels', 'status', '--json', '--probe']);
  if (channels.status !== 0) notes.push('channel probe unavailable: ' + red(channels.stderr || channels.stdout).slice(0, 300));
  ['AGENTS.md','START_HERE.md','USER.md','SOUL.md','MEMORY.md','System/Where I Live.md'].forEach(rel => { if (!exists(rel)) problems.push('missing ' + rel); });
  if (!exists('memory')) problems.push('missing memory directory');
  const status = problems.length ? 'degraded' : 'ok';
  const result = {
    ok: problems.length === 0,
    status,
    customer_summary: problems.length ? 'I found something that needs attention: ' + problems[0] : 'The local workspace health check passed.',
    support_summary: { health, openclawHealth, problems, notes },
    recommended_next_step: problems.length ? 'Share the support summary with Heyron support if this affects the customer.' : 'No repair action needed from this health check.'
  };
  fs.mkdirSync(path.join(root, 'System'), { recursive: true });
  fs.writeFileSync(path.join(root, 'System', 'Diagnostics.md'), '# Diagnostics\n\n~~~json\n' + JSON.stringify(result, null, 2) + '\n~~~\n');
  console.log(JSON.stringify(result, null, 2));
})();
