# Email Setup Guide for Office 365 SMTP

## Current Status

❌ Emails are NOT working - Authentication blocked by security defaults policy

## Solutions

---

## Option 3: Using App Password (EASIER - Recommended)

This is the simplest solution. You'll enable MFA on the account and generate an App Password specifically for SMTP authentication.

### Step-by-Step Guide:

#### Step 1: Enable MFA for the Account

1. Go to [Microsoft 365 Admin Center](https://admin.microsoft.com)
2. Navigate to **Users** → **Active users**
3. Click on **oc@oncuelogistics.com**
4. Click on **Account** tab → **Manage multifactor authentication**
5. Check the box next to the user
6. Click **Enable** in the right panel
7. Confirm by clicking **Enable multi-factor auth**

#### Step 2: Complete MFA Setup for the User

1. Sign in to [Outlook Web Access](https://outlook.office.com) with `oc@oncuelogistics.com`
2. You'll be prompted to set up MFA
3. Follow the wizard:
    - Choose **Mobile app** or **Phone** method
    - Complete the verification
    - Click **Finish**

#### Step 3: Generate App Password

**Option A: Through Office 365 Portal (Recommended)**

1. Go to https://www.office.com and sign in with `oc@oncuelogistics.com`
2. Click on your profile picture (top right) → **View account**
3. Click on **Security info** in the left sidebar
4. Click **+ Add method**
5. Select **App password** from the dropdown
6. Enter a name: `SMTP Email App` (or any name you prefer)
7. Click **Next**
8. **COPY THE PASSWORD** shown (it's a 16-character code like: `abcd efgh ijkl mnop`)
9. Click **Done**

**Option B: Through Azure AD (Alternative)**

1. Go to https://portal.azure.com
2. Sign in with `oc@oncuelogistics.com`
3. Navigate to **Microsoft Entra ID** → **Users** → Click on your user
4. Click **Authentication methods** tab
5. Click **+ Add authentication method**
6. Select **App password**
7. Enter a name: `SMTP Email App`
8. Click **Add**
9. **COPY THE PASSWORD** shown

**Option C: Direct URL (If above don't work)**

1. Go to: https://mysignins.microsoft.com/security-info
2. Sign in with `oc@oncuelogistics.com`
3. Click **Add sign-in method**
4. Select **App password**
5. Enter a name: `SMTP Email App`
6. **COPY THE PASSWORD** shown

> **Note:** The App Password is shown only once! Make sure to copy it before closing.

### Troubleshooting - If App Password Still Doesn't Work

If you get "credentials were incorrect" error even with the App Password, try these:

#### Check 1: Verify Authenticated SMTP is Enabled

1. Microsoft 365 Admin Center → Users → Active users
2. Click **oc@oncuelogistics.com**
3. Click **Mail** tab → **Manage email apps**
4. Make sure **Authenticated SMTP** is checked ✅

#### Check 2: Try Original Password

If MFA/App Password isn't working, try using your **original account password** instead:

```
MAIL_PASSWORD=UnitedNation@1
```

Sometimes App Passwords don't work if the tenant has specific security policies.

#### Check 3: Try Different Port/Encryption

Update your `.env`:

```
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

Or try:

```
MAIL_HOST=outlook.office365.com
MAIL_PORT=587
MAIL_ENCRYPTION=starttls
```

Or SSL:

```
MAIL_HOST=smtp.office365.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

#### Check 4: Wait for Propagation

Sometimes changes take 15-30 minutes to propagate. Wait 30 minutes and try again.

#### Check 5: Check for Conditional Access Blocks

1. Go to https://portal.azure.com
2. Microsoft Entra ID → Security → Conditional Access
3. Look for policies that might block "Legacy authentication" or "Other clients"
4. Either disable these policies or add `oc@oncuelogistics.com` as an exclusion

#### Check 6: Enable per-mailbox SMTP Auth via PowerShell

If you have PowerShell access:

```powershell
Install-Module -Name ExchangeOnlineManagement
Connect-ExchangeOnline
Set-CASMailbox -Identity oc@oncuelogistics.com -SmtpClientAuthenticationDisabled $false
```

#### Step 4: Update Your Laravel .env File

1. Open your `.env` file
2. Replace the `MAIL_PASSWORD` with the App Password you copied:

```
MAIL_PASSWORD=your_16_char_app_password_here
```

3. Save the file
4. Clear the config cache:

```bash
php artisan config:clear
```

#### Step 5: Test Email

```bash
php artisan mail:test oc@oncuelogistics.com
```

---

## Option 2: Conditional Access Policy (More Complex - Better for Organizations)

Use this if you have multiple users and want to maintain security defaults while allowing SMTP for specific accounts.

### Step-by-Step Guide:

#### Step 1: Disable Security Defaults

1. Go to [Azure Portal](https://portal.azure.com)
2. Navigate to **Microsoft Entra ID** → **Properties**
3. Scroll down and click **Manage security defaults**
4. Set **Security defaults** to **No**
5. Select a reason (e.g., "Using Conditional Access")
6. Click **Save**

#### Step 2: Create Conditional Access Policy

1. In Azure Portal, go to **Microsoft Entra ID** → **Security** → **Conditional Access**
2. Click **+ Create new policy**
3. Configure the policy:

**Name:** `Allow SMTP for Email Account`

**Assignments:**

- **Users:** Select users → Check `oc@oncuelogistics.com`
- **Cloud apps or actions:**
    - Select what this policy applies to: **All cloud apps** (or select **Office 365 Exchange Online** specifically)

**Conditions:**

- Skip this section (no conditions needed)

**Access controls:**

- **Grant:** Select **Grant access**
    - Check **Require multi-factor authentication**
    - Click **Select**

**Session:**

- Skip this section

**Enable policy:** **On**

4. Click **Create**

#### Step 3: Test SMTP

```bash
php artisan mail:test oc@oncuelogistics.com
```

---

## Which Should You Choose?

| Option                           | Best For                      | Difficulty     | Security Level |
| -------------------------------- | ----------------------------- | -------------- | -------------- |
| **Option 3: App Password**       | Single account, quick setup   | ⭐ Easy        | Good           |
| **Option 2: Conditional Access** | Multiple accounts, enterprise | ⭐⭐⭐ Complex | Better         |

## Recommendation

Start with **Option 3 (App Password)**. It's the standard way to handle SMTP authentication in Office 365 with modern security enabled. It will take about 5-10 minutes to set up.

Once you've completed the steps, run the test command again to verify emails are working.
