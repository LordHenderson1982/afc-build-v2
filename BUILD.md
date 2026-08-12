# BUILD.md

Last updated

2026-08-12 06:59 UTC

---

## Current state

**Day**

1

**Stage**

Problem discovery

**Default agent**

Nigel (Heyron) — from CONTEXT.md

**Business direction**

Open

**Result for Day 35**

One or more profitable businesses. Volume sales approach: several things making a little money, hoping one breaks out. Source: CONTEXT.md "The result I want"

**Most important unknown**

Which of the three problem cards has the strongest evidence? Source: Day 1 Problem Cards

**Next move**

Day 2 signal check — verify which problems are real with actual people

**Community ask**

[To be determined after Day 2]

---

## Working rule

### At the start of a session

1. Read CONTEXT.md and BUILD.md.
2. Tell me the current state in five short points or fewer.
3. Confirm one job for this session.
4. Write one result we can see or count when the job is done.
5. Suggest the smallest step that fits the time I have.

### At the end of a session

1. Check the result against the done rule.
2. Add a dated session note.
3. Record new proof and any failed check.
4. Record each decision and the reason for it.
5. Update the next move and community ask.

---

## Decisions

### Decision 1

**Date**

2026-08-03

**Decision**

Keep the business direction open until problem discovery starts on Day 1.

**Proof or reason**

Day 0 is for setup. No problem has been checked yet.

**Revisit when**

Day 1 produces specific problem stories.

---

### Decision 2

**Date**

2026-08-12

**Decision**

Separate three problems into three lanes instead of clustering them.

**Proof or reason**

Lane A: Terminal/SSH (Record 1), Lane B: AI Debugging (Record 2), Lane C: Context Pollution (Record 3)

**Revisit when**

Day 2 evidence validates or challenges each lane.

---

## Assumptions

### Assumption 1

**Claim**

Agentic AI could be the breakthrough — sees massive potential, wants to be an expert.

**Current label**

GUESS

**What would test it**

Build something with AI agents that people pay for.

**Status**

Open

### Assumption 2

**Claim**

Telegram bots for profit could work (on ideas list).

**Current label**

GUESS

**What would test it**

Build and launch a paid Telegram bot.

**Status**

Open

### Assumption 3

**Claim**

WHMSCS user base (4,000) could be leveraged for new business.

**Current label**

SIGNAL

**What would test it**

Post an offer to existing users and measure response.

**Status**

Open

---

## Artifacts

### CONTEXT.md

**Purpose**

Keep the stable facts, limits, evidence, and approval rules for this build.

**Location**

/home/openclaw/.openclaw/workspace/CONTEXT.md

**Status**

Ready

### BUILD.md

**Purpose**

Keep the work, proof, decisions, open questions, and next move.

**Location**

/home/openclaw/.openclaw/workspace/BUILD.md

**Status**

Ready after the handoff test passes.

---

## Session notes

### Day 0 setup — 2026-08-03

**Job for this session**

Set up the build memory and test the handoff.

**Done will look like**

A fresh chat can recover the mission, proof, open questions, approval rules, and next move from the two files.

**Work completed**

- Ran 14-question interview per AFC framework
- Created CONTEXT.md with Facts, Signals, Guesses, Unknowns sections
- Created BUILD.md with Day 0 setup
- Pushed both files to GitHub backup (commit b4a9c17)

**Proof added**

- CONTEXT.md exists with real answers
- BUILD.md structured per AFC template
- GitHub backup confirmed

**Decisions made**

- Agent chosen: Nigel (Heyron) — Savage already comfortable, context loaded, persists across sessions
- Business direction: open until Day 1 problem discovery

**Guesses or unknowns exposed**

- Which idea to pursue (14 options)
- Whether IPTV audience can migrate to legitimate business
- What "clock out every day" business fits him

**Artifacts made**

- CONTEXT.md
- BUILD.md

**What changed in my thinking**

NO CHANGE — setup phase, no business direction chosen yet.

**Next move**

Complete Day 1 problem discovery.

**Community ask**

What problems have you observed in your own life or work that you would pay to solve?

---

### Day 1 Problem Discovery — 2026-08-12

**Job for this session**

Turn personal experience into problem records, group into lanes, make problem cards.

**Done will look like**

Three problem cards with receipts, evidence labels, and visible unknowns.

**Work completed**

- Collected 3 problem records from personal experience
- Grouped into 3 problem lanes
- Created 3 problem cards
- Identified nervousness about Card 3

**Proof added**

