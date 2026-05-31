# Rebuild static index.html from partials (header, footer, home content, assets).
$root = Split-Path -Parent $MyInvocation.MyCommand.Path
$parts = @(
  "partials\head-static.html",
  "partials\header.html",
  "partials\home-content.html",
  "partials\footer.html"
)
$out = ($parts | ForEach-Object { Get-Content (Join-Path $root $_) -Raw }) -join "`n"
$out += "`n  <script src=`"assets/js/main.js`"></script>`n</body>`n</html>`n"
$out | Set-Content (Join-Path $root "index.html") -NoNewline
Write-Host "Built index.html"
