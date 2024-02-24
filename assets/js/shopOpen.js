// Gets the quantity input element
const quantityInput = document.querySelector('.quantityInput');

// Decrement button
document.querySelector('#decrement').addEventListener('click', function () {
    let value = parseInt(quantityInput.value) || 0;
    if (value > 1) {
        value--;
        quantityInput.value = value;
    }
});

// Increment button
document.querySelector('#increment').addEventListener('click', function () {
    let value = parseInt(quantityInput.value) || 0;
    if (value < parseInt(quantityInput.max)) {
        value++;
        quantityInput.value = value;
    }
});

const sizeButtons = document.querySelectorAll('.size-btn');

sizeButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        const selectedSize = button.getAttribute('value');
        document.getElementById('selectedSize').value = selectedSize;

        sizeButtons.forEach(function (btn) {
            btn.style.backgroundColor = '';
        });

        button.style.backgroundColor = 'grey';
        document.getElementById('sizeErrorMessage').style.display = 'none';
    });
});

//cartButton
const cartButtons = document.querySelectorAll('.cartButton');

cartButtons.forEach(button => {
    button.addEventListener('click', cartClick);
});

function cartClick() {
    // Check if a size is selected
    const selectedSizeInput = document.getElementById('selectedSize');
    const selectedSize = selectedSizeInput.value;

    if (selectedSize === 'S' || selectedSize === 'M' || selectedSize === 'L' || selectedSize === 'XL') {
        let button = this;
        button.classList.add('clicked');

        setTimeout(function () {
            button.classList.remove('clicked');
            submitForm(button);
        }, 2250);
    } else {
        document.getElementById('sizeErrorMessage').style.display = 'block';
    }
}

function submitForm(button) {
    // Check if a size is selected
    const selectedSizeInput = document.getElementById('selectedSize');
    const selectedSize = selectedSizeInput.value;

    if (selectedSize === 'S' || selectedSize === 'M' || selectedSize === 'L' || selectedSize === 'XL') {
        button.closest('form').submit();
    }
}
