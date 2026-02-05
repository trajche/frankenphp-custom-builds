# Burd FrankenPHP Builds

Custom FrankenPHP binaries with 78-79 extensions including MongoDB.

## Quick Start (GitHub Actions)

### Build FrankenPHP (Web Server)

```bash
# Trigger build
gh workflow run "Build FrankenPHP - macOS" \
  --field php_version=8.4 \
  --field architecture=arm64

# Monitor
gh run watch

# Download when complete
gh run download {RUN_ID}
```

### Build CLI Only

```bash
gh workflow run "Build CLI - macOS" \
  --field php_version=8.4 \
  --field architecture=arm64
```

## Test Binary

```bash
# Check version
./php-web-8.4-darwin-arm64 version
# Output: FrankenPHP vBurd PHP 8.4.x Caddy v2.x.x

# Count extensions (should be ~79)
./php-web-8.4-darwin-arm64 php-cli -r 'echo count(get_loaded_extensions()) . " extensions\n";'

# Verify MongoDB
./php-web-8.4-darwin-arm64 php-cli -r 'var_dump(extension_loaded("mongodb"));'
```

## PHP Versions

| Version | Extensions | Notes |
|---------|------------|-------|
| 8.3 | 79 | Includes IMAP |
| 8.4 | 78 | IMAP removed |
| 8.5 | 78 | IMAP removed |

## Configuration

Build configs are in `configs/frankenphp/build-{version}.env`:

```bash
export PHP_VERSION=8.4
export FRANKENPHP_VERSION=Burd
export PHP_EXTENSIONS="amqp,apcu,ast,bcmath,..."  # 78 extensions
export PHP_EXTENSION_LIBS="libavif,nghttp2,nghttp3,ngtcp2,watcher"
```

## Extensions (78 total)

All official FrankenPHP extensions plus MongoDB:

amqp, apcu, ast, bcmath, brotli, bz2, calendar, ctype, curl, dba, dom, exif, fileinfo, filter, ftp, gd, gmp, gettext, iconv, igbinary, imagick, intl, ldap, lz4, mbregex, mbstring, memcache, memcached, mysqli, mysqlnd, opcache, openssl, parallel, password-argon2, pcntl, pdo, pdo_mysql, pdo_pgsql, pdo_sqlite, pdo_sqlsrv, pgsql, phar, posix, protobuf, readline, redis, session, shmop, simplexml, soap, sockets, sodium, sqlite3, ssh2, sysvmsg, sysvsem, sysvshm, tidy, tokenizer, xlswriter, xml, xmlreader, xmlwriter, xsl, xz, yaml, zip, zlib, zstd, **mongodb**

## System Libraries

Required for HTTP/3, QUIC, and modern features:

- **libavif** - AVIF image support
- **nghttp2** - HTTP/2
- **nghttp3** - HTTP/3
- **ngtcp2** - QUIC
- **watcher** - File watching

## Build Times

| Type | Time |
|------|------|
| FrankenPHP | ~45 min |
| CLI | ~25 min |

## License

MIT
