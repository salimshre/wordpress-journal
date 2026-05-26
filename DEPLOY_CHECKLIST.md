# Deployment Checklist

Live domain: https://salimshrestha.com.np/

## 1. Choose Hosting

- Pick a WordPress-ready host such as cPanel hosting, Kinsta, or SiteGround.
- Create a new WordPress site or an empty hosting account with PHP, MySQL, and HTTPS support.
- Note the remote database name, database user, password, and host.

## 2. Prepare Local Export

- Keep the generated WordPress export XML from `exports/`.
- For a full migration, also back up `wp-content/uploads`, active theme files, and plugin files.
- Optionally create an UpdraftPlus backup from the local WordPress admin dashboard.

## 3. DNS Setup

- In the domain DNS panel for `salimshrestha.com.np`, point the root domain to the host:
  - Use an `A` record if the host provides an IP address.
  - Use a `CNAME` record for `www` if the host provides a canonical hostname.
- Wait for DNS propagation before final SSL validation.

## 4. Move Database and Files

- Import the WordPress XML through `Tools > Import > WordPress` on the live site, or restore an UpdraftPlus backup.
- If moving the database directly, export the local MySQL database and import it into the remote database using phpMyAdmin, Adminer, or the host migration tool.
- Upload `wp-content/uploads` to the live site.

## 5. Replace Local URLs

After the live site is connected to the final database, run:

```bash
wp search-replace 'http://localhost:8080' 'https://salimshrestha.com.np' --skip-columns=guid
```

Then flush permalinks:

```bash
wp rewrite flush
```

## 6. SSL and Final Checks

- Enable SSL in the hosting dashboard, usually via Let's Encrypt.
- Force HTTPS after the certificate is active.
- Confirm the homepage, blog page, posts, contact form, admin login, and permalinks work.
- Configure backups with UpdraftPlus to remote storage.
- Review SEO settings in Yoast SEO before publishing.
