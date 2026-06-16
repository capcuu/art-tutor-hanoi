# Build art-tutor-deploy.zip for deploy-theme-once.php (HTTPS upload via cPanel).
# Run from repo root:  .\scripts\build-theme-deploy.ps1

$ErrorActionPreference = 'Stop'

$repoRoot  = Split-Path -Parent $PSScriptRoot
$themeDir  = Join-Path $repoRoot 'wp-content\themes\art-tutor-hanoi'
$manifest  = Join-Path $themeDir 'backup\deploy-manifest.txt'
$staging   = Join-Path $env:TEMP 'art-tutor-deploy-staging'
$outZip    = Join-Path $repoRoot 'art-tutor-deploy.zip'
$deployPhp = Join-Path $repoRoot 'deploy-theme-once.php'

if (-not (Test-Path $manifest)) {
    Write-Error "Missing manifest: $manifest"
}

if (Test-Path $staging) { Remove-Item $staging -Recurse -Force }
New-Item -ItemType Directory -Path $staging | Out-Null

$lines = Get-Content $manifest | Where-Object {
    $_ -and -not $_.TrimStart().StartsWith('#')
}

$count = 0
foreach ($rel in $lines) {
    $rel = $rel.Trim().Replace('/', '\')
    $src = Join-Path $themeDir $rel
    if (-not (Test-Path $src)) {
        Write-Warning "Skip (not found): $rel"
        continue
    }
    $dest = Join-Path $staging $rel
    $destDir = Split-Path $dest -Parent
    if (-not (Test-Path $destDir)) {
        New-Item -ItemType Directory -Path $destDir -Force | Out-Null
    }
    Copy-Item $src $dest -Force
    $count++
}

if ($count -eq 0) {
    Write-Error 'No files copied — check deploy-manifest.txt'
}

if (Test-Path $outZip) { Remove-Item $outZip -Force }
Compress-Archive -Path (Join-Path $staging '*') -DestinationPath $outZip -CompressionLevel Optimal

Remove-Item $staging -Recurse -Force

$zipKb = [math]::Round((Get-Item $outZip).Length / 1KB, 1)
Write-Host ""
Write-Host "Built $outZip ($count files, ${zipKb} KB)" -ForegroundColor Green
Write-Host ""
Write-Host "Upload to server (cPanel File Manager -> public_html/):" -ForegroundColor Cyan
Write-Host "  1. deploy-theme-once.php"
Write-Host "  2. art-tutor-deploy.zip"
Write-Host "  3. Visit https://website-c1e4d7c9.zmu.xib.mybluehost.me/deploy-theme-once.php (logged in as admin)"
Write-Host "  4. Delete both files from server after deploy"
