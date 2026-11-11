# Pre-commit secret-scan hook (gitleaks)

This repo ships an optional pre-commit secret scan powered by [gitleaks](https://github.com/zricethezav/gitleaks).

## Install (opt-in, per clone)

The hook is **not** active by default (`core.hooksPath` cannot be forced on checkout). Enable it in your local clone:

```bash
git config core.hooksPath .githooks
```

## How it works

On each commit, `scripts/secret-scan.sh` snapshots the staged (index) blobs of all staged files into a scratch directory under the repo root and runs `gitleaks detect` against that snapshot. The commit is blocked (exit 1) if gitleaks finds a potential secret; otherwise it proceeds (exit 0).

The snapshot approach is used instead of bind-mounting `.git` because a `git worktree`'s `.git` is a pointer file holding an absolute host path that a container cannot resolve.

## gitleaks resolution

1. If `gitleaks` is on `PATH`, the local binary is used.
2. Otherwise a disposable `zricethezav/gitleaks:latest` Docker container is used.
3. If neither is available, the hook warns and allows the commit (the CI secret-scan job, added in Phase 10, is the backstop).

## Verification

- Planted secret (e.g. `AWS_SECRET_ACCESS_KEY=<redacted>` staged) → commit blocked, exit 1.
- Clean staged content → commit allowed, exit 0.
