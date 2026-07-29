# Channel Setup

Hosted channel setup is agent-executed.

Decision tree:
1. If the user says `/onboard`, first learn enough about the human/agent identity, then ask: “Do you want me connected to Telegram, Discord, both, or neither right now?”
2. If they choose Telegram, follow `/telegram-setup`.
3. If they choose Discord, follow `/discord-setup`.
4. If they paste a Telegram token, infer Telegram setup and proceed.
5. If they paste a Discord token, infer Discord setup and proceed.
6. Never ask for numeric Telegram user IDs, Discord user IDs, Discord server IDs, Application IDs, Client Secrets, or Public Keys during the default product flow.

Telegram:
- User steps: open BotFather, create/select a bot, copy the bot token, paste only that token here.
- Before writing config, warn the user: “I’m going to connect Telegram now. For the next minute, please don’t touch anything. You may see a couple of screens flash or reconnect. Don’t press buttons or try to reconnect anything — just wait about 60 seconds, then message me again.”
- Run `./heyron-channel-setup telegram` with the token on stdin. Do not ask for the numeric Telegram user ID. Token-only setup uses open DMs so the bot works immediately after setup.
- Verify with `openclaw channels status --json --probe`; only call it live when Telegram is configured, running, and probing OK.
- Tell the customer the next visible step only: start the bot and send a test message.

Discord:
- If the user already has a Discord bot token, ask them to paste only the Bot Token.
- If they do not have a bot yet, give the exact user steps from `/discord-setup`: create app, create/reset bot token, enable Message Content Intent, invite bot with required permissions, then paste only the Bot Token here.
- Do not ask for Client Secret, Public Key, Application ID, User ID, or Server ID unless a later advanced workflow truly needs it.
- Before writing config, warn the user: “I’m going to connect Discord now. For the next minute, please don’t touch anything. You may see a couple of screens flash or reconnect. Don’t press buttons or try to reconnect anything — just wait about 60 seconds, then message me again.”
- Run `./heyron-channel-setup discord` with the token on stdin.
- Verify with channel status/probe.
- Tell the customer the next visible step only: DM the bot, or @mention it in the invited server and send a test message.
- Explain the default: in Discord servers, the bot listens when someone @mentions it. If they want it to respond without @mentions in visible channels, they can ask you to turn mentions off later.

Never print tokens back to the user or write them into memory files.
