# FrankenPHP Custom Builds

Production-ready PHP binaries matching official FrankenPHP with MongoDB extension added.

## Features

- **Two binary types**: CLI-only and FrankenPHP server (with Caddy)
- **Multiple PHP versions**: 8.3, 8.4, 8.5
- **Multi-platform**: macOS (arm64/x86_64), Linux (x86_64/aarch64)
- **78-79 extensions**: All official FrankenPHP extensions + MongoDB
- **Complete system libraries**: libavif, nghttp2, nghttp3, ngtcp2, watcher for HTTP/3 and QUIC support
- **Laravel-ready**: All extensions needed for modern Laravel applications
- **Fast iteration**: Mac Silicon builds in ~40-45 minutes

## Quick Start

### Download Pre-built Binaries

Download from [GitHub Releases](../../releases):

```bash
# CLI-only binary
curl -L https://github.com/YOUR_USERNAME/frankenphp-custom-builds/releases/latest/download/php-cli-8.4-darwin-arm64 -o php
chmod +x php
./php -v

# FrankenPHP server binary
curl -L https://github.com/YOUR_USERNAME/frankenphp-custom-builds/releases/latest/download/php-web-8.4-darwin-arm64 -o php-web
chmod +x php-web
./php-web version
```

### Build Locally (macOS)

1. **Install dependencies:**
```bash
./scripts/install-deps-mac.sh
```

2. **Build CLI (PHP 8.4):**
```bash
./scripts/build-cli.sh 8.4
```

3. **Test binary:**
```bash
./scripts/test-binary.sh php-cli-8.4-darwin-arm64
```

4. **Build FrankenPHP:**
```bash
./scripts/build-frankenphp.sh 8.4
```

### Build via GitHub Actions

**For CLI builds:**
1. Go to the **Actions** tab
2. Select **"Build CLI - macOS"**
3. Click **"Run workflow"**
4. Select:
   - PHP version: `8.4`
   - Architecture: `arm64`
   - Upload artifact: ✓
5. Wait ~25 minutes
6. Download artifact from workflow run

**For FrankenPHP builds:**
1. Go to the **Actions** tab
2. Select **"Build FrankenPHP - macOS"**
3. Click **"Run workflow"**
4. Select:
   - PHP version: `8.4`
   - Architecture: `arm64`
   - Upload artifact: ✓
5. Wait ~40-60 minutes
6. Download artifact from workflow run

## Included Extensions

**Total: 78-79 extensions** (77 official FrankenPHP + mongodb + imap for PHP 8.3)

### Critical Extensions for Laravel
- **mbregex** - Multibyte regex (UTF-8 string operations)
- **password-argon2** - Modern password hashing
- **mysqlnd** - MySQL native driver
- **yaml** - Configuration file parsing
- **zstd** - Compression (Caddy uses this)
- **memcached** - Caching and session storage
- **mongodb** - MongoDB driver (custom addition)

### Complete Extension List
amqp, apcu, ast, bcmath, brotli, bz2, calendar, ctype, curl, dba, dom, exif, fileinfo, filter, ftp, gd, gmp, gettext, iconv, igbinary, imagick, intl, ldap, lz4, mbregex, mbstring, memcache, memcached, mysqli, mysqlnd, opcache, openssl, password-argon2, parallel, pcntl, pdo, pdo_mysql, pdo_pgsql, pdo_sqlite, pdo_sqlsrv, pgsql, phar, posix, protobuf, readline, redis, session, shmop, simplexml, soap, sockets, sodium, sqlite3, ssh2, sysvmsg, sysvsem, sysvshm, tidy, tokenizer, xlswriter, xml, xmlreader, xmlwriter, xsl, xz, zip, zlib, yaml, zstd, mongodb

**PHP 8.3 includes:** imap (removed in 8.4+)

