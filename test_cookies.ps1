
$baseUrl = "http://localhost:8004"
$headers = @{
    "Origin" = "http://localhost:8004"
    "Accept" = "application/json"
    "Referer" = "http://localhost:8004"
}

Write-Host "1. Getting CSRF Cookie..."
try {
    $response = Invoke-WebRequest -Uri "$baseUrl/sanctum/csrf-cookie" -Method Get -Headers $headers -SessionVariable session
    Write-Host "CSRF Cookie Response Status: $($response.StatusCode)"
    
    $cookies = $session.Cookies.GetCookies($baseUrl)
    $xsrfToken = $cookies | Where-Object { $_.Name -eq "XSRF-TOKEN" }
    
    if ($xsrfToken) {
        Write-Host "XSRF-TOKEN found: $($xsrfToken.Value)"
        $headers["X-XSRF-TOKEN"] = [System.Uri]::UnescapeDataString($xsrfToken.Value)
    } else {
        Write-Host "XSRF-TOKEN NOT found in cookies!"
        $cookies | Format-Table Name, Value, Domain
    }

    $email = "cookie_test_" + (Get-Random) + "@gmail.com"
    $password = "password123"

    Write-Host "`n2. Registering new user ($email)..."
    $registerBody = @{
        name = "Cookie Tester"
        email = $email
        password = $password
        role = "user"
    } | ConvertTo-Json

    try {
        $regResponse = Invoke-WebRequest -Uri "$baseUrl/api/register" -Method Post -Headers $headers -Body $registerBody -ContentType "application/json" -WebSession $session
        Write-Host "Registration Status: $($regResponse.StatusCode)"
    } catch {
        Write-Host "Registration Failed: $($_.Exception.Message)"
    }

    Write-Host "`n3. Attempting Login..."
    $body = @{
        email = $email
        password = $password
    } | ConvertTo-Json

    $loginResponse = Invoke-WebRequest -Uri "$baseUrl/api/login" -Method Post -Headers $headers -Body $body -ContentType "application/json" -WebSession $session
    
    Write-Host "Login Status: $($loginResponse.StatusCode)"
    Write-Host "Login Content: $($loginResponse.Content)"
    
    Write-Host "`nCookies after login:"
    $session.Cookies.GetCookies($baseUrl) | Format-Table Name, Value, Domain

} catch {
    Write-Host "Error: $($_.Exception.Message)"
    if ($_.Exception.Response) {
        Write-Host "Status: $($_.Exception.Response.StatusCode)"
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        Write-Host "Body: $($reader.ReadToEnd())"
    }
}
