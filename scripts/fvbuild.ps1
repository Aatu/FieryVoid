<#
.SYNOPSIS
  FieryVoid build orchestrator: autoload map + static ship files + client bundles.

.DESCRIPTION
  Runs the build steps in dependency order:

    1. Autoload map    phpab via autoload.sh, inside the php container  [-Autoload]
    2. Static ships    generateStaticShipFile.php, inside the container [-Statics]
    3. Client bundles  yarn build, on the host                          [-Client]

  With no flags all three run. -Server = steps 1+2 only - the everyday choice
  while yarn watch / yarn watch:legacy are running, because a full yarn build
  MINIFIES the legacy bundles that watch:legacy keeps readable.

  -Check is the pre-deploy gate and writes nothing: it regenerates the
  autoload map to a temp file and byte-compares it against the committed one,
  then runs the replay harness over the local game corpus.

  Every docker exec passes -w /usr/src/current. Without it, commands run in
  the container-local throwaway copy (/usr/src/fieryvoid) and their output
  never reaches the repo - see AUTOLOAD_GENERATOR_PLAN.md section 1c.

  The php container is found by asking docker compose from the repo root, so
  the compose project name does not matter: a checkout in FieryVoid/ gives
  fieryvoid-php-1, one in fieryvoid8/ gives fieryvoid8-php-1, and both work.
  Override with -Container or $env:FV_PHP_CONTAINER if you run the container
  outside compose.

.EXAMPLE
  .\FieryVoid\scripts\fvbuild.ps1              # full build (autoload + statics + yarn build)
  .\FieryVoid\scripts\fvbuild.ps1 -Server      # autoload + statics (daily driver)
  .\FieryVoid\scripts\fvbuild.ps1 -Autoload    # just the class map
  .\FieryVoid\scripts\fvbuild.ps1 -Check       # pre-deploy gate, writes nothing
  .\FieryVoid\scripts\fvbuild.ps1 -Server -Container my-php   # non-compose container
#>
[CmdletBinding()]
param(
    [switch]$Autoload,
    [switch]$Statics,
    [switch]$Client,
    [switch]$Server,
    [switch]$Check,
    [string]$Container = $env:FV_PHP_CONTAINER
)

$RepoRoot = Split-Path -Parent $PSScriptRoot

function Fail([string]$msg) {
    Write-Host "fvbuild: $msg" -ForegroundColor Red
    exit 1
}

# ---- which steps run
$anyFlag    = $Autoload -or $Statics -or $Client -or $Server -or $Check
$doAutoload = $Autoload -or $Server -or (-not $anyFlag)
$doStatics  = $Statics  -or $Server -or (-not $anyFlag)
$doClient   = $Client   -or (-not $anyFlag)
if ($Check) { $doAutoload = $false; $doStatics = $false; $doClient = $false }

# ---- preflight: the PHP steps need the container
#
# Ask compose from $RepoRoot rather than hardcoding a name: the project name
# follows the checkout directory, and scoping the lookup to this repo also
# means a sibling checkout's container can never be built into by accident.
if ($doAutoload -or $doStatics -or $Check) {
    if (-not $Container) {
        Push-Location $RepoRoot
        try {
            $ids = docker compose ps -q php
            $rc  = $LASTEXITCODE
        } finally {
            Pop-Location
        }
        if ($rc -ne 0) { Fail 'docker compose is not available - is Docker Desktop running?' }

        $ids = @($ids | Where-Object { $_ })
        if ($ids.Count -eq 0) {
            Fail "no php container for this checkout. Start it with:  docker compose up -d   (from $RepoRoot)"
        }
        if ($ids.Count -gt 1) {
            Fail "docker compose reports $($ids.Count) php containers for this checkout - name the one you want with:  `$env:FV_PHP_CONTAINER = '<name>'"
        }
        $Container = $ids[0]
    }

    $info = docker inspect -f '{{.Name}} {{.State.Running}}' $Container
    if ($LASTEXITCODE -ne 0) {
        Fail "container '$Container' does not exist. Start it with:  docker compose up -d   (from $RepoRoot)"
    }
    $ContainerName, $running = ("$info".TrimStart('/') -split ' ')
    if ($running -ne 'true') {
        Fail "$ContainerName is not running. Start it with:  docker compose up -d   (from $RepoRoot)"
    }
    Write-Host "container: $ContainerName" -ForegroundColor DarkGray
}

# ---- 1. autoload map
if ($doAutoload) {
    Write-Host "`n=== autoload map (phpab) ===" -ForegroundColor Cyan
    docker exec -w /usr/src/current $Container sh autoload.sh
    if ($LASTEXITCODE -ne 0) { Fail 'autoload generation failed - see phpab output above (a collision means two files declare the same class; resolve it, never hand-edit the map)' }
    $stat = git -C $RepoRoot diff --stat -- source/autoload.php
    if ($stat) {
        Write-Host 'source/autoload.php changed - review and commit it with your class files:'
        git -C $RepoRoot --no-pager diff --stat -- source/autoload.php
    } else {
        Write-Host 'source/autoload.php unchanged.'
    }
}

# ---- 2. static ship files
if ($doStatics) {
    Write-Host "`n=== static ship files ===" -ForegroundColor Cyan
    docker exec -w /usr/src/current $Container php generateStaticShipFile.php
    if ($LASTEXITCODE -ne 0) { Fail 'static ship generation failed' }
}

# ---- 3. client bundles
if ($doClient) {
    Write-Host "`n=== client bundles (yarn build) ===" -ForegroundColor Cyan
    Write-Host 'note: yarn build MINIFIES the legacy bundles; if yarn watch:legacy is running, its next rebuild makes them readable again.' -ForegroundColor Yellow
    Push-Location $RepoRoot
    try {
        yarn build
        if ($LASTEXITCODE -ne 0) { Fail 'yarn build failed' }
    } finally {
        Pop-Location
    }
}

# ---- pre-deploy gate (writes nothing)
if ($Check) {
    Write-Host "`n=== check 1/2: committed autoload map up to date? ===" -ForegroundColor Cyan
    # Generate beside the real map (phpab writes paths relative to the output
    # file, so the temp copy must live in source/ to be byte-comparable),
    # compare, then delete the temp file whatever the outcome.
    docker exec -w /usr/src/current $Container sh -c 'sh autoload.sh source/autoload.check.tmp >/dev/null && cmp -s source/autoload.check.tmp source/autoload.php; rc=$?; rm -f source/autoload.check.tmp; exit $rc'
    if ($LASTEXITCODE -ne 0) { Fail 'source/autoload.php is STALE (or generation failed) - run fvbuild.ps1 -Autoload and commit the result' }
    Write-Host 'autoload map is up to date.'

    Write-Host "`n=== check 2/2: replay harness ===" -ForegroundColor Cyan
    docker exec -w /usr/src/current $Container php tests/replay/replayHarness.php check
    if ($LASTEXITCODE -ne 0) { Fail 'replay harness found differences - do not deploy' }

    Write-Host "`nall checks passed." -ForegroundColor Green
}

Write-Host "`nfvbuild: done." -ForegroundColor Green