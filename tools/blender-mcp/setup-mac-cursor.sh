#!/usr/bin/env bash
# One-shot: install uv + write Cursor global MCP config for Blender (macOS/Linux).
set -euo pipefail

if ! command -v uvx >/dev/null 2>&1; then
  echo "→ Installing uv..."
  curl -LsSf https://astral.sh/uv/install.sh | sh
  # shellcheck disable=SC1091
  source "${HOME}/.local/bin/env"
fi

UVX_PATH="$(command -v uvx)"
echo "→ uvx found at: ${UVX_PATH}"

mkdir -p "${HOME}/.cursor"

cat > "${HOME}/.cursor/mcp.json" <<EOF
{
  "mcpServers": {
    "blender": {
      "command": "${UVX_PATH}",
      "args": ["--python", "3.11", "blender-mcp"],
      "env": {
        "UV_PYTHON_PREFERENCE": "only-managed",
        "BLENDER_HOST": "localhost",
        "BLENDER_PORT": "9876"
      }
    }
  }
}
EOF

echo "→ Wrote ${HOME}/.cursor/mcp.json"
echo
echo "✅ Xong! Giờ:"
echo "   1. Giữ Blender mở (Running on port 9876)"
echo "   2. Nhấn Cmd+Q thoát Cursor"
echo "   3. Mở lại Cursor → Settings → Tools → Home"
echo "   4. Server 'blender' sẽ hiện Connected"
