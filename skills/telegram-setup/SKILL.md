---
name: telegram-setup
description: Token-only Telegram onboarding for a Heyron-hosted Workspace OS. Use when the user says /telegram-setup, wants Telegram connected, pastes a Telegram bot token, or chooses Telegram during /onboard.
---

# Telegram Setup

Goal: user provides only the Telegram bot token, then Telegram works. No numeric Telegram user ID. No pairing code. No self-hosting docs.

User steps to give if they do not already have a token:
1. Open Telegram and message `@BotFather`.
2. Send `/newbot` or choose an existing bot.
3. Copy the bot token from BotFather.
4. Paste only the bot token here.

Agent steps:
1. Treat a pasted token as permission to configure Telegram. Do not repeat the token back.
2. Dry-run validate first: `printf %s "$TOKEN" | ./heyron-channel-setup telegram --dry-run`.
3. Before writing config, warn the user: “I’m going to connect Telegram now. For the next minute, please don’t touch anything. You may see a couple of screens flash or reconnect. Don’t press buttons or try to reconnect anything — just wait about 60 seconds, then message me again.”
4. If dry-run passes, write config: `printf %s "$TOKEN" | ./heyron-channel-setup telegram`.
5. Wait for the brief background connection refresh, then run `openclaw channels status --json --probe`.
6. Only call setup complete when Telegram is running/probing OK.
7. Tell the user: “Start the bot in Telegram and send it a test message.”

Expected config behavior: token-only Telegram uses open DMs (`dmPolicy="open"`, `allowFrom=["*"]`) so the bot works immediately.

If it fails:
- Token invalid/401: ask the user to regenerate/copy the BotFather token.
- Probe not running: run the workspace health check and summarize the support issue.
- Pairing code appears: this is wrong for Heyron onboarding; run health check and escalate with config summary.
