# Building From Source

Detailed instructions for building PHP binaries locally.

## Prerequisites

### macOS

**Required:**
- macOS 12+ (Monterey or later)
- Xcode Command Line Tools
- Homebrew package manager

**Install:**
```bash
# Install Xcode Command Line Tools
xcode-select --install

# Install Homebrew (if not installed)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### Linux

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install -y \
    git curl wget \
    build-essential autoconf automake libtool \
    pkg-config cmake \
    php-cli php-dev composer \
    libssl-dev libcurl4-openssl-dev \
    libxml2-dev libsqlite3-dev \
    libonig-dev libzip-dev libbz2-dev \
    libpng-dev libjpeg-dev libwebp-dev \
    libicu-dev libsodium-dev
```

**Fedora/RHEL:**
```bash
sudo dnf install -y \
    git curl wget \
    gcc gcc-c++ make autoconf automake libtool \
    pkg-config cmake \
    php-cli php-devel composer \
    openssl-devel libcurl-devel \
    libxml2-devel sqlite-devel \
    oniguruma-devel libzip-devel bzip2-devel \
    libpng-devel libjpeg-devel libwebp-devel \
    libicu-devel libsodium-devel
```

## Building CLI Binaries

### Step 1: Clone Repository

```bash
git clone https://github.com/YOUR_USERNAME/frankenphp-custom-builds.git
cd frankenphp-custom-builds
```

### Step 2: Install Dependencies (macOS)

```bash
./scripts/install-deps-mac.sh
```

This installs:
- PHP with ZTS (Zend Thread Safety)
- Go (for FrankenPHP builds)
- Build tools (pkg-config, autoconf, etc.)
- Composer

### Step 3: Build CLI Binary

```bash
# Build for current platform
./scripts/build-cli.sh 8.4

# Build specific version
./scripts/build-cli.sh 8.3

# Build all versions
for version in 8.3 8.4 8.5; do
    ./scripts/build-cli.sh $version
done
```

**Build process:**
1. Clones static-php-cli (if not present)
2. Installs composer dependencies
3. Copies craft configuration
4. Downloads PHP and extension sources
5. Compiles PHP with all extensions
6. Outputs: `php-cli-{version}-{os}-{arch}`

**Build time:** ~20-30 minutes

### Step 4: Test Binary

```bash
./scripts/test-binary.sh php-cli-8.4-darwin-arm64
```

Tests:
1. Version check
2. Extension list
3. Extension validation
4. MongoDB extension
5. Basic functionality

## Building FrankenPHP Binaries

### Step 1: Install Additional Dependencies

**macOS:**
```bash
brew install go watcher
```

**Linux:**
```bash
# Install Go
wget https://go.dev/dl/go1.22.0.linux-amd64.tar.gz
sudo tar -C /usr/local -xzf go1.22.0.linux-amd64.tar.gz
export PATH=$PATH:/usr/local/go/bin
```

### Step 2: Build FrankenPHP

```bash
# Build for current platform
./scripts/build-frankenphp.sh 8.4

# Build specific version
./scripts/build-frankenphp.sh 8.3
```

**Build process:**
1. Clones FrankenPHP repository (if not present)
2. Loads build environment config
3. Compiles PHP with --enable-embed --enable-zts
4. Compiles Caddy with FrankenPHP module
5. Links everything into single binary
6. Outputs: `frankenphp-{version}-{os}-{arch}`

**Build time:** ~40-60 minutes

### Step 3: Test FrankenPHP

```bash
./scripts/test-binary.sh frankenphp-8.4-darwin-arm64

# Or manually:
./frankenphp-8.4-darwin-arm64 version
./frankenphp-8.4-darwin-arm64 php-cli -v
./frankenphp-8.4-darwin-arm64 php-server --listen :8080 --root tests
```

## Customizing Extensions

### Adding Extensions

1. **Edit extension list:**
```bash
# Add to configs/extensions/base.txt
echo "grpc" >> configs/extensions/base.txt
```

2. **Update craft config:**
```yaml
# Edit configs/cli/craft-8.4.yml
extensions:
  - grpc  # Add here
  - mongodb
  - ffi
  # ... other extensions
```

3. **Add library dependencies (if needed):**
```yaml
libs:
  - libgrpc  # Add here
  - libmongoc
  - libffi
  # ... other libs
```

4. **Rebuild:**
```bash
./scripts/build-cli.sh 8.4
```

### Removing Extensions

1. **Comment out in craft config:**
```yaml
extensions:
  # - imagick  # Remove this
  - mongodb
  - ffi
```

2. **Rebuild:**
```bash
./scripts/build-cli.sh 8.4
```

## Docker Builds (Linux)

### Build CLI with Docker

