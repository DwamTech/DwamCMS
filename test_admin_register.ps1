
$baseUrl = "http://localhost:8005"
$adminEmail = "admin_test@gmail.com"
$adminPassword = "password123"

Write-Host "1. Logging in as Admin..."
$loginBody = @{
    email = $adminEmail
    password = $adminPassword
} | ConvertTo-Json

try {
    $loginResponse = Invoke-WebRequest -Uri "$baseUrl/api/login" -Method Post -Body $loginBody -ContentType "application/json"
    $loginData = $loginResponse.Content | ConvertFrom-Json
    $token = $loginData.token
    Write-Host "Admin Token: $token"
} catch {
    Write-Host "Admin Login Failed: $($_.Exception.Message)"
    exit
}

Write-Host "`n2. Registering as Admin (Role: editor)..."
$editorEmail = "new_editor_" + (Get-Random) + "@gmail.com"
$registerBody = @{
    name = "New Editor"
    email = $editorEmail
    password = "password123"
    role = "editor"
} | ConvertTo-Json

$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
}

try {
    $regResponse = Invoke-WebRequest -Uri "$baseUrl/api/register" -Method Post -Headers $headers -Body $registerBody -ContentType "application/json"
    $regData = $regResponse.Content | ConvertFrom-Json
    Write-Host "Registered User Role: $($regData.user.role)"
    
    if ($regData.user.role -eq "editor") {
        Write-Host "SUCCESS: Admin was able to set role to editor." -ForegroundColor Green
    } else {
        Write-Host "FAILURE: Admin role request ignored. Got $($regData.user.role)" -ForegroundColor Red
    }
} catch {
    Write-Host "Admin Register Failed: $($_.Exception.Message)"
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        Write-Host "Body: $($reader.ReadToEnd())"
    }
}

Write-Host "`n3. Registering as Public User (Role: editor)..."
$fakeEmail = "fake_editor_" + (Get-Random) + "@gmail.com"
$publicBody = @{
    name = "Fake Editor"
    email = $fakeEmail
    password = "password123"
    role = "editor"
} | ConvertTo-Json

try {
    # No Authorization header
    $publicResponse = Invoke-WebRequest -Uri "$baseUrl/api/register" -Method Post -Body $publicBody -ContentType "application/json"
    $publicData = $publicResponse.Content | ConvertFrom-Json
    Write-Host "Public User Role: $($publicData.user.role)"
    
    if ($publicData.user.role -eq "user") {
        Write-Host "SUCCESS: Public user forced to role 'user'." -ForegroundColor Green
    } else {
        Write-Host "FAILURE: Public user was able to set role to $($publicData.user.role)" -ForegroundColor Red
    }

    # Capture the token of the newly registered public user
    $publicToken = $publicData.token
    Write-Host "Public User Token: $publicToken"
} catch {
    Write-Host "Public Register Failed: $($_.Exception.Message)"
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        Write-Host "Body: $($reader.ReadToEnd())"
    }
}

Write-Host "`n4. Registering as Regular User (Authenticated) trying to set role 'editor'..."
$regularAttemptEmail = "regular_attempt_" + (Get-Random) + "@gmail.com"
$regularAttemptBody = @{
    name = "Regular Attempt"
    email = $regularAttemptEmail
    password = "password123"
    role = "editor"
} | ConvertTo-Json

$headersRegular = @{
    "Authorization" = "Bearer $publicToken"
    "Accept" = "application/json"
}

try {
    $regResponse = Invoke-WebRequest -Uri "$baseUrl/api/register" -Method Post -Headers $headersRegular -Body $regularAttemptBody -ContentType "application/json"
    $regData = $regResponse.Content | ConvertFrom-Json
    Write-Host "Registered User Role: $($regData.user.role)"
    
    if ($regData.user.role -eq "user") {
        Write-Host "SUCCESS: Regular authenticated user forced to role 'user'." -ForegroundColor Green
    } else {
        Write-Host "FAILURE: Regular user was able to set role! Got $($regData.user.role)" -ForegroundColor Red
    }
} catch {
    Write-Host "Regular User Register Failed: $($_.Exception.Message)"
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        Write-Host "Body: $($reader.ReadToEnd())"
    }
}
