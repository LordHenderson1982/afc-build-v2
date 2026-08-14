# BUILD.md

Last updated

2026-08-12 06:59 UTC

---

## Current state

**Day**

3

**Stage**

Problem Research Brief complete — research bet selected

---

## Day 3 Statement

Day 3 of building my first real business with AI.

The problem getting my next four research days is

**AFC community members, aspiring self-hosters, and developers new to the command line who need to set up a server, run a script, or deploy something requiring terminal but feel intimidated, avoid it, pay someone to do simple tasks, or give up entirely.**

It earned the time because **4 independent receipts (1 community signal + 3 direct AFC responses) confirm this problem exists, and I have clear reach paths (4,000 WHMCS users, 600 Telegram users, AFC members who already responded).**

The biggest thing I still do not know is **whether the problem is recurring or one-time, whether current alternatives work well enough, and whether people would actually pay for a solution.**

I will change or stop if **fewer than 5 out of 8 people describe it as recurring, OR if everyone has a tolerable solution already, OR if no one would pay for help.**

I am not choosing the product yet.

---

**Default agent**

Nigel (Heyron) — from CONTEXT.md

**Business direction**

Open

**Result for Day 35**

One or more profitable businesses. Volume sales approach: several things making a little money, hoping one breaks out. Source: CONTEXT.md "The result I want"

**Most important unknown**

Would they actually pay for help? (Terminal/SSH research bet selected)

**Next move**

Four-day research sprint on Terminal/SSH problem

**Community ask**

[Day 3 complete - Terminal/SSH selected as research bet]

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

### Day 2 Signal Check — 2026-08-14

**Job for this session**

Talk to actual people to verify which problems are real.

**Done will look like**

Gathered signals from real people on which problem resonates.

**Work completed**

- Received 3 AFC member responses on Terminal/SSH problem
- Insight: Cards 2 and 3 could be addressed through the Terminal solution by broadening scope

**Proof added**

- Response 1: Fear of breaking production systems ("could do some major damage")
- Response 2: Visual learner, can't read/keep up with terminal output
- Response 3: Complete beginner, not familiar with terminal

**Key insight**

Cards 2 (AI Debugging) and 3 (Context Pollution) can be addressed through a unified Terminal solution:
- Terminal = AI sees everything (solves Card 2)
- Terminal = Fresh session = clean context (solves Card 3)
- Broader scope: Terminal + OS basics + AI agent usage on servers

**Research completed**

- Spawned agent to research Card 2 across top 5 Agentic AI tools (Cursor, Windsurf, Claude Code, GitHub Copilot)
- Findings: Both problems are systemic industry issues, not specific to your setup
- AI Debugging: No tool has native screen sharing, all require manual copy/paste
- Context Pollution: All tools have issues after ~20 messages, AI ignores instructions

**Decisions made**

- Card 1 (Terminal/SSH) has 3 real voices — validated
- Card 2 (AI Debugging + Context Pollution) validated by industry research
- Cards 2 & 3 merged into single Card 2 per Day 2.5 consolidation
- Problem reframed: "People want to use AI agents on servers but don't know terminal, can't see what's happening, and get stuck"

**Next move**

Move to Day 3 solutioning

---

## Day 3 - Problem Research Brief

### Final comparison

| Question | Finalist 1 (Terminal/SSH) | Finalist 2 (AI Debugging + Context) |
|---|---|---|
| Triggering moment clarity | Clear — need to set up server, run script, deploy | Two problems merged: code breaks OR agent derails |
| Receipts (strength) | 4 receipts: 1 community signal + 3 direct AFC responses | 4 receipts: 2 personal experience + 2 industry research |
| Current behavior documented | Yes — pay someone, give up, use GUI alternatives | Yes — debug alone, waste credits, abandon projects |
| Unanswered risk size | Medium — how often, would they pay? | Medium — how many users, what solves it? |
| Access to matched people | Strong — 4,000 WHMCS, 600 Telegram, AFC | Weak — needs Agentic AI users specifically |
| 4-day learning potential | High — can reach people directly, ask specific questions | Lower — harder to find and reach affected users |
| Fit with time/money | Fits — no capital needed, uses existing skills | Fits — uses AI agent expertise |
| Durability question | Will this still matter in 6 months? | Will this still matter in 6 months? |

### Strongest case for Finalist 1 (Terminal/SSH)