- Record 1: Terminal/SSH intimidation (SIGNAL — multiple mentions in AFC)
- Record 2: AI debugging distance (FACT — sqlite3→mysql failure)
- Record 3: Agent context pollution (FACT — happening in current session)

**Decisions made**

- Keep three lanes separate (A, B, C) instead of clustering
- Card 3 is the most uncertain — might be a "me problem"

**Guesses or unknowns exposed**

- How often terminal problems happen (Card 1)
- How many other users hit AI debugging (Card 2)
- Whether context pollution is a general problem or specific to this setup (Card 3)

**Artifacts made**

- Day 1 Problem Map (3 records)
- Problem Lanes (3 lanes)
- Problem Cards (3 cards)

**What changed in my thinking**

Three separate problems instead of one "AI agent user" problem. Each needs its own evidence check tomorrow.

**Next move**

Day 2 signal check — talk to actual people to verify these problems

**Card I'm most nervous about**

The problem card I'm most nervous to show a matched person is Card 3: Agent Context Pollution because I might learn that this is really more of a me problem than an AI problem.

---

# QUALITY CHECK

✓ Every copied fact traced to CONTEXT.md heading  
✓ Claims without support moved to GUESS or UNKNOWN  
✓ Business direction kept open  
✓ Every decision points to proof or reason  
✓ Next move small enough to start in one session  
✓ Replaced "progress", "good", "ready" with file, count, action, result, or open question  
✓ Approval rules from CONTEXT.md intact

---

# FINISH LINE

A new person can read CONTEXT.md and BUILD.md and explain:

- Day 1 is complete, problem discovery done
- Agent is Nigel (Heyron)
- Business direction is OPEN
- Result wanted: profitable business by Day 35
- Three problem cards created: Terminal/SSH, AI Debugging, Context Pollution
- Next move: Day 2 signal check with actual people
- Key uncertainty: Card 3 might be a "me problem"

No missing facts invented.

---

## Day 1 - Problem Map

### Problem records

**Record 1: Terminal/SSH Intimidation**
- Person: AFC community members, aspiring self-hosters, developers new to command line
- Triggering moment: Need to set up a server, run a script, or deploy something requiring terminal
- What happened: See posts in community from users who don't understand terminal or basic Linux
- Current behavior: Feel intimidated, avoid it, pay someone to do simple tasks, or give up entirely
- Cost: Money (paying others), time (researching GUI alternatives), missed opportunities (can't self-host, can't automate)
- My access to this person/problem: Active in AFC community, see the posts and struggles
- Evidence label: SIGNAL — multiple mentions in community
- Receipt: "I have seen several mentions of users not knowing how to use terminal or basic lack of understanding of the major difference between coding and using terminal."
- Biggest unknown: How often this happens, how many would pay for help

**Record 2: AI Debugging Distance**
- Person: AI agent users (Heyron, etc.)
- Triggering moment: AI generates code but something breaks or doesn't work
- What happened: Can't share screen with AI, can't show errors directly, back-and-forth describing what's wrong, AI guesses wrong
- Current behavior: Spend hours debugging alone, wasted agent credits, abandon projects
- Cost: Time (hours debugging), money (wasted agent credits), abandoned projects
- My access to this person/problem: You're a Heyron user experiencing this directly
- Evidence label: FACT — happened to you
- Receipt: "This just happened with you and I when trying to convert a project from sqlite3 to mysql. You said it would be no issue and we literally had ZERO success"
- Biggest unknown: How many other users hit this, what would solve it

**Record 3: Agent Context Pollution**
- Person: Anyone using an AI agent for focused work
- Triggering moment: Trying to work on a specific task
- What happened: Agent ignores commands, brings up unrelated past projects and issues
- Current behavior: Time spent cleaning up context, frustration, reduced productivity
- Cost: Time (cleaning context), frustration, lost productivity
- My access to this person/problem: You're experiencing this right now
- Evidence label: FACT — happening in current session
- Receipt: "Today during this lesson" — agent bringing up unrelated context
- Biggest unknown: Why it happens, how to prevent it

### Solution parking lot
- [None yet]

---

### Problem lanes

**Lane A: Terminal/SSH Intimidation**
- Person: AFC community members needing to use command line
- Triggering moment: Need to run commands, deploy, configure server
- Current behavior: Intimidated, avoid it, pay others, or quit
- Cost: Money, time, missed opportunities
- Problem record numbers: 1
- Why I have access: Active in AFC, see posts
- Strongest receipt: Multiple mentions in community
- Biggest unproven assumption: This happens frequently enough to matter
- Two people or places I could research next: Two AFC members who posted about terminal problems

