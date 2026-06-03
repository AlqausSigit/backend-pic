# Script untuk memasukkan data statis ke Backend Laravel

$API_URL = "http://127.0.0.1:8000/api"
$TOKEN = ""

# 1. LOGIN
Write-Host "=== LOGIN ===" -ForegroundColor Green
$login_body = @{
    username = "admin123"
    password = "password123"
} | ConvertTo-Json

$login_response = Invoke-WebRequest -Uri "$API_URL/login" -Method POST -ContentType "application/json" -Body $login_body | ConvertFrom-Json
$TOKEN = $login_response.token
Write-Host "Token: $TOKEN" -ForegroundColor Cyan

# 2. INSERT KELAS
Write-Host "`n=== INSERT KELAS ===" -ForegroundColor Green
$kelas_list = @(
    @{ nama_kelas = "XI TJKT 1"; jurusan = "TJKT"; jumlah_siswa = 40 },
    @{ nama_kelas = "XI TJKT 2"; jurusan = "TJKT"; jumlah_siswa = 40 },
    @{ nama_kelas = "XI TJKT 3"; jurusan = "TJKT"; jumlah_siswa = 40 },
    @{ nama_kelas = "XI TJKT 4"; jurusan = "TJKT"; jumlah_siswa = 39 }
)

foreach ($kelas in $kelas_list) {
    $body = $kelas | ConvertTo-Json
    Invoke-WebRequest -Uri "$API_URL/kelas" -Method POST -ContentType "application/json" -Headers @{ Authorization = "Bearer $TOKEN" } -Body $body
    Write-Host "OK: $($kelas.nama_kelas)" -ForegroundColor Green
}

# 3. INSERT WADAH
Write-Host "`n=== INSERT WADAH ===" -ForegroundColor Green
$wadah_list = @(
    @{ kode_box = "BOX-7AF0B01"; jumlah_box = 30; status = "tersedia" },
    @{ kode_box = "BOX-7BA4002"; jumlah_box = 33; status = "tersedia" },
    @{ kode_box = "BOX-7C0D003"; jumlah_box = 30; status = "tersedia" },
    @{ kode_box = "BOX-7C74004"; jumlah_box = 31; status = "tersedia" },
    @{ kode_box = "BOX-7CDE005"; jumlah_box = 36; status = "tersedia" },
    @{ kode_box = "BOX-7D4E106"; jumlah_box = 34; status = "tersedia" },
    @{ kode_box = "BOX-7DA7107"; jumlah_box = 40; status = "tersedia" },
    @{ kode_box = "BOX-7E22C08"; jumlah_box = 33; status = "tersedia" },
    @{ kode_box = "BOX-7E9E009"; jumlah_box = 38; status = "tersedia" },
    @{ kode_box = "BOX-7F07F10"; jumlah_box = 35; status = "tersedia" }
)

foreach ($wadah in $wadah_list) {
    $body = $wadah | ConvertTo-Json
    Invoke-WebRequest -Uri "$API_URL/wadah" -Method POST -ContentType "application/json" -Headers @{ Authorization = "Bearer $TOKEN" } -Body $body
    Write-Host "OK: $($wadah.kode_box)" -ForegroundColor Green
}

# 4. INSERT PENGGUNA
Write-Host "`n=== INSERT PENGGUNA ===" -ForegroundColor Green
$users_list = @(
    @{ nama = "Petugas Distribusi"; username = "petugas123"; password = "password123"; role = "petugas" },
    @{ nama = "atlas mbg"; username = "owner"; password = "password123"; role = "admin" }
)

foreach ($user in $users_list) {
    $body = $user | ConvertTo-Json
    Invoke-WebRequest -Uri "$API_URL/users" -Method POST -ContentType "application/json" -Headers @{ Authorization = "Bearer $TOKEN" } -Body $body
    Write-Host "OK: $($user.username)" -ForegroundColor Green
}

Write-Host "`n=== SELESAI ===" -ForegroundColor Green
