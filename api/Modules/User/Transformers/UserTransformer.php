<?php namespace Modules\User\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\User\Entities\Sentinel\User;

class UserTransformer extends TransformerAbstract  implements UserTransformerInterface
{
  /**
   * Turn this item object into a generic array.
   *
   * @param $item
   * @return array
   */
  public function transform(User $item)
  {
      return [
          'id' => (int)$item->id,
          'created_at' => (string)$item->created_at,
          'updated_at' => (string)$item->updated_at,
      ];
  }
}
