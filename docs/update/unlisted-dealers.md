# Unlisted Dealer Feature Guide

## 1. What kind of feature is it?

**Unlisted Dealer** is an update to the unlisted visit flow.

It includes these types of features:

- **Save unlisted dealer details once**
    - In the past, users had to fill in the same unlisted dealer details every time they visited that dealer.
    - Now, the user does not need to fill in the details again.

- **Select an existing unlisted dealer**
    - When a user visits the same unlisted dealer again, they can simply select the saved dealer from a list.
    - No need to re-enter dealer details.

- **New model and database table**
    - A new `UnlistedDealer` model and `unlisted_dealers` table store saved unlisted dealer information.

- **New API endpoints**
    - API endpoints are added so the mobile app can fetch and select saved unlisted dealers.

- **Session and mobile user integration**
    - Session and mobile user controllers are updated so the selected unlisted dealer is linked to the visit.

---

## 2. Out of Scope

These are **not** part of this feature:

- No editing of saved unlisted dealer details.
- No deleting of unlisted dealers.
- No admin management screen for unlisted dealers.

---

## 3. Full Guide to Port This Feature to Another Project

This guide assumes you are copying the **Unlisted Dealer** feature from the original project into a **different Laravel project**.  
You cannot use `git cherry-pick` directly because the codebases are separate.  
Instead, you will manually copy files, merge changes, and adapt as needed.

---

### Step 1: Gather the source files

From the original project, collect these files.

**Created files:**

```text
app/Models/UnlistedDealer.php
database/migrations/2026_09_18_154504_create_unlisted_dealers_table.php
```

**Modified files (you need the full new version or the diff):**

```text
app/Http/Controllers/API/DealerController.php
app/Http/Controllers/API/MobileuserController.php
app/Http/Controllers/API/SessionController.php
app/Http/Controllers/API/v3/MobileuserController.php
app/Http/Controllers/API/v4/SessionController.php
routes/api.php
```

> **Tip:** Use `git show 154c0512dbce9dd3c8df82ac34b7697585b6f336 -- <file>` in the original project to see exactly what changed, or use a diff tool.

---

### Step 2: Check compatibility

Before copying, verify:

- Laravel version is similar (e.g., both Laravel 9, 10, or 11).
- PHP version is compatible.
- The target project has the same base models (`Mobileuser`, `Session`) or equivalents.
- The target project has a similar unlisted visit flow.

If the target project uses different model names or table names, you will need to adapt the code.

---

### Step 3: Copy created files

Copy each created file into the **same relative path** in the target project.

Example:

```bash
cp app/Models/UnlistedDealer.php /path/to/target-project/app/Models/
cp database/migrations/2026_09_18_154504_create_unlisted_dealers_table.php /path/to/target-project/database/migrations/
```

---

### Step 4: Merge modified files

For each modified file, manually apply the changes to the target project’s version.

**Option A – Use diff tool:**

1. In the original project, run:
    ```bash
    git show 154c0512dbce9dd3c8df82ac34b7697585b6f336 -- <file> > /tmp/<file>.diff
    ```
2. Open both the original and target versions of the file.
3. Apply the changes manually.

**Option B – Copy the whole file (if safe):**

If the target project’s version of the file has not diverged much, you can copy the entire file from the original project.  
But **be careful**: this may overwrite other changes in the target project.

> **Recommended:** Manually merge only the Unlisted Dealer related changes.

Key changes to look for:

- **`DealerController.php`**: new method(s) to save or list unlisted dealers.
- **`MobileuserController.php`** (and `v3`): changes to how unlisted dealer details are saved for a mobile user.
- **`SessionController.php`** (and `v4`): changes to how a session links to a saved unlisted dealer.
- **`routes/api.php`**: new routes for unlisted dealer endpoints.

---

### Step 5: Adapt namespaces and use statements

