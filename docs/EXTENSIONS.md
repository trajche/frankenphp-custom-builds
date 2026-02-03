# Extensions Documentation

Complete reference for all included PHP extensions.

## Extension Categories

- [Required Extensions](#required-extensions)
- [Database Extensions](#database-extensions)
- [Core Functionality](#core-functionality)
- [Image Processing](#image-processing)
- [XML/Data Processing](#xmldata-processing)
- [Internationalization](#internationalization)
- [Math Extensions](#math-extensions)
- [Caching Extensions](#caching-extensions)
- [System Extensions](#system-extensions)
- [Utility Extensions](#utility-extensions)

---

## Required Extensions

### mongodb
**Description:** Official MongoDB driver for PHP
**Version Support:** All (8.3, 8.4, 8.5)
**Platform:** All platforms
**Use Case:** MongoDB database connections

**Example:**
```php
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
$collection = new MongoDB\Collection($manager, 'mydb', 'mycollection');
$result = $collection->find(['name' => 'John']);
```

### FFI
**Description:** Foreign Function Interface - call C functions from PHP
**Version Support:** All (8.3, 8.4, 8.5)
**Platform:** All (incompatible with musl libc on Linux - use glibc builds)
**Use Case:** Call C libraries, system calls, performance-critical code

**Example:**
```php
$ffi = FFI::cdef("
    int printf(const char *format, ...);
", "libc.so.6");
$ffi->printf("Hello from FFI!\n");
```

**Note:** FFI requires glibc builds on Linux. Not available in musl-based static builds.

### SPX
**Description:** Simple Profiler Extension - lightweight profiling
**Version Support:** All (8.3, 8.4, 8.5)
**Platform:** Linux, macOS (NOT Windows)
**Use Case:** Performance profiling, debugging

**Example:**
```php
// Enable in php.ini or via environment
putenv('SPX_ENABLED=1');
putenv('SPX_AUTO_START=1');

// Your code here
// Profile data available at /_spx
```

**Configuration:**
```ini
spx.http_enabled=1
spx.http_key="dev"
spx.http_ip_whitelist="127.0.0.1"
```

### imap
**Description:** IMAP, POP3, and NNTP email functions
**Version Support:** PHP 8.3 ONLY (removed in 8.4+)
**Platform:** All
**Use Case:** Email client functionality

**Example:**
```php
$mbox = imap_open("{localhost:993/imap/ssl}INBOX", "user@example.com", "password");
$emails = imap_search($mbox, 'UNSEEN');
foreach ($emails as $email_number) {
    $message = imap_fetchbody($mbox, $email_number, 1);
}
imap_close($mbox);
```

**Alternative (PHP 8.4+):**
```bash
composer require webklex/php-imap
```

---

## Database Extensions

### pdo
**Description:** PHP Data Objects - database abstraction layer
**Version Support:** All
**Platform:** All

### pdo_mysql
**Description:** MySQL driver for PDO
**Example:**
```php
$pdo = new PDO('mysql:host=localhost;dbname=test', 'user', 'pass');
$stmt = $pdo->query('SELECT * FROM users');
```

### pdo_pgsql
**Description:** PostgreSQL driver for PDO
**Example:**
```php
$pdo = new PDO('pgsql:host=localhost;dbname=test', 'user', 'pass');
```

### pdo_sqlite
**Description:** SQLite driver for PDO
**Example:**
```php
$pdo = new PDO('sqlite:/path/to/database.db');
```

### mysqli
**Description:** MySQL Improved extension
**Example:**
```php
$mysqli = new mysqli('localhost', 'user', 'pass', 'database');
$result = $mysqli->query('SELECT * FROM users');
```

### redis
**Description:** Redis client extension
**Example:**
```php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->set('key', 'value');
echo $redis->get('key');
```

---

## Core Functionality

### curl
**Description:** Client URL library for HTTP requests
**Example:**
```php
$ch = curl_init('https://api.example.com/data');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
```

### openssl
**Description:** OpenSSL cryptographic functions
**Example:**
```php
$encrypted = openssl_encrypt('data', 'aes-256-cbc', $key, 0, $iv);
$decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
```

### mbstring
**Description:** Multibyte string functions
**Example:**
```php
$length = mb_strlen('Hello 世界', 'UTF-8');  // 8
$substr = mb_substr('Hello 世界', 0, 5, 'UTF-8');  // "Hello"
```

### zip
**Description:** ZIP archive manipulation
**Example:**
```php
$zip = new ZipArchive();
$zip->open('archive.zip', ZipArchive::CREATE);
$zip->addFile('file.txt');
$zip->close();
```

### zlib
**Description:** Gzip compression
**Example:**
```php
$compressed = gzcompress('data');
$decompressed = gzuncompress($compressed);
```

### bz2
**Description:** Bzip2 compression
**Example:**
```php
$compressed = bzcompress('data');
$decompressed = bzdecompress($compressed);
```

### sodium
**Description:** Modern cryptography library
**Example:**
```php
$key = sodium_crypto_secretbox_keygen();
$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
$encrypted = sodium_crypto_secretbox('message', $nonce, $key);
```

---

## Image Processing

### gd
**Description:** Image manipulation with GD library
**Example:**
```php
$image = imagecreatefromjpeg('photo.jpg');
imagefilter($image, IMG_FILTER_GRAYSCALE);
imagejpeg($image, 'photo_bw.jpg', 90);
imagedestroy($image);
```

**Supported formats:** JPEG, PNG, GIF, WebP

### imagick
**Description:** ImageMagick bindings (more powerful than GD)
**Example:**
```php
$image = new Imagick('photo.jpg');
$image->resizeImage(800, 600, Imagick::FILTER_LANCZOS, 1);
$image->writeImage('resized.jpg');
```

**Supported formats:** 200+ formats including JPEG, PNG, GIF, TIFF, PDF, SVG

### exif
**Description:** Extract EXIF metadata from images
**Example:**
```php
$exif = exif_read_data('photo.jpg');
echo $exif['Make'];  // Camera manufacturer
echo $exif['DateTime'];  // When photo was taken
```

---

## XML/Data Processing

### xml
**Description:** XML parser
**Example:**
```php
$parser = xml_parser_create();
xml_parse($parser, $xml_data);
xml_parser_free($parser);
```

### dom
**Description:** DOM XML manipulation
**Example:**
```php
$doc = new DOMDocument();
$doc->loadXML('<root><item>value</item></root>');
$items = $doc->getElementsByTagName('item');
```

### simplexml
**Description:** Simple XML parser
**Example:**
```php
$xml = simplexml_load_string($xml_string);
echo $xml->item[0];
```

### xmlreader
**Description:** Pull-based XML parser (memory efficient)
**Example:**
```php
$reader = new XMLReader();
$reader->open('large-file.xml');
while ($reader->read()) {
    if ($reader->nodeType == XMLReader::ELEMENT) {
        // Process element
    }
}
```

### xmlwriter
**Description:** XML writer
**Example:**
```php
$writer = new XMLWriter();
$writer->openMemory();
$writer->startElement('root');
$writer->writeElement('item', 'value');
$writer->endElement();
echo $writer->outputMemory();
```

### soap
**Description:** SOAP web services
**Example:**
```php
$client = new SoapClient('http://example.com/api?wsdl');
$result = $client->someMethod($params);
```

### xsl
**Description:** XSL transformations
**Example:**
```php
$xsl = new DOMDocument();
$xsl->load('transform.xsl');
$proc = new XSLTProcessor();
$proc->importStyleSheet($xsl);
echo $proc->transformToXML($xml_doc);
```

---

## Internationalization

### intl
**Description:** Internationalization functions (ICU)
**Example:**
```php
// Format numbers
$fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
echo $fmt->formatCurrency(1234.56, 'USD');  // $1,234.56

// Format dates
$fmt = new IntlDateFormatter('en_US', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
echo $fmt->format(time());

// Transliteration
echo transliterator_transliterate('Any-Latin; Latin-ASCII', 'Ça été');  // Ca ete
```

---

## Math Extensions

### bcmath
**Description:** Arbitrary precision mathematics
**Example:**
```php
$sum = bcadd('1234567890123456789', '9876543210987654321', 0);
$result = bcdiv('100', '3', 20);  // 33.33333333333333333333
```

### gmp
**Description:** GNU Multiple Precision mathematics
**Example:**
```php
$a = gmp_init('12345678901234567890');
$b = gmp_init('98765432109876543210');
$sum = gmp_add($a, $b);
echo gmp_strval($sum);
```

---

## Caching Extensions

### apcu
**Description:** APCu user cache (in-memory)
**Example:**
```php
apcu_store('key', 'value', 3600);
$value = apcu_fetch('key');
apcu_delete('key');
```

**Note:** Each PHP process has its own APCu cache (not shared between processes in CLI mode).

### opcache
**Description:** Opcode cache (PHP acceleration)
**Configuration:**
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

**Example:**
```php
opcache_reset();  // Clear cache
$status = opcache_get_status();
```

---

## System Extensions

### pcntl
**Description:** Process control (Unix only)
**Example:**
```php
$pid = pcntl_fork();
if ($pid == -1) {
    die('Fork failed');
} elseif ($pid) {
    // Parent process
    pcntl_wait($status);
} else {
    // Child process
    exit(0);
}
```

**Note:** Not available on Windows.

### posix
**Description:** POSIX functions (Unix only)
**Example:**
```php
$uid = posix_getuid();
$info = posix_getpwuid($uid);
echo $info['name'];  // Current user
```

### sockets
**Description:** Low-level socket programming
**Example:**
```php
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($socket, '127.0.0.1', 8080);
socket_write($socket, "GET / HTTP/1.1\r\n\r\n");
$response = socket_read($socket, 4096);
socket_close($socket);
```

---

## Utility Extensions

### fileinfo
**Description:** File type detection
**Example:**
```php
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, 'image.jpg');  // image/jpeg
finfo_close($finfo);
```

### filter
**Description:** Data filtering and validation
**Example:**
```php
$email = filter_var('user@example.com', FILTER_VALIDATE_EMAIL);
$clean = filter_var('<script>alert("xss")</script>', FILTER_SANITIZE_STRING);
```

### tokenizer
**Description:** PHP tokenizer
**Example:**
```php
$tokens = token_get_all('<?php echo "Hello"; ?>');
foreach ($tokens as $token) {
    echo token_name($token[0]) . "\n";
}
```

### calendar
**Description:** Calendar conversion functions
**Example:**
```php
$julian = gregoriantojd(12, 25, 2024);
$hebrew = jdtojewish($julian);
```

### ctype
**Description:** Character type checking
**Example:**
```php
ctype_alpha('abc');  // true
ctype_digit('123');  // true
ctype_alnum('abc123');  // true
```

### iconv
**Description:** Character set conversion
**Example:**
```php
$utf8 = iconv('ISO-8859-1', 'UTF-8', $latin1_string);
```

### session
**Description:** Session management
**Example:**
```php
session_start();
$_SESSION['user_id'] = 123;
session_destroy();
```

### phar
**Description:** PHP Archive
**Example:**
```php
$phar = new Phar('app.phar');
$phar->buildFromDirectory('/path/to/app');
$phar->setStub($phar->createDefaultStub('index.php'));
```

---

## Extension Compatibility Matrix

| Extension | PHP 8.3 | PHP 8.4 | PHP 8.5 | Linux | macOS | Windows |
|-----------|---------|---------|---------|-------|-------|---------|
| mongodb | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| ffi | ✅ | ✅ | ✅ | ⚠️ glibc | ✅ | ✅ |
| spx | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| imap | ✅ | ❌ | ❌ | ✅ | ✅ | ✅ |

**Legend:**
- ✅ Fully supported
- ⚠️ Partial support (see notes)
- ❌ Not supported

---

## Performance Notes

### Fast Extensions
- opcache, apcu (caching)
- sodium (modern crypto)
- filter, ctype (validation)

### Moderate Extensions
- curl, redis (network I/O bound)
- pdo, mysqli (database I/O bound)
- json, xml (parsing)

### Slow Extensions
- imagick, gd (image processing)
- soap (XML parsing + network)
- bcmath, gmp (large number calculations)

---

## Next Steps

- [Building Documentation](BUILDING.md)
- [GitHub Actions Workflows](WORKFLOWS.md)
- [Return to README](../README.md)
