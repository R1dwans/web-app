<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Article;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class MenuItemController extends Controller
{
    /**
     * Resolve URL from linkable model.
     */
    private function resolveUrl(string $type, int $id): string
    {
        return match ($type) {
            'article' => route('public.articles.show', Article::findOrFail($id)->slug),
            'page'    => route('public.pages.show', Page::findOrFail($id)->slug),
            default   => '',
        };
    }

    /**
     * Map linkable_type string to fully qualified class name.
     */
    private function resolveClass(string $type): string
    {
        return match ($type) {
            'article' => Article::class,
            'page'    => Page::class,
            default   => '',
        };
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'menu_id'       => 'required|exists:menus,id',
            'title'         => 'required|string|max:255',
            'url'           => 'nullable|string|max:255',
            'parent_id'     => 'nullable|exists:menu_items,id',
            'order'         => 'integer',
            'linkable_type' => 'nullable|in:article,page,custom',
            'linkable_id'   => 'nullable|integer',
        ]);

        $data = $request->only(['menu_id', 'parent_id', 'title', 'url', 'order', 'target', 'icon']);

        $linkableType = $request->input('linkable_type');
        if ($linkableType && $linkableType !== 'custom') {
            $linkableId = $request->input('linkable_id');
            $data['linkable_type'] = $this->resolveClass($linkableType);
            $data['linkable_id']   = $linkableId;
            $data['url']           = $this->resolveUrl($linkableType, $linkableId);
        } else {
            $data['linkable_type'] = null;
            $data['linkable_id']   = null;
        }

        MenuItem::create($data);

        return Redirect::back()->with('success', 'Menu item added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuItem $menuItem)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'url'           => 'nullable|string|max:255',
            'order'         => 'integer',
            'linkable_type' => 'nullable|in:article,page,custom',
            'linkable_id'   => 'nullable|integer',
        ]);

        $data = $request->only(['title', 'url', 'order', 'target', 'icon']);

        $linkableType = $request->input('linkable_type');
        if ($linkableType && $linkableType !== 'custom') {
            $linkableId = $request->input('linkable_id');
            $data['linkable_type'] = $this->resolveClass($linkableType);
            $data['linkable_id']   = $linkableId;
            $data['url']           = $this->resolveUrl($linkableType, $linkableId);
        } else {
            $data['linkable_type'] = null;
            $data['linkable_id']   = null;
        }

        $menuItem->update($data);

        return Redirect::back()->with('success', 'Menu item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return Redirect::back()->with('success', 'Menu item deleted successfully.');
    }

    /**
     * Reorder menu items via drag-and-drop (supports nesting).
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.order' => 'required|integer',
            'items.*.parent_id' => 'nullable|integer',
        ]);

        foreach ($request->input('items') as $item) {
            MenuItem::where('id', $item['id'])->update([
                'order' => $item['order'],
                'parent_id' => $item['parent_id'] ?? null,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
