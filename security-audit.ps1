# Security Audit Script using Larastan
# Summarizes vulnerabilities detected and provides fix recommendations

param(
    [int]$Level = 5,
    [switch]$ExportJson,
    [string]$OutputFile = "security-report.json"
)

Write-Host ""
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "    LARASTAN SECURITY AND CODE AUDIT" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Analysis Level: $Level (1=basic, 9=strict)" -ForegroundColor Gray
Write-Host "Target: app/ directory" -ForegroundColor Gray
Write-Host ""

# Run Larastan analysis
Write-Host "[*] Running Larastan static analysis..." -ForegroundColor Yellow
$analysisOutput = php vendor/bin/phpstan analyse --level=$Level --error-format=json --no-progress 2>$null

# Parse JSON output - extract JSON from output
$analysisJson = $null
try {
    $jsonString = $analysisOutput -join "`n"
    # Extract JSON part (starts with { and ends with })
    if ($jsonString -match '\{.*\}') {
        $jsonString = $matches[0]
    }
    $analysisJson = $jsonString | ConvertFrom-Json
} catch {
    Write-Host "[!] Failed to parse analysis output" -ForegroundColor Red
    Write-Host $analysisOutput
    exit 1
}

# Initialize counters
$totalErrors = 0
$securityIssues = @()
$typeErrors = @()
$undefinedIssues = @()
$otherIssues = @()

# Categorize issues
if ($analysisJson.files) {
    foreach ($file in $analysisJson.files.PSObject.Properties) {
        $filePath = $file.Name
        $messages = $file.Value.messages
        
        foreach ($msg in $messages) {
            $totalErrors++
            $issue = @{
                File = $filePath
                Line = $msg.line
                Message = $msg.message
            }
            
            # Categorize by type
            $msgLower = $msg.message.ToLower()
            
            if ($msgLower -match "sql|injection|xss|csrf|escape|sanitize|validate|auth|password|hash|encrypt|session|cookie|token") {
                $securityIssues += $issue
            }
            elseif ($msgLower -match "type|expects|given|return|parameter|argument") {
                $typeErrors += $issue
            }
            elseif ($msgLower -match "undefined|unknown|not found|does not exist|missing") {
                $undefinedIssues += $issue
            }
            else {
                $otherIssues += $issue
            }
        }
    }
}

# Display Summary
Write-Host ""
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "           ANALYSIS SUMMARY" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Total Issues Found: $totalErrors" -ForegroundColor $(if ($totalErrors -eq 0) { "Green" } else { "Yellow" })
Write-Host ""
Write-Host "+---------------------------------------------+"
Write-Host "| Category                    | Count         |"
Write-Host "+---------------------------------------------+"
Write-Host "| Security Vulnerabilities    | $($securityIssues.Count.ToString().PadLeft(13)) |" -ForegroundColor $(if ($securityIssues.Count -gt 0) { "Red" } else { "Green" })
Write-Host "| Type Errors                 | $($typeErrors.Count.ToString().PadLeft(13)) |" -ForegroundColor $(if ($typeErrors.Count -gt 0) { "Yellow" } else { "Green" })
Write-Host "| Undefined References        | $($undefinedIssues.Count.ToString().PadLeft(13)) |" -ForegroundColor $(if ($undefinedIssues.Count -gt 0) { "Yellow" } else { "Green" })
Write-Host "| Other Issues                | $($otherIssues.Count.ToString().PadLeft(13)) |"
Write-Host "+---------------------------------------------+"

