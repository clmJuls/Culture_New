document.addEventListener('DOMContentLoaded', function() {
    // Password validation
    const passwordValidation = {
        init: function() {
            this.passwordInput = document.querySelector('input[name="password"]');
            this.confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
            this.setupRequirements();
            this.addEventListeners();
        },

        requirements: {
            length: {
                regex: /.{8,}/,
                element: document.getElementById('length'),
                met: false
            },
            uppercase: {
                regex: /[A-Z]/,
                element: document.getElementById('uppercase'),
                met: false
            },
            lowercase: {
                regex: /[a-z]/,
                element: document.getElementById('lowercase'),
                met: false
            },
            number: {
                regex: /[0-9]/,
                element: document.getElementById('number'),
                met: false
            },
            special: {
                regex: /[@$!%*?&]/,
                element: document.getElementById('special'),
                met: false
            }
        },

        setupRequirements: function() {
            // Hide all requirements initially
            for (const requirement of Object.values(this.requirements)) {
                requirement.element.style.display = 'none';
                requirement.met = false;
            }
            document.getElementById('match').style.display = 'none';
        },

        handleRequirementValidation: function(requirement, isValid) {
            if (isValid && !requirement.met) {
                // Show green success state
                requirement.element.style.display = 'block';
                requirement.element.classList.add('valid');
                const icon = requirement.element.querySelector('i');
                icon.classList.remove('fa-exclamation-circle');
                icon.classList.add('fa-check-circle');

                // Start fade out animation
                setTimeout(() => {
                    requirement.element.classList.add('fade-out');
                    // Hide element after animation
                    setTimeout(() => {
                        requirement.element.style.display = 'none';
                        requirement.element.classList.remove('valid', 'fade-out');
                        icon.classList.remove('fa-check-circle');
                        icon.classList.add('fa-exclamation-circle');
                        requirement.met = true;
                    }, 2000);
                }, 500);
            } else if (!isValid) {
                requirement.met = false;
                requirement.element.classList.remove('valid', 'fade-out');
                requirement.element.style.display = 'block';
                const icon = requirement.element.querySelector('i');
                icon.classList.remove('fa-check-circle');
                icon.classList.add('fa-exclamation-circle');
            }
        },

        checkRequirements: function() {
            const password = this.passwordInput.value;
            const confirmPassword = this.confirmPasswordInput.value;
            
            if (password.length > 0) {
                for (const requirement of Object.values(this.requirements)) {
                    const isValid = requirement.regex.test(password);
                    this.handleRequirementValidation(requirement, isValid);
                }
            } else {
                for (const requirement of Object.values(this.requirements)) {
                    requirement.met = false;
                    requirement.element.style.display = 'none';
                }
            }

            // Handle password match
            const matchRequirement = {
                element: document.getElementById('match'),
                met: false
            };

            if (confirmPassword.length > 0) {
                const passwordsMatch = password === confirmPassword;
                this.handleRequirementValidation(matchRequirement, passwordsMatch);
            } else {
                matchRequirement.met = false;
                matchRequirement.element.style.display = 'none';
            }
        },

        addEventListeners: function() {
            this.passwordInput.addEventListener('input', () => this.checkRequirements());
            this.confirmPasswordInput.addEventListener('input', () => this.checkRequirements());
        }
    };

    // Email validation
    const emailValidation = {
        init: function() {
            this.emailInput = document.querySelector('input[name="email"]');
            this.emailError = document.getElementById('email-error');
            this.emailValidated = false;
            this.addEventListeners();
        },

        validateEmail: function(email) {
            const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return regex.test(email);
        },

        handleValidation: function() {
            const email = this.emailInput.value.trim();
            
            if (email.length > 0) {
                if (!this.validateEmail(email)) {
                    this.emailError.style.display = 'block';
                    this.emailInput.classList.add('error');
                    this.emailValidated = false;
                } else {
                    this.emailError.style.display = 'none';
                    this.emailInput.classList.remove('error');
                    this.emailValidated = true;
                }
            } else {
                this.emailError.style.display = 'none';
                this.emailInput.classList.remove('error');
                this.emailValidated = false;
            }
        },

        addEventListeners: function() {
            this.emailInput.addEventListener('input', () => this.handleValidation());
        }
    };

    // Initialize validations
    passwordValidation.init();
    emailValidation.init();
});
