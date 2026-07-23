#!/usr/bin/env bash
# Materialize .vscode/sftp.json from Cloud Agent secrets / env.
# Required env: FTP_USER, FTP_PASSWORD
# Optional: FTP_HOST (default zmu.xib.mybluehost.me), FTP_PORT, FTP_REMOTE_PATH
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="$ROOT/.vscode/sftp.json"

HOST="${FTP_HOST:-zmu.xib.mybluehost.me}"
PORT="${FTP_PORT:-21}"
USER="${FTP_USER:-}"
PASS="${FTP_PASSWORD:-${FTP_PASS:-}}"
REMOTE="${FTP_REMOTE_PATH:-/wp-content/themes/art-tutor-hanoi}"

if [[ -z "$USER" || -z "$PASS" ]]; then
  echo "Missing FTP credentials."
  echo "Add Runtime Secrets in https://cursor.com/dashboard/cloud-agents :"
  echo "  FTP_USER      = your Bluehost FTP username (…@arttutorhanoi.com)"
  echo "  FTP_PASSWORD  = FTP password"
  echo "  FTP_HOST      = zmu.xib.mybluehost.me   (optional)"
  echo "Then start a NEW cloud agent (existing runs do not receive new secrets)."
  exit 1
fi

mkdir -p "$ROOT/.vscode"
python3 - "$OUT" "$HOST" "$PORT" "$USER" "$PASS" "$REMOTE" <<'PY'
import json, sys
out, host, port, user, password, remote = sys.argv[1:7]
data = [
    {
        "name": f"{host} — Theme",
        "host": host,
        "protocol": "ftp",
        "port": int(port),
        "secure": False,
        "connectTimeout": 60000,
        "passive": True,
        "username": user,
        "password": password,
        "context": "wp-content/themes/art-tutor-hanoi",
        "remotePath": remote,
        "uploadOnSave": True,
        "downloadOnOpen": False,
        "ignore": [".vscode", ".git", "backup/**"],
    }
]
with open(out, "w", encoding="utf-8") as f:
    json.dump(data, f, indent=4)
    f.write("\n")
print(f"Wrote {out}")
print(f"  host={host} user={user} remotePath={remote}")
PY
