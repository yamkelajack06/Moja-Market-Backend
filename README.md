# Moja Market Backend

**University Group Project | Database Fundamentals & Mobile Computing**

Moja Market is a peer-to-peer marketplace Android app built for university students to buy, sell, and post item requests within their campus community. I designed and built the entire backend: a PHP REST API, a PostgreSQL relational database, and a Dockerised deployment pipeline hosted on a live server.

## Stack
PHP 8.2 · Apache · PostgreSQL · Cloudinary · Docker

## Features

**Authentication:** Secure registration and login system using bcrypt password hashing. Passwords are never stored in plain text and are never returned to the client under any circumstance.

**Marketplace Listings:** Full create, read, update, and delete support for item listings. Each listing supports multiple images stored as relational records, a stock quantity, condition, location, and live status (Available / Sold Out).

**Want Requests:** Buyers can post requests for specific items they are looking for, complete with a budget and a status that flips between Looking and Fulfilled as deals are made.

**Ratings System:** Users can rate items they have transacted on with a 1 to 5 star system. The API exposes average rating and total rater count per listing, with duplicate rating prevention enforced at the database level.

**Image Uploads:** A dual-mode upload pipeline handles images in both development and production environments. In production, images are uploaded directly to Cloudinary via cURL and a permanent hosted URL is returned to the app, keeping the server storage clean.

**Database Design:** A fully normalised PostgreSQL schema across users, items, images, want requests, and ratings tables, with parameterised queries used on every single endpoint to prevent SQL injection.

**API Architecture:** 15+ REST endpoints organised across auth, posts, users, images, and ratings, routed through a clean central router with consistent JSON response formatting across the entire API.

**Deployment:** Dockerised with PHP 8.2 and Apache, with mod_rewrite enabled for clean URL routing and environment variables managing all credentials securely via a .env file kept out of version control. The API is deployed and live on Render, serving the Android app in production.

## Contributors
Blessings Radingoane · Samkelo Mthembu · Yamkela Jack
