---
name: composio-setup
description: Create, reuse, inspect, or hand off an agent-owned Composio identity through agents.composio.dev so a Heyron Workspace OS agent can connect external apps/tools without making the human sign up first.
---

# Composio Setup

Use this when the user wants the agent to connect or act through external apps/tools such as Gmail, Calendar, Notion, Slack, GitHub, Linear, HubSpot, or similar services.

Core rule: the v0 default is agent-owned setup through `agents.composio.dev`. Do not send the customer to sign up for Composio first. Do not expose credentials.

Commands:
- `node skills/composio-setup/scripts/composio-agent.js status` — check for existing saved identity and verify it with `/api/whoami`.
- `node skills/composio-setup/scripts/composio-agent.js signup` — create an agent-owned Composio identity if none exists, then save it to `~/.composio/anonymous_user_data.json` with private permissions.
- `node skills/composio-setup/scripts/composio-agent.js cli` — fetch CLI readiness metadata without printing secret login commands.
- `node skills/composio-setup/scripts/composio-agent.js claim human@example.com` — optional admin handoff; only run after explicit user confirmation because it sends an external invite email.

User-facing behavior:
- If no Composio identity exists, ask permission before signup: “I can create a tool account for myself so I can connect apps. Want me to do that?”
- Explain that Composio is the tool bridge for connecting apps, not another thing they have to configure manually in v0.
- For app-specific OAuth, ask only for the app account/login action required by the Composio flow.
- Never paste Composio keys, API keys, user API keys, OAuth codes, or CLI login commands into chat or memory.
