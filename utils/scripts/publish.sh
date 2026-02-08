#!/usr/bin/env bash
set -euo pipefail

# Publish the panagea-core plugin from the monorepo to the split branch + tag.
# Usage: ./utils/scripts/publish.sh <version>

PREFIX="web/app/plugins/panagea-core"
PUBLISHED_BRANCH="panagea-core"  # both local temp ref and remote branch name

VERSION_INPUT="${1:-}"
if [[ -z "$VERSION_INPUT" ]]; then
  echo "Usage: $0 <version>"
  echo "Example: $0 0.1.0"
  exit 1
fi

# Normalize version/tag
VERSION="${VERSION_INPUT#v}"
TAG="v${VERSION}"

# Safety checks
current_branch=$(git rev-parse --abbrev-ref HEAD)
if [[ "$current_branch" != "main" ]]; then
  echo "Please run this script from branch 'main'. Current: $current_branch"
  exit 1
fi

repo_root=$(git rev-parse --show-toplevel)
if [[ "$PWD" != "$repo_root" ]]; then
  echo "Please run from the repository root: $repo_root"
  exit 1
fi

if [[ -n "$(git status --porcelain)" ]]; then
  echo "Working tree is not clean. Commit or stash changes first."
  exit 1
fi

if git show-ref --tags --quiet --verify "refs/tags/${TAG}"; then
  echo "Tag ${TAG} already exists. Bump the version."
  exit 1
fi

# Determine latest existing tag (if any) and enforce monotonic increase
latest_tag=$(git tag --list 'v*' --sort=-v:refname | head -n1 || true)
if [[ -n "$latest_tag" ]]; then
  latest_version="${latest_tag#v}"
  greatest=$(printf "%s\n%s\n" "$latest_version" "$VERSION" | sort -V | tail -n1)
  if [[ "$greatest" != "$VERSION" ]]; then
    echo "Version ${VERSION} must be greater than latest tag ${latest_tag}"
    exit 1
  fi
fi

echo "Splitting ${PREFIX} from main..."
split_ref=$(git subtree split --prefix="$PREFIX")

# Check if the split content is already published on the remote branch to avoid unnecessary force pushes
remote_tip=$(git ls-remote --heads origin "${PUBLISHED_BRANCH}" | awk '{print $1}' || true)
if [[ -n "$remote_tip" && "$remote_tip" == "$split_ref" ]]; then
  echo "No changes to publish: current split matches origin/${PUBLISHED_BRANCH} (${split_ref})."
  exit 0
fi

echo "Force pushing split to origin:${PUBLISHED_BRANCH}..."
git push origin "${split_ref}:${PUBLISHED_BRANCH}" --force

echo "Tagging ${TAG} at split ref..."
git tag -a "$TAG" "$split_ref" -m "panagea-core ${VERSION}"
git push origin "$TAG"

echo "Done. Published ${TAG} on branch ${PUBLISHED_BRANCH}."
