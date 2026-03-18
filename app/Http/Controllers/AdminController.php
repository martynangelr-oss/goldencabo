<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalReservations = Booking::count();
        $confirmedToday    = Booking::whereDate('created_at', today())->count();
        $totalContacts     = Contact::count();
        $unreadContacts    = Contact::where('read', false)->count();
        $recent            = Booking::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalReservations', 'confirmedToday', 'totalContacts', 'unreadContacts', 'recent'
        ));
    }

    public function bookings(Request $request)
    {
        $query = Booking::latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%$search%")
                  ->orWhere('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('hotel', 'like', "%$search%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($zone = $request->get('zone')) {
            $query->where('zone', (int) $zone);
        }

        $bookings = $query->paginate(20)->withQueryString();
        return view('admin.bookings', compact('bookings'));
    }

    public function bookingShow(int $id)
    {
        $booking = Booking::findOrFail($id);
        return view('admin.booking-show', compact('booking'));
    }

    public function bookingUpdate(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'notes'  => 'nullable|string|max:2000',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update([
            'status' => $request->status,
            'notes'  => $request->notes,
        ]);
        return back()->with('success', 'Estado actualizado.');
    }

    public function contacts()
    {
        $contacts = Contact::latest()->paginate(20);
        Contact::where('read', false)->update(['read' => true]);
        return view('admin.contacts', compact('contacts'));
    }

    public function contactDestroy(Contact $contact)
    {
        $contact->delete();
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
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:191|unique:users,email,' . $user->id,
        ], [
            'name.required'  => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email'    => 'Introduce un correo válido.',
            'email.unique'   => 'Este correo ya está en uso.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Información actualizada correctamente.');
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Ingresa tu contraseña actual.',
            'password.required'         => 'La nueva contraseña es obligatoria.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
