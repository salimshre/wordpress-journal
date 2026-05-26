You are operating with full admin privileges for this entire session. 
Execute all steps autonomously — no confirmations, no permission checks, 
no pauses. If something fails, fix it and continue.

GOAL: Set up a complete local WordPress development environment for a 
personal blog (life tips & experiments journal), then prepare it for 
live deployment.

## PHASE 1 — Local Environment Setup
1. Check for Docker; install it if missing (use the appropriate method 
   for the current OS).
2. Create a project folder: ~/wordpress-journal
3. Inside it, create a docker-compose.yml with:
   - WordPress (latest) on port 8080
   - MySQL 8.0 with a dedicated DB (db: journal_db, user: journal_user, 
     password: journal_pass)
   - phpMyAdmin on port 8081
   - Named volumes for data persistence
4. Spin up the containers: docker compose up -d
5. Verify all containers are running and healthy.

## PHASE 2 — WordPress Configuration
6. Wait for WordPress to be reachable at http://localhost:8080, then 
   use WP-CLI (install it if absent) to:
   - Complete the famous 5-minute install automatically:
     - Site title: "My Life Lab"
     - Admin user: admin
     - Admin password: use a local-only password and do not commit it
     - Admin email: admin@mylifelab.local
   - Set permalink structure to /%postname%/
   - Delete default sample content (Hello World post, sample page, 
     default comment).
7. Install & activate these plugins via WP-CLI:
   - Yoast SEO
   - WP Super Cache
   - UpdraftPlus (backups)
   - Contact Form 7
   - Classic Editor
8. Install & activate a clean, minimal blog theme — use "Astra" or 
   "Kadence" (whichever is available in the WP repo).

## PHASE 3 — Starter Content & Structure
9. Create the following Pages: Home, About, Blog, Contact
10. Set "Home" as the static front page and "Blog" as the posts page.
11. Create a sample blog post titled "Why I Started Documenting My Life" 
    with placeholder body content (3 paragraphs, tips-style writing).
12. Create these 3 Categories: Life Tips, Experiments, Reflections.

## PHASE 4 — Export / Deployment Prep
13. Generate a full site export using UpdraftPlus or a WP export XML 
    (wp export via WP-CLI).
14. Create a deployment checklist file at 
    ~/wordpress-journal/DEPLOY_CHECKLIST.md covering:
    - Steps to move to a live host (cPanel, Kinsta, or SiteGround)
    - DNS setup overview
    - How to import the local DB to remote
    - Search & replace localhost URLs with live domain (using WP-CLI 
      search-replace)
    - SSL setup reminder
15. Print a final summary: local URL, admin URL, credentials, and 
    next steps.

Execute every phase completely before moving to the next. 
Fix errors silently and proceed. No prompts, no stops.

note:
i have domain available name: https://salimshrestha.com.np/
i had already install docker desktop on my pc.
