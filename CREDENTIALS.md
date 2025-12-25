# 🔐 CREDENTIALS & CONFIGURATION

## MinIO Server Configuration

### API Endpoint
```
Primary: http://192.168.0.112:9000
Fallback: http://127.0.0.1:9000
```

### Web Console
```
Primary: http://192.168.0.112:52888
Fallback: http://127.0.0.1:52888
```

### Credentials
```
Username (RootUser): myuser
Password (RootPass): mypassword
```

### Bucket Configuration
```
Bucket Name: arsip-pusri
Region: us-east-1
Path Style: Enabled
```

---

## Application Configuration

### Database
```
Host: 127.0.0.1
Port: 3306
Database: data_pusri
Username: root
Password: (sesuaikan dengan MySQL Anda)
```

### Application Key
```
APP_KEY=base64:xypGB9cV0BywkS/p17pJgTo5Gh9oulWa5C/MoTWT5gY=
```

---

## Default User Accounts

### Admin
```
Badge: ADMIN001
Password: admin123
Role: admin
Department: IT
```

### Test Users
```
1. Badge: USER001, Password: user123, Dept: Produksi
2. Badge: USER002, Password: user123, Dept: Keuangan
3. Badge: USER003, Password: user123, Dept: HRD
```

---

## ⚠️ IMPORTANT NOTES

### For Production:
1. **WAJIB** ganti password default admin
2. **WAJIB** ganti MinIO credentials jika production
3. **WAJIB** gunakan HTTPS untuk production
4. **REKOMENDASI** ganti APP_KEY untuk production:
   ```bash
   php artisan key:generate
   ```

### Security Best Practices:
- Jangan share credentials di public repository
- Gunakan environment variables untuk sensitive data
- Enable firewall untuk MinIO port
- Backup database secara regular
- Monitor login logs untuk suspicious activity

---

## 🔧 Quick Setup Commands

### 1. Start MinIO
```bash
# Linux
minio server /data/minio --address "192.168.0.112:9000" --console-address "192.168.0.112:52888"

# Windows
minio.exe server C:\minio\data --address "192.168.0.112:9000" --console-address "192.168.0.112:52888"
```

### 2. Create Bucket
```bash
# Via MinIO Console
# 1. Open http://192.168.0.112:52888
# 2. Login: myuser / mypassword
# 3. Create bucket: arsip-pusri
```

### 3. Test Connection
```bash
# Test MinIO API
curl http://192.168.0.112:9000/minio/health/live

# Expected: HTTP 200 OK
```

---

© 2024 PT Pupuk Sriwidjaja
