@component('mail::message')

<table align="center">
    <tr>
        <td>
            <img src="{{ asset('img/emails/main-logo.png') }}" alt="Phase Logo" width="180">
        </td>
    </tr>
    <tr>
        <td>
            <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:19px;font-family:'open sans', 'helvetica neue', helvetica, arial, sans-serif;line-height:23px;color:#666666;text-align: center;">phase:<span style="color:#ff3366;">admin</span></p>
        </td>
    </tr>
</table>

@component('mail::subcopy')

# Phase {{ $subscription->subscription_plan->name }} Renewal Failure

We attempted to renew your '{{ $subscription->subscription_plan->name }}' subscription but the payment failed. Your
subscription has been cancelled and we will not attempt to charge you again unless you restart your subscription.

Thanks,<br>
{{ config('app.name') }}
@endcomponent

@endcomponent
