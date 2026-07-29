#!/usr/bin/env node
'use strict';
const { spawnSync } = require('child_process');
const args = process.argv.slice(2);
const r = spawnSync('./heyron-channel-setup', args, { stdio: 'inherit' });
process.exit(r.status == null ? 1 : r.status);
