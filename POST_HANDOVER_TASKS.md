# Post-Handover Refactoring Plan

## 1. Visual Editor Logic Fixes (Priority: High)
*   **Fix Color Overrides:** In `inc/class-visual-editor.php` -> `output_saved_styles`, remove or comment out the check that prevents overriding critical properties (`background`, `color`, `border-color`). Currently, it blocks user color changes.
*   **Load Page-Specific Styles:** In `inc/class-visual-editor.php` -> `output_saved_styles`, add logic to check `is_singular()` and retrieve/output styles saved in post meta (`sme_custom_styles`), as they are currently saved but never loaded.
*   **Re-enable UI:** Remove the temporary `return;` statement added to `add_admin_bar_menu` to show the buttons again.

## 2. Quick Editor Refactoring (Priority: Medium)
*   **Remove Hardcoded Strings:** In `inc/class-quick-editor.php`, replace the array of hardcoded strings (e.g., `'Disclaimer'`, `'No Professional Advice'`) with a robust class-based filtering system (e.g., filtering elements with `.legal-text` class or `data-no-edit` attribute). This makes the code cleaner and language-agnostic.

## 3. Architecture & Cleanup (Priority: Low)
*   **Consolidate Editor Classes:** Review `class-visual-editor.php`, `class-universal-editor.php`, and `class-quick-editor.php` to see if shared logic can be moved to a parent class or trait to reduce code duplication.
*   **Dead Code Removal:** Scan for any other temporary "demo" code or hardcoded values used for the presentation and replace them with dynamic options.