```bash
# Create Dockerfile
cat > Dockerfile.cli <<'EOF'
FROM alpine:3.19 AS builder

RUN apk add --no-cache \
    php php-dev php-phar php-zts \
    gcc g++ make cmake autoconf automake libtool \
    linux-headers git curl wget \
    openssl-dev curl-dev libxml2-dev \
    sqlite-dev oniguruma-dev libzip-dev bzip2-dev \
    libpng-dev libjpeg-turbo-dev libwebp-dev \
    icu-dev libsodium-dev gmp-dev

WORKDIR /build

# Clone static-php-cli
RUN git clone --depth 1 https://github.com/crazywhalecc/static-php-cli.git .
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php composer.phar install --no-dev

# Copy config
COPY configs/cli/craft-8.4.yml ./craft.yml

# Download and build
RUN php bin/spc download --with-php=8.4
RUN php bin/spc build --build-cli

# Output binary
FROM scratch
COPY --from=builder /build/buildroot/bin/php /php
EOF

# Build
docker build -f Dockerfile.cli -t php-cli-builder .

# Extract binary
docker create --name php-cli-tmp php-cli-builder
docker cp php-cli-tmp:/php ./php-cli-8.4-linux-x86_64
docker rm php-cli-tmp

# Test
chmod +x php-cli-8.4-linux-x86_64
./php-cli-8.4-linux-x86_64 -v
```

### Build FrankenPHP with Docker

```bash
cd frankenphp-custom-builds

# Clone FrankenPHP if needed
git clone https://github.com/dunglas/frankenphp.git

cd frankenphp

# Build using Docker
docker buildx bake --load static-builder-musl

# Extract binary
docker cp $(docker create --name tmp dunglas/frankenphp:static-builder-musl):/go/src/app/dist/frankenphp-linux-x86_64 ./frankenphp-linux-x86_64
docker rm tmp

# Test
chmod +x frankenphp-linux-x86_64
./frankenphp-linux-x86_64 version
```

## Troubleshooting

### Build Fails: Missing Dependencies

**Error:** "configure: error: Cannot find XXX"

**Solution:**
```bash
# macOS
brew install <package-name>

# Linux
sudo apt-get install lib<package>-dev
```

### Build Fails: Extension Not Found

**Error:** "Extension XXX not found"

**Solution:**
1. Check extension name in static-php-cli: `php bin/spc list-extensions`
2. Verify extension is supported on your platform
3. Check if extension requires additional libraries

### Binary Crashes on Startup

**Error:** "Illegal instruction" or "Segmentation fault"

**Possible causes:**
1. Architecture mismatch (built for different CPU)
2. Missing system libraries
3. Incompatible extension

**Solution:**
```bash
# Check binary architecture
file php-cli-8.4-darwin-arm64

# Check dependencies (macOS)
otool -L php-cli-8.4-darwin-arm64

# Check dependencies (Linux)
ldd php-cli-8.4-linux-x86_64

# Rebuild with debug symbols
export DEBUG_SYMBOLS=1
./scripts/build-cli.sh 8.4
```

### MongoDB Extension Fails to Load

**Error:** "Cannot load extension: mongodb"

**Solution:**
```bash
# Verify mongodb is in config
grep mongodb configs/cli/craft-8.4.yml

# Verify libmongoc is in libs
grep libmongoc configs/cli/craft-8.4.yml

# Test manually
./php-cli-8.4-darwin-arm64 -d extension=mongodb.so -m | grep mongodb
```

### Build is Very Slow

**Normal build times:**
- CLI: 20-30 minutes
- FrankenPHP: 40-60 minutes

**Speed up builds:**
1. Use cached builds (don't use `export CLEAN=1`)
2. Build only needed extensions
3. Use faster machine (GitHub Actions has better specs)
4. Disable optimization temporarily (for testing)

### Out of Disk Space

**Error:** "No space left on device"

**Solution:**
```bash
# Check disk space
df -h

# Clean build artifacts
rm -rf static-php-cli/buildroot
rm -rf frankenphp/dist

# Clean Docker (if using)
docker system prune -a
```

## Advanced Configuration

### Custom PHP Configuration

Edit compile flags in craft config:

```yaml
build-options:
  - --with-openssl
  - --with-curl
  - --enable-opcache=1
  - --with-pcre-jit
  - --enable-inline-optimization
  - --disable-phpdbg
```

### Static vs Dynamic Linking

**Fully static (musl):**
- Zero dependencies
- Larger binary size
- Best portability
- Use on Linux: Alpine, scratch containers

**Mostly static (glibc):**
- Requires glibc
- Smaller binary size
- Can load dynamic extensions
- Use on Linux: Ubuntu, Debian, Fedora

### Optimization Flags

```bash
# Maximum optimization
export CFLAGS="-O3 -march=native"
export CXXFLAGS="-O3 -march=native"
./scripts/build-cli.sh 8.4

# Debug build
export CFLAGS="-O0 -g"
export CXXFLAGS="-O0 -g"
export DEBUG_SYMBOLS=1
./scripts/build-cli.sh 8.4
```

### Cross-Compilation

**macOS arm64 → x86_64:**
```bash
# Not directly supported
# Use GitHub Actions with macos-13 runner instead
```

**Linux x86_64 → aarch64:**
```bash
# Use Docker buildx
docker buildx create --use
docker buildx build --platform linux/arm64 -t php-cli-arm64 .
```

## Performance Tips

1. **Use SSD** for build directory
2. **Increase RAM** (8GB minimum, 16GB recommended)
3. **Use ccache** to cache compilations:
   ```bash
   brew install ccache
   export CC="ccache gcc"
   export CXX="ccache g++"
   ```
4. **Parallel builds**:
   ```bash
   export MAKEFLAGS="-j$(nproc)"
   ```

## Next Steps

- [Extensions Documentation](EXTENSIONS.md)
- [GitHub Actions Workflows](WORKFLOWS.md)
- [Return to README](../README.md)
