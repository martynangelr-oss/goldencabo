<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Contact;
use App\Services\BookingService;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function __construct(
        private BookingService $bookings,
        private ContactService $contacts,
    ) {}

    public function dashboard()
    {
        $totalReservations = $this->bookings->countTotal();
        $confirmedToday = $this->bookings->countConfirmedToday();
        $totalContacts = $this->contacts->countTotal();
        $unreadContacts = $this->contacts->countUnread();
        $recent = $this->bookings->recent(5);

        return view('admin.dashboard', compact(
            'totalReservations', 'confirmedToday', 'totalContacts', 'unreadContacts', 'recent'
        ));
    }

    public function bookings(Request $request)
    {
        $bookings = $this->bookings->paginateForAdmin([
            'search' => substr(trim($request->get('search', '')), 0, 100),
            'status' => $request->get('status'),
            'zone' => $request->get('zone'),
        ]);

        return view('admin.bookings', compact('bookings'));
    }

    public function bookingShow(int $id)
    {
        $booking = $this->bookings->findById($id);

        return view('admin.booking-show', compact('booking'));
    }

    public function bookingUpdate(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'notes' => 'nullable|string|max:2000',
        ]);

        $booking = $this->bookings->findById($id);
        $this->bookings->updateStatus($booking, [
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Estado actualizado.');
    }

    public function bookingDestroy(Booking $booking)
    {
        $this->bookings->delete($booking);

        return redirect()->route('admin.bookings')->with('success', 'Reservación eliminada.');
    }

    public function contacts()
    {
        $contacts = $this->contacts->paginateForAdmin();
        $this->contacts->markAllRead();

        return view('admin.contacts', compact('contacts'));
    }

    public function contactDestroy(Contact $contact)
    {
        $this->contacts->delete($contact);

        return back()->with('success', 'Mensaje eliminado.');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:191', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', 'unique:users,email,'.$user->id],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Introduce un correo válido.',
            'email.unique' => 'Este correo ya está en uso.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Información actualizada correctamente.');
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Ingresa tu contraseña actual.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
