#!/usr/bin/env bash
# Pre-commit secret scan: gitleaks over a snapshot of staged blobs.
#
# Worktree-safe: snapshots staged (index) blobs via `git show ":<path>"` into a
# scratch directory under the repo root; never bind-mounts the worktree's .git
# pointer file (which holds an absolute host path a container cannot resolve).
set -euo pipefail

REPO_ROOT="$(git rev-parse --show-toplevel)"
SCRATCH="$REPO_ROOT/.secret-scan-scratch"

cleanup() {
  rm -rf "$SCRATCH"
}
trap cleanup EXIT

rm -rf "$SCRATCH"
mkdir -p "$SCRATCH"

staged_files="$(git diff --cached --name-only --diff-filter=ACM || true)"
if [ -z "$staged_files" ]; then
  exit 0
fi

while IFS= read -r path; do
  [ -z "$path" ] && continue
  dest="$SCRATCH/$path"
  mkdir -p "$(dirname "$dest")"
  git show ":$path" > "$dest"
done <<< "$staged_files"

if command -v gitleaks >/dev/null 2>&1; then
  gitleaks dir "$SCRATCH" --no-banner
else
  MSYS_NO_PATHCONV=1 docker run --rm -v "$SCRATCH:/repo:ro" zricethezav/gitleaks:latest dir /repo --no-banner
fi
