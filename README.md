# ITMOSOFT Laravel B2B

B2B account and purchasing-policy primitives for Laravel commerce applications. The package supports polymorphic company/account owners and buyers, account-level order rules, and payment-term metadata.

## Requirements

- PHP 8.3+
- Laravel 12 or 13

## Installation

```bash
composer require itmosoft/laravel-b2b
php artisan vendor:publish --tag=b2b-config
php artisan migrate
```

Laravel package discovery registers the service provider automatically.

## Attach B2B accounts to a company model

```php
use ITMOSOFT\B2B\Concerns\HasB2BAccounts;

class Company extends Model
{
    use HasB2BAccounts;
}
```

Create an account:

```php
$account = $company->b2bAccounts()->create([
    'code' => 'ACME-WHOLESALE',
    'name' => 'Acme Wholesale',
    'status' => 'active',
    'currency' => 'USD',
    'minimum_order_minor' => 10000,
    'maximum_order_minor' => 1000000,
    'requires_purchase_order' => true,
    'requires_quote' => false,
    'payment_terms_days' => 30,
]);
```

Amounts are integer minor units; do not use floats. Currency exponent handling and amount conversion remain application responsibilities.

## Buyer memberships

```php
use ITMOSOFT\B2B\Enums\B2BBuyerRole;

$membership = $account->addBuyer($user, B2BBuyerRole::Approver);
$membership->deactivate();
```

Roles: `owner`, `admin`, `buyer`, `approver`, `viewer`. The helper `canApprove()` checks the role and active membership status. It is not a substitute for application authorization policies.

## Validate order policy

Call this from your checkout/order orchestration before accepting an order:

```php
$account->validateOrder(
    amountMinor: 25000,
    purchaseOrderNumber: 'PO-2026-100',
    quoteAccepted: true,
);
```

It enforces account active status, minimum/maximum order amount, required PO number, and accepted quote requirement. It does not create an order, reserve inventory, enforce buyer spending limits, or verify the quote itself.

## Account lifecycle

```php
$account->suspend();
$account->activate();
$account->close();
```

Closed accounts cannot be reactivated.

## Important boundaries

This package provides B2B account membership and coarse purchasing rules. It does not provide company identity verification, approval chains, per-buyer spend enforcement, budget allocation, contract pricing, quotes, purchase order management, invoicing, tax exemption validation, credit checks, or payment collection. Integrate the corresponding ITMOSOFT packages and enforce authorization in the host application.

`payment_terms_days` is descriptive account configuration only; this package does not extend credit or calculate due dates.

## Testing

```bash
composer install
composer test
```

## License

MIT. See [LICENSE](LICENSE).
