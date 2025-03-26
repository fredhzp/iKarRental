# Kingdom's Railway Network 🚂

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A puzzle game where you design a circular railway through challenging terrain. Built with pure JavaScript, HTML, and CSS.

![Game Screenshot](/assets/screenshots/gameplay.png)

## Table of Contents
- [Features](#features)
- [How to Play](#how-to-play)
- [Installation](#installation)
- [Development](#development)
- [License](#license)

## Features

### Game Mechanics
- 🎮 Two difficulty levels (5x5 and 7x7 grids)
- ⛰️ Four terrain types with unique constraints:
  - Empty tiles
  - Bridges (straight tracks only)
  - Mountains (90° turns only)
  - Oases (no tracks allowed)
- 🔄 Single continuous loop requirement
- ⏱️ Timer with leaderboard

### Technical
- 💯 Pure JavaScript (no frameworks)
- 📱 Responsive design
- 💾 LocalStorage persistence for:
  - Game state
  - Leaderboards
- 🧩 Puzzle validation system
- ✨ Smooth UI transitions

## How to Play

1. **Start Menu**:
   - Enter your name
   - Select difficulty (Easy/Hard)
   - Click "Start Game"

2. **Gameplay**:
   - Left-click cells to cycle through track pieces
   - Right-click to rotate pieces
   - Create a complete loop connecting all accessible cells
   - The puzzle is complete when:
     - All required cells are connected
     - The path forms a single loop
     - No invalid placements exist

3. **Completion**:
   - Your time is recorded
   - View your position on the leaderboard
   - Return to menu to play again

## Installation

No installation required! Simply:

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/kingdom-railway.git
