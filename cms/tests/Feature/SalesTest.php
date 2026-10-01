<?php

namespace Tests\Feature;

use App\Models\SalesActivity;
use App\Models\SalesCompany;
use App\Models\SalesProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SalesTest extends TestCase
{
    use RefreshDatabase;

    private SalesCompany $company;

    private SalesProduct $product;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->company = SalesCompany::query()->create(['name' => 'Unimed']);
        $this->product = SalesProduct::query()->create([
            'company_id' => $this->company->id, 'brand_name' => 'Tardipen', 'active_ingredient' => 'Benzathine Benzylpenicillin',
            'dose' => '1.2 M.I.U.', 'dosage_form' => 'Vial (I.M.)', 'price' => 12345.67,
        ]);
    }

    private function as(string $role): User
    {
        $u = User::factory()->create(['role' => $role]);
        $this->actingAs($u);

        return $u;
    }

    public function test_guests_cannot_see_anything(): void
    {
        foreach (['/sales', '/sales/sheets', "/sales/sheets/{$this->company->id}", '/sales/file/sales/photos/x.png', '/sales/products/create', '/sales/users'] as $u) {
            $this->get($u)->assertRedirect('/admin/login');
        }
    }

    public function test_viewer_never_receives_prices(): void
    {
        $this->as('viewer');
        foreach (['/sales', '/sales?view=table', '/sales?q=tard', '/sales/sheets', "/sales/sheets/{$this->company->id}?prices=1"] as $u) {
            $this->get($u)->assertOk()->assertSee('Tardipen')->assertDontSee('12,345')->assertDontSee('12345')->assertDontSee('PRICE');
        }
        $this->assertArrayNotHasKey('price', $this->product->toArray());
    }

    public function test_viewer_cannot_change_anything(): void
    {
        $this->as('viewer');
        $this->get('/sales/products/create')->assertForbidden();
        $this->post('/sales/products', ['company_id' => $this->company->id, 'brand_name' => 'X'])->assertForbidden();
        $this->put("/sales/products/{$this->product->id}", ['brand_name' => 'Y'])->assertForbidden();
        $this->delete("/sales/products/{$this->product->id}")->assertForbidden();
        $this->get('/sales/companies')->assertForbidden();
        $this->get('/sales/users')->assertForbidden();
        $this->get('/sales/settings')->assertForbidden();
        $this->get('/admin')->assertForbidden();
        $this->get('/admin/pages')->assertForbidden();
    }

    public function test_sales_role_sees_prices_but_cannot_edit(): void
    {
        $this->as('sales');
        $this->get('/sales')->assertOk()->assertSee('12,345.67 IQD');
        $this->get("/sales/sheets/{$this->company->id}")->assertOk()->assertDontSee('12,345');
        $this->get("/sales/sheets/{$this->company->id}?prices=1")->assertOk()->assertSee('12,345.67 IQD')->assertSee('PRICE');
        $this->assertTrue(SalesActivity::query()->where('action', 'sheet.prices')->exists());
        $this->get('/sales/products/create')->assertForbidden();
    }

    public function test_editor_manages_products_with_private_photos(): void
    {
        $this->as('editor');
        $this->post('/sales/products', [
            'company_id' => $this->company->id, 'brand_name' => 'Nefomed', 'active_ingredient' => 'Nefopam', 'dose' => '20 mg / 2 ml',
            'dosage_form' => 'Ampoule', 'price' => '5000', 'active' => 1, 'photo' => UploadedFile::fake()->image('pack.png', 400, 300),
        ])->assertRedirect()->assertSessionHasNoErrors();
        $p = SalesProduct::query()->where('brand_name', 'Nefomed')->sole();
        $this->assertStringStartsWith('sales/photos/', $p->photo);
        Storage::disk('local')->assertExists($p->photo);
        $this->get('/sales/file/'.$p->photo)->assertOk();
        $this->assertFileDoesNotExist(public_path($p->photo));

        $this->put("/sales/products/{$p->id}", ['company_id' => $this->company->id, 'brand_name' => 'Nefomed', 'price' => '5500', 'active' => 1])->assertSessionHasNoErrors();
        $log = SalesActivity::query()->where('action', 'product.updated')->latest('id')->first();
        $this->assertStringContainsString('5,000 IQD → 5,500 IQD', $log->details);

        $this->post('/sales/products', ['company_id' => $this->company->id, 'brand_name' => 'Bad', 'photo' => UploadedFile::fake()->create('x.svg', 1, 'image/svg+xml')])->assertSessionHasErrors('photo');

        $this->delete("/sales/companies/{$this->company->id}")->assertSessionHas('err');
        $this->delete("/sales/products/{$p->id}")->assertRedirect();
        Storage::disk('local')->assertMissing($p->photo);
        $this->get('/sales/users')->assertForbidden();
    }

    public function test_file_route_rejects_other_paths(): void
    {
        $this->as('viewer');
        Storage::disk('local')->put('submissions/apply/cv.pdf', 'secret');
        $this->get('/sales/file/submissions/apply/cv.pdf')->assertNotFound();
        $this->get('/sales/file/sales/photos/../../submissions/apply/cv.pdf')->assertNotFound();
    }

    public function test_manager_cannot_create_or_touch_admins(): void
    {
        $this->as('manager');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->post('/sales/users', ['name' => 'A', 'email' => 'a@example.com', 'role' => 'admin', 'password' => 'long-enough-1', 'password_confirmation' => 'long-enough-1'])->assertSessionHasErrors('role');
        $this->get("/sales/users/{$admin->id}/edit")->assertForbidden();
        $this->put("/sales/users/{$admin->id}", ['name' => 'A', 'email' => $admin->email, 'role' => 'viewer'])->assertForbidden();
        $this->delete("/sales/users/{$admin->id}")->assertForbidden();
        $this->post('/sales/users', ['name' => 'V', 'email' => 'v@example.com', 'role' => 'viewer', 'password' => 'long-enough-1', 'password_confirmation' => 'long-enough-1'])->assertRedirect('/sales/users');
        $this->get('/admin')->assertForbidden();
    }

    public function test_users_cannot_change_their_own_role(): void
    {
        $me = $this->as('manager');
        $this->put("/sales/users/{$me->id}", ['name' => 'Me', 'email' => $me->email, 'role' => 'admin'])->assertSessionHasNoErrors();
        $this->assertSame('manager', $me->fresh()->role);
        $this->assertTrue($me->fresh()->active);
    }

    public function test_login_lands_by_role_and_switched_off_users_are_refused(): void
    {
        User::factory()->create(['email' => 's@example.com', 'password' => 'long-enough-1', 'role' => 'sales']);
        User::factory()->create(['email' => 'a@example.com', 'password' => 'long-enough-1', 'role' => 'admin']);
        User::factory()->create(['email' => 'off@example.com', 'password' => 'long-enough-1', 'role' => 'editor', 'active' => false]);

        $this->get('/admin');
        $this->post('/admin/login', ['email' => 's@example.com', 'password' => 'long-enough-1'])->assertRedirect('/sales');
        $this->post('/admin/logout');
        $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'long-enough-1'])->assertRedirect('/admin');
        $this->post('/admin/logout');
        $this->post('/admin/login', ['email' => 'off@example.com', 'password' => 'long-enough-1'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_switching_a_user_off_ends_their_session(): void
    {
        $u = $this->as('sales');
        $this->get('/sales')->assertOk();
        $u->forceFill(['active' => false])->save();
        $this->get('/sales')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_sheet_paginates_four_per_page_and_hides_inactive(): void
    {
        $this->as('viewer');
        foreach (range(1, 5) as $i) {
            SalesProduct::query()->create(['company_id' => $this->company->id, 'brand_name' => "Extra {$i}", 'sort' => $i]);
        }
        SalesProduct::query()->create(['company_id' => $this->company->id, 'brand_name' => 'Hidden one', 'active' => false]);
        $html = $this->get("/sales/sheets/{$this->company->id}")->assertOk()->assertDontSee('Hidden one')->getContent();
        $this->assertSame(2, substr_count($html, 'class="sheet"'));
    }

    public function test_every_sales_screen_opens_for_a_manager(): void
    {
        $this->as('manager');
        $other = User::factory()->create(['role' => 'viewer']);
        foreach (['/sales', "/sales?company={$this->company->id}&view=table", '/sales/products/create', "/sales/products/{$this->product->id}/edit",
            '/sales/companies', '/sales/companies/create', "/sales/companies/{$this->company->id}/edit", '/sales/users', '/sales/users/create',
            "/sales/users/{$other->id}/edit", '/sales/settings', '/sales/activity', '/sales/sheets', '/sales/sheets/all?prices=1'] as $u) {
            $this->get($u)->assertOk();
        }
        $this->post('/sales/settings', ['website' => 'alqawsangroup.com', 'phone' => '+964 1', 'qr_url' => 'javascript:alert(1)', 'currency' => 'USD'])->assertSessionHasErrors('qr_url');
        $this->post('/sales/settings', ['website' => 'alqawsangroup.com', 'phone' => '+964 1', 'qr_url' => 'https://alqawsangroup.com', 'currency' => 'USD'])->assertSessionHasNoErrors();
        $this->get('/sales')->assertSee('12,345.67 USD');
    }
}
