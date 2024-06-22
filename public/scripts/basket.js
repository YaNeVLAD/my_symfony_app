document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById('form');
    const submit = document.getElementById('submit');
    const paymentSum = form.getAttribute('data-variable');
    submit.setAttribute('disabled', 'disabled');
    isFormSubmitted = false;

    function validateAddress(input) {
        // Регулярное выражение для валидации строки в формате "г. <Название города>, ул. <Улица>, <Дом>"
        const regexp = /^г\.\s?[^\s,]+,\s?ул\.\s?[^\s,]+,\s?[^\s,]+$/;

        // Проверка соответствия строки регулярному выражению
        return regexp.test(input);
    }

    document.addEventListener('input', (event) => {
        const input = event.target;
        const value = input.value;
        validateAddress(value)
            ? submit.removeAttribute('disabled')
            : submit.setAttribute('disabled', 'disabled');
    });

    document.addEventListener("submit", (event) => {
        submitButton = event.target;
        if (isFormSubmitted) {
            event.preventDefault();
        } else {
            isFormSubmitted = true;
            submitButton.setAttribute('disabled', 'disabled');
        }
    });
})