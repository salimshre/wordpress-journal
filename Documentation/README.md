# Documentation

Structured documentation for the `wordpress-journal` project.

## Folder Structure

```text
Documentation/
├── README.md
├── deployment/
│   └── DEPLOY_CHECKLIST.md
├── runbooks/
│   └── PROJECT_RUNBOOK.md
└── troubleshooting/
    └── DOCKER_DESKTOP_WSL_FIX.md
```

## Recommended Reading Order

1. `runbooks/PROJECT_RUNBOOK.md`  
   Complete step-by-step record of the local WordPress setup, journal content import, static site generation, GitHub Pages deployment, and future update workflow.

2. `deployment/DEPLOY_CHECKLIST.md`  
   Checklist for moving from local or static hosting toward a live domain or production WordPress host.

3. `troubleshooting/DOCKER_DESKTOP_WSL_FIX.md`  
   Docker Desktop and WSL troubleshooting notes.

## Important Project Paths

Local WordPress source:

```text
http://localhost:8080
```

Free deployed static site:

```text
https://salimshre.github.io/wordpress-journal/
```

Generated static site folder:

```text
docs/
```

WordPress export:

```text
exports/wordpress-journal.xml
```

Automation scripts:

```text
scripts/
```

## Security Reminder

Do not commit live credentials, WordPress admin passwords, private journal files, or unfiltered diary content. Keep private notes in ignored or encrypted local files only.
