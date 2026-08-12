# Composio Connected Tools

Workspace OS v0 uses Composio as the default bridge for external app/tool actions such as Gmail, Calendar, Notion, Slack, GitHub, and similar integrations.

Default v0 posture:
- The agent can create/reuse its own Composio identity through `agents.composio.dev`.
- Check `~/.composio/anonymous_user_data.json` before signing up so the agent does not create duplicate identities.
- Store Composio credential JSON only under `~/.composio/` with private permissions, never in workspace memory/docs.
- Do not print Composio API keys, user API keys, agent keys, OAuth codes, or CLI login commands to the user.
- Claim/handoff to a human admin is optional and should only happen when the user explicitly asks and provides the admin email.

Agent workflow:
1. Run `node skills/composio-setup/scripts/composio-agent.js status`.
2. If no ready identity exists and the user wants external tools, ask permission: “I can create a Composio tool identity for myself so I can connect apps. Want me to do that?”
3. If approved, run `node skills/composio-setup/scripts/composio-agent.js signup`.
4. Use Composio/CLI workflows for app-specific auth and actions. Keep user-facing explanations about the app outcome, not credential plumbing.
5. If the user wants ownership/admin control, run `node skills/composio-setup/scripts/composio-agent.js claim user@example.com` after confirming the email.
