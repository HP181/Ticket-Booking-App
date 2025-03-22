@component('mail::message')
# Booking Cancelled

Dear {{ $booking->user->name }},

Your booking for **{{ $booking->event->title }}** has been cancelled.

## Cancelled Booking Details:
- **Booking Reference:** {{ $booking->booking_reference }}
- **Event:** {{ $booking->event->title }}
- **Date:** {{ $booking->event->event_date->format('F j, Y') }}
- **Time:** {{ \Carbon\Carbon::parse($booking->event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->event->end_time)->format('g:i A') }}
- **Venue:** {{ $booking->event->location }}
- **Ticket Type:** {{ $booking->ticketType->name }}
- **Original Quantity:** {{ $booking->quantity }}

If you have any questions or would like to make a new booking, please visit our website.

Regards,<br>
{{ config('app.name') }}
@endcomponent