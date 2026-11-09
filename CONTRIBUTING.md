# Contributing

Thank you for considering contributing to this project!

## Report a Bug

Open an issue describing the expected vs. actual behavior, steps to reproduce,
and the environment (language version, platform, relevant dependencies).

## Request a Feature

Open an issue with the use case and why it belongs in this project. Discuss
before opening a pull request.

## Pull Request Workflow

1. Create a branch named `phase-<n>/<topic>` (for example `phase-1/docs-api`).
2. Make your change. Keep commits scoped to a single concern.
3. Commit with conventional commits (e.g. `docs: ...`, `feat: ...`, `fix: ...`).
4. Rebase onto `main` before merging (rebase-merge; avoid merge commits).
5. Delete the feature branch after it is merged.

## Coding Standards

- Follow the project's existing code style and patterns.
- Run static analysis/linting tools before pushing (if configured).
- Keep changes focused; avoid committing commented-out code or dead weight.
- Update documentation when changing user-facing behavior.

## Testing

- Run the test suite locally before submitting a PR.
- Add tests for new functionality.
- Ensure all existing tests pass.

## Commit Authorship

No AI attribution in commits. Do not add `Co-Authored-By` or `Claude-Session`
trailers.