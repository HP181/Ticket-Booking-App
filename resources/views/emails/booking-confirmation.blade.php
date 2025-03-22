@component('mail::message')
# Booking Confirmation

Dear {{ $booking->user->name }},

Your booking for **{{ $booking->event->title }}** has been confirmed.

## Booking Details:
- **Booking Reference:** {{ $booking->booking_reference }}
- **Event:** {{ $booking->event->title }}
- **Date:** {{ $booking->event->event_date->format('F j, Y') }}
- **Time:** {{ \Carbon\Carbon::parse($booking->event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->event->end_time)->format('g:i A') }}
- **Venue:** {{ $booking->event->location }}
- **Ticket Type:** {{ $booking->ticketType->name }}
- **Quantity:** {{ $booking->quantity }}
- **Total Amount:** ${{ number_format($booking->total_amount, 2) }}

@component('mail::button', ['url' => route('bookings.show', $booking->id)])
View Booking Details
@endcomponent

Thank you for your purchase. We look forward to seeing you at the event!

Regards,<br>
{{ config('app.name') }}
@endcomponent