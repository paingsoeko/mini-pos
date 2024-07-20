<?php

namespace Database\Seeders;

use App\Enums\UnitType;
use App\Models\Contact;
use App\Models\Feature;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Helper\ProgressBar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::raw('SET time_zone=\'+00:00\'');
        $features = ['user', 'role', 'product'];
        $permissions = ['viewAny', 'view', 'create', 'update', 'delete', 'import', 'export', 'print'];

        $role = Role::create([
            'name' => 'SystemAdmin'
        ]);

        foreach ($features as $f){
            $feature = Feature::create(['name' => $f]);

            foreach ($permissions as $p){
                $permission = Permission::create([
                    'name' => $p,
                    'feature_id' => $feature->id,
                ]);

                RolePermission::create([
                    'role_id' => $role->id,
                    'permission_id'=> $permission->id,
                ]);
            }
        }



        // Admin Account
        $this->command->warn(PHP_EOL . 'Creating admin user...');
        $user = $this->withProgressBar(1, fn () => User::factory(1)->create([
            'name' => 'Business Owner',
            'email' => 'admin@app.com',
            'role_id' => $role->id,
            'role' => 'ADMIN',
        ]));
        $this->command->info('Admin user created.');


        // Customer Contact
        $this->command->warn(PHP_EOL . 'Creating customer contact...');
        $customer = $this->withProgressBar(1, fn () => Contact::factory(1)->create([
            'name' => 'Walk-In Customer',
            'email' => 'default@app.com',
            'phone' => '00000000000',
            'address' => '',
            'type' => 'customer',
        ]));
        $this->command->info('Admin user created.');


//        UnitCategory::create([
//            'name' => 'Units',
//            'created_by' => 1,
//        ]);
        // UnitCategory and Units
//        $this->command->warn(PHP_EOL . 'Creating Unit Category...');
//        $unitCategory = $this->withProgressBar(1, fn () => UnitCategory::create([
//
//        ]));
//        $this->command->info('Unit Category created.');
//
//        $this->command->warn(PHP_EOL . 'Creating Unit...');
//
//        $units = [
//            [
//                'name' => 'Pieces',
//                'short_name' => 'PCs',
//                'unit_category_id' => 1,
//                'unit_type' => UnitType::Reference,
//                'value' => 1,
//                'rounded_amount' => 0.0000,
//                'created_by' => 1,
//            ],
//            [
//                'name' => 'Dozen',
//                'short_name' => 'DZ',
//                'unit_category_id' => 1,
//                'unit_type' => UnitType::Bigger,
//                'value' => 12,
//                'rounded_amount' => 0.0000,
//                'created_by' => 1,
//            ],
//        ];
////
////        $this->withProgressBar($units, function ($unit) {
////            Unit::create($unit);
////        });
//        $this->withProgressBar($units, function ($unit) {
//            Unit::create($unit);
//        });
////        $unitCategory = $this->withProgressBar($units, fn ($units) => UnitCategory::create($units));
//        $this->command->info('Unit created.');
//





    }

    protected function withProgressBar(int $amount, Closure $createCollectionOfOne): Collection
    {
        $progressBar = new ProgressBar($this->command->getOutput(), $amount);

        $progressBar->start();

        $items = new Collection();

        foreach (range(1, $amount) as $i) {
            $items = $items->merge(
                $createCollectionOfOne()
            );
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->command->getOutput()->writeln('');

        return $items;
    }
}
