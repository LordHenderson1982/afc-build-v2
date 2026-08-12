# VPS Infrastructure

## VH Sports VPS

**Provider:** Njalla
**OS:** Ubuntu 26.04
**IP:** 80.78.27.88
**SSH Port:** 1106

### API Access

**Port:** 3333
**Endpoint:** `http://80.78.27.88:3333/run`
**Auth:** API key (stored in `.vps-config.json`)

### Access

| User | Purpose | SSH Key |
|------|---------|---------|
| root | Initial setup, admin | SSH key added at OS install |
| AgentNigel | Daily use, scripts | Same as root |

### Security

- [x] SSH port changed from 22 → 1106
- [ ] UFW enabled
- [x] User 'AgentNigel' created with sudo access
- [x] Root login set to 'prohibit-password' (key-only)
- [x] Command API running on port 3333 (AgentNigel user)

### Services Running

- **cmd-api** (port 3333) - HTTP command execution API

---

## Setup Log

### 2026-07-01
- Fresh Ubuntu 26.04 install via Njalla
- Changed SSH port to 1106
- Installed UFW firewall