1. **Receipts are independent and diverse** — 4 different people, 3 different responses (fear of damage, visual learner barrier, complete beginner)
2. **Access is immediate** — 4,000 WHMCS users already paying for tech, 600 Telegram users already engaged
3. **Triggering moment is specific** — "need to set up a server, run a script, or deploy something requiring terminal"
4. **Four days can change my mind** — ask 10 people "describe a recent time you avoided terminal" and get clear answer
5. **Current workaround visible** — they pay someone, give up, or find GUI alternatives

### Strongest case against Finalist 1 (Terminal/SSH)

1. **Competition exists** — free tutorials, YouTube, Stack Overflow
2. **May be one-time pain** — people learn once and done
3. **Uncertainty on payment** — might not pay, just endure

### Strongest case for Finalist 2 (AI Debugging + Context)

1. **Industry-validated problem** — research shows all 4 major tools have same issues
2. **Fits expertise** — best skill is working with AI agents
3. **Personal experience is real** — sqlite3→mysql failure happened
4. **Growing market** — more people using Agentic AI tools

### Strongest case against Finalist 2 (AI Debugging + Context)

1. **Hard to reach people** — need users who've hit this specific problem
2. **Two problems merged** — harder to research, less clear focus
3. **Access is weak** — your existing audience (WHMCS, Telegram) may not be Agentic AI users
4. **Industry may solve it** — tools are improving fast

### Most important unknown for each

- **Finalist 1:** Would they actually pay for help, or just endure?
- **Finalist 2:** Can I reach enough people who experience this?

### What four days can answer

- **Finalist 1:** Talk to 10+ WHMCS/Telegram users. Ask: "Describe a recent time you needed to use terminal but didn't know how." Then: "What did you do? Would you pay for help?"
- **Finalist 2:** Find and interview Agentic AI users. Harder — may need to post in specific communities, not your existing reach.

### Recommendation

**Finalist 1 (Terminal/SSH)** — because:
1. Clear path to evidence in 4 days
2. Your existing audience matches the problem
3. Specific triggering moment
4. Current workarounds are visible (pay someone, give up)

### Evidence that could reverse this recommendation

If in 4 days I find:
- Terminal problem is one-time learning, not recurring
- Everyone already has a solution they tolerate
- No one would pay for help

Then switch to Finalist 2 or drop both.

---

### Chosen problem

**Person:**
AFC community members, aspiring self-hosters, and developers new to the command line who need to interact with a server but feel intimidated by terminal.

**Triggering moment:**
Need to set up a server, run a script, or deploy something requiring terminal.

**Current behavior or workaround:**
Feel intimidated, avoid it, pay someone to do simple tasks, or give up entirely.

**Observed consequence:**
- Money: paying others to do simple tasks
- Time: researching GUI alternatives
- Missed opportunities: can't self-host, can't automate

**Strongest receipt:**
"I have seen several mentions of users not knowing how to use terminal or basic lack of understanding of the major difference between coding and using terminal." — AFC community signal

**Strongest countersignal:**
Some users figure it out on their own; plenty of free tutorials exist.

**What remains UNKNOWN:**
- How often this happens to the same person (recurring vs one-time)
- How many would actually pay for help vs just enduring
- Whether current solutions (tutorials, YouTube) already work well enough

**Why this problem gets four research days:**
Clear path to evidence. Your existing audience (WHMCS users, Telegram users) matches the problem. Specific triggering moment. Current workarounds are visible. Four days can answer whether people would pay.

### Reach List

| Person or place | Why matched | Warm, cold, public, or unknown | First research move |
|---|---|---|---|
| AFC members who already responded | Already voiced the problem | Warm | Follow up: "Can you describe a recent time this happened?" |
| WHMCS users (4,000) | Already paying for tech, likely server-adjacent | Warm (existing customer) | DM: "When's the last time you needed to use terminal and didn't know how?" |
| Telegram users (600) | Engaged, reachable | Warm | Post in group: "Quick question for those who use servers" |
| AFC community general | Problem first surfaced here | Public | Post asking about terminal experiences |
| Reseller contacts ("hustlers") | Always looking for new income streams, technical | Warm | Message directly about their terminal experiences |
| r/selfhosted subreddit | People trying to self-host | Public | Browse threads, comment asking about terminal struggles |
| r/commandline subreddit | Terminal users | Public | Post or search for "intimidated" posts |
| DevOps/Friendly community | People learning Linux | Public | Observe or post in relevant channels |
| YouTube tutorial comments | People struggling with terminal | Public | Read comments on popular terminal tutorials |
| Server setup services (digitalocean, linode docs) | People following guides | Public | Check comments/questions sections |

