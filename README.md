# My Life Lab WordPress Journal

A local WordPress development setup for a personal blog focused on life tips, routines, experiments, study systems, workout tracking, and practical lessons from long-term journaling.

This repository is for local development and deployment preparation for:

```text
https://salimshrestha.com.np/
```

## What Is Included

- WordPress on `http://localhost:8080`
- phpMyAdmin on `http://localhost:8081`
- MySQL 8.0 with persistent Docker volumes
- WP-CLI setup scripts
- Astra theme
- Yoast SEO, WP Super Cache, UpdraftPlus, Contact Form 7, and Classic Editor
- Public-safe journal-derived starter posts
- WordPress export XML in `exports/wordpress-journal.xml`
- Structured documentation in `Documentation/`

## Local Setup

Start the stack:

```bash
docker compose up -d
```

Check containers:

```bash
docker compose ps
```

Open the local site:

```text
http://localhost:8080
```

Open WordPress admin:

```text
http://localhost:8080/wp-login.php
```

Open phpMyAdmin:

```text
http://localhost:8081
```

## WordPress Content Setup

Run the base setup script:

```bash
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache -v "$PWD/scripts:/scripts" wpcli eval-file /scripts/setup-wordpress.php
```

Import the public-safe journal content:

```bash
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache -v "$PWD/scripts:/scripts" wpcli eval-file /scripts/import-journal-content.php
```

Refresh permalinks:

```bash
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache wpcli rewrite flush
```

## Export

Generate a WordPress XML export:

```bash
mkdir -p exports
docker compose run --rm --user 33:33 -e WP_CLI_CACHE_DIR=/tmp/wp-cli-cache -v "$PWD/exports:/exports" wpcli export --dir=/exports --filename_format=wordpress-journal.xml
```

The export file is written to:

```text
exports/wordpress-journal.xml
```

## Deployment Reminder

Before moving this site to production:

- Do not reuse local admin credentials on the live site.
- Create or rotate the live WordPress admin password before launch.
- Replace local URLs with the live domain using WP-CLI search-replace.
- Enable SSL for `https://salimshrestha.com.np/`.
- Configure backups before publishing.

See `Documentation/deployment/DEPLOY_CHECKLIST.md` for the full deployment checklist.

## Documentation

All project documentation is organized in:

```text
Documentation/
```

Start with:

```text
Documentation/README.md
```

## Security Notes

- Keep live credentials out of Git.
- Keep private journal files out of this repository.
- Use the encrypted local document only for private notes or credentials.
- Treat the Docker database passwords as local development defaults only.
