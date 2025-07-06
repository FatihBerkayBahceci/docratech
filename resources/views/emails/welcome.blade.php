@component('mail::message')

{{-- Header with DocraTech Branding --}}
<div style="text-align: center; margin-bottom: 30px;">
    <div style="background: linear-gradient(135deg, #5A1188 0%, #9D38CF 100%); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
        <h1 style="color: white; font-size: 28px; font-weight: bold; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
            🏥 {{ $companyName }}
        </h1>
        <p style="color: rgba(255,255,255,0.9); font-size: 16px; margin: 8px 0 0 0;">
            Advanced Medical Management System
        </p>
    </div>
</div>

# Welcome to Your Medical Platform, {{ $userName }}! 🎉

We're excited to have you join the **{{ $companyName }}** family. Your account has been successfully created and is ready to use.

## 👤 **Your Account Details**

<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151; width: 30%;">
                <strong>👤 Full Name:</strong>
            </td>
            <td style="padding: 8px 0; color: #6b7280;">
                {{ $userName }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151;">
                <strong>📧 Email:</strong>
            </td>
            <td style="padding: 8px 0; color: #6b7280;">
                {{ $user->email }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151;">
                <strong>🏷️ Role:</strong>
            </td>
            <td style="padding: 8px 0;">
                <span style="background: linear-gradient(135deg, #5A1188 0%, #9D38CF 100%); color: white; padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: 600;">
                    {{ $roleName }}
                </span>
            </td>
        </tr>
        @if($password)
        <tr>
            <td style="padding: 8px 0; font-weight: 600; color: #374151;">
                <strong>🔐 Temporary Password:</strong>
            </td>
            <td style="padding: 8px 0;">
                <code style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 6px; font-family: 'Courier New', monospace; font-weight: bold; letter-spacing: 1px;">
                    {{ $password }}
                </code>
            </td>
        </tr>
        @endif
    </table>
</div>

## 🚀 **Getting Started**

Now that your account is set up, here's what you can do:

<div style="background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 16px; margin: 20px 0; border-radius: 0 8px 8px 0;">
    <h3 style="color: #1e40af; margin: 0 0 12px 0; font-size: 18px;">🏥 For Medical Staff</h3>
    <ul style="margin: 0; padding-left: 20px; color: #374151;">
        <li>Access patient management system</li>
        <li>Schedule and manage appointments</li>
        <li>Review medical records securely</li>
        <li>Collaborate with your medical team</li>
    </ul>
</div>

<div style="background: #f0fdf4; border-left: 4px solid #22c55e; padding: 16px; margin: 20px 0; border-radius: 0 8px 8px 0;">
    <h3 style="color: #15803d; margin: 0 0 12px 0; font-size: 18px;">⚙️ For Administrative Staff</h3>
    <ul style="margin: 0; padding-left: 20px; color: #374151;">
        <li>Manage user accounts and permissions</li>
        <li>Monitor system performance</li>
        <li>Generate reports and analytics</li>
        <li>Configure system settings</li>
    </ul>
</div>

@component('mail::button', [
    'url' => $loginUrl,
    'color' => 'primary'
])
🔐 Access Your Account
@endcomponent

## 🔒 **Security Information**

<div style="background: #fffbeb; border: 1px solid #f59e0b; border-radius: 8px; padding: 16px; margin: 20px 0;">
    <h4 style="color: #92400e; margin: 0 0 12px 0; display: flex; align-items: center;">
        ⚠️ Important Security Notice
    </h4>
    <p style="margin: 0; color: #78350f; line-height: 1.6;">
        @if($password)
        <strong>Please change your temporary password immediately after your first login.</strong> This ensures the security of your account and patient data.
        @else
        Your account uses the password you set during registration. Please keep it secure and don't share it with anyone.
        @endif
    </p>
</div>

### 🛡️ **Security Best Practices:**
- Use a strong, unique password
- Enable two-factor authentication if available
- Never share your login credentials
- Always log out when finished
- Report any suspicious activity immediately

## 📞 **Need Help?**

Our support team is here to help you get started:

<div style="background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;">
    <p style="margin: 0 0 12px 0; color: #6b7280;">
        <strong>📧 Email Support:</strong> 
        <a href="mailto:{{ $supportEmail }}" style="color: #5A1188; text-decoration: none;">{{ $supportEmail }}</a>
    </p>
    <p style="margin: 0; color: #6b7280;">
        <strong>🕒 Support Hours:</strong> Monday - Friday, 8:00 AM - 6:00 PM
    </p>
</div>

@component('mail::panel')
**💡 Pro Tip:** Bookmark the login page and explore the user guide in the Help section after logging in. Our platform is designed to streamline your workflow and improve patient care efficiency.
@endcomponent

---

Thank you for choosing **{{ $companyName }}**. We're committed to providing you with the best medical management experience.

**Best regards,**  
The DocraTech Team 🏥

<div style="text-align: center; margin-top: 30px; padding: 20px; background: #f9fafb; border-radius: 8px;">
    <p style="color: #6b7280; font-size: 14px; margin: 0;">
        This email was sent to {{ $user->email }} because an account was created for you on {{ $companyName }}.
    </p>
    <p style="color: #9ca3af; font-size: 12px; margin: 8px 0 0 0;">
        © {{ date('Y') }} DocraTech Medical Platform. All rights reserved.
    </p>
</div>

@endcomponent
