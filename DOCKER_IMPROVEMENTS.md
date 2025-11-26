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
