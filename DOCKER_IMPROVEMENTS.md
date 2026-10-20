# Docker Environment Improvements

This document summarizes the changes made to improve the Docker development environment for FieryVoid.

## 1. Terminal Experience Improvements

### Bash Default Shell
- **Change**: Set `bash` as the default shell in `docker/php/Dockerfile`.
- **Benefit**: Enables arrow key navigation, command history, and better interactive features.
- **Implementation**: Added `SHELL ["/bin/bash", "-c"]` and symlinked `/bin/sh` to `/bin/bash`.

### Persistent Command History
- **Change**: Configured bash history to save to a persistent volume.
- **Benefit**: Command history survives container restarts and rebuilds.
- **Implementation**:
    - Added `bashhistory` volume in `docker-compose.yml`.
    - Set global `ENV` variables in `Dockerfile` (`HISTFILE`, `HISTSIZE`, `PROMPT_COMMAND`).

### Custom Command Prompt
- **Change**: Set a custom `PS1` prompt.
- **Benefit**: Shows the current user, hostname, and **working directory** in the terminal.
- **Implementation**: Added `ENV PS1='\u@\h:\w\$ '` to `Dockerfile`.

## 2. System Stability & Compatibility

### PHP 8.2 Compatibility
- **Issue**: The site was failing with "Unparenthesized ternary operator" errors and duplicate class definitions.
- **Fix**:
    - Installed `libzip-dev` and `zip` extension.
    - Ran `composer update` to upgrade `zetacomponents/console-tools`.
    - Updated `docker/php/start.sh` to exclude `*_old.php`, `layoutTest.php`, and `CraytanLopinb.php` from autoload generation.

### Database Persistence
- **Issue**: Database data was lost every time containers were removed.
- **Fix**: Added a persistent volume for MariaDB.
- **Implementation**:
    - Added `mariadb_data` volume in `docker-compose.yml`.
    - Mounted it to `/var/lib/mysql` in the `mariadb` service.

### MariaDB 11.4 (matches live)
- **Issue**: The local database was MariaDB 10.3 (end of life May 2023), while live runs 11.4.5, so SQL was only ever tested on a different server.
- **Fix**: `docker/mariadb/Dockerfile` is pinned to live's exact version, `mariadb:11.4.5`, with live's settings: character set and collation `utf8mb4` / `utf8mb4_unicode_ci`, time zone Central European (`TZ=Europe/Warsaw`; it was UTC before), and the B5CGM database default `utf8mb3_general_ci` (set in `db/emptyDatabase.sql`). Changed 2026-10-08.
- **Implementation**:
    - `MARIADB_AUTO_UPGRADE=1` upgrades an existing 10.3 volume in place on its first start. Tested on a copy of one: the data was identical afterwards.
    - The container has no `mysql` command any more. Use `mariadb`, `mariadb-dump` and `mariadb-admin` (README, "Backing up the local database").

## 3. Performance
- **Observation**: Startup times are faster.
- **Reason**:
    - **Database**: MariaDB no longer needs to re-initialize the empty database from SQL scripts on every start.
    - **Autoload**: The `phpab` class map generation is more efficient as it skips problematic/duplicate files.

## How to Apply Changes
If you need to rebuild the environment in the future, run:

```bash
docker-compose up -d --build --force-recreate
```
