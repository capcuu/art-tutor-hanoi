# Art Tutor Hanoi — WordPress project

Repo local cho site [Art Tutor Hanoi](https://art-tutor-hanoi.com): theme WordPress, prototype v2, scripts deploy.

## Cấu trúc

| Thư mục | Mục đích |
|---------|----------|
| `wp-content/themes/art-tutor-hanoi/` | **Theme chính** — deploy lên production |
| `v2/` | Prototype PHP/HTML cũ (tham khảo, rebuild) |
| `scripts/` | Script PowerShell deploy FTP |
| `doc/` | Tài liệu SEO, GSC |
| `artist_portfolio/` | App portfolio người lớn (PHP + Google Sheet) |

## Git hàng ngày

```powershell
git add .
git commit -m "Mô tả thay đổi"
git push
```

Nhánh local `master` track `origin/main`.

## Deploy theme

Xem `wp-content/themes/art-tutor-hanoi/README.md` và `scripts/build-theme-deploy.ps1`.

## Không commit

- `wp-config.php` (mật khẩu DB)
- `.vscode/sftp.json` (FTP) — dùng `sftp.json.example` làm mẫu
- `*.zip`, cache `artist_portfolio/cache/`
