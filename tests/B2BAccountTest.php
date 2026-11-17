<?php

namespace ITMOSOFT\B2B\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ITMOSOFT\B2B\Enums\B2BAccountStatus;
use ITMOSOFT\B2B\Enums\B2BBuyerRole;
use ITMOSOFT\B2B\Exceptions\InvalidB2BOperation;
use ITMOSOFT\B2B\Models\B2BAccount;
use ITMOSOFT\B2B\Tests\Fixtures\Company;
use ITMOSOFT\B2B\Tests\Fixtures\User;

class B2BAccountTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach (['test_companies', 'test_users'] as $table) {
            Schema::create($table, function (Blueprint $blueprint) {
                $blueprint->id();
                $blueprint->string('name');
            });
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    private function makeAccount(): array
    {
        $company = Company::query()->create(['name' => 'Acme']);
        $account = $company->b2bAccounts()->create([
            'code' => 'ACME-'.uniqid(),
            'name' => 'Acme Wholesale',
            'status' => 'active',
            'currency' => 'USD',
            'minimum_order_minor' => 5000,
            'maximum_order_minor' => 500000,
            'requires_purchase_order' => true,
            'payment_terms_days' => 30,
        ]);

        return [$account, $company];
    }

    public function test_company_can_create_b2b_account(): void
    {
        [$account, $company] = $this->makeAccount();

        $this->assertSame(B2BAccountStatus::Active, $account->status);
        $this->assertSame($company->id, $account->accountable->id);
        $this->assertSame(30, $account->payment_terms_days);
    }

    public function test_account_can_add_buyer_with_role(): void
    {
        [$account] = $this->makeAccount();
        $user = User::query()->create(['name' => 'Buyer']);
        $membership = $account->addBuyer($user, B2BBuyerRole::Approver);

        $this->assertSame(B2BBuyerRole::Approver, $membership->role);
        $this->assertTrue($membership->canApprove());
        $this->assertSame($user->id, $membership->buyer->id);
    }

    public function test_adding_same_buyer_updates_existing_membership(): void
    {
        [$account] = $this->makeAccount();
        $user = User::query()->create(['name' => 'Buyer']);

        $first = $account->addBuyer($user, B2BBuyerRole::Buyer);
        $second = $account->addBuyer($user, B2BBuyerRole::Admin);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(B2BBuyerRole::Admin, $second->role);
        $this->assertCount(1, $account->buyers);
    }

    public function test_order_policy_accepts_valid_order(): void
    {
        [$account] = $this->makeAccount();
        $account->validateOrder(25000, 'PO-100', true);

        $this->assertTrue(true);
    }

    public function test_order_policy_requires_purchase_order_number(): void
    {
        [$account] = $this->makeAccount();

        $this->expectException(InvalidB2BOperation::class);
        $account->validateOrder(25000);
    }

    public function test_order_policy_enforces_minimum_and_maximum(): void
    {
        [$account] = $this->makeAccount();

        try {
            $account->validateOrder(1000, 'PO-101');
            $this->fail('Expected minimum order validation failure.');
        } catch (InvalidB2BOperation $exception) {
            $this->assertStringContainsString('minimum', $exception->getMessage());
        }

        $this->expectException(InvalidB2BOperation::class);
        $account->validateOrder(600000, 'PO-102');
    }

    public function test_quote_requirement_is_enforced(): void
    {
        [$account] = $this->makeAccount();
        $account->requires_quote = true;
        $account->save();

        $this->expectException(InvalidB2BOperation::class);
        $account->validateOrder(25000, 'PO-103', false);
    }

    public function test_suspended_account_cannot_place_order(): void
    {
        [$account] = $this->makeAccount();
        $account->suspend();

        $this->expectException(InvalidB2BOperation::class);
        $account->validateOrder(25000, 'PO-104');
    }

    public function test_closed_account_cannot_be_reactivated(): void
    {
        [$account] = $this->makeAccount();
        $account->close();

        $this->expectException(InvalidB2BOperation::class);
        $account->activate();
    }
}
