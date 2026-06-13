<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttributeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Attributes/Index', [
            'attributes' => fn () => Attribute::with(['values' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn ($a) => [
                    'id'           => $a->id,
                    'name'         => $a->name,
                    'slug'         => $a->slug,
                    'type'         => $a->type,
                    'is_filterable' => $a->is_filterable,
                    'sort_order'   => $a->sort_order,
                    'values'       => $a->values->map(fn ($v) => [
                        'id'         => $v->id,
                        'value'      => $v->value,
                        'label'      => $v->label,
                        'color_code' => $v->color_code,
                        'sort_order' => $v->sort_order,
                    ]),
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:attributes,name'],
            'type'          => ['nullable', 'string', 'in:select,color,text'],
            'is_filterable' => ['boolean'],
            'sort_order'    => ['nullable', 'integer'],
        ]);

        Attribute::create($data);

        return back()->with('success', 'Attribute created.');
    }

    public function update(Request $request, Attribute $attribute): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:attributes,name,' . $attribute->id],
            'type'          => ['nullable', 'string', 'in:select,color,text'],
            'is_filterable' => ['boolean'],
            'sort_order'    => ['nullable', 'integer'],
        ]);

        $attribute->update($data);

        return back()->with('success', 'Attribute updated.');
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        $attribute->values()->delete();
        $attribute->delete();

        return back()->with('success', 'Attribute deleted.');
    }

    public function storeValue(Request $request, Attribute $attribute): RedirectResponse
    {
        $data = $request->validate([
            'value'      => ['required', 'string', 'max:100'],
            'label'      => ['nullable', 'string', 'max:100'],
            'color_code' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $attribute->values()->create($data);

        return back()->with('success', 'Value added.');
    }

    public function updateValue(Request $request, Attribute $attribute, AttributeValue $value): RedirectResponse
    {
        $data = $request->validate([
            'value'      => ['required', 'string', 'max:100'],
            'label'      => ['nullable', 'string', 'max:100'],
            'color_code' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $value->update($data);

        return back()->with('success', 'Value updated.');
    }

    public function destroyValue(Attribute $attribute, AttributeValue $value): RedirectResponse
    {
        $value->delete();

        return back()->with('success', 'Value deleted.');
    }
}
