document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");

    const inputs = {
        name: document.getElementById("name-input"),
        email: document.getElementById("email-input"),
        password: document.getElementById("password-input"),
        confirmPassword: document.getElementById("confirm-password-input"),
        role: document.getElementById("role-input"),
    };

    const showPasswordCheckbox = document.getElementById("show-password");

    function showError(input, message) {
        let errorElement = input.nextElementSibling;

        if (!errorElement || !errorElement.classList.contains("error-message")) {
            errorElement = document.createElement("p");
            errorElement.classList.add("error-message");
            errorElement.style.color = "red";
            errorElement.style.margin = "5px 0";
            errorElement.style.fontSize = "14px";
            input.parentNode.appendChild(errorElement);
        }

        errorElement.textContent = message;
        input.classList.add("incorrect");
    }

    function removeError(input) {
        input.classList.remove("incorrect");
        let errorElement = input.nextElementSibling;
        if (errorElement && errorElement.classList.contains("error-message")) {
            errorElement.remove();
        }
    }

    function validateField(input, validator, errorMessage) {
        if (!validator(input.value.trim())) {
            showError(input, errorMessage);
            return false;
        } else {
            removeError(input);
            return true;
        }
    }

    function validateForm() {
        let valid = true;

        valid = validateField(inputs.name, (val) => /^[A-Za-z ]+$/.test(val), "Name should contain only alphabets.") && valid;
        valid = validateField(inputs.email, (val) => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(val), "Enter a valid email address.") && valid;
        valid = validateField(inputs.password, (val) => val.length >= 8, "Password must be at least 8 characters long.") && valid;
        valid = validateField(inputs.confirmPassword, (val) => val === inputs.password.value, "Passwords do not match.") && valid;
        valid = validateField(inputs.role, (val) => val !== "", "Please select a role.") && valid;

        return valid;
    }

    // **Real-time Validation on Every Input Field**
    Object.values(inputs).forEach(input => {
        input.addEventListener("input", () => {
            validateField(input, () => true, ""); // Remove error on typing
        });
    });

    // **Real-time Validation for Confirm Password**
    inputs.confirmPassword.addEventListener("input", () => {
        validateField(inputs.confirmPassword, (val) => val === inputs.password.value, "Passwords do not match.");
    });

    // **Form Submission**
    form.addEventListener("submit", (e) => {
        if (!validateForm()) {
            e.preventDefault(); // Prevent form submission if validation fails
        }
    });

    // **Show Password Toggle (if exists)**
    if (showPasswordCheckbox) {
        showPasswordCheckbox.addEventListener("change", () => {
            const type = showPasswordCheckbox.checked ? "text" : "password";
            inputs.password.type = type;
            inputs.confirmPassword.type = type;
        });
    }

    // **Handle Server-side Errors**
    const serverError = document.getElementById("server-error");
    if (serverError) {
        showError(inputs.email, serverError.textContent);
    }
});