### Parked finalist

**Problem:** AI Debugging Distance + Context Pollution

**Why it is parked:**
Harder to reach matched people. Your existing audience (WHMCS, Telegram) may not be Agentic AI users. Access is weak — would need to find users in specific communities, not your existing reach. Also two problems merged makes research less focused.

**Evidence that could bring it back:**
If Terminal/SSH research shows: (1) problem is one-time only, (2) everyone has a tolerable solution, (3) no one would pay. Or if you find a clear path to reach Agentic AI users in your network.

---

### Research questions

#### Question 1: How often does this problem happen?

**Research question:** When someone encounters a task requiring terminal/SSH, how often does the intimidation or confusion actually stop them? Is it a one-time learning moment or a recurring pain?

**Decision this will inform:** Go, Change, or No Go (Day 7)

**Assumption underneath it:** The problem is recurring for the same person, not a one-time learning curve they get past.

**Evidence that would raise confidence:**
- Multiple recent incidents described by the same person
- People describe it happening "every time" or "regularly"
- The problem comes up across different tasks, not just one tutorial

**Evidence that would lower confidence:**
- People describe it as "I learned once and now I'm fine"
- After one successful experience, the problem goes away
- The triggering moment only happens once per person (first server setup)

**Best source or matched person:** WHMCS users who have set up servers, AFC members who've responded

**Day 4, Day 5, or several days:** Days 4-5 (receipts and behavior)

---

#### Question 2: What do people do now? Do current alternatives work?

**Research question:** When someone hits this problem, what do they actually do? Do free tutorials, YouTube, and Google searches solve it for them?

**Decision this will inform:** Go, Change, or No Go (Day 7)

**Assumption underneath it:** Current solutions (tutorials, YouTube, free help) don't work well enough for this audience.

**Evidence that would raise confidence:**
- People describe frustration with tutorials ("too advanced," "assumes knowledge")
- People give up or pay someone instead of figuring it out
- Current search results are overwhelming or confusing

**Evidence that would lower confidence:**
- People say "I just looked it up on YouTube and figured it out"
- Free resources are working fine
- The problem solves itself after basic learning

**Best source or matched person:** WHMCS users, Telegram users, r/selfhosted

**Day 4, Day 5, or several days:** Days 4-5 (alternatives)

---

#### Question 3: What's the actual cost? Would they pay?

**Research question:** What does this problem actually cost someone in time, money, and missed opportunities? When it happens, would they pay for help or just endure?

**Decision this will inform:** Go, Change, or No Go (Day 7)

**Assumption underneath it:** People would pay for a solution rather than just enduring or using free alternatives.

**Evidence that would raise confidence:**
- People describe paying someone to do simple tasks
- People express frustration strong enough to pay
- The cost (time wasted, money paid) adds up noticeably

**Evidence that would lower confidence:**
- People say "I just deal with it" or "it's not that big of a deal"
- No one has ever paid for help with this
- Free solutions are good enough

**Best source or matched person:** Reseller contacts (hustlers), AFC members, WHMCS users

**Day 4, Day 5, or several days:** Days 5-6 (conversations about cost and payment)

---

### Dangerous assumptions identified

1. **Recurrence** — Problem happens repeatedly, not one-time
2. **Inadequate alternatives** — Current tutorials/solutions don't work
3. **Payment willingness** — People would pay for help

If any of these three assumptions fails, the Day 7 decision changes to No Go or Change.

---

## Day 3 - Problem Research Brief

### Chosen problem

**Person:**
AFC community members, aspiring self-hosters, and developers new to the command line who need to interact with a server but feel intimidated by terminal.

**Triggering moment:**
Need to set up a server, run a script, or deploy something requiring terminal.

**Current behavior or workaround:**
Feel intimidated, avoid it, pay someone to do simple tasks, or give up entirely.

**Observed consequence:**
- Money: paying others to do simple tasks
- Time: researching GUI alternatives
- Missed opportunities: can't self-host, can't automate