# Show Security Issues Detail
if ($securityIssues.Count -gt 0) {
    Write-Host ""
    Write-Host "[!] SECURITY VULNERABILITIES DETECTED:" -ForegroundColor Red
    Write-Host "--------------------------------------------" -ForegroundColor Red
    foreach ($issue in $securityIssues) {
        $relativePath = $issue.File -replace [regex]::Escape((Get-Location).Path + "\"), ""
        Write-Host "  File: $relativePath" -ForegroundColor White
        Write-Host "  Line: $($issue.Line)" -ForegroundColor Gray
        Write-Host "  Issue: $($issue.Message)" -ForegroundColor Yellow
        Write-Host "  Fix: Review and apply proper input validation/sanitization" -ForegroundColor Cyan
        Write-Host ""
    }
}

# Show Type Errors
if ($typeErrors.Count -gt 0) {
    Write-Host ""
    Write-Host "[*] TYPE ERRORS:" -ForegroundColor Yellow
    Write-Host "--------------------------------------------"
    $shown = 0
    foreach ($issue in $typeErrors) {
        if ($shown -ge 5) {
            Write-Host "  ... and $($typeErrors.Count - 5) more type errors" -ForegroundColor Gray
            break
        }
        $relativePath = $issue.File -replace [regex]::Escape((Get-Location).Path + "\"), ""
        Write-Host "  - $relativePath`:$($issue.Line) - $($issue.Message)" -ForegroundColor Gray
        $shown++
    }
}

# Show Undefined References
if ($undefinedIssues.Count -gt 0) {
    Write-Host ""
    Write-Host "[*] UNDEFINED REFERENCES:" -ForegroundColor Yellow
    Write-Host "--------------------------------------------"
    $shown = 0
    foreach ($issue in $undefinedIssues) {
        if ($shown -ge 5) {
            Write-Host "  ... and $($undefinedIssues.Count - 5) more undefined references" -ForegroundColor Gray
            break
        }
        $relativePath = $issue.File -replace [regex]::Escape((Get-Location).Path + "\"), ""
        Write-Host "  - $relativePath`:$($issue.Line) - $($issue.Message)" -ForegroundColor Gray
        $shown++
    }
}

# Composer Security Audit
Write-Host ""
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "       DEPENDENCY SECURITY CHECK" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "[*] Running composer audit..." -ForegroundColor Yellow

$composerAudit = composer audit --format=json 2>&1
$composerVulns = @()

try {
    $auditJson = $composerAudit | ConvertFrom-Json
    if ($auditJson.advisories) {
        foreach ($pkg in $auditJson.advisories.PSObject.Properties) {
            foreach ($adv in $pkg.Value) {
                $composerVulns += @{
                    Package = $pkg.Name
                    Title = $adv.title
                    CVE = $adv.cve
                    Severity = $adv.severity
                }
            }
        }
    }
} catch {
    # Silently continue if no vulnerabilities or parsing fails
}

if ($composerVulns.Count -eq 0) {
    Write-Host "[OK] No known vulnerabilities in dependencies" -ForegroundColor Green
} else {
    Write-Host "[!] Found $($composerVulns.Count) vulnerable dependencies:" -ForegroundColor Red
    foreach ($vuln in $composerVulns) {
        Write-Host "  Package: $($vuln.Package)" -ForegroundColor White
        Write-Host "  Issue: $($vuln.Title)" -ForegroundColor Yellow
        if ($vuln.CVE) {
            Write-Host "  CVE: $($vuln.CVE)" -ForegroundColor Red
        }
        Write-Host "  Fix: Run 'composer update $($vuln.Package)'" -ForegroundColor Cyan
        Write-Host ""
    }
}

# Export JSON if requested
if ($ExportJson) {
    $report = @{
        timestamp = (Get-Date).ToString("yyyy-MM-dd HH:mm:ss")
        level = $Level
        summary = @{
            total = $totalErrors
            security = $securityIssues.Count
            typeErrors = $typeErrors.Count
            undefined = $undefinedIssues.Count
            other = $otherIssues.Count
            composerVulns = $composerVulns.Count
        }
        securityIssues = $securityIssues
        typeErrors = $typeErrors
        undefinedIssues = $undefinedIssues
        otherIssues = $otherIssues
        composerVulnerabilities = $composerVulns
    }
    
    $report | ConvertTo-Json -Depth 10 | Out-File -FilePath $OutputFile -Encoding UTF8
    Write-Host ""
    Write-Host "[OK] Report exported to: $OutputFile" -ForegroundColor Green
}

# Recommendations
Write-Host ""
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "          RECOMMENDATIONS" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host ""

if ($totalErrors -eq 0 -and $composerVulns.Count -eq 0) {
    Write-Host "[OK] Your code passed all checks at level $Level!" -ForegroundColor Green
    Write-Host "     Consider increasing analysis level for stricter checks." -ForegroundColor Gray
} else {
    Write-Host "Priority fixes:" -ForegroundColor White
    if ($securityIssues.Count -gt 0) {
        Write-Host "  1. [HIGH] Fix $($securityIssues.Count) security vulnerabilities immediately" -ForegroundColor Red
    }
    if ($composerVulns.Count -gt 0) {
        Write-Host "  2. [HIGH] Update vulnerable dependencies" -ForegroundColor Red
    }
    if ($undefinedIssues.Count -gt 0) {
        Write-Host "  3. [MEDIUM] Resolve $($undefinedIssues.Count) undefined references" -ForegroundColor Yellow
    }
    if ($typeErrors.Count -gt 0) {
        Write-Host "  4. [LOW] Fix $($typeErrors.Count) type errors for better code quality" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "Audit completed at $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" -ForegroundColor Gray
Write-Host ""
