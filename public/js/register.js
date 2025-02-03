function validateForm() {
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    
    const errorContainer = document.getElementById("errorMessages");
    errorContainer.innerHTML = ""; 

    let errorMessage = "";

    if (username.trim() === "") {
        errorMessage += "<p>Username is required.</p>";
    }

    if (email.trim() === "") {
        errorMessage += "<p>Email is required.</p>";
    } else {
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        if (!emailPattern.test(email)) {
            errorMessage += "<p>Please enter a valid email address.</p>";
        }
    }

    if (password.trim() === "") {
        errorMessage += "<p>Password is required.</p>";
    }

    if (confirmPassword.trim() === "") {
        errorMessage += "<p>Confirm Password is required.</p>";
    }

    if (password !== confirmPassword) {
        errorMessage += "<p>Passwords do not match.</p>";
    }

    if (role.trim() === "") {
        errorMessage += "<p>Role is required.</p>";
    }

    if (errorMessage !== "") {
        errorContainer.innerHTML = errorMessage;
        return false;
    }

    return true;
}
