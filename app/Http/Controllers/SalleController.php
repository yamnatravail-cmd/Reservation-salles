<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SalleController extends Controller
{
    // عرض جميع القاعات
    public function index()
    {
        $salles = Salle::all();
        return response()->json($salles);
    }

    // إنشاء قاعة جديدة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'services' => 'required|array',
            'services.*.produit_id' => 'required|exists:produits,id',
            'status' => 'required|in:disponible,indisponible',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/salles', 'public');
        }

        // إنشاء القاعة مع الصورة
        $salle = Salle::create([
            'nom' => $validated['nom'],
            'capacite' => $validated['capacite'],
            'prix' => $validated['prix'],
            'status' => $validated['status'],
            'image' => $imagePath,
        ]);

        // ربط الخدمات
        foreach ($validated['services'] as $serviceData) {
    $salle->services()->create([
        'produit_id' => $serviceData['produit_id'],
        // يمكنك إضافة خصائص أخرى هنا إن كانت موجودة في جدول services
    ]);
}


        return response()->json($salle, 201);
    }

 
    public function show(Salle $salle)
    {
        return response()->json($salle);
    }

  
    public function update(Request $request, Salle $salle)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'prix' => 'required|numeric|min:0',
            'status' => 'required|in:disponible,indisponible',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

       
        if ($request->hasFile('image')) {
            if ($salle->image) {
                Storage::disk('public')->delete($salle->image);
            }

            $imagePath = $request->file('image')->store('images/salles', 'public');
            $salle->image = $imagePath;
        }

        $salle->update([
            'nom' => $validated['nom'],
            'capacite' => $validated['capacite'],
            'prix' => $validated['prix'],
            'status' => $validated['status'],
            'image' => $salle->image,
        ]);

        return response()->json($salle);
    }

    
    
         public function destroy(Salle $salle)
{
    // حذف الصورة إن وجدت
    if ($salle->image) {
        Storage::disk('public')->delete($salle->image);
    }

    // حذف الخدمات المرتبطة أولاً
    $salle->services()->delete();

    // حذف القاعة
    $salle->delete();

    return response()->json(null);
}

public function detailsWithServices($id)
{
    // جلب القاعة مع الخدمات المرتبطة بها
    // مع البيانات المتعلقة بالمنتجات لكل خدمة (علاقة بين services و produits)

    $salle = Salle::with(['services.produit'])->find($id);

    if (!$salle) {
        return response()->json(['message' => 'Salle non trouvée'], 404);
    }

    return response()->json($salle);
}
public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:disponible,indisponible',
    ]);

    $salle = \App\Models\Salle::findOrFail($id);
    $salle->status = $request->status;
    $salle->save();

    return response()->json(['status' => $salle->status], 200);
}

}
