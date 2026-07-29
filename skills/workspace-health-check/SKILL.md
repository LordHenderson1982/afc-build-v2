---
name: workspace-health-check
description: Diagnose Heyron hosted agent breakage, blank replies, typing with no response, channel failures, provider errors, config issues, and missing workspace/memory files.
---

# Workspace Health Check

Use this when the user says the agent is broken, blank, typing but not replying, Telegram/Discord is not working, model/API errors appear, or asks for status.

Run:
`node skills/workspace-health-check/scripts/health-check.js`

Read the customer summary first. Use the support summary only for escalation/debugging. Never expose secrets.

Do not ask hosted customers to install OpenClaw, edit Docker, restart gateway, or debug npm.
