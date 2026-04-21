<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'course');

        $fields = CustomField::where('type', $type)
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $fields,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:course,ebook,bootcamp,team_training',
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'field_type' => 'required|in:text,textarea,select,checkbox,radio,file,date',
            'options' => 'nullable|array',
            'required' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $field = CustomField::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Custom field created successfully',
            'data' => $field,
        ], 201);
    }

    public function show(int $id)
    {
        $field = CustomField::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $field,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $field = CustomField::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'label' => 'string|max:255',
            'field_type' => 'in:text,textarea,select,checkbox,radio,file,date',
            'options' => 'nullable|array',
            'required' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $field->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Custom field updated successfully',
            'data' => $field,
        ]);
    }

    public function destroy(int $id)
    {
        $field = CustomField::findOrFail($id);
        $field->delete();

        return response()->json([
            'success' => true,
            'message' => 'Custom field deleted successfully',
        ]);
    }

    public function values(Request $request, int $id)
    {
        $fieldId = $request->get('field_id');
        $entityId = $request->get('entity_id');

        $query = CustomFieldValue::where('custom_field_id', $id);

        if ($entityId) {
            $query->where('entity_id', $entityId);
        }

        $values = $query->get();

        return response()->json([
            'success' => true,
            'data' => $values,
        ]);
    }

    public function saveValues(Request $request)
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
            'values' => 'required|array',
        ]);

        foreach ($validated['values'] as $fieldId => $value) {
            CustomFieldValue::updateOrCreate(
                [
                    'custom_field_id' => $fieldId,
                    'entity_type' => $validated['entity_type'],
                    'entity_id' => $validated['entity_id'],
                ],
                ['value' => $value]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Custom field values saved successfully',
        ]);
    }
}
