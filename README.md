# Image caching microservice

[![Build Status](https://travis-ci.org/strider2038/image-cache.svg?branch=master)](https://travis-ci.org/strider2038/image-cache) [![Coverage Status](https://coveralls.io/repos/github/strider2038/image-cache/badge.svg?branch=master)](https://coveralls.io/github/strider2038/image-cache?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/strider2038/image-cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/strider2038/image-cache/?branch=master)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/cfe1201a-7dab-4eeb-9b00-f0edd63a1690/big.png)](https://insight.sensiolabs.com/projects/cfe1201a-7dab-4eeb-9b00-f0edd63a1690)

Microservice for caching images from external storages (WebDAV or API). It can process images by applying simple transformations such as resizing, cropping and shifting.

## Goals for version v1.0

- [x] Migrate to Symfony Dependency Injection component
- [x] File operations service
- [x] Refactor render methods in images (eliminate side effect in classes)
- [x] Refactor controllers and routing to use different image caches
- [x] Basic logging
- [x] Transformation classes refactoring (changing names and using flyweight pattern)
- [x] Image saving with options
- [x] Migrate to PSR-7 request and response interfaces (PSR like realisation)
- [x] Image writing to sources
- [x] Move processing configuration parsing into key parser
- [x] Functional testing
- [x] Yandex Static Map source
- [x] Tune console and IDE debug via docker, not ssh
- [x] Split ImageController to action classes
- [x] Split ImageCache class to ImageStorage and ImageCache
- [x] Router rewrites Request URI, eliminate location in controllers
- [x] Logging guzzle requests
- [x] Add CodeSniffer (rejected) and SensioLabs Insight to CI
- [x] Yandex.Disk source
- [x] Migrate to collections based on Doctrine ArrayCollection
- [x] Migrate to validation based on Symfony and Doctrine annotations
- [x] Shift transformation
- [x] Rotate transformation
- [x] Flip and flop transformations
- [x] Multipurpose interface to request geographical map images
- [x] Make user-friendly configuration of microservice
- [x] Move all todos to github issues
- [x] Automatically create cache directories
- [x] Support for building docker containers in Travis
- [x] Acceptance testing
- [ ] Write project description in README.md

## Goals for version v1.1
- [ ] Performance optimization: configuration caching and/or compiling
- [ ] Performance optimization: composer cache 
- [ ] Performance optimization: compiling to PHAR archive
- [ ] Performance optimization: lazy services, validation cache
- [ ] Refactor ImageParameters to be ignored while saving
- [ ] Cache mechanism for source accessor
- [ ] Add log level to configuration
- [ ] Support for phpDocumentor
- [ ] JPEG optimization by https://github.com/tjko/jpegoptim
- [ ] Refactor HttpClientInterface to use object-based options
- [ ] Migrate from supervisor to systemd daemons
- [ ] Migrate to PHPUnit 7 when Phake will be updated

## Ideas
- Google map source (inspect licensing problems)
- Yandex map marker
- Layer support for GIF and PNG http://php.net/manual/ru/imagick.coalesceimages.php
- Add referrer control to Security component
- rename ImageExtractor and ImageWriter?
- rename ImageExtractor::extractImage() and ImageWriter::insertImage()?
- rename project to Image Caching Proxy Service?
