<?php

use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});


Route::get('/abc', function (){
// Build your query
    $query = \App\Models\StockBalance::query();

return $query->select(['transaction_id','product_id', 'batch_no', 'unit',
    'sale_price', 'created_by', 'updated_by'])
    ->selectRaw('SUM(current_quantity) as total_current_quantity', )
    ->selectRaw('SUM(purchase_quantity) as total_purchase_quantity')
//    ->groupBy('product_id', 'unit')
    ->get();
});

Route::get('/ttt', function (\App\Models\User $user){

    $user = Auth::user();
//    $aa = RolePermission::where('role_id', $user->role_id)
//        ->with('permission.feature')
//        ->get()
//        ->pluck('permission.feature.name', 'permission.name')
//        ->mapWithKeys(function ($feature, $permission) {
//            return ["{$feature}-{$permission}" => true];
//        });

      $aa =   $user->hasPermissionByFeatureAndName('product', 'viewAny');
//    $featureId = \App\Models\Feature::where('name', 'product')->first()->id;
//    $permissionId = \App\Models\Permission::where('feature_id', $featureId)->where('name', 'viewAny')->first()->id;
//
//    $aa =  RolePermission::where('role_id', $user->role_id)->pluck('permission_id');
//    $aa->contains($permissionId)
    if ($aa) {
        return 'has permissions';
    } else {
        return 'no permissions';
    }


});
