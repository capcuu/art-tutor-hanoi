#!/usr/bin/env python3
"""
Deploy art-tutor-hanoi theme files over FTP with local backup + rollback.

Reads credentials from:
  1) env FTP_HOST / FTP_USER / FTP_PASSWORD
  2) .vscode/sftp.json (first entry)

Usage:
  python3 scripts/deploy-ftp-with-rollback.py              # backup remote + upload
  python3 scripts/deploy-ftp-with-rollback.py --rollback   # restore last backup
"""
from __future__ import annotations

import argparse
import json
import os
import sys
import time
from ftplib import FTP, error_perm
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
THEME = ROOT / "wp-content" / "themes" / "art-tutor-hanoi"
BACKUP_ROOT = THEME / "backup" / "ftp-rollbacks"
STATE_FILE = BACKUP_ROOT / "last-deploy.json"

# Minimal set for art-feedback video banner replace
DEFAULT_PATHS = [
    "inc/art-feedback-page.php",
    "functions.php",
    "partials/page-content.php",
    "partials/home-hero.php",
    "inc/shortcodes/home-hero.php",
    "assets/css/main.css",
]


def load_ftp_config() -> dict:
    host = os.environ.get("FTP_HOST", "").strip()
    user = os.environ.get("FTP_USER", "").strip()
    password = (os.environ.get("FTP_PASSWORD") or os.environ.get("FTP_PASS") or "").strip()
    remote = os.environ.get("FTP_REMOTE_PATH", "/wp-content/themes/art-tutor-hanoi").strip()
    port = int(os.environ.get("FTP_PORT", "21"))

    if not (user and password):
        sftp_path = ROOT / ".vscode" / "sftp.json"
        if sftp_path.is_file():
            data = json.loads(sftp_path.read_text(encoding="utf-8"))
            cfg = data[0] if isinstance(data, list) else data
            host = host or str(cfg.get("host", "")).strip()
            user = user or str(cfg.get("username", "")).strip()
            password = password or str(cfg.get("password", "")).strip()
            remote = str(cfg.get("remotePath", remote)).strip() or remote
            port = int(cfg.get("port", port) or port)

    if not host:
        host = "zmu.xib.mybluehost.me"
    if not user or not password or password.startswith("YOUR_"):
        raise SystemExit(
            "FTP credentials missing. Run scripts/install-ftp-credentials.sh after setting "
            "FTP_USER + FTP_PASSWORD secrets, or create .vscode/sftp.json."
        )
    return {"host": host, "user": user, "password": password, "remote": remote.rstrip("/"), "port": port}


def connect(cfg: dict) -> FTP:
    ftp = FTP()
    ftp.connect(cfg["host"], cfg["port"], timeout=60)
    ftp.login(cfg["user"], cfg["password"])
    ftp.set_pasv(True)
    print(f"FTP OK {cfg['user']}@{cfg['host']}:{cfg['port']}")
    return ftp


def ensure_remote_dir(ftp: FTP, path: str) -> None:
    parts = [p for p in path.strip("/").split("/") if p]
    cur = ""
    for part in parts:
        cur += "/" + part
        try:
            ftp.mkd(cur)
        except error_perm:
            pass


def download_file(ftp: FTP, remote_file: str, local_file: Path) -> bool:
    local_file.parent.mkdir(parents=True, exist_ok=True)
    try:
        with local_file.open("wb") as fh:
            ftp.retrbinary(f"RETR {remote_file}", fh.write)
        return True
    except error_perm as exc:
        print(f"  skip download (missing?): {remote_file} ({exc})")
        if local_file.exists():
            local_file.unlink()
        return False


def upload_file(ftp: FTP, local_file: Path, remote_file: str) -> None:
    ensure_remote_dir(ftp, str(Path(remote_file).parent).replace("\\", "/"))
    with local_file.open("rb") as fh:
        ftp.storbinary(f"STOR {remote_file}", fh)


def deploy(paths: list[str]) -> None:
    cfg = load_ftp_config()
    stamp = time.strftime("%Y%m%d-%H%M%S")
    backup_dir = BACKUP_ROOT / stamp
    backup_dir.mkdir(parents=True, exist_ok=True)

    ftp = connect(cfg)
    backed = []
    try:
        print("Backing up remote files…")
        for rel in paths:
            remote = f"{cfg['remote']}/{rel}"
            local_backup = backup_dir / rel
            if download_file(ftp, remote, local_backup):
                backed.append(rel)
                print(f"  backed up {rel}")

        print("Uploading…")
        uploaded = []
        for rel in paths:
            local = THEME / rel
            if not local.is_file():
                print(f"  skip missing local: {rel}")
                continue
            remote = f"{cfg['remote']}/{rel}"
            upload_file(ftp, local, remote)
            uploaded.append(rel)
            print(f"  uploaded {rel}")

        state = {
            "timestamp": stamp,
            "backup_dir": str(backup_dir.relative_to(ROOT)),
            "remote_base": cfg["remote"],
            "backed_up": backed,
            "uploaded": uploaded,
            "host": cfg["host"],
            "user": cfg["user"],
        }
        STATE_FILE.write_text(json.dumps(state, indent=2) + "\n", encoding="utf-8")
        (backup_dir / "manifest.json").write_text(json.dumps(state, indent=2) + "\n", encoding="utf-8")
        print(f"\nDeploy complete. Rollback: python3 scripts/deploy-ftp-with-rollback.py --rollback")
        print(f"Backup: {backup_dir}")
    finally:
        ftp.quit()


def rollback() -> None:
    if not STATE_FILE.is_file():
        raise SystemExit(f"No deploy state at {STATE_FILE}")
    state = json.loads(STATE_FILE.read_text(encoding="utf-8"))
    backup_dir = ROOT / state["backup_dir"]
    if not backup_dir.is_dir():
        raise SystemExit(f"Backup dir missing: {backup_dir}")

    cfg = load_ftp_config()
    ftp = connect(cfg)
    try:
        print(f"Rolling back from {backup_dir}…")
        restored = []
        for rel in state.get("backed_up", []):
            local = backup_dir / rel
            if not local.is_file():
                print(f"  skip missing backup: {rel}")
                continue
            remote = f"{cfg['remote']}/{rel}"
            upload_file(ftp, local, remote)
            restored.append(rel)
            print(f"  restored {rel}")

        # New files that did not exist remotely: delete if we created them
        for rel in state.get("uploaded", []):
            if rel in state.get("backed_up", []):
                continue
            remote = f"{cfg['remote']}/{rel}"
            try:
                ftp.delete(remote)
                print(f"  deleted new file {rel}")
            except error_perm as exc:
                print(f"  could not delete {rel}: {exc}")

        print(f"\nRollback complete ({len(restored)} files).")
    finally:
        ftp.quit()


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--rollback", action="store_true")
    parser.add_argument("--paths", nargs="*", default=DEFAULT_PATHS)
    args = parser.parse_args()
    if args.rollback:
        rollback()
    else:
        deploy(args.paths)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
