---
name: discord-setup
description: Token-only Discord onboarding for a Heyron-hosted Workspace OS. Use when the user says /discord-setup, wants Discord connected, pastes a Discord bot token, or chooses Discord during /onboard.
---

# Discord Setup

Goal: user provides only the Discord Bot Token to the agent, then Discord works. No Discord user ID, server ID, Application ID, Client Secret, Public Key, or pairing code in the default product flow.

Decision tree:
1. If the user already has a Discord bot token, ask them to paste only the Bot Token and skip to Agent steps.
2. If they have a Discord app/bot but it is not invited, tell them to complete the invite steps below, then paste the Bot Token.
3. If they have not created a bot, walk them through all user steps below.

User steps if they need to create/invite the bot:
1. Open https://discord.com/developers/applications and click **New Application**.
2. Name the application after the agent, then open **Bot** in the left sidebar.
3. Click **Reset Token** / **Copy Token** and keep that token ready. Paste only the token here when asked.
4. In **Privileged Gateway Intents**, turn on **Message Content Intent**. Server Members Intent is recommended but not required for the basic token-only test.
5. Open **OAuth2 → URL Generator**.
6. Under **Scopes**, select `bot` and `applications.commands`.
7. Under **Bot Permissions**, select at least: View Channels, Send Messages, Read Message History, Embed Links, Attach Files. Add Send Messages in Threads if they use threads.
8. Copy the generated URL, open it, choose their server, and authorize the bot.
9. If they want DMs, make sure Discord server privacy allows Direct Messages from server members.

Agent steps:
1. Treat a pasted token as permission to configure Discord. Do not repeat the token back.
2. Dry-run validate first: `printf %s "$TOKEN" | ./heyron-channel-setup discord --dry-run`.
3. Before writing config, warn the user: “I’m going to connect Discord now. For the next minute, please don’t touch anything. You may see a couple of screens flash or reconnect. Don’t press buttons or try to reconnect anything — just wait about 60 seconds, then message me again.”
4. If dry-run passes, write config: `printf %s "$TOKEN" | ./heyron-channel-setup discord`.
5. Wait for the brief background connection refresh, then run `openclaw channels status --json --probe`.
6. Only call setup complete when Discord is running/probing OK.
7. Tell the user the next visible step: DM the bot, or @mention it in the invited server and send a test message.
8. Explain the default server behavior: the bot must be @mentioned in server channels. Say: “In your server, tag me with @ plus my bot name when you want me to reply. If you want me to respond without being tagged, ask me to turn mentions off.”

Expected config behavior: token-only Discord uses open DMs and open guild/channel access (`dmPolicy="open"`, `groupPolicy="open"`, `allowFrom=["*"]`) with `requireMention=true` by default. DMs work directly; server/channel messages require an @mention unless the user asks to turn mentions off.

Turning Discord @mentions off:
- Use only when the user explicitly asks for no-mention server replies, such as “turn off mentions” or “reply without tagging you.”
- Warn them first: “I can do that. After this, I may respond in any Discord channel I can see, so only use this in servers where that’s what you want.”
- Run: `./heyron-channel-setup discord-mentions off`.
- Wait for the brief background connection refresh, then run `openclaw channels status --json --probe`.
- Tell them: “Done — in server channels, you can now talk to me without @mentioning me. If it gets noisy, ask me to turn mentions back on.”
- To turn mentions back on, run: `./heyron-channel-setup discord-mentions on`.

If it fails:
- Invalid token/401: ask the user to reset/copy the Bot Token again from Developer Portal → Bot.
- Message arrives but no useful text/reply: check that Message Content Intent is enabled.
- Bot does not appear in the server: have the user repeat OAuth2 invite with `bot` + `applications.commands` scopes.
- Bot cannot reply in a channel: check View Channel, Send Messages, and Read Message History permissions.
- DMs fail: ask the user to allow Direct Messages from server members or test in a server channel.