If the target project uses a different root namespace (e.g., `App\` vs `Custom\`), update all `namespace` and `use` statements in the copied files.

Example:

```php
namespace App\Models;
```

becomes

```php
namespace Custom\Models;
```

Also check for class references like `App\Models\UnlistedDealer` and adjust if needed.

---

### Step 6: Run migrations in the target project

Copy the migration file into:

```text
database/migrations/
```

Then run:

```bash
php artisan migrate
```

If the target project already has an `unlisted_dealers` table, ensure there are no conflicts.  
You may need to edit the migration to match the target database structure.

---

### Step 7: Set up the model

Open:

```text
app/Models/UnlistedDealer.php
```

Make sure it has:

- Correct `$table` name (usually `unlisted_dealers`).
- Correct `$fillable` fields.
- Relationships to `Mobileuser` and/or `Session` if needed.

Example idea:

```php
protected $fillable = [
    'mobileuser_id',
    'name',
    'phone',
    'address',
    // add other fields used in the migration
];
```

Use the real code from the commit for exact fields.

---

### Step 8: Update routes

Open `routes/api.php` in the target project and add the Unlisted Dealer routes from the original project.

They likely look like:

```php
Route::get('/unlisted-dealers', [DealerController::class, 'unlistedDealers']);
Route::post('/unlisted-dealers', [DealerController::class, 'storeUnlistedDealer']);
```

Use the exact routes from the original project’s `routes/api.php`.

Then clear route cache:

```bash
php artisan route:clear
php artisan config:clear
php artisan optimize:clear
```

---

### Step 9: Update controller logic

In the target project, check these controllers for the new logic:

- **`DealerController.php`**
    - Add method(s) to list saved unlisted dealers.
    - Add method(s) to create a new saved unlisted dealer.

- **`MobileuserController.php`** and **`v3/MobileuserController.php`**
    - Apply the new logic for saving unlisted dealer details linked to the mobile user.

- **`SessionController.php`** and **`v4/SessionController.php`**
    - Apply the new logic so a session can reference a saved unlisted dealer instead of re-entering details.

> If the target project uses a different session flow, adapt the code to fit the flow but keep the same behavior.

---

### Step 10: Test the feature in the target project

Start the target project:

```bash
php artisan serve
```

Run the QA checklist in section 4.

---

### Step 11: Final verification

```bash
php artisan migrate:status
php artisan route:list | grep unlisted
php artisan test
```

If tests exist in the original project for this feature, copy them over and run them in the target project.

---

### Notes

- If the target project has a different unlisted visit flow, adapt the controller changes accordingly.
- If the target project already has an unlisted dealer concept, merge the new fields and logic instead of overwriting.
- Always commit your changes in the target project so you can roll back if needed.

---

## 4. QA Checklist After Copying to Another Project

After you have copied the feature and run migrations, perform the following tests.  
Check each item and mark it as passed or failed.  
If something fails, fix it before moving to production.

### 4.1 Database & Migrations

- [ ] `unlisted_dealers` table exists with the correct columns.
- [ ] Foreign keys are correct (e.g., `mobileuser_id` links to `mobileusers`).
- [ ] Indexes are correct for lookups.
- [ ] Migration can be rolled back without errors (`php artisan migrate:rollback`).
- [ ] Existing data is not affected by the new migration.

### 4.2 Model & Relationships

- [ ] `UnlistedDealer` model has correct `$table`, `$fillable`, and casts.
- [ ] Relationships to `Mobileuser` and `Session` work.
- [ ] Eager loading works without N+1 issues.

### 4.3 API Endpoints

- [ ] Endpoint to **list saved unlisted dealers** works.
- [ ] Endpoint to **save a new unlisted dealer** works.
- [ ] Response format is correct (JSON structure matches the app).
- [ ] Only the logged-in user’s unlisted dealers are returned.
- [ ] Error handling returns proper HTTP status codes and messages.
- [ ] Validation works for required fields.

### 4.4 Core Feature Behavior

- [ ] User can save an unlisted dealer during a visit.
- [ ] Next time the user visits the same unlisted dealer, they can **select** it instead of re-entering details.
- [ ] Selected unlisted dealer is correctly linked to the session/visit.
- [ ] Dealer details shown match the saved record.
- [ ] No duplicate unlisted dealer is created for the same user and same dealer.

### 4.5 Session & Mobile User Integration

- [ ] Session creation works when a saved unlisted dealer is selected.
- [ ] Session creation still works when a new unlisted dealer is created.
- [ ] Mobile user data is saved correctly for unlisted dealer visits.
- [ ] Existing session flow is not broken.

### 4.6 Edge Cases

- [ ] User with no saved unlisted dealers sees an empty list, not an error.
- [ ] User cannot select an unlisted dealer saved by another user.
- [ ] Saving the same dealer twice does not create duplicates.
- [ ] Missing required fields show proper validation errors.
- [ ] Large number of saved unlisted dealers does not cause performance issues.

### 4.7 Regression Tests

- [ ] Existing dealer list still works.
- [ ] Existing session flow still works.
- [ ] Existing mobile user login still works.
- [ ] Existing attendance check-in/check-out still works.
- [ ] Existing order and collection features still work.
- [ ] Existing API responses unchanged for other endpoints.
- [ ] No new errors in logs.

### 4.8 Security

- [ ] Only authenticated users can access unlisted dealer endpoints.
- [ ] User can only access their own unlisted dealers.
- [ ] SQL injection and XSS are prevented.
- [ ] Sensitive data is not exposed in API responses.

### 4.9 Performance

- [ ] Unlisted dealer list loads within acceptable time (< 2 seconds).
- [ ] Saving a new unlisted dealer is fast.
- [ ] Database queries are optimized (no N+1).
- [ ] Caching works if implemented.

### 4.10 Manual End-to-End Test Steps

1. Login as a mobile user.
2. Start an unlisted visit.
3. Fill in unlisted dealer details and save.
4. Confirm the new unlisted dealer is saved.
5. End the visit.
6. Start another unlisted visit.
7. Confirm the previously saved unlisted dealer appears in the list.
8. Select the saved unlisted dealer.
9. Confirm no details need to be re-entered.
10. Confirm the session is linked to the selected unlisted dealer.
11. Try to save the same dealer again (should not create a duplicate).
12. Login as a different user.
13. Confirm they cannot see the first user’s saved unlisted dealers.
14. Confirm editing is not possible (out of scope).
15. Confirm deleting is not possible (out of scope).
16. Confirm no admin management screen exists for unlisted dealers (out of scope).

---

That is the full guide and QA checklist to port the **Unlisted Dealer** feature to another project.
