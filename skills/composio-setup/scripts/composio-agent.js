#!/usr/bin/env node
'use strict';
const fs = require('fs');
const https = require('https');
const os = require('os');
const path = require('path');
const BASE = process.env.HEYRON_COMPOSIO_AGENT_BASE || 'https://agents.composio.dev';
const dataDir = path.join(os.homedir(), '.composio');
const dataFile = path.join(dataDir, 'anonymous_user_data.json');
const cmd = String(process.argv[2] || 'status').toLowerCase();
const arg = process.argv[3] || '';
function secureSavedFile() { try { fs.chmodSync(dataDir, 0o700); } catch {} try { if (fs.existsSync(dataFile)) fs.chmodSync(dataFile, 0o600); } catch {} }
function readSaved() { try { secureSavedFile(); return JSON.parse(fs.readFileSync(dataFile, 'utf8')); } catch { return null; } }
function save(data) { fs.mkdirSync(dataDir, { recursive: true, mode: 0o700 }); fs.writeFileSync(dataFile, JSON.stringify(data, null, 2) + '\n', { mode: 0o600 }); secureSavedFile(); }
function agentKeyFrom(data) { return data && (data.agent_key || data.agentKey); }
function redact(value) {
  if (Array.isArray(value)) return value.map(redact);
  if (value && typeof value === 'object') {
    const out = {};
    for (const [k, v] of Object.entries(value)) {
      out[k] = /key|token|secret/i.test(k) ? (v ? '[REDACTED]' : v) : redact(v);
    }
    return out;
  }
  if (typeof value === 'string') return value.replace(/(composio_agent_key|ak|uak)_[A-Za-z0-9_-]+/g, '$1_[REDACTED]');
  return value;
}
function request(method, pathname, body, key) {
  return new Promise((resolve, reject) => {
    const url = new URL(pathname, BASE);
    const payload = body ? JSON.stringify(body) : '';
    const headers = { 'accept': 'application/json,text/plain,*/*', 'user-agent': 'heyron-workspace-os/0.1' };
    if (payload) { headers['content-type'] = 'application/json'; headers['content-length'] = Buffer.byteLength(payload); }
    if (key) headers.authorization = 'Bearer ' + key;
    const req = https.request(url, { method, headers, timeout: 130000 }, res => {
      let text = '';
      res.setEncoding('utf8');
      res.on('data', chunk => { text += chunk; });
      res.on('end', () => {
        let parsed = text;
        try { parsed = JSON.parse(text); } catch {}
        resolve({ statusCode: res.statusCode, body: parsed, text });
      });
    });
    req.on('timeout', () => { req.destroy(new Error('request timeout')); });
    req.on('error', reject);
    if (payload) req.write(payload);
    req.end();
  });
}
async function whoami(key) { return request('GET', '/api/whoami', null, key); }
function print(obj) { console.log(JSON.stringify(redact(obj), null, 2)); }
(async () => {
  if (!['status','signup','cli','claim'].includes(cmd)) {
    console.error('Usage: composio-agent.js <status|signup|cli|claim email>');
    process.exit(2);
  }
  const saved = readSaved();
  const key = agentKeyFrom(saved);
  if (cmd === 'status') {
    if (!key) return print({ ok: true, configured: false, message: 'No saved Composio agent identity.' });
    const res = await whoami(key);
    return print({ ok: res.statusCode === 200, configured: res.statusCode === 200, statusCode: res.statusCode, whoami: res.body });
  }
  if (cmd === 'signup') {
    if (key) {
      const res = await whoami(key);
      if (res.statusCode === 200 && String(res.body.status || '').toUpperCase() === 'READY') return print({ ok: true, reused: true, whoami: res.body });
    }
    const res = await request('POST', '/api/signup', {}, null);
    if (res.statusCode !== 201 && res.statusCode !== 202) return print({ ok: false, statusCode: res.statusCode, error: res.body });
    if (res.body && res.body.agent_key) save(res.body);
    return print({ ok: res.statusCode === 201 || res.statusCode === 202, saved: Boolean(res.body && res.body.agent_key), signup: res.body, credentialPath: dataFile });
  }
  if (!key) return print({ ok: false, configured: false, message: 'No saved Composio identity. Run signup first after user approval.' });
  if (cmd === 'cli') {
    const res = await request('GET', '/api/cli', null, key);
    const rawAllowed = process.env.HEYRON_COMPOSIO_ALLOW_SECRET_OUTPUT === '1';
    if (rawAllowed) {
      console.log(res.text);
      return;
    }
    return print({ ok: res.statusCode === 200, statusCode: res.statusCode, message: res.statusCode === 200 ? 'CLI setup text fetched but not printed because it contains secret login credentials. Set HEYRON_COMPOSIO_ALLOW_SECRET_OUTPUT=1 only for an agent-executed install step; never paste it into chat or memory.' : res.body });
  }
  if (cmd === 'claim') {
    const email = String(arg || '').trim();
    if (!/^[^@s]+@[^@s]+.[^@s]+$/.test(email)) { console.error('Usage: composio-agent.js claim human@example.com'); process.exit(2); }
    const res = await request('POST', '/api/claim', { email }, key);
    return print({ ok: res.statusCode >= 200 && res.statusCode < 300, statusCode: res.statusCode, claim: res.body });
  }
})().catch(err => { console.error(err && err.message ? err.message : String(err)); process.exit(1); });
