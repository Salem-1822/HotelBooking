<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RoomController extends AdminRoomController
{
    public function index(Request $request)
    {
        $rooms = $this->roomQuery()->with('hotel')->latest()->get();
        $floors = $rooms->pluck('floor')->filter()->unique()->sort()->values();

        $totalRooms = $rooms->count();
        $availableRooms = $rooms->where('status', 'available')->count();
        $occupiedRooms = $rooms->where('status', 'occupied')->count();
        $reservedRooms = $rooms->where('status', 'reserved')->count();
        $maintenanceRooms = $rooms->where('status', 'maintenance')->count();
        $inactiveRooms = $rooms->where('status', 'inactive')->count();

        $roomsForJs = $rooms->map(function (Room $room) {
            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'name' => $room->name,
                'type' => $room->type,
                'capacity' => $room->capacity,
                'price_per_night' => number_format($room->price_per_night, 2),
                'bed_type' => $room->bed_type,
                'floor' => $room->floor,
                'size' => $room->size,
                'description' => $room->description,
                'status' => $room->status,
                'hotel_id' => $room->hotel_id,
                'hotel_name' => $room->hotel?->name,
                'main_image_url' => $room->main_image ? asset('storage/' . $room->main_image) : null,
                'gallery_images' => collect($room->gallery_images ?? [])->map(fn ($path) => asset('storage/' . $path))->all(),
                'updated_at' => $room->updated_at?->format('Y-m-d H:i'),
            ];
        });

        return view('rooms.index', [
            'layout' => $this->getLayout(),
            'routePrefix' => $this->getRoutePrefix(),
            'rooms' => $rooms,
            'roomsForJs' => $roomsForJs,
            'statusOptions' => $this->statusOptions,
            'roomTypes' => $this->roomTypes,
            'bedTypes' => $this->bedTypes,
            'floors' => $floors,
            'hotels' => Hotel::all(),
            'totalRooms' => $totalRooms,
            'availableRooms' => $availableRooms,
            'occupiedRooms' => $occupiedRooms,
            'reservedRooms' => $reservedRooms,
            'maintenanceRooms' => $maintenanceRooms,
            'inactiveRooms' => $inactiveRooms,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateRoom($request);
        $this->storeImages($request, $data);
        $room = Room::create($data);

        return $this->jsonOrRedirect($request, 'Room created successfully.', $room);
    }

    public function update(Request $request, Room $room)
    {
        $data = $this->validateRoom($request, null, $room->id);
        $this->storeImages($request, $data, $room);
        $room->update($data);

        return $this->jsonOrRedirect($request, 'Room updated successfully.', $room);
    }

    protected function roomQuery()
    {
        return Room::query();
    }

    protected function validateRoom(Request $request, ?int $hotelId = null, ?int $roomId = null): array
    {
        $rules = [
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'room_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rooms')->where(function ($query) use ($request) {
                    return $query->where('hotel_id', $request->hotel_id);
                })->ignore($roomId),
            ],
            'name' => 'required|string|max:255',
            'type' => ['required', 'string', Rule::in($this->roomTypes)],
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'bed_type' => ['required', 'string', Rule::in($this->bedTypes)],
            'floor' => 'required|integer|min:0',
            'size' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1500',
            'main_image' => $roomId ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096' : 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'status' => ['required', Rule::in(array_keys($this->statusOptions))],
        ];

        return $request->validate($rules, [
            'hotel_id.required' => 'Hotel selection is required.',
            'room_number.required' => 'Room number is required.',
            'name.required' => 'Room name is required.',
            'type.required' => 'Room type is required.',
            'capacity.required' => 'Capacity is required.',
            'price_per_night.required' => 'Price per night is required.',
            'bed_type.required' => 'Bed type is required.',
            'floor.required' => 'Floor is required.',
            'main_image.required' => 'Main image is required.',
            'main_image.image' => 'The main image must be a valid image file.',
            'gallery_images.*.image' => 'Each gallery image must be a valid image file.',
        ]);
    }

    protected function getLayout(): string
    {
        return 'super_admin.layouts.app';
    }

    protected function getRoutePrefix(): string
    {
        return 'super_admin.';
    }
}
