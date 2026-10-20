<?php echo json_encode(\App\Models\City::with(['hotels'])->get()->toArray(), JSON_PRETTY_PRINT);
