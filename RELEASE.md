# Release instructions

## Repository

- Repository: `ITMOSOFT/laravel-b2b`
- Composer package: `itmosoft/laravel-b2b`
- Description: `B2B commerce account management for Laravel applications, with company purchasing policies, buyer memberships, order controls, and payment terms.`

## Validate locally

```bash
composer validate --strict
composer install
composer test
```

Do not tag a release until local PHPUnit tests pass and GitHub Actions is green.

## User-run Git commands

Run these from the existing, already-connected repository. These commands are for you to execute; no repository changes are performed by the assistant.

```bash
git add .
git commit -m "feat: add B2B account and purchasing policies"
git push origin main
```

After tests and CI pass:

```bash
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

## GitHub Release

Title: `v1.0.0 - B2B Commerce`

Description:

```markdown
## Features
- Polymorphic B2B account ownership.
- Buyer memberships with owner, admin, buyer, approver, and viewer roles.
- Account activation, suspension, and closure.
- Minimum and maximum order amount rules.
- Required purchase order number and accepted quote checks.
- Payment terms metadata and account currency.

## Requirements
- PHP 8.3+
- Laravel 12 or 13

## Installation
composer require itmosoft/laravel-b2b
php artisan migrate

## Notes
This package provides account-level purchasing policy primitives. It does not implement approval chains, buyer spending-limit enforcement, credit decisions, quotes, purchase orders, tax exemption, or payment collection.
```

## Packagist

1. Sign in to Packagist.
2. Submit `https://github.com/ITMOSOFT/laravel-b2b`.
3. Confirm the package name is `itmosoft/laravel-b2b`.
4. Configure the GitHub service hook if prompted.
5. Verify Packagist detects the `v1.0.0` tag.
