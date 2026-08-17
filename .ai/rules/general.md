---
paths:
  - '.vscode/**'
---

# General

## Open this repo via WSL Remote, never UNC
Docker Desktop and Windows Explorer open this folder as \\wsl.localhost\... (UNC). CMD cannot use UNC as cwd, so the Windows Laravel LSP crashes with "root URI must be a Laravel project" and the error cannot be opened. Always open vscode-remote://wsl+Ubuntu/home/chang180/reverb-hub. .vscode/tasks.json folderOpen reuses the window into WSL; keep Laravel installed on the WSL remote, not on Windows.
