# Connect Tools With Composio

1. When the user wants external app actions, explain that Heyron can use Composio as the tool bridge.
2. Run `node skills/composio-setup/scripts/composio-agent.js status` to check for an existing agent identity.
3. If missing, ask concise permission to create an agent-owned Composio identity, then run signup.
4. Never paste Composio keys, API keys, or CLI login commands back to the user.
5. Claim/handoff to the human only if they explicitly ask and provide an admin email.
