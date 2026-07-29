# Repair Guide

When the user says the agent is broken, typing with no reply, blank, disconnected, or channel setup is failing:

1. Run `node skills/workspace-health-check/scripts/health-check.js` from the workspace.
2. Read the customer summary first.
3. If channel-specific, run or inspect `./heyron-channel-setup` / `skills/heyron-channel-setup/scripts/setup-channel.js`.
4. Do not ask the hosted customer to restart Docker, edit gateway config, install OpenClaw, or debug npm.
5. If the diagnostic says `needs_support`, give Heyron support the support summary.
