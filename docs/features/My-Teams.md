# My-team Feature Guide

## 1. What kind of feature is it?

**My-team** is a mobile-user team management and monitoring feature.

It includes these types of features:

- **Senior–Junior hierarchy**
    - Mobile users can have seniors and juniors.
    - Admin defines who is senior and who is junior.
    - Admin-level senior can have all mobile users under them.
    - Future mobile users can automatically go under the admin-level senior.
    - Admin selects juniors for each senior.
    - A senior sees only their assigned juniors.

- **Junior activity monitoring for seniors**
    - Senior can see junior’s current status:
        - Check-in
        - Check-out
        - Other attendance states
    - Senior can see junior’s last location.
    - Senior can see junior’s today total orders.
    - Senior can see junior’s today total collection.
    - Senior can see today’s visit summary.
    - Senior can see which dealer the junior visited.
    - Senior can see visit type:
        - Onsite
        - Offsite
        - Unlisted
    - Senior can see visit purpose.

- **Notifications**
    - Senior is notified only when a junior checks in.
    - Senior is notified only when a junior checks out.

- **Admin control**
    - Admin user must define seniors and juniors.
    - Admin can manage the full team structure.

---

## 2. Out of Scope

These are **not** part of this feature:

- No notifications for breaks or other events.
- No historical data beyond today.
- Juniors cannot be under multiple seniors.
- Seniors cannot be under other seniors.

---

## 3. Full Guide to Port This Feature to Another Project

This guide assumes you are copying the **My-team** feature from the original project into a **different Laravel project**.  
You cannot use `git cherry-pick` directly because the codebases are separate.  
Instead, you will manually copy files, merge changes, and adapt as needed.

---

### Step 1: Gather the source files

From the original project, collect these files.

**Created files:**

```text
app/Http/Controllers/API/MyTeamController.php
app/Http/Resources/API/TeamMemberResource.php
app/Http/Resources/API/MobileuserDetailResource.php
app/Http/Responses/API/ApiResponse.php
app/Services/API/TeamMemberService.php
database/migrations/2026_09_15_100000_create_mobileuser_teams_table.php
database/migrations/2026_09_15_200000_add_senior_columns_to_mobileusers_table.php
```

**Modified files (you need the full new version or the diff):**

```text
app/Http/Controllers/API/MobileuserController.php
app/Http/Controllers/API/v3/MobileuserController.php
app/Http/Controllers/API/v4/AttendanceController.php
app/Http/Controllers/API/v3/LocationController.php
app/Http/Controllers/MobileUserController.php
app/Http/Resources/API/MobileuserDetailResource.php
app/Http/Resources/API/TeamMemberResource.php
app/Models/Mobileuser.php
app/Models/Session.php
app/Traits/FCM.php
routes/api.php
resources/views/dashboards/mobileusers/user-edit.blade.php
resources/views/dashboards/mobileusers/user-register.blade.php
```

> **Tip:** Use `git show <commit-hash> -- <file>` in the original project to see exactly what changed, or use a diff tool.

---

### Step 2: Check compatibility

Before copying, verify:

- Laravel version is similar (e.g., both Laravel 9, 10, or 11).
- PHP version is compatible.
- Required packages are installed (e.g., FCM package, if any).
- The target project has the same base models (`Mobileuser`, `Session`) or equivalents.

If the target project uses different model names or table names, you will need to adapt the code.

---

### Step 3: Copy created files

Copy each created file into the **same relative path** in the target project.

Example:

```bash
cp app/Http/Controllers/API/MyTeamController.php /path/to/target-project/app/Http/Controllers/API/
```

Do this for all created files listed in Step 1.

---

### Step 4: Merge modified files

For each modified file, you must manually apply the changes to the target project’s version.

**Option A – Use diff tool:**

1. In the original project, run:
    ```bash
    git show <commit-hash> -- <file> > /tmp/<file>.diff
    ```
2. Open both the original and target versions of the file.
3. Apply the changes manually.

**Option B – Copy the whole file (if safe):**

If the target project’s version of the file has not diverged much, you can copy the entire file from the original project.  
But **be careful**: this may overwrite other changes in the target project.

