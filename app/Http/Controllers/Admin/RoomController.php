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
        $rooms = $this->roomQuery()->with(['hotel', 'reservations' => function ($q) {
            $q->with(['user', 'customer'])->latest();
        }])->latest()->get();

        $totalRooms       = $rooms->count();
        $availableRooms   = $rooms->where('status', 'available')->count();
        $occupiedRooms    = $rooms->where('status', 'occupied')->count();
        $maintenanceRooms = $rooms->where('status', 'maintenance')->count();

        $roomsForJs = $rooms->map(fn (Room $room) => $this->formatRoomForJs($room));

        return view('admin.rooms.index', [
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

    public function show(Room $room)
    {
        $this->authorizeRoom($room);

        $room->load(['hotel', 'reservations' => function ($q) {
            $q->with(['user', 'customer'])->latest();
        }]);

        $data = $this->formatRoomForJs($room);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json($data);
        }

        return redirect()->route($this->getRoutePrefix() . 'rooms.index');
    }

    public function store(Request $request)
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;
        if (!$hotelId) {
            abort(403, __('admin.no_hotel_assigned_error'));
        }
        $data = $this->validateRoom($request, $hotelId);
        $data['hotel_id'] = $hotelId;

        $this->storeImages($request, $data);

        $room = Room::create($data);

        return $this->jsonOrRedirect($request, __('admin.room_created_success'), $room);
    }

    public function update(Request $request, Room $room)
    {
        $this->authorizeRoom($room);
        $hotelId = Auth::guard('admin')->user()->hotel_id;
        $data = $this->validateRoom($request, $hotelId, $room->id);

        $this->storeImages($request, $data, $room);

        $room->update($data);

        return $this->jsonOrRedirect($request, __('admin.room_updated_success'), $room);
    }

    public function destroy(Room $room)
    {
        $this->authorizeRoom($room);
        $this->deleteFiles($room);
        $room->delete();

        return $this->jsonOrRedirect(request(), __('admin.room_deleted_success'));
    }

    protected function roomQuery()
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;
        return Room::where('hotel_id', $hotelId);
    }

    protected function authorizeRoom(Room $room): void
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;
        if (!$hotelId || $room->hotel_id !== $hotelId) {
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
            'type' => ['required', 'string', Rule::in($this->roomTypes)],
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1500',
            'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'status' => ['required', Rule::in(array_keys($this->statusOptions))],
        ];

        return $request->validate($rules, [
            'room_number.required' => 'Room number is required.',
            'type.required' => 'Room type is required.',
            'capacity.required' => 'Capacity is required.',
            'price_per_night.required' => 'Price per night is required.',
            'main_image.image' => 'The main image must be a valid image file.',
        ]);
    }

    protected function storeImages(Request $request, array &$data, ?Room $room = null): void
    {
        if ($request->hasFile('main_image')) {
            if ($room) {
                $room->deleteImageFiles();
            }
            $data['main_image'] = $request->file('main_image')->store('rooms/main', 'public');
        }
    }

    protected function deleteFiles(Room $room): void
    {
        $room->deleteImageFiles();
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

    public function formatRoomForJs(Room $room): array
    {
        $reservations = $room->reservations->map(function ($res) {
            $clientName = $res->guest_name ?? $res->user?->name ?? $res->customer?->name ?? '—';
            $clientPhone = $res->guest_phone ?? $res->user?->phone ?? $res->customer?->phone ?? '—';
            $clientEmail = $res->user?->email ?? $res->customer?->email ?? '—';
            $checkIn = is_string($res->check_in) ? $res->check_in : $res->check_in?->format('Y-m-d');
            $checkOut = is_string($res->check_out) ? $res->check_out : $res->check_out?->format('Y-m-d');

            return [
                'id'           => $res->id,
                'ref'          => '#MOR-RSV-' . str_pad($res->id, 5, '0', STR_PAD_LEFT),
                'client_name'  => $clientName,
                'client_phone' => $clientPhone,
                'client_email' => $clientEmail,
                'guests_count' => $res->guests_count,
                'check_in'     => $checkIn,
                'check_out'    => $checkOut,
                'status'       => $res->status,
                'total_price'  => number_format($res->total_price, 2),
            ];
        })->values()->all();

        $today = now()->toDateString();
        $activeReservation = $room->reservations->first(function ($r) use ($today) {
            $checkIn = is_string($r->check_in) ? $r->check_in : $r->check_in?->format('Y-m-d');
            $checkOut = is_string($r->check_out) ? $r->check_out : $r->check_out?->format('Y-m-d');
            return in_array($r->status, ['confirmed', 'checked_in']) && $checkIn <= $today && $checkOut >= $today;
        });

        if (!$activeReservation) {
            $activeReservation = $room->reservations->filter(function ($r) use ($today) {
                $checkIn = is_string($r->check_in) ? $r->check_in : $r->check_in?->format('Y-m-d');
                return in_array($r->status, ['confirmed', 'checked_in']) && $checkIn >= $today;
            })->sortBy('check_in')->first();
        }

        if (!$activeReservation) {
            $activeReservation = $room->reservations->first(function ($r) {
                return in_array($r->status, ['confirmed', 'checked_in']);
            });
        }

        if (!$activeReservation) {
            $activeReservation = $room->reservations->first();
        }

        $activeData = null;
        if ($activeReservation) {
            $clientName = $activeReservation->guest_name ?? $activeReservation->user?->name ?? $activeReservation->customer?->name ?? '—';
            $clientPhone = $activeReservation->guest_phone ?? $activeReservation->user?->phone ?? $activeReservation->customer?->phone ?? '—';
            $clientEmail = $activeReservation->user?->email ?? $activeReservation->customer?->email ?? '—';
            $checkIn = is_string($activeReservation->check_in) ? $activeReservation->check_in : $activeReservation->check_in?->format('Y-m-d');
            $checkOut = is_string($activeReservation->check_out) ? $activeReservation->check_out : $activeReservation->check_out?->format('Y-m-d');

            $activeData = [
                'id'           => $activeReservation->id,
                'ref'          => '#MOR-RSV-' . str_pad($activeReservation->id, 5, '0', STR_PAD_LEFT),
                'client_name'  => $clientName,
                'client_phone' => $clientPhone,
                'client_email' => $clientEmail,
                'guests_count' => $activeReservation->guests_count,
                'check_in'     => $checkIn,
                'check_out'    => $checkOut,
                'status'       => $activeReservation->status,
                'total_price'  => number_format($activeReservation->total_price, 2),
            ];
        }

        return [
            'id'                 => $room->id,
            'room_number'        => $room->room_number,
            'type'               => $room->type,
            'capacity'           => $room->capacity,
            'price_per_night'    => number_format($room->price_per_night, 2),
            'description'        => $room->description,
            'status'             => $room->status,
            'main_image_url'     => $room->main_image ? asset('storage/' . $room->main_image) : null,
            'updated_at'         => $room->updated_at?->format('Y-m-d H:i'),
            'hotel_name'         => $room->hotel?->name ?? '',
            'hotel_id'           => $room->hotel_id,
            'active_reservation' => $activeData,
            'reservations'       => $reservations,
        ];
    }

    protected function getHotelsForForm(): ?\Illuminate\Support\Collection
    {
        return null;
    }
}
