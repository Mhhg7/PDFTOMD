<?php

namespace Tests\Feature;

use App\Admin\Resources;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Stat;
use App\Models\Submission;
use App\Models\UiString;
use App\Models\User;
use App\Support\ContentBuilder;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SiteContentSeeder::class);
        $this->user = User::factory()->create(['email' => 'admin@example.com', 'password' => 'correct-horse-9']);
    }

    public function test_guests_are_sent_to_the_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/pages')->assertRedirect('/admin/login');
        $this->post('/admin/text', [])->assertRedirect('/admin/login');
    }

    public function test_login_and_logout(): void
    {
        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'correct-horse-9'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->user);
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_every_screen_opens(): void
    {
        $this->actingAs($this->user);
        $page = Page::query()->first();
        $urls = ['/admin', '/admin/pages', '/admin/pages/create', "/admin/pages/{$page->id}/edit", '/admin/text', '/admin/text?q=phone',
            '/admin/settings', '/admin/media', '/admin/submissions', '/admin/users', '/admin/users/create'];
        foreach (array_keys(Resources::all()) as $r) {
            $urls[] = "/admin/r/{$r}";
            $first = Resources::get($r)['model']::query()->first();
            if ($first) {
                $urls[] = "/admin/r/{$r}/{$first->getKey()}";
            }
        }
        foreach ($urls as $u) {
            $this->get($u)->assertOk();
        }
    }

    public function test_page_edit_is_cleaned_and_shown_on_the_site(): void
    {
        $this->actingAs($this->user);
        $page = Page::query()->where('slug', 'about-who')->first();
        $blocks = [
            ['type' => 'text', 'h' => ['en' => 'Hi', 'ar' => 'مرحبا'], 'p' => [['en' => 'Keep <b>this</b> <script>x()</script><img src=x onerror=y>', 'ar' => '<bdi dir="ltr" onclick="z">2009</bdi>']]],
            ['type' => 'stats', 'junk' => 'dropped'],
            ['type' => 'evil'],
            ['type' => 'image', 'src' => '../../.env', 'cap' => ['en' => 'x', 'ar' => '']],
        ];
        $this->put("/admin/pages/{$page->id}", [
            'title' => ['en' => 'Who We Are Now', 'ar' => 'من نحن'], 'lead' => ['en' => 'Lead', 'ar' => ''],
            'section' => 'about', 'sort' => 0, 'blocks' => json_encode($blocks),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $page->refresh();
        $this->assertSame('Who We Are Now', $page->title['en']);
        $this->assertCount(3, $page->blocks);
        $this->assertSame('Keep <b>this</b> x()', $page->blocks[0]['p'][0]['en']);
        $this->assertSame('<bdi dir="ltr">2009</bdi>', $page->blocks[0]['p'][0]['ar']);
        $this->assertSame(['type' => 'stats'], $page->blocks[1]);
        $this->assertArrayNotHasKey('src', $page->blocks[2]);
        $this->assertSame('Who We Are Now', ContentBuilder::get()['PAGES']['about-who']['t']['en']);
    }

    public function test_system_pages_cannot_be_deleted_but_new_ones_can(): void
    {
        $this->actingAs($this->user);
        $sys = Page::query()->where('slug', 'contact-inquiry')->first();
        $this->delete("/admin/pages/{$sys->id}")->assertForbidden();

        $this->post('/admin/pages', ['slug' => 'services-training', 'title' => ['en' => 'Training'], 'section' => 'services', 'blocks' => '[]'])->assertRedirect();
        $new = Page::query()->where('slug', 'services-training')->sole();
        $this->assertContains('services-training', collect(ContentBuilder::get()['NAV'])->firstWhere('id', 'services')['kids']);
        $this->delete("/admin/pages/{$new->id}")->assertRedirect('/admin/pages');
        $this->post('/admin/pages', ['slug' => 'about-who', 'title' => ['en' => 'Dup'], 'blocks' => '[]'])->assertSessionHasErrors('slug');
    }

    public function test_resource_crud(): void
    {
        $this->actingAs($this->user);
        $this->post('/admin/r/stats', ['value' => '30+', 'label' => ['en' => 'Warehouses', 'ar' => 'مستودعات'], 'icon' => 'warehouse', 'sort' => 9, 'active' => 1])
            ->assertRedirect()->assertSessionHasNoErrors();
        $stat = Stat::query()->where('value', '30+')->sole();
        $this->assertContains('30+', array_column(ContentBuilder::get()['STATS'], 'v'));

        $this->put("/admin/r/stats/{$stat->id}", ['value' => '31', 'label' => ['en' => 'Warehouses'], 'icon' => 'not-an-icon'])->assertSessionHasErrors('icon');
        $this->put("/admin/r/stats/{$stat->id}", ['value' => '31', 'label' => ['en' => 'Warehouses'], 'icon' => 'truck', 'active' => 0])->assertSessionHasNoErrors();
        $this->assertNotContains('31', array_column(ContentBuilder::get()['STATS'], 'v'), 'inactive items are hidden');

        $this->delete("/admin/r/stats/{$stat->id}")->assertRedirect('/admin/r/stats');
        $this->assertNull(Stat::query()->find($stat->id));
        $this->get('/admin/r/nope')->assertNotFound();
        $this->delete('/admin/r/forms/1')->assertForbidden();
    }

    public function test_photo_paths_must_point_at_uploads(): void
    {
        $this->actingAs($this->user);
        $this->post('/admin/r/gallery', ['image' => '../../.env', 'caption' => ['en' => 'x']])->assertSessionHasErrors('image');
        $this->post('/admin/r/gallery', ['image' => 'uploads/2026/09/a.jpg', 'caption' => ['en' => 'x']])->assertSessionHasNoErrors();
    }

    public function test_interface_text_and_settings(): void
    {
        $this->actingAs($this->user);
        $cta = UiString::query()->where('key', 'cta')->first();
        $this->post('/admin/text', ['text' => [$cta->id => ['en' => 'Partner "with" us', 'ar' => 'كن شريكاً']]])->assertRedirect();
        $this->assertSame('Partner &quot;with&quot; us', ContentBuilder::get()['UI']['en']['cta']);

        $this->post('/admin/settings', ['phone' => '+964 770 000 0000', 'email' => 'info@alqawsangroup.com', 'maps_url' => 'javascript:alert(1)'])->assertSessionHasErrors('maps_url');
        $this->post('/admin/settings', ['phone' => '+964 770 000 0000', 'email' => 'info@alqawsangroup.com', 'footer_links' => ['about-who', 'terms']])->assertSessionHasNoErrors();
        $this->assertSame('+964 770 000 0000', Setting::get('phone'));
        $this->assertSame(['about-who', 'terms'], ContentBuilder::get()['SETTINGS']['footerLinks']);
    }

    public function test_inbox_export_escapes_formulas(): void
    {
        $this->actingAs($this->user);
        $s = Submission::query()->create(['kind' => 'inquiry', 'data' => ['name' => '=HYPERLINK("x")', 'email' => 'a@example.com', 'subject' => 'S', 'message' => 'M']]);
        $this->get("/admin/submissions/{$s->id}")->assertOk();
        $this->assertTrue($s->fresh()->is_read);
        $csv = $this->get('/admin/submissions/export?kind=inquiry')->assertOk()->streamedContent();
        $this->assertStringContainsString("'=HYPERLINK", $csv);
    }

    public function test_users_cannot_delete_themselves(): void
    {
        $this->actingAs($this->user);
        $this->delete("/admin/users/{$this->user->id}")->assertSessionHas('err');
        $this->post('/admin/users', ['name' => 'Editor', 'email' => 'ed@example.com', 'password' => 'short', 'password_confirmation' => 'short'])->assertSessionHasErrors('password');
        $this->post('/admin/users', ['name' => 'Editor', 'email' => 'ed@example.com', 'password' => 'long-enough-1', 'password_confirmation' => 'long-enough-1'])->assertRedirect('/admin/users');
    }
}