> **Recommended:** Manually merge only the My-team related changes.

Key changes to look for:

- **Models** (`Mobileuser.php`, `Session.php`): new relationships, fillable fields, casts.
- **Controllers**: new methods, filtering by senior/junior, FCM triggers.
- **Routes** (`routes/api.php`): new routes for `/my-team`.
- **Views** (`user-edit.blade.php`, `user-register.blade.php`): new form fields for senior/junior assignment.
- **FCM trait** (`app/Traits/FCM.php`): topic configuration changes.

---

### Step 5: Adapt namespaces and use statements

If the target project uses a different root namespace (e.g., `App\` vs `Custom\`), update all `namespace` and `use` statements in the copied files.

Example:

```php
namespace App\Http\Controllers\API;
```

becomes

```php
namespace Custom\Http\Controllers\API;
```

Also check for class references like `App\Models\Mobileuser` and adjust if needed.

---

### Step 6: Run migrations in the target project

Copy the migration files into:

```text
database/migrations/
```

Then run:

```bash
php artisan migrate
```

If the target project already has a `mobileusers` table, ensure the new columns do not conflict.  
You may need to edit the migration to match the target table structure.

---

### Step 7: Update routes

Open `routes/api.php` in the target project and add the My-team routes.

They likely look like:

```php
Route::get('/my-team', [MyTeamController::class, 'index']);
Route::get('/my-team/{id}', [MyTeamController::class, 'show']);
```

Use the exact routes from the original project’s `routes/api.php`.

Then clear route cache:

```bash
php artisan route:clear
php artisan config:clear
php artisan optimize:clear
```

---

### Step 8: Configure FCM

The feature uses FCM to notify seniors on junior check-in/check-out.

1. Ensure the target project has the same FCM package installed.
2. Copy any FCM configuration from the original project’s `config/` folder or `.env`.
3. Add required environment variables:

```env
FCM_SERVER_KEY=your_key
FCM_SENDER_ID=your_sender_id
```

4. Clear config:

```bash
php artisan config:clear
```

---

### Step 9: Update views

If the target project uses Blade views for admin user management, copy the relevant sections from:

```text
resources/views/dashboards/mobileusers/user-edit.blade.php
resources/views/dashboards/mobileusers/user-register.blade.php
```

Add the new fields for:

- Marking a user as senior or junior.
- Selecting admin-level senior.
- Selecting juniors for a senior.

---

### Step 10: Test the feature in the target project

Start the target project:

```bash
php artisan serve
```

Run the manual test checklist in section 4.

---

### Step 11: Final verification

```bash
php artisan migrate:status
php artisan route:list | grep my-team
php artisan test
```

If tests exist in the original project for this feature, copy them over and run them in the target project.

---

### Notes

- If the target project has a different database schema, you may need to write new migrations.
- If the target project uses a different authentication system, adapt the controller logic accordingly.
- Always commit your changes in the target project so you can roll back if needed.

---

## 4. QA Checklist After Copying to Another Project

After you have copied the feature and run migrations, perform the following tests.  
Check each item and mark it as passed or failed.  
If something fails, fix it before moving to production.

### 4.1 Database & Migrations

- [ ] `mobileuser_teams` table exists with columns: `id`, `senior_id`, `junior_id`, `created_at`, `updated_at`.
- [ ] `mobileusers` table has new senior-related columns.
- [ ] Foreign keys are correct and constraints work.
- [ ] `junior_id` has a unique constraint (if required to prevent multiple seniors).
- [ ] Migrations can be rolled back without errors (`php artisan migrate:rollback`).
- [ ] Seeding works if seeders were added.

### 4.2 Models & Relationships

- [ ] `Mobileuser` model has correct relationships (`juniors`, `senior`, `adminSenior`, etc.).
- [ ] `Session` model changes are correct and do not break existing code.
- [ ] Eager loading works without N+1 issues.
- [ ] Mass assignment works for new fields (check `$fillable`).

### 4.3 API Endpoints

- [ ] `GET /api/my-team` returns list of juniors for the logged-in senior.
- [ ] `GET /api/my-team/{id}` returns detailed information for a specific junior.
- [ ] Senior cannot see juniors not assigned to them (403 or filtered out).
- [ ] Admin can see all users (if applicable).
- [ ] Response format matches `TeamMemberResource` and `MobileuserDetailResource`.
- [ ] Pagination works if implemented.
- [ ] Error handling returns proper HTTP status codes and messages.

### 4.4 Junior Activity Monitoring

- [ ] Senior can see junior’s current status (check-in, check-out, break-in, etc.).
- [ ] Senior can see junior’s last location.
- [ ] Senior can see junior’s today total orders.
- [ ] Senior can see junior’s today total collection.
- [ ] Senior can see today’s visit summary.
- [ ] Senior can see dealer visits with type (onsite, offsite, unlisted).
- [ ] Senior can see visit purpose.
- [ ] Data is correct for today only (no historical data).
- [ ] Data updates in real-time or on refresh as expected.

### 4.5 Notifications (FCM)

- [ ] Senior receives notification when junior checks in.
- [ ] Senior receives notification when junior checks out.
- [ ] No notification is sent for breaks or other events.
- [ ] Notification payload contains correct data (junior name, time, etc.).
- [ ] Notifications work on both Android and iOS (if applicable).
- [ ] FCM topic configuration is correct.
- [ ] Notifications are not sent to wrong users.

### 4.6 Admin Functionality

- [ ] Admin can mark a user as senior.
- [ ] Admin can mark a user as junior.
- [ ] Admin can assign juniors to a senior.
- [ ] Admin can set an admin-level senior.
- [ ] Future mobile users automatically go under admin senior (if implemented).
- [ ] Admin views show new fields correctly.
- [ ] Validation works for new fields (e.g., required, unique).
- [ ] Admin can edit existing users and change senior/junior assignments.

### 4.7 Edge Cases

- [ ] Junior cannot be assigned to multiple seniors (unique constraint).
- [ ] Senior cannot be under another senior.
- [ ] Deleting a senior reassigns or removes juniors correctly.
- [ ] Deleting a junior removes them from team.
- [ ] Empty team list returns empty array, not error.
- [ ] Senior with no juniors sees empty list.
- [ ] User with no senior role cannot access my-team endpoints.
- [ ] Large number of juniors does not cause performance issues.

### 4.8 Regression Tests

- [ ] Existing mobile user login still works.
- [ ] Existing attendance check-in/check-out still works.
- [ ] Existing location tracking still works.
- [ ] Existing order and collection features still work.
- [ ] Existing API responses unchanged for other endpoints.
- [ ] Existing admin user management still works.
- [ ] No new errors in logs.

### 4.9 Security

- [ ] Only authenticated users can access my-team endpoints.
- [ ] Senior can only see their own juniors.
- [ ] Junior cannot see other juniors or seniors.
- [ ] Admin routes are protected by admin middleware.
- [ ] SQL injection and XSS are prevented.
- [ ] Sensitive data is not exposed in API responses.

### 4.10 Performance

- [ ] My-team list loads within acceptable time (< 2 seconds).
- [ ] Detailed view loads within acceptable time.
- [ ] Notifications are sent within a few seconds of event.
- [ ] Database queries are optimized (no N+1).
- [ ] Caching works if implemented.

### 4.11 Manual End-to-End Test Steps

1. Login as admin.
2. Create a new mobile user as senior.
3. Create two mobile users as juniors.
4. Assign both juniors to the senior.
5. Login as senior.
6. Open My-team.
7. Verify list shows both juniors.
8. Click on a junior to see details.
9. Verify all details are correct (status, location, orders, collection, visits).
10. Login as junior.
11. Perform check-in.
12. Verify senior receives check-in notification.
13. Perform check-out.
14. Verify senior receives check-out notification.
15. Perform break-in.
16. Verify senior does NOT receive break notification.
17. Verify no historical data is shown (only today).
18. Try to assign a junior to another senior (should fail).
19. Try to make a senior under another senior (should fail).
20. Logout and verify endpoints are protected.

---

That is the full guide and QA checklist to port the **My-team** feature to another project.
