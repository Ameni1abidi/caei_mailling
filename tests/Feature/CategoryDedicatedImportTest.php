<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ImportLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryDedicatedImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');

        $this->category = Category::create([
            'name'        => 'Sénégal : Administration',
            'description' => 'Liste test pour import dédié',
            'couleur'     => '#6366f1',
            'icone'       => 'list',
        ]);
    }

    public function test_category_show_page_displays_import_buttons(): void
    {
        $response = $this->actingAs($this->user)->get(route('categories.show', $this->category));

        $response->assertStatus(200);
        $response->assertSee(route('categories.import', $this->category));
        $response->assertSee('Importer des contacts');
    }

    public function test_category_import_initiates_session_and_redirects_to_upload(): void
    {
        $response = $this->actingAs($this->user)->get(route('categories.import', $this->category));

        $response->assertRedirect(route('contacts.import.upload'));
        $response->assertSessionHas('import_target_category_id', $this->category->id);
    }

    public function test_upload_with_category_session_preassigns_category_id_to_import_log(): void
    {
        Storage::fake('public');

        $csvContent = "Email,Nom,Prenom\njohn@example.com,Doe,John\njane@example.com,Smith,Jane\n";
        $file = UploadedFile::fake()->createWithContent('contacts.csv', $csvContent);

        $response = $this->actingAs($this->user)
            ->withSession(['import_target_category_id' => $this->category->id])
            ->post(route('contacts.import.handle-upload'), [
                'file' => $file,
            ]);

        $importLog = ImportLog::latest()->first();
        $this->assertNotNull($importLog);
        $this->assertEquals([$this->category->id], $importLog->category_ids);
        $response->assertRedirect(route('contacts.import.mapping', $importLog->id));
    }

    public function test_step2_mapping_view_locks_target_category(): void
    {
        $importLog = ImportLog::create([
            'filename'     => 'contacts.csv',
            'temp_path'    => 'imports/fake.csv',
            'status'       => 'pending',
            'user_id'      => $this->user->id,
            'imported'     => 0,
            'duplicates'   => 0,
            'errors'       => 0,
            'total_rows'   => 2,
            'category_ids' => [$this->category->id],
        ]);

        $analysis = [
            'headers'        => ['Email', 'Nom', 'Prenom'],
            'total_estimate' => 2,
            'preview'        => [
                ['Email' => 'john@example.com', 'Nom' => 'Doe', 'Prenom' => 'John'],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->withSession([
                'import_target_category_id' => $this->category->id,
                "import_{$importLog->id}_analysis" => $analysis,
            ])
            ->get(route('contacts.import.mapping', $importLog->id));

        $response->assertStatus(200);
        $response->assertSee($this->category->name);
        $response->assertSee('Assigné automatiquement — Import dédié');
    }
}
