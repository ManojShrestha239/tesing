# FieldMA Client Setup & Customization Guide

**Purpose:** This guide outlines the steps to set up a new client instance of the FieldMA application by cloning the base repository, applying branding and configuration changes, uploading initial data, and optionally adding custom features.

**Estimated Time:** 1–2 hours (setup) + additional time for custom features  
**Difficulty:** Intermediate

---

## Prerequisites

- Git installed on your local machine
- Access to the base repository (GitLab/GitHub)
- New client’s branding assets (logo, color code)
- Company details (name, address, VAT, phone, email)
- Dealer and product lists in Excel format (optional, if bulk upload is needed)
- **Custom feature requirements** (if any) – documented and agreed upon with the client
- Access to the FieldMA admin panel (URL provided below)

---

## Step 1: Clone Repository and Remove Git History

1. Clone the base repository to your local machine:

    ```bash
    git clone git@gitlab.com:backend-project6182305/fieldma-2.o.git
    cd fieldma-2.o
    ```

2. Remove the existing `.git` folder to detach it from the base repository:

    ```bash
    rm -rf .git
    ```

---

## Step 2: Create a New Repository and Push Code

1. Create a new empty repository on your Git hosting service (e.g., GitLab, GitHub) for the client project.
2. Initialize a new Git repository in the project folder and link it to the new remote:

    ```bash
    git init
    git remote add origin git@gitlab.com:backend-project6182305/fieldma-2.o.git
    git add .
    git commit -m "Initial commit for <client-name>"
    git branch -M main
    git push -u origin main
    ```

---

## Step 3: Replace Logo

1. Prepare the client’s logo file in PNG format (or same format as existing).
2. Replace the existing logo file, keeping the same filename and path:

    ```
    /public/assets/pages/img/logo-fieldma.png
    ```

    Use the following command (adjust source path as needed):

    ```bash
    cp /path/to/new-logo.png public/assets/pages/img/logo-fieldma.png
    ```

---

## Step 4: Update Theme Color

1. Identify the **old primary color code** (e.g., `#FF6B6B`) used across the project.
2. Perform a global search and replace using your code editor (e.g., VSCode: `Cmd + Shift + H` on Mac, `Ctrl + Shift + H` on Windows/Linux).
3. Replace the old color code with the **new color code** provided by the client.
4. Ensure all files (CSS, SCSS, JS, Blade templates) where the color appears are updated.
5. Save all changes and verify visually later.

---

## Step 5: Change FCM Topic

1. Open `/config/app.php`.
2. Locate the following lines:

    ```php
    'prod-topic' => 'old_topic_name',
    'dev-topic' => 'old_topic_name_dev',
    ```

3. Replace the topic names with the new client-specific topic using the standard format:

    ```
    {company_name}_fieldMA_allUser
    ```

    Example: For company `abc`, the topic becomes `abc_fieldMA_allUser`.

4. Update both `prod-topic` and `dev-topic` accordingly.

---

## Step 6: Update Company Details in `.env`

1. Open the `.env` file in the project root.
2. Locate and update the following variables:

    ```env
    COMPANY_NAME=New Company Name
    COMPANY_ADDRESS=New Address
    COMPANY_VAT=New VAT Number
    COMPANY_PHONE=New Phone Number
    ```

3. Save the file.

---

## Step 7: Update Company Details in `/config/app.php`

1. Open `/config/app.php`.
2. Locate the company details section and update:

    ```php
    'company_name' => 'New Company Name',
    'company_email' => 'new@email.com',
    'company_phone' => 'New Phone',
    'company_address' => 'New Address',
    ```

3. Save the file.

---

## Step 8: Bulk Upload Dealer and Product Lists

### 8.1 Dealer Upload

1. Access the dealer creation page:
   `http://localhost/dealer/create`
2. Download the **sample Excel file** from that page.
3. Fill the Excel file with the new dealer details.
4. Upload the filled Excel file on the same page.

#### Dealer Import Data Standards & Validation Rules

To ensure a smooth import and avoid errors, the Excel file must follow these rules. The system performs rigorous validation before inserting data.

##### Required Columns

The Excel file **must** include a header row (first row). The following columns are mandatory:

| Column Name   | Required | Description                        |
| ------------- | -------- | ---------------------------------- |
| `area`        | ✅       | Area code (e.g., `ktm`, `pokhara`) |
| `subarea`     | ✅       | Sub‑area name (e.g., `baluwatar`)  |
| `dealer_code` | ✅       | Unique dealer code                 |
| `dealer_name` | ✅       | Dealer’s name                      |

**Optional columns** (used if present, otherwise default/empty):

- `mobile` – dealer’s mobile number
- `email` – dealer’s email address
- `address` – physical address
- `phone` – alternate phone number
- `state` – state/province
- `pan_vat_no` – PAN or VAT number
- `order_limit` – order limit (numeric or text)

##### Row‑Level Validations

1. **Required fields**
   `area`, `subarea`, `dealer_code`, `dealer_name` must not be empty (whitespace‑only or `null`).
   The value `'0'` is allowed (considered non‑empty).

2. **Maximum length**
   All string fields are checked against a maximum of **191 characters**.
   Fields validated: `dealer_code`, `dealer_name`, `mobile`, `email`, `address`, `phone`, `state`, `pan_vat_no`, `area`, `subarea`.

3. **File‑level uniqueness**
    - `dealer_code`: must be unique across the entire Excel file. Duplicate codes cause a row error.
    - `mobile`:
        - If **not empty**, must be unique across the file.
        - If **empty**, only one row may have an empty mobile (subsequent empty mobiles are rejected).
    - `email`:
        - If **not empty**, must be unique across the file.
        - **Empty emails are allowed** and not checked for uniqueness (multiple rows can have empty email).

