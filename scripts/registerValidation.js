// Registration Form Validation
document.addEventListener("DOMContentLoaded", function () {
    // Check if the registration form exists
    const form = document.getElementById('registrationForm');
    if (!form) return;

    // Input fields
    const firstNameField = document.getElementById('first');
    const lastNameField = document.getElementById('last');
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    const postalCodeField = document.getElementById('postal_code');

    // Error message containers
    const firstError = document.getElementById('first-error');
    const lastError = document.getElementById('last-error');
    const emailError = document.getElementById('email-error');
    const passwordError = document.getElementById('password-error');
    const confirmPasswordError = document.getElementById('confirm-password-error');
    const postalCodeError = document.getElementById('postal-code-error');

    // General error container
    const errorContainer = document.getElementById('error-container');
    const errorList = document.getElementById('error-list');

    // Validation patterns
    const postalCodePattern = /^[A-Za-z]\d[A-Za-z]\s?\d[A-Za-z]\d$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // --- Helper functions ---
    function safeSetStyle(element, styleProp, value) {
        if (element) element.style[styleProp] = value;
    }

    function safeShowError(errorElement, message) {
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = "block";
        }
    }

    function safeHideError(errorElement) {
        if (errorElement) {
            errorElement.textContent = "";
            errorElement.style.display = "none";
        }
    }

    function resetFieldStyle(field) {
        if (field) {
            field.style.border = "2px solid #e0e0e0";
            field.style.background = "#ffffff";
        }
    }

    function setErrorStyle(field) {
        if (field) {
            field.style.border = "2px solid #dc3545";
            field.style.background = "#fff5f5";
        }
    }

    // --- Validation functions ---
    function validateFirstName() {
        const value = firstNameField?.value.trim() || "";
        if (!value) {
            setErrorStyle(firstNameField);
            safeShowError(firstError, "First name is required.");
            return false;
        }
        resetFieldStyle(firstNameField);
        safeHideError(firstError);
        return true;
    }

    function validateLastName() {
        const value = lastNameField?.value.trim() || "";
        if (!value) {
            setErrorStyle(lastNameField);
            safeShowError(lastError, "Last name is required.");
            return false;
        }
        resetFieldStyle(lastNameField);
        safeHideError(lastError);
        return true;
    }

    function validateEmail() {
        const value = emailField?.value.trim() || "";
        if (!value) {
            setErrorStyle(emailField);
            safeShowError(emailError, "Email is required.");
            return false;
        }
        if (!emailPattern.test(value)) {
            setErrorStyle(emailField);
            safeShowError(emailError, "Please enter a valid email address.");
            return false;
        }
        resetFieldStyle(emailField);
        safeHideError(emailError);
        return true;
    }

    function validatePassword() {
        const value = passwordField?.value || "";
        if (!value) {
            setErrorStyle(passwordField);
            safeShowError(passwordError, "Password is required.");
            return false;
        }
        if (value.length < 6) {
            setErrorStyle(passwordField);
            safeShowError(passwordError, "Password must be at least 6 characters.");
            return false;
        }
        resetFieldStyle(passwordField);
        safeHideError(passwordError);
        return true;
    }

    function validateConfirmPassword() {
        const passwordValue = passwordField?.value || "";
        const confirmValue = confirmPasswordField?.value || "";
        if (!confirmValue) {
            setErrorStyle(confirmPasswordField);
            safeShowError(confirmPasswordError, "Please confirm your password.");
            return false;
        }
        if (passwordValue !== confirmValue) {
            setErrorStyle(confirmPasswordField);
            safeShowError(confirmPasswordError, "Passwords do not match.");
            return false;
        }
        resetFieldStyle(confirmPasswordField);
        safeHideError(confirmPasswordError);
        return true;
    }

    function validatePostalCode() {
        let value = postalCodeField?.value.trim().toUpperCase() || "";
        if (!value) {
            setErrorStyle(postalCodeField);
            safeShowError(postalCodeError, "Postal code is required.");
            postalCodeField?.focus();
            return false;
        }
        if (!postalCodePattern.test(value)) {
            setErrorStyle(postalCodeField);
            safeShowError(postalCodeError, "Invalid Canadian postal code format (e.g., A1A 1A1).");
            postalCodeField?.focus();
            return false;
        }
        // Format postal code with space if needed
        postalCodeField.value = value.replace(/^([A-Z]\d[A-Z])(\d[A-Z]\d)$/, '$1 $2');
        resetFieldStyle(postalCodeField);
        safeHideError(postalCodeError);
        return true;
    }

    // --- Handle form submit ---
    function handleFormSubmit(event) {
        event.preventDefault();

        safeSetStyle(errorContainer, 'display', 'none');
        if (errorList) errorList.innerHTML = "";

        const isFirstNameValid = validateFirstName();
        const isLastNameValid = validateLastName();
        const isEmailValid = validateEmail();
        const isPasswordValid = validatePassword();
        const isConfirmPasswordValid = validateConfirmPassword();
        const isPostalCodeValid = validatePostalCode();

        if (isFirstNameValid && isLastNameValid && isEmailValid &&
            isPasswordValid && isConfirmPasswordValid && isPostalCodeValid) {
            form.submit();
            return true;
        } else {
            // Focus on the first invalid field
            if (!isFirstNameValid) firstNameField?.focus();
            else if (!isLastNameValid) lastNameField?.focus();
            else if (!isEmailValid) emailField?.focus();
            else if (!isPasswordValid) passwordField?.focus();
            else if (!isConfirmPasswordValid) confirmPasswordField?.focus();
            else if (!isPostalCodeValid) postalCodeField?.focus();
        }

        return false;
    }

    // --- Event listeners ---
    firstNameField?.addEventListener('blur', validateFirstName);
    lastNameField?.addEventListener('blur', validateLastName);
    emailField?.addEventListener('blur', validateEmail);
    passwordField?.addEventListener('blur', validatePassword);
    confirmPasswordField?.addEventListener('blur', validateConfirmPassword);
    postalCodeField?.addEventListener('blur', validatePostalCode);

    firstNameField?.addEventListener('input', () => {
        if (firstNameField.value.trim() !== "") {
            resetFieldStyle(firstNameField);
            safeHideError(firstError);
        }
    });
    lastNameField?.addEventListener('input', () => {
        if (lastNameField.value.trim() !== "") {
            resetFieldStyle(lastNameField);
            safeHideError(lastError);
        }
    });
    emailField?.addEventListener('input', () => {
        if (emailField.value.trim() !== "") {
            resetFieldStyle(emailField);
            safeHideError(emailError);
        }
    });
    passwordField?.addEventListener('input', () => {
        if (passwordField.value !== "") {
            resetFieldStyle(passwordField);
            safeHideError(passwordError);
        }
    });
    confirmPasswordField?.addEventListener('input', () => {
        if (confirmPasswordField.value !== "") {
            resetFieldStyle(confirmPasswordField);
            safeHideError(confirmPasswordError);
        }
    });
    postalCodeField?.addEventListener('input', () => {
        postalCodeField.value = postalCodeField.value.toUpperCase();
        if (postalCodeField.value.trim() !== "") {
            resetFieldStyle(postalCodeField);
            safeHideError(postalCodeError);
        }
    });

    // Attach form submit handler
    form.onsubmit = handleFormSubmit;
});
