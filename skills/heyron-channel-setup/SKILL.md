---
name: heyron-channel-setup
description: Configure Telegram or Discord for a Heyron-hosted OpenClaw workspace from inside the container, then verify channel status without self-hosting instructions.
---

# Heyron Channel Setup

Use this as the low-level executor/verification layer for Telegram or Discord. For user-facing flows, prefer `/telegram-setup` or `/discord-setup`.

Rules:
- Treat a pasted bot token as delegated permission to configure that channel.
- Do not print or store tokens in memory files.
- For Telegram, run `./heyron-channel-setup telegram` with the token on stdin. Do not ask for the numeric Telegram user ID. Token-only setup uses open DMs so the bot works immediately after setup.
- For Discord setup, run `./heyron-channel-setup discord` with the token on stdin. Do not ask for Discord user/server IDs in the default product flow.
- Discord server/channel messages require @mentions by default. If the user asks to turn mentions off, run `./heyron-channel-setup discord-mentions off`; to restore the safer default, run `./heyron-channel-setup discord-mentions on`.
- Use `--dry-run` first when validating shape or config safety.
- Before the non-dry-run config write, warn the user in plain language: “For the next minute, please don’t touch anything. You may see a couple of screens flash or reconnect. Don’t press buttons or try to reconnect anything — just wait about 60 seconds, then message me again.”
- After writing config, wait for the brief background connection refresh, then verify with channel status/probe; do not call setup complete until the channel is running.
- Explain only the next customer-facing step.

Never send hosted users to generic self-hosting docs for normal channel setup.
