param(
    [int]$Port = 8000,
    [string]$EnvFile = ".env"
)

$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

function Get-NgrokTunnel {
    param([int]$TargetPort)

    try {
        $response = Invoke-RestMethod -Uri 'http://127.0.0.1:4040/api/tunnels'
    } catch {
        return $null
    }

    return $response.tunnels | Where-Object {
        $_.proto -eq 'https' -and (
            $_.config.addr -match ":$TargetPort$" -or
            $_.config.addr -match "/:$TargetPort$" -or
            $_.config.addr -eq "$TargetPort"
        )
    } | Select-Object -First 1
}

$hotFile = Join-Path $projectRoot 'public\hot'
if (Test-Path $hotFile) {
    Remove-Item -LiteralPath $hotFile -Force
}

npm run build | Out-Host
if ($LASTEXITCODE -ne 0) {
    throw "La compilacion frontend fallo. Revisa el error mostrado por npm run build."
}

$tunnel = Get-NgrokTunnel -TargetPort $Port

if (-not $tunnel) {
    throw "No hay un tunel HTTPS de ngrok activo para el puerto $Port. Inicia ngrok manualmente con: ngrok http $Port"
}

$publicUrl = $tunnel.public_url.TrimEnd('/')
$envPath = Join-Path $projectRoot $EnvFile
$envLines = Get-Content -Path $envPath
$updated = @()
$appUrlWritten = $false

foreach ($line in $envLines) {
    if ($line -match '^APP_URL=') {
        if (-not $appUrlWritten) {
            $updated += "APP_URL=$publicUrl"
            $appUrlWritten = $true
        }

        continue
    }

    $updated += $line
}

if (-not $appUrlWritten) {
    $updated += "APP_URL=$publicUrl"
}

$updatedText = ($updated -join [Environment]::NewLine) + [Environment]::NewLine
[System.IO.File]::WriteAllText($envPath, $updatedText, (New-Object System.Text.UTF8Encoding($false)))

php artisan optimize:clear | Out-Host
if ($LASTEXITCODE -ne 0) {
    throw "Laravel no pudo limpiar caches despues de sincronizar APP_URL."
}

Write-Host ''
Write-Host 'Ngrok URL sincronizada:' -ForegroundColor Cyan
Write-Host $publicUrl -ForegroundColor Green
Write-Host ''
Write-Host 'Abre este enlace desde otro dispositivo y la app cargara con CSS y JS compilados.' -ForegroundColor Yellow