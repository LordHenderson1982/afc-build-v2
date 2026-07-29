#!/bin/bash
# Bones Sports Daily - Fetch m3u and post to Telegram
# Simple parsing approach

M3U_URL="https://opop.pro/ztL7vBSWwXdoh"
TELEGRAM_BOT_TOKEN="${TELEGRAM_BOT_TOKEN}"
TELEGRAM_CHAT_ID="${TELEGRAM_CHAT_ID}"

if [ -z "$TELEGRAM_BOT_TOKEN" ] || [ -z "$TELEGRAM_CHAT_ID" ]; then
    echo "Error: TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID not set"
    exit 1
fi

# Fetch the playlist
PLAYLIST=$(curl -s "$M3U_URL")

# Build message
MESSAGE="🏆 Bones Sports Today%0A%0A"

# Parse each line: extract category|name pairs
CHANNELS=$(echo "$PLAYLIST" | sed -n 's/.*group-title="\([^"]*\)".*tvg-name="\([^"]*\)".*/\1|\2/p' | grep -v '|$')

# Group by category and build message
CURRENT_CAT=""
while IFS='|' read -r category name; do
    [ -z "$category" ] && continue
    [ -z "$name" ] && continue
    
    # Skip empty entries
    echo "$name" | grep -qE '^[A-Za-z]+.*[0-9]' || continue
    
    # New category header
    if [ "$category" != "$CURRENT_CAT" ]; then
        CURRENT_CAT="$category"
        ESC_CAT=$(python3 -c "import urllib.parse; print(urllib.parse.quote('$category'))")
        MESSAGE="${MESSAGE}%0A%F0%9F%9F%A8 $ESC_CAT%0A"
    fi
    
    # Add channel name
    ESC_NAME=$(python3 -c "import urllib.parse; print(urllib.parse.quote('$name'))")
    MESSAGE="${MESSAGE}${ESC_NAME}%0A"
done <<< "$CHANNELS"

# Send to Telegram
curl -s -X POST "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage" \
    -d chat_id="$TELEGRAM_CHAT_ID" \
    -d text="$MESSAGE" \
    -d parse_mode="HTML" > /dev/null

echo "Bones Sports posted at $(date)"
