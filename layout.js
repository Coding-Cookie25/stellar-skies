document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const homeLink = document.querySelector('a[href="/home"]');
    const loginLink = document.querySelector('a[href="/login"]');

    if (form) {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const formData = new FormData(form);
            const data = {};

            formData.forEach((value, key) => {
                data[key] = value;
            });

            console.log('Form Data Submitted:', data);
            alert('Form submitted successfully!');
        });
    }

    if (homeLink) {
        homeLink.addEventListener('click', (event) => {
            event.preventDefault();
            window.location.href = '/home';
        });
    }

    if (loginLink) {
        loginLink.addEventListener('click', (event) => {
            event.preventDefault();
            window.location.href = '/login';
        });
    }
});