##### Database‑Level Validations

After file‑level checks, the system queries the database to avoid duplicates with existing records.

- **Dealer Code**: If a dealer with the same code already exists in the database, the row is **skipped** (no update, no error).
- **Mobile**: If the same mobile (including empty string) already exists, the row is skipped.
- **Email**: For non‑empty emails, if the email already exists, the row is skipped. Empty emails are stored as `NULL` and are not checked.

##### Data Normalization

Before insertion, several fields are transformed:

| Field         | Transformation                                                                                                |
| ------------- | ------------------------------------------------------------------------------------------------------------- |
| `dealer_code` | Trimmed, used as the dealer `code` (exact string, case preserved).                                            |
| `dealer_name` | Trimmed.                                                                                                      |
| `address`     | Trimmed; if missing, stored as empty string `''`.                                                             |
| `mobile`      | Trimmed; if missing, stored as empty string `''`.                                                             |
| `email`       | Trimmed; if empty, stored as `NULL`.                                                                          |
| `phone`       | Trimmed; if missing, stored as empty string `''`.                                                             |
| `state`       | Trimmed; if missing, stored as empty string `''`.                                                             |
| `pan_vat_no`  | Trimmed; if missing, stored as empty string `''`.                                                             |
| `order_limit` | Stored as‑is (could be `null` if not provided).                                                               |
| `area`        | **Lowercased** and trimmed. Used as the `areacode` and also stored in the `area` column of the `areas` table. |
| `subarea`     | **Lowercased** and trimmed. Stored in the `subarea` column of the `subareas` table.                           |
| `status`      | Hardcoded to `'not-approved'`.                                                                                |
| `series`      | Hardcoded to `0`.                                                                                             |

- **Area & Subarea** are looked up or created:
    - **Area**: searched by `areacode` (lowercased). If not found, a new record is created with both `areacode` and `area` set to the lowercased code.
    - **Subarea**: searched by `subarea` (lowercased). If not found, a new record is created.
    - **Assignarea**: a relationship between area and subarea is ensured.

##### Recommended Excel File Formatting

- **Header row**: Must be present; column names can be any case (spaces become underscores).
- **Text encoding**: Use UTF‑8.
- **Mobile / Phone**: Use plain numbers (no spaces or dashes).
- **Email**: Use valid email format (stored as‑is, no strict validation).
- **Area & Subarea**: Consistent casing is fine; they will be lowercased.
- **Chunk size**: Large files are processed in chunks of 500 rows.

##### Error Reporting

- If any row fails validation, the entire import is aborted and an error message is returned listing each row number and specific errors.
- Database‑level duplicates (existing dealer code, mobile, or email) are **silently skipped** – they are not considered errors and do not stop the import.

---

### 8.2 Product Upload

1. Access the product creation page:
   `http://localhost/product/create`
2. Download the **sample Excel file** from that page.
3. Fill the Excel file with the new product details.
4. Upload the filled Excel file on the same page.

_Note: Product import validations are similar but not detailed in this guide. Please refer to the sample file and follow similar standards._

---

## Step 9: Implement Custom Features (If Requested)

If the client has requested custom features beyond the standard setup, follow these guidelines:

1. **Document Requirements**
   Clearly define the feature, expected behavior, and acceptance criteria. Get written approval from the client before starting development.

2. **Create a Feature Branch**
   Never develop directly on the `main` branch. Create a new branch for each custom feature:

    ```bash
    git checkout -b feature/<feature-name>
    ```

    Example: `feature/custom-pricing`

3. **Develop and Test**
    - Implement the feature in the feature branch.
    - Test thoroughly (unit tests, manual testing).
    - Ensure it does not break existing functionality.

4. **Merge Back**
   Once the feature is complete and approved, merge it into the `main` branch:

    ```bash
    git checkout main
    git merge feature/<feature-name>
    git push origin main
    ```

5. **Tag Releases**
   After merging significant changes, create a version tag:

    ```bash
    git tag -a v1.0.1 -m "Added custom pricing feature"
    git push origin v1.0.1
    ```

---

## Version Control & Branching Strategy

To maintain a clean and manageable codebase across multiple clients:

- **`main` branch** – Always stable, production-ready code.
- **`develop` branch** (optional) – Integration branch for ongoing work.
- **Feature branches** – Created from `develop` or `main` for each new feature or bug fix.
- **Hotfix branches** – Created from `main` for urgent fixes, then merged back into both `main` and `develop`.

**Branch naming conventions:**

- `feature/<name>` – New functionality
- `bugfix/<name>` – Bug fixes
- `hotfix/<name>` – Critical production fixes

**Commit messages** should be clear and descriptive (e.g., "Add custom pricing logic for client ABC").

---

## ✅ Completion Checklist

- [ ] Repository is pushed to the new remote
- [ ] Logo updated
- [ ] Theme color replaced globally
- [ ] FCM topics updated
- [ ] Company details updated in `.env` and `config/app.php`
- [ ] Dealer and product lists uploaded (if applicable)
- [ ] Custom features developed in feature branches and merged (if any)
- [ ] Tags created for version tracking
- [ ] Application tested for branding, data accuracy, and new features

---

## Troubleshooting

| Issue                          | Solution                                                   |
| ------------------------------ | ---------------------------------------------------------- |
| Logo not showing               | Check file name and path; clear browser cache              |
| Color not updated everywhere   | Use global search for remaining old color code             |
| Upload fails                   | Ensure Excel file matches sample format; check file size   |
| Push notifications not working | Verify FCM topic and Firebase configuration                |
| Merge conflicts                | Resolve conflicts locally, then commit and push            |
| Dealer import errors           | Check error messages for specific rows and fix accordingly |

---

**For any questions, contact the development team.**