### System Libraries (PHP_EXTENSION_LIBS)
- **libavif** - AVIF image format support
- **nghttp2** - HTTP/2 protocol support
- **nghttp3** - HTTP/3 protocol support
- **ngtcp2** - QUIC protocol support
- **watcher** - File system watching

### Internationalization
- intl

### Math
- bcmath, gmp

### Caching
- apcu, opcache

### System
- pcntl, posix, sockets

### Utilities
- fileinfo, filter, tokenizer, calendar, ctype, iconv, session, phar

## Binary Types

### CLI-Only (php-cli-*)
- Pure PHP CLI binary
- No web server
- Smaller size (~30-50MB)
- Use for: scripts, cron jobs, workers

```bash
./php-cli-8.4-darwin-arm64 script.php
./php-cli-8.4-darwin-arm64 -m  # List extensions
```

### FrankenPHP Server (php-web-*)
- PHP + Caddy web server
- Full HTTP/2, HTTP/3 support
- Larger size (~80-120MB)
- Use for: web apps, API servers

```bash
./php-web-8.4-darwin-arm64 php-server --listen :8080 --root /path/to/app
./php-web-8.4-darwin-arm64 php-cli script.php  # CLI mode also available
```

## Supported Platforms

| Platform | Architecture | CLI | FrankenPHP | Status |
|----------|-------------|-----|------------|--------|
| macOS | arm64 (Apple Silicon) | ✅ | ✅ | Fully supported |
| macOS | x86_64 (Intel) | ✅ | ✅ | Fully supported |
| Linux | x86_64 | 🚧 | 🚧 | Coming soon |
| Linux | aarch64 (ARM64) | 🚧 | 🚧 | Coming soon |
| Windows | x86_64 (WSL2) | 🚧 | 🚧 | Planned |

## PHP Version Support

| Version | IMAP | Status |
|---------|------|--------|
| PHP 8.3 | ✅ Yes | Supported |
| PHP 8.4 | ❌ No | Supported (IMAP removed) |
| PHP 8.5 | ❌ No | Supported |

