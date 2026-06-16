# Upload Fluent Forms import files to Bluehost dev (WordPress root).
# Run from repo root: powershell -ExecutionPolicy Bypass -File scripts/upload-fluentform-import.ps1

$ErrorActionPreference = 'Stop'
$repoRoot = Split-Path -Parent $PSScriptRoot
$configPath = Join-Path $repoRoot '.vscode\sftp.json'

if (-not (Test-Path $configPath)) {
	Write-Error "Missing $configPath"
}

$config = Get-Content $configPath -Raw | ConvertFrom-Json
$ftpHost = $config.host
$ftpUser = $config.username
$ftpPass = $config.password

$files = @(
	'import-fluentform-once.php',
	'adult-booking-form-export-slim.json'
)

foreach ($name in $files) {
	$local = Join-Path $repoRoot $name
	if (-not (Test-Path $local)) {
		Write-Error "Missing local file: $local"
	}
}

Write-Host "Uploading to ftp://$ftpHost/ (WordPress root)..."

foreach ($name in $files) {
	$local = Join-Path $repoRoot $name
	$uri = "ftp://$ftpHost/$name"
	Write-Host "  -> $name"

	$request = [System.Net.FtpWebRequest]::Create($uri)
	$request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
	$request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
	$request.UseBinary = $true
	$request.UsePassive = $true
	$request.KeepAlive = $false

	$bytes = [System.IO.File]::ReadAllBytes($local)
	$request.ContentLength = $bytes.Length
	$stream = $request.GetRequestStream()
	$stream.Write($bytes, 0, $bytes.Length)
	$stream.Close()

	$response = $request.GetResponse()
	Write-Host "     OK ($($response.StatusDescription))"
	$response.Close()
}

Write-Host ""
Write-Host "Done. Open in browser (logged in as WP admin):"
Write-Host "https://$ftpHost/import-fluentform-once.php?file=adult-booking-form-export-slim.json"
Write-Host ""
Write-Host "Delete both files on server after import succeeds."
