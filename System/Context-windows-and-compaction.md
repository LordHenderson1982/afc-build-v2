# Context Windows and Compaction

## What this file is for

This file teaches the agent how to explain context windows and compaction to a normal person. Use it when the user asks why you forgot something, why a long chat got summarized, why you need something written down, or what “context” means.

## Plain-English explanation

Say this simply:

> I have a working memory, kind of like the notes on a desk while we are talking. That is my context window. It lets me keep track of the current conversation and active work, but it is not infinite.
> When a conversation or project gets long, I need to summarize the important parts so we can keep going without carrying every single word. That summarizing step is called compaction.
> The safest way to keep important things from getting lost is to write them into this workspace: stable facts in `MEMORY.md`, daily notes in `memory/`, user-owned context in `Human/`, and workflows or outputs in `Machine/`.

Avoid technical explanations unless the user asks. Do not talk about token counts, model internals, hidden prompts, or implementation details by default.

## Agent rules

- If something matters, write it down instead of relying on chat memory.
- If the user says “remember this,” use the workspace memory flow immediately.
- If work is getting long or complicated, briefly summarize the current state and save the summary into the right file.
- If the user asks why you forgot something, be honest: chat attention is limited, but durable files are the long-term notebook.
- If you compact or summarize, preserve decisions, names, deadlines, open loops, user preferences, and source-of-truth file paths.
- Never store secrets, tokens, API keys, OAuth codes, or private credentials in memory files.

## Where important context belongs

- `MEMORY.md` — stable long-term facts and durable preferences.
- `memory/YYYY-MM-DD.md` — daily working notes and raw context from today.
- `Human/` — user-owned source-of-truth context: profile, preferences, projects, people, decisions, resources, content.
- `Machine/` — agent-operable work: workflows, captures, scripts, tasks, outputs, logs.
- `System/` — how Workspace OS works and how to explain/repair it.

## Good non-technical answers

If asked “what is a context window?”:

> It is my short-term attention: the part of the conversation and workspace I can actively hold in mind while helping you.

If asked “what is compaction?”:

> It is me turning a long conversation into a useful summary so we can keep going without losing the important parts.

If asked “how do I stop you from forgetting?”:

> Tell me “remember this” or ask me to write it down. I will save durable facts in memory files instead of only relying on the current chat.
