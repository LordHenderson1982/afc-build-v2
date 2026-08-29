# TAP Bowling

A comprehensive bowling app for players, leagues, and alley operators.

---

## Core Features

### 1. Scorekeeping
- Frame-by-frame scoring (standard 10-pin)
- Calculate scores automatically
- Track series history (multiple games per session)
- Calculate and track bowling average
- Handicap calculation (based on league rules)
- Split tracking and conversion rates
- Perfect game / 300 recognition
- Export scores as PDF/CSV

### 2. Lessons & Tips
- Video tutorials library
  - Beginner: How to hold the ball, approach, release
  - Intermediate: Spare shooting, timing
  - Advanced: Hook, speed control, oil patterns
- Tips from pro bowlers
- Technique guides with photos
- Lesson booking with certified instructors
- In-app purchases for premium content

### 3. Social Club
- User profiles (average, games played, strikes/spares)
- Friends list
- Challenge friends to matches
- Leaderboards (global, friends, local)
- Chat / messaging
- Share scores to social media
- Find local bowling friends

### 4. League Management
- Create/join leagues
- Team management
- Standings and schedules
- Handicap tracking
- Automated lane assignments
- League fee collection
- Match play and series formats

### 5. Booking System
- Find nearby bowling alleys
- View alley info (lanes, pricing, hours)
- Book lanes in advance
- Payment integration
- Special events booking (birthday parties, corporate)

---

## User Types

| User Type | Description |
|-----------|-------------|
| **Casual Bowler** | Tracks personal scores, watches tips, books lanes |
| **League Bowler** | Joins leagues, manages team, tracks handicap |
| **League Admin** | Creates leagues, manages standings, schedules |
| **Instructor** | Offers lessons, manages booking schedule |
| **Alley Operator** | Manages lanes, events, promotions |

---

## Revenue Model

### Free Tier
- Basic scorekeeping
- Limited score history (last 10 games)
- Basic tips (first 5 videos)
- Find alleys (read-only)

### Premium Tier ($4.99/month)
- Unlimited score history
- Advanced analytics (trends, strike rates)
- All video lessons
- Challenge friends
- League creation
- Booking with no fees

### Instructor Revenue
- Instructors keep 70% of lesson fees
- Platform takes 30%

### Affiliate
- Bowling ball / shoe recommendations
- Equipment affiliate links
- Alley partnerships

---

## Technical Architecture

### Mobile (React Native)
- iOS & Android apps
- Offline scorekeeping (sync when online)
- Push notifications for league updates
- Camera for video tips

### Backend (Node.js/Python)
- RESTful API
- User authentication (JWT)
- WebSocket for real-time chat
- Payment processing (Stripe)

### Database (PostgreSQL)
- Users, profiles, scores
- League data
- Lesson content
- Alley directory

### Infrastructure
- AWS or DigitalOcean
- Docker containers
- CDN for video content

---

## Development Phases

### Phase 1: MVP (Weeks 1-4)
- [ ] User authentication
- [ ] Basic scorekeeping (single player)
- [ ] Score history
- [ ] Simple user profile

### Phase 2: Social (Weeks 5-8)
- [ ] Friends list
- [ ] Challenge system
- [ ] Leaderboards
- [ ] Basic chat

### Phase 3: Leagues (Weeks 9-12)
- [ ] League creation
- [ ] Team management
- [ ] Standings
- [ ] Scheduling

### Phase 4: Content (Weeks 13-16)
- [ ] Video lesson library
- [ ] Tips section
- [ ] Instructor profiles
- [ ] Lesson booking

### Phase 5: Booking (Weeks 17-20)
- [ ] Alley directory
- [ ] Lane availability
- [ ] Booking flow
- [ ] Payments

---

## Competitors

| App | Strengths | Weaknesses |
|-----|-----------|------------|
| **League Secretary** | League focused | dated UI |
| **Bowling Score** | Simple scoring | no social |
| **Bowlder's Companion** | Statistics | limited features |
| **TapScore** | Pro features | expensive |

**Opportunity:** Combine all features into one modern app with strong social and league features.

---

## Branding

- **Name:** TAP Bowling
- **Colors:** Navy blue, orange (pins), white
- **Tagline:** "Your game, elevated."
- **Logo:** Bowling pin + tap motion

---

## Questions to Answer

1. MVP feature set - what's minimum viable?
2. Build for iOS or Android first? (or both?)
3. Self-build or outsource?
4. Funding model - bootstrap or seek investment?
5. Target launch date?

---

*Created: 2026-07-02*
*Status: MVP Phase 1 - Project Setup*

---

# MVP: TAP Bowling - Find & Book Bowling Alleys

**Goal:** Any bowler can find and book lanes at any alley in the USA (eventually worldwide)

## Features (MVP)
1. Alley directory with search
2. Alley detail pages
3. Simple booking request (email to alley)
4. User accounts (save favorites)