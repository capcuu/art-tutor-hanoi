# Upload art-tutor-hanoi theme via FTP (bypasses SFTP extension cache).
# Run from repo root:  .\scripts\upload-theme-ftp.ps1
# Optional:           .\scripts\upload-theme-ftp.ps1 -Paths @('inc\faq-page.php','data\faq-page.php')

param(
    [string[]]$Paths = @()
)

$ErrorActionPreference = 'Stop'

$repoRoot   = Split-Path -Parent $PSScriptRoot
$configPath = Join-Path $repoRoot '.vscode\sftp.json'
$themeDir   = Join-Path $repoRoot 'wp-content\themes\art-tutor-hanoi'

if (-not (Test-Path $configPath)) {
    Write-Error "Missing $configPath"
}

$config = Get-Content $configPath -Raw | ConvertFrom-Json
$remoteBase = $config.remotePath.TrimEnd('/')

function Send-FtpFile {
    param(
        [string]$LocalPath,
        [string]$RemotePath
    )

    $uri = "ftp://$($config.host)$RemotePath"
    $request = [System.Net.FtpWebRequest]::Create($uri)
    $request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $request.Credentials = New-Object System.Net.NetworkCredential($config.username, $config.password)
    $request.UseBinary = $true
    $request.UsePassive = $true
    $request.KeepAlive = $false

    $bytes = [System.IO.File]::ReadAllBytes($LocalPath)
    $request.ContentLength = $bytes.Length
    $stream = $request.GetRequestStream()
    $stream.Write($bytes, 0, $bytes.Length)
    $stream.Close()

    $response = $request.GetResponse()
    $response.Close()
}

function Ensure-FtpDirectory {
    param([string]$RemoteDir)

    if ($RemoteDir -eq '/' -or [string]::IsNullOrWhiteSpace($RemoteDir)) {
        return
    }

    $parent = Split-Path $RemoteDir -Parent
    if ($parent -and $parent -ne '/') {
        Ensure-FtpDirectory $parent
    }

    try {
        $uri = "ftp://$($config.host)$RemoteDir"
        $request = [System.Net.FtpWebRequest]::Create($uri)
        $request.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $request.Credentials = New-Object System.Net.NetworkCredential($config.username, $config.password)
        $request.UsePassive = $true
        $response = $request.GetResponse()
        $response.Close()
    } catch {
        # Directory likely already exists.
    }
}

# Verify login
$listUri = "ftp://$($config.host)/"
$listReq = [System.Net.FtpWebRequest]::Create($listUri)
$listReq.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectory
$listReq.Credentials = New-Object System.Net.NetworkCredential($config.username, $config.password)
$listReq.UsePassive = $true
$listResp = $listReq.GetResponse()
$listResp.Close()
Write-Host "FTP OK as $($config.username) on $($config.host)" -ForegroundColor Green

if ($Paths.Count -eq 0) {
    $manifest = Join-Path $themeDir 'backup\deploy-manifest.txt'
    if (Test-Path $manifest) {
        $Paths = Get-Content $manifest | Where-Object { $_ -and -not $_.TrimStart().StartsWith('#') }
    } else {
        $Paths = Get-ChildItem $themeDir -Recurse -File |
            Where-Object { $_.FullName -notmatch '\\backup\\|\\.vscode\\' } |
            ForEach-Object { $_.FullName.Substring($themeDir.Length + 1) }
    }
}

$ok = 0
foreach ($rel in $Paths) {
    $rel = $rel.Trim().Replace('/', '\')
    $local = Join-Path $themeDir $rel
    if (-not (Test-Path $local)) {
        Write-Warning "Skip (missing): $rel"
        continue
    }

    $remoteRel = $rel.Replace('\', '/')
    $remoteDir = "$remoteBase/$(Split-Path $remoteRel -Parent)".Replace('\', '/')
    $remoteFile = "$remoteBase/$remoteRel".Replace('\', '/')

    Ensure-FtpDirectory $remoteDir
    Send-FtpFile -LocalPath $local -RemotePath $remoteFile
    Write-Host "  uploaded $rel"
    $ok++
}

Write-Host ""
Write-Host "Done: $ok file(s) uploaded to $remoteBase" -ForegroundColor Green
