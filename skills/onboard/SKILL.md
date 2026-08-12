---
name: onboard
description: Initialize a Heyron hosted Workspace OS: identity, user profile, memory, projects, optional channels, health baseline, and first useful action. Use when the user says /onboard, first run, set yourself up, or asks how to start.
---

# Onboard

Use this when the workspace is fresh or the user asks to set the agent up.

Workflow:
1. Confirm this is a Heyron-hosted workspace, not a self-hosting install.
2. Ask only the few missing questions: what to call the user, desired agent name/vibe, top 1-3 jobs, timezone if useful.
3. Explain files, memory, tools, channels, connected apps, context windows, and compaction in plain English.
4. Run `node skills/workspace-memory/scripts/init-memory.js` and `node skills/onboard/scripts/onboard.js` after gathering enough context, or run them immediately to repair missing scaffold files.
5. Offer channel setup with one clear choice: “Do you want Telegram, Discord, both, or neither right now?”
6. If Telegram: use `/telegram-setup` / `skills/telegram-setup/SKILL.md`.
7. If Discord: use `/discord-setup` / `skills/discord-setup/SKILL.md`.
8. If the user wants Gmail, Calendar, Notion, GitHub, Slack, or other app actions, use `/composio-setup` / `skills/composio-setup/SKILL.md` so the agent can create or reuse its own Composio identity through agents.composio.dev.
9. Run a lightweight health check if the user reports breakage or setup uncertainty.
10. End by doing one useful first action, not with a documentation dump.

Do not tell hosted customers to install OpenClaw, edit Docker, edit gateway config, or restart services.
