document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.cart-qty-form').forEach((form) => {
    const input = form.querySelector('.qty-input');
    const decreaseButton = form.querySelector('[data-qty-action="decrease"]');
    const increaseButton = form.querySelector('[data-qty-action="increase"]');

    if (!input || !decreaseButton || !increaseButton) {
      return;
    }

    const clampQuantity = () => {
      const min = Number(input.min || 0);
      const max = Number(input.max || 0);
      let value = Number(input.value || min);

      if (Number.isNaN(value)) {
        value = min;
      }

      value = Math.max(min, Math.min(max, value));
      input.value = String(value);

      decreaseButton.disabled = value <= min;
      increaseButton.disabled = value >= max;
    };

    decreaseButton.addEventListener('click', () => {
      input.stepDown();
      clampQuantity();
    });

    increaseButton.addEventListener('click', () => {
      input.stepUp();
      clampQuantity();
    });

    input.addEventListener('input', clampQuantity);
    input.addEventListener('change', clampQuantity);

    clampQuantity();
  });
});
