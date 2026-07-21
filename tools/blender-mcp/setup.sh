#!/usr/bin/env bash
set -euo pipefail

if ! command -v uvx >/dev/null 2>&1; then
  echo "Installing uv (includes uvx)..."
  curl -LsSf https://astral.sh/uv/install.sh | sh
  export PATH="${HOME}/.local/bin:${PATH}"
fi

echo "Blender MCP server package is ready. Cursor will launch it via .cursor/mcp.json"
echo
echo "Next steps on your machine:"
echo "1. Install Blender 3.0+ from https://www.blender.org/download/"
echo "2. In Blender: Edit > Preferences > Add-ons > Install..."
echo "   Select: $(cd "$(dirname "$0")" && pwd)/addon.py"
echo "3. Enable addon: Interface: Blender MCP"
echo "4. Press N in 3D View > BlenderMCP tab > Connect to Claude"
echo "5. Restart Cursor so MCP picks up .cursor/mcp.json"
echo
echo "If Cursor reports 'spawn uvx ENOENT', set command to the full uvx path from: which uvx"
