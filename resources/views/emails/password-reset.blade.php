@component('mail::message')

{{-- Header with DocraTech Branding --}}
<div style="text-align: center; margin-bottom: 30px;">
    <div style="background: linear-gradient(135deg, #5A1188 0%, #9D38CF 100%); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
        <h1 style="color: white; font-size: 28px; font-weight: bold; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
            🏥 {{ $companyName }}
        </h1>
        <p style="color: rgba(255,255,255,0.9); font-size: 16px; margin: 8px 0 0 0;">
            Password Reset Request
        </p>
    </div>
</div>

# Password Reset Request 🔐

Hello {{ $userName }},

We received a request to reset your password for your **{{ $companyName }}** account. If you made this request, please click the button below to reset your password.

@component('mail::button', [
    'url' => $resetUrl,
    'color' => 'primary'
])
🔐 Reset Password
@endcomponent

<div style="background: #fffbeb; border: 1px solid #f59e0b; border-radius: 8px; padding: 16px; margin: 20px 0;">
    <h4 style="color: #92400e; margin: 0 0 12px 0; display: flex; align-items: center;">
        ⏰ Important Notice
    </h4>
    <p style="margin: 0; color: #78350f; line-height: 1.6;">
        This password reset link will expire in <strong>{{ $expiryMinutes }} minutes</strong> for security reasons.
    </p>
</div>

## 🛡️ **Security Information**

If you did not request a password reset, please ignore this email. Your password will remain unchanged.

For your security:
- Never share your password reset link with anyone
- Always verify the sender's email address
- Contact support immediately if you suspect unauthorized access

## 📞 **Need Help?**

If you're having trouble with the password reset process:

<div style="background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;">
    <p style="margin: 0 0 12px 0; color: #6b7280;">
        <strong>📧 Email Support:</strong> 
        <a href="mailto:{{ $supportEmail }}" style="color: #5A1188; text-decoration: none;">{{ $supportEmail }}</a>
    </p>
    <p style="margin: 0; color: #6b7280;">
        <strong>🕒 Support Hours:</strong> Monday - Friday, 8:00 AM - 6:00 PM
    </p>
</div>

---

**Best regards,**  
The DocraTech Security Team 🔒

<div style="text-align: center; margin-top: 30px; padding: 20px; background: #f9fafb; border-radius: 8px;">
    <p style="color: #6b7280; font-size: 14px; margin: 0;">
        This email was sent to {{ $user->email }} because a password reset was requested for your account.
    </p>
    <p style="color: #9ca3af; font-size: 12px; margin: 8px 0 0 0;">
        © {{ date('Y') }} DocraTech Medical Platform. All rights reserved.
    </p>
</div>

@endcomponent 