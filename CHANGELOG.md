# Changelog

All notable changes to `laravel-repository-with-service` will be documented in this file.

<!--
## [Unreleased] - YYYY-MM-DD

### Added
-

### Changed
-

### Deprecated
-

### Removed
-

### Fixed
-

### Security
-
-->
## [1.2.0] - 2026-04-16

### Added
- Complete Laravel Boost integration with three AI skills:
  - `repository-generator` skill for repository generation patterns
  - `service-generator` skill for service and business logic patterns
  - `service-binding` skill for dependency injection and container binding patterns
- Added installation, quick start, API, commands, and troubleshooting documentation

### Changed
- Updated model property type in eloquent-repository.stub and clean up service-api.stub

## 1.1.0 - 2026-03-30

### Added

- `make:repository` now supports subdirectory input (e.g., `Admin/User`)
- Files and namespaces are generated under the correct nested path, matching `make:service` behaviour

### Changed

- Updated README with badges, features section, and improved package description

## 1.0.1 - 2026-03-30

### Fixed

- Removed overridden `register()` method in `PackageProvider` that was bypassing Spatie's internal lifecycle, causing `vendor:publish --tag` to return no publishable resources
- Corrected publish tag in README from `service-repository-config` to `repository-with-service-config`
- Fixed typo in README `composer require` command (stray trailing quote)

## 1.0.0 - 2026-03-30

### Added

- Initial release
