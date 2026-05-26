# My Life Lab WordPress Journal Runbook

This document records the full workflow used to create, populate, export, and deploy the `wordpress-journal` project. Use it when rebuilding the same setup later.

Live static site:

```text
https://salimshre.github.io/wordpress-journal/
```

Local WordPress:

```text
http://localhost:8080
```

## 1. Project Goal

Build a local WordPress blog named `My Life Lab` for public-safe life tips and experiments derived from private journal notes.

Important content rule:

- Do publish: general lessons, routines, experiments, study systems, workout tracking, productivity notes.
- Do not publish: private diary details, names, personal events, credentials, sensitive data, or unfiltered journal text.

## 2. Local Stack

The local stack uses Docker Compose:

- WordPress latest on port `8080`
- MySQL 8.0
- phpMyAdmin on port `8081`
- WP-CLI container
- Named Docker volumes for persistence

Primary file:

```text
docker-compose.yml
```

Start the stack:

```bash
docker compose up -d
```

Check status:

```bash
docker compose ps
```

Open locally:

```text
Site:       http://localhost:8080
Admin:      http://localhost:8080/wp-login.php
phpMyAdmin: http://localhost:8081
```

Security note: local credentials should not be reused on production.

## 3. WordPress Setup

The project includes scripts for repeatable WordPress setup.

Base setup script:

```text
scripts/setup-wordpress.php
```

It creates/updates:

- Pages: `Home`, `About`, `Blog`, `Contact`
- Static front page: `Home`
- Posts page: `Blog`
- Categories: `Life Tips`, `Experiments`, `Reflections`
- A starter post if needed

Run it:

```bash
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache -v "$PWD/scripts:/scripts" wpcli eval-file /scripts/setup-wordpress.php
```

Flush permalinks:

```bash
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache wpcli rewrite flush
```

## 4. Plugins and Theme

Installed and activated plugins:

- Yoast SEO
- WP Super Cache
- UpdraftPlus
- Contact Form 7
- Classic Editor

Active theme:

- Astra

Install manually with WP-CLI if rebuilding:

```bash
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache wpcli plugin install wordpress-seo wp-super-cache updraftplus contact-form-7 classic-editor --activate --force
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache wpcli theme install astra --activate
```

If plugin installs fail because of permissions, fix WordPress content ownership:

```bash
docker compose exec -u root wordpress chown -R www-data:www-data /var/www/html/wp-content
```

## 5. Journal Content Import

Private journal source directory used during this session:

```text
C:\Users\StudyAcer\Documents\GitHub\journal\JOURNAL AND PLANS\Journal
```

The public-safe content was summarized manually into:

```text
scripts/import-journal-content.php
```

The script creates 10 polished posts:

- A Morning Reset for Days That Start Slowly
- The 10-Minute Start Rule
- My Pomodoro Study Experiment
- What Workout Tracking Taught Me About Discipline
- Reducing Digital Distraction by Adding Friction
- A Better Daily Journal Template
- How to Turn a Bad Day Into Data
- Weekly Planning Without Overthinking
- Learning From People Without Copying Everything
- The Small Action Recovery Plan

Run the import:

```bash
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache -v "$PWD/scripts:/scripts" wpcli eval-file /scripts/import-journal-content.php
```

Verify posts:

```bash
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache wpcli post list --post_type=post --post_status=publish --fields=post_title,post_name --format=table
```

Verify categories:

```bash
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache wpcli term list category --fields=name,slug,count --format=table
```

## 6. WordPress XML Export

Generate export:

```bash
mkdir -p exports
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache -v "$PWD/exports:/exports" wpcli export --dir=/exports --filename_format=wordpress-journal.xml
```

Export path:

```text
exports/wordpress-journal.xml
```

Use this export for migration or backup.

## 7. Static Site Generation

For free hosting, the WordPress site was converted to static HTML and deployed with GitHub Pages.

Static generator:

```text
scripts/generate-static-site.php
```

Generated output:

```text
docs/
```

Generate static site for GitHub Pages project URL:

```bash
mkdir -p docs
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache -e STATIC_BASE_URL=/wordpress-journal -v "$PWD/scripts:/scripts" -v "$PWD/docs:/static" wpcli eval-file /scripts/generate-static-site.php
```

Verify local output:

```text
docs/index.html
docs/blog/index.html
docs/assets/site.css
```

The static site includes:

- Home page
- Blog index
- About page
- Contact page
- Individual post pages
- CSS asset
- `.nojekyll`
- `robots.txt`