**Note:** IMAP extension was removed from PHP 8.4+. For PHP 8.4/8.5, use the [webklex/php-imap](https://github.com/Webklex/php-imap) package via Composer instead.

## GitHub Actions Workflows

### Build CLI - macOS
**File:** `.github/workflows/build-cli-mac.yml`

Builds PHP CLI binaries for macOS. Supports:
- PHP versions: 8.3, 8.4, 8.5, or all
- Architectures: arm64, x86_64, or both
- Manual trigger with inputs

**Build time:** ~20-30 minutes per binary

### Build FrankenPHP - macOS
**File:** `.github/workflows/build-frankenphp-mac.yml`

Builds FrankenPHP server binaries for macOS. Supports:
- PHP versions: 8.3, 8.4, 8.5, or all
- Architectures: arm64, x86_64, or both
- Manual trigger with inputs

**Build time:** ~40-60 minutes per binary

### Build CLI - Linux
**File:** `.github/workflows/build-cli-linux.yml` (coming soon)

Docker-based builds for Linux (musl/glibc).

### Build All
**File:** `.github/workflows/build-all.yml` (coming soon)

Complete build matrix for releases.

## Configuration Files

### Extension Lists
- `configs/extensions/base.txt` - Common extensions (all versions)
- `configs/extensions/php-8.3-extra.txt` - PHP 8.3 specific (IMAP)
- `configs/extensions/php-8.4-extra.txt` - PHP 8.4 specific
- `configs/extensions/php-8.5-extra.txt` - PHP 8.5 specific

### CLI Build Configs
- `configs/cli/craft-8.3.yml` - static-php-cli config for 8.3
- `configs/cli/craft-8.4.yml` - static-php-cli config for 8.4
- `configs/cli/craft-8.5.yml` - static-php-cli config for 8.5

### FrankenPHP Build Configs
- `configs/frankenphp/build-8.3.env` - FrankenPHP config for 8.3
- `configs/frankenphp/build-8.4.env` - FrankenPHP config for 8.4
- `configs/frankenphp/build-8.5.env` - FrankenPHP config for 8.5

## Build Scripts

### `scripts/install-deps-mac.sh`
Installs all required dependencies on macOS via Homebrew.

### `scripts/build-cli.sh <php_version> [arch] [os]`
Builds PHP CLI binary using static-php-cli.

**Examples:**
```bash
./scripts/build-cli.sh 8.4                    # Current platform
./scripts/build-cli.sh 8.3 arm64 darwin       # Specific config
```

### `scripts/build-frankenphp.sh <php_version> [arch] [os]`
Builds FrankenPHP server binary.

**Examples:**
```bash
./scripts/build-frankenphp.sh 8.4
./scripts/build-frankenphp.sh 8.3 arm64 darwin
```

### `scripts/test-binary.sh <binary_path>`
Tests a built binary (CLI or FrankenPHP).

**Examples:**
```bash
./scripts/test-binary.sh php-cli-8.4-darwin-arm64
./scripts/test-binary.sh php-web-8.4-darwin-arm64
```

## Testing

### Extension Tests
```bash
./php-cli-8.4-darwin-arm64 tests/test-extensions.php
```

Validates all required extensions are loaded.

### MongoDB Tests
```bash
./php-cli-8.4-darwin-arm64 tests/test-mongodb.php
```

Tests MongoDB extension functionality.

### Server Tests
```bash
./php-web-8.4-darwin-arm64 php-server --listen :8080 --root tests
curl http://localhost:8080/test-server.php
```

Tests FrankenPHP server functionality.

## Documentation

- **[BUILDING.md](docs/BUILDING.md)** - Detailed build instructions
- **[EXTENSIONS.md](docs/EXTENSIONS.md)** - Extension documentation
- **[WORKFLOWS.md](docs/WORKFLOWS.md)** - GitHub Actions usage

## Versioning

**Format:** `v{MAJOR}.{MINOR}.{PATCH}`

**Examples:**
- `v1.0.0` - Initial release
- `v1.1.0` - Add new PHP version
- `v1.0.1` - Bug fixes

## Contributing

Contributions welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

MIT License - see [LICENSE](LICENSE) file for details.

## Support

- **Issues**: [GitHub Issues](../../issues)
- **Discussions**: [GitHub Discussions](../../discussions)

## Acknowledgments

- [FrankenPHP](https://github.com/dunglas/frankenphp) - Modern PHP application server
- [static-php-cli](https://github.com/crazywhalecc/static-php-cli) - Static PHP compilation tool
- [Caddy](https://caddyserver.com/) - Fast, secure web server

## Binary Sizes (Approximate)

| Type | Platform | Size (Uncompressed) |
|------|----------|---------------------|
| CLI | macOS arm64 | ~35 MB |
| CLI | macOS x86_64 | ~40 MB |
| CLI | Linux x86_64 | ~30 MB |
| FrankenPHP | macOS arm64 | ~100 MB |
| FrankenPHP | macOS x86_64 | ~110 MB |
| FrankenPHP | Linux x86_64 | ~95 MB |

## Build Times (Single Binary)

| Build Type | Time |
|------------|------|
| CLI Mac | ~20-30 min |
| CLI Linux | ~25-40 min |
| FrankenPHP Mac | ~40-60 min |
| FrankenPHP Linux | ~50-70 min |

## Roadmap

- [x] Mac Silicon CLI builds
- [x] Mac Intel CLI builds
- [x] Mac FrankenPHP builds
- [ ] Linux CLI builds
- [ ] Linux FrankenPHP builds
- [ ] Windows WSL2 support
- [ ] ARM64 Linux builds
- [ ] Automated release workflow
- [ ] Binary compression (UPX)
- [ ] Code signing (macOS)

---

**Built with ❤️ for the PHP community**
