const https = require('https');

const url = 'https://svg.pm:25461/get.php?username=jhenderson&password=welcome1999&output=ts';

https.get(url, (res) => {
  let data = '';
  res.on('data', chunk => data += chunk);
  res.on('end', () => {
    const lines = data.split('\n');
    let currentGroup = '';
    const games = [];
    
    for (let i = 0; i < lines.length; i++) {
      const line = lines[i].trim();
      if (line.startsWith('#EXTINF:')) {
        const match = line.match(/group-title="([^"]+)"/);
        if (match) currentGroup = match[1];
        
        if (currentGroup.includes('MLB Today')) {
          const nameMatch = line.match(/, (.+)$/);
          if (nameMatch) {
            const gameName = nameMatch[1].trim();
            // Skip empty placeholders like "MLB 15:", "MLB 16:"
            if (gameName && gameName.length > 5 && !gameName.match(/^MLB \d+:$/)) {
              games.push(gameName);
            }
          }
        }
      }
    }
    
    if (games.length > 0) {
      console.log('🎯 Savage MLB Games Today\n');
      games.forEach(g => console.log(g));
    } else {
      console.log('No MLB Today games found');
    }
  });
}).on('error', e => console.error('Error:', e.message));