# GitHub Actions Workflows

Guide to using GitHub Actions for automated builds.

## Available Workflows

- [Build CLI - macOS](#build-cli---macos)
- [Build FrankenPHP - macOS](#build-frankenphp---macos-coming-soon)
- [Build CLI - Linux](#build-cli---linux-coming-soon)
- [Build All](#build-all-coming-soon)

---

## Build CLI - macOS

**File:** `.github/workflows/build-cli-mac.yml`

Builds PHP CLI binaries for macOS (Apple Silicon and Intel).

### Triggers

**Manual Dispatch:**
- Go to Actions → "Build CLI - macOS" → "Run workflow"

**Automatic:**
- Push to `main` branch (if configs change)
- Pull requests (if configs change)
- Tag creation (for releases)

### Inputs

| Input | Type | Options | Default | Description |
|-------|------|---------|---------|-------------|
| php_version | choice | 8.3, 8.4, 8.5, all | 8.4 | PHP version to build |
| architecture | choice | arm64, x86_64, both | arm64 | macOS architecture |
| upload_artifact | boolean | true/false | true | Upload binary as artifact |

### Usage Examples

#### Build Single Binary (Fastest)

**Goal:** Test Mac Silicon with PHP 8.4 (~25 minutes)

**Steps:**
1. Go to **Actions** tab
2. Select **"Build CLI - macOS"**
3. Click **"Run workflow"**
4. Set:
   - PHP version: `8.4`
   - Architecture: `arm64`
   - Upload artifact: ✓
5. Click **"Run workflow"**

**Result:** `php-cli-8.4-darwin-arm64` in ~25 minutes

#### Build All Mac Silicon Versions

**Goal:** Build PHP 8.3, 8.4, 8.5 for Apple Silicon

**Steps:**
1. Go to **Actions** tab
2. Select **"Build CLI - macOS"**
3. Click **"Run workflow"**
4. Set:
   - PHP version: `all`
   - Architecture: `arm64`
   - Upload artifact: ✓
5. Click **"Run workflow"**

**Result:** 3 binaries in ~30 minutes (parallel)

#### Build All Architectures for PHP 8.4

**Goal:** Build PHP 8.4 for both arm64 and x86_64

**Steps:**
1. Go to **Actions** tab
2. Select **"Build CLI - macOS"**
3. Click **"Run workflow"**
4. Set:
   - PHP version: `8.4`
   - Architecture: `both`
   - Upload artifact: ✓
5. Click **"Run workflow"**

**Result:** 2 binaries in ~25 minutes (parallel)

#### Build Complete Matrix

**Goal:** All versions, all architectures (6 binaries)

**Steps:**
1. Go to **Actions** tab
2. Select **"Build CLI - macOS"**
3. Click **"Run workflow"**
4. Set:
   - PHP version: `all`
   - Architecture: `both`
   - Upload artifact: ✓
5. Click **"Run workflow"**

**Result:** 6 binaries in ~30 minutes (parallel)

### Build Matrix

| Runner | Architecture | PHP Versions |
|--------|--------------|--------------|
| macos-14 | arm64 (Apple Silicon) | 8.3, 8.4, 8.5 |
| macos-13 | x86_64 (Intel) | 8.3, 8.4, 8.5 |

### Workflow Steps

1. **Checkout repository**
2. **Set up Homebrew**
3. **Install dependencies**
   - PHP with ZTS
   - Composer
   - Build tools
4. **Clone static-php-cli**
5. **Configure build**
   - Copy craft config
6. **Run SPC doctor**
7. **Download sources**
   - PHP source
   - Extension sources
8. **Build PHP CLI**
   - Compile PHP
   - Compile extensions
   - Link statically
9. **Test binary**
   - Version check
   - Extension test
   - MongoDB test
10. **Prepare artifact**
    - Rename binary
    - Generate checksum
11. **Upload artifact**
    - To GitHub Actions
    - To release (if tagged)

### Downloading Artifacts

#### From Workflow Run

1. Go to **Actions** tab
2. Click on workflow run
3. Scroll to **"Artifacts"** section
4. Download binary

#### From Command Line

```bash
# Install GitHub CLI
brew install gh

# List artifacts
gh run list --workflow=build-cli-mac.yml

# Download latest artifact
gh run download --name php-cli-8.4-darwin-arm64
```

### Build Times

| Configuration | Time | Cost |
|---------------|------|------|
| Single binary | ~25 min | Free |
| 3 versions (parallel) | ~30 min | Free |
| All architectures (parallel) | ~25 min | Free |
| Complete matrix (parallel) | ~30 min | Free |

**Note:** GitHub Actions is free for public repositories.

---

## Build FrankenPHP - macOS (Coming Soon)

**File:** `.github/workflows/build-server-mac.yml`

Similar to CLI builds but for FrankenPHP server binaries.

### Key Differences

- Longer build time (~50 minutes)
- Requires Go installation
- Builds Caddy + PHP
- Larger binary size (~100MB)

---

## Build CLI - Linux (Coming Soon)

**File:** `.github/workflows/build-cli-linux.yml`

Docker-based builds for Linux.

### Features

- **libc choice**: musl (fully static) or glibc (mostly static)
- **Architectures**: x86_64, aarch64 (ARM64)
- **Build time**: ~30-40 minutes

---

## Build All (Coming Soon)

**File:** `.github/workflows/build-all.yml`

Complete build matrix for releases.

### Triggered By

- Tag creation: `v*` (e.g., `v1.0.0`)
- Manual dispatch

### Builds

- CLI: 6 macOS + 4 Linux = 10 binaries
- FrankenPHP: 6 macOS + 4 Linux = 10 binaries
- **Total**: 20 binaries

### Workflow Steps

1. **Trigger all builds** (parallel)
2. **Collect artifacts**
3. **Generate checksums** (SHA256SUMS.txt)
4. **Create GitHub release**
5. **Upload all binaries**

### Build Time

**Total:** ~2-3 hours (parallel execution)

---

## Creating Releases

### Manual Release

1. **Tag the release:**
```bash
git tag v1.0.0
git push origin v1.0.0
```

2. **Wait for builds** (~2-3 hours)

3. **Verify release** on GitHub

### Automated Release

The `build-all.yml` workflow automatically:
- Builds all binaries
- Generates checksums
- Creates GitHub release
- Attaches binaries
- Generates release notes

---

## Workflow Configuration

### Customizing Build

**Edit config files:**
```bash
# Add extension
echo "grpc" >> configs/extensions/base.txt

# Update craft config
vim configs/cli/craft-8.4.yml

# Commit and push
git add configs/
git commit -m "Add GRPC extension"
git push

# Trigger build
gh workflow run build-cli-mac.yml -f php_version=8.4 -f architecture=arm64
```

### Caching

GitHub Actions caches:
- Homebrew packages
- Composer dependencies
- Downloaded sources (if enabled)

**Clear cache:**
Go to Actions → Caches → Delete

---

## Monitoring Builds

### View Build Logs

1. Go to **Actions** tab
2. Click on workflow run
3. Click on job name
4. View logs

### Download Logs

```bash
gh run view --log
```

### Build Notifications

Configure in repository settings:
- Settings → Notifications
- Email or Slack integration

---

## Troubleshooting Workflows

### Build Fails: Dependencies

**Error:** "Package not found"

**Solution:**
- Check Homebrew formula exists
- Update workflow to install package

### Build Fails: Timeout

**Error:** "Build exceeded timeout"

**Solution:**
- Reduce extensions
- Split into multiple builds
- Use faster runner (if available)

### Build Fails: Extension

**Error:** "Extension XXX failed to compile"

**Solution:**
- Check extension compatibility
- Review build logs
- Test locally first

### Artifact Upload Fails

**Error:** "Failed to upload artifact"

**Solution:**
- Check artifact size (<2GB)
- Verify artifact name is unique
- Check permissions

---

## Best Practices

### Development

1. **Test locally first:**
```bash
./scripts/build-cli.sh 8.4
./scripts/test-binary.sh php-cli-8.4-darwin-arm64
```

2. **Use single builds** for testing:
   - PHP version: `8.4`
   - Architecture: `arm64`

3. **Verify changes** before full matrix

### Production

1. **Use tagged releases** for stable versions
2. **Test binaries** before distribution
3. **Keep checksums** for verification
4. **Document changes** in release notes

### Cost Optimization

1. **Use manual dispatch** for testing
2. **Build only needed versions**
3. **Cache aggressively**
4. **Clean old artifacts**

---

## Advanced Usage

### Conditional Builds

Trigger builds only when configs change:

```yaml
on:
  push:
    paths:
      - 'configs/**'
      - 'scripts/**'
```

### Matrix Exclusions

Build only specific combinations:

```yaml
strategy:
  matrix:
    php: [8.3, 8.4, 8.5]
    arch: [arm64, x86_64]
    exclude:
      - php: 8.3
        arch: x86_64  # Skip Intel builds for 8.3
```

### Parallel Jobs

GitHub Actions runs matrix jobs in parallel (up to 20 concurrent jobs for free tier).

### Self-Hosted Runners

For faster builds or custom requirements:

1. Set up self-hosted runner
2. Update workflow:
```yaml
runs-on: self-hosted
```

---

## Workflow Security

### Secrets

Required for releases:
- `GITHUB_TOKEN` (automatically provided)

Optional:
- Signing keys (for code signing)
- Notification webhooks

### Permissions

Workflow permissions:
```yaml
permissions:
  contents: write  # For releases
  actions: read    # For artifacts
```

---

## Next Steps

- [Building Documentation](BUILDING.md)
- [Extensions Documentation](EXTENSIONS.md)
- [Return to README](../README.md)
