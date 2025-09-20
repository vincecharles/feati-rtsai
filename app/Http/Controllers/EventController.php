<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of events
     */
    public function index(Request $request)
    {
        $query = Event::with(['createdBy', 'attendees']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        // Filter by upcoming events
        if ($request->has('upcoming') && $request->upcoming) {
            $query->where('start_date', '>=', now());
        }

        $events = $query->orderBy('start_date', 'asc')->paginate(15);

        if ($request->expectsJson()) {
            return $this->successResponse('Events retrieved successfully', [
                'events' => $events->items(),
                'pagination' => $this->getPaginationData($events)
            ]);
        }

        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event
     */
    public function create()
    {
        $categories = $this->getCategories();
        $eventTypes = $this->getEventTypes();
        
        return view('events.create', compact('categories', 'eventTypes'));
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category' => 'required|string|max:100',
            'event_type' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'max_attendees' => 'nullable|integer|min:1',
            'registration_required' => 'boolean',
            'registration_deadline' => 'nullable|date|before:start_date',
            'cost' => 'nullable|numeric|min:0',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,cancelled,completed',
            'requirements' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('events', 'public');
            }

            // Create event
            $event = Event::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'event_type' => $validated['event_type'],
                'location' => $validated['location'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'max_attendees' => $validated['max_attendees'],
                'registration_required' => $validated['registration_required'] ?? false,
                'registration_deadline' => $validated['registration_deadline'],
                'cost' => $validated['cost'],
                'contact_person' => $validated['contact_person'],
                'contact_email' => $validated['contact_email'],
                'contact_phone' => $validated['contact_phone'],
                'image' => $imagePath,
                'status' => $validated['status'],
                'requirements' => $validated['requirements'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return $this->successResponse('Event created successfully', [
                    'event' => $event
                ]);
            }

            return redirect()->route('events.show', $event)
                ->with('success', 'Event created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to create event: ' . $e->getMessage());
            }
            
            return back()->withInput()
                ->with('error', 'Failed to create event: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        $event->load(['createdBy', 'attendees.user']);
        
        if (request()->expectsJson()) {
            return $this->successResponse('Event retrieved successfully', [
                'event' => $event
            ]);
        }
        
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified event
     */
    public function edit(Event $event)
    {
        $categories = $this->getCategories();
        $eventTypes = $this->getEventTypes();
        
        return view('events.edit', compact('event', 'categories', 'eventTypes'));
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category' => 'required|string|max:100',
            'event_type' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'max_attendees' => 'nullable|integer|min:1',
            'registration_required' => 'boolean',
            'registration_deadline' => 'nullable|date|before:start_date',
            'cost' => 'nullable|numeric|min:0',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,cancelled,completed',
            'requirements' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($event->image && Storage::disk('public')->exists($event->image)) {
                    Storage::disk('public')->delete($event->image);
                }
                
                $imagePath = $request->file('image')->store('events', 'public');
                $validated['image'] = $imagePath;
            }

            $event->update($validated);

            DB::commit();

            if ($request->expectsJson()) {
                return $this->successResponse('Event updated successfully', [
                    'event' => $event
                ]);
            }

            return redirect()->route('events.show', $event)
                ->with('success', 'Event updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to update event: ' . $e->getMessage());
            }
            
            return back()->withInput()
                ->with('error', 'Failed to update event: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified event
     */
    public function destroy(Event $event)
    {
        try {
            // Delete image if exists
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }
            
            $event->delete();

            if (request()->expectsJson()) {
                return $this->successResponse('Event deleted successfully');
            }

            return redirect()->route('events.index')
                ->with('success', 'Event deleted successfully.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return $this->errorResponse('Failed to delete event: ' . $e->getMessage());
            }
            
            return back()->with('error', 'Failed to delete event: ' . $e->getMessage());
        }
    }

    /**
     * Register for an event
     */
    public function register(Request $request, Event $event)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // Check if registration is required
            if (!$event->registration_required) {
                return $this->errorResponse('Registration is not required for this event');
            }

            // Check if registration deadline has passed
            if ($event->registration_deadline && now() > $event->registration_deadline) {
                return $this->errorResponse('Registration deadline has passed');
            }

            // Check if event is published
            if ($event->status !== 'published') {
                return $this->errorResponse('Event is not available for registration');
            }

            // Check if user is already registered
            if ($event->attendees()->where('user_id', $request->user_id)->exists()) {
                return $this->errorResponse('User is already registered for this event');
            }

            // Check if event is full
            if ($event->max_attendees && $event->attendees()->count() >= $event->max_attendees) {
                return $this->errorResponse('Event is full');
            }

            // Register user
            $event->attendees()->create([
                'user_id' => $request->user_id,
                'registered_at' => now(),
                'notes' => $request->notes,
            ]);

            return $this->successResponse('Successfully registered for the event');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to register for event: ' . $e->getMessage());
        }
    }

    /**
     * Cancel event registration
     */
    public function cancelRegistration(Request $request, Event $event)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $registration = $event->attendees()->where('user_id', $request->user_id)->first();
            
            if (!$registration) {
                return $this->errorResponse('User is not registered for this event');
            }

            $registration->delete();

            return $this->successResponse('Registration cancelled successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to cancel registration: ' . $e->getMessage());
        }
    }

    /**
     * Get event statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $stats = [
                'total_events' => Event::count(),
                'upcoming_events' => Event::where('start_date', '>=', now())->count(),
                'past_events' => Event::where('end_date', '<', now())->count(),
                'published_events' => Event::where('status', 'published')->count(),
                'draft_events' => Event::where('status', 'draft')->count(),
                'cancelled_events' => Event::where('status', 'cancelled')->count(),
                'total_attendees' => Event::withCount('attendees')->get()->sum('attendees_count'),
            ];

            return $this->successResponse('Event statistics retrieved successfully', $stats);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve event statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get upcoming events
     */
    public function getUpcomingEvents(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            
            $events = Event::where('start_date', '>=', now())
                ->where('status', 'published')
                ->orderBy('start_date', 'asc')
                ->limit($limit)
                ->get();

            return $this->successResponse('Upcoming events retrieved successfully', [
                'events' => $events
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve upcoming events: ' . $e->getMessage());
        }
    }

    /**
     * Get categories list
     */
    private function getCategories()
    {
        return [
            'Academic' => 'Academic',
            'Sports' => 'Sports',
            'Cultural' => 'Cultural',
            'Social' => 'Social',
            'Career' => 'Career Development',
            'Health' => 'Health & Wellness',
            'Technology' => 'Technology',
            'Community' => 'Community Service',
            'Entertainment' => 'Entertainment',
            'Other' => 'Other',
        ];
    }

    /**
     * Get event types list
     */
    private function getEventTypes()
    {
        return [
            'conference' => 'Conference',
            'workshop' => 'Workshop',
            'seminar' => 'Seminar',
            'meeting' => 'Meeting',
            'celebration' => 'Celebration',
            'competition' => 'Competition',
            'exhibition' => 'Exhibition',
            'training' => 'Training',
            'social' => 'Social Event',
            'other' => 'Other',
        ];
    }
}
