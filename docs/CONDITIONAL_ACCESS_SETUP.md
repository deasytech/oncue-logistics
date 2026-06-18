# Setting Up Conditional Access After Disabling Security Defaults

Now that you've disabled Security Defaults, your Office 365 tenant has no security policies enforcing MFA. You should set up Conditional Access to re-enable security controls.

## Step-by-Step Guide to Set Up Conditional Access

### Step 1: Access Azure Portal

1. Go to https://portal.azure.com
2. Sign in with your admin account

### Step 2: Navigate to Conditional Access

1. Click **Microsoft Entra ID** (or search for it)
2. In the left sidebar, click **Security**
3. Click **Conditional Access**
4. Click **+ New policy**

### Step 3: Create MFA Policy for All Users

**Name:** `Require MFA for All Users`

**Assignments:**

- **Users:**
    - Select **All users**
    - **Exclude:** Click **Users and groups** → Search and select `oc@oncuelogistics.com` (your SMTP account, if you want it excluded from MFA)
- **Cloud apps or actions:**
    - Select **All cloud apps**

**Conditions:**

- Skip this section (apply to all conditions)

**Access controls:**

- **Grant:**
    - Select **Grant access**
    - Check **Require multi-factor authentication**
    - Click **Select**

**Enable policy:** **On**

Click **Create**

---

### Step 4: Create Policy to Block Legacy Auth (Except SMTP)

**Name:** `Block Legacy Authentication`

**Assignments:**

- **Users:**
    - Select **All users**
- **Cloud apps or actions:**
    - Select **All cloud apps**

**Conditions:**

- **Client apps:**
    - Configure: **Yes**
    - Check all: **Browser**, **Mobile apps and desktop clients**, **Exchange ActiveSync clients**, **Other clients**

**Access controls:**

- **Grant:**
    - Select **Block access**
    - Click **Select**

**Enable policy:** **On**

Click **Create**

---

### Step 5: Create Policy to Allow SMTP (Optional)

If you blocked legacy auth above, you need to allow SMTP specifically:

**Name:** `Allow SMTP for Email Account`

**Assignments:**

- **Users:**
    - Select **Users and groups** → Check `oc@oncuelogistics.com`
- **Cloud apps or actions:**
    - Select **Office 365 Exchange Online** (or All cloud apps)

**Conditions:**

- Skip this section

**Access controls:**

- **Grant:**
    - Select **Grant access**
    - Check **Require multi-factor authentication** (optional - you can leave unchecked for service accounts)
    - Click **Select**

**Enable policy:** **On**

Click **Create**

---

### Step 6: Verify Policy Order

1. In Conditional Access, policies are evaluated in order
2. Make sure **Allow** policies are above **Block** policies
3. Use the **...** menu to reorder if needed

Priority order should be:

1. Allow SMTP for Email Account (if created)
2. Require MFA for All Users
3. Block Legacy Authentication

---

### Step 7: Test Email Again

After setting up Conditional Access, test your email:

```bash
php artisan config:clear
php artisan mail:test oc@oncuelogistics.com
```

---

## Important Notes

1. **Policy Propagation:** Changes can take 5-10 minutes to take effect
2. **Break-glass Account:** Always have at least one Global Admin excluded from MFA policies as an emergency access account
3. **SMTP Account:** Consider whether `oc@oncuelogistics.com` needs MFA for SMTP - typically service accounts used for SMTP don't use MFA

## Recommended Minimum Policies

At minimum, you should have:

1. **Require MFA for Admins** - Critical for security
2. **Require MFA for All Users** - General protection
3. **Block Legacy Auth** - Prevents old authentication methods
4. **Allow SMTP** (if needed) - Exception for your email account

Would you like me to run the email test now to see if disabling Security Defaults fixed the issue?