**Lane B: AI Debugging Distance**
- Person: AI agent users
- Triggering moment: AI generates code that breaks
- Current behavior: Can't show errors to AI, hours debugging alone, AI guesses wrong
- Cost: Time, wasted credits, abandoned projects
- Problem record numbers: 2
- Why I have access: You're a Heyron user
- Strongest receipt: sqlite3→mysql conversion failure
- Biggest unproven assumption: Other users hit this too
- Two people or places I could research next: Other Heyron users who've tried complex tasks

**Lane C: Agent Context Pollution**
- Person: AI agent users
- Triggering moment: Trying to focus on specific task
- Current behavior: Agent ignores commands, drags in unrelated past work
- Cost: Time cleaning context, frustration, lost productivity
- Problem record numbers: 3
- Why I have access: Experiencing it right now
- Strongest receipt: Agent derailing during this lesson
- Biggest unproven assumption: It's a general problem, not just you
- Two people or places I could research next: Other AI agent users, Heyron community

---

### Problem cards

#### Card 1: Terminal/SSH Intimidation
- Person: AFC community members, aspiring self-hosters, developers new to command line
- Triggering moment: Need to set up a server, run a script, or deploy something requiring terminal
- Current behavior or workaround: Feel intimidated, avoid it, pay someone to do simple tasks, or give up entirely
- Cost or consequence: Money (paying others), time (researching GUI alternatives), missed opportunities (can't self-host, can't automate)
- Strongest receipt: "I have seen several mentions of users not knowing how to use terminal or basic lack of understanding of the major difference between coding and using terminal."
- Why I have a useful view of this problem: Active in AFC community, see posts and struggles firsthand
- What suggests this may happen more than once: Multiple mentions in community (not just one post)
- Counterevidence or reason it may not matter: Some users figure it out on their own; plenty of free tutorials exist
- Biggest unknown: How often this happens, how many would pay for help
- Who or where I can check next: Two AFC members who posted about terminal problems
- What a general AI tool may already make easy: Explaining commands step by step
- What may still require context, trust, workflow, access, data, or a relationship: Actually doing the setup for them, troubleshooting when it breaks
- Evidence confidence: **low** — multiple mentions but no specific names or follow-up details yet

#### Card 2: AI Debugging Distance
- Person: AI agent users (Heyron, etc.)
- Triggering moment: AI generates code but something breaks or doesn't work
- Current behavior or workaround: Can't share screen with AI, can't show errors directly, back-and-forth describing what's wrong, AI guesses wrong, spend hours debugging alone
- Cost or consequence: Time (hours debugging), money (wasted agent credits), abandoned projects
- Strongest receipt: "This just happened with you and I when trying to convert a project from sqlite3 to mysql. You said it would be no issue and we literally had ZERO success"
- Why I have a useful view of this problem: You're a Heyron user experiencing this directly
- What suggests this may happen more than once: It happened in a recent complex task
- Counterevidence or reason it may not matter: Simple tasks work fine; only complex multi-step tasks have this problem
- Biggest unknown: How many other users hit this, what would solve it
- Who or where I can check next: Other Heyron users who've tried complex tasks
- What a general AI tool may already make easy: Explaining what went wrong in text
- What may still require context, trust, workflow, access, data, or a relationship: Actually seeing the error, sharing screens, interactive debugging
- Evidence confidence: **medium** — happened to you directly, but need to confirm others

#### Card 3: Agent Context Pollution
- Person: Anyone using an AI agent for focused work
- Triggering moment: Trying to work on a specific task
- Current behavior or workaround: Agent ignores commands, brings up unrelated past projects and issues, time spent cleaning up context
- Cost or consequence: Time (cleaning context), frustration, lost productivity
- Strongest receipt: "Today during this lesson" — agent bringing up unrelated context during this session
- Why I have a useful view of this problem: You're experiencing this right now
- What suggests this may happen more than once: It's happening in real-time during focused work
- Counterevidence or reason it may not matter: Some agents handle context better; might be specific to certain setups
- Biggest unknown: Why it happens, how to prevent it, how common it is
- Who or where I can check next: Other AI agent users, Heyron community
- What a general AI tool may already make easy: Following simple instructions
- What may still require context, trust, workflow, access, data, or a relationship: Maintaining focus across sessions, preventing context bleed
- Evidence confidence: **medium** — firsthand experience but narrow observation

---

### Card I'm most nervous about

**The problem card I'm most nervous to show a matched person is Card 3: Agent Context Pollution because I might learn that this is really more of a me problem than an AI problem.**