**Strongest receipt:**
"I have seen several mentions of users not knowing how to use terminal or basic lack of understanding of the major difference between coding and using terminal." — AFC community signal

Plus 3 direct responses:
- Fear of breaking production systems
- Visual learner can't keep up
- Complete beginner

**Strongest countersignal:**
Some users figure it out on their own; plenty of free tutorials exist.

**What remains UNKNOWN:**
- How often this happens (recurring vs one-time)
- How many would actually pay
- Whether current solutions work well enough

**Why this problem gets four research days:**
Clear path to evidence. Your existing audience matches the problem. Specific triggering moment. Current workarounds visible. Four days can answer whether people would pay.

---

### Reach List

**Fastest three matched paths:**
1. AFC members who already responded — Warm — Follow up
2. WHMCS users (4,000) — Warm — DM existing customers
3. Telegram users (600) — Warm — Post in group

**Remaining honest paths:**
4. AFC community general — Public
5. Reseller contacts — Warm
6. r/selfhosted — Public
7. r/commandline — Public
8. DevOps communities — Public
9. YouTube tutorial comments — Public
10. Server provider docs — Public

**Reach risk:**
MEDIUM — Can reach warm paths first, but public paths may be needed for volume.

---

### Research questions

**Question 1: How often does this problem happen?**
- Decision: Go/Change/No Go (Day 7)
- Assumption: Problem is recurring, not one-time
- Raises confidence: People describe it happening "regularly" or "every time"
- Lowers confidence: "I learned once and now I'm fine"
- Best source: WHMCS users, AFC members
- Days: 4-5

**Question 2: What do people do now? Do current alternatives work?**
- Decision: Go/Change/No Go (Day 7)
- Assumption: Current solutions don't work well enough
- Raises confidence: People give up or pay someone instead
- Lowers confidence: "I figured it out on YouTube"
- Best source: WHMCS, Telegram, r/selfhosted
- Days: 4-5

**Question 3: What's the actual cost? Would they pay?**
- Decision: Go/Change/No Go (Day 7)
- Assumption: People would pay for help
- Raises confidence: People describe paying someone
- Lowers confidence: "I just deal with it"
- Best source: Resellers, AFC, WHMCS
- Days: 5-6

---

### Decision rules

**GO when:**
At least 5 out of 8+ people interviewed describe the problem as recurring (happens more than once), AND at least 3 describe a real cost (time wasted, money paid, opportunity missed), AND at least 2 express willingness to pay for a solution.

**CHANGE when:**
- Problem is one-time only (people learn once and it's solved)
- Current alternatives work fine (people successfully use YouTube/tutorials)
- Problem is narrower than expected (only affects complete beginners, not the broader audience)

**NO GO when:**
- Cannot reach enough matched people (fewer than 5 interviews in 4 days)
- Everyone has a tolerable solution already
- No one would pay for help (universal "I just deal with it")
- Problem is too rare to matter

**Biggest risk these rules may miss:**
The problem is real but the market is too small. Even if people experience the problem, not enough would pay to make a business. Phase 2 would need to test willingness to pay more directly.

---

### Durable value question

**What may become easy or free:**
AI agents getting better at explaining terminal commands step-by-step. More and better free tutorials. GUI alternatives improving.

**What may still depend on context, workflow, trust, access, data, or a relationship:**
Actually doing the setup for them (not just explaining). Troubleshooting when it breaks. Personalized help for their specific situation. Having someone to call when stuck.

**Evidence Phase 2 should look for:**
Whether people value "someone who will do it for me" vs "someone who will teach me."

### Solution space is open

The research is NOT limited to "learning terminal." During research, explore what actually solves the problem:
- Automating server setup
- Running useful scripts
- Self-hosting services
- Deployment pipelines
- Someone who does it for them
- A hybrid (teach + do)

The goal is to find what people would actually pay for, not to assume a product form upfront.

---

### Things we are not choosing yet

- Product form
- Features
- Name
- Price
- Promise
- Build tool

---

### Parked finalist

**Problem:** AI Debugging Distance + Context Pollution

**Why parked:** Harder to reach matched people. Access is weak — would need to find users in specific communities, not your existing reach. Also two problems merged makes research less focused.

**Evidence that could bring it back:**
If Terminal/SSH research shows: (1) problem is one-time only, (2) everyone has a tolerable solution, (3) no one would pay. Or if you find a clear path to reach Agentic AI users in your network.

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