## 8. GitHub Pages Deployment

Repository:

```text
https://github.com/salimshre/wordpress-journal.git
```

Branch:

```text
main
```

GitHub Pages source:

```text
main branch, /docs folder
```

Deploy changes:

```bash
git add docs scripts/generate-static-site.php
git commit -m "Update static WordPress journal site"
git push origin main
```

Enable GitHub Pages through the repository settings:

```text
Settings > Pages > Build and deployment > Source: Deploy from a branch
Branch: main
Folder: /docs
Save
```

During this session, GitHub Pages was enabled with the GitHub API and confirmed as:

```text
https://salimshre.github.io/wordpress-journal/
```

Verify deployment:

```bash
curl -I https://salimshre.github.io/wordpress-journal/
curl -I https://salimshre.github.io/wordpress-journal/blog/
```

Expected result:

```text
HTTP 200
```

## 9. Future Content Update Workflow

Use this workflow whenever you edit local WordPress content and want to update the live static site.

1. Start Docker:

```bash
docker compose up -d
```

2. Edit content in local WordPress:

```text
http://localhost:8080/wp-login.php
```

3. Regenerate static HTML:

```bash
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache -e STATIC_BASE_URL=/wordpress-journal -v "$PWD/scripts:/scripts" -v "$PWD/docs:/static" wpcli eval-file /scripts/generate-static-site.php
```

4. Verify generated files:

```bash
git status --short
```

5. Commit and push:

```bash
git add docs
git commit -m "Update published journal content"
git push origin main
```

6. Open the live site:

```text
https://salimshre.github.io/wordpress-journal/
```

## 10. Custom Domain Later

Target domain:

```text
https://salimshrestha.com.np/
```

When ready:

1. Add a `CNAME` file in `docs/`:

```text
salimshrestha.com.np
```

2. Regenerate or keep the file after regeneration.

3. In GitHub:

```text
Settings > Pages > Custom domain
```

4. In DNS, point the domain to GitHub Pages.

Common GitHub Pages DNS records:

```text
A     @    185.199.108.153
A     @    185.199.109.153
A     @    185.199.110.153
A     @    185.199.111.153
CNAME www  salimshre.github.io
```

5. Enable HTTPS in GitHub Pages after DNS verifies.

6. Regenerate the static site with the final base URL:

```bash
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache -e STATIC_BASE_URL=https://salimshrestha.com.np -v "$PWD/scripts:/scripts" -v "$PWD/docs:/static" wpcli eval-file /scripts/generate-static-site.php
```

7. Commit and push:

```bash
git add docs
git commit -m "Configure custom domain static site"
git push origin main
```

## 11. Security Checklist

- Do not commit WordPress admin passwords.
- Do not commit live hosting credentials.
- Do not commit private journal files.
- Keep `readme-encrypted.docx` ignored by Git.
- Rotate admin credentials before any real WordPress migration.
- Treat Docker database passwords as local development defaults only.

Current ignored private file:

```text
readme-encrypted.docx
```

## 12. Troubleshooting

Docker command not found:

- Use Docker Desktop full path on Windows if needed:

```powershell
& 'C:\Program Files\Docker\Docker\resources\bin\docker.exe' compose ps
```

Docker engine permission denied:

- Start Docker Desktop.
- Ensure the current Windows user can access Docker Desktop.
- Retry after Docker Desktop fully starts.

WP-CLI cannot write plugins/uploads:

```bash
docker compose exec -u root wordpress chown -R www-data:www-data /var/www/html/wp-content
```

GitHub Pages shows 404:

- Confirm Pages is enabled for `main /docs`.
- Confirm `docs/index.html` is committed and pushed.
- Wait a few minutes for Pages to rebuild.

Static links broken on GitHub Pages:

- Regenerate with:

```bash
STATIC_BASE_URL=/wordpress-journal
```

Static links broken on custom domain:

- Regenerate with:

```bash
STATIC_BASE_URL=https://salimshrestha.com.np
```

## 13. Files Created or Updated

Core setup:

```text
docker-compose.yml
scripts/setup-wordpress.php
scripts/import-journal-content.php
scripts/generate-static-site.php
```

Documentation:

```text
README.md
Documentation/README.md
Documentation/deployment/DEPLOY_CHECKLIST.md
Documentation/runbooks/PROJECT_RUNBOOK.md
Documentation/troubleshooting/DOCKER_DESKTOP_WSL_FIX.md
```

Static deployment:

```text
docs/
```

Export:

```text
exports/wordpress-journal.xml
```
