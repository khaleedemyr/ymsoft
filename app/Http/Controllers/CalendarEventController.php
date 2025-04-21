<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarEvent;
use App\Models\DataOutlet;
use App\Models\CalendarActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CalendarEventController extends Controller
{
    public function index()
    {
        \Log::info('Calendar index page accessed');
        $outlets = DataOutlet::all();
        return view('calendar.index', compact('outlets'));
    }

    public function getEvents(Request $request)
    {
        \Log::info('getEvents called with params:', $request->all());
        
        $query = CalendarEvent::with('outlet');
        
        // Filter berdasarkan tanggal
        if ($request->has('start') && $request->has('end')) {
            $query->whereBetween('start_date', [$request->start, $request->end]);
        }
        
        // Filter berdasarkan outlet_id
        if ($request->has('outlet_id') && !empty($request->outlet_id) && $request->outlet_id != 'all') {
            \Log::info('Filtering by outlet:', ['outlet_id' => $request->outlet_id]);
            $query->where('outlet_id', $request->outlet_id);
        }
        
        // Filter berdasarkan status
        if ($request->has('status') && !empty($request->status) && $request->status != 'all') {
            \Log::info('Filtering by status:', ['status' => $request->status]);
            $query->where('status', $request->status);
        }
        
        $events = $query->get();
        \Log::info('Events found:', ['count' => $events->count()]);
        
        return $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_date,
                'end' => $event->end_date,
                'description' => $event->description,
                'outlet_id' => $event->outlet_id,
                'outlet_name' => $event->outlet ? $event->outlet->nama_outlet : 'N/A',
                'pic_name' => $event->pic_name,
                'pic_phone' => $event->pic_phone,
                'status' => $event->status,
                'className' => $this->getStatusClass($event->status),
                'extendedProps' => [
                    'outlet_name' => $event->outlet ? $event->outlet->nama_outlet : 'N/A',
                    'company_name' => $event->company_name,
                    'segment' => $event->segment,
                    'area' => $event->area,
                    'pic_name' => $event->pic_name,
                    'pic_position' => $event->pic_position,
                    'pic_phone' => $event->pic_phone,
                    'pax' => $event->pax,
                    'event_type' => $event->event_type,
                    'estimation_revenue' => $event->estimation_revenue,
                    'status' => $event->status
                ]
            ];
        });
    }

    public function store(Request $request)
    {
        \Log::info('Received event data for store:', $request->all());
        
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'outlet_id' => 'required|exists:tbl_data_outlet,id_outlet',
                'company_name' => 'nullable|string|max:255',
                'segment' => 'nullable|string|max:100',
                'area' => 'nullable|string|max:255',
                'pic_name' => 'required|string|max:255',
                'pic_position' => 'nullable|string|max:255',
                'pic_phone' => 'required|string|max:20',
                'pax' => 'nullable|integer',
                'event_type' => 'nullable|string|max:100',
                'estimation_revenue' => 'nullable|numeric',
                'status' => 'required|in:Tentative,Confirmed,Definitive'
            ]);

            // Cek jika ini adalah request update yang terkirim ke route POST
            if ($request->has('event_id') && !empty($request->event_id)) {
                \Log::info('Event ID found, redirecting to update method', ['event_id' => $request->event_id]);
                $event = CalendarEvent::findOrFail($request->event_id);
                return $this->update($request, $event);
            }

            $event = CalendarEvent::create($validated);
            \Log::info('Event created successfully:', ['id' => $event->id]);
            
            // Buat aktivitas untuk event baru
            CalendarActivity::create([
                'user_id' => Auth::id(),
                'event_id' => $event->id,
                'title' => 'Event baru dibuat',
                'description' => 'Event "' . $event->title . '" telah dibuat pada ' . ($event->outlet ? $event->outlet->nama_outlet : 'N/A')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Event berhasil ditambahkan',
                'event' => $event
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating event:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(CalendarEvent $event)
    {
        return response()->json($event->load('outlet'));
    }

    public function update(Request $request, CalendarEvent $event)
    {
        \Log::info('Updating event:', ['id' => $event->id, 'data' => $request->all()]);
        
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'outlet_id' => 'required|exists:tbl_data_outlet,id_outlet',
                'company_name' => 'nullable|string|max:255',
                'segment' => 'nullable|string|max:100',
                'area' => 'nullable|string|max:255',
                'pic_name' => 'required|string|max:255',
                'pic_position' => 'nullable|string|max:255',
                'pic_phone' => 'required|string|max:20',
                'pax' => 'nullable|integer',
                'event_type' => 'nullable|string|max:100',
                'estimation_revenue' => 'nullable|numeric',
                'status' => 'required|in:Tentative,Confirmed,Definitive'
            ]);

            $oldStatus = $event->status;
            
            $event->update($validated);
            \Log::info('Event updated successfully:', ['id' => $event->id]);
            
            if ($oldStatus != $request->status) {
                CalendarActivity::create([
                    'user_id' => Auth::id(),
                    'event_id' => $event->id,
                    'title' => 'Status event diubah',
                    'description' => 'Status event "' . $event->title . '" diubah dari ' . $oldStatus . ' menjadi ' . $request->status
                ]);
            } else {
                CalendarActivity::create([
                    'user_id' => Auth::id(),
                    'event_id' => $event->id,
                    'title' => 'Event diperbarui',
                    'description' => 'Detail event "' . $event->title . '" telah diperbarui'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Event berhasil diperbarui',
                'event' => $event
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating event:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(CalendarEvent $event)
    {
        CalendarActivity::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'title' => 'Event dihapus',
            'description' => 'Event "' . $event->title . '" telah dihapus'
        ]);
        
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil dihapus'
        ]);
    }

    public function upcoming()
    {
        \Log::info('Upcoming events requested');
        try {
            $events = CalendarEvent::with('outlet')
                ->where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->limit(5)
                ->get();
                
            \Log::info('Upcoming events found:', ['count' => $events->count(), 'events' => $events->pluck('id')->toArray()]);
            
            $mappedEvents = $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start_date' => $event->start_date->format('d M'),
                    'category' => $this->getStatusClass($event->status),
                    'outlet_name' => $event->outlet ? $event->outlet->nama_outlet : 'N/A',
                    'company_name' => $event->company_name,
                    'segment' => $event->segment
                ];
            });
            
            \Log::info('Returning mapped upcoming events', ['count' => $mappedEvents->count()]);
            return response()->json($mappedEvents);
        } catch (\Exception $e) {
            \Log::error('Error retrieving upcoming events: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Terjadi kesalahan saat memuat event mendatang'], 500);
        }
    }

    public function activities()
    {
        \Log::info('Activities requested');
        try {
            // Periksa apakah tabel calendar_activities ada
            if (\Schema::hasTable('calendar_activities')) {
                \Log::info('calendar_activities table exists');
                
                $activities = CalendarActivity::with(['user', 'event'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
                    
                \Log::info('Activities found:', ['count' => $activities->count(), 'activities' => $activities->pluck('id')->toArray()]);
                
                $mappedActivities = $activities->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'title' => $activity->title,
                        'description' => $activity->description,
                        'created_at' => $activity->created_at->diffForHumans(),
                        'user_name' => $activity->user ? $activity->user->nama_lengkap : 'System',
                        'user_avatar' => $activity->user ? $activity->user->avatar : 'build/images/users/avatar-default.jpg'
                    ];
                });
                
                \Log::info('Returning mapped activities', ['count' => $mappedActivities->count()]);
                return response()->json($mappedActivities);
            } else {
                \Log::warning('calendar_activities table does not exist');
                return response()->json([
                    [
                        'id' => 1,
                        'title' => 'Tabel aktivitas belum tersedia',
                        'description' => 'Fitur aktivitas sedang dalam pengembangan',
                        'created_at' => now()->diffForHumans(),
                        'user_name' => 'System',
                        'user_avatar' => 'build/images/users/avatar-default.jpg'
                    ]
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error retrieving activities: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                [
                    'id' => 1,
                    'title' => 'Terjadi kesalahan',
                    'description' => 'Gagal memuat aktivitas: ' . $e->getMessage(),
                    'created_at' => now()->diffForHumans(),
                    'user_name' => 'System',
                    'user_avatar' => 'build/images/users/avatar-default.jpg'
                ]
            ]);
        }
    }

    private function getStatusClass($status)
    {
        switch ($status) {
            case 'Tentative':
                return 'bg-warning-subtle';
            case 'Confirmed':
                return 'bg-info-subtle';
            case 'Definitive':
                return 'bg-success-subtle';
            default:
                return 'bg-secondary-subtle';
        }
    }
} 