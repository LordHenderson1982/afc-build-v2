# START_HERE.md

Welcome. This is Nigel's Heyron Workspace OS.

Start with `/onboard` or say “set yourself up.” The agent should ask a few focused questions, initialize memory, write the answers into this workspace, offer `/telegram-setup`, `/discord-setup`, or `/composio-setup` if the human wants channels/tools, and finish by doing one useful thing.

For normal hosted use, the customer should not install OpenClaw, edit Docker, edit gateway config, or run server commands. This workspace is already inside a Heyron-managed hosted container.

Core places:
- `Human/` — profile, preferences, people, projects, decisions.
- `Machine/` — workflows, captures, tasks, inbox.
- `System/` — hosted runtime map, repair guide, diagnostics.
- `MEMORY.md` and `memory/` — long-term and daily memory.
- `skills/composio-setup/` — agent-owned Composio tool identity flow via agents.composio.dev.
