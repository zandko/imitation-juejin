<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Illuminate\Notifications\DatabaseNotification;

class NotificationTransformer extends TransformerAbstract
{
    /**
     * @param DatabaseNotification $databaseNotification
     * @return array
     */
    public function transform(DatabaseNotification $databaseNotification)
    {
        return [
            'id' => $databaseNotification->id,
            'type' => $databaseNotification->type,
            'data' => $databaseNotification->data,
            'read_at' => $databaseNotification->read_at,
            'created_at' => $databaseNotification->created_at->toDateTimeString(),
            'updated_at' => $databaseNotification->updated_at->toDateTimeString(),
        ];
    }
}