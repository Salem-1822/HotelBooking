<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    protected array $statusOptions = ['available' => 'Available', 'occupied' => 'Occupied', 'maintenance' => 'Maintenance'];

    protected array $roomTypes = ['Single', 'Double', 'Twin', 'Deluxe', 'Suite'];

    public function index(Request $request)
    {
        $rooms = $this->roomQuery()->latest()->get();

        $totalRooms       = $rooms->count();
        $availableRooms   = $rooms->where('status', 'available')->count();
        $occupiedRooms    = $rooms->where('status', 'occupied')->count();
        $maintenanceRooms = $rooms->where('status', 'maintenance')->count();

        $roomsForJs = $rooms->map(function (Room $room) {
            return [
                'id'              => $room->id,
                'room_number'     => $room->room_number,
                'name'            => $room->name,
                'type'            => $room->type,
                'capacity'        => $room->capacity,
                'price_per_night' => number_format($room->price_per_night, 2),
                'description'     => $room->description,
                'status'          => $room->status,
                'main_image_url'  => $room->main_image ? asset('storage/' . $room->main_image) : null,
                'updated_at'      => $room->updated_at?->format('Y-m-d H:i'),
            ];
        });

        return view('rooms.index', [
            'layout'           => $this->getLayout(),
            'routePrefix'      => $this->getRoutePrefix(),
            'rooms'            => $rooms,
            'roomsForJs'       => $roomsForJs,
            'statusOptions'    => $this->statusOptions,
            'roomTypes'        => $this->roomTypes,
            'hotels'           => $this->getHotelsForForm(),
            'totalRooms'       => $totalRooms,
            'availableRooms'   => $availableRooms,
            'occupiedRooms'    => $occupiedRooms,
            'maintenanceRooms' => $maintenanceRooms,
        ]);
    }

    public function store(Request $request)
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;
        $data = $this->validateRoom($request, $hotelId);
        $data['hotel_id'] = $hotelId;

        $this->storeImages($request, $data);

        $room = Room::create($data);

        return $this->jsonOrRedirect($request, 'Room created successfully.', $room);
    }

    public function update(Request $request, Room $room)
    {
        $this->authorizeRoom($room);
        $hotelId = Auth::guard('admin')->user()->hotel_id;
        $data = $this->validateRoom($request, $hotelId, $room->id);

        $this->storeImages($request, $data, $room);

        $room->update($data);

        return $this->jsonOrRedirect($request, 'Room updated successfully.', $room);
    }

    public function destroy(Room $room)
    {
        $this->authorizeRoom($room);
        $this->deleteFiles($room);
        $room->delete();

        return $this->jsonOrRedirect(request(), 'Room deleted successfully.');
    }

    protected function roomQuery()
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;
        return Room::where('hotel_id', $hotelId);
    }

    protected function authorizeRoom(Room $room): void
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;
        if ($room->hotel_id !== $hotelId) {
            abort(403, 'Unauthorized action.');
        }
    }

    protected function validateRoom(Request $request, ?int $hotelId = null, ?int $roomId = null): array
    {
        $rules = [
            'room_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rooms')->where(function ($query) use ($hotelId) {
                    return $query->where('hotel_id', $hotelId);
                })->ignore($roomId),
            ],
            'name' => 'required|string|max:255',
            'type' => ['required', 'string', Rule::in($this->roomTypes)],
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1500',
            'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'status' => ['required', Rule::in(array_keys($this->statusOptions))],
        ];

        return $request->validate($rules, [
            'room_number.required' => 'Room number is required.',
            'name.required' => 'Room name is required.',
            'type.required' => 'Room type is required.',
            'capacity.required' => 'Capacity is required.',
            'price_per_night.required' => 'Price per night is required.',
            'main_image.image' => 'The main image must be a valid image file.',
        ]);
    }

    protected function storeImages(Request $request, array &$data, ?Room $room = null): void
    {
        if ($request->hasFile('main_image')) {
            if ($room && $room->main_image) {
                Storage::disk('public')->delete($room->main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('rooms/main', 'public');
        }
    }

    protected function deleteFiles(Room $room): void
    {
        if ($room->main_image) {
            Storage::disk('public')->delete($room->main_image);
        }
    }

    protected function jsonOrRedirect(Request $request, string $message, ?Room $room = null)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'room' => $room,
            ]);
        }

        return redirect()->route($this->getRoutePrefix() . 'rooms.index')->with('success', $message);
    }

    protected function getLayout(): string
    {
        return 'admin.layouts.app';
    }

    protected function getRoutePrefix(): string
    {
        return 'admin.';
    }

    protected function getHotelsForForm(): ?\Illuminate\Support\Collection
    {
        return null;
    }
}
