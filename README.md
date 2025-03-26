# iKarRental 🚗💨

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A complete car rental management system built with pure PHP. This application allows users to browse and book vehicles while providing administrators with tools to manage the fleet.

## Table of Contents
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Technical Details](#technical-details)
- [Database Structure](#database-structure)
- [License](#license)

## Features

### User Experience
- 🚘 Browse available vehicles with filters (date range, transmission, capacity, price)
- 📅 Book vehicles for specific time periods
- 👤 User registration and authentication system
- 📋 Personal profile page with booking history
- 🔍 Detailed vehicle information pages

### Admin Capabilities
- 👔 Admin dashboard (login: admin@ikarrental.hu / password: admin)
- ➕ Add new vehicles to the fleet
- ✏️ Edit existing vehicle details
- ❌ Remove vehicles (with associated bookings)
- 👁️ View all system bookings

### Technical Features
- 🔒 Secure authentication system
- 📱 Responsive design
- 📝 Form validation (client-side and server-side)
- 📊 Booking conflict prevention
- 💰 Automatic price calculation

## Installation

### Requirements
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)

### Setup
1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/ikarrental.git
