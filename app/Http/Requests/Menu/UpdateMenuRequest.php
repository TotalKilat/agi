<?php

namespace App\Http\Requests\Menu;

use App\Models\Menu;
use Illuminate\Validation\Validator;

class UpdateMenuRequest extends StoreMenuRequest
{
    public function after(): array
    {
        return [
            ...parent::after(),
            function (Validator $validator): void {
                /** @var Menu|null $menu */
                $menu = $this->route('menu');

                if ($menu && $this->parent_id === $menu->getKey()) {
                    $validator->errors()->add('parent_id', 'A menu cannot be its own parent.');
                }

                if ($menu && $this->parent_id && $menu->children()->exists()) {
                    $validator->errors()->add('parent_id', 'A menu with children must remain a top-level menu.');
                }
            },
        ];
    }
}
