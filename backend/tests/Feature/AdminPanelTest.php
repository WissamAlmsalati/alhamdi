<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);

        $category = Category::create(['name' => 'المكسرات', 'slug' => 'nuts', 'is_active' => true]);
        $brand = Brand::create(['name' => 'تركيا', 'slug' => 'turkey', 'is_active' => true]);
        $this->product = Product::create([
            'name' => 'فستق حلبي',
            'slug' => 'pistachio',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        Order::create(['product_id' => $this->product->id, 'name' => 'زبون', 'email' => 'a@example.com', 'phone' => '0911234567', 'status' => 'new']);
        Contact::create(['name' => 'مرسل', 'email' => 'b@example.com', 'message' => 'مرحباً', 'is_read' => false]);
        Certificate::create(['title' => 'السجل التجاري', 'expires_at' => now()->addDays(10), 'file' => 'certificates/x.pdf']);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('تسجيل الدخول');
    }

    public function test_every_admin_page_renders(): void
    {
        $order = Order::first();
        $contact = Contact::first();
        $certificate = Certificate::first();

        $pages = [
            route('admin.dashboard'),
            route('admin.products.index'),
            route('admin.products.index', ['q' => 'فستق', 'status' => 'active']),
            route('admin.products.create'),
            route('admin.products.edit', $this->product),
            route('admin.categories.index'),
            route('admin.categories.create'),
            route('admin.categories.edit', $this->product->category),
            route('admin.brands.index'),
            route('admin.brands.create'),
            route('admin.brands.edit', $this->product->brand),
            route('admin.orders.index'),
            route('admin.orders.index', ['status' => 'new']),
            route('admin.orders.show', $order),
            route('admin.contacts.index', ['filter' => 'unread']),
            route('admin.contacts.show', $contact),
            route('admin.certificates.index'),
            route('admin.certificates.create'),
            route('admin.certificates.edit', $certificate),
        ];

        foreach ($pages as $url) {
            $this->actingAs($this->admin)->get($url)->assertOk();
        }
    }

    public function test_dashboard_shows_inbox_counts_and_expiring_certificates(): void
    {
        $this->actingAs($this->admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('استفسارات جديدة')
            ->assertSee('السجل التجاري');
    }

    public function test_unchecking_visibility_hides_a_product(): void
    {
        $this->actingAs($this->admin)->put(route('admin.products.update', $this->product), [
            'name' => $this->product->name,
            'name_en' => 'Aleppo pistachio',
            'category_id' => $this->product->category_id,
            'is_active' => '0',
        ])->assertRedirect(route('admin.products.index'));

        $this->assertFalse($this->product->fresh()->is_active);
    }

    public function test_viewing_an_inquiry_marks_it_viewed(): void
    {
        $order = Order::first();

        $this->actingAs($this->admin)->get(route('admin.orders.show', $order))->assertOk();

        $this->assertSame('viewed', $order->fresh()->status);
    }

    public function test_certificate_can_be_created_and_is_listed_in_the_api(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post(route('admin.certificates.store'), [
            'title' => 'رخصة الاستيراد',
            'title_en' => 'Import license',
            'issuer' => 'وزارة الاقتصاد',
            'number' => 'IMP-1',
            'issued_at' => '2025-01-01',
            'expires_at' => '2027-01-01',
            'file' => UploadedFile::fake()->create('license.pdf', 100, 'application/pdf'),
            'is_active' => '1',
        ])->assertRedirect(route('admin.certificates.index'));

        $certificate = Certificate::where('title', 'رخصة الاستيراد')->firstOrFail();
        Storage::disk('public')->assertExists($certificate->file);

        $this->getJson('/api/certificates')
            ->assertOk()
            ->assertJsonFragment(['title' => 'رخصة الاستيراد', 'status' => 'valid', 'file_type' => 'pdf']);
    }

    public function test_certificate_requires_a_file_and_valid_dates(): void
    {
        $this->actingAs($this->admin)->post(route('admin.certificates.store'), [
            'title' => 'ملف',
            'issued_at' => '2026-01-01',
            'expires_at' => '2025-01-01',
        ])->assertSessionHasErrors(['file', 'expires_at']);
    }

    public function test_admin_defaults_to_arabic_right_to_left(): void
    {
        $this->actingAs($this->admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('استفسارات جديدة')
            ->assertSee('English');
    }

    public function test_admin_can_switch_to_english_left_to_right(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.products.index', ['lang' => 'en', 'page' => 2]))
            ->assertRedirect('/admin/products?page=2');

        $this->actingAs($this->admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('dir="ltr"', false)
            ->assertSee('New inquiries')
            ->assertSee('العربية')
            ->assertDontSee('استفسارات جديدة');

        $this->actingAs($this->admin)->get(route('admin.orders.index'))->assertOk()->assertSee('Product inquiries');
    }

    public function test_unsupported_locale_is_rejected(): void
    {
        $this->get(route('login', ['lang' => 'fr']))->assertOk()->assertSee('dir="rtl"', false);
    }

    public function test_guest_can_switch_language_on_login_page(): void
    {
        $this->get(route('login'))->assertSee('href="?lang=en"', false);

        $this->get(route('login', ['lang' => 'en']))->assertRedirect('/login');

        $this->get(route('login'))->assertOk()->assertSee('dir="ltr"', false)->assertSee('href="?lang=ar"', false);
    }

    public function test_validation_errors_are_arabic_by_default(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), ['name' => ''])
            ->assertSessionHasErrors(['name' => 'حقل الاسم مطلوب.']);
    }

    public function test_site_inquiry_requires_phone_and_accepts_no_email(): void
    {
        $this->postJson('/api/orders', ['product_id' => $this->product->id, 'name' => 'زبون'])
            ->assertStatus(422)->assertJsonValidationErrors(['phone']);

        $this->postJson('/api/orders', ['product_id' => $this->product->id, 'name' => 'زبون', 'phone' => '0911111111', 'message' => 'الوزن: 1كغ'])
            ->assertOk();

        $this->postJson('/api/contact', ['name' => 'مرسل', 'phone' => '0922222222', 'message' => 'مرحباً'])
            ->assertOk();

        $contact = Contact::where('phone', '0922222222')->firstOrFail();
        $this->actingAs($this->admin)->get(route('admin.contacts.show', $contact))->assertOk()->assertDontSee('mailto:?', false);
    }

    public function test_categories_api_includes_active_product_counts(): void
    {
        $this->getJson('/api/categories')->assertOk()->assertJsonFragment(['products_count' => 1]);
    }

    public function test_product_gallery_images_can_be_added_removed_and_listed_in_api(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->put(route('admin.products.update', $this->product), [
            'name' => $this->product->name,
            'name_en' => 'Aleppo pistachio',
            'category_id' => $this->product->category_id,
            'is_active' => '1',
            'gallery' => [
                UploadedFile::fake()->image('a.jpg', 400, 400),
                UploadedFile::fake()->image('b.jpg', 400, 400),
            ],
        ])->assertRedirect(route('admin.products.index'));

        $images = $this->product->fresh()->images;
        $this->assertCount(2, $images);
        Storage::disk('public')->assertExists($images[0]->path);

        $this->getJson('/api/products/'.$this->product->id)
            ->assertOk()
            ->assertJsonCount(2, 'images');

        $this->actingAs($this->admin)->get(route('admin.products.edit', $this->product))
            ->assertOk()->assertSee('remove_images[]', false);

        $this->actingAs($this->admin)->put(route('admin.products.update', $this->product), [
            'name' => $this->product->name,
            'name_en' => 'Aleppo pistachio',
            'category_id' => $this->product->category_id,
            'is_active' => '1',
            'remove_images' => [$images[0]->id],
        ]);

        $this->assertCount(1, $this->product->fresh()->images);
        Storage::disk('public')->assertMissing($images[0]->path);
    }

    public function test_gallery_rejects_non_image_files(): void
    {
        $this->actingAs($this->admin)->put(route('admin.products.update', $this->product), [
            'name' => $this->product->name,
            'name_en' => 'Aleppo pistachio',
            'category_id' => $this->product->category_id,
            'gallery' => [UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf')],
        ])->assertSessionHasErrors('gallery.0');
    }

    public function test_large_uploads_are_resized_and_saved_as_jpeg(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->put(route('admin.products.update', $this->product), [
            'name' => $this->product->name,
            'name_en' => 'Aleppo pistachio',
            'category_id' => $this->product->category_id,
            'is_active' => '1',
            'image' => UploadedFile::fake()->image('tall.png', 1242, 2688),
        ])->assertRedirect(route('admin.products.index'));

        $path = $this->product->fresh()->image;
        $this->assertStringEndsWith('.jpg', $path);
        [$width, $height] = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertSame(1600, max($width, $height));
        $this->assertSame(739, $width);
    }

    public function test_english_name_is_required(): void
    {
        $this->actingAs($this->admin)->post(route('admin.categories.store'), ['name' => 'القهوة'])
            ->assertSessionHasErrors('name_en');

        $this->actingAs($this->admin)->post(route('admin.categories.store'), ['name' => 'القهوة', 'name_en' => 'Coffee', 'is_active' => '1'])
            ->assertSessionHasNoErrors();

        $this->actingAs($this->admin)->get(route('admin.products.create'))
            ->assertOk()->assertSee('data-bilingual', false)->assertSee('name="name_en"', false);
    }

    public function test_admin_lists_show_names_in_the_panel_language(): void
    {
        $this->product->update(['name_en' => 'Aleppo pistachio']);
        $this->product->category->update(['name_en' => 'Nuts']);

        $this->actingAs($this->admin)->get(route('admin.products.index'))
            ->assertSee('class="title">فستق حلبي', false);

        $this->actingAs($this->admin)->get(route('admin.products.index', ['lang' => 'en']));

        $this->actingAs($this->admin)->get(route('admin.products.index'))
            ->assertSee('class="title">Aleppo pistachio', false)->assertSee('Nuts');

        // The edit form keeps both languages as editable values.
        $this->actingAs($this->admin)->get(route('admin.products.edit', $this->product))
            ->assertSee('value="فستق حلبي"', false)->assertSee('value="Aleppo pistachio"', false);
    }

    public function test_social_links_are_added_with_detected_platform_and_deleted(): void
    {
        $this->actingAs($this->admin)->post(route('admin.social-links.store'), ['url' => 'instagram.com/mix.ly'])
            ->assertRedirect(route('admin.social-links.index'));
        $this->actingAs($this->admin)->post(route('admin.social-links.store'), ['url' => 'https://wa.me/218912110640']);

        $this->getJson('/api/site')->assertOk()
            ->assertJsonFragment(['platform' => 'instagram', 'url' => 'https://instagram.com/mix.ly'])
            ->assertJsonFragment(['platform' => 'whatsapp']);

        $link = \App\Models\SocialLink::where('platform', 'instagram')->firstOrFail();
        $this->actingAs($this->admin)->get(route('admin.social-links.index'))->assertOk()->assertSee('Instagram');
        $this->actingAs($this->admin)->delete(route('admin.social-links.destroy', $link))->assertRedirect();

        $this->getJson('/api/site')->assertJsonMissing(['platform' => 'instagram'])->assertJsonCount(1, 'social_links');
        $this->actingAs($this->admin)->post(route('admin.social-links.store'), ['url' => 'not a link'])->assertSessionHasErrors('url');
    }

    public function test_site_images_can_be_uploaded_and_removed(): void
    {
        Storage::fake('public');

        $this->getJson('/api/site')->assertOk()->assertJsonPath('images.about_image', null);
        $this->actingAs($this->admin)->get(route('admin.site-content.edit'))->assertOk();

        $this->actingAs($this->admin)->put(route('admin.site-content.update'), [
            'about_image' => UploadedFile::fake()->image('about.jpg', 1200, 900),
            'quick_pistachio' => UploadedFile::fake()->image('p.png', 600, 600),
        ])->assertRedirect(route('admin.site-content.edit'));

        $path = \App\Models\SiteSetting::get('about_image');
        Storage::disk('public')->assertExists($path);
        $this->getJson('/api/site')->assertJsonPath('images.about_image', '/storage/'.$path)
            ->assertJsonPath('images.quick_almond', null);

        $this->actingAs($this->admin)->put(route('admin.site-content.update'), ['remove' => ['about_image']]);
        $this->assertNull(\App\Models\SiteSetting::get('about_image'));
        Storage::disk('public')->assertMissing($path);
        $this->assertNotNull(\App\Models\SiteSetting::get('quick_pistachio'));
    }
}
