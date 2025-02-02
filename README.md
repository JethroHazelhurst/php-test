# Optimized Development Workflow

This workflow is designed to provide a secure, automated, and portable development pipeline, ensuring alignment with industry best practices.

## Local Environment Setup

### OS: Windows 10

Look to move to Linux

### Editor: VS Code

- Integrated Terminal
- Extensions:
  - PHP Debug
  - GitHub Copilot
  - JDBC Database Client

### Development Stack

- **Laragon** (PHP/MySQL/Apache)
- **Git** (version control)
- **Composer** (Phinx and other PHP dependencies)
- **NPM** (SASS, BrowserSync)

---

## Project Initialization

### Remote Setup

- Create a **private GitHub repository**.
- Create a **Cloudways app** (no .env setup required initially).

### Local Setup

1. Create a project folder in `laragon/www`.
2. Open the folder in VS Code and initialize Git:
   ```bash
   git init
   git remote add origin [GITHUB_REPO_URL]
   ```
3. Initialize NPM and Composer:
   ```bash
   npm init -y
   composer init -y
   ```
4. Create the following configuration files:
   - `.env` (local configuration)
   - `.env.example` (template for production/staging)
   - `README.md` (setup and deploy instructions)

### Tooling Setup

- Install **Phinx** (for database migrations):
  ```bash
  composer require robmorgan/phinx
  ./vendor/bin/phinx init
  ```
- Add the following scripts to `package.json`:
  ```json
  "scripts": {
    "dev": "npm-run-all --parallel sass:watch browsersync",
    "sass:watch": "sass --watch scss:public/css",
    "browsersync": "browser-sync start --proxy='localhost' --files='**/*'",
    "build": "sass scss:public/css --style=compressed && terser js/*.js -o public/js/app.min.js"
  }
  ```
- Update `.gitignore` to exclude sensitive and generated files:
  ```
  .env
  node_modules/
  vendor/
  public/css/
  public/js/
  ```

### First Commit

```bash
git checkout -b develop  # Create and switch to develop branch
git add . && git commit -m "Initial commit"
git push -u origin develop
```

---

## Development Workflow

### Start Development

- Laragon auto-starts PHP and MySQL services.
- Run the development environment:
  ```bash
  npm run dev  # Watches SCSS/JS and reloads the browser
  ```

### Coding Workflow

1. Create feature branches:
   ```bash
   git checkout -b feature/new-login
   ```
2. Push changes to the remote branch:
   ```bash
   git push origin feature/new-login
   ```
3. Create a Pull Request (PR) from `feature/*` to `develop`.

### Testing and Staging

- GitHub Actions runs tests, builds assets, and deploys to the staging site on PR merges.

### Production Deployment

- Create a PR from `develop` to `main`.
- GitHub Actions deploys changes to production after tests and builds.

---

## GitHub Actions Pipeline

```yaml
name: CI/CD

on:
  push:
    branches: [develop, main]
  pull_request:
    branches: [develop, main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Install PHP dependencies
        run: composer install
      - name: Run PHPUnit tests
        run: vendor/bin/phpunit
      - name: Build assets
        run: npm ci && npm run build

  deploy-staging:
    needs: test
    if: github.ref == 'refs/heads/develop'
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Deploy to Staging
        uses: appleboy/ssh-action@v1
        env:
          DB_PASSWORD: ${{ secrets.STAGING_DB_PASSWORD }}
        with:
          host: ${{ secrets.STAGING_HOST }}
          username: ${{ secrets.STAGING_USER }}
          key: ${{ secrets.STAGING_SSH_KEY }}
          script: |
            cd /path/to/staging
            git pull origin develop
            echo "DB_PASSWORD=$DB_PASSWORD" >> .env
            composer install --no-dev
            npm run build
            ./vendor/bin/phinx migrate

  deploy-prod:
    needs: test
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Deploy to Production
        uses: appleboy/ssh-action@v1
        env:
          DB_PASSWORD: ${{ secrets.PROD_DB_PASSWORD }}
        with:
          host: ${{ secrets.PROD_HOST }}
          username: ${{ secrets.PROD_USER }}
          key: ${{ secrets.PROD_SSH_KEY }}
          script: |
            cd /path/to/production
            git pull origin main
            echo "DB_PASSWORD=$DB_PASSWORD" >> .env
            composer install --no-dev
            npm run build
            ./vendor/bin/phinx migrate
```

---

## Key Improvements

### Security

- `.env` files are never committed or transferred manually.
- Production secrets are securely stored in **GitHub Secrets**.

### Branching Strategy

- `develop` = Staging, `main` = Production (standard convention).

### Automation

- CI/CD pipeline automates tests, asset builds, and database migrations.

### Platform Agnostic

- Compatible with various hosts (AWS, DigitalOcean, etc.).

---

## Deployment Security Checklist

- Ensure `.env` is never committed (add to `.gitignore`).
- Store all production secrets in GitHub Secrets.
- Use **Phinx** to manage database changes.
- Restrict SSH keys to read-only access where applicable.

---

## Optional Upgrades

- **Static Analysis**: Add PHPStan or Psalm to `composer.json`.
- **Backups**: Automate MySQL dumps via the Cloudways API.
- **WSL2**: Improve performance and future Docker integration.

This workflow maintains security, automation, and portability while supporting your preference for Laragon.

