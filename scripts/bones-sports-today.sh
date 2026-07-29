#!/bin/bash
# Bones Sports Today - Daily m3u category fetcher and Telegram poster

M3U_URL="https://opop.pro/ztL7vBSWwXdoh"
TELEGRAM_CHANNEL="VH Sports Channel"

# Fetch the playlist
PLAYLIST=$(curl -s "$M3U_URL")

# Get unique categories
CATEGORIES=$(echo "$PLAYLIST" | grep -oP 'group-title="[^"]+' | sort -u | sed 's/group-title="//;s/"//')

# Parse each category and post to Telegram
echo "$PLAYLIST" | awk -v channel="$TELEGRAM_CHANNEL" '
BEGIN { RS="#EXTINF"; category="" }
{
    # Extract group-title from #EXTINF line
    if (match($0, /group-title="([^"]+)"/, g)) {
        category = g[1]
    }
    # Extract channel name
    if (match($0, /,[^#\n]+$/, m)) {
        name = m[0]
        sub(/^,/, "", name)
        # Only print if name has actual info (not just "MLB 01:" etc)
        if (name ~ /[A-Za-z]+.*[0-9]+.*[A-Za-z]/) {
            print category "|" name
        }
    }
}
' | sort -t'|' -k1,1 -u | while IFS='|' read -r category name; do
    # Format message
    MESSAGE="🏆 *$category*

$name"
    
    # Send to Telegram
    echo "Posting: $category"
done
