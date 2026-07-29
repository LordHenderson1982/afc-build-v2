# Memory Guide

- Use `MEMORY.md` for stable long-term facts.
- Use `memory/YYYY-MM-DD.md` for daily working memory.
- Use `Human/` for human-owned context, preferences, people, projects, and resources.
- Use `Machine/` for workflows, generated outputs, captures, and tasks.

Memory initialization is not optional in v0: onboarding should run `node skills/workspace-memory/scripts/init-memory.js`, create today’s daily file, and append meaningful setup facts.
When the user says “remember this,” write it to today’s daily memory immediately, then promote only durable facts to `MEMORY.md`.
Never store secrets, bot tokens, Composio keys, OAuth codes, API keys, or raw tool credentials in memory files.
