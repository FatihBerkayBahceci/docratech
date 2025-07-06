@component('mail::message')

<div style="text-align: center; margin-bottom: 32px;">
<img src="{{ config('app.url') }}/images/docratech-logo.png" alt="DocraTech Medical Platform" style="height: 60px; margin-bottom: 16px;">
<h1 style="color: #1e40af; font-size: 28px; margin: 0; font-weight: 700;">Password Reset Notification</h1>
<p style="color: #6b7280; font-size: 16px; margin: 8px 0 0 0;">Your account password has been reset by an administrator</p>
</div>

## Hello {{ $userName }},

Your **{{ $companyName }}** account password has been reset by a system administrator for security reasons.

### 🔐 Your New Temporary Password

Your new temporary password is:

@component('mail::panel')
<div style="text-align: center; padding: 20px;">
<span style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; color: #dc2626; background: #fef2f2; padding: 12px 24px; border-radius: 8px; border: 2px dashed #dc2626;">{{ $tempPassword }}</span>
</div>
@endcomponent

### ⚠️ Important Security Information

<div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 16px; margin: 16px 0;">
<h4 style="color: #92400e; margin: 0 0 12px 0; font-size: 16px;">🚨 Security Notice</h4>
<ul style="color: #92400e; margin: 0; padding-left: 20px;">
<li><strong>Change this password immediately</strong> after logging in</li>
<li>This is a <strong>temporary password</strong> - it should not be shared</li>
<li>All your active sessions have been terminated for security</li>
<li>Use a strong, unique password when changing it</li>
</ul>
</div>

### 📋 Next Steps

1. **Log in** to your account using the temporary password above
2. **Go to your profile settings** immediately after login
3. **Change your password** to a secure one of your choice
4. **Enable two-factor authentication** for enhanced security

@component('mail::button', ['url' => $loginUrl, 'color' => 'primary'])
🔐 Login to Your Account
@endcomponent

### 🔒 Password Security Guidelines

When creating your new password, please ensure it:

- Contains at least **8 characters**
- Includes **uppercase and lowercase letters**
- Contains at least **one number**
- Includes at least **one special character** (@, #, $, etc.)
- Is **unique** and not used on other platforms

### 📞 Need Help?

If you did not request this password reset or have concerns about your account security:

- **Contact our support team** immediately at [{{ $supportEmail }}](mailto:{{ $supportEmail }})
- **Report security concerns** through our secure support channel
- **Review your account activity** after logging in

@component('mail::subcopy')
**Security Reminder:** Never share your login credentials with anyone. {{ $companyName }} staff will never ask for your password via email or phone.

This password reset was performed on {{ now()->format('F j, Y \a\t g:i A T') }} for security reasons.

---

© {{ date('Y') }} {{ $companyName }}. All rights reserved.
@endcomponent

@endcomponent 