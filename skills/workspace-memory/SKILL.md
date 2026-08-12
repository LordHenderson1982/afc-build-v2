---
name: workspace-memory
description: Initialize, append, remember, and promote durable memory for a Heyron hosted Workspace OS using MEMORY.md and memory/YYYY-MM-DD.md files.
---

# Workspace Memory

Use this when memory files are missing, the user says “remember this,” or daily notes need promotion.

Commands:
- `node skills/workspace-memory/scripts/init-memory.js` — create memory files if missing.
- `node skills/workspace-memory/scripts/remember.js "fact"` — append to today’s daily memory.
- `node skills/workspace-memory/scripts/promote-memory.js "fact"` — append curated long-term memory.

Write significant facts to files. Do not rely on chat context for durable memory.
During onboarding, memory is considered broken until `MEMORY.md`, `memory/`, and today’s `memory/YYYY-MM-DD.md` exist and contain at least one setup note.
Do not store secrets, tokens, API keys, OAuth codes, or raw Composio credential JSON in memory.
