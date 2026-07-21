#!/usr/bin/env bash
# Configure Blender MCP for Cursor on macOS/Linux.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
MCP_JSON="${ROOT}/.cursor/mcp.json"

if ! command -v uvx >/dev/null 2>&1; then
  echo "Installing uv (includes uvx)..."
  if command -v brew >/dev/null 2>&1; then
    brew install uv
  else
    curl -LsSf https://astral.sh/uv/install.sh | sh
    export PATH="${HOME}/.local/bin:${PATH}"
  fi
fi

UVX_PATH="$(command -v uvx)"
echo "uvx: ${UVX_PATH}"

mkdir -p "${ROOT}/.cursor"
cat > "${MCP_JSON}" <<EOF
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

echo "Wrote ${MCP_JSON}"
echo
echo "Done. Next:"
echo "1. Keep Blender open with BlenderMCP running on port 9876"
echo "2. Quit Cursor completely (Cmd+Q on Mac), then reopen this project"
echo "3. Cursor Settings → MCP → blender should show Connected"
