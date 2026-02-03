#!/bin/bash
set -e

# Configuration
PHP_VERSION=${1:-8.4}
ARCH=${2:-$(uname -m)}
OS=${3:-$(uname -s | tr '[:upper:]' '[:lower:]')}

echo "======================================"
echo "Building PHP CLI"
echo "======================================"
echo "PHP Version: ${PHP_VERSION}"
echo "Architecture: ${ARCH}"
echo "OS: ${OS}"
echo "======================================"
echo

# Determine project root (script is in scripts/ subdirectory)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# Load configuration
CONFIG_FILE="configs/cli/craft-${PHP_VERSION}.yml"
if [ ! -f "$CONFIG_FILE" ]; then
    echo "Error: Config file not found: $CONFIG_FILE"
    exit 1
fi

echo "Using config: $CONFIG_FILE"
echo

# Clone static-php-cli if needed
if [ ! -d "static-php-cli" ]; then
    echo "Cloning static-php-cli..."
    git clone --depth 1 https://github.com/crazywhalecc/static-php-cli.git
    echo
fi

cd static-php-cli

# Install composer dependencies if needed
if [ ! -d "vendor" ]; then
    echo "Installing composer dependencies..."
    composer install --no-dev --optimize-autoloader
    echo
fi

# Copy config
echo "Copying configuration..."
cp "../${CONFIG_FILE}" ./craft.yml

# Check doctor
echo "Running SPC doctor..."
php bin/spc doctor --auto-fix || true
echo

# Download sources
echo "Downloading PHP ${PHP_VERSION} and extension sources..."
php bin/spc download --with-php=${PHP_VERSION} --for-extensions="$(grep -v '^#' ../configs/extensions/base.txt | grep -v '^$' | tr '\n' ',' | sed 's/,$//')"
echo

# Build
echo "Building PHP CLI..."
echo "This may take 20-30 minutes..."
php bin/spc build --build-cli
echo

# Test binary
echo "Testing binary..."
if [ ! -f "./buildroot/bin/php" ]; then
    echo "Error: Binary not found at ./buildroot/bin/php"
    exit 1
fi

./buildroot/bin/php -v
echo
./buildroot/bin/php -m | head -20
echo

# Rename and copy output
OUTPUT_NAME="php-cli-${PHP_VERSION}-${OS}-${ARCH}"
echo "Copying binary to: ${OUTPUT_NAME}"
cp ./buildroot/bin/php "../${OUTPUT_NAME}"

cd ..

# Make executable
chmod +x "${OUTPUT_NAME}"

# Get size
SIZE=$(du -h "${OUTPUT_NAME}" | cut -f1)

echo
echo "======================================"
echo "Build complete!"
echo "======================================"
echo "Binary: ${OUTPUT_NAME}"
echo "Size: ${SIZE}"
echo "======================================"
echo
echo "Test with:"
echo "  ./${OUTPUT_NAME} -v"
echo "  ./${OUTPUT_NAME} -m"
echo
