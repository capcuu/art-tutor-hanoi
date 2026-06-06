# Liệt kê file custom đã thay đổi — dùng trước khi upload lên server.
# Cách 1 (có Git):  git diff --name-only
# Cách 2 (hôm nay):  .\deploy-today.ps1

param(
    [int]$Days = 0   # 0 = hôm nay, 1 = hôm qua + hôm nay, ...
)

$root = $PSScriptRoot
$since = (Get-Date).Date.AddDays(-$Days)

$watch = @(
    "wp-content\themes\art-tutor-hanoi",
    "wp-content\themes\masu-wpcom",
    "v2",
    "wp-content\plugins\artist-countries-shortcode",
    "wp-content\plugins\artist-country-summary",
    "wp-content\plugins\arth-send-invoice",
    "wp-content\plugins\galleryFeedBackHozirontal",
    "wp-content\plugins\galleryFeedBackHozirontalPrivate",
    "wp-content\plugins\google-photo-comment-fixer-alt-final",
    "wp-content\plugins\gsheet-image-gallery-pinterest-old123",
    "wp-content\plugins\portfolio-form-fill",
    "wp-content\plugins\comment-email-notifier-acfV3",
    "wp-content\plugins\latest-by-comment-plugin-v1.1-fixed-v3"
)

Write-Host "File thay doi tu $($since.ToString('yyyy-MM-dd HH:mm')):" -ForegroundColor Cyan
Write-Host ""

$files = $watch | ForEach-Object {
    $path = Join-Path $root $_
    if (Test-Path $path) {
        Get-ChildItem $path -Recurse -File -ErrorAction SilentlyContinue |
            Where-Object { $_.LastWriteTime -ge $since }
    }
} | Sort-Object LastWriteTime -Descending

if (-not $files) {
    Write-Host "(khong co file nao)" -ForegroundColor Yellow
    exit 0
}

$files | ForEach-Object {
    $rel = $_.FullName.Replace("$root\", "").Replace("\", "/")
    Write-Host ("{0:HH:mm}  {1}" -f $_.LastWriteTime, $rel)
}

Write-Host ""
Write-Host "Tong: $($files.Count) file" -ForegroundColor Green
