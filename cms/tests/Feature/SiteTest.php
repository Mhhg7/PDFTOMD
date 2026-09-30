<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\UiString;
use App\Support\ContentBuilder;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SiteContentSeeder::class);
    }

    public function test_home_page_embeds_the_content(): void
    {
        $res = $this->get('/')->assertOk();
        $res->assertSee('window.QS = ', false)->assertSee('window.QS_API', false)->assertSee('site.js', false);
        $qs = ContentBuilder::get();
        $this->assertCount(31, $qs['PAGES']); // 30 pages plus the SIPHAT article
        $this->assertSame(['about', 'services', 'partners', 'products', 'media', 'careers', 'contact'], array_column(array_slice($qs['NAV'], 1), 'id'));
        $this->assertSame('TBC', $qs['PAGES']['partners-siphat']['blocks'][1]['items'][2]['v']);
        $this->assertArrayHasKey('field.company', $qs['UI']['ar']);
    }

    public function test_content_is_safe_inside_the_script_tag(): void
    {
        UiString::query()->where('key', 'cta')->update(['en' => '</script><script>alert(1)</script>']);
        ContentBuilder::forget();
        $this->get('/')->assertOk()->assertDontSee('</script><script>alert(1)', false);
    }

    public function test_inquiry_form_is_stored(): void
    {
        $this->postJson('/api/forms/inquiry', ['name' => 'Dr Test', 'email' => 'dr@example.com', 'subject' => 'Partnership', 'message' => 'Hello', 'lang' => 'ar'])
            ->assertOk()->assertJson(['ok' => true]);
        $s = Submission::query()->sole();
        $this->assertSame('inquiry', $s->kind);
        $this->assertSame('ar', $s->data['lang']);
        $this->assertFalse($s->is_read);
    }

    public function test_form_validation_and_unknown_forms(): void
    {
        $this->postJson('/api/forms/inquiry', ['name' => 'x'])->assertStatus(422)->assertJsonValidationErrors(['email', 'subject', 'message']);
        $this->postJson('/api/forms/secret', [])->assertNotFound();
    }

    public function test_honeypot_drops_bots_silently(): void
    {
        $this->postJson('/api/forms/newsletter', ['email' => 'bot@example.com', 'website_url' => 'http://spam'])->assertOk();
        $this->assertSame(0, Submission::query()->count());
    }

    public function test_job_application_keeps_the_cv_private(): void
    {
        Storage::fake('local');
        $this->post('/api/forms/apply', [
            'name' => 'Applicant', 'email' => 'a@example.com', 'phone' => '+964 700', 'role' => 'Pharmacist',
            'cv' => UploadedFile::fake()->create('my cv.pdf', 120, 'application/pdf'),
        ], ['Accept' => 'application/json'])->assertOk();
        $s = Submission::query()->sole();
        Storage::disk('local')->assertExists($s->attachment);
        $this->assertStringStartsWith('submissions/apply/', $s->attachment);
        $this->assertSame('my cv.pdf', $s->attachment_name);
    }

    public function test_cv_must_be_a_document(): void
    {
        $this->post('/api/forms/apply', [
            'name' => 'A', 'email' => 'a@example.com', 'phone' => '1', 'role' => 'x',
            'cv' => UploadedFile::fake()->create('evil.php', 1, 'application/x-php'),
        ], ['Accept' => 'application/json'])->assertStatus(422)->assertJsonValidationErrors('cv');
    }

    public function test_forms_are_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/forms/newsletter', ['email' => "n{$i}@example.com"])->assertOk();
        }
        $this->postJson('/api/forms/newsletter', ['email' => 'n6@example.com'])->assertStatus(429);
    }

    public function test_static_export_writes_content(): void
    {
        $dir = sys_get_temp_dir().'/qs-export-'.uniqid();
        $this->artisan('site:export', ['dir' => $dir, '--content-only' => true])->assertSuccessful();
        $this->assertStringStartsWith('/* Generated', file_get_contents($dir.'/content.js'));
        @unlink($dir.'/content.js');
        @rmdir($dir);
    }
}
